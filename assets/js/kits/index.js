// Author: Nikhil Handyal
// Date Created: 03/15/2012
// Dept: USC ROSKI SCHOOL OF FINE ARTS
// PROJECT: Loaner
// Description: Javascript functios required for kits/index page
//
// -------------------------------------------- KITS/INDEX.JS ----------------------------------------- //

$(document).ready(function() {
		// set focus to searchfield
		$("#searchBox").focus();
		
		setSearchField();
		
		document.getElementById("contentTableHeader").addEventListener("click", function(e) {
				var target =  e.target.className;
				buildURL({
						pageURL: "https://art.usc.edu/loanerV3/kits/index.php",
						sortField: target,
						defaultSort: "kitid"
				});
		});
		
		$('#cancel').click( function() { 
		 $.fancybox.close();
		 });	
		 
		 $('#successMessage').hide();
		 $('#failureMessage').hide();
});

function showDetailsLightbox(id){
		var targetURL = "https://art.usc.edu/loanerV3/kits/lightboxPages/indexDetails.php?id="+id;
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

function showEquipmentsDetailsLightbox(id){
		var targetURL = "https://art.usc.edu/loanerV3/equipments/lightboxPages/indexDetails.php?id="+id;
	
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

function showInactiveLightbox(){
		var target_URL = "lightboxPages/inactiveDetails.php";
		$.fancybox.showActivity();
		$.get(target_URL,function(response){
				$.fancybox({
								'autoDimensions'	: false,
								'width'						: 1100,
								'height'					: 618,
								'content'					: response
						});
				}
		);
}


function deactivate(id){
// 		if(confirm("Are you sure you want to deactivate kit "+id+"?")){
			var target_URL = "lightboxPages/deactivationNotes.php?kitid="+id;
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
// 		}
	}
	
function sendDeactivateEmail(){
		$("#message").val($("#deactivation-notes").val());

    // Yes; submit the form to the PHP script via Ajax

    	 $("#deactivateForm").fadeOut();
    	   
  	  $.ajax ({
      url: 'deactivationEmail.php',
      type: 'POST',
      data: $("#deactivateForm").serialize(),
      success: submitFinished
    });
}

function submitFinished( response ) {
  	var messageDelay = 5000;
  	var jsonResponse = JSON.parse(response);
	if(jsonResponse.status == 0){
		  $('#successMessage').fadeIn().delay(messageDelay).fadeOut();
		  setTimeout("$.fancybox.close()", 5000);

   		 $('#message').val( "" );
		window.location = "http://art.usc.edu/loanerV3/kits/index.php";
    
 	 } else {
  	  

		$.fancybox.close();

	    showError(jsonResponse.message);

	

  }
}






// function deactivate(id){
// 		if(confirm("Are you sure you want to deactivate kit "+id+"?")){
// 			var notes = prompt("Please enter the reason","None");
// 			if(notes!=null){
// 				$.post("functions.php",
// 						{
// 								"deactivate":id,
// 								"notes":notes,
// 						},
// 						function(response){
// 								var jsonResponse = JSON.parse(response);
// 								if(jsonResponse.status == 0){
// 										alert(jsonResponse.message);
// 										window.location.reload();
// 								}
// 								else
// 										$.fancybox.close();
// 										showError(jsonResponse.message);
// 						}
// 				);
// 						$.post("deactivationEmail.php",
// 						{
// 								"deactivate":id,
// 								"notes":notes,
// 						},
// 						function(response){
// 						}
// 				);
// 				
// 			}else{
// 				return false;
// 			}
// 		}
// 		else{
// 				return false;
// 		}
// }


// function deactivateKits(id){
// 		if(confirm("Are you sure you want to deactivate kit "+id+"?")){
// 				$.get("http://art.usc.edu/loanerV3/kits/functions.php",
// 						{
// 								"deactivate":id
// 						},
// 						function(response){
// 								var jsonResponse = JSON.parse(response);
// 								if(jsonResponse.status == 0){
// 										alert(jsonResponse.message);
// 										window.location = "http://art.usc.edu/loanerV3/kits";
// 								}
// 								else
// 										$.fancybox.close();
// 										showError(jsonResponse.message);
// 						}
// 				);
// 		}
// 		else{
// 				return false;
// 		}
// }

function reactivate(id){
		if(confirm("Are you sure you want to Reactivate kit "+id+"?")){
				$.get("functions.php",
						{
							"reactivate":id
						},
							function(response){
								var jsonResponse = JSON.parse(response);
								if(jsonResponse.status == 0){
										alert(jsonResponse.message);
										window.location.reload();
								}
								else
										$.fancybox.close();
										showError(jsonResponse.message);
						}
				);
		}
		else{
				return false;
		}
}