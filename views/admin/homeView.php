<section class="content">
	<nav>
		<div class="nav-btn icon" id="overviewBtn">
			<a href="#overview">
				<span class="nav-logo"><i class="fa fa-compass"></i></span>
				<span class="nav-label"><?php echo \__("adminMenu-Overview"); ?></span>
			</a>
		</div>
		<?php
		if(in_array("MANAGE_CLIENTS", $this->privileges))
		{
			?>
			<div class="nav-btn icon" id="clientsBtn">
				<a href="#clients">
					<span class="nav-logo"><i class="fa fa-bullhorn"></i></span>
					<span class="nav-label"><?php echo \__("adminMenu-Clients"); ?></span>
				</a>
			</div>
			<?php
		}

		if(in_array("EDIT_PARAMS", $this->privileges) || 
		   in_array("EDIT_SUPPORTS", $this->privileges) ||
		   in_array("ADIT_ADMINS", $this->privileges) ||
		   in_array("MANAGE_CLIENTS", $this->privileges))
		{
			?>
			<div class="nav-btn icon" id="paramsBtn">
				<a href="#params">
					<span class="nav-logo"><i class="fa fa-cog"></i></span>
					<span class="nav-label"><?php echo \__("adminMenu-Params"); ?></span>
				</a>
			</div>
			<?php
		}
		?>
	</nav><!--whitespace
 --><section class="corps">
		<div id="mainContainer" style="display:none;">

		</div>
		<div id="loadContainer" style="display:none;">
			<i class="fa fa-refresh fa-spin fa-4x"></i>
		</div>
	</section>
</section>