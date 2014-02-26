<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="ISO-8859-1" />
<?php foreach($css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>
<?php foreach($js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
<?php endforeach; ?>
	<style type="text/css">
		h2 { font: bold 1.2em arial,sans-serif; }
	</style>
</head>
<body>
	<h2><?php echo $title; ?></h2>
	<div>
		<?php echo $output; ?>
	</div>
</body>
</html>