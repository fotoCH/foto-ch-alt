app.controller('MapCtrl', ['$scope', '$http', '$state', '$stateParams', '$rootScope', '$location', 'languages', '$window', function ($scope, $http, $state, $stateParams, $rootScope, $location, languages, $window) {

    // http://openlayers.org/en/latest/apidoc/index.html | just in case...

    $scope.name = "";
    $scope.ch = $stateParams.ch;
    $scope.photo = $stateParams.photo;
    $scope.land = $stateParams.land;
    $scope.kanton = $stateParams.kanton;
    $scope.aps = [];


    $scope.textsearch_timeout = false;
    $scope.textquery = '';
    $scope.photographer = true;
    $scope.photos = true;

    $scope.distance = 10;

    $scope.detail = function(id, type){
        if(id && type){
            $rootScope.detail(id,type);
        }
    };

    $scope.textsearchfocus = function() {
        $scope.textsearch_focus = true;
    };

    $scope.textsearchblur = function() {
        if($scope.textquery.length > 0) {
            $scope.textsearch_focus = true;
        } else {
            $scope.textsearch_focus = false;
        }
    };

    $scope.textsearch = function(filter) {
        $window.scrollTo(0, 0);
        $scope.filtering = true;
        $scope.textquery = filter;
        $scope.queryOffset = 0;
        if($scope.textsearch_timeout) {
            clearTimeout($scope.textsearch_timeout);
        }
        $scope.tableRows = [];
        $scope.textsearch_timeout = setTimeout(function() {
            $scope.queryOffset = 0;
            loadData();
        }, 800);
    };

    $scope.search = function () {
        loadData();
    };



    hosta = $location.$$host.split('.');

    var layer = new ol.layer.Tile({
        source: new ol.source.OSM()
    });
    var vectorSource = new ol.source.Vector({
        features: []
    });
    //var wgs84 =  new OpenLayers.Projection("EPSG:4326");
    var vectorLayer = new ol.layer.Vector({
        source: vectorSource
    });

    var clusterSource = new ol.source.Cluster({
        distance: parseInt($scope.distance, 10),
        source: vectorSource
    });

    var styleCache = {};
    var clusters = new ol.layer.Vector({
        source: clusterSource,
        style: function(feature) {
            var size = feature.get('features').length;
            var style = styleCache[size];
            if (!style) {
                style = new ol.style.Style({
                    image: new ol.style.Circle({
                        radius: 10,
                        stroke: new ol.style.Stroke({
                            color: '#fff'
                        }),
                        fill: new ol.style.Fill({
                            color: '#3399CC'
                        })
                    }),
                    text: new ol.style.Text({
                        text: size.toString(),
                        fill: new ol.style.Fill({
                            color: '#fff'
                        })
                    })
                });
                styleCache[size] = style;
            }
            return style;
        }
    });


    var availableHeight = document.getElementsByClassName('l-main')[0].offsetHeight - document.getElementsByClassName('filter-bar')[0].offsetHeight;
    if(availableHeight > 400){
        angular.element(document.getElementsByClassName('map')).css('height', availableHeight + 'px');
    }

    $scope.map = new ol.Map({
        target: 'map',
        layers: [layer, vectorLayer],
        view: new ol.View({
            zoom: 7,
            center: ol.proj.transform(
                [8.231788635253906, 46.79851150512695], 'EPSG:4326', 'EPSG:3857')
        })
    });
    $scope.prj = 'EPSG:3857';


    var iconStylePhotographer = new ol.style.Style({
        image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
            anchor: [0.5, 1],
            anchorXUnits: 'fraction',
            anchorYUnits: 'fraction',
            opacity: 0.75,
            src: 'assets/img/marker.png'
        }))
    });

    var iconStylePhoto = new ol.style.Style({
        image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
            anchor: [0.5, 1],
            anchorXUnits: 'fraction',
            anchorYUnits: 'fraction',
            opacity: 0.75,
            src: 'assets/img/marker-blue.png'
        }))
    });

    $scope.displayFeatureInfo = function (e) {
        pixel = [e.offsetX, e.offsetY];

       /* $scope.posTop = pixel[0] + 'px';
        $scope.posLeft = pixel[1] + 'px';*/

        var feature = $scope.map.forEachFeatureAtPixel(pixel, function (feature, layer) {
            return feature;
        });

        if (feature) {
            $scope.name = feature.get('name');
            var id = feature.get('id');
            $scope.id = id;
            $scope.swissname = feature.get('swissname');
            if ($scope.swissname == 'fotoquery') {
                $scope.type = 'photo';
                $scope.image_path = feature.get('image_path');
                $scope.copyr = feature.get('dc_right');
            } else {
                $scope.type = 'photographer';
                $http.get($rootScope.ApiUrl + '/?a=perioden&id=' + id, {cache: true}).success(function (data) {
                    $scope.aps = data;
                });
            }
        } else {
            // kein Marker an dieser Stelle --> reset
            $scope.type = '';
        }
    };

    addmarker = function (ort) {
        var randx = 0.0;
        var randy = 0.0;
        if (ort.swissname == 'fotoquery') {
            //randx=Math.random()/250;
            //randy=Math.random()/500;
            var x = Math.sin(ort.id) * 10000;
            randx = (x - Math.floor(x)) / 250;
            x = Math.sin(ort.id) * 11111;
            randy = (x - Math.floor(x)) / 500;
        }

        var iconFeature = new ol.Feature({
            //geometry: new ol.geom.Point(ol.proj.transform([0+ort.lon, 0+ort.lat], 'EPSG:4326', 'EPSG:21781')),
            //geometry: new ol.geom.Point(ol.proj.transform([0+ort.lon, 0+ort.lat], 'EPSG:4326', 'EPSG:3857')),

            geometry: new ol.geom.Point(ol.proj.transform([+ort.lon + randx, +ort.lat + randy], 'EPSG:4326', $scope.prj)),

            name: ort.name,
            id: ort.id,
            swissname: ort.swissname,
            image_path: ort.image_path,
            dc_right: ort.dc_right

        });
        //console.log(ort.lon,+ort.lon);
        if (ort.swissname != 'fotoquery')
            iconFeature.setStyle(iconStylePhotographer);
        else
            iconFeature.setStyle(iconStylePhoto);

        vectorSource.addFeature(iconFeature);

    };
    var phot = "";
    var land = "";
    var kanton = "";
    if ($scope.photo == 1) {
        console.log('ja');
        phot = "&photos=1";
    }
    if ($scope.land != undefined) {
        land = "&land=" + $scope.land;
        phot = "&photo=0";
    }
    if ($scope.kanton != undefined) {
        kanton = "&kanton=" + $scope.kanton;
        phot = "";
        phot = "&photo=0";
    }

    /*
    $http.get($rootScope.ApiUrl + '/?a=orte' + phot + land + kanton, {cache: true}).success(function (data) {

    });*/

    var clearMap = function() {
        vectorSource.clear();
    };

    var loadData = function (append) {

        $scope.result = false;
        $scope.aps = {};

        var textquery = '';
        $rootScope.textualSearch = $scope.textquery;
        if($scope.textquery != '') {
            textquery = '&query='+$scope.textquery;
        }
        $scope.query = $rootScope.ApiUrl +
            '/?a=orte&photographer=' + $scope.photographer + '&photos=' + $scope.photos + textquery;

        $http({
            method: "GET",
            url: $scope.query,
            headers: {
                'Content-Type': "text/plain"
            },
            transformResponse: [function (data) {
                return data;
            }],
            onProgress: function(event) {
                try {

                    clearMap();
                    var response = event.currentTarget.responseText;
                    response = response.replace(/}{/g, "},{");
                    response = "[" + response + "]";
                    var newresult = JSON.parse(response);
                    newresult = newresult[newresult.length-1];
                    setValues(newresult);
                } catch (e) {
                    console.log(e);
                }
            }
        }).then(function(e) {
            $scope.filtering = false;
            //$rootScope.loadednum = $scope.tableRows.length;
        });
    };

    var setValues = function (newresult) {
        $scope.result = newresult;
        angular.forEach($scope.result, function (value, key) {
            addmarker(value);
        });
    };


    loadData();




    //map.geocode('');
    //console.log($location.$$host);
    //console.log(languages);
    // console.log($scope);
}]);