
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
            url: '/photographer?anf&query',
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
            url: '/institution?anf&query',
            templateUrl: 'app/components/institution/institution.html',
            controller: 'InstitutionCtrl'
        })
        // Institution detail view
        .state('institutionDetail', {
            url: '/institution/detail?id',
            templateUrl: 'app/components/institution/institutionDetail.html',
            controller: 'InstitutionCtrl'
        })
        // Exhibition search view
        .state('exhibition', {
            url: '/exhibition?anf',
            templateUrl: 'app/components/exhibition/exhibition.html',
            controller: 'ExhibitionCtrl'
        })
        // Institution detail view
        .state('exhibitionDetail', {
            url: '/exhibition/detail?id',
            templateUrl: 'app/components/exhibition/exhibitionDetail.html',
            controller: 'ExhibitionCtrl'
        })
        // Inventory search view
        .state('inventory', {
            url: '/inventory?anf',
            templateUrl: 'app/components/inventory/inventory.html',
            controller: 'InventoryCtrl'
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
            url: '/test?ch&photo',
            templateUrl: 'app/components/test/test.html',
            controller: 'TestCtrl'
        })
        // Fotoportal
        .state('photo', {
            url: '/photo?anf&query',
            templateUrl: 'app/components/photo/photo.html',
            controller: 'PhotoCtrl'
        })
        // Fotoportal Detailseite
        .state('photoDetail', {
            url: '/photo/detail?id',
            templateUrl: 'app/components/photo/photoDetail.html',
            controller: 'PhotoCtrl'
        })
        // Literatur
        .state('literatur', {
            url: '/literatur',
            templateUrl: 'app/components/literatur/literatur.html',
            controller: 'LiteraturCtrl'
        })
        // Literatur
        .state('literaturDetail', {
            url: '/literatur/detail?id',
            templateUrl: 'app/components/literatur/literaturDetail.html',
            controller: 'LiteraturCtrl'
        })
        // Powersearch
        .state('search', {
            url: '/search?query',
            templateUrl: 'app/components/powersearch/powersearch.html',
            controller: 'PowersearchCtrl'
        });
 

}]);	