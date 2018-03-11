<h1>
	<?php echo \__("errorWhileDeletingSupport"); ?>
</h1>
<p>
	<?php echo \__("errorWhileDeletingSupport-message"); ?>
</p>
<div class="input-group clearfix pull-right">
	<button type="button" class="btn btn-default " onclick="DLM.go('/support/display/<?php echo $this->supportID; ?>')">
		<?php echo \__("goBack"); ?>
	</button>
</div>