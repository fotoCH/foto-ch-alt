app.controller('TimelineCtrl', [
    '$scope',
    '$http',
    '$rootScope',
    '$window',
    '$state',
    function($scope, $http, $rootScope, $window, $state) {
        $scope.direction = "new-to-old";
        $scope.minYear = 1800;
        $scope.initYearAmount = 3;
        $scope.starting = false;
        $scope.changed = false;

        setTimeout(function() {
            $scope.button_label = $rootScope.translations.descending;
        }, 100);

        $scope.switchdirection = function() {
            if($scope.direction == "new-to-old") {
                $scope.direction = "old-to-new";
                $scope.button_label = $rootScope.translations.ascending;
            } else {
                $scope.direction = "new-to-old";
                $scope.button_label = $rootScope.translations.descending;
            }
            $scope.initYears(true);
        }

        $scope.increaseLimit = function() {
            var currentLast = $scope.years[$scope.years.length - 1];
            if($scope.direction == 'new-to-old') {
                if(currentLast - 1 >= $scope.minYear) {
                    $scope.years.push(currentLast - 1);
                }
            } else {
                if(currentLast + 1 <= new Date().getFullYear()) {
                    $scope.years.push(currentLast + 1);
                }
            }
        }

        $scope.updateStart = function() {
            $scope.starting = parseInt($scope.starting);
            if($scope.starting > 1700 && $scope.starting <= new Date().getFullYear()) {
                if($scope.changed) {
                    clearTimeout($scope.changed);
                }
                $scope.changed = setTimeout(function() {
                    $scope.initYears(true);
                }, 500);
            }
        }

        $scope.initYears = function(start) {
            setTimeout(function() {
                $scope.years = [];
                if($scope.direction == 'new-to-old' && typeof(start) == 'undefined') {
                    $scope.starting = new Date().getFullYear();
                } else if(typeof(start) == 'undefined') {
                    $scope.starting = $scope.minYear;
                }

                for(var count = 0; count < $scope.initYearAmount; count++) {
                    if($scope.direction == 'new-to-old') {
                        if($scope.starting + count >= $scope.minYear) {
                            $scope.years.push($scope.starting - count);
                        }
                    } else {
                        if($scope.starting + count <= new Date().getFullYear()) {
                            $scope.years.push($scope.starting + count);
                        }
                    }
                }
                console.log('done');
                $scope.$apply();
            }, 2);
        }
        $scope.initYears();
    }
]);

app.directive('yearhighlight', function () {
    return {
        restrict: 'E',
        scope: {
            year: '=',
        },
        templateUrl: 'app/shared/misc/year.html',
        controller: 'YearHightlightController'
    }
});

app.controller('YearHightlightController', [
    '$scope',
    '$http',
    '$rootScope',
    '$window',
    '$state',
    function($scope, $http, $rootScope, $window, $state) {
        $scope.data = {};
        $scope.loading = true;
        $scope.complete = false;

        $http.get($rootScope.ApiUrl + '/?a=year&wanted='+$scope.year).success(function(data) {
            $scope.data = data[$scope.year];
            $scope.loading = false;
        });

        $scope.loadingClass = function() {
            if($scope.bloading)
                return 'loading';
            return '';
        }

        $scope.showComplete = function() {
            $scope.bloading = true;
            $http.get($rootScope.ApiUrl + '/?a=year&wanted='+$scope.year+'&nolimit=true').success(function(data) {
                $scope.bloading = false;
                $scope.complete = true;
                $scope.data = data[$scope.year];
            });
        }
    }
]);
