<?php




if (!defined('ABSPATH')) {

	die('-1');

}

function create_tf_year_posttype() {

	$post['tf-yearlist'] = array(

		'labels' => array(

			'all_items' => 'All Yearlist',

			'menu_name' => 'Yearlist',

			'singular_name' => 'Year',

			'edit_item' => 'Edit Year',

			'add_new' => 'New Year',

			'add_new_item' => 'New Year',

			'view_item' => 'View Year',

			'items_archive' => 'Yearlist Archive',

			'search_items' => 'Search Year',

			'not_found' => 'No Year found.',

			'not_found_in_trash' => 'No Year found in trash.',

		),

		'label' => 'Yearlist',

		'public' => false,

		'publicly_queryable' => false,

		'exclude_from_search' => false,

		'show_ui' => true,

		'show_in_menu' => true,

		'has_archive' => false,

		'rewrite' => false,

		'menu_icon' => 'dashicons-building',

		'supports' => array('title'),

		'query_var' => false,

	);

	foreach ($post as $key => $args) {

		register_post_type($key, $args);

		add_action('add_meta_boxes', function () use ($key) {

		});

	}

} 
 

function tf_year_create_tables() {

	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

}

add_action('init', 'create_tf_year_posttype');

function prevent_duplicate_title($data, $postarr) {
    // Check if it's the right post type
    if ($data['post_type'] == 'tf-yearlist') {
        // Check for duplicate title
        $existing_post = get_page_by_title($data['post_title'], 'OBJECT', 'tf-yearlist');

        // If a post with the same title exists, prevent saving
        if ($existing_post && $existing_post->ID != $postarr['ID']) {
            // Display an error message
            add_filter('redirect_post_location', function ($location) {
                remove_filter('redirect_post_location', __FILTER__);
                return add_query_arg('message', 12, $location);
            });

            // Set an error message
            $error_message = __('Error: Duplicate post title. Please choose a unique title.', 'your-text-domain');
            add_action('admin_notices', function () use ($error_message) {
                echo '<div class="error"><p>' . esc_html($error_message) . '</p></div>';
            });

            // Prevent saving
            $data['post_status'] = 'draft'; // You can customize this behavior
        }
    }
    return $data;
}
add_filter('wp_insert_post_data', 'prevent_duplicate_title', 10, 2);