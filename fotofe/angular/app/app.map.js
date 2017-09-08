app.controller('MapCtrl', ['$scope', '$http', '$state', '$stateParams', '$rootScope', '$location', 'languages', '$window', function ($scope, $http, $state, $stateParams, $rootScope, $location, languages, $window) {
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

  var availableHeight = $('.l-main').prop('offsetHeight') - $('.filter-bar').prop('offsetHeight');
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
