
var app = angular.module('fotochWebApp', [
  'ngRoute'
]);


/**
 * Controls the Blog
 */
app.controller('BlogCtrl', function (/* $scope, $location, $http */) {
  console.log("Blog Controller reporting for duty.");
});

/**
 * Controls all other Pages
 */
app.controller('PageCtrl', function (/* $scope, $location, $http */) {
  console.log("Page Controller reporting for duty.");

  // Activates the Carousel
  $('.carousel').carousel({
    interval: 500
  });

  // Activates Tooltips for Social Links
  $('.tooltip-social').tooltip({
    selector: "a[data-toggle=tooltip]"
  });
});

app.controller('FotografCtrl', ['$scope', '$http','$location', function ($scope, $http, $location ) {
  console.log("Fotograf Controller reporting for duty.");
        var urlBase = 'http://www2.foto-ch.ch/api';
        
        var id=$location.search().id;
        var anf=$location.search().anf;
        $http.get(urlBase+'/?a=sprache').success (function(data){
			$scope.spr = data;
		});
        $scope.debug=anf;
        if (anf>='A'){
			$http.get(urlBase+'/?anf='+anf).success (function(data){
				$scope.list = data;
			});
		} else {
			$http.get(urlBase+'/?id='+id).success (function(data){
				$scope.detail = data;
				$scope.list=null;
			});
		}
        var abc=new Array();
        
        for ( var i=0; i<26; i++){
            abc[i]=String.fromCharCode(65+i);
        }
        $scope.abc=abc;

  


}]);

app.filter('rawHtml', ['$sce', function($sce){
	  return function(val) {
	    return $sce.trustAsHtml(val);
	  };
	}]);

app.directive('stField', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'partials/stField.html',
		    	scope: {
		    		  label: '=label',
		    		  value: '=value'
		    		},
		  };
		});

app.directive('stFieldRaw', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'partials/stFieldRaw.html',
		    	scope: {
		    		  label: '=label',
		    		  value: '=value'
		    		},
		  };
		});

app.service('fotochService', ['$http', function ($http) {

    var urlBase = 'http://www2.foto-ch.ch/api';

    this.getFotografs = function (anf) {
        return $http.get(urlBase+'?anf='+anf);
    };

    this.getFotograf = function (id) {
        return $http.get(urlBase + '?id=' + id);
    };

    this.getLang = function () {
        return $http.get(urlBase + '?a=sprache');
    };

}]);

