<?php
// Dados para o cabecalho
$item_1 = '<a href="administration/admin_main" title="Back to main area"><i class="icon-arrow-left-3 fg-darker on-left"></i></a>Backoffice: <small>Validate New Compounds/Values</small>';
$item_2 = '<a class="element" href="administration/admin_main" title="Main area">Main</a>'.
          '<a class="element" href="administration/admin_validate_data/validate_molecule" target="validate_main" title="Validate new compound">Validate New Compound</a>'.
          '<a class="element" href="administration/admin_validate_data/validate_prop_value" target="validate_main" title="Validate new value">Validate New Value</a>';
$data = array(
		'title' => $item_1,
        'menu_items' => $item_2,
        'logout_url' => 'administration/admin_validate_data',
		'css_files' => array('public/css/pages/admin.css'),
		'js_files' => array());

$this->load->view('layout/admin_header', $data);
?>
        
        <iframe name="validate_main" src="administration/admin_validate_data/validate_molecule" frameborder="0" style="width:100%; height:600px; border:0 none;"></iframe>
<?php $this->load->view('layout/admin_footer'); ?>