<?php
// Dados para o cabecalho
$item_1 = '<a href="administration/admin_main" title="Back to main area"><i class="icon-arrow-left-3 fg-darker on-left"></i></a>Backoffice: <small>Properties/Compounds Management</small>';
$item_2 = '<a class="element" href="administration/admin_main" title="Main area">Main</a>'.
		  '<a class="element" href="administration/admin_compounds/mols_management" target="compounds_main" title="All Compounds">All Compounds</a>'.
          '<a class="element" href="administration/admin_compounds/" target="compounds_main" title="Add Compounds">Add Compounds</a>'.
          '<a class="element" href="administration/admin_compounds/class_management" target="compounds_main" title="ALL Class">All Class</a>'.
          '<a class="element" href="administration/admin_compounds/subclass_management" target="compounds_main" title="ALL Subclass">All Subclass</a>'.
          '<a class="element" href="administration/admin_compounds/family_management" target="compounds_main" title="ALL Family">All Family</a>'.
          '<a class="element" href="administration/admin_compounds/chars_management" target="compounds_main" title="ALL Characteristic">All Characteristic</a>';
$data = array(
		'title' => $item_1,
        'menu_items' => $item_2,
        'logout_url' => 'administration/admin_compounds',
		'css_files' => array('public/css/pages/admin.css'),
		'js_files' => array());

$this->load->view('layout/admin_header', $data);
?>
        
        <iframe name="compounds_main" src="administration/admin_compounds/mols_management" frameborder="0" style="width:100%; height:600px; border:0 none;"></iframe>
<?php $this->load->view('layout/admin_footer'); ?>