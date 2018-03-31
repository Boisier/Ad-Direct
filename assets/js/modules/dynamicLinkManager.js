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
  this.go = function (link, data = null, isFormData = false) {
    $("#mainContainer").hide();
    $("#loadContainer").css("display", "flex");

    this.execute(link, data, isFormData).done(function (data) {
      $("#mainContainer").html(data);
      $("#loadContainer").hide();
      $("#mainContainer").show();
    }).fail()
    {
      //$("#mainContainer").html("error");
      $("#loadContainer").hide();
      $("#mainContainer").show();
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
  this.execute = function (link, data = null, isFormData = false) {
    //Abort ongoing queries
    if (this.loading == true)
      this.xhrObject.abort();
    else
      DLM.loading = true;

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
    if (this.loading == true)
      this.xhrObject.abort();
    else
      DLM.loading = true;


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

    if (this.loading == true)
      this.xhrObject.abort();
    else
      DLM.loading = true;

    this.jqXHR = $.ajax({
      method: "POST",
      url: url,
      data: formDataEl,
      processData: false,
      contentType: false,
      xhr: function () {
        var jqXHR = null;
        if (window.ActiveXObject) {
          jqXHR = new window.ActiveXObject("Microsoft.XMLHTTP");
        }
        else {
          jqXHR = new window.XMLHttpRequest();
        }
        //Upload progress
        jqXHR.upload.addEventListener("progress", function (e) {
          if (e.lengthComputable) {
            var percentage = Math.round((e.loaded * 100) / e.total);

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
