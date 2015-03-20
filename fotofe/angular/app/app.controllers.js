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

		    if ($state.includes('aboutFotoch') || $state.includes('contact')) {		// Reload content after switching language
		    	$rootScope.reloadPages();
		    }
		    else if ($state.includes('home')) {
		    	$rootScope.reloadHome();
		    }
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
  $scope.input='';
  $scope.fotographerSelected = function(selected) {
      //window.alert('You have selected ' + selected.originalObject.id);
	  $state.go('fotographerDetail', {id: selected.originalObject.id, anf: ''} );
    };

    $scope.enterFunc = function(selected) {
      var val=document.getElementById('fotographer-autocomplete_value').value;
  	  $state.go('fotographer', {anf: val} );
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

	    $scope.enterFunc = function(selected) {
	      var val=document.getElementById('institution-autocomplete_value').value;
	  	  $state.go('institution', {anf: val} );
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

app.controller('InventoryCtrl', ['$scope', '$http','$location', '$state','$stateParams', '$rootScope', function ($scope, $http, $location, $state, $stateParams, $rootScope ) {
	  console.log("Inventory Controller reporting for duty.");
	  
	  var id=$stateParams.id
	  var anf=$stateParams.anf;
	  $scope.institutionSelected = function(selected) {
	      //window.alert('You have selected ' + selected.originalObject.id);
		  $state.go('inventoryDetail', {id: selected.originalObject.id, anf: ''} );
	    };

	  //$scope.debug='anf:'+anf+' id:'+id+$state;
	  if (anf>='A'){
			$http.get($rootScope.ApiUrl+'/?a=inventory&anf='+anf).success (function(data){
				$scope.list = data;
			});
		} else {
			if (id){
				$http.get($rootScope.ApiUrl+'/?a=inventory&id='+id).success (function(data){
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

app.controller('StaticPageCtrl', ['$scope', '$http','$location', '$state','$stateParams', '$rootScope', function ($scope, $http, $location, $state, $stateParams, $rootScope ) {
  	console.log("Static Page Controller reporting for duty.");
  
	function loadContent(){
	  $http.get($rootScope.ApiUrl+'/?a=pages&lang='+$rootScope.lang).success (function(data){
			$scope.pages = data;
		});
  	}
  	
  	loadContent();

  	$rootScope.reloadPages = function(){
  		loadContent();
  	};
}]);

app.controller('HomeCtrl', ['$scope', '$http','$location', '$state','$stateParams', '$rootScope', function ($scope, $http, $location, $state, $stateParams, $rootScope ) {
  	console.log("Home Controller reporting for duty.");

	function loadContent(){
	  $http.get($rootScope.ApiUrl+'/?a=partner&lang='+$rootScope.lang).success (function(data){
			$scope.partner = data;
		});
  	}
  	
  	loadContent();

  	$rootScope.reloadHome = function(){
  		loadContent();
  	};
}]);

app.controller('LoginCtrl', ['$scope', '$http','$state','$stateParams', '$rootScope', function ($scope, $http, $location, $state, $stateParams, $rootScope ) {
	  console.log("Login Controller reporting for duty.");
	  

	}]);

