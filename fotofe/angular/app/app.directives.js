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
		    	  headline: '=headline',
		    	  value: '=value'
		    }
		  };
		});

app.directive('sectionRaw', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/section/raw.html',
	    	scope: {
	    		  headline: '=headline',
	    		  value: '=value'
	    	}
		  };
		});

app.directive('sectionList', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/list/defaultList.html',
	    	scope: {
	    		  headline: '=headline',
	    		  values: '=values'
	    	}
		  };
		});

app.directive('inventoryReference', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/list/inventoryReference.html',
	    	scope: {
	    		  headline: '=headline',
	    		  inventories: '=inventories',
	    		  labels: '='
	    	}
		  };
		});

app.directive('photographerList', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/list/photographerList.html',
	    	scope: {
	    		  photographer: '='
	    	}
		  };
		});

app.directive('photographerReference', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/list/photographerReference.html',
	    	scope: {
	    		  headline: '=',
	    		  photographer: '='
	    	}
		  };
		});

app.directive('institutionList', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/list/institutionList.html',
	    	scope: {
	    		  institutions: '='
	    	}
		  };
		});

app.directive('exhibitionList', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/list/exhibitionList.html',
	    	scope: {
	    		  exhibitions: '=',
	    		  labels: '='
	    	}
		  };
		});

app.directive('exhibitionReference', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/list/exhibitionReference.html',
	    	scope: {
	    		  headline: '=',
	    		  exhibitions: '=',
	    		  labels: '='

	    	}
		  };
		});

app.directive('mediaPartner', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/media/mediaPartner.html',
	    	scope: {
	    		  partner: '='
	    	}
		  };
		});

app.directive('panel', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/panel/panel.html',
	    	scope: {
	    		  headline: '=headline',
	    	}
		  };
		});