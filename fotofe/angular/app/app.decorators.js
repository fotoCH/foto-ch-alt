/**
 Bootstrap UI Template Directories overwrites...
 **/

app.config(function($provide) {
  $provide.decorator('uibAccordionDirective', function($delegate) {
    //array of datepicker directives
    $delegate[0].templateUrl = "app/components/bootstrap/accordion/accordion.html";
    return $delegate;
  });
});

app.config(function($provide) {
  $provide.decorator('uibAccordionGroupDirective', function($delegate) {
    //array of datepicker directives
    $delegate[0].templateUrl = "app/components/bootstrap/accordion/accordion-group.html";
    return $delegate;
  });
});

app.config(function($provide) {
  $provide.decorator('uibModalWindowDirective', function($delegate) {
    $delegate[0].templateUrl = "app/components/bootstrap/modal/window.html";
    return $delegate;
  });
});

app.config(function($provide) {
  $provide.decorator('uibTabDirective', function($delegate) {
    $delegate[0].templateUrl = "app/components/bootstrap/tabs/tab.html";
    return $delegate;
  });
});

app.config(function($provide) {
  $provide.decorator('uibTabsetDirective', function($delegate) {
    $delegate[0].templateUrl = "app/components/bootstrap/tabs/tabset.html";
    return $delegate;
  });
});

app.config(function($provide) {
  $provide.decorator("$xhrFactory", [
    "$delegate", "$injector", "$q",
    function($delegate, $injector, $q) {
      return function(method, url) {
        var xhr = $delegate(method, url);
        var $http = $injector.get("$http");
        var callConfig = $http.pendingRequests[$http.pendingRequests.length - 1];
        if (angular.isFunction(callConfig.onProgress)) {
          xhr.addEventListener("progress", callConfig.onProgress);
        }
        return xhr;
      };
    }
  ]);
});
