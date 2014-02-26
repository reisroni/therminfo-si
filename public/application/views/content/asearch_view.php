<?php
$data = array(
		'page' => 'search_menu',
		'title' => 'Search',
		'css_files' => array('public/css/pages/qsearch.css','public/css/pages/asearch.css'),
		'js_files' => array('public/js/asearch.js'));

$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<div id="formContainer" class="center">
		<h2 id="formHeader">
			<span class="formLegendTitle">Advanced Search</span> [<a href="help#asearch" target="_blank">Help</a>]
		</h2>
		<form id="tForm" class="center" action="<?php echo site_url('asearch');?>" method="post">
			<fieldset>
				<legend class="desc">General</legend>
				<ul>
					<li class="leftHalf">
						<span><label for="compound">Compound Name:</label></span>
						<input type="search" id="compound" class="search" name="compound" size="25" title="Insert a compound name" placeholder="Insert a compound name" />
					</li>
					<li class="middleFourth">
						<span><label for="state">Physical State:</label></span>
						<select id="state" class="select" name="state" title="Choose a physical state">
							<option value="all">All</option>
							<option value="l">Liquid</option>
							<option value="g">Gas</option>
							<option value="c">Crystal</option>
						</select>
					</li>
					<li class="rightFourth">
						<span><label for="mw">Molecular Weight:</label></span>
						<select id="intervalMW" class="select" name="intervalmw" title="Choose the range for molecular weight">
							<option value="=">=</option>
							<option value="&gt;">&gt;</option>
							<option value="&lt;">&lt;</option>
						</select>
						<input type="search" id="mw" class="search" name="mw" size="5" title="Insert a molecular weight" placeholder="Weight" />
					</li>
					<li class="full">
						<span><label for="formula">Molecular Formula:</label></span>
						<input type="search" id="formula" class="search" name="formula" size="25" title="Insert a molecular formula" placeholder="Insert a molecular formula" /><br />
						<span class="underlineText">Wildcard</span>: <strong>?</strong> represents one character
					</li>
					<li id="as_smiles" class="full">
						<span><label for="smiles">SMILES:</label></span>
						<input type="search" id="smiles" class="search" name="smiles" size="25" title="Insert a SMILES" placeholder="Insert a SMILES" />
						<select id="smilesThreshold" class="select" name="threshold" title="Choose a similarity threshold value" >
							<option value="1" >Identical Structures</option>
							<option value="0.95" >Similar Structures, >=95%</option>
							<option value="0.90" >Similar Structures, >=90%</option>
							<option value="0.80" >Similar Structures, >=80%</option>
							<option value="0.70" >Similar Structures, >=70%</option>
							<option value="i1" >Similar Structures, 90-95%</option>
							<option value="i2" >Similar Structures, 80-90%</option>
							<option value="i3" >Similar Structures, 70-80%</option>
						</select>
					</li>
				</ul>
			</fieldset>
			<fieldset>
				<legend id="classLegend" class="desc">Classes and Family</legend>
				<ul id="classList">
					<li>
						<span><label for="class">Class:</label></span>
						<select id="class" class="select" name="classe" title="Choose a class">
							<?php echo $class; ?>
						</select>
					</li>
					<li>
						<span><label for="subclass">Sub-Class:</label></span>
						<select id="subclass" class="select" name="subclass" title="Choose a sub-class">
							<?php echo $subclass; ?>
						</select>
					</li>
					<li>
						<span><label for="family">Family:</label></span>
						<select id="family" class="select" name="family" title="Choose a family">
							<?php echo $family; ?>
						</select>
					</li>
				</ul>
			</fieldset>
			<fieldset>
				<legend id="charLegend" class="desc">Characteristics</legend>
				<ul id="charList">
					<li>
						<table id="charTable" class="center">
							<tr>
								<td class="charTitle"><strong>CH Groups</strong></td>                                                                                   
								<td><input type="checkbox" id="char1" class="checkbox" name="ch[]" value="Alkane" /><label for="char1"> Alkane </label><br /><span class="groupsText">[R-H]</span></td>
								<td><input type="checkbox" id="char2" class="checkbox" name="ch[]" value="Alkene" /><label for="char2"> Alkene </label><br /><span class="groupsText">[R<sub>2</sub>C=CR<sub>2</sub>]</span></td>
								<td><input type="checkbox" id="char3" class="checkbox" name="ch[]" value="Alkyne" /><label for="char3"> Alkyne </label><br /><span class="groupsText">[R-C&equiv;C-R]</span></td>
								<td><input type="checkbox" id="char4" class="checkbox" name="ch[]" value="Arene" /><label for="char4"> Arene </label><br /><span class="groupsText">[Ar-H]</span></td>
							</tr>
							<tr>
								<td class="charTitle" rowspan="2"><strong>CHO Groups</strong></td>     
								<td><input type="checkbox" id="char5" class="checkbox" name="ch[]" value="Alcohol" /><label for="char5"> Alcohol </label><br /><span class="groupsText">[R-OH]</span></td>
								<td><input type="checkbox" id="char6" class="checkbox" name="ch[]" value="Ether" /><label for="char6"> Ether </label><br /><span class="groupsText">[R-O-R]</span></td>                                                     
								<td><input type="checkbox" id="char7" class="checkbox" name="ch[]" value="Peroxide" /><label for="char7"> Peroxide </label><br /><span class="groupsText">[R-O-O-R, R-O-OH]</span></td>             
								<td><input type="checkbox" id="char8" class="checkbox" name="ch[]" value="Aldehyde" /><label for="char8"> Aldehyde </label><br /><span class="groupsText">[R(C=O)H]</span></td>                
							</tr>
							<tr class="noborder">
								<td><input type="checkbox" id="char9" class="checkbox" name="ch[]" value="Ketone" /><label for="char9"> Ketone </label><br /><span class="groupsText">[R(C=O)R]</span></td>                    
								<td colspan="2"><input type="checkbox" id="char10" class="checkbox" name="ch[]" value="Carboxylic Acid" /><label for="char10"> Carboxilic Acid </label><br /><span class="groupsText">[R(C=O)OH]</span></td>       
								<td><input type="checkbox" id="char11" class="checkbox" name="ch[]" value="Ester" /><label for="char11"> Ester </label><br /><span class="groupsText">[R(C=O)O-R]</span></td> 
							</tr> 
							<tr>
								<td class="charTitle"><strong>CHN Groups</strong></td>
								<td><input type="checkbox" id="char12" class="checkbox" name="ch[]" value="Amine" /><label for="char12"> Amine </label><br /><span class="groupsText">[R<sub>3</sub>N]</span></td>                                                          
								<td><input type="checkbox" id="char13" class="checkbox" name="ch[]" value="Hydrazine" /><label for="char13"> Hydrazine </label><br /><span class="groupsText">[R-NH-NH-R]</span></td>
								<td><input type="checkbox" id="char14" class="checkbox" name="ch[]" value="Imine" /><label for="char14"> Imine </label><br /><span class="groupsText">[R-N=R, R-N=N-R]</span></td>
								<td><input type="checkbox" id="char15" class="checkbox" name="ch[]" value="Nitrile/Isonitrile" /><label for="char15"> Nitrile/Isonitrile </label><br /><span class="groupsText">[R-C &equiv; N, R-N<sup>+</sup> &equiv; C<sup>-</sup>]</span></td>
							</tr>														  
							<tr>
								<td class="charTitle"><strong>CHON Groups</strong></td>
								<td colspan="3"><input type="checkbox" id="char16" class="checkbox" name="ch[]" value="NOx" /><label for="char16"> NOx </label><br /><span class="groupsText">[R<sub>3</sub>N<sup>+</sup>-O<sup>-</sup>, R-N=O, R-O-N=O, R-N<sup>+</sup>(=O)O<sup>-</sup>, R-O-N<sup>+</sup>(=O)O<sup>-</sup>]</span></td>
								<td><input type="checkbox" id="char17" class="checkbox" name="ch[]" value="Amide" /><label for="char17"> Amide </label><br /><span class="groupsText">[R(C=O)NR<sub>2</sub>]</span></td>
							</tr>
							<tr>
								<td class="charTitle"><strong>CHS Groups</strong></td>
								<td><input type="checkbox" id="char18" class="checkbox" name="ch[]" value="Thiol" /><label for="char18"> Thiol </label><br /><span class="groupsText">[R-SH]</span></td>                                           
								<td><input type="checkbox" id="char19" class="checkbox" name="ch[]" value="Thioether" /><label for="char19"> Thioether </label><br /><span class="groupsText">[R-S-R]</span></td>            
								<td><input type="checkbox" id="char20" class="checkbox" name="ch[]" value="Polysulphide" /><label for="char20"> Polysulphide </label><br /><span class="groupsText">[R-S-S-R, R-S-SH]</span></td>      
								<td><input type="checkbox" id="char21" class="checkbox" name="ch[]" value="Thiocarbonyl" /><label for="char21"> Thiocarbonyl </label><br /><span class="groupsText">[R(C=S)R, R(C=S)H]</span></td>
							</tr>
							<tr>
								<td class="charTitle"><strong>CHOS Groups</strong></td>
								<td colspan="4"><input type="checkbox" id="char22" class="checkbox" name="ch[]" value="SOx" /><label for="char22"> SOx </label><span class="groupsText">[R<sub>2</sub>S=O, R<sub>2</sub>S(=O)<sub>2</sub>, R-O-S(=O)-O-R, R-O-S(=O)<sub>2</sub>-O-R]</span></td>
							</tr>
							<tr>
								<td class="charTitle"><strong>CHX Groups</strong><br /><span class="groupsText"> (X=F, Cl, Br, I)</span></td>   
								<td colspan="4"><input type="checkbox" id="char23" class="checkbox" name="ch[]" value="Halogen" /><label for="char23"> Halogen </label><span class="groupsText">[R-X]</span></td>
							</tr>
							<tr>
								<td class="charTitle" rowspan="2"><strong>Physical</strong></td>
								<td><input type="checkbox" id="char24" class="checkbox" name="ch[]" value="Radical" /><label for="char24"> Radical</label></td>
								<td><input type="checkbox" id="char25" class="checkbox" name="ch[]" value="Charges" /><label for="char25"> Charges</label></td>
								<td><input type="checkbox" id="char26" class="checkbox" name="ch[]" value="Ionic" /><label for="char26"> Ionic</label></td>
								<td><input type="checkbox" id="char27" class="checkbox" name="ch[]" value="Solvation" /><label for="char27"> Solvation</label></td>
							</tr>
							<tr class="noborder">
								<td colspan="4"><input type="checkbox" id="char28" class="checkbox" name="ch[]" value="Polymer" /><label for="char28"> Polymer</label></td>                
							</tr>
						</table>
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