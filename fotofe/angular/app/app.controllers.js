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

app.controller('PhotographerCtrl', ['$scope', '$http','$location', '$state','$stateParams', '$rootScope', function ($scope, $http, $location, $state, $stateParams, $rootScope ) {
  console.log("Fotograf Controller reporting for duty.");
  
  var id=$stateParams.id
  var anf=$stateParams.anf;
  $scope.input='';
  $scope.photographerSelected = function(selected) {
      //window.alert('You have selected ' + selected.originalObject.id);
	  $state.go('photographerDetail', {id: selected.originalObject.id, anf: ''} );
    };

    $scope.enterFunc = function(selected) {
      var val=document.getElementById('photographer-autocomplete_value').value;
  	  $state.go('photographer', {anf: val} );
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
	  $http.get($rootScope.ApiUrl+'/?recent=10').success (function(data){
			$scope.recent = data;
		});
  	}
  	
  	loadContent();

  	$rootScope.reloadHome = function(){
  		loadContent();
  	};
}]);

app.controller('LoginCtrl', ['$scope', '$http','$state','$stateParams', '$rootScope', '$window', function ($scope, $http, $state, $stateParams, $rootScope, $window ) {
	  console.log("Login Controller reporting for duty.");
	  console.log($scope);
		$scope.doLogin = function (user){
			console.log("$scope.doLogin");
			  $http.get($rootScope.ApiUrl+'/?a=user&b=login&user='+user.username+'&password='+user.password).success (function(data){

					var status=data.status;
					
					if (status=='ok'){
						$scope.errorMsg='Login ok '+$scope.spr.welcome+' '+data.vorname+' '+data.nachname;
					    $rootScope.user = user.username;
					    $rootScope.userLevel = parseInt(data.level);
					    $rootScope.authToken = data.token;
					    $window.sessionStorage.authToken=data.token;
					    $http.defaults.headers.common['X-AuthToken']=$rootScope.authToken ;
					} else {
						$scope.errorMsg='Bad login';
					}
					
				});
		}
				$scope.doLogout = function (){
					console.log("$scope.doLogout");
					  $http.get($rootScope.ApiUrl+'/?a=user&b=logout').success (function(data){
						var resp = data;

						$rootScope.user = '';
						$rootScope.userLevel = '';
						$rootScope.authToken = '';
						$http.defaults.headers.common['X-AuthToken']=undefined;
						$window.sessionStorage.authToken=undefined;
							
				});
		}
}]);

