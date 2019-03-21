/**
 * FILTER
 */


/** 
 * ADD + debug_version behind a template to avoid the cache
 **/
var d = new Date();
var debug_version = '?version=' + d.getTime();


app.filter('rawHtml', ['$sce', function ($sce) {
    return function (val) {
        return $sce.trustAsHtml(val);
    };
}]);


app.filter('readMoreFilter', ['$sce', function ($sce) {
    return function(str, args) {
        var strToReturn = str,
            length = str.length,
            foundWords = [],
            countingWords = (!!args[1]);

        if (!str || str === null) {
            // If no string is defined return the entire string and warn user of error
            console.log("Warning: Truncating text was not performed as no text was specified");
        }

        // Check length attribute
        if (!args[2] || args[2] === null) {
            // If no length is defined return the entire string and warn user of error
            console.log("Warning: Truncating text was not performed as no length was specified");
        } else if (typeof args[2] !== "number") { // if parameter is a string then cast it to a number
            length = Number(args[2]);
        }

        if (length <= 0) {
            return "";
        }


        if (str) {
            if (countingWords) { // Count words

                foundWords = str.split(/\s+/);

                if (foundWords.length > length) {
                    strToReturn = foundWords.slice(0, length).join(' ') + '...';
                }

            } else {  // Count characters

                if (str.length > length) {
                    strToReturn = str.slice(0, length) + '...';
                }
            }
        }
        return $sce.trustAsHtml(strToReturn);
    };
}]);

/**
 * DIRECTIVES
 */

app.directive('contentDefault', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/content/default.html',
        scope: {
            headline: '=',
            value: '='
        }
    };
});

/**
 * As seen on http://angularscript.com/angularjs-back-to-top-button-backtop/
 */

app.directive('scrollToTop', function () {
        return {
            restrict: 'E',
            transclude: true,
            replace: true,
            templateUrl: 'app/shared/navigation/scrollToTop.html',
            scope: {
                text: "@buttonText",
                textloaded: "@loadedText",
                loading: "=",
                loaded: "@loadednum",
                total: "@totalcnt"
            },
            link: function(scope, element) {

                scope.text = scope.text || 'Scroll top';
                scope.speed = parseInt(scope.speed, 10) || 300;

                var self = this;

                scope.currentYPosition = function() {
                    if (self.pageYOffset)
                        return self.pageYOffset;
                    if (document.documentElement && document.documentElement.scrollTop)
                        return document.documentElement.scrollTop;
                    if (document.body.scrollTop)
                        return document.body.scrollTop;
                    return 0;
                };

                scope.smoothScroll = function() {
                    var startY = scope.currentYPosition();
                    var stopY = 0;
                    var distance = stopY > startY ? stopY - startY : startY - stopY;
                    if (distance < 100) {
                        scrollTo(0, stopY);
                        return;
                    }
                    var speed = Math.round(scope.speed / 100);
                    var step = Math.round(distance / 25);
                    var leapY = stopY > startY ? startY + step : startY - step;
                    var timer = 0;
                    if (stopY > startY) {
                        for (var i = startY; i < stopY; i += step) {
                            setTimeout("window.scrollTo(0, " + leapY + ")", timer * speed);
                            leapY += step;
                            if (leapY > stopY) leapY = stopY;
                            timer++;
                        }
                        return;
                    }
                    for (var j = startY; j > stopY; j -= step) {
                        setTimeout("window.scrollTo(0, " + leapY + ")", timer * speed);
                        leapY -= step;
                        if (leapY < stopY) leapY = stopY;
                        timer++;
                    }
                };

                scope.button = element.find('button');

                scope.button.on('click', function() {
                    scope.smoothScroll();
                    element.removeClass('showbutton');
                });

                window.addEventListener('scroll', function() {
                    if (window.pageYOffset > 0) {
                        element.addClass('showbutton');
                    } else {
                        element.removeClass('showbutton');
                    }
                });
            }
        }
    });

app.directive('contentRaw', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/content/raw.html',
        controller: ['$scope', function($scope) {
            $scope.$watch('value', function() {
                var re = /data-target-id="([0-9]*)" data-target-type="([a-z]*)"/g; 
                var m;
                 
                while ((m = re.exec($scope.value)) !== null) {
                    if (m.index === re.lastIndex) {
                        re.lastIndex++;
                    }
                    $scope.value = $scope.value.replace(m[0], ' ng-click="$root.detail('+m[1]+', \''+m[2]+'\')" ');
                }
            });
        }],
        scope: {
            headline: '=',
            value: '='
        }
    };
});


// http://stackoverflow.com/questions/17417607/angular-ng-bind-html-and-directive-within-it
app.directive('compile', ['$compile', function ($compile) {
    return function(scope, element, attrs) {
        scope.$watch(
            function(scope) {
                // watch the 'compile' expression for changes
                return scope.$eval(attrs.compile);
            },
            function(value) {
                // when the 'compile' expression changes
                // assign it into the current DOM
                element.html(value);

                // compile the new DOM and link it to the current
                // scope.
                // NOTE: we only compile .childNodes so that
                // we don't get into infinite loop compiling ourselves
                $compile(element.contents())(scope);
            }
        );
    };
}])

app.directive('readMoreWrapper', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/content/readmore-wrapper.html',
        scope: {
            headline: '=',
            value: '=',
            showmore: '@',
            showless: '@'
        }
    };
});

app.directive('defaultList', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/list/defaultList.html',
        scope: {
            headline: '=',
            values: '=',
            linkTo: '@'
        }
    };
});

app.directive('historyBack', function () {
    return {
        restrict: 'A',
        scope: {back: '@back'},
        link: function (scope, elem, attrs) {
            elem.bind('click', function () {
                $window.history.back();
                scope.$apply();
            });
        }
    }
});


app.directive('inventoryList', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/list/inventoryList.html',
        scope: {
            inventories: '=',
            labels: '=',
            institution: '@'
        }
    };
});

app.directive('inventoryReference', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/list/inventoryReference.html' + debug_version,
        scope: {
            headline: '=',
            inventories: '=',
            labels: '=',
            hidetitle: '='
        }
    };
});

app.directive('photographerList', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/list/photographerList.html',
        transclude: true,
        scope: {
            photographer: '=',
            limit: '='
        }
    };
});

app.directive('photographerReference', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/list/photographerReference.html',
        scope: {
            headline: '=',
            photographer: '='
        }
    };
});

app.directive('institutionList', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/list/institutionList.html',
        scope: {
            institutions: '=',
            venue: '@'
        }
    };
});

app.directive('exhibitionList', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/list/exhibitionList.html',
        scope: {
            exhibitions: '=',
            labels: '='
        }
    };
});

app.directive('exhibitionReference', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/list/exhibitionReference.html',
        scope: {
            headline: '=',
            exhibitions: '=',
            labels: '='
        }
    };
});

app.directive('mediaPartner', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/media/mediaPartner.html',
        scope: {
            partner: '='
        }
    };
});

app.directive('photoTeaser', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/media/photoTeaser.html',
        scope: {
            photos: '=',
            labels: '=',
            headline: '=',
            query: '@'
        }
    };
});

app.directive('panel', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/panel/panel.html',
        scope: {
            headline: '='
        }
    };
});

app.directive('editingInfo', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/misc/editingInfo.html',
        scope: {
            date: '=',
            author: '=',
            labels: '='
        }
    };
});

app.directive('powerSearch', function () {
    return{
        restrict: 'AE',
        templateUrl: 'app/shared/form/powerSearch.html'
    }
});

app.directive('inContentMenu', function () {
    return{
        restrict: 'AE',
        templateUrl: 'app/shared/navigation/inContentMenu.html'
    }
});

app.directive('photoTeaserBox', function () {
    return{
        restrict: 'AE',
        templateUrl: 'app/shared/media/photoTeaserBox.html',
        scope: {
            photos: '=',
            filterPhotos: '=',
            limit: '=',
            imageRootUrl: '@',
            showDetail: '@'
        }
    }
});

app.directive('loadingIndicator', function () {
    return{
        restrict: 'AE',
        replace: true,
        templateUrl: 'app/shared/misc/loadingIndicator.html'
    }
});

app.directive('loadingError', function () {
    return{
        restrict: 'AE',
        replace: true,
        scope: {
            text: '@'
        },
        templateUrl: 'app/shared/misc/loadingError.html'
    }
});

app.directive("ngTouchend", function () {
    return {
        controller: function ($scope, $element, $attrs) {
            $element.bind('touchend', onTouchEnd);

            function onTouchEnd(event) {
                var method = '$scope.' + $element.attr('ng-touchend');
                $scope.$apply(function () {
                    eval(method);
                });
            };
        }
    };
});

var readMore = angular.module('readMore', []);
app.directive('readMore', function() {
    return {
        restrict: 'AE',
        scope: {
            text: '=ngModel',
            showless: '@',
            showmore: '@'
        },
        replace: true,
        templateUrl: 'app/shared/content/readmore.html',
        controller: ['$scope', '$attrs', '$element',
            function($scope, $attrs, $element) {
                $scope.textLength = $attrs.length;
                $scope.isExpanded = false; // initialise extended status
                $scope.countingWords = $attrs.words !== undefined ? ($attrs.words === 'true') : true; //if this attr is not defined the we are counting words not characters

                if (!$scope.countingWords && $scope.text.length > $attrs.length) {
                    $scope.showLinks = true;
                } else if ($scope.countingWords && $scope.text.split(" ").length > $attrs.length) {
                    $scope.showLinks = true;
                } else {
                    $scope.showLinks = false;
                }

                $scope.changeLength = function (card) {
                    $scope.height = '';
                    $scope.isExpanded = !$scope.isExpanded;
                    $scope.textLength = $scope.textLength !== $attrs.length ?  $attrs.length : $scope.text.length;
                };
            }]
    };
});

