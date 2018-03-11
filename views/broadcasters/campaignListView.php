<a class="list-group-item list-group-item-action" href="#" onclick="event.preventDefault(); DLM.go('/campaign/display/<?php echo $this->campaignID; ?>')">
	<div class="row align-items-center py-2">
		<div class="col-sm-7">
			<h5 class="mb-0 font-weight-bold"><?php echo $this->campaignName; ?></h5>
			<small><?php echo $this->supportName; ?></small>
			<?php
				if($this->pending > 0)
				{
					?>
					<span class="badge badge-pill badge-warning ml-1" title="<?php echo \__("pendingAds"); ?>"><?php echo $this->pending; ?></span>
					<?php
				}
			?>
		</div>
		<div class="col-sm-5">
			<span class="dates">
				<?php 
				$dateFormat = \Library\Localization::dateFormat();
	
				echo date($dateFormat, $this->startDate);
				echo "<br>";
				echo date($dateFormat, $this->endDate);
				?>
			</span>
		</div>
	</div> 
</a>