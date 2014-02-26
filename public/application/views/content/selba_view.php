<?php
$data = array(
		'page' => 'pred_menu',
		'title' => 'Properties Prediction',
		'css_files' => array('public/css/pages/qsearch.css','public/css/pages/ssearch.css'),
		'js_files' => array('public/js/ssearch.js'));

$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<?php if (is_null($result)): ?>
	<div id="formContainer" class="center">
		<h2 id="formHeader">
			<span class="formLegendTitle">Extended Laidler Bond Additivity Method <sub>(ELBA)</sub></span><br />
			<span><strong>(for now restricted to non-polycyclic hydrocarbons)</strong></span> [<a href="help#selba" target="_blank">Help</a>]
		</h2>
		<form id="tForm" class="center" action="<?php echo site_url('selba');?>" method="post">
			<fieldset>
				<legend class="desc">Draw Structure</legend>
				<ul>
					<li id="as-applet" class="textCenter">
						<applet id="jchem-p" codebase="<?php echo base_url('assets/jchempaint/');?>" code="org.openscience.jchempaint.applet.JChemPaintEditorApplet" archive="jchempaint-applet-core.jar" width="515" height="400">
							<param name="implicitHs" value="false" />
							<param name="codebase_lookup" value="false" />
							<param name="language" value="en" />
						</applet>
					</li>
					<li class="textCenter">
						<select id="selba-ebonds-select" class="select" name="sebonds_select" title="Choose double bonds in trans configuration">
							<option value="0">0</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
						</select>
						<span>double bonds in <em>trans</em> configuration (E) [<strong>cyclic</strong> compounds: 8- or 12-atoms ring]</span>
					</li>
					<li class="textCenter">
						<label for="smileInput">SMILES Input: </label>
						<input type="text" class="text" id="smiles-input" size="40" title="Insert a SMILES to draw" placeholder="Insert a SMILES to draw" />
						<input type="hidden" id="h-smiles" name="smiles" />
						<input type="hidden" id="h-molfile" name="molfile" />
						<input type="hidden" id="h-smiles-ch" name="smiles_chiral" />
						<button type="button" id="load-btn" class="btTxt clickable" title="Load SMILES">Load SMILES</button>
						<button type="button" id="clear-btn" class="btTxt clickable" title="Clear">Clear</button>
					</li>
				</ul>
			</fieldset>
			<fieldset>
				<legend class="desc">Predict Properties</legend>
				<ul>
					<li class="textCenter">
						<input type="submit" class="btTxt clickable" name="submit" value="Predict" title="Predict Properties" />
					</li>
				</ul>
			</fieldset>
		</form>
	</div>
	<div id="pageContentTextInfo" class="center bodyText">
		<?php echo $info; ?>
	</div>
	<?php else: ?>
	<div id="goBackForm" class="bodyText textCenter">
		<form class="textCenter" action="<?php echo site_url('selba');?>" method="post">
			<input type="submit" class="btTxt clickable" value="Draw another compound" title="Go Back" />
		</form>
	</div>
	<div id="pageContentTextPanel" class="center bodyText">
		<div id="pageContentTextInfo" class="center bodyText">
			<?php echo $info; ?>
		</div>
	</div>
	<div id="pageContentTextResult" class="center bodyText">
		<?php echo $result; ?>
	</div>
	<?php endif; ?>
</div>
<?php $this->load->view('layout/footer'); ?>