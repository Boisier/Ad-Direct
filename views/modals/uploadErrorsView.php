<div class="modal fade" tabindex="-1" role="dialog" id="mainModal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">
					<?php echo \__("ErrorOnUploadModal-title"); ?>
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p><?php echo \__("ErrorOnUploadModal-message"); ?></p>
				<?php
				
				foreach($this->errors as $error)
				{
					switch($error)
					{
						case "ERROR_UPLOAD":
							$title = \__("uploadError-title");
							$body  = \__("uploadError-message");
						break;
						case "TOO_HEAVY":
							$title = \__("creativeTooHeavy-title");
							$body  = \__("creativeTooHeavy-message", ["maxSize" => $this->specs['sizeLimit']]);
						break;
						case "WRONG_MIME":
							$title = \__("badFormat-title");
							$body  = \__("badFormat-message", ["supportedMimes" => join(", ", $this->specs['supportedMimes'])]);
						break;
						case "BAD_DIMENSIONS":
							$title = \__("badDimensions-title");
							$body  = \__("badDimensions-message", ["height" => $this->specs['height'], "width" => $this->specs['width']]);
						break;
						case "BAD_FRAMERATE":
							$title = \__("badFramerate-title");
							$body  = \__("badFramerate-message", ["acceptedFramerates" => join(", ", \Library\Params::get("ACCEPTED_FRAMERATES"))]);
						break;
						case "BAD_CODEC":
							$title = \__("badCodec-title");
							$body  = \__("badCodec-message", ["acceptedCodecs" => join(", ", \Library\Params::get("AUTHORIZED_CODECS"))]);
						break;
						case "TOO_LONG":
							$title = \__("videoTooLong-title");
							$body  = \__("videoTooLong-message", ["maxDuration" => $this->specs['displayDuration']]);
						break;
						default;
							$title = \__("unknownErrorOnUpload-title");
							$body  = \__("unknownErrorOnUpload-message");
					}
					
					?>
					<p>
						<strong><?php echo $title; ?></strong><br>
						<?php echo $body; ?>
					</p>
					<br>
					<?php
				}
				?>
				<p><?php echo \__("ErrorOnUploadModal-contactUs"); ?></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">
					<?php echo \__("close"); ?>
				</button>
			</div>
		</div>
	</div>
</div>