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
		    }
		  };
		});

app.directive('sectionRaw', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/section/raw.html',
	    	scope: {
	    		  title: '=title',
	    		  value: '=value'
	    	}
		  };
		});

app.directive('sectionList', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/section/list.html',
	    	scope: {
	    		  title: '=title',
	    		  values: '=values'
	    	}
		  };
		});

app.directive('sectionListInventory', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/section/inventoryList.html',
	    	scope: {
	    		  title: '=title',
	    		  inventories: '=inventories',
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
	    		  title: '=title',
	    	}
		  };
		});