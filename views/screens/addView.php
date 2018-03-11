<h1>
	<?php echo \__("addScreen"); ?>
</h1>
<div class="alert alert-danger form-alert" role="alert" id="missingFieldError" style="display:none">
	<?php echo \__("missingFieldError"); ?>
</div>
<form class="form-horizontal mt-5" name="addScreen" action="/screen/add/<?php echo $this->supportID; ?>">
	<div class="form-group row">
		<label for="newScreenName" class="col-sm-4 col-form-label">
			<?php echo \__("screenNameField"); ?>
		</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="screenName" id="newScreenName" placeholder="<?php echo \__("optional"); ?>">
		</div>
	</div>
	<div class="form-group row">
		<label for="newScreenWidth" class="col-sm-4 col-form-label">
			<?php echo \__("screenWidthField"); ?>
		</label>
		<div class="col-sm-8">
			<div class="input-group">
				<input type="number" class="form-control" name="screenWidth" id="newScreenWidth" placeholder="<?php echo \__("screenWidthField"); ?>">
				<span class="input-group-addon">px</span>
			</div>
		</div>
	</div>
	<div class="form-group row">
		<label for="newScreenHeight" class="col-sm-4 col-form-label">
			<?php echo \__("screenHeightField"); ?>
		</label>
		<div class="col-sm-8">
			<div class="input-group">
				<input type="number" class="form-control" name="screenHeight" id="newScreenHeight" placeholder="<?php echo \__("screenHeightField"); ?>">
				<span class="input-group-addon">px</span>
			</div>
		</div>
	</div>
	<div class="form-group row mt-5">
		<div class="col-sm-12 text-right">
			<button type="button" class="btn btn-secondary" onclick="DLM.go('/support/display/<?php echo $this->supportID; ?>')">
				<?php echo \__("goBack"); ?>
			</button>
			<button type="submit" class="btn btn-success" onclick="event.preventDefault(); DLM.sendForm('addScreen')">
				<?php echo \__("add"); ?>
			</button>
		</div>
	</div>
</form>