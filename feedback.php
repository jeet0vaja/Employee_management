<?php

// Callback function to display the page content
function my_plugin_feedback_page_callback()
{
?>
    <style>
        /* .sidebar .sidebar-shortcuts-large>.btn {
                width: 25px;
                line-height: normal;
            } */

        .table-bordered tbody tr td table {
            width: 100%;
        }

        .table-bordered tbody tr td table thead {
            background-color: #62b5df;
            color: #fff;
        }

        .table-bordered tbody tr td {
            padding: 0px;
        }

        .table-bordered tbody tr td table tbody tr td,
        .table-bordered tbody tr td table thead tr th {
            padding: 2px 8px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }

        .table-bordered tbody tr td table tr {
            padding: 5px;
            height: 30px;
        }

        .btn {
            margin-right: 10px;
            margin-bottom: 10px;
        }

        #id_overall_graph_button {
            margin-right: 7px;
        }

        span.reviewer_label {
            font-size: 12px;
        }

        .table-bordered tbody tr td table thead tr th {
            display: flex;
            flex-direction: column;
            height: 55px;
        }

        /* .table-bordered tbody tr td table tbody tr td:first-child{
                width: 234px;
            } */
        /* .question-table tbody tr td:first-child{
                width: 208px;
            } */

        .feedback_inner_wrapper {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
        }

        .feedback_inner_wrapper .form-group {
            width: 33.33%;
            margin-top: 21px;
        }

        .review_for {
            width: 100%;
        }

        .submit-wrapper input.btn {
            margin: 0px 3px;
        }

        .question-table tr.selected {
            background-color: #d0e9c6 !important;
        }

        .other-questions tr.selected {
            background-color: #d0e9c6 !important;
        }
    </style>
    <link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ ) . 'assets/css/bootstrap452.css'; ?>">
    <script src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/js/bootstrap452.js'; ?>"></script>
    <div class="wrap">
        <h1>Feedback</h1>
        <form method="post" action="" id="form_feedback">
            <div class="row mb-3">
                <div class="col">
                    <?php
                    $yearlist = get_posts([
                        'post_type' => 'tf-yearlist',
                        'post_status' => 'publish',
                        'numberposts' => -1,
                        'orderby'   => 'title',
                        'order'      => 'ASC'
                    ]);
                    ?>
                    <label for="year" class="form-label">Year:</label>
                    <select name="year" id="year" class="form-select">
                        <!-- Populate this dropdown with your years dynamically or manually -->
                        <option value="">Select Year</option>
                        <?php
                        foreach ($yearlist as $year) {
                            $year_name = $year->post_title;
                            $year_id = $year->ID;
                            echo '<option value="' . $year_id . '">' . $year_name . '</option>';
                        }
                        ?>
                        <!-- Add more options as needed -->
                    </select>
                </div>

                <div class="col">
                    <?php
                    $departments = get_posts([
                        'post_type' => 'departments',
                        'post_status' => 'publish',
                        'numberposts' => -1,
                        'orderby'   => 'title',
                        'order'      => 'ASC'
                    ]);
                    ?>
                    <label for="department" class="form-label">Department:</label>
                    <select name="department" id="department" class="form-select">
                        <!-- Populate this dropdown with your departments dynamically or manually -->
                        <option value="">Select Department</option>
                        <?php
                        foreach ($departments as $dept) {
                            $dept_name = $dept->post_title;
                            $dept_id = $dept->ID;
                            echo '<option value="' . $dept_id . '">' . $dept_name . '</option>';
                        }
                        ?>
                        <!-- Add more options as needed -->
                    </select>
                </div>

                <div class="col">
                    <?php
                    $employelist = get_users();
                    ?>
                    <label for="employee_name" class="form-label">Employee Name:</label>
                    <select name="employee_name" id="employee_name" class="form-select">
                        <!-- Populate this dropdown with your employee names dynamically or manually -->
                        <option value="">Select Employee</option>
                        <?php
                            
                        foreach ($employelist as $employee) {
                            $emp_meta = get_user_meta($employee->ID);
                            $emp_name = $employee->display_name;
                            // echo "hello" . $emp_meta['user_department'][0];
                            $options = '<option value="' . $emp_meta['user_department'][0] . '">' . $emp_name . '</option>';
                            echo $options;
                        }
                        ?> <!-- Add more options as needed -->
                    </select>
                </div>
            </div>
            <!-- Add other form fields as needed -->
            <div class="row">
                <div class="col text-center">
                    <button type="submit" class="btn btn-primary" name="submit_feedback">Submit</button>
                </div>
            </div>
        </form>
    </div>
    <?php
    if (isset($_POST['submit_feedback'])) {
        global $wpdb;
        $year_name = $_POST['year'];
        $department_name = $_POST['department'];
        $review_for = $_POST['employee_name'];

        $sql = "select res_detail.*, res.review_for, res.review_by, res.comments from wp_tf_reviewsystem_responces AS res LEFT JOIN wp_tf_reviewsystem_responce_details AS res_detail ON res.id = res_detail.tf_reviewsystem_responce_id LEFT JOIN wp_feedback_para AS para ON para.post_id = res.tf_reviewsystem_id Where para.year_id = " . $year_name . " AND para.department_id = " . $department_name . " AND para.review_for =" . $review_for . " AND res_detail.responce !='' ORDER BY res_detail.`id` ASC";
        $query = $wpdb->prepare($sql, $year_name, $department_name, $review_for);
        $myrow = $wpdb->get_results($query);
        //echo "<pre>";
       // print_r($myrow);
       // echo count($myrow);
      //  exit;

    ?>

        <section class="employee-data-response">
            <div class="table-responsive">

                <?php
               // $z = 0;
                $question_data = array();
                $que_array = array();
                $reviewer_data = array();
                $answer_array = array();
                $reviewer_array = array();
                $i = 1;

                
                    for($z=0; $z<count($myrow); $z++){
                   
                    $sql = "SELECT * FROM `wp_questions` WHERE `post_id` = " . $myrow[$z]->question_id . "";

                    $qr = $wpdb->get_results($sql);
                    $num_rows = count($qr);
                    //echo "<br>";
                  //  print_r($qr);  
                    if ($num_rows > 0) {

                        foreach ($qr as $row) {
                           // print_r($row->title);
                            $que_id = $myrow[$z]->question_id;
                            if (!in_array($que_id, $que_array)) {
                                $que_array[] = $que_id;
                                $question_data[] = $row->title;
                            }
                        }
                        // print_r($question_data);
                        $user_id = $myrow[$z]->review_by;
                        $userData = get_userdata($user_id);

                        if (!in_array($userData->display_name, $reviewer_array)) {
                            $reviewer_array[] = $userData->display_name;
                            $reviewer_ids[] = $userData->ID;
                        }

                        
                        
                        $reviewer_data['reviewer'] = $reviewer_array;
                        $reviewer_data['reviewer_id'] = $reviewer_ids;
                        $cnt = count($reviewer_array) - 1;
                        if($myrow[$z]->responce != ''){
                            $answer[$cnt][] = $myrow[$z]->responce;
                        }

                        if($myrow[$z]->score != ''){
                            $answer[$cnt][] = $myrow[$z]->score;
                        }

                      
                    }
                    
                }
             
                $sumArray = array();
                $new_arr = array();
                $cntZero_Arr = array();
                $cntZero = 1;
                foreach ($answer as $k => $subArray) {

                    foreach ($subArray as $id => $value) {
                        isset($sumArray[$id]) || $sumArray[$id] = 0 || $sumArray[$id] = 0;
                        $sumArray[$id] += $value;
                        if ($value == 0 || $value == '') {
                            $cntZero_Arr[$id] += $cntZero;
                        } else {
                            $cntZero_Arr[$id] += 0;
                        }
                    }
                }
                //  echo "<pre>";  echo $review_for;  print_r($answer); print_r($cntZero_Arr); die;

                if (in_array($review_for, $reviewer_data['reviewer_id'])) {

                    $key = array_search($review_for, $reviewer_data['reviewer_id']);
                }
                $unsetarray = $answer[$key];
                unset($answer[$key]);
                array_unshift($answer, $unsetarray);

                unset($reviewer_data['reviewer_id'][$key]);
                array_unshift($reviewer_data['reviewer_id'], $review_for);

                $unset_reviewer_name = $reviewer_data['reviewer'][$key];
                unset($reviewer_data['reviewer'][$key]);
                array_unshift($reviewer_data['reviewer'], $unset_reviewer_name);


            //   echo "<pre>";  echo $review_for;  print_r($reviewer_data); print_r($answer); print_r($question_data); die;

                if (count($reviewer_data['reviewer']) > 0) {
                ?>
                    <table class="table table-bordered table-hover question-table">
                        <tr>
                            <td width="30%">
                                <table class="fdtable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col">Questions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($question_data as $ques_array) { ?>
                                            <tr>
                                                <td><?php echo $ques_array; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </td>
                            <?php for ($r = 0; $r < count($reviewer_data['reviewer_id']); $r++) {  ?>
                                <td width="10%">
                                    <table class="fdtable">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th scope="col">
                                                    <?php echo $reviewer_data['reviewer'][$r];  ?>
                                                    <?php if ($review_for == $reviewer_data['reviewer_id'][$r]) {
                                                        echo "<span class='reviewer_label'>(Self Review)</span>";
                                                    } else {
                                                        echo "<span class='reviewer_label'>(Peer Reviews " . $r . ")</span>";
                                                    } ?>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php for ($l1 = 0; $l1 < count($answer[$r]); $l1++) {
                                            ?>
                                                <tr>
                                                    <td class="center"><?php echo $answer[$r][$l1]; ?></td>
                                                </tr>
                                            <?php  } ?>
                                        </tbody>
                                    </table>
                                </td><?php } ?>
                            <td width="5%">
                                <table class="fdtable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for ($l1 = 0; $l1 < count($sumArray); $l1++) { ?>
                                            <tr>
                                                <td class="center"><?php echo $sumArray[$l1]; ?></td>
                                            </tr>
                                        <?php  } ?>
                                    </tbody>
                                </table>
                            </td>
                            <td width="5%">
                                <table class="fdtable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col">Average</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for ($l1 = 0; $l1 < count($sumArray); $l1++) {
                                        //     echo $sumArray[$l1];
                                        //     echo count($reviewer_data['reviewer']);
                                        //    echo $cntZero_Arr[$l1];

                                            $avg = ($sumArray[$l1] / (count($reviewer_data['reviewer']) - $cntZero_Arr[$l1]));
                                            $final_avg = round($avg, 2);
                                            if ($final_avg < 50) { ?>
                                                <tr style="background-color: #FFCCCB !important;">
                                                    <td class="center"><?php echo round($avg, 2); ?></td>
                                                </tr>
                                            <?php } else {
                                            ?>
                                                <tr>
                                                    <td class="center"><?php echo round($avg, 2); ?></td>
                                                </tr>
                                        <?php  }
                                        } ?>
                                    </tbody>
                                </table>

                            </td>


                        </tr>
                    </table>

                <?php } else {
                    echo '<div class="center no-data-available"><h2 style="color:red"> No data available </h2></div>';
                } ?>
                <?php /******Metrix of Yes/No Question*******/ ?>
              
                <?php
 $sql = "select res_detail.*, res.review_for, res.review_by, res.comments from wp_tf_reviewsystem_responces AS res LEFT JOIN wp_tf_reviewsystem_responce_details AS res_detail ON res.id = res_detail.tf_reviewsystem_responce_id LEFT JOIN wp_feedback_para AS para ON para.post_id = res.tf_reviewsystem_id Where para.year_id = " . $year_name . " AND para.department_id = " . $department_name . " AND para.review_for =" . $review_for . " AND res_detail.score !='' ORDER BY res_detail.`id` ASC";
 $query = $wpdb->prepare($sql, $year_name, $department_name, $review_for);
 $myrow1 = $wpdb->get_results($query);


$question_data1 = array();
$que_array1 = array();
$reviewer_data1 = array();
$answer_array1 = array();
$reviewer_array1 = array(); 
$comment_array1 = array();
for($z=0; $z<count($myrow1); $z++){
                   
    $sql = "SELECT * FROM `wp_questions` WHERE `post_id` = " . $myrow1[$z]->question_id . "";

    $qr = $wpdb->get_results($sql);
    $num_rows = count($qr);
    
    if ($num_rows > 0) {

        foreach ($qr as $row) {
           // print_r($row->title);
            $que_id = $myrow1[$z]->question_id;
            if (!in_array($que_id, $que_array1)) {
                $que_array1[] = $que_id;
                $question_data1[] = $row->title;
            }
        }
      
        $user_id = $myrow1[$z]->review_by;
        $userData = get_userdata($user_id);

        if (!in_array($userData->display_name, $reviewer_array1)) {
            $reviewer_array1[] = $userData->display_name;
            $reviewer_ids1[] = $userData->ID;
            $comment_array1[] = $myrow1[$z]->comments;
        }

        
        
        $reviewer_data1['reviewer'] = $reviewer_array1;
        $reviewer_data1['reviewer_id'] = $reviewer_ids1;
        $reviewer_data1['comment'] = $comment_array1;
        $cnt = count($reviewer_array1) - 1;
      
        if($myrow1[$z]->score != ''){
            $answer_array1[$cnt][] = $myrow1[$z]->score;
        }

      
    }
    
}

//echo "<pre>"; print_r($question_data1);

if (in_array($review_for, $reviewer_data1['reviewer_id'])) {

    $key = array_search($review_for, $reviewer_data1['reviewer_id']);
}
$unsetarray = $answer_array1[$key];
unset($answer_array1[$key]);
array_unshift($answer_array1, $unsetarray);

 
unset($reviewer_data1['reviewer_id'][$key]);
array_unshift($reviewer_data1['reviewer_id'], $review_for);

$unset_reviewer_name = $reviewer_data1['reviewer'][$key];
unset($reviewer_data1['reviewer'][$key]);
array_unshift($reviewer_data1['reviewer'], $unset_reviewer_name);

$unset_reviewer_comment = $reviewer_data1['comment'][$key];
unset($reviewer_data1['comment'][$key]);
array_unshift($reviewer_data1['comment'], $unset_reviewer_comment);

 //echo "<pre>";  print_r( $reviewer_data1 ); print_r($answer_array1);  print_r($comment_array1); die;
if (count($reviewer_data1['reviewer']) > 0) {

?>

    <table class="table table-bordered table-hover other-questions">
        <tr>
            <td width="30%">
                <table class="fdtable_choice">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Questions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($question_data1 as $ques_array) { ?>
                            <tr class="comment more">
                                <td><?php echo $ques_array; ?></td>
                            </tr>
                        <?php } ?>
                        <tr class="comment more">
                            <td>Additional Comments</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <?php for ($r = 0; $r < count($reviewer_data1['reviewer_id']); $r++) {

                $comments_text = $reviewer_data1['comment'];
                 
            ?>
                <td width="10%">

                    <table class="fdtable_choice">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">
                                    <?php echo $reviewer_data1['reviewer'][$r];  ?>
                                    <?php if ($review_for == $reviewer_data1['reviewer_id'][$r]) {
                                        echo "<span class='reviewer_label'>(Self Review)</span>";
                                    } else {
                                        echo "<span class='reviewer_label'>(Peer Reviews " . $r . ")</span>";
                                    } ?>

                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($l1 = 0; $l1 < count($answer_array1[$r]); $l1++) {

                            ?>
                                <tr>
                                    <?php if ($answer_array1[$r][$l1] == 'No') { ?>
                                        <td class="center" style="color: red; font-weight:600;"><?php echo $answer_array1[$r][$l1]; ?></td>
                                    <?php } else { ?>
                                        <td class="center"><?php $ans = $answer_array1[$r][$l1]; if($ans == '1'){ echo "Yes";}elseif($ans == '4'){ echo "No";}else{ $ans;} ?></td>
                                    <?php }
                                    ?>
                                </tr>
                            <?php  } ?>
                            <tr>
                                <td class="center">
                                    <a type="button" data-toggle="modal" data-target="#exampleModalCenter<?php echo $reviewer_data1['reviewer_id'][$r]; ?>">
                                        Show Comment
                                    </a>
                                    <div class="modal fade" id="exampleModalCenter<?php echo $reviewer_data1['reviewer_id'][$r]; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background-color: #65aee2;">
                                                    <?php
                                                    //$name = $comments_text['roll_no'];
                                                    // $name_sql = "SELECT * FROM `employee_master` WHERE emp_id = " . $name;
                                                    // $name_result = mysqli_query($conn, $name_sql);
                                                    // $name_row = mysqli_fetch_array($name_result);
                                                    ?>
                                                    <p class="modle_head_name"><?php echo $reviewer_data1['reviewer'][$r]; ?>'s Comment</p>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff;">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body" style="max-height: 200px; word-break: break-word;">
                                                    <p class="comment-popup">
                                                        <?php
                                                        if (!empty($comments_text[$r])) {
                                                            echo $comments_text[$r];
                                                            //if($comments_text[$r] == '1'){ echo "Yes";}elseif($comments_text[$r] == '4'){ echo "No";}else{ $comments_text[$r];}
                                                        } else {
                                                            echo "No Comments";
                                                        }
                                                        ?>
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>

                        </tbody>
                    </table>

                </td><?php } ?>
            <td width="5%">
                <table class="fdtable_choice">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col" class="trasparant-th">Total</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </td>
            <td width="5%">
                <table class="fdtable_choice">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col" class="trasparant-th">Average</th>
                        </tr>
                    </thead>

                </table>
            </td>

        </tr>
    </table>
<?php } ?>
<?php /*****END of Yes/No Code******/ ?>
              


            </div>
        </section>
    <?php }
    ?>
    <script src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/js/jquery364min.js'; ?>"></script>

    <script>
        jQuery(document).ready(function($) {
            $('#department').on('change', function() {
                var departmentId = $(this).val();

                // Make an AJAX request to get employees based on the selected department
                $.ajax({
                    type: 'POST',
                    url: ajaxurl, // WordPress AJAX URL
                    data: {
                        action: 'get_emps',
                        department_id: departmentId
                    },
                    dataType: 'json',
                    success: function(employees) {
                        updateEmployeeDropdown(employees);
                    }
                });
            });

            function updateEmployeeDropdown(employees) {
                var employeeDropdown = $('#employee_name');
                employeeDropdown.empty().append('<option value="">Select Employee</option>');

                $.each(employees, function(index, employee) {
                    // console.log(employee);
                    var empName = employee.data.display_name;
                    var empValue = employee.ID; // Adjust this based on your employee data structure
                    employeeDropdown.append('<option value="' + empValue + '">' + empName + '</option>');
                });
            }
        });
    </script>
    <?php
}


function create_submenu_page_generate_link_callback()
{
    $user_c = get_user_by('id', $_GET['surveyreview_for']);
    $post_id = $_GET['post_id'];
    global $wpdb;
    if ($_GET['review_feedback_status'] == 0) {
        echo "<div class='message-reason-late'><h4 style='color:red'>" . $user_c->display_name . " didn't submitted the reason of late submission hance, you can not generate a new link.</h4></div>";
    } else {
        // echo "<h4>" . $user_c->display_name . " submitted the reason of late submission, you can generate a new link.</h4>";
        $sql_for_pera = "SELECT * FROM `wp_feedback_para` WHERE `post_id` = " . $post_id . "";
        $pera = $wpdb->get_results($sql_for_pera);

        $sql_for_msg = "SELECT * FROM `wp_feedback_status` WHERE `post_id` = " . $post_id . " AND `peer_review_id` = " . $user_c->ID . "";
        $msg = $wpdb->get_results($sql_for_msg); ?>

        <div class="message-reason">


            <h2>Here is the <strong><?php echo $user_c->display_name ?>'s</strong> reason of late submission</h2>
            <h4><?php echo $msg[0]->reason_message ?></h4>



            <form action="" method="post" id="reschedule_review">
                <input type="hidden" name="user_email" value="<?php echo $user_c->user_email; ?>" class="peer_email">
                <input type="hidden" name="post_id" value="<?php echo $post_id ?>">
                <input type="hidden" name="ajaxurl" value="<?php echo admin_url('admin-ajax.php'); ?>">
                <input type="hidden" name="user_token" value="<?php echo $pera[0]->token; ?>">
                <input type="hidden" name="peer_review_id" value="<?php echo $user_c->ID; ?>">
                <input type="hidden" name="review_for" value="<?php echo $pera[0]->review_for; ?>">
                <input type="submit" class="btn btn-primary send_new_link" id="send_new_link" name="submit" value="Generate Link">
            </form>
        </div>
        <script src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/js/jquery364min.js'; ?>"></script>
        <script>
            $("#send_new_link").click(function() {
                alert('send_new_link');
                var myAjax = $('input[name="ajaxurl"]').val();
                // alert(myAjax); return false;
                $.ajax({
                    type: "post",
                    // dataType : "json",
                    url: myAjax,
                    data: {
                        action: "send_new_link_mail",
                        form_data: $('#reschedule_review').serialize(),
                        user_token: $('input[name="user_token"]').val(),
                        user_email: $('input[name="user_email"]').val(),
                        post_id: $('input[name="post_id"]').val(),
                        peer_review_id: $('input[name="peer_review_id"]').val(),
                        review_for: $('input[name="review_for"]').val(),
                    },
                    success: function(response) {
                        console.log(response);
                        alert('sent');
                    }
                });
            });
        </script>
<?php }
}
