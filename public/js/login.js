/***************************************** 
 * login.js
 * Script da pagina 'structural search'
 * Copyright (c) 2011, ThermInfo
 *****************************************/
 
/**
 * Valida o preenchimento do formulario
 */
function validateForm() {
	var d = document.getElementById('tForm');
	var result = true;
	
	// Campo e-mail, password e codigo de seguranca
	if (d.email.value == '') {
		alert('You have not entered your e-mail. Please go back and try again!');
		d.email.focus();
		result = false;
	} else if (d.password.value == '') {
		alert('You have not entered your password. Please go back and try again!');
		d.password.focus();
		result = false;
	} else if (d.vercode.value == '') {
		alert('You have not entered the Security Code. Please go back and try again!');
		d.vercode.focus();
		result = false;
	} else {
		var AtPos = d.email.value.indexOf("@");
		var StopPos = d.email.value.lastIndexOf(".");
	
		// E-mail without at or dot - xxxx or the email start with @
		if (AtPos == -1 || StopPos == -1 || AtPos == 0) {
			alert('The e-mail address is not valid. Please go back and try again! '+
			'Make sure your e-mail adress has the format: xxx@(at)xxx.(dot)xxx');
			d.email.focus();
			result = false;
		}
		// E-mail without the dot in the end - xxxx@xxxx
		else if (StopPos < AtPos) {
			alert('The e-mail address is not valid. Please go back and try again! '+
			'Make sure your e-mail adress has the format: xxx@(at)xxx.(dot)xxx');
			d.email.focus();
			result = false;
		}
		// There is nothing between at and dot - xxxx@.xxxx
		else if (StopPos - AtPos == 1) {                                                                                         
			alert('The e-mail address is not valid. Please go back and try again! '+
			'Make sure your e-mail adress has the format: xxx@(at)xxx.(dot)xxx');
			d.email.focus();
			result = false;
		}
	}
	return result;
}