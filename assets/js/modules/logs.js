function getNextLogPage(userID)
{
    var nextPage = currentLogPage + 1;
    DLM.execute("/record/getpage/" + userID + "/" + nextPage).done(function(response)
    {
        console.log(response);

        if(!response.success)
            return;

        currentLogPage++;

        if(response.end)
        {
            //Hide "Show more" Btn
            $("#ShowMoreLogsBtn").fadeOut(150);
            $("#endOfLogs").fadeIn(150);
        }

        //Append lines
        $("#logTable tbody").append(response.html);
    });
}

