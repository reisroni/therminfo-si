<?php
$data = array(
		'page' => 'pred_menu',
		'title' => 'Properties Prediction',
		'css_files' => array('public/css/pages/qsearch.css'),
		'js_files' => array());

$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<div id="formContainer" class="center">
		<h2 id="formHeader">
			<span class="formLegendTitle">Extended Laidler Bond Additivity Method <sub>(ELBA)</sub></span><br />
			<span><strong>(for now restricted to non-polycyclic hydrocarbons)</strong></span> [<a href="help#qelba" target="_blank">Help</a>]
		</h2>
		<form id="tForm" class="center" action="<?php echo site_url('qelba');?>" method="post">
			<fieldset>
				<legend class="desc">Input</legend>
				<ul class="textCenter">
					<li class="full">
						<input type="search" id="qelba-term-input" class="search" name="qelba_term_input" title="Insert a search term" placeholder="Insert a search term" />
						<select id="qelba-type-select" class="select" name="qelba_type_select" title="Choose the term type">
							<option id="name" value="name">Name</option>
							<option id="smiles" value="smiles">SMILES</option>
						</select>
					</li>
					<li class="full">
						<select id="qelba-ebonds-select" class="select" name="ebonds_select" title="Choose double bonds in trans configuration">
							<option value="0">0</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
						</select>
						<span>double bonds in <em>trans</em> configuration (E) [<strong>cyclic</strong> compounds: 8- or 12-atoms ring]</span>
					</li>
				</ul>
			</fieldset>
			<fieldset>
				<legend class="desc">Predict Properties</legend>
				<ul>
					<li class="textCenter"><input type="submit" class="btTxt clickable" name="submit" value="Predict" title="Predict Properties" /></li>
				</ul>
			</fieldset>
		</form>
	</div>
	<?php if (! is_null($info)): ?>
	<div id="pageContentTextPanel" class="center bodyText">
		<div id="pageContentTextInfo" class="center bodyText">
			<?php echo $info; ?>
		</div>
	</div>
	<?php endif; ?>
	<div id="pageContentTextResult" class="center bodyText">
		<?php echo $result; ?>
	</div>
</div>
<?php $this->load->view('layout/footer'); ?>