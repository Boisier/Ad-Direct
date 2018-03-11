<div class="text-right my-3">
	<button class="btn btn-outline-secondary my-4 mr-0" type="button" onclick="DLM.go('/campaign/form/add/<?php echo $this->broadcasterID; ?>')">
		<?php echo \__("addCampaign"); ?>
	</button>
</div>
<div class="list-group">
	<?php echo $this->campaignList; ?>
</div>