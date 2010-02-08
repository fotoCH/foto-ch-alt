function FotoSuggestions(url) {
	this.http = this.createRequestObject();
	this.asco=null;
	this.sTextboxValue=null;
	this.btext=null;
	this.aSuggestions = [];
	this.iSuggestions = [];
	this.url=url;
	
}


FotoSuggestions.prototype.createRequestObject = function() {
    var ro;
    var browser = navigator.appName;
    if(browser == "Microsoft Internet Explorer"){
        ro = new ActiveXObject("Microsoft.XMLHTTP");
    }else{
        ro = new XMLHttpRequest();
    }
    return ro;
}



FotoSuggestions.prototype.sndReq=function(action) {
    var pointer = this;
    action=escape(action);    // wegen umlauten
    this.http.open('get', this.url+'suchbegriff='+action);
    //this.http.onreadystatechange = this.handleResponse;
    this.http.onreadystatechange = function () { pointer.handleResponse() };
    this.http.send(null);
    this.aSuggestions = [];
    this.iSuggestions = [];
    //asco.autosuggest(abc, btype);
}


FotoSuggestions.prototype.handleResponse=function() {
    if(this.http.readyState == 4){
	
        var response = this.http.responseText;
        var update = new Array();
	var results = response.split('\r\n');
	for (var i = 0; i < results.length-1; i++) {
		var fields = results[i].split('|');
		this.aSuggestions.push(fields[1]);
		this.iSuggestions.push(fields[0]);
	}
	this.asco.autosuggest(this.aSuggestions, this.iSuggestions, this.btype);
	//asco.autosuggest(abc, btype);
    }
}


/**
 * Request suggestions for the given autosuggest control. 
 * @scope protected
 * @param oAutoSuggestControl The autosuggest control to provide suggestions for.
 */
FotoSuggestions.prototype.requestSuggestions = function (oAutoSuggestControl /*:AutoSuggestControl*/,
                                                          bTypeAhead /*:boolean*/) {
    this.sTextboxValue = oAutoSuggestControl.textbox.value;
    this.asco=oAutoSuggestControl;
    this.btype=bTypeAhead;
    if (this.sTextboxValue.length > 0){
    
        //convert value in textbox to lowercase
        var sTextboxValueLC = this.sTextboxValue.toLowerCase();

        //search for matching states
	this.sndReq(sTextboxValueLC);
  
    }

    //provide suggestions to the control
    //oAutoSuggestControl.autosuggest(abc, bTypeAhead);
};