// Author: Nikhil Handyal
// Date Created: 03/15/2012
// Dept: USC ROSKI SCHOOL OF FINE ARTS
// PROJECT: Loaner
// Description: Javascript functios required for fines/index page
//
// -------------------------------------------- FINES/INDEX.JS ----------------------------------------- //

var xhr;
$(document).ready(function(){
		// set focus to live search box
		$("#liveSearch").focus();
		
		xhr = getXmlHttpRequestObject();
		liveSearchUpdateUsers("");
				
		$('#liveSearch').keyup(function(){
				if($('#liveSearch').val() != $('#liveSearch-previous').val()){
						$('#liveSearch-previous').val($('#liveSearch').val());
						liveSearchUpdateUsers($('#liveSearch').val());
				}
		});
		
});

function liveSearchUpdateUsers(search){
		if(xhr.readyState == 0 || xhr.readyState == 4){
				var url = "liveSearch.php?search="+search;
				xhr.open('GET',url,true);
				xhr.onreadystatechange = function() {
						if(xhr.readyState == 4){
								$('#user-selection-table-container').html(xhr.responseText);
						}
				}
				xhr.send();
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

function showLoansDetailsLightbox(id,view){
		var targetURL = "https://art.usc.edu/loanerV3/loans/lightboxPages/indexDetails.php?lid="+id+"&view="+view;
	
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

function deactivateKits(id){
		if(confirm("Are you sure you want to deactivate kit "+id+"?")){
				$.get("https://art.usc.edu/loanerV3/kits/functions.php",
						{
								"deactivate":id
						},
						function(response){
								var jsonResponse = JSON.parse(response);
								if(jsonResponse.status == 0){
										alert(jsonResponse.message);
										window.location = "http://art.usc.edu/loanerV3/kits";
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

function deactivateEquipments(id){
		if(confirm("Are you sure you want to deactivate equipment "+id+"?")){
				$.get("https://art.usc.edu/loanerV3/equipments/functions.php",
						{
								"deactivate":id
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

function returnLoan(loanID){
		var targetURL = "https://art.usc.edu/loanerV3/loans/lightboxPages/indexReturn.php?lid="+loanID;
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
				$.get("https://art.usc.edu/loanerV3/loans/renewFunctions.php",{
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

function details(loanID, view){
		var targetURL = "https://art.usc.edu/loanerV3/loans/lightboxPages/indexDetails.php?lid="+loanID+"&view=short";
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

function editFine(loanID, currentFine,userNum){
		var htmlContent = "<div style='padding:7px 20px; width:400px'>";
		htmlContent += "<div style='margin-bottom:7px; width:100%; text-align:center; font-size:12px; font-weight:bold; border-bottom:1px solid;'>Edit Fine</div>";
		htmlContent += "<div class='pf-element' style='padding-bottom:5px'><div class='pf-description-float' style =\"font-weight:bold;\">Current Fine:</div><div class='pf-content-float'>$"+currentFine+"</div><div class='clear'></div></div>";
		htmlContent += "<div class='pf-element' style='padding-bottom:5px; border-bottom:1px solid;'><div class='pf-description-float' style =\"font-weight:bold;\">New Fine:</div><div class='pf-content-float'><input class='pf-text-input' id='new-fine' type='text' name='new-fine' /></div><div class='clear'></div></div>";
		htmlContent += "<div style='width:110px; margin:auto; margin-top: 5px;'><div class='form-button' onclick='submitEditFine()'>Submit</div></div>";
		htmlContent += "<input type='hidden' id='fb-loanID' value='"+loanID+"' /><input type='hidden' id='fb-currentFine' value='"+currentFine+"' />";
		htmlContent += "<input type='hidden' id='editFine-userNum' value='"+userNum+"'/>";
		htmlContent += "</div>";
		$.fancybox({	
				'centerOnScroll'	: true, 
				'content'					: htmlContent
		});
}

function submitEditFine(){
		var newFine = parseInt($("#new-fine").val());
		var userNum = $('#editFine-userNum').val();
		if(!isNaN(newFine) && newFine >= 0){
				// new fine is valid
				$.get("https://art.usc.edu/loanerV3/loans/editLoanFunctions.php",
						{
								"loanID"		: $('#fb-loanID').val(),
								"newFine"		: newFine
						},
						function(response){
								var jsonResponse = JSON.parse(response);
								if(jsonResponse.status == 0){
										$.fancybox.close();
									//	alert(userNum);
										loadUserFines(userNum);
										//window.location.reload();
								}
								else{
										$.fancybox.close();
										showError("jsonResponse.message");
								}
						}
				);
		}
}

function getXmlHttpRequestObject() {
		if (window.XMLHttpRequest) {
				return new XMLHttpRequest();
		} else if(window.ActiveXObject) {
				return new ActiveXObject("Microsoft.XMLHTTP");
		} else {
				alert("Unable to process request");
		}
}

function loadUserFines(userNum,object){
	
		$('.user-selected').removeClass('user-selected');
		$(object).addClass("user-selected");
	
		$.get("loadUserFines.php",{"userNum":userNum},function(response){
				$('#user-fines-container').html(response);
				var outstandingBalance = parseInt($('#outstandingBalance').val());
				if(outstandingBalance == 0){
						$('#user-fines-container').html("<div id='user-fines-placeholder'>This user has no outstanding fines.</div>");
				}
		});
}

function payUserFine(userNum, userid, name){
		var htmlContent = "<div style='width:400px; min-height:100px'>";
		htmlContent += "<div style='width:100%; padding:5px 0px 5px 0px; text-align:center; background:rgb(80,80,80); color:white; font-size:16px; font-weight:bold'>Pay Fine For: "+name+"</div>";
		htmlContent += "<form id='payFine-form' method='POST' style='margin-top:15px'>";
		htmlContent += "<div><span style='width:38% ;float:left'>Payment Amount ($):</span>";
		htmlContent += "<input type='text' id='payFine-fancybox'/><div class='clear'></div></div>";
		htmlContent += "<div style='width:150px; margin:auto; margin-top:7px'><input type='submit' value='Pay' style='width:95px'/>&nbsp&nbsp<img id='submitWaiting' src='../etc/loading.gif' width='15' height='15' style='display: none'/>";
		htmlContent += "<input type='hidden' id='payFine-userNum' value='"+userNum+"'/>";
		htmlContent += "<input type='hidden' id='payFine-userid' value='"+userid+"'/>";
		htmlContent += "</form>";
		htmlContent += "</div>";
		$.fancybox({'content': htmlContent});
		$('#payFine-form').submit(function(event){
				submitUserFine(event);
		});
}

function submitUserFine(formEvent){
		hideError();
		$('#submitWaiting').css({"display" : "inline"});
		var userid = $('#payFine-userid').val();
		var userNum = $('#payFine-userNum').val();
		var paymentAmount = parseInt($('#payFine-fancybox').val());
		var outstandingBalance = parseInt($('#outstandingBalance').val());
		if(paymentAmount > outstandingBalance){
				$.fancybox.close();
				showError("User payment cannot exceed outstanding balance");
				paymentAmount = 0;
		}
		else if(paymentAmount <= 0 || isNaN(paymentAmount)){
				$.fancybox.close();
				showError("Invalid input");
		}
		else{
				$.get("payFine.php",{
								"userid"					: userid,
								"paymentAmount"		: paymentAmount
						},function(response){
								var jsonResponse = JSON.parse(response);
								if(jsonResponse.status == 0){
										$('#submitWaiting').css({"display" : "none"});
										$.fancybox.close();
										
										loadUserFines(userNum);
								}
								else{
										$('#submitWaiting').css({"display" : "none"});
										$.fancybox.close();
										showError(jsonResponse.message);
								}
						}
				);
		}
		formEvent.preventDefault();
}