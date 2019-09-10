// Author: Nikhil Handyal
// Date Created: 03/15/2012
// Dept: USC ROSKI SCHOOL OF FINE ARTS
// PROJECT: Loaner
// Description: Javascript functios required for loans/returnLoan page
//
// -------------------------------------------- LOANS/returnLoan.JS ----------------------------------------- //

var uidset = false;
var itemidset = false;
var eq_type = "";

$(document).ready(function(){

		$("#userid").focus();
		
		$('#userid').change(function(){
				//validate user id
				var userid = $("#userid").val();
				if(userid != ""){
						$('#uidWaiting').css({"display" : "inline"});
						$.get("returnLoanFunctions.php?",
								{
										"validateUID"	:	userid
								},
								function(response){
										var jsonResponse = JSON.parse(response);
										if(jsonResponse.status == 0){
												uidset = true;
												$('#userid').attr({"readonly":"readonly"}).addClass("readonly");
												$('#uidWaiting').css({"display" : "none"});
												$('#uidResultImg').css({"display" : "inline"});
												hideError();
										}
										else{
												$('#uidWaiting').css({"display" : "none"});
												showError(jsonResponse.message);
										}
								}
						);
				}
		});
		
		$('#itemid').change(function(){
				validateItemID();
		});
		
		$('#submit').click(function(){
				returnLoan();
		});
		
		$('#reset').click(function(){
				window.location.reload();
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

function validateItemID(){
		if(uidset == true){
				$('#idWaiting').css({"display" : "inline"});
				$.get("returnLoanFunctions.php?",
						{
								"validateItemID"	:	$("#itemid").val(),
								"userid"					: $("#userid").val()
						},
						function(response){
								var jsonResponse = JSON.parse(response);
								eq_type = jsonResponse.type;
								if(jsonResponse.status == 0){
										hideError();
										itemidset = true;
// 										$('#itemid').attr({"readonly":"readonly"}).addClass("readonly");
										$('#idWaiting').css({"display" : "none"});
										$('#idResultImg').css({"display" : "inline"});
										$("#loan-details-container").html(jsonResponse.loanInformation);
										$("#equipment-checkout-wrapper").html(jsonResponse.listedEquipment);
										
										// Add event listeners for missing and broken items
										$(".missing-item").click(function(){
												missingItem(this);
										});
										$('.broken-item').click(function(){
												brokenItem(this);		
										});
								}
								else{
										$('#idWaiting').css({"display" : "none"});
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

function returnLoan(){
		var confirmReturn = true;
		var notes = "";
		
		
		if(!uidset || !itemidset){
				showError("Make sure all required fields are filled.");
				return false;
		}
	
		if( (document.getElementsByClassName("not-scanned")).length != 0){
				showError("Make sure all items have been scanned.");
				return false;
		}
		
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
		 
		
		
		//make an ajax post request.
		if(confirmReturn){
				$('#submitWaiting').css({"display" : "inline"});
				$.post("returnLoanFunctions.php?return=1",
								{    
										"itemid"     	: $("#itemid").val(),
										"userid"    	: $("#userid").val(),
										"notes"     	: notes,
										"type"			: eq_type,
										"brokenEQ"		: brokenEQ,
										"missingEQ"		: missingEQ
								},
								function(response){
										var jsonResponse = JSON.parse(response);
										if(jsonResponse.status == 0){
												$('#submitWaiting').css({"display" : "none"});
												var htmlContent = "<div style='width:300px; font-size:20px; text-align:center;'>"+jsonResponse.message+"</div>"
										$.fancybox({	
												'centerOnScroll'	: true, 
												'content'					: htmlContent,
												'onClosed'				: function(){window.location = "https://art.usc.edu/loanerV3/loans";}
										});
										}
										else{
												showError(jsonResponse.message);
												$('#submitWaiting').css({"display" : "none"});
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