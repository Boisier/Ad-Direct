<nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-between mb-3">
	<span class="navbar-brand"><?php echo \__("params"); ?></span>
</nav>
<?php
if(in_array("EDIT_PARAMS", $this->privileges) || in_array("MANAGE_CLIENTS", $this->privileges))
{
	?>
	<div class="list-group mt-3">
		<?php
		if(in_array("EDIT_PARAMS", $this->privileges))
		{
			?>
			<h5 class="list-group-item list-group-item-action" onclick="DLM.go('/param/display/globals/')">
				<i class="fa fa-cog fa-fw mr-3"></i><?php echo \__("globalParameters"); ?>
			</h5>
			<?php
		}
	
		if(in_array("EDIT_PARAMS", $this->privileges))
		{
			?>
			<h5 class="list-group-item list-group-item-action" onclick="DLM.go('/broadcastergroup/home/')">
				<i class="fa fa-users fa-fw mr-3"></i><?php echo \__("broadcasterGroups"); ?>
			</h5>
			<?php
		}
		?>
	</div>
	<?php
}

if(in_array("EDIT_SUPPORTS", $this->privileges))
{
	?>
	<div class="list-group mt-3">
		<h5 class="list-group-item list-group-item-action" onclick="DLM.go('/support/home/')">
			<i class="fa fa-television fa-fw mr-3"></i>&nbsp;<?php echo \__("displaySupports"); ?>
		</h5>
		<!--<h5 class="list-group-item list-group-item-action" onclick="DLM.go('/mediaType/home/')">
			<i class="fa fa-play-circle fa-fw mr-3"></i>&nbsp;<?php echo \__("mediaTypes"); ?>
		</h5>-->
	</div>
	<?php
}

$editAdmins = in_array("EDIT_ADMINS", $this->privileges);
$manageClients = in_array("MANAGE_CLIENTS", $this->privileges);

if($editAdmins || $manageClients)
{
	?>
	<div class="list-group mt-3">
        <?php
        if($editAdmins)
        {
	        ?>
            <h5 class="list-group-item list-group-item-action" onclick="DLM.go('/user/admins/')">
                <i class="fa fa-user fa-fw mr-3"></i>&nbsp;<?php echo \__("adminAccounts"); ?>
            </h5>
	        <?php
        }

        /*if($manageClients)
        {
	        ?>
            <h5 class="list-group-item list-group-item-action" onclick="DLM.go('/record/home/')">
                <i class="fa fa-list fa-fw mr-3"></i>&nbsp;<?php echo \__("clientsLogs"); ?>
            </h5>
	        <?php
        }*/
        ?>
	</div>
	<?php
}
?>
</div>