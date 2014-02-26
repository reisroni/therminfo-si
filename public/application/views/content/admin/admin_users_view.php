<?php
// Dados para o cabecalho
$item_1 = '<a href="administration/admin_main" title="Back to main area"><i class="icon-arrow-left-3 fg-darker on-left"></i></a>Backoffice: <small>Users Management</small>';
$item_2 = '<a class="element" href="administration/admin_main" title="Main area">Main</a>'.
          '<a class="element" href="administration/admin_users/new_users_management" target="users_main" title="Validate new users">Validate New Users</a>'.
          '<a class="element" href="administration/admin_users/users_management" target="users_main" title="All users">View All Users</a>'.
          '<a class="element" href="administration/admin_users/outdated_users" target="users_main" title="Removed users">Removed Users</a>';
$data = array(
		'title' => $item_1,
        'menu_items' => $item_2,
        'logout_url' => 'administration/admin_users',
		'css_files' => array('public/css/pages/admin.css'),
		'js_files' => array());

$this->load->view('layout/admin_header', $data);
?>
        
        <iframe name="users_main" src="administration/admin_users/new_users_management" frameborder="0" style="width:100%; height:600px; border:0 none;"></iframe>
<?php $this->load->view('layout/admin_footer'); ?>