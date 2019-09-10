	var messageDelay = 2000;  // How long to display status messages (in milliseconds)	
	 $("div#showDue").show();
	 
	 $('#cancel').click( function() { 
		 $.fancybox.close();
		 });	 
	
	$("#attach").click(function(){
	
		$("#email-content").append('<br /><br />~~-');
		$("#email-content").html($("#email-content").html()+$("div#dueDetails").html());
		
	});
	
	function submitForm() {
	  $("#message").val($("#email-content").html());
	  
	 if ( !$('#senderEmail').val() || !$('#receiverEmail').val() || !$('#subject').val()) {

  	  // No; display a warning message and return to the form
   	 $('#incompleteMessage').fadeIn().delay(messageDelay).fadeOut();
     $("#contactForm").fadeOut().delay(messageDelay).fadeIn();

  	} else {

    // Yes; submit the form to the PHP script via Ajax

    $('#sendingMessage').fadeIn();
     $("#contactForm").fadeOut();

	  
  	  $.ajax ({
      url: 'processForm.php',
      type: 'POST',
      data: $("#contactForm").serialize(),
      success: submitFinished
    });
  }
  }


// Handle the Ajax response

function submitFinished( response ) {
  response = $.trim( response );
   $('#sendingMessage').fadeOut();

  if ( response == "success" ) {

	  $('#successMessage').fadeIn().delay(messageDelay).fadeOut();
	  setTimeout("$.fancybox.close()", 2000);
	  
   	 $('#receiverEmail').val( "" );
   	 $('#senderEmail').val( "" );
   	 $('#message').val( "" );
     $('#subject').val( "" );
    
  } else {
  
	  $('#failureMessage').fadeIn().delay(messageDelay).fadeOut();
	  setTimeout("$.fancybox.close()", 2000);

  }
}