// Author: Nikhil Handyal
// Date Created: 03/15/2012
// Dept: USC ROSKI SCHOOL OF FINE ARTS
// PROJECT: Loaner
// Description: Javascript functios required for loans/issueLoan page
//
// -------------------------------------------- LOANS/issueLoan.JS ----------------------------------------- //
// var flag=0;
var validItemIDS = false;
$(document).ready(function(){

		addItem();
		
		// give focus to the userid element
		$("#userid").focus().change(function(){validateUID()});
		
		
		// check to see if get user id variable is posted, if it is verify it and continue
		var urlVars = getUrlVars();
		if(urlVars['userid'] != undefined){
				// verify the userid
				$("#userid").val(decodeURIComponent(urlVars['userid']));
				validateUID();
		}
		
		// add event listeners
		$("#add-item").click(function(){addItem();});
		$("#reset").click(function(){window.location.reload()});
		$("#submit-loan").click(function(){submitLoan();});
		
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

// GLOBAL VALIDATION FUNCTIONS
function validateUID(){
		var userid = $("#userid").val();
		if(userid != ""){
				$('#uidWaiting').css({"display" : "inline"});
				$.get("issueLoanFunctions.php?",
						{
								"validateUID"	:	userid
						},
						function(response){
								var jsonResponse = JSON.parse(response);
								if(jsonResponse.status == 0){
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
}

function validateIID(fieldsetOBJ){
		var type = $(fieldsetOBJ).find(".item-type option:selected").val();
		var itemID = $(fieldsetOBJ).find(".itemid").val();
		if($("#userid").hasClass("readonly") && itemID != ""){
				$(fieldsetOBJ).find('.idWaiting').css({"display" : "inline"});
				$.get("issueLoanFunctions.php?",
						{
								"validateItemID"	:	itemID,
								"userid"					: $("#userid").val(),
								"type"						: type
						},
						function(response){
								var jsonResponse = JSON.parse(response);
								var itemOnPage = false;
								if(jsonResponse.status == 0){
										// check if equipment already exists on page
										for(var i = 0; i < jsonResponse.equipmentIDS.length; i++){
												$('.includedEquipment').each(function(){
														if($(this).val() == jsonResponse.equipmentIDS[i]){
																itemOnPage = true;
														}
												});
										}
										
										if(!itemOnPage){
												// add included equipment to page data div
												for(var i = 0; i < jsonResponse.equipmentIDS.length; i++){
										$("#page-data").append("<input type='hidden' class='includedEquipment "+jsonResponse.itemID+"' value='"+jsonResponse.equipmentIDS[i]+"' />");
												}
												
												hideError();
												validItemIDS = true;
// 												$(fieldsetOBJ).find(".itemid").attr({"readonly":"readonly"}).addClass("readonly");
												$(fieldsetOBJ).find('.item-type').attr({"disabled":"disabled"});
												$(fieldsetOBJ).find('.idWaiting').css({"display" : "none"});
												$(fieldsetOBJ).find('.idResultImg').css({"display" : "inline"});
												$(fieldsetOBJ).find(".loan-length").val(jsonResponse.loan_length);
												$(fieldsetOBJ).find(".hidden-elements").css("display","block");
// 												$(fieldsetOBJ).append(jsonResponse.htmlData);
												$(fieldsetOBJ).find("#detail").html(jsonResponse.htmlData);
												// validate loan length
												validateLoanLength($(fieldsetOBJ).find(".hidden-elements"));
												// Add event listeners for missing and broken items
												$(".missing-item").click(function(){
													missingItem(this);
												});
												$('.broken-item').click(function(){
													brokenItem(this);		
												});
										}
										else{
												$(fieldsetOBJ).find("#detail").html("");
												showError("This item is already staged for checkout.");
												$(fieldsetOBJ).find('.idWaiting').css({"display" : "none"});
										}
								}
								else{
										$(fieldsetOBJ).find('.idWaiting').css({"display" : "none"});
										$(fieldsetOBJ).find("#detail").html("");
										showError(jsonResponse.message);
								}
						}
				);
		}
		else if(!$("#userid").hasClass("readonly"))
				showError("You must enter a userid before scanning loan items");
}

function validateLoanLength(hiddenElementsOBJ){
		var llength = $(hiddenElementsOBJ).find(".loan-length").val();
		if (llength < 0 || isNaN(Number(llength))){
				$(hiddenElementsOBJ).find(".valid-loan-length").val("0");
				showError("Loan length must be greater than or equal to 0")
		}
		else{
				$(hiddenElementsOBJ).find(".valid-loan-length").val("1");
				hideError();
		}
}

function validateEqID(callingOBJ){
		var fieldsetOBJ = $(callingOBJ).parent().parent().parent();
		$(fieldsetOBJ).find(".equipment-wrapper").each(function(){
				if($(this).attr("id") == $(callingOBJ).val() && !$(this).hasClass("missing-notify")){
						$(this).removeClass("not-scanned").addClass("scanned");
						//$(callingOBJ).addClass("readonly").attr({"readonly":"readonly"});
				}
		});
}

function submitLoan(){
// 		validItemIDS = true;
		// Check to see if a valid userID has been entered
		if(!$("#userid").hasClass("readonly")){
				showError("You must enter a valid userid.");
				return false;
		}
		
		// make sure all items on page have valid itemID's
// 		$('.loan-item .itemid').each(function(){
		// $('.loan-item').each(function(){
// 				if(!$(this).hasClass("readonly")){
// 						showError("All items on page do not have valid item ID's.");
// 						validItemIDS = false;
// 				}
// 		});
		
		if(validItemIDS){
				// make sure all equipment on page has been scanned
				if($(".not-scanned").length != 0){
						showError("All staged equipments has not been scanned.");
						return false;
				}
				
				// prep loan items for submit
				// store item id and type
				loanItemArray = [];
				$(".loan-item").each(function(i){
						var type = $(this).find(".item-type option:selected").val();
						var itemID = $(this).find(".itemid").val();
						var notes = $(this).find(".notes").val();
						var loanType = $(this).find(".loan-type option:selected").val();
						var loanLength = $(this).find(".loan-length").val();
						
						// generate a comma seperated list of broken and missing equipment
						 var brokenEQ = $(this).find('.broken').map(function() {
												return $(this).attr("id");
										}).get().join(',');
		 
						 var missingEQ = $(this).find('.missing').map(function() {
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
// 					 if (brokenEQ != "N/A" || missingEQ != "N/A")
// 						{	confirmReturn = confirm("You have indicated that there are broken or missing items. Are you sure this is correct?");}
// 		 					alert(brokenEQ);
// 		 					alert(missingEQ);
				loanItemArray[i] = ({"itemid":itemID,"type":type,"notes":notes,"loanType":loanType,"loanLength":loanLength,"brokenEQ":brokenEQ,"missingEQ":missingEQ});
				});
				//loanItemArray = JSON.stringify(loanItemArray);
				
				
				$.post("submitLoan.php",
						{
								"userid"			:	$('#userid').val(),
								"loanItems"		: loanItemArray
						},
						function(response){
								var jsonResponse = JSON.parse(response);
								if(jsonResponse.status == 0){
										hideError();
// 										alert(jsonResponse.missing);
										var htmlContent = "<div style='width:300px; font-size:20px; text-align:center;'>"+jsonResponse.message+"</div>"
										$.fancybox({	
												'centerOnScroll'	: true, 
												'content'					: htmlContent,
												'onClosed'				: function(){window.location = "https://art.usc.edu/loanerV3/loans/index.php";}
										});
								}
								else{
										showError(jsonResponse.message);
								}
						}
				);
				
		}
		else{
			showError("Make sure all required fields are filled.");
			return false;
		}
}


// support validation functions
// find the correct destination objects for the global validation functions
function changeItemID(callingOBJ){
		validItemIDS = false;
		var fieldsetOBJ = $(callingOBJ).parent().parent().parent().parent().parent().parent().parent();
		var item = $(fieldsetOBJ).find('.flagid').val();
		var eql = document.getElementsByClassName("includedEquipment "+item);
		var eql_length = eql.length;
		var j;
		for(j=0; j < eql_length; j++)
		{	
			eql[0].remove();
		}
		var itemno = $(fieldsetOBJ).find('.itemid').val();
		$(fieldsetOBJ).find('.flagid').val(itemno);
		validateIID(fieldsetOBJ);
}

function changeItemType(callingOBJ){
		var fieldsetOBJ = $(callingOBJ).parent().parent().parent().parent().parent().parent().parent();
		validateIID(fieldsetOBJ);
}

function changeLoanLength(callingOBJ){
		var hiddenElementsOBJ = $(callingOBJ).parent().parent().parent().parent().parent().parent().parent();
		validateLoanLength(hiddenElementsOBJ);
}

function addItem(){

		var loanItemCount = parseInt($("#loan-item-count").val())+1;
		var newItemHTML = "<fieldset class='loan-item' id='loan-item-"+loanItemCount+"' style='display:none'>"+$(".loan-item-template").html()+"</fieldset>";
		var newItemID = "loan-item-"+loanItemCount;
		
		$("#loanerform").append(newItemHTML);
		$("#"+newItemID).find(".item-number").html("Item: "+loanItemCount);
		if(loanItemCount != 1)
				$("#"+newItemID).find(".item-legend").append("<div class='remove-item' onclick='removeItem(this)'></div>");
		$("#loan-item-count").val(loanItemCount);
		$("#loan-item-"+loanItemCount).fadeIn(750);
}

function removeItem(callingObj){
		var parent = $(callingObj).parent().parent().parent().parent().parent().parent();
		var cssOBJ = {"overflow":"hidden"};
		$("#loan-item-count").val(parseInt($("#loan-item-count").val())-1);
		var itemNo = $(parent).find('.itemid').val();
		var eqlist = document.getElementsByClassName("includedEquipment "+itemNo);
		var i;
		var leng = eqlist.length;
		for(i=0; i < leng; i++)
		{	
			eqlist[0].remove();
		}
		$(parent).css(cssOBJ).animate({"height":0,"opacity":0,"margin-bottom":0},750,function(){
				$(parent).remove();
				var i = 1;
				$('.loan-item').each(function(){
						$(this).attr("id","loan-item-"+i);
						$(this).find(".item-number").html("Item: "+i);
						i++;
				});
		});
	
}

function getUrlVars() {
		var vars = {};
		var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
				vars[key] = value;
		});
		return vars;
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