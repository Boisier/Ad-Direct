<h2 style="font-weight:bold;
		   font-size:20px">
	<?php echo \__("campaignUpdateTitle"); ?>
</h2>
<p><?php echo \__("campaignUpdateMessage", ["broadcasterName" => $this->broadcasterName,
												   "campaignName" => $this->campaignName]); ?></p>
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
				   padding:15px 0 0 5px;">
			<?php echo date($dateFormat, $this->campaignStartDate); ?>
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
			<?php echo date($dateFormat, $this->campaignEndDate); ?>
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
<p>
	<strong><?php echo \__("displayFile"); ?></strong><br>
	<a href="<?php echo $this->displayFileURL; ?>"><?php echo $this->displayFileURL; ?></a>
</p>