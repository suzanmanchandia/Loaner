
$(document).ready(function(){
		// set focus to live search box
		positionReportControls();
		 $("#reports").css("color","rgb(255,255,255)");	
	  $("#reports").css("font-weight","bold");
		
		$("#liveSearch").focus();
		
// 		xhr = getXmlHttpRequestObject();
		liveSearchUpdateUsers("");
				
		$('#liveSearch').keyup(function(){
				if($('#liveSearch').val() != $('#liveSearch-previous').val()){
						$('#liveSearch-previous').val($('#liveSearch').val());
						liveSearchUpdateUsers($('#liveSearch').val());
				}
		});
		
});

function liveSearchUpdateUsers(search){
	
		      $.ajax({
		url:"liveSearch.php?search="+search,
		type:"GET",
		success:function(response){
		$("#user-selection-table-container").html(response);	
		}
		});	
}

				function fullscreen(){
						var fsw = $(document).width()-100;
						var fsy = Math.floor((9*fsw)/16);
						var targetURL = $('#report-url').val();
						if(targetURL != ""){
								$.fancybox({
										'type'						: 'iframe',
										'autoDimensions'	: false,
										'width'						: fsw,
										'height'					: fsy,
										'href'						: targetURL
								});
						}
				} 
				
				
				function loansMenu1(){
					$('#users').html("");
					$.ajax({
						url:"generateLoanReport.php",
						type:"GET",
						success:function(response){
							$('#menu1-content').html(response);
						}
					});
				}
				
				function loansMenu2(reportHeader){
// 					$('#user-container').show();
							$('#menu1-content').html("");							
							$.get("loans-menu2.php",{},function(response){
								
								$('#users').html(response);
						});
				}
				
				function loadUserReports(userNum,obj){
					$.ajax({
						url:"generateUserReport.php?userNum="+userNum,
						type:"GET",
						success:function(response){
							$('#menu1-content').html(response);
						}
					});
				}
				
				function expireItems(){
					$('#users').html("");
					$('#menu1-content').html("");	
					$.ajax({
						url:"generateItemReport.php",
						type:"GET",
						success:function(response){
							$('#menu1-content').html(response);
						}
					});
				
				
				}
				function printPage(id)
				{
  					  var html="<html>"; 
						html+="<head>";

						html+="<style type='text/css'>html{text-align:center} li{display:none}</style>";
						html+="</head><body>";
						html+= document.getElementById(id).innerHTML;
						html+="</body></html>";

  					 var printWin = window.open('','','left=200,top=10,width=400,height=500,toolbar=0,scrollbars=0,status  =0');
  					 printWin.document.write(html);
  					 printWin.document.close();
  					 printWin.focus();
  					 printWin.print();
  					 printWin.close();
				}		
				
				function saveAsPDF(id)
				{
  					  var html="<html>"; 
						html+="<head>";

						html+="<style type='text/css'>html{text-align:center} li{display:none}</style>";
						html+="</head><body>";
						html+= document.getElementById(id).innerHTML;
						html+="</body></html>";
						$('#rep-content').val(html);
						$('#myform').submit();

				}		
				
				function positionReportControls(){
						var iframeContainerPosition = $('#report-iframe-container').position();
						var cssOBJ = {
								'top' 	: iframeContainerPosition.top,
								'left'	: iframeContainerPosition.left
						};
						$('#report-controls').css(cssOBJ);
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
