/***************************************** 
 * change_pass.js
 * Script da pagina 'Change Password'
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
function change_form_validation() {
	var result = true;
	var user_email = $('#change-email').val() == 'none' ? '' : $('#change-email').val();
	var user_pass = $('#change-old-pass').val() == 'none' ? '' : $('#change-old-pass').val();
	var user_new_pass1 = $('#change-new-pass-1').val() == 'none' ? '' : $('#change-new-pass-1').val();
	var user_new_pass2 = $('#change-new-pass-2').val() == 'none' ? '' : $('#change-new-pass-2').val();
	var code = $('#verCode').val() == 'none' ? '' : $('#verCode').val();

	// Verifica se os campos foram preenchidos
	if (user_email == '') {
		alert('You have not entered your e-mail. Please go back and try again!');
		$('#change-email').focus();
		result = false;
	} else if (user_pass == '') {
		alert('You have not entered your password. Please go back and try again!');
		$('#change-old-pass').focus();
		result = false;
	} else if (user_new_pass1 == '') {
		alert('You have not entered your new password. Please go back and try again!');
		$('#change-new-pass-1').focus();
		result = false;
	} else if (user_new_pass2 == '') {
		alert('You have not entered the new password confirmation. Please go back and try again!');
		$('#change-new-pass-2').focus();
		result = false;
	} else if (code == '') {
		alert('You have not entered the Security Code. Please go back and try again!');
		$('#verCode').focus();
		result = false;
	} else if (! check_email(user_email)) {
		alert('The e-mail address is not valid. Please go back and try again! '+
		'Make sure your e-mail adress has the format: xxx@(at)xxx.(dot)xxx');
		$('#change-email').focus();
		result = false;
	} else if (user_new_pass1 != user_new_pass2) {
		alert('New Passwords do not match. Please go back and try again!');
		result = false;
	}
	return result;
}

$(function() {
	$('#tForm').submit(function(){ 
		return change_form_validation();
	});
});