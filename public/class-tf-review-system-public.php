<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://techforceglobal.com
 * @since      1.0.0
 *
 * @package    Tf_Review_System
 * @subpackage Tf_Review_System/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Tf_Review_System
 * @subpackage Tf_Review_System/public
 * @author     Techforce <sanju.techforce@gmail.com>
 */
class Tf_Review_System_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->load_phpmailer();
		add_filter('template_include', array($this, 'include_template'));
		add_filter('theme_page_templates', array($this, 'register_custom_template'));
		//add_action('save_post', array($this, 'save_custom_template'));
		add_action('after_setup_theme', array($this,'create_tf_review_page'));
		add_shortcode('tf_review_system_template', array($this, 'tf_review_system_template_shortcode'));

		add_action("wp_ajax_ers_tf_reviewsystemresponsestore", array($this,"ers_tf_reviewsystemresponsestore"));
		add_action("wp_ajax_nopriv_ers_tf_reviewsystemresponsestore", array($this, "ers_tf_reviewsystemresponsestore"));

        add_action("wp_ajax_expired_review", array($this,"expired_review"));
        add_action("wp_ajax_nopriv_expired_review", array($this,"expired_review"));

	}

    private function load_phpmailer() {
        // Ensure the files are only included once
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            require ABSPATH . WPINC . '/PHPMailer/Exception.php';
            require ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
            require ABSPATH . WPINC . '/PHPMailer/SMTP.php';
        }
    }

	  // Method to include the custom template
	  public function include_template($template) {
		if (is_page() && get_page_template_slug() == 'template-tf-review-system.php') {
			$new_template = plugin_dir_path(__FILE__) . 'templates/template-tf-review-system.php'; 
			if (file_exists($new_template)) {
				return $new_template;
			}
		}
		return $template;
	}
 
	 // Method to register the custom template
	 public function register_custom_template($templates) {
       // wp_nonce_field('save_custom_template_nonce_action', 'save_custom_template_nonce');
		$templates['template-tf-review-system.php'] = 'TF Review System Template';
		return $templates;
	}
 

function create_tf_review_page() {
  
    // The title of the new page
    $page_title = 'Employee Review';
    $page_content = 'This is the TF Review System page.';
    $page_template = 'template-tf-review-system.php'; // The template file name

    // Check if the page already exists using WP_Query
    $args = array(
        'post_type' => 'page',
        'title' => $page_title,
        'post_status' => 'publish',
        'posts_per_page' => 1
    );
    $page_query = new WP_Query($args);

    // If the page doesn't exist, create it
    if (!$page_query->have_posts()) {
        // Create the new page
        $new_page_id = wp_insert_post(array(
            'post_type' => 'page',
            'post_title' => $page_title,
            'post_content' => $page_content,
            'post_status' => 'publish',
            'post_author' => 1,
        ));
        
        // Assign the custom template to the new page
        if (!is_wp_error($new_page_id)) {
            update_post_meta($new_page_id, '_wp_page_template', $page_template);
        }
    }

    // Reset post data
    wp_reset_postdata();
}
 
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tf_Review_System_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tf_Review_System_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tf-review-system-public.css', array(), $this->version, 'all' );
		wp_enqueue_style('bootstrap.min', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css');
		wp_enqueue_style('frontform', plugin_dir_url(__FILE__) . 'css/frontform.css');
		
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tf_Review_System_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tf_Review_System_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tf-review-system-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script('bootstrap.min', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'), null, true);
	
		wp_enqueue_script('custom-js', plugin_dir_url(__FILE__) . 'js/custom.js', array('jquery'), null, true);
		// wp_enqueue_script('jquery-dual-listbox', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-dual-listbox/1.0.4/jquery.dualListBox.min.js', array('jquery'), null, true);
  
		 
		 // Localize script to use AJAX URL in JS
		 wp_localize_script('custom-js', 'ajax_object', array(
			 'ajax_url' => admin_url('admin-ajax.php'),
             'nonce'    => wp_create_nonce('submit_review')
		 ));
  
	}

	public function ers_tf_reviewsystemresponsestore()
	{
		
         // Verify the nonce
        if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'submit_review')) {
            wp_send_json_error('Invalid nonce.');
        }
	    parse_str($_POST['form_data'], $str);
		global $post;
		global $wpdb;
		$tf_reviewsystem_id = sanitize_text_field($_POST['tf_reviewsystem_id']);
        $review_for = sanitize_text_field($_POST['review_for']);
        $review_by_id = sanitize_text_field($_POST['review_by_id']);
        $summary = sanitize_text_field($_POST['summary']);
	
		$tf_reviewsystem = get_post_meta($tf_reviewsystem_id);
		 
		$department = $tf_reviewsystem['_department'][0];
		//$user = $tf_reviewsystem['_user'][0];
		$year = $tf_reviewsystem['_year'][0];

		$table_name = $wpdb->prefix ."tf_reviewsystem_responces";
	 
		$success = $wpdb->insert($table_name, array(
			"tf_reviewsystem_id" => $tf_reviewsystem_id,
			"department_id" => $department,
			"year_id" => $year,
			"review_for" => $review_for,
			"review_by" => $review_by_id,
			"comments" => $summary,
			'datetime' => gmdate('y-m-d h:m:s'),
			"is_submitted" => 1,
		));
		$tf_reviewsystem_responce_id = $wpdb->insert_id;
		$response_table_name = $wpdb->prefix ."tf_reviewsystem_responce_details";
		foreach ($str as $key => $value) {
			
			if(is_numeric($key)){
	
			
				$val = (int) $value;
				if ($val != 0 && strpos($value, '-') == false && $val <= 4) {
					$field = "score";
				} else {
					$field = "responce";
				}
				$department = get_post_meta($key, '_department', true);
				if ($value != 0 && is_numeric($value)) {
					$success = $wpdb->insert($response_table_name, array(
						"tf_reviewsystem_responce_id" => $tf_reviewsystem_responce_id,
						"question_id" => $key,
						$field => $value,
						"department_id" => $department,
					));
				}
			}
	
		}
	 
		//update review feedback status
		$table_name = $wpdb->prefix ."tf_reviewsystem_feedback_status";
		$feedback_data_to_update = array(
			'status' => '1',
		);
		$where_clause = array(
			'post_id' => $tf_reviewsystem_id,
			'peer_review_id' => $_POST['review_by_id'],
		);
		$wpdb->update($table_name, $feedback_data_to_update, $where_clause);
		wp_send_json_success();
	}
	

 
public function expired_review()
{
      // Verify the nonce
      if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'submit_review')) {
        wp_send_json_error('Invalid nonce.');
    }

	global $post;
	global $wpdb;
	$reson_msg = $_POST['reason'];
	$post_id = $_POST['post_id'];
	$emp_id = $_POST['emp_id'];
	$hr_name =  get_option('hr_name');
	$hr_email = get_option('hr_email');
	 
    $user = get_user_by('id', $emp_id);
    $emp_name = $user->display_name;

	$table_name = $wpdb->prefix . 'tf_reviewsystem_feedback_status';
	$feedback_data_to_update = array(
		'reason_message' => $reson_msg,
		'reason_status' => '1',
	);
	$where_clause = array(
		'post_id' => $post_id,
		'peer_review_id' => $emp_id,
	);
	$wpdb->update($table_name, $feedback_data_to_update, $where_clause);

	$url = "" . admin_url() . "/edit.php?post_type=review";
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

	//date_default_timezone_set('Etc/UTC');

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
	if($mail->send()){
        wp_send_json_success(true);
    }else{
        wp_send_json_success(false);
    }
}


// Register the shortcode
function tf_review_system_template_shortcode() {
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'my_nonce_action')) {
        wp_die('Nonce verification failed');
    }
    
	 global $wpdb;
    // Decode the parameters
	$token = isset($_GET['t']) ? base64_decode($_GET['t']) : '';
	$employee = isset($_GET['e']) ? base64_decode($_GET['e']) : '';
	$expire_date = isset($_GET['d']) ? base64_decode($_GET['d']) : '';

   
	$current_date = gmdate('Y-m-d');
	$date1 = strtotime($current_date);
	$date2 = strtotime($expire_date);

	$datediff = $date2 - $date1;

    $table_name = $wpdb->prefix . "tf_reviewsystem_feedback_para";
	if (!empty($_GET['t']) && !empty($_GET['e'])) {
		$result_para = $wpdb->get_row($wpdb->prepare("SELECT * FROM %s WHERE `token` LIKE %s", $table_name, $token));
	}
	//$post_id = $result_para->post_id;
	$post_id = $result_para ? $result_para->post_id : '';
    $tf_review_site_logo = get_option('tf_review_site_logo'); 

	if ($expire_date < $current_date) { 
        
        ?>
 <div class="container">
            <div class="bd">
                <div class="header_logo" align="center"><img src="<?php echo esc_attr($tf_review_site_logo); ?>"></div>
                <div class="clearfix"></div>

    <div class="link-expired-container">
    <div class="link-expired-wrapper">
    <h1>Link Expired</h1>
    <p>The link you have followed has expired.</p>
    <form id="reason_form" class="form-horizontal">
    <?php //$nonce = wp_create_nonce('submit_review'); ?>
        <label for="reson for expire">Add Reason for Late Submission</label>
        <!-- <input type="text" name="reason" id="reason" required /> -->
        <textarea name="reason" id="reason" required></textarea>
        <input type="hidden" name="post_id" id="post_id" value="<?php echo $post_id; ?>">
        <input type="hidden" name="emp_id" id="emp_id" value="<?php echo $employee; ?>">
        <input type="button" name="submit" id="submit_reason" value="Submit">
    </form>
    </div>
    <div class="thankyoutf_reviewsystem" style="display: none;">
                    <div class="imim wel">
                         <img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/thank-you-placard.png';?>" alt="">
                    </div>
                </div>  
                </div></div>
                </div>
	<?php }else{
	
    
    $tf_reviewsystem_responces_table_name = $wpdb->prefix . "tf_reviewsystem_responces";
	$tf_reviewsystem_responces_res = $wpdb->get_results($wpdb->prepare("select * from %s WHERE `review_by` LIKE %s AND `tf_reviewsystem_id` LIKE %s AND is_submitted=1",$tf_reviewsystem_responces_table_name,$employee,$post_id));
  
?>

	<?php 
	ob_start();
	?>
	  <style type="text/css">
        #msform fieldset:not(:first-of-type) {
            display: none;
        }
    </style>
	 
        <div class="container">
            <div class="bd">
                <div class="header_logo" align="center"><img src="<?php echo esc_attr($tf_review_site_logo); ?>"></div>
                <div class="clearfix"></div>

                <?php
                 if ( !empty($tf_reviewsystem_responces_res)) { ?>
                    <div class="already_submitted_form row">
                    <div class="container">
                         <h2>You've Already Responded!</h2>
                         <h4>You can only respond once...</h4>
                    </div>
                </div>  

                <?php }else{ ?>

             
                 <form id="msform">
                 <?php wp_nonce_field('submit_review', 'review_nonce'); ?>
                    <div class="row form-ul">
                        <ul>
                            <li>
                                <p>Year</p>
                                <?php
                                $yearData = get_post($result_para->year_id);
                                echo esc_html($yearData->post_title); 
                                ?>
                                </span>
                            </li>
                            <li>
                                <p>Review For</p>
                                <span>
                                <?php
                                    $user_reviewfor = get_user_by('id', $result_para->review_for);
                                    echo esc_html($user_reviewfor->display_name); ?>
                                </span>
                            </li>
                            <li>
                                <p>Review By</p>
                                <span>
                                    <?php
                                    $user_reviewbyname = get_user_by('id', $employee);
                                    echo esc_html($user_reviewbyname->display_name); ?>
                                </span>
                            </li>
                            <li>
                                <p>Department</p>
                                <span>
                                <?php 
                                    $departmentData = get_post($result_para->department_id);
                                    echo esc_html($departmentData->post_title);
                                   ?>
                                </span>
                            </li>
                            <li>
                                <p>Review End Date</p>
                                <span>
                                    <?php echo esc_html($expire_date); ?>
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
                    <fieldset>
                        <?php
                        // echo $result_para->post_id;
                        $html = '';
                        $question_data = get_post_meta($result_para->post_id, '_duallistbox_demo1', true);
                      
                        $total = count($question_data);
                        $flag = 0;
                        $q = 0;
                     
                        foreach ($question_data as $question_no => $question_id) {
                            $optionshtml = '';
                             $question = get_the_title($question_id); 
                             $question_type_value = get_post_meta($question_id, '_question_type_dropdown', true);
                           
                             
                            $question_type_value = isset($question_type_value) && !empty($question_type_value) ? $question_type_value : "";
                          if ($question_type_value == "Yes/No") {
                                $optionshtml = '<div class="yes_no_div">
                                <label class="lbl color option" style="color: rgb(0, 0, 0);">
                                <input type="radio" name="'.$question_id.'" value="4" class="choice op1 oop1">Yes</label>
                                <label class="lbl color" style="color: rgb(0, 0, 0);">
                                <input type="radio" name="'.$question_id.'" value="1" class="choice op1 oop1">No</label>
                                </div>';
                            } elseif ($question_type_value == "OpenEnded") {
                                $optionshtml = '<label class="lbl color option" style="color: rgb(0, 0, 0);">
                                <input type="number" class="12 openended" name="'.$question_id.'" maxlength="150" value="0" min="0" max="100">
                                <span class="remainingC" style="float: right;"></span></label>';
                            } 

                            if ($question_type_value == "OpenEnded") {
                                $flag++;
                                $html .= '<div class="form-group">';
                                $qk = (int) $question_no + 1;
                                $q++;
                                $html .= '<h2 class="title color">Q' . $q . '. <span class="translate" data-id="' . $question_id . '" data-key="question">' . $question . '</span></h2>';
                                $html .= $optionshtml;
                                $html .= '</div>';
                            }
                        }


                        if ($flag != 0) {
                            echo $html;
                        ?>
                            <input type="button" name="nextbtn" class="next btn btn-info" value="Next" />
                    </fieldset>
                    <fieldset>
                    <?php  } ?>


                    <?php $html = '';
                    $question_data = get_post_meta($result_para->post_id, '_duallistbox_demo1', true);
                   
                    $total = count($question_data);
                    foreach ($question_data as $question_no => $question_id) {
                        $optionshtml = '';
                        $question = get_the_title($question_id);
                        $question_type_value = get_post_meta($question_id, '_question_type_dropdown', true);
                            
                        $question_type_value = isset($question_type_value) && !empty($question_type_value) ? $question_type_value : "";
                        if ($question_type_value == "Yes/No") {
                            $optionshtml = '<div class="yes_no_div">
                            <label class="lbl color option" style="color: rgb(0, 0, 0);">
                            <input type="radio" name="'.$question_id.'" value="4" class="choice op1 oop1">Yes</label>
                            <label class="lbl color" style="color: rgb(0, 0, 0);">
                            <input type="radio" name="'.$question_id.'" value="1" class="choice op1 oop1">No</label>
                            </div>';
                        } elseif ($question_type_value == "OpenEnded") {
                            $optionshtml = '<label class="lbl color option" style="color: rgb(0, 0, 0);">
                            <input type="number" class="12 openended" name="'.$question_id.'" maxlength="150" value="0" min="0" max="100">
                            <span class="remainingC" style="float: right;"></span></label>';
                        } 

                        $html .= ' <div class="form-group">';
                        if ($question_type_value != "OpenEnded") {
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
                    if ($flag != 0) {
                    ?>
                        <input type="button" name="previous" class="previous btn btn-default" value="Previous" />
                    <?php } ?>
                    <input type="button" name="submit" class="submit btn btn-success" value="Submit" id="submit_data" />
                    </fieldset>

                    <input type="hidden" name="tf_reviewsystem_id" value="<?php echo esc_attr($post_id) ?>">
                    <input type="hidden" name="review_by_id" value="<?php echo esc_attr($employee); ?>" />
                    <input type="hidden" name="review_for" value="<?php echo esc_attr($result_para->review_for); ?>" />
                </form>
            <?php }?>
                <div class="thankyoutf_reviewsystem" style="display: none;">
                    <div class="imim wel">
                         <img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/thank-you-placard.png';?>" alt="">
                    </div>
                </div>  

             


                <div class="clearfix"></div>
                
 

            </div>
        </div>
   

	<?php }

     return ob_get_clean();
}



}
