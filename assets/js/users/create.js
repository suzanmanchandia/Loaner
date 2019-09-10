// Author: Nikhil Handyal
// Date Created: 03/15/2012
// Dept: USC ROSKI SCHOOL OF FINE ARTS
// PROJECT: Loaner
// Description: Javascript functios required for users/create page
//
// -------------------------------------------- USERS/CREATE.JS ----------------------------------------- //

$("document").ready(function(){
		//set focus to userid field
		$("#userid").focus();
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
		
		$('#submitWaiting').css({"display" : "inline"});
		$.post("functions.php?create=1",
				{
						"userid"    		: $("#userid").val(),
						"password"  		: $("#password").val(),
						"cpassword" 		: $("#cpassword").val(),
						"fname"     		: $("#fname").val(),
						"lname"     		: $("#lname").val(),
						"email"     		: $("#email").val(),
						"phone"     		: $("#phone").val(),
						"add"       		: $("#add").val(),  
						"city"      		: $("#city").val(),
						"state"     		: $("#state").val(),
						"zip"       		: $("#zip").val(),
						"deptID"				: $("#deptID").val(),
						"class"     		: $("#class").val(),
						"role"      		: $("#role").val(),
						"status"    		: $("#status").val(),
						"notes"     		: $("#notes").val(),
						"access_areas"	: access_area_str
				},
				function(response){
						var jsonResponse = JSON.parse(response);
						if(jsonResponse.status == 0){
								$('#submitWaiting').css({"display" : "none"});
								alert(jsonResponse.message);
								window.location = "https://art.usc.edu/loanerV3/users";
						}
						else{
								$('#submitWaiting').css({"display" : "none"});
								showError(jsonResponse.message);
						}
				}
		);//end of Ajax Post Request
} // End of submitForm