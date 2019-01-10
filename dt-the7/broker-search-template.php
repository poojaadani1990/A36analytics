<?php

/**
 * Template Name: Broker Search Template
 */

add_action( 'wp_enqueue_scripts', 'enqueue_analytics_scripts', 10, 0 );

/*
 * Start the page
 */
get_header();

include get_template_directory() . '/inc/analytics/broker-search.php';
include dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';

?>

<?php get_footer(); ?>