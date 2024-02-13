<?php
/*
Plugin Name: Employee Review System
Plugin URI: #
Description: Employee Review System
Version: 1.0.0
Author: Techforce
Author URI: http://techforceglobal.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: employee-reviewsystem
Domain Path: /languages
*/
if (!defined('ABSPATH')) exit; // Exit if accessed directly      
//require __DIR__ . '/vendor/autoload.php';
ob_start();
include_once('feedback.php');
include_once('questionnaire.php');
include_once('review_year.php');
include_once('resources.php');
include_once('settings.php');

require ABSPATH . WPINC . '/PHPMailer/Exception.php';
require ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
require ABSPATH . WPINC . '/PHPMailer/SMTP.php';

ini_set('memory_limit', -1);

/* Custom Post Type Start */
// Hooking up our function to theme setup
add_action('init', 'create_tf_reviewsystem_posttype');

function create_submenu_page()
{
	add_submenu_page(
		'edit.php?post_type=tf_reviewsystem',
		'Feedback',
		'Feedback',
		'manage_options',
		'feedback',
		'my_plugin_feedback_page_callback' //callback function
	);
}
add_action('admin_menu', 'create_submenu_page');


function create_submenu_page_generate_link()
{
	add_submenu_page(
		'edit.php?post_type=tf_reviewsystem',
		'Link',
		'Link',
		'manage_options',
		'link',
		'create_submenu_page_generate_link_callback' //callback function
	);
}
add_action('admin_menu', 'create_submenu_page_generate_link');


function getquestions()
{
	if (isset($_POST['post_id']) && !empty($_POST['post_id'])) {
		getQuestionsByDepartment($_POST['post_id']);
	}
}

function getpeerReviewer()
{
	if (isset($_POST['post_id']) && !empty($_POST['post_id'])) {
		getpeerReviewerByEmp($_POST['post_id']);
	}
}

function getlocations()
{

	$id = $_POST['id'];
	$location = get_post_meta($id, 'locations', true);
	$loc_data = array();
	foreach ($location as $value) {
		$alllocations = get_the_title($value);
		array_push($loc_data, array('value' => $value, 'name' => $alllocations));
	}
	wp_send_json_success($loc_data);
}

function get_emps()
{

	$department_id = isset($_POST['department_id']) ? $_POST['department_id'] : '';

	// Fetch employees based on the selected department
	$employees = get_users(['meta_key' => 'user_department', 'meta_value' => $department_id]);
	//echo "hello";

	// Return employee data as JSON
	wp_send_json($employees);
	// print_r($employees);
	// exit;
}

function send_new_link_mail()
{
	$post_id = $_POST['post_id'];
	$user_email = $_POST['user_email'];
	$user_token = $_POST['user_token'];
	$new_date = date('Y-m-d');
	$peer_review_id = $_POST['peer_review_id'];
	$review_for_id = $_POST['review_for'];

	$post = get_post($post_id);
	$slug = $post->post_name;
	$url = site_url() . '/index.php' . '/u' . '/' . $slug . '?t=' . base64_encode($user_token) . '&e=' . base64_encode($peer_review_id) . '&d=' . base64_encode($new_date) . '&tu=' . $review_for_id;

	$template_content = '<p>&nbsp;</p>

<table border="1" cellpadding="10" cellspacing="0" style="width:100%">
	<tbody>
		<tr>
			<td>
			<h1><strong>Annual Review Process - {review_type}</strong></h1>
			</td>
		</tr>
	</tbody>
</table>

<table border="1" cellpadding="10" cellspacing="0" style="width:100%">
	<tbody>
		<tr>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" style="width:100%">
				<tbody>
					<tr>
					</tr>
					<tr>
						<td style="text-align:left; vertical-align:top">
						<p>Hello {name},&nbsp;</p>

						<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%">
							<tbody>
								<tr>
									<td>
									<table align="left" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; width:100%">
										<tbody>
											<tr>
												<td>
												<table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; width:100%">
													<tbody>
														<tr>
															<td>
															<table align="center" border="0" cellpadding="0" cellspacing="0" style="height:169px; width:100% !important">
																<tbody>
																	<tr>
																		<td style="height:53px">
																		<p>I hope this message finds you well.</p>

																		<p>As part of our Annual Performance Review process, we have initiated the review for <strong>{target_user}</strong>.We kindly request you to take a moment to complete the Performance Review form through the provided link:</p>
																		</td>
																	</tr>
																	<tr>
																		<td style="height:53px">
																		<p>📝 <a href="{url}">Click here for the Performance Review form</a></p>

																		<p>📅 Review End Date : {end_date}</p>

																		<p>⌛ Review End Time : 12:00 AM</p>
																		</td>
																	</tr>
																	<tr>
																		<td style="height:21px">&nbsp;</td>
																	</tr>
																	<tr>
																		<td style="height:74px">We greatly appreciate your time and cooperation in this matter. Please be aware that if we do not receive the completed review form by the specified date, we will be unable to include your input in the evaluation process. It is important to recognize the significance of peer reviews, as they play a crucial role in the overall assessment.
																		<p>Thank you for your understanding and prompt attention to this matter.</p>
																		</td>
																	</tr>
																	<tr>
																		<td style="height:74px">
																		<p>Warm regards,<br />
																		Techforce - HR Dept.</p>
																		</td>
																	</tr>
																	<tr>
																		<td style="height:21px">&nbsp;</td>
																	</tr>
																</tbody>
															</table>
															</td>
														</tr>
													</tbody>
												</table>
												</td>
											</tr>
										</tbody>
									</table>
									</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
	</tbody>
</table>

<p>&nbsp;</p>';

	$first_name = '';
	$last_name = '';
	$review_type = '';
	$target_user = '';
	$vars = array(
		'{name}' => $first_name . '&nbsp;' . $last_name,
		'{review_type}' => $review_type,
		'{url}' => $url,
		'{target_user}' => $target_user,
		'{end_date}' => $new_date,
	);

	$subject = 'Annual Review Process - Peer Review';
	$mail_message = strtr($template_content, $vars);
	$email = $user_email;
	//$headers = array('Content-Type: text/html; charset=UTF-8');

	date_default_timezone_set('Etc/UTC');

	$mail = new PHPMailer\PHPMailer\PHPMailer();
	$mail->isSMTP();
	$mail->SMTPSecure = "tls";
	$mail->Debugoutput = 'html';
	$mail->CharSet = "UTF-8";
	$mail->Host = "" . get_option('smtp_host') . "";
	$mail->Port = get_option('smtp_port');
	$mail->SMTPAuth = true;
	$mail->Username = "" . get_option('smtp_username') . "";
	$mail->Password = "" . get_option('smtp_password') . "";
	$mail->setFrom("" . get_option('smtp_username') . "");
	$mail->IsHTML(true);
	$mail->addAddress($email);
	$mail->Subject = $subject;
	$mail->Body = $mail_message;
	$mail->AltBody = $mail_message;
	$mail->send();
}

function sendEmailtousers()
{

	global $wpdb;
	$post_id = $_POST['post_id'];
	add_post_meta($post_id, 'mail_send_status', 'sent');
	$review_for_id = $_POST['review_for_id'];
	$peer_review_id = $_POST['peer_review_id'];
	$peer_review = explode(',', $peer_review_id);
	array_push($peer_review, $review_for_id);

	$department = get_post_meta($post_id, 'tf_reviewsystemdepartment');
	$yearlist = get_post_meta($post_id, 'tf_reviewsystemcompany', true);
	$question_data = get_post_meta($post_id, 'question_ids', true);
	$enddate = get_post_meta($post_id, 'enddate', true);
	$review_enddt = date('Y-m-d', strtotime($enddate));

	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < 10; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	$table_name = $wpdb->prefix . 'feedback_para';
	$results = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE post_id = %d", $post_id));
	if ($results !== null && (is_array($results) || $results instanceof Countable) && count($results) > 0) {
		$randomString = $results->token;
	} else {
		$wpdb->insert($table_name, array(
			"post_id" => $post_id,
			"year_id" => $yearlist,
			"department_id" => $department[0],
			"review_for" => $review_for_id,
			"peer_review" => $peer_review_id,
			"questionlist" => $question_data,
			"review_enddt" => $review_enddt,
			"token" => $randomString,
		));
	}
	for ($i = 0; $i < count($peer_review); $i++) {

		$user = get_user_by('id', $peer_review[$i]);
		// echo "<pre>";
		// print_r($user);
		$first_name = $user->first_name;
		$last_name = $user->last_name;
		$target_user_name = $first_name . '&nbsp;' . $last_name;

		if ($peer_review[$i] == $review_for_id) {
			$target_user = 'You';
			$review_type = 'Self Review';
		} else {
			$target_user = $target_user_name;
			$review_type = 'Peer Review';
		}

		$token = $randomString;
		$format_review_enddt = date("d-M-Y", strtotime($review_enddt));
		$post = get_post($post_id);
		$slug = $post->post_name;
		$url = site_url() . '/index.php' . '/u' . '/' . $slug . '?t=' . urlencode(base64_encode($token)) . '&e=' . urlencode(base64_encode($peer_review[$i])) . '&d=' . base64_encode($review_enddt) . '&tu=' . $review_for_id;

		$template_content = '<p>&nbsp;</p>

<table border="1" cellpadding="10" cellspacing="0" style="width:100%">
	<tbody>
		<tr>
			<td>
			<h1><strong>Annual Review Process - {review_type}</strong></h1>
			</td>
		</tr>
	</tbody>
</table>

<table border="1" cellpadding="10" cellspacing="0" style="width:100%">
	<tbody>
		<tr>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" style="width:100%">
				<tbody>
					<tr>
					</tr>
					<tr>
						<td style="text-align:left; vertical-align:top">
						<p>Hello {name},&nbsp;</p>

						<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%">
							<tbody>
								<tr>
									<td>
									<table align="left" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; width:100%">
										<tbody>
											<tr>
												<td>
												<table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; width:100%">
													<tbody>
														<tr>
															<td>
															<table align="center" border="0" cellpadding="0" cellspacing="0" style="height:169px; width:100% !important">
																<tbody>
																	<tr>
																		<td style="height:53px">
																		<p>I hope this message finds you well.</p>

																		<p>As part of our Annual Performance Review process, we have initiated the review for <strong>{target_user}</strong>.We kindly request you to take a moment to complete the Performance Review form through the provided link:</p>
																		</td>
																	</tr>
																	<tr>
																		<td style="height:53px">
																		<p>📝 <a href="{url}">Click here for the Performance Review form</a></p>

																		<p>📅 Review End Date : {end_date}</p>

																		<p>⌛ Review End Time : 12:00 AM</p>
																		</td>
																	</tr>
																	<tr>
																		<td style="height:21px">&nbsp;</td>
																	</tr>
																	<tr>
																		<td style="height:74px">We greatly appreciate your time and cooperation in this matter. Please be aware that if we do not receive the completed review form by the specified date, we will be unable to include your input in the evaluation process. It is important to recognize the significance of peer reviews, as they play a crucial role in the overall assessment.
																		<p>Thank you for your understanding and prompt attention to this matter.</p>
																		</td>
																	</tr>
																	<tr>
																		<td style="height:74px">
																		<p>Warm regards,<br />
																		Techforce - HR Dept.</p>
																		</td>
																	</tr>
																	<tr>
																		<td style="height:21px">&nbsp;</td>
																	</tr>
																</tbody>
															</table>
															</td>
														</tr>
													</tbody>
												</table>
												</td>
											</tr>
										</tbody>
									</table>
									</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
	</tbody>
</table>

<p>&nbsp;</p>';
		$vars = array(
			'{name}' => $first_name . '&nbsp;' . $last_name,
			'{review_type}' => $review_type,
			'{url}' => $url,
			'{target_user}' => $target_user,
			'{end_date}' => $format_review_enddt,
		);

		$subject = 'Annual Review Process - Peer Review';
		$mail_message = strtr($template_content, $vars);
		$email = $user->user_email;
		//$headers = array('Content-Type: text/html; charset=UTF-8');


		date_default_timezone_set('Etc/UTC');

		$mail = new PHPMailer\PHPMailer\PHPMailer();
		$mail->isSMTP();
		$mail->SMTPSecure = "tls";
		$mail->Debugoutput = 'html';
		$mail->CharSet = "UTF-8";
		$mail->Host = "" . get_option('smtp_host') . "";
		$mail->Port = get_option('smtp_port');
		$mail->SMTPAuth = true;
		$mail->Username = "" . get_option('smtp_username') . "";
		$mail->Password = "" . get_option('smtp_password') . "";
		$mail->setFrom("" . get_option('smtp_username') . "");
		$mail->IsHTML(true);
		$mail->addAddress($email);
		$mail->Subject = $subject;
		$mail->Body = $mail_message;
		$mail->AltBody = $mail_message;
		$mail->send();
	}
}


function getpeerlist()
{

	$id = $_POST['id'];

	$args = array(
		'role'    => 'Subscriber',
		'orderby' => 'last_name',
		'order'   => 'ASC',
		'exclude'   => $id
	);
	$employeeData = get_users($args);

	$loc_data = array();
	foreach ($employeeData as $value) {

		$display_name = $value->display_name;
		$ID = $value->ID;
		array_push($loc_data, array('value' => $ID, 'name' => $display_name));
	}
	wp_send_json_success($loc_data);
}


add_filter('manage_tf_reviewsystem_posts_columns', 'custom_post_type_columns');
function custom_post_type_columns($columns)
{
	return array(
		'cb' => '<input type="checkbox" />',
		'title' => __('Title'),
		'tf_reviewsystemcompany' => __('Yearlist'),
		'tf_reviewsystemdepartment' => __('Department'),
		'tf_reviewsystemreview_for' => __('Review For'),
		'tf_reviewsystemlocation' => __('Peer Review'),
		'avaliablity' => __('End Date'),
		'date' => __('Date'),
		'sendemail' => __('Send Email'),
	);
}

add_filter('post_row_actions', 'my_action_row', 10, 2);

function my_action_row($actions, $post)
{
	$actions['view'] = '<a href="' . get_permalink($post->ID) . '" rel="bookmark" aria-label="' . $post->post_title . '" target="_blank">View</a>';
	return $actions;
}

add_action('manage_tf_reviewsystem_posts_custom_column', 'fill_custom_post_type_columns', 10, 2);

function fill_custom_post_type_columns($column, $post_id)
{
	switch ($column) {
		case 'avaliablity':
			$avaliablity = get_post_meta($post_id, 'avaliablity', true);
			$startdate = get_post_meta($post_id, 'startdate', true);
			$enddate = get_post_meta($post_id, 'enddate', true);
			// if ($avaliablity == 'always') {
			// 	echo "Always Avaliable";
			// } else if ($avaliablity == 'specific') {
			// 	echo $enddate;
			// }
			echo $enddate;
			break;
		case 'tf_reviewsystemlocation':
			$tf_reviewsystemlocation = get_post_meta($post_id, 'tf_reviewsystemlocation', true);
			$employee_ex = explode(',', $tf_reviewsystemlocation);
			$submitter = get_post_meta($post_id, 'tf_reviewsystemreview_for', true);
			array_push($employee_ex, $submitter);
			//print_r($employee_ex);
			$emp_name = '';
			$i = 1;
			foreach ($employee_ex as $eid) :

				$user = get_user_by('id', $eid);
				$emp_name .= $user->display_name . ',';
				$submitter = get_post_meta($post_id, 'tf_reviewsystemreview_for', true);
				$submit_reviewer = get_post_meta($post_id, 'review_feedback_status', true);
				global $wpdb;

				$sql_to_check_status = "SELECT * FROM `wp_feedback_status` WHERE `post_id` = " . $post_id . " AND peer_review_id=" . $eid;
				$value = $wpdb->get_row($sql_to_check_status);
				$enddate_str = get_post_meta($post_id, 'enddate', true);
				$enddate = date('Y-m-d', strtotime($enddate_str));
				$date1 = strtotime(date('Y-m-d'));
				$date2 = strtotime($enddate);
				$dateDifference = ($date2 - $date1);
				$peeruser = get_user_by('id', $value->peer_review_id);
				if ($value->status == '1') {
					echo '<button type="button" class="btn success_link_green">' . $peeruser->user_login . '</button>';
				} elseif ($value->status == '0') {
					if ($dateDifference < 0) {
						echo '<a class="pending_link" href="edit.php?post_type=tf_reviewsystem&page=link&surveyreview_for=' . $peeruser->ID . '&post_id=' . $value->post_id . '&review_feedback_status=' . $value->reason_status . '" class="btn" >' . $peeruser->user_login . '</a>';
					} else {
						echo '<button type="button" class="btn pending_link_red">' . $peeruser->user_login . '</button>';
					}
				}
				// if ($value->status == '1') {
				// 	echo '<button type="button" class="btn" style="cursor: not-allowed; margin-bottom: 10px; border-radius: 5px; background-color: green;color: #fff;padding: 5px 15px;">' . $peeruser->user_login . '</button>';
				// } elseif ($value->status == '0') {
				// 	if ($dateDifference < 0) {
				// 		echo '<a class="pending_link" href="edit.php?post_type=tf_reviewsystem&page=link&surveyreview_for=' . $peeruser->ID . '&post_id=' . $value->post_id . '&review_feedback_status=' . $value->reason_status . '" class="btn" >' . $peeruser->user_login . '</a>';
				// 	} else {
				// 		echo '<button type="button" style="cursor: not-allowed ;margin-bottom: 10px; border-radius: 5px; background-color: red;color: #fff;padding: 5px 15px;">' . $peeruser->user_login . '</button>';
				// 	}
				// }

				// print_r($review_status);



?>
			<?php
				$i++;
			endforeach;
			break;
		case 'tf_reviewsystemcompany':
			$tf_reviewsystem_company = get_post_meta($post_id, 'tf_reviewsystemcompany', true);
			$companys = get_post($tf_reviewsystem_company);

			echo $companys->post_title;
			break;
		case 'tf_reviewsystemdepartment':
			$tf_reviewsystemdepartment = get_post_meta($post_id, 'tf_reviewsystemdepartment', true);
			$department = get_post($tf_reviewsystemdepartment);

			echo $department->post_title;
			break;

		case 'tf_reviewsystemreview_for':
			$tf_reviewsystem_review_for = get_post_meta($post_id, 'tf_reviewsystemreview_for', true);
			$user = get_user_by('id', $tf_reviewsystem_review_for);
			echo $user->display_name;
			//$review_for = get_post($tf_reviewsystem_review_for);

			//echo $review_for->post_title;
			break;

		case 'sendemail':
			$tf_reviewsystem_review_for = get_post_meta($post_id, 'tf_reviewsystemreview_for', true);
			$tf_reviewsystemlocation = get_post_meta($post_id, 'tf_reviewsystemlocation', true);
			$mail_Sent = get_post_meta($post_id, 'mail_send_status', true);

			$smtp_host = get_option('smtp_host');
			$smtp_port = get_option('smtp_port');
			$smtp_username = get_option('smtp_username');
			$smtp_password = get_option('smtp_password');

			if (!empty($smtp_host) && !empty($smtp_port) && !empty($smtp_username) && !empty($smtp_password)) {
				if ($mail_Sent == 'sent') {
					echo $email_html = '<button class="reviewlist sent-mail" data-review_for_id="' . $tf_reviewsystem_review_for . '" data-peer_review_id="' . $tf_reviewsystemlocation . '" post_id="' . $post_id . '" style="cursor: not-allowed" disabled>Sent</button>';
				} else {
					echo $email_html = '<button class="reviewlist sendEmail " data-review_for_id="' . $tf_reviewsystem_review_for . '" data-peer_review_id="' . $tf_reviewsystemlocation . '" post_id="' . $post_id . '">Send</button>';
				}
			} else {

				echo '<a href="edit.php?post_type=tf_reviewsystem&page=custom-settings"><p style="color:red">Please configure smtp settings</p></a>';
			}
			//echo $mail_Sent;

			break;

		case 'url':
			$url = get_post_meta($post_id, 'url', true);
			echo $url;
			break;
	}
}
add_filter('manage_edit-tf_reviewsystem_sortable_columns', 'my_sortable_cake_column');

// Hook function to add submenu page

function my_sortable_cake_column($columns)
{
	$columns['tf_reviewsystemcompany'] = 'tf_reviewsystemcompany';
	$columns['tf_reviewsystemlocation'] = 'tf_reviewsystemlocation';

	//To make a column 'un-sortable' remove it from the array
	//unset($columns['date']);

	return $columns;
}
add_action('pre_get_posts', 'smashing_posts_orderby');
function smashing_posts_orderby($query)
{
	if (!is_admin() || !$query->is_main_query()) {
		return;
	}

	if ('tf_reviewsystemcompany' === $query->get('orderby')) {
		$query->set('orderby', 'meta_value');
		$query->set('meta_key', 'tf_reviewsystemcompanyname');
		$query->set('meta_type', 'text');
	}
	if ('tf_reviewsystemlocation' === $query->get('orderby')) {
		$query->set('orderby', 'meta_value');
		$query->set('meta_key', 'tf_reviewsystemlocationname');
		$query->set('meta_type', 'text');
	}
}

function submittf_reviewsystem()
{
	$result = 1;
	$tf_reviewsystem = get_posts(
		array(
			'post_type' => 'tf_reviewsystem',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'fields' => 'ids',
		)
	);
	if (($key = array_search($_POST['post_id'], $tf_reviewsystem)) !== false) {
		unset($tf_reviewsystem[$key]);
	}

	foreach ($tf_reviewsystem as $p) {
		//get the meta you need form each post
		$tf_reviewsystem_company = get_post_meta($p, "tf_reviewsystemcompany", true);
		$tf_reviewsystem_location = get_post_meta($p, "tf_reviewsystemlocation", true);
		if ($tf_reviewsystem_company == $_POST['tf_reviewsystemcompany'] && $tf_reviewsystem_location == $_POST['tf_reviewsystemlocation']) {
			$result = 0;
		}
	}

	if ($result != 0) {
		if ($_POST) {
			//echo "<pre>"; print_r($_POST);
			$question_id = $_POST['question_ids'];
			$post_id = $_POST['post_id'];
			$Welcometext = $_POST['Welcometext'];
			$thankyoutext = $_POST['thankyoutext'];
			$font_color = $_POST['font_color'];
			$button_font_color = $_POST['button_font_color'];
			$button_bg_color = $_POST['button_bg_color'];
			$bg_image_id = $_POST['bg_image_id'];
			$header_image_id = $_POST['header_image_id'];
			$footer_image_id = $_POST['footer_image_id'];
			$fav_image_id = $_POST['fav_image_id'];
			$footer2_image_id = $_POST['footer2_image_id'];
			$tf_reviewsystemdepartment = $_POST['tf_reviewsystemdepartment'];
			$avaliablity = $_POST['avaliablity'];
			$startdate = $_POST['startdate'];
			$enddate = $_POST['enddate'];
			$tf_reviewsystemcompany = $_POST['tf_reviewsystemcompany'];
			$tf_reviewsystemlanguage = $_POST['tf_reviewsystemlanguage'];
			$tf_reviewsystemreview_for = $_POST['tf_reviewsystemreview_for'];
			$tf_reviewsystemlocation = $_POST['tf_reviewsystemlocation'];
			$url = $_POST['url'];
			$post_title = $_POST['post_title'];
			parse_str($_POST['formdata'], $str);
			$formdata = $str['tf_reviewsystems']['languages'];
			$hexurl = getName(6);

			$question_ids = get_post_meta($post_id, 'question_ids');
			// add_post_meta($post_id, 'review_feedback_status', '0');

			//insert status table

			$review_for = explode(',', $tf_reviewsystemlocation);
			array_push($review_for, $tf_reviewsystemreview_for);

			global $wpdb;
			$table_name = $wpdb->prefix . "feedback_status";

			$sqltocheck = "select * from $table_name where post_id = $post_id";
			$result = $wpdb->get_results($sqltocheck);
			if (count($result) > 0) {
				$wpdb->delete($table_name, array('post_id' => $post_id));
			}
			if (count($review_for) > 0) {
				foreach ($review_for as $value) {
					$status_to_insert = array(
						'post_id' => $post_id,
						'peer_review_id' => $value,
						'status' => '0',
						'reason_status' => '0',
						'reason_message' => '',
						'miss_review' => '0',
					);
					$wpdb->insert($table_name, $status_to_insert);
				}
			}


			//insert status table

			if ($question_ids != '') {

				update_post_meta($post_id, 'question_ids', $question_id);
			} else {
				add_post_meta($post_id, 'question_ids', $question_id);
			}
			$Welcometexts = get_post_meta($post_id, 'Welcometext');
			if ($Welcometexts != '') {
				update_post_meta($post_id, 'Welcometext', $Welcometext);
			} else {
				add_post_meta($post_id, 'Welcometext', $Welcometext);
			}

			$thankyoutexts = get_post_meta($post_id, 'thankyoutext');
			if ($thankyoutexts != '') {
				update_post_meta($post_id, 'thankyoutext', $thankyoutext);
			} else {
				add_post_meta($post_id, 'thankyoutext', $thankyoutext);
			}

			$font_colors = get_post_meta($post_id, 'font_color');
			if ($font_colors != '') {
				update_post_meta($post_id, 'font_color', $font_color);
			} else {
				add_post_meta($post_id, 'font_color', $font_color);
			}

			$button_font_colors = get_post_meta($post_id, 'button_font_color');
			if ($button_font_colors != '') {
				update_post_meta($post_id, 'button_font_color', $button_font_color);
			} else {
				add_post_meta($post_id, 'button_font_color', $button_font_color);
			}

			$button_bg_colors = get_post_meta($post_id, 'button_bg_color');
			if ($button_bg_colors != '') {
				update_post_meta($post_id, 'button_bg_color', $button_bg_color);
			} else {
				add_post_meta($post_id, 'button_bg_color', $button_bg_color);
			}

			$bg_image_ids = get_post_meta($post_id, 'bg_image_id');
			if ($bg_image_ids != '') {
				update_post_meta($post_id, 'bg_image_id', $bg_image_id);
			} else {
				add_post_meta($post_id, 'bg_image_id', $bg_image_id);
			}

			$header_image_ids = get_post_meta($post_id, 'header_image_id');
			if ($header_image_ids != '') {
				update_post_meta($post_id, 'header_image_id', $header_image_id);
			} else {
				add_post_meta($post_id, 'header_image_id', $header_image_id);
			}

			$footer_image_ids = get_post_meta($post_id, 'footer_image_id');
			if ($footer_image_ids != '') {
				update_post_meta($post_id, 'footer_image_id', $footer_image_id);
			} else {
				add_post_meta($post_id, 'footer_image_id', $footer_image_id);
			}
			$fav_image_ids = get_post_meta($post_id, 'fav_image_id');
			if ($fav_image_ids != '') {
				update_post_meta($post_id, 'fav_image_id', $fav_image_id);
			} else {
				add_post_meta($post_id, 'fav_image_id', $fav_image_id);
			}
			$footer2_image_ids = get_post_meta($post_id, 'footer2_image_id');
			if ($footer2_image_ids != '') {
				update_post_meta($post_id, 'footer2_image_id', $footer2_image_id);
			} else {
				add_post_meta($post_id, 'footer2_image_id', $footer2_image_id);
			}

			$tf_reviewsystemdepartments = get_post_meta($post_id, 'tf_reviewsystemdepartment');
			if ($tf_reviewsystemdepartments != '') {
				update_post_meta($post_id, 'tf_reviewsystemdepartment', $tf_reviewsystemdepartment);
			} else {
				add_post_meta($post_id, 'tf_reviewsystemdepartment', $tf_reviewsystemdepartment);
			}

			$avaliablitys = get_post_meta($post_id, 'avaliablity');
			if ($avaliablitys != '') {
				update_post_meta($post_id, 'avaliablity', $avaliablity);
			} else {
				add_post_meta($post_id, 'avaliablity', $avaliablity);
			}

			$startdates = get_post_meta($post_id, 'startdate');
			if ($startdates != '') {
				update_post_meta($post_id, 'startdate', $startdate);
			} else {
				add_post_meta($post_id, 'startdate', $startdate);
			}

			$enddates = get_post_meta($post_id, 'enddate');
			if ($enddates != '') {
				update_post_meta($post_id, 'enddate', $enddate);
			} else {
				add_post_meta($post_id, 'enddate', $enddate);
			}

			$tf_reviewsystemcompanys = get_post_meta($post_id, 'tf_reviewsystemcompany');
			if ($tf_reviewsystemcompanys != '') {
				update_post_meta($post_id, 'tf_reviewsystemcompany', $tf_reviewsystemcompany);
			} else {
				add_post_meta($post_id, 'tf_reviewsystemcompany', $tf_reviewsystemcompany);
			}

			$tf_reviewsystemcompanynames = get_post_meta($post_id, 'tf_reviewsystemcompanyname');
			if ($tf_reviewsystemcompanynames != '') {
				update_post_meta($post_id, 'tf_reviewsystemcompanyname', get_the_title($tf_reviewsystemcompany));
			} else {
				add_post_meta($post_id, 'tf_reviewsystemcompanyname', get_the_title($tf_reviewsystemcompany));
			}

			$tf_reviewsystemlanguages = get_post_meta($post_id, 'tf_reviewsystemlanguage');

			if ($tf_reviewsystemlanguages != '') {
				update_post_meta($post_id, 'tf_reviewsystemlanguage', $tf_reviewsystemlanguage);
			} else {
				add_post_meta($post_id, 'tf_reviewsystemlanguage', $tf_reviewsystemlanguage);
			}


			//$tf_reviewsystemreview_for = get_post_meta($post_id, 'tf_reviewsystemreview_for');

			if ($tf_reviewsystemreview_for != '') {
				update_post_meta($post_id, 'tf_reviewsystemreview_for', $tf_reviewsystemreview_for);
			} else {
				add_post_meta($post_id, 'tf_reviewsystemreview_for', $tf_reviewsystemreview_for);
			}



			$tf_reviewsystemlocations = get_post_meta($post_id, 'tf_reviewsystemlocation');
			if ($tf_reviewsystemlocations != '') {
				update_post_meta($post_id, 'tf_reviewsystemlocation', $tf_reviewsystemlocation);
			} else {
				add_post_meta($post_id, 'tf_reviewsystemlocation', $tf_reviewsystemlocation);
			}
			$tf_reviewsystemlocationnames = get_post_meta($post_id, 'tf_reviewsystemlocationname');
			if ($tf_reviewsystemlocationnames != '') {
				update_post_meta($post_id, 'tf_reviewsystemlocationname', get_the_title($tf_reviewsystemlocation));
			} else {
				add_post_meta($post_id, 'tf_reviewsystemlocationname', get_the_title($tf_reviewsystemlocation));
			}

			$urls = get_post_meta($post_id, 'url');
			if ($urls != '') {
				update_post_meta($post_id, 'url', $url);
			} else {
				add_post_meta($post_id, 'url', $url);
			}

			$formdatas = get_post_meta($post_id, 'languages');
			if ($formdatas != '') {
				update_post_meta($post_id, 'languages', $formdata);
			} else {
				add_post_meta($post_id, 'languages', $formdata);
			}
			$time = current_time('mysql');
			wp_update_post(array(
				'ID' => $post_id,
				'post_title' => $post_title,
				'post_modified' => $time,
				'post_modified_gmt' => get_gmt_from_date($time),
			));

			if ($post_id) {
				$post = get_post($post_id);
				$post_name = $post->post_name;
				if ($post_name == '') {
					wp_update_post(array(
						'ID' => $post_id,
						'post_name' => $hexurl,
						'post_modified' => $time,
						'post_modified_gmt' => get_gmt_from_date($time),
					));
				}
			}




			wp_publish_post($post_id);
			global $wpdb;
			$table_name = $wpdb->prefix . 'tf_reviewsystem';
			$values = array(
				'post_id' => $post_id,
				'status' => 1,
			);

			$responces_languages = replacedata($table_name, $values);
			if (empty($responces_languages)) {

				$wpdb->replace(
					$table_name,
					array(
						'title' => $_POST['post_title'],
						'post_id' => $post_id,
						'company_id' => $tf_reviewsystemcompany,
						'status' => 1,
					),
					array(
						'%s',
					)
				);
			} else {
				$wpdb->update(
					$table_name,
					array(
						'title' => $_POST['post_title'],
						'company_id' => $tf_reviewsystemcompany,
						'status' => 1,
					),
					array(
						'post_id' => $post_id,
					)
				);
			}

			wp_send_json_success(['redirect' => admin_url('edit.php?post_type=tf_reviewsystem')]);
		}
	} else {
		return false;
	}
	//do whatever you want with it

}
function getName($n)
{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randomString = '';

	for ($i = 0; $i < $n; $i++) {
		$index = rand(0, strlen($characters) - 1);
		$randomString .= $characters[$index];
	}

	return $randomString;
}
add_action('restrict_manage_posts', 'addtf_reviewsystemcustomfilters');
function addtf_reviewsystemcustomfilters()
{
	if (isset($_GET['post_type'])) {
		if ($_GET['post_type'] != "tf_reviewsystem") { ?>
			<style type="text/css">
				span.view {
					display: none;
				}
			</style>

		<?php }
		if ($_GET['post_type'] == "tf_reviewsystem") {

			$locations = get_posts([
				'post_type' => 'locations',
				'post_status' => 'publish',
				'numberposts' => -1,
				'orderby'   => 'title',
				'order'      => 'ASC'
			]);

			$companies = get_posts([
				'post_type' => 'companies',
				'post_status' => 'publish',
				'numberposts' => -1,
				'orderby'   => 'title',
				'order'      => 'ASC'
			]);
		?>
			<select name="tf_reviewsystemlocation">
				<option value=""><?php _e('Filter By Location', 'tf_reviewsystem'); ?></option>
				<?php
				$current_v = isset($_GET['tf_reviewsystemlocation']) ? $_GET['tf_reviewsystemlocation'] : '';
				foreach ($locations as $key => $value) {
					printf(
						'<option value="%s"%s>%s</option>',
						$value->ID,
						$value->ID == $current_v ? ' selected="selected"' : '',
						$value->post_title
					);
				}
				?>
			</select>

			<select name="tf_reviewsystemcompany">
				<option value=""><?php _e('Filter By Company', 'tf_reviewsystem'); ?></option>
				<?php
				$current_v = isset($_GET['tf_reviewsystemcompany']) ? $_GET['tf_reviewsystemcompany'] : '';
				foreach ($companies as $key => $value) {
					printf(
						'<option value="%s"%s>%s</option>',
						$value->ID,
						$value->ID == $current_v ? ' selected="selected"' : '',
						$value->post_title
					);
				}
				?>
			</select>
	<?php
		}
	}
}

add_filter('parse_query', 'tf_reviewsystemfiltercallback');
function tf_reviewsystemfiltercallback($query)
{
	global $pagenow;
	if (isset($_GET['post_type'])) {
		if ($_GET['post_type'] == "tf_reviewsystem" && is_admin() && $pagenow == 'edit.php' && isset($_GET['tf_reviewsystemcompany']) && $_GET['tf_reviewsystemcompany'] != '' && $query->is_main_query()) {
			$query->query_vars['meta_key'] = 'tf_reviewsystemcompany';
			$query->query_vars['meta_value'] = $_GET['tf_reviewsystemcompany'];
		}

		if ($_GET['post_type'] == "tf_reviewsystem" && is_admin() && $pagenow == 'edit.php' && isset($_GET['tf_reviewsystemlocation']) && $_GET['tf_reviewsystemlocation'] != '' && $query->is_main_query()) {
			$query->query_vars['meta_key'] = 'tf_reviewsystemlocation';
			$query->query_vars['meta_value'] = $_GET['tf_reviewsystemlocation'];
		}
	}
}

add_action('admin_enqueue_scripts', 'my_admin_enqueue_scripts');
function my_admin_enqueue_scripts()
{
	if ('tf_reviewsystem' == get_post_type()) {
		wp_dequeue_script('autosave');
	}
}

add_filter('single_template', 'tf_reviewsystem_template');

function getSurveylanguages()
{
	global $post;
	$postId = $post->ID;
	$languages = get_post_meta($postId, 'tf_reviewsystemlanguage', true);
	$languages = explode(',', $languages);
	$languageshtml = "";
	$data = getLanguages();
	foreach ($data as $key => $value) {
		$text = "English";
		if ($key == 'ar') {
			$text = "عربى";
		}
		if ($key == 'hi') {
			$text = "हिंदी";
		}
		if ($key == 'ur') {
			$text = "اردو";
		}
		if ($key == 'ta') {
			$text = "தமிழ்";
		}
		if ($key == 'ml') {
			$text = "മലയാളം";
		}
		if ($key == 'bn') {
			$text = "বাংলা";
		}

		if (in_array($key, $languages)) {
			$lang = $data[$key];
			$languageshtml .= '<label class="lbl_1" for="' . $key . '">';
			$languageshtml .= '<input type="radio" name="lang" class="language btn_color_bg" id="' . $key . '" value="' . $key . '" />';
			$languageshtml .= '<span class="lan_text btn_color">' . $text . '</span>';
			$languageshtml .= '</label>';
		}
	}

	$html = '<label class=" lbl_1 " for="English">';
	$html .= '<input type="radio" name="lang" class="language btn_color_bg" id="English" value="en" checked/>';
	$html .= '<span class="lan_text btn_color">English</span>';
	$html .= '</label>';
	$html .= $languageshtml;

	echo $html;
}

function get_tf_reviewsystem_header_logo()
{
	global $post;
	if ($post->post_type == 'tf_reviewsystem') {
		$tf_reviewsystem_id = $post->ID;
		$header_image_id = get_post_meta($tf_reviewsystem_id, 'header_image_id', true);
		$header_image_info = wp_get_attachment_image_src($header_image_id, 'full')[0] ?? null;
		if (is_array($header_image_info)) {
			return $header_image_info[0];
		}
	}
}

function get_tf_reviewsystem_footer_logo()
{
	global $post;
	if ($post->post_type == 'tf_reviewsystem') {
		$tf_reviewsystem_id = $post->ID;
		$footer_image_id = get_post_meta($tf_reviewsystem_id, 'footer_image_id', true);
		$footer_image_info =  wp_get_attachment_image_src($footer_image_id, 'full')[0] ?? null;
		if (is_array($footer_image_info)) {
			return $footer_image_info[0];
		} else {
			return $footer_image_info;
		}
	}
}

function get_tf_reviewsystem_footer2_logo()
{
	global $post;
	if ($post->post_type == 'tf_reviewsystem') {
		$tf_reviewsystem_id = $post->ID;
		$footer2_image_id = get_post_meta($tf_reviewsystem_id, 'footer2_image_id', true);
		$footer2_image_id_info = wp_get_attachment_image_src($footer2_image_id, 'full')[0] ?? null;
		if (is_array($footer2_image_id_info)) {
			return $footer2_image_id_info[0];
		} else {
			return $footer2_image_id_info;
		}
	}
}
function get_tf_reviewsystem_fav_logo()
{
	global $post;
	if ($post->post_type == 'tf_reviewsystem') {
		$tf_reviewsystem_id = $post->ID;
		$fav_image_id = get_post_meta($tf_reviewsystem_id, 'fav_image_id', true);
		$image_info = wp_get_attachment_image_src($fav_image_id, 'full');
		if ($image_info && is_array($image_info)) {
			return $image_info[0];
		} else {
			return ''; 
		}
	}
}

function get_tf_reviewsystem_welcome_text()
{
	global $post;
	if ($post->post_type == 'tf_reviewsystem') {
		$tf_reviewsystem_id = $post->ID;
		$Welcometext = get_post_meta($tf_reviewsystem_id, 'Welcometext', true);
		return $Welcometext;
	}
}

function get_tf_reviewsystem_thankyou_text()
{
	global $post;
	if ($post->post_type == 'tf_reviewsystem') {
		$tf_reviewsystem_id = $post->ID;
		$thankyoutext = get_post_meta($tf_reviewsystem_id, 'thankyoutext', true);
		return $thankyoutext;
	}
}

function ValidateSurveyDate($avaliablity, $startdate, $enddate)
{
	$Datevalidation = false;
	$today = strtotime(date('Y-m-d'));

	if ($avaliablity == 'always') {
		$Datevalidation = true;
	} else if ($avaliablity == 'specific') {
		$startdate = strtotime(date('Y-m-d', strtotime($startdate)));
		$enddate = strtotime(date('Y-m-d', strtotime($enddate)));

		if ($startdate <= $today && $enddate >= $today) {
			$Datevalidation = true;
		}
	}

	return $Datevalidation;
}

function CustomOption($value, $group, $question_id, $optionId)
{
	return ' <label class="lbl color option"><input type="radio" name="' . $group . '"  value="' . $value . '" class="choice op1 removesv"><span class="lb_text translate" data-id="' . $question_id . '" data-key="options" data-option="' . $optionId . '">' . $value . '</span></label>';
}

function ReverseAgreeDisagree($group)
{
	return '<label class="lbl color option"><input type="radio" name="' . $group . '" value="1" class="choice op1 oop1"><div class="st_agree"></div></label><label class="lbl color"><input type="radio" name="' . $group . '" value="2" class="choice op1 oop1"><div class="agree"></div></label><label class="lbl color"><input type="radio" name="' . $group . '" value="3" class="choice op1 oop1"><div class="dis_agree"></div></label><label class="lbl color"><input type="radio" name="' . $group . '" value="4" class="choice op1 oop1"><div class="st_dis_agree"></div></label>';
}

function AgreeDisagree($group)
{
	return '<label class="lbl color option"><input type="radio" name="' . $group . '" value="4" class="choice op1 oop1"><div class="st_agree"></div></label><label class="lbl color"><input type="radio" name="' . $group . '" value="3" class="choice op1 oop1"><div class="agree"></div></label><label class="lbl color"><input type="radio" name="' . $group . '" value="2" class="choice op1 oop1"><div class="dis_agree"></div></label><label class="lbl color"><input type="radio" name="' . $group . '" value="1" class="choice op1 oop1"><div class="st_dis_agree"></div></label>';
}
function OpenEnded($group)
{
	$text_length = get_post_meta($group, 'text_length', true);
	return '<label class="lbl color option"><input type="number" class="' . $group . ' OpenEnded" name="' . $group . '" maxlength="' . $text_length . '" value="0" min="0" max="100">
    <span class="remainingC" style="float: right;"></span></label><script src="' . plugin_dir_url(__FILE__) . 'assets/js/jqueryajax191min.js"></script>
    <script type="text/javascript">
	$(document).ready(function() {
		$(".OpenEnded").on("change", function() {
			var value = parseFloat($(this).val());
			if (isNaN(value) || value < 0 || value > 100) {
				alert("Please enter a number between 0 and 100.");
				$(this).val(0); // Reset to a valid value, you can choose a different default value if needed.
			}
		});
	});
    </script>';
}
function Rating($group)
{
	return '<label class="lbl color"><input type="radio" name="' . $group . '" value="4" class="choice op1 oop1"><div class="nst_agree"></div></label>
	<label class="lbl color"><input type="radio" name="' . $group . '" value="3.5" class="choice op1 oop1"><div class="nagree"></div></label>
	<label class="lbl color"><input type="radio" name="' . $group . '" value="3" class="choice op1 oop1"><div class="nsw_agree"></div></label>
	<label class="lbl color"><input type="radio" name="' . $group . '" value="2.5" class="choice op1 oop1"><div class="nnand"></div></label>
	<label class="lbl color"><input type="radio" name="' . $group . '" value="2" class="choice op1 oop1"><div class="nsw_disagree"></div></label>
	<label class="lbl color"><input type="radio" name="' . $group . '" value="1.5" class="choice op1 oop1"><div class="ndis_agree"></div></label>
	<label class="lbl color option"><input type="radio" name="' . $group . '" value="1" class="choice op1 oop1"><div class="nst_disagree"></div></label>';
}

function YesNo($group)
{
	return '<div class="yes_no_div"><label class="lbl color option"><input type="radio" name="' . $group . '" value="4" class="choice op1 oop1"><div class="th_up"><img src="' . plugins_url('assets/images/thums_up.png', __FILE__) . '"></div></label><label class="lbl color"><input type="radio" name="' . $group . '" value="1" class="choice op1 oop1"><div class="th_down"><img src="' . plugins_url('assets/images/thums_down.png', __FILE__) . '"></div></label></div>';
}
function year_name($post_id, $year_id)
{
	return get_the_title(get_post_meta($post_id, 'tf_reviewsystemcompany', true));
}

function employee_name($post_id, $review_for)
{
	$user_id = get_post_meta($post_id, 'tf_reviewsystemreview_for', true);
	$user = get_user_by('id', $user_id);
	return $user->display_name;
}

function department_name($post_id, $department_id)
{
	//echo get_post_meta($post_id, 'tf_reviewsystemdepartment', true); die;
	return get_the_title(get_post_meta($post_id, 'tf_reviewsystemdepartment', true));
}

function getSurveyQuestions()
{
	global $post;
	if ($post->post_type == 'tf_reviewsystem') {
		$tf_reviewsystem_id = $post->ID;
		$question_data = get_post_meta($tf_reviewsystem_id, 'question_ids', true);
		$tf_reviewsystemlanguage = get_post_meta($tf_reviewsystem_id, 'languages', true);
		if (is_array($tf_reviewsystemlanguage)) {
			$tf_reviewsystemlanguage['en'] = array(
				'Welcometext' => get_post_meta($tf_reviewsystem_id, 'Welcometext', true),
				'thankyoutext' => get_post_meta($tf_reviewsystem_id, 'thankyoutext', true),
				'changelanguage' => 'Change Language',
				'starttf_reviewsystem' => 'Start tf_reviewsystem',
				'previous' => 'Previous',
				'next' => 'Next',
			);
		}

		$questions_data = explode(',', $question_data);
		$languages = array();
		$translation = array();
		$html = '';
		$total = count($questions_data);
		foreach ($questions_data as $question_no => $question_id) {
			$optionshtml = '';
			$question = get_the_title($question_id);
			$questions_data = get_post_meta($question_id, 'questions', true);
			$department_id = get_post_meta($question_id, 'department', true);
			$img_id = get_post_meta($question_id, 'question_image', true);
			$src = wp_get_attachment_image_src($img_id, 'full')[0];

			$currentimgid = $question_id;

			if (!isset($src) && empty($src)) {
				$img_id = get_post_meta($department_id, 'category_image', true);
				$src = wp_get_attachment_image_src($img_id, 'full')[0];
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

			$html .= '<fieldset>';

			if (isset($src) && !empty($src)) {
				$html .= '<img src="' . $src . '" id="Image' . $currentimgid . '" style="display: none;">';
			}

			$html .= '<input type="hidden" class="currentpage" value="' . $currentimgid . '">';
			$qk = (int) $question_no + 1;
			$html .= '<h2 class="title color">Q' . $qk . '. <span class="translate" data-id="' . $question_id . '" data-key="question">' . $question . '</span> <a href="#" ><span data-id="' . $question_id . '" class="gts glyphicon glyphicon-volume-up"> </span></a></h2><div class="clearfix"></div>';
			$html .= "<link rel='stylesheet' href='" . plugin_dir_url(__FILE__) . "assets/css/bootstrap335.css'>
			<audio id='questionAudio'><source id='questionAudioData' type='audio/mpeg' /></audio>
			<audio id='optionAudio'><source id='optionAudioData' type='audio/mpeg' /></audio>";
			$html .= $optionshtml;
			$html .= '<input type="button" data-id="previous" name="previous" class="previous action-button prv btn_color_bg btn_color translate" value="Previous" />';
			$html .= '<input type="button" data-id="next" name="nextquestion" class="next nextquestion action-button prv btn_color_bg btn_color translate" value="Next" />';
			if ($total == $question_no + 1) {
				$html .= '<input type="hidden" id = "finishbtn" />';
			}

			$html .= '</fieldset>';

			if (isset($questions_data['languages']) && is_array($questions_data['languages'])) {
				foreach ($questions_data['languages'] as $key => $value) {
					$options = isset($value['option']) && !empty($value['option']) ? $value['option'] : "";

					$enoptions = isset($questions_data['option']) && !empty($questions_data['option']) ? $questions_data['option'] : "";

					$translation['en'][$question_id] = array(
						'question' => $question,
						'options' => $enoptions,
					);
					switch ($key) {
						case 'ar':
							$translation['ar'][$question_id] = array(
								'question' => $value['question'],
								'options' => $options,
							);
							break;
						case 'hi':
							$translation['hi'][$question_id] = array(
								'question' => $value['question'],
								'options' => $options,
							);
							break;
						case 'ur':
							$translation['ur'][$question_id] = array(
								'question' => $value['question'],
								'options' => $options,
							);
							break;
						case 'ta':
							$translation['ta'][$question_id] = array(
								'question' => $value['question'],
								'options' => $options,
							);
							break;
						case 'ml':
							$translation['ml'][$question_id] = array(
								'question' => $value['question'],
								'options' => $options,
							);
							break;
						case 'bn':
							$translation['bn'][$question_id] = array(
								'question' => $value['question'],
								'options' => $options,
							);
							break;
						default:
							$translation['en'][$question_id] = array(
								'question' => $value['question'],
								'options' => $options,
							);
							break;
					}
				}
			}
		}
		$html .= '<input type="hidden" id="current_language" value="">';
		$html .= '<input type="hidden" id="translation_data" value="' . base64_encode(json_encode($translation)) . '">';
		$html .= '<input type="hidden" id="tf_reviewsystem_translation_data" value="' . base64_encode(json_encode($tf_reviewsystemlanguage)) . '">';
	}

	return $html;
}

function textToSpeech()
{
	putenv('GOOGLE_APPLICATION_CREDENTIALS=' . plugin_dir_path(__FILE__) . 'project-f69475a33b51.json');
	$client = new TextToSpeechClient();
	$ssml = SsmlBuilder::factory();
	if (isset($_POST['options'])) {
		foreach ($_POST['options'] as $key => $value) {
			$ssml->paragraph(html_entity_decode($value))
				->brk()
				->brk()
				->brk()
				->brk()
				->text('');
		}
		$optionstext = (new SynthesisInput())
			->setSsml(strval($ssml));
	}
	// sets text to be synthesised
	$question = (new SynthesisInput())
		->setText(html_entity_decode($_POST['question']));

	$current_language = $_POST['current_language'];

	$language = 'en-US';
	if ($current_language == 'ar') {
		$language = 'ar-OM';
	}
	if ($current_language == 'hi') {
		$language = "hi-IN";
	}
	if ($current_language == 'ur') {
		$language = "ur-IN";
	}
	if ($current_language == 'ta') {
		$language = "ta-IN";
	}
	if ($current_language == 'ml') {
		$language = "ml-IN";
	}
	if ($current_language == 'bn') {
		$language = "bn-BD";
	}
	// build the voice request, select the language code ("en-US") and the ssml
	// voice gender
	$voice = (new VoiceSelectionParams())
		->setLanguageCode($language)
		->setSsmlGender(SsmlVoiceGender::FEMALE);

	// Effects profile
	$effectsProfileId = "telephony-class-application";

	// select the type of audio file you want returned
	$audioConfig = (new AudioConfig())
		->setAudioEncoding(AudioEncoding::MP3)
		//->setSpeakingRate(0.75)
		->setEffectsProfileId(array($effectsProfileId));

	// perform text-to-speech request on the text input with selected voice
	// parameters and audio file type
	$response = $client->synthesizeSpeech($question, $voice, $audioConfig);
	$audioContent = $response->getAudioContent();
	$audioContent_response = '';
	if (isset($_POST['options'])) {
		$responseOptions = $client->synthesizeSpeech($optionstext, $voice, $audioConfig);
		$audioContentOptions = $responseOptions->getAudioContent();
		$audioContent_response = base64_encode($audioContentOptions);
	}

	wp_send_json_success(['audiodata' => base64_encode($audioContent), 'audioContentOptions' => $audioContent_response]);
}
function clean($string)
{
	$string = str_replace(' ', ' ', $string);
	$string = preg_replace('/[^A-Za-z0-9\-]/', ' ', $string);

	return preg_replace('/-+/', '-', $string);
}
function tf_reviewsystem_template($single)
{
	global $post;
	if ($post->post_type == 'tf_reviewsystem') {
		$tf_reviewsystem_id = $post->ID;
		$avaliablity = get_post_meta($tf_reviewsystem_id, 'avaliablity', true);
		$startdate = get_post_meta($tf_reviewsystem_id, 'startdate', true);
		$enddate = get_post_meta($tf_reviewsystem_id, 'enddate', true);

		// if (ValidateSurveyDate($avaliablity, $startdate, $enddate) == true) {
		// 	if (file_exists(plugin_dir_path(__FILE__) . '/survey.php')) {
		// 		return plugin_dir_path(__FILE__) . '/survey.php';
		// 	}
		// } else {
		// 	if (file_exists(plugin_dir_path(__FILE__) . '/403.php')) {
		// 		return plugin_dir_path(__FILE__) . '/403.php';
		// 	}
		// 	// 403 Redirect...
		// }
		if (file_exists(plugin_dir_path(__FILE__) . '/survey.php')) {
			return plugin_dir_path(__FILE__) . '/survey.php';
		}
	} elseif ($post->post_type == 'page') {
		return plugin_dir_path(__FILE__) . '/page.php';
	}

	return $single;
}

function getSurveyQuestion()
{
	$post_id = $_POST['post_id'];
	$postids = array();
	$question_data = get_post_meta($post_id, 'question_ids', true);
	$questions_data = explode(',', $question_data);
	// print_r($questions_data);die;
	$questionshtml = "";
	foreach ($questions_data as $questions) {
		$que = get_post($questions);
		$title = $que->post_title;
		$postids[] = $que->ID;
		$questions_data = get_post_meta($questions, 'questions', true);
		$options = isset(unserialize($questions_data)['option']) ? unserialize($questions_data)['option'] : "";

		if (!empty($questions_data)) {
			$questions_array = unserialize($questions_data);
			if (!empty($questions_array)) {
				array_values($questions_array);
				array_unshift($questions_array, "");
				unset($questions_array[0]);
			}
		}
		$questions_options = array();

		$type = 'Options';
		if ($questions_array['type'] == 'checkbox') {
			$type = 'SA(4) / A (3) / D (2) / SD(1)';
		} else if ($questions_array['type'] == "yes/no") {
			$type = "Yes(4) / No(1)";
		} else if ($questions_array['type'] == "rating") {
			$type = "SD(1)/ D(1.5)/ SWD(2) / NAND(2.5) /SWA(3) / A(3.5) / SA(4)";
		} else if ($questions_array['type'] == "openended") {
			$type = "Open Ended";
		} else {
			$questions_options = $options;
		}
		if ($que->ID != '') {
			$questionshtml .= SurveyQuestionHTML($que->ID, $title, $type, $questions_options);
		}
	}

	wp_send_json_success(['data' => $questionshtml, 'ids' => base64_encode(json_encode($postids))]);
}

function create_tf_reviewsystem_posttype()
{

	add_action("wp_ajax_getquestions", "getquestions");
	add_action("wp_ajax_nopriv_getquestions", "getquestions");

	add_action("wp_ajax_getSurveyQuestion", "getSurveyQuestion");
	add_action("wp_ajax_submittf_reviewsystem", "submittf_reviewsystem");

	add_action("wp_ajax_tf_reviewsystemresponsestore", "tf_reviewsystemresponsestore");
	add_action("wp_ajax_nopriv_tf_reviewsystemresponsestore", "tf_reviewsystemresponsestore");

	add_action("wp_ajax_expired_review", "expired_review");
	add_action("wp_ajax_nopriv_expired_review", "expired_review");

	add_action("wp_ajax_getlocations", "getlocations");
	add_action("wp_ajax_textToSpeech", "textToSpeech");
	add_action("wp_ajax_nopriv_textToSpeech", "textToSpeech");
	add_action("wp_ajax_nopriv_getlocations", "getlocations");

	add_action("wp_ajax_getpeerlist", "getpeerlist");
	add_action("wp_ajax_nopriv_getpeerlist", "getpeerlist");

	add_action("wp_ajax_dismis_error_notice", "dismis_error_notice");
	add_action("wp_ajax_nopriv_dismis_error_notice", "dismis_error_notice");

	add_action("wp_ajax_get_emps", "get_emps");
	add_action("wp_ajax_nopriv_get_emps", "get_emps");

	add_action("wp_ajax_send_new_link_mail", "send_new_link_mail");
	add_action("wp_ajax_nopriv_send_new_link_mail", "send_new_link_mail");

	add_action("wp_ajax_sendEmailtousers", "sendEmailtousers");


	register_post_type(
		'tf_reviewsystem',
		array(
			'labels' => array(
				'name' => __('Review'),
				'singular_name' => __('Review'),
				'all_items' => 'All Reviews',
				'menu_name' => 'Review',
				'singular_name' => 'Review',
				'edit_item' => '',
				'add_new' => 'New Review',
				'add_new_item' => 'New Review',
				'view_item' => 'View Review',
				'items_archive' => 'Reviews Archive',
				'search_items' => 'Search Review',
				'not_found' => 'No Review found.',
				'not_found_in_trash' => 'No Review found in trash.',
			),
			'public' => true,
			'has_archive' => false,
			'supports' => array('title'),
			'rewrite' => array('slug' => 'u'),

		)
	);
}
function review_system_menu_page()
{
	add_menu_page(
		'tf_reviewsystem',                   // Page title
		'tf_reviewsystem',                   // Menu title
		'manage_options',           // Capability required to access the page
		'review-menu',          // Menu slug
		'review_system_menu_page_callback', // Callback function to display the page content
		'dashicons-clipboard'        // Icon for the menu (you can choose from Dashicons)
	);
}

// Hook to add the menu page to the admin menu
//add_action('admin_menu', 'review_system_menu_page');

// Callback function to display the page content
function review_system_menu_page_callback()
{
	?>
	<div class="wrap">
		<h1>tf_reviewsystem</h1>
		<p>This is the content of the tf_reviewsystem menu page.</p>
	</div>
<?php
}


function wpse_add_custom_meta_box_2()
{
	add_meta_box(
		'tf_reviewsystemgenrator', // $id
		'Review Information', // $title
		'tf_reviewsystemgenrator', // $callback
		'tf_reviewsystem', // $page
		'normal', // $context
		'high' // $priority
	);
}

add_action('add_meta_boxes', 'wpse_add_custom_meta_box_2');
function tf_reviewsystemresponsestore()
{
	parse_str($_POST['form_data'], $str);
	unset($str['lang']);
	global $post;
	global $wpdb;
	$tf_reviewsystem_id = $_POST['tf_reviewsystem_id'];

	$tf_reviewsystem = get_post_meta($tf_reviewsystem_id);
	$company_id = $tf_reviewsystem['tf_reviewsystemcompany'][0];
	$location_id = $tf_reviewsystem['tf_reviewsystemlocation'][0];
	$category_id = $tf_reviewsystem['tf_reviewsystemdepartment'][0];
	$table_name = $wpdb->prefix . 'tf_reviewsystem_responces';

	$success = $wpdb->insert($table_name, array(
		"tf_reviewsystem_id" => $tf_reviewsystem_id,
		"company_id" => $company_id,
		"location_id" => $location_id,
		"review_for" => $_POST['review_for'],
		"review_by" => $_POST['review_by_id'],
		"comments" => $_POST['summary'],
		"is_offline" => '',
		'datetime' => date('y-m-d h:m:s'),
		"is_submitted" => 1,
	));
	$tf_reviewsystem_responce_id = $wpdb->insert_id;
	$response_table_name = $wpdb->prefix . 'tf_reviewsystem_responce_details';
	foreach ($str as $key => $value) {

		$val = (int) $value;
		if ($val != 0 && strpos($value, '-') == false && $val <= 4) {
			$field = "score";
		} else {
			$field = "responce";
		}
		$department = get_post_meta($key, 'department', true);
		if ($value != 0) {
			$success = $wpdb->insert($response_table_name, array(
				"tf_reviewsystem_responce_id" => $tf_reviewsystem_responce_id,
				"question_id" => $key,
				$field => $value,
				"category_id" => $department,
			));
		}
	}
	// update_post_meta($tf_reviewsystem_id, 'review_feedback_status', '1');
	//update review feedback status
	$table_name = $wpdb->prefix . 'feedback_status';
	$feedback_data_to_update = array(
		'status' => '1',
	);
	$where_clause = array(
		'post_id' => $tf_reviewsystem_id,
		'peer_review_id' => $_POST['review_by_id'],
	);
	$wpdb->update($table_name, $feedback_data_to_update, $where_clause);
	//update review feedback status
	//print_r($success);
	wp_send_json_success();
}

function expired_review()
{
	global $post;
	global $wpdb;
	$reson_msg = $_POST['reason'];
	$tf_reviewsystem_res_id = $_POST['post_id'];
	$emp_id = $_POST['emp_id'];

	$hr_name = $_POST['hr_name'];
	$hr_email = $_POST['hr_email'];
	$emp_name = $_POST['emp_name'];


	$table_name = $wpdb->prefix . 'feedback_status';
	$feedback_data_to_update = array(
		'reason_message' => $reson_msg,
		'reason_status' => '1',
	);
	$where_clause = array(
		'post_id' => $tf_reviewsystem_res_id,
		'peer_review_id' => $emp_id,
	);
	$wpdb->update($table_name, $feedback_data_to_update, $where_clause);

	$url = "" . admin_url() . "/edit.php?post_type=tf_reviewsystem";
	$subject = 'Reason For ' . $emp_name . '';
	$mail_message = '<html lang="en">
                <body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; text-align: center;">
                
                    <div style="background-color: #ffffff; max-width: 600px; margin: 0 auto; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);">
                        <h1 style="color: #333;">Hello ' . $hr_name . '!</h1>
                        <p style="color: #555;">Here is the reason like why ' . $emp_name . ' had missed the review end date.</p>
                        <p style="color: #555;">Reason from ' . $emp_name . ':</p>
                        <p style="color: #555;">' . $reson_msg . '</p>
                        <p style="color: #555;"><a href=" ' . $url . '" >Click here for Feedback</a></p>
                    </div>
                
                </body>
                </html>';
	$email = $hr_email;

	date_default_timezone_set('Etc/UTC');

	$mail = new PHPMailer\PHPMailer\PHPMailer();
	$mail->isSMTP();
	$mail->SMTPSecure = "tls";
	$mail->Debugoutput = 'html';
	$mail->CharSet = "UTF-8";
	$mail->Host = "" . get_option('smtp_host') . "";
	$mail->Port = get_option('smtp_port');
	$mail->SMTPAuth = true;
	$mail->Username = "" . get_option('smtp_username') . "";
	$mail->Password = "" . get_option('smtp_password') . "";
	$mail->setFrom("" . get_option('smtp_username') . "");
	$mail->IsHTML(true);
	$mail->addAddress($email);
	$mail->Subject = $subject;
	$mail->Body = $mail_message;
	$mail->AltBody = $mail_message;
	$mail->send();
}

function tf_reviewsystemgenrator()
{
	global $post;
	$title = $post->post_title;
	$postId = $post->ID;
	$Welcometext = get_post_meta($postId, 'Welcometext', true);
	$thankyoutext = get_post_meta($postId, 'thankyoutext', true);
	$font_color = get_post_meta($postId, 'font_color', true);
	$button_font_color = get_post_meta($postId, 'button_font_color', true);
	$button_bg_color = get_post_meta($postId, 'button_bg_color', true);
	$bg_image_id = get_post_meta($postId, 'bg_image_id', true);
	$header_image_id = get_post_meta($postId, 'header_image_id', true);
	$footer_image_id = get_post_meta($postId, 'footer_image_id', true);
	$fav_image_id = get_post_meta($postId, 'fav_image_id', true);
	$footer2_image_id = get_post_meta($postId, 'footer2_image_id', true);
	$avaliablity = get_post_meta($postId, 'avaliablity', true);
	$startdate = get_post_meta($postId, 'startdate', true);
	$enddate = get_post_meta($postId, 'enddate', true);
	$tf_reviewsystemcompany = get_post_meta($postId, 'tf_reviewsystemcompany', true);
	$languages = get_post_meta($postId, 'tf_reviewsystemlanguage', true);
	$tf_reviewsystemreview_for = get_post_meta($postId, 'tf_reviewsystemreview_for', true);
	$tf_reviewsystemlocation = get_post_meta($postId, 'tf_reviewsystemlocation', true);
	$tf_reviewsystemdepartment = get_post_meta($postId, 'tf_reviewsystemdepartment', true);
	$url = get_post_meta($postId, 'url', true);
	$location = get_post_meta($tf_reviewsystemcompany, 'locations', true);
	$loc_data = array();
	//print_r($tf_reviewsystemreview_for);
	foreach ($location as $value) {
		$alllocations = get_the_title($value);
		array_push($loc_data, array('value' => $value, 'name' => $alllocations));
	}

	$questions_array = array();
	$questions_array = get_post_meta($postId, 'languages', true);

	$departments = get_posts([
		'post_type' => 'departments',
		'post_status' => 'publish',
		'numberposts' => -1,
		'orderby'   => 'title',
		'order'      => 'ASC'
	]);
	$locations = get_posts([

		'post_type' => 'locations',

		'post_status' => 'publish',

		'numberposts' => -1,
		'orderby'   => 'title',
		'order'      => 'ASC'
	]);
	$companies = get_posts([
		'post_type' => 'companies',
		'post_status' => 'publish',
		'numberposts' => -1,
		'orderby'   => 'title',
		'order'      => 'ASC'
	]);
	$yearlist = get_posts([
		'post_type' => 'tf-yearlist',
		'post_status' => 'publish',
		'numberposts' => -1,
		'orderby'   => 'title',
		'order'      => 'ASC'
	]);

	$employelist = get_users(array('fields' => array('ID')));

	$args = array(
		'role'    => 'Subscriber',
		'orderby' => 'last_name',
		'order'   => 'ASC'
	);
	$employeeData = get_users($args);

	// function get_employee(){
	// 	$employelist = get_users(array('fields' => array('ID')));
	// 	$employeeData = get_users( $args );
	// 	$data = array();
	// 	foreach ($employeeData as $key => $value) {
	// 		$data[$key]['value'] = $value->ID;
	// 		$data[$key]['name'] = $value->display_name;
	// 	}
	// }
	// add_action('wp_ajax_get_employee', 'get_employee');
	// add_action('wp_ajax_nopriv_get_employee', 'get_employee');

	// $employelist = get_posts([
	// 	'post_type' => 'tf-yearlist',
	// 	'post_status' => 'publish',
	// 	'numberposts' => -1,
	// 	'orderby'   => 'title',
	// 	'order'      => 'ASC'
	// ]);

	$tf_reviewsystem_questions = array('Item 1', 'Item 2');
?>
	<div class="ui container">
		<div class="ui loading form">
			<div class="ui fluid">
				<div class="ui primary test right floated button" id="generatetf_reviewsystem">
					Open Review Builder
				</div>

				<div style="margin-bottom: 12px;" id="tf_reviewsystemurl">
					<?php if (isset($url)) { ?>
						<label>URL</label>
						<div class="ui labeled">
							<div class="ui label">
								<a href="<?= get_permalink($postId); ?>" target="_blank"><?= site_url('u/') . $url; ?></a>
							</div>
						</div>
					<?php } ?>
				</div>
				<div class="field">
					<label>Welcome Text</label>
					<input type="text" name="Welcometext" id="Welcometext" value="<?= $Welcometext ?>">
				</div>
				<div class="field">
					<label>Thank you Text</label>
					<input type="text" name="thankyoutext" id="thankyoutext" value="<?= $thankyoutext ?>">
				</div>
				<!-- <h4 class="ui top attached block header">Languages</h4> -->

				<div class="field">
					<label>Font Color</label>
					<div class="form-field term-group">
						<input type="text" id="font_color" name="font_color" class="custom_media_url" value="<?= $font_color ?>">
					</div>
					<label>Button Font Color</label>
					<div class="form-field term-group">
						<input type="text" id="button_font_color" name="button_font_color" class="custom_media_url" value="<?= $button_font_color ?>">
					</div>
					<label>Button Background Color</label>
					<div class="form-field term-group">
						<input type="text" id="button_bg_color" name="button_bg_color" class="custom_media_url" value="<?= $button_bg_color ?>">
					</div>
				</div>
				<div class="field">
					<label>Background Image(Size: 1280 * 890)</label>
					<div class="form-field term-group">
						<input type="hidden" id="bg-image-id" name="bg-logo" class="custom_media_url" value="<?= $bg_image_id ?>">
						<div id="bg-image-wrapper">
							<?php if (isset($bg_image_id) && !empty($bg_image_id)) { ?>
								<img src="<?= wp_get_attachment_image_src($bg_image_id)[0] ?>" style="max-width: 350px;">
							<?php } ?>
						</div>
						<p class="mp">
							<?php if (isset($bg_image_id)) { ?>
								<input type="button" class="ui green button button-secondary ct_tax_media_button" id="bg_logo_add" name="ct_tax_media_button" value="Add Image" <?php echo ($bg_image_id == '') ? '' : 'style="display: none;"' ?>>
							<?php } else { ?>
								<input type="button" class="ui green button button-secondary ct_tax_media_button" id="bg_logo_add" name="ct_tax_media_button" value="Add Image">
							<?php } ?>
							<?php if (isset($bg_image_id) && !empty($bg_image_id)) { ?>
								<input type="button" class="ui red button button-secondary ct_tax_media_remove" id="bg_logo_remove" name="ct_tax_media_remove" value="Remove Image">
							<?php } else { ?>
								<input type="button" class="ui red button button-secondary ct_tax_media_remove" id="bg_logo_remove" name="ct_tax_media_remove" value="Remove Image" style="display: none;">
							<?php } ?>
						</p>
					</div>
				</div>
				<div class="field">
					<label>Header Logo (Size: 200 * 200)</label>
					<div class="form-field term-group">
						<input type="hidden" id="header-image-id" name="header-logo" class="custom_media_url" value="<?= $header_image_id ?>">
						<div id="header-image-wrapper">
							<?php if (isset($header_image_id) && !empty($header_image_id)) { ?>
								<img src="<?= wp_get_attachment_image_src($header_image_id)[0] ?>" style="max-width: 350px;">
							<?php } ?>
						</div>
						<p class="mp">
							<?php if (isset($header_image_id)) { ?>
								<input type="button" class="ui green button button-secondary ct_tax_media_button" id="header_logo_add" name="ct_tax_media_button" value="Add Image" <?php echo ($header_image_id == '') ? '' : 'style="display: none;"' ?>>
							<?php } else { ?>
								<input type="button" class="ui green button button-secondary ct_tax_media_button" id="header_logo_add" name="ct_tax_media_button" value="Add Image">
							<?php } ?>
							<?php if (isset($header_image_id) && !empty($header_image_id)) { ?>
								<input type="button" class="ui red button button-secondary ct_tax_media_remove" id="header_logo_remove" name="ct_tax_media_remove" value="Remove Image">
							<?php } else { ?>
								<input type="button" class="ui red button button-secondary ct_tax_media_remove" id="header_logo_remove" name="ct_tax_media_remove" value="Remove Image" style="display: none;">
							<?php } ?>
						</p>
					</div>
				</div>
				<div class="field">
					<label>Footer Logo (Size: 200 * 200)</label>
					<div class="form-field term-group">
						<input type="hidden" id="footer-image-id" name="footer-logo" class="custom_media_url" value="<?= $footer_image_id ?>">
						<div id="footer-image-wrapper">
							<?php if (isset($footer_image_id) && !empty($footer_image_id)) { ?>
								<img src="<?= wp_get_attachment_image_src($footer_image_id)[0] ?>" style="max-width: 350px;">
							<?php } ?>
						</div>
						<p class="mp">
							<?php if (isset($footer_image_id)) { ?>
								<input type="button" class="ui green button button-secondary ct_tax_media_button" id="footer_logo_add" name="ct_tax_media_button" value="Add Image" <?php echo ($footer_image_id == '') ? '' : 'style="display: none;"' ?>>
							<?php } else { ?>
								<input type="button" class="ui green button button-secondary ct_tax_media_button" id="footer_logo_add" name="ct_tax_media_button" value="Add Image">
							<?php } ?>
							<?php if (isset($footer_image_id) && !empty($footer_image_id)) { ?>
								<input type="button" class="ui red button button-secondary ct_tax_media_remove" id="footer_logo_remove" name="ct_tax_media_remove" value="Remove Image">
							<?php } else { ?>
								<input type="button" class="ui red button button-secondary ct_tax_media_remove" id="footer_logo_remove" name="ct_tax_media_remove" value="Remove Image" style="display: none;">
							<?php } ?>
						</p>
					</div>
				</div>
				<div class="field">
					<label>Footer Logo 2(Size: 200 * 200)</label>
					<div class="form-field term-group">
						<input type="hidden" id="footer2-image-id" name="footer2-logo" class="custom_media_url" value="<?= $footer2_image_id ?>">
						<div id="footer2-image-wrapper">
							<?php if (isset($footer2_image_id) && !empty($footer2_image_id)) { ?>
								<img src="<?= wp_get_attachment_image_src($footer2_image_id)[0] ?>" style="max-width: 350px;">
							<?php } ?>
						</div>
						<p class="mp">
							<?php if (isset($footer2_image_id)) { ?>
								<input type="button" class="ui green button button-secondary ct_tax_media_button" id="footer2_logo_add" name="ct_tax_media_button" value="Add Image" <?php echo ($footer2_image_id == '') ? '' : 'style="display: none;"' ?>>
							<?php } else { ?>
								<input type="button" class="ui green button button-secondary ct_tax_media_button" id="footer2_logo_add" name="ct_tax_media_button" value="Add Image">
							<?php } ?>
							<?php if (isset($footer2_image_id) && !empty($footer2_image_id)) { ?>
								<input type="button" class="ui red button button-secondary ct_tax_media_remove" id="footer2_logo_remove" name="ct_tax_media_remove" value="Remove Image">
							<?php } else { ?>
								<input type="button" class="ui red button button-secondary ct_tax_media_remove" id="footer2_logo_remove" name="ct_tax_media_remove" value="Remove Image" style="display: none;">
							<?php } ?>
						</p>
					</div>
				</div>
				<div class="field">
					<label>Fav Logo (Size: 16 * 16)</label>
					<div class="form-field term-group">
						<input type="hidden" id="fav-image-id" name="fav-logo" class="custom_media_url" value="<?= $fav_image_id ?>">
						<div id="fav-image-wrapper">
							<?php if (isset($fav_image_id) && !empty($fav_image_id)) { ?>
								<img src="<?= wp_get_attachment_image_src($fav_image_id)[0] ?>" style="max-width: 350px;">
							<?php } ?>
						</div>
						<p class="mp">
							<?php if (isset($fav_image_id)) { ?>
								<input type="button" class="ui green button button-secondary ct_tax_media_button" id="fav_logo_add" name="ct_tax_media_button" value="Add Image" <?php echo ($fav_image_id == '') ? '' : 'style="display: none;"' ?>>
							<?php } else { ?>
								<input type="button" class="ui green button button-secondary ct_tax_media_button" id="fav_logo_add" name="ct_tax_media_button" value="Add Image">
							<?php } ?>
							<?php if (isset($fav_image_id) && !empty($fav_image_id)) { ?>
								<input type="button" class="ui red button button-secondary ct_tax_media_remove" id="fav_logo_remove" name="ct_tax_media_remove" value="Remove Image">
							<?php } else { ?>
								<input type="button" class="ui red button button-secondary ct_tax_media_remove" id="fav_logo_remove" name="ct_tax_media_remove" value="Remove Image" style="display: none;">
							<?php } ?>
						</p>
					</div>
				</div>
			</div>
			<input type="hidden" id="activestep">
			<div class="ui dimmer modals page">
				<div class="ui fullscreen modal scrolling">
					<i class="close icon"></i>
					<div class="header">
						Review Builder
					</div>
					<div class="ui segment scrolling content ">
						<div class="ui fluid steps">
							<div class="active step" id="progress-step-1">
								<i class="question circle icon"></i>
								<div class="content">
									<div class="title">Select Questions</div>
									<div class="description">Choose your questions</div>
								</div>
							</div>
							<div class="step" id="progress-step-2">
								<i class="bug icon"></i>
								<div class="content">
									<div class="title">Rules & Validation</div>
									<div class="description">Validating Review</div>
								</div>
							</div>
							<div class="step" id="progress-step-3">
								<i class="paper plane outline icon"></i>
								<div class="content">
									<div class="title">Generate URL</div>
								</div>
							</div>
						</div>
						<div id="startstep1">
							<div class="ui segment form">
								<div class="field" id="tf_reviewsystemtitlediv">
									<label>Review Title</label>
									<input type="text" id="tf_reviewsystem_title" value="<?= $title ?>" placeholder="Review Title">
								</div>
							</div>
							<input type="hidden" name="ajaxurl" id="ajaxurl" value="<?php echo admin_url('admin-ajax.php'); ?>">
							<input type="hidden" name="tf_reviewsystemids" id="tf_reviewsystemids">
							<div class="ui segment form">
								<div class="ui sub header">Select Department</div>
								<div class="ui fluid selection dropdown" id="SurveyGenDropdown" tabindex="0">
									<input name="tf_reviewsystemdepartment" type="hidden" value="<?php echo $tf_reviewsystemdepartment; ?>" id="tf_reviewsystemdepartment">
									<i class="dropdown icon"></i>
									<div class="default text">Select Department</div>
									<div class="menu transition hidden" tabindex="-1">
										<?php foreach ($departments as $key => $value) { ?>
											<div class="item" data-value="<?= $value->ID ?>">
												<?= $value->post_title ?></div>
										<?php } ?>
									</div>
								</div>
								<br>
								<div class="ui form field" id="categoryquestions_loader">
									<div class="two fields">
										<div class="field">
											<h4 class="ui top attached block header">
												Questions
											</h4>
											<div class="ui bottom attached segment" id="categoryquestions">
											</div>
										</div>
										<div class="field" id="tf_reviewsystems_question">
											<h4 class="ui top attached block header">
												Review Questions
											</h4>
											<div class="ui bottom attached segment" id="tf_reviewsystemquestions">

											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id="startstep2" style="display: none;">
							<div class="ui segment form">
								<!-- <div class="grouped fields" id="tf_reviewsystemavaliablity">
									<label>tf_reviewsystem Avaliablity?</label>
									<div class="field">
										<div class="ui radio checkbox avaliablity">
											<input type="radio" name="avaliablity" id="always" value="always" <?php echo ($avaliablity == 'always') ? 'checked' : '' ?>>
											<label for="always">Always avaliable</label>
										</div>
									</div>
									<div class="field">
										<div class="ui radio checkbox avaliablity">
											<input type="radio" name="avaliablity" id="specific" value="specific" <?php echo ($avaliablity == 'specific') ? 'checked' : '' ?>>
											<label for="specific">Avaliable on specific time</label>
										</div>
									</div>
								</div> -->
								<div class="two fields" id="datedependsonava" <?php //echo ($avaliablity == 'specific') ? '' : 'style="display: none;"' 
																				?>>
									<div class="field" id="startdates">
										<label>Start date</label>
										<div class="ui calendar" id="rangestart">
											<div class="ui input left icon">
												<i class="calendar icon"></i>
												<?php if ($startdate == '') {
													$startdate = date('Y-m-d');
												} ?>
												<input type="text" placeholder="Start" name="startdate" id="startdate" value="<?= $startdate ?>" readonly>
											</div>
										</div>
									</div>
									<div class="field" id="enddates">
										<label>End date</label>
										<div class="ui calendar" id="rangeend">
											<div class="ui input left icon">
												<i class="calendar icon"></i>
												<input type="text" placeholder="End" name="enddate" id="enddate" value="<?= $enddate ?>" readonly>
											</div>
										</div>
									</div>
								</div>
								<div class="field" id="yearfield">
									<label>Select Year</label>
									<select class="ui dropdown tf_reviewsystemc" name="yearlist" id="yearlist">
										<option value="">Select Year</option>
										<?php foreach ($yearlist as $key => $value) { ?>
											<?php if ($tf_reviewsystemcompany == $value->ID) { ?>
												<option value="<?= $value->ID ?>" selected><?= $value->post_title ?></option>
											<?php } else { ?>
												<option value="<?= $value->ID ?>"><?= $value->post_title ?></option>
											<?php } ?>

										<?php } ?>
									</select>
								</div>



								<div class="field" id="reviewforfield">
									<label>Review For</label>
									<?php //echo "<pre>"; print_r($employeeData);  
									?>
									<select class="ui dropdown review_for" name="review_for" id="review_for">
										<option value="">Select Employee</option>
										<?php foreach ($employeeData as $key => $value) { ?>
											<?php if ($tf_reviewsystemreview_for == $value->ID) { ?>
												<option value="<?= $value->ID ?>" selected><?= $value->display_name ?></option>
											<?php } else { ?>
												<option value="<?= $value->ID ?>"><?= $value->display_name ?></option>
											<?php } ?>

										<?php } ?>
									</select>
								</div>

								<div class="field" id="peerlistfield">
									<label>Select reviewer</label>
									<div class="ui selection dropdown select-language multiple">
										<input name="emppeerlist" type="hidden" value="<?= $tf_reviewsystemlocation ?>" id="emppeerlist">
										<div class="default text" value="">Select reviewer</div>
										<i class="dropdown icon"></i>
										<div class="menu ui transition hidden" id="peerlist">
											<?php foreach ($employeeData as $key => $value) { ?>
												<div class="item" data-value="<?= $value->ID ?>"><?= $value->display_name ?></div>
											<?php } ?>
										</div>
									</div>
								</div>

							</div>
						</div>
						<div id="startstep3" style="display: none;">
							<div class="ui segment form">
								<div class="field" id="urlfield">
									<label>URL</label>
									<div class="ui labeled input">
										<div class="ui label">
											<?= site_url('u'); ?>
										</div>
										<input type="text" name="url" placeholder="URL" value="<?= $url ?>" id="url">
									</div>

								</div>
							</div>
						</div>
					</div>
					<div class="actions">
						<div class="ui negative button">
							Cancel
						</div>
						<div class="ui positive right labeled icon button">
							<span id="tf_reviewsystemsubmit">Next</span>
							<i class="checkmark icon"></i>
						</div>
					</div>
				</div>
				<script type="text/javascript">
					jQuery(document).ready(function($) {

						$(".tf_reviewsystemc").dropdown({
							onChange: function(o, n, t) {
								$.ajax({

									type: "post",

									dataType: "json",

									url: myAjax.ajaxurl,

									data: {
										action: "getlocations",
										id: o
									},

									success: function(response) {

										/*$.each(response.data, function(i,item){

										});*/
										$('.slocat')
											.dropdown({
												values: response.data
											});
										$('.slocat').dropdown({
											onChange: function(o, n, t) {
												$("#clocation").val(o);
											}
										});
										/*$('.slocat').append('<div class="item" data-value="'+item.id+'">'+item.value+'</div>');*/
									}

								});

							}
						});

						$(".review_for").dropdown({
							onChange: function(o, n, t) {
								console.log("Tttt");
								$.ajax({

									type: "post",

									dataType: "json",

									url: myAjax.ajaxurl,

									data: {
										action: "getpeerlist",
										id: o
									},

									success: function(response) {
										console.log(response);
										console.log(response.data.length);

										/*$.each(response.data, function(i,item){

										});*/
										$html = '';
										for (i = 0; i < response.data.length; ++i) {
											$html += '<div class="item" data-value="' + response.data[i].value + '">' + response.data[i].name + '</div>';

										}
										$('#peerlist').html($html);

										// $('.peerlist')
										// .dropdown({
										// 	values:response.data
										// });
										// 	$('.peerlist').dropdown({onChange: function(o, n, t) {
										// 		$("#cpeerlist").val(o);
										// 	}
										// });
										/*$('.slocat').append('<div class="item" data-value="'+item.id+'">'+item.value+'</div>');*/
									}

								});

							}
						});


						$('.peerlist').dropdown({
							onChange: function(o, n, t) {
								$("#cpeerlist").val(o);
							}
						});

						$('.slocat').dropdown({
							onChange: function(o, n, t) {
								$("#clocation").val(o);
							}
						});
						var myplugin_media_upload;
						$('#header_logo_add').click(function(e) {
							e.preventDefault();
							if (myplugin_media_upload) {
								myplugin_media_upload.open();
								return;
							}

							myplugin_media_upload = wp.media.frames.file_frame = wp.media({
								multiple: false

							});

							myplugin_media_upload.on('select', function() {
								$('#header_logo_add').hide();
								$('#header_logo_remove').show();
								var attachments = myplugin_media_upload.state().get('selection').map(
									function(attachment) {
										attachment.toJSON();
										return attachment;
									});
								var i;
								for (i = 0; i < attachments.length; ++i) {
									$('#header-image-wrapper').append(
										'<div class="myplugin-image-preview"><img src="' +
										attachments[i].attributes.url + '" style="max-width: 350px;"></div>'
									);
									$('#header-image-id').val(attachments[i].id);
								}
							});

							myplugin_media_upload.open();
						});

						$("body").on("click", "#header_logo_remove", function() {
							$("#header-image-id").val(""), $("#header-image-wrapper").html("");
							$('#header_logo_add').show();
							$('#header_logo_remove').hide();
						});
					});
					jQuery(document).ready(function($) {
						var myplugin_media_upload;
						$('#bg_logo_add').click(function(e) {
							e.preventDefault();
							if (myplugin_media_upload) {
								myplugin_media_upload.open();
								return;
							}

							myplugin_media_upload = wp.media.frames.file_frame = wp.media({
								multiple: false

							});

							myplugin_media_upload.on('select', function() {
								$('#bg_logo_add').hide();
								$('#bg_logo_remove').show();
								var attachments = myplugin_media_upload.state().get('selection').map(
									function(attachment) {
										attachment.toJSON();
										return attachment;
									});

								var i;
								for (i = 0; i < attachments.length; ++i) {
									$('#bg-image-wrapper').append(
										'<div class="myplugin-image-preview"><img src="' +
										attachments[i].attributes.url + '" style="max-width: 350px;"></div>'
									);
									$('#bg-image-id').val(attachments[i].id);
								}
							});

							myplugin_media_upload.open();
						});

						$("body").on("click", "#bg_logo_remove", function() {
							$("#bg-image-id").val(""), $("#bg-image-wrapper").html("");
							$('#bg_logo_add').show();
							$('#bg_logo_remove').hide();
						})
					});
					jQuery(document).ready(function($) {
						var myplugin_media_upload;
						$('#footer_logo_add').click(function(e) {
							e.preventDefault();
							if (myplugin_media_upload) {
								myplugin_media_upload.open();
								return;
							}

							myplugin_media_upload = wp.media.frames.file_frame = wp.media({
								multiple: false

							});

							myplugin_media_upload.on('select', function() {
								$('#footer_logo_add').hide();
								$('#footer_logo_remove').show();
								var attachments = myplugin_media_upload.state().get('selection').map(
									function(attachment) {
										attachment.toJSON();
										return attachment;
									});

								var i;
								for (i = 0; i < attachments.length; ++i) {
									$('#footer-image-wrapper').append(
										'<div class="myplugin-image-preview"><img src="' +
										attachments[i].attributes.url + '" style="max-width: 350px;"></div>'
									);
									$('#footer-image-id').val(attachments[i].id);
								}
							});

							myplugin_media_upload.open();
						});

						$("body").on("click", "#footer_logo_remove", function() {
							$("#footer-image-id").val(""), $("#footer-image-wrapper").html("");
							$('#footer_logo_add').show();
							$('#footer_logo_remove').hide();
						})
					});
					jQuery(document).ready(function($) {
						var myplugin_media_upload;
						$('#footer2_logo_add').click(function(e) {
							e.preventDefault();
							if (myplugin_media_upload) {
								myplugin_media_upload.open();
								return;
							}

							myplugin_media_upload = wp.media.frames.file_frame = wp.media({
								multiple: false

							});

							myplugin_media_upload.on('select', function() {
								$('#footer2_logo_add').hide();
								$('#footer2_logo_remove').show();
								var attachments = myplugin_media_upload.state().get('selection').map(
									function(attachment) {
										attachment.toJSON();
										return attachment;
									});

								var i;
								for (i = 0; i < attachments.length; ++i) {
									$('#footer2-image-wrapper').append(
										'<div class="myplugin-image-preview"><img src="' +
										attachments[i].attributes.url + '" style="max-width: 350px;"></div>'
									);
									$('#footer2-image-id').val(attachments[i].id);
								}
							});

							myplugin_media_upload.open();
						});

						$("body").on("click", "#footer2_logo_remove", function() {
							$("#footer2-image-id").val(""), $("#footer2-image-wrapper").html("");
							$('#footer2_logo_add').show();
							$('#footer2_logo_remove').hide();
						})
					});
					jQuery(document).ready(function($) {
						var myplugin_media_upload;
						$('#fav_logo_add').click(function(e) {
							e.preventDefault();
							if (myplugin_media_upload) {
								myplugin_media_upload.open();
								return;
							}

							myplugin_media_upload = wp.media.frames.file_frame = wp.media({
								multiple: false

							});

							myplugin_media_upload.on('select', function() {
								$('#fav_logo_add').hide();
								$('#fav_logo_remove').show();
								var attachments = myplugin_media_upload.state().get('selection').map(
									function(attachment) {
										attachment.toJSON();
										return attachment;
									});

								var i;
								for (i = 0; i < attachments.length; ++i) {
									$('#fav-image-wrapper').append(
										'<div class="myplugin-image-preview"><img src="' +
										attachments[i].attributes.url + '" style="max-width: 350px;"></div>'
									);
									$('#fav-image-id').val(attachments[i].id);
								}
							});

							myplugin_media_upload.open();
						});

						$("body").on("click", "#fav_logo_remove", function() {
							$("#fav-image-id").val(""), $("#fav-image-wrapper").html("");
							$('#fav_logo_add').show();
							$('#fav_logo_remove').hide();
						})
					});
				</script>
				<script type="text/javascript">
					jQuery(document).ready(function(e) {

						e(".menu .item").tab(), e(".ui.accordion").accordion(), e("#question").on("keyup", function() {
							"" == e("textarea#question").val() ? e("#title").text("Question") : e("#title").text(e("textarea#question").val())
						}), e("#questionstype").dropdown({
							onChange: function(o, n, t) {
								0 == t.data("option") ? e("#accordion").hide() : e("#accordion").show()
							}
						})
					});
				</script>
				<style type="text/css">
					.ui.fullscreen.scrolling.modal,
					.ui.fullscreen.modal {
						left: unset !important;
					}

					.ui.dimmer {
						z-index: 9999;
					}

					.step {
						cursor: pointer;
					}

					.ui.accordion,
					.ui.accordion .accordion {
						margin-bottom: 8px;
					}

					#post-body-content {
						display: none;
					}
				</style>
				<?php
			}
			add_action('admin_init', 'wpdocs_codex_init');
			function wpdocs_codex_init()
			{
				add_action('delete_post', 'wpdocs_codex_sync', 10);
			}

			function wpdocs_codex_sync($pid)
			{
				if (get_post_type($pid) == 'tf_reviewsystem') {
					post_delete('wp_tf_reviewsystem', $pid);
				}
				if (get_post_type($pid) == 'locations') {
					post_delete('wp_locations', $pid);
				}
				if (get_post_type($pid) == 'arrangements') {
					post_delete('wp_arrangements', $pid);
				}
				if (get_post_type($pid) == 'contract_types') {
					post_delete('wp_contract_types', $pid);
				}
				if (get_post_type($pid) == 'company_types') {
					post_delete('wp_company_types', $pid);
				}
				if (get_post_type($pid) == 'companies') {
					post_delete('wp_companies', $pid);
				}
				if (get_post_type($pid) == 'questions') {
					post_delete('wp_questions', $pid);
				}
				if (get_post_type($pid) == 'usergroup') {
					post_delete('wp_tf_reviewsystem_usergroup', $pid);
				}
			}
			function post_delete($table_name, $pid)
			{
				global $wpdb;
				$query = $wpdb->prepare('SELECT post_id FROM ' . $table_name . ' WHERE post_id = %d', $pid);
				$var = $wpdb->get_var($query);
				if ($var) {
					$query2 = $wpdb->prepare('DELETE FROM ' . $table_name . ' WHERE post_id = %d', $pid);
					$wpdb->query($query2);
				}
			}
			// Add custom column to Users admin panel
			function custom_users_columns($columns)
			{
				$columns['department'] = 'Department';
				return $columns;
			}
			add_filter('manage_users_columns', 'custom_users_columns');

			// Populate the custom column with data
			function custom_users_column_content($value, $column_name, $user_id)
			{
				if ($column_name == 'department') {
					$department_id = get_user_meta($user_id, 'user_department', true);

					// Get department name based on the stored ID
					$department_name = '';
					if ($department_id) {
						$department = get_post($department_id);
						$department_name = $department ? $department->post_title : '';
					}

					return esc_html($department_name);
				}
				return $value;
			}
			add_filter('manage_users_custom_column', 'custom_users_column_content', 10, 3);



			function tf_reviewsystem_activation_actions()
			{
				do_action('tf_reviewsystem_activation');
			}
			register_activation_hook(__FILE__, 'tf_reviewsystem_activation_actions');

			add_action('tf_reviewsystem_activation', 'tf_reviewsystem_options');

			function tf_reviewsystem_options()
			{

				global  $table_prefix, $wpdb;
				$feedback_paraTable = $table_prefix . 'feedback_para';
				if ($wpdb->get_var("show tables like '$feedbacktf_reviewsystem_options_paraTable'") != $feedback_paraTable) {
					$sql = "CREATE TABLE $feedback_paraTable (
					para_id INT NOT NULL AUTO_INCREMENT,
					year_id INT,
					post_id INT,
					department_id INT,
					review_for INT,
					peer_review INT,
					questionlist TEXT,
					review_enddt DATE,
					token VARCHAR(255),
					PRIMARY KEY (para_id)
				);";

					// Include Upgrade Script
					require_once(ABSPATH . '/wp-admin/includes/upgrade.php');

					// Create Table
					dbDelta($sql);

					$sql = "ALTER TABLE $feedback_paraTable ADD PRIMARY KEY (`para_id`)";
					$wpdb->query($sql);

					$sql = "ALTER TABLE $feedback_paraTable MODIFY `para_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1";
					$wpdb->query($sql);
				}

				$feedback_statusTable = $table_prefix . 'feedback_status';
				if ($wpdb->get_var("show tables like '$feedback_statusTable'") != $feedback_statusTable) {
					$sql = "CREATE TABLE $feedback_statusTable (
					id INT NOT NULL AUTO_INCREMENT,
					post_id INT,
					peer_review_id INT,
					status VARCHAR(255),
					reason_status VARCHAR(255),
					reason_message TEXT,
					miss_review INT,
					PRIMARY KEY (id)
				);";

					// Include Upgrade Script
					//require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

					// Create Table
					dbDelta($sql);

					$sql = "ALTER TABLE $feedback_statusTable ADD PRIMARY KEY (`id`)";
					$wpdb->query($sql);

					$sql = "ALTER TABLE $feedback_statusTable MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1";
					$wpdb->query($sql);
				}
				$departmentsTable = $table_prefix . 'departments';
				if ($wpdb->get_var("show tables like '$departmentsTable'") != $departmentsTable) {
					$sql = "CREATE TABLE $departmentsTable (
					id INT NOT NULL AUTO_INCREMENT,
					post_id INT NOT NULL,
					title TEXT,
					datetime timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
					PRIMARY KEY (id)
				);";

					// Include Upgrade Script
					//require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

					// Create Table
					dbDelta($sql);

					$sql = "ALTER TABLE $departmentsTable ADD PRIMARY KEY (`id`)";
					$wpdb->query($sql);

					$sql = "ALTER TABLE $departmentsTable MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1";
					$wpdb->query($sql);
				}

				$questionsTable = $table_prefix . 'questions';
				if ($wpdb->get_var("show tables like '$questionsTable'") != $questionsTable) {
					$sql = "CREATE TABLE $questionsTable (
					id INT NOT NULL AUTO_INCREMENT,
					post_id INT NOT NULL,
					title TEXT,
					category_id INT NOT NULL,
					datetime timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
					PRIMARY KEY (id)
				);";

					// Include Upgrade Script
					//require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

					// Create Table
					dbDelta($sql);

					$sql = "ALTER TABLE $questionsTable ADD PRIMARY KEY (`id`)";
					$wpdb->query($sql);

					$sql = "ALTER TABLE $questionsTable MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1";
					$wpdb->query($sql);
				}
			}

			//delete the post from DB
			add_action('wp_trash_post', 'custom_post_type_trash_hook', 10, 1);
			function custom_post_type_trash_hook($post_id)
			{
				// Get the post object
				$post = get_post($post_id);
				// Check if it's the desired custom post type
				if ($post && $post->post_type == 'tf_reviewsystem') {
					global $wpdb;
					$feedback_para = $wpdb->prefix . 'feedback_para';
					$post_id = $post->ID;
					// Delete the record
					$wpdb->delete(
						$feedback_para,
						array('post_id' => $post_id),
						array('%d') // WHERE
					);
					$feedback_status = $wpdb->prefix . 'feedback_status';
					$wpdb->delete(
						$feedback_status,
						array('post_id' => $post_id),
						array('%d') // WHERE
					);
				}
			}
			ob_end_clean();

			// Hook to add content below the search box using JavaScript
			// Hook to add content after filter buttons using JavaScript
			add_action('admin_footer', 'custom_post_type_additional_content_js');

			function custom_post_type_additional_content_js()
			{
				// Check if it's the desired custom post type
				global $post_type;
				if ('tf_reviewsystem' === $post_type) {
				?>
					<script>
						jQuery(document).ready(function($) {
							// Find the filter buttons container and append your content
							jQuery('<div class="color-info"><ul><li><span class="clr-red"></span><p>Pending</p></li><li><span class="clr-green"></span><p>Submitted</p></li><li><span class="clr-blue"></span><p>Missed</p></li></ul></div>').insertAfter('.tablenav.top #post-query-submit');
						});
					</script>
				<?php
				}
			}
			add_action('admin_notices', 'display_start_demo_button');

			function display_start_demo_button()
			{
				// Check if the notice has been dismissed
				$notice_dismissed = get_option('your_plugin_demo_notice_dismissed');

				//print_r($notice_dismissed);
				// echo "hello";
				// exit;

				// Check if it's the plugin activation notice and on the dashboard
				if (is_admin() && !$notice_dismissed) {
				?>
					<div class="notice notice-success is-dismissible custom-notice">
						<p><?php _e('Thank you for installing Employee Review System Plugin!'); ?></p>
						<h4><?php _e('You can add reviewers as a WordPress user and you can assign them to departments'); ?></h4>
						<button class="button button-primary" id="start-demo-button"><?php _e('Start Demo'); ?></button>
					</div>
					<script>
						document.addEventListener('DOMContentLoaded', function() {
							function handleNextPagesButtonClick() {
								//alert('Next clicked under Pages!');
								jQuery('#menu-posts-tf_reviewsystem').on({
									mouseenter: function() {
										jQuery('#menu-posts-tf_reviewsystem .wp-submenu-wrap').css('display', 'none');
										console.log('Mouse entered');
									},
									mouseleave: function() {
										//jQuery('#menu-posts-tf-yearlist .wp-submenu-wrap').css('display', 'block');
										console.log('Mouse left');
									}
								});
								jQuery('.next-button-container').css('display', 'none');
								var settingsMenu = jQuery('#menu-posts-tf_reviewsystem');
								var newDiv = '<div class="new-div-review next-button-container"><button class="button button-primary" id="finish-button">Finish</button><ul style="color:#fff;"><li>Here you can Create Review.</li><li>Edit Review</li><li>SMTP Configuration</li><li>Mail Configuration</li><li>Feedback display</li></ul></div>';
								settingsMenu.append(newDiv);
								jQuery('#next-button-pages').remove();
								jQuery('#finish-button').on('click', function() {
									alert('Demo Completed successfully!');
									jQuery('.new-div-review').remove();
									jQuery('#start-demo-button').show();
									jQuery('.custom-notice').css('display', 'none');
									jQuery.ajax({
										type: "post",
										dataType: "json",
										url: '<?php echo admin_url('admin-ajax.php'); ?>', // Make sure this is defined properly
										data: {
											action: "dismis_error_notice",
										},
										success: function(response) {
											console.log('Notice dismissed successfully');
										},
										error: function(error) {
											console.error('Error dismissing notice:', error);
										}
									});
								});
							}
							document.getElementById('start-demo-button').addEventListener('click', function() {
								//alert('Start Demo clicked!');
								jQuery('#menu-posts-questions a.menu-top').trigger('click');
								var nextButton = '<div class="next-button-container"><button class="button button-primary" id="next-button">Next</button><ul style="color:#fff;"><li>Here you cann Add Questions.</li><li>Edit Questions</li><li>Add Department</li></ul></div>';
								jQuery('#menu-posts-questions .wp-submenu').after(nextButton);
								jQuery('.menu-icon-questions').off('mouseenter mouseleave');
								jQuery('#next-button').on('click', function() {
									jQuery('#menu-posts-tf-yearlist').on({
										mouseenter: function() {
											jQuery('#menu-posts-tf-yearlist .wp-submenu-wrap').css('display', 'none');
											console.log('Mouse entered');
										},
										mouseleave: function() {
											console.log('Mouse left');
										}
									});
									//alert('Next clicked under Questions!');
									jQuery('#next-button').remove();
									jQuery('.next-button-container').css('display', 'none');
									jQuery('#menu-posts-tf-yearlist a.menu-top').trigger('click');
									jQuery('.message-for-questions').remove();
									var menuPages = jQuery('#menu-posts-tf-yearlist');
									var nextButtonPages = '<div class="next-button-container"><button class="button button-primary" id="next-button-pages">Next</button><ul style="color:#fff;"><li>Here you cann Add Year.</li><li>Edit Year</li></ul></div>';
									menuPages.append(nextButtonPages);
									jQuery('#next-button-pages').on('click', handleNextPagesButtonClick);
								});
								jQuery('#start-demo-button').hide();
							});
						});
					</script>
			<?php
				}
			}
			function dismis_error_notice()
			{
				update_option('your_plugin_demo_notice_dismissed', true);
				wp_send_json_success('Notice dismissed successfully');
			}
