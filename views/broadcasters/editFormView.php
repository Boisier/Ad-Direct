<h1>
	<?php echo \__("editBroadcaster"); ?>
</h1>
<div class="alert alert-danger form-alert" role="alert" id="missingFieldError" style="display:none">
	<?php echo \__("missingFieldError"); ?>
</div>
<div class="alert alert-danger form-alert" role="alert" id="alreadyExistError" style="display:none">
	<?php echo \__("broadcasterNameAlreadyExist"); ?>
</div>
<form class="form-horizontal mt-5" name="editBroadcaster" action="/broadcaster/edit/<?php echo $this->broadcasterID; ?>/">
	<div class="form-group row">
		<label for="addBroadcasterName" class="col-sm-4 col-form-label"><?php echo \__("broadcasterNameField"); ?></label>
		<div class="col-sm-8">
			<input class="form-control" type="text" name="name" id="addBroadcasterName" placeholder="<?php echo \__("broadcasterNameField"); ?>" value="<?php echo $this->broadcasterName; ?>">
		</div>
    </div>
	<div class="form-group row">
		<label for="editBroadcasterGroup" class="col-sm-4 col-form-label">
			<?php echo \__("broadcasterGroupField"); ?>
		</label>
		<div class="col-sm-8">
			<select class="form-control" name="groupID" id="editBroadcasterGroup">
				<option value="0" <?php echo $this->groupID == 0 ? "selected" : ""; ?>><?php echo \__("none"); ?></option>
				<?php
				foreach($this->broadcasterGroups as $group)
				{
					?>
					<option value="<?php echo $group['ID']; ?>" <?php echo $this->groupID == $group['ID'] ? "selected" : ""; ?>><?php echo $group['name']; ?></option>
					<?php
				}
				?>
			</select>
		</div>
    </div>
	<div class="form-group row mt-5">
    	<div class="col-sm-12 text-right">
			<button type="button" class="btn btn-secondary" onclick="DLM.go('/broadcaster/display/<?php echo $this->broadcasterID; ?>')">
				<?php echo \__("goBack"); ?>
			</button>
			<button type="submit" class="btn btn-success" onclick="event.preventDefault(); DLM.sendForm('editBroadcaster')">
				<?php echo \__("save"); ?>
			</button>
		</div>
	</div>
</form>