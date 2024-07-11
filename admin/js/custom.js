jQuery(document).ready(function ($) {
  // Initialize the dual listbox plugin

  var dualListbox = $(
    'select[name="duallistbox_demo1[]"]'
  ).bootstrapDualListbox({
    nonSelectedListLabel: "Available Payees",
    selectedListLabel: "Selected Payees",
    preserveSelectionOnMove: "moved",
    moveAllLabel: "Move all",
    removeAllLabel: "Remove all",
  });

  $("#department_select").on("change", function () {
    
    var departmentId = $(this).val();
    console.log("%c Line:4 🍰 departmentId", "color:#7f2b82", departmentId);
    var ajax_nonce = $('#ers_review_meta_nonce').val();

    $.ajax({
      url: ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "get_department_questions",
        department_id: departmentId,
        security: ajax_nonce,
      },
      success: function (response) {
        if (response.success) {
          var dualListbox = $("#dual_listbox");
          var userSelect = $("#user_selectbox");

          dualListbox.empty();
          userSelect.empty();

          var questionData = response.data[0].questionData;
          var udata = response.data[0].udata;

          userSelect.append(
            $("<option>", {
              value: "",
              text: "Please select",
            })
          );

          $.each(questionData, function (index, item) {
            dualListbox.append(
              $("<option>", {
                value: item.id,
                text: item.title,
              })
            );
          });

          $.each(udata, function (index, item) {
            userSelect.append(
              $("<option>", {
                value: item.uid,
                text: item.username,
              })
            );
          });

          // Refresh the dual listbox to reflect new options
          dualListbox.bootstrapDualListbox("refresh");
        } else {
          alert("Failed to fetch data");
        }
      },
    });
  });

  $("#user_selectbox").on("change", function () {
    console.log("user_selectbox");
    var userId = $(this).val();
    var ajax_nonce = $('#ers_review_meta_nonce').val();
    console.log("%c Line:4 🍰 departmentId", "color:#7f2b82", userId);

    $.ajax({
      url: ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "get_user_reviwer",
        user_id: userId,
        security: ajax_nonce,
      },
      success: function (response) {
        console.log("%c Line:78 🥤 response", "color:#fca650", response);

        if (response.success) {
          var reviewerlist = $("#reviewerlist");
          reviewerlist.empty();

          var udata = response.data;

          reviewerlist.append(
            $("<option>", {
              value: "",
              text: "Please select",
            })
          );

          $.each(udata, function (index, item) {
            reviewerlist.append(
              $("<option>", {
                value: item.uid,
                text: item.username,
              })
            );
          });
        } else {
          alert("Failed to fetch data");
        }
      },
    });
  });



  $("#ers_department").on("change", function () {
    console.log("user_selectbox");
    var department_id = $(this).val();
    var ajax_nonce = $('#search_nonce').val();
    
    console.log("%c Line:4 🍰 departmentId", "color:#7f2b82", department_id);

    $.ajax({
      url: ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "get_department_user",
        department_id: department_id,
        security: ajax_nonce,
      },
      success: function (response) {
        console.log("%c Line:78 🥤 response", "color:#fca650", response);

        if (response.success) {
          var userlist = $("#userlist");
          userlist.empty();

          var udata = response.data;

          userlist.append(
            $("<option>", {
              value: "",
              text: "Please select",
            })
          );

          $.each(udata, function (index, item) {
            userlist.append(
              $("<option>", {
                value: item.uid,
                text: item.username,
              })
            );
          });

         
        } else {
          alert("Failed to fetch data");
        }
      },
    });
  });


  $("#submit_feedback").on("click", function () {
    console.log("user_selectbox");
    var year_val = $('#yearlist').val();
    var department_val = $('#ers_department').val();
    var user_val = $('#userlist').val();

    console.log("%c Line:4 🍰 departmentId", "color:#7f2b82", year_val);

    $.ajax({
      url: ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "get_feedbackData",
        year_id: year_val,
        department_id: department_val,
        user_id: user_val,
        nonce: ajax_object.nonce,
      },
      success: function (response) {
        console.log("%c Line:78 🥤 response", "color:#fca650", response);

        if (response.success) {
        
          $("#employee-data-response").html(response.data);
           
        } else {
          alert("Failed to fetch data");
        }
      },
    });
  });


  $(".sendEmailbtn").click(function () {
     
    $.ajax({
      type: "post",
      dataType: "json",
      url: ajax_object.ajax_url,
      data: {
        action: "ers_sendEmailtousers",
        review_for_id: $(".sendEmailbtn").attr("data-review_for_id"),
        peer_review_id: $(".sendEmailbtn").attr("data-peer_review_id"),
        post_id: $(this).attr("post_id"),
        nonce: ajax_object.nonce,
        
      },
      success: function (response) {
        
        $(this).prop("disabled", true);
        $(this).text("Sent");
        $(this).css("background-color", "#7692d3");
      },
      statusCode: {
        404: function () {
          alert("page not found");
        },
        200: function (response) {
          console.log("%c Line:143 🍷 response", "color:#33a5ff", response);

          $(this).prop("disabled", true);
          $(this).text("Sent");
          $(this).css("background-color", "#7692d3");
          location.reload(true);
        },
      },
    });
  });

  var mediaUploader;

  $("#tf_review_fav_icon_button").click(function (e) {
    e.preventDefault();
    if (mediaUploader) {
      mediaUploader.open();
      return;
    }
    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: "Choose Fav Icon",
      button: {
        text: "Choose Fav Icon",
      },
      multiple: false,
    });
    mediaUploader.on("select", function () {
      var attachment = mediaUploader.state().get("selection").first().toJSON();
      $("#tf_review_fav_icon").val(attachment.url);
      $("#tf_review_fav_icon_preview").html(
        '<img src="' +
          attachment.url +
          '" style="max-width: 150px; height: auto;">'
      );
    });
    mediaUploader.open();
  });

  $("#tf_review_site_logo_button").click(function (e) {
    e.preventDefault();
    if (mediaUploader) {
      mediaUploader.open();
      return;
    }
    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: "Choose Site Logo",
      button: {
        text: "Choose Site Logo",
      },
      multiple: false,
    });
    mediaUploader.on("select", function () {
      var attachment = mediaUploader.state().get("selection").first().toJSON();
      $("#tf_review_site_logo").val(attachment.url);
      $("#tf_review_site_logo_preview").html(
        '<img src="' +
          attachment.url +
          '" style="max-width: 150px; height: auto;">'
      );
    });
    mediaUploader.open();
  });
 
 

});
