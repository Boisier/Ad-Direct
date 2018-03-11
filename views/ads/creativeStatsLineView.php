<div class="row">
	<div class="col-sm-2">
		<strong><?php echo $this->screenName; ?></strong>
	</div>
	<div class="col-sm-10">
		<?php echo \__($this->creativeStatus);
		if($this->creativeStatus == "creativeStatus-4")
		    echo " ({$this->conversionStatus})";
		?>
	</div>
</div>
<div class="row mb-3">
	<small class="col-sm-12">
		<?php echo \__("uploadCaption", ["uploader" => $this->creativeUploader,
										 "uploadTime" => $this->creativeUploadTime]);?>
	</small>
</div>