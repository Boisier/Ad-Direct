<hr>
<div class="ad-stats">
	<h5 class="mb-0"><?php echo \__("creatives"); ?></h5>
	<div class="ad-creatives-state mb-3">
	<?php
		if($this->creativesStates == "")
			echo \__("noCreatives"); 
		else
			echo $this->creativesStates;
		?>
	</div>
	<h5><?php echo \__("stats"); ?></h5>
	<div class="row">
		<div class="col-sm-4">
			<?php echo \__("printsTotal"); ?>
		</div>
		<div class="col-sm-8">
			<strong><?php echo $this->adPrintsTotal;?></strong>
		</div>
	</div>
</div>