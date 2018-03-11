<h1>
	<?php echo \__("addAdminAccount"); ?>
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
<form class="form-horizontal mt-5" name="addAdminForm" action="/user/create/admin/">
	<div class="form-group row">
		<label for="addAdminName" class="col-sm-4 col-form-label">
			<?php echo \__("adminNameField"); ?>
		</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="addAdminName" name="adminName" placeholder="<?php echo \__("adminNameField"); ?>">
		</div>
	</div>
	<div class="form-group row">
		<label for="addAdminEmail" class="col-sm-4 col-form-label">
			<?php echo \__("adminEmailField"); ?>
		</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="addAdminEmail" name="adminEmail" placeholder="<?php echo \__("adminEmailField"); ?>">
			<!-- //TODO: email validation -->
		</div>
	</div>
	<div class="form-group row">
		<label for="adminPassword" class="col-sm-4 col-form-label">
			<?php echo \__("adminPasswordField"); ?>
		</label>
		<div class="col-sm-8">
			<div class="input-group">
				<input type="text" class="form-control" id="adminPassword" name="adminPassword" placeholder="<?php echo \__("adminPasswordField"); ?>">
				<span class="input-group-btn">
					<button class="btn btn-default" type="button" onclick="generatePassword('adminPassword')">
						<i class="fa fa-fw fa-key"></i>
					</button>
				</span>
			</div>
		</div>
	</div>
    <div class="form-group row">
        <label for="adminTimezone" class="col-sm-4 col-form-label"><?php echo \__("timezoneField"); ?></label>
        <div class="col-sm-8">
            <select class="form-control" name="adminTimezone" id="adminTimezone">
				<?php
				foreach($this->timezones as $timezoneName => $timezone)
				{
					?>
                    <option value="<?php echo $timezone; ?>">
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
			<input type="checkbox" data-toggle="toggle" id="manageClientsCheckBox" name="privileges[]" value="MANAGE_CLIENTS">
		</div>
		<label for="manageClientsCheckBox" class="col-sm-9 col-sm-label">
			<?php echo \__("manageClientsPrivilege"); ?>
		</label>
	</div>
	<div class="form-group row">
		<div class="col-sm-3">
			<input type="checkbox" data-toggle="toggle" id="approveCreativesCheckBox" name="privileges[]" value="APPROVE_CREATIVES">
		</div>
		<label for="approveCreativesCheckBox" class="col-sm-9 col-sm-label">
			<?php echo \__("approveCreativesPrivilege"); ?>
		</label>
	</div>
	<div class="form-group row">
		<div class="col-sm-3">
			<input type="checkbox" data-toggle="toggle" id="editParamsCheckBox" name="privileges[]" value="EDIT_PARAMS">
		</div>
		<label for="editParamsCheckBox" class="col-sm-9 col-sm-label">
			<?php echo \__("editParamsPrivilege"); ?>
		</label>
	</div>
	<div class="form-group row">
		<div class="col-sm-3">
			<input type="checkbox" data-toggle="toggle" id="editSupportsCheckBox" name="privileges[]" value="EDIT_SUPPORTS">
		</div>
		<label for="editSupportsCheckBox" class="col-sm-9 col-sm-label">
			<?php echo \__("editSupportsPrivilege"); ?>
		</label>
	</div>
	<div class="form-group row">
		<div class="col-sm-3">
			<input type="checkbox" data-toggle="toggle" id="editAdminsCheckBox" name="privileges[]" value="EDIT_ADMINS">
		</div>
		<label for="editAdminsCheckBox" class="col-sm-9 col-sm-label">
			<?php echo \__("editAdminsPrivilege"); ?>
		</label>
	</div>
	<div class="form-group row">
		<div class="col-sm-3">
			<input type="checkbox" data-toggle="toggle" id="broadcastCreativesCheckBox" name="privileges[]" value="BROADCAST_ADMIN">
		</div>
		<label for="broadcastCreativesCheckBox" class="col-sm-9 col-form-label">
			<?php echo \__("broadcastCreativesPrivilege"); ?>
		</label>
	</div>
	<div class="form-group mt-5">
		<div class="col-sm-12 text-right">
			<button type="button" class="btn btn-secondary" onclick="DLM.go('/user/admins/')">
				<?php echo \__("goBack"); ?>
			</button>
			<button type="submit" class="btn btn-success" onclick="event.preventDefault(); DLM.sendForm('addAdminForm')">
				<?php echo \__("add"); ?></button>
		</div>
	</div>
</form>
<script type="text/javascript">
	$(function () {
	  $("input[type=checkbox]").bootstrapSwitch({onColor: 'success', offColor: 'danger'});
	})
</script>