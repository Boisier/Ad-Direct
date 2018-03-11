<div class="text-right my-3">
	<button class="btn btn-outline-secondary my-4 mr-0" type="button"  onclick="DLM.go('/user/form/createClient/<?php echo $this->broadcasterID; ?>')">
		<?php echo \__("addClient"); ?>
	</button>
</div>
<section id="clientList">
	<?php echo $this->clientList; ?>
</section>
