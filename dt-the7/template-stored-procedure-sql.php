<?php

/**
 * Template Name: Analytics Mysql Stored Procedure Template
 */

/*
 * Start the page
 */
get_header();

/* Call Mysql Procedure */
global $wpdb;
$sql = "CALL `GetCompanyDetailsNew` ('2')";
$res = $wpdb->get_results($sql);

?>

<table>
    <tr>
        <td>ID</td>
        <td>Company Name</td>
        <td>PLAN YEAR</td>
        <td>TOTAL PREMIUMS</td>
    <tr>
    <?php
    $i = 1;
    foreach($res as $r){
    ?>
    <tr>
        <td><?php echo $i; ?></td>
        <td><?php echo $r->SPONSOR_DFE_NAME; ?></td>
        <td><?php echo $r->PLAN_YEAR; ?></td>
        <td><?php echo $r->TOTAL_PREMIUMS; ?></td>
    </tr>
    <?php
    $i++;
    }
    ?>
</table>

<?php get_footer(); ?>