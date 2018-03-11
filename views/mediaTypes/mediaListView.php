<div class="card mt-3">
	<div class="card-body">
		<h4 class="card-title"><?php echo $this->mediaName; ?></h4>
		<h6 class="card-subtitle mb-2 text-muted"><?php echo $this->mimesList; ?></h6>
		<a href="#" class="card-link" onclick="event.preventDefault(); DLM.go('/mediaType/form/edit/<?php echo $this->mediaID; ?>')">
			<?php echo \__("edit"); ?>
		</a>
		<?php
		if($this->mediaID > 2)
		{
			?>
			<a href="#" class="card-link" onclick="event.preventDefault(); DLM.modal('/mediaType/form/delete/<?php echo $this->mediaID; ?>')">
				<?php echo \__("delete"); ?>
			</a>
			<?php
		}
		?>
	</div>
</div>