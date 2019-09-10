// Author: Nikhil Handyal
// Date Created: 03/15/2012
// Dept: USC ROSKI SCHOOL OF FINE ARTS
// PROJECT: Loaner
// Description: Javascript functios required for equipments/index page
//
// -------------------------------------------- EQUIPMENTS/INDEX.JS ----------------------------------------- //

$(document).ready(function() {
		// set focus for search box
		$("#searchBox").focus();
		
		setSearchField();
		
		document.getElementById("contentTableHeader").addEventListener("click", function(e) {
				var target =  e.target.className;
				buildURL({
						pageURL: "http://art.usc.edu/loanerV3/equipments/index.php",
						sortField: target,
						defaultSort: "equipmentid"
				});
		});
		
		$('#cancel').click( function() { 
		 $.fancybox.close();
		 });	
		 
		 $('#successMessage').hide();
		 $('#failureMessage').hide();
		 
          $('#equipSubCatID').change(function(){
    				$('#search').submit();
		});
		
});

function showDetailsLightbox(id){
		var targetURL = "lightboxPages/indexDetails.php?id="+id;
		$.fancybox.showActivity();
		$.get(targetURL,function(response){
				$.fancybox({
								'autoDimensions'	: false,
								'width'						: 1100,
								'height'					: 618,
								'content'					: response
						});
				}
		);
}

function showKitsDetailsLightbox(id){
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
// 		if(confirm("Are you sure you want to deactivate equipment "+id+"?")){
			var target_URL = "lightboxPages/deactivationNotes.php?equipid="+id;
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
		window.location = "http://art.usc.edu/loanerV3/equipments/index.php";
    
 	 } else {
	 	 $.fancybox.close()
  		showError(jsonResponse.message);
// 	  $('#failureMessage').fadeIn().delay(messageDelay).fadeOut();
// 	  setTimeout("$.fancybox.close()", 2000);
// 	  window.location = "http://art.usc.edu/loanerV3/equipments/index.php";

  }
}




function reactivate(id){
		if(confirm("Are you sure you want to Reactivate equipment "+id+"?")){
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


function getXMLHTTP() { //fuction to return the xml http object
		var xmlhttp=false;	
		try{
				xmlhttp=new XMLHttpRequest();
		}
		catch(e){		
				try{			
						xmlhttp= new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch(e){
						try{
								xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
						}
						catch(e1){
								xmlhttp=false;
						}
				}
		}
		return xmlhttp;
}

function getSubCatt(strURL) {	
		$('#search').submit();
		var req = getXMLHTTP();
		if (req) {					
				req.onreadystatechange = function() {
						if (req.readyState == 4) {
								// only if "OK"
								if (req.status == 200) 
										document.getElementById('equipSubCat').innerHTML=req.responseText;
												
								// else
// 										alert("There was a problem while using XMLHTTP:\n" + req.statusText);
		
						}
				}			
				req.open("GET", strURL, true);
				req.send(null);
		}
          
}




