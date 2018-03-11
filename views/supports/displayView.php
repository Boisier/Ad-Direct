<nav class="breadcrumb bg-white py-0">
  <a class="breadcrumb-item" href="#" onclick="event.preventDefault(); DLM.go('/home/params/')"><?php echo \__("adminMenu-Params"); ?></a>
  <a class="breadcrumb-item" href="#" onclick="event.preventDefault(); DLM.go('/support/home/')"><?php echo \__("displaySupports"); ?></a>
  <span class="breadcrumb-item active"><?php echo $this->supportName ?></span>
</nav>
<nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-between mb-3">
	<span class="navbar-brand"><?php echo $this->supportName ?></span>
	<ul class="navbar-nav">
		<li class="nav-item">
        	<a class="nav-link" href="#" onclick="event.preventDefault(); DLM.go('/support/form/rename/<?php echo $this->supportID; ?>')">
				<span><?php echo \__("rename"); ?></span>
			</a>
      	</li>
		<li class="nav-item">
        	<a class="nav-link" href="#" onclick="event.preventDefault(); DLM.modal('/support/form/delete/<?php echo $this->supportID; ?>')">
				<span><?php echo \__("delete"); ?></span>
			</a>
      	</li>
	</ul>
</nav>
<?php echo $this->screens; ?>
<div class="bigParaph" id="addAdBtn">
	<button type="button" class="btn btn-outline-secondary" onclick="DLM.go('/screen/form/add/<?php echo $this->supportID; ?>')">
		<?php echo \__("addScreen"); ?>
	</button>
</div>