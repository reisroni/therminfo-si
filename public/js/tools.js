/***************************************** 
 * tools.js
 * Script da pagina 'Tools'
 * Copyright (c) 2011, ThermInfo
 *****************************************/
// Production (root folder)
var uri = location.protocol + '//' + location.host + '/';

// Development (folder inside root)
//var uri = location.protocol + '//' + location.host + '/projects/mvc/';

/**
 * Valida o preenchimento do formulario
 * e carrega o resultado
 */
function mw_form_validation() {
	var term = $('#mw_term').val() == 'none' ? '' : $('#mw_term').val();
	var term_type = $('#termType').val() == 'none' ? '' : $('#termType').val();

	// Verifica se o campo foi preenchido
	if (term == '') {
		alert('You have not insert a term. Please go back and try again!');
		$('#mw_term').focus();
	} else {
		// Efectua a calculo
		$("#pageContentTextResult").html('<img src="'+uri+'public/media/images/load.gif" alt="Searching" /><p class="normalText"><strong>Processing...</strong></p>');
		$.ajax({
			type: "POST",
			url: uri+"tools/mw",
			data: "term="+term+"&termtype="+term_type+"&ajax=y&submit=y",
			cache: false,
			success: function(data) {
				$("#pageContentTextResult").hide().html(data).fadeIn("normal");
			}
		}).fail(function(xhr, status) {
			$("#pageContentTextResult").hide().html('<p class="errorText"><strong>For some reason, the result can not be displayed!</strong><br />Error: '+xhr.statusText+'</p>').fadeIn("normal");
		});
	}
	return false;
}

$(function() {
	// Esconder a informacao no inicio
	if ($('#termType').val() != 'formula') {
		$('#formRule').hide();
	}
	// Mostrar ou esconder a informação
	$('#termType').change(function() {
		if ($(this).val() == 'formula') {
			$('#formRule').show();
		} else {
			$('#formRule').hide();
		}
	});
	// Submeter o formulario
	$('#tForm').submit(function(){
		return mw_form_validation();
	});
});