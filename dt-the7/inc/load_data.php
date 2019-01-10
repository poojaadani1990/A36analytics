<?php
//$year = $_POST['year'];
    global $wpdb;
    
    $tbl_WD5500DH = 'WD5500DH';
    //$tbl_WD5500DH = 'wd5500dh';
    $tbl_WDA1DH = 'WDA1DH';
    //$tbl_WDA1DH = 'wda1dh';
    $tbl_WDADH = 'WDADH';
    //$tbl_WDADH = 'wdadh';
    $tbl_EIN_COMPANY_NAMES = 'EIN_COMPANY_NAMES';
    //$tbl_EIN_COMPANY_NAMES = 'ein_company_names';


    $result = $wpdb->get_results("SELECT * FROM {$tbl_WD5500DH} WHERE SPONS_DFE_EIN = '$ein' AND PLAN_YEAR = '{$year}' GROUP BY PLAN_YEAR", ARRAY_A);
    ?>
    <div class="display-all-overview">
        <h3>Coverage Overview</h3>
        <div class="coverage-overview">
            <div class="left-co">                        
                <?php /*<div class="latest-plan-year">
                    <span>Latest Plan Year:</span> <?php echo $result[0]['PLAN_YEAR']; ?>
                </div>
                <div class="latest-plan-year">
                    <span>Total Participants:</span> <?php echo number_format($result[0]['TOT_ACTIVE_PARTCP_CNT'], 0); ?>
                </div> */ ?>                    
    <?php

                                
    $coverage_overview = $wpdb->get_row("SELECT Sum(t3.HEALTH_PREM) AS SumOfHEALTH_PREM, Sum(t3.DENTAL_PREM) AS SumOfDENTAL_PREM, Sum(t3.LIFE_PREM) AS SumOfLIFE_PREM, Sum(t3.STD_PREM) AS SumOfSTD_PREM, Sum(t3.LTD_PREM) AS SumOfLTD_PREM, Sum(t3.VISION_PREM) AS SumOfVISION_PREM, Sum(t3.OTHER_PREM) AS SumOfOTHER_PREM, Sum(t3.WLFR_TOT_CHARGES_PAID_AMT) AS SumOfWLFR_TOT_CHARGES_PAID_AMT
                                FROM {$tbl_EIN_COMPANY_NAMES} AS t4 INNER JOIN ({$tbl_WD5500DH} AS t1 INNER JOIN ({$tbl_WDA1DH} AS t2 INNER JOIN {$tbl_WDADH} AS t3 ON (t2.ACK_ID = t3.ACK_ID) AND (t2.FORM_ID = t3.FORM_ID)) ON t1.ACK_ID = t3.ACK_ID) ON t4.SPONS_DFE_EIN = t1.SPONS_DFE_EIN
                                WHERE t4.SPONS_DFE_EIN = {$ein}
                                GROUP BY t4.SPONS_DFE_EIN, t1.PLAN_YEAR
                                HAVING (((t4.SPONS_DFE_EIN)={$ein}) AND ((t1.PLAN_YEAR)={$year}))", ARRAY_A);
    $health = $dental = $life = $std = $ltd = $vision = $other = $total = 0;
    if (!empty($coverage_overview)) {
        $health = $coverage_overview['SumOfHEALTH_PREM'];
        $dental = $coverage_overview['SumOfDENTAL_PREM'];
        $life = $coverage_overview['SumOfLIFE_PREM'];
        $std = $coverage_overview['SumOfSTD_PREM'];
        $ltd = $coverage_overview['SumOfLTD_PREM'];
        $vision = $coverage_overview['SumOfVISION_PREM'];
        $other = $coverage_overview['SumOfOTHER_PREM'];
        $total = $coverage_overview['SumOfWLFR_TOT_CHARGES_PAID_AMT'];
        
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
        <!--Carrier Overview Section-->
    <?php
    $carrier_overview = $wpdb->get_results("SELECT t3.INS_CARRIER_NAME_NORMALIZED, Sum(t3.HEALTH_PREM) AS SumOfHEALTH_PREM, Sum(t3.DENTAL_PREM) AS SumOfDENTAL_PREM, Sum(t3.LIFE_PREM) AS SumOfLIFE_PREM, Sum(t3.STD_PREM) AS SumOfSTD_PREM, Sum(t3.LTD_PREM) AS SumOfLTD_PREM, Sum(t3.VISION_PREM) AS SumOfVISION_PREM, Sum(t3.OTHER_PREM) AS SumOfOTHER_PREM, Sum(t3.WLFR_TOT_CHARGES_PAID_AMT) AS SumOfWLFR_TOT_CHARGES_PAID_AMT
                        FROM ({$tbl_WD5500DH} AS t1 INNER JOIN ({$tbl_WDA1DH} AS t2 INNER JOIN {$tbl_WDADH} AS t3 ON (t2.FORM_ID = t3.FORM_ID) AND (t2.ACK_ID = t3.ACK_ID)) ON t1.ACK_ID = t3.ACK_ID) INNER JOIN {$tbl_EIN_COMPANY_NAMES} AS t4 ON t1.SPONS_DFE_EIN = t4.SPONS_DFE_EIN
                        WHERE t4.SPONS_DFE_EIN = {$ein}
                        GROUP BY t3.INS_CARRIER_NAME_NORMALIZED, t1.PLAN_YEAR, t4.SPONS_DFE_EIN
                        HAVING (((t1.PLAN_YEAR)={$year}) AND ((t4.SPONS_DFE_EIN)={$ein})) ORDER BY SumOfWLFR_TOT_CHARGES_PAID_AMT DESC ", ARRAY_A);
                        
    ?>
        <h3>Carrier Overview</h3>
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
                        <th>%</th>
                    </tr>
    <?php
    if ($carrier_overview) {
        $pr_health = $pr_dental = $pr_life = $pr_std = $pr_ltd = $pr_vision = $pr_other = $pr_total = 0;
        foreach ($carrier_overview as $key => $s_pre) {
            $pr_health = $pr_health + $s_pre['SumOfHEALTH_PREM'];
            $pr_dental = $pr_dental + $s_pre['SumOfDENTAL_PREM'];
            $pr_life = $pr_life + $s_pre['SumOfLIFE_PREM'];
            $pr_std = $pr_std + $s_pre['SumOfSTD_PREM'];
            $pr_ltd = $pr_ltd + $s_pre['SumOfLTD_PREM'];
            $pr_vision = $pr_vision + $s_pre['SumOfVISION_PREM'];
            $pr_other = $pr_other + $s_pre['SumOfOTHER_PREM'];
            $pr_total = $pr_total + $s_pre['SumOfWLFR_TOT_CHARGES_PAID_AMT'];
            ?>
                            <tr>
                                <td><?php echo $s_pre['INS_CARRIER_NAME_NORMALIZED']; ?></td>
                                <td><?php echo number_format($s_pre['SumOfHEALTH_PREM'], 1); ?></td>
                                <td><?php echo number_format($s_pre['SumOfDENTAL_PREM'], 1); ?></td>
                                <td><?php echo number_format($s_pre['SumOfLIFE_PREM'], 1); ?></td>
                                <td><?php echo number_format($s_pre['SumOfSTD_PREM'], 1); ?></td>
                                <td><?php echo number_format($s_pre['SumOfLTD_PREM'], 1); ?></td>
                                <td><?php echo number_format($s_pre['SumOfVISION_PREM'], 1); ?></td>
                                <td><?php echo number_format($s_pre['SumOfOTHER_PREM'], 1); ?></td>
                                <td><?php echo number_format($s_pre['SumOfWLFR_TOT_CHARGES_PAID_AMT'], 1); ?></td>
                                <td><?php echo number_format($s_pre['SumOfWLFR_TOT_CHARGES_PAID_AMT'] / $total * 100, 1); ?></td>
                            </tr>
            <?php
        }
        ?>
                        <tr>
                            <td>Total</td>
                            <td><?php echo number_format($pr_health, 1); ?></td>
                            <td><?php echo number_format($pr_dental, 1); ?></td>
                            <td><?php echo number_format($pr_life, 1); ?></td>
                            <td><?php echo number_format($pr_std, 1); ?></td>
                            <td><?php echo number_format($pr_ltd, 1); ?></td>
                            <td><?php echo number_format($pr_vision, 1); ?></td>
                            <td><?php echo number_format($pr_other, 1); ?></td>
                            <td><?php echo number_format($pr_total, 1); ?></td>
                            <td><?php echo '100'; ?></td>
                        </tr>
                        <tr>
                            <td>%</td>
                            <td><?php echo ( $total > 0 ) ? number_format($health / $total * 100, 1) : 0; ?></td>
                            <td><?php echo ( $total > 0 ) ? number_format($dental / $total * 100, 1) : 0; ?></td>
                            <td><?php echo ( $total > 0 ) ? number_format($life / $total * 100, 1) : 0; ?></td>
                            <td><?php echo ( $total > 0 ) ? number_format($std / $total * 100, 1) : 0; ?></td>
                            <td><?php echo ( $total > 0 ) ? number_format($ltd / $total * 100, 1) : 0; ?></td>
                            <td><?php echo ( $total > 0 ) ? number_format($vision / $total * 100, 1) : 0; ?></td>
                            <td><?php echo ( $total > 0 ) ? number_format($other / $total * 100, 1) : 0; ?></td>
                            <td>100</td>
                            <td><?php echo '100'; ?></td>
                        </tr>
        <?php
    }
    ?>
                </table>
            </div>
            <script type="text/javascript">
                Highcharts.chart('container-coverage-overview', {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie',
                        width: 500
                    },
                    title: {
                        text: 'Coverage Overview'
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
                                    y: <?php if ($health) { echo ( $total > 0 ) ? $health / $total : 0; }else{ echo 'null'; } ?>,
                                    sliced: true,
                                    selected: true
                                }, {
                                    name: 'Dental',
                                    y: <?php if ($dental) { echo ( $total > 0 ) ? $dental / $total : 0; }else{ echo 'null'; } ?>
                                }, {
                                    name: 'Life',
                                    y: <?php if ($life) { echo ( $total > 0 ) ? $life / $total : 0; }else{ echo 'null'; } ?>
                                }, {
                                    name: 'STD',
                                    y: <?php if ($std) { echo ( $total > 0 ) ? $std / $total : 0; }else{ echo 'null'; } ?>
                                }, {
                                    name: 'LTD',
                                    y: <?php if ($ltd) { echo ( $total > 0 ) ? $ltd / $total : 0; }else{ echo 'null'; } ?>
                                }, {
                                    name: 'Vision',
                                    y: <?php if ($vision) { echo ( $total > 0 ) ? $vision / $total : 0; }else{ echo 'null'; } ?>
                                }, {
                                    name: 'Other',
                                    y: <?php if ($other) { echo ( $total > 0 ) ? $other / $total : 0; }else{ echo 'null'; } ?>
                                }]
                        }]
                });
            </script>
        </div>
        <!--Broker Overview Section-->
        <h3>Broker Overview</h3>
    <?php
    /* $broker_overview = $wpdb->get_results("SELECT t1.PLAN_YEAR, t2.INS_BROKER_NAME_NORMALIZED, Sum(t2.INS_BROKER_REV_HEALTH) AS SumOfINS_BROKER_REV_HEALTH, Sum(t2.INS_BROKER_REV_DENTAL) AS SumOfINS_BROKER_REV_DENTAL, Sum(t2.INS_BROKER_REV_LIFE) AS SumOfINS_BROKER_REV_LIFE, Sum(t2.INS_BROKER_REV_STD) AS SumOfINS_BROKER_REV_STD, Sum(t2.INS_BROKER_REV_LTD) AS SumOfINS_BROKER_REV_LTD, Sum(t2.INS_BROKER_REV_VISION) AS SumOfINS_BROKER_REV_VISION, Sum(t2.INS_BROKER_REV_OTHER) AS SumOfINS_BROKER_REV_OTHER, Sum(t2.BROKER_REVENUE) AS SumOfINS_BROKER_REV_TOT"
      . " FROM ({$tbl_WD5500DH} AS t1 INNER JOIN ({$tbl_WDA1DH} AS t2 INNER JOIN {$tbl_WDADH} AS t3 ON (t2.FORM_ID = t3.FORM_ID) AND (t2.ACK_ID = t3.ACK_ID)) ON t1.ACK_ID = t3.ACK_ID) INNER JOIN {$tbl_EIN_COMPANY_NAMES} AS t4 ON t1.SPONS_DFE_EIN = t4.SPONS_DFE_EIN"
      . " WHERE t4.SPONS_DFE_EIN = {$ein} AND t1.SPONS_DFE_EIN = {$ein} AND t1.PLAN_YEAR= {$year}"
      . " GROUP BY t1.PLAN_YEAR, t4.SPONS_DFE_EIN, t1.SPONS_DFE_EIN, t2.INS_BROKER_NAME_NORMALIZED", ARRAY_A); */
    $broker_overview = $wpdb->get_results("SELECT t1.PLAN_YEAR, t2.INS_BROKER_NAME_NORMALIZED, Sum(t2.INS_BROKER_REV_HEALTH) AS SumOfINS_BROKER_REV_HEALTH, Sum(t2.INS_BROKER_REV_DENTAL) AS SumOfINS_BROKER_REV_DENTAL, Sum(t2.INS_BROKER_REV_LIFE) AS SumOfINS_BROKER_REV_LIFE, Sum(t2.INS_BROKER_REV_STD) AS SumOfINS_BROKER_REV_STD, Sum(t2.INS_BROKER_REV_LTD) AS SumOfINS_BROKER_REV_LTD, Sum(t2.INS_BROKER_REV_VISION) AS SumOfINS_BROKER_REV_VISION, Sum(t2.INS_BROKER_REV_OTHER) AS SumOfINS_BROKER_REV_OTHER, Sum(t2.BROKER_REVENUE) AS SumOfINS_BROKER_REV_TOT"
            . " FROM ({$tbl_WD5500DH} AS t1 INNER JOIN ({$tbl_WDA1DH} AS t2 INNER JOIN {$tbl_WDADH} AS t3 ON (t2.FORM_ID = t3.FORM_ID) AND (t2.ACK_ID = t3.ACK_ID)) ON t1.ACK_ID = t3.ACK_ID) INNER JOIN {$tbl_EIN_COMPANY_NAMES} AS t4 ON t1.SPONS_DFE_EIN = t4.SPONS_DFE_EIN"
            . " WHERE t4.SPONS_DFE_EIN = {$ein} AND t1.SPONS_DFE_EIN = {$ein} AND t1.PLAN_YEAR= {$year}"
            . " GROUP BY t1.PLAN_YEAR, t4.SPONS_DFE_EIN, t1.SPONS_DFE_EIN, t2.INS_BROKER_NAME_NORMALIZED ORDER BY SumOfINS_BROKER_REV_TOT DESC", ARRAY_A);
            
    ?>
        <div class="broker-overview">
            <div class="table-wrap">
                <table>
                    <tr>
                        <th>Revenue ($K)</th>
                        <th>Health</th>
                        <th>Dental</th>
                        <th>Life</th>
                        <th>STD</th>
                        <th>LTD</th>
                        <th>Vision</th>
                        <th>Other</th>
                        <th>Total</th>
                        <th>%</th>
                    </tr>
    <?php
    if ($broker_overview) {
        $br_health = $br_dental = $br_life = $br_std = $br_ltd = $br_vision = $br_other = $br_total = 0;
        foreach ($broker_overview as $key => $b_single) {
            $br_main_total = $br_main_total + $b_single['SumOfINS_BROKER_REV_TOT'];
        }
        foreach ($broker_overview as $key => $b_single) {
            $br_health = $br_health + $b_single['SumOfINS_BROKER_REV_HEALTH'];
            $br_dental = $br_dental + $b_single['SumOfINS_BROKER_REV_DENTAL'];
            $br_life = $br_life + $b_single['SumOfINS_BROKER_REV_LIFE'];
            $br_std = $br_std + $b_single['SumOfINS_BROKER_REV_STD'];
            $br_ltd = $br_ltd + $b_single['SumOfINS_BROKER_REV_LTD'];
            $br_vision = $br_vision + $b_single['SumOfINS_BROKER_REV_VISION'];
            $br_other = $br_other + $b_single['SumOfINS_BROKER_REV_OTHER'];
            $br_total = $br_total + $b_single['SumOfINS_BROKER_REV_TOT'];
            ?>
                            <tr>
                                <td><?php echo $b_single['INS_BROKER_NAME_NORMALIZED']; ?></td>
                                <td><?php echo number_format($b_single['SumOfINS_BROKER_REV_HEALTH'], 1); ?></td>
                                <td><?php echo number_format($b_single['SumOfINS_BROKER_REV_DENTAL'], 1); ?></td>
                                <td><?php echo number_format($b_single['SumOfINS_BROKER_REV_LIFE'], 1); ?></td>
                                <td><?php echo number_format($b_single['SumOfINS_BROKER_REV_STD'], 1); ?></td>
                                <td><?php echo number_format($b_single['SumOfINS_BROKER_REV_LTD'], 1); ?></td>
                                <td><?php echo number_format($b_single['SumOfINS_BROKER_REV_VISION'], 1); ?></td>
                                <td><?php echo number_format($b_single['SumOfINS_BROKER_REV_OTHER'], 1); ?></td>
                                <td><?php echo number_format($b_single['SumOfINS_BROKER_REV_TOT'], 1); ?></td>
                                <td><?php echo number_format($b_single['SumOfINS_BROKER_REV_TOT'] / $br_main_total * 100, 1); ?></td>
                            </tr>
            <?php
        }
        ?>
                        <tr>
                            <td>Total</td>
                            <td><?php echo number_format($br_health, 1); ?></td>
                            <td><?php echo number_format($br_dental, 1); ?></td>
                            <td><?php echo number_format($br_life, 1); ?></td>
                            <td><?php echo number_format($br_std, 1); ?></td>
                            <td><?php echo number_format($br_ltd, 1); ?></td>
                            <td><?php echo number_format($br_vision, 1); ?></td>
                            <td><?php echo number_format($br_other, 1); ?></td>
                            <td><?php echo number_format($br_total, 1); ?></td>
                            <td><?php echo "100"; ?></td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td><?php echo number_format($br_health / $br_main_total * 100, 1); ?></td>
                            <td><?php echo number_format($br_dental / $br_main_total * 100, 1); ?></td>
                            <td><?php echo number_format($br_life / $br_main_total * 100, 1); ?></td>
                            <td><?php echo number_format($br_std / $br_main_total * 100, 1); ?></td>
                            <td><?php echo number_format($br_ltd / $br_main_total * 100, 1); ?></td>
                            <td><?php echo number_format($br_vision / $br_main_total * 100, 1); ?></td>
                            <td><?php echo number_format($br_other / $br_main_total * 100, 1); ?></td>
                            <td><?php echo "100"; ?></td>
                            <td><?php echo "100"; ?></td>
                        </tr>
        <?php
    }
    ?>
                </table>
            </div>
        </div>
        <!--Policy Detail Section-->
    <?php
    $policy_detail = $wpdb->get_results("SELECT t3.ACK_ID, t3.FORM_ID,t1.SPONS_DFE_EIN, t1.PLAN_YEAR, t3.SCH_A_PLAN_NUM, t3.INS_CARRIER_NAME_NORMALIZED, t1.PLAN_NAME, t3.INS_CONTRACT_NUM, t3.SCH_A_PLAN_YEAR_BEGIN_DATE, t3.SCH_A_PLAN_YEAR_END_DATE, Sum(t3.HEALTH_PREM) AS SumOfHEALTH_PREM, Sum(t3.DENTAL_PREM) AS SumOfDENTAL_PREM, Sum(t3.LIFE_PREM) AS SumOfLIFE_PREM, Sum(t3.STD_PREM) AS SumOfSTD_PREM, Sum(t3.LTD_PREM) AS SumOfLTD_PREM, Sum(t3.VISION_PREM) AS SumOfVISION_PREM, Sum(t3.OTHER_PREM) AS SumOfOTHER_PREM, Sum(t3.WLFR_TOT_CHARGES_PAID_AMT) AS SumOfWLFR_TOT_CHARGES_PAID_AMT FROM {$tbl_WD5500DH} AS t1 INNER JOIN ({$tbl_WDA1DH} AS t2 INNER JOIN {$tbl_WDADH} AS t3 ON (t3.FORM_ID = t2.FORM_ID) AND (t2.ACK_ID = t3.ACK_ID)) ON t1.ACK_ID = t3.ACK_ID WHERE t1.SPONS_DFE_EIN={$ein} GROUP BY t1.SPONS_DFE_EIN, t1.PLAN_YEAR, t3.SCH_A_PLAN_NUM, t3.INS_CARRIER_NAME_NORMALIZED, t1.PLAN_NAME, t3.INS_CONTRACT_NUM, t3.SCH_A_PLAN_YEAR_BEGIN_DATE, t3.SCH_A_PLAN_YEAR_END_DATE HAVING (((t1.SPONS_DFE_EIN)={$ein}) AND ((t1.PLAN_YEAR)={$year}))", ARRAY_A);
    
    if ($policy_detail) {
        foreach ($policy_detail as $key => $pd_single) {
            ?>
                <h3>Policy Detail – <?php echo $pd_single['INS_CARRIER_NAME_NORMALIZED']; ?>, Plan <?php echo $pd_single['SCH_A_PLAN_NUM']; ?>, Policy # <?php echo $pd_single['INS_CONTRACT_NUM']; ?>, Year <?php echo $year; ?></h3>
                <div class="policy-detail">
                    <span class="policy-detail-span">Carrier : <?php echo $pd_single['INS_CARRIER_NAME_NORMALIZED']; ?></span>
                    <span class="policy-detail-span">Plan Name :<?php echo $pd_single['PLAN_NAME']; ?></span>
                    <span class="policy-detail-span">Plan : <?php echo $pd_single['SCH_A_PLAN_NUM']; ?></span>
                    <span class="policy-detail-span">Policy : <?php echo $pd_single['INS_CONTRACT_NUM']; ?></span>
                    <span class="policy-detail-span">Plan Start Date:  <?php echo validateDate($pd_single['SCH_A_PLAN_YEAR_BEGIN_DATE']) ? $pd_single['SCH_A_PLAN_YEAR_BEGIN_DATE'] : 'N/A'; ?></span>
                    <span class="policy-detail-span">Plan End Date:  <?php echo validateDate($pd_single['SCH_A_PLAN_YEAR_END_DATE']) ? $pd_single['SCH_A_PLAN_YEAR_END_DATE'] : 'N/A'; ?></span>
                    <h4>Carrier premium ($K):</h4>
                    <div class="table-wrap">
                        <table>
                            <tr>
                                <th>Health</th>
                                <th>Dental</th>
                                <th>Life</th>
                                <th>STD</th>
                                <th>LTD</th>
                                <th>Vision</th>
                                <th>Other</th>
                                <th>Total</th>
                            </tr>
                            <tr>
                                <td><?php echo number_format($pd_single['SumOfHEALTH_PREM'], 1); ?></td>
                                <td><?php echo number_format($pd_single['SumOfDENTAL_PREM'], 1); ?></td>
                                <td><?php echo number_format($pd_single['SumOfLIFE_PREM'], 1); ?></td>
                                <td><?php echo number_format($pd_single['SumOfSTD_PREM'], 1); ?></td>
                                <td><?php echo number_format($pd_single['SumOfLTD_PREM'], 1); ?></td>
                                <td><?php echo number_format($pd_single['SumOfVISION_PREM'], 1); ?></td>
                                <td><?php echo number_format($pd_single['SumOfOTHER_PREM'], 1); ?></td>
                                <td><?php echo number_format($pd_single['SumOfWLFR_TOT_CHARGES_PAID_AMT'], 1); ?></td>
                            </tr>
                        </table>
                    </div>
                    <!-- Policy Revenue Section -->
					<?php
                    $ack_id = $pd_single['ACK_ID'];
                    $form_id = $pd_single['FORM_ID'];
					
                    /*$broker_revenue_detail_custom = $wpdb->get_results("SELECT t3.ACK_ID, t3.FORM_ID, t1.PLAN_YEAR, t3.SCH_A_PLAN_NUM, t3.INS_CARRIER_NAME_NORMALIZED, t3.INS_CONTRACT_NUM, t2.INS_BROKER_NAME, t2.INS_BROKER_NAME_NORMALIZED, t2.INS_BROKER_US_CITY, t2.INS_BROKER_US_STATE, t2.INS_BROKER_COMM_PD_AMT, t2.INS_BROKER_FEES_PD_AMT, t2.BROKER_REVENUE, sum(t2.INS_BROKER_COMM_PD_AMT) as SUM_INS_BROKER_COMM_PD_AMT, sum(t2.INS_BROKER_FEES_PD_AMT) as SUM_INS_BROKER_FEES_PD_AMT
                        FROM ({$tbl_WD5500DH} AS t1 INNER JOIN ({$tbl_WDA1DH} AS t2 INNER JOIN {$tbl_WDADH} AS t3 ON (t2.FORM_ID = t3.FORM_ID) AND (t2.ACK_ID = t3.ACK_ID)) ON t1.ACK_ID = t3.ACK_ID) INNER JOIN {$tbl_EIN_COMPANY_NAMES} AS t4 ON t1.SPONS_DFE_EIN = t4.SPONS_DFE_EIN
                        WHERE (((t1.PLAN_YEAR)={$year}) AND ((t4.SPONS_DFE_EIN)={$ein}) AND ((t1.SPONS_DFE_EIN)={$ein})) AND ( (t3.ACK_ID) = '$ack_id' ) AND ( (t3.FORM_ID) = {$form_id} )
                        GROUP BY t1.PLAN_YEAR, t3.SCH_A_PLAN_NUM, t3.INS_CARRIER_NAME_NORMALIZED, t3.INS_CONTRACT_NUM, t2.INS_BROKER_NAME, t2.INS_BROKER_NAME_NORMALIZED, t2.INS_BROKER_US_CITY, t2.INS_BROKER_US_STATE, t2.INS_BROKER_COMM_PD_AMT, t2.INS_BROKER_FEES_PD_AMT, t2.BROKER_REVENUE, t4.SPONS_DFE_EIN, t1.SPONS_DFE_EIN ORDER BY BROKER_REVENUE DESC", ARRAY_A); */
                         $broker_revenue_detail_custom = $wpdb->get_results("SELECT t3.ACK_ID, t3.FORM_ID, t1.PLAN_YEAR, t3.SCH_A_PLAN_NUM, t3.INS_CARRIER_NAME_NORMALIZED, t3.INS_CONTRACT_NUM, t2.INS_BROKER_NAME, t2.INS_BROKER_NAME_NORMALIZED, t2.INS_BROKER_US_CITY, t2.INS_BROKER_US_STATE, sum(t2.INS_BROKER_COMM_PD_AMT) as INS_BROKER_COMM_PD_AMT, sum(t2.INS_BROKER_FEES_PD_AMT) as INS_BROKER_FEES_PD_AMT, sum(t2.BROKER_REVENUE) as BROKER_REVENUE, sum(t2.INS_BROKER_COMM_PD_AMT) as SUM_INS_BROKER_COMM_PD_AMT, sum(t2.INS_BROKER_FEES_PD_AMT) as SUM_INS_BROKER_FEES_PD_AMT FROM 
(WD5500DH AS t1 INNER JOIN (WDA1DH AS t2 INNER JOIN WDADH AS t3 ON (t2.FORM_ID = t3.FORM_ID) AND (t2.ACK_ID = t3.ACK_ID)) ON t1.ACK_ID = t3.ACK_ID) INNER JOIN EIN_COMPANY_NAMES AS t4 ON t1.SPONS_DFE_EIN = t4.SPONS_DFE_EIN 
WHERE (((t1.PLAN_YEAR)={$year}) AND ((t4.SPONS_DFE_EIN)={$ein}) AND ((t1.SPONS_DFE_EIN)={$ein})) AND ( (t3.ACK_ID) = '$ack_id' ) AND ( (t3.FORM_ID) = 1 ) 
GROUP BY t1.PLAN_YEAR, 
t3.SCH_A_PLAN_NUM, 
t3.INS_CARRIER_NAME_NORMALIZED, 
t3.INS_CONTRACT_NUM, 
t2.INS_BROKER_NAME, 
t2.INS_BROKER_NAME_NORMALIZED, 
t2.INS_BROKER_US_CITY, 
t2.INS_BROKER_US_STATE, 
t4.SPONS_DFE_EIN, t1.SPONS_DFE_EIN 
ORDER BY BROKER_REVENUE DESC", ARRAY_A); 
                        
						
                        ?>
						
                        <h4>Broker revenue ($K):</h4>
                        <div class="broker_revenue_custom">
                            <div class="table-wrap">
                                <table>
                                    <tr>
                                        <th>Name (Normalized)</th>
                                        <th>Name (as filed)</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Commissions</th>
                                        <th>Fees</th>
                                        <th>Total</th>
                                        <th>% Total</th>
                                    </tr>
                                    <?php
                                    $broker_revenue_detail = $broker_revenue_detail_custom;
                                    if ($broker_revenue_detail) {
                                        $ct_broker_comm_pd = $ct_broker_fees_pd = $total = 0;
                                        foreach ($broker_revenue_detail as $key => $brd_single) {
                                            $total = $total + $brd_single['BROKER_REVENUE'];
                                        }
                                        foreach ($broker_revenue_detail as $key => $brd_single) {
                                            $ct_broker_comm_pd = $ct_broker_comm_pd + $brd_single['SUM_INS_BROKER_COMM_PD_AMT'];
                                            $ct_broker_fees_pd = $ct_broker_fees_pd + $brd_single['SUM_INS_BROKER_FEES_PD_AMT'];
                                            /* $total = $total + $brd_single['BROKER_REVENUE']; */
                                            ?>
                                            <tr>
                                                <td><?php echo $brd_single['INS_BROKER_NAME_NORMALIZED']; ?></td>
                                                <td><?php echo $brd_single['INS_BROKER_NAME']; ?></td>
                                                <td><?php echo $brd_single['INS_BROKER_US_CITY']; ?></td>
                                                <td><?php echo $brd_single['INS_BROKER_US_STATE']; ?></td>
                                                <td><?php echo number_format($brd_single['INS_BROKER_COMM_PD_AMT'], 1); ?></td>
                                                <td><?php echo number_format($brd_single['INS_BROKER_FEES_PD_AMT'], 1); ?></td>
                                                <td><?php echo number_format($brd_single['BROKER_REVENUE'], 1); ?></td>
                                                <td><?php echo number_format($brd_single['BROKER_REVENUE'] / $total * 100, 1); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        <tr>
                                            <td>Total</td>
                                            <td><?php echo "-"; ?></td>
                                            <td><?php echo "-"; ?></td>
                                            <td><?php echo "-"; ?></td>
                                            <td><?php echo number_format($ct_broker_comm_pd, 1); ?></td>
                                            <td><?php echo number_format($ct_broker_fees_pd, 1); ?></td>
                                            <td><?php echo number_format($total, 1); ?></td>
                                            <td><?php echo "100"; ?></td>                                
                                        </tr>
                                        <tr>
                                            <td>%</td>
                                            <td><?php echo "-"; ?></td>
                                            <td><?php echo "-"; ?></td>
                                            <td><?php echo "-"; ?></td>
                                            <td><?php echo number_format($ct_broker_comm_pd / $total * 100, 1); ?></td>
                                            <td><?php echo number_format($ct_broker_fees_pd / $total * 100, 1); ?></td>
                                            <td><?php echo '100'; ?></td>
                                            <td><?php echo "100"; ?></td>                                
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                            </div>    
                        </div> 
                                                <!-- End Policy Revenue Section -->
                </div>                            
            <?php
        }
    }
    ?>
        <!--Carrier History Section-->
        <h3>Carrier History</h3>
        <div class="carrier-history">                
    <?php
    $min_max_year = $wpdb->get_row("SELECT MIN(PLAN_YEAR) AS mini, MAX(PLAN_YEAR) AS maxi FROM {$tbl_WD5500DH}", ARRAY_A);
    ?>
            <div class="table-wrap">
                <table>
                    <tr>
                        <th>Premium ($K)</th>
    <?php for ($i = 2009; $i < $min_max_year['maxi']; $i++) { ?>
                            <th><?php echo $i; ?></th>
                            <?php
                        }
                        ?>
                    </tr>                
                        <?php
                        $carrier_history = $wpdb->get_results("SELECT t3.INS_CARRIER_NAME_NORMALIZED, t1.PLAN_YEAR, Sum(t3.WLFR_TOT_CHARGES_PAID_AMT) AS SumOfWLFR_TOT_CHARGES_PAID_AMT FROM {$tbl_WD5500DH} AS t1 INNER JOIN ({$tbl_WDA1DH} AS t2 INNER JOIN {$tbl_WDADH} AS t3 ON (t3.FORM_ID = t2.FORM_ID) AND (t2.ACK_ID = t3.ACK_ID)) ON t1.ACK_ID = t3.ACK_ID WHERE t1.SPONS_DFE_EIN={$ein} GROUP BY t1.SPONS_DFE_EIN, t3.INS_CARRIER_NAME_NORMALIZED, t1.PLAN_YEAR HAVING (((t1.SPONS_DFE_EIN)={$ein}))", ARRAY_A);
                        $new_carrier_history = array();
                        if ($carrier_history) {
                            foreach ($carrier_history as $key => $single_ch) {
                                if (!array_key_exists($single_ch['INS_CARRIER_NAME_NORMALIZED'], $new_carrier_history)) {
                                    $new_carrier_history[$single_ch['INS_CARRIER_NAME_NORMALIZED']] = array();
                                }
                                for ($i = 2009; $i <= $min_max_year['maxi']; $i++) {
                                    if ($single_ch['PLAN_YEAR'] == $i) {
                                        $new_carrier_history[$single_ch['INS_CARRIER_NAME_NORMALIZED']][$i] = $single_ch['SumOfWLFR_TOT_CHARGES_PAID_AMT'];
                                    }
                                }
                            }
                        }
                        if ($new_carrier_history) {
                            krsort($new_carrier_history);
                            foreach ($new_carrier_history as $key => $single_nc_history) {
                                ?>
                            <tr>
                                <td><?php echo $key; ?></td>
            <?php for ($i = 2009; $i < $min_max_year['maxi']; $i++) { ?>
                                    <td>
                                    <?php echo isset($single_nc_history[$i]) ? number_format($single_nc_history[$i], 1) : '0.0'; ?>
                                    </td>
                                        <?php
                                    }
                                    ?>
                            </tr>
                                <?php
                            }
                        }
                        ?>
                </table> 
            </div>
            <h2>Share of premium:</h2>
            <div id="container-carrier-history" style="min-width: 310px; height: auto; margin: 0 auto"></div>
            <script type="text/javascript">
                Highcharts.chart('container-carrier-history', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Share of Premium'
                    },
                    xAxis: {
                        categories: [<?php for ($i = 2009; $i < $min_max_year['maxi']; $i++) {
                    echo "'{$i}'" . ",";
                } ?>]
                    },
                    yAxis: {
                        min: 0
                    },
                    tooltip: {
                        pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
                        shared: true
                    },
                    plotOptions: {
                        column: {
                            stacking: 'percent'
                        }
                    },
                    series: [<?php
                foreach ($new_carrier_history as $key => $single_nc_history) {
                    $js_s = '';
                    for ($i = 2009; $i < $min_max_year['maxi']; $i++) {
                        $main_data = isset($single_nc_history[$i]) ? str_replace(',', '', number_format($single_nc_history[$i], 1)) : '0.0';
                        $js_s .= "$main_data" . ", ";
                    }
                    echo "{ name: '" . $key . "', data: [" . $js_s . "] }, ";
                }
                ?>]
                });
            </script>
        </div>
        <!--Broker History Section;-->
        <h3>Broker History</h3>
        <div class="broker-history"> 
            <div class="table-wrap">
                <table>
                    <tr>
                        <th>Revenue ($K)</th>
                        <?php for ($i = 2009; $i < $min_max_year['maxi']; $i++) { ?>
                            <th><?php echo $i; ?></th>
                            <?php
                        }
                        ?>
                    </tr>                      
                    <?php
                    $broker_history = $wpdb->get_results("SELECT t2.INS_BROKER_NAME_NORMALIZED, t1.PLAN_YEAR,
                                    Sum(t2.BROKER_REVENUE) AS SumOfINS_BROKER_REV_TOT
                                    FROM {$tbl_WD5500DH} AS t1 INNER JOIN ({$tbl_WDA1DH} AS t2 INNER JOIN {$tbl_WDADH} AS t3 ON (t3.FORM_ID =
                                    t2.FORM_ID) AND (t2.ACK_ID = t3.ACK_ID)) ON t1.ACK_ID =
                                    t3.ACK_ID
                                    WHERE t1.SPONS_DFE_EIN={$ein}
                                    GROUP BY t1.SPONS_DFE_EIN, t2.INS_BROKER_NAME_NORMALIZED,
                                    t1.PLAN_YEAR
                                    HAVING (((t1.SPONS_DFE_EIN)={$ein}))", ARRAY_A);

                    $new_broker_history = array();
                    if ($broker_history) {
                        foreach ($broker_history as $key => $single_bh) {
                            if (!array_key_exists($single_bh['INS_BROKER_NAME_NORMALIZED'], $new_broker_history)) {
                                $new_broker_history[$single_bh['INS_BROKER_NAME_NORMALIZED']] = array();
                            }
                            for ($i = 2009; $i <= $min_max_year['maxi']; $i++) {
                                if ($single_bh['PLAN_YEAR'] == $i) {
                                    $new_broker_history[$single_bh['INS_BROKER_NAME_NORMALIZED']][$i] = $single_bh['SumOfINS_BROKER_REV_TOT'];
                                }
                            }
                        }
                    }
                    if ($new_broker_history) {
                        krsort($new_broker_history);
                        foreach ($new_broker_history as $key => $single_nb_history) {
                            ?>
                            <tr>
                                <td><?php echo $key; ?></td>
                                    <?php for ($i = 2009; $i < $min_max_year['maxi']; $i++) { ?>
                                    <td>
                                    <?php echo isset($single_nb_history[$i]) ? number_format($single_nb_history[$i], 1) : '0.0'; ?>
                                    </td>
                                    <?php
                                }
                                ?>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </table>
            </div>
            <h2>Share of commissions and fees:</h2>
            <div id="container-broker-history" style="min-width: 310px; height: auto; margin: 0 auto"></div>
            <script type="text/javascript">
                Highcharts.chart('container-broker-history', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Share of Commissions and Fees:'
                    },
                    xAxis: {
                        categories: [<?php for ($i = 2009; $i < $min_max_year['maxi']; $i++) {
                        echo "'{$i}'" . ",";
                    } ?>]
                    },
                    yAxis: {
                        min: 0
                    },
                    tooltip: {
                        pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
                        shared: true
                    },
                    plotOptions: {
                        column: {
                            stacking: 'percent'
                        }
                    },
                    series: [<?php
                    foreach ($new_broker_history as $key => $single_nb_history) {
                        $js_s = '';
                        for ($i = 2009; $i < $min_max_year['maxi']; $i++) {
                            $main_data = isset($single_nb_history[$i]) ? str_replace(',', '', number_format($single_nb_history[$i], 1)) : '0.0';
                            $js_s .= "$main_data" . ", ";
                        }
                        echo "{ name: '" . $key . "', data: [" . $js_s . "] }, ";
                    }
                    ?>]
                });
            </script>
            
        </div>
        <!--Participant History Section-->
        <h3>Participant History</h3>
    <?php
    $participant_history = $wpdb->get_results("SELECT t1.PLAN_YEAR, Max(t1.TOT_ACTIVE_PARTCP_CNT) AS MaxOfTOT_ACTIVE_PARTCP_CNT, Max(t1.RTD_SEP_PARTCP_RCVG_CNT) AS MaxOfRTD_SEP_PARTCP_RCVG_CNT
                        FROM {$tbl_WD5500DH} AS t1  INNER JOIN ({$tbl_WDA1DH} AS t2 INNER JOIN {$tbl_WDADH} AS t3 ON (t3.FORM_ID = t2.FORM_ID) AND (t2.ACK_ID = t3.ACK_ID)) ON t1.ACK_ID = t3.ACK_ID WHERE t1.SPONS_DFE_EIN = {$ein} GROUP BY t1.PLAN_YEAR;", ARRAY_A);
    ?>            
        <div class="participant-history"> 
            <h2>Participant Overview</h2>
            <div id="container-participant" style="min-width: 310px; height: auto; margin: 0 auto"></div>
            <script type="text/javascript">
                Highcharts.chart('container-participant', {
                    chart: {
                        type: 'area'
                    },
                    title: {
                        text: ''
                    },
                    subtitle: {
                        text: ''
                    },
                    xAxis: {
                        categories: [<?php for ($i = 2009; $i < $min_max_year['maxi']; $i++) {
        echo "'{$i}'" . ",";
    } ?>],
                        tickmarkPlacement: 'on',
                        title: {
                            enabled: false
                        }
                    },
                    yAxis: {
                        title: {
                            text: 'Participants'
                        },
                        labels: {
                            formatter: function () {
                                //return this.value / 1000;
                            }
                        }
                    },
                    tooltip: {
                        split: true,
                        valueSuffix: ' Participants'
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
                    series: [{
                            name: 'Other',
                            color: '#727276',
                            data: [<?php
    for ($i = 2009; $i < $min_max_year['maxi']; $i++) {
        $key = searchForId($i, $participant_history);
        $oh = isset($participant_history[$key]['MaxOfRTD_SEP_PARTCP_RCVG_CNT']) ? str_replace(',', '', $participant_history[$key]['MaxOfRTD_SEP_PARTCP_RCVG_CNT']) : '0.0';
        echo $oh . ', ';
    }
    ?>]
                        }, {
                            name: 'Active',
                            color: '#9DC8F1',
                            data: [<?php
    for ($i = 2009; $i < $min_max_year['maxi']; $i++) {
        $key = searchForId($i, $participant_history);
        $ph = isset($participant_history[$key]['MaxOfTOT_ACTIVE_PARTCP_CNT']) ? str_replace(',', '', $participant_history[$key]['MaxOfTOT_ACTIVE_PARTCP_CNT']) : '0.0';
        echo $ph . ', ';
    }
    ?>]
                        }, ]
                });
            </script>
            
            <div class="table-wrap">
                <table>
                    <tr>
                        <th></th>
                            <?php for ($i = 2009; $i < $min_max_year['maxi']; $i++) { ?>
                            <th><?php echo $i; ?></th>
                                <?php
                            }
                            ?>
                    </tr>
                    <tr>
                        <td>Active Participants</td>
                        <?php
                        for ($i = 2009; $i < $min_max_year['maxi']; $i++) {
                            $key = searchForId($i, $participant_history);
                            ?>
                            <td>
                            <?php
                            $ap_d = $participant_history[$key]['MaxOfTOT_ACTIVE_PARTCP_CNT'];
                            echo $ap_d ? $ap_d : 0;
                            ?>
                            </td>
                                <?php
                            }
                            ?>
                    </tr>
                    <tr>
                        <td>Other Participants</td>
                        <?php
                        for ($i = 2009; $i < $min_max_year['maxi']; $i++) {
                            $key = searchForId($i, $participant_history);
                            ?>
                            <td>
                            <?php
                            $op_d = $participant_history[$key]['MaxOfRTD_SEP_PARTCP_RCVG_CNT'];
                            echo $op_d ? $op_d : 0;
                            ?>
                            </td>
                                <?php
                            }
                            ?>
                    </tr>
                    <tr>
                        <td>Total</td>
                        <?php
                        for ($i = 2009; $i < $min_max_year['maxi']; $i++) {
                            $key = searchForId($i, $participant_history);
                            ?>
                            <td>
        <?php
        $participate_no = $participant_history[$key]['MaxOfTOT_ACTIVE_PARTCP_CNT'];
        $other_no = $participant_history[$key]['MaxOfRTD_SEP_PARTCP_RCVG_CNT'];
        echo $participate_no + $other_no;
        ?>
                            </td>
        <?php
    }
    ?>
                    </tr>
                </table>
            </div>

        </div>
    </div>
    <?php
    die;