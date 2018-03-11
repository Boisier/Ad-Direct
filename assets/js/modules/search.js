function mainSearch(searchString)
{
	if(searchString.length === 0)
	{
		$(".broadcasterComponent, .searchComponent").stop();
	
		$(".searchComponent").fadeOut(250, function()
		{
			$(".broadcasterComponent").fadeIn(250);
		});
		
		return;
	}
	
	$(".broadcasterComponent").fadeOut(250, function()
  	{
		$(".searchComponent").fadeIn(250);
	});
	
	DLM.execute("/search/search/", {search: searchString}, false).done(function(data)
	{
		$("#searchSection").html(data);
	});
}