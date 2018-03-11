<body class="login">
    <div id="backgroundOverlay"></div>
	<div id="fullPageBlock">
		<div id="loginBox">
			<div class="blocContainer">
				<img src="/assets/images/logo.png" id="bigLogo">
			</div>
			<div class="blocContainer wide">
				<h2 class="accessTitle mb-5 mt-4"><?php echo \__("termsOfUse"); ?></h2>
				<?php
                if(\__("lang") == "fr")
                    $term = "TERMS_OF_USE_FR";
                else
                    $term = "TERMS_OF_USE_EN";
                    
				echo \Library\Params::get($term);
				?>
                <div class="row text-center pr-5 pl-5 pt-3">
                        <button type="button" class="btn btn-outline-primary btn-block" onclick="location.href='/user/approvelegals/<?php echo $this->userID ?>'">
                            <?php echo \__("acceptLegalsBtn") ?>
                        </button>
                </div>
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