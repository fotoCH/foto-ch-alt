
/**
 * Configure the Routes
 */

	
app.config(['$urlRouterProvider', '$stateProvider', function($urlRouterProvider, $stateProvider) {
 
    $urlRouterProvider.otherwise('/home');
 
    $stateProvider.state('home', {
        url: '/home',
        templateUrl: 'partials/home.html'
    });
    
    $stateProvider.state('fotograf', {
        url: '/fotograf?id&anf',
        templateUrl: 'partials/fotograf.html',
        controller: "FotografCtrl"
    });
    

 
}]);	