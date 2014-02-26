<?php
// Dados para o cabecalho
$item_1 = '<a href="administration/admin_main" title="Back to main area"><i class="icon-arrow-left-3 fg-darker on-left"></i></a>Backoffice: <small>News Management</small>';
$item_2 = '<a class="element" href="administration/admin_main" title="Main area">Main</a>';
$data = array(
		'title' => $item_1,
        'menu_items' => $item_2,
        'logout_url' => 'administration/admin_news',
		'css_files' => array('public/css/pages/admin.css'),
		'js_files' => array());

$this->load->view('layout/admin_header', $data);
?>
        
        <iframe name="news_main" src="administration/admin_news/news_management" frameborder="0" style="width:100%; height:600px; border:0 none;"></iframe>
<?php $this->load->view('layout/admin_footer'); ?>