<nav class="breadcrumb bg-white py-0">
	<a class="breadcrumb-item" href="#" onclick="event.preventDefault(); DLM.go('/home/overview/')"><?php echo \__("adminMenu-Overview"); ?></a>
	<span class="breadcrumb-item active"><?php echo \__("legalPage-".$this->legalName); ?></span>
</nav>
<?php
switch($this->legalName)
{
	case "terms":
		if(\__("lang") == "fr")
			$textID = "TERMS_OF_USE_FR";
		else
			$textID = "TERMS_OF_USE_EN";
	break;
	case "specs":
		if(\__("lang") == "fr")
			$textID = "TECHNICAL_SPECS_FR";
		else
			$textID = "TECHNICAL_SPECS_EN";
	break;
	case "creatives":
		if(\__("lang") == "fr")
			$textID = "CREATIVE_RECOS_FR";
		else
			$textID = "CREATIVE_RECOS_EN";
	break;
	default:
		$textID = "";
}

echo \Library\Params::get($textID);
?>