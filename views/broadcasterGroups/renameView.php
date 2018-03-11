<h1>
	<?php echo \__("renameBroadcasterGroup"); ?>
</h1>
<div class="alert alert-danger form-alert" role="alert" id="missingFieldError" style="display:none">
	<?php echo \__("missingBroadcasterGroupName"); ?>
</div>
<div class="alert alert-danger form-alert" role="alert" id="alreadyExistError" style="display:none">
	<?php echo \__("broadcasterGroupNameAlreadyExist"); ?>
</div>
<form class="form-horizontal mt-5" name="renameBroadcasterGroup" action="/broadcastergroup/update/<?php echo $this->groupID; ?>/">
	<div class="form-group row">
		<label for="editBroadcasterGroupName" class="col-sm-4 col-form-label"><?php echo \__("broadcasterGroupNameField"); ?></label>
		<div class="col-sm-8">
			<input class="form-control" type="text" name="name" id="editBroadcasterGroupName" placeholder="<?php echo \__("broadcasterGroupNameField"); ?>" value="<?php echo $this->groupName; ?>">
		</div>
    </div>
	<div class="form-group row mt-5">
    	<div class="col-sm-12 text-right">
			<button type="button" class="btn btn-secondary" onclick="DLM.go('/broadcastergroup/home/')">
				<?php echo \__("goBack"); ?>
			</button>
			<button type="submit" class="btn btn-success" onclick="event.preventDefault(); DLM.sendForm('renameBroadcasterGroup')">
				<?php echo \__("rename"); ?>
			</button>
		</div>
	</div>
</form>
<h1 class="mt-5">
	<?php echo \__("deleteBroadcasterGroup"); ?>
</h1>
<div class="form-group row mt-2">
	<div class="col-sm-12 text-right">
		<button type="button" class="btn btn-danger" onclick="DLM.go('/broadcastergroup/delete/<?php echo $this->groupID; ?>')">
			<?php echo \__("delete"); ?>
		</button>
	</div>
</div>