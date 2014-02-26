/***************************************** 
 * asearch.js
 * Script da pagina 'Advanced search'
 * Copyright (c) 2011, ThermInfo
 *****************************************/
// Production (root folder)
var uri = location.protocol + '//' + location.host + '/';

// Development (folder inside root)
//var uri = location.protocol + '//' + location.host + '/projects/mvc/';

/**
 * Valida o preenchimento do formulario
 * e carrega o conteudo da pesquisa
 */
function asearch_form_validation() {
	var compound_name = $("#compound").val();
	var state = $("#state").val();
	var mw_interval = $("#intervalMW").val();
	var mw = $("#mw").val();
	var formula = $("#formula").val();
	var smiles = $("#smiles").val();
	var smiles_threshold = $("#smilesThreshold").val();
	var classe = $("#class").val();
	var subclasse = $("#subclass").val();
	var family = $("#family").val();
	var ch = new Array();
	$("#tForm input:checked").each(function(){ ch.push($(this).val()); });
	var code = $("#verCode").val();
	
	// Valida os campos de pesquisa e o codigo de seguranca
	if (compound_name == "" && state == "all" && mw == "" && formula == "" && smiles == "" && classe == "all" && subclasse == "all" && family == "all" && ch.length == 0) {
		alert("You have not entered search details. Please go back and try again!");
		$("#compound").focus();	
	} else if (code == "") {
		alert("You have not entered the Security Code. Please go back and try again!");
		$("#verCode").focus();
	} else {
		// Efectua a pesquisa
		$("#pageContentTextResult").html('<img src="'+uri+'public/media/images/search.gif" alt="Searching" /><p><strong>Searching...</strong></p>');
		$.ajax({
			type: "POST",
			url: uri+"asearch",
			data: "vercode="+code+"&compound="+compound_name+"&state="+state+"&intervalmw="+mw_interval+"&mw="+mw+"&formula="+formula+"&smiles="+smiles+"&threshold="+smiles_threshold+"&classe="+classe+"&subclass="+subclasse+"&family="+family+"&ch="+ch+"&ajax=y&submit=y",
			cache: false,
			success: function(data) {
				$("#pageContentTextResult").hide().html(data).fadeIn("normal");
				$("#verCode").val(null);
			}
		}).fail(function(xhr, status) {
			$("#pageContentTextResult").hide().html('<p class="errorText"><strong>For some reason, the result can not be displayed!</strong><br />Error: '+xhr.statusText+'</p>').fadeIn("normal");
		}).done(function(html) {
			$("#captchaImg").attr("src", function() {
				return $(this).attr("src");
			});
		});
	}
	return false;
}

/**
 * Desactivar subclasses
 */
function make_disable(opclass) {
	var subclass = $("#subclass");

	if (opclass == "01 - Acyclic Compounds") {
		for (x = 6; x <= 12; x = x + 1) {
			subclass[0].options[x].disabled = true;
		}
	} else {
		for (x = 1; x <= 12; x = x + 1) {
			subclass[0].options[x].disabled = false;
		}
	}
}

/**
 * Esconder e mostrar as classes, familias
 * e caracteristicas
 */
function more_options() {
	$("#classList").hide();
	$("#classLegend").append('<span id="legendLink1">[more]</span>');
	$("#charList").hide();
	$("#charLegend").append('<span id="legendLink2">[more]</span>');
	
	// Mostrar e esconder as classes
	$("#legendLink1").click(function() {
		if ($(this).text() == "[more]") {
			$("#classList").slideDown("slow");
			$(this).text("[less]");
		}
		else if ($(this).text() == "[less]") {
			$("#classList").slideUp("slow");
			$(this).text("[more]");
		}
	});
	// Mostrar e esconder as caracteristicas
	$("#legendLink2").click(function() {
		if ($(this).text() == "[more]") {
			$("#charList").slideDown("slow");
			$(this).text("[less]");
		} 
		else if ($(this).text() == "[less]") {
			$("#charList").slideUp("slow");
			$(this).text("[more]");
		}
	});
}

$(function() {
	// Classes, Familias e Caracteristicas
	more_options();
	// desactivar algumas subclasses
	$("#class").change(function() {
		make_disable($(this).val());
	});
	// Submeter o formulario
	$("#tForm").submit(function() {
		return asearch_form_validation();
	});
});