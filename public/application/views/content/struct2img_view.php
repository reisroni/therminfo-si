<?php
$data = array(
		'page' => 'tools_menu',
		'title' => 'Tools!',
		'css_files' => array('public/css/pages/qsearch.css','public/css/pages/ssearch.css'),
		'js_files' => array('public/js/ssearch.js'));

$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<div id="formContainer" class="center">
		<h2 id="formHeader">
			<span class="formLegendTitle">2D Structure/SMILES -> Image</span> [<a href="help#struct2img" target="_blank">Help</a>]
		</h2>
		<form id="tForm" class="center" action="<?php echo site_url('tools/struct2img');?>" method="post" onsubmit="return validateForm()">
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
						<label for="smiles-input">SMILES: </label>
						<input type="text" class="text" id="smiles-input" size="30" title="Insert a SMILES to draw" placeholder="Insert a SMILES to draw" />
						<input type="hidden" id="h-smiles" name="smiles" />
						<input type="hidden" id="h-molfile" name="molfile" />
						<input type="hidden" id="h-smiles-ch" name="smiles_chiral" />
						<button type="button" id="load-btn" class="btTxt clickable" title="Load SMILES">Load SMILES</button>
						<button type="button" id="clear-btn" class="btTxt clickable" title="Clear Draw">Clear</button>
						<input type="submit" class="btTxt clickable" name="submit" value="Generate" title="Generate" />
					</li>
				</ul>
			</fieldset>
		</form>
	</div>
	<div id="pageContentTextResult" class="center bodyText">
		<?php echo $result; ?>
	</div>
</div>
<?php $this->load->view('layout/footer'); ?>