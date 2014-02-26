<?php
// Dados para o cabecalho
$item_1 = '<a href="administration/admin_main" title="Back to main area"><i class="icon-arrow-left-3 fg-darker on-left"></i></a>Backoffice: <small>Properties/Values Management</small>';
$item_2 = '<a class="element" href="administration/admin_main" title="Main area">Main</a>'.
		  '<a class="element" href="administration/admin_properties/props_management" target="properties_main" title="All properties">View All Properties</a>'.
          '<a class="element" href="administration/admin_properties/props_vals_management" target="properties_main" title="All properties values">View All Properties Values</a>';
$data = array(
		'title' => $item_1,
        'menu_items' => $item_2,
        'logout_url' => 'administration/admin_properties',
		'css_files' => array('public/css/pages/admin.css'),
		'js_files' => array());

$this->load->view('layout/admin_header', $data);
?>
        
        <iframe name="properties_main" src="administration/admin_properties/props_management" frameborder="0" style="width:100%; height:600px; border:0 none;"></iframe>
<?php $this->load->view('layout/admin_footer'); ?>