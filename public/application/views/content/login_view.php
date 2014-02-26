<?php
$data = array(
		'page' => NULL,
		'title' => 'Login!',
		'css_files' => array('public/css/pages/login.css'),
		'js_files' => array('public/js/login.js'));
$this->load->helper('form');
$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<div id="formContainer" class="center">
		<h2 id="formHeader">
			<span class="formLegendTitle">Log in to ThermInfo!</span> [<a href="help#login" target="_blank">Help</a>]
		</h2>
		<form id="tForm" class="center" action="<?php echo site_url("login/redirect/{$url}");?>" method="post" onsubmit="return validateForm()">
			<fieldset>
				<legend class="desc">User Credentials</legend>
				<div id="loginText">
					<p>Your e-mail address and your password are required. Note that password is case sensitive.</p>
					<p>
						<a href="register_user" title="Register Here"><strong>You do not have an account? Register Here.</strong></a><br />
						<a href="change_pass" title="Change Your Password"><strong>Change Your Password Here.</strong></a><br />
						<a href="reset_pass" title="Reset Your Password"><strong>Forgot your password? Click Here</strong></a><br />
					</p>
				</div>
				<ul>
					<li class="full textCenter">
						<label for="email">E-mail address: </label>
						<input type="email" id="email" class="email" name="email" value="<?php echo form_prep($email); ?>" size="39" title="Insert your e-mail" placeholder="Insert your e-mail" />
					</li>
					<li class="full textCenter">
						<label id="login_pass" for="password">Password: </label>
						<input type="password" id="password" class="password" name="password" size="39" title="Insert your password" placeholder="Insert your password" />
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
						<div id="submitButton"><input type="submit" class="btTxt clickable" name="submit" value="Login" title="Login" /></div>
					</li>
					<li class="full">
						<div id="formCodeInfo">[Type only <strong><span class="underlineText">numerical characters</span></strong>. Ignore letters and special characters.]</div>
					</li>
				</ul>
			</fieldset>
		</form>
	</div>
	<div id="pageContentTextInfo" class="center bodyText">
		<?php echo $info; ?>
	</div>
</div>
<?php $this->load->view('layout/footer'); ?>