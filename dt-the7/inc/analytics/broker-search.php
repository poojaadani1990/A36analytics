<?php
/* Ajax call */
if( isset($_POST['action'] ) && $_POST['action'] == "search_for_broker" ){
	include dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/wp-load.php';
	global $wpdb;
	
	$searchbroker = $_POST['broker_name'];
	/* Broker details */
        $broker_sql = "SELECT WDA1DH.ins_broker_name_normalized, 
		   WDA1DH.ins_broker_us_state, 
		   WDA1DH.ins_broker_zip, 
		   sum(WDA1DH.ins_broker_comm_pd_amt) as ins_broker_comm_pd_amt, 
		   sum(WDA1DH.ins_broker_fees_pd_amt) as ins_broker_fees_pd_amt 
        FROM  WDA1DH 
		 WHERE 	INS_BROKER_NAME_NORMALIZED LIKE '$searchbroker%'  
	GROUP BY WDA1DH.INS_BROKER_NAME_NORMALIZED";
        $broker_sql_result = $wpdb->get_results($broker_sql);
       ?>
	<div class="full-width">
	<h3 class="search-title">Broker Details</h3>
	<div class="search-elements">
		<div class="tbl-divs" style="margin-top: 10px;border: 1px solid #dddddd;padding: 22px;background-color: #f1f1f1;">
			<table class="tbl-search-results">
				<thead>
					<th>Broker Name</th>
					<th>State</th>
					<th>Zipcode</th>
					<th>Comission</th>
					<th>Fees</th>
				</thead>
				<tbody>
					<?php
					foreach($broker_sql_result as $bro){
					    $broker_names = $bro->ins_broker_name_normalized;
                        $broker_val=trim($broker_names, '"');
						?>
						<tr class="sent_sponser_page" data-id="<?php echo $broker_val; ?>">
							<td><?php echo $bro->ins_broker_name_normalized; ?></td>
							<td><?php echo $bro->ins_broker_us_state; ?></td>
							<td><?php echo $bro->ins_broker_zip; ?></td>
							<td><?php echo number_format($bro->ins_broker_comm_pd_amt,1); ?></td>
							<td><?php echo number_format($bro->ins_broker_fees_pd_amt,1); ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
	</div>

     <?php
	/* Broker with carrier details */
	$sql = "SELECT WDA1DH.ins_broker_name_normalized, 
		   WDA1DH.ins_broker_us_state, 
		   WDA1DH.ins_broker_zip, 
		   sum(WDA1DH.ins_broker_comm_pd_amt) as ins_broker_comm_pd_amt, 
		   sum(WDA1DH.ins_broker_fees_pd_amt) as ins_broker_fees_pd_amt, 
		   WDADH.ins_carrier_name_normalized 
	FROM   WDA1DH 
		   INNER JOIN WDADH 
				   ON ( WDA1DH.ack_id = WDADH.ack_id ) 
	WHERE  ( WDA1DH.ins_broker_name_normalized = '".$searchbroker."' )
	GROUP BY WDADH.ins_carrier_name_normalized";
	$results = $wpdb->get_results($sql);
	?>
	<div class="full-width">
	<h3 class="search-title">Broker V/s Carrier Details</h3>
	<div class="search-elements">
		<div class="tbl-divs" style="margin-top: 10px;border: 1px solid #dddddd;padding: 22px;background-color: #f1f1f1;">
			<table class="tbl-search-results">
				<thead>
					<th>Carrier Name</th>
					<th>State</th>
					<th>Zipcode</th>
					<th>Comission</th>
					<th>Fees</th>
				</thead>
				<tbody>
					<?php
					foreach($results as $res){
						?>
						<tr>
							<td><?php echo $res->ins_carrier_name_normalized; ?></td>
							<td><?php echo $res->ins_broker_us_state; ?></td>
							<td><?php echo $res->ins_broker_zip; ?></td>
							<td><?php echo number_format($res->ins_broker_comm_pd_amt,1); ?></td>
							<td><?php echo number_format($res->ins_broker_fees_pd_amt,1); ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
	</div>
	<?php
	
	/* Broker with sponsor detials */
	$get_broker_wd_sponser_sql = "SELECT WDA1DH.ins_broker_name_normalized, 
		   WDA1DH.ins_broker_us_state, 
		   WDA1DH.ins_broker_zip, 
		   sum(WDA1DH.ins_broker_comm_pd_amt) as ins_broker_comm_pd_amt, 
		   sum(WDA1DH.ins_broker_fees_pd_amt) as ins_broker_fees_pd_amt, 
		   WDADH.ins_carrier_name_normalized,
		   EIN_COMPANY_NAMES.SPONSOR_DFE_NAME,
		   EIN_COMPANY_NAMES.SPONS_DFE_MAIL_US_STATE,
		   EIN_COMPANY_NAMES.SPONS_DFE_MAIL_US_ZIP,
		   EIN_COMPANY_NAMES.TOTAL_PREMIUMS,
		   EIN_COMPANY_NAMES.BROKER_REVENUE,
           WDADH.SCH_A_EIN
	FROM   WDA1DH 
		   INNER JOIN WDADH 
				   ON ( WDA1DH.ack_id = WDADH.ack_id ) 
		   INNER JOIN EIN_COMPANY_NAMES
				   ON ( WDADH.SCH_A_EIN = EIN_COMPANY_NAMES.SPONS_DFE_EIN ) 
	WHERE  ( WDA1DH.ins_broker_name_normalized = '".$searchbroker."' )
	GROUP BY WDADH.SCH_A_EIN";
	
	$get_broker_wd_sponser = $wpdb->get_results($get_broker_wd_sponser_sql);
        ?>
	<div class="full-width">
	<h3 class="search-title">Broker V/s Sponser Details</h3>
	<div class="search-elements-ajax">
		<div class="tbl-divs" style="margin-top: 10px;border: 1px solid #dddddd;padding: 22px;background-color: #f1f1f1;">
			<table id="tbl-search-results2">
				<thead>
					<th>Sponser Name</th>
					<th>State</th>
					<th>Zipcode</th>
					<th>Premium</th>
					<th>Revenue</th>
				</thead>
				<tbody class="broker-search-results">
					<?php
					foreach($get_broker_wd_sponser as $res){
						?>
						<tr>
							<td><?php echo $res->SPONSOR_DFE_NAME; ?></td>
							<td><?php echo $res->SPONS_DFE_MAIL_US_STATE; ?></td>
							<td><?php echo $res->SPONS_DFE_MAIL_US_ZIP; ?></td>
							<td><?php echo number_format($res->TOTAL_PREMIUMS,1); ?></td>
							<td><?php echo number_format($res->BROKER_REVENUE,1); ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
	</div>
	<?php
	die();
}

if( is_page_template( 'broker-search-template.php' ) ){
    $is_template = 1;
} else {
    $is_template = 0;
}


?>
<script src="https://twitter.github.io/typeahead.js/js/handlebars.js"></script>
<script src="https://twitter.github.io/typeahead.js/releases/latest/typeahead.bundle.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script>
jQuery(document).ready(function(){
	jQuery('#tbl-search-results2').DataTable();
       
	jQuery(document).on('click','.btn-broker-submit',function(event){
		event.preventDefault();
		jQuery('.main-loaders').show();
		jQuery('.display_ajax_results').html('');
		var broker_name = jQuery('#txt-search').val();
                
		var table = jQuery('.tbl-search-results').DataTable();
                var table2 = jQuery('#tbl-search-results2').DataTable();
		
		var dataString = { broker_name : broker_name, action : "search_for_broker" }
		
		jQuery.ajax({
			type: "POST",
			url: "<?php echo get_template_directory_uri() ?>/inc/analytics/broker-search.php",
			data: dataString,
			success:function(res){
				table.destroy();
				table2.destroy(); 
				
				jQuery('.display_ajax_results').html(res);
				jQuery('.main-loaders').hide();
				jQuery('.tbl-search-results').DataTable({
				        dom: 'lBftip',
				        buttons: [
				            {
				            extend: 'copyHtml5',
				            text: "<img src='"+analytics.theme_url+"/assets/images/copy-icon.png'>",
				            },
				            {
				            extend: 'excelHtml5',
				            title: 'Broker Search With Carrier',
				            text: "<img src='"+analytics.theme_url+"/assets/images/xls-icon.png'>",
				            messageTop: 'Search By Broker Name : '+ broker_name,
				            },
				            {
				            extend: 'pdfHtml5',
				            title: 'Broker Search With Carrier',
				            text : "<img src='"+analytics.theme_url+"/assets/images/pdf-icon.png'>",
				            messageTop: 'Search By Broker Name : '+ broker_name,
				            },
				            {
				            extend: 'csvHtml5',
				            title: 'Broker Search With Carrier',
				            messageTop: 'Search By Broker Name : '+ broker_name,
				            text : "<img src='"+analytics.theme_url+"/assets/images/csv-icon.png'>",
				            customize: function (csv) {
						     return "Search By Broker Name : "+broker_name+"\n"+  csv;
					     }
				            }
				        ]
				    });
				jQuery('#tbl-search-results2').DataTable( {
				        dom: 'lBftip',
				        buttons: [
				            {
				            extend: 'copyHtml5',
				            text: "<img src='"+analytics.theme_url+"/assets/images/copy-icon.png'>",
				            },
				            {
				            extend: 'excelHtml5',
				            title: 'Broker Search Excel',
				            messageTop: 'Search By Broker Name : '+ broker_name,
				            text: "<img src='"+analytics.theme_url+"/assets/images/xls-icon.png'>",
				            },
				            {
				            extend: 'pdfHtml5',
				            title: 'Broker Search PDF',
				            messageTop: 'Search By Broker Name : '+ broker_name,
				            text : "<img src='"+analytics.theme_url+"/assets/images/pdf-icon.png'>",
				            },
				            {
				            extend: 'csvHtml5',
				            title: 'Broker Search CSV',
				            messageTop: 'Search By Broker Name : '+ broker_name,
				            text : "<img src='"+analytics.theme_url+"/assets/images/csv-icon.png'>",
				            customize: function (csv) {
						     return "Search By Broker Name : "+broker_name+"\n"+  csv;
						  }
				            }
				        ]
				    } );
				
				
				set_panels();
				jQuery(".display_ajax_results").togglepanels();
				jQuery(".display_ajax_results h2.ui-accordion-header:first-child").addClass('ui-accordion-header-active ui-state-active').removeClass(' ui-state-default ui-corner-bottom');
				jQuery( ".display_ajax_results" ).find( "h3" ).trigger( "click" );
			}
		});
		
		
	});
});
jQuery(document).bind('ready ajaxComplete',function() {
		jQuery(".sent_sponser_page").click(function() {
			var broker_name = jQuery(this).data("id");
            window.location = "<?php echo site_url(); ?>/slide2/?broker="+broker_name+"&back=123";
		});
	});
</script>
<style>
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loaders {
    border: 16px solid #f3f3f3;
    border-radius: 50%;
    border-top: 16px solid #3498db;
    width: 75px;
    height: 75px;
    -webkit-animation: spin 2s linear infinite;
    animation: spin 2s linear infinite;
    margin: 0px auto;
}
.main-loaders {
	display: none;
}
</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<div class="search-wrapper">
    <form id="frm-broker-search-analytics" class="frm-broker-search-analytics" name="frm-search-analytics" action="<?php echo isset( $page_link ) ? $page_link : '#' ?>" method="post">
        <div class="search-container basic-search">
            <div class="full-width">
                <h3 class="search-title">Basic Search</h3>
                <div class="search-elements">
                    <div class="md-6 search-box" id="the-carrior">
                        <input type="text" id="txt-search" class="txt-search" name="broker" placeholder="Start typing Broker Name" style="width: 100%; height: 50px;"/>
                    </div>
                    <?php
			$sql_broker = "SELECT INS_BROKER_NAME_NORMALIZED FROM `WDA1DH` GROUP BY INS_BROKER_NAME_NORMALIZED";
                        $get_broker_name = $wpdb->get_results($sql_broker);
                    ?>
                    <div class="md-6 search-submit">
                        <div class="md-6">
                            <button id="btn-submit" class="btn-broker-submit" name="btn-submit"><i class="fa fa-search"></i>Search</button>
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
<?php foreach($get_broker_name as $bro_names){ ?>
    '<?php echo addslashes($bro_names->INS_BROKER_NAME_NORMALIZED); ?>',
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
    
    <?php if( $is_template == 1 ){ 
	?>
	
    <div class="warning-message-over-results" style="display:none">
		<span>Your search returned over 500 results.  Continue to use filters to further refine results.</span>
   	</div>
        <div id="search-results" class="search-results">
            <div class="search-section-header">
                <h2>Search Results</h2>
            </div>
			<div class="main-loaders">
				<div class="loaders"></div>
			</div>
			<div class="display_ajax_results">
                            <div class="full-width">
					<h3 class="search-title">Broker  Details</h3>
					<div class="search-elements">
						<div class="tbl-divs" style="margin-top: 10px;border: 1px solid #dddddd;padding: 22px;background-color: #f1f1f1;">
							<table class="tbl-search-results">
								<thead>
									<th>Broker Name</th>
									<th>State</th>
									<th>Zipcode</th>
									<th>Comission</th>
									<th>Fees</th>
								</thead>
								<tbody class="broker-search-results">
									
								</tbody>
							</table>
						</div>
					</div>
				</div>
                            
				<div class="full-width">
					<h3 class="search-title">Broker V/s Carrier Details</h3>
					<div class="search-elements">
						<div class="tbl-divs" style="margin-top: 10px;border: 1px solid #dddddd;padding: 22px;background-color: #f1f1f1;">
							<table class="tbl-search-results">
								<thead>
									<th>Carrier Name</th>
									<th>State</th>
									<th>Zipcode</th>
									<th>Comission</th>
									<th>Fees</th>
								</thead>
								<tbody class="broker-search-results">
									
								</tbody>
							</table>
						</div>
					</div>
				</div>
				
				<div class="full-width">
					<h3 class="search-title">Broker V/s Sponser Details</h3>
					<div class="search-elements">
						<div class="tbl-divs" style="margin-top: 10px;border: 1px solid #dddddd;padding: 22px;background-color: #f1f1f1;">
							<table id="tbl-search-results2">
								<thead>
									<th>Sponser Name</th>
									<th>State</th>
									<th>Zipcode</th>
									<th>Premium</th>
									<th>Revenue</th>
								</thead>
								<tbody class="broker-search-results">
									
								</tbody>
							</table>
						</div>
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