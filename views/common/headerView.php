<body>
	<header id="topBar">
		<div id="logoTop"><span></span></div>
		<div id="helloBox"><?php echo \__("helloMsg", ["userName" => $this->userName]); ?></div>
		<div id="actionsTop">
			<?php
            if(!\Library\User::isAdmin())
            {
                ?>
                <a href="#" onclick="event.preventDefault(); DLM.go('/home/legals/terms')" id="languageSwitch" class="headerLink"><?php echo \__("termsMsg"); ?></a><!--whitespace
             --><a href="#" onclick="event.preventDefault(); DLM.go('/home/legals/specs')" id="languageSwitch" class="headerLink"><?php echo \__("specsMsg"); ?></a><!--whitespace
             --><a href="#" onclick="event.preventDefault(); DLM.go('/home/legals/creatives')" id="languageSwitch" class="headerLink"><?php echo \__("creativeMsg"); ?></a><!--whitespace
             --><?
            }
            
			if(\__("lang") == "fr")
			{
				?><a href="/?lang=en_EN" id="languageSwitch" class="headerLink">EN</a><?php
			}
			else
			{
				?><a href="/?lang=fr_FR" id="languageSwitch" class="headerLink">FR</a><?php
			}
			?><a id="logoutBtn" class="headerLink" href="/auth/logout/"><i class="fa fa-power-off"></i></a>
		</div>
	</header>