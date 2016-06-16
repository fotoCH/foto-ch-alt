app.controller('DetailController', [
    '$scope',
    '$http', 
    '$location', 
    '$state', 
    '$stateParams', 
    '$rootScope',
    '$window',
    'DetailService',
    '$uibModalStack',
    'params',
    function($scope, $http, $location, $state, $stateParams, $rootScope, $window, DetailService, $uibModalStack, params) {

        $scope.close = function() {
            $uibModalStack.dismissAll();
        }

        switch(params.type) {
            case 'photo':
                DetailService.getPhoto(params.id).then(function(photo) {
                    $scope.title = photo.data.title;
                    $scope.subtitle = photo.data.name;
                    $scope.photo = photo.data;
                });
                $scope.spr = $rootScope.translations;
                $scope.bodytemplate = DetailService.getBodyTemplate('photo');
                break;

            case 'photographer':
                DetailService.getPhotographer(params.id).then(function(photographer) {
                    var title = '';
                    angular.forEach(photographer.data.namen, function(value, key) {
                        title+= value.vorname + ", " + value.nachname + " ";
                    });
                    $scope.title = title;
                    $scope.subtitle = photographer.data.heimatort;
                    $scope.detail = photographer.data;
                });
                $scope.translations = $rootScope.translations;
                $scope.bodytemplate = DetailService.getBodyTemplate(params.type);
                break;

            case 'institution':
                DetailService.getInstitute(params.id).then(function(institute) {
                    $scope.title = institute.data.name;
                    $scope.subtitle = institute.data.art;
                    $scope.detail = institute.data;
                });
                $scope.translations = $rootScope.translations;
                $scope.bodytemplate = DetailService.getBodyTemplate(params.type);
                break;

            case 'inventory':
                DetailService.getInventory(params.id).then(function(inventory) {
                    $scope.title = inventory.data.name;
                    $scope.subtitle = '';
                    $scope.detail = inventory.data;
                });
                $scope.translations = $rootScope.translations;
                $scope.bodytemplate = DetailService.getBodyTemplate(params.type);
                break;

            case 'exhibition':
                DetailService.getExhibition(params.id).then(function(exhibition) {
                    $scope.title = exhibition.data.titel;
                    $scope.subtitle = '';
                    $scope.detail = exhibition.data;
                });
                $scope.translations = $rootScope.translations;
                $scope.bodytemplate = DetailService.getBodyTemplate(params.type);
                break;

            case 'literature':
                DetailService.getLiterature(params.id).then(function(literature) {
                    $scope.title = literature.data.titel;
                    $scope.subtitle = '';
                    $scope.detail = literature.data;
                });
                $scope.translations = $rootScope.translations;
                $scope.bodytemplate = DetailService.getBodyTemplate(params.type);
                break;

            default:
                console.log('Unknown Detail Type: ' + params.type);
        }
    }
]);

angular.module('fotochWebApp').service('DetailService', function($http, $rootScope) {

    function getBodyTemplate(type) {
        return "app/components/details/" + type + ".html";
    }

    function getPhoto(id) {
        return $http.get($rootScope.ApiUrl + '/?a=photo&id=' + id).success(function (data) {
            return data;
        });
    }

    function getExhibition(id) {
        return $http.get($rootScope.ApiUrl + '/?a=exhibition&id=' + id).success(function (data) {
            return data;
        });
    }

    function getPhotographer(id) {
        return $http.get($rootScope.ApiUrl + '/?a=photographer&id=' + id).success(function (data) {
            return data;
        });
    }

    function getInstitute(id) {
        return $http.get($rootScope.ApiUrl + '/?a=institution&id=' + id).success(function (data) {
            return data;
        });
    }

    function getInventory(id) {
        return $http.get($rootScope.ApiUrl + '/?a=inventory&id=' + id).success(function (data) {
            return data;
        });
    }

    function getLiterature(id) {
        return $http.get($rootScope.ApiUrl + '/?a=literature&id=' + id).success(function (data) {
            return data;
        });
    }

    return {
        getPhoto: getPhoto,
        getPhotographer : getPhotographer,
        getExhibition : getExhibition,
        getInstitute : getInstitute,
        getInventory : getInventory,
        getLiterature : getLiterature,
        getBodyTemplate : getBodyTemplate
    };
});