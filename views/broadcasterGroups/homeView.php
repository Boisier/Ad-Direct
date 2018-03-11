<nav class="breadcrumb bg-white py-0">
  	<a class="breadcrumb-item" href="#" onclick="event.preventDefault(); DLM.go('/home/params/')">
	  <?php echo \__("adminMenu-Params"); ?>
	</a>
  	<span class="breadcrumb-item active"><?php echo \__("broadcasterGroups"); ?></span>
</nav>
<nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-between mb-3">
	<span class="navbar-brand"><?php echo \__("broadcasterGroups"); ?></span>
</nav>
<div class="text-right">
	<button class="btn btn-outline-secondary my-4 mr-0" type="button" onclick="DLM.go('/broadcastergroup/form/create')">
		<?php echo \__("addBroadcasterGroup"); ?>
	</button>
</div>
<?php
if(count($this->broadcasterGroups) == 0)
{
	?>
	<div class="text-center font-weight-bold my-5">
		<?php echo \__("noBroadcasterGroups"); ?>
	</div>
	<?php
	return;
}
?>
<div class="list-group mt-3">
	<?php
	foreach($this->broadcasterGroups as $group)
	{
		?>
		<h5 class="list-group-item list-group-item-action" onclick="DLM.go('/broadcastergroup/form/rename/<?php echo $group['ID']; ?>')">
			<?php echo $group['name']; ?>
		</h5>
		<?php
	}
	?>
</div>