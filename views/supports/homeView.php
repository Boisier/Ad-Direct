<nav class="breadcrumb bg-white py-0">
  <a class="breadcrumb-item" href="#" onclick="event.preventDefault(); DLM.go('/home/params/')"><?php echo \__("adminMenu-Params"); ?></a>
  <span class="breadcrumb-item active"><?php echo \__('displaySupports'); ?></span>
</nav>
<nav class="navbar navbar-light bg-light">
	<div class="navbar-brand"><?php echo \__("displaySupports"); ?></div>
</nav>
<div class="text-right">
	<button class="btn btn-outline-secondary my-4 mr-0" type="button" onclick="DLM.go('/support/form/create')">
		<?php echo \__("addSupport"); ?>
	</button>
</div>
<div class="support-list">
	<?php echo $this->supportList; ?>
</ul>