function tabManager()
{
	var currentTab = "";
	
	this.checkHash = function()
	{
    	var hash = window.location.hash.substring(1);
		
		$(".nav-btn.open").removeClass("open");
		
		if(hash.length > 0 && currentTab != hash)
		{
			currentTab = hash; 
		}
		else
		{
			currentTab = "overview"; 
		}
		
		$("#"+currentTab+"Btn").addClass("open");
		
		if($.isNumeric(currentTab))
		{
			DLM.go("/campaign/display/"+currentTab+"/");
		}
		else
		{
			DLM.go("/home/"+currentTab+"/");
		}
	}
}

var tabManagerHandler = new tabManager();

$(window).on("hashchange", tabManagerHandler.checkHash);
$(window).ready(tabManagerHandler.checkHash);
