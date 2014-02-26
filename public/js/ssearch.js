/***************************************** 
 * ssearch.js
 * Script da pagina 'structural search'
 * Copyright (c) 2011, ThermInfo
 *****************************************/
 
/**
 * Valida o preenchimento do formulario
 */
function struc_form_validation() {
	var result = true;
	var search_smiles = $('#jchem-p')[0].getSmiles();
	
	if (search_smiles == '') {
		alert('You have not entered a structure. Please go back and try again!');
		result = false;
	}
	return result;
}

/**
 * Envia o SMILES e o ficheiro MOL 
 * da applet
 */
function write_smi_mol() {
	$('#h-smiles').val($('#jchem-p')[0].getSmiles());
	$('#h-smiles-ch').val($('#jchem-p')[0].getSmilesChiral());
	$('#h-molfile').val($('#jchem-p')[0].getMolFile());
}

/**
 * Carrega um SMILES na applet
 */
function load_smiles(smiles) {
	try {
		$('#jchem-p')[0].loadModelFromSmiles(smiles);
	} catch(err) {
		alert(err.message);
	}
}

/**
 * Limpa a applet
 */
function clear_applet() {
	$('#jchem-p')[0].clear();
}

/**
 * Abre uma janela
 */
function open_window(url) {
	window.open(url, 'popup_window', 'width=400,height=400,scrollbars=1,resizable=1,toolbar=0');
	return false;
}

$(function() {
	var clear;
	
	// Atribuir os eventos aos botoes
	$('#load-btn').click(function(){
		load_smiles($('#smiles-input').val());
	});
	$('#clear-btn').click(function(){
		clear = confirm('Do you really want to clear the structure?');
		if (clear) {
			clear_applet();
		}
	});
	
	// Submeter o formulario
	$('#tForm').submit(function() {
		write_smi_mol();
		return struc_form_validation();
	});
});