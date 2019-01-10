<?php

/**
 * Template Name: Fetch Data Template
 */
 
 
$tbl_WD5500DH = 'WD5500DH';
$tbl_WDA1DH = 'WDA1DH';
$tbl_WDADH = 'WDADH';
$tbl_EIN_COMPANY_NAMES = 'EIN_COMPANY_NAMES';
$tbl_state = 'states';


// Get Latitude and Longitude from the address
function get_lat_long( $address ){

    $address = str_replace(" ", "+", $address);

    $api_link = "https://maps.google.com/maps/api/geocode/json?key=AIzaSyAI2Jc0LEvGW1LwaXD49hPL9bTk0uIKwR8&address=$address&sensor=false";

    $json = file_get_contents( $api_link );
    
    $latlong = json_decode($json);

    $lat = $latlong->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
    $long = $latlong->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
    
    if( !empty( $lat ) && !empty( $long ) ){
        return array( 
            'lat' => $lat, 
            'lng' => $long 
                );
    } else {
        return 0;
    }
    
}

function set_lat_long( $table, $lat_long, $id ){
    global $wpdb;
    echo $id . ' - ';
    $str_update = "UPDATE {$table} SET LATITUDE = " .$lat_long['lat']  . ", LONGITUDE = " .$lat_long['lng']  . " WHERE ID = " . $id;
    $wpdb->get_results( $str_update );
}

$limit = '';
if( isset( $_GET['from'] ) && isset( $_GET['to'] ) ){
    $limit = ' AND E.ID >= ' . $_GET['from'] . ' AND E.ID <= ' . $_GET['to'];
}

$str_companies = "SELECT E.ID, E.SPONS_DFE_EIN, CONCAT( E.SPONS_DFE_MAIL_US_ADDRESS1, ' ' , E.SPONS_DFE_MAIL_US_ADDRESS2, ' ', SPONS_DFE_MAIL_US_CITY ) AS ADDRESS, S.state_name, S.state_code
FROM {$tbl_EIN_COMPANY_NAMES} as E 
LEFT JOIN {$tbl_state} S ON S.state_code = E.SPONS_DFE_MAIL_US_STATE
WHERE ( TRIM( CONCAT( E.SPONS_DFE_MAIL_US_ADDRESS1, E.SPONS_DFE_MAIL_US_ADDRESS2 ) ) ) <> '' AND ( E.LATITUDE = 0 && E.LONGITUDE = 0 ) " . $limit;

$companies = $wpdb->get_results( $str_companies );

foreach ( $companies as $ck => $cv ) {
    $lat_long = get_lat_long( $cv->ADDRESS );
    if( $lat_long != 0 ){
        set_lat_long( $tbl_EIN_COMPANY_NAMES, $lat_long, $cv->ID );
    }
    if( $lat_long == 0 ){
        $lat_long = get_lat_long( $cv->ADDRESS . ' ' . $cv->state_code . ' USA' );
        set_lat_long( $tbl_EIN_COMPANY_NAMES, $lat_long, $cv->ID );
    }
}
exit;