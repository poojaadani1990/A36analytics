<?php
/**
 * Template Name: Analytics Page Template
 */

add_action( 'wp_enqueue_scripts', 'enqueue_analytics_scripts' );

get_header();

?>
<div class="">
    <?php include_once dirname(__FILE__).'/analytics.php';?>
</div>
<?php
get_footer();
