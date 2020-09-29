<?php
header('Expires: Wed, 21 Oct 2015 07:28:00 GMT'); // 1 hour
?>
<!DOCTYPE html>
<!--[if lt IE 7]>
  <html class="lt-ie10 lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
  <html class="lt-ie10 lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
  <html class="lt-ie10 lt-ie9"> <![endif]-->
<!--[if IE 9]>
  <html class="lt-ie10"> <![endif]-->
<!--[if gt IE 8]><!-->
<html ng-app="fotochWebApp" ng-controller="MainCtrl" > <!--<![endif]-->
  <head>
    <title ng-bind="ngMeta.title">fotoCH</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta name="description"
      content="{{ ngMeta.description }}">
    <meta name="keywords"
      content="Fotografie, Foto, Schweiz, Lexikon, Institution, Ausstellung, Biografie, Nachlass, Verzeichnis, Geschichte">
    <meta name="robots" content="follow, index">
    <meta name="fragment" content="!">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ ngMeta.title }}">
    <meta property="og:url" content="{{ ngMeta.ogUrl }}">
    <meta property="og:type" content="{{ ngMeta.ogType }}">
    <meta property="og:description" content="{{ ngMeta.description }}">
    <meta property="og:image" content="{{ ngMeta.ogImage }}">

    <!-- Twitter data -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ ngMeta.title }}">
    <meta name="twitter:description" content="{{ ngMeta.description }}">
    <meta name="twitter:url" content="{{ ngMeta.ogUrl }}">
    <meta name="twitter:image" content="{{ ngMeta.ogImage }}">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css" media="screen">
    <link rel="stylesheet" href="assets/css/normalize.min.css">
    <!--<link rel="stylesheet" href="assets/css/angucomplete-alt.css"> -->
    <!-- <link rel="stylesheet" href="assets/css/rangeSlider.min.css"> -->
    <link rel="stylesheet" href="assets/css/main.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">
    <link rel="stylesheet" href="assets/css/less/vendor/jquery.fancybox.css" type="text/css" media="screen">

    <script>
     (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
       (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                              m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
     })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

     ga('create', 'UA-79617939-1', 'auto');
     ga('send', 'pageview');

    </script>

    <script>
     // ugly ugly fix for directlinks
     var l = window.location.href;
     if (l.search("%23detail")>0){
       var newl = l.replace("%23detail", "#detail");
       window.location.href = newl;
     }
    </script>


    <!-- Matomo -->
    <script type="text/javascript">
     var _paq = window._paq || [];
     /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
     _paq.push(['trackPageView']);
     _paq.push(['enableLinkTracking']);
     (function() {
       var u="//stats.foto-ch.ch/";
       _paq.push(['setTrackerUrl', u+'matomo.php']);
       _paq.push(['setSiteId', '1']);
       var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
       g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
     })();
    </script>
    <!-- End Matomo Code -->


    <!--[if lt IE 10]>

    <![endif]-->
    <script src="assets/libs/modernizr.only-flexbox.js"></script>
    <script>
     // Add a Modernizr-test for the weird, inbetween, flexbox implementation
     // in IE10, necessary for the "sticky" footer.
     // (See https://github.com/Modernizr/Modernizr/issues/812)
     // (This could be rolled into a custom Modernizr build in production later.)
     Modernizr.addTest('flexboxtweener', Modernizr.testAllProps('flexAlign', 'end', true));
    </script>
    <!--<script src="https://api3.geo.admin.ch/loader.js?lang=en" type="text/javascript"></script>-->


    <link rel="shortcut icon" href="assets/img/favicon.ico">
    <link rel="icon" sizes="16x16 32x32 64x64" href="assets/img/favicon.ico">
    <link rel="icon" type="image/png" sizes="192x192" href="assets/img/favicon-192x192.png">
    <link rel="icon" type="image/png" sizes="160x160" href="assets/img/favicon-160x160.png">
    <link rel="icon" type="image/png" sizes="96x96" href="assets/img/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="64x64" href="assets/img/favicon-64x64.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicon-16x16.png">

    <link rel="apple-touch-icon" href="assets/img/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="114x114" href="assets/img/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="72x72" href="assets/img/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="144x144" href="assets/img/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="60x60" href="assets/img/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="120x120" href="assets/img/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="152x152" href="assets/img/apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon-180x180.png">

    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage" content="assets/img/apple-touch-icon-144x144.png">
  </head>
  <body class="lang-{{lang}}" ng-class="{'showOverlay':isMenuOpen}">
    <header class="l-header" ng-class="isHome()" offset="0">
      <div class="l-container">
        <div class="logo-header">
          <a href="#/">fotoCH</a>
        </div>
        <div class="nav-container left" ng-controller="NavigationCtrl" class="ng-cloak">
          <nav ngCloak class="nav-main ng-cloak" ng-class="{'is-open':isMenuOpen}">
            <ul>
              <li><a ng-class="getClass('photographer')" ui-sref="photographer">{{spr.photographers}}</a></li>
              <li><a ng-class="getClass('institution')" ui-sref="institution">{{spr.institutionen}}</a></li>
              <li><a ng-class="getClass('inventory')" ui-sref="inventory">{{spr.bestaende2}}</a></li>
              <li><a ng-class="getClass('exhibition')" ui-sref="exhibition">{{spr.ausstellungen}}</a></li>
              <li><a ng-class="getClass('literatur')" ui-sref="literatur">{{spr.literatur}}</a></li>
              <li><a ng-class="getClass('photo')" ui-sref="photo">{{spr.photos}}</a></li>
              <li><a ng-class="getClass('timeline')" ui-sref="timeline">{{spr.timeline}}</a></li>
              <li><a ng-class="getClass('projects')" ui-sref="projects">{{spr.projects}}</a></li>
            </ul>
          </nav>
        </div>
        <div class="nav-container right" ng-controller="NavigationCtrl" class="ng-cloak">
          <nav ngCloak class="nav-main ng-cloak" ng-class="{'is-open':isMenuOpen}">
            <ul>
              <li class=""><a ng-class="getLclass('de')" ng-click="setLanguage('de')">de</a></li>
              <li class=""><a ng-class="getLclass('fr')" ng-click="setLanguage('fr')">fr</a></li>
              <li class=""><a ng-class="getLclass('it')" ng-click="setLanguage('it')">it</a></li>
              <li class=""><a ng-class="getLclass('rm')" ng-click="setLanguage('rm')">rm</a></li>
              <li class=""><a ng-class="getLclass('en')" ng-click="setLanguage('en')">en</a></li>

            </ul>
            <button type="button" class="btn btn--link nav-main__toggle" ng-click="toggleMobileMenu()">
              <span class="icon-bar"></span>
            </button>
          </nav>
        </div>
      </div>
      </div>
    </header>
    <div class="l-main ui-view-container" role="main">
      <div class="view" ui-view></div>
    </div>


    <div class="bottom-panel" ng-if="$root.user" ng-class="getUserLevelClass()">
      <div class="user-panel">
        <a ui-sref="profile" class="username" ng-if="$root.user">{{spr.logged_in_as}}: <strong>{{$root.user_data.username}}</strong></a>
        <ul class="submenu">
          <li><a ui-sref="profile">{{spr.profile}}</a></li>
          <li ng-if="$root.manageUsersAllowed()"><a ui-sref="user-management">{{spr.useradmin}}</a></li>
          <li>
            <a ng-if="$root.pendingAllowed()" ui-sref="update-requests">{{spr.updaterequests}}
              <span class="counter" ng-if="$root.pendingRequests">{{$root.pendingRequests}}</span>
            </a>
          </li>
          <li ng-if="$root.manageProjectsAllowed()"><a ui-sref="manage-projects">{{spr.manageProjects}}</a></li>
          <li><a ng-click="$root.doLogout()">{{spr.logout}}</a></li>
        </ul>
      </div>
    </div>

    <footer class="l-footer" ngCloak class="ng-cloak">
      <div class="l-container">
        <div class="copyright">
          © Büro für Fotografiegeschichte Bern
        </div>
        <nav class="nav-meta ng-cloak" ng-controller="NavigationCtrl">
          <ul>
            <li><a ng-class="getClass('/support')" ui-sref="support">{{spr.support}}</a></li>
            <li><a ng-class="getClass('/contact')" ui-sref="contact">{{spr.kontakt}}</a></li>
            <li><a ng-class="getClass('/aboutfotoch')" ui-sref="aboutFotoch">{{spr.about_fotoch}}</a></li>
            <li><a ng-if="!$root.user_data" ng-class="getClass('/login')" ui-sref="login">Login</a></li>
          </ul>
        </nav>
      </div>
    </footer>


    <script src="assets/libs/jquery-2.1.3.min.js"></script>

    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular.min.js"></script>

    <script src="assets/libs/angular-animate.min.js"></script>
    <script src="assets/libs/angular-ui-router.min.js"></script>
    <script src="assets/libs/angular-sanitize.min.js"></script>
    <script src="assets/libs/angular-cookies.min.js"></script>
    <script src="assets/libs/angucomplete-alt.min.js"></script>
    <script src="assets/libs/headroom.min.js"></script>
    <script src="assets/libs/angular.headroom.min.js"></script>
    <script src="assets/libs/marked.js"></script>
    <script src="assets/libs/angular-md.min.js"></script>
    <script src="assets/libs/ng-infinite-scroll.min.js"></script>
    <script src="assets/libs/angular-filter.min.js"></script>
    <script src="assets/libs/rangeSlider.min.js"></script>
    <script src="assets/libs/images-loaded.js"></script>
    <script src="assets/libs/jquery.fancybox.pack.js"></script>
    <script src="assets/libs/masonry.js"></script>
    <script src="assets/libs/angular-masonry-directive.js"></script>
    <script src="assets/libs/ngMeta.min.js"></script>
    <script src="assets/libs/angular-seo.min.js"></script>

    <script src="assets/libs/analytics/angulartics.min.js"></script>
    <script src="assets/libs/analytics/angulartics-ga.js"></script>

    <!-- TinyMCE -->
    <script src="assets/libs/tinymce/tinymce.min.js"></script>
    <script src="assets/libs/tinymce.min.js"></script>

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <script src="assets/libs/ui-bootstrap.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBkANlM2icSyDnixD4FIZWoTuBFJqPL6fc&libraries=places"
      defer></script>
    <script src="assets/libs/markerclusterer.js"></script>
    <script src="assets/libs/oms.min.js"></script>

    <script src="app/app.module.js"></script>
    <script src="app/app.controllers.js"></script>
    <script src="app/app.typetable.js"></script>
    <script src="app/app.countnumber.js"></script>
    <script src="app/app.photogrid.js"></script>
    <script src="app/app.testcontroller.js"></script>
    <script src="app/app.directives.js"></script>
    <script src="app/app.routes.js"></script>
    <script src="app/app.decorators.js"></script>
    <script src="app/app.details.js"></script>
    <script src="app/app.editable.js"></script>
    <script src="app/app.timeline.js"></script>
    <script src="app/app.projects.js"></script>
    <script src="app/app.stats.js"></script>
    <script src="app/app.map.js"></script>
    <script src="app/app.administration.js"></script>
  </body>
</html>
