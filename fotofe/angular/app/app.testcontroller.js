app.controller('TestCtrl', ['$scope', '$http', '$state', '$stateParams', '$rootScope', '$location', 'languages', function ($scope, $http, $state, $stateParams, $rootScope, $location, languages) {
    console.log("Test Controller reporting for duty.");
    $scope.name="";
    $scope.tn="abc";
    $scope.aps=[];
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
    
    $scope.setname=function(n,i,s){
    	this.name=n;
    	this.id=i;
    	this.swissname=s;
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
    		$scope.name=feature.get('name');
    		var id=feature.get('id');
    		$scope.id=id;
    		$scope.swissname=feature.get('swissname');
    		//$scope.setname(g,id,sn);
    	    $http.get($rootScope.ApiUrl + '/?a=perioden&id='+id, { cache: true }).success(function (data) {
    	        //console.log(data);
    	        $scope.aps=data;

    	    });    		
    	    
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
    		  swissname: ort.swissname
    		});
    	iconFeature.setStyle(iconStyle);
    	vectorSource.addFeature(iconFeature);
    	
    };
    
    $http.get($rootScope.ApiUrl + '/?a=orte', { cache: true }).success(function (data) {
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