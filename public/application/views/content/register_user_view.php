<?php
$data = array(
		'page' => NULL,
		'title' => 'Register!',
		'css_files' => array('public/css/pages/register_user.css'),
		'js_files' => array('public/js/register_user.js'));
$this->load->helper('form');
$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<div id="formContainer" class="center">
		<h2 id="formHeader">
			<span class="formLegendTitle">Register an account on ThermInfo</span> [<a href="help#register" target="_blank">Help</a>]
		</h2>
		<form id="tForm" class="center" action="<?php echo site_url('register_user');?>" method="post">
			<fieldset id="register-fields">
				<legend class="desc">Registration Fields</legend>
				<p id="register-intro-text">
					On this page you can register an account for inserting new compounds on ThermInfo.<br />
					Your name, e-mail address and Institution are required.<br />
					Afterwards, you have to wait for approval by an adminstrator when that happens you will receive an e-mail containing the password to Login.<br />
					Thus if you fail to provide your e-mail address, you will not be able to use this service.<br />
					<span class="smallText">Note: <span class="req">*</span> required fields.</span>
				</p>
				<div class="center">
					<ul>
						<li>
							<label for="register-name">First Name: <span class="req">*</span></label>
							<input type="text" id="register-fname" class="text" name="register_f_name" value="<?php echo form_prep($r_fname); ?>" size="35" title="Insert your first name" placeholder="Insert your first name" />
						</li>
                        <li>
							<label for="register-name">Last Name: <span class="req">*</span></label>
							<input type="text" id="register-lname" class="text" name="register_l_name" value="<?php echo form_prep($r_lname); ?>" size="35" title="Insert your last name" placeholder="Insert your last name" />
						</li>
						<li>
							<label for="register-email">E-mail: <span class="req">*</span></label>
							<input type="email" id="register-email" class="email" name="register_email" value="<?php echo form_prep($r_email); ?>" size="35" title="Insert your e-mail" placeholder="Insert your e-mail" />
						</li>
						<li>
							<label for="register-institution">Institution: <span class="req">*</span></label>
							<input type="text" id="register-institution" class="text" name="register_institution" value="<?php echo form_prep($r_inst); ?>" size="35" title="Insert your institution" placeholder="Insert your institution" />
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
							<input type="submit" class="btTxt clickable" name="submit" value="Send" title="Submit User" />
						</div>
					</li>
					<li class="full">
						<div id="formCodeInfo">[Type only <strong><span class="underlineText">numerical characters</span></strong>. Ignore letters and special characters.]</div>
					</li>
				</ul>
			</fieldset>
			<input type="hidden" name="register_ip" value="<?php echo $ip;?>" />
			<input type="hidden" name="register_agent" value="<?php echo $agent;?>" />
		</form>
	</div>
	<div id="pageContentTextInfo" class="center bodyText">
		<?php echo $result; ?>
	</div>
</div>
<?php $this->load->view('layout/footer'); ?>