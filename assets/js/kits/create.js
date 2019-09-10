// Author: Nikhil Handyal
// Date Created: 03/15/2012
// Dept: USC ROSKI SCHOOL OF FINE ARTS
// PROJECT: Loaner
// Description: Javascript functios required for kits/create page
//
// -------------------------------------------- KITS/CREATE.JS ----------------------------------------- //

$("document").ready(function(){
		$("#kitid").focus();
});

function submitForm(){
		var access_area_ids = new Array();

		// Make comman seperated list of access areas
		var aa = $(".access_area:checked");
		if(aa.length == 0){
				access_area_ids.push("0");
		}
		else{
				for(i=0; i<aa.length; i++){
						access_area_ids.push($(aa[i]).val());
				}
		}

		var access_area_str = access_area_ids.join(",");
	
		//make an ajax post request.
		$('#submitWaiting').css({"display" : "inline"});
		$.post("functions.php?create=1",
				{
						"kitid"     		: $("#kitid").val(),
						"desc"      		: $("#desc").val(),
						"loan_length"		:	$("#loan_length").val(),
						"notes"     		: $("#notes").val(),
						"access_areas"	:	access_area_str
				},
				function(response){
						var jsonResponse = JSON.parse(response);
						if(jsonResponse.status == 0){
								$('#submitWaiting').css({"display" : "none"});
								alert(jsonResponse.message);
								window.location = "http://art.usc.edu/loanerV3/kits";
						}
						else{
								$('#submitWaiting').css({"display" : "none"});
								showError(jsonResponse.message);
						}
				}
		);//end of Ajax Post Request
				
} //end of submitForm