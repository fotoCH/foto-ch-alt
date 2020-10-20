app.controller("ProjectsController", [
  "$scope",
  "$http",
  "$rootScope",
  "$window",
  "$state",
  "$timeout",
  function($scope, $http, $rootScope, $window, $state, $timeout) {
    $scope.projects = [];

    // get projects
    $scope.updateProjectList = function() {
      $scope.projects = [];
      $http
        .get($rootScope.ApiUrl + "/?a=projects&type=list")
        .success(function(data) {
          $timeout(function() {
            $scope.projects = data;
          }, 0);
        });
    };
    $scope.updateProjectList();
  }
]);

app.controller("ProjectDetailController", [
  "$scope",
  "$http",
  "$rootScope",
  "$window",
  "$state",
  "$timeout",
  "$stateParams",
  function($scope, $http, $rootScope, $window, $state, $timeout, $stateParams) {
    $http
      .get(
        $rootScope.ApiUrl +
          "/?a=projects&type=get&title=" +
          $stateParams.project_identification
      )
      .success(function(data) {
        $scope.detail = data;
      });
  }
]);

app.controller("ManageProjectsCtrl", [
  "$scope",
  "$http",
  "$rootScope",
  "$state",
  "$timeout",
  function($scope, $http, $rootScope, $state, $timeout) {
    $scope.formData = {};
    $scope.projects = [];
    $scope.formTitle = "";
    $scope.formActionValue = $rootScope.translations.create;

    $timeout(function() {
      if (!$rootScope.manageProjectsAllowed()) {
        $state.go("home");
      }
      $scope.formTitle = $rootScope.translations.createProject;
    }, 0);

    $scope.prepareCreateForm = function() {
      $scope.formTitle = $rootScope.translations.create;
      $scope.formActionValue = $rootScope.translations.create;
      $scope.formData = {};
    };

    // get projects
    $scope.updateProjectList = function() {
      $scope.projects = [];
      $http
        .get($rootScope.ApiUrl + "/?a=projects&type=list")
        .success(function(data) {
          $timeout(function() {
            $scope.projects = data;
          }, 0);
        });
    };
    $scope.updateProjectList();

    $scope.editProject = function(id) {
      $scope.createProjectToggle = true;
      $scope.formTitle = $rootScope.translations.edit;
      $scope.formActionValue = $rootScope.translations.update;

      $http
        .get($rootScope.ApiUrl + "/?a=projects&type=get&id=" + id)
        .success(function(data) {
          $timeout(function() {
            $scope.formData = data;
            console.log($scope.formData);
            if ($scope.formData.published == 1) {
              $scope.formData.published = true;
            }
          }, 0);
        });
    };

    $scope.deleteProject = function(id, name) {
      if (confirm("Delete Project " + name + "?")) {
        $http
          .get($rootScope.ApiUrl + "/?a=projects&type=delete&id=" + id)
          .success(function(data) {
            $timeout(function() {
              $scope.updateProjectList();
            }, 0);
          });
      }
    };

    $scope.processForm = function() {
      console.log($scope.formData);
      if (typeof $scope.formData.id === "undefined") {
        $http({
          method: "POST",
          url: $rootScope.ApiUrl + "/?a=projects&type=create",
          data: $scope.formData
        }).then(
          function(response) {
            $timeout(function() {
              $scope.updateProjectList();
            }, 0);
          },
          function(response) {
            console.log(response);
          }
        );
      } else {
        $http({
          method: "POST",
          url: $rootScope.ApiUrl + "/?a=projects&type=update",
          data: $scope.formData
        }).then(
          function(response) {
            $timeout(function() {
              $scope.updateProjectList();
            }, 0);
          },
          function(response) {
            console.log(response);
          }
        );
      }
    };
  }
]);
