<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly      
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title><?php the_title() ?></title>
    <link href="<?php echo plugin_dir_url(__FILE__) . 'assets/css/style.css'; ?>" rel="stylesheet">
    <link href="<?php echo plugin_dir_url(__FILE__) . 'assets/css/bootstrap.min.css'; ?>" rel="stylesheet">
    <link href="<?php echo plugin_dir_url(__FILE__) . 'assets/css/frontform.css'; ?>" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

    <?php if (!empty(get_tf_reviewsystem_fav_logo())) { ?>
        <link rel="icon" href="<?php get_tf_reviewsystem_fav_logo() ?>" type="image/gif" sizes="16x16">
    <?php } ?>
    <style>
        html,
        body {
            width: 100%;
            height: 100%;
            overflow-x: hidden;
            margin: 0px;
            padding: 0px;
        }

        /* Mark input boxes that gets an error on validation: */
        input.invalid {
            background-color: #ffdddd;
        }

        /* Hide all steps by default: */
        .tab {
            display: none;
        }

        button {
            background-color: #04AA6D;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 17px;
            font-family: Raleway;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.8;
        }

        #prevBtn {
            background-color: #bbbbbb;
        }

        /* Make circles that indicate the steps of the form: */
        .step {
            height: 15px;
            width: 15px;
            margin: 0 2px;
            background-color: #bbbbbb;
            border: none;
            border-radius: 50%;
            display: inline-block;
            opacity: 0.5;
        }

        .step.active {
            opacity: 1;
        }

        /* Mark the steps that are finished and valid: */
        .step.finish {
            background-color: #04AA6D;
        }
    </style>

</head>

<?php
$current_date = date('Y-m-d');
$expire_date = $_GET['d'];

$final_expire_date = base64_decode($expire_date);

$date1 = strtotime($current_date);
$date2 = strtotime($final_expire_date);

$datediff = $date2 - $date1;
if ($datediff < 0) {

    global $post;
    $survey_id = $post->ID;
    $emp_id = base64_decode(urldecode($_GET['e']));
?>
    <div class="link-expired-container">
        <h1>Link Expired</h1>
        <p>The link you have followed has expired.</p>
        <form action="" method="post" class="form-horizontal">
            <label for="reson for expire">Add Reason for Late Submission</label>
            <!-- <input type="text" name="reason" id="reason" required /> -->
            <textarea name="reason" id="reason" required></textarea>
            <input type="hidden" name="ajaxurl" value="<?php admin_url('admin-ajax.php') ?>">
            <input type="hidden" name="post_id" value="<?php $survey_id ?>">
            <input type="hidden" name="emp_id" value="<?php $emp_id ?>">
            <input type="hidden" name="emp_name" value="<?php $emp_name ?>">
            <input type="hidden" name="hr_name" value="<?php echo get_option('hr_name'); ?>">
            <input type="hidden" name="hr_email" value="<?php echo get_option('hr_email'); ?>">
            <input type="submit" name="submit" id="submit" value="Submit">
        </form>
    </div>
    <style>

    </style>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#submit").on("click", function() {
                //alert("Thank You");
                var myAjax = $('input[name="ajaxurl"]').val();
                // alert(myAjax); return false;
                $.ajax({
                    type: "post",
                    // dataType : "json",
                    url: myAjax,
                    data: {
                        action: "expired_review",
                        reason: $('#reason').val(),
                        post_id: $('input[name="post_id"]').val(),
                        emp_id: $('input[name="emp_id"]').val(),
                        emp_name: $('input[name="emp_name"]').val(),
                        hr_name: $('input[name="hr_name"]').val(),
                        hr_email: $('input[name="hr_email"]').val()
                    },
                    success: function(response) {
                        alert("Thank You");
                        // window.location.replace("http://localhost/Wpreview/wp-admin/edit.php?post_type=survey");
                    }
                });
            });
        })
    </script>
<?php

} else {



?>

    <body class="bd_main bd_other">
        <div class="bd">
            <div class="header_logo" align="center"><img src="<?php get_tf_reviewsystem_header_logo() ?>"></div>
            <div class="wel">
                <h1><?php the_title() ?></h1>
            </div>
            <div class="clearfix"></div>
            <div class="thumbnail_1">
                <div class="imim">
                    <input type="hidden" id="current_page" value="0">
                    <img class="img1" id="fix_image">
                </div>
            </div>
            <div id="tf_reviewsystemcontainer" class="maintf_reviewsystemcontent reviewformcontent">
                <form id="msform">
                    <?php
                    $expiredate_e = $_GET['d'];
                    $expiredate = base64_decode($expiredate_e);
                    $emp_id = '';

                    $yearlist = get_posts([
                        'post_type' => 'tf-yearlist',
                        'post_status' => 'publish',
                        'numberposts' => -1,
                        'orderby'   => 'title',
                        'order'      => 'ASC'
                    ]);

                    $table_name = $wpdb->prefix . 'feedback_para';

                    $emp_id = '';
                    if (!empty($_GET['t']) && !empty($_GET['e'])) {
                        $token = base64_decode(urldecode($_GET['t']));
                        $emp_id = base64_decode(urldecode($_GET['e']));

                        $sel_para = "select * from $table_name WHERE `token` LIKE '" . $token . "'";
                    } else {
                        $sel_para = "select * from $table_name";
                    }

                    $result_para = $wpdb->get_row($wpdb->prepare($sel_para));
                    // print_r($result_para);
                    $questionlist = $result_para->questionlist;
                    $tf_reviewsystem_responces_table_name = $wpdb->prefix . 'tf_reviewsystem_responces';
                    //print_r($emp_id);
                    if (property_exists($result_para, 'location_id')) {
                        $location_id = $result_para->location_id;
                    } else {
                        // Handle the case where 'location_id' property is undefined
                        $location_id = null; // or some default value
                    }
                    $tf_reviewsystem_responces_query = "select * from $tf_reviewsystem_responces_table_name WHERE `review_by` LIKE '" . $emp_id . "' AND is_submitted=1";
                    $tf_reviewsystem_responces_res = $wpdb->get_results($tf_reviewsystem_responces_query);
                    //print_r($tf_reviewsystem_responces_res->is_submitted);
                    //echo "result = " . $tf_reviewsystem_responces_res;
                    // print_r($tf_reviewsystem_responces_res);
                    if ($tf_reviewsystem_responces_res == 1) {
                        echo "<h2>You've Already Responded!</h2>";
                        echo "<h4>You can only respond once...</h4>";
                        exit;
                    }
                    ?>
                    <!-- fieldsets -->
                    <div class="tab">
                        <div class="form-card">
                            <div class="row form-ul">
                                <ul>
                                    <li>
                                        <p>Year</p>
                                        <?php echo year_name($result_para->post_id, $result_para->year_id); ?>
                                        </span>
                                    </li>
                                    <li>
                                        <p>Review For</p>
                                        <span>
                                            <?php echo employee_name($result_para->post_id, $result_para->review_for); ?>
                                        </span>
                                    </li>
                                    <li>
                                        <p>Review By</p>
                                        <span>
                                            <?php
                                            $user_reviewbyname = get_user_by('id', $emp_id);
                                            echo  $user_reviewbyname->display_name; ?>
                                        </span>
                                    </li>
                                    <li>
                                        <p>Department</p>
                                        <span>
                                            <?php echo department_name($result_para->post_id, $result_para->department_id); ?>
                                        </span>
                                    </li>
                                    <li>
                                        <p>Review End Date</p>
                                        <span>
                                            <?php echo $expiredate; ?>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <div class="row form-scoreinfo">
                                <h4>Score</h4>
                                <ul>
                                    <li>
                                        <p>0</p><span>: No Idea</span>
                                    </li>
                                    <li>
                                        <p>1-20</p><span>: Poor</span>
                                    </li>
                                    <li>
                                        <p>21-40</p><span>: Below Average</span>
                                    </li>
                                    <li>
                                        <p>41-60</p><span>: Average</span>
                                    </li>
                                    <li>
                                        <p>61-80</p><span>: Good</span>
                                    </li>
                                    <li>
                                        <p>81-100</p><span>: Excellent</span>
                                    </li>
                                </ul>
                            </div>
                            <?php
                            $html = '';
                            $question_data = get_post_meta($result_para->post_id, 'question_ids', true);
                            $questions_data = explode(',', $question_data);
                            $total = count($questions_data);
                            $flag = 0;
                            $q = 0;
                            foreach ($questions_data as $question_no => $question_id) {
                                $optionshtml = '';
                                $question = get_the_title($question_id);
                                $questions_data = get_post_meta($question_id, 'questions', true);
                                $department_id = get_post_meta($question_id, 'department', true);
                                $img_id = get_post_meta($question_id, 'question_image', true);
                                $image_info = wp_get_attachment_image_src($img_id, 'full');
                                if ($image_info && is_array($image_info)) {
                                    $src = $image_info[0];
                                }
                                $currentimgid = $question_id;
                                if (!isset($src) && empty($src)) {
                                    $img_id = get_post_meta($department_id, 'category_image', true);
                                    $image_info = wp_get_attachment_image_src($img_id, 'full');
                                    if ($image_info && is_array($image_info)) {
                                        $src = $image_info[0];
                                    }
                                    $currentimgid = $department_id;
                                }
                                $options = isset($questions_data['option']) && !empty($questions_data['option']) ? $questions_data['option'] : "";
                                if ($questions_data['type'] == "responce") {
                                    if (is_array($options)) {
                                        foreach ($options as $key => $option) {
                                            $optionshtml .= CustomOption($option, $question_id, $question_id, $key);
                                        }
                                    }
                                } elseif ($questions_data['type'] == "checkbox") {
                                    $optionshtml .= AgreeDisagree($question_id);
                                } elseif ($questions_data['type'] == "yes/no") {
                                    $optionshtml .= YesNo($question_id);
                                } elseif ($questions_data['type'] == "rating") {
                                    $optionshtml .= Rating($question_id);
                                } elseif ($questions_data['type'] == "openended") {
                                    $optionshtml .= OpenEnded($question_id);
                                } elseif ($questions_data['type'] == "rcheckbox") {
                                    $optionshtml .= ReverseAgreeDisagree($question_id);
                                }
                                if ($questions_data['type'] == "openended") {
                                    $flag++;
                                    $html .= '<div class="form-group forminput-box">';
                                    $qk = (int) $question_no + 1;
                                    $q++;
                                    $html .= '<h2 class="title color">Q' . $q . '. <span class="translate" data-id="' . $question_id . '" data-key="question">' . $question . '</span></h2>';
                                    $html .= $optionshtml;
                                    $html .= '</div>';
                                }
                            }
                            echo $html;
                            if ($flag != 0) {  ?>
                        </div>
                    </div>
                    <div class="tab" id="stage2">
                        <div class="form-card">
                        <?php  } ?>
                        <?php $html = '';
                        $question_data = get_post_meta($result_para->post_id, 'question_ids', true);
                        $questions_data = explode(',', $question_data);
                        $total = count($questions_data);
                        foreach ($questions_data as $question_no => $question_id) {
                            $optionshtml = '';
                            $question = get_the_title($question_id);
                            $questions_data = get_post_meta($question_id, 'questions', true);
                            $department_id = get_post_meta($question_id, 'department', true);
                            $img_id = get_post_meta($question_id, 'question_image', true);
                            $image_info = wp_get_attachment_image_src($img_id, 'full');
                            if ($image_info && is_array($image_info)) {
                                $src = $image_info[0];
                            }

                            $currentimgid = $question_id;

                            if (!isset($src) && empty($src)) {
                                $img_id = get_post_meta($department_id, 'category_image', true);
                                $image_info = wp_get_attachment_image_src($img_id, 'full');
                                if ($image_info && is_array($image_info)) {
                                    $src = $image_info[0];
                                }
                                $currentimgid = $department_id;
                            }

                            $options = isset($questions_data['option']) && !empty($questions_data['option']) ? $questions_data['option'] : "";
                            if ($questions_data['type'] == "responce") {
                                if (is_array($options)) {
                                    foreach ($options as $key => $option) {
                                        $optionshtml .= CustomOption($option, $question_id, $question_id, $key);
                                    }
                                }
                            } elseif ($questions_data['type'] == "checkbox") {
                                $optionshtml .= AgreeDisagree($question_id);
                            } elseif ($questions_data['type'] == "yes/no") {
                                $optionshtml .= YesNo($question_id);
                            } elseif ($questions_data['type'] == "rating") {
                                $optionshtml .= Rating($question_id);
                            } elseif ($questions_data['type'] == "openended") {
                                $optionshtml .= OpenEnded($question_id);
                            } elseif ($questions_data['type'] == "rcheckbox") {
                                $optionshtml .= ReverseAgreeDisagree($question_id);
                            }
                            $html .= '<div class="form-group forminput-box">';
                            if ($questions_data['type'] != "openended") {
                                $qk = (int) $question_no + 1;
                                $q++;
                                $html .= '<h2 class="title color">Q' . $q . '. <span class="translate" data-id="' . $question_id . '" data-key="question">' . $question . '</span></h2><div class="clearfix"></div>';
                                $html .= $optionshtml;
                            }
                            $html .= '</div>';
                        }
                        $html .= '<div class="control-group forminput-box formcomment-box">
                        <label class="forminput-boxradio">
                        Comments</label>
                        <div><textarea id="summary" name="summary" cols="5" rows="5"></textarea></div>
                        </div>';
                        echo $html;
                        ?>
                        </div>
                    </div>
                    <div>
                        <div>
                            <button type="button" id="prevBtn" onclick="nextPrev(-1)" class="btn_color_bg btn_color">Previous</button>
                            <button type="button" id="nextBtn" onclick="nextPrev(1)" class="btn_color_bg btn_color">Next</button>
                        </div>
                    </div>
                    <!-- Circles which indicates the steps of the form: -->
                    <div style="text-align:center;margin-top:40px;display: none;">
                        <span class="step"></span>
                        <span class="step"></span>
                    </div>
                </form>

                <input type="hidden" name="ajaxurl" value="<?php admin_url('admin-ajax.php') ?>">
                <input type="hidden" name="tf_reviewsystem_id" value="<?php get_the_ID() ?>">
                <input type="hidden" name="review_by_id" value="<?php echo $user_reviewbyname->ID; ?>" />
                <input type="hidden" name="review_for" value="<?php echo $result_para->review_for ?>" />
            </div>
            <div class="thankyoutf_reviewsystem" style="display: none;">
                <div class="imim wel">
                    <p class="color translate" data-id="thankyoutext"><?php get_tf_reviewsystem_thankyou_text() ?></p>
                    <a href='#' style='float: center;'><span class='gtstt glyphicon glyphicon-volume-up'> </span></a>
                    <audio id='thankAudio'>
                        <source id='thankAudioData' type='audio/mpeg' />
                    </audio>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="logos">
                <?php
                if (!empty(get_tf_reviewsystem_footer_logo())) { ?>
                    <img src="<?php get_tf_reviewsystem_footer_logo() ?>" class="lg_1" id="mon">
                <?php } ?>
                <?php
                if (!empty(get_tf_reviewsystem_footer2_logo())) { ?>
                    <img src="<?php get_tf_reviewsystem_footer2_logo() ?>" class="lg_2" id="mon">
                <?php }
                $img_id = get_post_meta(get_the_ID(), 'bg_image_id', true);
                $dynamic_theme_option_name = get_option('dynamic_theme_option_name');

                if (empty($img_id)) {
                    $bg_image = $dynamic_theme_option_name['bg_image_0'] ?? null;
                    if (is_array($bg_image)) {
                        $img_id = $bg_image[0] ?? null;
                    }
                }
                $image_info = wp_get_attachment_image_src($img_id, 'full');
                if (is_array($image_info) && isset($image_info[0])) {
                    $img_src = $image_info[0];
                }else{
                    $img_src = '';
                }
                if ($img_src == '' || $img_src == null) {
                    $img_src = get_template_directory_uri() . '/images/back.jpg';
                }
                $button_bg_color = get_post_meta(get_the_ID(), 'button_bg_color', true);
                if (empty($button_bg_color)) {
                    $bg_color_value = $dynamic_theme_option_name['button_bg_color_0'] ?? null;
                    if (is_array($bg_color_value)) {
                        $button_bg_color = $bg_color_value[0] ?? null;
                    }
                }
                if ($button_bg_color == '' || $button_bg_color == null) {
                    $button_bg_color = '#365377';
                }
                $button_font_color = get_post_meta(get_the_ID(), 'button_font_color', true);
                if (empty($button_font_color)) {
                    $button_font_color_info = $dynamic_theme_option_name['button_font_color_0'] ?? null;
                    if (is_array($button_font_color_info)) {
                        $button_font_color = $button_font_color_info[0] ?? null;
                    }
                }
                if ($button_font_color == '' || $button_font_color == null) {
                    $button_font_color = '#fff';
                }
                $font_color = get_post_meta(get_the_ID(), 'font_color', true);
                if (empty($font_color)) {
                    $font_color_info = $dynamic_theme_option_name['font_color_0'] ?? null;
                    if (is_array($font_color_info)) {
                        $font_color = $font_color_info[0] ?? null;
                    }
                }
                if ($font_color == '' || $font_color == null) {
                    $font_color = '#000';
                }
                /*$button_font_color = $dynamic_theme_option_name['button_font_color_0'];
     $font_color = $dynamic_theme_option_name['font_color_0'];*/
                ?>

            </div>

            <!-- jQuery -->
            <script src="<?php plugin_dir_url(__FILE__) . 'assets/js/jquery.min.js' ?>"></script>
            <!-- jQuery easing plugin -->
            <script src="<?php plugin_dir_url(__FILE__) . 'assets/js/jquery.easing.min.js' ?>" type="text/javascript"></script>

            <script type="text/javascript">
                $(document).ready(function() {
                    var img_src = '<?php echo $img_src; ?>';
                    var button_bg_color = '<?php echo $button_bg_color; ?>';
                    var button_font_color = '<?php echo $button_font_color; ?>';
                    var font_color = '<?php echo $font_color; ?>';
                    $(".bd_main").css("background-image", "url(" + img_src + ")");
                    $(".btn_color_bg").css("background-color", button_bg_color);
                    $(".btn_color").css("color", button_font_color);
                    $(".color").css("color", font_color);
                    $(".wel h1").css("color", font_color);
                });
            </script>

            <script>
                var currentTab = 0; // Current tab is set to be the first tab (0)
                showTab(currentTab); // Display the current tab

                function showTab(n) {
                    // This function will display the specified tab of the form...
                    var x = document.getElementsByClassName("tab");
                    x[n].style.display = "flex";
                    //... and fix the Previous/Next buttons:
                    if (n == 0) {
                        document.getElementById("prevBtn").style.display = "none";
                    } else {
                        document.getElementById("prevBtn").style.display = "inline";
                    }
                    if (n == (x.length - 1)) {
                        document.getElementById("nextBtn").innerHTML = "Submit";
                        document.getElementById("nextBtn").className = "form_submitBtn";


                    } else {
                        document.getElementById("nextBtn").innerHTML = "Next";
                    }
                    //... and run a function that will display the correct step indicator:
                    fixStepIndicator(n)
                }

                function nextPrev(n) {
                    // This function will figure out which tab to display
                    var x = document.getElementsByClassName("tab");
                    if (currentTab <= 1) {
                        //alert(x.length);
                        // Exit the function if any field in the current tab is invalid:
                        if (n == 1 && !validateForm()) return false;
                        // Hide the current tab:
                        x[currentTab].style.display = "none";
                        // Increase or decrease the current tab by 1:
                        currentTab = currentTab + n;
                    }
                    // if you have reached the end of the form...
                    if (currentTab >= x.length) {
                        // ... the form gets submitted:

                        submitForm();
                        //document.getElementById("msform").submit();
                        //return false;
                    }
                    // Otherwise, display the correct tab:
                    showTab(currentTab);
                }

                function validateForm() {
                    // This function deals with validation of the form fields

                    var x, y, i, valid = true;
                    x = document.getElementsByClassName("tab");

                    y = x[currentTab].getElementsByTagName("input");
                    //alert(x[currentTab].getElementsByTagName("input").type);
                    // A loop that checks every input field in the current tab:
                    for (i = 0; i < y.length; i++) {
                        // If a field is empty...

                        if (y[i].type == 'text') {
                            if (y[i].value == "") {
                                // add an "invalid" class to the field:
                                y[i].className += " invalid";
                                // and set the current valid status to false
                                valid = false;
                            }
                        }

                    }
                    // If the valid status is true, mark the step as finished and valid:
                    if (valid) {
                        document.getElementsByClassName("step")[currentTab].className += " finish";
                    }
                    return valid; // return the valid status
                }

                function fixStepIndicator(n) {
                    // This function removes the "active" class of all steps...
                    var i, x = document.getElementsByClassName("step");
                    for (i = 0; i < x.length; i++) {
                        x[i].className = x[i].className.replace(" active", "");
                    }
                    //... and adds the "active" class on the current step:
                    x[n].className += " active";
                }

                function submitForm() {

                    var flag1 = 0;
                    // var flag2 = 0;

                    // Validate radio buttons
                    var radioInputs = document.querySelectorAll("#msform input[type='radio']");
                    radioInputs.forEach(function(radio) {
                        if (!document.querySelector("input[name='" + radio.name + "']:checked")) {
                            flag1++;
                        }
                    });

                    // Validate text inputs
                    // var textInputs = document.querySelectorAll("#msform input[type='text']");
                    // textInputs.forEach(function(textInput) {
                    //     if (textInput.value.trim() === '') {
                    //         flag2++;
                    //     }
                    // });

                    if (flag1 > 0) {
                        alert('Please Select One response');
                        document.querySelector('#stage2').style.display = "block";
                        //return false;
                    } else {

                        var myAjax = $('input[name="ajaxurl"]').val();
                        //alert(myAjax); return false;
                        $.ajax({
                            type: "post",
                            // dataType : "json",
                            url: myAjax,
                            data: {
                                action: "tf_reviewsystemresponsestore",
                                form_data: $('#msform').serialize(),
                                tf_reviewsystem_id: $('input[name="tf_reviewsystem_id"]').val(),
                                review_for: $('input[name="review_for"]').val(),
                                review_by_id: $('input[name="review_by_id"]').val(),
                                summary: $('#summary').val()
                            },
                            success: function(response) {
                                if (response.success == true) {
                                    document.cookie = "enable=" + $('input[name="tf_reviewsystem_id"]')
                                        .val();
                                    $('#fix_image').hide();
                                    $('.maintf_reviewsystemcontent').hide();
                                    $('.thankyoutf_reviewsystem').show();
                                }
                            }
                        });
                    }
                }
            </script>
        </div>
    </body>
<?php } ?>

</html>