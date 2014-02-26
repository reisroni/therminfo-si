<?php
// Dados para o cabecalho
$item_1 = '<a href="administration/admin_main" title="Back to main area"><i class="icon-arrow-left-3 fg-darker on-left"></i></a>Backoffice: <small>Database Control</small>';
$item_2 = '<a class="element" href="administration/admin_main" title="Main area">Main</a>';
$data = array(
		'title' => $item_1,
        'menu_items' => $item_2,
        'logout_url' => 'administration/admin_db_control',
		'css_files' => array('public/css/pages/admin.css'),
		'js_files' => array());

$this->load->view('layout/admin_header', $data);
?>
		<h2>phpMyAdmin:</h2>
		<p>Administrate the database using phpMyAdmin - <a href="assets/phpmyadmin/index.php" title="Open phpMyAdmin" target="_blank">Here</a></p>
<?php $this->load->view('layout/admin_footer'); ?>