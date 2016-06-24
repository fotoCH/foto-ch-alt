
/**
 * Configure the Routes
 */

	
app.config(
    ['$urlRouterProvider', '$stateProvider', '$locationProvider', '$analyticsProvider',
    function($urlRouterProvider, $stateProvider, $locationProvider, $analyticsProvider) {

    $urlRouterProvider.otherwise('/home');
 
    $stateProvider
    // Homepage
    .state('home', {
        url: '/home',
        templateUrl: 'app/components/views/home.html',
        controller: "HomeCtrl"
    })
    // Photographer search view
    .state('photographer', {
        url: '/photographer',
        templateUrl: 'app/components/views/photographer.html',
        controller: 'PhotographerCtrl'
    })

    // Institution search view
    .state('institution', {
        url: '/institution',
        templateUrl: 'app/components/views/institution.html',
        controller: 'InstitutionCtrl'
    })
    // Exhibition search view
    .state('exhibition', {
        url: '/exhibition',
        templateUrl: 'app/components/views/exhibition.html',
        controller: 'ExhibitionCtrl'
    })
    // Inventory search view
    .state('inventory', {
        url: '/inventory',
        templateUrl: 'app/components/views/inventory.html',
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
    .state('profile', {
        url: '/profile',
        templateUrl: 'app/components/meta/profile.html',
        controller: 'ProfileCtrl'
    })
    .state('update-requests', {
        url: '/pending-requests',
        templateUrl: 'app/components/meta/requests.html',
        controller: 'PendingCtrl'
    })
    // Testpage for development
    .state('test', {
        url: '/test?ch&photo&kanton&land',
        templateUrl: 'app/components/test/test.html',
        controller: 'TestCtrl'
    })
    // Fotoportal
    .state('photo', {
        url: '/photo',
        templateUrl: 'app/components/views/photo.html',
        controller: 'PhotoCtrl'
    })
    // Literatur
    .state('literatur', {
        url: '/literatur',
        templateUrl: 'app/components/views/literatur.html',
        controller: 'LiteraturCtrl'
    })
    // Literatur
    .state('timeline', {
        url: '/timeline',
        templateUrl: 'app/components/views/timeline.html',
        controller: 'TimelineCtrl'
    })
}]);