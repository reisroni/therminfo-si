<?php
$data = array(
		'page' => 'tools_menu',
		'title' => 'Tools!',
		'css_files' => array(),
		'js_files' => array('public/js/tools.js'));
$this->load->helper('form');
$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<div id="formContainer" class="center">
		<h2 id="formHeader">
			<span class="formLegendTitle">Molecular Weight Calculator</span> [<a href="help#mw" target="_blank">Help</a>]
		</h2>
		<form id="tForm" class="center" action="<?php echo site_url('tools/mw');?>" method="post">
			<fieldset>
				<legend class="desc">Input</legend>
				<ul class="textCenter">
					<li class="full">
						<input type="text" id="mw_term" class="text" name="term" title="Insert a term" placeholder="Insert a term" value="<?php echo form_prep($term); ?>" />
						<select id="termType" class="select" name="termtype" title="Choose a term type">
							<option value="name"<?php echo $option_name; ?>>Name</option>
							<option value="smiles"<?php echo $option_smiles; ?>>SMILES</option>
							<option value="inchi"<?php echo $option_inchi; ?>>InChI</option>
							<option value="formula"<?php echo $option_form; ?>>Molecular Formula</option>
						</select>
						<input type="submit" class="btTxt clickable" name="submit" value="Calculate MW" title="Calculate" />
					</li>
					<li class="full">
						<p id="formRule">
							Atoms may be in any order. Use upper-case and lower-case to write the elements symbol correctly. The formula must contain ONLY letters, integer coefficients and eventually brackets. [<a href="help#mw_form" target="_blank"><strong>Help</strong></a>]
						</p>
					</li>
				</ul>
			</fieldset>
		</form>
	</div>
	<div id="pageContentTextResult" class="center">
		<?php echo $result; ?>
	</div>
</div>
<?php $this->load->view('layout/footer'); ?>