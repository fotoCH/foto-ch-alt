app.controller("StatsController", [
  "$scope",
  "$http",
  "$rootScope",
  "$window",
  "$state",
  "$timeout",
  function($scope, $http, $rootScope, $window, $state, $timeout) {
    $scope.stats = [];
    $scope.numberOfFotografen = 0;
    $scope.numberOfOrte = 0;
    $scope.numberOfKantone = 0;

    // get stats
    function updateStatsList() {
      $scope.stats = [
        {
          name: "Loading..."
        }
      ];
      $http.get($rootScope.ApiUrl + "/?a=stats").success(function(data) {
        $scope.numberOfFotografen = data.length;
        $scope.stats = buildList(data);
        $scope.numberOfKantone = $scope.stats.length;
        angular.forEach($scope.stats, function(s) {
          $scope.numberOfOrte = $scope.numberOfOrte + s.orte.length;
        });
      });
    }

    function buildList(photographers) {
      var result = [];
      var current_kanton;
      var current_ort;

      angular.forEach(photographers, function(p) {
        if (p.kanton !== current_kanton) {
          result.push({
            name: p.kanton,
            orte: [
              {
                name: p.ort,
                fotografen: [
                  {
                    id: p.id,
                    vorname: p.vorname,
                    nachname: p.nachname,
                    geburtsdatum: p.geburtsdatum,
                    todesdatum: p.todesdatum
                  }
                ]
              }
            ]
          });
          current_kanton = p.kanton;
          current_ort = p.ort;
        } else {
          var orte = result[result.length - 1].orte;

          if (p.ort !== current_ort) {
            orte.push({
              name: p.ort,
              fotografen: [
                {
                  id: p.id,
                  vorname: p.vorname,
                  nachname: p.nachname,
                  geburtsdatum: p.geburtsdatum,
                  todesdatum: p.todesdatum
                }
              ]
            });
            current_ort = p.ort;
          } else {
            var fotografen = orte[orte.length - 1].fotografen;
            fotografen.push({
              id: p.id,
              vorname: p.vorname,
              nachname: p.nachname,
              geburtsdatum: p.geburtsdatum,
              todesdatum: p.todesdatum
            });
          }
        }
      });
      return result;
    }

    updateStatsList();
  }
]);
