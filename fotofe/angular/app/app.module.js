
var app = angular.module('fotochWebApp', [
    'ui.router',
    'ngAnimate',
    'angular.filter',
    'angucomplete-alt',
    'headroom',
    'yaru22.md',
    'infinite-scroll',
    'ui-rangeSlider',
    'ui.bootstrap',
    'ui.bootstrap.modal',
    'ui.bootstrap.popover',
    'ngSanitize',
    'ngCookies'
]);

app.run(function($rootScope, $http, $location, $q, languages, $cacheFactory, $cookies) {
    $rootScope.user = '';
    $rootScope.userLevel = '';
    $rootScope.authToken = '';
    if(! $cookies.get('lang')) {
        $rootScope.lang = 'de';
        $cookies.put('lang', 'de');
    } else {
        $rootScope.lang = $cookies.get('lang');
    }
    $rootScope.imageRootUrl = 'https://www2.foto-ch.ch/';
    $rootScope.filterCache = $cacheFactory('filterCache');

    var hosta=$location.$$host.split('.');
    if (hosta[0]=='www') hosta.shift();
    if (hosta.length>0 && ((l=languages.indexOf(hosta[0]))>=0)){
      //console.log("GUI-Language from URL-host: "+hosta[0]);
      $rootScope.lang = hosta[0];
    }
    
    // Development Server API URL
    $rootScope.ApiUrl = 'http://localhost/fotoch/api';

    // Production Server API URL
    //$rootScope.ApiUrl = 'https://www2.foto-ch.ch/api';

    
    var token=window.sessionStorage.authToken;
    $rootScope.userInfoCall = $q.defer();
    if ((token!==undefined) && ($rootScope.authToken != token)){
        $http.get($rootScope.ApiUrl+'/?a=user&b=info&token='+token).success (function(data){
            var resp = data;
            //console.log(data);
            if (data!==0){
                $rootScope.user = data.user;
                $rootScope.userLevel = parseInt(data.level);
                $rootScope.instComment = parseInt(data.inst_comment);
                $rootScope.authToken = token;
                $http.defaults.headers.common['X-AuthToken']=token;
                console.log("autorelogin");
                $rootScope.userInfoCall.resolve();
                //$window.sessionStorage.authToken=undefined;
            }
        });
    } else {
        $rootScope.userInfoCall.resolve();
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

