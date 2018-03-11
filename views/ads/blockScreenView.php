<?php

//If the "reason" is set to defaultAd, all "ad-ID" references represent the broadcasterID
//This choice has been made to prevent any code duplication

//Prep html attributes
$blockSize = $this->multipleScreens ? "col-sm-6" : "col-sm-12";

$screenBlockID = "screenBlock{$this->adID}-{$this->screenID}";
$dropAreaID = "dropArea{$this->adID}-{$this->screenID}";


if($this->multipleScreens ||$this->screenWidth > $this->screenHeight)
{
	$width = $this->multipleScreens ? 260 : 500;
	$height = floor($width * ($this->screenHeight / $this->screenWidth));
}
else
{
	$height = 500;
	$width = floor($height * ($this->screenWidth / $this->screenHeight));
}

$blockDimensions = "width:{$width}px; height:{$height}px;";

$uploadFormID = "uploadForm{$this->adID}-{$this->screenID}";
$uploadInputID = "uploadInput{$this->adID}-{$this->screenID}";

$creativeBlockID = "creativeBlock{$this->adID}-{$this->screenID}";
$creativeImgID = "creative{$this->adID}-{$this->screenID}";

if($this->hasCreative)
{
	$uploadSection = "display: none;";
	$creativeSection = "display: block;";
	$src = $this->creativePath;
}
else
{
	$uploadSection = "display: block;";
	$creativeSection = "display: none;";
	$src = "";
}

if($this->reason == "campaign")
{
	$uploadFormAction = "/creative/add/{$this->adID}/{$this->screenID}/";
	$displayAction = "/creative/display/{$this->adID}/{$this->screenID}/";
	$deleteAction = "/creative/form/delete/{$this->adID}/{$this->screenID}/";
}
else if($this->reason == "defaultAd")
{
	$uploadFormAction = "/defaultad/add/{$this->adID}/{$this->screenID}/";
	$displayAction = "/defaultad/zoom/{$this->adID}/{$this->screenID}/";
	$deleteAction = "/defaultad/form/delete/{$this->adID}/{$this->screenID}/";
}

?>
<div class="screenBlock <?php echo $blockSize; ?>" id="<?php echo $screenBlockID; ?>">
	<!-- No creative -->
	<div class="dropArea" 
		 id="<?php echo $dropAreaID; ?>" 
		 style="<?php echo $uploadSection.$blockDimensions; ?>" 
		 onclick="$('#<?php echo $uploadInputID; ?>').click()"
		 data-adID="<?php echo $this->adID; ?>"
		 data-screenID="<?php echo $this->screenID; ?>">
		<div class="strut"></div><!--whitespace
	 --><span class="addMsg">
			<i class="fa fa-plus"></i><br>
			<?php echo \__("dragOrClick"); ?><br>
			<small><?php echo $this->mediaTypeName; ?></small>
		</span>
	</div>
	<form class="uploadCreativeForm" 
		  name="<?php echo $uploadFormID; ?>" 
		  id="<?php echo $uploadFormID; ?>" 
		  action="<?php echo $uploadFormAction; ?>">
		<input type="file" 
			   name="creative" 
			   id="<?php echo $uploadInputID; ?>" 
			   onchange="uploadCreativeFromInput(<?php echo $this->adID; ?>, <?php echo $this->screenID; ?>)">
	</form>
	<script type="text/javascript">
		prepareDropArea(<?php echo $this->adID; ?>, <?php echo $this->screenID; ?>);
	</script>
	
	<!-- Creative -->
	<div id="<?php echo $creativeBlockID; ?>"
		 class="creativeBlock"
		 style="<?php echo $creativeSection.$blockDimensions; ?>">
		<img src="<?php echo $src; ?>" 
			 id="<?php echo $creativeImgID; ?>" 
			 style="<?php echo $blockDimensions; ?>">
		<?php
		if($this->creativeMediaType == 2)
		{
			?>
			<div class="creativeVideo" style="<?php echo $blockDimensions; ?>">
				<div class="strut"></div>
				<i class="fa fa-file-video-o"></i>
			</div>
			<?php
		}
		?>
		<div class="creativeOverlay">
			<div class="strut"></div><!--whitespace
		 --><span class="actions">
				<span onclick="DLM.modal('<?php echo $displayAction; ?>')">
					<i class="fa fa-picture-o"></i><br>
					<?php echo \__("display"); ?>
				</span>
				<?php if($this->screenHeight > $this->screenWidth) echo '<br><br><br>'; ?>
				<span onclick="DLM.modal('<?php echo $deleteAction; ?>')">
					<i class="fa fa-trash"></i><br>
					<?php echo \__("delete"); ?>
				</span>
			</span>
		</div>
	</div>
</div>
<!-- TODO: JS for the upload -->