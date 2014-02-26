<!DOCTYPE html>
<html lang="en" id="compound">
<head>
	<title>ThermInfo: Compound Record</title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<meta name="description" content="ThermInfo is a project aiming to develop a chemoinformatics database system for prediction of thermochemical properties.  It involves a partnership between the Molecular Energetics Group of CQB (Centro de Quimica e Bioquimica) and LaSIGE (Large-Scale Informatics Systems Laboratory). The chemistry team has considerable expertise on a variety of experimental thermochemical techniques, on assessing thermochemical data, and on the development of prediction methods. The informatics team has extensive experience in web systems development, in particular biomolecular databases. ThermInfo will develop an information system for collecting and presenting thermochemical properties obtained from critically evaluated experimental data and several estimation methods." /> 
	<meta name="keywords" content="ThermInfo, therminfo, Ana Teixeira, Roni Reis, chemistry, thermochemistry, thermochemical properties, organic compounds, enthalpy, information system, database, chemoinformatics, search engine, properties prediction, Lasige, xldb, cqb, search engine, structural data, similarity search, structural search" /> 
	<base href="<?php echo base_url();?>" />
	<link rel="shortcut icon" href="public/media/images/therminfo.ico" />
	<link rel="stylesheet" href="public/css/main.css" type="text/css" media="screen, projection, print" />
	<link rel="stylesheet" href="public/css/pages/compound.css" type="text/css" media="screen, projection, print" />
	<link rel="stylesheet" href="public/css/vendor/theme_1/jquery-ui.css" type="text/css" media="screen, projection, print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" href="public/css/base/ie.css" type="text/css" media="screen, projection" />
	<![endif]-->
	<script type="text/javascript" src="public/js/ganalytics.js"></script>
	<script type="text/javascript" src="public/js/vendor/jquery.min.js"></script>
	<script type="text/javascript" src="public/js/vendor/jquery-ui.custom.min.js"></script>
	<script type="text/javascript" src="public/js/compound.js"></script>
</head>
<body>
	<a id="skip-link" href="<?php echo uri_string();?>#pageContentMain" title="Skip link">skip to main content</a>
	<div id="pageTopBar">
		<div id="pageTopBarDate" class="smallText left textCenter"><?php echo date('l, F d, Y');?></div>
		<div id="pageTopBarLinks" class="right">
			<div class="left"><a href="https://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a><script type="text/javascript" src="//platform.twitter.com/widgets.js"></script></div>
		</div>
	</div>
	<div id="pageTop">
		<div id="pageTopLogo" class="left textCenter"><a class="img-link" href="main" title="Go to Therminfo Homepage"><img id="logo" src="public/media/images/logo.png" width="168" height="107" alt="Therminfo Logo" /></a></div>
	</div>
	<div id="pageContent">
		<div id="pageContentMain">
		<h1 id="compoundTitle"><?php echo $molecule; ?></h1>
			<div class="column center">
				<div class="portlet">
					<div class="portlet-header">Image</div>
					<div class="portlet-content"><?php echo $molImage; ?></div>
				</div>
				<div class="portlet">
					<div class="portlet-header">Structural Data</div>
					<div class="portlet-content"><?php echo $sd_result; ?></div>
				</div>
				<div class="portlet">
					<div class="portlet-header">Properties</div>
					<div class="portlet-content"><?php echo $d_result; ?></div>
				</div>
				<div class="portlet">
					<div class="portlet-header">References</div>
					<div class="portlet-content"><?php echo $r_result; ?></div>
				</div>
			</div>
			<div id="printButton" class="clear textCenter">
				<button class="btTxt clickable" type="button" title="Print this Page" onclick="window.print()">Print this Page</button>
			</div>
		</div>
	</div>
	<div id="pageFooter" class="clear textCenter smallText">
		<p>Therminfo &nbsp;&middot;&nbsp; Copyright &#169; 2009 - <?php echo date('Y');?> &nbsp;&middot;&nbsp; LaSIGE - XLDB</p>
	</div>
</body>
</html>