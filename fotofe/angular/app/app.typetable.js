app.controller('TypeTableCtrl', [
    '$scope',
    '$http', 
    '$location', 
    '$state', 
    '$stateParams', 
    '$rootScope',
    '$window',
    function($scope, $http, $location, $state, $stateParams, $rootScope, $window) {
        $scope.sortDirection = 'desc';
        $scope.sortParameter = false;
        $scope.grid = $scope.display == 'grid' ? true : false;
        $scope.realFilters = [];

        $scope.filterModels = {};
        $scope.filterToggles = {};
        $scope.directFilters = [];

        $scope.queryLimit = 30;
        $scope.queryOffset = 0;

        $scope.tableHead = [];
        $scope.tableRows = [];

        $scope.totalcnt = 0;

        $scope.hasImage = false;

        $scope.fields_obj = JSON.parse($scope.fields);
        $scope.textquery = $rootScope.textualSearch;
        $scope.searchquery = $scope.textquery;
        $scope.textsearch_timeout = false;
        $scope.textsearch_focus = false;
        $scope.carousel = [];
        $scope.translations = $rootScope.translations;
        setTimeout(function() {
            $scope.translations = $rootScope.translations;
            $scope.setHeadings();
        }, 500);
        $scope.filtering = true;

        loadData();

        $scope.filterValue = function(value, target) {
            $scope.filtering = true;
            var toFilter = target+":"+value;
            if($scope.directFilters.indexOf(toFilter) == -1) {
                $scope.directFilters.push(toFilter);
            } else {
                $scope.directFilters.splice($scope.directFilters.indexOf(toFilter), 1);
            }
            $scope.queryOffset = 0;
            loadData();
        };

        $scope.filterActive = function(value, target) {
            var toFilter = target+":"+value;
            if($scope.directFilters.indexOf(toFilter) == -1) {
                return '';
            } else {
                return 'active';
            }
        };

        $scope.setFilter = function(key, index) {
            $http.get($rootScope.ApiUrl + '/?a=filters&type=' + key).success(function (data) {
                var sourceFilter = $scope.filters;
                if(typeof(sourceFilter) == 'string') {
                    sourceFilter = JSON.parse(sourceFilter);
                }
                var filter = {};
                filter.title = $scope.translations[key];
                if(typeof(filter.title) == 'undefined') {
                    var lang_key = sourceFilter[index][key];
                    if(lang_key.indexOf(".") > 0) {
                        lang_key = lang_key.split(".");
                        lang_key = lang_key[1];
                    }
                    filter.title = $scope.translations[lang_key]
                }
                filter.key = key;
                filter.target = sourceFilter[index][key];
                if(typeof(data.possible_values[0]) == 'object') {
                    filter.assoc = true;
                } else {
                    filter.assoc = false;
                }
                filter.values = data.possible_values;
                $scope.realFilters[index] = filter;
            });
        }

        $scope.prepareFilters = function() {
            if(typeof($scope.filters) !== 'undefined') {
                $scope.filters = JSON.parse($scope.filters);
                for(var filterIndex = 0; filterIndex < $scope.filters.length; filterIndex++) {
                    for(key in $scope.filters[filterIndex]) {
                        $scope.setFilter(key, filterIndex);
                    }
                }
            }
        }
        $scope.prepareFilters();

        $scope.currentViewClass = function() {
            if($scope.grid) {
                return 'to-table';
            } else {
                return 'to-grid';
            }
        }

        $scope.switchView = function() {
            if($scope.grid) {
                $scope.grid = false;
            } else {
                $scope.grid = true;
            }
        };

        $scope.increaseLimit = function() {
            if($scope.tableRows.length >= $scope.queryOffset + $scope.queryLimit) {
                $scope.filtering = true;
                $scope.queryOffset += $scope.queryLimit;
                // load data, but append to current dataset.
                loadData(true);
            }
        }

        $scope.setHeadings = function() {
            if(typeof($scope.translations) == 'undefined') {
                $http.get($rootScope.ApiUrl + '/?a=sprache&lang=' + $rootScope.lang).success(function (data) {
                    $rootScope.translations = data;
                    $scope.translations = data;
                    $scope.setHeadings();
                });
            } else {
                var sortings = $scope.sortings.split(", ");
                var index = 0;
                $scope.tableHead = [];
                for(var key in $scope.fields_obj) {
                    $scope.tableHead.push({
                        key : key,
                        title : $scope.translations[key],
                        sort: sortings[index]
                    });
                    index++;
                }
            }
        }
        $scope.setHeadings();

        $scope.reset = function() {
            $scope.textquery = '';
            $scope.searchquery = '';
            $scope.queryOffset = 0;
            $scope.textsearchblur();
            $scope.directFilters = [];
            loadData();
        }

        $scope.thisSortingClass = function (cell) {
            if($scope.sortParameter == cell) {
                return "sort-active " + $scope.sortDirection;
            }
            return "";
        }

        $scope.updateSorting = function(parameter) {
            $scope.filtering = true;
            $scope.queryOffset = 0;
            if($scope.sortDirection == 'desc') {
                $scope.sortDirection = 'asc';
            } else {
                $scope.sortDirection = 'desc';
            }
            $scope.sortParameter = parameter;
            $scope.tableRows = {};
            loadData();
        }

        $scope.textsearchfocus = function() {
            $scope.textsearch_focus = true;
        }

        $scope.textsearchblur = function() {
            if($scope.textquery.length > 0) {
                $scope.textsearch_focus = true;
            } else {
                $scope.textsearch_focus = false;
            }
        }

        $scope.textsearch = function(filter) {
            $window.scrollTo(0, 0);
            $scope.filtering = true;
            $scope.textquery = filter;
            $scope.queryOffset = 0;
            if($scope.textsearch_timeout)
                clearTimeout($scope.textsearch_timeout);
            $scope.tableRows = [];
            $scope.textsearch_timeout = setTimeout(function() {
                $scope.queryOffset = 0;
                loadData();
            }, 800);
        }

        $scope.parseCellValue = function(type, value) {
            if(type == "date") {
                return $scope.parseDate(value);
            }
            if(type == "shortenBySplit" || type == "shorten") {
                return $scope.shortenBySplit(value);   
            }
            if(type == 'nobreak') {
                return '<span class="nobreak">'+value+'</span>';
            }
            if(type == 'image') {
                $scope.hasImage = true;
                return '<img src="'+$rootScope.imageRootUrl+'/thumb/'+value+'" />';
            }
            return value;
        }

        $scope.parseDate = function(value) {
            if(value == '0000-01-01' || value == '0000-00-00') {
                return '-';
            } else if(value.indexOf('-00-00') != -1) {
                return value.substring(0, 4);
            } else {
                var date = new Date(value);
                return ('0' + date.getDate()).slice(-2) + '.' + ('0' + (date.getMonth()+1)).slice(-2) + '.' + date.getFullYear();
            }
        }

        $scope.shortenBySplit = function(value) {
            if(value == null)
                return '';
            var valueArray = value.split(",");
            if(valueArray.length > 2) {
                // display the first two...
                // the rest goes in to mouseover popup
                var firsts = [valueArray[0], valueArray[1]];
                var result = '<div class="cropped">';
                result += firsts.join(", ");
                result += '<ul>';
                for(var index = 2; index < valueArray.length; index++) {
                    result += '<li>'+valueArray[index]+'</li>';
                }
                result += '</ul>';
                result += '</div>';
                return result;
            } else {
                return valueArray.join(", ");
            }
        }

        function setValues(data, append) {
            if(typeof($scope.fields_obj) !== 'object') {
                $scope.fields_obj = JSON.parse($scope.fields_obj);
            }
            var rows = [];

            var regex = /({([^}]+)+})/g;
            var match;

            $scope.totalcnt = data[$scope.type+'_total_count'];

            $scope.carousel = [];
            for(var rowNo = 0; rowNo < data[$scope.type+'_results'].length; rowNo++) {
                // fill all ids in the carousel array for the detail overlay
                $scope.carousel.push(data[$scope.type+'_results'][rowNo].id);

                var completeRow = data[$scope.type+'_results'][rowNo];
                var rowId = completeRow.id;
                var row = [];
                for(var wanted_column in $scope.fields_obj) {
                    var rowValue = '';
                    var hasMatch = false;
                    var hasAtleastOneValue = false;
                    while ((match = regex.exec($scope.fields_obj[wanted_column])) !== null) {
                        if(!hasMatch)
                            hasMatch = '';
                        if (match.index === regex.lastIndex) {
                            regex.lastIndex++;
                        }
                        var potentialValue = match[2];
                        var splitted = [];
                        if(potentialValue != null) {
                            splitted = potentialValue.split(":");
                        } 
                        var value = '';
                        if(splitted.length > 1) {
                            // parse required
                            value = $scope.parseCellValue(splitted[0], completeRow[splitted[1]]);
                        } else {
                            value = completeRow[match[2]];
                        }

                        if(value == null || value == '') {
                            value = '';
                        } else {
                            hasAtleastOneValue = true;
                        }
                        if(hasMatch == '') {
                            hasMatch = $scope.fields_obj[wanted_column].replace(match[0], value);
                        } else {
                            hasMatch = hasMatch.replace(match[0], value);
                        }
                    }
                    if(hasMatch) {
                        if(hasAtleastOneValue) {
                            // remove comma at the end of value (if there is one, e.g. in name when vorname is not set)
                            row.push(hasMatch.trim().replace(/\,$/,''));
                        } else {
                            row.push('-');
                        }
                    } else {
                        var value = '-';
                        var toCheck = completeRow[$scope.fields_obj[wanted_column]];
                        if(typeof(toCheck) != 'undefined' || toCheck != null) {
                            value = toCheck;
                        }
                        row.push(value);
                    }
                }
                rows.push({
                    id: rowId, 
                    dataset: row
                });
            }
            if(typeof(append) !== 'undefined') {
                console.log('concat');
                console.log(rows);
                $scope.tableRows = $scope.tableRows.concat(rows);
            } else {
                console.log('all');
                console.log(rows);
                $scope.tableRows = rows;
            }
        }

        function loadData(append) {

            console.log('load data: ' + $scope.queryOffset);
            $scope.query = $rootScope.ApiUrl +
                '/?a=streamsearch'+
                '&type='+$scope.type +
                '&limit='+$scope.queryLimit +
                '&offset='+$scope.queryOffset;

            if($scope.directFilters.length > 0) {
                $scope.query += '&direct=' + $scope.directFilters.join(",");
            }

            var textquery = '';
            $rootScope.textualSearch = $scope.textquery;
            if($scope.textquery != '') {
                textquery = '&query='+$scope.textquery;
            }
            var sorting = '';
            if($scope.sortParameter != '') {
                sorting = '&sort='+$scope.sortParameter + '&sortdir='+$scope.sortDirection;
            }
            $http({
                method: "GET",
                url: $scope.query + textquery + sorting,
                headers: {
                   'Content-Type': "text/plain"
                },
                transformResponse: [function (data) {
                  return data;
                }]/*,
                onProgress: function(event) {
                    try {
                        console.log('progress');
                        var response = event.currentTarget.responseText;
                        response = response.replace(/}{/g, "},{");
                        response = "[" + response + "]";
                        var newresult = JSON.parse(response);
                        newresult = newresult[newresult.length-1];
                        setValues(newresult, append);
                    } catch (e) {
                        console.log(e);
                    }
                }*/
            }).then(function(response) {
                var data = response.data.replace(/}{/g, "},{");
                var result = JSON.parse("[" + data + "]");
                result = result[result.length - 1];
                setValues(result, append);
                $scope.filtering = false;
                $rootScope.loadednum = $scope.tableRows.length;
            });
        }

        if($scope.searchquery != '') {
            $scope.textsearchfocus();
        }
    }
]);

app.directive('typeTable', function () {
    return {
        restrict: 'E',
        scope: {
            fields: '@',
            detailRoute: '@',
            type: '@',
            sortings: '@',
            display : '@',
            filters: '@'
        },
        templateUrl: 'app/shared/content/typetable.html',
        controller: 'TypeTableCtrl'
    }
});

app.filter('orderObjectBy', function() {
  return function(items, field, reverse) {
    console.log(items, field);
    var filtered = [];
    angular.forEach(items, function(item) {
      filtered.push(item);
    });
    filtered.sort(function (a, b) {
      return (a[field] > b[field] ? 1 : -1);
    });
    if(reverse) filtered.reverse();
    return filtered;
  };
});
