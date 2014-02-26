/***************************************** 
 * admin.js
 * Script da pagina 'Adminstration'
 * Copyright (c) 2012, ThermInfo
 *****************************************/

$(function() {
	// Tabs
	$( "#tabs" ).tabs({ 
		cookie: { expires: 1}
	});
	
	// User box
	$('#user-pane-link').click(function(event) {
		$('#admin-user-pane').toggle();
		event.preventDefault ? event.preventDefault() : event.returnValue = false;
	});
	$(document).mouseup(function(event) {
		if (! $(event.target).is('#admin-user-pane,#user-pane-img img,#user-pane-text li,#user-pane-text a,#user-pane-text strong')) {
			$('#admin-user-pane').hide();
		}
	});
});