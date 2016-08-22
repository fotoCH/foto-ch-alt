app.controller('EditableController', [
    '$scope',
    '$http',
    '$rootScope',
    '$window',
    '$state',
    function($scope, $http, $rootScope, $window, $state) {
        $scope.allowed = false;
        $scope.editing = false;

        $scope.changeEdit = function() {
            if($scope.editing) {
                $scope.editing = false;
            } else {
                $scope.editing = true;
            }
        }

        $scope.save = function(newValue) {
            var query = $rootScope.ApiUrl + '/?a=request';
            query+= '&entry=' + $scope.entry;
            query+= '&field=' + $scope.field;
            query+= '&type=' + $scope.type;
            query+= '&value=' + newValue;
            if($scope.institution) {
                query+= '&institution='+$scope.institution;
            }
            $http({
                method: 'GET',
                url: query
            }).then(function (response) {
                $scope.changeEdit();
                $scope.value = newValue;
            });
        }

        $scope.checkShortcuts = function (e, value) {
            if(e.ctrlKey && e.which == 10){
                $scope.save(value);
            }
        }

        $scope.allowedToEdit = function() {
            if($rootScope.user_data && ( parseInt($rootScope.user_data.level) >= 8 || $rootScope.user_data.inst == $scope.institution || $scope.stock in $rootScope.user_data.stocks)) {
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
            'field': '@',
            'value': '=',
            'type': '@',
            'entry': '=',
            'institution': '=',
            'stock': '='
        }
    };
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
