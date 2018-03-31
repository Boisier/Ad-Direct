<a href="#" onclick="event.preventDefault(); DLM.go('/broadcaster/display/<?php echo $this->broadcasterID; ?>')"
   class="list-group-item list-group-item-action broadcaster all-broadcaster <?php echo $this->groupID; ?>-broadcaster">
	<div class="d-flex w-100 justify-content-between">
		<h5 class="card-title mb-0">
			<?php echo $this->broadcasterName; ?>
		</h5>
		<span>
			<small class="mr-5">
				<i class="fa fa-user"></i>&nbsp;&nbsp;
				<?php
				echo $this->nbrClients." ";
				echo $this->nbrClients > 1 ? \__("clients") : \__("client");
				?>
			</small>
			<small>
				<i class="fa fa-bullhorn"></i>&nbsp;&nbsp;
				<?php
				echo $this->nbrCampaigns." ";
				echo $this->nbrCampaigns > 1 ? \__("campaigns") : \__("campaign");
				?>
			</small>
			<?php
            $class = "badge badge-pill ml-2 ";

            switch($this->status)
            {
                case \Objects\Broadcaster::BROADCASTER_STATUS_PLAYING:
                    $class .= "badge-success";
                break;
                case \Objects\Broadcaster::BROADCASTER_STATUS_PENDING:
                    $class .= "badge-warning";
                break;
                default;
                    $class .= "badge-danger";
            }
            ?>
            <span class="<?php echo $class; ?>" title="<?php echo \__("pendingAds"); ?>">&nbsp;</span>
		</span>
	</div>
</a>
