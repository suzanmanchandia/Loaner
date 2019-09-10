// Author: Nikhil Handyal
// Date Created: 03/15/2012
// Dept: USC ROSKI SCHOOL OF FINE ARTS
// PROJECT: Loaner
// Description: Javascript functios required for equipments/edit page
//
// -------------------------------------------- EQUIPMENTS/EDIT.JS ----------------------------------------- //

var validKitID = false;

$(document).ready(function() {
		// set focus to kit id
		$("#kitid").focus();
		

		$("#kitid").blur(function(){
				validateKitID();
		});
		
		$(".manu-changeDay").change(function(){
				var daysInMonth = 32 - new Date($("#manuDate-year > option:selected").val(), $("#manuDate-month > option:selected").val()-1, 32).getDate();
				var optionHtml = "";
				for(var i = 1; i <= daysInMonth; i++){
						optionHtml += "<option value='"+i+"'";
						if (i == 1) { optionHtml += " selected='selected'";}
						optionHtml += ">"+i+"</option>";
				}
				$("#manuDate-day").html(optionHtml);
		});
		
		$(".purch-changeDay").change(function(){
				var daysInMonth = 32 - new Date($("#purchDate-year > option:selected").val(), $("#purchDate-month > option:selected").val()-1, 32).getDate();
				var optionHtml = "";
				for(var i = 1; i <= daysInMonth; i++){
						optionHtml += "<option value='"+i+"'";
						if (i == 1) { optionHtml += " selected='selected'";}
						optionHtml += ">"+i+"</option>";
				}
				$("#purchDate-day").html(optionHtml);
		});
		
		$(".yearSelect").change(function(){
				var optionHtml = "";
				var selected = parseInt($(this).children("option:selected").val());
				for (var i = selected+10; i >= selected -10; i--){
						optionHtml += "<option value='"+i+"'";
						if(i == selected) { optionHtml += " selected='selected'";}
						optionHtml += ">"+i+"</option>";
				}
				$(this).html(optionHtml);
		});
});


function validateKitID(){
		var id = $("#kitid").val();
		if(id == ""){
				$(".pf-error").css("display","none");
				validKitID = true;
				return;
		}
		else{
				$.ajax({
								type: "GET",
								async: false,
								url: "functions.php",
								data: {validate: id},
								success: function(response){
										if(response == 0){
												$(".pf-error").css("display","none");
												validKitID = true;
										}
										else{
												$(".pf-error").css("display","inline");
												validKitID = false;
										}
								}
				});
		}
}

function submitForm(){
		validateKitID();
		
		if(validKitID == false){
				alert("Invalid Kit Id");
				return;
		}
			
		var access_area_ids = new Array();
		
		// Make comman seperated list of access areas
		var aa = $(".access_area:checked");
		if(aa.length == 0){
				access_area_ids.push("0");
		}
		else{
				for(i=0; i<aa.length; i++){
						access_area_ids.push($(aa[i]).val());
				}
		}
		
		var access_area_str = access_area_ids.join(",");
		
		var manufactureDate = (new Date($("#manuDate-year > option:selected").val(),$("#manuDate-month > option:selected").val()-1,$("#manuDate-day > option:selected").val()).getTime())/1000;
		var purchaseDate = (new Date($("#purchDate-year > option:selected").val(),$("#purchDate-month > option:selected").val()-1,$("#purchDate-day > option:selected").val()).getTime())/1000;
		
		//make an ajax post request.
		$('#submitWaiting').css({"display" : "inline"});
		$.post("functions.php?edit=1",
						{
						"kitid"       			:   $("#kitid").val(),
						"equipmentid"				:   $("#equipmentid").val(),
						"loanlenEQ"					:		$("#loanlenEQ").val(),
						"model"      				:   $("#model").val(),
						"cond"       				:   $("#cond").val(),
						"desc"       				:   $("#desc").val(),
						"notes"      				:   $("#notes").val(),
						"equipCatID"				:		$("#equipCatID").val(),
						"equipSubCatID"			:		$("#equipSubCatID").val(),						
						"manufacturer"			:		$("#manufacturer").val(),
						"manufactureDate"		:		manufactureDate,
						"expectedLifetime"	:		$("#expectedLifetime").val(),
						"manufSerial"				:		$("#manufSerial").val(),
						"location"					:		$("#location").val(),
						"owner"							: 	$("#owner").val(),
						"purchaseDate"			:		purchaseDate,
						"purchasePrice"			:		$("#purchasePrice").val(),
						"ipAddress"					:		$("#ipAddress").val(),
						"macAddress"				:		$("#macAddress").val(),
						"hostName"					:		$("#hostName").val(),
						"connectType"				:		$("#connectType").val(),
						"warrantyInfo"			:		$("#warrantyInfo").val(),
						"access_areas"			:		access_area_str
						},
						function(response){
								var jsonResponse = JSON.parse(response);
								if(jsonResponse.status == 0){
										$('#submitWaiting').css({"display" : "none"});
										alert(jsonResponse.message);
										$(window).scrollTop(0);
										window.location.reload();
								}
								else{
										$('#submitWaiting').css({"display" : "none"});
										showError(jsonResponse.message);
								}
						}
		); //end of Ajax Post Request
}		 //end of binding Submit Button

function getXMLHTTP() { //fuction to return the xml http object
		var xmlhttp=false;	
		try{
				xmlhttp=new XMLHttpRequest();
		}
		catch(e)	{		
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

function getSubCat(strURL) {		
		var req = getXMLHTTP();

		if (req) {
				req.onreadystatechange = function() {
						if (req.readyState == 4) {
								// only if "OK"
								if (req.status == 200) {	
										document.getElementById('equipSubCat').innerHTML=req.responseText;						
								}
								else{
										alert("There was a problem while using XMLHTTP:\n" + req.statusText);
								}
						}				
				}			
		req.open("GET", strURL, true);
		req.send(null);
		}
}

function getNetwork(strURL) {		

		var req = getXMLHTTP();
		
		if (req) {
				req.onreadystatechange = function() {
						if (req.readyState == 4) {
								// only if "OK"
								if (req.status == 200) {	
										document.getElementById('networkInfo').innerHTML=req.responseText;						
								}
								else{
										alert("There was a problem while using XMLHTTP:\n" + req.statusText);
								}
						}				
				}				
		req.open("GET", strURL, true);
		req.send(null);
		}
}


function showUser(str){
		if (str==""){
				document.getElementById("networkInfo").innerHTML="";
				return;
		}
		if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp=new XMLHttpRequest();
		}
		else{// code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function(){
				if (xmlhttp.readyState==4 && xmlhttp.status==200){
						document.getElementById("networkInfo").innerHTML=xmlhttp.responseText;
				}
		}
		xmlhttp.open("GET","networkInfo.php?",true);
		xmlhttp.send();
}

function deactivate(id){
		if(confirm("Are you sure you want to deactivate equipment "+id+"?")){
				$.get("functions.php",
						{
								"deactivate":id
						},
						function(response){
								var jsonResponse = JSON.parse(response);
								if(jsonResponse.status == 0){
										alert(jsonResponse.message);
										window.location = "http://art.usc.edu/loanerV3/equipments";
								}
								else
										showError(jsonResponse.message);
						}
				);
		}
		else{
				return false;
		}
}