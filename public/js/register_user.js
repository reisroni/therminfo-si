/***************************************** 
 * register_user.js
 * Script da pagina 'Register'
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
function register_form_validation() {
	var result = true;
	var user_fname = $('#register-fname').val() == 'none' ? '' : $('#register-fname').val();
    var user_lname = $('#register-lname').val() == 'none' ? '' : $('#register-lname').val();
	var user_email = $('#register-email').val() == 'none' ? '' : $('#register-email').val();
	var user_inst = $('#register-institution').val() == 'none' ? '' : $('#register-institution').val();
	var code = $('#verCode').val() == 'none' ? '' : $('#verCode').val();

	// Verifica se os campos foram preenchidos
	if (user_fname == '') {
		alert('You have not entered your first name. Please go back and try again!');
		$('#register-fname').focus();
		result = false;
    } else if (user_lname == '') {
		alert('You have not entered your last name. Please go back and try again!');
		$('#register-lname').focus();
		result = false;
	} else if (user_email == '') {
		alert('You have not entered your e-mail. Please go back and try again!');
		$('#register-email').focus();
		result = false;
	} else if (user_inst == '') {
		alert('You have not entered your institution. Please go back and try again!');
		$('#register-institution').focus();
		result = false;
	} else if (code == '') {
		alert('You have not entered the Security Code. Please go back and try again!');
		$('#verCode').focus();
		result = false;
	} else if (! check_email(user_email)) {
		alert('The e-mail address is not valid. Please go back and try again! '+
		'Make sure your e-mail adress has the format: xxx@(at)xxx.(dot)xxx');
		$('#register-email').focus();
		result = false;
	}
	return result;
}

$(function() {
	$('#tForm').submit(function(){ 
		return register_form_validation();
	});
});