
/**
 * Configure the Routes
 */

	
app.config(['$urlRouterProvider', '$stateProvider', function($urlRouterProvider, $stateProvider) {
 
    $urlRouterProvider.otherwise('/fotograf');
 
    $stateProvider.state('home', {
        url: 'partials/home',
        templateUrl: 'home.html'
    });
    
    $stateProvider.state('fotograf', {
        url: '/fotograf?id&anf',
        templateUrl: 'partials/fotograf.html',
        controller: "FotografCtrl"
    });
    

 
}]);	