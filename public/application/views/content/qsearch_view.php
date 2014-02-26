<?php
$data = array(
		'page' => 'search_menu',
		'title' => 'Search',
		'css_files' => array('public/css/pages/qsearch.css'),
		'js_files' => array('public/js/qsearch.js'));

$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<div id="formContainer" class="center">
		<h2 id="formHeader">
			<span class="formLegendTitle">Quick Search</span> [<a href="help#qsearch" target="_blank">Help</a>]
		</h2>
		<form id="tForm" class="center" action="<?php echo site_url('qsearch');?>" method="post">
			<fieldset>
				<legend class="desc">Input</legend>
				<ul class="textCenter">
					<li class="full">
						<input type="search" id="searchTerm" class="search" name="searchterm" title="Insert a search term" placeholder="Insert a search term" />
						<select id="typeOfSearch" class="select" name="searchtype" title="Choose the term type">
							<option id="name" value="name" title="Please do not use wildcards">Name</option>
							<option id="formula" value="formula" title="Atoms may be in any order">Molecular Formula</option>
							<option id="thermId" value="thermId" title="ThermInfo ID format: CONNNNNNN (NNNNNNN = 7 digits)">ThermInfo ID</option>
							<option id="casrn" value="casrn" title="CASRN format: NNNNNNN-NN-N (1-7 digits, hyphen, 2 digits, hyphen, 1 digit)">CAS RN</option>
							<option id="smiles" value="smiles" title="Select a similarity threshold value">SMILES</option>
							<option id="inchi" value="inchi" title="Select a layer number">InChi</option>
						</select>
						<div id="smilesPane">
							<select id="smilesThreshold" class="select" name="smilesthreshold" title="Choose a similarity threshold value">
								<option value="1" >Identical Structures</option>
								<option value="0.95" >Similar Structures, >= 95%</option>
								<option value="0.90" >Similar Structures, >= 90%</option>
								<option value="0.80" >Similar Structures, >= 80%</option>
								<option value="0.70" >Similar Structures, >= 70%</option>
								<option value="i1" >Similar Structures, 90-95%</option>
								<option value="i2" >Similar Structures, 80-90%</option>
								<option value="i3" >Similar Structures, 70-80%</option>
							</select>
						</div>
						<div id="inchiPane">
							<select id="inchiLayer" class="select" name="inchiLayer" title="Choose a layer number">
								<option value="0" >All</option>
								<option value="1" >1</option>
								<option value="2" >2</option>
								<option value="3" >3</option>
							</select>
						</div>
					</li>
					<li id="qs_rules" class="full">
						<div id="nameRule">
							Please do not use wildcards. [<a href="help#searchname" target="_blank"><strong>Help</strong></a>]
						</div>
						<div id="formRule">
							Atoms may be in <strong>any</strong> order.<br /><span class="underlineText">Wildcard</span>: <strong>?</strong> represents one character [<a href="help#searchform" target="_blank"><strong>Help</strong></a>]
						</div>
						<div id="idFormat">
							ThermInfo ID format: <strong>CONNNNNNN</strong> (NNNNNNN = 7 digits). [<a href="help#searchid" target="_blank"><strong>Help</strong></a>]
						</div>
						<div id="casrnFormat">
							CASRN format: <strong>NNNNNNN-NN-N</strong> (1-7 digits, hyphen, 2 digits, hyphen, 1 digit). [<a href="help#searchcasrn" target="_blank"><strong>Help</strong></a>]
						</div>
						<div id="smilesFormat">
							<strong>SMILES:</strong> Select a similarity threshold value [<a href="help#searchsmiles" target="_blank"><strong>Help</strong></a>]
						</div>
						<div id="inchiFormat">
							<strong>InChi format:</strong> InChi=1/layer1/layer2/.. or InChi=1S/layer1/layer2/.. Select a layer number [<a href="help#searchinchi" target="_blank"><strong>Help</strong></a>]
						</div>
					</li>
				</ul>
			</fieldset>
			<fieldset>
				<legend class="desc">Security code</legend>
				<ul class="textCenter">
					<li id="sCode" class="middleFourth">
						<img src="image/captcha" id="captchaImg" alt="Captcha Code Image" />
					</li>
					<li class="middleFourth">
						<div id="formCodeInput"><input type="text" id="verCode" class="text" name="vercode" size="10" title="Insert the security code, only numerical characters" placeholder="Security code" /></div>
					</li>
					<li class="middleFourth">
						<div id="submitButton"><input type="submit" class="btTxt clickable" name="submit" value="Search" title="Search" /></div>
					</li>
					<li class="full">
						<div id="formCodeInfo">[Type only <strong><span class="underlineText">numerical characters</span></strong>. Ignore letters and special characters.]</div>
					</li>
                    <?php if (isset($count) && $count): ?>
                    <li class="full">
						<div>This Search Method was already used <span class="orangeText"><?php echo $count; ?></span> times</div>
					</li>
                    <?php endif; ?>
				</ul>
			</fieldset>
		</form>
	</div>
	<div id="pageContentTextResult" class="center bodyText">
		<?php echo $result; ?>
	</div>
</div>
<?php $this->load->view('layout/footer'); ?>