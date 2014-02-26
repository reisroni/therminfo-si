/***************************************** 
 * admin_add.js
 * Script da pagina 'Adminstration (New Data)'
 * Copyright (c) 2012, ThermInfo
 *****************************************/
// Production (root folder)
var uri = location.protocol + '//' + location.host + '/';

// Development (folder inside root)
//var uri = location.protocol + '//' + location.host + '/projects/mvc/';
 
/**
 * Valida os dados inseridos no formulario
 * via ajax.
 */
function validate_data() {
	var ref_id = $('#a-ref-select').val() == 'none' ? '' : $('#a-ref-select').val();
	var mol_id = $('input[type="radio"][name="mol-id"]:checked').val();
	var prop_id = $('#a-prop-select').val() == 'none' ? '' : $('#a-prop-select').val();
	var prop_num = $("#ref-prop-num:checked").val() == undefined ? true : false;
	var input_type = $('input[type="radio"][name="input_type"]:checked').val();
	var mols_value = $('#a-mols').val();
	var mols_file = $('#file-path').val();
	
	if (ref_id == '') {
		alert('You have not chosen a reference');
		$('#a-ref-select').focus();
	} else if (mol_id == undefined) {
		alert('You have not chosen a compound ID');
		$('#mol-id-1').focus();
	} else if (prop_id == '') {
		alert('You have not chosen a property');
		$('#a-prop-select').focus();
	} else if (input_type == undefined) {
		alert('You have not chosen the input type');
		$('#input-type-1').focus();
	} else if (input_type == 'box' && mols_value == '') {
		alert('You have not entered the values');
		$('#a-mols').focus();
	} else if (input_type == 'file' && mols_file == null) {
		alert('You have not upload a file');
		$('#a-mols-file').focus();
	} else {
		var post = $.param({
			'ref-id': ref_id, 'mol-id': mol_id,
			'prop-id': prop_id, 'mols': mols_value,
			'prop-num': prop_num, 'file': mols_file,
			'input-type': input_type, 'submit': 'y'
		});
		$.ajax({
			type: 'POST',
			url: uri+'admin/validate_data',
			data: post,
			cache: false,
			beforeSend: function(xhr) {
				$('#dialog').html('<div class="center a-loading"><img src="'+uri+'public/media/images/load.gif" alt="Progress bar" /><br />Processing...</div>');
				$('#dialog').dialog('open');
			},
			success: function(data) {
				$('#dialog').html('<div>'+data+'</div>');
			}
		}).fail(function(xhr, status) {
			$('#dialog').html('<p class="errorText textCenter"><strong>For some reason, there was a failure!</strong><br />Error: '+xhr.statusText+'</p>');
		});
	}
	return false;
}

/**
 * Adiciona os dados inseridos no formulario,
 * depois de validados via ajax.
 */
function add_data() {
	var ref_id = $('#a-ref-select').val() == 'none' ? '' : $('#a-ref-select').val();
	var mol_id = $('input[type="radio"][name="mol-id"]:checked').val();
	var prop_id = $('#a-prop-select').val() == 'none' ? '' : $('#a-prop-select').val();
	var input_type = $('input[type="radio"][name="input_type"]:checked').val();
	var mols = $('#a-mols').val();
	var mols_file = $('#file-path').val();
	
	if (ref_id == '') {
		alert('You have not chosen a reference');
		$('#a-ref-select').focus();
	} else if (mol_id == undefined) {
		alert('You have not chosen a compound ID');
		$('#mol-id-1').focus();
	} else if (prop_id == '') {
		alert('You have not chosen a property');
		$('#a-prop-select').focus();
	} else if (input_type == undefined) {
		alert('You have not chosen the input type');
		$('#input-type-1').focus();
	} else if (mols == '' && mols_file == null) {
		alert('You have not entered the values');
		if (input_type == 'box')
			$('#a-mols').focus();
		else
			$('#a-mols-file').focus();
	} else {
		$.ajax({
			type: 'POST',
			url: uri+'admin/add_data',
			data: 'submit=y',
			cache: false,
			beforeSend: function(xhr) {
				$('#dialog').html('<div class="center a-loading"><img src="'+uri+'public/media/images/load.gif" alt="Progress bar" /><br />Processing...</div>');
				$('#dialog').dialog('open');
			},
			success: function(data) {
				$('#dialog').html('<div>'+data+'</div>');
			}
		}).fail(function(xhr, status) {
			$('#dialog').html('<p class="errorText textCenter"><strong>For some reason, there was a failure!</strong><br />Error: '+xhr.statusText+'</p>');
		});
	}
	return false;
}

/**
 * Adiciona novo autor, via ajax.
 */
function add_author() {
	var name = $('#a-author-field').val();
	
	if (name == '') {
		alert('Insert a name');
		$('#a-author-field').focus();
	} else {
		$.ajax({
			type: 'POST',
			url: uri+'admin/add_author',
			data: 'a-ref-author='+name+'&submit=y',
			cache: false,
			success: function(data) {
				if (data == 1) {
					populate_auths();
					$('#a-author-field').val(null);
					$('#add-author').dialog('close');
					alert('Added a new author');
				} else {
					alert('Couldn\'t add a new author');
				}
			}
		}).fail(function(xhr, status) {
			alert('For some reason, there was a failure! Error: '+xhr.statusText);
		});
	}
}

/**
 * Adiciona nova referencia, via ajax.
 */
function add_ref() {
	var authors = new Array();
	$('#a-author-select option:selected').each(function(){authors.push($(this).val());});
	var title = $('#a-title-field').val();
	var type = $('#a-type-select').val();
	var book = $('#a-book-field').val(), vol = $('#a-volume-field').val();
	var editor = $('#a-editor-field').val(), pub = $('#a-publisher-field').val();
	var journal = $('#a-journal-field').val(), issue = $('#a-issue-field').val();
	var year = $('#a-year-field').val(), bpage = $('#a-bpage-field').val(), epage = $('#a-epage-field').val();
	
	if (authors.length == 0) {
		alert('You have not chosen a author');
		$('#a-author-select').focus();
	} else if (title == '') {
		alert('You have not entered a title');
		$('#a-title-field').focus();
	} else if (type == 'none') {
		alert('You have not chosen a reference type');
		$('#a-type-select').focus();
	} else if (year == '') {
		alert('You have not entered the year');
		$('#a-year-field').focus();
	} else {
		var postar = $.param({
			'a-ref-author-select': authors, 'a-ref-title': title, 
			'a-ref-type': type, 'a-ref-book': book, 'a-ref-volume': vol, 
			'a-ref-editor': editor, 'a-ref-publisher': pub, 'a-ref-journal': journal, 
			'a-ref-issue': issue, 'a-ref-year': year,
			'a-ref-bpage': bpage, 'a-ref-epage': epage, 'submit': 'y'
		});
		$.ajax({
			type: 'POST',
			url: uri+'admin/add_reference',
			data: postar,
			cache: false,
			beforeSend: function(xhr) {
				$('.ui-dialog-buttonset').prepend('<span class="ref-load"><img src="'+uri+'public/media/images/load_1.gif" alt="Progress bar" />&nbsp;</span>');
			},
			success: function(data) {
				populate_refs();
				$('#add-ref').dialog('close');
				$('#a-author-select').val(null); $('#a-title-field').val(null); $('#a-type-select').val(null);
				$('#a-book-field').val(null); $('#a-volume-field').val(null); $('#a-editor-field').val(null);
				$('#a-publisher-field').val(null); $('#a-journal-field').val(null); $('#a-issue-field').val(null);
				$('#a-year-field').val(null); $('#a-bpage-field').val(null); $('#a-epage-field').val(null);
				hide_fields('ref');
				$('.ref-load').remove();
				$('#msg').html(data).fadeIn('normal');
				$('#msg').delay(5000).fadeOut('slow');
			}
		}).fail(function(xhr, status) {
			$('#add-ref').dialog('close');
			$('.ref-load').remove();
			$('#msg').html('<p class="errorPane"><strong>For some reason, there was a failure!</strong><br />Error: '+xhr.statusText+'</p>').fadeIn('normal');
			$('#msg').delay(5000).fadeOut('slow');
		});
	}
}

/**
 * Preenche a 'drop-box' das referencias via ajax.
 */
function populate_refs() {
	// Referencias
	$.post(uri+'admin/get_lists', {list : 'refs'}, function(html) {
		$('#a-ref-select').empty().append(html);
	}).fail(function() {
		$('#a-ref-select').empty().append('<option value="none">Select a reference</option>');
	});
}

/**
 * Preenche a 'drop-box' das propriedades via ajax.
 */
function populate_props() {
	// Propriedades
	$.post(uri+'admin/get_lists', {list : 'props'}, function(html) {
		$('#a-prop-select').empty().append(html);
	}).fail(function() {
		$('#a-prop-select').empty().append('<option value="none">Select a property</option>');
	});
}

/**
 * Preenche a 'drop-box' dos autores via ajax.
 */
function populate_auths() {
	// Autores
	$.post(uri+'admin/get_lists', {list : 'auth'}, function(html) {
		$('#a-author-select').empty().append(html);
	}).error(function() {
		$('#a-author-select').empty().append('<option value="none">Select a property</option>');
	});
}

/**
 * Esconde alguns campos do formulario.
 */
function hide_fields(type) {
	if (type == 'ref') {
		$('#book-type-ref').hide();
		$('#paper-type-ref').hide();
		$('#other-type-ref').hide();
	}
	if (type == 'add') {
		$('#a-mols').hide();
		$('#a-file').hide();
		$('#a-file-list').hide();
	}
}

/**
 * Mostra alguns campos escondidos, 
 * do formulario.
 */
function show_fields(type) {
	if (type == 'box') {
		$('#a-mols').show();
	}
	if (type == 'file') {
		$('#a-file').show();
		$('#a-file-list').show();
	}
	if (type == 'book') {
		$('#book-type-ref').show();
		$('#other-type-ref').show();
	}
	if (type == 'paper') {
		$('#paper-type-ref').show();
		$('#other-type-ref').show();
	}
}

/**
 * Apresenta um janela de alerta.
 */
function add_alert(text) {
	$('body').append('<div id="alert" title="Alert"><p class="textCenter errorText">'+text+'</p></div>');
	$('#alert').dialog({
		width: 300,
		height: 100,
		modal: true,
		dialogClass: 'alert',
		close: function(event, ui) {
			$(this).dialog('destroy');
			$('#alert').remove();
		}
	});
}

$(function() {
	// Preenche as drop-box
	populate_refs();
	populate_props();
	
	// Upload de ficheiros
	$('#a-mols-file').upload({
		name: 'input-file',
		action: uri+'admin/upload_file',
		autoSubmit: true,
		params: {upload: 'y', type: 'json'},
		onSubmit: function() {
			$('#a-file-list').fadeIn('normal').html('<p><img src="'+uri+'public/media/images/load_1.gif" alt="loading"> Uploading...</p>');
		},
		onComplete: function(response) {
			data = $.parseJSON(response);
			if (data.status == 'ok') {
				$('#a-file-list').hide().html('<p>'+data.msg+' - <a id="delete-link" href="admin#delete-link">delete</a></p>'+
				'<input type="hidden" id="file-path" name="file_path" value="'+data.file+'"/>'+
				'<script type="text/javascript" src="public/js/admin_del.js"></script>').fadeIn('normal');
			} else {
				$('#a-file-list').html(null);
				alert(data.msg);
			}
		}
	});
	
	// Esconde os campos
	hide_fields('add');
	hide_fields('ref');
	
	// Define as janelas de dialago
	$('#add-ref').dialog({
		autoOpen: false,
		width: 500,
		height: 450,
		modal: true,
		buttons: { 
			'Insert': function() { 
				add_ref();
			},
			'Cancel': function() {
				populate_refs();
				$(this).dialog('close');
				$('#a-author-select').val(null); $('#a-title-field').val(null);
				$('#a-type-select').val(null); $('#a-book-field').val(null);
				$('#a-volume-field').val(null); $('#a-editor-field').val(null);
				$('#a-publisher-field').val(null); $('#a-journal-field').val(null);
				$('#a-issue-field').val(null); $('#a-year-field').val(null);
				$('#a-bpage-field').val(null); $('#a-epage-field').val(null);
				hide_fields('ref');
			}
		}
	});
	$('#add-author').dialog({
		autoOpen: false,
		width: 300,
		modal: true,
		buttons: { 
			'Insert': function() {
				add_author();
			},
			'Cancel': function() {
				populate_auths();
				$(this).dialog('close');
				$('#a-author-field').val(null);
			}
		}
	});
	$('#dialog').dialog({
		autoOpen: false,
		width: 800,
		height: 400,
		cache: false,
		modal: true,
		buttons: { 
			'Ok': function() { 
				$(this).dialog('close');
			}
		}
	});
	
	// Atribui os diversos eventos 'click'
	$('#add-ref-bt').click(function() {
		populate_auths();
		$('#add-ref').dialog('open');
		return false;
	});
	$('#validate_btn').click(function() {
		return validate_data();
	});
	$('#insert_btn').click(function() {
		return add_data();
	});
	$('#reset_btn').click(function() {
		clear = confirm('Do you really want to clear the form?');
		if (clear) {
			hide_fields('add');
		}
		return clear;
	});
	$('#add-auth-bt').click(function() {
		$('#add-author').dialog('open');
		return false;
	});
	
	// Altera o campo dos valores
	$('input[type="radio"][name="input_type"]').change(function() {
		hide_fields('add');
		show_fields($(this).val());
	});
	// Altera os campos do formulario das referencias
	$('#a-type-select').change(function() {
		hide_fields('ref');
		show_fields($(this).val());
	});
});