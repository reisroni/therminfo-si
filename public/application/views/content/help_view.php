<?php
$data = array(
		'page' => 'help_menu',
		'title' => 'Help!',
		'css_files' => array('public/css/pages/news.css','public/css/vendor/theme_2/jquery-ui.css'),
		'js_files' => array('public/js/vendor/plugins/jquery.cookie.js','public/js/vendor/jquery-ui.custom.min.js','public/js/help.js'));

$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<div class="center bodyText">
		<div id="help-tabs">
			<ul>
				<li><a href="#help-1" title="Data Description">Data Description</a></li>
				<li><a href="#help-2" title="System Features">System Features</a></li>
				<li><a href="#help-3" title="Searching for Compounds">Searching</a></li>
				<li><a href="#help-4" title="Predicting Data">Predicting Data</a></li>
				<li><a href="#help-5" title="Tools">Tools</a></li>
				<li><a href="#help-6" title="Inserting New Compounds to the Database">Inserting New Compounds</a></li>
				<li><a href="#help-7" title="Browser Related Issues">Browser Related Issues</a></li>
			</ul>
			<div id="help-1" class="bodyText-2">
				<?php $this->load->view('content/help/help_1'); ?>
			</div>
			<div id="help-2">
				System Features
			</div>
			<div id="help-3">
				Searching
			</div>
			<div id="help-4">
				Predicting Data
			</div>
			<div id="help-5">
				Tools
			</div>
			<div id="help-6">
				Inserting New Compounds to the Database
			</div>
			<div id="help-7">
				Browser Related Issues
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('layout/footer'); ?>