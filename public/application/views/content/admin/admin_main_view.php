<?php
// Dados para o cabecalho
$item_1 = 'Backoffice: <small>Main</small>';
$data = array(
		'title' => $item_1,
        'menu_items' => NULL,
        'logout_url' => 'administration/admin_main',
		'css_files' => array('public/css/pages/admin.css'),
		'js_files' => array());

$this->load->view('layout/admin_header', $data);
?>
        <div class="grid fluid">
            <div class="row">
                <div class="span12">
                    <div style="margin: 0 auto; width: 93%">
                    <a href="administration/admin_users" title="Users Management">
                        <div class="tile double bg-amber">
                            <div class="tile-content icon">
                                <i class="icon-user"></i>
                            </div>
                            <div class="tile-status">
                                <span class="name">Users</span>
                            </div>
                        </div>
                    </a>
					<a href="administration/admin_insert_data" title="Insert New Compounds/Values">
						<div class="tile double bg-cyan">
							<div class="tile-content icon">
								<i class="icon-plus"></i>
							</div>
							<div class="tile-status">
								<span class="name">Insert New Compounds/Values</span>
							</div>
						</div>
					</a>
                    <a href="administration/admin_validate_data" title="Validate New Compounds/Values">
                        <div class="tile double bg-amber">
                            <div class="tile-content icon">
                                <i class="icon-checkmark"></i>
                            </div>
                            <div class="tile-status">
                                <span class="name">Validate New Compounds/Values</span>
                            </div>
                        </div>
                    </a>
                    <a href="administration/admin_compounds" title="Compounds Management">
                        <div class="tile double bg-cyan">
                            <div class="tile-content icon">
                                <i class="icon-lab"></i>
                            </div>
                            <div class="tile-status">
                                <span class="name">Compounds</span>
                            </div>
                        </div>
                    </a>
                    <a href="administration/admin_properties" title="Properties/Values Management">
                        <div class="tile double bg-cyan">
                            <div class="tile-content icon">
                                <i class="icon-list"></i>
                            </div>
                            <div class="tile-status">
                                <span class="name">Properties/Values</span>
                            </div>
                        </div>
                    </a>
					<a href="administration/admin_references" title="References Management">
						<div class="tile double bg-amber">
							<div class="tile-content icon">
								<i class="icon-book"></i>
							</div>
							<div class="tile-status">
								<span class="name">References</span>
							</div>
						</div>
					</a>
                    <a href="administration/admin_news" title="News Management">
	                    <div class="tile double bg-cyan">
	                        <div class="tile-content icon">
	                            <i class="icon-newspaper"></i>
	                        </div>
	                        <div class="tile-status">
	                            <span class="name">News</span>
	                        </div>
	                    </div>
                    </a>
					<a href="administration/admin_db_statistics" title="Database Statistics">
						<div class="tile double bg-amber">
							<div class="tile-content icon">
								<i class="icon-stats-up"></i>
							</div>
							<div class="tile-status">
								<span class="name">Database Statistics</span>
							</div>
						</div>
					</a>
                    <?php if ($user_type == 'superadmin'): ?>
                    <a href="administration/admin_db_control" title="Database Control">
	                    <div class="tile double bg-amber">
	                        <div class="tile-content icon">
	                            <i class="icon-database"></i>
	                        </div>
	                        <div class="tile-status">
	                            <span class="name">Database Control</span>
	                        </div>
	                    </div>
                    </a>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
<?php $this->load->view('layout/admin_footer'); ?>