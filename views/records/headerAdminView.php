<nav class="breadcrumb bg-white py-0">
	<a class="breadcrumb-item" href="#" onclick="event.preventDefault(); DLM.go('/param/')"><?php echo \__("adminMenu-Params"); ?></a>
	<a class="breadcrumb-item" href="#" onclick="event.preventDefault(); DLM.go('/user/admins/')"><?php echo \__("adminAccounts"); ?></a>
	<span class="breadcrumb-item active"><?php echo $this->userName ?></span>
</nav>