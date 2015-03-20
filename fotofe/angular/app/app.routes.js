
/**
 * Configure the Routes
 */

	
app.config(['$urlRouterProvider', '$stateProvider', function($urlRouterProvider, $stateProvider) {
 
    $urlRouterProvider.otherwise('/home');
 
    $stateProvider
        // Homepage
        .state('home', {
            url: '/home',
            templateUrl: 'app/components/home/home.html',
            controller: "HomeCtrl"
        })
        
        // Photographer search view
        .state('photographer', {
            url: '/photographer?anf',
            templateUrl: 'app/components/photographer/photographer.html',
            controller: 'PhotographerCtrl'
        })

        // Photographer detail view
        .state('photographerDetail', {
            url: '/photographer/detail?id',
            templateUrl: 'app/components/photographer/photographerDetail.html',
            controller: 'PhotographerCtrl'
        })
        // Institution search view
        .state('institution', {
            url: '/institution?anf',
            templateUrl: 'app/components/institution/institution.html',
            controller: 'InstitutionCtrl'
        })
        // Institution detail view
        .state('institutionDetail', {
            url: '/institution/detail?id',
            templateUrl: 'app/components/institution/institutionDetail.html',
            controller: 'InstitutionCtrl'
        })
        // Inventory detail view
        .state('inventoryDetail', {
            url: '/inventory/detail?id',
            templateUrl: 'app/components/inventory/inventoryDetail.html',
            controller: 'InventoryCtrl'
        })
        // Contact page
        .state('contact', {
            url: '/contact',
            templateUrl: 'app/components/meta/contact.html',
            controller: 'StaticPageCtrl'
        })
        // About fotoCH
        .state('aboutFotoch', {
            url: '/aboutfotoch',
            templateUrl: 'app/components/meta/aboutFotoch.html',
            controller: 'StaticPageCtrl'
        })
        // Testpage for development
        .state('login', {
            url: '/login',
            templateUrl: 'app/components/meta/login.html',
            controller: 'LoginCtrl'
        })
        // Testpage for development
        .state('test', {
            url: '/test',
            templateUrl: 'app/components/test/test.html',
            controller: 'LoginCtrl'
        });
 

}]);	