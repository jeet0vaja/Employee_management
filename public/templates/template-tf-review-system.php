<?php
/**
 * Template Name: TF Review System Template
 */
 if (!defined('ABSPATH')) exit; // Exit if accessed directly      
 ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <div id="page" class="site">
        <header id="masthead" class="site-header">
         
        </header><!-- #masthead -->

        <div id="content" class="site-content">
         
            <?php echo do_shortcode('[tf_review_system_template]'); ?>
        </div>
        <?php get_footer(); ?>