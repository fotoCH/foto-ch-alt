
/**
 * Configure the Routes
 */

	
app.config(['$urlRouterProvider', '$stateProvider', function($urlRouterProvider, $stateProvider) {
 
    $urlRouterProvider.otherwise('/home');
 
    $stateProvider.state('home', {
        url: '/home',
        templateUrl: 'app/components/home/home.html'
    });
    
    $stateProvider.state('fotographer', {
        url: '/fotographer?id&anf',
        templateUrl: 'app/components/fotographer/fotographer.html',
        controller: "FotographerCtrl"
    });
    
    $stateProvider.state('test', {
        url: '/test',
        templateUrl: 'app/components/test/test.html'
    });
 

}]);	