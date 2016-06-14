
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

        // Institution search view
        .state('institution', {
            url: '/institution?anf&query',
            templateUrl: 'app/components/institution/institution.html',
            controller: 'InstitutionCtrl'
        })
        // Exhibition search view
        .state('exhibition', {
            url: '/exhibition?anf',
            templateUrl: 'app/components/exhibition/exhibition.html',
            controller: 'ExhibitionCtrl'
        })
        // Inventory search view
        .state('inventory', {
            url: '/inventory?anf',
            templateUrl: 'app/components/inventory/inventory.html',
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
        // Literatur
        .state('literatur', {
            url: '/literatur',
            templateUrl: 'app/components/literatur/literatur.html',
            controller: 'LiteraturCtrl'
        })
        // Powersearch
        .state('search', {
            url: '/search?query',
            templateUrl: 'app/components/powersearch/powersearch.html',
            controller: 'PowersearchCtrl'
        })
        // Institution detail view
        .state('exhibitionDetail', {
            url: '/detail?id',
            parent: 'exhibition',
            onEnter: [
                "$uibModal",
                "$location",
                function($uibModal, $location) {
                  $uibModal.open({
                    controller: "ExhibitionCtrl",
                    templateUrl: 'app/components/exhibition/exhibitionDetail.html',
                    size: 'lg'
                  }).result.then(function() {
                    closeModal($location, 'exhibition');
                  }, function() {
                    closeModal($location, 'exhibition');
                  });
              }
            ]
        })
        // Inventory detail view
        .state('inventoryDetail', {
            url: '/detail?id',
            parent: 'inventory',
            onEnter: [
                "$uibModal",
                "$location",
                function($uibModal, $location) {
                  $uibModal.open({
                    controller: "InventoryCtrl",
                    templateUrl: 'app/components/inventory/inventoryDetail.html',
                    size: 'lg'
                  }).result.then(function() {
                    closeModal($location, 'inventory');
                  }, function () {
                    closeModal($location, 'inventory');
                  });
              }
            ]
        })
        // Institution detail view
        .state('institutionDetail', {
            url: '/detail?id',
            parent: 'institution',
            onEnter: [
                "$uibModal",
                "$location",
                function($uibModal, $location) {
                  $uibModal.open({
                    controller: "InstitutionCtrl",
                    templateUrl: 'app/components/institution/institutionDetail.html',
                    size: 'lg'
                  }).result.then(function() {
                    closeModal($location, 'institution');
                  }, function () {
                    closeModal($location, 'institution');
                  });
                }
            ]
        })
        // Photographer detail view
        .state('photographerDetail', {
            url: '/detail?id',
            parent: 'photographer',
            onEnter: [
                "$uibModal",
                "$location",
                function($uibModal, $location) {
                  $uibModal.open({
                    controller: "PhotographerCtrl",
                    templateUrl: 'app/components/photographer/photographerDetail.html',
                    size: 'lg'
                  }).result.then(function() {
                    closeModal($location, 'photographer');
                  }, function () {
                    closeModal($location, 'photographer');
                  });
                }
            ]
        })
        // Fotoportal Detailseite
        .state('photoDetail', {
            url: '/detail?id',
            parent: 'photo',
            onEnter: [
                "$uibModal",
                "$location",
                function($uibModal, $location) {
                  $uibModal.open({
                    controller: "PhotoCtrl",
                    templateUrl: 'app/components/photo/photoDetail.html',
                    size: 'lg'
                  }).result.then(function() {
                    closeModal($location, 'photo');
                  }, function () {
                    closeModal($location, 'photo');
                  });
                }
            ]
        })
        // Literatur
        .state('literaturDetail', {
            url: '/detail?id',
            parent: 'literatur',
            onEnter: [
                "$uibModal",
                "$location",
                function($uibModal, $location) {
                  $uibModal.open({
                    controller: "LiteraturCtrl",
                    templateUrl: 'app/components/literatur/literaturDetail.html',
                    size: 'lg'
                  }).result.then(function() {
                    closeModal($location, 'literatur');
                  }, function () {
                    closeModal($location, 'literatur');
                  });
                }
            ]
        });

}]);

function closeModal($location, parent) {
    var path = $location.path();
    $location.search("id", null);
    $location.path("/"+parent);
}