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
		    templateUrl: 'app/shared/section/list.html',
	    	scope: {
	    		  headline: '=headline',
	    		  values: '=values'
	    	}
		  };
		});

app.directive('sectionListInventory', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/section/inventoryList.html',
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
	    		  headline: '=headline',
	    		  photographer: '=photographer'
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