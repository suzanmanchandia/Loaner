$(document).ready(function(){
		
});


function loadContainer2(tableName, header, fieldName,callingObj){
		$.get("functions.php?loadContainer2=true",{
						"tableName"	: tableName,
						"fieldName"	: fieldName,
						"header"		: header
				},
				function(response){
						var href = 'javascript:add("'+tableName+'","'+header+'","'+fieldName+'")';
						header = header+"<a href='"+href+"'><img src='../etc/add.png' style='float:right; margin-right:35px'/></a>";
						$('#sub-container-3-content').html("");
						$('#sub-container-3-header').html("");
						$('.sub-container-1-selected').removeClass('sub-container-1-selected');
						$(callingObj).addClass('sub-container-1-selected');
						$('#sub-container-2-header').html(header);
						$('#sub-container-2-content').html(response);
				}
		);
}

function loadContainer3(tableName, header, fieldName,callingObj){
		$.get("functions.php?loadContainer3=true",{
						"tableName"	: tableName,
						"fieldName"	: fieldName,
						"header"		: header
				},
				function(response){
						var href = 'javascript:add("'+tableName+'","'+header+'","'+fieldName+'")';
						header = header;//+"<a href='"+href+"'><img src='../etc/add.png' style='float:right; margin-right:35px'/></a>";
						$('#sub-container-3-content').html("");
						$('#sub-container-3-header').html("");
						$('.sub-container-1-selected').removeClass('sub-container-1-selected');
						$(callingObj).addClass('sub-container-1-selected');
						$('#sub-container-2-header').html(header);
						$('#sub-container-2-content').html(response);
				}
		);
}

function loadAccessAreaUsers(accessid, header, callingObj){
		header = header.replace(/\+/g," ");
		$.get("functions.php",{
						"loadAccessAreaUsers"	: accessid,
						"header"							: header
				},
				function(response){
						var href = 'javascript:addUsersAccessArea('+accessid+',"'+header+'")';
						header = "Users for: "+header+"<a href='"+href+"'><img src='../etc/add.png' style='float:right; margin-right:35px'/></a>";
						$('.sub-container-2-selected').removeClass('sub-container-2-selected');
						$(callingObj).addClass('sub-container-2-selected');
						$('#sub-container-3-header').html(header);
						$('#sub-container-3-content').html(response);
				}
		);
}

function editSubCategory(equipCatID, header){
		var htmlContent = "<div style='width:400px; height: 100px'>";
		htmlContent += "<div style='width:100%; padding:5px 0px 5px 0px; text-align:center; background:rgb(80,80,80); color:white; font-size:16px; font-weight:bold'>Edit Sub-Category</div>";
		htmlContent += "<form id='edit-subCat-form' method='POST' style='margin-top:15px'>";
		htmlContent += "<div><span style='width:38% ;float:left'>Sub-Category Name:</span>";
		htmlContent += "<input type='text' id='edit-fancybox' value='"+header+"' style='width:60%; float:right'/><div class='clear'></div></div>";
		htmlContent += "<div style='width:100px; margin:auto; margin-top:7px'><input type='submit' value='Edit'/>&nbsp&nbsp<img id='submitWaiting' src='../etc/loading.gif' width='15' height='15' style='display: none'/><input type='hidden' id='equipCatID' value='"+equipCatID+"'</input><input type='hidden' id='oldSubCat' value='"+header+"'/></div>";
		htmlContent += "</form>";
		htmlContent += "</div>";
		$.fancybox({'content': htmlContent});
		$('#edit-subCat-form').submit(function(event){
				editSubCat(event);
		});
}

function editSubCat(formEvent){
		$('#submitWaiting').css({"display" : "inline"});
		var equipCatID = $('#equipCatID').val();
		$.post("functions.php?editSubCat=true",{
						"newSubCat"		: $('#edit-fancybox').val(),
						"oldSubCat"		: $('#oldSubCat').val(),
						"equipCatID"	: equipCatID
				},function(response){
						var jsonResponse = JSON.parse(response);
						if(jsonResponse.status == 0){
								$('#submitWaiting').css({"display" : "none"});
								loadSubCategories(equipCatID, $('#ec-'+equipCatID));
								$.fancybox.close();
								var successString = $('#edit-fancybox').val() +" successfully edited in "+$('#ec-'+equipCatID).html();
								showError(successString);
						}
						else{
								$('#submitWaiting').css({"display" : "none"});
								$.fancybox.close();
								showError(jsonResponse.message);
						}
				}
		);
		formEvent.preventDefault();
}

function deleteSubCategory(equipCatID, currentValue){
		var htmlContent = "<div style='width:400px; height: 100px'>";
		htmlContent += "<div style='width:100%; padding:5px 0px 5px 0px; text-align:center; background:rgb(80,80,80); color:white; font-size:16px; font-weight:bold'>Delete Sub-Category</div>";
		htmlContent += "<form id='delete-subCat-form' method='POST' style='margin-top:15px'>";
		htmlContent += "<div><span style='width:38% ;float:left'>Sub-Category Name:</span>";
		htmlContent += "<input type='text' id='delete-fancybox' value='"+currentValue+"' readonly='readonly' style='width:60%; float:right; border:none; font-weight:bold; color:red'/><div class='clear'></div></div>";
		htmlContent += "<div style='width:100px; margin:auto; margin-top:7px'><input type='submit' value='Delete'/>&nbsp&nbsp<img id='submitWaiting' src='../etc/loading.gif' width='15' height='15' style='display: none'/><input type='hidden' id='equipCatID' value='"+equipCatID+"'/></div>";
		htmlContent += "</form>";
		htmlContent += "</div>";
		$.fancybox({'content': htmlContent});
		$('#delete-subCat-form').submit(function(event){
				if(confirm("Are you sure you want to delete this sub-category? This action cannot be undone."))
						deleteSubCat(event);
				else{
						$.fancybox.close();
						event.preventDefault();
				}
		});
}

function deleteSubCat(formEvent){
		$('#submitWaiting').css({"display" : "inline"});
		var equipCatID = $('#equipCatID').val();
		$.post("functions.php?deleteSubCat=true",{
						"subCat"			: $('#delete-fancybox').val(),
						"equipCatID"	: equipCatID
				},function(response){
						var jsonResponse = JSON.parse(response);
						if(jsonResponse.status == 0){
								$('#submitWaiting').css({"display" : "none"});
								loadSubCategories(equipCatID, $('#ec-'+equipCatID));
								$.fancybox.close();
								var successString = $('#delete-fancybox').val() +" successfully deleted from "+$('#ec-'+equipCatID).html();
								showError(successString);
						}
						else{
								$('#submitWaiting').css({"display" : "none"});
								$.fancybox.close();
								showError(jsonResponse.message);
						}
				}
		);
		formEvent.preventDefault();
}

function loadSubCategories(equipCatID, callingObj){
		$.get("functions.php?loadSubCategories=true",{
						"equipCatID"	: equipCatID
				},
				function(response){
						var href = 'javascript:addSubCategory('+equipCatID+')';
						header = "Sub-Category<a href='"+href+"'><img src='../etc/add.png' style='float:right; margin-right:35px'/></a>";
						$('.sub-container-2-selected').removeClass('sub-container-2-selected');
						$(callingObj).addClass('sub-container-2-selected');
						$('#sub-container-3-header').html(header);
						$('#sub-container-3-content').html(response);
				}
		);
}

function addSubCategory(equipCatID){
		var htmlContent = "<div style='width:400px; height: 100px'>";
		htmlContent += "<div style='width:100%; padding:5px 0px 5px 0px; text-align:center; background:rgb(80,80,80); color:white; font-size:16px; font-weight:bold'>Add Sub-Category</div>";
		htmlContent += "<form id='add-subCat-form' method='POST' style='margin-top:15px'>";
		htmlContent += "<div><span style='width:38% ;float:left'>Sub-Category Name:</span>";
		htmlContent += "<input type='text' id='add-fancybox' style='width:60%; float:right'/><div class='clear'></div></div>";
		htmlContent += "<div style='width:100px; margin:auto; margin-top:7px'><input type='submit' value='Add'/>&nbsp&nbsp<img id='submitWaiting' src='../etc/loading.gif' width='15' height='15' style='display: none'/><input type='hidden' id='sub-cat-equipCatID' value='"+equipCatID+"'</input></div>";
		htmlContent += "</form>";
		htmlContent += "</div>";
		$.fancybox({'content': htmlContent});
		$('#add-subCat-form').submit(function(event){
				addSubCat(event);
		});
}

function addSubCat(formEvent){
		$('#submitWaiting').css({"display" : "inline"});
		var equipCatID = $('#sub-cat-equipCatID').val();
		$.post("functions.php?addSubCat=true",{
						"newSubCat"		: $('#add-fancybox').val(),
						"equipCatID"	: equipCatID
				},function(response){
						var jsonResponse = JSON.parse(response);
						if(jsonResponse.status == 0){
								$('#submitWaiting').css({"display" : "none"});
								loadSubCategories(equipCatID, $('#ec-'+equipCatID));
								$.fancybox.close();
								var successString = $('#add-fancybox').val() +" successfully added to "+$('#ec-'+equipCatID).html();
								showError(successString);
						}
						else{
								$('#submitWaiting').css({"display" : "none"});
								$.fancybox.close();
								showError(jsonResponse.message);
						}
				}
		);
		formEvent.preventDefault();
}

function add(tableName, header, fieldName){
		var htmlContent = "<div style='width:400px; height: 100px'>";
		htmlContent += "<div style='width:100%; padding:5px 0px 5px 0px; text-align:center; background:rgb(80,80,80); color:white; font-size:16px; font-weight:bold'>Add "+header+"</div>";
		htmlContent += "<form id='add-form' method='POST' style='margin-top:15px'>";
		if(tableName == 'notificationEmails')
				var title = header+' Email:';
		else
				var title = header+' Name:';
		htmlContent += "<div><span style='width:30% ;float:left'>"+title+"</span>";
		htmlContent += "<input type='text' id='add-fancybox' style='width:65%; float:right'/><div class='clear'></div></div>";
		htmlContent += "<div style='width:100px; margin:auto; margin-top:7px'><input type='submit' value='Add'/>&nbsp&nbsp<img id='submitWaiting' src='../etc/loading.gif' width='15' height='15' style='display: none'/></div>";
		htmlContent += "<input type='hidden' id='tableName-fancybox' value='"+tableName+"'/><input type='hidden' id='fieldName-fancybox' value='"+fieldName+"'/><input type='hidden' id='header-fancybox' value='"+header+"'/>";
		htmlContent += "</form>";
		htmlContent += "</div>";
		$.fancybox({'content': htmlContent});
		$('#add-form').submit(function(event){
				submitAdd(event);
		});
}

function submitAdd(formEvent){
		$('#submitWaiting').css({"display" : "inline"});
		$.post("functions.php",{
						"add"						: $('#add-fancybox').val(),
						"tableName"			: $('#tableName-fancybox').val(),
						"fieldName"			: $('#fieldName-fancybox').val()
				},function(response){
						var jsonResponse = JSON.parse(response);
						if(jsonResponse.status == 0){
								$('#submitWaiting').css({"display" : "none"});
								var header = $('#header-fancybox').val();
								loadContainer2($('#tableName-fancybox').val(), header, $('#fieldName-fancybox').val(), $('#'+header));
								$.fancybox.close();
								var successString = $('#add-fancybox').val() +" successfully added to "+header;
								showError(successString);
						}
						else{
								$('#submitWaiting').css({"display" : "none"});
								$.fancybox.close();
								showError(jsonResponse.message);
						}
				}
		);
		formEvent.preventDefault();
}


function edit(tableName, header, fieldName, currentValue){
		var htmlContent = "<div style='width:400px; height: 100px'>";
		htmlContent += "<div style='width:100%; padding:5px 0px 5px 0px; text-align:center; background:rgb(80,80,80); color:white; font-size:16px; font-weight:bold'>Edit "+header+"</div>";
		htmlContent += "<form id='edit-form' method='POST' style='margin-top:15px'>";
		if(header=="Amount")
		{
			htmlContent += "<div><span style='width:30% ;float:left'>Daily Amount:$</span>";
		}else if(header=="Notifications")
		{
			htmlContent += "<div><span style='width:30% ;float:left'>"+header+" Email:</span>";
		}
		else
		{
			htmlContent += "<div><span style='width:30% ;float:left'>"+header+" Name:</span>";
		}
		htmlContent += "<input type='text' id='edit-fancybox' value='"+currentValue+"' style='width:65%; float:right'/><div class='clear'></div></div>";
		htmlContent += "<div style='width:100px; margin:auto; margin-top:7px'><input type='submit' value='Edit'/>&nbsp&nbsp<img id='submitWaiting' src='../etc/loading.gif' width='15' height='15' style='display: none'/></div>";
		htmlContent += "<input type='hidden' id='tableName-fancybox' value='"+tableName+"'/><input type='hidden' id='fieldName-fancybox' value='"+fieldName+"'/><input type='hidden' id='header-fancybox' value='"+header+"'/>";
		htmlContent += "<input type='hidden' id='currentValue-fancybox' value='"+currentValue+"'/>";
		htmlContent += "</form>";
		htmlContent += "</div>";
		$.fancybox({'content': htmlContent});
		$('#edit-form').submit(function(event){
				submitEdit(event);
		});
}



function submitEdit(formEvent){
		$('#submitWaiting').css({"display" : "inline"});
		$.post("functions.php",{
						"edit"					: $('#edit-fancybox').val(),
						"tableName"			: $('#tableName-fancybox').val(),
						"fieldName"			: $('#fieldName-fancybox').val(),
						"currentValue"	: $('#currentValue-fancybox').val()
				},function(response){
						var jsonResponse = JSON.parse(response);
						if(jsonResponse.status == 0){
								$('#submitWaiting').css({"display" : "none"});
								var header = $('#header-fancybox').val();
								if(header=="Amount")
								loadContainer3($('#tableName-fancybox').val(), header, $('#fieldName-fancybox').val(), $('#'+header));
								else
								loadContainer2($('#tableName-fancybox').val(), header, $('#fieldName-fancybox').val(), $('#'+header));
								$.fancybox.close();
								var successString = $('#edit-fancybox').val() +" successfully edited in "+header;
								showError(successString);
						}else if(jsonResponse.status == -1){
							$.fancybox.close();
							var successString = $('#edit-fancybox').val() +"&nbsp&nbsp is not a valid value";
							showError(successString);
						}
						else{
								$('#submitWaiting').css({"display" : "none"});
								$.fancybox.close();
								showError(jsonResponse.message);
						}
				}
		);
		formEvent.preventDefault();
}

function del(tableName, header, fieldName, currentValue){
		var htmlContent = "<div style='width:400px; height: 100px'>";
		htmlContent += "<div style='width:100%; padding:5px 0px 5px 0px; text-align:center; background:rgb(80,80,80); color:white; font-size:16px; font-weight:bold'>Delete "+header+"</div>";
		htmlContent += "<form id='del-form' method='POST' style='margin-top:15px'>";
		if(tableName == 'notificationEmails')
				var title = header+' Email:';
		else
				var title = header+' Name:';
		htmlContent += "<div><span style='width:30% ;float:left'>"+title+"</span>";
		htmlContent += "<input type='text' id='delete-fancybox' value='"+currentValue+"' readonly='readonly' style='width:65%; float:right; border:none; font-weight:bold; color:red'/><div class='clear'></div></div>";
		htmlContent += "<div style='width:100px; margin:auto; margin-top:7px'><input type='submit' value='Delete'/>&nbsp&nbsp<img id='submitWaiting' src='../etc/loading.gif' width='15' height='15' style='display: none'/></div>";
		htmlContent += "<input type='hidden' id='tableName-fancybox' value='"+tableName+"'/><input type='hidden' id='fieldName-fancybox' value='"+fieldName+"'/><input type='hidden' id='header-fancybox' value='"+header+"'/>";
		htmlContent += "</form>";
		htmlContent += "</div>";
		$.fancybox({'content': htmlContent});
		$('#del-form').submit(function(event){
				if(confirm("You cannot undo a delete. Are you sure you want to continue?"))
						submitDel(event);
				else{
						$.fancybox.close();
						event.preventDefault();
				}
		});
}

function submitDel(formEvent){
		$('#submitWaiting').css({"display" : "inline"});
		$.post("functions.php",{
						"delete"				: $('#delete-fancybox').val(),
						"tableName"			: $('#tableName-fancybox').val(),
						"fieldName"			: $('#fieldName-fancybox').val()
				},function(response){
						var jsonResponse = JSON.parse(response);
						if(jsonResponse.status == 0){
								$('#submitWaiting').css({"display" : "none"});
								var header = $('#header-fancybox').val();
								loadContainer2($('#tableName-fancybox').val(), header, $('#fieldName-fancybox').val(), $('#'+header));
								$.fancybox.close();
								var successString = $('#delete-fancybox').val() +" successfully deleted from "+header;
								showError(successString);
						}
						else{
								$('#submitWaiting').css({"display" : "none"});
								$.fancybox.close();
								showError(jsonResponse.message);
						}
				}
		);
		formEvent.preventDefault();
}

function removeUsersFromAccessArea(id){
		var header = $('#aa-header').val();
		if(confirm("Are you sure you want remove all users from "+header+"? This action cannot be undone.")){
				$('#submitWaiting-aau').css({"opacity" : "1"});
				$.post('functions.php?rufa=true',{
						"accessid"	: id
						},
						function(response){
								var jsonResponse = JSON.parse(response);
								if(jsonResponse.status == 0){
										$('#submitWaiting-aau').css({"opacity" : "0"});
										var callingObj = $('#aa-'+id);
										loadAccessAreaUsers(id,header,callingObj);
										var successString = ("All users successfully removed from "+header);
										showError(successString);
								}
								else{
										$('#submitWaiting-aau').css({"opacity" : "0"});
										showError(jsonResponse.message);
								}
						}
				);
		}
}

function addUsersAccessArea(aid, header){
		$.get('functions.php?addUsersAA=true',{"accessid":aid,"header":header},function(response){
				$.fancybox({'content': response});
				$('#aa-select-all').click(function(){
						var checkedStatus = this.checked;
						$('.all-users').each(function(){
								this.checked = checkedStatus;
						});
				});
		});
}

function aaSubmitUsers(){
		var aa_array = new Array();
		var selectedCheckboxes = $('.all-users:checked');
		if(selectedCheckboxes.length == 0)
				return false;
		
		selectedCheckboxes.each(function(){
				aa_array.push($(this).val());
		});
		var aa_string = aa_array.join(",");
		var accessid = $('#aa-accessid').val();
		var header = $('#aa-header').val();
		
		$('#submitWaiting').css({"display" : "inline"});
		$.post("functions.php?aaAddBatchUsers=true",{
						"accessid"			: accessid,
						"users"					: aa_string
				},function(response){
						var jsonResponse = JSON.parse(response);
						if(jsonResponse.status == 0){
								$('#submitWaiting').css({"display" : "none"});
								loadAccessAreaUsers(accessid, header, $('#'+header));
								$.fancybox.close();
								showError("Users successfully added to "+header);
						}
						else{
								$('#submitWaiting').css({"display" : "none"});
								$.fancybox.close();
								showError(jsonResponse.message);
						}
				}
		);
}

function deleteUserFromAccessArea(accessid, userid){
		userid = userid.replace(/\+/g," ");
		if(confirm("Are you sure you want to delete "+userid+" from this access area? This action cannot be undone.")){
				var header = $('#aa-header').val();
				
				$.post('functions.php?deleteUserAA=true',{
								"userid"		: userid,
								"accessid"		: accessid
						},function(response){
								var jsonResponse = JSON.parse(response);
								if(jsonResponse.status == 0){
										loadAccessAreaUsers(accessid, header, $('#'+header));
										showError(userid+" deleted from "+header);
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

function removeUsersFromAllAccessAreas(){
		if(confirm("Clicking OK will remove ALL USERS FROM ALL ACCESS AREAS. THIS ACTION CANNOT BE UNDONE.")){
				$.post('functions.php?removeUsersFromAllAccessAreas=true',{},function(response){
						var jsonResponse = JSON.parse(response);
						if(jsonResponse.status == 0){
								$('#sub-container-3-content').html("");
								$('#sub-container-3-header').html("");
								var successString = ("All users removed from all access areas.");
								showError(successString);
						}
						else{
								showError(jsonResponse.message);
						}
				});
		}
		else{
				return false;
		}
}