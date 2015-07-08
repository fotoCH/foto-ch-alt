/**
 * Controllers
 */

app.controller('MainCtrl', ['$scope', '$http', '$state', '$stateParams', '$rootScope', '$location', 'languages', function ($scope, $http, $state, $stateParams, $rootScope, $location, languages) {
    // console.log("Main Controller reporting for duty.");

    function loadTranslation() {
        $http.get($rootScope.ApiUrl + '/?a=sprache&lang=' + $rootScope.lang).success(function (data) {
            $scope.spr = data;
        });
    }

    loadTranslation();

    $scope.setLanguage = function (lang) {
        console.log('switch to language', lang);
        $rootScope.lang = lang;
        $rootScope.isLangSwitchOpen = false;	// Close the language switch after selection of new language
        $rootScope.isMenuOpen = false;			// Close the mobile menu after selection of new language
        var hosta = $location.$$host.split('.');
        console.log($location);
        for (i = 0, len = hosta.length; i < len; ++i) {
            if (languages.indexOf(hosta[i]) >= 0) {
                hosta[i] = lang;
                var port = ($location.$$port == 80 ? ':' : ':' + $location.$$port);
                window.location.href = $location.$$protocol + "://" + (hosta.join('.')) + port + window.location.pathname + window.location.hash;
            }
        }

        loadTranslation();

        if ($state.includes('aboutFotoch') || $state.includes('contact')) {		// Reload content after switching language
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
    $rootScope.$on('$stateChangeSuccess',
        function () {
            $rootScope.isMenuOpen = false;
        });

    $rootScope.goBack = function(){
        window.history.back();
    }
}]);

app.controller('NavigationCtrl', ['$scope', '$location', '$rootScope', function ($scope, $location, $rootScope) {
    // console.log("Navigation Controller reporting for duty.");
    $scope.getClass = function (path) {
        if ($location.path().substr(0, path.length) == path) {
            return "is-active"
        } else {
            return ""
        }
    };

}]);

app.controller('PhotographerCtrl', ['$scope', '$http', '$location', '$state', '$stateParams', '$rootScope', function ($scope, $http, $location, $state, $stateParams, $rootScope) {
    // console.log("Fotograf Controller reporting for duty.");

    var id = $stateParams.id
    var anf = $stateParams.anf;
    $scope.input = '';
    $scope.photographerSelected = function (selected) {
        //window.alert('You have selected ' + selected.originalObject.id);
        $state.go('photographerDetail', {id: selected.originalObject.id, anf: ''});
    };

    $scope.enterFunc = function (selected) {

        var val = document.getElementById('photographer-autocomplete_value').value;
        $state.go('photographer', {anf: val});
    };


    //$scope.debug='anf:'+anf+' id:'+id+$state;
    if (anf >= 'A') {
        $http.get($rootScope.ApiUrl + '/?anf=' + anf).success(function (data) {
            $scope.list = data;
        });
    } else {
        if (id) {
            $http.get($rootScope.ApiUrl + '/?id=' + id).success(function (data) {
                $scope.readMoreLimit = 50;
                $scope.detail = data;
                $scope.list = null;
            });
        }
    }
    var abc = new Array();

    for (var i = 0; i < 26; i++) {
        abc[i] = String.fromCharCode(65 + i);
    }
    $scope.abc = abc;
}]);


app.controller('InstitutionCtrl', ['$scope', '$http', '$location', '$state', '$stateParams', '$rootScope', function ($scope, $http, $location, $state, $stateParams, $rootScope) {
    // console.log("Institution Controller reporting for duty.");

    var id = $stateParams.id
    var anf = $stateParams.anf;
    $scope.institutionSelected = function (selected) {
        //window.alert('You have selected ' + selected.originalObject.id);
        $state.go('institutionDetail', {id: selected.originalObject.id, anf: ''});
    };

    $scope.enterFunc = function (selected) {
        var val = document.getElementById('institution-autocomplete_value').value;
        $state.go('institution', {anf: val});
    };


    //$scope.debug='anf:'+anf+' id:'+id+$state;
    if (anf >= 'A') {
        $http.get($rootScope.ApiUrl + '/?a=institution&anf=' + anf).success(function (data) {
            $scope.list = data;
        });
    } else {
        if (id) {
            $http.get($rootScope.ApiUrl + '/?a=institution&id=' + id).success(function (data) {
                $scope.detail = data;
                $scope.list = null;
            });
        }
    }
    var abc = new Array();

    for (var i = 0; i < 26; i++) {
        abc[i] = String.fromCharCode(65 + i);
    }
    $scope.abc = abc;
}]);

app.controller('InventoryCtrl', ['$scope', '$http', '$location', '$state', '$stateParams', '$rootScope', function ($scope, $http, $location, $state, $stateParams, $rootScope) {
    // console.log("Inventory Controller reporting for duty.");

    var id = $stateParams.id
    var anf = $stateParams.anf;
    $scope.institutionSelected = function (selected) {
        //window.alert('You have selected ' + selected.originalObject.id);
        $state.go('inventoryDetail', {id: selected.originalObject.id, anf: ''});
    };

    //$scope.debug='anf:'+anf+' id:'+id+$state;
    if (anf >= 'A') {
        $http.get($rootScope.ApiUrl + '/?a=inventory&anf=' + anf).success(function (data) {
            $scope.list = data;
        });
    } else {
        if (id) {
            $http.get($rootScope.ApiUrl + '/?a=inventory&id=' + id).success(function (data) {
                $scope.detail = data;
                $scope.list = null;
            });
        }
    }
    var abc = new Array();

    for (var i = 0; i < 26; i++) {
        abc[i] = String.fromCharCode(65 + i);
    }
    $scope.abc = abc;
}]);

app.controller('StaticPageCtrl', ['$scope', '$http', '$location', '$state', '$stateParams', '$rootScope', function ($scope, $http, $location, $state, $stateParams, $rootScope) {
    // console.log("Static Page Controller reporting for duty.");

    function loadContent() {
        $http.get($rootScope.ApiUrl + '/?a=pages&lang=' + $rootScope.lang).success(function (data) {
            $scope.pages = data;
        });
    }

    loadContent();

    $rootScope.reloadPages = function () {
        loadContent();
    };
}]);

app.controller('HomeCtrl', ['$scope', '$http', '$location', '$state', '$stateParams', '$rootScope', function ($scope, $http, $location, $state, $stateParams, $rootScope) {

    function loadContent() {
        $http.get($rootScope.ApiUrl + '/?a=inventory&id=234').success(function (data) {
            $scope.photos = data.photos;
            $scope.limit = 4;
        });
    }

    loadContent();

    $rootScope.reloadHome = function () {
        loadContent();
    };

    $scope.submitForm = function(){
        $state.go('search', {query: $scope.powersearch});

    };
}]);

app.controller('PowersearchCtrl', ['$scope', '$http', '$location', '$state', '$stateParams', '$rootScope', function ($scope, $http, $location, $state, $stateParams, $rootScope) {

    $scope.limit = 10;

    function search(query) {
        $http.get($rootScope.ApiUrl + '/?a=search&query=' + query).success(function (data) {
            $scope.result = data;
            //console.log($scope.result);
        });
    }

    if($stateParams.query){
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
            $rootScope.userLevel = '';
            $rootScope.authToken = '';
            $http.defaults.headers.common['X-AuthToken'] = undefined;
            $window.sessionStorage.authToken = undefined;

        });
    }
}]);

app.controller('ExhibitionCtrl', ['$scope', '$http', '$location', '$state', '$stateParams', '$rootScope', function ($scope, $http, $location, $state, $stateParams, $rootScope) {
    // console.log("Exhibition Controller reporting for duty.");

    var id = $stateParams.id
    var anf = $stateParams.anf;
    $scope.exhibitionSelected = function (selected) {
        //window.alert('You have selected ' + selected.originalObject.id);
        $state.go('exhibitionDetail', {id: selected.originalObject.id, anf: ''});
    };

    $scope.enterFunc = function (selected) {
        var val = document.getElementById('exhibition-autocomplete_value').value;
        $state.go('exhibition', {anf: val});
    };

    //$scope.debug='anf:'+anf+' id:'+id+$state;
    if (anf >= 'A') {
        $http.get($rootScope.ApiUrl + '/?a=exhibition&anf=' + anf).success(function (data) {
            $scope.list = data;
        });
    } else {
        if (id) {
            $http.get($rootScope.ApiUrl + '/?a=exhibition&id=' + id).success(function (data) {
                $scope.detail = data;
                $scope.list = null;
            });
        }
    }
    var abc = new Array();

    for (var i = 0; i < 26; i++) {
        abc[i] = String.fromCharCode(65 + i);
    }
    $scope.abc = abc;
}]);

// Controller for the contact form on the contact form
app.controller('contactFormCtrl', function ($scope, $http) {
    // console.log("Contact Form Controller reporting for duty.")
    $scope.formData = {};

    $scope.processForm = function () {
        $http({
            method: 'POST',
            url: 'sendContactMail.php',
            data: $.param($scope.formData), 		// Pass in data as strings
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            } 		// Set headers so angular passing info as form data (not request payload)
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

app.controller('PhotoCtrl', ['$scope', '$http', '$state', '$stateParams', '$location', '$rootScope', function ($scope, $http, $state, $stateParams, $location, $rootScope) {

    var anf = $stateParams.anf;
    var id = $stateParams.id;
    $scope.filterClass = 'inactive';
    $scope.limit = 12;
    $scope.viewClass = '';

    $scope.toggleFilter = function(){
        if($scope.filterClass === 'active'){
            $scope.filterClass = 'inactive';
        }else{
            $scope.filterClass = 'active';
        }
    };

    $scope.changeView = function(cssClass){
        if(cssClass){
            $scope.viewClass = cssClass;
        }else{
            $scope.viewClass = '';
        }

        if($scope.filterClass === 'active'){
            $scope.filterClass = 'inactive';
        }else{
            $scope.filterClass = 'active';
        }
    };

    $scope.loadMore = function(){
        $scope.limit = $scope.limit + 12;
    }

    $scope.filterExcludeNullStockId = function(){
        return function( photo ) {
            return photo.stock_id !== null;
        };
    }

    $scope.filterExcludeNullInstitutionId = function(){
        return function( photo ) {
            return photo.institution !== null;
        };
    }

    $scope.updateSelect = function(val){
        if(val === null){
            angular.forEach($scope.filterPhotos,function(value,index, array){
                if(value === null){
                    $scope.filterPhotos[index] = '';
                }
            });
        }
    }

    if (!id) {
        /*
         Overview
         */
        $http.get($rootScope.ApiUrl + '/?a=photo').success(function (data) {
            $scope.list = data;
            $scope.photos = data.res;
        });
    } else {
        /*
         Detailpage
         */
        $http.get($rootScope.ApiUrl + '/?a=photo&id=' + id).success(function (data) {
            $scope.photo = data;
        });

    }


}]);

app.controller('TestCtrl', ['$scope', '$http', '$state', '$stateParams', '$rootScope', '$location', 'languages', function ($scope, $http, $state, $stateParams, $rootScope, $location, languages) {
    console.log("Test Controller reporting for duty.");
    hosta = $location.$$host.split('.');
    if (hosta[0] == 'www') hosta.shift();
    if (hosta.length > 0 && ((l = languages.indexOf(hosta[0])) >= 0)) {
        console.log("GUI-Language from URL-host: " + hosta[0]);
    }
    //console.log($location.$$host);
    //console.log(languages);
    // console.log($scope);
}]);


