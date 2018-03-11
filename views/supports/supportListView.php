<?php
if($this->reason == "supports")
{
	$link = "/support/display/{$this->supportID}";
}
else if($this->reason == "defaultAds")
{
	$link = "/defaultad/display/{$this->broadcasterID}/{$this->supportID}/";
}
?>
<div class="card text-center support-block link" onclick="DLM.go('<?php echo $link; ?>')">
	<div class="card-body">
		<h3 class="card-title"><?php echo $this->supportName; ?></h3>
		<?php
		$iconURL = "assets/images/supportsIcons/{$this->supportID}.png";

		if(file_exists($iconURL))
		{
			?>
			<div class="supportIcon" style="background-image:url(<?php echo $iconURL; ?>)"></div>
			<?php
		}
		?>
		<span class="screenNbr">
			<?php 
			if($this->screenNbr == 0)
				echo \__("noScreen");
			else if($this->screenNbr == 1)
				echo \__("screenInSupportBox", ["screenNbr" => $this->screenNbr]);
			else
				echo \__("screensInSupportBox", ["screenNbr" => $this->screenNbr]);
			?>
		</span>
	</div>
</div>