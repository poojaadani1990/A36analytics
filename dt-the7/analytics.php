<?php
/*
 * Check if the date is valid or not
 */

include_once 'inc/common.php';
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
.loader {
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 120px;
  height: 120px;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
  margin: 0px auto;
}

.main-loader{
    display:none;
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
</style>


<div class="analytics-container">
    
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <?php
    if(isset($_GET['back'])){
    ?>
    <button id="btn-back" onclick="history.go(-1);">Back to search results</button>
    <?php
    }
    ?>
    <?php
	function localize_us_number($phone) {
	  $numbers_only = preg_replace("/[^\d]/", "", $phone);
	  return preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "($1)-$2-$3", $numbers_only);
	}
 
    if( isset( $ein_no ) && !empty( $ein_no ) ){
        $ein = $ein_no;
    } else {
        $ein = isset($_GET['ein']) ? $_GET['ein'] : '';
    }
    if (!empty($ein)) {

        $table1 = 'WD5500DH';
        $table2 = 'WDA1DH';
        $table3 = 'WDADH';
        $table4 = 'EIN_COMPANY_NAMES';
        
        global $wpdb;

        $get_years = $wpdb->get_results("SELECT PLAN_YEAR FROM {$table1} WHERE SPONS_DFE_EIN = '$ein' GROUP BY PLAN_YEAR ORDER BY PLAN_YEAR DESC");
        $year = $get_years[0]->PLAN_YEAR;

        $result = $wpdb->get_results("SELECT *,max(TOT_ACTIVE_PARTCP_CNT) as max_TOT_ACTIVE_PARTCP_CNT FROM {$table1} WHERE SPONS_DFE_EIN = '$ein' AND PLAN_YEAR = '{$year}' GROUP BY PLAN_YEAR", ARRAY_A);
        if (!empty($result)) {
            ?>
            <script type="text/javascript">
                jQuery(window).load(function () {
                    <?php echo "load_data( {$year}, {$ein} );" ?>
                });
            </script>
            <input type="hidden" id="start_year" value="<?php echo $year; ?>" />
            <div class="sponser-wrap">
                <h2><?php echo $result[0]['SPONSOR_DFE_NAME']; ?></h2>
                <div class="sbi-name"><span>Plan year </span> 
                    <div class="select-bg">
                        <select class="business_year" data-ein="<?php echo $ein; ?>">
                    <?php
                    foreach ($get_years as $years) {
                        ?>	
                                <option><?php echo $years->PLAN_YEAR; ?></option>
                        <?php
                    }
                    ?>
                        </select>
                    </div>
                </div>
                <div class="md-6 sbi-address-wrapper">
                    <div class="sbi-address">
                    <?php
                    $address = '';
                    $address .= $result[0]['SPONSOR_DFE_NAME'];
                    $address .= isset($result[0]['SPONS_DFE_MAIL_US_ADDRESS1']) ? '<br/>' . $result[0]['SPONS_DFE_MAIL_US_ADDRESS1'] : '';
                    $address .= isset($result[0]['SPONS_DFE_MAIL_US_ADDRESS2']) && ( trim( $result[0]['SPONS_DFE_MAIL_US_ADDRESS2'] ) != '0000-00-00' ) &&  trim($_POST['SPONS_DFE_MAIL_US_ADDRESS2']) != '' ? '<br/>' . $result[0]['SPONS_DFE_MAIL_US_ADDRESS2'] : '';
                    $address .= isset($result[0]['SPONS_DFE_MAIL_US_CITY']) ? '<br/>' . $result[0]['SPONS_DFE_MAIL_US_CITY'] .',' : '';
                    $address .= isset($result[0]['SPONS_DFE_MAIL_US_STATE']) ? ' ' . $result[0]['SPONS_DFE_MAIL_US_STATE'] : '';
                    $address .= isset($result[0]['SPONS_DFE_MAIL_US_ZIP']) ? ' ' . $result[0]['SPONS_DFE_MAIL_US_ZIP'] : '';
                    echo $address;
                    ?>
                    </div>
            <?php if ($result[0]['SPONS_DFE_PHONE_NUM']) { ?>
                        <div class="sbi-phone"><span>Phone:</span> <?php echo localize_us_number($result[0]['SPONS_DFE_PHONE_NUM']); ?></div>
                <?php } ?>
                </div>
                    
                    <div class="md-6 sbi-counters-wrapper">
                    
                <?php if ($result[0]['max_TOT_ACTIVE_PARTCP_CNT']) { ?>
                    <div class="sbi-ae"><span>Active Employees:</span> <?php echo number_format($result[0]['max_TOT_ACTIVE_PARTCP_CNT'],0); ?></div>
        <?php } ?>
            <?php if ($result[0]['BUSINESS_CODE']) { ?>
                    <div class="sbi-industry"><span>Industry:</span> <?php echo trim($result[0]['INDUSTRY'],'"'); ?></div>
            <?php } ?>
            <?php if ($result[0]['SPONS_SIGNED_NAME']) { ?>
                    <div class="sbi-phone"><span>Contact:</span> <?php echo $result[0]['SPONS_SIGNED_NAME']; ?></div>
            <?php } ?>
        <?php if ($result[0]['SPONS_DFE_EIN']) { ?>
                    <div class="sbi-ein"><span>EIN:</span> <?php echo $result[0]['SPONS_DFE_EIN']; ?></div>
        <?php } ?>
                    </div>
            </div>
             <div class="main-loader">
                    <div class="loader"></div>
                </div>
            <div id="accordion" class="main-wrap-overview">
                
            </div>            
        <?php
    }
} else {
    echo '<div class="err-div"><p>EIN number is missing</p></div>';
}
?>
</div>

<?php

