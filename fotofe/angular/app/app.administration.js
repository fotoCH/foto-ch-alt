app.controller('PendingCtrl', [
    '$scope',
    '$http',
    '$rootScope',
    '$state',
    function ($scope,
              $http,
              $rootScope,
              $state) {

        setTimeout(function() {
            if( ! $rootScope.pendingAllowed() ) {
                $state.go("home");
            }
        }
        , 100);

        $scope.overwriting = false;
        $scope.pending = [];
        $scope.rejected = [];
        $scope.accepted = [];
        $scope.getRequests = function () {
            $http.get($rootScope.ApiUrl + '/?a=request&action=pendingList').success(function (data) {
                $scope.pending = data;
            });
            $http.get($rootScope.ApiUrl + '/?a=request&action=rejectedList').success(function (data) {
                $scope.rejected = data;
            });
            $http.get($rootScope.ApiUrl + '/?a=request&action=acceptedList').success(function (data) {
                $scope.accepted = data;
            });
        }
        $scope.getRequests();

        $scope.overwrite = function (index) {
            if ($scope.overwriting == index) {
                $scope.overwriting = false;
            } else {
                $scope.overwriting = index;
            }
            console.log($scope.overwriting);
        }

        $scope.accept = function (id, value) {
            var q = $rootScope.ApiUrl + '/?a=request&action=accept&id=' + id;
            if (typeof(value) !== 'undefined') {
                q += '&overwrite=' + value;
            }
            $http.get(q).success(function (data) {
                $rootScope.updatePendingRequests();
                $scope.getRequests();
            });
        }

        $scope.reject = function (id) {
            $http.get($rootScope.ApiUrl + '/?a=request&action=reject&id=' + id).success(function (data) {
                $rootScope.updatePendingRequests();
                $scope.getRequests();
            });
        }
    }
]);

app.controller('ProfileCtrl', function ($scope) {

});

app.controller('UserCtrl', [
    '$scope',
    '$http',
    '$rootScope',
    'adminService',
    '$httpParamSerializer',
    '$state',
    function ($scope, $http, $rootScope, adminService, $httpParamSerializer, $state) {
        $scope.users = {};
        $scope.stocks = {};
        $scope.currentUser = $rootScope.user;
        $scope.levels = $rootScope.user_levels;

        $scope.isLoading = true;

        setTimeout(function() {
            if( ! $rootScope.manageUsersAllowed() ) {
                $state.go("home");
            }
        }
        , 100);

        var loadUsers = function () {
            adminService.getStocks().then(function (result) {
                $scope.stocks = result;
            }).catch(function () {
                $scope.errorMsg = 'Network error.';
                console.log($scope.errorMsg);
                $scope.isLoading = false;
            });
            $http.get($rootScope.ApiUrl + '/?a=usermanagement&action=getUsers').then(function (result) {
                $scope.users = result.data;
                $scope.isLoading = false;
            });
        };

        $scope.changeLevel = function (userId, level, oldValue) {
            $scope.isLoading = true;
            $http.get($rootScope.ApiUrl + '/?a=usermanagement&action=changeUserLevel&id=' + userId + '&level=' + level).success(function (data) {
                $scope.isLoading = false;
                if (data.changeUserLevel !== 'success') {
                    console.log('Level not changed: ' + data.changeUserLevel);
                    level = parseInt(oldValue);
                }
            });
        };

        $scope.submitStocks = function (stocks, userId) {
            $scope.isLoading = true;
            var data = $httpParamSerializer({user: userId, stocks: stocks.toString()});

            var url = $rootScope.ApiUrl + '/?a=usermanagement&action=changeStocks&' + data;
            //var url = $rootScope.ApiUrl + '?a=usermanagement&action=changeStocks';

            $http({
                url: url,
                method: "post",
                data: data,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(
                    function(response){
                        $scope.isLoading = false;
                        if (response.data.changeStocks !== 'success') {
                            alert('Stocks not changed: ' + response.data.changeStocks);
                        } else {
                            loadUsers();
                        }
                    },
                    function(response){
                        $scope.errorMsg = 'Network error.';
                        alert($scope.errorMsg);
                        $scope.isLoading = false;
                    }
                );

        };

        $scope.delete = function (userId) {
            if (confirm('Delete user?')) {
                $scope.isLoading = true;
                $http.get($rootScope.ApiUrl + '?a=usermanagement&action=deleteUser&id=' + userId).success(function (data) {
                    $scope.isLoading = false;
                    if (data.deleteUser !== 'success') {
                        alert('User not deleted: ' + data.deleteUser);
                    } else {
                        loadUsers();
                    }

                });
            }
        };

        loadUsers();

        $scope.getUserStocks = function (userStocks) {
            var value = [];
            for (i = 0; i < userStocks.length; i++) {
                value.push(userStocks[i].id);
            }
            return value;
        }
    }
]);

app.controller('AddUserCtrl', [
    '$scope',
    '$http',
    '$rootScope',
    '$httpParamSerializer',
    '$state',
    'adminService',
    function ($scope, $http, $rootScope, $httpParamSerializer, $state, adminService) {
        $scope.levels = $rootScope.user_levels;
        $scope.isLoading = true;

        setTimeout(function() {
            if( ! $rootScope.manageUsersAllowed() ) {
                $state.go("home");
            }
        }
        , 100);

        adminService.getStocks().then(function (result) {
            $scope.stocks = result;
            $scope.isLoading = false;
        }).catch(function () {
            $scope.errorMsg = 'Network error.';
            alert($scope.errorMsg);
            $scope.isLoading = false;
        });

        $scope.userForm = {};
        $scope.newuser = {};

        /*
         $scope.newuser = {
         "level": "3",
         "username": "mmu",
         "vorname": "Max",
         "nachname": "Muster",
         "password": "test123"
         };
         */

        $scope.submitUser = function () {
            if ($scope.userForm.$valid) {
                var formData = angular.copy($scope.newuser);
                formData.stocks = formData.stocks.toString();
                var userUri = $httpParamSerializer(formData);
                // todo in Post umschreiben, Fehler scheint das hier zu sein(evtl. nur lokal): http://stackoverflow.com/questions/36295758/http-post-method-is-actally-sending-a-get
                var promise = $http.get($rootScope.ApiUrl + '?a=usermanagement&action=addUser&' + userUri);
                //var promise = $http.post($rootScope.ApiUrl + '?a=request&action=addUser', $scope.newuser);

                promise.error(function (data, status, headers, config) {
                    $scope.errorMsg = 'Network error.';
                    alert($scope.errorMsg);
                });
                promise.success(function (data, status, headers, config) {
                    if (data.addUser == 'success') {
                        $state.go('user-management');
                    } else {
                        $scope.errorMsg = data.addUser;
                        alert('User not added: ' + $scope.errorMsg);
                    }
                });
            }
        }
    }
]);

app.factory('adminService', function ($rootScope, $http) {

    var getStocks = function () {

        return $http.get($rootScope.ApiUrl + '/?a=usermanagement&action=getStocks').then(function (response) {
            return response.data;
        });
    };

    return {
        getStocks: getStocks
    };
});