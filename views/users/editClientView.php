<h1><?php echo \__("editClient"); ?></h1>
<div class="alert alert-danger form-alert" role="alert" id="missingFieldError" style="display:none">
	<?php echo \__("missingFieldError"); ?>
</div>
<div class="alert alert-danger form-alert" role="alert" id="fatalErrorError" style="display:none">
	<?php echo \__("fatalError"); ?>
</div>
<div class="alert alert-danger form-alert" role="alert" id="badEmailError" style="display:none">
	<?php echo \__("badEmailError"); ?>
</div>
<div class="alert alert-danger form-alert" role="alert" id="emailAlreadyUsedError" style="display:none">
	<?php echo \__("emailAlreadyUsedError"); ?>
</div>
<form class="form-horizontal clearfix" name="editUser" action="/user/edit/">
	<div class="form-group row">
		<label for="editUserName" class="col-sm-4 col-form-label"><?php echo \__("clientNameField"); ?></label>
		<div class="col-sm-8">
			<input class="form-control" type="text" name="userName" id="editUserName" placeholder="<?php echo \__("clientNameField"); ?>" value="<?php echo $this->clientName; ?>">
		</div>
    </div>
	<div class="form-group row">
		<label for="editUserEmail" class="col-sm-4 col-form-label"><?php echo \__("clientEmailField"); ?></label>
		<div class="col-sm-8">
			<input class="form-control" type="text" name="userEmail" id="editUserEmail" placeholder="<?php echo \__("clientEmailField"); ?>" value="<?php echo $this->clientEmail; ?>">
		</div>
    </div>
	<div class="form-group row">
		<label for="editUserPassword" class="col-sm-4 col-form-label"><?php echo \__("clientPasswordField"); ?></label>
		<div class="col-sm-8">
			<div class="input-group">
				<input class="form-control" type="text" name="userPassword" id="editUserPassword" placeholder="<?php echo \__("clientPasswordField"); ?>">
				<span class="input-group-btn">
					<button class="btn btn-secondary" type="button" onclick="generatePassword('editClientPassword')"><i class="fa fa-fw fa-key"></i></button>
				</span>
			</div>
			<small class="text-muted"><?php echo \__("emptyMeansNoUpdate"); ?></small>
		</div>
    </div>
    <div class="form-group row">
        <label for="editUserTimezone" class="col-sm-4 col-form-label"><?php echo \__("timezoneField"); ?></label>
        <div class="col-sm-8">
            <select class="form-control" name="userTimezone" id="editUserTimezone">
				<?php
				foreach($this->timezones as $timezoneName => $timezone)
				{
					?>
                    <option value="<?php echo $timezone; ?>" <?php echo $this->timezone == $timezone ? "selected" : ""; ?>>
						<?php echo $timezoneName; ?>
                    </option>
					<?
				}
				?>
            </select>
        </div>
    </div>
	<div class="form-group row">
		<div class="col-sm-3">
			<input type="checkbox" data-toggle="toggle" id="editUserTrusted" name="privileges[]" value="TRUSTED_CLIENT" <?php echo in_array("TRUSTED_CLIENT", $this->privileges) ? "checked" : ""; ?>>
		</div>
		<label for="editClientTrusted" class="col-sm-9 col-form-label">
			<?php echo \__("trustedClient"); ?><br>
			<small class="text-muted"><?php echo \__("trustedClient-description"); ?> </small>
		</label>
	</div>
	<div class="form-group row mt-5">
    	<div class="col-sm-12 text-right">
			<input type="hidden" name="userID" value="<?php echo $this->clientID; ?>">
            <input type="hidden" name="isAdmin" value="0">
			<button type="button" class="btn btn-secondary" onclick="DLM.go('/broadcaster/display/<?php echo $this->broadcasterID; ?>/CLIENTS/')">
				<?php echo \__("goBack"); ?>
			</button>
			<button type="submit" class="btn btn-success" onclick="event.preventDefault(); DLM.sendForm('editUser')">
				<?php echo \__("save"); ?>
			</button>
		</div>
	</div>
</form>
<script type="text/javascript">
	$(function () {
	  $("input[type=checkbox]").bootstrapSwitch({onColor: 'success', offColor: 'danger'});
	});
</script>