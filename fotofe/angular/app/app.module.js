
var app = angular.module('fotochWebApp', [
	'ui.router',
	'angucomplete-alt',
	'headroom',
    'yaru22.md'
]);

app.run(function($rootScope, $http) {
    $rootScope.user = '';
    $rootScope.userLevel = '';
    $rootScope.authToken = '';
    $rootScope.lang = 'de';
    $rootScope.ApiUrl = 'https://www2.foto-ch.ch/api';
    var token=window.sessionStorage.authToken;
    if (token!==undefined){
		  $http.get($rootScope.ApiUrl+'/?a=user&b=info&token='+token).success (function(data){
				var resp = data;
				console.log(data);
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

