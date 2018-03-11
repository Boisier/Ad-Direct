<h1>
	<?php echo \__("renameSupport"); ?>
</h1>
<div class="alert alert-danger form-alert" role="alert" id="missingFieldError" style="display:none">
	<?php echo \__("missingFieldError"); ?>
</div>
<div class="alert alert-danger form-alert" role="alert" id="alreadyExistError" style="display:none">
	<?php echo \__("supportNameAlreadyExist"); ?>
</div>
<form class="form-horizontal mt-5" name="renameSupport" action="/support/edit/<?php echo $this->supportID; ?>/">
	<div class="form-group row">
		<label for="editSupportName" class="col-sm-4 col-form-label"><?php echo \__("supportNameField"); ?></label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="name" id="editSupportName" placeholder="<?php echo \__("supportNameField"); ?>" value="<?php echo $this->supportName; ?>">
		</div>
	</div>
	<div class="form-group row mt-5">
		<div class="col-sm-12 text-right">
			<button type="button" class="btn btn-secondary" onclick="DLM.go('/support/display/<?php echo $this->supportID; ?>')">
				<?php echo \__("goBack"); ?>
			</button>
			<button type="submit" class="btn btn-success" onclick="event.preventDefault(); DLM.sendForm('renameSupport')">
				<?php echo \__("rename"); ?>
			</button>
		</div>
	</div>
</form>