<div class="card mt-3">
	<div class="card-body">
		<h4 class="card-title"><?php echo $this->screenName; ?></h4>
		<h6 class="card-subtitle mb-2 text-muted"><?php echo "{$this->screenWidth} x {$this->screenHeight}"; ?></h6>
		<a href="#" class="card-link" onclick="event.preventDefault(); DLM.go('/screen/form/edit/<?php echo $this->screenID; ?>')"><?php echo \__("edit"); ?></a>
    	<a href="#" class="card-link"onclick="event.preventDefault(); DLM.modal('/screen/form/delete/<?php echo $this->screenID; ?>')"><?php echo \__("delete"); ?></a>
	</div>
</div>