<nav class="breadcrumb bg-white py-0">
	<a class="breadcrumb-item" href="#" onclick="event.preventDefault(); DLM.go('/home/clients/')"><?php echo \__("adminMenu-Clients"); ?></a>
	<a class="breadcrumb-item" href="#" onclick="event.preventDefault(); DLM.go('/broadcaster/display/<?php echo $this->broadcasterID; ?>/clients/')"><?php echo $this->broadcasterName ?></a>
	<span class="breadcrumb-item active"><?php echo $this->userName ?></span>
</nav>