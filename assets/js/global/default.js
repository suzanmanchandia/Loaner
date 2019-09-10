// Author: Nikhil Handyal
// Date Created: 03/15/2012
// Dept: USC ROSKI SCHOOL OF FINE ARTS
// PROJECT: Loaner
// Description: Global default javascript functions used throughout Loaner
//
// -------------------------------------------- DEFAULT.JS ----------------------------------------- //

$(document).ready(function(){
		$(document).dblclick(function(){
				hideError();
		});
		
		$.ajaxSetup({
				timeout : 5000,
				error		: function(xhr, status, error){
								if($.fn.fancybox){
										$.fancybox.hideActivity();
										$.fancybox.close();
								}
								showError("An error ccured while processing the request: "+status+". Please Try Agian Later.");
								$('.waiting').error(function(){}).css({"display" : "none"});
						}
		});
		
		var config = {
				over: expand,
				timeout: 250,
				out: collapse
		};
		
		$('#current-visible').hoverIntent(config);
});

function expand(){
		$('.options').stop().animate({height: 30},400,function(){});
}

function collapse(){
		$('.options').stop().animate({height: 0},400,function(){});
}

function sysadmToggleDept(deptID){
		$.post("/loanerV3/includes/functions.php",{
						"deptID" : deptID
				},function(response){
						var jsonResponse = JSON.parse(response);
						if(jsonResponse.status == 0){
								window.location.reload();
						}
				}
		);
}

function showError(errorMessage){
		$('#error').html(errorMessage);
		$('#error').css({display : "block"});
}

function hideError(){
		$('#error').css({display : "none"});
}

function deptToggle(uid){
		var selectedElement = $('select option:selected').val();
		$.post("../includes/functions.php",{
						"sysadmDeptToggle" 	: selectedElement,
						"uid"								: uid
				},
				function(response){
						var jsonResponse = JSON.parse(response);
						if(jsonResponse.status == 0){
								hideError();
								window.location.reload();
						}
						else{
								showError(jsonResponse.message);
						}
				}
		);
}

function fancyBoxResize(){
		var contentHeight = $('.createForm').height()+10;
		if(contentHeight < 570){
				$('#fancybox-wrapInner').css({'height':contentHeight});
				$('#fancybox-content').animate({'height':contentHeight},200,function(){$.fancybox.center();});
		}
}