<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

<script type="text/javascript"><!--
function ClearInput(id){
	var input = document.getElementById(id);
	input.value = '';
}
//-->
</script>

	<div class="pagecontent">

		<a name="about"></a>

		<div id="loginpage" align="center">


				<div class="c">&nbsp;</div>

				<div class="bottom-a mbot5" align="center">
					<a href="<?php echo $__siteurl; ?>"><?php echo $__sitename; ?></a>
					<br />
					[<?php echo $_t['adminlogin']; ?>]
				</div>

				<?php if ($_error) { ?>
				<div class="error">
					<?php echo $_error; ?>
				</div>
				<?php } ?>

				<div id="form">
					<form name="loginform" id="loginform" action="<?php echo $__adminurl; ?>/" method="post">

					<p>
						<label><?php echo $_t['username']; ?><br />
						<input name="<?php echo Authentification::USERNAME; ?>" id="user_login" class="input" value="" size="20" tabindex="10" type="text" /></label>
					</p>
					<p>
						<label><?php echo $_t['password']; ?><br />
						<input name="<?php echo Authentification::PASSWORD; ?>" id="user_pass" class="input" value="" size="20" tabindex="20" type="password" onclick="ClearInput(this.id);" /></label>
					</p>
					<p class="forgetmenot"><label><input name="<?php echo Authentification::REMEMBER; ?>" id="rememberme" value="1" tabindex="90" type="checkbox" /> <?php echo $_t['rememberme']; ?></label></p>

					<input name="redirect" type="hidden" value="<?php echo $redirect; ?>" />

					<p class="submit">
						<input type="submit" class="login-button" tabindex="100" value="<?php echo $_t['login']; ?>" id="submit" name="submit" />
					</p>
					</form>
				</div><!-- /form-->

				<div id="bottom" style="width:280px;" align="center">
					<div class="bottom-text smallfont mtop5 mbot10">

					</div>
				</div><!-- /bottom-->


			<script type="text/javascript"><!--
			try{document.getElementById('user_login').focus();}catch(e){}
			//-->
			</script>

		</div><!-- /loginpage-->

	</div><!-- /pagecontent-->

<?php $outline_include_1 = new Outline('_adminfooter'); require $outline_include_1->get(); Outline::finish(); ?>