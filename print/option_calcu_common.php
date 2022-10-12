<?
/*
* @date : 20180530
* @author : chunter
* @brief : 공통 가격 계산 부분을 별도 파일로 처리. (할인, 세금) 
* @desc : 모든 계산식에서 필요함.
*/
?>
<? 
	$opt_price = 0;
	foreach ($response as $key => $value) {
	    if ($key == "user_data") continue;
        
		if (!$value) $value = 0;
		$opt_price += $value;
	}	

	
	if ($_POST['print_goods_type'] == "DG05")
	{		
		//책자인 경우 중간 sum 이 필요하다.
		$response['paper_total_price'] = $outsidePaperPrice + $insidePaperPriceTotal + $inpagePaperPriceTotal;	
		$response['print_total_price'] = $outsidePrintPrice + $insidePrintPriceTotal + $inpagePrintPriceTotal;
		$response['print_etc_total_price'] = $outsidePrintEtcPrice + $insidePrintEtcPriceTotal + $inpagePrintEtcPriceTotal;
	
		$outsidePriceTotal = $outsidePaperPrice	+ $outsidePrintPrice + $outsideWingPrice + $outsideGlossPrice + $outsideSCPrice + $outsideSCVPrice + $outsidePrintEtcPrice;
		$insidePriceTotal = $insidePaperPriceTotal	+ $insidePrintPriceTotal + $insidePrintEtcPriceTotal + $insideGlossPrice;
		$inpagePriceTotal = $inpagePaperPriceTotal	+ $inpagePrintPriceTotal + $inpagePrintEtcPriceTotal;		
		$response['outside_total_price'] = $outsidePriceTotal;
		$response['inside_total_price'] = $insidePriceTotal;
		$response['inpage_total_price'] = $inpagePriceTotal;
	}

//debug($response);
//debug("opt_price:".$opt_price);
	//회원 그룹별  할인 ,적립 가격  확인.
	if ($sess[grpno]) {
	    $m_goods = new M_goods();
        list($grpnm, $grpdc, $grpsc) = $m_goods->getGroupDisSaveInfo($sess[grpno]);

        if($grpdc > 0)
            $response[dc_price] = round($opt_price * (100 - $grpdc) / 100, -1);
 	} else {
        $response[dc_price] = 0;
	}
//debug($response);

    //현수막,실사출력 할인.
    if ($r_ipro_paper_pr_dc) {
        //debug($r_ipro_paper_pr_dc);
        $prdc = array_shift($r_ipro_paper_pr_dc);
        //debug($prdc[val]);
        
        if($prdc[val] > 0)
            $response[dc_price] += round($opt_price * (100 - $prdc[val]) / 100, -1);
        
        //debug($response[dc_price]);
    }


	$response[opt_price] = $opt_price;
	
	//pdf 직접 주문인 경우 5% 할인 지정. 		5% 는 하드코딩처리.
	if ($_POST['order_direct']) {
		$response[order_direct] = floor($response[opt_price] * 0.05);
		$response[dc_price] += $response[order_direct]; 
	}
	
	$response[sale_price] = $response[opt_price] - $response[dc_price];
    
    if ($print_goods_item[$_POST[goodsno]][vat_use] != "N") { 
        $response[vat_price] = floor($response[sale_price] * 0.1);              //부가세
        $response[sale_price] += $response[vat_price];
    }
	
	$response[sale_price] -= ($response[sale_price] % 100);	//10단위 절사.
	
	ob_start();	
	print_r($response);
	$ret = ob_get_contents();
	ob_end_clean();
		
	$response[sale_price_detail] = $ret;
	//echo json_encode($response);
?>