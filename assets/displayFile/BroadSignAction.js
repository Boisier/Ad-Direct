// jQuery BroadSign Player API
// Plugin by Frank JE Flitton
// https://github.com/FrankFlitton/jQuery-BroadSign-Player-API


// Initiate connection to broadsign server

var BroadSignSocket = new WebSocket("ws://localhost:2326");
console.log("BroadSign Socket Init");


var BSState = "";
(function($) {
    $.fn.BroadSignState = function( options ) {
        
        if (BroadSignSocket.readyState == 0) {
            BSState = "CONNECTING";
        } else if (BroadSignSocket.readyState == 1) {
            BSState = "OPEN";
        } else if (BroadSignSocket.readyState == 2) {
            BSState = "CLOSING";
        } else if (BroadSignSocket.readyState == 3) {
            BSState = "CLOSED";
        } else { 
            
        };   
        
        console.log("BroadSign Socket Status: " + BSState);
        
      };
}(jQuery));

// Print state of Websocket to console, for debugging purposes
        $.fn.BroadSignState();

// Listen and print to console
BroadSignSocket.onmessage = function (WSMessage) {
    log("BroadSign Socket Message: " + WSMessage.data);
};
// Listen and print to console
BroadSignSocket.onopen = function (WSMessage) {
    log("BroadSign Socket Status: OPEN");
};
BroadSignSocket.onclose = function (WSMessage) {
    log("BroadSign Socket Status: CLOSED");
};

BroadSignSocket.onerror = function (WSMessage) 
{
	log("BroadSign Socket Status: ERROR");
};


(function($) {
  
  $.fn.BroadSignAction = function( options ) {
      
        console.log("Start BroadSign Socket Call");
      
    // Print state of Websocket to console, for debugging purposes
        $.fn.BroadSignState();
    
    // Unix time stamp for unique calls as default
        var callID = new Date().getTime(); 
    
    // Establish our default settings
        var settings = {
            "action"     : "stop",
            "frame_id"   : null,
            "enabled"    : null,
            "name"       : null,
            "id"       : callID
        };
    
    // User Overides
        var userSettings = $.extend(settings, options);
    
    // Validation 
    
    var callId = "id=\"" + userSettings.id + "\" ";
    var callAction = "action=\"" + userSettings.action + "\" ";
    
    var callFrame_id = "";
    if (null != userSettings.frame_id) {
      var callFrame_id = "frame_id=\"" + userSettings.frame_id + "\" ";
    }
      
    var callEnabled = "";
    if (null != userSettings.enabled) {
      var callEnabled = "enabled=\"" + userSettings.enabled + "\" ";
    }

    var callName = "";
    if (null != userSettings.name) {
      var callName = "name=\"" + userSettings.name + "\" ";
    }

    // Package to send to BroadSign
    var callXML = "<rc version=\"1\" " + callId + callAction + callFrame_id + callEnabled + callName + "/>\r\n\r\n"; 
    // Frameid is ommitted. Not needed as content is fullscreen.
    console.log("To send: " + callXML);
    BroadSignSocket.send(callXML);
    console.log("Sent");

    return this;
  };


}(jQuery));