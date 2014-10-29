function sxValidate(email) {
	return email.match('[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}');
}

function sxStore(value) {

	jQuery( 'input[name=sx_email]' ).css("border","1px solid green");

	var data = {
		action: 'sx_optin',
		sx_ajax_email: value
	};

	// Ajax call to send value to php handler
	jQuery.post(ajaxurl, data, function(response) {
		//Refresh Page
		location.reload();
	});
}

jQuery( document ).ready(function($) {

	//User clicked accept
	$( '#sx_accept' ).on('click',function(e) {
		e.preventDefault();

		//Get email
		var email = $( 'input[name=sx_email]' ).val();
		//Validate email here
		var valid = sxValidate(email);

		if (valid) {
			//Store email value
			sxStore(email);
		}
		else {
			//Error invalid address
			$( 'input[name=sx_email]' ).css("border","1px solid red");
		}
	});

	$( '#sx_decline' ).on('click',function(e) {
		e.preventDefault();

		var value = 'no';
		//Store User Preference
		sxStore(value);
	});
});
