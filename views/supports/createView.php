<h1>
	<?php echo \__("createSupport"); ?>
</h1>
<div class="alert alert-danger form-alert" role="alert" id="missingFieldError" style="display:none">
	<?php echo \__("missingFieldError"); ?>
</div>
<div class="alert alert-danger form-alert" role="alert" id="alreadyExistError" style="display:none">
	<?php echo \__("SupportNameAlreadyExist"); ?>
</div>
<form class="form-horizontal mt-5" name="createSupport" action="/support/create/">
	<div class="form-group row">
		<label for="supportName" class="col-sm-4 col-form-label">
			<?php echo \__("supportNameField"); ?>
		</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="name" id="supportName" placeholder="<?php echo \__("supportNameField"); ?>">
		</div>
	</div>
	<div class="form-group row mt-5">
		<div class="col-sm-12 text-right">
			<button type="button" class="btn btn-secondary" onclick="DLM.go('/support/home')"><?php echo \__("goBack"); ?></button>
			<button type="submit" class="btn btn-success" onclick="event.preventDefault(); DLM.sendForm('createSupport')"><?php echo \__("create") ?></button>
		</div>
	</div>
</form>