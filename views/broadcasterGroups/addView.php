<h1>
	<?php echo \__("addBroadcasterGroup"); ?>
</h1>
<div class="alert alert-danger" role="alert" id="missingFieldError" style="display:none">
	<?php echo \__("missingGroupName"); ?>
</div>
<div class="alert alert-danger" role="alert" id="alreadyExistError" style="display:none">
	<?php echo \__("broadcasterGroupNameAlreadyExist"); ?>
</div>
<form class="form-horizontal mt-5" name="addBroadcasterGroup" action="/broadcastergroup/create/">
	<div class="form-group row">
		<label for="addBroadcasterGroupName" class="col-sm-4 col-form-label"><?php echo \__("broadcasterGroupNameField"); ?></label>
		<div class="col-sm-8">
			<input class="form-control" type="text" name="name" id="addBroadcasterGroupName" placeholder="<?php echo \__("broadcasterGroupNameField"); ?>">
		</div>
    </div>
	<div class="form-group row mt-5">
    	<div class="col-sm-12 text-right">
			<button type="button" class="btn btn-secondary" onclick="DLM.go('/broadcastergroup/home/')">
				<?php echo \__("goBack"); ?>
			</button>
			<button type="submit" class="btn btn-success" onclick="event.preventDefault(); DLM.sendForm('addBroadcasterGroup')">
				<?php echo \__("create") ?>
			</button>
    	</div>
    </div>
</form>