
var app = angular.module('fotochWebApp', [
	'ui.router',
    'angular.filter',
	'angucomplete-alt',
	'headroom',
    'yaru22.md',
    'infinite-scroll'
]);

app.run(function($rootScope, $http, $location, languages) {
    $rootScope.user = '';
    $rootScope.userLevel = '';
    $rootScope.authToken = '';
    $rootScope.lang = 'de';
	  var hosta=$location.$$host.split('.');
	  if (hosta[0]=='www') hosta.shift();
	  if (hosta.length>0 && ((l=languages.indexOf(hosta[0]))>=0)){
		  //console.log("GUI-Language from URL-host: "+hosta[0]);
		  $rootScope.lang = hosta[0];
	  }
    
    
    $rootScope.ApiUrl = 'https://www2.foto-ch.ch/api';
    var token=window.sessionStorage.authToken;
    if (token!==undefined){
		  $http.get($rootScope.ApiUrl+'/?a=user&b=info&token='+token).success (function(data){
				var resp = data;
				//console.log(data);
				if (data!==0){
				$rootScope.user = data.user;
				$rootScope.userLevel = parseInt(data.level);
				$rootScope.authToken = token;
				$http.defaults.headers.common['X-AuthToken']=token;
				//$window.sessionStorage.authToken=undefined;
				}

		  });
    }
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

//app.constant('apiUrl','https://www2.foto-ch.ch/api');

app.constant('languages',['de','fr','it','en','rm']);

