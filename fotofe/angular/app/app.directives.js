app.filter('rawHtml', ['$sce', function($sce){
	  return function(val) {
	    return $sce.trustAsHtml(val);
	  };
	}]);

app.directive('contentDefault', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/content/default.html',
		    scope: {
		    	  headline: '=',
		    	  value: '='
		    }
		  };
		});

app.directive('contentRaw', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/content/raw.html',
	    	scope: {
	    		  headline: '=',
	    		  value: '='
	    	}
		  };
		});

app.directive('defaultList', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/list/defaultList.html',
	    	scope: {
	    		  headline: '=',
	    		  values: '='
	    	}
		  };
		});

app.directive('inventoryReference', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/list/inventoryReference.html',
	    	scope: {
	    		  headline: '=',
	    		  inventories: '=',
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

app.directive('photoTeaser', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/media/photoTeaser.html',
	    	scope: {
	    		  photos: '=',
	    		  labels: '=',
	    		  headline: '='
	    	}
		  };
		});

app.directive('panel', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/panel/panel.html',
	    	scope: {
	    		  headline: '=',
	    	}
		  };
		});

app.directive('editingInfo', function() {
	  return {
		    restrict: 'E',
		    templateUrl: 'app/shared/misc/editingInfo.html',
	    	scope: {
	    		  date: '=',
	    		  author: '=',
	    		  labels: '='
	    	}
		  };
		});