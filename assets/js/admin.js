jQuery(document).ready(function ($) {
  window.errpr =
    '<div class="ui basic red pointing prompt label transition visible error_message">{error}</div>';
  $(".ui.form").removeClass("loading");
  $(".ui.dropdown").dropdown();
  var question_count = 0;
  var location_count = 0;
  $("#questions").sortable();
  $("#questions").disableSelection();
  $(".ui.accordion").accordion();
  $("#menu-posts-questions .wp-submenu.wp-submenu-wrap").prepend(
    $("#menu-posts-questions .wp-submenu li:nth-child(4)").detach()
  );
  $("#menu-posts-questions .wp-submenu.wp-submenu-wrap").show();
  dragula(
    [
      document.getElementById("categoryquestions"),
      document.getElementById("tf_reviewsystemquestions"),
    ],
    {
      moves: function (el, source, handle, sibling) {
        return true;
      },
    }
  )
    .on("drag", function (el) {
      // add 'is-moving' class to element being dragged
      el.classList.add("is-moving");
    })
    .on("dragend", function (el) {
      // remove 'is-moving' class from element after dragging has stopped
      el.classList.remove("is-moving");
      // add the 'is-moved' class for 600ms then remove it
      window.setTimeout(function () {
        el.classList.add("is-moved");
        window.setTimeout(function () {
          el.classList.remove("is-moved");
        }, 600);
      }, 100);
    })
    .on("drop", function (el, target, source, sibling) {
      var tf_reviewsystemids = $("#tf_reviewsystemids").val();
      var tf_reviewsystemidArray = tf_reviewsystemids.split(",");
      if ($(target).attr("id") == "tf_reviewsystemquestions") {
        var questionlist = $("#tf_reviewsystemquestions > .accordion");
        var questionlistsort = [];
        $.each(questionlist, function (i, item) {
          questionlistsort.push($(item).attr("id"));
        });
        $("#tf_reviewsystemids").val("");
        $("#tf_reviewsystemids").val(questionlistsort.join());
      } else {
        var tf_reviewsystemids = $("#tf_reviewsystemids").val();
        var unusednumberArr = tf_reviewsystemids.split(",");
        var selected = $(el).attr("id");
        unusednumberArr = jQuery.grep(unusednumberArr, function (value) {
          return value != selected;
        });
        $("#tf_reviewsystemids").val(unusednumberArr.join(","));
      }
    });
  $("#SurveyGenDropdown").dropdown({
    onChange: function (val) {
      $("#categoryquestions_loader").addClass("loading");
      $.ajax({
        type: "post",
        dataType: "json",
        url: myAjax.ajaxurl,
        data: { action: "getquestions", post_id: val },
        success: function (response) {
          if (response.success == true) {
            $("#categoryquestions").empty();
            $("#categoryquestions").append(response.data.data);
            var tf_reviewsystemids = $("#tf_reviewsystemids").val();
            var unusednumberArr = tf_reviewsystemids.split(",");
            $.each(response.data.ids, function (i, item) {
              if (unusednumberArr.indexOf(String(item)) !== -1) {
                $("#categoryquestions " + "#" + item).hide();
              }
            });
            $("#tf_reviewsystemids").val().split(",").indexOf(319);
            $(".ui.accordion").accordion();
            $("#categoryquestions_loader").removeClass("loading");
          }
        },
      });
    },
  });
  if ($("#generatetf_reviewsystem").length == 0) {
  } else {
    $.ajax({
      type: "post",
      dataType: "json",
      url: myAjax.ajaxurl,
      data: { action: "getSurveyQuestion", post_id: $("#post_ID").val() },
      success: function (response) {
        if (response.success == true) {
          $("#tf_reviewsystemquestions").append(response.data.data);
          var tf_reviewsystemids = $("#tf_reviewsystemids").val();
          var unusednumberArr = tf_reviewsystemids.split(",");
          var postids = jQuery.parseJSON(atob(response.data.ids));
          $.each(postids, function (i, item) {
            if ($("#tf_reviewsystemids").val() == "") {
              $("#tf_reviewsystemids").val(item);
            } else {
              $("#tf_reviewsystemids").val(
                $("#tf_reviewsystemids").val() + "," + item
              );
            }
          });
          $(".ui.accordion").accordion();
          $("#categoryquestions_loader").removeClass("loading");
        }
      },
    });
  }
  $("#tf_reviewsystem_title").keyup(function () {
    $("#title").val(this.value);
  });
  window.validate = function () {
    //alert($(".ui.dropdown.selection").attr("name"));
    var form = $("form[name='post']");
    $("#titlediv").addClass("ui form");
    $("#titlewrap").addClass("field");
    $("form[name='post']").form({
      on: "submit",
      inline: true,
      fields: {
        post_title: {
          identifier: "post_title",
          rules: [
            {
              type: "empty",
              prompt: "Please enter your {name}",
            },
          ],
        },
        code: {
          identifier: "code",
          rules: [
            {
              type: "empty",
              prompt: "Please enter your code",
            },
          ],
        },
        contract_arrangement: {
          identifier: "contract_arrangement",
          rules: [
            {
              type: "empty",
              prompt: "Please enter your Contract Arrangement",
            },
          ],
        },
        contract_type: {
          identifier: "contract_type",
          rules: [
            {
              type: "empty",
              prompt: "Please enter your Contract Type",
            },
          ],
        },
        company_type: {
          identifier: "company_type",
          rules: [
            {
              type: "empty",
              prompt: "Please enter your Company Type",
            },
          ],
        },
        "questions[type]": {
          identifier: "questions[type]",
          rules: [
            {
              type: "empty",
              prompt: "Please Select Type",
            },
          ],
        },
        department: {
          identifier: "department",
          rules: [
            {
              type: "empty",
              prompt: "Please Select Category",
            },
          ],
        },
        /*location:{
					identifier: 'location',
					rules: [{
						type   : 'empty',
						prompt : 'Please Select Location'
					}]
				},*/
        clocations: {
          identifier: "clocations",
          rules: [
            {
              type: "empty",
              prompt: "Please Select Location",
            },
          ],
        },
        /*headcount:{
					identifier: 'headcount',
					rules: [{
					  	type   : 'empty',
					  	prompt : 'Please enter your Headcount'
					}]	
				},*/
        cheadcount: {
          identifier: "cheadcount",
          rules: [
            {
              type: "empty",
              prompt: "Please enter your Headcount",
            },
          ],
        },
        tf_reviewsystem_title: {
          identifier: "tf_reviewsystem_title",
          rules: [
            {
              type: "empty",
              prompt: "Please enter tf_reviewsystem title",
            },
          ],
        },
        tf_reviewsystem: {
          identifier: "tf_reviewsystem",
          rules: [
            {
              type: "empty",
              prompt: "Please Select tf_reviewsystem",
            },
          ],
        },
        user: {
          identifier: "user",
          rules: [
            {
              type: "empty",
              prompt: "Please Select User",
            },
          ],
        },
      },
    });
  };

  window.arraytobinary = function (arrayvalues) {
    return btoa(JSON.stringify(arrayvalues));
  };

  window.binarytoarray = function (binaryvalues) {
    return JSON.parse(atob(binaryvalues));
  };

  $(".company_location").dropdown({
    onChange: function (v, a, b) {
      var selectedlocation = [];
      if ($("#company_location").val() != "") {
        selectedlocation = binarytoarray($("#company_location").val());
      }
      var selectedindex = $(this)
        .find("option[value=" + $(b).data("value") + "]")
        .data("id");
      selectedlocation[selectedindex] = v;
      $("#company_location").val(arraytobinary(selectedlocation));
      $(".new-location-button").removeAttr("disabled");
    },
  });

  function addLocation(Location, location_val = "", headcount_val = "") {
    error = false;
    $(".error_message").remove();
    ////console.log($(".company_location").val());
    var clocation = $(".company_location");
    $.each(clocation, function (k, option) {
      if ($(this).dropdown("get value") == "") {
        $(this).after(window.errpr.replace("{error}", "Select Location"));
        error = true;
        return false;
      }
    });

    if (error == false) {
      $(".company_location").addClass("disabled");

      html =
        '<div class="fields" id="location' +
        Location +
        '">' +
        '<div class="eight wide field">' +
        "<label>Locations</label>" +
        '<select class="ui dropdown company_location" data-validate="clocations" data-id="' +
        Location +
        '" name="locations[' +
        Location +
        ']" required>' +
        addLocationOption(Location, location_val) +
        "</select>" +
        "</div>" +
        '<div class="six wide field">' +
        "<label>Headcount</label>" +
        '<input type="text" data-validate="cheadcount" name="Headcount[' +
        Location +
        ']" value="' +
        headcount_val +
        '" placeholder="Headcount" required/>' +
        "</div>" +
        '<div class="two wide field">' +
        '<button class="ui red icon button option-delete" style="margin-top:24px;" Onclick="deleteLocation(' +
        Location +
        ')"><i class="trash icon"></i></button>' +
        "</div>" +
        '<script type="text/javascript">jQuery( document ).ready(function($) {$(".company_location").dropdown({onChange:function(a,o,n){var i=[];""!=$("#company_location").val()&&(i=binarytoarray($("#company_location").val())),i[$(this).find("option[value="+$(n).data("value")+"]").data("id")]=a,$("#company_location").val(arraytobinary(i)),$(".new-location-button").removeAttr("disabled");}}); var form = $(\'form[name="post"]\');form.form("add rule","Headcount[' +
        Location +
        ']",{rules:[{type:"empty",prompt:"Please enter your Headcount"}]});});</script>';

      ("</div>");
      $("#extrafields").append(html);
    }

    window.validate();
  }

  function addLocationOption(key, value = "") {
    var selectedlocation = [];
    if ($("#company_location").val() != "") {
      selectedlocation = binarytoarray($("#company_location").val());
    }
    //console.log(selectedlocation);
    var html;
    var locations = jQuery.parseJSON(atob($("#locations").val()));
    html += '<option class="default text" value="">Select Location</option>';
    $.each(locations, function (k, option) {
      //console.log(selectedlocation.includes(option.ID));
      if (selectedlocation.includes(option.ID.toString()) != true) {
        if (option.ID == value) {
          html +=
            '<option data-id="' +
            key +
            '" value="' +
            option.ID +
            '" selected>' +
            option.post_title +
            "</option>";
        } else {
          html +=
            '<option data-id="' +
            key +
            '" value="' +
            option.ID +
            '">' +
            option.post_title +
            "</option>";
        }
      }
    });

    return html;
  }
  $("#location").on("click", ".new-location-button", function (event) {
    event.preventDefault();

    var unusednumber = $("#location_removed").val();

    if (unusednumber != "") {
      var unusednumberArr = unusednumber.split(",");

      var selected = unusednumberArr[0];

      unusednumberArr = jQuery.grep(unusednumberArr, function (value) {
        return value != selected;
      });

      $("#location_removed").val(unusednumberArr.join(","));

      addLocation(selected);

      $(this).attr("disabled", true);
      return true;
    }

    if ($("#location_count").val() > 0) {
      addLocation(++location_count);
      $("#location_count").val(location_count);
    } else {
      location_count = 1;
      addLocation(1);
      $("#location_count").val(1);
    }
    $(this).attr("disabled", true);
  });
  window.deleteLocation = function (id) {
    var selectedlocation = [];
    if ($("#company_location").val() != "") {
      selectedlocation = binarytoarray($("#company_location").val());
    }

    //console.log(selectedlocation);

    if (typeof selectedlocation[id] != "undefined") {
      selectedlocation = jQuery.grep(selectedlocation, function (value) {
        return value != selectedlocation[id];
      });

      $("#company_location").val(arraytobinary(selectedlocation));
    }

    //console.log(selectedlocation);

    var unusednumber = $("#location_removed").val();

    var locationnumber = id;

    var locationcount = parseInt($("#location_count").val(), 10);

    if (locationnumber == locationcount) {
      $("#location" + id).remove();

      location_count = $("#location_count").val();

      $("#location_count").val(--location_count);
    } else {
      $("#location" + id).remove();

      if (unusednumber == "") {
        $("#location_removed").val(locationnumber);
      } else {
        $("#location_removed").val(unusednumber + "," + locationnumber);
      }
    }
    $(".new-location-button").removeAttr("disabled");
  };
  window.getLanguagearray = function () {
    var lang = $("#question_languages").val();
    return (languages = JSON.parse(atob(lang)));
  };
  window.deleteOption = function (opt) {
    $("#options" + opt).remove();
    option_count = $("#option_count").val();
    $("#option_count").val(--option_count);
    deletelangoption(opt);
  };
  var text = $(".post-type-tf_reviewsystem #publish").val();
  $(".post-type-tf_reviewsystem #publish").hide();
  $(".post-type-tf_reviewsystem #publishing-action").append(
    '<button type="button" id="tf_reviewsystem_publish" class="button button-primary button-large" >' +
      text +
      "</button>"
  );
  window.addOption = function (value = "", langoption = "") {
    opt = $("#option_count").val();
    opt++;
    var html =
      '<div class="fields" id="options' +
      opt +
      '"><div class="fourteen wide field">' +
      '<input name="questions[option][' +
      opt +
      ']" type="text" placeholder="Option" value="' +
      value +
      '" required>' +
      '</div><div class="two wide field"><button class="ui red icon button option-delete" id="optiondelete" style="margin-top:0px;" Onclick="deleteOption(' +
      opt +
      ')"><i class="trash icon"></i></button></div>';
    addlangoption(opt, langoption);
    $("#options").append(html);
    $("#option_count").val(opt);
  };
  window.deletelangoption = function (opt) {
    var languages = getLanguagearray();
    $.each(languages, function (i, item) {
      $("#" + i + "options" + opt).remove();
    });
  };
  window.addlangoption = function (opt, langoptionval = "") {
    var languages = getLanguagearray();
    $.each(languages, function (i, item) {
      var html = null;
      if (
        langoptionval.hasOwnProperty(i) &&
        langoptionval[i] != "" &&
        langoptionval[i].hasOwnProperty("option") &&
        langoptionval[i].option[opt] != "" &&
        typeof langoptionval[i].option[opt] != "undefined"
      ) {
        html =
          '<div id="' +
          i +
          "options" +
          opt +
          '" style="margin-bottom:5px;">' +
          '<input name="questions[languages][' +
          i +
          "][option][" +
          opt +
          ']" type="text" placeholder="Option" value="' +
          langoptionval[i].option[opt] +
          '">' +
          "</div>";
      } else {
        html =
          '<div id="' +
          i +
          "options" +
          opt +
          '" style="margin-bottom:5px;">' +
          '<input name="questions[languages][' +
          i +
          "][option][" +
          opt +
          ']" type="text" placeholder="Option" value="">' +
          "</div>";
      }
      $("#" + i + "langcontainer").append(html);
    });
  };
  if ($("#headcounts").val()) {
    var location = jQuery.parseJSON(atob($("#location_val").val()));
    var headcounts = jQuery.parseJSON(atob($("#headcounts").val()));
    var locations = jQuery.parseJSON(atob($("#locations").val()));
    $.each(headcounts, function (i, item) {
      if (i == 0) {
        $("#headcount_zero").val(headcounts[i]);
      } else {
        addLocation(++location_count, location[i], item);
      }
      var selectedlocation = [];
      if ($("#company_location").val() != "") {
        selectedlocation = binarytoarray($("#company_location").val());
      }
      var selectedindex = location_count;
      selectedlocation[selectedindex] = location[i];
      $("#company_location").val(arraytobinary(selectedlocation));
      //console.log("Rinkal");
      //console.log(selectedlocation);
      $(".new-location-button").removeAttr("disabled");
    });
  }
  if ($("#question_json").val()) {
    var questions = jQuery.parseJSON(atob($("#question_json").val()));
    window.languages_values = null;
    if (
      questions.hasOwnProperty("question_image") &&
      questions.question_image != ""
    ) {
      wp.media
        .attachment(questions.question_image)
        .fetch()
        .then(function (data) {
          window.image_url = wp.media
            .attachment(questions.question_image)
            .get("url");
          $("#question_image_src").attr("src", window.image_url);
        });
    }
    if (questions.hasOwnProperty("languages") && questions.languages != "") {
      window.languages_values = questions.languages;
    }
    if (questions.hasOwnProperty("option")) {
      if (questions.option) {
        var option_count = Object.keys(questions.option).length;
        $.each(questions.option, function (k, option) {
          addOption(option, window.languages_values);
        });
      }
    }
  }
  // tf_reviewsystem Generator
  $(".fullscreen.modal").modal(
    "attach events",
    "#generatetf_reviewsystem",
    "show"
  );
  $(".fullscreen.modal").modal({
    closable: false,
    onDeny: function () {
      return true;
    },
    onApprove: function () {
      window.onbeforeunload = null;
      var step = $("#activestep").val();
      var form = $("form[name='post']");
      if (step == "") {
        $(".error_message").remove();
        var error = true;
        $(".error_message").remove();
        if ($("#tf_reviewsystem_title").val() == "") {
          $("#tf_reviewsystemtitlediv").append(
            window.errpr.replace("{error}", "Add Review")
          );
          error = false;
        }
        if ($("#tf_reviewsystemids").val() == "") {
          $("#tf_reviewsystems_question").append(
            window.errpr.replace("{error}", "Add Questions")
          );
          error = false;
        }
        if (error == false) {
          return false;
        }
        $("#progress-step-1").removeClass("active");
        $("#progress-step-2").addClass("active");
        $("#startstep1").hide();
        $("#tf_reviewsystemsubmit").html("Next");
        $("#startstep2").show();
        $("#activestep").val(2);
      } else if (step == 2) {
        var error = false;
        $(".error_message").remove();
        // if($("#tf_reviewsystemcompany").val() == ""){
        // 	// $('.error_message').remove();
        // 	$('#companyfield').append(window.errpr.replace("{error}", "Select Company"));
        // 	error = true;
        // }

        if ($("#yearlist").val() == "") {
          // $('.error_message').remove();
          $("#yearfield").append(
            window.errpr.replace("{error}", "Please Select Year")
          );
          error = true;
        }

        if ($("#startdate").val() == "") {
          // $('.error_message').remove();
          $("#rangestart").append(
            window.errpr.replace("{error}", "Please Select Start Date")
          );
          error = true;
        }

        if ($("#enddate").val() == "") {
          // $('.error_message').remove();
          $("#rangeend").append(
            window.errpr.replace("{error}", "Please Select End Date")
          );
          error = true;
        }

        if ($("#review_for").val() == "") {
          // $('.error_message').remove();
          $("#reviewforfield").append(
            window.errpr.replace("{error}", "Select Employee")
          );
          error = true;
        }
        if ($("#emppeerlist").val() == "") {
          // $('.error_message').remove();
          $("#peerlistfield").append(
            window.errpr.replace("{error}", "Select Employees")
          );
          error = true;
        }

        // if($("#tf_reviewsystemlocation").val() == ""){
        // 	// $('.error_message').remove();
        // 	$('#locationfield').append(window.errpr.replace("{error}", "Select Location"));
        // 	error = true;
        // }
        /*if($("#tf_reviewsystemlanguages").val() == ""){
					$('#Languagefield').append(window.errpr.replace("{error}", "Select Language"));
					error = true;
				}*/
        // if (!$("input[name='avaliablity']").is(":checked")) {
        //   // $('.error_message').remove();
        //   $("#tf_reviewsystemavaliablity").append(
        //     window.errpr.replace(
        //       "{error}",
        //       "Select tf_reviewsystem Avaliablity"
        //     )
        //   );
        //   error = true;
        // }
        // if ($('input[name="avaliablity"]:checked').val() == "specific") {
        //   if ($("#startdate").val() == "") {
        //     $("#startdates").append(
        //       window.errpr.replace("{error}", "Select Start Date")
        //     );
        //     error = true;
        //   }
        //   if ($("#enddate").val() == "") {
        //     $("#enddates").append(
        //       window.errpr.replace("{error}", "Select End Date")
        //     );
        //     error = true;
        //   }
        // }
        if (error == true) {
          return false;
        }
        $("#tf_reviewsystemsubmit").html("Submit");
        $("#progress-step-2").removeClass("active");
        $("#progress-step-3").addClass("active");
        $("#startstep2").hide();
        $("#startstep3").show();
        $("#activestep").val(3);
      } else {
        if ($("#url").val() == "") {
          // $('.error_message').remove();
          $("#urlfield").append(window.errpr.replace("{error}", "Enter URL"));
          error = true;
        }
        if (error == true) {
          return false;
        }
        //window.alert($("#review_for").dropdown("get value"));
        $.ajax({
          type: "post",
          dataType: "json",
          url: myAjax.ajaxurl,
          data: {
            action: "submittf_reviewsystem",
            question_ids: $("#tf_reviewsystemids").val(),
            post_id: $("#post_ID").val(),
            post_title: $("#title").val(),
            post_type: $("#post_type").val(),
            Welcometext: $('input[name="Welcometext"]').val(),
            thankyoutext: $('input[name="thankyoutext"]').val(),
            font_color: $("#font_color").val(),
            button_font_color: $("#button_font_color").val(),
            button_bg_color: $("#button_bg_color").val(),
            bg_image_id: $("#bg-image-id").val(),
            header_image_id: $("#header-image-id").val(),
            footer_image_id: $("#footer-image-id").val(),
            fav_image_id: $("#fav-image-id").val(),
            footer2_image_id: $("#footer2-image-id").val(),
            tf_reviewsystemdepartment: $("#tf_reviewsystemdepartment").val(),
            avaliablity: $('input[name="avaliablity"]:checked').val(),
            startdate: $('input[name="startdate"]').val(),
            enddate: $('input[name="enddate"]').val(),
            //tf_reviewsystemcompany:$("#tf_reviewsystemcompany").dropdown("get value"),
            //tf_reviewsystemlanguage:$("#tf_reviewsystemlanguages").val(),
            //tf_reviewsystemlocation:$("#clocation").val(),
            tf_reviewsystemcompany: $("#yearlist").dropdown("get value"),
            tf_reviewsystemreview_for: $("#review_for").val(),
            tf_reviewsystemlanguage: $("#tf_reviewsystemlanguages").val(),
            tf_reviewsystemlocation: $("#emppeerlist").val(),
            url: $('input[name="url"]').val(),
            formdata: $("#post").serialize(),
          },
          success: function (response) {
            if (response.success == true) {
              window.location.href = response.data.redirect;
            }
            if (response == 0) {
              window.alert(
                "review with this company and location already available 1"
              );
            }
          },
        });
      }
      return false;
    },
  });
  $("#tf_reviewsystem_publish").click(function () {
    //alert("test"+$("#review_for").val());

    if (stpevalidation()) {
      $.ajax({
        type: "post",
        dataType: "json",
        url: myAjax.ajaxurl,
        data: {
          action: "submittf_reviewsystem",
          question_ids: $("#tf_reviewsystemids").val(),
          post_id: $("#post_ID").val(),
          post_title: $("#title").val(),
          post_type: $("#post_type").val(),
          Welcometext: $('input[name="Welcometext"]').val(),
          thankyoutext: $('input[name="thankyoutext"]').val(),
          font_color: $("#font_color").val(),
          button_font_color: $("#button_font_color").val(),
          button_bg_color: $("#button_bg_color").val(),
          bg_image_id: $("#bg-image-id").val(),
          header_image_id: $("#header-image-id").val(),
          footer_image_id: $("#footer-image-id").val(),
          footer2_image_id: $("#footer2-image-id").val(),
          fav_image_id: $("#fav-image-id").val(),
          tf_reviewsystemdepartment: $("#tf_reviewsystemdepartment").val(),
          avaliablity: $('input[name="avaliablity"]:checked').val(),
          startdate: $('input[name="startdate"]').val(),
          enddate: $('input[name="enddate"]').val(),
          //tf_reviewsystemcompany:$("#tf_reviewsystemcompany").dropdown("get value"),
          //tf_reviewsystemlanguage:$("#tf_reviewsystemlanguages").val(),
          //tf_reviewsystemlocation:$("#tf_reviewsystemlocation").dropdown("get value"),

          tf_reviewsystemcompany: $("#yearlist").dropdown("get value"),
          tf_reviewsystemreview_for: $("#review_for").val(),
          tf_reviewsystemlanguage: $("#tf_reviewsystemlanguages").val(),
          tf_reviewsystemlocation: $("#emppeerlist").val(),

          url: $('input[name="url"]').val(),
          formdata: $("#post").serialize(),
        },
        success: function (response) {
          if (response.success == true) {
            window.location.href = response.data.redirect;
          }
          if (response == 0) {
            window.alert(
              "review with this company and location already available  2"
            );
          }
        },
      });
    }
  });
  $(".sendEmail").click(function () {
    //alert("clicked");
    console.log($(".sendEmail").attr("data-review_for_id"));
    console.log($(".sendEmail").attr("data-peer_review_id"));
    console.log($(this).attr("post_id"));
    $.ajax({
      type: "post",
      dataType: "json",
      url: myAjax.ajaxurl,
      data: {
        action: "sendEmailtousers",
        review_for_id: $(".sendEmail").attr("data-review_for_id"),
        peer_review_id: $(".sendEmail").attr("data-peer_review_id"),
        post_id: $(this).attr("post_id"),
        //url: $('input[name="url"]').val(),
        formdata: $("#post").serialize(),
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
          //alert(response);
          $(this).prop("disabled", true);
          $(this).text("Sent");
          $(this).css("background-color", "#7692d3");
        },
      },
    });
  });
  $("#department").change(function () {
    var dept_id = $(this).val();
    alert(dept_id);
  });

  window.stpevalidation = function (argument) {
    var error = false;
    $(".error_message").remove();
    if ($("#tf_reviewsystemids").val() == "") {
      $(".error_message").remove();
      $("#tf_reviewsystems_question").append(
        window.errpr.replace("{error}", "Select Questions")
      );
      error = true;
    }
    $(".error_message").remove();
    // if($("#tf_reviewsystemcompany").val() == ""){
    // 	$('#companyfield').append(window.errpr.replace("{error}", "Select Company"));
    // 	error = true;
    // }

    if ($("#yearlist").val() == "") {
      $("#yearfield").append(window.errpr.replace("{error}", "Select Year"));
      error = true;
    }

    if ($("#review_for").val() == "") {
      // $('.error_message').remove();
      $("#reviewforfield").append(
        window.errpr.replace("{error}", "Select Employee")
      );
      error = true;
    }
    if ($("#emppeerlist").val() == "") {
      // $('.error_message').remove();
      $("#peerlistfield").append(
        window.errpr.replace("{error}", "Select Employees")
      );
      error = true;
    }

    // if($("#tf_reviewsystemlocation").val() == ""){
    // 	$('#locationfield').append(window.errpr.replace("{error}", "Select Location"));
    // 	error = true;
    // }

    if ($("#tf_reviewsystemlanguages").val() == "") {
      $("#Languagefield").append(
        window.errpr.replace("{error}", "Select Language")
      );
      error = true;
    }
    if (!$("input[name='avaliablity']").is(":checked")) {
      $("#tf_reviewsystemavaliablity").append(
        window.errpr.replace("{error}", "Select tf_reviewsystem Avaliablity")
      );
      error = true;
    }
    if ($('input[name="avaliablity"]:checked').val() == "specific") {
      if ($("#startdate").val() == "") {
        $("#startdates").append(window.errpr.replace("{error}", "Enter URL"));
        error = true;
      }
      if ($("#enddate").val() == "") {
        $("#enddates").append(window.errpr.replace("{error}", "Enter URL"));
        error = true;
      }
    }
    if ($('input[name="avaliablity"]:checked').val() == "always") {
      $("#startdate").val("");
      $("#enddate").val("");
    }
    if ($("#url").val() == "") {
      $("#urlfield").append(window.errpr.replace("{error}", "Enter URL"));
      error = true;
    }
    if (error == true) {
      $(".fullscreen.modal").modal("show");
      return false;
    } else {
      return true;
    }
  };
  $("#progress-step-1").click(function () {
    if (stpevalidation()) {
      $(".step.active").removeClass("active");
      $("#progress-step-1").addClass("active");
      $("#startstep1").show();
      $("#tf_reviewsystemsubmit").html("Next");
      $("#startstep2").hide();
      $("#startstep3").hide();
      $("#activestep").val("");
    }
  });
  $("#progress-step-2").click(function () {
    if (stpevalidation()) {
      $(".step.active").removeClass("active");
      $("#progress-step-2").addClass("active");
      $("#startstep2").show();
      $("#tf_reviewsystemsubmit").html("Next");
      $("#startstep1").hide();
      $("#startstep3").hide();
      $("#activestep").val(2);
    }
  });
  $("#progress-step-3").click(function () {
    if (stpevalidation()) {
      $(".step.active").removeClass("active");
      $("#progress-step-3").addClass("active");
      $("#startstep3").show();
      $("#startstep2").hide();
      $("#tf_reviewsystemsubmit").html("Submit");
      $("#startstep1").hide();
      $("#activestep").val(3);
    }
  });
  $("#rangestart").calendar({
    type: "date",
    endCalendar: $("#rangeend"),
  });
  $("#rangeend").calendar({
    type: "date",
    startCalendar: $("#rangestart"),
  });
  $(".checkbox.avaliablity").checkbox({
    onChange: function () {
      if ($(this).val() == "always") {
        $("#datedependsonava").hide();
      } else {
        $("#datedependsonava").show();
      }
    },
  });
  window.validate();

  var buttonClicked = false;
  $(".sendEmail").on("click", function () {
    if (!buttonClicked) {
      $(this).prop("disabled", true);
      $(this).text("Sent");
      $(this).css("background-color", "#7692d3");
      buttonClicked = true;
    }
  });

  // $(".ui.dropdown").dropdown({
  //   allowAdditions: true,
  //   forceSelection: false,
  //   maxSelections: 5,
  // });
  // // Add a change event listener to check the number of selected items
  // $(".ui.dropdown").on("change", function () {
  //   var selectedCount = $(this).find(".label").length;
  //   // Check if the selected count exceeds the maximum allowed
  //   if (selectedCount > 5) {
  //     alert("You can select a maximum of 5 items.");
  //     // You can also remove the last selected item or take other actions
  //   }
  // });
  
});
