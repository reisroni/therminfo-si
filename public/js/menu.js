/***************************************** 
 * menu.js
 * Script das funcoes do menu
 * Copyright (c) 2011, ThermInfo
 *****************************************/

function hideMenu(x) {
	if (x != null && x != '') {
		document.getElementById(x).style.display = 'none';
	}
}
function showMenu(x) {
	if (x != null && x != '') {
		if (document.getElementById(x).style.display == 'block') {
			hideMenu(x);
		} else {
			document.getElementById(x).style.display = 'block';
		}
	}
}
function makeCurrent(x) {
	if (x != null && x != '') {
		document.getElementById(x).attributes['class'].value = 'current';
	}
}