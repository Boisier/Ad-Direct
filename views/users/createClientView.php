<h1><?php echo \__("addClient"); ?></h1>
<div class="alert alert-danger form-alert" role="alert" id="missingFieldError" style="display:none">
	<?php echo \__("missingFieldError"); ?>
</div>
<div class="alert alert-danger form-alert" role="alert" id="badEmailError" style="display:none">
	<?php echo \__("badEmailError"); ?>
</div>
<div class="alert alert-danger form-alert" role="alert" id="fatalErrorError" style="display:none">
	<?php echo \__("fatalError"); ?>
</div>
<div class="alert alert-danger form-alert" role="alert" id="emailAlreadyUsedError" style="display:none">
	<?php echo \__("emailAlreadyUsedError"); ?>
</div>
<form class="form-horizontal mt-5" name="addAccount" action="/user/create/client/">
	<div class="form-group row">
		<label for="addClientName" class="col-sm-4 col-form-label"><?php echo \__("clientNameField"); ?></label>
		<div class="col-sm-8">
			<input class="form-control" type="text" name="clientName" id="addClientName" placeholder="<?php echo \__("clientNameField"); ?>">
		</div>
    </div>
	<div class="form-group row">
		<label for="addClientEmail" class="col-sm-4 col-form-label"><?php echo \__("clientEmailField"); ?></label>
		<div class="col-sm-8">
			<input class="form-control" type="text" name="clientEmail" id="addClientEmail" placeholder="<?php echo \__("clientEmailField"); ?>">
		</div>
    </div>
	<div class="form-group row">
		<label for="addClientPassword" class="col-sm-4 col-form-label"><?php echo \__("clientPasswordField"); ?></label>
		<div class="col-sm-8">
			<div class="input-group">
				<input class="form-control" type="text" name="clientPassword" id="addClientPassword" placeholder="<?php echo \__("clientPasswordField"); ?>">
				<span class="input-group-btn">
					<button class="btn btn-secondary" type="button" onclick="generatePassword('addClientPassword')"><i class="fa fa-fw fa-key"></i></button>
				</span>
			</div>
			<small class="text-muted"><?php echo \__("clientCreationNoficationMessage"); ?></small>
		</div>
    </div>
	<div class="form-group row">
		<label for="addClientTimeZone" class="col-sm-4 col-form-label"><?php echo \__("timezoneField"); ?></label>
        <div class="col-sm-8">
            <select class="form-control" name="clientTimezone" id="addClientTimezone">
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
	<div class="form-group row">
		<div class="col-sm-3">
			<input type="checkbox" data-toggle="toggle" id="addClientTrusted" name="privileges[]" value="TRUSTED_CLIENT">
		</div>
		<label for="addClientTrusted" class="col-sm-9 col-form-label">
			<?php echo \__("trustedClient"); ?><br>
			<small class="text-muted"><?php echo \__("trustedClient-description"); ?> </small>
		</label>
	</div>
	<div class="form-group row mt-5">
    	<div class="col-sm-12 text-right">
			<input type="hidden" name="broadcasterID" value="<?php echo $this->broadcasterID; ?>">
			<button type="button" class="btn btn-secondary" onclick="DLM.go('/broadcaster/display/<?php echo $this->broadcasterID; ?>/CLIENTS/')">
				<?php echo \__("goBack"); ?>
			</button>
			<button type="submit" class="btn btn-success" onclick="event.preventDefault(); DLM.sendForm('addAccount')">
				<?php echo \__("create"); ?>
			</button>
		</div>
	</div>
</form>
<script type="text/javascript">
	$(function () {
	  $("input[type=checkbox]").bootstrapSwitch({onColor: 'success', offColor: 'danger'});
	});
</script>