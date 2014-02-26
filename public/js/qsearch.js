/***************************************** 
 * qsearch.js
 * Script da pagina 'quick search'
 * Copyright (c) 2011, ThermInfo
 *****************************************/
// Production (root folder)
var uri = location.protocol + '//' + location.host + '/';

// Development (folder inside root)
//var uri = location.protocol + '//' + location.host + '/projects/mvc/';

/**
 * Valida o preenchimento do formulario
 * e carrega o conteudo da pesquisa.
 */
function qsearch_form_validation() {
	var search_term = $('#searchTerm').val();
	var search_type = $('#typeOfSearch').val();
	var smiles_threshold = $('#smilesThreshold').val();
	var inchi_layer = $('#inchiLayer').val();
	var code = $('#verCode').val();

	// Valida o termo da pesquisa e o codigo de seguranca
	if (search_term == '') {
		alert('You have not entered search details. Please go back and try again!');
		$('#searchTerm').focus();
	} else if (code == '') {
		alert('You have not entered the Security Code. Please go back and try again!');
		$('#verCode').focus();
	} else {
		var post = $.param({
			'vercode': code, 'searchtype': search_type,
			'searchterm': search_term, 'smilesthreshold': smiles_threshold,
			'inchiLayer': inchi_layer, 'ajax': 'y', 'submit': 'y'
		});
		// Efectua a Pesquisa
		$('#pageContentTextResult').html('<img src="'+uri+'public/media/images/search.gif" alt="Searching" /><p><strong>Searching...</strong></p>');
		$.ajax({
			type: 'POST',
			url: uri+'qsearch',
			data: post,
			cache: false,
			success: function(data) {
				$('#pageContentTextResult').hide().html(data).fadeIn('normal');
				$('#verCode').val(null);
			}
		}).fail(function(xhr, status) {
			$('#pageContentTextResult').hide().html('<p class="errorText"><strong>For some reason, the result can not be displayed!</strong><br />Error: '+xhr.statusText+'</p>').fadeIn('normal');
		}).done(function(html) {
			$('#captchaImg').attr('src', function() {
				return $(this).attr('src');
			});
		});
	}
	return false;
}

/**
 * Mostra um elemento.
 */
function show_rule(option) {
	if (option == 'name') {
		$('#nameRule').show();
	}
	if (option == 'formula') {
		$('#formRule').show();
	}
	if (option == 'thermId') {
		$('#idFormat').show();
	}
	if (option == 'casrn') {
		$('#casrnFormat').show();
	}
	if (option == 'smiles') {
		$('#smilesFormat').show();
		$('#smilesPane').show();
	}
	if (option == 'inchi') {
        $('#inchiFormat').show();
        $('#inchiPane').show();
    }
}

/**
 * Esconde todos os elementos.
 */
function hide_rules() {
	$('#nameRule').hide();
	$('#formRule').hide();
	$('#idFormat').hide();
	$('#casrnFormat').hide();
	$('#smilesFormat').hide();
	$('#smilesPane').hide();
	$('#inchiFormat').hide();
	$('#inchiPane').hide();
}

$(function() {
	// Esconder as informacoes
	hide_rules();
	show_rule('name');
	
	// Alterar o tipo de pesquisa
	$('#typeOfSearch').change(function() {
		hide_rules();
		show_rule($(this).val());
	});
	
	// Submeter o formulario
	$('#tForm').submit(function() {
		return qsearch_form_validation();
	});
});