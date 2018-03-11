<?php
$dateFormat = \Library\Localization::dateFormat();

?>
<div class="card mt-4 ad-block bg-light" data-adid="<?php echo $this->adID ?>" id="adBlock<?php echo $this->adID ?>">
	<div class="card-body">
		<h3 class="playName"><?php echo $this->adName; ?>
        <?php
        if(\Library\User::isAdmin())
            echo " #".$this->adID;
        ?>
        </h3>
		<button type="button" class="btn btn-danger pull-right remove-btn" 
				onclick="DLM.modal('/ad/form/delete/<?php echo $this->adID ?>')">
			<i class="fa fa-trash"></i>
		</button>
		<hr>
		<div class="row">
			<div class="col-sm-6 playDates"
				 id="formGroup<?php echo $this->adID ?>">
				<div class="form-group">
					<label for="startDateAd<?php echo $this->adID ?>"><?php echo \__("adStartDate"); ?></label>
					<input type="text" 
						   id="startDateAd<?php echo $this->adID; ?>" 
						   class="form-control">
				</div>
				<div class="form-group">
					<label for="endDateAd<?php echo $this->adID ?>"><?php echo \__("adEndDate"); ?></label>
					<input type="text" 
						   id="endDateAd<?php echo $this->adID ?>" 
						   class="form-control">
				</div>
				<script type="text/javascript">

                    <?php $timezone = \Library\Localization::getCurrentTimezone(); ?>
					
                    $(function()
					{	
						var adID = <?php echo $this->adID ?>;

						var campaignStartDate = moment.tz("<?php echo date("c", $this->campaignStartDate); ?>", "<?php echo $timezone; ?>");
						var campaignEndDate = moment.tz("<?php echo date("c", $this->campaignEndDate); ?>", "<?php echo $timezone; ?>");

						var adStartDate = moment.tz("<?php echo date("c", $this->startDate); ?>", "<?php echo $timezone; ?>");
						var adEndDate = moment.tz("<?php echo date("c", $this->endDate); ?>", "<?php echo $timezone; ?>");

						var locale = "<?php echo \__("local"); ?>";

						$("#startDateAd"+adID).datetimepicker({
							defaultDate: adStartDate,
							minDate: campaignStartDate,
							maxDate: campaignEndDate,
							locale: locale,
							useCurrent: false,
							icons: {
								time: "fa fa-clock-o",
								date: "fa fa-calendar",
								up: "fa fa-arrow-up",
								down: "fa fa-arrow-down",
								previous: "fa fa-chevron-left",
								next: "fa fa-chevron-right"
							}
						});

						$("#endDateAd"+adID).datetimepicker({
							defaultDate: adEndDate,
							minDate: adStartDate,
							maxDate: campaignEndDate,
							locale: locale,
							useCurrent: false,
							icons: {
								time: "fa fa-clock-o",
								date: "fa fa-calendar",
								up: "fa fa-arrow-up",
								down: "fa fa-arrow-down",
								previous: "fa fa-chevron-left",
								next: "fa fa-chevron-right"
							}
						});

						$("#startDateAd"+adID).on("dp.change", function (e) 
						{
							$("#endDateAd"+adID).data("DateTimePicker").minDate(e.date);
						}).on("dp.hide", function (e) 
						{
							DLM.execute('/ad/update/startDate/<?php echo $this->adID ?>', {startDate: e.date.unix()}, false);
						});

						$("#endDateAd"+adID).on("dp.change", function (e) 
						{
							$("#startDateAd"+adID).data("DateTimePicker").maxDate(e.date);
						}).on("dp.hide", function (e) {
							DLM.execute('/ad/update/endDate/<?php echo $this->adID ?>', {endDate: e.date.unix()}, false);
						});
					});
				</script>
			</div><!--whitespace
		 --><div class="col-sm-6 my-auto">
				<?php 
				switch($this->review['status'])
				{
					case \Controllers\ReviewController::AD_INCOMPLETE:
						$msg = "adStatus-incomplete";
						$icon = "minus";
						$iconColor = "muted";
					break;
					case \Controllers\ReviewController::AD_PENDING:
						$msg = "adStatus-pending";
						$icon = "clock-o";
						$iconColor = "warning";
					break;
					case \Controllers\ReviewController::AD_REJECTED:
						$msg = "adStatus-rejected";
						$icon = "times";
						$iconColor = "danger";
					break;
					case \Controllers\ReviewController::AD_APPROVED:
					case \Controllers\ReviewController::AD_AUTO_APPROVED:
						$msg = "adStatus-approved";
						$icon = "check";
						$iconColor = "success";
					break;
					default;
						$msg = "adStatus-unknown";
						$icon = "question";
						$iconColor = "muted";
				}
									
				?>
				<div class="row">
					<div class="col-sm-8 my-auto text-center">
						<h5 class="playStatusTitle"><?php echo \__($msg); ?></h5>
						<?php
						if(!empty($this->review['comment']))
						{
							?><small class="test-muted"><?php echo $this->review['comment']; ?></small><?php
						}
						?>
					</div><!--whitespace
				 --><div class="col-sm-4">
						<i class="fa fa-<?php echo $icon; ?> fa-4x text-<?php echo $iconColor; ?>"></i>
					</div>
				</div>
				<?php
			
				if($this->review['status'] == \Controllers\ReviewController::AD_PENDING && $this->canReview)
				{
					?>
					<div class="row mt-4">
						<div class="col-sm-12 my-auto text-center">
							<button type="button" class="btn btn-outline-success" onclick="DLM.modal('/review/form/review/<?php echo $this->adID; ?>/approve/')">
								<?php echo \__("approve"); ?>
							</button>
							<button type="button" class="btn btn-outline-danger" onclick="DLM.modal('/review/form/review/<?php echo $this->adID; ?>/reject/')">
								<?php echo \__("reject"); ?>
							</button>
						</div>
					</div>
					<?php
				}
				else
				{
				}
				?>
			</div>
		</div>
		<hr>
		<div class="row screenList">
			<?php echo $this->screens; ?>
		</div>
		<?php echo $this->details; ?>
	</div>
</div>