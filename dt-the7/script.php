<?php

ini_set( 'max_execution_time', 0 );

include dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';
global $wpdb;

$test_sql = "SELECT * FROM `testing_store`";
$test_res = $wpdb->get_results($test_sql);
$offset = $test_res[0]->value;

$select = "SELECT * FROM `WDA1DH` LIMIT 1000 offset ".$offset."";
$get_res = $wpdb->get_results($select);

foreach($get_res as $res){
	
	$add = str_replace('#','',$res->INS_BROKER_US_ADDRESS1);
	$add = str_replace(' ','%20',$add);
	$add1 = $res->INS_BROKER_US_ADDRESS2;
	$add1 = str_replace(' ','%20',$add1);
	$city = str_replace(' ','%20',$res->INS_BROKER_US_CITY);
	$state = str_replace(' ','%20',$res->INS_BROKER_US_STATE);
	$zip = $res->INS_BROKER_ZIP;
	
	$url = "https://us-street.api.smartystreets.com/street-address?auth-id=36815afa-fa7b-688b-cae4-5bc966cac410&auth-token=U4z210gZjHRCE2yOmsxb&candidates=10&street=".$add."&city=".$city."&state=".$state."&zipcode=".$zip."&street2=".$add1."";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);
	$get_data =curl_exec($ch);
	curl_close($ch);
	
	
	$response = json_decode($get_data);

	if(is_null($response)){
		
	}else{
		foreach($response as $data){
		
			$full_address = $data->delivery_line_1 .', '.$data->last_line;
			$lat = $data->metadata->latitude;
			$long = $data->metadata->longitude;
			$delivery_line1 = $data->delivery_line_1;
			$delivery_line2 = $data->last_line;
			$city_name = $data->components->city_name;
			$state_abbreviation = $data->components->state_abbreviation;
			$full_zipcode = $data->components->zipcode .'-'. $data->components->plus4_code;
			$county_name = $data->metadata->county_name;
			$county_fips = $data->metadata->county_fips;
			
			$sql = "UPDATE `WDA1DH` SET `LONGITUDE`='".$lat."',`LATTITUDE`='".$long."',`Addressee`='".$full_address."',`Delivery_line_1`='".$delivery_line1."',`Delivery_line_2`='".$delivery_line2."',`City_name`='".$city_name."',`State_abbreviation`='".$state_abbreviation."',`Full_zipcode`='".$full_zipcode."',`County_name`='".$county_name."',`County_fips`='".$county_fips."' WHERE Id = '".$res->Id."' ";
			$updated = $wpdb->query($sql);
			
		}
	}
}

$new_offset = $offset + 1000;
$test_sql = "UPDATE `testing_store` SET `value` = '". $new_offset ."'";
$test_res = $wpdb->query($test_sql);


?>