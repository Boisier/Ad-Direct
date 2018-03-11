<div class="modal fade" tabindex="-1" role="dialog" id="mainModal">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<?php
				switch($this->creativeMediaType)
				{
					case 1: //Picture
						
						?><img src="<?php echo $this->creativePath; ?>"><?php
					
					break;
					case 2:
					
						?>
						<video controls>
                            <?php
                            
                            if(file_exists($this->creativeOriginalPath)) {
	                            ?>
                                <source src="<?php echo $this->creativePath; ?>" type="video/webm">
                                <source src="<?php echo $this->creativeOriginalPath; ?>" type="video/mp4">
	                            <?php
                            }
                            else
                            {
	                            ?>
                                <source src="<?php echo $this->creativePath; ?>" type="video/mp4">
	                            <?php
                            }
                            ?>
						</video>
						<?php
					break;
					default;
					
						?>
						<a href src="<?php echo $this->creativePath; ?>" target="_blank" style="text-align:center;">
							<?php echo \__("displayCreative"); ?>
						</a>
						<?php
				}
				?>
			</div>
		</div>
	</div>
</div>