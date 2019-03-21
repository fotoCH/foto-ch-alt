app.controller('ProjectsController', [
    '$scope',
    '$http',
    '$rootScope',
    '$window',
    '$state',
    '$timeout',
    function($scope, $http, $rootScope, $window, $state, $timeout) {
        $scope.projects = [];

        // get projects
        $scope.updateProjectList = function() {
            $scope.projects = [];
            $http.get($rootScope.ApiUrl + '/?a=projects&type=list').success(function (data) {
                $timeout(function() {
                    $scope.projects = data;
                }, 0);
            });
        }
        $scope.updateProjectList();

    }
]);

app.controller('ProjectDetailController', [
    '$scope',
    '$http',
    '$rootScope',
    '$window',
    '$state',
    '$timeout',
    '$stateParams',
    function($scope, $http, $rootScope, $window, $state, $timeout, $stateParams) {
        $scope.detail = {
            'title' : ''
        };
        $scope.imageRoot = $rootScope.imageRootUrl;

        $http.get($rootScope.ApiUrl + '/?a=projects&type=get&title='+$stateParams.project_identification).success(function (data) {
            $scope.detail = data;

            // images
            $scope.detail.images = $scope.detail.images.split(",");
            if($scope.detail.images.length > 0 && $scope.detail.images[0] != '') {
                angular.forEach($scope.detail.images, function(value, key) {
                    $http.get($rootScope.ApiUrl + '/?a=photo&id=' + value).success(function (data) {
                        $scope.detail.images[key] = data;
                    });
                });
            }

            // literature
            $scope.detail.literature = $scope.detail.literature.split(",");
            if($scope.detail.literature.length > 0 && $scope.detail.literature != '') {
                angular.forEach($scope.detail.literature, function(value, key) {
                    $http.get($rootScope.ApiUrl + '/?a=literature&id=' + value).success(function (data) {
                        data.id = value;
                        $scope.detail.literature[key] = data;
                    });
                });
            }

            // literature
            $scope.detail.exhibitions = $scope.detail.exhibitions.split(",");
            if($scope.detail.exhibitions.length > 0 && $scope.detail.exhibitions != '') {
                angular.forEach($scope.detail.exhibitions, function(value, key) {
                    $http.get($rootScope.ApiUrl + '/?a=exhibition&id=' + value).success(function (data) {
                        data.id = value;
                        $scope.detail.exhibitions[key] = data;
                    });
                });
            }
            
            // people
            $scope.detail.people = $scope.detail.people.split(";");
            if($scope.detail.people.length > 0 && $scope.detail.people[0] != '') {
                angular.forEach($scope.detail.people, function(value, key) {
                    var groups = $scope.detail.people[key].split(":");
                    $scope.detail.people[key] = {};
                    $scope.detail.people[key].title = groups[0];
                    $scope.detail.people[key].people = groups[1].split(",");
                    angular.forEach($scope.detail.people[key].people, function(v, k) {
                        $http.get($rootScope.ApiUrl + '/?a=photographer&id=' + v).success(function (data) {
                            data.id = v;
                            $scope.detail.people[key].people[k] = data;
                        });
                    });
                });
            }
        });
    }
]);

app.controller('ManageProjectsCtrl', [
    '$scope',
    '$http',
    '$rootScope',
    '$state',
    '$timeout',
    function ($scope, $http, $rootScope, $state, $timeout) {
        $scope.formData = {};
        $scope.projects = [];
        $scope.formTitle = '';
        $scope.formActionValue = $rootScope.translations.create;

        $timeout(function() {
            if( ! $rootScope.manageProjectsAllowed() ) {
                $state.go("home");
            }
            $scope.formTitle = $rootScope.translations.createProject;
        }, 0);
        
        $scope.prepareCreateForm = function() {
            $scope.formTitle = $rootScope.translations.create;
            $scope.formActionValue = $rootScope.translations.create;
            $scope.formData = {}
        }

        // get projects
        $scope.updateProjectList = function() {
            $scope.projects = [];
            $http.get($rootScope.ApiUrl + '/?a=projects&type=list').success(function (data) {
                $timeout(function() {
                    $scope.projects = data;
                }, 0);
            });
        }
        $scope.updateProjectList();

        $scope.editProject = function(id) {
            $scope.createProjectToggle = true;
            $scope.formTitle = $rootScope.translations.edit;
            $scope.formActionValue = $rootScope.translations.update;

            $http.get($rootScope.ApiUrl + '/?a=projects&type=get&id='+id).success(function (data) {
                $timeout(function() {
                    $scope.formData = data;
                    console.log($scope.formData);
                    if($scope.formData.published == 1) {
                        $scope.formData.published = true;
                    }
                }, 0);
            });
        }

        $scope.deleteProject = function(id, name) {
            if(confirm('Delete Project ' + name + '?')) {
                $http.get($rootScope.ApiUrl + '/?a=projects&type=delete&id='+id).success(function (data) {
                    $timeout(function() {
                        $scope.updateProjectList();
                    }, 0);
                });
            }
        }

        $scope.processForm = function() {
            console.log($scope.formData);
            if(typeof($scope.formData.id) === 'undefined') {
                $http({
                    method: 'POST',
                    url: $rootScope.ApiUrl + '/?a=projects&type=create',
                    data: $scope.formData
                }).then(
                    function(response) {
                        $timeout(function() {
                            $scope.updateProjectList();
                        }, 0);
                    }, 
                    function(response) {
                        console.log(response)
                    }
                );
            } else {
                $http({
                    method: 'POST',
                    url: $rootScope.ApiUrl + '/?a=projects&type=update',
                    data: $scope.formData
                }).then(
                    function(response) {
                        $timeout(function() {
                            $scope.updateProjectList();
                        }, 0);
                    }, 
                    function(response) {
                        console.log(response)
                    }
                );
            }
        }

    }
]);