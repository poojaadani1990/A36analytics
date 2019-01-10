<?php
/**
 * Template Name: Carrier Search Page Template
 */


/* Ajax Code */
if(isset($_POST['action']) && $_POST['action'] == 'carrier_ajax') {

include dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';
$name = $_POST['carrier'];
global $wpdb;
//Carrier With Broker details //
$sql = "SELECT WDADH.ACK_ID,WDADH.INS_CARRIER_NAME_NORMALIZED,WDADH.SCH_A_EIN,WDADH.SCH_A_PLAN_NUM,WDADH.INS_POLICY_FROM_DATE,WDADH.INS_POLICY_TO_DATE,WDA1DH.ACK_ID,WDA1DH.INS_BROKER_NAME_NORMALIZED from WDADH INNER JOIN WDA1DH
ON WDADH.ACK_ID=WDA1DH.ACK_ID where WDADH.INS_CARRIER_NAME_NORMALIZED='$name' GROUP BY WDA1DH.INS_BROKER_NAME_NORMALIZED";
$results = $wpdb->get_results($sql);

//Carrier With Company details //
$sql2 = "SELECT EIN_COMPANY_NAMES.SPONS_DFE_EIN,EIN_COMPANY_NAMES.SPONSOR_DFE_NAME,EIN_COMPANY_NAMES.TOTAL_PREMIUMS,EIN_COMPANY_NAMES.BROKER_REVENUE,EIN_COMPANY_NAMES.SPONS_DFE_MAIL_US_STATE,EIN_COMPANY_NAMES.SPONS_DFE_MAIL_US_ZIP,WDADH.SCH_A_EIN from EIN_COMPANY_NAMES INNER JOIN WDADH
ON EIN_COMPANY_NAMES.SPONS_DFE_EIN = WDADH.SCH_A_EIN where WDADH.INS_CARRIER_NAME_NORMALIZED='$name' GROUP BY WDADH.SCH_A_EIN";
$result2 = $wpdb->get_results($sql2);

//Carrier details //
$sql3 = "SELECT INS_CARRIER_NAME_NORMALIZED,SCH_A_EIN,SCH_A_PLAN_NUM,INS_POLICY_FROM_DATE,INS_POLICY_TO_DATE from WDADH WHERE INS_CARRIER_NAME_NORMALIZED LIKE '$name%' GROUP BY SCH_A_EIN";
$result3 = $wpdb->get_results($sql3);


$vals = "";
$var = "";
$val = "";
$get_data = array();
foreach ($result3 as $keys ) {
	$test = $keys->INS_CARRIER_NAME_NORMALIZED;
        
	$vals.='<tr class="sent_sponser_page" data-id="' . $test . '">
	    <td>'.$keys->INS_CARRIER_NAME_NORMALIZED.'</td>
            <td>'.$keys->SCH_A_EIN.'</td>
            <td>'.$keys->SCH_A_PLAN_NUM.'</td>
            <td>'.$keys->INS_POLICY_FROM_DATE.'</td>
            <td>'.$keys->INS_POLICY_TO_DATE.'</td>
	</tr>';
		
	}

foreach ($results as $key ) {
	
	$var.='<tr>
			<td>'.$key->INS_BROKER_NAME_NORMALIZED.'</td>
			<td>'.$key->SCH_A_EIN.'</td>
			<td>'.$key->SCH_A_PLAN_NUM.'</td>
			<td>'.$key->INS_POLICY_FROM_DATE.'</td>
			<td>'.$key->INS_POLICY_TO_DATE.'</td>
	</tr>';
		
	}

  foreach ($result2 as $company ) {
  
  $val.='<tr>
      <td>'.$company->SPONSOR_DFE_NAME.'</td>
      <td>'.$company->SPONS_DFE_MAIL_US_STATE.'</td>
      <td>'.$company->SPONS_DFE_MAIL_US_ZIP.'</td>
      <td>'.$company->TOTAL_PREMIUMS.'</td>
      <td>'.$company->BROKER_REVENUE.'</td>
      
    </tr>';
    
  }
  $get_data['carrierdetail'] = $vals;
  $get_data['carrier'] = $var;
  $get_data['company'] = $val;
  echo json_encode($get_data);
  

  
die();

}
  if( is_page_template( 'template-carrier-search-page.php' ) ) {
    $is_template = 1;
} else {
    $is_template = 0;
}

global $post;
$current_id = $post->ID;

add_action( 'wp_enqueue_scripts', 'enqueue_analytics_scripts' );
get_header();
?>
<style>
.loader {
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 120px;
  height: 120px;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
  margin: auto;
}

/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
.search-results
{
    display:none;
}
.loader
{
    display:none;
}
</style>
<script src="https://twitter.github.io/typeahead.js/js/handlebars.js"></script>
<script src="https://twitter.github.io/typeahead.js/releases/latest/typeahead.bundle.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>

<script type="text/javascript">
jQuery(document).ready(function() {
  
  jQuery('#btn-submit').click(function( event ) {
  	
  	event.preventDefault();
  	var carrier_name = jQuery('#carrier-search').val();
  	var pageurl = '<?php bloginfo('template_url'); ?>/template-carrier-search-page.php';
  	var table = jQuery('.tbl-search-results').DataTable();
  	
  	jQuery.ajax({ 
  	   type: 'POST',
       url: pageurl, 
       data: {carrier:carrier_name,action: 'carrier_ajax'},
       dataType: 'json',
       beforeSend: function( ) {
        jQuery(".loader").show();
        jQuery(".search-results").hide();
        },
       success: function (data) 
       {
          
       		table.destroy();
            jQuery("#responsecontainer2").html(data['carrierdetail']);
            jQuery("#responsecontainer").html(data['carrier']);
            jQuery("#responsecontainers1").html(data['company']);
            jQuery('.tbl-search-results').DataTable( {
             dom: 'lBftip',
             buttons: [
                {
	            extend: 'copyHtml5',
	            text: "<img src='"+analytics.theme_url+"/assets/images/copy-icon.png'>",
	        
	        },
                {
                extend: 'excelHtml5',
                title: 'Carrier Search Excel',
                text: "<img src='"+analytics.theme_url+"/assets/images/xls-icon.png'>",
                messageTop: 'Search By Carrier Name : '+ carrier_name,
                },
                {
                extend: 'pdfHtml5',
                title: 'Carrier Search PDF',
                text : "<img src='"+analytics.theme_url+"/assets/images/pdf-icon.png'>",
                messageTop: 'Search By Carrier Name : '+ carrier_name,
                },
                {
                extend: 'csvHtml5',
                title: 'Carrier Search CSV',
                text : "<img src='"+analytics.theme_url+"/assets/images/csv-icon.png'>",
                messageTop: 'Search By Carrier Name : '+ carrier_name,
                customize: function (csv) {
                         return "Search By Broker Name : "+carrier_name+"\n"+  csv;
                      }
                }
            ]
              
           } );
           
       		jQuery(".loader").hide();
       		
       		
       },
       	complete: function() {
       jQuery(".search-results").show();
    }
  	});
  });
  });
  
  jQuery(document).bind('ready ajaxComplete',function() {
		jQuery(".sent_sponser_page").click(function() {
			var carrier_name = jQuery(this).data("id");
            window.location = "<?php echo site_url(); ?>/slide1/?carrier="+carrier_name+"&back=123";
		});
	});

</script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<div class="search-wrapper">
 <form id="frm-search-carrier" class="frm-search-carrier" name="frm-search-carrier" action="<?php echo isset( $page_link ) ? $page_link : '#' ?>" method="post">
    <div class="search-container basic-search">
        <div class="full-width">
            <h3 class="search-title">Carrier Search</h3>
                <div class="search-elements">
                    <div class="md-6 search-box" id="the-carrior">
                        <input type="text" id="carrier-search" class="txt-search" name="carrier" placeholder="Start typing Carrier Name" style="width: 100%; height: 50px;"/>
                    </div>
                    <?php
			$sql_carrier = "SELECT INS_CARRIER_NAME_NORMALIZED FROM `WDADH` GROUP BY INS_CARRIER_NAME_NORMALIZED";
                        $get_carrier_name = $wpdb->get_results($sql_carrier);
                    ?>
                    
                   <div class="md-6 search-submit">
                     
                    <div class="md-6">
                      <button  id="btn-submit" class="btn-submit" name="btn-submit"><i class="fa fa-search"></i>Search</button>
                   </div>
                </div>
              </div>
        </div>
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

jQuery('#the-carrior .txt-search').typeahead({
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
<div class="loader"></div>
<?php if( $is_template == 1 ){ ?>

	<div class="warning-message-over-results" style="display:none">
   	<span>Your search returned over 500 results.  Continue to use filters to further refine results.</span>
 </div>

 <div id="search-results" class="search-results">
            <div class="search-section-header">
                <h2>Search Results</h2>
            </div>

            <div class="display_ajax_results">
            <div class="full-width">
             <h3 class="search-title">Carrier Details</h3>
               <div class="search-elements">
                    <div class="tbl-divs" style="margin-top: 10px;border: 1px solid #dddddd;padding: 22px;background-color: #f1f1f1;">
                <table class="tbl-search-results">
                    <thead>
                        <th>Carrier Name</th>
                        <th>EIN Number</th>
                        <th>Plan Number</th>
                        <th>Policy From Date</th>
                        <th>Policy To Date</th>
                    </thead>
                    <tbody id="responsecontainer2">

                    </tbody>
                </table>
          </div>
      </div>
    </div>       
             <!-- Carrier V/s Broker Details -->
           
               <h3 class="search-title">Carrier V/s Broker Details</h3>
               <div class="search-elements">
                    <div class="tbl-divs" style="margin-top: 10px;border: 1px solid #dddddd;padding: 22px;background-color: #f1f1f1;">
                <table class="tbl-search-results">
                    <thead>
                        <th>Broker Name</th>
                        <th>EIN Number</th>
                        <th>Plan Number</th>
                        <th>Policy From Date</th>
                        <th>Policy To Date</th>
                    </thead>
                    <tbody id="responsecontainer">

                    </tbody>
                </table>
          </div>
      </div>

        <!-- Carrier V/s Company Details -->     
     <h3 class="search-title">Carrier V/s Company Details</h3>
        <div class="search-elements">
            <div class="tbl-divs" style="margin-top: 10px;border: 1px solid #dddddd;padding: 22px;background-color: #f1f1f1;">
                <table class="tbl-search-results">
                    <thead>
                        <th>Sponsor Name</th>
                        <th>State</th>
                        <th>Zipcode</th>
                        <th>Premium</th>
                        <th>Revenue</th>
                    </thead>
                    <tbody id="responsecontainers1">

                    </tbody>
                </table>
          </div>
          </div>
     
        </div>
          <div class="err-div">
                <p></p>
                <div id="search-loader">

                </div>
            </div>
        </div>
           

		
    <?php } ?>
</div>

<?php
get_footer();
