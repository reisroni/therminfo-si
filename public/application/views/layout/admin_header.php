<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="ISO-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="Backoffice for ThermInfo 2.0."> 
	<title>ThermInfo Administration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?php echo base_url();?>">
	<link rel="shortcut icon" href="public/media/images/therminfo.ico">
    
    <link rel="stylesheet" href="public/css/vendor/metro-bootstrap.css">
    <link rel="stylesheet" href="public/css/vendor/metro-bootstrap-responsive.css">
	<!-- <link rel="stylesheet" href="public/css/main.css"> -->
	<!--[if lt IE 8]>
	<link rel="stylesheet" href="public/css/base/ie.css">
	<![endif]-->
	<?php foreach($css_files as $style): ?>
	<link rel="stylesheet" href="<?php echo $style; ?>">
	<?php endforeach; ?>
    <script src="public/js/vendor/modernizr.min.js"></script>
</head>
<body class="metro">
    <div class="page-wrapper">
    <a id="skip-link" href="<?php echo uri_string();?>#section-main" title="Skip link">skip to main content</a>
    <header class="fixed-header">
        <nav class="navigation-bar fixed-top shadow">
            <div class="navigation-bar-content container">
                <a class="element" href="<?php echo base_url('main');?>" title="Home"><i class="icon-home"></i> ThermInfo</a>
                <span class="element-divider"></span>
                <?php echo $menu_items;?>
                <div class="element place-right">
                    <span><?php echo $user_email;?></span>
                    <a class="dropdown-toggle" href="/" title="Options">
                        <span class="icon-cog"></span>
                    </a>
                    <ul class="dropdown-menu place-right inverse" data-role="dropdown">
                        <li><a href="change_pass" title="Change Password">Change Password</a></li>
                        <li><a href="<?php echo base_url('main');?>" title="ThermInfo home">Home <i class="icon-home"></i></a></li>
                        <li><a href="logout/redirect/<?php echo $logout_url;?>" title="Logout">Logout <i class="icon-exit"></i></a></li>
                    </ul>
                </div>
                <span class="element place-right">Hello <?php echo $user_name;?></span>
                <span class="element-divider place-right"></span>
            </div>
        </nav>
    </header>
    <section id="section-main" class="container">
        <header>
            <h1><?php echo $title;?></h1>
            <!--[if lt IE 9]>
            <p class="text-center">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/" title="Browse Happy">upgrade your browser</a> to improve your experience.</p>
            <![endif]-->
        </header>