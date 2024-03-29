<!DOCTYPE html>
<html lang="en">
<head>
	<title>ThermInfo: <?php echo $title;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<meta name="description" content="ThermInfo is a project aiming to develop a chemoinformatics database system for prediction of thermochemical properties.  It involves a partnership between the Molecular Energetics Group of CQB (Centro de Quimica e Bioquimica) and LaSIGE (Large-Scale Informatics Systems Laboratory). The chemistry team has considerable expertise on a variety of experimental thermochemical techniques, on assessing thermochemical data, and on the development of prediction methods. The informatics team has extensive experience in web systems development, in particular biomolecular databases. ThermInfo will develop an information system for collecting and presenting thermochemical properties obtained from critically evaluated experimental data and several estimation methods." /> 
	<meta name="keywords" content="ThermInfo, therminfo, Ana Teixeira, Roni Reis, chemistry, thermochemistry, thermochemical properties, organic compounds, enthalpy, information system, database, chemoinformatics, search engine, properties prediction, Lasige, xldb, cqb, search engine, structural data, similarity search, structural search" /> 
	<base href="<?php echo base_url();?>" />
	<link rel="shortcut icon" href="public/media/images/therminfo.ico" />
	<link rel="stylesheet" href="public/css/main.css" type="text/css" media="screen, projection" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" href="public/css/base/ie.css" type="text/css" media="screen, projection" />
	<![endif]-->
	<?php foreach($css_files as $style): ?>
	<link rel="stylesheet" href="<?php echo $style; ?>" type="text/css" media="screen, projection" />
	<?php endforeach; ?>
	<script type="text/javascript" src="public/js/vendor/jquery.min.js"></script>
	<script type="text/javascript" src="public/js/ganalytics.js"></script>
	<script type="text/javascript" src="public/js/menu.js"></script>
	<?php foreach($js_files as $script): ?>
	<script type="text/javascript" src="<?php echo $script; ?>"></script>
	<?php endforeach; ?>
</head>
<body onload="makeCurrent('<?php echo $page;?>')">
	<iframe id="fixApplet" name="frame_for_fix_applet" scrolling="no" frameborder="0"></iframe>
	<a id="skip-link" href="<?php echo uri_string();?>#pageContentMain" title="Skip link">skip to main content</a>
	<div id="pageTopBar">
		<div id="pageTopBarDate" class="smallText left textCenter"><?php echo date('l, F d, Y');?></div>
		<div id="pageTopBarLinks" class="right">
			<div class="left"><a href="https://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a><script type="text/javascript" src="//platform.twitter.com/widgets.js"></script></div>
		</div>
	</div>
	<div id="pageTop">
		<div id="pageTopLogo" class="left textCenter"><a class="img-link" href="main" title="Go to Therminfo Homepage"><img id="logo" src="public/media/images/logo.png" width="168" height="107" alt="Therminfo Logo" /></a></div>
		<h1 id="pageTopTitle" class="right titleText"><?php echo $title;?></h1>
	</div>
	<div id="pageContent">
		<div id="pageContentCanvas" class="clear">
			<div id="pageContentNav" class="left">
				<?php $this->load->view('layout/menu'); ?>
			</div>