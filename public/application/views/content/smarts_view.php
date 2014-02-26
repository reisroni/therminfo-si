<?php
$data = array(
		'page' => 'search_menu',
		'title' => 'Search',
		'css_files' => array('public/css/pages/qsearch.css'),
		'js_files' => array());

$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<div id="formContainer" class="center">
		<h2 id="formHeader">
			<span class="formLegendTitle">Substructure Search using SMARTS Patterns</span> [<a href="help#smartsearch" target="_blank">Help</a>]
		</h2>
		<form id="tForm" class="center" action="<?php echo site_url('smarts');?>" method="post">
			<fieldset>
				<legend class="desc">Input</legend>
				<ul>
					<li class="textRight"><input type="search" class="search" name="smarts_1" size="70" title="Insert a smarts pattern" placeholder="Insert a smarts pattern" /></li>
					<li class="textRight">
						<select name="option_1" class="select" title="Add more pattern">
							<option value="AND">AND</option>
							<option value="OR">OR</option>
                        </select>
						<input type="search" class="search" name="smarts_2" size="70" title="Insert a smarts pattern" placeholder="Insert a smarts pattern" />
					</li>
					<li class="textRight">
						<select name="option_2" class="select" title="Add more pattern">
							<option value="AND">AND</option>
							<option value="OR">OR</option>
                        </select>
						<input type="search" class="search" name="smarts_3" size="70" title="Insert a smarts pattern" placeholder="Insert a smarts pattern" />
					</li>
					<li>
						<p class="text_note textCenter">About SMARTS (SMiles ARbitrary Target Specification) [<a href="http://www.daylight.com/dayhtml/doc/theory/theory.smarts.html" target="_blank"><strong>Help</strong></a>]</p>
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