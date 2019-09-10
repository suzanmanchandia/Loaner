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