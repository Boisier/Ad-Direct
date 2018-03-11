<div class="card mt-3">
	<div class="card-body">
		<h4 class="card-title"><?php echo $this->adminName; ?></h4>
		<h6 class="card-subtitle mb-2 text-muted"><?php echo $this->adminEmail; ?></h6>
	</div>
	<ul class="list-group list-group-flush">
		<?php
		//Clients
		$class = "list-group-item-danger";
	
		if(in_array("MANAGE_CLIENTS", $this->privileges))
			$class = "list-group-item-success";
		
		?><li class="list-group-item <?php echo $class; ?>"><?php echo \__("manageClientsPrivilege"); ?></li><?php
		
		
		//Pending
		$class = "list-group-item-danger";
	
		if(in_array("APPROVE_CREATIVES", $this->privileges))
			$class = "list-group-item-success";
		
		?><li class="list-group-item <?php echo $class; ?>"><?php echo \__("approveCreativesPrivilege"); ?></li><?php
		
		
		//Globals
		$class = "list-group-item-danger";
	
		if(in_array("EDIT_PARAMS", $this->privileges))
			$class = "list-group-item-success";
		
		?><li class="list-group-item <?php echo $class; ?>"><?php echo \__("editParamsPrivilege"); ?></li><?php
		
		
		//Supports
		$class = "list-group-item-danger";
	
		if(in_array("EDIT_SUPPORTS", $this->privileges))
			$class = "list-group-item-success";
		
		?><li class="list-group-item <?php echo $class; ?>"><?php echo \__("editSupportsPrivilege"); ?></li><?php
		
		
		//Admins management
		$class = "list-group-item-danger";
	
		if(in_array("EDIT_ADMINS", $this->privileges))
			$class = "list-group-item-success";
		
		?><li class="list-group-item <?php echo $class; ?>"><?php echo \__("editAdminsPrivilege"); ?></li><?php

		
		//Creatives broadcasteing
		$class = "list-group-item-danger";
	
		if(in_array("BROADCAST_ADMIN", $this->privileges))
			$class = "list-group-item-success";
		
		?><li class="list-group-item <?php echo $class; ?>"><?php echo \__("broadcastCreativesPrivilege"); ?></li><?php

		?>
	</ul>
	<div class="card-body">
		<a href="#" class="card-link" onclick="event.preventDefault(); DLM.go('record/userlogs/<?php echo $this->adminID ?>')"><?php echo \__("logs"); ?></a>
		<a href="#" class="card-link" onclick="event.preventDefault(); DLM.go('user/form/editadmin/<?php echo $this->adminID ?>')"><?php echo \__("edit"); ?></a>
		<?php
		if($this->adminID != 1)
		{
			?>
			<a href="#" class="card-link" onclick="event.preventDefault(); DLM.modal('user/form/deleteadmin/<?php echo $this->adminID ?>')"><?php echo \__("delete"); ?></a>
			<?php
		}
		?>
	</div>
</div>