'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

function addEmptyAd(campaignID) {
  var url = '/ad/create/' + campaignID;
  DLM.execute(url).done(function (data) {
    try {
      var response = JSON.parse(data);
    } catch (e) {
      return;
    }
    $('#adList').append(response.html);

    if (response.hideAddBtn == true) $('#addAdBtn').hide();
  });
}

var currentUploadAdID, currentUploadscreenID;

function uploadCreativeFromInput(adID, screenID) {
  var formName = '#uploadForm' + adID + '-' + screenID;
  var creativeForm = $(formName);

  currentUploadAdID = adID;
  currentUploadscreenID = screenID;

  var form = new FormData(creativeForm[0]);
  var url = creativeForm.attr('action');

  sendCreative(form, url);
}

$(function () {
  $(document).on('dragenter dragstart dragend dragleave dragover drag drop', function (e) {
    e.preventDefault();
    e.stopPropagation();
  });
});

function prepareDropArea(adID, screenID) {
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

function sendCreative(form, url) {
  var dropAreaID = '#dropArea' + currentUploadAdID + '-' + currentUploadscreenID;

  storedCaption = $('#dropArea' + currentUploadAdID + '-' + currentUploadscreenID + ' .addMsg').html();

  $('.dropArea:visible').not('#dropArea' + currentUploadAdID + '-' + currentUploadscreenID).fadeTo(100, 0).addClass('toShowAgain');

  var inProgressCallback = function inProgressCallback(percentage) {
    var dropAreaID = '#dropArea' + currentUploadAdID + '-' + currentUploadscreenID;

    $(dropAreaID + ' .addMsg').html(percentage + '%');
  };

  var doneCallback = function doneCallback(response) {
    $('.toShowAgain').removeClass('toShowAgain').fadeTo(100, 1);

    if (response === null || (typeof response === 'undefined' ? 'undefined' : _typeof(response)) !== 'object') {
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
  };

  var errorCallback = function errorCallback(response) {
    var comboID = currentUploadAdID + '-' + currentUploadscreenID;
    $('#dropArea' + comboID + ' .addMsg').html(storedCaption);
    generateModal(response.html);
  };

  DLM.advancedSendForm(form, url, inProgressCallback, doneCallback, errorCallback);
}
'use strict';

function setBroadcasterTab(tabID) {
  console.log(tabID);

  if (tabID === currentBroadcasterTab) {
    return;
  }

  $('.broadcaster').stop();

  var oldTab = currentBroadcasterTab;
  currentBroadcasterTab = tabID;

  $('.' + oldTab + '-tab').removeClass('active');
  $('.' + currentBroadcasterTab + '-tab').addClass('active');

  $('.' + oldTab + '-broadcaster').fadeOut(250, function () {
    $('.' + currentBroadcasterTab + '-broadcaster').fadeIn(250);
  });
}
"use strict";

function DynamicLinkManager() {

  this.xhrObject = null;
  this.loading = false;

  this.advancedProgressCallback;
  this.advancedDoneCallback;
  this.advancedFailCallback;

  /**
   * Change current "page" by the one at the given link
   * @param {string}  link       The new page link
   * @param {mixed}   data       = null        Data to pass as POST
   * @param {boolean} isFormData = false is the data a formData or not
   */
  this.go = function (link) {
    var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var isFormData = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

    $("#mainContainer").css("display", "none");
    $("#loadContainer").css("display", "flex");

    this.execute(link, data, isFormData).done(function (data) {
      $("#mainContainer").html(data);
      $("#loadContainer").css("display", "none");
      $("#mainContainer").css("display", "block");
    }).fail();
    {
      //$("#mainContainer").html("error");
      $("#loadContainer").css("display", "none");
      $("#mainContainer").css("display", "block");
    }
    ;
  };

  this.modal = function (url) {
    this.execute(url).done(generateModal);
  };

  /**
   * @param {string} link
   * @param {object} data
   * @param {bool} isFormData
   * @return {}
   */
  this.execute = function (link) {
    var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var isFormData = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

    //Abort ongoing queries
    if (this.loading == true) this.xhrObject.abort();else DLM.loading = true;

    //set up request params
    var content = "application/x-www-form-urlencoded; charset=UTF-8";
    var processData = true;

    if (isFormData) {
      content = false;
      processData = false;
    }

    //Execute the query and handle response
    this.xhrObject = $.ajax({
      method: "POST",
      url: link,
      data: data,
      processData: processData,
      contentType: content
    }).always(function (data) {
      this.loading = false;
    });

    return this.xhrObject;
  };

  /**
   * Send a form
   * @param {[[Type]]} formName [[Description]]
   */
  this.sendForm = function (formName) {
    var formElement = $("form[name=" + formName + "]");

    var form = new FormData(formElement[0]);
    var url = formElement.attr("action");

    $("#mainContainer").hide();
    $("#loadContainer").css("display", "flex");

    //Clear current loading if needed
    if (this.loading == true) this.xhrObject.abort();else DLM.loading = true;

    this.xhrObject = $.ajax({
      method: "POST",
      url: url,
      data: form,
      processData: false,
      contentType: false
    }).done(function (data, textStatus, xhr) {
      $("#mainContainer").html(data);
      $("#loadContainer").hide();
      $("#mainContainer").show();
    }).fail(function (xhr, textStatus, data) {
      $(".form-alert").hide();
      //$("#" + xhr.responseText + "Error").show();

      $("#loadContainer").hide();
      $("#mainContainer").show();
    }).always(function () {
      this.loading = false;
    });
  };

  /**
   * Send a form
   * @param {[[Type]]} formName [[Description]]
   */
  this.advancedSendForm = function (formDataEl, url, inProgressCallback, doneCallback, failCallback) {
    this.advancedProgressCallback = inProgressCallback;
    this.advancedDoneCallback = doneCallback;
    this.advancedFailCallback = failCallback;
    //Clear current loading if needed

    if (this.loading == true) this.xhrObject.abort();else DLM.loading = true;

    this.jqXHR = $.ajax({
      method: "POST",
      url: url,
      data: formDataEl,
      processData: false,
      contentType: false,
      xhr: function xhr() {
        var jqXHR = null;
        if (window.ActiveXObject) {
          jqXHR = new window.ActiveXObject("Microsoft.XMLHTTP");
        } else {
          jqXHR = new window.XMLHttpRequest();
        }
        //Upload progress
        jqXHR.upload.addEventListener("progress", function (e) {
          if (e.lengthComputable) {
            var percentage = Math.round(e.loaded * 100 / e.total);

            DLM.advancedProgressCallback(percentage);
          }
        }, false);

        return jqXHR;
      }
    }).always(function () {
      this.loading = false;
    }).done(this.advancedDoneCallback).fail(this.advancedFailCallback);
  };
}

var DLM = new DynamicLinkManager();
"use strict";

function igniteFormList(listName) {

	$("#" + listName + "InputField").on("keydown", { listName: listName }, addItemToList);
	$("#" + listName + "AddButton").on("click", { listName: listName }, addItemToList);

	$("#" + listName + "ItemsContainer .removeBtn").on("click", { listName: listName }, removeItemFromList);
}

function addItemToList(event) {
	if (event.type == "keydown" && event.keyCode != 13) return;

	event.preventDefault();

	var listName = event.data.listName;
	var itemValue = $("#" + listName + "InputField").val();
	var itemID = "Item" + Date.now();

	//Generate elements
	var item = '<div class="input-group" class="listElement" id="' + listName + itemID + 'Display"><input type="text" class="form-control" disabled value="' + itemValue + '"><span class="input-group-btn"><button class="btn btn-outline-secondary removeBtn" type="button" data-listItem="' + listName + itemID + '"><i class="fa fa-times fa-fw"></i></button></span></div>';

	var hidden = "<input type=\"hidden\" name=\"" + listName + "[]\" id=\"" + listName + itemID + "\" value=\"" + itemValue + "\">";

	$("#" + listName + "ItemsContainer").append(item);
	$("#" + listName + "ItemsHolder").append(hidden);

	$("#" + listName + itemID + "Display .removeBtn").on("click", { listName: listName }, removeItemFromList);

	$("#" + listName + "InputField").val("");
}

function removeItemFromList(event) {
	event.preventDefault();

	console.log(event.data.listName);

	var listName = event.data.listName;
	var itemID = $(event.currentTarget).data("listitem");

	$("#" + itemID).remove();
	$("#" + itemID + "Display").remove();
}
"use strict";

function getNextLogPage(userID) {
    var nextPage = currentLogPage + 1;
    DLM.execute("/record/getpage/" + userID + "/" + nextPage).done(function (response) {
        console.log(response);

        if (!response.success) return;

        currentLogPage++;

        if (response.end) {
            //Hide "Show more" Btn
            $("#ShowMoreLogsBtn").fadeOut(150);
            $("#endOfLogs").fadeIn(150);
        }

        //Append lines
        $("#logTable tbody").append(response.html);
    });
}
"use strict";

function generateModal(modalContent) {
	$("body").append(modalContent);

	$("#mainModal").modal('toggle');
	$("#mainModal").on("hidden.bs.modal", function (e) {
		$("#mainModal").remove();
	});
}
"use strict";

function generatePassword(fieldID) {
    var length = 8,
        charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!?$&#-_",
        retVal = "";

    for (var i = 0, n = charset.length; i < length; ++i) {
        retVal += charset.charAt(Math.floor(Math.random() * n));
    }

    $("#" + fieldID).val(retVal);
}
"use strict";

function mainSearch(searchString) {
	if (searchString.length === 0) {
		$(".broadcasterComponent, .searchComponent").stop();

		$(".searchComponent").fadeOut(250, function () {
			$(".broadcasterComponent").fadeIn(250);
		});

		return;
	}

	$(".broadcasterComponent").fadeOut(250, function () {
		$(".searchComponent").fadeIn(250);
	});

	DLM.execute("/search/search/", { search: searchString }, false).done(function (data) {
		$("#searchSection").html(data);
	});
}
"use strict";

function tabManager() {
	var currentTab = "";

	this.checkHash = function () {
		var hash = window.location.hash.substring(1);

		$(".nav-btn.open").removeClass("open");

		if (hash.length > 0 && currentTab != hash) {
			currentTab = hash;
		} else {
			currentTab = "overview";
		}

		$("#" + currentTab + "Btn").addClass("open");

		if ($.isNumeric(currentTab)) {
			DLM.go("/campaign/display/" + currentTab + "/");
		} else {
			DLM.go("/home/" + currentTab + "/");
		}
	};
}

var tabManagerHandler = new tabManager();

$(window).on("hashchange", tabManagerHandler.checkHash);
$(window).ready(tabManagerHandler.checkHash);
