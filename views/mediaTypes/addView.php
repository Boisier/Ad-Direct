<h1>
	<?php echo \__("addMediaType"); ?>
</h1>
<div class="alert alert-danger form-alert" role="alert" id="missingFieldError" style="display:none">
	<?php echo \__("missingFieldError"); ?>
</div>
<div class="alert alert-danger form-alert" role="alert" id="alreadyExistError" style="display:none">
	<?php echo \__("mediaTypeNameAlreadyExist"); ?>
</div>
<form class="form-horizontal mt-5" name="createMediaType" action="/mediaType/create/">
	<div class="form-group row">
		<label for="mediaTypeName" class="col-sm-4 col-form-label">
			<?php echo \__("mediaTypeNameField"); ?>
		</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="name" id="mediaTypeName" placeholder="<?php echo \__("mediaTypeNameField"); ?>">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-4 col-form-label">
			<?php echo \__("mimeTypes"); ?>
		</label>
		<div class="col-sm-8">
			<div class="vertical-input-group" id="mimeListItemsContainer">
				<!-- Items goes here -->
			</div>
		</div>
	</div>
	<div class="form-group row">
		<label for="mimeListAddButton" class="col-sm-4 col-form-label">
			<?php echo \__("addMimeType"); ?>
		</label>
		<div class="col-sm-8">
			<div class="input-group">
				<input type="text" class="form-control" id="mimeListInputField">
				<span class="input-group-btn">
					<button class="btn btn-outline-secondary" type="button" id="mimeListAddButton" placeholder="<?php echo \__("mimeType"); ?>">
						<i class="fa fa-check fa-fw"></i>
					</button>
				</span>
			</div>
		</div>
	</div>
	<div class="form-group row mt-5" id="mimeListItemsHolder">
		<div class="col-sm-12 text-right">
			<button type="button" class="btn btn-secondary" onclick="DLM.go('/mediaType/home/')"><?php echo \__("goBack"); ?></button>
			<button type="submit" class="btn btn-success" onclick="event.preventDefault(); DLM.sendForm('createMediaType')"><?php echo \__("add"); ?></button>
		</div>
	</div>
</form>
<script type="text/javascript"> igniteFormList("mimeList"); </script>