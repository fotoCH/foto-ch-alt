app.controller('TestCtrl', ['$scope', '$http', '$state', '$stateParams', '$rootScope', '$location', 'languages', function ($scope, $http, $state, $stateParams, $rootScope, $location, languages) {
    console.log("Test Controller reporting for duty.");
    $scope.name="";
    $scope.ch=$stateParams.ch;
    $scope.photo=$stateParams.photo;
    $scope.land=$stateParams.land;
    $scope.kanton=$stateParams.kanton;
    $scope.aps=[];
    hosta = $location.$$host.split('.');
    var layer = ga.layer.create('ch.swisstopo.pixelkarte-farbe');
    var layer2 =new ol.layer.Tile({
      source: new ol.source.OSM()
    });
    var vectorSource = new ol.source.Vector({
  	  features: []
  	});
    //var wgs84 =  new OpenLayers.Projection("EPSG:4326");
  	var vectorLayer = new ol.layer.Vector({
  	  source: vectorSource
  	});
    
  	if ($scope.ch=='1'){
  		$scope.map = new ga.Map({
  	      target: 'map',
  	      layers: [layer, vectorLayer],
  	      view: new ol.View({
  	        resolution: 500,
  	        center: [670000, 160000]
  	      })
  	    });
  		$scope.prj='EPSG:21781';
  	} else {
  	    $scope.map = new ol.Map({
  	      target: 'map',
  	      layers: [layer2, vectorLayer],
  	      view: new ol.View({
  	        //resolution: 500,
  	    	  zoom: 7,
  	        center: ol.proj.transform(
  	                [8.231788635253906,46.79851150512695], 'EPSG:4326', 'EPSG:3857')
  	      })
  	    });
  	  $scope.prj='EPSG:3857';
  		
  	}

    $scope.changemap=function(n){
    	$state.go('test', {ch: n});
    }
    $scope.changephoto=function(n){
    	$state.go('test', {photo: n});
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

    var iconStyleB = new ol.style.Style({
    	  image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
    	    anchor: [0.5, 1],
    	    anchorXUnits: 'fraction',
    	    anchorYUnits: 'fraction',
    	    opacity: 0.75,
    	    src: 'assets/img/marker-blue.png'
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
    		if ($scope.swissname=='fotoquery'){
    		    $scope.image_path=feature.get('image_path');
    		    $scope.copyr=feature.get('dc_right');
    		} else {
    		//$scope.setname(g,id,sn);
    	    $http.get($rootScope.ApiUrl + '/?a=perioden&id='+id, { cache: true }).success(function (data) {
    	        //console.log(data);
    	        $scope.aps=data;
    	        
	    
    	    });    		
    	    }
    	  } else {
    	    //info.innerHTML = '&nbsp;';
    	  }

    	};

    /*$scope.map.on('click', function(evt) {
    	  displayFeatureInfo(evt.pixel);
    	});*/
    
    addmarker=function(ort){
	var randx=0.0;
	var randy=0.0;
    	if (ort.swissname=='fotoquery'){
    	    //randx=Math.random()/250;
    	    //randy=Math.random()/500;
    	    var x=Math.sin(ort.id) * 10000;
    	    randx=(x - Math.floor(x))/250;
    	    x=Math.sin(ort.id) * 11111;
    	    randy=(x - Math.floor(x))/500;
    	}
    
    	var iconFeature = new ol.Feature({
    		  //geometry: new ol.geom.Point(ol.proj.transform([0+ort.lon, 0+ort.lat], 'EPSG:4326', 'EPSG:21781')),
    		  //geometry: new ol.geom.Point(ol.proj.transform([0+ort.lon, 0+ort.lat], 'EPSG:4326', 'EPSG:3857')),
    		  
    		geometry: new ol.geom.Point(ol.proj.transform([+ort.lon+randx, +ort.lat+randy], 'EPSG:4326', $scope.prj)),
    		
    		  name: ort.name,
    		  id: ort.id,
    		  swissname: ort.swissname,
    		  image_path: ort.image_path,
    		  dc_right: ort.dc_right
    		  
    		});
    	//console.log(ort.lon,+ort.lon);
    	if (ort.swissname!='fotoquery')
    	    iconFeature.setStyle(iconStyle);
    	else
    	    iconFeature.setStyle(iconStyleB);
    	
    	vectorSource.addFeature(iconFeature);
    	
    };
    var phot="";
    var land="";
    var kanton="";
    if ($scope.photo==1){
	phot="&photos=1";
    }
    if ($scope.land!=undefined){
	land="&land="+$scope.land;
	phot="&photo=0";
    }
    if ($scope.kanton!=undefined){
	kanton="&kanton="+$scope.kanton;
	phot="";
	phot="&photo=0";
    }
    
    $http.get($rootScope.ApiUrl + '/?a=orte'+phot+land+kanton, { cache: true }).success(function (data) {
        //console.log(data);
    	angular.forEach(data, function(value, key) {
    		  //console.log(value.id+" "+value.swissname);
    		  addmarker(value);
    		});
    });

    //map.geocode('');
    //console.log($location.$$host);
    //console.log(languages);
    // console.log($scope);
}]);