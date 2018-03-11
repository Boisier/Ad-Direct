<nav class="navbar navbar-light bg-light">
	<div class="navbar-brand mr-auto broadcasterComponent">
		<i class="fa fa-building"></i>
		&nbsp;&nbsp;
		<?php echo \__("clients-tabTitle"); ?>
	</div>
	<div class="navbar-brand mr-auto searchComponent" style="display:none;">
		<i class="fa fa-search"></i>
		&nbsp;&nbsp;
		<?php echo \__("searchResults"); ?>
	</div>
    <div class="my-2 my-lg-0">
		<input class="form-control mr-sm-2" type="text" placeholder="<?php echo \__("search"); ?>" onkeyup="mainSearch(this.value);">
    </div>
</nav>
<div class="row my-4 broadcasterComponent">
	<div class="col-sm-8">
		<div class="form-group">
			<select class="form-control custom-select" id="broadcaster-groups-list" onchange="setBroadcasterTab(this.value);">
				<option value="all"><?php echo \__("AllBroadcasterGroups"); ?></option>
				<?php
				foreach($this->broadcasterGroups as $group)
				{
					?>
					<option value="<?php echo $group['ID']; ?>"><?php echo $group['name']; ?></option>
					<?php
				}
				?>
			</select>
		</div>
	</div>
	<div class="col-sm-4 text-right">
		<button class="btn btn-outline-secondary" type="button" onclick="DLM.go('/broadcaster/form/add')">
			<?php echo \__("addBroadcaster"); ?>
		</button>
	</div>
	<script type="text/javascript">
		var currentBroadcasterTab = "all";
	</script>		 
</div>
<section id="broadcastersList" class="list-group broadcasterComponent">
	<?php echo $this->broadcastersList; ?>
	
	<?php
	foreach($this->broadcasterGroups as $group)
	{
		?>
		<div class="<?php echo $group['ID']; ?>-broadcaster"></div>
		<?php
	}
	?>
</section>
<section id="searchSection" class="searchComponent mt-4" style="display:none;">
</section>