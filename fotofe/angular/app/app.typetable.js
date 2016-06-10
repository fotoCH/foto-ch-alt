app.controller('TypeTableCtrl', [
    '$scope',
    '$http', 
    '$location', 
    '$state', 
    '$stateParams', 
    '$rootScope', 
    function($scope, $http, $location, $state, $stateParams, $rootScope) {
        $scope.sortDirection = 'desc';
        $scope.sortParameter = false;

        $scope.tableHead = [];
        $scope.tableRows = [];
        $scope.query = $rootScope.ApiUrl + '/?a=streamsearch&type='+$scope.type;
        $scope.fields = JSON.parse($scope.fields);
        $scope.textquery = '';
        $scope.textsearch_timeout = false;
        $scope.textsearch_focus = false;
        $scope.translations = $rootScope.translations;
        $scope.filtering = true;
        

        function setHeadings() {
            var sortings = $scope.sortings.split(", ");
            var index = 0;
            for(var key in $scope.fields) {
                $scope.tableHead.push({
                    key : key,
                    title : $scope.translations[key],
                    sort: sortings[index]
                });
                index++;
            }
        }

        $scope.thisSortingClass = function (cell) {
            if($scope.sortParameter == cell) {
                return "sort-active " + $scope.sortDirection;
            }
            return "";
        }

        $scope.updateSorting = function(parameter) {
            $scope.filtering = true;
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
            $scope.filtering = true;
            $scope.textquery = filter;
            if($scope.textsearch_timeout)
                clearTimeout($scope.textsearch_timeout);
            $scope.tableRows = {};
            $scope.textsearch_timeout = setTimeout(function() {
                loadData();
            }, 800);
        }

        function setValues(data) {
            if(typeof($scope.fields) !== 'object') {
                $scope.fields = JSON.parse($scope.fields);
            }
            var rows = [];

            var regex = /({([^}]+)+})/g;
            var match;

            for(var rowNo = 0; rowNo < data[$scope.type+'_results'].length; rowNo++) {
                var completeRow = data[$scope.type+'_results'][rowNo];
                var rowId = completeRow.id;
                var row = [];
                for(var wanted_column in $scope.fields) {
                    var rowValue = '';
                    var hasMatch = false;
                    var hasAtleastOneValue = false;
                    while ((match = regex.exec($scope.fields[wanted_column])) !== null) {
                        if(!hasMatch)
                            hasMatch = '';
                        if (match.index === regex.lastIndex) {
                            regex.lastIndex++;
                        }
                        var value = completeRow[match[2]];
                        if(value == null || value == '') {
                            value = '';
                        } else {
                            hasAtleastOneValue = true;
                        }
                        if(hasMatch == '') {
                            hasMatch = $scope.fields[wanted_column].replace(match[0], value);
                        } else {
                            hasMatch = hasMatch.replace(match[0], value);
                        }
                    }
                    if(hasMatch) {
                        if(hasAtleastOneValue) {
                            row.push(hasMatch);
                        } else {
                            row.push('-');
                        }
                    } else {
                        var value = '-';
                        var toCheck = completeRow[$scope.fields[wanted_column]];
                        if(typeof(toCheck) != 'undefined' || toCheck != null) {
                            value = toCheck;
                        }
                        row.push(value);
                    }
                }
                rows.push({id: rowId, dataset: row});
            }
            $scope.tableRows = rows;
        }

        function loadData() {
            var textquery = '';
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
                }],
                onProgress: function(event) {
                    try {
                        var response = event.currentTarget.responseText;
                        response = response.replace(/}{/g, "},{");
                        response = "[" + response + "]";
                        var newresult = JSON.parse(response);
                        newresult = newresult[newresult.length-1];
                        setValues(newresult);
                    } catch (e) {
                        console.log(e);
                    }
                }
            }).then(function(e) {
                $scope.filtering = false;
            });
        }

        setHeadings();
        loadData();
    }
]);

app.directive('typeTable', function () {
    return {
        restrict: 'E',
        scope: {
            fields: '@',
            detailRoute: '@',
            type: '@',
            sortings: '@'
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