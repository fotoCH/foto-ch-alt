/**
 * FILTER
 */
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

app.directive('contentRaw', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/content/raw.html',
        scope: {
            headline: '=',
            value: '='
        }
    };
});

app.directive('readMoreWrapper', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/content/readmore-wrapper.html',
        scope: {
            headline: '=',
            value: '='
        }
    };
});

app.directive('defaultList', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/list/defaultList.html',
        scope: {
            headline: '=',
            values: '='
        }
    };
});


app.directive('inventoryList', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/list/inventoryList.html',
        scope: {
            inventories: '=',
            labels: '='
        }
    };
});

app.directive('inventoryReference', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/list/inventoryReference.html',
        scope: {
            headline: '=',
            inventories: '=',
            labels: '='
        }
    };
});

app.directive('photographerList', function () {
    return {
        restrict: 'E',
        templateUrl: 'app/shared/list/photographerList.html',
        scope: {
            photographer: '='
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
            institutions: '='
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
            headline: '='
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

app.directive('photoTeaserBox', function () {
    return{
        restrict: 'AE',
        templateUrl: 'app/shared/media/photoTeaserBox.html'
    }
});

app.directive('loadingIndicator', function () {
    return{
        restrict: 'AE',
        replace: true,
        templateUrl: 'app/shared/misc/loadingIndicator.html'
    }
});

var readMore = angular.module('readMore', []);
app.directive('readMore', function() {
    return {
        restrict: 'AE',
        scope: {
            text: '=ngModel'
        },
        replace: true,
        templateUrl: 'app/shared/content/readmore.html',
        controller: ['$scope', '$attrs', '$element',
            function($scope, $attrs, $element) {
                //console.log($element.find('span.l-readmore__text'));
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
