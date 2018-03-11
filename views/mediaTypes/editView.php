<h1>
	<?php echo \__("editMediaType"); ?>
</h1>
<div class="alert alert-danger form-alert" role="alert" id="missingFieldError" style="display:none">
	<?php echo \__("missingFieldError"); ?>
</div>
<div class="alert alert-danger form-alert" role="alert" id="alreadyExistError" style="display:none">
	<?php echo \__("mediaTypeNameAlreadyExist"); ?>
</div>
<form class="form-horizontal mt-5" name="editMediaType" action="/mediaType/update/<?php echo $this->mediaID; ?>">
	<div class="form-group row">
		<label for="mediaTypeName" class="col-sm-4 col-form-label">
			<?php echo \__("mediaTypeNameField"); ?>
		</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="name" id="mediaTypeName" value="<?php echo $this->mediaName; ?>">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-4 col-form-label">
			<?php echo __("mimeTypes"); ?>
		</label>
		<div class="col-sm-8">
			<div class="vertical-input-group" id="mimeListItemsContainer">
				<?php
				foreach($this->mimes as $i => $mime)
				{
					?>
					<div class="input-group" class="listElement" id="mimeListItem<?php echo $i; ?>Display">
						<input type="text" class="form-control" disabled value="<?php echo $mime; ?>">
						<span class="input-group-btn">
							<button class="btn btn-outline-secondary removeBtn" type="button" data-listItem="mimeListItem<?php echo $i; ?>">
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
		<label for="mimeListInputField" class="col-sm-4 col-form-label">
			<?php echo \__("addMimeType"); ?>
		</label>
		<div class="col-sm-8">
			<div class="input-group">
				<input type="text" class="form-control" id="mimeListInputField" placeholder="<?php echo __("mimeType"); ?>">
				<span class="input-group-btn">
					<button class="btn btn-outline-secondary" type="button" id="mimeListAddButton">
						<i class="fa fa-check fa-fw"></i>
					</button>
				</span>
			</div>
		</div>
	</div>
	<div class="form-group row mt-5" id="mimeListItemsHolder">
		<div class="col-sm-12 text-right">
			<button type="button" class="btn btn-default" onclick="DLM.go('/mediaType/home/')"><?php echo \__("goBack"); ?></button>
			<button type="submit" class="btn btn-success" onclick="event.preventDefault(); DLM.sendForm('editMediaType')"><?php echo \__("save"); ?></button>
			<?php
			foreach($this->mimes as $i => $mime)
			{
				?>
				<input type="hidden" name="mimeList[]" id="mimeListItem<?php echo $i; ?>" value="<?php echo $mime; ?>">
				<?php
			}
			?>
		</div>
	</div>
</form>
<script type="text/javascript"> igniteFormList("mimeList"); </script>