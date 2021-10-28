<html>
 <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
<head> 
<?php


if(isset($_GET["restS"])){
    echo '<pre>';print_r($track_result);die('test');
}
$widget_translation = "";
if (isset($track_result['widget_translation']) && is_array($track_result['widget_translation'])) {
    $widget_translation = $track_result['widget_translation'];
}

//echo "<pre>"; print_r($track_result);
if (!isset($track_result['error'])) {
    $table_width = (isset($full_width_result)) ? '100%' : '50%';

    $bookedDate = '';
    $inTransitDate = '';
    $outOfDeliveryDate = '';
    $deliveredDate = '';
    $rtoDate = '';
    $outOfDeliveryCase = '';
    $origin = '';
    $destination = '';

    if (isset($track_result['scan'])) {
        $x = 0;
        $y = 0;
         
        $track_result['status_time'] = ((isset($track_result['scan']['0']['time'])) && (!empty($track_result['scan']['0']['time']))) ? date("d M, Y H:i", strtotime($track_result['scan']['0']['time'])) :"";
        $track_result['status_time'] = !empty($track_result['status_time'])?str_replace(',', ' ', $track_result['status_time']):"";

        if ($track_result['shipment_type'] == '2') {
            foreach ($track_result['scan'] as $scans) {

              

                if (isset($scans['details']) && (strstr(strtoupper($scans['details']), 'OUT FOR PICK UP'))) {
                    $OOPDate = date('d M', strtotime($scans['time']));

                }

                // if(isset($scans['details'])  && (strstr(strtoupper($scans['details']) ,'OUT FOR PICK UP')) ){
                //     $OOPDate = date('d M',strtotime($scans['time']));

                // }

            }
        } else {
            foreach ($track_result['scan'] as $scans) {



                if ($track_result['location_settings'] == '1') {

                    //echo $track_result['HIDELOCATION'];exit;
                    $main = explode("#", $track_result['HIDELOCATION']);
                    // echo "<pre>";print_r($main);exit;

                    foreach ($main as $key => $value) {
                        $test = str_replace($value, "", $scans['details'], $count);

                        if ($count > 0) {
                            //echo "<pre>";print_r($scans);
                            $scans['details'] = $test;
                            break;
                            //$scans;exit;

                        }
                    }

                }

                /*if(isset($scans['details'])){
                $scans['details']  = strtoupper($scans['details']);
                }*/

                if (isset($scans['details']) && (strstr(strtoupper($scans['details']), 'PICKED UP'))) {
                    $bookedDate = !empty($scans['time'])? date('d M', strtotime($scans['time'])):"";

                } else if (isset($scans['details']) && (strstr(strtoupper($scans['details']), 'ITEM BOOKED'))) {
                    $bookedDate = !empty($scans['time'])? date('d M', strtotime($scans['time'])):"";

                } else if (isset($scans['details']) && (strstr(strtoupper($scans['details']), 'ITEM BAGGED'))) {
                    $bookedDate =!empty($scans['time'])? date('d M', strtotime($scans['time'])):"";

                }
                if ($bookedDate == '' && in_array($track_result['onj_current_status'], array('PKP', 'SCH'))) {


                    $bookedDate = !empty($track_result['status_time'])? date('d M', strtotime($track_result['status_time'])):"";



                }

                if (isset($scans['details']) && (strstr(strtoupper($scans['details']), 'IN TRANSIT'))) {
                    if (!empty($scans['time'])) {
                        $inTransitDate = !empty($scans['time'])?date('d M', strtotime($scans['time'])):"";
                    }

                } else {

                    $inTransitDate = $bookedDate;
                }

                /*if($bookedDate < date('d/m/Y',strtotime($track_result['status_time'])) ){

                }*/
                if (isset($scans['details']) && (strstr(strtoupper($scans['details']), 'OUT FOR DELIVERY'))) {
                    $outOfDeliveryCase = 'Yes';
                }

                if (isset($scans['details']) && (strstr(strtoupper($scans['details']), 'OUT FOR DELIVERY'))) {
                    $outOfDeliveryDate = !empty($scans['time'])?date('d M', strtotime($scans['time'])):"";
                }
                if ($outOfDeliveryDate == '') {
                    $outOfDeliveryDate = $bookedDate;

                }

                if (isset($scans['details']) && (strstr(strtoupper($scans['details']), 'DELIVERED'))) {
                    $deliveredDate = !empty($scans['time'])?date('d M', strtotime($scans['time'])):"";
                }
                if (isset($scans['details']) && (strstr($scans['details'], 'RTO Delivered'))) {
                    $rtoDate = !empty($scans['time'])?date('d M', strtotime($scans['time'])):"";
                } else if (isset($scans['details']) && (strstr($scans['details'], 'Return To Origin'))) {
                    $rtoDate = !empty($scans['time'])?date('d M', strtotime($scans['time'])):"";
                } else if (isset($scans['details']) && (strstr($scans['details'], 'Return To Shipper'))) {
                    $rtoDate = !empty($scans['time'])?date('d M', strtotime($scans['time'])):"";
                }

            }
        }

    }

    if (($track_result['onj_current_status'] == 'RTO' || $track_result['onj_current_status'] == 'RTD') && $outOfDeliveryCase != 'Yes') {
        $sw_progressBarLiWidth = '33.3%';
    } else {
        $sw_progressBarLiWidth = '25%';
    }

    ?>
<?php if(isset($track_result['widget_style']) && !empty($track_result['widget_style'])) {
echo "<style>".$track_result['widget_style']."</style>";
}
?>
<style>
@font-face{
	font-family:Ebrima;src:url(https://shipway.in/fonts/Ebrima_Regular.ttf);
}
body{/*font-family:ebrima !important; */}
.sw_float_me{ clear: left; }
.sw_dnone{display: none;}
.sw_scanContainer{/*margin:0 auto;*/width: 100% !important;}
.sw_scan{width: 50%;margin: 0 auto;position: relative;}
.shp_table{width:60%;border:thin solid #428BCA;padding:0px;border-collapse:collapse;margin:0 auto;}
.shp_table th{border:1px solid #428BCA;padding:10px;text-align:center;}
.shp_table td{padding:5px 15px;border:1px solid #428BCA;font-family:Ebrima;font-size:13px;text-align:left;}
.onj-tr{background-color:#428BCA;color:#fff;}
#track_result table{margin:0 auto;}
.wrapper-sw_progressBar{width:50%;margin:auto;}
.sw_progressBar{padding: 0}
.sw_progressBar li{list-style-type:none;float:left;width:<?php echo $sw_progressBarLiWidth; ?>;position:relative;text-align:center;}
.sw_progressBar li:before{content:" ";border-radius:50%;width:50px;height:52px;display:block;	margin:0 auto 10px;background-color:#d7e4e8}
.sw_progressBar li:after{content:"";position:absolute;width:100%;height:5px;background-color:#d7e4e8;top:21px;left:-50%;z-index:-1;}
.sw_progressBar li:first-child:after{content:none;}
.sw_progressBar li.active{color:black;}
.sw_progressBar li.active:before{background-color:#3f82ea;content:url('https://shipway.in/images/check_image8.png');}
.sw_progressBar li.active:after{background-color:#3f82ea;}
.sw_progressBar li.activeGreen{color:black;}
.sw_progressBar li.activeGreen:before{border-color:#4ac752;background-color:#4ac752;content:url('https://shipway.in/images/check_image8.png');}
.sw_progressBar li.activeGreen:after{background-color:#4ac752;}
.sw_progressBar li.active1{color:black; line-height: initial;}
.sw_progressBar li.active1:before{border-color:#f69737;background-color:#f69737;content:"! ";font-size:40px; color: #fff;}
.sw_progressBar li.active1:after{background-color:#f69737;}
.sw_progressBar li.active2{color:black;}
.sw_progressBar li.active2:before{background-color:#f69737;content:url('https://shipway.in/images/check_image8.png');}
.sw_progressBar li.active2:after{background-color:#f69737;}
.sw_scanTimeDiv{float:left;padding:20px 0 33px 0;border-right:3px solid #000000; width: 50%;}
.sw_scanTimeSpan{color:#3f82ea;font-size:20px;display:block;}
.sw_scanDetailDiv{float:left;padding:20px 0px 0px 30px; width: 50%;}
.sw_trackMover{border-radius:50%;padding:8px;background:#3f82ea;position:absolute;margin:28px auto;left: 48.7%;}
.sw_sw_track_c_n{clear:left;text-align:center;padding-top: 30px;}
.sw_track_c{font-size:24px;}
.sw_track_n{font-size:22px;}
.sw_current_status{color:#3f82ea; font-size: 30px; margin: 20px 0 10px 0;  text-align: center;}
.sw_expected_date_delivery{font-size: 20px;margin: 0 0 15px 0;text-align: center;}
.statusColor{color: black;}
.track_divider{font-size: 27px;}

@-moz-document url-prefix(){
	/* .sw_trackMover{margin:28px 10.5%;display:block;} */
	/*.sw_scanDetailDiv{width:  52%;}*/
	/* #trackresult .sw_trackMover{margin:28px 108px} */
	#trackresult .sw_scanTimeDiv{/*width: 24.5%;*/}
}
/*Shopify widget code*/
#trackresult .sw_scanContainer{width: initial; /*margin-left: 15%;*/}
#trackresult .wrapper-sw_progressBar{width: 100%;}
#trackresult .sw_scanTimeSpan{font-size: 15px;}
#trackresult .sw_scanTimeDiv{ /*width: 25%;*/}
#trackresult .sw_sw_track_c_n{margin-left:-64px}
.content-box{background: initial;}
/*Shopify widget code*/

/* Branded tracking page */
#btpage .wrapper-sw_progressBar{ width: 80%; }
#btpage .sw_scanContainer{/* margin-left: 24%;*/ }
#btpage .sw_scanTimeDiv{ /*width: 30%;*/}
/* Branded tracking page */

/*@media(max-width:767px){
.sw_outerContainer{width:100%;}
.sw_progressBar{padding:0}
.sw_scanContainer{margin-left:inherit; width: 100%;}
.wrapper-sw_progressBar{width:100%; margin-left:initial;margin-right:initial;}
.sw_trackMover, .track_divider{display:none;}
.sw_track_n{display:block;}
.sw_scanTimeDiv{padding:0;clear:left;border:none; width: 100%;}
.sw_scanDetailDiv{padding:0;clear:left;padding-bottom:20px;}
.sw_expected_date_delivery{font-size: initial;}
.sw_scanTimeSpan{display: inline-block;}

#btpage .wrapper-sw_progressBar { width: 100%;}
#btpage .sw_scanContainer{ margin-left: 5%; }
#btpage .sw_scanTimeDiv {width: 100%;}
#btpage .sw_scanDetailDiv{width: 100%;}
#btpage .sw_float_me{float:none; display: inline-block; border-left:  1px solid #000; padding-left: 15px;}
#btpage .sw_trackMoverFirst{ display:block; border-radius: 50%;padding: 8px;background: #3f82ea;position: absolute;margin: 32px -24px; }

/**FOR PROGRESS BAR IN MOBILE**/


/****/
}*/

@media (max-width: 550px) {
	.logo{ margin-left: auto; margin-right:auto;}
}


@media (max-width: 1270px) {
	.sw_scanContainer {
	    width: 100%;
	}

}
@media (min-width: 992px) and (max-width: 1070px) {
	.logo{ margin-left: auto; margin-right:auto;}

	.sw_scanContainer {
	    width: 100%;
	}
	.sw_dnone {
    	display: block;
	}

	.sw_scanTimeDiv {
	    padding: 30px 0px 42px 0;
	    width: 50%;
	    float: left;
	    border-right: 3px solid #000000;
	}

	.sw_trackMover, .track_divider {
	    display: block;
	}
	.sw_trackMover {
	    margin: 28px auto;
	    left: 48.3%;
	}


	.sw_scanDetailDiv {
	    float: left;
	    padding: 20px 0px 0px 30px;
	    width: 50%;
	    clear: none;
	}
	.sw_scanTimeSpan {
    	display: block;
	}
	.wrapper-sw_progressBar {
	    width: 80%;
	    margin: auto;
	}
	.sw_sw_track_c_n{padding: 30px 0 0 37% !important;}
	.sw_sw_track_c_n span{float:left;clear:none;font-size: 20px;}
}

@media (min-width: 811px) and (max-width: 991px) {
	.logo{ margin-left: auto; margin-right:auto;}

	.sw_scanContainer {
	   /* margin-left: 27%;*/
	    width: 100%;
	}
	.sw_dnone {
    	display: block;
	}

	.sw_scanTimeDiv {
	    padding: 30px 0px 30px 0;
	    width: 50%;
	    float: left;
	    border-right: 3px solid #000000;
	}

	.sw_trackMover, .track_divider {
	    display: block;
	}
	.sw_trackMover {
	    margin: 28px auto;
	    left: 48%;
	}


	.sw_scanDetailDiv {
	    float: left;
	    padding: 20px 0px 0px 30px;
	    width: 50%;
	    clear: none;
	}
	.sw_scanTimeSpan {
    	display: block;
	}

	.wrapper-sw_progressBar {
	    width: 80%;
	    margin: auto;
	}

	.sw_sw_track_c_n{padding: 30px 0 0 33% !important;}
	.sw_sw_track_c_n span{float:left;clear:none;font-size: 20px;}
}
@media (min-width: 768px) and (max-width: 810px) {
	.logo{ margin-left: auto; margin-right:auto;}

	.sw_scanContainer {
/*	    margin-left: 24%;
*/	    width: 100%;
	}
	.sw_dnone {
    	display: block;
	}

	.sw_scanTimeDiv {
	    padding: 30px 42px 35px 40px;
	    width: 50%;
	    float: left;
	    border-right: 3px solid #000000;
	}

	.sw_trackMover, .track_divider {
	    display: block;
	}
	.sw_trackMover {
	    margin: 28px auto;
	    left: 48%;
	}


	.sw_scanDetailDiv {
	    float: left;
	    padding: 20px 0px 0px 10px;
	    width: 50%;
	    clear: none;
	}
	.sw_scanTimeSpan {
    	display: block;
	}

	.wrapper-sw_progressBar {
	    width: 90%;
	    margin: auto;
	}

	.sw_sw_track_c_n{padding: 30px 0 0 33% !important;}
	.sw_sw_track_c_n span{float:left;clear:none;font-size: 20px;}
}


@media (min-width: 680px) and (max-width: 767px) {
	.logo{ margin-left: auto; margin-right:auto;}

	.sw_scanContainer {
	    /*margin-left: 27%;*/
	    width: 100%;
	}
	.sw_dnone {
    	display: block;
	}

	.sw_scanTimeDiv {
	    padding: 30px 42px 30px 0;
	    width: 50%;
	    float: left;
	    border-right: 3px solid #000000;
	}

	.sw_trackMover, .track_divider {
	    display: block;
	}
	.sw_trackMover {
	    margin: 28px auto;
	    left: 48%;
	}


	.sw_scanDetailDiv {
	    float: left;
	    padding: 20px 0px 0px 10px;
	    width: 50%;
	    clear: none;
	}
	.sw_scanTimeSpan {
    	display: block;
	}
	.wrapper-sw_progressBar {
	    width: 90%;
	    margin: auto;
	}
	.sw_sw_track_c_n{padding: 30px 0 0 33% !important;}
	.sw_sw_track_c_n span{float:left;clear:none;font-size: 20px;}
}

@media (min-width: 551px) and (max-width: 679px) {
	.logo{ margin-left: auto; margin-right:auto;}

	.sw_scanContainer {
/*	    margin-left: 21%;
*/	    width: 100%;
	}
	.sw_dnone {
    	display: block;
	}

	.sw_scanTimeDiv {
	    padding: 30px 42px 30px 0;
	    width: 50%;
	    float: left;
	    border-right: 3px solid #000000;
	}

	.sw_trackMover, .track_divider {
	    display: block;
	}
	.sw_trackMover {
	    margin: 28px auto;
	    left: 47%;
	}


	.sw_scanDetailDiv {
	    float: left;
	    padding: 10px 0px 0px 20px;
	    width: 50%;
	    clear: none;
	}
	.sw_scanTimeSpan {
    	display: block;
	}
	.wrapper-sw_progressBar {
	    width: 100%;
	    margin: auto;
	}
	.sw_sw_track_c_n{padding: 30px 0 0 28% !important;}
	.sw_sw_track_c_n span{float:left;clear:none;font-size: 20px;}
}

@media (min-width: 479px) and (max-width: 550px) {
	.logo{ margin-left: auto; margin-right:auto;}

	.sw_scanContainer {
/*	    margin-left: 17%;
*/		width: 100%;
	}
	.sw_dnone {
    	display: block;
	}

	.sw_scanTimeDiv {
	    padding: 32px 30px 30px 0;
	    width: 50%;
	    float: left;
	    border-right: 3px solid #000000;
	}

	.sw_trackMover, .track_divider {
	    display: block;
	}
	.sw_trackMover {
	    margin: 28px auto;
	    left: 47%;
	}


	.sw_scanDetailDiv {
	    float: left;
	    padding: 10px 0px 0px 10px;
	    width: 50%;
	    clear: none;
		font-size: 13px;
	}
	.sw_scanTimeSpan {
    	display: block;
	}
	.wrapper-sw_progressBar {
	    width: 100%;
	    margin: auto;
	}
	.sw_sw_track_c_n{padding: 30px 0 0 25% !important;}
	.sw_sw_track_c_n span{float:left;clear:none;font-size: 20px;}
}

@media (min-width: 320px) and (max-width: 478px) {
	.logo{ margin-left: auto; margin-right:auto;}

	.sw_scanContainer {
/*	    margin-left: 5%;
*/		width: 100%;
	}
	.sw_dnone {
    	display: block;
	}

	.sw_scanTimeDiv {
	    padding: 20px 30px 30px 0;
	    width: 40%;
	    float: left;
	    border-right: 3px solid #000000;
	}

	.sw_trackMover, .track_divider {
	    display: block;
	}
	.sw_trackMover {
	    margin: 28px auto;
	    left: 35%;
	}


	.sw_scanDetailDiv {
	    float: left;
	    padding: 10px 0px 0px 10px;
	    width: 60%;
	    clear: none;
	    font-size: 12px;
	}
	.sw_scanTimeSpan {
    	display: block;
	}
	.wrapper-sw_progressBar {
	    width: 100%;
	    margin: auto;
	}
	.sw_sw_track_c_n{padding: 30px 0 0 15% !important;}
	.sw_sw_track_c_n span{float:left;clear:none;font-size: 20px;}
}



</style>
<!-- </head>-->

<?php //if(isset($track_result['current_status']) &&( $track_result['current_status'] !='notfound' && $track_result['current_status'] !='NFI')){
    $widget_translation = "";
    if (isset($track_result['widget_translation']) && is_array($track_result['widget_translation'])) {
        $widget_translation = $track_result['widget_translation'];
    }

    $currentStatus = '';
    $expectedDateOfDelivery = '';
    $validStatusArray = array('NFI', 'PKP', 'SCH', 'INT', 'OOD', 'DEL', 'RTO', 'RTD', 'UND', 'CAN', 'NWI', '22', 'CNA', 'DRE', '25', 'ODA', 'ONH', 'CRTA', 'DNB', 'FDR', '23', 'ITEM DESPATCHED', 'SMD', 'DEX', 'UND', '24', "RCAN", "RCLO", "RDEL", "RINT", "ROOP", "RPKP", "RPSH", "RSCH", "RSMD");

    if (isset($track_result['current_status']) && in_array($track_result['onj_current_status'], $validStatusArray)) {

        if (isset($track_result['extra_fields']['expected_delivery_date']) && !empty($track_result['extra_fields']['expected_delivery_date']) && strtotime($track_result['extra_fields']['expected_delivery_date']) >= strtotime(date("Y-m-d"))) {

            $expectedDateOfDelivery = (is_array($widget_translation) && isset($widget_translation['expected_date_of_delivery'])) ? $widget_translation['expected_date_of_delivery'] . "(" . date('F d, Y', strtotime($track_result['extra_fields']['expected_delivery_date'])) . ")" : 'Expected Date of Delivery' . "(" . date('F d, Y', strtotime($track_result['extra_fields']['expected_delivery_date'])) . ")";
            if(isset($track_result['current_status_code'])){
            	  if ($track_result['current_status_code'] == 'DEL' || $track_result['current_status_code'] == 'RTD') {
                $expectedDateOfDelivery = "";
            }
            }
          

            //   if($track_result['awb_no']=='9400111899562875276549'){
            //     //  echo  $expected_delivery_date = date('Y-m-d',strtotime($track_result['extra_fields']['expected_delivery_date']));
            // // $now = strtotime($user_insert_date);
            //   }

            $expected_delivery_date = !empty($track_result['extra_fields']['expected_delivery_date'])?strtotime($track_result['extra_fields']['expected_delivery_date']):"";
            $now = time();

            if ($now > $expected_delivery_date) {
                $expectedDateOfDelivery = "";
            }

        }
//$expectedDateOfDelivery = "Expected Date of Delivery (July 19, 2019)";

        if (!empty($track_result['destination_from'])) {

            $origin = "Origin:" . $track_result['destination_from'];
        }
        if (!empty($track_result['destination_to'])) {
            $destination = "Destination: " . $track_result['destination_to'];
        }

        $undeliveredArr = array('UND', '22', '23', '25', 'SMD', 'DEX', 'PNR', 'CAN', 'CNA', 'CRTA', 'DNB', 'DRE', 'FDR', 'ODA', '24', '25', 'ODA', 'DNB', 'FDR', '23');

        $outOfDeliveryArr = array('OOD', 'DEL', 'UND', 'CAN', '22', 'CNA', 'DRE', '25', 'ODA', 'CRTA', 'DNB', 'FDR', '23', 'ITEM DESPATCHED', 'SMD', 'DEX', '24');

        if (in_array($track_result['onj_current_status'], array('PKP', 'SCH'))) {
            $currentStatus = (is_array($widget_translation) && isset($widget_translation['shipment_booked'])) ? $widget_translation['shipment_booked'] : 'Shipment Booked';
        }

        if (in_array($track_result['onj_current_status'], $undeliveredArr)) {

            $currentStatus = (is_array($widget_translation) && isset($widget_translation['shipment'])) ? $widget_translation['shipment'] . ' ' . $widget_translation['Undelivered'] : 'Shipment Undelivered';
        }

        if (in_array($track_result['onj_current_status'], array('INT'))) {

            $currentStatus = (is_array($widget_translation) && isset($widget_translation['shipment'])) ? $widget_translation['shipment'] . ' ' . $widget_translation['intransit'] : 'Shipment In Transit';
        }

        if (in_array($track_result['onj_current_status'], array('DEL', 'ITEM DESPATCHED'))) {
            $currentStatus = (is_array($widget_translation) && isset($widget_translation['shipment'])) ? $widget_translation['shipment'] . ' ' . $widget_translation['delivered'] : 'Shipment Delivered';
        }

        if ($track_result['onj_current_status'] != 'RTO' && $track_result['onj_current_status'] != 'RTD') {

            if (in_array($track_result['onj_current_status'], $outOfDeliveryArr)) {
                $currentStatus = (is_array($widget_translation) && isset($widget_translation['shipment'])) ? $widget_translation['shipment'] . ' ' . $widget_translation['out_for_delivery'] : 'Shipment Out For Delivery';
            }

        }

        if (in_array($track_result['onj_current_status'], array('DEL', 'ITEM DESPATCHED'))) {
            $currentStatus = (is_array($widget_translation) && isset($widget_translation['shipment'])) ? $widget_translation['shipment'] . ' ' . $widget_translation['delivered'] : 'Shipment Delivered';
        }

        if (in_array($track_result['onj_current_status'], $undeliveredArr)) {
            $currentStatus = (is_array($widget_translation) && isset($widget_translation['shipment'])) ? $widget_translation['shipment'] . ' ' . $widget_translation['Undelivered'] : 'Shipment Undelivered';
        }
        if (($track_result['onj_current_status'] == 'RTO' || $track_result['onj_current_status'] == 'RTD')) {

            $currentStatus = (is_array($widget_translation) && isset($widget_translation['shipment'])) ? $widget_translation['shipment'] . ' ' . $widget_translation['return_to_origin'] : 'Shipment Return to Origin ';
        }

        if (in_array($track_result['onj_current_status'], array("RCAN", "RCLO", "RDEL", "RINT", "ROOP", "RPKP", "RPSH", "RSCH", "RSMD"))) {
            //$currentStatus = isset($track_result['onj_current_status'])?$track_result['onj_current_status']:$track_result['current_status'];
            $currentStatus = isset($track_result['current_status']) ? $track_result['current_status'] : $track_result['onj_current_status'];
        }

        ?>

<div class="sw_outerContainer">
	<p class="sw_current_status current_status "><?php echo $currentStatus; ?></p>
	<p class="sw_expected_date_delivery expected_delivery_date"  ><?php echo $expectedDateOfDelivery; ?></p>
<?php

        if (isset($track_result['shipment_type']) && $track_result['shipment_type'] == '2') {
// echo $track_result['onj_current_status'];exit;
            ?>



 <!-- reverse status bar start -->
  <div class="wrapper-sw_progressBar">
      <ul class="sw_progressBar">



		<?php $reversesehdulearray = array('RSCH', 'RPKP', 'RINT', 'RDEL', 'RSMD', 'ROOP', 'RCAN');
            $reversepickuparray = array('RPKP', 'RINT', 'RDEL');
            $reverseintransitarray = array('RINT', 'RDEL', 'ROOD');
            $reverseoutarray = array('RDEL');
            $reversedelarray = array('RDEL');
            $canceledstatus = array("RSCH", "RCAN");
			$closedstatus = array("RSCH", "RCLO");
			$reverseUndelivered = array('RUND');

            if ($track_result['onj_current_status'] == 'RCAN') {
                ?> <li class="<?php if (in_array($track_result['onj_current_status'], $canceledstatus)) {?> active <?php }?> "><span class="statusColor"><?php echo 'Pickup Scheduled'; ?></span><br/>

       		<!--<span style="font-size: 20px; margin-left: 55px;"><?php //echo $origin ?></span>  <br/>-->
   			</span>
   		</li>
   		<li class="<?php if (in_array($track_result['onj_current_status'], $canceledstatus)) {?> active1 <?php }?> "><span class="statusColor"><?php echo 'Return Request Cancelled'; ?></span><br/>

       		<!--<span style="font-size: 20px; margin-left: 55px;"><?php //echo $origin ?></span>  <br/>-->
   			</span>
   		</li>
			<?php

            }if ($track_result['onj_current_status'] == 'RCLO') {
                ?> <li class="<?php if (in_array($track_result['onj_current_status'], $closedstatus)) {?> active <?php }?> "><span class="statusColor"><?php echo 'Pickup Scheduled'; ?></span><br/>

       		<!--<span style="font-size: 20px; margin-left: 55px;"><?php //echo $origin ?></span>  <br/>-->
   			</span>
   		</li>
   		<li class="<?php if (in_array($track_result['onj_current_status'], $closedstatus)) {?> active1 <?php }?> "><span class="statusColor"><?php echo 'Return Request Closed'; ?></span><br/>

       		<!--<span style="font-size: 20px; margin-left: 55px;"><?php //echo $origin ?></span>  <br/>-->
   			</span>
   		</li>
			<?php

            }

            if ($track_result['onj_current_status'] != "RCAN" && $track_result['onj_current_status'] != "RCLO") {

                ?>

		 <li class="<?php if (in_array($track_result['onj_current_status'], $reversesehdulearray)) {?> active <?php }?> "><span class="statusColor"><?php echo 'Pickup Scheduled'; ?></span><br/>

       		<!--<span style="font-size: 20px; margin-left: 55px;"><?php //echo $origin ?></span>  <br/>-->
   			</span>
   		</li>

   		 <li class="<?php if (in_array($track_result['onj_current_status'], $reversepickuparray)) {?> active <?php }?> "><span class="statusColor"><?php echo 'Shipment Picked Up'; ?></span><br/>

       		<!--<span style="font-size: 20px; margin-left: 55px;"><?php //echo $origin ?></span>  <br/>-->
   			</span>
   		</li>

   		 <li class="<?php if (in_array($track_result['onj_current_status'], $reverseintransitarray)) {?> active <?php }?> "><span class="statusColor"><?php echo 'Return In Transit'; ?></span><br/>

       		<!--<span style="font-size: 20px; margin-left: 55px;"><?php //echo $origin ?></span>  <br/>-->
   			</span>
		   </li>
		   <?php 
						if(isset($_GET['rest'])){
				$track_result['onj_current_status'] = 'RUND';
			}

			?>
		
		<?php 
			// if(isset($_GET['rest'])){
			// 	//echo $track_result['onj_current_status'];
			// 	$track_result['onj_current_status'] = 'RUND';
			// }
			//$track_result['onj_current_status'] = 'RUND';
			
			if (!in_array($track_result['onj_current_status'], $reversedelarray) && in_array($track_result['onj_current_status'], $reverseUndelivered)) {?>
				<li class="<?php if (in_array($track_result['onj_current_status'], $reverseUndelivered)) {?> active1 <?php }?> ">
				<span style="color: #3f82ea;"><?php echo 'Return UnDelivered'; ?></span><br/>
			
				</span>
				</li>
		<?php }else{ ?>
			<li class="<?php if (in_array($track_result['onj_current_status'], $reversedelarray) && !in_array($track_result['onj_current_status'], $reverseUndelivered)) {?> activeGreen <?php }?> "><span class="statusColor"><?php echo 'Return Delivered'; ?></span><br/>

			<!--<span style="font-size: 20px; margin-left: 55px;"><?php //echo $origin ?></span>  <br/>-->
			</span>
			</li>
			
		<?php }?>
		
		   

	   <?php }?>
	   

      </ul>
    </div>


    <!-- reverse bar closed -->

<?php } else {

            ?>
	 <div class="wrapper-sw_progressBar">
      <ul class="sw_progressBar">

		<?php $shipmentBookedArr = array('NFI', 'PKP', 'SCH', 'INT', 'OOD', 'DEL', 'RTO', 'RTD', 'UND', 'CAN', 'NWI', '22', 'CNA', 'DRE', '25', 'ODA', 'ONH', 'CRTA', 'DNB', 'FDR', '23', 'ITEM DESPATCHED', 'SMD', 'DEX', '24');?>

      <li class="<?php if (in_array($track_result['onj_current_status'], $shipmentBookedArr)) {?> active <?php }?> "><span class="statusColor"><?php echo (is_array($widget_translation) && isset($widget_translation['shipment_booked'])) ? $widget_translation['shipment_booked'] : 'Shipment Booked'; ?></span><br/>
       		<span style="color: #3f82ea;"><?php echo $bookedDate ?>
       		<!--<span style="font-size: 20px; margin-left: 55px;"><?php //echo $origin ?></span>  <br/>-->
   			</span>
   		</li>
			<?php $intransitArr = array('INT', 'OOD', 'DEL', 'RTO', 'RTD', 'UND', 'NWI', 'CAN', '22', 'CNA', 'DRE', '25', 'ODA', 'ONH', 'CRTA', 'DNB', 'FDR', '23', 'ITEM DESPATCHED', 'SMD', 'DEX', '24');?>
      	 	<li class="<?php if (in_array($track_result['onj_current_status'], $intransitArr)) {?> active <?php }?>"><span class="statusColor"><?php echo (is_array($widget_translation) && isset($widget_translation['intransit'])) ? $widget_translation['intransit'] : 'In Transit'; ?></span><br/>
      		<span style="color: #3f82ea;">
      		<?php if (in_array($track_result['onj_current_status'], $intransitArr)) {
                if (isset($track_result['onj_current_status']) && $track_result['onj_current_status'] == 'INT') {echo (!empty($track_result['status_time']) && $track_result['status_time'] != 'N/A') ? date('d M', strtotime($track_result['status_time'])) : '';} else {echo $inTransitDate;}}

            ?>
      	    </span>
      	</li>

			<?php if (($track_result['onj_current_status'] == 'RTO' || $track_result['onj_current_status'] == 'RTD')) {
                if ($outOfDeliveryCase == 'Yes') {?>
			<li class="active"><span class="statusColor"><?php echo (is_array($widget_translation) && isset($widget_translation['out_for_delivery'])) ? $widget_translation['out_for_delivery'] : 'Out For Delivery'; ?></span><br/>
			<span style="color: #428BCA;"><?php	echo $outOfDeliveryDate; ?></span>
		</li>

	    	<?php }}?>


   			<?php if ($track_result['onj_current_status'] != 'RTO' && $track_result['onj_current_status'] != 'RTD') {?>

           <li class="<?php if (in_array($track_result['onj_current_status'], $outOfDeliveryArr)) {?> active <?php }?>"><span class="statusColor"><?php echo (is_array($widget_translation) && isset($widget_translation['out_for_delivery'])) ? $widget_translation['out_for_delivery'] : 'Out For Delivery'; ?></span><br/>
        	<span style="color: #3f82ea;">
        	<?php // echo $outOfDeliveryDate; ?>

       		<?php if (in_array($track_result['onj_current_status'], $outOfDeliveryArr)) {
                if (isset($track_result['onj_current_status']) && $track_result['onj_current_status'] == 'OOD') {echo (!empty($track_result['status_time']) && $track_result['status_time'] != 'N/A') ? date('d M', strtotime($track_result['status_time'])) : '';} else {echo $outOfDeliveryDate;}?>
        	</span>
        </li>
       <?php }}?>

				<?php if (in_array($track_result['onj_current_status'], array('DEL', 'ITEM DESPATCHED'))) {?>
		<li class="activeGreen"><?php echo (is_array($widget_translation) && isset($widget_translation['delivered'])) ? $widget_translation['delivered'] : 'Delivered'; ?><br/>
			<span style="color: #3f82ea;">
			<!--<span style="padding-left: 72px; font-size: 20px; "><?php echo $destination ?> <br/></span> -->
			<?php if (isset($track_result['onj_current_status']) && in_array($track_result['onj_current_status'], array('DEL', 'ITEM DESPATCHED'))) {echo (!empty($track_result['status_time']) && $track_result['status_time'] != 'N/A') ? date('d M', strtotime($track_result['status_time'])) : '';} else {echo $deliveredDate;}?>

			</span>
		</li>
			<?php }?>


                <?php if (in_array($track_result['onj_current_status'], $undeliveredArr)) {?>
			<li class="active1"><?php echo (is_array($widget_translation) && isset($widget_translation['Undelivered'])) ? $widget_translation['Undelivered'] : 'Undelivered'; ?><br/>


				<span style="color: #3f82ea;">
				<!--<span style="padding-left: 72px; font-size: 20px; ">Destination: Hisar <br/></span> -->
				<!--<span style="padding-left: 72px; font-size: 20px; "><?php echo $destination ?> <br/> -->
				</span>
				<span style="color: #3f82ea;">
				<?php if (isset($track_result['onj_current_status']) && in_array($track_result['onj_current_status'], array('UND', 'CNA', 'DRE', '25', 'ONH', 'ODA', 'CRTA', 'DNB', 'FDR', '23', 'SMD', 'CAN', 'DEX'))) {echo (!empty($track_result['status_time']) && $track_result['status_time'] != 'N/A') ? date('d M', strtotime($track_result['status_time'])) : '';}?>

				</span>
			</li>
				<?php }?>

				<?php if (@$track_result['onj_current_status'] == 'RTO' || @$track_result['onj_current_status'] == 'RTD') {
					$class = "active";
					   $RTD_STATUS = "Return to origin";
                      $rtostatuss = "Return to Origin (Intransit)";
                   if($track_result['onj_current_status'] == 'RTD'){
                   $rtostatuss = "Return to Origin (Delivered)";
                   $class = "activeGreen";
                   }
                   if($track_result['onj_current_status'] == 'RTO'){
                   $rtostatuss = "Return to Origin (Intransit)";
                    $class = "active";
                   }

					?>

						<li class="<?php echo $class?>"> <span class="statusColor"><?php echo (is_array($widget_translation) && isset($widget_translation['return_to_origin'])) ? $widget_translation['return_to_origin'] . ' (' . $widget_translation['intransit'] . ')' :$rtostatuss; ?></span><br/>


     			<!--<span style="color: 3f82ea;"> <span style="margin-left: 55px; font-size: 20px; "><?php echo $destination ?> </span><br/>-->
   				<span style="color: #3f82ea;">
				<?php if (isset($track_result['onj_current_status']) && $track_result['onj_current_status'] == 'RTO' || $track_result['onj_current_status'] == 'RTD') { echo (!empty($track_result['status_time']) && $track_result['status_time'] != 'N/A') ? date('d M', strtotime($track_result['status_time'])) : '';;} else {echo $rtoDate;}?>

     			 </span>
     		</li>
		<?php }

            if (((@$track_result['onj_current_status'] != 'UND') && (@$track_result['onj_current_status'] != 'RTO') && (@$track_result['onj_current_status'] != 'DEL') && (@$track_result['onj_current_status'] != 'RTD') && (@$track_result['onj_current_status'] != '25') && (@$track_result['onj_current_status'] != 'CNA') && (@$track_result['onj_current_status'] != 'ODA') && (@$track_result['onj_current_status'] != 'ONH') && (@$track_result['onj_current_status'] != 'DNB') && (@$track_result['onj_current_status'] != 'CRTA') && (@$track_result['onj_current_status'] != 'ITEM DESPATCHED') && (@$track_result['onj_current_status'] != 'FDR') && (@$track_result['onj_current_status'] != '23') && (@$track_result['onj_current_status'] != '24') && (@$track_result['onj_current_status'] != 'DEX') && (@$track_result['onj_current_status'] != 'SMD') && (@$track_result['onj_current_status'] != '22')
                && (@$track_result['onj_current_status'] != 'DRE')
                && (@$track_result['onj_current_status'] != 'CAN')) || ((@$track_result['onj_current_status'] == 'INT') && (@$track_result['onj_current_status'] == 'OOD') && (@$track_result['onj_current_status'] == 'DEX'))) {?>
			<li class=""><span class="statusColor"><?php echo (is_array($widget_translation) && isset($widget_translation['delivered'])) ? $widget_translation['delivered'] : 'Delivered'; ?></span><br/><!--<span style="padding-left: 88px; font-size: 20px;  color: 3f82ea;" ><?php echo $destination; ?> <br/></span> -->
			</li>
				<?php }?>


      </ul>
    </div>
	<?php

        }?>



<br/><br/>

<!--<div  align="center" id="viewDetails" style="color: #428BCA; text-align: center;font-size: 18px;border-color: #2FCD97;  margin-top: 49px !important;padding-top: 64px;"><u><a href="#" style="color: 3f82ea;" onclick="showHideTable();">View Details</a>
	<p style="display: none;" id="showHide">show</p>
</u></div> <br/><br/> -->
<?php if (isset($track_result['courier_name'])) {?>

<div class="sw_sw_track_c_n">
	<span  class="sw_track_c carrier"><?php echo $track_result['courier_name']; ?>
	</span>
	<span class="track_divider carrier "> - </span>
	<span class="sw_track_n awbno"> <?php echo $track_result['awb_no']; ?></span>
</div>
<?php }?>
 <br/><br/>


<?php }?>
 <!-- --->
<!-- New Design starts here -->

	<div class="sw_scanContainer">
		<div class="sw_scan">
	<?php
$scan_time = '';
    if (isset($track_result['scan'])) {
        foreach ($track_result['scan'] as $scans) {

            if ($track_result['location_settings'] == '1') {

                //echo $track_result['HIDELOCATION'];exit;
                $main = explode("#", $track_result['HIDELOCATION']);
                // echo "<pre>";print_r($main);exit;

                foreach ($main as $key => $value) {
                    $test = str_replace($value, "", $scans['details'], $count);

                    if ($count > 0) {
                        //echo "<pre>";print_r($scans);
                        $scans['details'] = $test;
                        break;
                        //$scans;exit;

                    }
                }

            }

            $scan_time = !empty($scans['time'])?date('M d, Y', strtotime($scans['time'])):"";
          if(!empty($scans['details'])) {
            ?>
      
    	<div class="sw_float_me">
    		<span class="sw_trackMoverFirst sw_dnone" ></span>
	        <div class="sw_scanTimeDiv">
	            <span class="sw_scanTimeSpan"><?php echo $scan_time ?></span>
	            <span class="time"><?php $status_time = date("H:i", strtotime($scans['time']));
            echo    ((trim($status_time) != '00:00') && (!empty($scan_time)) ) ? date("D, H:i", strtotime($scans['time'])) :"";?>
	            </span>
	        </div>
        	<span class="sw_trackMover"></span>
	        <div class="sw_scanDetailDiv">
	            <span style="font-weight: 600;">
	                <?php echo $scans['details']; ?>
	            </span> <br/>
	            <span class="location"><?php echo $scans['location']; ?></span>
	        </div>
    	</div>

	<?php }}}?>
	    </div>

	</div>

</div>
<!-- New Design ends here -->
<br/><br/><br/>	<br/><br/><br/>	<br/>
<?php }?>







