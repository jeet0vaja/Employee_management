jQuery(document).ready(function($) {
    var current = 1, current_step, next_step, steps;
 $(".next").click(function() {
    current_step = $(this).parent();
    next_step = $(this).parent().next();
    next_step.show();
    current_step.hide();
});   
$(".previous").click(function() {
    current_step = $(this).parent();
    next_step = $(this).parent().prev();
    next_step.show();
    current_step.hide();

});
$("#submit_data").click(function() {
   

        var flag1 = 0;

		// Validate radio buttons
		 
		var radio_length = $("#msform input[type=radio]").length;
		var total_radio = radio_length / 2;
		$("#msform input[type=radio]:checked").each(function() {
			if (this.checked == true) {
				flag1++;

			}
		});
         
        if ((total_radio == flag1) && (flag1 != 0)) {
        
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'ers_tf_reviewsystemresponsestore',
                    security: ajax_object.nonce,
                    form_data: $('#msform').serialize(),
					tf_reviewsystem_id: $('input[name="tf_reviewsystem_id"]').val(),
					review_for: $('input[name="review_for"]').val(),
					review_by_id: $('input[name="review_by_id"]').val(),
					summary: $('#summary').val()
                },
                success: function(response) {
                    console.log("%c Line:46 🌮 response", "color:#ffdd4d", response);
                    
                    if (response.success == true) {
                        $('#msform').remove();
                        $('.thankyoutf_reviewsystem').show();
                        
                    }else {
                        alert('An error occurred: ' + response.data);
                    }
                }
            });

        }else {
			alert('Please Select One response');
			return false;
		}
});


$("#submit_reason").on("click", function () {
    var post_id = $('#post_id').val();
    var emp_id = $('#emp_id').val();
  
     

    $.ajax({
      url: ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "expired_review",
        post_id: post_id,
        emp_id: emp_id,
        security: ajax_object.nonce,
      },
      success: function (response) {
        console.log("%c Line:78 🥤 response", "color:#fca650", response);

        if (response.success) {
            
            $('.link-expired-wrapper').remove();
            $('.thankyoutf_reviewsystem').show();
           
        } else {
          alert("Failed to fetch data");
        }
      },
    });
  });


});
