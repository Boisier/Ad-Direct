<div class="modal fade" tabindex="-1" role="dialog" id="mainModal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">
					<?php echo \__("deleteScreen", ["screenName" => $this->screenName]); ?>
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p><?php echo \__("deleteScreen-warningMessage", ["screenName" => $this->screenName]); ?></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal" 
						onclick="DLM.go('/screen/delete/<?php echo $this->screenID; ?>')">
					<?php echo \__("delete"); ?>
				</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">
					<?php echo \__("cancel"); ?>
				</button>
			</div>
		</div>
	</div>
</div>