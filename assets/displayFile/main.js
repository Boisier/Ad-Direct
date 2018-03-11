//DownloadDefaultAds
//DownloadCreatives
//Display
/////


var campaignData;


/**
 * Beginning of operation to display the ad
 */
function startDisplay()
{
	log("Starting display procedures");
	//Retrieve Campaign Specs
	//Access the cach
	caches.open(CAMPAIGNCACHE).then(function(cache) 
	{
		cache.match(dataURL).then(function(response) 
		{
			if(response === undefined)
			{
				log("No campaign data found");
				retrieveCampaignData();
				return;
			}
			
			response.json().then(function(reponseData)
			{
				log("Campaign data found");
				
				if(currentTime - reponseData.time > 600)
				{

					//Force update this file as well
                    caches.delete(server + "/assets/displayFile/main.js");

					log("Campaign data too old, removing it");
					retrieveCampaignData();
					return
				}
				
				log("Campaign data OK");
				loadCampaignData().then(prepareDisplay);
			})
		});
	});
}




/**
 * Retrieve the campaign data
 */
function retrieveCampaignData()
{
	log("Retrieving campaign Data");
	
	var myHeaders = new Headers();
	//myHeaders.append('pragma', 'no-cache');
	//myHeaders.append('cache-control', 'no-cache');

	fetch(dataURL, {headers: myHeaders}).then(function(response) 
	{
		if(!response.ok) 
		{
			log("Couldn't update campaign data");
			loadCampaignData().then(prepareDisplay);
			return;
		}
		
		caches.open(CAMPAIGNCACHE).then(function(cache) 
		{
			cache.delete(dataURL).then(function() 
			{
				log("Campaign data updated");
				cache.put(dataURL, response).then(loadCampaignData).then(parseCampaignData);
			});
		});
	});
}



function loadCampaignData()
{
	return new Promise((resolve, reject) => {
		log("Loading campaign data");

		caches.open(CAMPAIGNCACHE).then(function(cache) 
		{
			cache.match(dataURL).then(function(response) 
			{
				response.json().then(function(reponseData) {
					campaignData = reponseData; 
					resolve();
				});
			});
		});
	});
}




function parseCampaignData()
{
	log("Parsing campaign Data");
	var currentCampaignContent = [];
	
	caches.open(CAMPAIGNCACHE).then(function(cache) 
	{	
		if(campaignData.status != "OK")
		{
			//The campaign ID does not exist anymore
			caches.delete(CAMPAIGNCACHE);
			
			registerDisplay("CAMPAIGN_ERROR");
			stopDisplay("Error with the campaign");
			
			return;
		}
		
		log("Parsing campaign ads");
		
		var ad;
		
		//Gather available creatives
		for(var adPos in campaignData.ads) 
		{
			ad = campaignData.ads[adPos];
			
			for(var screenID in ad.creatives)
			{	
				currentCampaignContent.push(createURL(ad.creatives[screenID].path));
			}
		}
		
		log("Parsing campaign default creatives");
		
		//Gather available default ads
		for(screenID in campaignData.screens) 
		{	
			if(campaignData.screens[screenID].defaultCreative === false)
				continue;
			
			currentCampaignContent.push(createURL(campaignData.screens[screenID].defaultCreative));
		}
		
		cache.keys().then(function(keys) 
		{
			log("Cleaning up the cache");	
			keys.forEach(function(request) 
			{
				elementIndex = currentCampaignContent.indexOf(request.url);
				
				if(elementIndex == -1)
				{
					//Remove old links
					cache.delete(request);
					return;
				}
				
				//Remove duplicates
				currentCampaignContent.splice(elementIndex.path, 1);
			});
			
			for(var path in currentCampaignContent)
			{	
				console.log(currentCampaignContent[path]);
			}
			
			console.log(currentCampaignContent);
			
			log("Adding " + currentCampaignContent.length + " new element.s to the cache");
			
			for(el in currentCampaignContent)
			{
				log("     "+currentCampaignContent[el]+"");	
			}
			
			cache.addAll(currentCampaignContent).then(function() 
			{
				cache.add(dataURL).then(function() {
					prepareDisplay();
				}).catch(errorWhileAddingToCache);				

			}).catch(errorWhileAddingToCache);
		});
	});
}



function createURL(URI)
{
	return server + URI;
}






function prepareDisplay()
{
	log("Preparing display");
	
	if(currentTime < campaignData.start || currentTime > campaignData.end)
	{
		//Out of the campaign interval let's stop here
		registerDisplay("OUT_OF_CAMPAIGN");
		return stopDisplay("notInCampaignInterval");
	}
	
	log("Campaign interval is okay");
	
	//Build the structure
	buildStructure();
	
	//Is there anything to show ?
	if(campaignData.ads.length == 0)
	{
		displayDefaultAds();
		return;
	}
	
	//Select Ad to play
	var adKey = selectAdToDisplay();
	
	localStorage.setItem("campaign-" + campaignID + "-last-displayed", adKey);
	log("Display ad " + adKey + " in loop");
	
	//Put the creatives in place
	log("placing the creatives");
	
	if(inDisplay)
	{
		registerDisplay("SKIPPED");
		stopDisplay("too late - screen already displayed - skip");
		return;
	}
	
	for(screenID in campaignData.ads[adKey].creatives)
	{
		placeCreative(campaignData.ads[adKey].creatives[screenID], screenID);
	}
	
	log("----- READY TO DISPLAY -----");
	
	registerDisplay(campaignData.ads[adKey].adID);
	
	readyToDisplay = true;
}




function buildStructure()
{
	log("Building display structure");
	
	var totalWidth = 0;
	var screenNbr = 0;
	
	for(screenID in campaignData.screens)
	{
		var screen = campaignData.screens[screenID];
		
		var screenBlock = document.createElement("div");
		screenBlock.className = "screen-block";
		screenBlock.setAttribute("id", "screen-block-" + screen.screenID);
		screenBlock.style.width = screen.screenWidth + "px";
		screenBlock.style.height = screen.screenHeight + "px";
		
		totalWidth += parseInt(screen.screenWidth);
		
		$("#display-block").append(screenBlock);
		
		++screenNbr;
	}
	
	$("#display-block").css("width", totalWidth + "px");
	
	log("Structure built (" + screenNbr + " screen.s)");
}





function displayDefaultAds()
{
	log("Displaying default ads");
	
	for(screenID in campaignData.screens)
	{
		if(campaignData.screens[screenID].defaultCreative === false)
		{
            registerDisplay("NO_DEFAULT_AD");
            return stopDisplay("No default ad to display");
		}
		
		var creativeBlock = buildPictureBlock(campaignData.screens[screenID].defaultCreative, screenID);
		$("#screen-block-" + screenID).html(creativeBlock);
	}
	
	registerDisplay("DEFAULT_AD");
}





function selectAdToDisplay()
{
	log("Selecting ad to play");
	
	var lastDisplayed = Number(localStorage.getItem("campaign-" + campaignID + "-last-displayed"));
	
	if(lastDisplayed === null)
		return 0; //No history, play the first of the array;
	
	if(lastDisplayed + 1 >= campaignData.ads.length)
		return 0; //End of loop, play the first of the array;
	
	return lastDisplayed + 1;
}





function placeCreative(creative, screenID)
{
	if(creative.mediaType == 1)
	{
		log("Screen " + screenID + " is a picture");
		
		buildPictureBlock(creative.path, screenID);
	}
	else if(creative.mediaType == 2)
	{
		log("Screen " + screenID + " is a video");
		
		buildVideoBlock(creative.path, screenID);
	}
}


function buildPictureBlock(url, screenID)
{
	block = document.createElement("img");
	block.src = createURL(url);
	block.width = campaignData.screens[screenID].screenWidth;
	block.height = campaignData.screens[screenID].screenHeight;
	block.className = "screen-picture";
	
	$("#screen-block-" + screenID).html(block);
}



function buildVideoBlock(url, screenID)
{
	if(videoEngine == "html")
	{
		block = document.createElement("video");
		block.src = createURL(url);
		block.width = campaignData.screens[screenID].screenWidth;
		block.height = campaignData.screens[screenID].screenHeight;
		block.setAttribute("preload", "auto");
		block.className = "screen-video"
		block.setAttribute("crossOrigin", "anonymous");

		$("#screen-block-" + screenID).html(block);
	}
	else if(videoEngine == "flash")
	{
		$("#screen-block-" + screenID).flowplayer({
			swf: "/assets/third-parties/flowplayer/flowplayer.swf",
			ratio: false,
			clip: {
				sources: [
					  { type: "video/mp4",
						src:  url,
						engine: "flash" }
				]
			}
		});
		
		$("#screen-block-" + screenID + " .fp-engine").css({
			width: campaignData.screens[screenID].screenWidth + "px",
			height: (campaignData.screens[screenID].screenHeight * 1.2) + "px",
			marginTop: (campaignData.screens[screenID].screenHeight * -0.1) + "px"
		});
	}
}


function BroadSignPlay()
{
	log("BroadSignPlay() fired");
	
	inDisplay = true;
	
	if(!readyToDisplay)
	{
		registerDisplay("SKIPPED");
		stopDisplay("Content not ready");
	}
	
	log("----- DISPLAYING -----");
	
	if(videoEngine == "html")
	{
        $(".screen-video").each(function() { $(this).get(0).load(); })
        $(".screen-video").each(function() { $(this).get(0).play(); })

		$(".screen-video").on("ended", videoEnded);
	}
}


function videoEnded()
{
	if(displayEnded)
		return;
	
	displayEnded = true;
	stopDisplay("The first video has ended");
}




function registerDisplay(ref)
{	
	//Get the history
	var campaignHistory = JSON.parse(localStorage.getItem("campaign-" + campaignID + "-history"));
	
	//Build needed structure
	if(campaignHistory === null)
	{
		campaignHistory = {
			lastPush: currentTime,
			ballID: Date.now() + "-" + frameID,
			frameID: displayUnitID,
			history: []
		};
	}
	
	//Register current display
	campaignHistory.history.push([ref, currentTime]);
	
	//Save new campaignHistory
	localStorage.setItem("campaign-" + campaignID + "-history", JSON.stringify(campaignHistory));
	
	//Do we need to send it ?
	/*if(currentTime - campaignHistory.lastPush < 600)
		return;*/
	//Let's try to do real-time : send the view each time.
	//The no-connection fallback is conserved
	
	$.ajax({
		method: "POST",
		url: uplinkURL,
		data: campaignHistory
	}).done(function (data) 
	{
		localStorage.removeItem("campaign-" + campaignID + "-history");
	});
}



function errorWhileAddingToCache(e)
{
	console.log(e);
	registerDisplay("CANNOT_ADD_TO_CACHE");
	stopDisplay("Error while adding to cache");
}