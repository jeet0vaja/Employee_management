<?php


if ( ! defined( 'ABSPATH' ) ) {

	die( '-1' );

}


function load_resource($hook_suffix){
	
	wp_enqueue_media();

	wp_register_style('dragula-ui-css', plugins_url('assets/css/dragula.css' , __FILE__ ));
	wp_register_style('semantic.ui.admin', plugins_url('assets/css/semantic/semantic.min.css' , __FILE__ ));

	wp_register_style('semantic.calendar.admin', plugins_url('assets/css/calendar/dist/calendar.min.css' , __FILE__ ));

	wp_register_style('tf_reviewsystem.admin', plugins_url('assets/css/admin.css' , __FILE__ ));
	wp_register_style('bootstrap-notify.css', plugins_url('assets/css/notify/bootstrap-notify.css' , __FILE__ ));
	wp_register_style('bootstrap-notify.min.css', plugins_url('assets/css/notify/bootstrap-notify.min.css' , __FILE__ ));
	wp_register_style('alert-bangtidy.css', plugins_url('assets/css/notify/alert-bangtidy.css' , __FILE__ ));
	wp_register_style('alert-bangtidy.min.css', plugins_url('assets/css/notify/alert-bangtidy.min.css' , __FILE__ ));
	wp_register_style('alert-blackgloss.css', plugins_url('assets/css/notify/alert-blackgloss.css' , __FILE__ ));
	wp_register_style('alert-blackgloss.min.css', plugins_url('assets/css/notify/alert-blackgloss.min.css' , __FILE__ ));

	wp_register_script( 'semantic.ui.js', plugins_url('assets/css/semantic/semantic.min.js' , __FILE__ ), array('jquery'));
	
	wp_register_script( 'semantic.calendar.js', plugins_url('assets/css/calendar/dist/calendar.min.js' , __FILE__ ), array('jquery', 'semantic.ui.js'));

	wp_register_script( 'jquery.validate.min.js', plugins_url('assets/js/jquery.validate.min.js' , __FILE__ ), array('jquery'));
	wp_register_script( 'dragula-ui', '//s3-us-west-2.amazonaws.com/s.cdpn.io/45226/dragula.min.js');
	wp_register_script( 'tf_reviewsystem.admin.js', plugins_url('assets/js/admin.js', __FILE__ ), array('dragula-ui'));
	wp_localize_script( 'tf_reviewsystem.admin.js', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
	wp_register_script( 'bootstrap-notify.js', plugins_url('assets/js/bootstrap-notify.js', __FILE__ ), array('jquery'));
		
	wp_enqueue_style('dragula-ui-css');
	wp_enqueue_style('semantic.ui.admin');
	wp_enqueue_style('semantic.calendar.admin');
	wp_enqueue_style('tf_reviewsystem.admin');
	wp_enqueue_style('bootstrap-notify.css');
	wp_enqueue_style('bootstrap-notify.min.css');
	wp_enqueue_style('alert-bangtidy.css');
	wp_enqueue_style('alert-bangtidy.min.css');
	wp_enqueue_style('alert-blackgloss.css');
	wp_enqueue_style('alert-blackgloss.min.css');

	wp_enqueue_script('semantic.ui.js');
	wp_enqueue_script('semantic.calendar.js');
	wp_enqueue_script('jquery.validate.min.js');
	wp_enqueue_script('tf_reviewsystem.admin.js');
	wp_enqueue_script('bootstrap-notify.js');
}
add_action( 'init', 'load_resource' );
/*
function load_resource($hook_suffix){
	
	wp_enqueue_media();
	wp_register_style('semantic.ui.admin', plugins_url('assets/css/semantic/semantic.min.css' , __FILE__ ));

	wp_register_style('semantic.calendar.admin', plugins_url('assets/css/calendar/dist/calendar.min.css' , __FILE__ ));

	wp_register_style('tf_reviewsystem.admin', plugins_url('assets/css/admin.css' , __FILE__ ));
	wp_register_style('bootstrap-notify.css', plugins_url('assets/css/notify/bootstrap-notify.css' , __FILE__ ));
	wp_register_style('bootstrap-notify.min.css', plugins_url('assets/css/notify/bootstrap-notify.min.css' , __FILE__ ));
	wp_register_style('alert-bangtidy.css', plugins_url('assets/css/notify/alert-bangtidy.css' , __FILE__ ));
	wp_register_style('alert-bangtidy.min.css', plugins_url('assets/css/notify/alert-bangtidy.min.css' , __FILE__ ));
	wp_register_style('alert-blackgloss.css', plugins_url('assets/css/notify/alert-blackgloss.css' , __FILE__ ));
	wp_register_style('alert-blackgloss.min.css', plugins_url('assets/css/notify/alert-blackgloss.min.css' , __FILE__ ));

	wp_register_script( 'semantic.ui.js', plugins_url('assets/css/semantic/semantic.min.js' , __FILE__ ), array('jquery'));
	
	wp_register_script( 'semantic.calendar.js', plugins_url('assets/css/calendar/dist/calendar.min.js' , __FILE__ ), array('jquery', 'semantic.ui.js'));

	wp_register_script( 'jquery.validate.min.js', plugins_url('assets/js/jquery.validate.min.js' , __FILE__ ), array('jquery'));
	wp_register_script( 'dragula-ui', '//s3-us-west-2.amazonaws.com/s.cdpn.io/45226/dragula.min.js');
	wp_register_script( 'tf_reviewsystem.admin.js', plugins_url('assets/js/admin.js', __FILE__ ), array('dragula-ui'));
	wp_localize_script( 'tf_reviewsystem.admin.js', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
	wp_register_script( 'bootstrap-notify.js', plugins_url('assets/js/bootstrap-notify.js', __FILE__ ), array('jquery'));
	//wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), '4.5.2', true);

	wp_enqueue_style('dragula-ui-css');
	wp_enqueue_style('semantic.ui.admin');
	wp_enqueue_style('semantic.calendar.admin');
	wp_enqueue_style('tf_reviewsystem.admin');
	wp_enqueue_style('bootstrap-notify.css');
	wp_enqueue_style('bootstrap-notify.min.css');
	wp_enqueue_style('alert-bangtidy.css');
	wp_enqueue_style('alert-bangtidy.min.css');
	wp_enqueue_style('alert-blackgloss.css');
	wp_enqueue_style('alert-blackgloss.min.css');

	wp_enqueue_script('semantic.ui.js');
	wp_enqueue_script('semantic.calendar.js');
	wp_enqueue_script('jquery.validate.min.js');
	wp_enqueue_script('tf_reviewsystem.admin.js');
	wp_enqueue_script('bootstrap-notify.js');
	//wp_enqueue_script('bootstrap-js');
}
add_action( 'init', 'load_resource' );
*/