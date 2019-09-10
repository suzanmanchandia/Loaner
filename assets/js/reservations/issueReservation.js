// Author: Nikhil Handyal
// Date Created: 03/15/2012
// Dept: USC ROSKI SCHOOL OF FINE ARTS
// PROJECT: Loaner
// Description: Javascript functios required for resrvations/issueReservation page
//
// -------------------------------------------- RESERVATIONS/ISSUERESERVATION.JS ----------------------------------------- //

var uidset = false;
var itemIDset = false;
var validLoanLength = false;

$(document).ready(function(){
		
		$("#userid").focus();
		$("#issue_date").datepicker({minDate: 0});
		
		$('#userid').change(function(){
				//validate user id
				var userid = $("#userid").val();
				if(userid != ""){
						$('#uidWaiting').css({"display" : "inline"});
						$.get("issueReservationFunctions.php?",
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
				if(uidset == true){
						$('#idWaiting').css({"display" : "inline"});
						$.get("issueReservationFunctions.php?",
								{
										"validateItemID"	:	$("#itemid").val(),
										"userid"					: $("#userid").val(),
										"type"						:	$("#item-type option:selected").val()
								},
								function(response){
										var jsonResponse = JSON.parse(response);
										if(jsonResponse.status == 0){
												hideError();
												$('#itemid').attr({"readonly":"readonly"}).addClass("readonly");
												$('#idWaiting').css({"display" : "none"});
												$('#idResultImg').css({"display" : "inline"});
												$("#loan_length").val(jsonResponse.loan_length);
												$("#listed-equipment").html(jsonResponse.equipmentHTML);
												$('#item-type').attr({"disabled":"disabled"});
												validateLoanLength();
												itemIDset = true;
										}
										else{
												$('#idWaiting').css({"display" : "none"});
												showError(jsonResponse.message);
										}
								}
						);
				}
		});
		
		$('#loan_length').change(function(){
				validateLoanLength();
		});
		
		$('#submit').click(function(){
				submitReservation();
		});
		
		$('#reset').click(function(){
				window.location.reload();
		});
});

function validateLoanLength(){
		var llength = $("#loan_length").val();
		if (llength <= 0 || isNaN(Number(llength))){
				showError("Loan length must be greater than 0");
				validLoanLength = false;
		}
		else{
				hideError();
				validLoanLength = true;
		}
}

function submitReservation(){
		
		var notes = "";
		
		if(!uidset || !itemIDset){
				showError("Make sure all required fields are filled.");
				return false;
		}
		
		if(!validLoanLength){
				showError('Loan length must be greater than 0');
				return false;
		}
		
		if($.trim($('#issue_date').val()).length == 0){
				showError('Please select an issue date');
				return false;
		}
		
		if($('#notes').val() == ""){
				notes = "+";
		}
		else{
				notes = $('#notes').val();
		}
		
		
		//make an ajax post request.
		$('#submitWaiting').css({"display" : "inline"});
		$.post("issueReservationFunctions.php?issue=true",
						{    
								"itemid"     	: $("#itemid").val(),
								"userid"    	: $("#userid").val(),
								"issue_date" 	: $("#issue_date").val(),
								"notes"     	: notes,
								"loan_length"	: $("#loan_length").val(),
								"type"				: $("#item-type option:selected").val()
						},
						function(response){
								var jsonResponse = JSON.parse(response);
								if(jsonResponse.status == 0){
										$('#submitWaiting').css({"display" : "none"});
										alert(jsonResponse.message);
										window.location = "https://art.usc.edu/loanerV3/reservations";
								}
								else{
										$('#submitWaiting').css({"display" : "none"});
										showError(jsonResponse.message);
								}
						}
		);//end of Ajax Post Request
}