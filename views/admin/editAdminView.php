<h1>
	<?php echo \__("editAdminAccount"); ?>
</h1>
<div class="alert alert-danger form-alert" role="alert" id="missingFieldError" style="display:none">
	<?php echo \__("missingFieldError"); ?>
</div>
<div class="alert alert-danger form-alert" role="alert" id="badEmailError" style="display:none">
	<?php echo \__("badEmailError"); ?>
</div>
<div class="alert alert-danger form-alert" role="alert" id="emailAlreadyUsedError" style="display:none">
	<?php echo \__("emailAlreadyUsedError"); ?>
</div>
<div class="alert alert-danger form-alert" role="alert" id="fatalErrorError" style="display:none">
	<?php echo \__("fatalError"); ?>
</div>
<form class="form-horizontal mt-5" name="editAdminForm" action="/user/edit/">
	<div class="form-group row">
		<label for="userName" class="col-sm-4 col-form-label">
			<?php echo \__("adminNameField"); ?>
		</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="userName" name="userName" placeholder="<?php echo \__("adminNameField"); ?>" value="<?php echo $this->adminName; ?>">
		</div>
	</div>
	<div class="form-group row">
		<label for="userEmail" class="col-sm-4 col-form-label">
			<?php echo \__("adminEmailField"); ?>
		</label>
		<div class="col-sm-8">
			<input type="email" class="form-control" id="userEmail" name="userEmail" placeholder="<?php echo \__("adminEmailField"); ?>" value="<?php echo $this->adminEmail; ?>">
			<!-- //TODO: email validation -->
		</div>
	</div>
	<div class="form-group row">
		<label for="userPassword" class="col-sm-4 col-form-label">
			<?php echo \__("adminPasswordField"); ?>
		</label>
		<div class="col-sm-8">
			<div class="input-group">
				<input type="text" class="form-control" id="userPassword" name="userPassword" placeholder="<?php echo \__("adminPasswordField"); ?>">
				<span class="input-group-btn">
					<button class="btn btn-default" type="button" onclick="generatePassword('adminPassword')">
						<i class="fa fa-fw fa-key"></i>
					</button>
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
	<h5 class="mt-3"><?php echo \__("authorizations"); ?></h5>
	<div class="form-group row">
		<div class="col-sm-3">
			<input type="checkbox" data-toggle="toggle" id="manageClientsCheckBox" name="privileges[]" value="MANAGE_CLIENTS" <?php echo in_array("MANAGE_CLIENTS", $this->privileges) ? "checked" : ""; ?>>
		</div>
		<label for="manageClientsCheckBox" class="col-sm-9 col-form-label">
			<?php echo \__("manageClientsPrivilege"); ?>
		</label>
	</div>
	<div class="form-group row">
		<div class="col-sm-3">
			<input type="checkbox" data-toggle="toggle" id="approveCreativesCheckBox" name="privileges[]" value="APPROVE_CREATIVES" <?php echo in_array("APPROVE_CREATIVES", $this->privileges) ? "checked" : ""; ?>>
		</div>
		<label for="approveCreativesCheckBox" class="col-sm-9 col-form-label">
			<?php echo \__("approveCreativesPrivilege"); ?>
		</label>
	</div>
	<div class="form-group row">
		<div class="col-sm-3">
			<input type="checkbox" data-toggle="toggle" id="editParamsCheckBox" name="privileges[]" value="EDIT_PARAMS" <?php echo in_array("EDIT_PARAMS", $this->privileges) ? "checked" : ""; ?>>
		</div>
		<label for="editParamsCheckBox" class="col-sm-9 col-form-label">
			<?php echo \__("editParamsPrivilege"); ?>
		</label>
	</div>
	<div class="form-group row">
		<div class="col-sm-3">
			<input type="checkbox" data-toggle="toggle" id="editSupportsCheckBox" name="privileges[]" value="EDIT_SUPPORTS" <?php echo in_array("EDIT_SUPPORTS", $this->privileges) ? "checked" : ""; ?>>
		</div>
		<label for="editSupportsCheckBox" class="col-sm-9 col-form-label">
			<?php echo \__("editSupportsPrivilege"); ?>
		</label>
	</div>
	<div class="form-group row">
		<div class="col-sm-3">
			<input type="checkbox" data-toggle="toggle" id="editAdminsCheckBox" name="privileges[]" value="EDIT_ADMINS" <?php echo in_array("EDIT_ADMINS", $this->privileges) ? "checked" : ""; ?>>
		</div>
		<label for="editAdminsCheckBox" class="col-sm-9 col-form-label">
			<?php echo \__("editAdminsPrivilege"); ?>
		</label>
	</div>
	<div class="form-group row">
		<div class="col-sm-3">
			<input type="checkbox" data-toggle="toggle" id="broadcastCreativesCheckBox" name="privileges[]" value="BROADCAST_ADMIN" <?php echo in_array("BROADCAST_ADMIN", $this->privileges) ? "checked" : ""; ?>>
		</div>
		<label for="broadcastCreativesCheckBox" class="col-sm-9 col-form-label">
			<?php echo \__("broadcastCreativesPrivilege"); ?>
		</label>
	</div>
	<div class="form-group mt-5">
		<div class="col-sm-12 text-right">
			<input type="hidden" name="userID" value="<?php echo $this->adminID; ?>">
            <input type="hidden" name="isAdmin" value="1">
			<button type="button" class="btn btn-secondary" onclick="DLM.go('/user/admins/')">
				<?php echo \__("goBack"); ?>
			</button>
			<button type="submit" class="btn btn-success" onclick="event.preventDefault(); DLM.sendForm('editAdminForm')">
				<?php echo \__("save"); ?></button>
		</div>
	</div>
</form>
<script type="text/javascript">
	$(function () {
	  $("input[type=checkbox]").bootstrapSwitch({onColor: 'success', offColor: 'danger'});
	});
</script>