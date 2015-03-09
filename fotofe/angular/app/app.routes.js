
/**
 * Configure the Routes
 */

	
app.config(['$urlRouterProvider', '$stateProvider', function($urlRouterProvider, $stateProvider) {
 
    $urlRouterProvider.otherwise('/home');
 
    $stateProvider
        // Homepage
        .state('home', {
            url: '/home',
            templateUrl: 'app/components/home/home.html'
        })
        
        // Fotographer search view
        .state('fotographer', {
            url: '/fotographer?anf',
            templateUrl: 'app/components/fotographer/fotographer.html',
            controller: "FotographerCtrl"
        })

        // Fotographer detail view
        .state('fotographerDetail', {
            url: '/fotographer/detail?id',
            templateUrl: 'app/components/fotographer/fotographerDetail.html',
            controller: "FotographerCtrl"
        })
        // Testpage for development
        .state('login', {
            url: '/login',
            templateUrl: 'app/components/login/login.html',
            controller: "LoginCtrl"
        })

        // Testpage for development
        .state('test', {
            url: '/test',
            templateUrl: 'app/components/test/test.html',
            controller: "LoginCtrl"
        });
 

}]);	