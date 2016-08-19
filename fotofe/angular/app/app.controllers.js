/**
 * Controllers
 */

app.controller('MainCtrl',
    ['$scope', '$http', '$state', '$stateParams', '$rootScope', '$location', 'languages', '$uibModal', '$cookies', '$window', '$uibModalStack', 'TranslationService',
        function ($scope, $http, $state, $stateParams, $rootScope, $location, languages, $uibModal, $cookies, $window, $uibModalStack, TranslationService) {
            $rootScope.textualSearch = '';

            $rootScope.pendingRequests = 0;
            $rootScope.translationLoaded = false;

            if (typeof($window.sessionStorage.user_data) !== 'undefined' &&
                typeof($rootScope.user_data) == 'undefined') {
                $rootScope.user_data = JSON.parse($window.sessionStorage.user_data);
            }

            $rootScope.accCache = {};

            $rootScope.updatePendingRequests = function () {
                if ($rootScope.user_data && $rootScope.user_data.level >= 8) {
                    $http.get($rootScope.ApiUrl + '?a=request&action=pendingAmount').success(function (data) {
                        $rootScope.pendingRequests = data.count;
                    });
                } else {
                    $rootScope.pendingRequests = 0;
                }
            }
            $rootScope.updatePendingRequests();
            if ($rootScope.user_data && $rootScope.user_data.level >= 8) {
                setInterval(function () {
                    $rootScope.updatePendingRequests();
                }, 10000);
            }


            function loadTranslation() {
                var translationPromise = TranslationService.getTranslation();
                translationPromise.then(function (data) {
                    $scope.spr = data;
                    $rootScope.translations = data;
                    $rootScope.translationLoaded = true;
                });
            }

            $rootScope.currentDetail = '';
            $rootScope.$on('$locationChangeSuccess', function () {
                if ($location.hash().indexOf('detail') >= 0) {
                    checkHashForModal();
                } else {
                    $uibModalStack.dismissAll();
                    $rootScope.currentDetail = '';
                }
            });

            $rootScope.pendingAllowed = function () {
                if ($rootScope.user_data.level >= 8) {
                    return true;
                }
                return false;
            }

            $scope.getUserLevelClass = function () {
                return 'level-' + $rootScope.user_data.level;
            }

            $rootScope.doLogout = function () {
                $http.get($rootScope.ApiUrl + '/?a=user&b=logout').success(function (data) {
                    var resp = data;

                    $rootScope.user = '';
                    $rootScope.userLevel = 0;
                    $rootScope.authToken = '';
                    $rootScope.instComment = 0;
                    $http.defaults.headers.common['X-AuthToken'] = undefined;
                    $rootScope.user_data = false;
                    $window.sessionStorage.authToken = undefined;
                    $window.sessionStorage.removeItem('user_data');

                    $state.go("home");
                });
            }

            $rootScope.setTitle = function (title) {
                $scope.title = title;
            }

            $rootScope.setDescription = function (desciption) {
                $scope.description = desciption;
            }

            $rootScope.detail = function (id, type, carousel) {
                $uibModalStack.dismissAll();
                $rootScope.openNew = false;

                if ($rootScope.currentDetail != 'detail=' + id + '&type=' + type) {
                    $rootScope.openNew = true;
                    $rootScope.currentDetail = 'detail=' + id + '&type=' + type;
                    $location.hash('detail=' + id + '&type=' + type);
                    setTimeout(function () {
                        $rootScope.detailModal = $uibModal.open({
                            controller: "DetailController",
                            templateUrl: 'app/shared/content/detail.html',
                            size: 'lg',
                            animation: false,
                            resolve: {
                                params: function () {
                                    return {
                                        id: id,
                                        type: type,
                                        carousel: carousel
                                    };
                                }
                            }
                        }).result.then(function () {
                        }, function () {
                            if (!$rootScope.openNew) {
                                $location.hash('!');
                                $rootScope.currentDetail = '';
                            }
                        });
                    }, 300);
                }
            }

            function checkHashForModal() {
                var hash = $location.hash();
                hash = hash.split('&');
                if (hash.length > 1) {
                    var id = 0;
                    var type = 'unknown';
                    for (index = 0; index < hash.length; index++) {
                        var split = hash[index].split('=');
                        if (index == 0 && split[0] == 'detail') {
                            id = split[1];
                        }
                        if (index == 1 && split[0] == 'type') {
                            type = split[1];
                        }
                    }
                    $rootScope.detail(id, type);
                }
            }

            checkHashForModal();

            loadTranslation();

            $scope.isHome = function () {
                if ($location.path().substr(0, '/home'.length) == '/home') {
                    return "is-home"
                } else {
                    return ""
                }
            }

            $scope.setLanguage = function (lang) {
                $rootScope.lang = lang;
                $cookies.put('lang', lang);
                $rootScope.isLangSwitchOpen = false;    // Close the language switch after selection of new language
                $rootScope.isMenuOpen = false;            // Close the mobile menu after selection of new language
                var hosta = $location.$$host.split('.');
                for (i = 0, len = hosta.length; i < len; ++i) {
                    if (languages.indexOf(hosta[i]) >= 0) {
                        hosta[i] = lang;
                        var port = ($location.$$port == 80 ? ':' : ':' + $location.$$port);
                        window.location.href = $location.$$protocol + "://" + (hosta.join('.')) + port + window.location.pathname + window.location.hash;
                    }
                }

                loadTranslation();

                if ($state.includes('aboutFotoch') || $state.includes('contact')) {        // Reload content after switching language
                    $rootScope.reloadPages();
                }
                else if ($state.includes('home')) {
                    $rootScope.reloadHome();
                }

                $state.reload();
            };

            $scope.getLclass = function (lang) {
                if ($rootScope.lang == lang) {
                    return "is-active"
                } else {
                    return ""
                }
            };

            $scope.toggleMobileMenu = function () {
                $rootScope.isMenuOpen = !$rootScope.isMenuOpen;
            };
            $rootScope.isMenuOpen = false;

            $scope.toggleLangSwitch = function () {
                $rootScope.isLangSwitchOpen = !$rootScope.isLangSwitchOpen;
            };
            $rootScope.isLangSwitchOpen = false;

            // Close mobile menu on state change
            $rootScope.$on('$stateChangeSuccess', function () {
                $rootScope.isMenuOpen = false;
            });

            $rootScope.goBack = function () {
                window.history.back();
            }

            $rootScope.showInfo = function (text) {
                alert(text.replace(/\\n/g, "\n"));
            }

        }]);
angular.module('fotochWebApp').service('TranslationService', function ($http, $rootScope) {

    var getTranslation = function () {
        return $http.get($rootScope.ApiUrl + '/?a=sprache&lang=' + $rootScope.lang).then(function (result) {
            return result.data;
        });
    };

    return {getTranslation: getTranslation};
});

app.controller('NavigationCtrl', ['$scope', '$location', '$rootScope', function ($scope, $location, $rootScope) {
    $scope.getClass = function (path) {
        var splittedPath = $location.path().split("/");
        if (splittedPath[1] == path) {
            return "is-active";
        } else {
            return "";
        }
    };

}]);

app.controller('InstitutionCtrl', [
    '$scope',
    '$http',
    '$location',
    '$state',
    '$stateParams',
    '$rootScope',
    '$filter',
    '$uibModalStack',
    'ngMeta',
    function ($scope, $http, $location, $state, $stateParams, $rootScope, $filter, $uibModalStack, ngMeta) {
        function setMeta() {
            ngMeta.setTitle($rootScope.translations.institution_title);
            ngMeta.setTag('description', $rootScope.translations.institution_description);
        }

        // wait for translations to be loaded before setting title
        if (!$rootScope.translationLoaded) {

            var cancelWatcher = $rootScope.$watch('translationLoaded', function (newValue, oldvalue) {
                if (newValue) {
                    setMeta();
                    cancelWatcher();
                }
            });
        } else {
            setMeta();
        }
    }
]);

app.controller('InventoryCtrl', [
    '$scope',
    '$http',
    '$location',
    '$state',
    '$stateParams',
    '$rootScope',
    '$uibModalStack',
    'ngMeta',
    function ($scope, $http, $location, $state, $stateParams, $rootScope, $uibModalStack, ngMeta) {

        function setMeta() {
            ngMeta.setTitle($rootScope.translations.inventory_title);
            ngMeta.setTag('description', $rootScope.translations.inventory_description);
        }

        // wait for translations to be loaded before setting title
        if (!$rootScope.translationLoaded) {

            var cancelWatcher = $rootScope.$watch('translationLoaded', function (newValue, oldvalue) {
                if (newValue) {
                    setMeta();
                    cancelWatcher();
                }
            });
        } else {
            setMeta();
        }
    }
]);

app.controller('StaticPageCtrl', [
    '$scope',
    '$http',
    '$location',
    '$state',
    '$stateParams',
    '$rootScope',
    '$uibModalStack',
    'ngMeta',
    function ($scope, $http, $location, $state, $stateParams, $rootScope, $uibModalStack, ngMeta) {

        function loadContent() {
            $http.get($rootScope.ApiUrl + '/?a=pages&lang=' + $rootScope.lang).success(function (data) {
                $scope.pages = data;
            });
        }

        loadContent();

        $rootScope.reloadPages = function () {
            loadContent();
        };
    }
]);

app.controller('ExhibitionCtrl', [
    '$scope',
    '$http',
    '$location',
    '$state',
    '$stateParams',
    '$rootScope',
    '$uibModalStack',
    'ngMeta',
    function ($scope, $http, $location, $state, $stateParams, $rootScope, $uibModalStack, ngMeta) {
        function setMeta() {
            ngMeta.setTitle($rootScope.translations.exhibition_title);
            ngMeta.setTag('description', $rootScope.translations.exhibition_description);
        }

        // wait for translations to be loaded before setting title
        if (!$rootScope.translationLoaded) {

            var cancelWatcher = $rootScope.$watch('translationLoaded', function (newValue, oldvalue) {
                if (newValue) {
                    setMeta();
                    cancelWatcher();
                }
            });
        } else {
            setMeta();
        }
    }
]);

app.controller('PhotographerCtrl', [
    '$scope',
    '$http',
    '$location',
    '$state',
    '$stateParams',
    '$rootScope',
    '$filter',
    '$timeout',
    '$q',
    '$uibModalStack',
    'ngMeta',
    function ($scope, $http, $location, $state, $stateParams, $rootScope, $filter, $timeout, $q, $uibModalStack, ngMeta) {
        function setMeta() {
            ngMeta.setTitle($rootScope.translations.photographer_title);
            ngMeta.setTag('description', $rootScope.translations.photographer_description);
        }

        // wait for translations to be loaded before setting title
        if (!$rootScope.translationLoaded) {

            var cancelWatcher = $rootScope.$watch('translationLoaded', function (newValue, oldvalue) {
                if (newValue) {
                    setMeta();
                    cancelWatcher();
                }
            });
        } else {
            setMeta();
        }
    }
]);

app.controller('PhotoCtrl', [
    '$scope',
    '$http',
    '$state',
    '$stateParams',
    '$location',
    '$rootScope',
    '$filter',
    '$cacheFactory',
    '$timeout',
    '$uibModalStack',
    'ngMeta',
    function ($scope, $http, $state, $stateParams, $location, $rootScope, $filter, $cacheFactory, $timeout, $uibModalStack, ngMeta) {
        function setMeta() {
            ngMeta.setTitle($rootScope.translations.photos_title);
            ngMeta.setTag('description', $rootScope.translations.photos_description);
        }

        // wait for translations to be loaded before setting title
        if (!$rootScope.translationLoaded) {

            var cancelWatcher = $rootScope.$watch('translationLoaded', function (newValue, oldvalue) {
                if (newValue) {
                    setMeta();
                    cancelWatcher();
                }
            });
        } else {
            setMeta();
        }
    }
]);

app.controller('PendingCtrl', [
    '$scope',
    '$http',
    '$rootScope',
    function ($scope,
              $http,
              $rootScope) {

        $scope.overwriting = false;
        $scope.pending = [];
        $scope.rejected = [];
        $scope.accepted = [];
        $scope.getRequests = function () {
            $http.get($rootScope.ApiUrl + '?a=request&action=pendingList').success(function (data) {
                $scope.pending = data;
            });
            $http.get($rootScope.ApiUrl + '?a=request&action=rejectedList').success(function (data) {
                $scope.rejected = data;
            });
            $http.get($rootScope.ApiUrl + '?a=request&action=acceptedList').success(function (data) {
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
            var q = $rootScope.ApiUrl + '?a=request&action=accept&id=' + id;
            if (typeof(value) !== 'undefined') {
                q += '&overwrite=' + value;
            }
            $http.get(q).success(function (data) {
                $rootScope.updatePendingRequests();
                $scope.getRequests();
            });
        }

        $scope.reject = function (id) {
            $http.get($rootScope.ApiUrl + '?a=request&action=reject&id=' + id).success(function (data) {
                $rootScope.updatePendingRequests();
                $scope.getRequests();
            });
        }

    }
]);

/** Litarature **/
app.controller('LiteraturCtrl', [
    '$scope',
    '$http',
    '$location',
    '$state',
    '$stateParams',
    '$rootScope',
    '$uibModalStack',
    'ngMeta',
    function ($scope, $http, $location, $state, $stateParams, $rootScope, $uibModalStack, ngMeta) {
        function setMeta() {
            ngMeta.setTitle($rootScope.translations.literature_title);
            ngMeta.setTag('description', $rootScope.translations.literature_description);
        }

        // wait for translations to be loaded before setting title
        if (!$rootScope.translationLoaded) {

            var cancelWatcher = $rootScope.$watch('translationLoaded', function (newValue, oldvalue) {
                if (newValue) {
                    setMeta();
                    cancelWatcher();
                }
            });
        } else {
            setMeta();
        }
    }
]);

app.controller('HomeCtrl',
    ['$scope', '$http', '$location', '$state', '$stateParams', '$rootScope', '$analytics', 'ngMeta',
        function ($scope, $http, $location, $state, $stateParams, $rootScope, $analytics, ngMeta) {

            function setMeta() {
                ngMeta.setTitle($rootScope.translations.home_title);
                ngMeta.setTag('description', $rootScope.translations.home_description);
            }

            // wait for translations to be loaded before setting title
            if (!$rootScope.translationLoaded) {
                var cancelWatcher = $rootScope.$watch('translationLoaded', function (newValue, oldvalue) {
                    if (newValue) {
                        setMeta();
                        cancelWatcher();
                    }
                });
            } else {
                setMeta();
            }

            function zeroFill(number, width) {
                width -= number.toString().length;
                if (width > 0) {
                    return new Array(width + (/\./.test(number) ? 2 : 1)).join('0') + number;
                }
                return number + ""; // always return a string
            }

            function getHeaderImage() {
                var amountOfHeaderImages = 11;
                var imageNo = Math.floor((Math.random() * amountOfHeaderImages) + 1);
                $scope.imgURL = 'assets/img/home-intro/header-' + zeroFill(imageNo, 3) + '.jpg';
            }

            getHeaderImage();

            $rootScope.reloadHome = function () {
                getHeaderImage();
            };

            $scope.submitForm = function () {
                $state.go('search', {query: $scope.powersearch});
            };

            $scope.getGenderClass = function (gender) {
                if (gender == 'm') {
                    return 'gender-m';
                } else if (gender == 'f') {
                    return 'gender-f';
                }
                return '';
            }

            // recent updated
            $scope.recent_photographer = {};
            $scope.recentAmount = 5;
            function recents() {
                $http.get($rootScope.ApiUrl + '/?recent=' + $scope.recentAmount + '&nocache=true').success(function (data) {
                    $scope.recent_photographer = data.res;
                });
            }

            recents();

            // most viewed
            $scope.mostviewed_photographer = {};
            function mostviewed() {
                $http.get($rootScope.ApiUrl + '/?mostviewed=' + $scope.recentAmount + '&nocache=true').success(function (data) {
                    $scope.mostviewed_photographer = data.res;
                });
            }

            mostviewed();

            $scope.statistics = {};
            function statistics() {
                $http.get($rootScope.ApiUrl + '/?a=statistics').success(function (data) {
                    $scope.statistics = data;
                });
            }

            statistics();

        }]);

app.controller('LoginCtrl', ['$scope', '$http', '$state', '$stateParams', '$rootScope', '$window', '$timeout', 'DetailService', function ($scope, $http, $state, $stateParams, $rootScope, $window, $timeout, DetailService) {

    $scope.doLogin = function (user) {
        $http.get($rootScope.ApiUrl + '/?a=user&b=login&user=' + user.username + '&password=' + user.password).success(function (data) {

            var status = data.status;

            if (status == 'ok') {
                $scope.errorMsg = $scope.spr.login_success;
                $timeout(function () {
                    $scope.errorMsg = '';
                }, 5000);
                $rootScope.user = user.username;
                $rootScope.user_data = {
                    "username": user.username,
                    "forename": data.vorname,
                    "name": data.nachname,
                    "inst": data.inst_comment,
                    "level": data.level,
                    "email": data.email
                }
                if (data.inst_comment != '') {
                    DetailService.getInstitute(data.inst_comment).then(function (institute) {
                        $rootScope.user_data.institute = institute.data;
                        $window.sessionStorage.user_data = JSON.stringify($rootScope.user_data);
                    });
                }
                $window.sessionStorage.user_data = JSON.stringify($rootScope.user_data);

                $rootScope.userLevel = parseInt(data.level);
                $rootScope.instComment = parseInt(data.inst_comment);
                $rootScope.authToken = data.token;

                $window.sessionStorage.authToken = data.token;
                $http.defaults.headers.common['X-AuthToken'] = $rootScope.authToken;
                $state.go("profile");
            } else {
                $scope.errorMsg = 'Bad login';
            }

        });
    }
}]);

// Controller for the contact form on the contact form
app.controller('contactFormCtrl', function ($scope, $http) {
    // console.log("Contact Form Controller reporting for duty.")
    $scope.formData = {};

    $scope.processForm = function () {
        $http({
            method: 'POST',
            url: 'sendContactMail.php',
            data: $.param($scope.formData),         // Pass in data as strings
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }         // Set headers so angular passing info as form data (not request payload)
        })
            .success(function (data) {
                if (!data.success) {
                    // Something went wrong, bind errors to error variables
                    $scope.errorName = data.errors.name;
                    $scope.errorEmail = data.errors.email;
                    $scope.errorMessage = data.errors.message;
                } else {
                    // Everything alright, bind success message to message
                    $scope.message = data.message;
                }
            });
    };
});

app.controller('ProfileCtrl', function ($scope, $http, $location) {

});

app.controller('homeSearch', [
    '$scope',
    '$http',
    '$location',
    '$state',
    '$stateParams',
    '$rootScope',
    '$q',
    function ($scope, $http, $location, $state, $stateParams, $rootScope, $q) {
        $scope.searchActive = false;
        $scope.user = false;
        $scope.timeout = false;
        $scope.result = false;
        $scope.isLoading = false;
        $scope.xhr = false;
        $scope.limit = 8;
        $scope.photolimit = 20;

        $scope.setIdArrays = function () {
            var parts = ['exhibition', 'institution', 'literature', 'photographer', 'photos', 'stock'];
            for (var index = 0; index < parts.length; index++) {
                $scope.result[parts[index] + '_ids'] = [];
                if (typeof($scope.result[parts[index] + '_results']) !== 'undefined') {
                    for (var item = 0; item < $scope.result[parts[index] + '_results'].length; item++) {
                        $scope.result[parts[index] + '_ids'].push($scope.result[parts[index] + '_results'][item].id);
                    }
                }
            }
        }

        $scope.change = function (user) {

            $scope.user = user;
            $scope.isLoading = true;

            // avoid query spams
            clearTimeout($scope.timeout);
            $scope.timeout = setTimeout(function () {
                $scope.result = {};
                $rootScope.textualSearch = $scope.user.query;
                $http({
                    method: "GET",
                    url: $rootScope.ApiUrl + '/?a=streamsearch&query=' + $scope.user.query
                    + '&limit=' + $scope.limit
                    + '&photolimit=' + $scope.photolimit,
                    headers: {
                        'Content-Type': "text/plain"
                    },
                    transformResponse: [function (data) {
                        return data;
                    }],
                    onProgress: function (event) {
                        try {
                            var response = event.currentTarget.responseText;
                            response = response.replace(/}{/g, "},{");
                            response = "[" + response + "]";
                            var newresult = JSON.parse(response);
                            newresult = newresult[newresult.length - 1];
                            $scope.result = newresult;
                            $scope.setIdArrays();
                            $scope.$apply();
                        } catch (e) {
                            console.log("Invalid response: " + event.currentTarget.responseText);
                            console.log(e);
                        }
                    }
                }).then(function (e) {
                    $scope.isLoading = false;
                });

            }, 1500);
        }

        $scope.focus = function () {
            $scope.searchActive = true;
        }

        $scope.blur = function () {
            if (typeof($scope.user.query) !== 'undefined' && $scope.user.query.length > 0) {
                $scope.searchActive = true;
            } else {
                $scope.searchActive = false;
            }
        }

    }
]);