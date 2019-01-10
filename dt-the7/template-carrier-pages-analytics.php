<?php
/**
 * Template Name: Carrier Page Template
 */

/*
 * Enqueue necessary scripts and styles
 */
 
if(isset($_POST['action']) && $_POST['action'] == "filter_carrier_data"){
	
	include dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';

	global $wpdb;
	$year = $_POST['years'];
	$carrier = $_POST['carrier'];
	
	/*** Wrapper Code Start***/
	?>
	
	<h3 class="header_accordion" >Premium By Line</h3>
	<div class="coverage-overview">
		<div class="left-co">                        
				
			<?php
			
			$sql = "SELECT WD5500DH.plan_year,WD5500DH.TOT_ACTIVE_PARTCP_CNT,
				WDADH.ins_carrier_name_normalized, 
				Sum(WDADH.ins_prsn_covered_eoy_cnt) AS SumOfINS_PRSN_COVERED_EOY_CNT, 
				Sum(WDADH.health_prem)              AS SumOfHEALTH_PREM, 
				Sum(WDADH.dental_prem)              AS SumOfDENTAL_PREM, 
				Sum(WDADH.life_prem)                AS SumOfLIFE_PREM, 
				Sum(WDADH.ltd_prem)                 AS SumOfLTD_PREM, 
				Sum(WDADH.std_prem)                 AS SumOfSTD_PREM, 
				Sum(WDADH.vision_prem)              AS SumOfVISION_PREM, 
				Sum(WDADH.other_prem)               AS SumOfOTHER_PREM,
				Sum(WDADH.WLFR_TOT_CHARGES_PAID_AMT) AS SumOfWLFR_TOT_CHARGES_PAID_AMT
				FROM WDADH 
				INNER JOIN WD5500DH ON WD5500DH.ack_id = WDADH.ack_id
				WHERE ( ( WD5500DH.plan_year ) = '". $year ."' ) AND 
				( ( WDADH.ins_carrier_name_normalized ) = '".$carrier."' )
				GROUP  BY WD5500DH.plan_year, WDADH.ins_carrier_name_normalized";
			
			$coverage_overview = $wpdb->get_row($sql, ARRAY_A);
			
			
			$health = $dental = $life = $std = $ltd = $vision = $other = $total = $total_participants = 0;
			if (!empty($coverage_overview)) {
				$health = $coverage_overview['SumOfHEALTH_PREM'];
				$dental = $coverage_overview['SumOfDENTAL_PREM'];
				$life = $coverage_overview['SumOfLIFE_PREM'];
				$std = $coverage_overview['SumOfSTD_PREM'];
				$ltd = $coverage_overview['SumOfLTD_PREM'];
				$vision = $coverage_overview['SumOfVISION_PREM'];
				$other = $coverage_overview['SumOfOTHER_PREM'];
				$total = $coverage_overview['SumOfWLFR_TOT_CHARGES_PAID_AMT'];
				$total_participants = $coverage_overview['TOT_ACTIVE_PARTCP_CNT'];
			}
			?>
			<h2>Premium ($K)</h2>
			<?php
			if ($health) {
				?>
				<div class="health"><span>Health:</span> <?php echo number_format($health, 1); ?></div>
			<?php } ?>
			<?php
			if ($dental) {
				?>
				<div class="dental"><span>Dental:</span> <?php echo number_format($dental, 1); ?></div>
			<?php } ?>
			<?php
			if ($life) {
				?>
				<div class="life"><span>Life:</span> <?php echo number_format($life, 1); ?></div>
			<?php } ?>
			<?php
			if ($std) {
				?>
				<div class="std"><span>STD:</span> <?php echo number_format($std, 1); ?></div>
			<?php } ?>
			<?php
			if ($ltd) {
				?>
				<div class="ltd"><span>LTD:</span> <?php echo number_format($ltd, 1); ?></div>
			<?php } ?>
			<?php
			if ($vision) {
				?>
				<div class="vision"><span>Vision:</span> <?php echo number_format($vision, 1); ?></div>
			<?php } ?>
			<?php
			if ($other) {
				?>
				<div class="other"><span>Other:</span> <?php echo number_format($other, 1); ?></div>
			<?php } ?>
			<div class="total"><span>Total:</span> <?php echo number_format($total, 1); ?></div>
		</div>
		<div class="left-co-graph">
			<div id="container-coverage-overview" style="min-width: 310px; height: auto; max-width: 600px; margin: 0 auto"></div>
		</div>
	</div> 

	<script type="text/javascript">
		Highcharts.chart('container-coverage-overview', {
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				type: 'pie',
				width: 450
			},
			title: {
				text: 'Premium By Line'
			},
			tooltip: {
				pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: true,
						format: '<b>{point.name}</b>: {point.percentage:.1f} %',
						style: {
							color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
						}
					}
				}
			},
			series: [{
					name: '',
					colorByPoint: true,
					size: '60%',
					innerSize: '40%',
					data: [{
							name: 'Health',
							y: <?php echo $health / $total; ?>,
							sliced: true,
							selected: true
						}, {
							name: 'Dental',
							y: <?php echo $dental / $total; ?>
						}, {
							name: 'Life',
							y: <?php echo $life / $total; ?>
						}, {
							name: 'STD',
							y: <?php echo $std / $total; ?>
						}, {
							name: 'LTD',
							y: <?php echo $ltd / $total; ?>
						}, {
							name: 'Vision',
							y: <?php echo $vision / $total; ?>
						}, {
							name: 'Other',
							y: <?php echo $other / $total; ?>
						}]
				}]
		});
	</script>
	
	<?php
				
				$premium_by_state = "SELECT WD5500DH.PLAN_YEAR, WDADH.INS_CARRIER_NAME_NORMALIZED, WD5500DH.SPONS_DFE_MAIL_US_STATE, Sum(WDADH.WLFR_TOT_CHARGES_PAID_AMT) AS SumOfWLFR_TOT_CHARGES_PAID_AMT
				FROM WDADH INNER JOIN WD5500DH ON WD5500DH.ACK_ID = WDADH.ACK_ID
				WHERE (((WD5500DH.PLAN_YEAR)='". $year ."') AND ((WDADH.INS_CARRIER_NAME_NORMALIZED)='".$carrier."')) GROUP BY WD5500DH.PLAN_YEAR, WDADH.INS_CARRIER_NAME_NORMALIZED, WD5500DH.SPONS_DFE_MAIL_US_STATE";
				
				$result_premium_state = $wpdb->get_results($premium_by_state, ARRAY_A);
				
				?>
				<!-- Start State Sections -->
				<h3>Premium by State</h3>
				<div class="carrier-overview">
					<div class="table-wrap">
						
					</div>
				    <div id="container_us_map"></div>
					<script type="text/javascript">
					   var data = [ 
					   <?php foreach ($result_premium_state as $s_pre) { 
					   $state = '"'. $s_pre['SPONS_DFE_MAIL_US_STATE'] .'"';
					    $total_charge = $s_pre['SumOfWLFR_TOT_CHARGES_PAID_AMT'];
					   $charge =  str_replace(',', '', number_format($total_charge,2));
					   if($charge != '0.00' &&  $charge > 0 ){
					   ?>
					   {
							"value": <?php echo $charge; ?>,
							"code": <?php echo $state; ?> },
					   <?php }
					   }
					   ?>
							];
					
					   // Make codes uppercase to match the map data
					  jQuery.each(data, function () {
					    this.code = this.code.toUpperCase();
					  });
					  // Instantiate the map
					 
					  Highcharts.mapChart('container_us_map', {

						chart: {
						  map: 'countries/us/us-all',
						  borderWidth: 1
						},

						title: {
						  text: 'Premium by state ($k)'
						},

						exporting: {
						  sourceWidth: 600,
						  sourceHeight: 500
						},

						legend: {
						  layout: 'horizontal',
						  borderWidth: 0,
						  backgroundColor: 'rgba(255,255,255,0.85)',
						  floating: true,
						  verticalAlign: 'top',
						  y: 25
						},

						mapNavigation: {
						  enabled: true
						},

						colorAxis: {
						  min: 1,
						  type: 'logarithmic',
						  minColor: '#EEEEFF',
						  maxColor: '#000022',
						  stops: [
							[0, '#EFEFFF'],
							[0.67, '#4444FF'],
							[1, '#000022']
						  ]
						},

						series: [{
						  animation: {
							duration: 1000
						  },
						  data: data,
						  joinBy: ['postal-code', 'code'],
						  dataLabels: {
							enabled: true,
							color: '#FFFFFF',
							format: '{point.code}'
						  },
						  name: 'Total Charges',
						  tooltip: {
							pointFormat: '{point.code}: {point.value}'
						  }
						}]
					  });
					</script>
				</div>
				
				<!-- End State Sections -->
				
				<!-- Start Top Broker Sections -->
				
				<?php
				
				$top_broker_sql = "SELECT WD5500DH.PLAN_YEAR, WDADH.INS_CARRIER_NAME_NORMALIZED, WDA1DH.INS_BROKER_NAME_NORMALIZED, Sum(WDA1DH.INS_BROKER_REV_HEALTH) AS SumOfINS_BROKER_REV_HEALTH, Sum(WDA1DH.INS_BROKER_REV_LIFE) AS SumOfINS_BROKER_REV_LIFE, Sum(WDA1DH.INS_BROKER_REV_DENTAL) AS SumOfINS_BROKER_REV_DENTAL, Sum(WDA1DH.INS_BROKER_REV_LTD) AS SumOfINS_BROKER_REV_LTD, Sum(WDA1DH.INS_BROKER_REV_STD) AS SumOfINS_BROKER_REV_STD, Sum(WDA1DH.INS_BROKER_REV_VISION) AS SumOfINS_BROKER_REV_VISION, Sum(WDA1DH.INS_BROKER_REV_OTHER) AS SumOfINS_BROKER_REV_OTHER, Sum(WDA1DH.BROKER_REVENUE)
FROM WDADH INNER JOIN WD5500DH ON WD5500DH.ACK_ID = WDADH.ACK_ID 
INNER JOIN WDA1DH ON ((WDADH.FORM_ID = WDA1DH.FORM_ID) AND (WD5500DH.ACK_ID = WDA1DH.ACK_ID))
WHERE (((WD5500DH.PLAN_YEAR)='". $year ."') AND ((WDADH.INS_CARRIER_NAME_NORMALIZED)='".$carrier."'))
GROUP BY WD5500DH.PLAN_YEAR, WDADH.INS_CARRIER_NAME_NORMALIZED, WDA1DH.INS_BROKER_NAME_NORMALIZED
ORDER BY Sum(WDA1DH.BROKER_REVENUE) DESC LIMIT 10";
				
				$result_top_broker = $wpdb->get_results($top_broker_sql, ARRAY_A);
				
				?>
				
				<h3>Top Brokers</h3>
				<div class="carrier-overview">
					<div class="table-wrap">
						
					</div>
					<div id="container_top_broker"></div>
					<script>
					Highcharts.chart('container_top_broker', {
					  chart: {
						type: 'bar'
					  },
					  title: {
						text: 'Top 10 Brokers by Premium'
					  },
					  xAxis: {
						categories: [
						<?php
						foreach ($result_top_broker as $s_pre) {
						$broker_normalized_name = $s_pre['INS_BROKER_NAME_NORMALIZED'];
						?>
						'<?php echo addslashes($broker_normalized_name); ?>',
						<?php } ?>
						]
					  },
					  yAxis: {
						min: 0,
						title: {
						  text: 'label - ($K)'
						}
					  },
					  legend: {
						reversed: true
					  },
					  plotOptions: {
						series: {
						  stacking: 'normal'
						}
					  },
					  series: [{
						name: 'Health',
						data: [
						<?php
						foreach($result_top_broker as $s_pre){
							echo str_replace(',', '', number_format($s_pre['SumOfINS_BROKER_REV_HEALTH'],2)) . ',';
						}
						?>
						]
					  }, {
						name: 'Life',
						data: [
						<?php
						foreach($result_top_broker as $s_pre){
							echo str_replace(',', '', number_format($s_pre['SumOfINS_BROKER_REV_LIFE'],2)) . ',';
						//echo number_format($s_pre['SumOfINS_BROKER_REV_LIFE'],1) . ',';
						}
						?>
						]
					  }, {
						name: 'Dental',
						data: [
						<?php
						foreach($result_top_broker as $s_pre){
							echo str_replace(',', '', number_format($s_pre['SumOfINS_BROKER_REV_DENTAL'],2)) . ',';
						//echo number_format($s_pre['SumOfINS_BROKER_REV_DENTAL'],1) . ',';
						}
						?>
						]
					  },
					  {
						name: 'LTD',
						data: [
						<?php
						foreach($result_top_broker as $s_pre){
							echo str_replace(',', '', number_format($s_pre['SumOfINS_BROKER_REV_LTD'],2)) . ',';
						//echo number_format($s_pre['SumOfINS_BROKER_REV_LTD'],1) . ',';
						}
						?>
						]
					  },
					  {
						name: 'STD',
						data: [
						<?php
						foreach($result_top_broker as $s_pre){
							echo str_replace(',', '', number_format($s_pre['SumOfINS_BROKER_REV_STD'],2)) . ',';
						//echo number_format($s_pre['SumOfINS_BROKER_REV_STD'],1) . ',';
						}
						?>
						]
					  },
					  {
						name: 'Vision',
						data: [
						<?php
						foreach($result_top_broker as $s_pre){
							echo str_replace(',', '', number_format($s_pre['SumOfINS_BROKER_REV_VISION'],2)) . ',';
						//echo number_format($s_pre['SumOfINS_BROKER_REV_VISION'],1) . ',';
						}
						?>
						]
					  },
					  {
						name: 'Other',
						data: [
						<?php
						foreach($result_top_broker as $s_pre){
							echo str_replace(',', '', number_format($s_pre['SumOfINS_BROKER_REV_OTHER'],2)) . ',';
						//echo number_format($s_pre['SumOfINS_BROKER_REV_OTHER'],1) . ',';
						}
						?>
						]
					  }]
					});
					</script>
				    
				</div>
				
				<!-- End Top Broker Sections -->
				
				<?php
				$size_segments_sql = "SELECT WD5500DH.PLAN_YEAR, WDADH.INS_CARRIER_NAME_NORMALIZED, EMPLOYER_SIZE.EMPLOYER_SIZE,EMPLOYER_SIZE.MIN,EMPLOYER_SIZE.MAX,EMPLOYER_SIZE.SORT_ORDER, Sum(WDADH.WLFR_TOT_CHARGES_PAID_AMT) AS SumOfWLFR_TOT_CHARGES_PAID_AMT FROM EMPLOYER_SIZE, WDADH INNER JOIN WD5500DH ON WD5500DH.ACK_ID = WDADH.ACK_ID 
				WHERE (((WD5500DH.TOT_ACTIVE_PARTCP_CNT) Between EMPLOYER_SIZE.MIN And EMPLOYER_SIZE.MAX)) AND (((WD5500DH.PLAN_YEAR)='". $year ."') AND ((WDADH.INS_CARRIER_NAME_NORMALIZED)='".$carrier."')) GROUP BY EMPLOYER_SIZE.EMPLOYER_SIZE, EMPLOYER_SIZE.SORT_ORDER ORDER BY EMPLOYER_SIZE.SORT_ORDER";

				
				$result_size_segments = $wpdb->get_results($size_segments_sql, ARRAY_A);
				
				
				?>
				<h3> Employer Size segments </h3>
				<div class="coverage-overview">
					<div class="left-co">                        
						<div id="container_size_segments" style="min-width: 310px; height: auto; max-width: 600px; margin: 0 auto"></div>
					</div>
					<div class="left-co-graph">
						<table>
							<tr>
								<th>EMPLOYER_SIZE</th>
								<th>CHARGES</th>
							</tr>
							<?php
							foreach($result_size_segments as $res){
							?>
							<tr>
								<td><?php echo $res['EMPLOYER_SIZE']?></td>
								<td><?php echo number_format($res['SumOfWLFR_TOT_CHARGES_PAID_AMT'],1)?></td>
							</tr>			
							<?php
							}
							?>
						</table>
					</div>
				</div>
				
				<script type="text/javascript">
					Highcharts.chart('container_size_segments', {
						chart: {
							plotBackgroundColor: null,
							plotBorderWidth: null,
							plotShadow: false,
							type: 'pie',
							width: 450
						},
						title: {
							text: 'Size segments'
						},
						tooltip: {
							pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
						},
						plotOptions: {
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: true,
									format: '<b>{point.name}</b>: {point.percentage:.1f} %',
									style: {
										color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
									}
								}
							}
						},
						series: [{
								name: '',
								colorByPoint: true,
								size: '60%',
								innerSize: '40%',
								data: [
								<?php
								foreach($result_size_segments as $res){
								?>
								{
										name: '<?php echo $res['EMPLOYER_SIZE']?>',
										y: <?php echo round($res['SumOfWLFR_TOT_CHARGES_PAID_AMT'],2)?>,
										sliced: true,
										selected: true
								},
								<?php } ?>
								]
							}]
					});
				</script>
				
				
				<!-- Start Industry segment -->
				
				<?php
				
				$industry_segments_sql = "SELECT WD5500DH.PLAN_YEAR, WDADH.INS_CARRIER_NAME_NORMALIZED, WD5500DH.NAICS_2_Digit_Industry_Detail, Sum(WDADH.WLFR_TOT_CHARGES_PAID_AMT) AS SumOfWLFR_TOT_CHARGES_PAID_AMT
				FROM WDADH INNER JOIN WD5500DH ON WD5500DH.ACK_ID = WDADH.ACK_ID
				WHERE (((WD5500DH.PLAN_YEAR)='". $year ."') AND ((WDADH.INS_CARRIER_NAME_NORMALIZED)='".$carrier."'))
				GROUP BY WD5500DH.PLAN_YEAR, WDADH.INS_CARRIER_NAME_NORMALIZED, WD5500DH.NAICS_2_Digit_Industry_Detail ORDER BY SumOfWLFR_TOT_CHARGES_PAID_AMT DESC";
                
				$result_industry_segments = $wpdb->get_results($industry_segments_sql, ARRAY_A);
				
				
				?>
				<h3> Industry Segmentation </h3>
				<div class="coverage-overview">
					<div id="container_industry_segments" style="min-width: 310px; height: auto; max-width: 600px; margin: 0 auto"></div>
				</div>
				<script type="text/javascript">
					Highcharts.chart('container_industry_segments', {
						chart: {
							plotBackgroundColor: null,
							plotBorderWidth: null,
							plotShadow: false,
							type: 'pie',
							/* width: 450 */
						},
						title: {
							text: 'Premium By Industry ($M)'
						},
						tooltip: {
							pointFormat: '{series.name}: <b>$ {point.percentage:.1f}</b>'
						},
						plotOptions: {
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: true,
									format: '<b>{point.name}</b>: $ {point.percentage:.1f}',
									style: {
										color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
									}
								}
							}
						},
						series: [{
								name: '',
								colorByPoint: true,
								size: '60%',
								innerSize: '40%',
								data: [
								<?php
								foreach($result_industry_segments as $res){
								$Industry_Detail = trim($res['NAICS_2_Digit_Industry_Detail'], '"');
								
								?>
								{
										name: '<?php echo $Industry_Detail; ?>',
										y: <?php echo $res['SumOfWLFR_TOT_CHARGES_PAID_AMT']; ?>,
										sliced: true,
										selected: true
								},
								<?php } ?>
								]
							}]
					});
				</script>
				
				<!-- End Industry segment -->
				
				<!-- Start Premium Overtime -->
				
				<?php
				$min_max_year = $wpdb->get_row("SELECT MIN(PLAN_YEAR) AS mini, MAX(PLAN_YEAR) AS maxi FROM WD5500DH", ARRAY_A);
				$premium_overtime_sql = "SELECT WD5500DH.plan_year, 
					   WDADH.ins_carrier_name_normalized, 
					   Sum(WDADH.health_prem) AS SumOfHEALTH_PREM, 
					   Sum(WDADH.dental_prem) AS SumOfDENTAL_PREM, 
					   Sum(WDADH.life_prem)   AS SumOfLIFE_PREM, 
					   Sum(WDADH.ltd_prem)    AS SumOfLTD_PREM, 
					   Sum(WDADH.std_prem)    AS SumOfSTD_PREM, 
					   Sum(WDADH.vision_prem) AS SumOfVISION_PREM, 
					   Sum(WDADH.other_prem)  AS SumOfOTHER_PREM
				FROM   WDADH 
					   INNER JOIN WD5500DH 
							   ON WD5500DH.ack_id = WDADH.ack_id 
				WHERE  ( ( ( WD5500DH.plan_year ) > '2008' ) 
						 AND ( ( WDADH.ins_carrier_name_normalized ) = 
								   '".$carrier."' ) ) 
				GROUP  BY WD5500DH.plan_year, 
						  WDADH.ins_carrier_name_normalized 
				ORDER  BY WD5500DH.plan_year";

				$result_premium_overtime = $wpdb->get_results($premium_overtime_sql, ARRAY_A);
				
				$values = array();
				
				
				foreach($result_premium_overtime as $val){
					$pre_values = array();
					$pre_values['Health'] = $val['SumOfHEALTH_PREM'];
					
					$pre_values['Dental'] = $val['SumOfDENTAL_PREM'];
					$pre_values['Life'] = $val['SumOfLIFE_PREM'];
					$pre_values['LTD'] = $val['SumOfLTD_PREM'];
					$pre_values['STD'] = $val['SumOfSTD_PREM'];
					$pre_values['VISION'] = $val['SumOfVISION_PREM'];
					$pre_values['OTHER'] = $val['SumOfOTHER_PREM'];
					arsort($pre_values);
					$values[$val['plan_year']] = $pre_values; 
				}
			
				
				
				$tmp_arr = array();
				foreach($values as $key=>$val){
					$tmp_arr = $val;
					break;
				}
				$falg = 0;
				foreach($values as $key=>$val){
					if($flag > 0){
						$tmp_arr = array_merge_recursive($tmp_arr,$val);
						
					}
					$flag++;
					
				}
				
				?>
				<h3> Historical Premium </h3>
				<div class="coverage-overview">
					<div id="container_premium_overtime"></div>
				</div>
				<script>
				Highcharts.chart('container_premium_overtime', {
				  chart: {
				    type: 'area'
				  },
				  title: {
				    text: 'Historical Premium By Line'
				  },
				  xAxis: {
				    /*categories: [
					<?php foreach($values as $res=>$val){ ?>
					'<?php echo $res; ?>',
					<?php } ?>
					],*/
					categories: [<?php for ($i = 2009; $i <= $min_max_year['maxi']; $i++) {
						echo "'{$i}'" . ",";
					} ?>],
				    tickmarkPlacement: 'on',
				    title: {
				      enabled: false
				    }
				  },
				  yAxis: {
				    labels: {
				      formatter: function () {
				        return this.value / 1000;
				      }
				    }
				  },
				  tooltip: {
				    split: true,
				    valueSuffix: ' millions'
				  },
				  plotOptions: {
				    area: {
				      stacking: 'normal',
				      lineColor: '#666666',
				      lineWidth: 1,
				      marker: {
				        lineWidth: 1,
				        lineColor: '#666666'
				      }
				    }
				  },
				  /* series: [
				  <?php
				  foreach ($values as $key => $val) 
				  {
						$js_s = '';
						
						foreach($val as $vals){
								
									$main_data = isset($vals) ? str_replace(',', '', number_format($vals, 1)) : '0.0';
									$js_s .= "$main_data" . ", ";
						}
					  echo "{ name: '" . $key . "', data: [" . $js_s . "] }, "; 
				  }
				  ?>
				  ] */
				  series: [
				  {
				    name: 'Vision',
				    data: [<?php 
					//rsort($vision);
					foreach($tmp_arr['VISION'] as $res){
						echo round($res,2). ',';
					}	
					?>
					]
				  },
				  {
				    name: 'Health',
				    data: [
					<?php 
					//rsort($Health);
					foreach($tmp_arr['Health'] as $val){
						echo round($val,2) . ',';
					}
					?>
					]
				  },
				  {
				    name: 'Dental',
				    data: [
					<?php
					//rsort($Dental);
					foreach($tmp_arr['Dental'] as $res){
						echo round($res,2) . ',';
					}
					?>
					]
				  },
				  {
				    name: 'Life',
				    data: [
					<?php 
					//rsort($life);
					foreach($tmp_arr['Life'] as $res){
						echo round($res,2). ',';
					}
					?>
					]
				  },
				  {
				    name: 'Other',
				    data: [<?php 
					//rsort($other);
					foreach($tmp_arr['OTHER'] as $res){
						echo round($res,2). ',';
					}
					?>
					]
				  },
				  {
				    name: 'LTD',
				    data: [<?php 
					//rsort($ltd);
					foreach($tmp_arr['LTD'] as $res){
						echo round($res,2). ',';
					}
					?>
					]
				  },
				  {
				    name: 'STD',
				    data: [<?php 
					//rsort($std);
					foreach($tmp_arr['STD'] as $res){
						echo round($res,2). ',';
					}
					?>
					]
				  },
				  ]
				});
				</script>

				
				<!-- End Premium Overtime -->
				
				<!-- Start Historical Clients -->
				
				<?php
				$historical_client_sql = "SELECT data.plan_year, count(data.maxofins_prsn_covered_eoy_cnt) AS count from(
				SELECT WD5500DH.plan_year, 
							   Max(WDADH.ins_prsn_covered_eoy_cnt) AS 
							   maxofins_prsn_covered_eoy_cnt 
						FROM   WDADH 
							   INNER JOIN WD5500DH 
									   ON WD5500DH.ack_id = WDADH.ack_id 
						WHERE  ( ( ( WD5500DH.plan_year ) > '2008' ) 
								 AND ( ( WDADH.ins_carrier_name_normalized ) = 
										   '".$carrier."' ) ) 
						GROUP  BY WD5500DH.plan_year, 
								  WDADH.ins_carrier_name_normalized, 
								  WD5500DH.spons_dfe_ein 
						ORDER  BY WD5500DH.plan_year ) AS data
				GROUP BY data.plan_year;";

				$result_historical_client = $wpdb->get_results($historical_client_sql, ARRAY_A);
				
				?>
				<h3> Historical Clients </h3>
				<div class="coverage-overview">
					<div id="container_history_clients"></div>
				</div>
				<script>
				Highcharts.chart('container_history_clients', {
				  chart: {
					type: 'area'
				  },
				  title: {
					text: 'Historical Clients'
				  },
				  
				  xAxis: {
					allowDecimals: false,
					labels: {
					  formatter: function () {
						return this.value; // clean, unformatted number for year
					  }
					}
				  },
				  yAxis: {
					labels: {
					  formatter: function () {
						return this.value / 1000 + 'k';
					  }
					}
				  },
				  tooltip: {
				  
				  	pointFormat: 'Total clients <b> {point.y:,.0f} </b><br/> in {point.x} Plan Year'
				  },
				  plotOptions: {
					area: {
					  pointStart: <?php if(isset($result_historical_client[0]['plan_year']) && $result_historical_client[0]['plan_year']!= "") { echo $result_historical_client[0]['plan_year']; }else{ echo $year; }?>,
					  marker: {
						enabled: false,
						symbol: 'circle',
						radius: 2,
						states: {
						  hover: {
							enabled: true
						  }
						}
					  }
					},
				  },
				  series: [{
					showInLegend: false,
					data: [
					  <?php foreach($result_historical_client as $res_historic){ 
						echo $res_historic['count'] . ','; 
					   } ?>
					]
				  }]
				});
				</script>
				
				
				<!-- End Historical Clients -->
				
				
				<!-- Start LivesOver Time -->
				
				<?php
				$LivesOver_client_sql = "SELECT data.plan_year, data.maxofins_prsn_covered_eoy_cnt from(
				SELECT WD5500DH.plan_year, 
							   Max(WDADH.ins_prsn_covered_eoy_cnt) AS 
							   maxofins_prsn_covered_eoy_cnt 
						FROM   WDADH 
							   INNER JOIN WD5500DH 
									   ON WD5500DH.ack_id = WDADH.ack_id 
						WHERE  ( ( ( WD5500DH.plan_year ) > '2008' ) 
								 AND ( ( WDADH.ins_carrier_name_normalized ) = 
										   '".$carrier."' ) ) 
						GROUP  BY WD5500DH.plan_year, 
								  WDADH.ins_carrier_name_normalized, 
								  WD5500DH.spons_dfe_ein 
						ORDER  BY WD5500DH.plan_year ) AS data
				GROUP BY data.plan_year;";

				$result_LivesOver_client = $wpdb->get_results($LivesOver_client_sql, ARRAY_A);
				
				?>
				<h3> Historical Lives Covered </h3>
				<div class="coverage-overview">
					<div id="container_LivesOver_client"></div>
				</div>
				<script>
				Highcharts.chart('container_LivesOver_client', {
				  chart: {
					type: 'area'
				  },
				  title: {
					text: 'Historical Lives Covered'
				  },
				  
				  xAxis: {
					allowDecimals: false,
					labels: {
					  formatter: function () {
						return this.value; // clean, unformatted number for year
					  }
					}
				  },
				  yAxis: {
					labels: {
					  formatter: function () {
						return this.value / 1000 + 'k';
					  }
					}
				  },
				  tooltip: {
				 	pointFormat: 'Total employees covered <b>{point.y:,.0f}</b><br/>in {point.x} Plan year'
				  },
				  plotOptions: {
					area: {
					  pointStart: <?php if(isset($result_LivesOver_client[0]['plan_year']) && $result_LivesOver_client[0]['plan_year'] != "") {echo $result_LivesOver_client[0]['plan_year']; }else{ echo $year; } ?>,
					  marker: {
						enabled: false,
						symbol: 'circle',
						radius: 2,
						states: {
						  hover: {
							enabled: true
						  }
						}
					  }
					},
				  },
				  series: [{
					showInLegend: false,
					data: [
					  <?php foreach($result_LivesOver_client as $res_historic){ 
						echo $res_historic['maxofins_prsn_covered_eoy_cnt'] . ','; 
					   } ?>
					]
				  }]
				});
				</script>
				
				<!-- End LivesOver Time -->
				
				<!-- Start Top 50 Clients -->
				
				<?php
				$top_client_sql = "SELECT WD5500DH.plan_year, 
					   WDADH.ins_carrier_name_normalized, 
					   WD5500DH.sponsor_dfe_name, 
					   WD5500DH.SPONS_DFE_EIN,
					   Sum(WDADH.ins_prsn_covered_eoy_cnt)  AS SumOfINS_PRSN_COVERED_EOY_CNT, 
					   Sum(WDADH.health_prem)               AS SumOfHEALTH_PREM, 
					   Sum(WDADH.dental_prem)               AS SumOfDENTAL_PREM, 
					   Sum(WDADH.life_prem)                 AS SumOfLIFE_PREM, 
					   Sum(WDADH.ltd_prem)                  AS SumOfLTD_PREM, 
					   Sum(WDADH.std_prem)                  AS SumOfSTD_PREM, 
					   Sum(WDADH.vision_prem)               AS SumOfVISION_PREM, 
					   Sum(WDADH.other_prem)                AS SumOfOTHER_PREM, 
					   Sum(WDADH.wlfr_tot_charges_paid_amt) AS SumOfWLFR_TOT_CHARGES_PAID_AMT 
				FROM   WDADH  
					   INNER JOIN WD5500DH ON WD5500DH.ack_id = WDADH.ack_id
				WHERE ( ( ( WD5500DH.plan_year ) = '". $year ."' ) 
						 AND 
				( 
				( WDADH.ins_carrier_name_normalized ) = '".$carrier."' ) )
				GROUP  BY WD5500DH.plan_year, 
						  WDADH.ins_carrier_name_normalized, 
						  WD5500DH.sponsor_dfe_name 
				ORDER  BY Sum(WDADH.wlfr_tot_charges_paid_amt) DESC LIMIT 50";
				$result_top_client = $wpdb->get_results($top_client_sql, ARRAY_A);
				?>
				<h3>Top 50 Clients</h3>
				<div class="carrier-overview">
					<div class="table-wrap">
						<table>
							<tr>
								<th>Premium ($K)</th>
								<th>Health</th>
								<th>Dental</th>
								<th>Life</th>
								<th>STD</th>
								<th>LTD</th>
								<th>Vision</th>
								<th>Other</th>
								<th>Total</th>
							</tr>
							<?php
							if ($result_top_client) {
								
								$plan_year = $carrier_name = $state = $total_charge = 0;
								
								foreach ($result_top_client as $s_pre) {
								
									
									$sponsor_dfe_name = $s_pre['sponsor_dfe_name'];
									$health = $s_pre['SumOfHEALTH_PREM'];
									$dental = $s_pre['SumOfDENTAL_PREM'];
									$life = $s_pre['SumOfLIFE_PREM'];
									$ltd = $s_pre['SumOfLTD_PREM'];
									$std = $s_pre['SumOfSTD_PREM'];
									$vision = $s_pre['SumOfVISION_PREM'];
									$other = $s_pre['SumOfOTHER_PREM'];
									$total = $s_pre['SumOfWLFR_TOT_CHARGES_PAID_AMT'];
									?>
									<tr class="sent_sponser_page" data-id="<?php echo $s_pre['SPONS_DFE_EIN']?>" >
										<td><?php echo $sponsor_dfe_name; ?></td>
										<td><?php echo number_format($health, 1); ?></td>
										<td><?php echo number_format($dental, 1); ?></td>
										<td><?php echo number_format($life, 1); ?></td>
										<td><?php echo number_format($ltd, 1); ?></td>
										<td><?php echo number_format($std, 1); ?></td>
										<td><?php echo number_format($vision, 1); ?></td>
										<td><?php echo number_format($other, 1); ?></td>
										<td><?php echo number_format($total, 1); ?></td>
										
									</tr>
									<?php
								}
							}
							?>
						</table>
					</div>
				</div>
				
				<!-- End Top 50 Clients -->
				
				
	
	<?php
	/*** Wrapper Code End ***/
	
	
	
	die();
}

function enqueue_analytics_scriptsss() {
    wp_enqueue_script( 'datatables-js', get_stylesheet_directory_uri() . '/assets/js/datatables.min.js', array( 'jquery' ), null, TRUE );
    wp_enqueue_script( 'analytics-js', get_stylesheet_directory_uri() . '/assets/js/analytics.js', array( 'datatables-js' ), null, TRUE );
    
    wp_enqueue_style( 'datatables-css', get_stylesheet_directory_uri() . '/assets/css/datatables.min.css', array(), null );
    wp_enqueue_style( 'analytics-css', get_stylesheet_directory_uri() . '/assets/css/analytics.css', array(), null );
    
    wp_localize_script( 'analytics-js', 'analytics',
        array(
            'ajax_url' => get_stylesheet_directory_uri() . '/inc/analytics-ajax-functions.php',
            'analytics_url' => get_site_url() . '/analytics',
        )
    );
}
add_action('wp_enqueue_scripts', 'enqueue_analytics_scriptsss');

get_header();

?>
<div class="">
	
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
	
	.wf-container-main {
	    display: block !important;
	}
	
	.analytics-container .sponser-wrap div.select-bg select.analytics-yrs-selectbox {
	    width: 100px;
	    padding-left: 10px;
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

	<script>
	jQuery(document).on('change','.analytics-yrs-selectbox',function(){
		jQuery('.main-carrier-wrap').html("");
		jQuery('.main-loader').show();
		
		var years = jQuery(this).val();
		var carrier = jQuery('.carrier_name').data('id');
		
		var dataString = {years:years,carrier:carrier,action:"filter_carrier_data"}
		
		jQuery.ajax({
			type: "POST",
			url: "<?php echo get_template_directory_uri() ?>/template-carrier-pages-analytics.php",
			data: dataString,
			success:function(res){
				jQuery('.main-carrier-wrap').html(res);
				/* console.log(res); */
				set_panels();
				jQuery("#accordion").togglepanels();
			

				jQuery('.header_accordion').trigger('click');
						jQuery('.main-loader').hide();
			}
		});
	});
	
	jQuery(document).bind('ready ajaxComplete',function() {
		jQuery(".sent_sponser_page").click(function() {
			var EIN_ID = jQuery(this).data("id");
			window.location = "<?php echo site_url(); ?>/sponsor/?ein="+EIN_ID+"&back=123";
		});
	});
	</script>

	<div class="analytics-container">
		<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>-->
		<script src="https://code.highcharts.com/highcharts.js"></script>
		<script src="https://code.highcharts.com/maps/modules/map.js"></script>
		<script src="https://code.highcharts.com/maps/modules/data.js"></script>
		<script src="https://code.highcharts.com/maps/modules/exporting.js"></script>
		<script src="https://code.highcharts.com/maps/modules/offline-exporting.js"></script>
		<script src="https://code.highcharts.com/mapdata/countries/us/us-all.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		
		<?php
		
		$carrier = isset($_GET['carrier']) ? $_GET['carrier'] : '';
		$year = '';
		$ein_no = '';
		if (!empty($carrier)) {

			$table1 = 'WD5500DH';
			$table2 = 'WDA1DH';
			$table3 = 'WDADH';
			$table4 = 'EIN_COMPANY_NAMES';
			
			global $wpdb;
			
			$query1 = "SELECT WD5500DH.plan_year,WDADH.ins_carrier_name_normalized,WD5500DH.SPONS_DFE_EIN FROM WDADH INNER JOIN WD5500DH ON WD5500DH.ack_id = WDADH.ack_id WHERE ( ( WDADH.ins_carrier_name_normalized ) = '". $carrier ."' AND WD5500DH.plan_year <= 2017 ) GROUP BY WD5500DH.plan_year, WDADH.ins_carrier_name_normalized ORDER BY WD5500DH.PLAN_YEAR DESC";
			
							
			$results_years = $wpdb->get_results($query1, ARRAY_A);
			$year = $results_years[0]['plan_year'];
			$ein_no = $results_years[0]['SPONS_DFE_EIN'];

			
			/* if (!empty($result)) { */
				?>
				<div id="accordion" class="main-wrap-overview">
				<script type="text/javascript">
				jQuery(window).load(function () {
					/* Accordion JS */
					set_panels();
					jQuery("#accordion").togglepanels();
				

					jQuery('.header_accordion').trigger('click');
					
					});
				</script>
				
				<div class="sponser-wrap">
					<h2><?php echo $carrier; ?></h2>
					<div class="sbi-name carrier_name" data-id="<?php echo $carrier; ?>">
						<span>Plan year </span> 
						<div class="select-bg">
						   <select class="analytics-yrs-selectbox">
								<?php foreach($results_years as $yrs){ 
									if($yrs['plan_year'] != 0 && $yrs['plan_year'] >= 2009  ){
								?>
									<option><?php echo $yrs['plan_year']?></option>
								<?php
									}
								}
								?>
							</select>
						</div>
					</div>
				</div>
				
				<div class="main-loader">
					<div class="loader"></div>
				</div>
				<div class="main-carrier-wrap">
				
				<h3 class="header_accordion" >Premium By Line</h3>
				<div class="coverage-overview">
					<div class="left-co">                        
							
						<?php
						$sql = "SELECT WD5500DH.plan_year,WD5500DH.TOT_ACTIVE_PARTCP_CNT,
							WDADH.ins_carrier_name_normalized, 
							Sum(WDADH.ins_prsn_covered_eoy_cnt) AS SumOfINS_PRSN_COVERED_EOY_CNT, 
							Sum(WDADH.health_prem)              AS SumOfHEALTH_PREM, 
							Sum(WDADH.dental_prem)              AS SumOfDENTAL_PREM, 
							Sum(WDADH.life_prem)                AS SumOfLIFE_PREM, 
							Sum(WDADH.ltd_prem)                 AS SumOfLTD_PREM, 
							Sum(WDADH.std_prem)                 AS SumOfSTD_PREM, 
							Sum(WDADH.vision_prem)              AS SumOfVISION_PREM, 
							Sum(WDADH.other_prem)               AS SumOfOTHER_PREM,
							Sum(WDADH.WLFR_TOT_CHARGES_PAID_AMT) AS SumOfWLFR_TOT_CHARGES_PAID_AMT
							FROM WDADH 
							INNER JOIN WD5500DH ON WD5500DH.ack_id = WDADH.ack_id
							WHERE ( ( WD5500DH.plan_year ) = '". $year ."' ) AND 
							( ( WDADH.ins_carrier_name_normalized ) = '".$carrier."' )
							GROUP  BY WD5500DH.plan_year, WDADH.ins_carrier_name_normalized";
							
							
						$coverage_overview = $wpdb->get_row($sql, ARRAY_A);
						
						
						
						$health = $dental = $life = $std = $ltd = $vision = $other = $total = $total_participants = 0;
						if (!empty($coverage_overview)) {
							$health = $coverage_overview['SumOfHEALTH_PREM'];
							$dental = $coverage_overview['SumOfDENTAL_PREM'];
							$life = $coverage_overview['SumOfLIFE_PREM'];
							$std = $coverage_overview['SumOfSTD_PREM'];
							$ltd = $coverage_overview['SumOfLTD_PREM'];
							$vision = $coverage_overview['SumOfVISION_PREM'];
							$other = $coverage_overview['SumOfOTHER_PREM'];
							$total = $coverage_overview['SumOfWLFR_TOT_CHARGES_PAID_AMT'];
							$total_participants = $coverage_overview['TOT_ACTIVE_PARTCP_CNT'];
						}
						?>
						<h2>Premium ($K) </h2>
						<?php
						if ($health) {
							?>
							<div class="health"><span>Health:</span> <?php echo number_format($health, 1); ?></div>
						<?php } ?>
						<?php
						if ($dental) {
							?>
							<div class="dental"><span>Dental:</span> <?php echo number_format($dental, 1); ?></div>
						<?php } ?>
						<?php
						if ($life) {
							?>
							<div class="life"><span>Life:</span> <?php echo number_format($life, 1); ?></div>
						<?php } ?>
						<?php
						if ($std) {
							?>
							<div class="std"><span>STD:</span> <?php echo number_format($std, 1); ?></div>
						<?php } ?>
						<?php
						if ($ltd) {
							?>
							<div class="ltd"><span>LTD:</span> <?php echo number_format($ltd, 1); ?></div>
						<?php } ?>
						<?php
						if ($vision) {
							?>
							<div class="vision"><span>Vision:</span> <?php echo number_format($vision, 1); ?></div>
						<?php } ?>
						<?php
						if ($other) {
							?>
							<div class="other"><span>Other:</span> <?php echo number_format($other, 1); ?></div>
						<?php } ?>
						<div class="total"><span>Total:</span> <?php echo number_format($total, 1); ?></div>
					</div>
					<div class="left-co-graph">
						<div id="container-coverage-overview" style="min-width: 310px; height: auto; max-width: 600px; margin: 0 auto"></div>
					</div>
				</div> 

				<script type="text/javascript">
					Highcharts.chart('container-coverage-overview', {
						chart: {
							plotBackgroundColor: null,
							plotBorderWidth: null,
							plotShadow: false,
							type: 'pie',
							width: 450
						},
						title: {
							text: 'Premium By Line'
						},
						tooltip: {
							pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
						},
						plotOptions: {
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: true,
									format: '<b>{point.name}</b>: {point.percentage:.1f} %',
									style: {
										color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
									}
								}
							}
						},
						series: [{
								name: '',
								colorByPoint: true,
								size: '60%',
								innerSize: '40%',
								data: [{
										name: 'Health',
										y: <?php echo $health / $total; ?>,
										sliced: true,
										selected: true
									}, {
										name: 'Dental',
										y: <?php echo $dental / $total; ?>
									}, {
										name: 'Life',
										y: <?php echo $life / $total; ?>
									}, {
										name: 'STD',
										y: <?php echo $std / $total; ?>
									}, {
										name: 'LTD',
										y: <?php echo $ltd / $total; ?>
									}, {
										name: 'Vision',
										y: <?php echo $vision / $total; ?>
									}, {
										name: 'Other',
										y: <?php echo $other / $total; ?>
									}]
							}]
					});
				</script>
				
				<?php
				
				$premium_by_state = "SELECT WD5500DH.PLAN_YEAR, WDADH.INS_CARRIER_NAME_NORMALIZED, WD5500DH.SPONS_DFE_MAIL_US_STATE, Sum(WDADH.WLFR_TOT_CHARGES_PAID_AMT) AS SumOfWLFR_TOT_CHARGES_PAID_AMT
				FROM WDADH INNER JOIN WD5500DH ON WD5500DH.ACK_ID = WDADH.ACK_ID
				WHERE (((WD5500DH.PLAN_YEAR)='". $year ."') AND ((WDADH.INS_CARRIER_NAME_NORMALIZED)='".$carrier."')) GROUP BY WD5500DH.PLAN_YEAR, WDADH.INS_CARRIER_NAME_NORMALIZED, WD5500DH.SPONS_DFE_MAIL_US_STATE";
				
				$result_premium_state = $wpdb->get_results($premium_by_state, ARRAY_A);
				
				?>
				<!-- Start State Sections -->
				<h3>Premium by State</h3>
				<div class="carrier-overview">
					<div class="table-wrap">
						
					</div>
				    <div id="container_us_map"></div>
					<script type="text/javascript">
					   var data = [ 
					   <?php foreach ($result_premium_state as $s_pre) { 
					   $state = '"'. $s_pre['SPONS_DFE_MAIL_US_STATE'] .'"';
					   $total_charge = $s_pre['SumOfWLFR_TOT_CHARGES_PAID_AMT'];
                                           $total_charge = number_format($total_charge,2,".","");
                                           if($total_charge != '0.00' &&  $total_charge > 0){
					   ?>
					   {
							"value": <?php echo $total_charge ; ?>,
							"code": <?php echo $state; ?> },
                                           <?php } }
                                           ?>
							];
					
					   // Make codes uppercase to match the map data
					  jQuery.each(data, function () {
					    this.code = this.code.toUpperCase();
					  });
					  // Instantiate the map
					  Highcharts.mapChart('container_us_map', {

						chart: {
						  map: 'countries/us/us-all',
						  borderWidth: 1
						},

						title: {
						  text: 'Premium by state ($k)'
						},

						exporting: {
						  sourceWidth: 600,
						  sourceHeight: 500
						},

						legend: {
						  layout: 'horizontal',
						  borderWidth: 0,
						  backgroundColor: 'rgba(255,255,255,0.85)',
						  floating: true,
						  verticalAlign: 'top',
						  y: 25
						},

						mapNavigation: {
						  enabled: true
						},

						colorAxis: {
						  min: 1,
						  type: 'logarithmic',
						  minColor: '#EEEEFF',
						  maxColor: '#000022',
						  stops: [
							[0, '#EFEFFF'],
							[0.67, '#4444FF'],
							[1, '#000022']
						  ]
						},

						series: [{
						  animation: {
							duration: 1000
						  },
						  data: data,
						  joinBy: ['postal-code', 'code'],
						  dataLabels: {
							enabled: true,
							color: '#FFFFFF',
							format: '{point.code}'
						  },
						  name: 'Total Charges',
						  tooltip: {
							pointFormat: '{point.code}: {point.value}'
						  }
						}]
					  });
					</script>
				</div>
				
				<!-- End State Sections -->
				
				<!-- Start Top Broker Sections -->
				
				<?php
				
				$top_broker_sql = "SELECT WD5500DH.PLAN_YEAR, WDADH.INS_CARRIER_NAME_NORMALIZED, WDA1DH.INS_BROKER_NAME_NORMALIZED, Sum(WDA1DH.INS_BROKER_REV_HEALTH) AS SumOfINS_BROKER_REV_HEALTH, Sum(WDA1DH.INS_BROKER_REV_LIFE) AS SumOfINS_BROKER_REV_LIFE, Sum(WDA1DH.INS_BROKER_REV_DENTAL) AS SumOfINS_BROKER_REV_DENTAL, Sum(WDA1DH.INS_BROKER_REV_LTD) AS SumOfINS_BROKER_REV_LTD, Sum(WDA1DH.INS_BROKER_REV_STD) AS SumOfINS_BROKER_REV_STD, Sum(WDA1DH.INS_BROKER_REV_VISION) AS SumOfINS_BROKER_REV_VISION, Sum(WDA1DH.INS_BROKER_REV_OTHER) AS SumOfINS_BROKER_REV_OTHER, Sum(WDA1DH.BROKER_REVENUE)
FROM WDADH INNER JOIN WD5500DH ON WD5500DH.ACK_ID = WDADH.ACK_ID 
INNER JOIN WDA1DH ON ((WDADH.FORM_ID = WDA1DH.FORM_ID) AND (WD5500DH.ACK_ID = WDA1DH.ACK_ID))
WHERE (((WD5500DH.PLAN_YEAR)='". $year ."') AND ((WDADH.INS_CARRIER_NAME_NORMALIZED)='".$carrier."'))
GROUP BY WD5500DH.PLAN_YEAR, WDADH.INS_CARRIER_NAME_NORMALIZED, WDA1DH.INS_BROKER_NAME_NORMALIZED
ORDER BY Sum(WDA1DH.BROKER_REVENUE) DESC LIMIT 10";
				
				$result_top_broker = $wpdb->get_results($top_broker_sql, ARRAY_A);
				
				?>
				
				<h3>Top Brokers</h3>
				<div class="carrier-overview">
					<div class="table-wrap">
						
					</div>
					<div id="container_top_broker"></div>
					<script>
					Highcharts.chart('container_top_broker', {
					  chart: {
						type: 'bar'
					  },
					  title: {
						text: 'Top 10 Brokers by Premium'
					  },
					  xAxis: {
						categories: [
						<?php
						foreach ($result_top_broker as $s_pre) {
						$broker_normalized_name = $s_pre['INS_BROKER_NAME_NORMALIZED'];
						?>
						'<?php echo addslashes($broker_normalized_name); ?>',
						<?php } ?>
						]
					  },
					  yAxis: {
						min: 0,
						title: {
						  text: 'label - ($K)'
						}
					  },
					  legend: {
						reversed: true
					  },
					  plotOptions: {
						series: {
						  stacking: 'normal'
						}
					  },
					   series: [{
						name: 'Health',
						data: [
						<?php
						foreach($result_top_broker as $s_pre){
							echo str_replace(',', '', number_format($s_pre['SumOfINS_BROKER_REV_HEALTH'],2)) . ',';
						}
						?>
						]
					  }, {
						name: 'Life',
						data: [
						<?php
						foreach($result_top_broker as $s_pre){
							echo str_replace(',', '', number_format($s_pre['SumOfINS_BROKER_REV_LIFE'],2)) . ',';
						//echo number_format($s_pre['SumOfINS_BROKER_REV_LIFE'],1) . ',';
						}
						?>
						]
					  }, {
						name: 'Dental',
						data: [
						<?php
						foreach($result_top_broker as $s_pre){
							echo str_replace(',', '', number_format($s_pre['SumOfINS_BROKER_REV_DENTAL'],2)) . ',';
						//echo number_format($s_pre['SumOfINS_BROKER_REV_DENTAL'],1) . ',';
						}
						?>
						]
					  },
					  {
						name: 'LTD',
						data: [
						<?php
						foreach($result_top_broker as $s_pre){
							echo str_replace(',', '', number_format($s_pre['SumOfINS_BROKER_REV_LTD'],2)) . ',';
						//echo number_format($s_pre['SumOfINS_BROKER_REV_LTD'],1) . ',';
						}
						?>
						]
					  },
					  {
						name: 'STD',
						data: [
						<?php
						foreach($result_top_broker as $s_pre){
							echo str_replace(',', '', number_format($s_pre['SumOfINS_BROKER_REV_STD'],2)) . ',';
						//echo number_format($s_pre['SumOfINS_BROKER_REV_STD'],1) . ',';
						}
						?>
						]
					  },
					  {
						name: 'Vision',
						data: [
						<?php
						foreach($result_top_broker as $s_pre){
							echo str_replace(',', '', number_format($s_pre['SumOfINS_BROKER_REV_VISION'],2)) . ',';
						//echo number_format($s_pre['SumOfINS_BROKER_REV_VISION'],1) . ',';
						}
						?>
						]
					  },
					  {
						name: 'Other',
						data: [
						<?php
						foreach($result_top_broker as $s_pre){
							echo str_replace(',', '', number_format($s_pre['SumOfINS_BROKER_REV_OTHER'],2)) . ',';
						//echo number_format($s_pre['SumOfINS_BROKER_REV_OTHER'],1) . ',';
						}
						?>
						]
					  }]
					});
					</script>
				    
				</div>
				
				<!-- End Top Broker Sections -->
				
				
				<?php
				$size_segments_sql = "SELECT WD5500DH.PLAN_YEAR, WDADH.INS_CARRIER_NAME_NORMALIZED, EMPLOYER_SIZE.EMPLOYER_SIZE,EMPLOYER_SIZE.MIN,EMPLOYER_SIZE.MAX,EMPLOYER_SIZE.SORT_ORDER, Sum(WDADH.WLFR_TOT_CHARGES_PAID_AMT) AS SumOfWLFR_TOT_CHARGES_PAID_AMT FROM EMPLOYER_SIZE, WDADH INNER JOIN WD5500DH ON WD5500DH.ACK_ID = WDADH.ACK_ID 
				WHERE (((WD5500DH.TOT_ACTIVE_PARTCP_CNT) Between EMPLOYER_SIZE.MIN And EMPLOYER_SIZE.MAX)) AND (((WD5500DH.PLAN_YEAR)='". $year ."') AND ((WDADH.INS_CARRIER_NAME_NORMALIZED)='".$carrier."')) GROUP BY EMPLOYER_SIZE.EMPLOYER_SIZE, EMPLOYER_SIZE.SORT_ORDER ORDER BY EMPLOYER_SIZE.SORT_ORDER";

				
				$result_size_segments = $wpdb->get_results($size_segments_sql, ARRAY_A);
				
				
				?>
				<h3> Employer Size Segments </h3>
				<div class="coverage-overview">
					<div class="left-co">                        
						<div id="container_size_segments" style="min-width: 310px; height: auto; max-width: 600px; margin: 0 auto"></div>
					</div>
					<div class="left-co-graph">
						<table>
							<tr>
								<th>EMPLOYER SIZE</th>
								<th>CHARGES</th>
							</tr>
							<?php
							foreach($result_size_segments as $res){
							?>
							<tr>
								<td><?php echo $res['EMPLOYER_SIZE']?></td>
								<td><?php echo number_format($res['SumOfWLFR_TOT_CHARGES_PAID_AMT'],1)?></td>
							</tr>			
							<?php
							}
							?>
						</table>
					</div>
				</div>
				
				<script type="text/javascript">
					Highcharts.chart('container_size_segments', {
						chart: {
							plotBackgroundColor: null,
							plotBorderWidth: null,
							plotShadow: false,
							type: 'pie',
							width: 450
						},
						title: {
							text: 'Premium By Line'
						},
						tooltip: {
							pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
						},
						plotOptions: {
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: true,
									format: '<b>{point.name}</b>: {point.percentage:.1f} %',
									style: {
										color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
									}
								}
							}
						},
						series: [{
								name: '',
								colorByPoint: true,
								size: '60%',
								innerSize: '40%',
								data: [
								<?php
								foreach($result_size_segments as $res){
								?>
								{
										name: '<?php echo $res['EMPLOYER_SIZE']?>',
										y: <?php echo round($res['SumOfWLFR_TOT_CHARGES_PAID_AMT'],2)?>,
										sliced: true,
										selected: true
								},
								<?php } ?>
								]
							}]
					});
				</script>
				
				<!-- Start Industry segment -->
				
				<?php
				
				$industry_segments_sql = "SELECT WD5500DH.PLAN_YEAR, WDADH.INS_CARRIER_NAME_NORMALIZED, WD5500DH.NAICS_2_Digit_Industry_Detail, Sum(WDADH.WLFR_TOT_CHARGES_PAID_AMT) AS SumOfWLFR_TOT_CHARGES_PAID_AMT
				FROM WDADH INNER JOIN WD5500DH ON WD5500DH.ACK_ID = WDADH.ACK_ID
				WHERE (((WD5500DH.PLAN_YEAR)='". $year ."') AND ((WDADH.INS_CARRIER_NAME_NORMALIZED)='".$carrier."'))
				GROUP BY WD5500DH.PLAN_YEAR, WDADH.INS_CARRIER_NAME_NORMALIZED, WD5500DH.NAICS_2_Digit_Industry_Detail ORDER BY SumOfWLFR_TOT_CHARGES_PAID_AMT DESC";
                // echo $industry_segments_sql;
				$result_industry_segments = $wpdb->get_results($industry_segments_sql, ARRAY_A);
				
				
				?>
				<h3> Industry Segmentation </h3>
				<div class="coverage-overview">
					<div id="container_industry_segments" style="min-width: 310px; height: auto; max-width: 600px; margin: 0 auto"></div>
				</div>
				<script type="text/javascript">
					Highcharts.chart('container_industry_segments', {
						chart: {
							plotBackgroundColor: null,
							plotBorderWidth: null,
							plotShadow: false,
							type: 'pie',
							/* width: 450 */
						},
						title: {
							text: 'Premium By Industry ($M)'
						},
						tooltip: {
							pointFormat: '{series.name}: <b>$ {point.percentage:.1f}</b>'
						},
						plotOptions: {
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: true,
									format: '<b>{point.name}</b>: $ {point.percentage:.1f}',
									style: {
										color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
									}
								}
							}
						},
						series: [{
								name: '',
								colorByPoint: true,
								size: '60%',
								innerSize: '40%',
								data: [
								<?php
								foreach($result_industry_segments as $res){
								$Industry_Detail = trim($res['NAICS_2_Digit_Industry_Detail'], '"');
								
								?>
								{
										name: '<?php echo $Industry_Detail; ?>',
										y: <?php echo $res['SumOfWLFR_TOT_CHARGES_PAID_AMT']; ?>,
										sliced: true,
										selected: true
								},
								<?php } ?>
								]
							}]
					});
				</script>
				
				<!-- End Industry segment -->
				
				<!-- Start Premium Overtime -->
				
				<?php
				$min_max_year = $wpdb->get_row("SELECT MIN(PLAN_YEAR) AS mini, MAX(PLAN_YEAR) AS maxi FROM WD5500DH", ARRAY_A);
				$premium_overtime_sql = "SELECT WD5500DH.plan_year, 
					   WDADH.ins_carrier_name_normalized, 
					   Sum(WDADH.health_prem) AS SumOfHEALTH_PREM, 
					   Sum(WDADH.dental_prem) AS SumOfDENTAL_PREM, 
					   Sum(WDADH.life_prem)   AS SumOfLIFE_PREM, 
					   Sum(WDADH.ltd_prem)    AS SumOfLTD_PREM, 
					   Sum(WDADH.std_prem)    AS SumOfSTD_PREM, 
					   Sum(WDADH.vision_prem) AS SumOfVISION_PREM, 
					   Sum(WDADH.other_prem)  AS SumOfOTHER_PREM
				FROM   WDADH 
					   INNER JOIN WD5500DH 
							   ON WD5500DH.ack_id = WDADH.ack_id 
				WHERE  ( ( ( WD5500DH.plan_year ) > '2008' ) 
						 AND ( ( WDADH.ins_carrier_name_normalized ) = 
								   '".$carrier."' ) ) 
				GROUP  BY WD5500DH.plan_year, 
						  WDADH.ins_carrier_name_normalized 
				ORDER  BY WD5500DH.plan_year";

				$result_premium_overtime = $wpdb->get_results($premium_overtime_sql, ARRAY_A);
				
				$values = array();
				
				
				foreach($result_premium_overtime as $val){
					$pre_values = array();
					$pre_values['Health'] = $val['SumOfHEALTH_PREM'];
					
					$pre_values['Dental'] = $val['SumOfDENTAL_PREM'];
					$pre_values['Life'] = $val['SumOfLIFE_PREM'];
					$pre_values['LTD'] = $val['SumOfLTD_PREM'];
					$pre_values['STD'] = $val['SumOfSTD_PREM'];
					$pre_values['VISION'] = $val['SumOfVISION_PREM'];
					$pre_values['OTHER'] = $val['SumOfOTHER_PREM'];
					arsort($pre_values);
					$values[$val['plan_year']] = $pre_values; 
				}
			
				
				
				$tmp_arr = array();
				foreach($values as $key=>$val){
					$tmp_arr = $val;
					break;
				}
				$falg = 0;
				foreach($values as $key=>$val){
					if($flag > 0){
						$tmp_arr = array_merge_recursive($tmp_arr,$val);
						
					}
					$flag++;
					
				}
				
				?>
				<h3> Historical Premium </h3>
				<div class="coverage-overview">
					<div id="container_premium_overtime"></div>
				</div>
				<script>
				Highcharts.chart('container_premium_overtime', {
				  chart: {
				    type: 'area'
				  },
				  title: {
				    text: 'Historical Premium By Line'
				  },
				  xAxis: {
				    /*categories: [
					<?php foreach($values as $res=>$val){ ?>
					'<?php echo $res; ?>',
					<?php } ?>
					],*/
					categories: [<?php for ($i = 2009; $i <= $min_max_year['maxi']; $i++) {
						echo "'{$i}'" . ",";
					} ?>],
				    tickmarkPlacement: 'on',
				    title: {
				      enabled: false
				    }
				  },
				  yAxis: {
				    labels: {
				      formatter: function () {
				        return this.value / 1000;
				      }
				    }
				  },
				  tooltip: {
				    split: true,
				    valueSuffix: ' millions'
				  },
				  plotOptions: {
				    area: {
				      stacking: 'normal',
				      lineColor: '#666666',
				      lineWidth: 1,
				      marker: {
				        lineWidth: 1,
				        lineColor: '#666666'
				      }
				    }
				  },
				  /* series: [
				  <?php
				  foreach ($values as $key => $val) 
				  {
						$js_s = '';
						
						foreach($val as $vals){
								
									$main_data = isset($vals) ? str_replace(',', '', number_format($vals, 1)) : '0.0';
									$js_s .= "$main_data" . ", ";
						}
					  echo "{ name: '" . $key . "', data: [" . $js_s . "] }, "; 
				  }
				  ?>
				  ] */
				  series: [
				  {
				    name: 'Vision',
				    data: [<?php 
					//rsort($vision);
					foreach($tmp_arr['VISION'] as $res){
						echo round($res,2). ',';
					}	
					?>
					]
				  },
				  {
				    name: 'Health',
				    data: [
					<?php 
					//rsort($Health);
					foreach($tmp_arr['Health'] as $val){
						echo round($val,2) . ',';
					}
					?>
					]
				  },
				  {
				    name: 'Dental',
				    data: [
					<?php
					//rsort($Dental);
					foreach($tmp_arr['Dental'] as $res){
						echo round($res,2) . ',';
					}
					?>
					]
				  },
				  {
				    name: 'Life',
				    data: [
					<?php 
					//rsort($life);
					foreach($tmp_arr['Life'] as $res){
						echo round($res,2). ',';
					}
					?>
					]
				  },
				  {
				    name: 'Other',
				    data: [<?php 
					//rsort($other);
					foreach($tmp_arr['OTHER'] as $res){
						echo round($res,2). ',';
					}
					?>
					]
				  },
				  {
				    name: 'LTD',
				    data: [<?php 
					//rsort($ltd);
					foreach($tmp_arr['LTD'] as $res){
						echo round($res,2). ',';
					}
					?>
					]
				  },
				  {
				    name: 'STD',
				    data: [<?php 
					//rsort($std);
					foreach($tmp_arr['STD'] as $res){
						echo round($res,2). ',';
					}
					?>
					]
				  },
				  ]
				});
				</script>
				
				<!-- End Premium Overtime -->
				
				<!-- Start Historical Clients -->
				
				<?php
				$historical_client_sql = "SELECT data.plan_year, count(data.maxofins_prsn_covered_eoy_cnt) AS count from(
				SELECT WD5500DH.plan_year, 
							   Max(WDADH.ins_prsn_covered_eoy_cnt) AS 
							   maxofins_prsn_covered_eoy_cnt 
						FROM   WDADH 
							   INNER JOIN WD5500DH 
									   ON WD5500DH.ack_id = WDADH.ack_id 
						WHERE  ( ( ( WD5500DH.plan_year ) > '2008' ) 
								 AND ( ( WDADH.ins_carrier_name_normalized ) = 
										   '".$carrier."' ) ) 
						GROUP  BY WD5500DH.plan_year, 
								  WDADH.ins_carrier_name_normalized, 
								  WD5500DH.spons_dfe_ein 
						ORDER  BY WD5500DH.plan_year ) AS data
				GROUP BY data.plan_year;";

				$result_historical_client = $wpdb->get_results($historical_client_sql, ARRAY_A);
				
				?>
				<h3> Historical Clients </h3>
				<div class="coverage-overview">
					<div id="container_history_clients"></div>
				</div>
				<script>
				Highcharts.chart('container_history_clients', {
				  chart: {
					type: 'area'
				  },
				  title: {
					text: 'Historical Clients'
				  },
				  
				  xAxis: {
					allowDecimals: false,
					labels: {
					  formatter: function () {
						return this.value; // clean, unformatted number for year
					  }
					}
				  },
				  yAxis: {
					labels: {
					  formatter: function () {
						return this.value / 1000 + 'k';
					  }
					}
				  },
				  tooltip: {
					pointFormat: 'Total clients <b> {point.y:,.0f} </b><br/> in {point.x} Plan Year'
				  },
				  plotOptions: {
					area: {
					  pointStart: <?php if(isset($result_historical_client[0]['plan_year']) && $result_historical_client[0]['plan_year']!= "") { echo $result_historical_client[0]['plan_year']; }else{ echo $year; }?>,
					  marker: {
						enabled: false,
						symbol: 'circle',
						radius: 2,
						states: {
						  hover: {
							enabled: true
						  }
						}
					  }
					},
				  },
				  series: [{
					showInLegend: false,
					data: [
					  <?php foreach($result_historical_client as $res_historic){ 
						echo $res_historic['count'] . ','; 
					   } ?>
					]
				  }]
				});
				</script>
				
				
				<!-- End Historical Clients -->
				
				
				<!-- Start LivesOver Time -->
				
				<?php
				$LivesOver_client_sql = "SELECT data.plan_year, data.maxofins_prsn_covered_eoy_cnt from(
				SELECT WD5500DH.plan_year, 
							   Max(WDADH.ins_prsn_covered_eoy_cnt) AS 
							   maxofins_prsn_covered_eoy_cnt 
						FROM   WDADH 
							   INNER JOIN WD5500DH 
									   ON WD5500DH.ack_id = WDADH.ack_id 
						WHERE  ( ( ( WD5500DH.plan_year ) > '2008' ) 
								 AND ( ( WDADH.ins_carrier_name_normalized ) = 
										   '".$carrier."' ) ) 
						GROUP  BY WD5500DH.plan_year, 
								  WDADH.ins_carrier_name_normalized, 
								  WD5500DH.spons_dfe_ein 
						ORDER  BY WD5500DH.plan_year ) AS data
				GROUP BY data.plan_year";
				

				$result_LivesOver_client = $wpdb->get_results($LivesOver_client_sql, ARRAY_A);
				
				?>
				<h3> Historical Lives Covered </h3>
				<div class="coverage-overview">
					<div id="container_LivesOver_client"></div>
				</div>
				<script>
				Highcharts.chart('container_LivesOver_client', {
				  chart: {
					type: 'area'
				  },
				  title: {
					text: 'Historical Lives Covered'
				  },
				  
				  xAxis: {
					allowDecimals: false,
					labels: {
					  formatter: function () {
						return this.value; // clean, unformatted number for year
					  }
					}
				  },
				  yAxis: {
					labels: {
					  formatter: function () {
						return this.value / 1000 + 'k';
					  }
					}
				  },
				  tooltip: {
					pointFormat: 'Total employees covered <b>{point.y:,.0f}</b><br/>in {point.x} Plan year'
				  },
				  plotOptions: {
					area: {
					  pointStart: <?php if(isset($result_LivesOver_client[0]['plan_year']) && $result_LivesOver_client[0]['plan_year'] != "") {echo $result_LivesOver_client[0]['plan_year']; }else{ echo $year; } ?>,
					  marker: {
						enabled: false,
						symbol: 'circle',
						radius: 2,
						states: {
						  hover: {
							enabled: true
						  }
						}
					  }
					},
				  },
				  series: [{
					showInLegend: false,
					data: [
					  <?php foreach($result_LivesOver_client as $res_historic){ 
						echo $res_historic['maxofins_prsn_covered_eoy_cnt'] . ','; 
					   } ?>
					]
				  }]
				});
				</script>
				
				<!-- End LivesOver Time -->
				
				<!-- Start Top 50 Clients -->
				
				<?php
				$top_client_sql = "SELECT WD5500DH.plan_year, 
					   WDADH.ins_carrier_name_normalized, 
					   WD5500DH.sponsor_dfe_name, 
					   WD5500DH.SPONS_DFE_EIN,
					   Sum(WDADH.ins_prsn_covered_eoy_cnt)  AS SumOfINS_PRSN_COVERED_EOY_CNT, 
					   Sum(WDADH.health_prem)               AS SumOfHEALTH_PREM, 
					   Sum(WDADH.dental_prem)               AS SumOfDENTAL_PREM, 
					   Sum(WDADH.life_prem)                 AS SumOfLIFE_PREM, 
					   Sum(WDADH.ltd_prem)                  AS SumOfLTD_PREM, 
					   Sum(WDADH.std_prem)                  AS SumOfSTD_PREM, 
					   Sum(WDADH.vision_prem)               AS SumOfVISION_PREM, 
					   Sum(WDADH.other_prem)                AS SumOfOTHER_PREM, 
					   Sum(WDADH.wlfr_tot_charges_paid_amt) AS SumOfWLFR_TOT_CHARGES_PAID_AMT 
				FROM   WDADH  
					   INNER JOIN WD5500DH ON WD5500DH.ack_id = WDADH.ack_id
				WHERE ( ( ( WD5500DH.plan_year ) = '". $year ."' ) 
						 AND 
				( 
				( WDADH.ins_carrier_name_normalized ) = '".$carrier."' ) )
				GROUP  BY WD5500DH.plan_year, 
						  WDADH.ins_carrier_name_normalized, 
						  WD5500DH.sponsor_dfe_name 
				ORDER  BY Sum(WDADH.wlfr_tot_charges_paid_amt) DESC LIMIT 50";
				$result_top_client = $wpdb->get_results($top_client_sql, ARRAY_A);
				?>
				<h3>Top 50 Clients</h3>
				<div class="carrier-overview">
					<div class="table-wrap">
						<table>
							<tr>
								<th>Premium ($K)</th>
								<th>Health</th>
								<th>Dental</th>
								<th>Life</th>
								<th>STD</th>
								<th>LTD</th>
								<th>Vision</th>
								<th>Other</th>
								<th>Total</th>
							</tr>
							<?php
							if ($result_top_client) {
								
								$plan_year = $carrier_name = $state = $total_charge = 0;
								
								foreach ($result_top_client as $s_pre) {
								
									
									$sponsor_dfe_name = $s_pre['sponsor_dfe_name'];
									$health = $s_pre['SumOfHEALTH_PREM'];
									$dental = $s_pre['SumOfDENTAL_PREM'];
									$life = $s_pre['SumOfLIFE_PREM'];
									$ltd = $s_pre['SumOfLTD_PREM'];
									$std = $s_pre['SumOfSTD_PREM'];
									$vision = $s_pre['SumOfVISION_PREM'];
									$other = $s_pre['SumOfOTHER_PREM'];
									$total = $s_pre['SumOfWLFR_TOT_CHARGES_PAID_AMT'];
									?>
									<tr class="sent_sponser_page" data-id="<?php echo $s_pre['SPONS_DFE_EIN']?>" >
										<td><?php echo $sponsor_dfe_name; ?></td>
										<td><?php echo number_format($health, 1); ?></td>
										<td><?php echo number_format($dental, 1); ?></td>
										<td><?php echo number_format($life, 1); ?></td>
										<td><?php echo number_format($ltd, 1); ?></td>
										<td><?php echo number_format($std, 1); ?></td>
										<td><?php echo number_format($vision, 1); ?></td>
										<td><?php echo number_format($other, 1); ?></td>
										<td><?php echo number_format($total, 1); ?></td>
										
									</tr>
									<?php
								}
							}
							?>
						</table>
					</div>
				</div>
				
				<!-- End Top 50 Clients -->
				
				
				</div>
				
				</div>
				
			<?php
		/* } */
	} else {
		echo '<div class="err-div"><p>Carrier Name Is Missing</p></div>';
	}
	?>
	</div>
</div>
<?php
get_footer();