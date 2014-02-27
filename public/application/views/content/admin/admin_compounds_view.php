<?php
// Dados para o cabecalho
$item_1 = '<a href="administration/admin_main" title="Back to main area"><i class="icon-arrow-left-3 fg-darker on-left"></i></a>Backoffice: <small>Compounds Management</small>';
$item_2 = '<a class="element" href="administration/admin_main" title="Main area">Main</a>'.
          '<div class="element"><a class="dropdown-toggle" href="/" title="Compounds menu">Compounds</a><ul class="dropdown-menu inverse" data-role="dropdown">'.
		  '<li><a href="administration/admin_compounds/mols_management" target="compounds_main" title="All Molecules">All Molecules</a></li>'.
          '<li><a href="administration/admin_compounds/mols_management" target="compounds_main" title="Add Molecules">Add Molecules</a></li>'.
          '<li><a href="administration/admin_compounds/synonym_management" target="compounds_main" title="All Molecules Synonym">Molecules Synonym</a></li>'.
          '<li><a href="administration/admin_compounds/others_db_management" target="compounds_main" title="All Molecules Others DB">Molecules Others DB</a></li></ul></div>'.
          '<div class="element"><a class="dropdown-toggle" href="/" title="Compounds Classification">Classification</a><ul class="dropdown-menu inverse" data-role="dropdown">'.
          '<li><a href="administration/admin_compounds/class_management" target="compounds_main" title="All Class">Class</a></li>'.
          '<li><a href="administration/admin_compounds/subclass_management" target="compounds_main" title="All Subclass">Subclass</a></li>'.
          '<li><a href="administration/admin_compounds/family_management" target="compounds_main" title="All Family">Family</a></li>'.
          '<li><a href="administration/admin_compounds/chars_management" target="compounds_main" title="All Characteristic">Characteristic</a></li></ul></div>';
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