<h1><?php echo \__("editParam"); ?></h1>
<div class="alert alert-danger form-alert" role="alert" id="missingFieldError" style="display:none">
	<?php echo \__("missingFieldError"); ?>
</div>
<div class="alert alert-danger form-alert" role="alert" id="fatalErrorError" style="display:none">
	<?php echo \__("fatalError"); ?>
</div>
<form class="form-horizontal clearfix" name="editGlobal" action="/param/update/global/">
	<div class="form-group row">
		<label for="paramLocalName" class="col-sm-4 col-form-label"><?php echo \__("parameterID"); ?></label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="paramLocalName" id="paramLocalName" disabled value="<?php echo \__($this->param['name']); ?>">
		</div>
	</div>
			<?php
			switch($this->param['type'])
			{
				case "text";
					?>
					<div class="form-group row">
						<label for="paramValue" class="col-sm-4 col-form-label"><?php echo \__("paramValueField") ; ?></label>
						<div class="col-sm-8">
							<textarea class="form-control" name="paramValue" id="paramValue" autofocus><?php 
								echo $this->paramValue; 
						  ?></textarea>
						</div>
					</div>
					<script>
                        $('#paramValue').summernote('destroy');
                        
                        $(document).ready(function() {
                            $('#paramValue').summernote({
                                toolbar: [
                                    ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
                                    ['font', ['superscript', 'subscript']],
                                    ['para', ['ul', 'ol', 'paragraph']]
                                ]
                            });
                        });
					</script>
					<?php
				break;
				case "rawtext":
					?>
					<div class="form-group row">
						<label for="paramValue" class="col-sm-4 col-form-label"><?php echo \__("paramValueField") ; ?></label>
						<div class="col-sm-8">
							<textarea class="form-control" name="paramValue" id="paramValue" autofocus data-toggle="tooltip" data-placement="top" title="<?php echo \__("oneElementPerLine"); ?>"><?php 
								echo $this->paramValue; 
						  ?></textarea>
						</div>
					</div>
					<script type="text/javascript">
						$(function () {
						  $('[data-toggle="tooltip"]').tooltip()
						})
					</script>
					<?php
				break;
				case "list":
					?>
					<div class="form-group row">
						<label for="paramValue" class="col-sm-4 col-form-label"><?php echo \__("paramValuesField") ; ?></label>
						<div class="col-sm-8">
							<div class="vertical-input-group" id="paramValueItemsContainer">
								<?php
								foreach($this->paramValue as $i => $item)
								{
									?>
									<div class="input-group" class="listElement" id="paramListItem<?php echo $i; ?>Display">
										<input type="text" class="form-control" disabled value="<?php echo $item; ?>">
										<span class="input-group-btn">
											<button class="btn btn-outline-secondary removeBtn" type="button" data-listitem="paramListItem<?php echo $i; ?>">
												<i class="fa fa-times fa-fw"></i>
											</button>
										</span>
									</div>
									<?php
								}
								?>
							</div>
						</div>
					</div>
					<div class="form-group row">
						<label for="paramValueInputField" class="col-sm-4 col-form-label">
							<?php echo \__("addItemToParam"); ?>
						</label>
						<div class="col-sm-8">
							<div class="input-group">
								<input type="text" class="form-control" id="paramValueInputField" placeholder="<?php echo __("value"); ?>">
								<span class="input-group-btn">
									<button class="btn btn-outline-secondary" type="button" id="paramValueAddButton">
										<i class="fa fa-check fa-fw"></i>
									</button>
								</span>
							</div>
						</div>
					</div>
					<?php
				break;
				case "int":
					?>
					<div class="form-group row">
						<label for="paramValue" class="col-sm-4 col-form-label"><?php echo \__("paramValuesField") ; ?></label>
						<div class="col-sm-8">
							<input type="number" class="form-control" name="paramValue" value="<?php echo $this->param['value']; ?>">
						</div>
					<div>
					<?php
				break;
				case "duration":
					?>
					<div class="form-group row">
						<label for="paramValue" class="col-sm-4 col-form-label"><?php echo \__("years") ; ?></label>
						<div class="col-sm-8">
							<select class="form-control" name="paramValue[]">
								<?php
								for($i = 0; $i <= 100; $i++)
								{
									?><option value="<?php echo $i; ?>" 
									  <?php echo $i == $this->paramValue["years"] ? "selected" : ""; ?>>
										<?php echo $i; ?>
									</option><?php
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label for="paramValue" class="col-sm-4 col-form-label"><?php echo \__("months") ; ?></label>
						<div class="col-sm-8">
							<select class="form-control" name="paramValue[]">
								<?php
								for($i = 0; $i <= 11; $i++)
								{
									?><option value="<?php echo $i; ?>" 
									  <?php echo $i == $this->paramValue["months"] ? "selected" : ""; ?>>
										<?php echo $i; ?>
									</option><?php
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label for="paramValue" class="col-sm-4 col-form-label"><?php echo \__("weeks") ; ?></label>
						<div class="col-sm-8">
							<select class="form-control" name="paramValue[]">
								<?php
								for($i = 0; $i <= 5; $i++)
								{
									?><option value="<?php echo $i; ?>" 
									  <?php echo $i == $this->paramValue["weeks"] ? "selected" : ""; ?>>
										<?php echo $i; ?>
									</option><?php
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label for="paramValue" class="col-sm-4 col-form-label"><?php echo \__("days") ; ?></label>
						<div class="col-sm-8">
							<select class="form-control" name="paramValue[]">
								<?php
								for($i = 0; $i <= 6; $i++)
								{
									?><option value="<?php echo $i; ?>" 
									  <?php echo $i == $this->paramValue["days"] ? "selected" : ""; ?>>
										<?php echo $i; ?>
									</option><?php
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label for="paramValue" class="col-sm-4 col-form-label"><?php echo \__("hours") ; ?></label>
						<div class="col-sm-8">
							<select class="form-control" name="paramValue[]">
								<?php
								for($i = 0; $i <= 24; $i++)
								{
									?><option value="<?php echo $i; ?>" 
									  <?php echo $i == $this->paramValue["hours"] ? "selected" : ""; ?>>
										<?php echo $i; ?>
									</option><?php
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label for="paramValue" class="col-sm-4 col-form-label"><?php echo \__("minutes") ; ?></label>
						<div class="col-sm-8">
							<select class="form-control" name="paramValue[]">
								<?php
								for($i = 0; $i <= 59; $i++)
								{
									?><option value="<?php echo $i; ?>" 
									  <?php echo $i == $this->paramValue["minutes"] ? "selected" : ""; ?>>
										<?php echo $i; ?>
									</option><?php
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label for="paramValue" class="col-sm-4 col-form-label"><?php echo \__("seconds") ; ?></label>
						<div class="col-sm-8">
							<select class="form-control" name="paramValue[]">
								<?php
								for($i = 0; $i <= 59; $i++)
								{
									?><option value="<?php echo $i; ?>" 
									  <?php echo $i == $this->paramValue["seconds"] ? "selected" : ""; ?>>
										<?php echo $i; ?>
									</option><?php
								}
								?>
							</select>
						</div>
					</div>
					<?php
				break;
			}
			?>
		</div>
	</div>
	<div class="form-group row mt-5">
		<div class="col-sm-12 text-right" id="paramValueItemsHolder">
			<input type="hidden" class="form-control" name="paramName" value="<?php echo $this->param['name']; ?>">
			<button type="button" class="btn btn-secondary" onclick="DLM.go('/param/display/globals')">
				<?php echo \__('goBack'); ?>
			</button>
			<button type="submit" class="btn btn-success" onclick="event.preventDefault(); DLM.sendForm('editGlobal')">
				<?php echo \__('save'); ?>
			</button>
			<?php
			if($this->param['type'] == "list")
			{
				foreach($this->paramValue as $i => $item)
				{
					?>
					<input type="hidden" name="paramValue[]" id="paramListItem<?php echo $i; ?>" value="<?php echo $item; ?>">
					<?php
				}
				?>
				<script type="text/javascript"> igniteFormList("paramValue"); </script>
				<?php
			}
			?>
		</div>
	</div>
</form>