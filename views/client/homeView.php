
<section class="content client">
	<nav>
        <div class="nav-btn icon" id="overviewBtn">
            <a href="#overview">
                <span class="nav-logo"><i class="fa fa-compass"></i></span>
                <span class="nav-label"><?php echo \__("adminMenu-Overview"); ?></span>
            </a>
        </div>
		<?php
		foreach($this->campaigns as $campaign)
		{
			?>
			<div class="nav-btn compact" id="<?php echo $campaign->getID(); ?>Btn">
				<a href="#<?php echo $campaign->getID(); ?>">
					<span class="nav-label"><?php echo $campaign->getName(); ?></span>
					<small class="nav-sublabel"><?php echo $campaign->getSupport()->getName(); ?></small>
				</a>
			</div>
			<?php
		}
		?>
	</nav><!--
 --><section class="corps client">
		<div id="mainContainer" style="display:none;">

		</div>
		<div id="loadContainer" style="display:none;">
			<i class="fa fa-refresh fa-spin fa-4x"></i>
		</div>
	</section>
</section>