<?php
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
global $post;
$tf_reviewsystem_id = $post->ID;
$tf_reviewsystem = get_post_meta($tf_reviewsystem_id);

$header_src = wp_get_attachment_image_src($tf_reviewsystem['header_image_id'][0], 'full')[0];
$footer_src = wp_get_attachment_image_src($tf_reviewsystem['footer_image_id'][0], 'full')[0];
$footer2_src = wp_get_attachment_image_src($tf_reviewsystem['footer2_image_id'][0], 'full')[0];
$fav_src = wp_get_attachment_image_src($tf_reviewsystem['fav_image_id'][0], 'full')[0];

$question_data = get_post_meta($postId, 'question_ids', true);
$questions_data = explode(',', $question_data);
$questionsarray = array();

if (!empty($questions_data[0])) {
  foreach ($questions_data as $questions) {
    $que = get_post($questions);
    $title = $que->post_title;
    $postids[] = $que->ID;
    $questions_data = get_post_meta($questions, 'questions', true);
    $department_id = get_post_meta($questions, 'department', true);
    $department = get_post_meta($department_id, 'category_image', true);
    $src = wp_get_attachment_image_src($department, 'full')[0];

    if (!empty($questions_data)) {
      $questions_array = unserialize($questions_data);
      if (!empty($questions_array)) {
        array_values($questions_array);
        array_unshift($questions_array, "");
        unset($questions_array[0]);
      }
    }
    $type = 'custom';
    if ($questions_array['type'] == 'checkbox') {
      $type = 'agree/disagree';
    }
    if ($questions_array['type'] == 'yes/no') {
      $type = 'yesno';
    }
    $question['qus'] = $title;
    $question['page'] = $department_id;
    $question['type'] = $type;
    $question['options'] = $questions_array['option'];
    $question['img_src'] = $src;
    $question['que_id'] = $questions;
    array_push($questionsarray, $question);
    //print_r($question);
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title><?php the_title() ?></title>
  <link href="<?php bloginfo('template_url') . '/css/style.css' ?>" rel="stylesheet">
  <link rel="icon" href="<?php $fav_src ?>" type="image/gif" sizes="16x16">
  <style>
    html,
    body {
      width: 100%;
      height: 100%;
      overflow-x: hidden;
      margin: 0px;
      padding: 0px;
    }
  </style>
</head>

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
    <div id="tf_reviewsystemcontainer" class="maintf_reviewsystemcontent">

      <fieldset class="active">
        <div class="container">
          <?php 
          global $post;
          $tf_reviewsystem_id = $post->ID;
          $emp_id = base64_decode(urldecode($_GET['e']));
          ?>
          <h1>Link Expired</h1>
          <p>The link you have followed has expired.</p>
          <form action="" method="post" class="form-horizontal">
            <label for="reson for expire">Add Reason for Late Submission</label>
            <!-- <input type="text" name="reason" id="reason" required /> -->
            <textarea name="reason" id="reason" required=""></textarea>
            <input type="hidden" name="ajaxurl" value="<?php admin_url('admin-ajax.php') ?>">
            <input type="hidden" name="post_id" value="<?php $tf_reviewsystem_id ?>">
            <input type="hidden" name="emp_id" value="<?php $emp_id ?>">
            <input type="submit" name="submit" id="submit" value="Submit">
          </form>
          <style>
            .form-horizontal {
              display: flex;
              flex-wrap: wrap;
              flex-direction: column;
            }

            .form-horizontal label {
              margin-top: 10px;
              font-weight: 500;
              color: #428BCA;
              font-size: 14px;
            }

            .form-horizontal #reason {
              height: 65px;
              border-radius: 6px;
              border: 1px solid #d8d8d8;
              margin: 10px 0px 15px;
              resize: none;
            }

            .form-horizontal #reason:focus-visible {
              box-shadow: none;
              outline: none;
            }

            .form-horizontal #submit {
              background-color: #428BCA !important;
              border: none;
              border: none;
              padding: 10px 20px;
              width: fit-content;
              color: #fff;
              margin: 0px auto;
            }

            .form-horizontal #submit:hover {
              background-color: #286090 !important;
              cursor: pointer;
            }
          </style>
          <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
          <script>
            $(document).ready(function() {
              $("#submit").on("click",function() {
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
                  },
                  success: function(response) {
                    alert("Thank You");
                    window.location.replace("http://localhost/Wpreview/wp-admin/edit.php?post_type=tf_reviewsystem");
                  }
                });
              });
            })
          </script>
        </div>
      </fieldset>
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
      <?php } ?>
    </div>
    <!-- jQuery -->
    <script src="<?php bloginfo('template_url') . '/js/jquery.min.js' ?>"></script>
    <!-- jQuery easing plugin -->
    <script src="<?php bloginfo('template_url') . '/js/jquery.easing.min.js' ?>" type="text/javascript"></script>
    <script src="<?php bloginfo('template_url') . '/js/js_inner.js' ?>" type="text/javascript"></script>
  </div>
</body>

</html>