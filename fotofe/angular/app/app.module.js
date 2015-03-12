
var app = angular.module('fotochWebApp', [
	'ui.router',
	'angucomplete-alt',
	'headroom',
	'angular-loading-bar'
]);





app.run(function($rootScope) {
    $rootScope.user = '';
    $rootScope.userLevel = '';
    $rootScope.authToken = '';
    $rootScope.lang = 'de';
    $rootScope.ApiUrl = 'https://www2.foto-ch.ch/api';
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

