<?php
$data = array(
		'page' => NULL,
		'title' => 'Reset Password!',
		'css_files' => array('public/css/pages/register_user.css'),
		'js_files' => array('public/js/reset_pass.js'));
$this->load->helper('form');
$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<div id="formContainer" class="center">
		<h2 id="formHeader">
			<span class="formLegendTitle">Reset your password!</span> [<a href="help#register" target="_blank">Help</a>]
		</h2>
		<form id="tForm" class="center" action="<?php echo site_url('reset_pass');?>" method="post">
			<fieldset id="reset-fields">
				<legend class="desc">E-mail Address</legend>
				<p id="reset-intro-text">
					On this page you can reset your password. Please fill your e-mail address and check your e-mail inbox!
				</p>
				<div class="center">
					<ul>
						<li>
							<label for="reset-email">E-mail: <span class="req">*</span></label>
							<input type="email" id="reset-email" class="email" name="reset_email" value="<?php echo form_prep($rs_email); ?>" size="35" title="Insert your e-mail" placeholder="Insert your e-mail" />
						</li>
					</ul>
				</div>
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
						<div id="submitButton">
							<input type="submit" class="btTxt clickable" name="submit" value="Reset" title="Reset Password" />
						</div>
					</li>
					<li class="full">
						<div id="formCodeInfo">[Type only <strong><span class="underlineText">numerical characters</span></strong>. Ignore letters and special characters.]</div>
					</li>
				</ul>
			</fieldset>
		</form>
	</div>
	<div id="pageContentTextInfo" class="center bodyText">
		<?php echo $result; ?>
	</div>
</div>
<?php $this->load->view('layout/footer'); ?>