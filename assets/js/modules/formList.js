function igniteFormList(listName)
{
	
	$("#"+listName+"InputField").on("keydown", { listName : listName }, addItemToList);
	$("#"+listName+"AddButton").on("click", { listName : listName }, addItemToList);
	
	$("#"+listName+"ItemsContainer .removeBtn").on("click", { listName : listName }, removeItemFromList);
}


function addItemToList(event)
{	
	if(event.type == "keydown" && event.keyCode != 13)
		return;
	
	event.preventDefault();
	
	var listName = event.data.listName;
	var itemValue = $("#"+listName+"InputField").val();
	var itemID = "Item"+Date.now();
	
	//Generate elements
	var item = '<div class="input-group" class="listElement" id="'+listName+itemID+'Display"><input type="text" class="form-control" disabled value="'+itemValue+'"><span class="input-group-btn"><button class="btn btn-outline-secondary removeBtn" type="button" data-listItem="'+listName+itemID+'"><i class="fa fa-times fa-fw"></i></button></span></div>';
	
	var hidden = `<input type="hidden" name="`+listName+`[]" id="`+listName+itemID+`" value="`+itemValue+`">`;
	
	$("#"+listName+"ItemsContainer").append(item);
	$("#"+listName+"ItemsHolder").append(hidden);
	
	$("#"+listName+itemID+"Display .removeBtn").on("click", { listName : listName }, removeItemFromList);
	
	$("#"+listName+"InputField").val("");
}



function removeItemFromList(event)
{
	event.preventDefault();
	
	console.log(event.data.listName);
	
	var listName = event.data.listName;
	var itemID = $(event.currentTarget).data("listitem"); 
	
	$("#"+itemID).remove();
	$("#"+itemID+"Display").remove();
}

