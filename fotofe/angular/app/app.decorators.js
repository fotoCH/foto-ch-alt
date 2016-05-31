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
