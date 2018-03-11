<?php
//To be able to reuse the javascript used in the campaigns, the broadcasterID will be known here as adID.
//This is is just a change of name. The JS script being actually working with combo IDs
?>
<div class="card mt-4 ad-block bg-light" id="adBlock<?php echo $this->broadcasterID ?>" data-adid="<?php echo $this->broadcasterID ?>">
	<div class="card-body">
		<h3 class="card-title"><?php echo $this->displayName; ?></h4>
		<hr>
		<div class="row screenList">
			<?php echo $this->screens; ?>
		</div>
	</div>
</div>