app.filter('rawHtml', ['$sce', function($sce){
	  return function(val) {
	    return $sce.trustAsHtml(val);
	  };
	}]);

app.directive('sectionDefault', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/section/default.html',
		    	scope: {
		    		  title: '=title',
		    		  value: '=value'
		    		},
		  };
		});

app.directive('sectionRaw', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/section/raw.html',
		    	scope: {
		    		  title: '=title',
		    		  value: '=value'
		    		},
		  };
		});

app.directive('sectionList', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/section/list.html',
		    	scope: {
		    		  title: '=title',
		    		  values: '=values'
		    		},
		  };
		});