<?php
$panelClass = $this->clientLive ? "border-primary" : "border-warning";
?>
<div class="card mt-3 <?php echo $panelClass; ?>">
	<div class="card-body">
    	<h4 class="card-title"><?php echo $this->clientName; ?></h4>
    	<h6 class="card-subtitle mb-3 text-muted"><a href="mailto:<?php echo $this->clientEmail; ?>"><?php echo $this->clientEmail; ?></a></h6>
    	<a href="#" class="card-link text-secondary" onclick="event.preventDefault(); DLM.go('/user/form/editClient/<?php echo $this->clientID; ?>')">
			<?php echo \__("edit"); ?>
	 	</a>
		<a href="#" class="card-link text-secondary" onclick="event.preventDefault(); DLM.go('/user/toggle/<?php echo $this->clientID; ?>');">
			 <?php echo $this->clientLive ? \__("deactivate") : \__("activate"); ?>
		</a>
        <a href="#" class="card-link text-secondary" onclick="event.preventDefault(); DLM.go('record/userlogs/<?php echo $this->clientID; ?>')">
			<?php echo \__("logs"); ?>
        </a>
		<a href="#" class="card-link text-secondary"onclick="event.preventDefault(); DLM.modal('user/form/deleteclient/<?php echo $this->clientID; ?>')">
			<?php echo \__("delete"); ?>
		</a>
  	</div>
</div>
