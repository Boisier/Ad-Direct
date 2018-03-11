<h2 style="font-weight:bold;
		   font-size:20px">
	<?php echo \__("clientLoginUpdateTitle"); ?>
</h2>
<p><?php echo \__("clientLoginUpdateMessage"); ?></p>
<table style="margin: 50px 0;
			  font-size:20px;
			  border-collapse: collapse;">
	<tr>
		<td style="width:300px;
				   text-align:right;
				   padding:0 5px 0 0;">
			<?php echo \__("username"); ?>
		</td>
		<td style="width:300px;
				   font-weight: bold;
				   padding:0 0 0 5px;">
			<?php echo $this->clientEmail; ?>
		</td>
	</tr>
	<tr>
		<td style="width:300px;
				   text-align:right;
				   padding:15px 5px 0 0;">
			<?php echo \__("password"); ?>
		</td>
		<td style="width:300px;
				   font-weight: bold;
				   padding:15px 0 0 5px;">
			*****
		</td>
	</tr>
</table>