<?php

//include dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/wp-load.php';

include_once 'common.php';

define( 'TBL_WD5500DH', 'WD5500DH' );
define( 'TBL_WDA1DH', 'WDA1DH' );
define( 'TBL_WDADH', 'WDADH' );
define( 'TBL_EIN_COMPANY_NAMES', 'EIN_COMPANY_NAMES' );
define( 'TBL_STATE', 'states' );
define( 'TBL_ZIPCODE', 'zipcodes' );

/* AJAX Calls */
function analytics_search(){
    global $wpdb;
    $result = array(
        'success_flag' => 0,
        'message' => 'No records found'
    );
    
    $arr_selects = '';
    $str_mile_param = '';
    $arr_params = array();
    
    /*----------------------------------------
     * Basic Search
     */
    
    /* Search for Employer name OR EIN no. */
    if( isset( $_POST['txt-search'] ) && !empty( $_POST['txt-search'] ) ){
        $search_str = trim( $_POST['txt-search'] );
        $search_fulltext_str = str_replace( ' ', ' +', $search_str );
        $arr_params[] = '( MATCH ( `SPONSOR_DFE_NAME` ) AGAINST ( "+' . $search_fulltext_str . '" IN BOOLEAN MODE ) OR `SPONS_DFE_EIN` = "' . $search_str . '" )';
    }
    
    /* Search for State */
    if( isset( $_POST['ddl-state'] ) && !empty( $_POST['ddl-state'] ) ){
        $search_state = trim( $_POST['ddl-state'] );
        $arr_params[] = '( `SPONS_DFE_MAIL_US_STATE` = "' . $search_state . '" )';
    }
    
    
    /*-------------------------------------
     * Employer Size, Premium, and Broker Revenue Search
     */
    
    /* Search for Participants */
    if( isset( $_POST['txt-participants-from'] ) || isset( $_POST['txt-participants-to'] ) ){
        
        if(isset( $_POST['txt-participants-from'] ) && $_POST['txt-participants-from'] != ''){
            $p_from = trim( $_POST['txt-participants-from'] );
        }else{
            $p_from = '0';
        }
        if(isset( $_POST['txt-participants-to'] ) && $_POST['txt-participants-to'] != ''){
            $p_to = trim( $_POST['txt-participants-to'] );
        }else{
            $p_to = "5000+";
        }
        
        if( substr( $p_from, -1 ) == '+' && rtrim( $p_from, "+" ) > 0 ){
            $plus_from = rtrim( $p_from, "+" );
            $arr_params[] = '( `PARTICIPANTS` >= ' . $plus_from . ' )';
        } elseif( substr( $p_to, -1 ) == '+' && rtrim( $p_to, "+" ) > 0 ) {
            $plus_from = rtrim( $p_from, "+" );
            $plus_to = rtrim( $p_fto, "+" );
            $arr_params[] = '( `PARTICIPANTS` >= ' . rtrim( $plus_from, "+" ) . ' )';
        } else {
            $participants_from = ( is_numeric( $p_from ) && $p_from >= 0 ) ? $p_from : 0;
            $participants_to = ( is_numeric( $p_to ) && $p_to >= 0 ) ? $p_to : 0;

            if( $participants_from >= 0 && $participants_to > 0 && $participants_from <= $participants_to ){
                $arr_params[] = '( `PARTICIPANTS` >= ' . $participants_from . ' AND `PARTICIPANTS` <= ' . $participants_to . ' )';
            }
        }
    }
    
    /* Search for Premium */
    if( isset( $_POST['txt-premium-from'] ) || isset( $_POST['txt-premium-to'] ) ){
        
        if(isset( $_POST['txt-premium-from'] ) && $_POST['txt-premium-from'] != ''){
            $p_from = trim( $_POST['txt-premium-from'] );
        }else{
            $p_from = '0';
        }
        if(isset( $_POST['txt-premium-to'] ) && $_POST['txt-premium-to'] != ''){
            $p_to = trim( $_POST['txt-premium-to'] );
        }else{
            $p_to = '1000000+';   
        }
        
        if( substr( $p_from, -1 ) == '+' && rtrim( $p_from, "+" ) > 0 ){
            $plus_from = rtrim( $p_from, "+" );
            $arr_params[] = '( `TOTAL_PREMIUMS` >= ' . $plus_from . ' )';
        } elseif( substr( $p_to, -1 ) == '+' && rtrim( $p_to, "+" ) > 0 ) {
            $plus_from = rtrim( $p_from, "+" );
            $plus_to = rtrim( $p_fto, "+" );
            $arr_params[] = '( `TOTAL_PREMIUMS` >= ' . rtrim( $plus_from, "+" ) . ' )';
        } else {
            $premium_from = ( is_numeric( $p_from ) && $p_from >= 0 ) ? $p_from : 0;
            $premium_to = ( is_numeric( $p_to ) && $p_to >= 0 ) ? $p_to : 0;
            
            if( $premium_from >= 0 && $premium_to > 0 && $premium_from <= $premium_to ){
                $arr_params[] = '( `TOTAL_PREMIUMS` >= ' . $premium_from . ' AND `TOTAL_PREMIUMS` <= ' . $premium_to . ' )';
            }
        }
    }
    
    /* Search for Broker Revenue */
    if( isset( $_POST['txt-broker-revenue-from'] ) || isset( $_POST['txt-broker-revenue-to'] ) ){
        
        if(isset($_POST['txt-broker-revenue-from']) && $_POST['txt-broker-revenue-from'] != '' ){
            $p_from = trim( $_POST['txt-broker-revenue-from'] );
        }else{
            $p_from = '0';
        }
        if(isset($_POST['txt-broker-revenue-to']) || $_POST['txt-broker-revenue-to'] != ''){
            $p_to = trim( $_POST['txt-broker-revenue-to'] );
        }else{
            $p_to = '1000000+';
        }
        
        if( substr( $p_from, -1 ) == '+' && rtrim( $p_from, "+" ) > 0 ){
            $plus_from = rtrim( $p_from, "+" );
            $arr_params[] = '( `BROKER_REVENUE` >= ' . $plus_from . ' )';
        } elseif( substr( $p_to, -1 ) == '+' && rtrim( $p_to, "+" ) > 0 ) {
            $plus_from = rtrim( $p_from, "+" );
            $plus_to = rtrim( $p_fto, "+" );
            $arr_params[] = '( `BROKER_REVENUE` >= ' . rtrim( $plus_from, "+" ) . ' )';
        } else {
            $broker_revenue_from = ( is_numeric( $p_from ) && $p_from >= 0 ) ? $p_from : 0;
            $broker_revenue_to = ( is_numeric( $p_to ) && $p_to >= 0 ) ? $p_to : 0;
            if( $broker_revenue_from >= 0 && $broker_revenue_to > 0 && $broker_revenue_from <= $broker_revenue_to ){
                $arr_params[] = '( `BROKER_REVENUE` >= ' . $broker_revenue_from . ' AND `BROKER_REVENUE` <= ' . $broker_revenue_to . ' )';
            }
        }
    }
    
    /*-------------------------------------
     * Geography Search
     */
    
    /* Search for City */
    if( isset( $_POST['txt-city'] ) && !empty( $_POST['txt-city'] ) ){
        $search_city = trim( $_POST['txt-city'] );
        $arr_params[] = '( `SPONS_DFE_MAIL_US_CITY` LIKE "%' . $search_city . '%" )';
    }
    
    $zipcode_flag = 0;
    
    /* Search by Distance from Zipcode */
    /*
    if( isset( $_POST['txt-miles'] ) && !empty( $_POST['txt-miles'] ) && isset( $_POST['txt-zipcode'] ) && !empty( $_POST['txt-zipcode'] ) ){
        $ml = trim( $_POST['txt-miles'] );
        $miles = ( is_numeric( $ml ) && $ml >= 0 ) ? $ml : 0;
        $zipcode = trim( $_POST['txt-zipcode'] );
        
        //Get Latitude and Longitue of selected zipcode
        $str_latlong = "SELECT * FROM " . TBL_ZIPCODE . " WHERE ZIP = {$zipcode}";
        $latlong = $wpdb->get_row( $str_latlong );
        $lat = $latlong->LAT;
        $lng = $latlong->LNG;
        
        if( substr( $ml, -1 ) == '+' && rtrim( $ml, "+" ) > 0 ){
            $miles = rtrim( $ml, "+" );
            $str_mile_param = ' HAVING distance >= 0 ';
        } else {
            $str_mile_param = ' HAVING distance <= ' . $miles . ' ';
        }
        
        if( $miles > 0 ){
            $selects = ', ( 3956 * 2 * Asin(Sqrt(Power(Sin(( ' . $lat . ' - LATITUDE ) * Pi() / 180 / 2), 
                                                      2) + 
                                                                        Cos(' . $lat . ' * Pi() / 180) * Cos( 
                                                                        LATITUDE * Pi() / 180 
                                                                                                 ) * 
                                                                        Power(Sin( 
                                                                             ( ' . $lng . ' - LONGITUDE 
                                                      ) * Pi() / 180 / 2), 2))) ) AS distance';
            $arr_params[] = ' 1 = 1 ';
        } else {
            $zipcode_flag = 1;
        }
    } else {
        $zipcode_flag = 1;
    }
     * 
     */
    
    
    if( isset( $_POST['txt-miles'] ) && !empty( $_POST['txt-miles'] ) && isset( $_POST['txt-zipcode'] ) && !empty( $_POST['txt-zipcode'] ) ){
        $ml = trim( $_POST['txt-miles'] );
        $miles = ( is_numeric( $ml ) && $ml >= 0 ) ? $ml : 0;
        $zipcode = trim( $_POST['txt-zipcode'] );
        
        /* Get Latitude and Longitue of selected zipcode */
        $str_latlong = "SELECT * FROM " . TBL_ZIPCODE . " WHERE ZIP = {$zipcode}";
        $latlong = $wpdb->get_row( $str_latlong );
        $lat = $latlong->LAT;
        $lng = $latlong->LNG;
        
        if( substr( $ml, -1 ) == '+' && rtrim( $ml, "+" ) > 0 ){
            $miles = rtrim( $ml, "+" );
            $str_mile_param = ' HAVING distance >= 0 ORDER BY distance ';
        } else {
            $str_mile_param = ' HAVING distance <= ' . $miles . ' ORDER BY distance ';
        }
        
        if( $miles > 0 ){
            $selects = ', z.ZIP, z.LAT, z.LNG ';
            $joins = ' JOIN zipcodes AS z ON z.ZIP = ECN.SPONS_DFE_MAIL_US_ZIP ';
            $selects .= ', ( 3956 * 2 * Asin(Sqrt(Power(Sin(( ' . $lat . ' - LAT ) * Pi() / 180 / 2), 
                                                      2) + 
                                                                        Cos(' . $lat . ' * Pi() / 180) * Cos( 
                                                                        LAT * Pi() / 180 
                                                                                                 ) * 
                                                                        Power(Sin( 
                                                                             ( ' . $lng . ' - LNG 
                                                      ) * Pi() / 180 / 2), 2))) ) AS distance';
            $arr_params[] = ' 1 = 1 ';
        } else {
            $zipcode_flag = 1;
        }
    } else {
        $zipcode_flag = 1;
    }
    
    
    /* Search for Zipcode */
    if( isset( $_POST['txt-zipcode'] ) && !empty( $_POST['txt-zipcode'] ) && $zipcode_flag == 1 ){
        $search_zipcode = trim( $_POST['txt-zipcode'] );
        $arr_params[] = '( `SPONS_DFE_MAIL_US_ZIP` = "' . $search_zipcode . '" )';
    }
    
    /* Search For Carrier Name */
   if( isset( $_POST['txt-carrier'] ) && !empty( $_POST['txt-carrier'] ) ){
	$search_carriername = trim( $_POST['txt-carrier'] );
	$arr_params[] = '( `SPONS_DFE_EIN` = `SCH_A_EIN`)';
	$arr_params[] = '( `INS_CARRIER_NAME_NORMALIZED` = "'.$search_carriername.'") group by SPONS_DFE_EIN';
	
   }
   
    /* Search For Broker Name */
   if( isset( $_POST['txt-broker'] ) && !empty( $_POST['txt-broker'] ) ){
	$search_brokername = trim( $_POST['txt-broker'] );
// 	echo $search_brokername;
	$broke_val1 =str_replace(array("'", "\"", "&quot;"), "", htmlspecialchars($search_brokername ) );
	$broke_vals = str_replace(array('\\', '/'), '', $broke_val1);
	$broker_val2=trim($broke_vals);
    
	

	echo $vals;
	$arr_params[] = '( BROKER_TBL.ACK_ID = CARRIER_TBL.ACK_ID)';
    $arr_params[] = '( BROKER_TBL.INS_BROKER_NAME_NORMALIZED = "'.$broker_val2.'" ) group by CARRIER_TBL.INS_CARRIER_NAME_NORMALIZED';
	
   }
    
    
    /*      
     * State join query
     */
    $search_query = "SELECT ECN.*,CARRIER_TBL.*,BROKER_TBL.*, s.state_name {$selects} "
            . "FROM " . TBL_EIN_COMPANY_NAMES . " as ECN "
            . "LEFT JOIN " . TBL_STATE . " as s ON ECN.SPONS_DFE_MAIL_US_STATE = s.state_code {$joins} , WDADH as CARRIER_TBL, WDA1DH as BROKER_TBL";
    
    //print_r($arr_params);
    
    if( !empty( $arr_params ) ){
        /*$search_params = " WHERE " . implode( ' AND ' ,$arr_params ) . " " . $str_mile_param;*/
        $search_params = " WHERE " . implode( ' AND ' ,$arr_params ) . " " . $str_mile_param . " LIMIT 500";
        $search_query .= $search_params;
        
    } else {
        $result['message'] = 'No search criteria selected';
        echo json_encode( $result );
        die();
    }
    
    //echo $search_query; exit;
    
    $fetched_data = $wpdb->get_results( $search_query );
    
    /*echo '<pre>';
    print_r($fetched_data);
    echo '</pre>';
    die;*/
    
    if( !empty( $fetched_data ) ){
        $result['success_flag'] = 1;
        $result['message'] = '';
        $result['data'] = $fetched_data;
        $result['data_count'] = sizeof($fetched_data);
        if( $zipcode_flag == 0 ){
            $result['distance'] = 1;
        }
    } else {
        $result['message'] = 'No records found';
    }
    echo json_encode( $result );
    die();
}
add_action('wp_ajax_nopriv_analytics_search', 'analytics_search');
add_action('wp_ajax_analytics_search', 'analytics_search');

function display_data_yearwise(){
    $year = isset( $_POST['year'] ) ? $_POST['year'] : '';
    if( !empty( $year ) ){
        $ein = isset($_POST['ein']) ? $_POST['ein'] : '';
        if ($year == '') {
            return '0';
        }
        include get_template_directory() . '/inc/load_data.php';
    }
    die();
}
add_action('wp_ajax_nopriv_display_data_yearwise', 'display_data_yearwise');
add_action('wp_ajax_display_data_yearwise', 'display_data_yearwise');


function load_analytics(){
    $ein_no = $_POST['ein'];
    include get_template_directory() . '/analytics.php';
    die();
}
add_action('wp_ajax_nopriv_load_analytics', 'load_analytics');
add_action('wp_ajax_load_analytics', 'load_analytics');
/* END AJAX Calls */


/*
 * Enqueue necessary scripts and styles
 */

function enqueue_analytics_scripts( $show_results = 1, $show_analytics = 1 ){
    wp_enqueue_script( 'datatables-js' );
    //wp_enqueue_script( 'highcharts-js' );
    //wp_enqueue_script( 'exporting-js' );
    wp_enqueue_script( 'analytics-js' );

    wp_enqueue_style( 'datatables-css' );
    wp_enqueue_style( 'analytics-css' );
    wp_enqueue_style( 'fontawesome-css' );
    
    $arr_extra = array(
        'show_results' => $show_results,
        'show_analytics' => $show_analytics,
        'theme_url' => get_stylesheet_directory_uri(),
    );
    
    wp_localize_script( 'analytics-js', 'analytics_extra', $arr_extra );
}

function register_analytics_scripts() {
    wp_register_script( 'datatables-js', get_stylesheet_directory_uri() . '/assets/js/datatables.min.js', array( 'jquery' ), null, TRUE );
    wp_register_script( 'highcharts-js', 'https://code.highcharts.com/highcharts.js', array( 'jquery' ), null, TRUE );
	wp_register_script( 'exporting-js', 'https://code.highcharts.com/modules/exporting.js', array( 'jquery' ), null, TRUE );
    wp_register_script( 'analytics-js', get_stylesheet_directory_uri() . '/assets/js/analytics.js', array( 'datatables-js' ), null, TRUE );

    wp_register_style( 'datatables-css', get_stylesheet_directory_uri() . '/assets/css/datatables.min.css', array(), null );
    wp_register_style( 'analytics-css', get_stylesheet_directory_uri() . '/assets/css/analytics.css', array(), null );
    wp_register_style( 'fontawesome-css', get_stylesheet_directory_uri() . '/fonts/FontAwesome/css/all.min.css', array(), null );

    wp_localize_script( 'analytics-js', 'analytics',
        array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'theme_url' => get_stylesheet_directory_uri(),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'register_analytics_scripts' );



/* Shotcode to show search fields */
function sc_analytics_search( $attr ) {
    extract( shortcode_atts( array(
        'type' => 'none',
        'results' => 0,
        'analytics' => 0,
        'page' => 0
    ), $attr ) );
    
    switch ( $type ) {
        case 'sponser':
            if( $results == 0 ){
                $show_results = 0;
            } else {
                $show_results = 1;
            }
            if( $analytics == 0 ){
                $show_analytics = 0;
            } else {
                $show_analytics = 1;
            }
            if( $page > 0 ){
                $page_link = get_permalink( $page );
            } else {
                $page_link = '#';
            }
            
            enqueue_analytics_scripts( $show_results, $show_analytics );
            echo '<div class="search-wrapper-sidebar">';
            include get_template_directory() . '/inc/analytics/sponser-search.php';
            echo '</div>';
            break;
        default:
            echo '';
            break;
    }
}
add_shortcode( 'analytics_search', 'sc_analytics_search' );


/* Shotcode to show search results */
function sc_analytics_search_results( $attr ) {
    extract( shortcode_atts( array(
        'type' => 'none',
        'results' => 0,
        'analytics' => 0
    ), $attr ) );
    
    switch ( $type ) {
        case 'sponser':
            $is_searched = 1;
            // enqueue_analytics_scripts();
            
            ?>
           	<div class="warning-message-over-results" style="display:none">
           		<span>Your search returned over 500 results.  Continue to use filters to further refine results.</span>
           	</div>
                <div id="search-results" class="search-results">
                    <div class="tbl-div">
                        <table class="tbl-search-results">
                            <thead>
                                
                                <th>Company Name</th>
                                <th>Carrier Name</th>
                                <th>Broker Name</th>
                                <th>State</th>
                                <th>Participants</th>
                                <th>Premium</th>
                                <th>Broker Revenue</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="err-div">
                        <p></p>
                        <div id="search-loader">

                        </div>
                    </div>
                </div>
            <?php
            break;
        default:
            echo '';
            break;
    }
}
add_shortcode( 'analytics_search_result', 'sc_analytics_search_results' );


/* Shotcode to show search analytics */
function sc_analytics_search_analytics( $attr ) {
    extract( shortcode_atts( array(
        'type' => 'none',
        'results' => 0,
        'analytics' => 0
    ), $attr ) );
    
    switch ( $type ) {
        case 'sponser':
            $is_searched = 1;
            // enqueue_analytics_scripts();
            ?>
            <div class="sidebar-analytics-result">
                <div id="analytics-result" >
                    <div class="analytics-result">
                    </div>
                </div>
            </div>
            <?php
            break;
        default:
            echo '';
            break;
    }
}
add_shortcode( 'analytics_search_analytics', 'sc_analytics_search_analytics' );

/* Necesary Functions */
function print_val( $arr = array(), $key = '' ){
    if( is_array( $arr ) && !empty( $arr ) && isset( $arr[$key] ) ){
        return $arr[$key];
    }
}

function print_input_val( $arr = array(), $key = '' ){
    $val = print_val( $arr, $key );
    echo ' value="' . $val . '" ';
}

function print_selected(  $arr = array(), $key = '', $val = '' ){
    if( is_array( $arr ) && !empty( $arr ) && isset( $arr[$key] ) ){
        if( $arr[$key] == trim( $val ) ){
            echo ' selected="selected" ';
        }
    }
}