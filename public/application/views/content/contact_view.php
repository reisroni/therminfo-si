<?php
$data = array(
		'page' => 'contact_menu',
		'title' => 'Contact Us!',
		'css_files' => array('public/css/pages/contact.css'),
		'js_files' => array('public/js/contact.js'));
$this->load->helper('form');
$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<div id="formContainer" class="center">
		<h2 id="formHeader">
			<span class="formLegendTitle">Leave us a comment!</span>
		</h2>
		<form id="tForm" class="center" action="<?php echo site_url('contact');?>" method="post">
			<div id="contact-intro-text">
				<p>
					Do you have a suggestion, comment, or wish to send a message to us?<br />
					Did you find any erroneous or incomplete data?<br />
					Then please fill out this contact form. Your name and e-mail address are required. <br />
					<strong>Thanks for the feedback!</strong>
					<img src="http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=http://www.therminfo.com&choe=UTF-8&" alt="QR code" style="float: right;"/>
				</p>
				<div id="contact-logos">
					<h3 class="orangeText">Find us on</h3>
					<div class="center">
						<a href="http://twitter.com/ThermInfo" title="ThermInfo Twitter" target="_blank">
							<img src="public/media/images/twitter_logo.png" alt="Twitter logo" />
						</a>
						<a href="http://www.facebook.com/people/ThermInfo-Fcul/100000751574838" title="ThermInfo Facebook"  target="_blank">
							<img src="public/media/images/facebook_logo.png" alt="Facebook logo" />
						</a>
						<a href="http://www.youtube.com/therminfo" title="ThermInfo Youtube Channel" target="_blank">
							<img src="public/media/images/youtube_logo.png" alt="Youtube logo" />
						</a>
						<a href="mailto:therminfo@gmail.com" title="E-mail">
							<img src="public/media/images/gmail_logo.png" alt="E-mail logo" />
						</a>
					</div>
					<span class="smallText">Note: <span class="req">*</span> required fields.</span>
				</div>
			</div>
			<fieldset id="contact-fields">
				<legend class="desc">Contact form</legend>
				<ul>
					<li>
						<label for="contact-name">Name: <span class="req">*</span></label>
						<input type="text" id="contact-name" class="text" name="contact_name" value="<?php echo form_prep($c_name); ?>" size="35" title="Insert your name" placeholder="Insert your name" />
					</li>
					<li>
						<label for="contact-email">E-mail: <span class="req">*</span></label>
						<input type="email" id="contact-email" class="email" name="contact_email" value="<?php echo form_prep($c_email); ?>" size="35" title="Insert your e-mail" placeholder="Insert your e-mail" />
					</li>
					<li>
						<label for="contact-subject">Subject: <span class="req">*</span></label>
						<select id="contact-subject" class="select" name="contact_subject" title="Select the subject">
							<option value="none">Select the subject</option>
							<option value="Suggestion">Suggestion</option>
							<option value="Comment">Comment</option>
							<option value="Question">Question</option>
							<option value="Erroneous Data">Erroneous Data</option>
							<option value="Incomplete Data">Incomplete Data</option>
							<option value="Other">Other</option>
						</select>
					</li>
					<li>
						<label for="contact-msg">Message: <span class="req">*</span></label>
						<textarea id="contact-msg" class="textarea" name="contact_msg" rows="8" cols="55" placeholder="Insert your message"><?php echo form_prep($c_msg); ?></textarea>
					<li>
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
						<div id="submitButton">
							<input type="submit" class="btTxt clickable" name="submit" value="Send" title="Submit Message" />
						</div>
						<div id="resetButton">
							<input type="reset" class="btTxt clickable" value="Clear" title="Clear All" />
						<div>
					</li>
					<li class="full">
						<div id="formCodeInfo">[Type only <strong><span class="underlineText">numerical characters</span></strong>. Ignore letters and special characters.]</div>
					</li>
				</ul>
			</fieldset>
			<input type="hidden" name="contact_ip" value="<?php echo $ip;?>" />
			<input type="hidden" name="contact_agent" value="<?php echo $agent;?>" />
		</form>
	</div>
	<div id="pageContentTextInfo" class="center bodyText">
		<?php echo $result; ?>
	</div>
</div>
<?php $this->load->view('layout/footer'); ?>