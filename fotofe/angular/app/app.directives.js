app.filter('rawHtml', ['$sce', function($sce){
	  return function(val) {
	    return $sce.trustAsHtml(val);
	  };
	}]);

app.directive('stField', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/fields/stField.html',
		    	scope: {
		    		  label: '=label',
		    		  value: '=value'
		    		},
		  };
		});

app.directive('stFieldRaw', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/fields/stFieldRaw.html',
		    	scope: {
		    		  label: '=label',
		    		  value: '=value'
		    		},
		  };
		});