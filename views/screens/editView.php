<h1>
	<?php echo \__("editScreen"); ?>
</h1>
<div class="alert alert-danger form-alert" role="alert" id="missingFieldError" style="display:none">
	<?php echo \__("missingFieldError"); ?>
</div>
<form class="form-horizontal mt-5" name="addScreen" action="/screen/edit/<?php echo $this->screenID; ?>">
	<div class="form-group row">
		<label for ="editScreenName" class="col-sm-4 col-form-label">
			<?php echo \__("screenNameField"); ?>
		</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="screenName" id="editScreenName" placeholder="<?php echo \__("optional"); ?>" value="<?php echo $this->screenName; ?>">
		</div>
	</div>
	<div class="form-group row">
		<label for="editScreenWidth" class="col-sm-4 col-form-label">
			<?php echo \__("screenWidthField"); ?>
		</label>
		<div class="col-sm-8">
			<div class="input-group">
				<input type="number" class="form-control" name="screenWidth" id="editScreenWidth" placeholder="<?php echo \__("screenWidthField"); ?>" value="<?php echo $this->screenWidth; ?>">
				<span class="input-group-addon">px</span>
			</div>
		</div>
	</div>
	<div class="form-group row">
		<label for="editScreenHeight" class="col-sm-4 col-form-label">
			<?php echo \__("screenHeightField"); ?>
		</label>
		<div class="col-sm-8">
			<div class="input-group">
				<input type="number" class="form-control" name="screenHeight" id="editScreenHeight" placeholder="<?php echo \__("screenHeightField"); ?>" value="<?php echo $this->screenHeight; ?>">
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