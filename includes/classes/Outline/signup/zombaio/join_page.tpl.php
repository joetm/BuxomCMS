<?php $outline = Outline::get_context(); ?><form method="POST" action="https://secure.zombaio.com/?287649262.952284.EN">

		
		<input name="return_url_approve" type="hidden" id="return_url_approve" style="width:120px;border-color:#666666;" value="<?php echo $options['approval_url']; ?>">

		
		<input name="return_url_decline" type="hidden" id="return_url_decline" style="width:120px;border-color:#666666;" value="<?php echo $options['denial_url']; ?>">

		
		<input name="return_url_error" type="hidden" id="return_url_error" style="width:120px;border-color:#666666;" value="<?php echo $options['error_url']; ?>">

		
		<input name="site" type="hidden" id="site" style="width:120px;border-color:#666666;" value="<?php echo __sitename; ?>">


		
		Choose your Username:<br />
		<input name="Username" type="text" class="faqinput" id="username" style="width:120px;border-color:#666666;" value="JohnDoe">
		<br />

		
		Choose your Password:<br />
		<input name="Password" type="password" class="faqinput" id="password" style="width:120px;border-color:#666666;" value="mysecret">
		<br />

		
		Your Email:<br />
		<input name="Email" type="text" class="faqinput" id="email" style="width:120px;border-color:#666666;" value="john@doe.com">


		<div>
			<input type="button" onClick="window.location='https://secure.zombaio.com/?SITE_ID.PRICING_ID.LANG;" value="Trial €2,95 (3 days) then €29,95 every 30 days">

		</div>


		<div class="c mbot10"></div>

		<div>
			Processor: <?php echo $options[processor]; ?>
			<br />
		</div>

	<input type="submit" value="Continue -->" name="zomPay">

</form>
<?php Outline::finish(); ?>