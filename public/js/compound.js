/***************************************** 
 * compound.js
 * Script da pagina 'compound'
 * Copyright (c) 2011, ThermInfo
 *****************************************/
 
$(function() {
	// Sortable
	$( ".column" ).sortable({
		handle: ".portlet-header",
		revert: true,
		opacity: 0.6
	});
	// Add class header, icon and widget
	$( ".portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
		.find( ".portlet-header" )
			.addClass( "ui-widget-header ui-corner-all" )
			.prepend( "<span class='ui-icon ui-icon-minusthick'></span>")
			.end()
		.find( ".portlet-content" );
	// Icon click function
	$( ".portlet-header .ui-icon" ).click(function() {
		$( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
		$( this ).parents( ".portlet:first" ).find( ".portlet-content" ).toggle();
	});
	// hover states on the static widgets
	$('.ui-widget-header').hover(
		function() { $(this).addClass('ui-state-hover'); }, 
		function() { $(this).removeClass('ui-state-hover'); }
	);
});