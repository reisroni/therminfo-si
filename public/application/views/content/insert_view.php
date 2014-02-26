<?php
$data = array(
		'page' => 'insert_menu',
		'title' => 'Insert Data!',
		'css_files' => array(),
		'js_files' => array());

$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<div id="pageContentTextPanel" class="center">
		<h2 class="orangeText textCenter">... Soon ...</h2>
		<p>Hello <?php echo $name;?> - <?php echo anchor('logout/redirect/insert', 'Logout', 'title="Logout"');?></p>
	</div>
</div>
<?php $this->load->view('layout/footer'); ?>