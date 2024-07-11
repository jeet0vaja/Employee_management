<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://techforceglobal.com
 * @since      1.0.0
 *
 * @package    Tf_Review_System
 * @subpackage Tf_Review_System/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tf_Review_System
 * @subpackage Tf_Review_System/admin
 * @author     Techforce <sanju.techforce@gmail.com>
 */
class Tf_Review_System_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->load_phpmailer();
        //Add department dropdown on user page
        add_action('show_user_profile', array($this, 'display_department_dropdown'));
        add_action('edit_user_profile', array($this, 'display_department_dropdown'));
        add_action('personal_options_update', array($this, 'save_department_dropdown'));
        add_action('edit_user_profile_update', array($this, 'save_department_dropdown'));
		
		//register_activation_hook( __FILE__, array( $this, 'create_feedback_table' ) );
        //register_activation_hook( __FILE__, 'create_feedback_table' );
		add_action( 'init', array( $this, 'register_department_post_type' ) );
        add_filter( 'manage_edit-department_columns', array( $this, 'set_custom_edit_department_columns' ) );
        add_action( 'manage_department_posts_custom_column', array( $this, 'custom_department_column' ), 10, 2 );

        add_action( 'init', array( $this, 'register_yearlist_post_type' ) );
		add_filter( 'manage_edit-yearlist_columns', array( $this, 'set_custom_edit_yearlist_columns' ) );
        add_action( 'manage_yearlist_posts_custom_column', array( $this, 'custom_yearlist_column' ), 10, 2 );

        

        add_action( 'admin_menu', array( $this, 'add_admin_menus' ) );
		
         add_action( 'init', array( $this, 'register_question_post_type' ) );
		 add_filter( 'manage_edit-question_columns', array( $this, 'set_custom_edit_question_columns' ) );
         add_action( 'manage_question_posts_custom_column', array( $this, 'custom_question_column' ), 10, 2 );
         add_action( 'add_meta_boxes',  array( $this, 'ers_add_question_meta_box' ) );
		 add_action( 'save_post',  array( $this, 'ers_save_question_meta_box' ) );

		 add_action( 'add_meta_boxes',  array( $this, 'ers_add_department_meta_box' ) );
		 add_action( 'save_post',  array( $this, 'ers_save_department_meta_box' ) );

        add_action( 'init', array( $this, 'register_review_post_type' ) );
        add_filter( 'manage_edit-review_columns', array( $this, 'set_custom_edit_review_columns' ) );
        add_action( 'manage_review_posts_custom_column', array( $this, 'custom_review_column' ), 10, 2 );
        add_action( 'add_meta_boxes',  array( $this, 'ers_add_review_meta_box' ) );
		add_action( 'save_post',  array( $this, 'ers_save_review_meta_box' ) );


         
        add_action( 'admin_init', array( $this, 'register_settings' ) );

       
       //  add_action( 'admin_post_submit_feedback', array( $this, 'handle_form_submission' ) );

         add_action('wp_ajax_get_department_questions', array( $this, 'handle_get_department_questions' ));
         add_action('wp_ajax_nopriv_get_department_questions', array( $this, 'handle_get_department_questions' )); // For non-logged-in users
        
         add_action('wp_ajax_get_user_reviwer', array( $this, 'handle_get_user_reviwer' ));
         add_action('wp_ajax_nopriv_get_user_reviwer', array( $this, 'handle_get_user_reviwer' )); // For non-logged-in users
        
        // add_action('admin_footer',  array( $this, 'set_user_dropdown_value' ));
        add_action("wp_ajax_ers_sendEmailtousers", array( $this, 'ers_sendEmailtousers' ));
        add_action('wp_ajax_nopriv_ers_sendEmailtousers', array( $this, 'ers_sendEmailtousers' )); // For non-logged-in users

        add_action("wp_ajax_get_feedbackData", array( $this, 'get_feedbackData' ));
        add_action('wp_ajax_nopriv_get_feedbackData', array( $this, 'get_feedbackData' )); // For non-logged-in users
        
        add_action('wp_ajax_get_department_user', array( $this, 'handle_get_department_user' ));
         add_action('wp_ajax_nopriv_get_department_user', array( $this, 'handle_get_department_user' )); // For non-logged-in users
        

        
	}
    private function load_phpmailer() {
        // Ensure the files are only included once
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            require ABSPATH . WPINC . '/PHPMailer/Exception.php';
            require ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
            require ABSPATH . WPINC . '/PHPMailer/SMTP.php';
        }
    }


    

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tf-review-system-admin.css', array(), $this->version, 'all' );
        wp_enqueue_style( 'bootstrap-duallistbox', plugin_dir_url( __FILE__ ) . 'css/bootstrap-duallistbox.css', array(), $this->version, 'all' );
        wp_enqueue_style( 'bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
        // Enqueue the custom CSS file for dual listbox
        //wp_enqueue_style('jquery-dual-listbox-css', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-dual-listbox/1.0.4/jquery.dualListBox.min.css');

	}

	/**
	 * Register the JavaScript for the admin area.
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
        wp_enqueue_script('bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js', array('jquery'), null, true);
	//	wp_enqueue_script('jquery-ui-datepicker');
      //  wp_enqueue_style('jquery-ui-datepicker-style', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css');
        //wp_enqueue_script('my-plugin-script', plugin_dir_url(__FILE__) . 'js/my-plugin-script.js', array('jquery', 'jquery-ui-datepicker'), '1.0', true);
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tf-review-system-admin.js', array( 'jquery' ), $this->version, false );
        // Localize script to use AJAX URL in JS
      //  wp_localize_script('tf-review-system-admin', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
       wp_enqueue_script( 'jquery.bootstrap-duallistbox', plugin_dir_url( __FILE__ ) . 'js/jquery.bootstrap-duallistbox.js', array( 'jquery' ), $this->version, false );
       //wp_enqueue_script( 'bootstrap', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ), $this->version, false );
     
       // Enqueue the custom JavaScript file
       wp_enqueue_script('custom-js', plugin_dir_url(__FILE__) . 'js/custom.js', array('jquery'), null, true);
      // wp_enqueue_script('jquery-dual-listbox', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-dual-listbox/1.0.4/jquery.dualListBox.min.js', array('jquery'), null, true);

       
       // Localize script to use AJAX URL in JS
       wp_localize_script('custom-js', 'ajax_object', array(
           'ajax_url' => admin_url('admin-ajax.php'),
           'nonce'    => wp_create_nonce('my_nonce')
       ));

       wp_enqueue_media();

	}

    public function display_department_dropdown($user) {
         

    // Options for the dropdown
    $departments = get_posts(
        array(
            'post_type'  => 'department',
            'numberposts' => -1
        )
    );

        $selected_department = get_user_meta($user->ID, 'department', true);
        ?>
<h3>Department Information</h3>
<table class="form-table">
    <tr>
        <th><label for="department">Department</label></th>
        <td>
            <select name="department" id="department">
                <?php foreach ($departments as $value) : ?>
                <option value="<?php echo esc_attr($value->ID); ?>"
                    <?php selected($selected_department, $value->ID); ?>>
                    <?php echo esc_html($value->post_title); ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?php wp_nonce_field('my_nonce_action', 'my_user_department'); ?>
        </td>
    </tr>
</table>
<?php
    }

    public function save_department_dropdown($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        if (isset($_POST['my_user_department']) && wp_verify_nonce($_POST['my_user_department'], 'my_nonce_action')) {
        if (isset($_POST['department'])) {
            update_user_meta($user_id, 'department', sanitize_text_field($_POST['department']));
        }
        }
    }

	// public function create_department_table() {
    //     global $wpdb;
    //     $table_name = $wpdb->prefix . 'ers_departments';
    //     $charset_collate = $wpdb->get_charset_collate();

    //     $sql = "CREATE TABLE $table_name (
    //         id mediumint(9) NOT NULL AUTO_INCREMENT,
    //         title varchar(255) NOT NULL,
    //         PRIMARY KEY (id)
    //     ) $charset_collate;";

    //     require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    //     dbDelta( $sql );
    // }

	
	public function add_admin_menus() {

        
        add_submenu_page(
            'edit.php?post_type=review',
            __('Feedback', 'tf-review-functions'),
            __('Feedback', 'tf-review-functions'),
            'manage_options',
            'ers-feedback',
            array( $this, 'ers_feedback_page' )
             
        );

        // Submenu: Setting
		add_submenu_page(
            'edit.php?post_type=review',
            __( 'Setting', 'tf-review-functions' ),
            __( 'Setting', 'tf-review-functions' ),
            'manage_options',
            'ers-setting',
            array( $this, 'ers_setting_page' )
        );
        // Submenu: Setting

        add_submenu_page(
            'edit.php?post_type=review',
            __('Link', 'tf-review-functions'),
            __('Link', 'tf-review-functions'),
            'manage_options',
            'ers-link',
            array( $this, 'ers_link_page' )
             
        );

    }


	public function register_department_post_type() {
        $labels = array(
            'name'               => _x( 'Departments', 'post type general name', 'tf-review-functions' ),
            'singular_name'      => _x( 'Department', 'post type singular name', 'tf-review-functions' ),
            'menu_name'          => _x( 'Departments', 'admin menu', 'tf-review-functions' ),
            'name_admin_bar'     => _x( 'Department', 'add new on admin bar', 'tf-review-functions' ),
            'add_new'            => _x( 'Add New', 'department', 'tf-review-functions' ),
            'add_new_item'       => __( 'Add New Department', 'tf-review-functions' ),
            'new_item'           => __( 'New Department', 'tf-review-functions' ),
            'edit_item'          => __( 'Edit Department', 'tf-review-functions' ),
            'view_item'          => __( 'View Department', 'tf-review-functions' ),
            'all_items'          => __( 'All Departments', 'tf-review-functions' ),
            'search_items'       => __( 'Search Departments', 'tf-review-functions' ),
            'parent_item_colon'  => __( 'Parent Departments:', 'tf-review-functions' ),
            'not_found'          => __( 'No departments found.', 'tf-review-functions' ),
            'not_found_in_trash' => __( 'No departments found in Trash.', 'tf-review-functions' )
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'department' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-networking',
            'supports'           => array( 'title', 'editor' ),
        );

        register_post_type( 'department', $args );
    }


	public function set_custom_edit_department_columns($columns) {
        $columns['title'] = __( 'Department Name', 'tf-review-functions' );
        $columns['date'] = __( 'Date', 'tf-review-functions' );
        return $columns;
    }

    public function custom_department_column($column, $post_id) {
        switch ( $column ) {
            case 'title':
                echo get_the_title( $post_id );
                break;
        }
    }

    public function register_yearlist_post_type() {
        $labels = array(
            'name'               => _x( 'Yearlists', 'post type general name', 'tf-review-functions' ),
            'singular_name'      => _x( 'Yearlist', 'post type singular name', 'tf-review-functions' ),
            'menu_name'          => _x( 'Yearlists', 'admin menu', 'tf-review-functions' ),
            'name_admin_bar'     => _x( 'Yearlist', 'add new on admin bar', 'tf-review-functions' ),
            'add_new'            => _x( 'Add New', 'Yearlist', 'tf-review-functions' ),
            'add_new_item'       => __( 'Add New Yearlist', 'tf-review-functions' ),
            'new_item'           => __( 'New Yearlist', 'tf-review-functions' ),
            'edit_item'          => __( 'Edit Yearlist', 'tf-review-functions' ),
            'view_item'          => __( 'View Yearlist', 'tf-review-functions' ),
            'all_items'          => __( 'All Yearlists', 'tf-review-functions' ),
            'search_items'       => __( 'Search Yearlists', 'tf-review-functions' ),
            'parent_item_colon'  => __( 'Parent Yearlists:', 'tf-review-functions' ),
            'not_found'          => __( 'No yearlists found.', 'tf-review-functions' ),
            'not_found_in_trash' => __( 'No yearlists found in Trash.', 'tf-review-functions' )
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'yearlist' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-networking',
            'supports'           => array( 'title', 'editor' ),
        );

        register_post_type( 'yearlist', $args );
    }
    public function set_custom_edit_yearlist_columns($columns) {
        $columns['title'] = __( 'Yearlist Name', 'tf-review-functions' );
        $columns['date'] = __( 'Date', 'tf-review-functions' );
        return $columns;
    }

    public function custom_yearlist_column($column, $post_id) {
        switch ( $column ) {
            case 'title':
                echo get_the_title( $post_id );
                break;
        }
    }


    public function register_question_post_type() {
        $labels = array(
            'name'               => _x( 'Questions', 'post type general name', 'tf-review-functions' ),
            'singular_name'      => _x( 'Question', 'post type singular name', 'tf-review-functions' ),
            'menu_name'          => _x( 'Questions', 'admin menu', 'tf-review-functions' ),
            'name_admin_bar'     => _x( 'Question', 'add new on admin bar', 'tf-review-functions' ),
            'add_new'            => _x( 'Add New', 'question', 'tf-review-functions' ),
            'add_new_item'       => __( 'Add New Question', 'tf-review-functions' ),
            'new_item'           => __( 'New Question', 'tf-review-functions' ),
            'edit_item'          => __( 'Edit Question', 'tf-review-functions' ),
            'view_item'          => __( 'View Question', 'tf-review-functions' ),
            'all_items'          => __( 'All Questions', 'tf-review-functions' ),
            'search_items'       => __( 'Search Questions', 'tf-review-functions' ),
            'parent_item_colon'  => __( 'Parent Questions:', 'tf-review-functions' ),
            'not_found'          => __( 'No questions found.', 'tf-review-functions' ),
            'not_found_in_trash' => __( 'No questions found in Trash.', 'tf-review-functions' )
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'question' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-networking',
            'supports'           => array( 'title', 'editor' ),
        );

        register_post_type( 'question', $args );
    }



public function set_custom_edit_question_columns($columns) {
    $columns['title'] = __( 'Question Name', 'tf-review-functions' );
    $columns['date'] = __( 'Date', 'tf-review-functions' );
    return $columns;
}

public function custom_question_column($column, $post_id) {
    switch ( $column ) {
        case 'title':
            echo get_the_title( $post_id );
            break;
    }
}


	public function ers_add_department_meta_box() {
        add_meta_box(
            'ers_department_meta',
            __( 'Department Details', 'tf-review-functions' ),
            array( $this, 'ers_department_meta_box_callback' ),
            'department',
            'normal',
            'high'
        );
    }

	public function ers_department_meta_box_callback( $post ) {
        wp_nonce_field( 'ers_save_department_meta', 'ers_department_meta_nonce' );

        $department_code = get_post_meta( $post->ID, '_ers_department_code', true );

        echo '<label for="ers_department_code">';
        _e( 'Department Code', 'tf-review-functions' );
        echo '</label> ';
        echo '<input type="text" id="ers_department_code" name="ers_department_code" value="' . esc_attr( $department_code ) . '" size="25" />';
    }

 

function ers_save_department_meta_box( $post_id ) {
    if ( ! isset( $_POST['ers_department_meta_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['ers_department_meta_nonce'], 'ers_save_department_meta' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['ers_department_code'] ) ) {
        $department_code = sanitize_text_field( $_POST['ers_department_code'] );
        update_post_meta( $post_id, '_ers_department_code', $department_code );
    }
}



function handle_get_department_questions() {
   
    if (isset($_POST['department_id'])) {
        $department_id = sanitize_text_field($_POST['department_id']);
        check_ajax_referer('ers_save_review_meta', 'security');
        $args = array(
            'post_type' => 'question',
            'meta_query' => array(
                array(
                    'key' => '_department',
                    'value' => $department_id,
                    'compare' => '='
                )
            )
        );

        
        

        $questions = get_posts($args);
        
        $data = array();
        foreach ($questions as $question) {
            $data[] = array(
                'id' => $question->ID,
                'title' => $question->post_title,
            );
        }
        
        $user_args = array(
            'exclude' => array(1),
            'meta_query' => array(
                array(
                    'key' => 'department',
                    'value' => $department_id,
                    'compare' => '='
                )
            )
        );
        
        $userlists = get_users( $user_args );

        $udata = array();
        foreach ($userlists as $userlist) {
            $udata[] = array(
                'uid' => $userlist->ID,
                'username' => $userlist->user_nicename,
            );
        }

        $finalData[] = array(
            'questionData'=>$data,
            'udata'=>$udata
        );
       
        // Return data as JSON
        wp_send_json_success($finalData);
    } else {
        wp_send_json_error('Invalid department ID');
    }
}




function handle_get_user_reviwer() {
    if (isset($_POST['user_id'])) {
        check_ajax_referer('ers_save_review_meta', 'security');
        $user_id = sanitize_text_field($_POST['user_id']);
        $userlists = get_users(array('exclude' => array(1, $user_id)));
         
        $udata = array();
        foreach ($userlists as $userlist) {
            $udata[] = array(
                'uid' => $userlist->ID,
                'username' => $userlist->user_nicename,
            );
        }

        
        // Return data as JSON
        wp_send_json_success($udata);
    } else {
        wp_send_json_error('Invalid department ID');
    }
}
function handle_get_department_user(){

    if (isset($_POST['search_nonce']) && wp_verify_nonce($_POST['search_nonce'], 'search_reviews')) {
    if (isset($_POST['department_id'])) {
    $department_id = sanitize_text_field($_POST['department_id']);
    $user_args = array(
        'exclude' => array(1),
        'meta_query' => array(
            array(
                'key' => 'department',
                'value' => $department_id,
                'compare' => '='
            )
        )
    );
    
    $userlists = get_users( $user_args );

    $udata = array();
    foreach ($userlists as $userlist) {
        $udata[] = array(
            'uid' => $userlist->ID,
            'username' => $userlist->user_nicename,
        );
    }
    wp_send_json_success($udata);
    
    } else {
        wp_send_json_error('Invalid department ID');
    }
    }else {
        wp_send_json_error('Security check failed');
    }

}
//Question page meta fields

public function ers_add_question_meta_box() {
    add_meta_box(
        'ers_question_meta',
        __( 'Question Details', 'tf-review-functions' ),
        array( $this, 'ers_question_meta_box_callback' ),
        'question',
        'normal',
        'high'
    );

    
}

public function ers_question_meta_box_callback( $post ) {
    wp_nonce_field( 'ers_save_question_meta', 'ers_question_meta_nonce' );

    //$question_code = get_post_meta( $post->ID, '_ers_question_code', true );

    // Retrieve current value
    $department = get_post_meta($post->ID, '_department', true);

    // Options for the dropdown
    $departments = get_posts(
        array(
            'post_type'  => 'department',
            'numberposts' => -1
        )
    );

    //echo "<pre>"; print_r($departments);

    //$departments = array('HR', 'Sales', 'Marketing', 'Development', 'Support');

    echo '<div class="dropdown-section"><label for="department">Select Department:</label>';
    echo '<select name="department" id="department">';
    foreach ($departments as $dept) {
        echo '<option value="' . esc_attr($dept->ID) . '"' . selected($department, $dept->ID, false) . '>' . esc_html($dept->post_title) . '</option>';
    }
    echo '</select></div>';


   // Retrieve current value
   $question_type_value = get_post_meta($post->ID, '_question_type_dropdown', true);
    
   // Static options for the dropdown
   $question_type_options = array('Yes/No', 'OpenEnded');

   echo '<div class="dropdown-section"><label for="question_type_dropdown">Select Type:</label>';
   echo '<select name="question_type_dropdown" id="question_type_dropdown">';
   foreach ($question_type_options as $option) {
       echo '<option value="' . esc_attr($option) . '"' . selected($question_type_value, $option, false) . '>' . esc_html($option) . '</option>';
   }
   echo '</select></div>';
   
 
}



function ers_save_question_meta_box( $post_id ) {

if ( ! isset( $_POST['ers_question_meta_nonce'] ) ) {
    return;
}
if ( ! wp_verify_nonce( $_POST['ers_question_meta_nonce'], 'ers_save_question_meta' ) ) {
    return;
}
if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return;
}
if ( ! current_user_can( 'edit_post', $post_id ) ) {
    return;
}

  
// Sanitize and save the data
if (isset($_POST['department'])) {
    update_post_meta($post_id, '_department', sanitize_text_field($_POST['department']));
}

if (isset($_POST['question_type_dropdown'])) {
    update_post_meta($post_id, '_question_type_dropdown', sanitize_text_field($_POST['question_type_dropdown']));
}

}



public function register_review_post_type() {
    $labels = array(
        'name'               => _x( 'Reviews', 'post type general name', 'tf-review-functions' ),
        'singular_name'      => _x( 'Review', 'post type singular name', 'tf-review-functions' ),
        'menu_name'          => _x( 'Reviews', 'admin menu', 'tf-review-functions' ),
        'name_admin_bar'     => _x( 'Review', 'add new on admin bar', 'tf-review-functions' ),
        'add_new'            => _x( 'Add New', 'Review', 'tf-review-functions' ),
        'add_new_item'       => __( 'Add New Review', 'tf-review-functions' ),
        'new_item'           => __( 'New Review', 'tf-review-functions' ),
        'edit_item'          => __( 'Edit Review', 'tf-review-functions' ),
        'view_item'          => __( 'View Review', 'tf-review-functions' ),
        'all_items'          => __( 'All Reviews', 'tf-review-functions' ),
        'search_items'       => __( 'Search Reviews', 'tf-review-functions' ),
        'parent_item_colon'  => __( 'Parent Reviews:', 'tf-review-functions' ),
        'not_found'          => __( 'No reviews found.', 'tf-review-functions' ),
        'not_found_in_trash' => __( 'No reviews found in Trash.', 'tf-review-functions' )
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'review' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-networking',
        'supports'           => array( 'title', 'editor' ),
    );

    register_post_type( 'review', $args );
}
public function set_custom_edit_review_columns($columns) {
    $columns['title'] = __( 'Review Name', 'tf-review-functions' );
    $columns['yearlist'] = __( 'Yearlist', 'tf-review-functions' );
    $columns['department'] = __( 'Department', 'tf-review-functions' );
    $columns['review_for'] = __( 'Review For', 'tf-review-functions' );
    $columns['peer_review'] = __( 'Peer Review', 'tf-review-functions' );
    $columns['end_date'] = __( 'End Date', 'tf-review-functions' );
    $columns['sendemail'] = __( 'Send Email', 'tf-review-functions' );
   // $columns['date'] = __( 'Date', 'tf-review-functions' );
    return $columns;
}

public function custom_review_column($column, $post_id) {
    switch ( $column ) {
        case 'title':
            echo get_the_title( $post_id );
            break;
        case 'yearlist':
            $year_id =  get_post_meta($post_id, '_year', true);
            $yearData = get_post($year_id);
            //echo '<pre>'; print_r($yearData);
            echo $yearData->post_title;
        break;
        case 'department':
            $department_id =  get_post_meta($post_id, '_department', true);
            $departmentData = get_post($department_id);
            echo $departmentData->post_title;
        break;
        case 'review_for':
            $user_id =  get_post_meta($post_id, '_user', true);
            if(!empty($user_id )){
            $u_data = get_user_by( 'id', $user_id );
            echo $u_data->user_nicename; } 
        break;
        case 'peer_review':
            $review_for_id =  get_post_meta($post_id, '_user', true);
            $reviewer_ids =  get_post_meta($post_id, '_reviewer', true); 
            if(!empty($review_for_id ) && is_array($reviewer_ids )){
            array_push($reviewer_ids, $review_for_id); 
             
            $emp_name = '';
			$i = 1;
            foreach ($reviewer_ids as $eid) :
                    $user = get_user_by('id', $eid);
					$emp_name .= $user->display_name . ',';
					$submitter = get_post_meta($post_id, '_user', true);
					global $wpdb;
	 
			        $value = $wpdb->get_row($wpdb->prepare("SELECT * FROM `".$wpdb->prefix ."tf_reviewsystem_feedback_status` WHERE `post_id` =%d AND peer_review_id=%d", $post_id, $eid)); 
					if(!empty( $value )){
                    $enddate_str = get_post_meta($post_id, '_enddt', true);
					$enddate = gmdate('Y-m-d', strtotime($enddate_str));
					$date1 = strtotime(gmdate('Y-m-d'));
					$date2 = strtotime($enddate);
					$dateDifference = ($date2 - $date1);
					$peeruser = get_user_by('id', $value->peer_review_id);
					if ($value->status == '1') {
						echo '<button type="button" class="btn success_link_green">' . esc_attr($peeruser->user_login) . '</button>';
					} elseif ($value->status == '0') {
						if ($dateDifference < 0 || $dateDifference == 0) {
							$linkurl = 'edit.php?post_type=review&page=ers-link&surveyreview_for=' . $peeruser->ID . '&post_id=' . $value->post_id . '&review_feedback_status=' . $value->reason_status . '';
							$nonce_url = wp_nonce_url($linkurl, 'my_nonce_action');
                            echo '<a class="pending_link" href="'.esc_url($nonce_url).'" class="btn" >' . esc_attr($peeruser->user_login) . '</a>';
						} else {
							echo '<button type="button" class="btn pending_link_red">' . esc_attr($peeruser->user_login) . '</button>';
						}
					}
                }
					$i++;

            endforeach;
          //  $u_data = get_user_by( 'id', $reviewer_id );
           // echo $u_data->user_nicename;
        }
        break;
        case 'end_date':
            $enddt =  get_post_meta($post_id, '_enddt', true);
             echo $enddt;
        break;
        case 'sendemail':
			$user_id = get_post_meta($post_id, '_user', true);
            $reviewer_ids = get_post_meta($post_id, '_reviewer', true);
            
            if(!empty($user_id )){
               
               
                if (is_array($reviewer_ids)) {    
                $reviewer_ids_implode = implode(',',$reviewer_ids);
                }else{
                    $reviewer_ids_implode = $reviewer_ids;
                }
			$mail_Sent = get_post_meta($post_id, 'mail_send_status', true);

			$smtp_host = get_option('tf_review_smtp_host_text');
			$smtp_port = get_option('tf_review_smtp_port_text');
			$smtp_username = get_option('tf_review_smtp_username_text');
			$smtp_password = get_option('tf_review_smtp_password_text');

			$enddate_str = get_post_meta($post_id, '_enddt', true);
            $enddate_str_explode = explode('-',$enddate_str);
            $new_enddate_str = $enddate_str_explode['2'].'-'.$enddate_str_explode['0'].'-'.$enddate_str_explode['1'];
			$enddate = gmdate('Y-m-d', strtotime($new_enddate_str));
			$date1 = strtotime(gmdate('Y-m-d'));
			$date2 = strtotime($enddate);
			$dateDifference = ($date2 - $date1);


			if (!empty($smtp_host) && !empty($smtp_port) && !empty($smtp_username) && !empty($smtp_password)) {
				 
				if ($mail_Sent == 'sent') {
					echo '<button class="reviewlist sent-mail" data-review_for_id="' . esc_attr($user_id) . '" data-peer_review_id="' . esc_attr($reviewer_ids_implode) . '" post_id="' . esc_attr($post_id) . '" style="cursor: not-allowed" disabled>Sent</button>';
				} else if ($dateDifference < 0 || $dateDifference == 0) {
					echo '<p style="color:red">Expire End Date</p>';
				}else{
					echo '<button class="reviewlist sendEmailbtn " data-review_for_id="' . esc_attr($user_id) . '" data-peer_review_id="' . esc_attr($reviewer_ids_implode) . '" post_id="' . esc_attr($post_id) . '">Send</button>';
				}
			} else {
			$linkurl = 'edit.php?post_type=review&page=ers-setting';
				echo '<a href="'.esc_url($linkurl).'"><p style="color:red">Please configure smtp settings</p></a>';
			}
			//echo $mail_Sent;
        }
			break;
    } 
}



//Review page meta fields

public function ers_add_review_meta_box() {
    add_meta_box(
        'ers_review_meta',
        __( 'Review Builder', 'tf-review-functions' ),
        array( $this, 'ers_review_meta_box_callback' ),
        'review',
        'normal',
        'high'
    );

    
}

public function ers_review_meta_box_callback( $post ) {
    wp_nonce_field( 'ers_save_review_meta', 'ers_review_meta_nonce' );

    //$question_code = get_post_meta( $post->ID, '_ers_question_code', true );
  // Retrieve current value
  
    $yearlist =  get_post_meta($post->ID, '_year', true);
    // Options for the dropdown
    $yearlists = get_posts(
        array(
            'post_type'  => 'yearlist',
            'numberposts' => -1
        )
    );
        
    echo '<div class="dropdown-section"><label for="yearlist">Year:</label>';
    echo '<select name="yearlist" id="yearlist">';
    foreach ($yearlists as $dept) {
        echo '<option value="' . esc_attr($dept->ID) . '"' . selected($yearlist, $dept->ID, false) . '>' . esc_html($dept->post_title) . '</option>';
    }
    echo '</select></div>';

    $startdt =  get_post_meta($post->ID, '_startdt', true);
    $enddt =  get_post_meta($post->ID, '_enddt', true);
   
    
    ?>
<script src="<?php //echo plugin_dir_url( __FILE__ ) . 'js/jquery-3.2.1.slim.min.js' ?>"></script>

<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<?php echo '<div class="datepicker-section row">
    <div class="col-md-6">
        <div class="date-section">
            <label>Start Date: </label>
            <div id="startdt-datepicker" class="input-group date" data-date-format="mm-dd-yyyy">
                <input class="form-control" type="text" name="startdt" value="'.$startdt.'" readonly />
                <span class="input-group-addon">
                    <i class="glyphicon glyphicon-calendar"></i>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="date-section">
            <label>End Date: </label>
            <div id="enddt-datepicker" class="input-group date" data-date-format="mm-dd-yyyy">
                <input class="form-control" type="text" name="enddt" value="'.$enddt.'" readonly />
                <span class="input-group-addon">
                    <i class="glyphicon glyphicon-calendar"></i>
                </span>
            </div>
        </div>
    </div>

</div>';
?>


<script src="https://code.jquery.com/jquery-3.6.1.min.js"
    integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous">
</script>


<script>
$(function() {

    $("#startdt-datepicker").datepicker({
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        title: "Geeksforgeeks datepicker"
    }).datepicker();

    $("#enddt-datepicker").datepicker({
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        title: "Geeksforgeeks datepicker"
    }).datepicker();
});
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
</script>

<?php 

    // Retrieve current value
    $department = get_post_meta($post->ID, '_department', true);

    // Options for the dropdown
    $departments = get_posts(
        array(
            'post_type'  => 'department',
            'numberposts' => -1
        )
    );
 

    echo '<div class="dropdown-section"><label for="department">Select Department:</label>';
    echo '<div class="dropdown-wrapper"><select name="department" id="department_select">';
    echo '<option value="0">--Please Select--</option>';
    foreach ($departments as $dept) {
        echo '<option value="' . esc_attr($dept->ID) . '"' . selected($department, $dept->ID, false) . '>' . esc_html($dept->post_title) . '</option>';
    }
    echo '</select></div></div>';


    ?>
<script src="<?php echo plugin_dir_url( __FILE__ ) . 'js/jquery.bootstrap-duallistbox.js' ?>"></script>
<?php 

 if(!empty($department)){

 // Retrieve current value
 $options = get_post_meta($post->ID, '_duallistbox_demo1', true);

    $args = array(
        'post_type' => 'question',
        'meta_query' => array(
            array(
                'key' => '_department',
                'value' => $department,
                'compare' => '='
            )
        )
    );
    
    $questions = get_posts($args);

    echo '<div class="dropdown-section" id="dropdown-section" ><select multiple="multiple" size="10" name="duallistbox_demo1[]"
    title="duallistbox_demo1[]" id="dual_listbox">';
     
    foreach ($questions as $question) {
    $selected = is_array($options) && in_array($question->ID, $options) ? 'selected' : '';
    echo '<option value="' . esc_attr($question->ID) . '" ' . $selected . '>' . esc_html($question->post_title) . '</option>';
    }
    echo '</select></div>';
}else{

    echo '<div class="dropdown-section" id="dropdown-section" ><select multiple="multiple" size="10" name="duallistbox_demo1[]"
    title="duallistbox_demo1[]" id="dual_listbox"></select></div>';
}
 
    $user_id = get_post_meta($post->ID, '_user', true);
   
    $args = array(
        'exclude' => array(1),
    );
    
    $userlists = get_users( $args );
  
    echo '<input type="hidden" name="userlist" value="'.$user_id.'">';
    echo '<div class="dropdown-section"><label for="userlist">Review For:</label>';
    if(!empty($user_id)){
        $u_data = get_user_by( 'id', $user_id );
        echo '&nbsp;<strong>'.$u_data->user_nicename.'</strong>';
 
    }else{
        echo '<div class="dropdown-wrapper"><select name="userlist" id="user_selectbox">';
    echo '<option>--Please Select--</option>';
    echo '</select></div>';
    }
   
    echo '</div>';

    $userlist = null;
    $args = array(
        'exclude' => array(1),
    );
    
    $userlists = get_users( $args );
    $reviewer_ids = get_post_meta($post->ID, '_reviewer', true);
   
    //echo '<input type="hidden" name="reviewerlist[]" value="'.$reviewer_ids.'">';
 
    echo '<div class="dropdown-section"><label for="reviewerlist">Select Reviewer:</label>';
    if(!empty($reviewer_ids)){
        $emp_name='';
        foreach ($reviewer_ids as $eid) :
            $user = get_user_by('id', $eid);
            $emp_name .= $user->display_name . ',';
        endforeach;
         
        echo '&nbsp;<strong>'.$emp_name.'</strong>';
    }else{
        echo '<div class="dropdown-wrapper"><select name="reviewerlist[]" id="reviewerlist" multiple>';
        echo '<option>--Please Select--</option>';
        echo '</select></div>';
    }
   echo '</div>';

                

  
}

 


function ers_save_review_meta_box( $post_id ) {
if ( ! isset( $_POST['ers_review_meta_nonce'] ) ) {
    return;
}
if ( ! wp_verify_nonce( $_POST['ers_review_meta_nonce'], 'ers_save_review_meta' ) ) {
    return;
}
if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return;
}
if ( ! current_user_can( 'edit_post', $post_id ) ) {
    return;
}

 // echo "<pre>"; print_r($_POST); die;
// Sanitize and save the data
if (isset($_POST['department'])) {
    update_post_meta($post_id, '_department', sanitize_text_field($_POST['department']));
}

if (isset($_POST['userlist'])) {
    update_post_meta($post_id, '_user', sanitize_text_field($_POST['userlist']));
}


if (isset($_POST['yearlist'])) {
    update_post_meta($post_id, '_year', sanitize_text_field($_POST['yearlist']));
}

if (isset($_POST['reviewerlist'])) {
    update_post_meta($post_id, '_reviewer', array_map('sanitize_text_field',$_POST['reviewerlist']));
}

if (isset($_POST['startdt'])) {
    update_post_meta($post_id, '_startdt', sanitize_text_field($_POST['startdt']));
}

if (isset($_POST['enddt'])) {
    update_post_meta($post_id, '_enddt', sanitize_text_field($_POST['enddt']));
}
if (isset($_POST['duallistbox_demo1'])) {
    update_post_meta($post_id, '_duallistbox_demo1', array_map('sanitize_text_field',$_POST['duallistbox_demo1']));
}

return;

}
 
// Link Page
public function ers_link_page() {
    ?>
<div class="wrap">

    <h2>Link Form</h2>


    <?php 
    // Check the nonce
	 if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'my_nonce_action')) {
        wp_die('Nonce verification failed');
    }else{

   

    $user_c = get_user_by('id', esc_attr($_GET['surveyreview_for']));
    $post_id = $_GET['post_id'];
    global $wpdb;
    if ($_GET['review_feedback_status'] == 0) {
        echo "<div class='message-reason-late'><h4 style='color:red'>" . $user_c->display_name . " didn't submitted the reason of late submission hance, you can not generate a new link.</h4></div>";
    } else {
        $feedback_para_table_name = $wpdb->prefix ."tf_reviewsystem_feedback_para";
        $feedback_status_table_name = $wpdb->prefix ."tf_reviewsystem_feedback_status";
      
        $sql_for_pera = $wpdb->get_results( $wpdb->prepare("select * from %s where post_id =%d",$feedback_para_table_name,$post_id) );
		
        $sql_for_msg = $wpdb->get_results( $wpdb->prepare("select * from %s where post_id =%d AND `peer_review_id` =%d",$feedback_status_table_name,$post_id,$user_c->ID) );
		
        // $sql_for_pera = "SELECT * FROM $feedback_para_table WHERE `post_id` = " . $post_id . "";
        // $pera = $wpdb->get_results($sql_for_pera);

        // $sql_for_msg = "SELECT * FROM `wp_feedback_status` WHERE `post_id` = " . $post_id . " AND `peer_review_id` = " . $user_c->ID . "";
        // $msg = $wpdb->get_results($sql_for_msg); ?>

    <div class="message-reason">
 
        <h2>Here is the <strong><?php echo $user_c->display_name ?>'s</strong> reason of late submission</h2>
        <h4><?php echo $msg[0]->reason_message ?></h4>
 
        <form action="" method="post" id="reschedule_review">
            <?php wp_nonce_field('reschedule_review', 'reschedule_nonce'); ?>
            <input type="hidden" name="user_email" value="<?php echo $user_c->user_email; ?>" class="peer_email">
            <input type="hidden" name="post_id" value="<?php echo $post_id ?>">
            <input type="hidden" name="ajaxurl" value="<?php echo admin_url('admin-ajax.php'); ?>">
            <input type="hidden" name="user_token" value="<?php echo $pera[0]->token; ?>">
            <input type="hidden" name="peer_review_id" value="<?php echo $user_c->ID; ?>">
            <input type="hidden" name="review_for" value="<?php echo $pera[0]->review_for; ?>">
            <input type="submit" class="btn btn-primary send_new_link" id="send_new_link" name="submit"
                value="Generate Link">
        </form>
    </div>

<?php }
 }
   ?></div><?php 

}
 
	 // Feedback Page
	 public function ers_feedback_page() {
?>
<div class="wrap">

    <h2>Feedback Form</h2>
    <form id="search-form">
        <?php wp_nonce_field('search_reviews', 'search_nonce'); ?>

        <?php 
                //$yearlist = get_post_meta($post->ID, '_yearlist', true);

                $yearlist = null;
                // Options for the dropdown
                $yearlists = get_posts(
                    array(
                        'post_type'  => 'yearlist',
                        'numberposts' => -1
                    )
                );
                echo '<div class="row mb-3">'; 
                echo '<div class="dropdown-section col"><label for="yearlist">Year:</label>';
                echo '<select name="yearlist" id="yearlist">';
                foreach ($yearlists as $dept) {
                    echo '<option value="' . esc_attr($dept->ID) . '"' . selected($yearlist, $dept->ID, false) . '>' . esc_html($dept->post_title) . '</option>';
                }
                echo '</select></div>';
                ?>

        <?php 
                //$department = get_post_meta($post->ID, '_department', true);
                $department = null;
                // Options for the dropdown
                $departments = get_posts(
                    array(
                        'post_type'  => 'department',
                        'numberposts' => -1
                    )
                );
                 
                echo '<div class="dropdown-section col"><label for="department">Department:</label>';
                echo '<select name="department" id="ers_department">';
                foreach ($departments as $dept) {
                    echo '<option value="' . esc_attr($dept->ID) . '"' . selected($department, $dept->ID, false) . '>' . esc_html($dept->post_title) . '</option>';
                }
                echo '</select></div>';
                ?>


        <?php 
                //$yearlist = get_post_meta($post->ID, '_yearlist', true);
                $userlist = null;
                $args = array(
                    'exclude' => array(1),
                );
                
                $userlists = get_users( $args );

               
                echo '<div class="dropdown-section col"><label for="userlist">Employee Name:</label>';
                echo '<select name="userlist" id="userlist">';
                foreach ($userlists as $dept) {
                    echo '<option value="' . esc_attr($dept->ID) . '"' . selected($userlist, $dept->ID, false) . '>' . esc_html($dept->user_nicename) . '</option>';
                }
                echo '</select></div></div>';
                 echo '<div class="row"><div class="col text-center">';
                echo '<input type="button" id="submit_feedback" value="Submit Feedback" class="button button-primary">';
                echo '</div></div>';
                //echo '</div>';
                ?>

        <?php //submit_button('Submit Feedback'); ?>
    </form>
    <section class="employee-data-response">
        <div class="table-responsive" id="employee-data-response"></div>
    </section>

</div>
<?php

    }

    // public function handle_form_submission() {
    //     if ( isset( $_POST['department'], $_POST['yearlist'], $_POST['userlist'] ) ) {
            
    //         global $wpdb;
    //         $table_name = $wpdb->prefix . 'feedback';

    //         $department = sanitize_text_field( $_POST['department'] );
    //         $yearlist = sanitize_text_field( $_POST['yearlist'] );
    //         $userlist = sanitize_text_field( $_POST['userlist'] );

    //         // $query = "SELECT * FROM $table_name WHERE 1=1";

    //         // if ( $department ) {
    //         //     $query .= $wpdb->prepare( " AND department = %s", $department );
    //         // }

    //         // if ( $year ) {
    //         //     $query .= $wpdb->prepare( " AND year = %d", $year );
    //         // }

    //         // $results = $wpdb->get_results( $query );
            
    //         // echo '<h2>Feedback Results</h2>';
    //         // echo '<table class="widefat striped">';
    //         // echo '<thead><tr><th>ID</th><th>Department</th><th>Year</th><th>Feedback</th></tr></thead>';
    //         // echo '<tbody>';
    //         // foreach ( $results as $row ) {
    //         //     echo '<tr>';
    //         //     echo '<td>' . esc_html( $row->id ) . '</td>';
    //         //     echo '<td>' . esc_html( $row->department ) . '</td>';
    //         //     echo '<td>' . esc_html( $row->year ) . '</td>';
    //         //     echo '<td>' . esc_html( $row->feedback ) . '</td>';
    //         //     echo '</tr>';
    //         // }
    //         // echo '</tbody></table>';

    //         // $wpdb->insert(
    //         //     $table_name,
    //         //     array(
    //         //         'department' => sanitize_text_field( $_POST['department'] ),
    //         //         'year' => intval( $_POST['year'] ),
    //         //         'feedback' => sanitize_textarea_field( $_POST['feedback'] ),
    //         //     )
    //         // );
    //         // wp_redirect( admin_url( 'options-general.php?page=tf-review-settings&message=success' ) );
    //         // exit;
    //     }
    // }
     

    public function register_settings() {
        register_setting( 'tf-review-settings-group', 'tf_review_smtp_host_text' );
        register_setting( 'tf-review-settings-group', 'tf_review_smtp_port_text' );
        register_setting( 'tf-review-settings-group', 'tf_review_smtp_username_text' );
        register_setting( 'tf-review-settings-group', 'tf_review_smtp_password_text' );
        register_setting( 'tf-review-settings-group', 'tf_review_hr_name_text' );
        register_setting( 'tf-review-settings-group', 'tf_review_hr_email_text' );

        register_setting( 'tf-review-settings-group', 'tf_review_fav_icon' );
        register_setting( 'tf-review-settings-group', 'tf_review_site_logo' );


        add_settings_section(
            'tf_review_main_section', // Section ID
            'Main Settings', // Title
            null, // Callback to output section text
            'tf-review-settings' // Page
        );

        add_settings_field(
            'tf_review_smtp_host_text', // Field ID
            'SMTP Host', // Title
            array( $this, 'option_smtp_host_text_callback' ), // Callback to display the field
            'tf-review-settings', // Page
            'tf_review_main_section' // Section ID
        );

        add_settings_field(
            'tf_review_smtp_port_text', // Field ID
            'SMTP Port', // Title
            array( $this, 'option_smtp_port_text_callback' ), // Callback to display the field
            'tf-review-settings', // Page
            'tf_review_main_section' // Section ID
        );

        add_settings_field(
            'tf_review_smtp_username_text', // Field ID
            'SMTP Username', // Title
            array( $this, 'option_smtp_username_text_callback' ), // Callback to display the field
            'tf-review-settings', // Page
            'tf_review_main_section' // Section ID
        );
        add_settings_field(
            'tf_review_smtp_password_text', // Field ID
            'SMTP Password', // Title
            array( $this, 'option_smtp_password_text_callback' ), // Callback to display the field
            'tf-review-settings', // Page
            'tf_review_main_section' // Section ID
        );

        add_settings_field(
            'tf_review_hr_name_text', // Field ID
            'HR Name', // Title
            array( $this, 'option_hr_name_text_callback' ), // Callback to display the field
            'tf-review-settings', // Page
            'tf_review_main_section' // Section ID
        );

        add_settings_field(
            'tf_review_hr_email_text', // Field ID
            'HR Email', // Title
            array( $this, 'option_hr_email_text_callback' ), // Callback to display the field
            'tf-review-settings', // Page
            'tf_review_main_section' // Section ID
        );
         
       add_settings_field(
                'tf_review_fav_icon', // Field ID
                'Fav Icon', // Title
                array($this, 'option_tf_review_fav_icon_callback'), // Callback to display the field
                'tf-review-settings', // Page
                'tf_review_main_section' // Section ID
            );

            add_settings_field(
                'tf_review_site_logo', // Field ID
                'Site Logo', // Title
                array($this, 'option_tf_review_site_logo_callback'), // Callback to display the field
                'tf-review-settings', // Page
                'tf_review_main_section' // Section ID
            );
            

    }

    public function ers_setting_page() {
        ?>
<div class="wrap">
    <h1>TF Review Settings</h1>
    <form method="post" action="options.php">
        <?php
                settings_fields( 'tf-review-settings-group' );
                do_settings_sections( 'tf-review-settings' );
                submit_button();
                ?>
    </form>
</div>
<?php
    }

   
    public function option_smtp_host_text_callback() {
        $option = get_option( 'tf_review_smtp_host_text' );
        echo "<input type='text' name='tf_review_smtp_host_text' value='" . esc_attr( $option ) . "' />";
    }

    public function option_smtp_port_text_callback() {
        $option = get_option( 'tf_review_smtp_port_text' );
        echo "<input type='text' name='tf_review_smtp_port_text' value='" . esc_attr( $option ) . "' />";
    }

    public function option_smtp_username_text_callback() {
        $option = get_option( 'tf_review_smtp_username_text' );
        echo "<input type='text' name='tf_review_smtp_username_text' value='" . esc_attr( $option ) . "' />";
    }

    public function option_smtp_password_text_callback() {
        $option = get_option( 'tf_review_smtp_password_text' );
        echo "<input type='password' name='tf_review_smtp_password_text' value='" . esc_attr( $option ) . "' />";
    }

    public function option_hr_name_text_callback() {
        $option = get_option( 'tf_review_hr_name_text' );
        echo "<input type='text' name='tf_review_hr_name_text' value='" . esc_attr( $option ) . "' />";
    }


    public function option_hr_email_text_callback() {
        $option = get_option( 'tf_review_hr_email_text' );
        echo "<input type='text' name='tf_review_hr_email_text' value='" . esc_attr( $option ) . "' />";
    }

    public function option_tf_review_fav_icon_callback() {
        $option = get_option('tf_review_fav_icon');
        ?>
<input type="text" id="tf_review_fav_icon" name="tf_review_fav_icon"
    value="<?php echo isset($option) ? esc_attr($option) : ''; ?>" />
<input type="button" class="button" id="tf_review_fav_icon_button" value="Upload Image" />
<div id="tf_review_fav_icon_preview" style="margin-top: 10px;">
    <?php if (!empty($option)): ?>
    <img src="<?php echo esc_url($option); ?>" style="max-width: 150px; height: auto;">
    <?php endif; ?>
</div>
<?php
    }

    public function option_tf_review_site_logo_callback() {
        $option = get_option('tf_review_site_logo');
        ?>
<input type="text" id="tf_review_site_logo" name="tf_review_site_logo"
    value="<?php echo isset($option) ? esc_attr($option) : ''; ?>" />
<input type="button" class="button" id="tf_review_site_logo_button" value="Upload Image" />
<div id="tf_review_site_logo_preview" style="margin-top: 10px;">
    <?php if (!empty($option)): ?>
    <img src="<?php echo esc_url($option); ?>" style="max-width: 150px; height: auto;">
    <?php endif; ?>
</div>
<?php
    }



public function ers_sendEmailtousers()
{
	global $wpdb;
   // echo "<pre>"; print_r($_POST); die;
    // Check the nonce
    if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'my_nonce') ) {
        wp_send_json_error('Invalid nonce');
        die();
    } 
	$post_id = sanitize_text_field($_POST['post_id']);
	add_post_meta($post_id, 'mail_send_status', 'sent');
	$review_for_id = sanitize_text_field($_POST['review_for_id']);
	$peer_review_id = sanitize_text_field($_POST['peer_review_id']);
	$peer_review = explode(',', $peer_review_id);
	array_push($peer_review, $review_for_id);
    
    $department =  get_post_meta($post_id, '_department', true);
	$yearlist = get_post_meta($post_id, '_year', true);
    $question_data = get_post_meta($post_id, '_duallistbox_demo1', true);
   // echo "<pre>"; print_r($question_data); die;
	$enddate_str = get_post_meta($post_id, '_enddt', true);

    $enddate_str_explode = explode('-',$enddate_str);
    $new_enddate_str = $enddate_str_explode['2'].'-'.$enddate_str_explode['0'].'-'.$enddate_str_explode['1'];
    $review_enddt = gmdate('Y-m-d', strtotime($new_enddate_str));
 
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < 10; $i++) {
		$randomString .= $characters[wp_rand(0, $charactersLength - 1)];
	}
	$table_name = $wpdb->prefix ."tf_reviewsystem_feedback_para";
	$results = $wpdb->get_row($wpdb->prepare("SELECT * FROM %s WHERE post_id = %d", $table_name,$post_id));
	//echo $wpdb->last_query();
    //echo "<pre>"; print_r($results); die;
	if ($results !== null && (is_array($results) || $results instanceof Countable) && count($results) > 0) {
		$randomString = $results->token;
	} else {

        

		$wpdb->insert($table_name, array(
			"post_id" => $post_id,
			"year_id" => $yearlist,
			"department_id" => $department,
			"review_for" => $review_for_id,
			"peer_review" => $peer_review_id,
			"questionlist" => $question_data,
			"review_enddt" => $review_enddt,
			"token" => $randomString,
		));
 
	}

        $feedback_status_table_name = $wpdb->prefix ."tf_reviewsystem_feedback_status";
        $result = $wpdb->get_results( $wpdb->prepare("select * from %s where post_id =%d",$feedback_status_table_name,$post_id) );
			
        if (count($result) > 0) {
            $wpdb->delete($feedback_status_table_name, array('post_id' => $post_id));
        }

	for ($i = 0; $i < count($peer_review); $i++) {
 
        $wpdb->insert($feedback_status_table_name, array(
			"post_id" => $post_id,
			"peer_review_id" => $peer_review[$i],
			"status" => '0',
			"reason_status" => '0',
			"reason_message" => '',
			"miss_review" => '0'
		));
        
		$user = get_user_by('id', $peer_review[$i]);
		 
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
		$format_review_enddt = gmdate("d-M-Y", strtotime($review_enddt));
		//$post = get_post($post_id);
		$slug = 'employee-review';
		$url = site_url() . '/index.php' . '/u' . '/' . $slug . '?t=' . urlencode(base64_encode($token)) . '&e=' . urlencode(base64_encode($peer_review[$i])) . '&d=' . base64_encode($review_enddt) . '&tu=' . $review_for_id;
        $nonce_url = wp_nonce_url($url, 'my_nonce_action');
        
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
			'{url}' => esc_url($nonce_url),
			'{target_user}' => $target_user,
			'{end_date}' => $format_review_enddt,
		);

		$subject = 'Annual Review Process - Peer Review';
		$mail_message = strtr($template_content, $vars);
		$email = $user->user_email;
		//$headers = array('Content-Type: text/html; charset=UTF-8');


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
		$mail->send();
	}
}



public function get_feedbackData()
{
	global $wpdb;
    if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'my_nonce') ) {
        wp_send_json_error('Invalid nonce');
        die();
    } 
    if (isset($_POST['year_id']) && isset($_POST['department_id']) && isset($_POST['user_id'])) {

        $year_id = sanitize_text_field($_POST['year_id']);
        $department_id = sanitize_text_field($_POST['department_id']);
        $review_for = sanitize_text_field($_POST['user_id']);
        
        // Prepare table names separately
        $table_responces = $wpdb->prefix . 'tf_reviewsystem_responces';
        $table_responce_details = $wpdb->prefix . 'tf_reviewsystem_responce_details';
        $table_feedback_para = $wpdb->prefix . 'tf_reviewsystem_feedback_para';
        
        // Execute query
        $myrow = $wpdb->get_results($wpdb->prepare(
            
            "SELECT res_detail.*, res.review_for, res.review_by, res.comments, para.post_id
            FROM %s AS res 
            LEFT JOIN %s AS res_detail 
            ON res.id = res_detail.tf_reviewsystem_responce_id 
            LEFT JOIN %s AS para 
            ON para.post_id = res.tf_reviewsystem_id 
            WHERE para.year_id = %d AND para.department_id = %d AND para.review_for = %d 
            AND res_detail.responce != '' 
            ORDER BY res_detail.id ASC", 
            $table_responces, 
            $table_responce_details, 
            $table_feedback_para, 
            $year_id, 
            $department_id, 
            $review_for
        ));
      

            $question_data = array();
            $que_array = array();
            $reviewer_data = array();
            $answer_array = array();
            $reviewer_array = array();
            $i = 1;
            $answer = array();
//echo "<pre>"; print_r($myrow); 
//echo $wpdb->last_query;
            for ($z = 0; $z < count($myrow); $z++) {
                
                   $que_id = $myrow[$z]->question_id;
                   if (!in_array($que_id, $que_array)) {
                       $que_array[] = $que_id;
                       $question_data[] = get_the_title($myrow[$z]->question_id);
                   }

                $userData = get_userdata( $myrow[$z]->review_by );
              
                if (!in_array($userData->display_name, $reviewer_array)) {
                    $reviewer_array[] = $userData->display_name;
                    $reviewer_ids[] = $userData->ID;
                }

                $reviewer_data['reviewer'] = $reviewer_array;
                $reviewer_data['reviewer_id'] = $reviewer_ids;
                $cnt = count($reviewer_array) - 1;
                if ($myrow[$z]->responce != '') {
                    $answer[$cnt][] = $myrow[$z]->responce;
                }

                if ($myrow[$z]->score != '') {
                    $answer[$cnt][] = $myrow[$z]->score;
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

            if (isset($reviewer_data['reviewer_id']) && is_array($reviewer_data['reviewer_id']) && in_array($review_for, $reviewer_data['reviewer_id'])) {
                $key = array_search($review_for, $reviewer_data['reviewer_id']);
            }
         //   $key = null;

            $unsetarray = isset($answer[$key]) ? $answer[$key] : null;
            unset($answer[$key]);
            if (is_array($answer)) {
                array_unshift($answer, $unsetarray);
            }


            unset($reviewer_data['reviewer_id'][$key]);
          
            if (isset($reviewer_data['reviewer_id']) && is_array($reviewer_data['reviewer_id'])) {
                array_unshift($reviewer_data['reviewer_id'], $review_for);
            }
            $unset_reviewer_name = null; // Initialize $unset_reviewer_name before using it

            // Check if 'reviewer' key exists in $reviewer_data
            if (isset($reviewer_data['reviewer']) && is_array($reviewer_data['reviewer'])) {
                // Your code that sets the value of $key

                // Check if the key exists before accessing it
                if (isset($reviewer_data['reviewer'][$key])) {
                    $unset_reviewer_name = $reviewer_data['reviewer'][$key];
                }
            }
            unset($reviewer_data['reviewer'][$key]);
            
            if (isset($reviewer_data['reviewer']) && is_array($reviewer_data['reviewer'])) {
                array_unshift($reviewer_data['reviewer'], $unset_reviewer_name);
            }
            $no_data = 0;
                if (isset($reviewer_data['reviewer']) && is_array($reviewer_data['reviewer']) && count($reviewer_data['reviewer']) > 0) {
					$no_data++;
               
                    $html1 .= '<table class="table table-bordered table-hover question-table"><tr>';
					$html1 .= '<td width="30%">';
							$html1 .= ' <table class="fdtable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col">Questions</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
								foreach ($question_data as $ques_array) {
									$html1 .= '<tr><td>'.$ques_array.'</td></tr>';
								}
								$html1 .= '</tbody></table>';
								$html1 .= '</td>';
                          		  for ($r = 0; $r < count($reviewer_data['reviewer_id']); $r++) {
									$html1 .= '<td width="10%">';
									$html1 .= '<table class="fdtable">';
									$html1 .= '<thead class="thead-dark">';
									$html1 .= '<tr><th scope="col">';
												
									if ($review_for == $reviewer_data['reviewer_id'][$r]) {
										$self_html= "<span class='reviewer_label' >(Self Review) </span>";
									} else {
										$self_html= "<span class='reviewer_label'>(Peer Reviews " . $r . ")</span>";
									} 
									$html1 .=$reviewer_data['reviewer'][$r]. $self_html;
									$html1 .=' </th> </tr>';
									$html1 .=' </thead>';
									$html1 .=' <tbody>';
                                              
									if (isset($answer[$r]) && is_array($answer[$r])) {
										$ans_html ='';
										for ($l1 = 0; $l1 < count($answer[$r]); $l1++) {
											$ans_html .='<tr><td class="center">'.$answer[$r][$l1].'</td></tr>';
										}
										$html1 .= $ans_html;
									}  
									$html1 .=' </tbody></table> </td>';
									}
									$html1 .=' <td width="5%"><table class="fdtable">';
									$html1 .='  <thead class="thead-dark">
											<tr>
												<th scope="col">Total</th>
											</tr>
										</thead>';
                                    $html1 .=' <tbody>';
                                         for ($l1 = 0; $l1 < count($sumArray); $l1++) {
											$html1 .='  <tr><td class="center">'.$sumArray[$l1].'</td></tr>';
                                        }  
										$html1 .='</tbody> </table></td>';
							$html1 .=' <td width="5%">
                                <table class="fdtable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col">Average</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                          for ($l1 = 0; $l1 < count($sumArray); $l1++) {
                                           
                                            $avg = ($sumArray[$l1] / (count($reviewer_data['reviewer']) - $cntZero_Arr[$l1]));
                                            $final_avg = round($avg, 2);
                                            if ($final_avg < 50) { 
                                                $html_AVG ='<tr style="background-color: #FFCCCB !important;">
                                                    <td class="center">'.round($avg, 2).'</td>
                                                </tr>';
                                             } else {
                                            
                                                $html_AVG = '<tr>
                                                    <td class="center">'.round($avg, 2).'</td>
                                                </tr>';
                                         }
										 $html1 .= $html_AVG;
                                        }
										$html1 .= '</tbody></table></td>';
										$html1 .= '</tr></table>';
                } 
 
               
                  // Execute query
        $myrow1 = $wpdb->get_results($wpdb->prepare(
            
            "SELECT res_detail.*, res.review_for, res.review_by, res.comments, para.post_id
            FROM %s AS res 
            LEFT JOIN %s AS res_detail 
            ON res.id = res_detail.tf_reviewsystem_responce_id 
            LEFT JOIN %s AS para 
            ON para.post_id = res.tf_reviewsystem_id 
            WHERE para.year_id = %d AND para.department_id = %d AND para.review_for = %d 
            AND res_detail.score != '' 
            ORDER BY res_detail.id ASC", 
            $table_responces, 
            $table_responce_details, 
            $table_feedback_para, 
            $year_id, 
            $department_id, 
            $review_for
        ));
      

            $question_data1 = array();
            $que_array1 = array();
            $reviewer_data1 = array();
            $answer_array1 = array();
            $reviewer_array1 = array();
            $i = 1;
            $answer = array();
//echo "<pre>"; print_r($myrow1); 
            for ($z = 0; $z < count($myrow1); $z++) {
                
                   $que_id = $myrow1[$z]->question_id;
                   if (!in_array($que_id, $que_array1)) {
                       $que_array1[] = $que_id;
                       $question_data1[] = get_the_title($myrow1[$z]->question_id);
                   }

                $userData = get_userdata( $myrow1[$z]->review_by );
              
                if (!in_array($userData->display_name, $reviewer_array1)) {
                    $reviewer_array1[] = $userData->display_name;
                    $reviewer_ids1[] = $userData->ID;
                    $comment_array1[] = $myrow1[$z]->comments;
                }

                $reviewer_data1['reviewer'] = $reviewer_array1;
                $reviewer_data1['reviewer_id'] = $reviewer_ids1;
                $reviewer_data1['comment'] = $comment_array1;

                $cnt = count($reviewer_array1) - 1;
              
                if ($myrow1[$z]->score != '') {
                    $answer_array1[$cnt][] = $myrow1[$z]->score;
                }
             }
            

            if (isset($reviewer_data1['reviewer_id']) && is_array($reviewer_data1['reviewer_id']) && in_array($review_for, $reviewer_data1['reviewer_id'])) {
                $key = array_search($review_for, $reviewer_data1['reviewer_id']);
            }
         //   $key = null;

            $unsetarray = isset($answer[$key]) ? $answer[$key] : null;
            unset($answer[$key]);
            if (is_array($answer)) {
                array_unshift($answer, $unsetarray);
            }


            unset($reviewer_data1['reviewer_id'][$key]);
          
            if (isset($reviewer_data1['reviewer_id']) && is_array($reviewer_data1['reviewer_id'])) {
                array_unshift($reviewer_data1['reviewer_id'], $review_for);
            }
            $unset_reviewer_name = null; // Initialize $unset_reviewer_name before using it

            // Check if 'reviewer' key exists in $reviewer_data1
            if (isset($reviewer_data1['reviewer']) && is_array($reviewer_data1['reviewer'])) {
                // Your code that sets the value of $key

                // Check if the key exists before accessing it
                if (isset($reviewer_data1['reviewer'][$key])) {
                    $unset_reviewer_name = $reviewer_data1['reviewer'][$key];
                }
            }
            unset($reviewer_data1['reviewer'][$key]);
            
            if (isset($reviewer_data1['reviewer']) && is_array($reviewer_data1['reviewer'])) {
                array_unshift($reviewer_data1['reviewer'], $unset_reviewer_name);
            }
            $unset_reviewer_comment = $reviewer_data1['comment'][$key];
            unset($reviewer_data1['comment'][$key]);
            
            if (is_array($reviewer_data1['comment'])) {
                array_unshift($reviewer_data1['comment'], $unset_reviewer_comment);
            }
            $no_data = 0;
                if (isset($reviewer_data1['reviewer']) && is_array($reviewer_data1['reviewer']) && count($reviewer_data1['reviewer']) > 0) {
					$no_data++;
               
                    $html1 .= '<table class="table table-bordered table-hover question-table"><tr>';
					$html1 .= '<td width="30%">';
							$html1 .= ' <table class="fdtable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col">Questions</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
								foreach ($question_data1 as $ques_array) {
									$html1 .= '<tr><td>'.$ques_array.'</td></tr>';
								}
                                $html1 .='<tr class="comment more">
										   <td>Additional Comments</td>
									   </tr>';
								$html1 .= '</tbody></table>';
								$html1 .= '</td>';
                          		  for ($r = 0; $r < count($reviewer_data1['reviewer_id']); $r++) {
                                    $comments_text = $reviewer_data1['comment'];
									$html1 .= '<td width="10%">';
									$html1 .= '<table class="fdtable">';
									$html1 .= '<thead class="thead-dark">';
									$html1 .= '<tr><th scope="col">';
												
									if ($review_for == $reviewer_data1['reviewer_id'][$r]) {
										$self_html= "<span class='reviewer_label' >(Self Review) </span>";
									} else {
										$self_html= "<span class='reviewer_label'>(Peer Reviews " . $r . ")</span>";
									} 
									$html1 .=$reviewer_data1['reviewer'][$r]. $self_html;
									$html1 .=' </th> </tr>';
									$html1 .=' </thead>';
									$html1 .=' <tbody>';
                                              
                                    if (is_array($answer_array1[$r])) {
                                        for ($l1 = 0; $l1 < count($answer_array1[$r]); $l1++) {

                                  
                                         $html1 .='<tr>';
                                               if ($answer_array1[$r][$l1] == 'No') {
                                                 $html1 .=' <td class="center" style="color: red; font-weight:600;">'.$answer_array1[$r][$l1].'</td>';
                                                } else {  
                                                 $ans = $answer_array1[$r][$l1];
                                                 $html1 .='<td class="center">';
                                                                        if ($ans == '1') {
                                                                         $html1 .= "No";
                                                                        } elseif ($ans == '4') {
                                                                         $html1 .= "Yes";
                                                                        } else {
                                                                         $html1 .= $ans;
                                                                        } 
                                                                        $html1 .='</td>';
                                                }
                                                $html1 .=' </tr>';

                                                
                                         }
                                         $html1 .=' <tr>
											   <td class="center">
												   <a type="button" data-toggle="modal" data-target="#exampleModalCenter'. $reviewer_data1['reviewer_id'][$r].'">
													   Show Comment
												   </a>
												   <div class="modal fade" id="exampleModalCenter'.$reviewer_data1['reviewer_id'][$r].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
													   <div class="modal-dialog modal-dialog-centered" role="document">
														   <div class="modal-content">
															   <div class="modal-header" style="background-color: #65aee2;">';
																  
															   $html1 .='<p class="modle_head_name">'.$reviewer_data1['reviewer'][$r].' Comment</p>';
															   $html1 .='<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff;">
																	   <span aria-hidden="true">&times;</span>
																   </button>
															   </div>';
															   $html1 .='<div class="modal-body" style="max-height: 200px; word-break: break-word;">
																   <p class="comment-popup">';
																	    
																	   if (!empty($comments_text[$r])) {
																		   $comments_text = $comments_text[$r];
																		   //if($comments_text[$r] == '1'){ echo "Yes";}elseif($comments_text[$r] == '4'){ echo "No";}else{ $comments_text[$r];}
																	   } else {
																		$comments_text = "No Comments";
																	   }
																	   
																	   $html1 .= $comments_text;
																	   $html1 .=' </p>';
																	   $html1 .='</div>';
																	   $html1 .='<div class="modal-footer">
																   <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
															   </div>';
															   $html1 .='</div>
													   </div>
												   </div>

											   </td>
										   </tr>';
                                    } 
									$html1 .=' </tbody></table> </td>';
									}
                                    $html1 .=' <td width="5%">
                                    <table class="fdtable_choice">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th scope="col" class="trasparant-th">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
     
                                        </tbody>
                                    </table>
                                </td>';
                                $html1 .=' <td width="5%">
                                    <table class="fdtable_choice">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th scope="col" class="trasparant-th">Average</th>
                                            </tr>
                                        </thead>
     
                                    </table>
                                </td>';
										$html1 .= '</tr></table>';
                } 
 
               

                if($no_data == 0){
					$html1 =  '<div class="center no-data-available"><h2 style="color:red"> No data available </h2></div>';
				}
                                 
        // Return data as JSON
        wp_send_json_success($html1);
    } else {
        wp_send_json_error('Invalid department ID');
    }
 
  
}
 

// Create the feedback table on plugin activation
 /*

public function create_feedback_table() {
     
            global  $table_prefix, $wpdb;
			$feedback_paraTable = $table_prefix . 'tf_reviewsystem_feedback_para';
			if ($wpdb->get_var("show tables like '$feedback_paraTable'") != $feedback_paraTable) {
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
		
				//$sql = "ALTER TABLE $feedback_paraTable ADD PRIMARY KEY (`para_id`)";
				$wpdb->query($wpdb->prepare("ALTER TABLE $feedback_paraTable ADD PRIMARY KEY (`para_id`)"));

				//$sql = "ALTER TABLE $feedback_paraTable MODIFY `para_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1";
				$wpdb->query($wpdb->prepare("ALTER TABLE $feedback_paraTable MODIFY `para_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1"));
			}

			$feedback_statusTable = $table_prefix . 'tf_reviewsystem_feedback_status';
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

				//$sql = "ALTER TABLE $feedback_statusTable ADD PRIMARY KEY (`id`)";
				$wpdb->query($wpdb->prepare("ALTER TABLE $feedback_statusTable ADD PRIMARY KEY (`id`)"));

				//$sql = "ALTER TABLE $feedback_statusTable MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1";
				$wpdb->query($wpdb->prepare("ALTER TABLE $feedback_statusTable MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1"));

			}

            $responces_table = $wpdb->prefix . 'tf_reviewsystem_responces';
            if ($wpdb->get_var("SHOW TABLES LIKE '$responces_table'") != $responces_table) {
                $sql = "CREATE TABLE " . $responces_table . " ( id INT NOT NULL AUTO_INCREMENT, tf_reviewsystem_id INT NOT NULL, company_id INT NOT NULL,location_id INT NOT NULL,review_for INT NOT NULL,review_by INT NOT NULL,`comments` longtext NOT NULL, is_offline BOOLEAN,datetime TIMESTAMP, PRIMARY KEY  (id) ) " . $charset_collate . ";";
                dbDelta($sql);
            }
            $responce_details_table = $wpdb->prefix . 'tf_reviewsystem_responce_details';
            if ($wpdb->get_var("SHOW TABLES LIKE '$responce_details_table'") != $responce_details_table) {
                $sql = "CREATE TABLE " . $responce_details_table . " ( id INT NOT NULL AUTO_INCREMENT, tf_reviewsystem_responce_id INT NOT NULL, question_id INT NOT NULL,score INT,responce TEXT,category_id INT NOT NULL, PRIMARY KEY  (id) ) " . $charset_collate . ";";
                dbDelta($sql);
            }
            
}
*/

  
 
}