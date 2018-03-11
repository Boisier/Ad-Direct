<body class="login">
    <div id="backgroundOverlay"></div>
    <div id="fullPageBlock">
		<div id="loginBox">
			<div class="blocContainer">
				<img src="/assets/images/logo.png" id="bigLogo">
			</div>
			<div class="blocContainer">
				<h2 class="accessTitle"><?php echo \__("loginText"); ?></h2>
				<?php
					if(\Library\Session::exist("loginEvent"))
					{
						$event = \Library\Session::read("loginEvent");

						echo '<p class="errorMessage">'.\__($event).'</p>';

						\Library\Session::remove('loginEvent');
					}
				?>
				<form action="/Auth/login" method="POST" name="loginForm">
					<input type="text" name="login" placeholder="<?php echo \__("loginPlaceholder"); ?>" autofocus>
					<input type="password" name="password" placeholder="<?php echo \__("passwordPlaceholder"); ?>">
					<input type="submit" value="<?php echo \__("loginButton"); ?>">
				</form>
			</div>
		</div>
	</div>
	<section id="language">
		<?php
		if(\__("lang") == 'fr')
		{
			?>
			<a href="/?lang=en_EN">EN</a>
			<?php
		}
		else
		{
			?>
			<a href="/?lang=fr_FR">FR</a>
			<?php
		}
		?>
	</section>