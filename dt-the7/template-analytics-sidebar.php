<?php
/**
 * Template Name: Analytics Page with-sidebar Template
 */

add_action( 'wp_enqueue_scripts', 'enqueue_analytics_scripts' );

get_header();

?>
<div class="main-wrap">
    <div class="left-side-content main-content-temp">
        <?php include_once dirname(__FILE__).'/analytics.php';?>
    </div><!--left over-->
    
    <div class="right-side-bar">
        <?php
            get_sidebar();
        ?>
    </div>
</div>
<?php
get_footer();

