function generateModal(modalContent)
{
	$("body").append(modalContent);
	
	$("#mainModal").modal('toggle');
	$("#mainModal").on("hidden.bs.modal", function(e) {
		$("#mainModal").remove();
	})
}