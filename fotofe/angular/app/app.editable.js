app.controller('EditableController', [
    '$scope',
    '$http',
    '$rootScope',
    function($scope, $http, $rootScope) {
        $scope.allowed = '';
        $scope.editing = false;

        $scope.changeEdit = function() {
            if($scope.editing) {
                $scope.editing = false;
            } else {
                $scope.editing = true;
            }
        }

        $scope.save = function(newValue) {
            console.log("SAVE > " + newValue);
        }

        $scope.allowedToEdit = function() {
            // prevent from checking on every field...
            if($scope.allowed !== '') {
                return $scope.allowed;
            }

            // check user rights
            if($rootScope.user_data 
                && ( parseInt($rootScope.user_data.level) >= 8 
                    || $rootScope.user_data.institution == $scope.institution )) {
                $scope.allowed = true;
                return true;
            }
            $scope.allowed = false;
            return false;
        }
        $scope.allowedToEdit();
    }
]);

app.directive('editable', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/form/editable.html',
        controller: 'EditableController',
        scope: {
            'title' : '=',
            'field': '=',
            'value': '=',
            'type': '=',
            'entry': '=',
            'institution': '='
        }
    };
});

angular.module('fotochWebApp').service('EditRequest', function($http, $rootScope) {

});




app.directive('focusMe', function($timeout) {
  return {
    scope: { trigger: '@focusMe' },
    link: function(scope, element) {
      scope.$watch('trigger', function(value) {
        if(value === "true") { 
          // console.log('trigger',value);
          $timeout(function() {
            element[0].focus(); 
          });
        }
      });
    }
  };
});
