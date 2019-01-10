<?php

if( is_page_template( 'template-search-analytics.php' ) ){
    $is_template = 1;
} else {
    $is_template = 0;
}

global $wpdb;
$states = $wpdb->get_results( "SELECT `state_code`, `state_name` as state FROM " . TBL_STATE . " as state WHERE `state_code` IN( SELECT DISTINCT SPONS_DFE_MAIL_US_STATE FROM " . TBL_EIN_COMPANY_NAMES . " ) ");

$data = array(
    'txt_search' => '',
    'ddl_state' => '',
    'txt_participants_from' => '',
    'txt_participants_to' => '',
    'txt_premium_from' => '',
    'txt_premium_to' => '',
    'txt_broker_revenue_from' => '',
    'txt_broker_revenue_to' => '',
    'txt_city' => '',
    'txt_miles' => '',
    'txt_zipcode' => ''
);

$is_searched = 0;

$show_results = ( isset( $show_results ) ? $show_results : 1 );
$show_analytics = ( isset( $show_analytics ) ? $show_analytics : 1 );

if( !empty( $_POST ) && isset( $_POST['hdn-form'] ) && $_POST['hdn-form'] == 'sponser' ){
    $is_searched = 1;
    foreach ( $_POST as $pk => $pv ) {
        $data_key = str_replace( '-', '_', $pk );
        if( isset( $data[$data_key] ) ){
            $data[$data_key] = $pv;
        }
    }
}

global $post;
$current_id = $post->ID;

echo '<script>var searched_data = ' . json_encode( $data ) . ';</script>';

?>
<script src="https://twitter.github.io/typeahead.js/js/handlebars.js"></script>
<script src="https://twitter.github.io/typeahead.js/releases/latest/typeahead.bundle.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<div class="search-wrapper">
    <form id="frm-search-analytics" class="frm-search-analytics" name="frm-search-analytics" action="<?php echo isset( $page_link ) ? $page_link : '#' ?>" method="post">
        <input type="hidden" id="hdn-form" class="hdn-form" name="hdn-form" value="sponser" />
        <input type="hidden" id="hdn-back-id" class="hdn-back-id" name="hdn-back-id" value="<?php echo $current_id; ?>" />
        <div class="search-container basic-search">
            <div class="full-width">
                <h3 class="search-title">Basic Search</h3>
                <div class="search-elements">
                    <div class="md-6 search-box">
                        <input type="text" id="txt-search" class="txt-search" name="txt-search" <?php print_input_val( $data, 'txt_search' ); ?> placeholder="Employer Name or EIN" />
                    </div>
                    <div class="md-6 search-submit">
                        <div class="md-6">
                            <select id="ddl-state" class="ddl-state" name="ddl-state">
                                <option value="">- Select State -</option>
                                <?php
                                if( !is_object( $states ) && !empty( $states ) ){
                                    foreach ( $states as $sk => $sv ) {
                                        if( isset( $sv->state ) ){
                                            echo '<option value="' . $sv->state_code . '">' . $sv->state . '</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="md-6">
                            <button type="submit" id="btn-submit" class="btn-submit" name="btn-submit"><i class="fa fa-search"></i>Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="search-container employer-search">
            <div class="full-width">
                <h3 class="search-title">Size, Premium, Broker Revenue</h3>
                <div class="search-elements">
                    <div class="search-criteria">
                        <div class="md-4 search-label">
                            <label>Participants</label>
                        </div>
                        <div class="md-4">
                            <select id="txt-participants-from" class="txt-participants-from search-from" name="txt-participants-from">
                                <option value="">- Select From -</option>
                                <option value="0">0</option>
                                <option value="100">100</option>
                                <option value="300">300</option>
                                <option value="500">500</option>
                                <option value="1000">1,000</option>
                                <option value="5000">5,000</option>
                                <!--<option value="5000+">5,000+</option>-->
                            </select>
                        </div>
                        <div class="md-4">
                            <select id="txt-participants-to" class="txt-participants-to search-to" name="txt-participants-to">
                                <option value="">- Select To -</option>
                                <option value="0">0</option>
                                <option value="100">100</option>
                                <option value="300">300</option>
                                <option value="500">500</option>
                                <option value="1000">1,000</option>
                                <option value="5000">5,000</option>
                                <option value="5000+">5,000+</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="search-criteria">
                        <div class="md-4 search-label">
                            <label>Premium</label>
                        </div>
                        <div class="md-4">
                            <select id="txt-premium-from" class="txt-premium-from search-from" name="txt-premium-from">
                                <option value="">- Select From -</option>
                                <option value="0">0</option>
                                <option value="1000">1,000</option>
                                <option value="10000">10,000</option>
                                <option value="50000">50,000</option>
                                <option value="100000">100,000</option>
                                <option value="500000">500,000</option>
                                <option value="1000000">1,000,000</option>
                                <!--<option value="1000000+">1,000,000+</option>-->
                            </select>
                        </div>
                        <div class="md-4">
                            <select id="txt-premium-to" class="txt-premium-to search-to" name="txt-premium-to">
                                <option value="">- Select To -</option>
                                <option value="0">0</option>
                                <option value="1000">1,000</option>
                                <option value="10000">10,000</option>
                                <option value="50000">50,000</option>
                                <option value="100000">100,000</option>
                                <option value="500000">500,000</option>
                                <option value="1000000">1,000,000</option>
                                <option value="1000000+">1,000,000+</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="search-criteria">
                        <div class="md-4 search-label">
                            <label>Broker Revenue</label>
                        </div>
                        <div class="md-4">
                            <select id="txt-broker-revenue-from" class="txt-broker-revenue-from search-from" name="txt-broker-revenue-from">
                                <option value="">- Select From -</option>
                                <option value="0">0</option>
                                <option value="1000">1,000</option>
                                <option value="10000">10,000</option>
                                <option value="50000">50,000</option>
                                <option value="100000">100,000</option>
                                <option value="500000">500,000</option>
                                <option value="1000000">1,000,000</option>
                                <!--<option value="1000000+">1,000,000+</option>-->
                            </select>
                        </div>
                        <div class="md-4">
                            <select id="txt-broker-revenue-to" class="txt-broker-revenue-to search-to" name="txt-broker-revenue-to">
                                <option value="">- Select To -</option>
                                <option value="0">0</option>
                                <option value="1000">1,000</option>
                                <option value="10000">10,000</option>
                                <option value="50000">50,000</option>
                                <option value="100000">100,000</option>
                                <option value="500000">500,000</option>
                                <option value="1000000">1,000,000</option>
                                <option value="1000000+">1,000,000+</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="search-container geography-search">
            <div class="full-width">
                <h3 class="search-title">Geography</h3>
                <div class="search-elements">
                    <div class="md-6">
                        <input type="text" id="txt-city" class="txt-city" name="txt-city" <?php print_input_val( $data, 'txt_city' ); ?> placeholder="City"/>
                    </div>
                    <div class="md-6">
                        <div class="md-6">
                            <select id="txt-miles" class="txt-miles" name="txt-miles">
                                <option value="0" selected="selected">- Within 0 Miles -</option>
                                <!--<option value="0">0</option>-->
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="200">200</option>
                                <option value="200+">200+</option>
                            </select>
                        </div>
                        <div class="md-6">
                            <input type="text" id="txt-zipcode" class="txt-zipcode" name="txt-zipcode" <?php print_input_val( $data, 'txt_zipcode' ); ?> placeholder="From Zip Code"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
		<div class="search-container carrier-search">
            <div class="full-width">
                <h3 class="search-title">Carrier Search</h3>
                <div class="search-elements">
                    <div id="the-basics">
					  <input class="typeahead" type="text" id="txt-carrier" name="txt-carrier" placeholder="Start typing Carrier Name">
					</div>
                </div>
            </div>
			<?php
			$sql_carrier = "SELECT INS_CARRIER_NAME_NORMALIZED FROM `WDADH` GROUP BY INS_CARRIER_NAME_NORMALIZED";
			$get_carrier_name = $wpdb->get_results($sql_carrier);
			
			?>
			<script>
var substringMatcher = function(strs) {
  return function findMatches(q, cb) {
    var matches, substringRegex;

    // an array that will be populated with substring matches
    matches = [];

    // regex used to determine if a string contains the substring `q`
    substrRegex = new RegExp(q, 'i');

    // iterate through the pool of strings and for any string that
    // contains the substring `q`, add it to the `matches` array
    jQuery.each(strs, function(i, str) {
      if (substrRegex.test(str)) {
        matches.push(str);
      }
    });

    cb(matches);
  };
};

var states = [
<?php foreach($get_carrier_name as $carr_names){ ?>
    '<?php echo addslashes($carr_names->INS_CARRIER_NAME_NORMALIZED); ?>',
<?php
}
?>
];

jQuery('#the-basics .typeahead').typeahead({
  hint: true,
  highlight: true,
  minLength: 1
},
{
  name: 'states',
  source: substringMatcher(states)
})
    </script>
        </div>
		
		<div class="search-container broker-search">
            <div class="full-width">
                <h3 class="search-title">Broker Search</h3>
                <div class="search-elements">
                    <div id="the-basics">
			 <input class="typeaheadddd" type="text" id="txt-broker" name="txt-broker" placeholder="Start typing Broker Name">
		   </div>
                </div>
            </div>
             <?php
			$sql_broker = "SELECT INS_BROKER_NAME_NORMALIZED FROM `WDA1DH` GROUP BY INS_BROKER_NAME_NORMALIZED";
                        
			$get_broker_name = $wpdb->get_results($sql_broker);
            ?> 
                    <script>
var substringMatcher = function(strs) {
  return function findMatches(q, cb) {
    var matches, substringRegex;

    // an array that will be populated with substring matches
    matches = [];

    // regex used to determine if a string contains the substring `q`
    substrRegex = new RegExp(q, 'i');

    // iterate through the pool of strings and for any string that
    // contains the substring `q`, add it to the `matches` array
    jQuery.each(strs, function(i, str) {
      if (substrRegex.test(str)) {
        matches.push(str);
      }
    });

    cb(matches);
  };
};

var states = [
<?php foreach($get_broker_name as $bro_names){ ?>
     '<?php echo addslashes($bro_names->INS_BROKER_NAME_NORMALIZED); ?>',
<?php
}
?>
];

jQuery('#the-basics .typeaheadddd').typeahead({
  hint: true,
  highlight: true,
  minLength: 1
},
{
  name: 'states',
  source: substringMatcher(states)
})
    </script>
                    
        </div>
		
    </form>
    
    <?php if( $is_template == 1 ){ ?>
    	<div class="warning-message-over-results" style="display:none">
   	<span>Your search returned over 500 results.  Continue to use filters to further refine results.</span>
   	</div>
        <div id="search-results" class="search-results">
            <div class="search-section-header hidden">
                <h2>Search Results</h2>
            </div>
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
        <div id="analytics-result" >
            <div class="search-section-header hidden">
                <h2>Analytics</h2>
            </div>
            <div class="analytics-result">
            </div>
        </div>
    <?php } ?>
</div>