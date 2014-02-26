<!DOCTYPE html>
<html lang="en" id="compound">
<head>
	<title>ThermInfo</title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<base href="<?php echo base_url();?>" />
	<link rel="shortcut icon" href="public/media/images/therminfo.ico" />
	<link rel="stylesheet" href="public/css/main.css" type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="public/css/pages/admin.css" type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="public/css/vendor/theme_1/jquery-ui.css" type="text/css" media="screen, projection" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" href="public/css/base/ie.css" type="text/css" media="screen, projection" />
	<![endif]-->
</head>
<body>
	<div id="pageTopBar">
		<div id="pageTopBarDate" class="smallText left textCenter"><?php echo date('l, F d, Y');?></div>
		<div id="pageTopBarLinks" class="right">
			<ul id="page-top-links" class="smallText">
				<li class="left">Hello <?php echo $name;?></li>
				<li class="left"><a id="user-pane-link" href="admin#admin-user-pane"><div class="ui-icon ui-icon-triangle-1-s">Open Box</div></a></li>
			</ul>
		</div>
	</div>
	<div id="admin-user-pane" class="bodyText">
		<div id="user-pane-img">
			<img src="public/media/images/user_face.png" alt="User Face" />
		</div>
		<div id="user-pane-text">
			<ul id="user-list-1">
				<li><strong>E-mail: </strong><?php echo $user_email;?></li>
				<li><strong>Institution: </strong><?php echo $user_inst;?></li>
			</ul>
			<ul id="user-list-2">
				<li><a class="lnk" href="change_pass" title="Change Password">Change Password</a></li>
				<li>
					<a class="btn white" href="logout/redirect/admin" title="Logout">Logout</a>
					<a class="btn white" href="main" title="Go Back">Go Back</a>
				</li>
			</ul>
		</div>
	</div>
	<div id="pageTop">
		<div id="pageTopLogo" class="left textCenter"><a class="img-link" href="main" title="Go to Therminfo Homepage"><img id="logo" src="public/media/images/logo.png" alt="Therminfo Logo" /></a></div>
		<h1 id="adminTitle" class="left">BackOffice</h1>
	</div>
	<div id="pageContent">
		<div id="pageContentMain" class="bodyText">
			<div id="tabs">
				<ul>
					<li><a href="#users" title="Users">Users</a></li>
					<li><a href="admin/add_data" title="insert">Insert New Data</a></li>
					<li><a href="#validate" title="Validate New Entry">Validate New Entry</a></li>
					<li><a href="#compounds" title="Compounds">Compounds</a></li>
					<li><a href="#properties" title="Properties">Properties</a></li>
					<li><a href="#references" title="References">References</a></li>
					<li><a href="#statistics" title="Database Statistics">Database Statistics</a></li>
					<li><a href="#news" title="News">News</a></li>
					<?php if ($user_type == 'superadmin'): ?>
					<li><a href="admin/db_control" title="control">Database Control</a></li>
					<?php endif; ?>
				</ul>
				<div id="users">
					<div class="admin-content-pane">
						<div class="admin-content-menu">
							<ul class="admin-menu">
								<li><a href="admin/new_users_management" target="users_main" title="New users requests">New Users</a></li>
								<li><a href="admin/users_management" target="users_main" title="Add, delete and update users">Users Management</a></li>
							</ul>
						</div>
						<div class="admin-content-main">
							<iframe name="users_main" style="width: 100%; height: 28em; border: 0 none;" src="admin/new_users_management" frameborder="0"></iframe>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div id="insert">
					<p class="textCenter"><img src="public/media/images/load.gif" alt="Progress bar" /></p>
				</div>
				<div id="validate">
					<div class="admin-content-pane">
						<div class="admin-content-menu">
							<ul class="admin-menu">
								<li><a href="admin/mol_validate" target="validate_main" title="Validate new moleule entry">Validate Molecules</a></li>
								<li><a href="admin/prop_value_validate" target="validate_main" title="Validate new property value entry">Validate Values</a></li>
							</ul>
						</div>
						<div class="admin-content-main">
							<iframe name="validate_main" style="width: 100%; height: 28em; border: 0 none;" src="admin/mol_validate" frameborder="0"></iframe>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div id="compounds">
					<div class="admin-content-pane">
						<div class="admin-content-menu">
							<ul class="admin-menu">
								<li><a href="admin/mols_management" target="compounds_main" title="Add, delete and update molecules">Molecules Management</a></li>
								<li><a href="admin/synonyms_management" target="compounds_main" title="Add, delete and update synonyms">Synonyms Management</a></li>
								<li><a href="admin/others_db_management" target="compounds_main" title="Add, delete and update others DB">Others DB Management</a></li>
								<li><a href="admin/chars_management" target="compounds_main" title="Add and update characteristics">Characteristics Management</a></li>
								<li><a href="admin/classes_management" target="compounds_main" title="Add and update classes">Classes Management</a></li>
								<li><a href="admin/subclasses_management" target="compounds_main" title="Add and update subclasses">Subclasses Management</a></li>
								<li><a href="admin/families_management" target="compounds_main" title="Add and update families">Families Management</a></li>
							</ul>
						</div>
						<div class="admin-content-main">
							<iframe name="compounds_main" style="width: 100%; height: 28em; border: 0 none;" src="admin/mols_management" frameborder="0"></iframe>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div id="properties">
					<div class="admin-content-pane">
						<div class="admin-content-menu">
							<ul class="admin-menu">
								<li><a href="admin/props_vals_management" target="properties_main" title="Add, delete and update values">Values Management</a></li>
								<li><a href="admin/props_management" target="properties_main" title="Add and update properties">Properties Management</a></li>
							</ul>
						</div>
						<div class="admin-content-main">
							<iframe name="properties_main" style="width: 100%; height: 28em; border: 0 none;" src="admin/props_vals_management" frameborder="0"></iframe>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div id="references">
					<div class="admin-content-pane">
						<div class="admin-content-menu">
							<ul class="admin-menu">
								<li><a href="admin/ref_management" target="references_main" title="Add, delete and update references">References Management</a></li>
								<li><a href="admin/authors_management" target="references_main" title="Add and update authors">Authors Management</a></li>
							</ul>
						</div>
						<div class="admin-content-main">
							<iframe name="references_main" style="width: 100%; height: 28em; border: 0 none;" src="admin/ref_management" frameborder="0"></iframe>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div id="statistics">
					Database Statistics
				</div>
				<div id="news">
					<div class="admin-content-main">
						<iframe name="news_main" style="width: 100%; height: 28em; border: 0 none;" src="admin/news_management" frameborder="0"></iframe>
					</div>
					<div class="clear"></div>
				</div>
				<?php if ($user_type == 'superadmin'): ?>
				<div id="control">
					<p class="textCenter"><img src="public/media/images/load.gif" alt="Progress bar" /></p>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div id="pageFooter" class="clear textCenter smallText">
		<p>Therminfo &nbsp;&middot;&nbsp; Copyright &#169; 2009 - <?php echo date('Y');?> &nbsp;&middot;&nbsp; LaSIGE - XLDB</p>
	</div>
	<script type="text/javascript" src="public/js/vendor/jquery.min.js"></script>
	<script type="text/javascript" src="public/js/vendor/plugins/jquery.cookie.js"></script>
	<script type="text/javascript" src="public/js/vendor/jquery-ui.custom.min.js"></script>
	<script type="text/javascript" src="public/js/admin.js"></script>
</body>
</html>