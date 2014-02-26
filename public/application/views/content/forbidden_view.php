<?php
$data = array(
		'page' => NULL,
		'title' => 'Forbidden Area!',
		'css_files' => array(),
		'js_files' => array());

$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<div id="pageContentTextPanel" class=" bodyText center">
		<h2><span class="orangeText">ERROR:</span> Authorization Required</h2><br />
		<p>This server could not verify that you are authorized to access the document 
		requested. Either you supplied the wrong credentials (e.g. bad password), or your 
		are not a ThermInfo Administrator. If you know your credentials are correct but you 
		are still receiving an error, please <?php echo anchor('contact/', 'contact us', 'title="Contact us"'); ?>.</p>
	</div>
</div>
<?php $this->load->view('layout/footer'); ?>