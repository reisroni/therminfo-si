/***************************************** 
 * admin_del.js
 * Script da pagina 'Adminstration (New Data)'
 * Copyright (c) 2012, ThermInfo
 *****************************************/
// Production (root folder)
//var uri = location.protocol + '//' + location.host + '/';

// Development (folder inside root)
var uri = location.protocol + '//' + location.host + '/projects/mvc/';

$(function() {
	$('#delete-link').on('click', function(e) {
		e.preventDefault();
		del = confirm('Do you really want to delete the file?');
		if (del) {
			var file_path = $('#file-path').val();
			$('#a-file-list').append('<span class="up_img"><img src="'+uri+'public/media/images/load_1.gif" alt="loading"> Deleting...</span>');
			$.post(uri+'administration/admin_insert_data/delete_file', {'delete': 'y', 'path': file_path},
				function(response) {
					if (response.status == 'ok') {
						$('#a-file-list').fadeOut('normal', function(){
							$(this).html(null);
						});
						$('input[type="file"][name="input-file"]').val(null);
					} else {
						alert('File not deleted!');
						$('.up_img').remove();
					}
				}, 'json'
			).error(function() { alert('Failure'); $('.up_img').remove(); });
		}
		
		return false;
	});
});