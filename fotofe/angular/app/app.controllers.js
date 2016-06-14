/**
 * Controllers
 */

app.controller('MainCtrl', 
    ['$scope', '$http', '$state', '$stateParams', '$rootScope', '$location', 'languages', '$uibModal', 
    function ($scope, $http, $state, $stateParams, $rootScope, $location, languages, $uibModal) {
    // console.log("Main Controller reporting for duty.");
    $rootScope.textualSearch = '';

    function loadTranslation() {
        $http.get($rootScope.ApiUrl + '/?a=sprache&lang=' + $rootScope.lang).success(function (data) {
            $scope.spr = data;
            $rootScope.translations = data;
        });
    }

    $scope.title =  'fotoCH';
    $rootScope.setTitle = function(title) {
        $scope.title = title;
    }

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

app.controller('NavigationCtrl', ['$scope', '$location', '$rootScope', function ($scope, $location, $rootScope) {
    // console.log("Navigation Controller reporting for duty.");
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
    function ($scope, $http, $location, $state, $stateParams, $rootScope, $filter, $uibModalStack) {
        $uibModalStack.dismissAll();

        $rootScope.setTitle('fotoCH - Institutionen');
        $scope.translations = $rootScope.translations;

        var id = $stateParams.id;

        if(id) {
            $http.get($rootScope.ApiUrl + '/?a=institution&id=' + id).success(function (data) {
                $scope.detail = data;
                $scope.list = null;
            });
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
    function ($scope, $http, $location, $state, $stateParams, $rootScope, $uibModalStack) {
        $uibModalStack.dismissAll();

        $rootScope.setTitle('fotoCH - Bestände');
        $scope.translations = $rootScope.translations;

        var id = $stateParams.id;
        var anf = $stateParams.anf;
        if(id) {
            $http.get($rootScope.ApiUrl + '/?a=inventory&id=' + id).success(function (data) {
                console.log(data);
                $scope.detail = data;
            });
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
    function ($scope, $http, $location, $state, $stateParams, $rootScope, $uibModalStack) {
        $uibModalStack.dismissAll();

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

app.controller('HomeCtrl', ['$scope', '$http', '$location', '$state', '$stateParams', '$rootScope', function ($scope, $http, $location, $state, $stateParams, $rootScope) {

    $rootScope.setTitle('fotoCH - Dokumentation der Schweizer Fotografie');

    function zeroFill( number, width ) {
      width -= number.toString().length;
      if ( width > 0 ) {
        return new Array( width + (/\./.test( number ) ? 2 : 1) ).join( '0' ) + number;
      }
      return number + ""; // always return a string
    }

    function getHeaderImage() {
        var amountOfHeaderImages = 2;
        var imageNo = Math.floor((Math.random() * amountOfHeaderImages) + 1);
        $scope.imgURL = 'assets/img/home-intro/header-'+zeroFill(imageNo, 3)+'.png';
    }
    getHeaderImage();

    $rootScope.reloadHome = function () {
        getHeaderImage();
    };

    $scope.submitForm = function () {
        $state.go('search', {query: $scope.powersearch});
    };

    $scope.getGenderClass = function(gender) {
        if(gender=='m') {
            return 'gender-m';
        } else if(gender == 'f') {
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

app.controller('PowersearchCtrl', ['$scope', '$http', '$location', '$state', '$stateParams', '$rootScope', function ($scope, $http, $location, $state, $stateParams, $rootScope) {

    $scope.limit = 10;

    function search(query) {
        $http.get($rootScope.ApiUrl + '/?a=search&query=' + query).success(function (data) {
            $scope.result = data;
            $scope.powersearch = query;
        });
    }

    $scope.submitForm = function () {
        $state.go('search', {query: $scope.powersearch});
    }

    // open and close result boxes
    $scope.animateBox = function (e) {
        var element = jQuery(e.currentTarget);

        var parentHeight = element.parent().prop('scrollHeight');

        if (element.hasClass('closed')) {
            element.parent().animate({height: parentHeight}, 200);
        } else {
            element.parent().animate({height: element.outerHeight(true)}, 200);
        }
        element.toggleClass('closed');

    }

    // if query is given, then search
    if ($stateParams.query) {
        search($stateParams.query);
    }
}]);

app.controller('LoginCtrl', ['$scope', '$http', '$state', '$stateParams', '$rootScope', '$window', function ($scope, $http, $state, $stateParams, $rootScope, $window) {
    // console.log("Login Controller reporting for duty.");
    // console.log($scope);
    $scope.doLogin = function (user) {
        // console.log("$scope.doLogin");
        $http.get($rootScope.ApiUrl + '/?a=user&b=login&user=' + user.username + '&password=' + user.password).success(function (data) {

            var status = data.status;

            if (status == 'ok') {
                $scope.errorMsg = 'Login ok ' + $scope.spr.welcome + ' ' + data.vorname + ' ' + data.nachname;
                $rootScope.user = user.username;
                $rootScope.userLevel = parseInt(data.level);
                $rootScope.instComment = parseInt(data.inst_comment);
                $rootScope.authToken = data.token;
                $window.sessionStorage.authToken = data.token;
                $http.defaults.headers.common['X-AuthToken'] = $rootScope.authToken;
            } else {
                $scope.errorMsg = 'Bad login';
            }

        });
    }
    $scope.doLogout = function () {
        // console.log("$scope.doLogout");
        $http.get($rootScope.ApiUrl + '/?a=user&b=logout').success(function (data) {
            var resp = data;

            $rootScope.user = '';
            $rootScope.userLevel = 0;
            $rootScope.authToken = '';
            $rootScope.instComment = 0;
            $http.defaults.headers.common['X-AuthToken'] = undefined;
            $window.sessionStorage.authToken = undefined;

        });
    }
}]);

app.controller('ExhibitionCtrl', [
    '$scope',
    '$http',
    '$location',
    '$state',
    '$stateParams',
    '$rootScope',
    '$uibModalStack',
    function ($scope, $http, $location, $state, $stateParams, $rootScope, $uibModalStack) {
        $uibModalStack.dismissAll();

        $rootScope.setTitle('fotoCH - Fotografie Ausstelungen in der Schweiz');
        $scope.translations = $rootScope.translations;

        var id = $stateParams.id

        if(id) {
            $http.get($rootScope.ApiUrl + '/?a=exhibition&id=' + id).success(function (data) {
                $scope.detail = data;
            });
        }

    }
]);

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
    function ($scope, $http, $location, $state, $stateParams, $rootScope, $filter, $timeout, $q, $uibModalStack) {
        $uibModalStack.dismissAll();

        $rootScope.setTitle('fotoCH - Fotografen');

        var id = $stateParams.id;
        $scope.translations = $rootScope.translations;

        if (id) {
            /**
             *  detailpage
             */
            $http.get($rootScope.ApiUrl + '/?id=' + id).success(function (data) {
                $scope.readMoreLimit = 50;
                $scope.detail = data;
                $scope.list = null;
            });
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
    function ($scope, $http, $state, $stateParams, $location, $rootScope, $filter, $cacheFactory, $timeout, $uibModalStack) {
        $uibModalStack.dismissAll();

        $rootScope.setTitle('fotoCH - Fotos Schweiz');

        var id = $stateParams.id;

        if (id) {
            /*
             Detailpage
             */
            $scope.doComments = false;
            $http.get($rootScope.ApiUrl + '/?a=photo&id=' + id).success(function (data) {
                $scope.photo = data;
                    
                if (parseInt(data.inst_id)==$rootScope.instComment){
                    if (data.comment){
                        $scope.comment=data.comment;
                    } else {
                        $scope.comment={name: 'Fehler, beim Laden der Kommentare, das sollte nicht passieren...'};
                    }

                    $scope.doComments = true;

                    $scope.submit = function () {
                        console.log($scope.comment);
                        $http.post($rootScope.ApiUrl + '/?a=photo&id=' + id, $scope.comment).success(function (data) {
                           //console.log(data);
                        });
                        return;
                    }
                }
            });
        }
    }
]);

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

        $scope.change = function(user) {

            $scope.user = user;
            $scope.isLoading = true;

            // avoid query spams
            clearTimeout($scope.timeout);
            $scope.timeout = setTimeout(function() {
                $scope.result =  {};
                $rootScope.textualSearch = $scope.user.query;
                $http({
                    method: "GET",
                    url: $rootScope.ApiUrl + '/?a=streamsearch&query=' + $scope.user.query 
                        + '&limit='+$scope.limit 
                        + '&photolimit='+$scope.photolimit,
                    headers: {
                       'Content-Type': "text/plain"
                    },
                    transformResponse: [function (data) {
                      return data;
                    }],
                    onProgress: function(event) {
                        try {
                            var response = event.currentTarget.responseText;
                            response = response.replace(/}{/g, "},{");
                            response = "[" + response + "]";
                            var newresult = JSON.parse(response);
                            newresult = newresult[newresult.length-1];
                            $scope.$apply(function(){
                                $scope.result = newresult;
                            });
                        } catch (e) {
                            console.log("Invalid response: " + event.currentTarget.responseText);
                            console.log(e);
                        }
                    }
                }).then(function(e) {
                    $scope.isLoading = false;
                });

            }, 1500);
        }

        $scope.focus = function() {
            $scope.searchActive = true;
        }

        $scope.blur = function() {
            if(typeof($scope.user.query) !== 'undefined' && $scope.user.query.length > 0) {
                $scope.searchActive = true;
            } else {
                $scope.searchActive = false;
            }
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
    function ($scope, $http, $location, $state, $stateParams, $rootScope, $uibModalStack) {
        // close all open modals
        $uibModalStack.dismissAll();

        $rootScope.setTitle('fotoCH - Literatur zur Schweizer Fotografie');
        $scope.translations = $rootScope.translations;
        var id = $stateParams.id;

        // detail
        if(id) {
            $http.get($rootScope.ApiUrl + '/?a=literature&id=' + id).success(function (data) {
                $scope.detail = data;
                $scope.list = null;
            });
        }

        $scope.close = function() {
            $location.search('id', null);
            $scope.$close(true);
        }

    }
]);




