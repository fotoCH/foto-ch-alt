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
    '$analytics',
    function($scope, $http, $location, $state, $stateParams, $rootScope, $window, DetailService, $uibModalStack, params, $analytics) {

        $scope.close = function() {
            $uibModalStack.dismissAll();
            $rootScope.accCache = {};
        }

        // enable accordion cache
        var cacheIndex = params.type + params.id;
        if (typeof $rootScope.accCache[cacheIndex] !== 'undefined') {
            $scope.temporaryAccCache = $rootScope.accCache[cacheIndex];
        }else{
            $scope.temporaryAccCache = {};
        }

        // update accordion cache on click
        $scope.updateAccCache = function (e, status, id) {
            $rootScope.accCache[cacheIndex] = $scope.temporaryAccCache;
        }

        $analytics.pageTrack(params.type + '/detail/' + params.id);

        $scope.carousel = false;
        $scope.prev = false;
        $scope.next = false;
        $scope.type = params.type;
        $scope.id = params.id;

        if(typeof(params.carousel) !== 'undefined') {
            $scope.carousel = params.carousel;

            var requiredIndex = $scope.carousel.indexOf(params.id);
            if(requiredIndex > 0) {
                $scope.prev = $scope.carousel[requiredIndex-1];
            }
            if(requiredIndex < $scope.carousel.length) {
                $scope.next = $scope.carousel[requiredIndex+1];
            }
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
                    $scope.getContent = function (attribute_string) {
                        if($scope.detail[attribute_string + '_s'] != null && typeof $scope.detail[attribute_string + '_s'][$scope.contentLanguage] !== 'undefined'){
                            return $scope.detail[attribute_string + '_s'][$scope.contentLanguage];
                        }else if(typeof $scope.detail[attribute_string] !== 'undefined'){
                            return $scope.detail[attribute_string];
                        }else{
                            return '';
                        }
                    }
                    $scope.setContentLanguage = function (contentLanguage){
                        if($scope.contentLanguage != contentLanguage){
                            $scope.contentLanguage = contentLanguage;
                            $scope.detail.umfeld = $scope.getContent('umfeld'); // if not done like this contentRaw-directive throws en error
                        }
                    }

                    var title = '';
                    var name = photographer.data.namen[0];
                    if(name.vorname !== '') {
                        title+= name.vorname + " ";
                    }
                    if(name.namenszusatz !== '') {
                        title+= name.namenszusatz + " ";
                    }
                    title+= name.nachname + " ";
                    $scope.title = title;
                    $scope.subtitle = photographer.data.fldatum;
                    $scope.detail = photographer.data;

                    $scope.setContentLanguage($rootScope.lang);

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

        $scope.toptitle = $rootScope.translations[params.type];
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
        return $http.get($rootScope.ApiUrl + '/?a=photographer&id=' + id + '&lang=' + $rootScope.lang).success(function (data) {
            return data;
        });
    }

    function getInstitute(id) {
        return $http.get($rootScope.ApiUrl + '/?a=institution&id=' + id + '&lang=' + $rootScope.lang).success(function (data) {
            return data;
        });
    }

    function getInventory(id) {
        return $http.get($rootScope.ApiUrl + '/?a=inventory&id=' + id + '&lang=' + $rootScope.lang).success(function (data) {
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