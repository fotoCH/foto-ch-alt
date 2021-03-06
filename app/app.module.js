var get_current_url = function() {
  return (
    location.protocol + "//" + location.hostname + ":" + location.port + "/"
  );
};

var app = angular.module("fotochWebApp", [
  "ui.router",
  "ngAnimate",
  "angular.filter",
  "angucomplete-alt",
  "headroom",
  "yaru22.md",
  "infinite-scroll",
  "ui-rangeSlider",
  "ui.bootstrap",
  "ui.bootstrap.modal",
  "ui.bootstrap.popover",
  "ngSanitize",
  "ngCookies",
  "masonry",
  "angulartics",
  "angulartics.google.analytics",
  "ngMeta",
  "seo"
]);

app.run(function(
  $rootScope,
  $http,
  $location,
  $q,
  languages,
  $cacheFactory,
  $cookies,
  $state,
  ngMeta
) {
  $rootScope.user = "";
  $rootScope.userLevel = "";
  $rootScope.authToken = "";
  $rootScope.previous = "";
  if (!$cookies.get("lang")) {
    $rootScope.lang = "de";
    $cookies.put("lang", "de");
  } else {
    $rootScope.lang = $cookies.get("lang");
  }
  $rootScope.filterCache = $cacheFactory("filterCache");

  $rootScope.$on("$stateChangeSuccess", function(
    event,
    toState,
    toParams,
    fromState
  ) {
    $state.previous = fromState;
    $rootScope.previous = fromState;
  });

  var hosta = $location.$$host.split(".");
  if (hosta[0] == "www") hosta.shift();
  if (hosta.length > 0 && (l = languages.indexOf(hosta[0])) >= 0) {
    $rootScope.lang = hosta[0];
  }

  $rootScope.imageRootUrl = "https://" + $rootScope.lang + ".foto-ch.ch/";
  $rootScope.$on("$stateChangeStart", function(event, toState) {
    if (toState.name == "profile" && !$rootScope.user_data) {
      event.preventDefault();
      $state.go("login");
    }
  });
  $rootScope.$on("$stateChangeSuccess", function(event, toState) {
    ngMeta.setTag("ogUrl", $location.$$absUrl);
  });

  // Development Server API URL
  // $rootScope.ApiUrl = 'http://raeffu.local:8888/fotoch/api';

  // Production Server API URL
  // DEV
  //$rootScope.ApiUrl = 'http://foto-ch.dev/api/api';
  // PROD
  //$rootScope.ApiUrl = 'https://'+$rootScope.lang+'.foto-ch.ch/api';

  //$rootScope.ApiUrl = 'http://localhost:8020/api';
  $rootScope.ApiUrl = get_current_url() + "api";

  var token = window.sessionStorage.authToken;
  $http.defaults.headers.common["X-Authtoken"] = token;

  $rootScope.userInfoCall = $q.defer();
  if (token !== undefined && $rootScope.authToken != token) {
    $http
      .get($rootScope.ApiUrl + "/?a=user&b=info&token=" + token)
      .success(function(data) {
        var resp = data;
        if (data !== 0) {
          $rootScope.user = data.user;
          $rootScope.userLevel = parseInt(data.level);
          $rootScope.instComment = parseInt(data.inst_comment);
          $rootScope.authToken = token;
          $http.defaults.headers.common["X-Authtoken"] = token;
          $rootScope.userInfoCall.resolve();
        }
      });
  } else {
    $rootScope.userInfoCall.resolve();
  }
});

app.service("fotochService", [
  "$http",
  function($http) {
    //var urlBase = 'https://foto-ch.ch/api/';
    //var urlBase = 'http://localhost:8020/api/';
    var urlBase = get_current_url + "api/";

    this.getFotografs = function(anf) {
      return $http.get(urlBase + "?anf=" + anf);
    };

    this.getFotograf = function(id) {
      return $http.get(urlBase + "?id=" + id);
    };

    this.getLang = function() {
      return $http.get(urlBase + "?a=sprache");
    };
  }
]);

app.constant("languages", ["de", "fr", "it", "en", "rm"]);
