/**
 * Controllers
 */

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
		    $rootScope.isLangSwitchOpen = false;	// Close the language switch after selection of new language
		    $rootScope.isMenuOpen = false;			// Close the mobile menu after selection of new language
		    loadTranslation();
	  };
		
	  $scope.getLclass = function(lang) {
		    if ($rootScope.lang == lang) {
		      return "is-active"
		    } else {
		      return ""
		    }
	  };
	  
	  $scope.toggleMobileMenu = function() {
			$rootScope.isMenuOpen = !$rootScope.isMenuOpen;
	  };
	  $rootScope.isMenuOpen = false;

	  $scope.toggleLangSwitch = function() {
			$rootScope.isLangSwitchOpen = !$rootScope.isLangSwitchOpen;
	  };
	  $rootScope.isLangSwitchOpen = false;

	  // Close mobile menu on state change
	  $rootScope.$on('$stateChangeSuccess', 
	  		function(){
	  			$rootScope.isMenuOpen = false;
	  });
	}]);

app.controller('NavigationCtrl', ['$scope', '$location', '$rootScope', function ($scope, $location, $rootScope) {
  console.log("Navigation Controller reporting for duty.");
  $scope.getClass = function(path) {
	    if ($location.path().substr(0, path.length) == path) {
	      return "is-active"
	    } else {
	      return ""
	    }
	};
	
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


app.controller('InstitutionCtrl', ['$scope', '$http','$location', '$state','$stateParams', '$rootScope', function ($scope, $http, $location, $state, $stateParams, $rootScope ) {
	  console.log("Institution Controller reporting for duty.");
	  
	  var id=$stateParams.id
	  var anf=$stateParams.anf;
	  $scope.institutionSelected = function(selected) {
	      //window.alert('You have selected ' + selected.originalObject.id);
		  $state.go('institutionDetail', {id: selected.originalObject.id, anf: ''} );
	    };

	  //$scope.debug='anf:'+anf+' id:'+id+$state;
	  if (anf>='A'){
			$http.get($rootScope.ApiUrl+'/?a=institution&anf='+anf).success (function(data){
				$scope.list = data;
			});
		} else {
			if (id){
				$http.get($rootScope.ApiUrl+'/?a=institution&id='+id).success (function(data){
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

