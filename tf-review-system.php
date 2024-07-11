<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://techforceglobal.com
 * @since             1.0.0
 * @package           Tf_Review_System
 *
 * @wordpress-plugin
 * Plugin Name:       Employee Review System
 * Plugin URI:        https://techforceglobal.com
 * Description:       The Employee Review System is a highly efficient and user-friendly plugin for WordPress that helps team members provide feedback about individual employees in an organised and streamlined manner.
 * Version:           1.0.0
 * Author:            Techforce
 * Author URI:        https://techforceglobal.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tf-review-system
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TF_REVIEW_SYSTEM_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tf-review-system-activator.php
 */
function activate_tf_review_system() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tf-review-system-activator.php';
	Tf_Review_System_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tf-review-system-deactivator.php
 */
function deactivate_tf_review_system() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tf-review-system-deactivator.php';
	Tf_Review_System_Deactivator::deactivate();
}


function create_feedback_table() {
     
	global  $table_prefix, $wpdb;
	$feedback_paraTable = $table_prefix . 'tf_reviewsystem_feedback_para';
	 // Execute the query
    $results = $wpdb->get_results($wpdb->prepare("SHOW TABLES LIKE %s", $feedback_paraTable));
	if (empty($results)) {
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
 
    // Execute the query
    $wpdb->query($wpdb->prepare("ALTER TABLE %s ADD PRIMARY KEY (`para_id`)", $feedback_paraTable));
		 
		//$wpdb->query($wpdb->prepare("ALTER TABLE $feedback_paraTable ADD PRIMARY KEY (`para_id`)"));
		$wpdb->query($wpdb->prepare("ALTER TABLE %s MODIFY `para_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1",$feedback_paraTable));
	}

	$feedback_statusTable = $table_prefix . 'tf_reviewsystem_feedback_status';
	$results = $wpdb->get_results($wpdb->prepare("SHOW TABLES LIKE %s", $feedback_statusTable));
	if (empty($results)) {
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

		 
		$wpdb->query($wpdb->prepare("ALTER TABLE %s ADD PRIMARY KEY (`id`)",$feedback_statusTable));
		$wpdb->query($wpdb->prepare("ALTER TABLE %s MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1",$feedback_statusTable));

	}

	$responces_table = $wpdb->prefix . 'tf_reviewsystem_responces';
	$results = $wpdb->get_results($wpdb->prepare("SHOW TABLES LIKE %s", $responces_table));
	if (empty($results)) {
		$sql = "CREATE TABLE " . $responces_table . " ( id INT NOT NULL AUTO_INCREMENT, tf_reviewsystem_id INT NOT NULL, company_id INT NOT NULL,location_id INT NOT NULL,review_for INT NOT NULL,review_by INT NOT NULL,`comments` longtext NOT NULL, is_offline BOOLEAN,datetime TIMESTAMP, PRIMARY KEY  (id) ) " . $charset_collate . ";";
		dbDelta($sql);
	}
	$responce_details_table = $wpdb->prefix . 'tf_reviewsystem_responce_details';
	$results = $wpdb->get_results($wpdb->prepare("SHOW TABLES LIKE %s", $responce_details_table));
	if (empty($results)) {
		$sql = "CREATE TABLE " . $responce_details_table . " ( id INT NOT NULL AUTO_INCREMENT, tf_reviewsystem_responce_id INT NOT NULL, question_id INT NOT NULL,score INT,responce TEXT,category_id INT NOT NULL, PRIMARY KEY  (id) ) " . $charset_collate . ";";
		dbDelta($sql);
	}
	
}



register_activation_hook( __FILE__, 'activate_tf_review_system' );
register_deactivation_hook( __FILE__, 'deactivate_tf_review_system' );
register_activation_hook( __FILE__, 'create_feedback_table');



/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-tf-review-system.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tf_review_system() {

	$plugin = new Tf_Review_System();
	$plugin->run();

}
run_tf_review_system();
