// Author: Nikhil Handyal
// Date Created: 03/15/2012
// Dept: USC ROSKI SCHOOL OF FINE ARTS
// PROJECT: Loaner
// Description: Javascript functios required for users/index page
//
// -------------------------------------------- USERS/INDEX.JS ----------------------------------------- //

$(document).ready(function() {
		
		// set focus to search field
		$("#searchBox").focus();
		
		setSearchField();
		
		document.getElementById("contentTableHeader").addEventListener("click", function(e) {
				var target =  e.target.className;
				buildURL({
						pageURL: "https://art.usc.edu/loanerV3/users/index.php",
						sortField: target,
						defaultSort: "userid"
				});
		});
});

function showDetailsLightbox(id){
		var targetURL = "lightboxPages/indexDetails.php?id="+id;
		$.fancybox.showActivity();
		$.get(targetURL,function(response){
				$.fancybox({
								'autoDimensions'	: false,
								'width'						: 850,
								'height'					: 500,
								'content'					: response
						});
				}
		);
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
								else{
										$.fancybox.close();
										showError(jsonResponse.message);
								}
						}
				);
		}
		else{
				return false;
		}
}

function suspend(setSuspend,userID){
		if(setSuspend)
				confirmString = "Are you sure you want to suspend "+userID+"?";
		else
				confirmString = "Are you sure you want to un-suspend "+userID+"?";
		if(confirm(confirmString)){
				$.get("functions.php",
						{
								"suspend"	: setSuspend,
								"id"			: userID
						},
						function(response){
								var jsonResponse = JSON.parse(response);
								if(jsonResponse.status == 0){
										alert(jsonResponse.message);
										window.location.reload();
								}
								else{
										$.fancybox.close();
										showError(jsonResponse.message);
								}
						}
				);
		}
		else{
				return;
		}
}

function suspendAllStudents(setSuspend){
		if(setSuspend)
				confirmString = "Are you sure you want to suspend ALL STUDENT accounts?";
		else
				confirmString = "Are you sure you want to un-suspend ALL STUDENT accounts?";
				
		if(confirm(confirmString)){
				$.get("functions.php",
						{
								"suspend_all"	: setSuspend
						},
						function(response){
								var jsonResponse = JSON.parse(response);
								if(jsonResponse.status == 0){
										alert(jsonResponse.message);
										window.location.reload();
								}
								else{
										$.fancybox.close();
										showError(jsonResponse.message);
								}
						}
				);
		}
		else{
				return;
		}
}

function deleteAccount(userID){
		if(confirm("Are you sure you want to delete the account for "+userID+"? THIS ACTION CANNOT BE UNDONE.")){
				$.get("functions.php",
						{
								"delete"	: userID
						},
						function(response){
								var jsonResponse = JSON.parse(response);
								if(jsonResponse.status == 0){
										alert(jsonResponse.message);
										window.location.reload();
								}
								else{
										$.fancybox.close();
										showError(jsonResponse.message);
								}
						}
				);
		}
		else{
				return;
		}
}

function showEmail(userID){
		var target_URL = "lightboxPages/email.php?userid="+userID;
		$.fancybox.showActivity();
		$.get(target_URL,function(response){
				$.fancybox({
								'autoDimensions'	: true,
							// 	'width'						: 1140,
// 								'height'					: 545,
								'content'					: response
						});
				}
		);
}
