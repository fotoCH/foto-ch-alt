app.controller('CountUpNumberCtrl', [
    '$scope',
    '$http', 
    '$location', 
    '$state', 
    '$stateParams', 
    '$rootScope',
    '$window',
    function($scope, $http, $location, $state, $stateParams, $rootScope, $window) {
        $scope.counted = false;
        $scope.num = 0;
        $scope.id = "counter-" + new Date().getTime();

        $rootScope.$on('$stateChangeSuccess', function () {
            $scope.countUp();
        });

        $scope.countUp = function() {
            var steps = 40;
            var timeout = 50;
            var intv = Math.floor(parseInt($scope.number) / steps);

            var running = setInterval(function() {
                if($scope.num >= $scope.number) {
                    $scope.counted = true;
                    clearInterval(running);
                }
                if($scope.num + intv >= $scope.number) {
                    $scope.num = $scope.number;
                } else {
                    $scope.num += intv;
                }
                $scope.$apply();

            }, timeout);
        };

        angular.element($window).bind("scroll", function() {
            if($scope.counted == true) {
                return;
            }
            var element = document.getElementById($scope.id);
            if(typeof(element) == 'undefined' || element == null) {
                return;
            }
            if(isElementInViewport(element)) {
                $scope.countUp();
            }
        });

    }
]);

app.directive('countUpNumber', function () {
    return {
        restrict: 'E',
        scope: {
            title: '@',
            number: '@'
        },
        templateUrl: 'app/shared/misc/countup.html',
        controller: 'CountUpNumberCtrl'
    }
});


function isElementInViewport (el) {

    //special bonus for those using jQuery
    if (typeof jQuery === "function" && el instanceof jQuery) {
        el = el[0];
    }

    var rect = el.getBoundingClientRect();

    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && /*or $(window).height() */
        rect.right <= (window.innerWidth || document.documentElement.clientWidth) /*or $(window).width() */
    );
}