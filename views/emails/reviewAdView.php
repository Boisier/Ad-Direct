<h2 style="font-weight:bold;
		   font-size:20px">
	<?php echo \__("adToReviewTitle"); ?>
</h2>
<p><?php echo \__("adToReviewMessage", ["clientName" => $this->clientName, "campaignName" => $this->campaignName]); ?></p>
<?php 
	$dateFormat = \Library\Localization::dateFormat();
?>
<table style="margin: 50px 0;
			  font-size:20px;
			  border-collapse: collapse;">
	<tr>
		<td style="width:300px;
				   text-align:right;
				   padding:0 5px 0 0;">
			<?php echo \__("adStartDate"); ?>
		</td>
		<td style="width:300px;
				   font-weight: bold;
				   padding:0 0 0 5px;">
			<?php echo date($dateFormat, $this->adStartDate); ?>
		</td>
	</tr>
	<tr>
		<td style="width:300px;
				   text-align:right;
				   padding:15px 5px 0 0;">
			<?php echo \__("adEndDate"); ?>
		</td>
		<td style="width:300px;
				   font-weight: bold;
				   padding:15px 0 0 5px;">
			<?php echo date($dateFormat, $this->adEndDate); ?>
		</td>
	</tr>
	<tr>
		<td style="width:300px;
				   text-align:right;
				   padding:15px 5px 0 0;">
			<?php echo \__("displaySupport"); ?>
		</td>
		<td style="width:300px;
				   font-weight: bold;
				   padding:15px 0 0 5px;">
			<?php echo $this->supportName; ?>
		</td>
	</tr>
</table>
<p><?php echo \__("adToReviewFooter"); ?></p>