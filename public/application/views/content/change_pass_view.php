<?php
$data = array(
		'page' => NULL,
		'title' => 'Change Password!',
		'css_files' => array('public/css/pages/register_user.css'),
		'js_files' => array('public/js/change_pass.js'));
$this->load->helper('form');
$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<div id="formContainer" class="center">
		<h2 id="formHeader">
			<span class="formLegendTitle">Change Password</span> [<a href="help#change" target="_blank">Help</a>]
		</h2>
		<form id="tForm" class="center" action="<?php echo site_url('change_pass');?>" method="post">
			<fieldset id="change-fields">
				<legend class="desc">Password Fields</legend>
				<p id="change-intro-text">
					Enter your E-mail adress, current password, and new password (twice for verification) then click on Change Password.<br />
					<span class="smallText">Note: <span class="req">*</span> required fields.</span>
				</p>
				<div class="center">
					<ul>
						<li>
							<label for="change-email">E-mail: <span class="req">*</span></label>
							<input type="email" id="change-email" class="email" name="change_email" value="<?php echo form_prep($ch_email); ?>" size="35" title="Insert your e-mail" placeholder="Insert your e-mail" />
						</li>
						<li>
							<label for="change-old-pass">Old Password: <span class="req">*</span></label>
							<input type="password" id="change-old-pass" class="text" name="change_old_pass" size="35" title="Insert your password" placeholder="Insert your password" />
						</li>
						<li>
							<label for="change-new-pass-1">New Password: <span class="req">*</span></label>
							<input type="password" id="change-new-pass-1" class="text" name="change_new_pass_1" size="35" title="Insert new password" placeholder="Insert new password" />
						</li>
						<li>
							<label for="change-new-pass-2">Verify Password: <span class="req">*</span></label>
							<input type="password" id="change-new-pass-2" class="text" name="change_new_pass_2" size="35" title="Insert new password again" placeholder="Insert new password again" />
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
							<input type="submit" class="btTxt clickable" name="submit" value="Change" title="Change Password" />
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