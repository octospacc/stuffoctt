// const response = document.getElementById("response");

function hideError() {
  $("#response-error").fadeOut(600, function() {
    $("#response-error").css("display", "none");
  });
}

function updateData() {
  // var title = $("#pTitle").val();
  // var url = $("#pURL").val();
  // var content = $("#wmd-input").val();
  // var description = $("#pMeta").val();
  // var tag = $("#pTag").val();
  // var category = $("#pCategory").val();
  // var posttype = $("#pType").val();
  // var pimage = $("#pImage").val();
  // var paudio = $("#pAudio").val();
  // var pvideo = $("#pVideo").val();
  // var pquote = $("#pQuote").val();
  // var plink = $("#pLink").val();
  // var pDate = $("#pDate").val();
  // var pTime = $("#pTime").val();
  // var oldfile = $("#oldfile").val();
  // var dateTime = pDate + " " + pTime;
  // var autoSave = 'autoSave';

  // Prepare data to send to PHP
  var data = {
    title: $("#pTitle").val(),
    url: $("#pURL").val(),
    content: $("#wmd-input").val(),
    description: $("#pMeta").val(),
    tag: $("#pTag").val(),
    category: $("#pCategory").val(),
    posttype: $("#pType").val(),
    pimage: $("#pImage").val(),
    paudio: $("#pAudio").val(),
    pvideo: $("#pVideo").val(),
    pquote: $("#pQuote").val(),
    plink: $("#pLink").val(),
    dateTime: $("#pDate").val() + " " + $("#pTime").val(),
    autoSave: 'autoSave',
    addEdit: addEdit,
    oldfile: $("#oldfile").val(),
	parent_page: parent_page
  };
  
  $.ajax({
    url: base_path + 'admin/autosave',
    type: "POST",
    data: data,
    success: function(response) {
      $("#response").html(response.message);
      $("#oldfile").val(response.file);
      hideError();
      $("#response").fadeIn(600, function() {
        $("#response").css("display", "block");
      });
      setTimeout(function() {
        $("#response").fadeOut(600, function() {
          $("#response").css("display", "none");
        });
      }, 6000);
    },
    error: function(response) {
      var httpError = "";
      if (response.status !== 0) {
        httpError += ": " + response.status;
        if (response.responseText) {
          httpError += " " + response.responseText;
        }
      }
      $("#response-error").html("Error in Autosaving: " + response.statusText + httpError);
      $("#response-error").fadeIn(600, function() {
        $("#response-error").css("display", "block");
      });
      setTimeout(hideError, saveInterval/2);
    }
  });
}

$(document).ready(function() {
  setInterval(updateData, saveInterval);
});
