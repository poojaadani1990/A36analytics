<?php

/**
 * Template Name: Analytics Search Template
 */

add_action( 'wp_enqueue_scripts', 'enqueue_analytics_scripts', 10, 0 );

/*
 * Start the page
 */
get_header();

include get_template_directory() . '/inc/analytics/sponser-search.php';

?>

<?php get_footer(); ?>