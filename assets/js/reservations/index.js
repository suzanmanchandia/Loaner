// Author: Nikhil Handyal
// Date Created: 03/15/2012
// Dept: USC ROSKI SCHOOL OF FINE ARTS
// PROJECT: Loaner
// Description: Javascript functios required for resrvations/index page
//
// -------------------------------------------- RESERVATIONS/INDEX.JS ----------------------------------------- //

$(document).ready(function() {
		// set focus to search box
		$("#searchBox").focus();
		
		setSearchField();
		
		document.getElementById("contentTableHeader").addEventListener("click", function(e) {
				var target =  e.target.className;
				buildURL({
						pageURL: "https://art.usc.edu/loanerV3/reservations/index.php",
						sortField: target,
						defaultSort: "issue_date"
				});
		});
		
		$('.equipment-input').live("keypress", function(e) {
				/* ENTER PRESSED*/
				if (e.keyCode == 13) {
						/* FOCUS ELEMENT */
						var inputs = $(this).parents("form").eq(0).find(":input");
						var idx = inputs.index(this);

						if (idx == inputs.length - 1) {
								inputs[0].select()
						} else {
								inputs[idx + 1].focus(); //  handles submit buttons
								inputs[idx + 1].select();
						}
						return false;
				}
		});
});

function cancel(loanID){
		if(confirm("This will cancel reservation #"+loanID+". Are you sure you want to continue?")){
				$.get("issueReservationFunctions.php",{
								"cancel"	: loanID
						},
						function(response){
								var jsonResponse = JSON.parse(response);
								if(jsonResponse.status == 0){
										hideError();
										alert(jsonResponse.message);
										window.location = "http://art.usc.edu/loanerV3/reservations/index.php";
								}
								else{
										showError(jsonResponse.message);
								}
						}
				);
		}
		else{
				return false;
		}
}

function validateCheckout(loanID){
		$.get("issueReservationFunctions.php",{
						"validateCheckoutTime" : loanID
				},
				function(response){
						var jsonResponse = JSON.parse(response);
						if(jsonResponse.status == 0){
								$('#lightBoxData').html("");
								var dueDate_Input = "<input type='hidden' id='res_due_date' value='"+jsonResponse.due_date+"' />";
								$('#lightBoxData').html(dueDate_Input);
								showCheckoutLightbox(loanID);
								
						}
						else{
								$.fancybox.close();
								showError(jsonResponse.message);
						}
				}
		);
}

function showDetailsLightbox(loanID,view){
		var targetURL = "lightboxPages/indexDetails.php?lid="+loanID+"&view="+view;
		$.fancybox.showActivity();
		$.get(targetURL,function(response){
				$.fancybox({
								'autoDimensions'	: false,
								'width'						: 1100,
								'height'					: 618,
								'content'					: response,
								'onComplete'			: function(){fancyBoxResize()}
						});
				}
		);
}

function showCheckoutLightbox(loanID){
		var targetURL = "lightboxPages/checkoutReservation.php?lid="+loanID;
		$.fancybox.showActivity();
		$.get(targetURL,function(response){
				$.fancybox({
						'autoDimensions'	: false,
						'width'						: 1100,
						'height'					: 618,
						'centerOnScroll'	: true,
						'content'					: response,
						'onComplete'			: function(){fancyBoxResize()}
				});
		});
}

function validateEqID(obj){ // function to see if scanned item is displayed in the item list
		var unScannedItems = document.getElementsByClassName("not-scanned");
		for (var i = 0; i < unScannedItems.length; i++){
				if(obj.value == unScannedItems[i].id){
						var itemID = '#'+unScannedItems[i].id;
						$(itemID).removeClass('not-scanned').addClass('scanned');;
						$('#'+obj.id).attr({"readonly":"readonly"}).addClass("readonly");
						break;
				}
		}
}

function validateSubmit(){
		if($('#all-items-ok').val() == "false"){
				alert("There are items in this loan that cannot be checked out due to there condition. You must edit the condition of the items before you can check them out.");
		}
		else if( (document.getElementsByClassName("not-scanned")).length == 0){
				submitReservation();
		}
		else{
				alert("Please verify all form elements are correct and try again.");
		}
}

function submitReservation(){
		var notes = "";
		
		if($('#notes').val() == ""){
				notes = "None";
		}
		else{
				notes = $('#notes').val();
		}
		
		$('#submitWaiting').css({"display" : "inline"});
		$.get("issueReservationFunctions.php",{
				'checkoutReservation'	: $('#lid').val(),
				'notes'								: $('#notes').val(),
				'itemID'							: $('#itemID').val(),
				'type'								: $('#type').val(),
				'due_date'						: $('#res_due_date').val()
		},
		function(response){
				var jsonResponse = JSON.parse(response);
				if(jsonResponse.status == 0){
						$('#submitWaiting').css({"display" : "none"});
						alert(jsonResponse.message);
						window.location = "http://art.usc.edu/loanerV3/reservations/index.php";
				}
				else{
						$('#submitWaiting').css({"display" : "none"});
						alert(jsonResponse.message);
				}
		}
		);
}

function  missingItem(callingObj){
		var equipmentWrapper = $(callingObj).parent().parent();
		if($(equipmentWrapper).hasClass("missing")){
				$(equipmentWrapper).removeClass("missing").addClass("not-scanned");
		}
		else{
				$(equipmentWrapper).removeClass("not-scanned scanned broken").addClass("missing");
		}
}

function brokenItem(callingObj){
		var equipmentWrapper = $(callingObj).parent().parent();
		if($(equipmentWrapper).hasClass("broken")){
				$(equipmentWrapper).removeClass("broken").addClass("not-scanned");
		}
		else{
				$(equipmentWrapper).removeClass("not-scanned missing").addClass("broken");
		}
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

function showUsersDetailsLightbox(id){
		var targetURL = "https://art.usc.edu/loanerV3/users/lightboxPages/indexDetails.php?id="+id;
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