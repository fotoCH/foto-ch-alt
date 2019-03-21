app.controller('PhotoGridCtrl', [
    '$scope',
    '$http', 
    '$location', 
    '$state', 
    '$stateParams', 
    '$rootScope',
    function($scope, $http, $location, $state, $stateParams, $rootScope) {
        $scope.photos = {};
        $scope.root = $rootScope.imageRootUrl;

        $scope.loadImages = function () {
            if($scope.type == 'random') {
                $http.get($rootScope.ApiUrl + '/?a=photo&random='+$scope.amount).success(function (data) {
                    $scope.photos = data.res;
                });
            }
        }
        $scope.loadImages();
    }
]);

app.directive('photoGrid', function () {
    return {
        restrict: 'E',
        scope: {
            'type' : '@',
            'amount' : '@'
        },
        transclude: true,
        templateUrl: 'app/shared/misc/photogrid.html',
        controller: 'PhotoGridCtrl'
    }
});