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
        if ($location.path().substr(0, path.length) == path) {
            return "is-active"
        } else {
            return ""
        }
    };

}]);

app.controller('InstitutionCtrl', ['$scope', '$http', '$location', '$state', '$stateParams', '$rootScope', '$filter', function ($scope, $http, $location, $state, $stateParams, $rootScope, $filter) {
    // console.log("Institution Controller reporting for duty.");

    var id = $stateParams.id
    var anf = $stateParams.anf;
    var query = $stateParams.query;

    if (!id) {
        /**
         *  Overview
         */

            // load all institutions
        $scope.loading = true;
        $http.get($rootScope.ApiUrl + '/?a=institution', { cache: true }).success(function (data) {
            $scope.list = data;
            onLoaded();
        });

        // things to do after ajax-content has loaded successfully
        var onLoaded = function () {
            $scope.loading = false;

            configureFilters();

            $scope.filtersReady = true;
            // filter photographer on every change of the filter model
            $scope.$watchCollection('filterInstitutions', function (n, o) {
                filterInstitutions();
            });
        };

        var configureFilters = function () {
            if (query) {
                $scope.filterInstitutions = {"$": query};
            }
        }


        // filtering institutions before passing to directive (a little ugly, but results in better performance - cause no exchange between scopes needed)
        var filterInstitutions = function () {
            $scope.filteredInstitutions = $filter('filter')($scope.list.res, $scope.filterInstitutions);

            // filter on first char (speacial chars not included)
            if ($scope.firstChar) {
                var filteredInstitutions = [];
                $scope.filteredInstitutions.forEach(function (item) {
                    if (item.name.charAt(0).toUpperCase() == $scope.firstChar) {
                        filteredInstitutions.push(item);
                    }
                });
                $scope.filteredInstitutions = filteredInstitutions;
            }
        }

        $scope.setFirstChar = function (char) {
            $scope.firstChar = char;
            filterInstitutions();
        }

        $scope.resetFilter = function () {
            $scope.filterInstitutions = {};
        }

        // remove undefined from filter model (angular error). Needed when select-option value=""
        $scope.updateSelect = function (val) {
            if (val === null) {
                angular.forEach($scope.filterInstitutions, function (value, index) {
                    if (value === null) {
                        this[index] = '';
                    }
                }, $scope.filterInstitutions);

            }
        }

    } else {
        /**
         *  Detailpage
         */

        $http.get($rootScope.ApiUrl + '/?a=institution&id=' + id).success(function (data) {
            $scope.detail = data;
            $scope.list = null;
        });

    }


    $scope.institutionSelected = function (selected) {
        //window.alert('You have selected ' + selected.originalObject.id);
        $state.go('institutionDetail', {id: selected.originalObject.id, anf: ''});
    };

    $scope.enterFunc = function (selected) {
        var val = document.getElementById('institution-autocomplete_value').value;
        $state.go('institution', {anf: val});
    };


    //$scope.debug='anf:'+anf+' id:'+id+$state;
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
        $http.get($rootScope.ApiUrl + '/?a=photo&random=4').success(function (data) {
            $scope.photos = data.res;
            $scope.limit = 4;
        });
    }

    loadContent();

    $rootScope.reloadHome = function () {
        loadContent();
    };

    $scope.submitForm = function () {
        $state.go('search', {query: $scope.powersearch});

    };
}]);

app.controller('PowersearchCtrl', ['$scope', '$http', '$location', '$state', '$stateParams', '$rootScope', function ($scope, $http, $location, $state, $stateParams, $rootScope) {

    $scope.limit = 6;

    function search(query) {
        $http.get($rootScope.ApiUrl + '/?a=search&query=' + query).success(function (data) {
            $scope.result = data;
            $scope.powersearch = query;
            //console.log($scope.result);
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
        $scope.loading = true;
        $http.get($rootScope.ApiUrl + '/?a=exhibition&anf=' + anf).success(function (data) {

            $scope.list = data;
            $scope.loading = false;
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

app.controller('PhotographerCtrl', ['$scope', '$http', '$location', '$state', '$stateParams', '$rootScope', '$filter', '$timeout', '$q', function ($scope, $http, $location, $state, $stateParams, $rootScope, $filter, $timeout, $q) {
    // console.log("Fotograf Controller reporting for duty.");

    var id = $stateParams.id
    var anf = $stateParams.anf;
    var query = $stateParams.query;
    $scope.activeChar = anf;
    $scope.input = '';


    $scope.photographerSelected = function (selected) {
        //window.alert('You have selected ' + selected.originalObject.id);
        $state.go('photographerDetail', {id: selected.originalObject.id, anf: ''});
    };

    $scope.enterFunc = function (selected) {
        var val = document.getElementById('photographer-autocomplete_value').value;
        $state.go('photographer', {anf: val});
    };

    if (!id) {
        /*
         Overview/search-page
         */
        var limitExpander = 20;
        var cachedFilters = $rootScope.filterCache.get('filterPhotographer');
        var cachedLimit = $rootScope.filterCache.get('limitPhotographer');

        // set filters
        if (cachedFilters !== undefined) {
            $scope.filterPhotographer = $rootScope.filterCache.get('filterPhotographer');
            console.log($scope.filterPhotographer);
        } else {
            $scope.filterPhotographer = {"$": query};
        }

        // set limit
        if (cachedLimit !== undefined) {
            $scope.limit = $rootScope.filterCache.get('limitPhotographer');
        } else {
            $scope.limit = limitExpander;
        }

        // cache filterObject & limit on page change (only to detail)
        $scope.$on('$stateChangeStart', function (event, toState) {
            if (toState.name == 'photographerDetail') {
                $rootScope.filterCache.put('filterPhotographer', $scope.filterPhotographer);
                $rootScope.filterCache.put('limitPhotographer', $scope.limit);
            } else {
                $rootScope.filterCache.remove('filterPhotographer');
                $rootScope.filterCache.remove('limitPhotographer');
            }
        });

        // filtering photographers before passing to directive (a little ugly, but results in better performance - cause no exchange between scopes needed)
        var filterPhotographers = function () {
            var time = new Date();
            // clone object, since we don't want to change the current filter object
            var filterObj = jQuery.extend({}, $scope.filterPhotographer);

            //filter on array values with custom comparator
            /*$scope.filteredPhotographer = $filter('filter')($scope.list.res, filterObj, comparator);

             //remove array from filterObject clone (already filtered)
             angular.forEach(filterObj, function(value, index, array){
             if(angular.isArray(value)){
             this[index] = undefined;
             }
             }, filterObj);
             */
            // filter rest
            $scope.filteredPhotographer = $filter('filter')($scope.list.res, filterObj);
        }

        $scope.comparator = function (actual, expected) {

            if (angular.isArray(expected) && actual) {
                for (i in expected) {
                    if (actual.indexOf(expected[i]) > -1) {
                        //console.log(expected[i] + ' gefunden in ' + actual)
                        return true;
                    }
                }
            } else if (angular.isString(expected) && actual) {
                if (actual.indexOf(expected) > -1) {
                    return true;
                }
            }
            return false;
        }

        // toggle filter (only mobile version)
        $scope.toggleFilter = function () {
            if ($scope.filterClass === 'active') {
                $scope.filterClass = 'inactive';
            } else {
                $scope.filterClass = 'active';
            }
        };

        // get filters from result array
        var configureFilters = function () {
            var begin = new Date();
            $scope.filter = {};

            //  detect if web workers available
            if (typeof(Worker) !== "undefined") {

                console.log(new Date());

                $scope.callWebWorker = function () {

                    var worker = new Worker('app/filterPhotographer.js');
                    var defer = $q.defer();
                    worker.onmessage = function (e) {
                        defer.resolve(e.data);
                        worker.terminate();
                    };
                    worker.postMessage($scope.list.res);
                    return defer.promise;
                }

                $scope.callWebWorker().then(function (workerReply) {
                    $scope.filter.fotografengattungen = workerReply[0];
                    $scope.filter.bildgattungen = workerReply[1];
                    $scope.filter.kanton = workerReply[2];
                    $scope.filter.venues = workerReply[3];
                    // display filters
                    $scope.filtersReady = true;
                });

            } else {
                // add filters to array
                var fotografengattungen = '';
                var bildgattungen = '';
                var kanton = '';
                var venues = '';

                angular.forEach($scope.list.res, function (value, index, array) {
                    if (value.fotografengattungen != '') {
                        fotografengattungen = fotografengattungen + value.fotografengattungen + ',';
                    }
                    if (value.bildgattungen != '') {
                        bildgattungen = bildgattungen + value.bildgattungen + ',';
                    }
                    if (value.kanton != '') {
                        kanton = kanton + value.kanton + ',';
                    }
                    if (value.arbeitsperioden != '') {
                        venues = venues + value.arbeitsperioden + ',';
                    }

                    //$scope.list.res[index].fotografengattungenstring = value.fotografengattungen.toString();
                    //$scope.list.res[index].bildgattungenstring = value.bildgattungen.toString();
                }, $scope.list.res);

                // set filters

                $scope.filter.fotografengattungen = $filter('unique')(fotografengattungen.split(',').filter(Boolean));
                $scope.filter.bildgattungen = $filter('unique')(bildgattungen.split(',').filter(Boolean));
                $scope.filter.kanton = $filter('unique')(kanton.split(',').filter(Boolean));
                $scope.filter.venues = $filter('unique')(venues.split(',').filter(Boolean));
                if (query) {
                    $scope.filter.searchfield = query;
                }

                // display filters
                $scope.filtersReady = true;
            }

            var middle = new Date();

            filterPhotographers();

            // filter photographer on every change of the filter model
            $scope.$watchCollection('filterPhotographer', function (n, o) {
                filterPhotographers();
            });

            $scope.resetFilter = function () {
                $scope.filterPhotographer = {};
            }

            $scope.loading = false;
            /*
             console.log(begin);
             console.log(middle);
             console.log(new Date());*/
        }

        // show more results
        $scope.loadMore = function () {
            $scope.limit = $scope.limit + limitExpander;
        }

        // remove undefined from filter model (angular error). Needed when select-option value=""
        $scope.updateSelect = function (val) {
            if (val === null) {
                angular.forEach($scope.filterPhotographer, function (value, index) {
                    if (value === null) {
                        $scope.filterPhotographer[index] = '';
                    }
                });
            }
        }

        // ajax calls
        $scope.loading = true;
        if (anf >= 'A') {
            $http.get($rootScope.ApiUrl + '/?anf=' + anf, { cache: true }).success(function (data) {
                $scope.list = data;
                configureFilters();
            });
        } else {
            $scope.$on('$viewContentLoaded', function (event) {
                //prevent browser from freezing before state change
                $timeout(loadPhotographers, 0);
            });

            var loadPhotographers = function () {
                $http.get($rootScope.ApiUrl + '/?a=photographer', { cache: true }).success(function (data) {

                    /**
                     $scope.callWebWorker = function () {

                        var worker = new Worker('app/assignJSON.js');
                        var defer = $q.defer();
                        worker.onmessage = function(e) {
                            defer.resolve(e.data);
                            worker.terminate();
                        };
                        worker.postMessage(data);
                        return defer.promise;
                    }

                     $scope.callWebWorker().then(function (workerReply) {
                        $scope.list = workerReply;
                        configureFilters();
                    });
                     */
                    $scope.list = data;
                    $timeout(configureFilters, 0);
                }).error(function (data, status) {
                        $scope.loading = false;
                        $scope.loadingError = true;
                    });
            }
        }
    } else {
        /**
         *  detailpage
         */
        $http.get($rootScope.ApiUrl + '/?id=' + id).success(function (data) {
            $scope.readMoreLimit = 50;
            $scope.detail = data;
            $scope.list = null;
        });

        // remove filter if not returning to overview
        $scope.$on('$stateChangeStart', function (event, toState) {
            if (toState.name != 'photographer') {
                $rootScope.filterCache.remove('filterPhotographer');
                $rootScope.filterCache.remove('limitPhotographer');
            }
        });

    }
    var abc = new Array();

    for (var i = 0; i < 26; i++) {
        abc[i] = String.fromCharCode(65 + i);
    }
    $scope.abc = abc;
}]);

app.controller('PhotoCtrl', ['$scope', '$http', '$state', '$stateParams', '$location', '$rootScope', '$filter', '$cacheFactory', '$timeout', function ($scope, $http, $state, $stateParams, $location, $rootScope, $filter, $cacheFactory, $timeout) {

    var anf = $stateParams.anf;
    var id = $stateParams.id;

    if (!id) {
        /*
         Overview
         */
        $scope.loading = true;
        $scope.filterClass = 'inactive';
        $scope.viewClass = '';
        $scope.filterDate = {};
        $scope.filterDate.from = 1839;
        $scope.filterDate.to = new Date().getFullYear();
        var cachedFilters = $rootScope.filterCache.get('filterPhotos');
        var cachedLimit = $rootScope.filterCache.get('limitPhotos');
        var cachedViewClass = $rootScope.filterCache.get('viewClass');
        var limitExpander = 12;


        // set filters
        if (cachedFilters !== undefined) {
            $scope.filterPhotos = $rootScope.filterCache.get('filterPhotos');
        } else {
            $scope.filterPhotos = {"$": $stateParams.query};
        }
        // set limit
        if (cachedLimit !== undefined) {
            $scope.limit = $rootScope.filterCache.get('limitPhotos');
        } else {
            $scope.limit = limitExpander;
        }
        // set limit
        if (cachedLimit !== undefined) {
            $scope.viewClass = $rootScope.filterCache.get('viewClass');
        } else {
            $scope.viewClass = '';
        }

        // cache filterObject & limit on page change (only to detail)
        $scope.$on('$stateChangeStart', function (event, toState) {
            if (toState.name == 'photoDetail') {
                $rootScope.filterCache.put('filterPhotos', $scope.filterPhotos);
                $rootScope.filterCache.put('limitPhotos', $scope.limit);
                $rootScope.filterCache.put('viewClass', $scope.viewClass);
            } else {
                $rootScope.filterCache.remove('filterPhotos');
                $rootScope.filterCache.remove('limitPhotos');
                $rootScope.filterCache.remove('viewClass');
            }
        });

        // toggle filter (only mobile version)
        $scope.toggleFilter = function () {
            if ($scope.filterClass === 'active') {
                $scope.filterClass = 'inactive';
            } else {
                $scope.filterClass = 'active';
            }
        };

        $scope.changeView = function (cssClass) {
            if (cssClass) {
                $scope.viewClass = cssClass;
            } else {
                $scope.viewClass = '';
            }
        };

        $scope.loadMore = function () {
            $scope.limit = $scope.limit + limitExpander;
        }


        $scope.filterExcludeNullStockId = function () {
            return function (photo) {
                return photo.stock_id !== null;
            };
        }

        $scope.filterExcludeNullInstitutionId = function () {
            return function (photo) {
                return photo.institution !== null;
            };
        }

        $scope.updateSelect = function (val) {
            if (val === null) {
                angular.forEach($scope.filterPhotos, function (value, index, array) {
                    if (value === null) {
                        $scope.filterPhotos[index] = '';
                    }
                });
            }
        }

        $scope.$watchCollection('filterPhotos', function (n, o) {
            filterPhotos();
        });
        /*
         $scope.$watchCollection('filterDate', function (n, o) {
         var debouncing = function(n,o){
         if($(document).mous){
         filterPhotos();
         }else{
         console.log('not same');
         }
         }
         $timeout(debouncing, 300, true, n, o);

         });*/

        $scope.filterYear = function () {
            filterPhotos();
        }

        // filtering photos before passing to directive (a little ugly, but results in better performance) (?)
        var filterPhotos = function () {
            $scope.filteredPhotos = $filter('filter')($scope.photos, $scope.filterPhotos);
            if ($scope.filteredPhotos && $scope.allowDateFilter && $scope.filterDate) {

                var ms = new Date().getMilliseconds();
                var filteredPhotos = [];
                $scope.filteredPhotos.forEach(function (item) {
                    var from = new Date($scope.filterDate.from.toString());
                    var to = new Date($scope.filterDate.to.toString());
                    var date = new Date(item.dc_created);
                    if (from <= date && date <= to) {
                        filteredPhotos.push(item);
                    }
                });
                $scope.filteredPhotos = filteredPhotos;
                //--> 30-40ms
            }
        }

        $scope.resetFilter = function () {
            $scope.filterPhotos = {};
            $scope.allowDateFilter = false;
            $scope.filterDate.from = 1839;
            $scope.filterDate.to = new Date().getFullYear();
        }

        $http.get($rootScope.ApiUrl + '/?a=photo', { cache: true }).success(function (data) {
            $scope.loading = false;
            $scope.photos = data.res;
            filterPhotos();
        });
    } else {
        /*
         Detailpage
         */
        $http.get($rootScope.ApiUrl + '/?a=photo&id=' + id).success(function (data) {
            $scope.photo = data;
        });

        // remove filter if not returning to overview
        $scope.$on('$stateChangeStart', function (event, toState) {
            if (toState.name != 'photo') {
                $rootScope.filterCache.remove('filterPhotos');
                $rootScope.filterCache.remove('limitPhotos');
                $rootScope.filterCache.remove('viewClass');
            }
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


