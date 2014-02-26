/***************************************** 
 * reset_pass.js
 * Script da pagina 'Reset Password'
 * Copyright (c) 2012, ThermInfo
 *****************************************/
 
 /**
  * Verifica um e-mail
  */
function check_email(email) {
	var er = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
	var result = false;
	
	if (typeof(email) == 'string') {
		if (er.test(email)) { 
			result = true; 
		}
	} else if (typeof(email) == 'object') {
		if (er.test(email.value)) { 
			result = true; 
		}
	}
	return result
}

/**
 * Valida o preenchimento do formulario
 */
function reset_form_validation() {
	var result = true;
	var user_email = $('#reset-email').val() == 'none' ? '' : $('#reset-email').val();
	var code = $('#verCode').val() == 'none' ? '' : $('#verCode').val();

	// Verifica se os campos foram preenchidos
	if (user_email == '') {
		alert('You have not entered your e-mail. Please go back and try again!');
		$('#reset-email').focus();
		result = false;
	} else if (code == '') {
		alert('You have not entered the Security Code. Please go back and try again!');
		$('#verCode').focus();
		result = false;
	} else if (! check_email(user_email)) {
		alert('The e-mail address is not valid. Please go back and try again! '+
		'Make sure your e-mail adress has the format: xxx@(at)xxx.(dot)xxx');
		$('#reset-email').focus();
		result = false;
	}
	return result;
}

$(function() {
	$('#tForm').submit(function(){ 
		return reset_form_validation();
	});
});