<div class="modal fade" tabindex="-1" role="dialog" id="mainModal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">
					<?php echo \__("{$this->action}ReviewAd"); ?>
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" name="reviewAd" action="/review/validate/">
					<textarea class="form-control" rows="3" name="comment" id="reviewComment" placeholder="<?php echo \__("reviewComment"); ?>"></textarea>
					<div class="form-group row mt-4">
						<div class="col-sm-12 text-right">
							<input type="hidden" name="action" value="<?php echo $this->action; ?>">
							<input type="hidden" name="adID" value="<?php echo $this->adID; ?>">
							<button type="button" class="btn <?php echo $this->action == "approve" ? "btn-success" : "btn-danger"; ?>" data-dismiss="modal" 
									onclick="event.preventDefault(); DLM.sendForm('reviewAd')">
								<?php echo \__($this->action); ?>
							</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">
								<?php echo \__("cancel"); ?>
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>