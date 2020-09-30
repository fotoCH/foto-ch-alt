/**
 * Configure the Routes
 */

app
  .config([
    "$urlRouterProvider",
    "$stateProvider",
    "$locationProvider",
    "$analyticsProvider",
    "ngMetaProvider",
    "$httpProvider",
    function(
      $urlRouterProvider,
      $stateProvider,
      $locationProvider,
      $analyticsProvider,
      ngMetaProvider,
      $httpProvider
    ) {
      $urlRouterProvider.otherwise("/home");

      $stateProvider
        // Homepage
        .state("home", {
          url: "/home",
          templateUrl: "app/components/views/home.html",
          controller: "HomeCtrl",
          meta: {}
        })
        // Photographer search view
        .state("photographer", {
          url: "/photographer",
          templateUrl: "app/components/views/photographer.html",
          controller: "PhotographerCtrl",
          meta: {}
        })

        // Institution search view
        .state("institution", {
          url: "/institution",
          templateUrl: "app/components/views/institution.html",
          controller: "InstitutionCtrl"
        })
        // Exhibition search view
        .state("exhibition", {
          url: "/exhibition",
          templateUrl: "app/components/views/exhibition.html",
          controller: "ExhibitionCtrl"
        })
        // Inventory search view
        .state("inventory", {
          url: "/inventory",
          templateUrl: "app/components/views/inventory.html",
          controller: "InventoryCtrl"
        })
        // Support page
        .state("support", {
          url: "/support",
          templateUrl: "app/components/meta/support.html",
          controller: "StaticPageCtrl"
        })
        // Contact page
        .state("contact", {
          url: "/contact",
          templateUrl: "app/components/meta/contact.html",
          controller: "StaticPageCtrl"
        })
        // About fotoCH
        .state("aboutFotoch", {
          url: "/aboutfotoch",
          templateUrl: "app/components/meta/aboutFotoch.html",
          controller: "StaticPageCtrl"
        })
        // Testpage for development
        .state("login", {
          url: "/login",
          templateUrl: "app/components/meta/login.html",
          controller: "LoginCtrl"
        })
        // user-profile
        .state("profile", {
          url: "/profile",
          templateUrl: "app/components/meta/profile.html",
          controller: "ProfileCtrl"
        })
        // change requests
        .state("update-requests", {
          url: "/pending-requests",
          templateUrl: "app/components/meta/requests.html",
          controller: "PendingCtrl"
        })
        // user management
        .state("user-management", {
          url: "/user-management",
          templateUrl: "app/components/meta/users.html",
          controller: "UserCtrl"
        })
        // user management
        .state("add-user", {
          url: "/add-user",
          templateUrl: "app/components/meta/userform.html",
          controller: "AddUserCtrl"
        })
        // change requests
        .state("manage-projects", {
          url: "/manage-projects",
          templateUrl: "app/components/meta/projects.html",
          controller: "ManageProjectsCtrl"
        })
        // Testpage for development
        .state("test", {
          url: "/test?ch&photo&kanton&land",
          templateUrl: "app/components/test/test.html",
          controller: "TestCtrl"
        })
        .state("map", {
          url: "/map?ch&photo",
          templateUrl: "app/components/views/map.html",
          controller: "MapCtrl"
        })
        // Fotoportal
        .state("photo", {
          url: "/photo",
          templateUrl: "app/components/views/photo.html",
          controller: "PhotoCtrl"
        })
        // Literatur
        .state("literatur", {
          url: "/literatur",
          templateUrl: "app/components/views/literatur.html",
          controller: "LiteraturCtrl"
        })
        // Timeline
        .state("timeline", {
          url: "/timeline",
          templateUrl: "app/components/views/timeline.html",
          controller: "TimelineCtrl"
        })
        // Projects
        .state("projects", {
          url: "/projects",
          templateUrl: "app/components/views/projects.html",
          controller: "ProjectsController"
        })
        // Project Detail
        .state("project-detail", {
          url: "/project/{project_identification}",
          templateUrl: "app/components/details/project.html",
          controller: "ProjectDetailController"
        })
        // Facts and Figures
        .state("stats", {
          url: "/stats",
          templateUrl: "app/components/views/stats.html",
          controller: "StatsController"
        });

      $locationProvider.html5Mode(true);

      // meta tags default values
      ngMetaProvider.setDefaultTitle("fotoCH");
      ngMetaProvider.setDefaultTag(
        "desciption",
        "fotoCH ist ein Online-Werk, das über die historische Fotografie in der Schweiz informiert. Es besteht aus einem biografischen Lexikon der Fotografinnen und Fotografen und einem Repertorium der fotografischen Archive und Nachlässe."
      );
      ngMetaProvider.setDefaultTag("ogType", "website");

      // for seo, call ready as soon as all http-requests are done, code from https://github.com/steeve/angular-seo/issues/30
      var interceptor = [
        "$q",
        "$injector",
        "$timeout",
        "$rootScope",
        function($q, $injector, $timeout, $rootScope) {
          return {
            response: function(resp) {
              var $http = $injector.get("$http");
              if (!$http.pendingRequests.length) {
                $timeout(function() {
                  if (!$http.pendingRequests.length) {
                    $rootScope.htmlReady();
                  }
                }, 700); // Use .7s as safety interval
              }
              return resp;
            }
          };
        }
      ];

      $httpProvider.interceptors.push(interceptor);
    }
  ])
  .run(function(ngMeta) {
    ngMeta.init();
  });
