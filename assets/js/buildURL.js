function buildURL(obj){
		
		var getVars = getUrlVars();
		var sortField =  obj.sortField;
		var currentURL = obj.pageURL;
		var defaultSort = obj.defaultSort;
		var search = "?"; 
		
		if (Object.keys(getVars).length == 0 ){ // no parameters have been set
				if(sortField != "filler" && sortField !="loans-desc" && sortField != "" ){
						search += "sf=" + sortField;
						if (sortField == defaultSort){ // ----------------------------------------------------------------------------- default sort field
								search += "&dir=DESC";
						}
						else{
								search += "&dir=ASC";
						}
				}
		}
		else{ 																												// some parameters have been set but not all
				for (v in getVars){ 																			// build all the search parameters except for page, sort, and dir
						if (v != "sf" && v != "dir" && v != "page"){
								search += v + "=" + getVars[v] + "&";
						}
				}
				if (getVars["sf"] != undefined && sortField != "filler" && sortField != "" ){ 												// sf and dir have previously been set
						if(sortField != "filler" && sortField != "" ){
								search += "sf=" + sortField + "&";
								if (getVars["sf"] != sortField){ 											//current sort field is different from selected sort field
										search += "dir=ASC";
								}
								else{
										if (getVars["dir"] == "ASC"){
												search += "dir=DESC";
										}
										else{
												search += "dir=ASC";
										}
								}
						}
				}
				else{
						if(sortField != "filler" && sortField != "" ){
								search += "sf=" + sortField + "&";
								if (sortField == defaultSort){ // ------------------------------------------------------------------------------ default sort field
										search += "dir=DESC";
								}
								else{
										search += "dir=ASC";    
								}
						}
				}
		}
		
		if(sortField != "filler" && sortField !="loans-desc" && sortField != "" ){
				var targetURL = currentURL + search;
				window.location = targetURL;
		}
}

function getUrlVars() {
		var vars = {};
		var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
				vars[key] = value;
		});
		return vars;
}

function setSearchField(){
		var getVars = getUrlVars();
		var enteredSearch = decodeURIComponent(getVars["s"]);
		enteredSearch = enteredSearch.replace(/[+]/g, " ");
		if (enteredSearch != "undefined")
				document.getElementById("searchBox").setAttribute("value", enteredSearch);
}