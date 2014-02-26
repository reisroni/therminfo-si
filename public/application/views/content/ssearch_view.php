<?php
$data = array(
		'page' => 'search_menu',
		'title' => 'Search',
		'css_files' => array('public/css/pages/qsearch.css','public/css/pages/ssearch.css'),
		'js_files' => array('public/js/ssearch.js'));

$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<?php if (is_null($result)): ?>
	<div id="formContainer" class="center">
		<h2 id="formHeader">
			<span class="formLegendTitle">Structural Search</span> [<a href="help#ssearch" target="_blank">Help</a>]
		</h2>
		<form id="tForm" class="center" action="<?php echo site_url('ssearch');?>" method="post">
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
				<legend class="desc">Search</legend>
				<ul>
					<li class="leftHalf textRight">
						<select class="select" name="threshold" title="Choose a structure threshold value">
							 <option value="1">Identical Structures</option>
							 <option value="0.95">Similar Structures, >= 95%</option>
							 <option value="0.90">Similar Structures, >= 90%</option>
							 <option value="0.80">Similar Structures, >= 80%</option>
							 <option value="0.70">Similar Structures, >= 70%</option>
							 <option value="i1">Similar Structures, 90-95%</option>
							 <option value="i2">Similar Structures, 80-90%</option>
							 <option value="i3">Similar Structures, 70-80%</option>
						</select>
					</li>
					<li class="rightHalf textCenter">
						<input type="submit" class="btTxt clickable" name="submit" value="Search" title="Search" />
					</li>
                    <?php if (isset($count) && $count): ?>
                    <li class="full textCenter">
						<div>This Search Method was already used <span class="orangeText"><?php echo $count; ?></span> times</div>
					</li>
                    <?php endif; ?>
				</ul>
			</fieldset>
		</form>
	</div>
	<div id="pageContentTextInfo" class="center bodyText">
		<?php echo $info; ?>
	</div>
	<?php else: ?>
	<div id="goBackForm" class="bodyText textCenter">
		<form class="textCenter" action="<?php echo site_url('ssearch');?>" method="post">
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