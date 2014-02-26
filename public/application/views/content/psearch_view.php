<?php
$data = array(
		'page' => 'search_menu',
		'title' => 'Property Search',
		'css_files' => array(),
		'js_files' => array());

$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<div id="formContainer" class="center">
		<h2 id="formHeader">
			<span class="formLegendTitle">Thermochemical Property Search</span> [<a href="help#propsearch" target="_blank">Help</a>]
		</h2>
		<form id="tForm" class="center" action="<?php echo site_url('psearch');?>" method="post">
			<fieldset>
				<legend class="desc">Input</legend>
				<ul>
					<li class="textRight">
						<select class="select" name="prop_1" title="Select a property">
                            <?php echo $sel_props; ?>
						</select>
                        <input type="text" class="text" name="prop_1_value_1" size="1" title="Value 1" /><span> to </span>
						<input type="text" class="text" name="prop_1_value_2" size="1" title="Value 2" />
					</li>
					<li class="textRight">
                        <select class="select" name="option_1" title="Add more property">
							<option value="AND">AND</option>
							<option value="OR">OR</option>
                        </select>
						<select class="select" name="prop_2" title="Select a property">
                            <?php echo $sel_props; ?>
						</select>
                        <input type="text" class="text" name="prop_2_value_1" size="1" title="Value 1" /><span> to </span>
						<input type="text" class="text" name="prop_2_value_2" size="1" title="Value 2" />
					</li>
					<li class="textRight">
                        <select class="select" name="option_2" title="Add more property">
							<option value="AND">AND</option>
							<option value="OR">OR</option>
                        </select>
						<select name="prop_3" class="select" title="Select a property">
                            <?php echo $sel_props; ?>
						</select>
                        <input type="text" class="text" name="prop_3_value_1" size="1" title="Value 1" /><span> to </span>
						<input type="text" class="text" name="prop_3_value_2" size="1" title="Value 2" />
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