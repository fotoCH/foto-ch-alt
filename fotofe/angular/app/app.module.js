
var app = angular.module('fotochWebApp', [
	'ui.router',
	'angucomplete-alt'
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
app.controller('BlogCtrl', function (/* $scope, $location, $http */) {
	  console.log("Blog Controller reporting for duty.");
	});

app.controller('MainCtrl', ['$scope', '$http', '$state','$stateParams', '$rootScope', function ($scope, $http, $state, $stateParams, $rootScope ) {
	  console.log("Main Controller reporting for duty.");
	  
	  function loadTranslation(){
		  $http.get($rootScope.ApiUrl+'/?a=sprache&lang='+$rootScope.lang).success (function(data){
				$scope.spr = data;
			});
	  }
	  
	  loadTranslation();
	  
	  $scope.setLanguage = function(lang) {
		    console.log('switch to language', lang);
		    $rootScope.lang = lang;
		    loadTranslation();

	  };
		
	  $scope.getLclass = function(lang) {
		    if ($rootScope.lang == lang) {
		      return "is-active"
		    } else {
		      return ""
		    }
	  };
	  
	}]);


app.controller('NavigationCtrl', ['$scope', '$location', function ($scope, $location) {
  console.log("Navigation Controller reporting for duty.");
  $scope.getClass = function(path) {
	    if ($location.path().substr(0, path.length) == path) {
	      return "is-active"
	    } else {
	      return ""
	    }
	};
	$scope.toggleMobileMenu = function(){
		$scope.isMenuOpen = !$scope.isMenuOpen;
	};
	$scope.isMenuOpen = false;
}]);

app.controller('FotographerCtrl', ['$scope', '$http','$location', '$state','$stateParams', '$rootScope', function ($scope, $http, $location, $state, $stateParams, $rootScope ) {
  console.log("Fotograf Controller reporting for duty.");
  
  var id=$stateParams.id
  var anf=$stateParams.anf;
  $scope.fotographerSelected = function(selected) {
      //window.alert('You have selected ' + selected.originalObject.id);
	  $state.go('fotographerDetail', {id: selected.originalObject.id, anf: ''} );
    };

  //$scope.debug='anf:'+anf+' id:'+id+$state;
  if (anf>='A'){
		$http.get($rootScope.ApiUrl+'/?anf='+anf).success (function(data){
			$scope.list = data;
		});
	} else {
		if (id){
			$http.get($rootScope.ApiUrl+'/?id='+id).success (function(data){
				$scope.detail = data;
				$scope.list=null;
			});
		}
	}
  var abc=new Array();
  
  for ( var i=0; i<26; i++){
      abc[i]=String.fromCharCode(65+i);
  }
  $scope.abc=abc;

  


}]);

app.controller('LoginCtrl', ['$scope', '$http','$state','$stateParams', '$rootScope', function ($scope, $http, $location, $state, $stateParams, $rootScope ) {
	  console.log("Login Controller reporting for duty.");
	  

	}]);


app.filter('rawHtml', ['$sce', function($sce){
	  return function(val) {
	    return $sce.trustAsHtml(val);
	  };
	}]);

app.directive('stField', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/fields/stField.html',
		    	scope: {
		    		  label: '=label',
		    		  value: '=value'
		    		},
		  };
		});

app.directive('stFieldRaw', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/fields/stFieldRaw.html',
		    	scope: {
		    		  label: '=label',
		    		  value: '=value'
		    		},
		  };
		});

app.run(function($rootScope) {
    $rootScope.user = '';
    $rootScope.userLevel = '';
    $rootScope.authToken = '';
    $rootScope.lang = 'de';
    $rootScope.ApiUrl = 'http://www2.foto-ch.ch/api';
})

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

