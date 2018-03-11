function addEmptyAd (campaignID) {
  let url = '/ad/create/' + campaignID
  DLM.execute(url).done(function (data) {
    try {
      var response = JSON.parse(data)
    }
    catch (e) {
      return
    }
    $('#adList').append(response.html)

    if (response.hideAddBtn == true)
      $('#addAdBtn').hide()
  });
}


var currentUploadAdID, currentUploadscreenID;

function uploadCreativeFromInput (adID, screenID) {
  var formName = '#uploadForm' + adID + '-' + screenID;
  var creativeForm = $(formName);
  
  currentUploadAdID = adID;
  currentUploadscreenID = screenID;

  let form = new FormData(creativeForm[0])
  var url = creativeForm.attr('action');
  
  sendCreative(form, url);
}

$(function () {
  $(document).on('dragenter dragstart dragend dragleave dragover drag drop', function (e) {
    e.preventDefault();
    e.stopPropagation();
  });
})

function prepareDropArea (adID, screenID) {
  var comboID = adID + '-' + screenID;
  
  $('#dropArea' + comboID).on('dragover dragenter', function () {
    $(this).addClass('onDrop');
  }).on('dragleave dragend', function () {
    $(this).removeClass('onDrop');
  }).on('drop', function (e) {
    e.preventDefault();
    e.stopPropagation();
    
    $(this).removeClass('onDrop');
    
    var files = e.originalEvent.dataTransfer;
    
    if (typeof files.files[0] !== 'undefined') {
      var form = new FormData();
      
      form.append('creative', files.files[0]);
      
      var adID = $(this).data('adid');
      var screenID = $(this).data('screenid');
      
      currentUploadAdID = adID;
      currentUploadscreenID = screenID;
      
      var url = $('#uploadForm' + adID + '-' + screenID).attr('action');
      
      sendCreative(form, url);
      return;
    }
    
    $(this).removeClass('onDrop');
  });
}


var storedCaption;

function sendCreative (form, url) {
  var dropAreaID = '#dropArea' + currentUploadAdID + '-' + currentUploadscreenID;
  
  storedCaption = $('#dropArea' + currentUploadAdID + '-' + currentUploadscreenID + ' .addMsg').html();
  
  $('.dropArea:visible').not('#dropArea' + currentUploadAdID + '-' + currentUploadscreenID).fadeTo(100, 0).addClass('toShowAgain');
  
  var inProgressCallback = function (percentage) {
    var dropAreaID = '#dropArea' + currentUploadAdID + '-' + currentUploadscreenID;
    
    $(dropAreaID + ' .addMsg').html(percentage + '%');
  };
  
  var doneCallback = function (response) {
    $('.toShowAgain').removeClass('toShowAgain').fadeTo(100, 1);
    
    if (response === null || typeof response !== 'object') {
      //Error in the response, do nothing
      return;
    }
    
    //response ok, update block
    var comboID = currentUploadAdID + '-' + currentUploadscreenID;
    
    if (response.success == false) {
      $('#dropArea' + comboID + ' .addMsg').html(storedCaption);
      generateModal(response.html);
      return;
    }
    
    $('#adBlock' + currentUploadAdID).replaceWith(response.html);
    
  }
  
  var errorCallback = function (response) {
    var comboID = currentUploadAdID + '-' + currentUploadscreenID;
    $('#dropArea' + comboID + ' .addMsg').html(storedCaption);
    generateModal(response.html);
  }
  
  DLM.advancedSendForm(form, url, inProgressCallback, doneCallback, errorCallback);
}
