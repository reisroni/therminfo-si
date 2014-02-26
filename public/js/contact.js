/***************************************** 
 * contact.js
 * Script da pagina 'Contact us'
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
function contact_form_validation() {
	var result = true;
	var name = $('#contact-name').val() == 'none' ? '' : $('#contact-name').val();
	var email = $('#contact-email').val() == 'none' ? '' : $('#contact-email').val();
	var subject = $('#contact-subject').val() == 'none' ? '' : $('#contact-subject').val();
	var msg = $('#contact-msg').val() == 'none' ? '' : $('#contact-msg').val();
	var code = $('#verCode').val() == 'none' ? '' : $('#verCode').val();

	// Verifica se os campos foram preenchidos
	if (name == '') {
		alert('You have not entered your name. Please go back and try again!');
		$('#contact-name').focus();
		result = false;
	} else if (email == '') {
		alert('You have not entered your e-mail. Please go back and try again!');
		$('#contact-email').focus();
		result = false;
	} else if (! check_email(email)) {
		alert('The e-mail address is not valid. Please go back and try again! '+
		'Make sure your e-mail adress has the format: xxx@(at)xxx.(dot)xxx');
		$('#contact-email').focus();
		result = false;
	} else if (subject == '') {
		alert('You have not chosen a subject. Please go back and try again!');
		$('#contact-subject').focus();
		result = false;
	} else if (msg == '') {
		alert('You have not entered the message. Please go back and try again!');
		$('#contact-msg').focus();
		result = false;
	} else if (code == '') {
		alert('You have not entered the Security Code. Please go back and try again!');
		$('#verCode').focus();
		result = false;
	}
	return result;
}

$(function() {
	// Submete o formulario
	$('#tForm').submit(function(){ 
		return contact_form_validation();
	});
	// Confirma se quer apagar o formulario
	$('input[type="reset"]').click(function() {
		return confirm('Do you really want to clear the form?');
	});
});