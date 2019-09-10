   var curVal = "";
   
    $("document").ready(function() {
			var headerNames = new Array('dashboard','user','kits','equipment','loans','reservations','system','reports','fines','help','curUser','logout');
			var i = 0;
			$("#dashboard").css("color","rgb(255,255,255)");
			for(i=0;i<headerNames.length;i++)
			{
				
					var id = "#"+headerNames[i];
					//alert(id);
					/*$(id).click(function() {
						alert($(id).css("color"));
						$(id).css("color","rgb(255,255,255)");
						alert($(id).css("color"));
						
					});*/
					
			}
			
    });