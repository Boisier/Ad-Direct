<nav class="breadcrumb bg-white py-0">
  <a class="breadcrumb-item" href="#" onclick="event.preventDefault(); DLM.go('/home/clients/')"><?php echo \__("adminMenu-Clients"); ?></a>
  <span class="breadcrumb-item active"><?php echo $this->broadcasterName ?></span>
</nav>
<nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-between mb-3">
	<span class="navbar-brand"><?php echo $this->broadcasterName ?></span>
	<ul class="navbar-nav">
		<li class="nav-item">
        	<a class="nav-link" href="#" onclick="event.preventDefault(); DLM.go('/broadcaster/form/edit/<?php echo $this->broadcasterID; ?>')">
				<span><?php echo \__("edit"); ?></span>
			</a>
      	</li>
		<li class="nav-item">
        	<a class="nav-link" href="#" onclick="event.preventDefault(); DLM.modal('/broadcaster/form/delete/<?php echo $this->broadcasterID; ?>')">
				<span><?php echo \__("delete"); ?></span>
			</a>
      	</li>
	</ul>
</nav>
<ul class="nav nav-pills nav-justified" role="tablist">
	<li class="nav-item">
		<a class="nav-link <?php echo $this->currentTab == "campaigns" ? "active" : ""; ?>" data-toggle="tab" href="#companyCampaigns">
			<i class="fa fa-bullhorn"></i>&nbsp;&nbsp;<?php echo \__("campaigns"); ?>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $this->currentTab == "clients" ? "active" : ""; ?>" data-toggle="tab" href="#companyAccounts">
			<i class="fa fa-user"></i>&nbsp;&nbsp;<?php echo \__("clients"); ?>
		</a>
	</li>
</ul>
<div class="tab-content">
	<div role="tabpanel" class="tab-pane fade <?php echo $this->currentTab == "campaigns" ? "show active" : ""; ?>" id="companyCampaigns">
		<?php echo $this->broadcasterCampaigns; ?>
	</div>
	<div role="tabpanel" class="tab-pane fade <?php echo $this->currentTab == "clients" ? "show active" : ""; ?>" id="companyAccounts">
		<?php echo $this->broadcasterClients; ?>
	</div>
</div>