// Author: Nikhil Handyal
// Date Created: 03/15/2012
// Dept: USC ROSKI SCHOOL OF FINE ARTS
// PROJECT: Loaner
// Description: Javascript functios required for loans/index page
//
// -------------------------------------------- LOANS/INDEX.JS ----------------------------------------- //

$(document).ready(function() {
		// set focus to search field
		$("#searchBox").focus();
		
		setSearchField();
		
		document.getElementById("contentTableHeader").addEventListener("click", function(e) {
				var target =  e.target.className;
				buildURL({
						pageURL: "https://art.usc.edu/loanerV3/loans/index.php",
						sortField: target,
						defaultSort: "issue_date"
						// setSearchField: false
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
		// 	$('#searchBox').live("keypress", function(e) {
// 				/* ENTER PRESSED*/
// 				if (e.keyCode == 13) {
// 					alert("testing");
// 				}
// 				});
		
});

function details(loanID, view){
		var targetURL = "lightboxPages/indexDetails.php?lid="+loanID+"&view="+view;
		$.fancybox.showActivity();
		$.get(targetURL,function(response){
				$.fancybox({
								'autoDimensions'	: false,
								'centerOnScroll'	: true, 
								'width'						: 1100,
								'height'					: 618,
								'content'					: response,
								'onComplete'			: function(){fancyBoxResize()}
						});		
				}
		);
}

function returnLoan(loanID){
		var targetURL = "lightboxPages/indexReturn.php?lid="+loanID;
		$.fancybox.showActivity();
		$.get(targetURL,function(response){
				$.fancybox({
						'autoDimensions'	: false,
						'centerOnScroll'	: true, 
						'width'			: 1100,
						'height'		: 618,
						'content'		: response,
						'onComplete'		: function(){fancyBoxResize()}
				});
				
				// Add event listeners
				$(".missing-item").click(function(){
						missingItem(this);
				});
				$('.broken-item').click(function(){
						brokenItem(this);		
				});
		});
}

function renew(loanID, view){
		if(confirm("Are you sure you want to renew loan "+loanID+"?")){
				$.get("renewFunctions.php",{
						"loanID"	: loanID,
						"view"		: view
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

function validateReturnSubmit(){
		if( (document.getElementsByClassName("not-scanned")).length == 0 ){
				returnSubmit();
		}
		else{
				alert("Please verify all items have been scanned");
		}
}

function returnSubmit(){
		var confirmReturn = true;
		
		// return loan preprocessing
		var notes = "";
		
		if($('#notes').val() == ""){
				notes = "+";
		}
		else{
				notes = $('#notes').val();
		}
		
		// generate a comma seperated list of broken and missing equipment
		 var brokenEQ = $('.broken').map(function() {
												return $(this).attr("id");
										}).get().join(',');
		 
		 var missingEQ = $('.missing').map(function() {
												return $(this).attr("id");
										}).get().join(',');
		 
		 // set the broken and missing variables to N/A if there are no broken or missing equipment
		 if (brokenEQ == ""){
				brokenEQ = "N/A";
		 }
		 
		 if (missingEQ == ""){
				missingEQ = "N/A";
		 }
		 
		 // notify the user if they are submitting a loan with broken / missing equipment
		 if (brokenEQ != "N/A" || missingEQ != "N/A")
				confirmReturn = confirm("You have indicated that there are broken or missing items. Are you sure this is correct?");
		 
		if (confirmReturn)
				console.log("returning loan");
		
		
		if(confirmReturn){
				$('#submitWaiting').css({"display" : "inline"});
				$.post('returnLoanFunctions.php?return=true',
								{    
										"itemid"		  : $("#itemid").val(),
										"userid"    	: $("#userid").val(),
										"notes"     	: notes,
										"type"				: $('#item-type').val(),
										"brokenEQ"		: brokenEQ,
										"missingEQ"		: missingEQ
								},
								function(response){
										var jsonResponse = JSON.parse(response);
										if(jsonResponse.status == 0){
												$('#submitWaiting').css({"display" : "none"});
												var htmlContent = "<div style='width:300px; line-height:50px; text-align:center; font-size:18px; font-weight:bold;'>"+jsonResponse.message+"</div>";
												$.fancybox({	
														'centerOnScroll'	: true, 
														'content'					: htmlContent,
														'onClosed'				: function(){window.location.reload();}
												});
										}
										else{
												$('#submitWaiting').css({"display" : "none"});
												alert(jsonResponse.message);
										}
								}
				);//end of Ajax Post Request
		}
}

function  missingItem(callingObj){
		var equipmentWrapper = $(callingObj).parent().parent();
		var status = $(equipmentWrapper).find(".status .details-content");
		if($(equipmentWrapper).hasClass("missing")){
				var originalCondition = $(equipmentWrapper).find(".original-condition").val();
				$(status).text(originalCondition);
				$(equipmentWrapper).removeClass("missing");
				if(!$(equipmentWrapper).hasClass("scanned"))
						$(equipmentWrapper).addClass("not-scanned");
		}
		else{
				$(status).text("Missing");
				$(equipmentWrapper).removeClass("broken").removeClass("not-scanned").addClass("missing");
		}
}

function brokenItem(callingObj){
		var equipmentWrapper = $(callingObj).parent().parent();
		var status = $(equipmentWrapper).find(".status .details-content");
		if($(equipmentWrapper).hasClass("broken")){
				var originalCondition = $(equipmentWrapper).find(".original-condition").val();
				$(status).text(originalCondition);
				$(equipmentWrapper).removeClass("broken");
				if(!$(equipmentWrapper).hasClass("scanned"))
						$(equipmentWrapper).addClass("not-scanned");
		}
		else{
				$(status).text("Damaged");
				$(equipmentWrapper).removeClass("missing").addClass("broken");
		}
}

function editFine(loanID, currentFine){
		var htmlContent = "<div style='padding:7px 20px; width:400px'>";
		htmlContent += "<div style='margin-bottom:7px; width:100%; text-align:center; font-size:12px; font-weight:bold; border-bottom:1px solid;'>Edit Fine</div>";
		htmlContent += "<div class='pf-element' style='padding-bottom:5px'><div class='pf-description-float' style =\"font-weight:bold;\">Current Fine:</div><div class='pf-content-float'>$"+currentFine+"</div><div class='clear'></div></div>";
		htmlContent += "<div class='pf-element' style='padding-bottom:5px; border-bottom:1px solid;'><div class='pf-description-float' style =\"font-weight:bold;\">New Fine:</div><div class='pf-content-float'><input class='pf-text-input' id='new-fine' type='text' name='new-fine' /></div><div class='clear'></div></div>";
		htmlContent += "<div style='width:110px; margin:auto; margin-top: 5px;'><div class='form-button' onclick='submitEditFine()'>Submit</div></div>";
		htmlContent += "<input type='hidden' id='fb-loanID' value='"+loanID+"' /><input type='hidden' id='fb-currentFine' value='"+currentFine+"' />";
		htmlContent += "</div>";
		$.fancybox({	
				'centerOnScroll'	: true, 
				'content'					: htmlContent
		});
}

function submitEditFine(){
		var newFine = parseInt($("#new-fine").val());
		if(!isNaN(newFine) && newFine >= 0){
				// new fine is valid
				$.get("editLoanFunctions.php",
						{
								"loanID"		: $('#fb-loanID').val(),
								"newFine"		: newFine
						},
						function(response){
								var jsonResponse = JSON.parse(response);
								if(jsonResponse.status == 0){
										window.location.reload();
								}
								else{
										$.fancybox.close();
										showError("jsonResponse.message");
								}
						}
				);
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