<h2 style="font-weight:bold;
		   font-size:20px">
	<?php echo \__("endOfBroadcastTitle"); ?>
</h2>
<?php 
	$dateFormat = \Library\Localization::dateFormat();
	$endDate = date($dateFormat, $this->adEndDate)
?>
<p><?php echo \__("endOfBroadcastMessage", ["campaignName" => $this->campaignName, "endDate" => $endDate]); ?></p>

