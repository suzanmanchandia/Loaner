// Author: Nikhil Handyal
// Date Created: 03/15/2012
// Dept: USC ROSKI SCHOOL OF FINE ARTS
// PROJECT: Loaner
// Description: Javascript functios required for users/edit page
//
// -------------------------------------------- USERS/EDIT.JS ----------------------------------------- //

$("document").ready(function(){
		//set focus to userid field
		$("#fname").focus();
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
		$.post("functions.php?edit=1",
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
								$(window).scrollTop(0);
								window.location.reload();
						}
						else{
								$('#submitWaiting').css({"display" : "none"});
								showError(jsonResponse.message);
						}
				}
		);//end of Ajax Post Request
} // End of submitForm

function suspend(setSuspend,userID,role){
		if(setSuspend)
				confirmString = "Are you sure you want to suspend "+userID+"?";
		else
				confirmString = "Are you sure you want to un-suspend "+userID+"?";
		if(confirm(confirmString)){
				$.get("functions.php",
						{
								"suspend"	: setSuspend,
								"id"			: userID,
								"role"		: role	
						},
						function(response){
								var jsonResponse = JSON.parse(response);
								if(jsonResponse.status == 0){
										alert(jsonResponse.message);
										window.location.reload();
								}
								else
										showError(jsonResponse.error);
						}
				);
		}
		else{
				return;
		}
}

function deleteAccount(userID){
		if(confirm("Are you sure you want to delete the account for "+userID+"? This action cannot be undone.")){
				$.get("functions.php",
						{
								"delete"	: userID
						},
						function(response){
								var jsonResponse = JSON.parse(response);
								if(jsonResponse.status == 0){
										alert(jsonResponse.message);
										window.location = "http://art.usc.edu/loanerV3/users/index.php";
								}
								else
										showError(jsonResponse.error);
						}
				);
		}
		else{
				return;
		}
}

function lockUser(setLock, userID, role){
		if(setLock)
				confirmString = "Are you sure you want to lock "+userID+"?";
		else
				confirmString = "Are you sure you want to un-lock "+userID+"?";
				
		if(confirm(confirmString)){
				$.get("functions.php",
						{
								"lock"		: setLock,
								"id"			: userID,
								"role"		: role	
						},
						function(response){
								var jsonResponse = JSON.parse(response);
								if(jsonResponse.status == 0){
										alert(jsonResponse.message);
										window.location.reload();
								}
								else
										showError(jsonResponse.error);
						}
				);
		}
		else{
				return false;
		}
}