<a href="#" onclick="event.preventDefault(); DLM.go('/broadcaster/display/<?php echo $this->broadcasterID; ?>/clients/')" 
   class="list-group-item list-group-item-action">
	<h5 class="mb-0 font-weight-bold"><?php echo $this->clientName; ?></h5>
	<small><?php echo $this->clientEmail; ?></small>
</a>