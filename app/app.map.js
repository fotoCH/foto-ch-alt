app.directive('googleMaps', ['$http', function ($http) {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            orte: '=',  // only used for data binding to grid/list view
            url: '='
        },
        templateUrl: 'app/shared/content/map.html',
        link: function ($scope) {
            $scope.selectedMarkers = [];
            var initiallyDisplayedPreviews = 20;
            $scope.totalDisplayed = initiallyDisplayedPreviews;
            $scope.total = 0;
            var markers = [];
            var markerCluster;
            var minClusterZoom = 10;

            var availableHeight = $(window).height() - $('.filter-bar').height() - $('.l-header').height() - $('.l-footer').height();
            $('#map').css('height', availableHeight + 'px');

            var scrollCounter = 0;
            $('.preview').scroll(function () {
                scrollCounter++;
                if (scrollCounter > 20) {
                    scrollCounter = 0;
                    $scope.$apply(updateTotalDisplayed);
                }
            });

            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 5,
                center: {lat: 46.956830, lng: 7.450751}
            });

            var spider = new OverlappingMarkerSpiderfier(map, {
                markersWontMove: true,
                markersWontHide: true
            });

            spider.addListener('format', function (marker, status) {
                var icon = 'assets/img/m/marker.svg';
                switch (status) {
                    case OverlappingMarkerSpiderfier.markerStatus.SPIDERFIED:
                        icon = 'assets/img/m/marker-highlight.svg';
                        break;
                    case OverlappingMarkerSpiderfier.markerStatus.SPIDERFIABLE:
                        icon = 'assets/img/m/marker-plus.svg';
                        break;
                }
                marker.setIcon(icon);
            });

            spider.addListener('spiderfy', function (spiderCluster) {
                $scope.$apply(function () {
                    setSelectedMarkers(spiderCluster);
                });
            });

            $scope.$watch('url', function (url) {
                reloadData(url);
            });

            $scope.clearSelection = function () {
                setSelectedMarkers([]);
            };

            addMarkers($scope.orte);

            function reloadData(url) {
                url = url.replace(/limit=\d\d&/g, '');
                url = url + '&georef=true';

                $http({
                    method: 'GET',
                    url: url,
                    headers: {
                        'Content-Type': "text/plain"
                    },
                    transformResponse: [function (data) {
                        return data;
                    }]
                }).then(function success(response) {
                    // TODO wtf? stremasearch is a mess!
                    var result = parseResponse(response);
                    clearMarkers();
                    var orte = result['photos_results'];
                    $scope.total = parseInt(result['photos_total_count']);
                    $scope.totalDisplayed = initiallyDisplayedPreviews;
                    $scope.orte = orte;
                    addMarkers(orte);
                });
            }

            function setSelectedMarkers (value) {
                $scope.selectedMarkers = value;
                $scope.totalDisplayed = initiallyDisplayedPreviews;
            }

            function addMarkers(orte) {
                markers = orte.map(function (ort) {
                    var position = {lat: parseFloat(ort.lat), lng: parseFloat(ort.lon)};

                    var marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        ort: ort,
                        icon: 'assets/img/m/blue.svg'
                    });

                    marker.addListener('spider_click', function () {
                        $scope.$apply(function () {
                            setSelectedMarkers([marker]);
                        });
                    });

                    spider.addMarker(marker);

                    return marker;
                });

                setSelectedMarkers(markers);

                markerCluster = new MarkerClusterer(
                    map,
                    markers,
                    getClusterOptions()
                );

                google.maps.event.addListener(markerCluster, 'clusterclick', function (cluster) {
                    $scope.$apply(function () {
                        setSelectedMarkers(cluster.getMarkers());
                    });
                });

            }

            function clearMarkers() {
                if (markerCluster) {
                    markerCluster.clearMarkers();
                }
                markers = [];
                setSelectedMarkers([]);
                spider.removeAllMarkers();
            }

            function getClusterOptions () {
                var style = [{
                    url: 'assets/img/m/1.png',
                    height: 53,
                    width: 53,
                    anchor: [0, 0],
                    textColor: '#ffffff',
                    textSize: 10
                }, {
                    url: 'assets/img/m/2.png',
                    height: 56,
                    width: 56,
                    anchor: [0, 0],
                    textColor: '#ffffff',
                    textSize: 11
                }, {
                    url: 'assets/img/m/3.png',
                    height: 66,
                    width: 66,
                    anchor: [0, 0],
                    textColor: '#ffffff',
                    textSize: 11
                }, {
                    url: 'assets/img/m/4.png',
                    height: 78,
                    width: 78,
                    anchor: [0, 0],
                    textColor: '#ffffff',
                    textSize: 11
                }, {
                    url: 'assets/img/m/5.png',
                    height: 90,
                    width: 90,
                    anchor: [0, 0],
                    textColor: '#ffffff',
                    textSize: 11
                }];

                return {
                    imagePath: 'assets/img/m/',
                    maxZoom: minClusterZoom,
                    zoomOnClick: false,
                    styles: style
                };
            }

            function updateTotalDisplayed () {
                if ($scope.totalDisplayed < $scope.total) {
                    $scope.totalDisplayed += 20;
                }
            }

            function parseResponse(response) {
                try {
                    var data = response.data.replace(/}{/g, "},{");
                    var result = JSON.parse("[" + data + "]");
                    result = result[result.length - 1];
                    return result
                }
                catch (e) {
                    console.log(e);
                    return [];
                }
            }
        }
    };
}]);

app.controller('MapCtrl', ['$scope', '$http', '$state', '$stateParams', '$rootScope', '$location', 'languages', '$window', function ($scope, $http, $state, $stateParams, $rootScope, $location, languages, $window) {
    // TODO use googleMaps directive here also
    $scope.photos = true;
    $scope.photographer = true;

    $scope.ort = null;
    var selectedMarker;
    var markers = [];
    var markerCluster;

    $scope.textsearch_timeout = false;

    $scope.detail = function (id, type) {
        if (id && type) {
            $rootScope.detail(id, type);
        }
    };

    $scope.textsearchfocus = function () {
        $scope.textsearch_focus = true;
    };

    $scope.textsearchblur = function () {
        if ($scope.searchquery && $scope.searchquery.length > 0) {
            $scope.textsearch_focus = true;
        } else {
            $scope.textsearch_focus = false;
        }
    };

    $scope.textsearch = function (filter) {
        $window.scrollTo(0, 0);
        $scope.filtering = true;
        if ($scope.textsearch_timeout) {
            clearTimeout($scope.textsearch_timeout);
        }
        $scope.textsearch_timeout = setTimeout(function () {
            loadData();
        }, 800);
    };

    $scope.search = function () {
        loadData();
    };

    $scope.reset = function () {
        $scope.searchquery = null;
        $scope.textsearch_focus = false;
    };

    var availableHeight = $(window).height() - $('.filter-bar').height() - $('.l-header').height() - $('.l-footer').height();
    $('#map').css('height', availableHeight + 'px');

    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 8,
        center: {lat: 46.956830, lng: 7.450751}
    });
    map.addListener('click', function () {
        $scope.$apply(function () {
            $scope.ort = null;
            if (selectedMarker) {
                var icon = getIcon(selectedMarker.ort.type);
                selectedMarker.setIcon(icon);
            }
        });
    });

    initGoogleSearchbox();
    loadData();

    function loadData() {
        if (markerCluster) {
            markerCluster.clearMarkers();
        }
        markers = [];

        var textquery = '';
        if (angular.isDefined($scope.searchquery)) {
            textquery = '&query=' + $scope.searchquery;
        }

        var url = $rootScope.ApiUrl +
            '/?a=orte&photographer=' +
            $scope.photographer +
            '&photos=' + $scope.photos
            + textquery;

        $http({
            method: 'GET',
            url: url
        }).then(function success(response) {
            $scope.filtering = false;
            addMarkers(response.data);
        }, function error(response) {
            console.log('error');
            console.log(response);
        });
    }

    function addMarkers(orte) {

        var spider = new OverlappingMarkerSpiderfier(map, {
            markersWontMove: true,
            markersWontHide: true,
            basicFormatEvents: true
        });

        markers = orte.map(function (ort) {
            var position = {lat: parseFloat(ort.lat), lng: parseFloat(ort.lon)};

            var marker = new google.maps.Marker({
                position: position,
                map: map,
                ort: ort,
                icon: getIcon(ort.type)
            });

            marker.addListener('spider_click', function () {
                openPopup(marker);
                if (selectedMarker) {
                    var icon = getIcon(selectedMarker.ort.type);
                    selectedMarker.setIcon(icon);
                }
                selectedMarker = this;
                selectedMarker.setIcon('assets/img/m/highlight.svg');
            });

            spider.addMarker(marker);

            return marker;
        });

        markerCluster = new MarkerClusterer(
            map,
            markers,
            {imagePath: 'assets/img/m/', maxZoom: 15}
        );
    }

    function openPopup(marker) {
        $scope.$apply(function () {
            if (marker.ort.type == 'arbeitsort') {
                getPerioden(marker.ort.id);
            }
            $scope.ort = marker.ort;
        });
    }

    function getPerioden(id) {
        $http.get(
            $rootScope.ApiUrl + '/?a=perioden&id=' + id,
            {cache: true}
        ).success(function (data) {
            $scope.aps = data;
        });
    }

    function getIcon(type) {
        var icon = type == 'foto' ? 'assets/img/m/blue.svg' : 'assets/img/m/red.svg';
        return icon;
    }

    function initGoogleSearchbox() {
        // Create the search box and link it to the UI element.
        var input = document.getElementById('pac-input');
        var searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function () {
            searchBox.setBounds(map.getBounds());
        });

        var searchMarkers = [];
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function () {
            var places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            // Clear out the old searchMarkers.
            searchMarkers.forEach(function (marker) {
                marker.setMap(null);
            });
            searchMarkers = [];

            // For each place, get the icon, name and location.
            var bounds = new google.maps.LatLngBounds();
            places.forEach(function (place) {
                if (!place.geometry) {
                    console.log("Returned place contains no geometry");
                    return;
                }
                var icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25)
                };

                // Create a marker for each place.
                searchMarkers.push(new google.maps.Marker({
                    map: map,
                    icon: icon,
                    title: place.name,
                    position: place.geometry.location
                }));

                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            map.fitBounds(bounds);
        });
    }
}]);
