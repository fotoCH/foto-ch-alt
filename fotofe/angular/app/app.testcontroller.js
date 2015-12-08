app.controller('TestCtrl', ['$scope', '$http', '$state', '$stateParams', '$rootScope', '$location', 'languages', function ($scope, $http, $state, $stateParams, $rootScope, $location, languages) {
    console.log("Test Controller reporting for duty.");
    $scope.name="";
    $scope.tn="abc";
    hosta = $location.$$host.split('.');
    if (hosta[0] == 'www') hosta.shift();
    if (hosta.length > 0 && ((l = languages.indexOf(hosta[0])) >= 0)) {
        console.log("GUI-Language from URL-host: " + hosta[0]);
    }
    var layer = ga.layer.create('ch.swisstopo.pixelkarte-farbe');
    var vectorSource = new ol.source.Vector({
  	  features: []
  	});
    //var wgs84 =  new OpenLayers.Projection("EPSG:4326");
  	var vectorLayer = new ol.layer.Vector({
  	  source: vectorSource
  	});
    
    $scope.map = new ga.Map({
      target: 'map',
      layers: [layer, vectorLayer],
      view: new ol.View({
        resolution: 500,
        center: [670000, 160000]
      })
    });
    
    $scope.setname=function(n){
    	this.name=n;
    	console.log(n);
    }
    
    var iconStyle = new ol.style.Style({
    	  image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
    	    anchor: [0.5, 1],
    	    anchorXUnits: 'fraction',
    	    anchorYUnits: 'fraction',
    	    opacity: 0.75,
    	    src: 'assets/img/marker.png'
    	  }))
    	});
    
    $scope.displayFeatureInfo = function(e) {
    		pixel=[e.offsetX,e.offsetY];
    		console.log(pixel);
    		
    	  var feature = $scope.map.forEachFeatureAtPixel(pixel, function(feature, layer) {
    	    return feature;
    	  });

    	  //var info = document.getElementById('info');
    	  if (feature) {
    		var g=feature.get('name');
    		var id=feature.get('id');
    		$scope.setname(g);
    	    
    	  } else {
    	    //info.innerHTML = '&nbsp;';
    	  }

    	};

    /*$scope.map.on('click', function(evt) {
    	  displayFeatureInfo(evt.pixel);
    	});*/
    
    addmarker=function(ort){
    	var iconFeature = new ol.Feature({
    		  geometry: new ol.geom.Point(ol.proj.transform([0+ort.lon, 0+ort.lat], 'EPSG:4326', 'EPSG:21781')),
    		  name: ort.name,
    		  id: ort.id,
    		});
    	iconFeature.setStyle(iconStyle);
    	vectorSource.addFeature(iconFeature);
    	
    };
    
    $http.get($rootScope.ApiUrl + '/?a=orte', { cache: false }).success(function (data) {
        //console.log(data);
    	angular.forEach(data, function(value, key) {
    		  //console.log(value.id+" "+value.swissname);
    		  addmarker(value);
    		});
    });
    console.log(ol.proj.transform([47.2, 7.5], 'EPSG:4326', 'EPSG:21781'));
    console.log(ol.proj.transform([670000, 160000], 'EPSG:21781', 'EPSG:4326'));
    //map.geocode('');
    //console.log($location.$$host);
    //console.log(languages);
    // console.log($scope);
}]);