<?
/*
* @date : 20180725
* @author : chunter
* @brief : 변경된 계산식 개발 완료.
* @desc : 자동계산_가격계산_수식.doc 문서 참고.
 
* @date : 20180711
* @author : chunter
* @brief : 표지의 지류 사이즈 계산식 수정.
* @desc : 표지의 지류 사이즈를 구할때 제본방식(세로, 가로)에 따라 가로로 2배할지 세로로 2배할지 정한다. 
 * 날개의 두깨도 더한다 (날개 사이즈 *2). 
 * 내지의 총 지류 두께로 책등 사이즈를 구하고 책등 사이즈로 더한다.  
    
* @date : 20180704
* @author : chunter
* @brief : 인쇄비 계산을 페이지당 계산으로 변경함에 따라 내지 별색 인쇄시 양단면 코드를 무조건 "O" 로 넘김
* @desc : 내지 인쇄시 양단면 의미가 없다. 무조건 페이지수량으로 계산.

* @date : 20180508
* @author : chunter
* @brief : 책자 인쇄 계산식.
* @desc : 계산식 개발후 검증이 필요하고. 검증 완료후 각 계산 파일별 중복 내용 정리가 필요함
*/
?>
<? 
	$calcu_mode = "Y";
	include_once 'lib_print.php';
	$mode = $_POST[mode];
	
	$insidePageTotal = 0;					//후가공 계산을 위해 내지 총 종이 수량.	
	$insidePageWidthTotal = 0;		//책등두께를 구하기 위해 내지 페이지의 지류 두께
	
	//*******************************
	//내지 계산
	//*******************************
	foreach ($_POST['inside_page'] as $key => $value) 
	{		
		$inside_print1 = "inside_print1_" . $key;
		$inside_print2 = "inside_print2_" . $key;
		$inside_print3 = "inside_print3_" . $key;
		$inside_print3_check = "inside_print3_check_" . $key;
		$inside_print_key = getPrintSideCode($_POST[$inside_print1], $_POST[$inside_print2]);
		
		if ($_POST['inside_paper'][$key])
		{
							
			$insidePaperPrice[] = calcuBookPaperDigital($_POST['opt_size'], $_POST['inside_page'][$key], $_POST['cnt'], $_POST['inside_paper'][$key], $_POST['inside_paper_gram'][$key],$inside_print_key);
			
			//책등 두께를 위한 지류 두께 구하기			20180712	chunter
			if ($r_ipro_paper[$_POST['inside_paper'][$key]]['paper'][$_POST['inside_paper_gram'][$key]]['width'])
			{
				if (substr($inside_print_key, 0, 1) == "D")
					$inside_page_cnt = $_POST['inside_page'][$key] / 2;			//장당 두께이기 때문에 양면은 /2 처리.
				else
					$inside_page_cnt = $_POST['inside_page'][$key];
				$insidePageWidthTotal += ($r_ipro_paper[$_POST['inside_paper'][$key]]['paper'][$_POST['inside_paper_gram'][$key]]['width'] * $inside_page_cnt);
			}			
		}
		
		
		$insidePageSub = $_POST['inside_page'][$key] * $_POST['cnt'];	
		if ($inside_print_key)
		{	
			//print_r($_POST['opt_size']."<BR>");
			//print_r($_POST['inside_page'][$key]."<BR>");
			//print_r($_POST['cnt']."<BR>");
			//print_r($inside_print_key."<BR>");			
			//echo "$insidePageSub\r\n";
			$printPrice = calcuPrintDigitalBookInside($_POST['opt_size'], $insidePageSub, $inside_print_key);
			
			//전체 금액에서 할인을 한다.
			$insidePrintPrice[] = calcuPrintDCDigitalBookInside($printPrice, $_POST['opt_size'], $inside_print_key);		
		}
	
		 
		//별색 계산. 별색은 여러개 선택이 가능함.
		$optPrintPriceEtc = 0;
		if ($_POST[$inside_print3_check] == "Y") 
		{			
 			if (is_array($_POST[$inside_print3]))
			{
				foreach ($_POST[$inside_print3] as $pkey => $pvalue) {
					$optPrintPriceEtcTemp = calcuPrintETCDigitalBookInside($_POST['opt_size'], $insidePageSub, $pvalue);
					
					//전체 금액에서 할인을 한다.
					$optPrintPriceEtc += calcuPrintDCDigitalBookInside($optPrintPriceEtcTemp, $_POST['opt_size'], $pvalue);	
				}			
			} else {
				if ($_POST[$inside_print3])
				{
					$optPrintPriceEtcTemp = calcuPrintETCDigitalBookInside($_POST['opt_size'], $insidePageSub, $_POST[$inside_print3]);
					//전체 금액에서 할인을 한다.
					$optPrintPriceEtc = calcuPrintDCDigitalBookInside($optPrintPriceEtcTemp, $_POST['opt_size'], $_POST[$inside_print3]);
				}
			}
			//print_r($_POST[opt_print3]);
			//print_r($printPriceEtc);
		}
		$insidePrintPriceEtc[] = $optPrintPriceEtc;

		
		//양면일경우 주문페이지수 /2, 단면을 그대로 처리. 
		if ($_POST[$inside_print2] == "D")
			$insidePageTotal += $_POST['inside_page'][$key] / 2;
		else
			$insidePageTotal += $_POST['inside_page'][$key];
	}
	
	$insidePaperPriceTotal = 0;
	$insidePrintPriceTotal = 0;
	$insidePrintEtcPriceTotal = 0;
	if (is_array($insidePaperPrice))
	{
		foreach ($insidePaperPrice as $key => $value) {
			$insidePaperPriceTotal += $value;
			$insidePrintPriceTotal += $insidePrintPrice[$key];
			$insidePrintEtcPriceTotal += $insidePrintPriceEtc[$key];
		}
	}
	
	$response['inside_paper_price'] = $insidePaperPriceTotal;	
	$response['inside_print_price'] = $insidePrintPriceTotal;
	$response['inside_print_etc_price'] = $insidePrintEtcPriceTotal;
	
	//*******************************
	//간지 계산
	//*******************************	
	foreach ($_POST['inpage_page'] as $key => $value) 
	{
		$inpage_print1 = "inpage_print1_" . $key;
		$inpage_print2 = "inpage_print2_" . $key;
		$inpage_print3 = "inpage_print3_" . $key;
		$inpage_print3_check = "inpage_print3_check_" . $key;		
		$inpage_print_key = getPrintSideCode($_POST[$inpage_print1], $_POST[$inpage_print2]);
		
		if ($_POST['inpage_paper'][$key])
		{
			$inpagePaperPrice[] = calcuBookPaperDigital($_POST['opt_size'], $_POST['inpage_page'][$key], $_POST['cnt'], $_POST['inpage_paper'][$key], $_POST['inpage_paper_gram'][$key],$inpage_print_key);
			
			//책등 두께를 위한 지류 두께 구하기			20180712	chunter
			if ($r_ipro_paper[$_POST['inpage_paper'][$key]]['paper'][$_POST['inpage_paper_gram'][$key]]['width'])
			{
				if (substr($inpage_print_key, 0, 1) == "D")
					$inpage_page_cnt = $_POST['inpage_page'][$key] / 2;			//장당 두께이기 때문에 양면은 /2 처리.
				else
					$inpage_page_cnt = $_POST['inpage_page'][$key];
				$insidePageWidthTotal += ($r_ipro_paper[$_POST['inpage_paper'][$key]]['paper'][$_POST['inpage_paper_gram'][$key]]['width'] * $inpage_page_cnt);
			}
		}	
				
		
		$inpagePageSub = $_POST['inpage_page'][$key] * $_POST['cnt'];
		if ($inpage_print_key)
		{			
			$printPrice = calcuPrintDigitalBookInside($_POST['opt_size'], $inpagePageSub, $inpage_print_key);
			//전체 금액에서 할인을 한다.
			$insidePrintPrice[] = calcuPrintDCDigitalBookInside($printPrice, $_POST['opt_size'], $inpage_print_key);
		}	
		
		//별색 계산. 별색은 여러개 선택이 가능함.
		$optPrintPriceEtc = 0;
		if ($_POST[$inpage_print3_check] == "Y") 
		{
			if (is_array($_POST[$inpage_print3]))
			{
				foreach ($_POST[$inpage_print3] as $pkey => $pvalue) {
					$optPrintPriceEtcTemp = calcuPrintETCDigitalBookInside($_POST['opt_size'], $inpagePageSub, $pvalue);		//인쇄비 페이지당 단가.
					//전체 금액에서 할인을 한다.
					$optPrintPriceEtc += calcuPrintDCDigitalBookInside($optPrintPriceEtcTemp, $_POST['opt_size'], $pvalue);		
				}			
			} else {
				if ($_POST[$inpage_print3])
				{
					$optPrintPriceEtcTemp = calcuPrintETCDigitalBookInside($_POST['opt_size'], $inpagePageSub, $_POST[$inpage_print3]);		//인쇄비 페이지당 단가.
					//전체 금액에서 할인을 한다.
					$optPrintPriceEtc = calcuPrintDCDigitalBookInside($optPrintPriceEtcTemp, $_POST['opt_size'], $_POST[$inpage_print3]);
				}	
				
			}
			//print_r($_POST[opt_print3]);
			//print_r($printPriceEtc);
		}
		$inpagePrintPriceEtc[] = $optPrintPriceEtc;
					
		
		//양면일경우 주문페이지수 /2, 단면을 그대로 처리. 
		if ($_POST[$inpage_print2] == "D")
			$insidePageTotal += $_POST['inpage_page'][$key] / 2;
		else
			$insidePageTotal += $_POST['inpage_page'][$key];
	}
	
	$inpagePaperPriceTotal = 0;
	$inpagePrintPriceTotal = 0;
	$inpagePrintEtcPriceTotal = 0;
	if (is_array($inpagePaperPrice))
	{
		foreach ($inpagePaperPrice as $key => $value) {
			$inpagePaperPriceTotal += $value;
			$inpagePrintPriceTotal += $inpagePrintPrice[$key];
			$inpagePrintEtcPriceTotal += $inpagePrintPriceEtc[$key];
		}
	}
		
	$response['inpage_paper_price'] = $inpagePaperPriceTotal;
	$response['inpage_print_price'] = $inpagePrintPriceTotal;
	$response['inpage_print_etc_price'] = $inpagePrintEtcPriceTotal;
	
	//*******************************
	
	
	
	//*******************************
	//표지 계산
	//*******************************
	//제본 안함 경우 제외.
	if ($_POST['opt_bind'] && $_POST['opt_bind'] != "BD7")
	{		
		$opt_bind_type = $_POST['opt_bind_type'];
	}
	
	//날개있음 처리.
	$outside_wing_width = 0;
	if ($_POST['outside_wing'] == "WI2")
	{
		$outside_wing_width = $_POST['outside_wing_width'] * 2;	
	}
	
	//인쇄 key 만들기.
	$outside_print_key = getPrintSideCode($_POST['outside_print1'], $_POST['outside_print2']);
	//print_r($outside_print_key);		
		
	//지류값 계산시 표지 사이즈 * 2 해당하는 규격을 찾는다. 제본 type, 날개 크기로 영향을 받는다.
	$bookCoverSizeKey = getBookCoverSize($_POST['opt_size'], $opt_bind_type, $outside_wing_width, $insidePageWidthTotal);
	
	//echo "$outside_print_key\r\n";	
	//표지는 건수만큼 지류가 필요한다. print_key 를 강제로 단면으로 넘긴다.			20180712		chunter
	//-->양/단면에 따라 페이지수를 다르게 하므로 $outside_print_key 를 넘겨야 한다			20180725		chunter
	//표지는 낱장과 같은 공식으로 변경			20180801		chunter
	if ($_POST['outside_paper'])
		//$outsidePaperPrice = calcuBookPaperDigital($bookCoverSizeKey, $outside_page, $_POST['cnt'], $_POST['outside_paper'], $_POST['outside_paper_gram'], $outside_print_key);
		$outsidePaperPrice = calcuPaperDigital($bookCoverSizeKey, $_POST['cnt'], $_POST['outside_paper'], $_POST['outside_paper_gram']);
		
	
	if ($outside_print_key)
	{
		//표지 페이지수는 단면1, 양면2 로 셋팅된다.
		$outside_page = 1;
		if ($_POST['outside_print2'] == "D") $outside_page = 2;
		//표지는 부수별 가격 구간을 구해야 한다.		양/단면에 따른 page수를 반영한다.				20180725
		$printPrice = calcuPrintDigital($bookCoverSizeKey, $outside_page * $_POST['cnt'], $outside_print_key);		
		//전체가격에서 할인.		
		$outsidePrintPrice = calcuPrintDCDigital($printPrice, $bookCoverSizeKey, $outside_print_key);
	}
	
	
	//별색 계산. 별색은 여러개 선택이 가능함.
	$outsidePrintEtcPrice = 0;
	if ($_POST[outside_print3_check] == "Y") {	
		if (is_array($_POST[outside_print3]))
		{
			foreach ($_POST[outside_print3] as $pkey => $pvalue) {
				$printPrice = calcuPrintETCDigital($bookCoverSizeKey, $_POST['cnt'], $pvalue, $outside_print_key);
				$outsidePrintEtcPrice += calcuPrintDCDigital($printPrice, $bookCoverSizeKey, $pvalue);	
			}			
		} else {
			if ($_POST[outside_print3])
			{
				$printPrice = calcuPrintETCDigital($bookCoverSizeKey, $_POST['cnt'], $_POST[outside_print3], $outside_print_key);
				$outsidePrintEtcPrice = calcuPrintDCDigital($printPrice, $bookCoverSizeKey, $_POST[outside_print3]);
			}	
		}
		//print_r($_POST[opt_print3]);
		//print_r($printPriceEtc);
	}		
		
	//$outsideWingPrice = calcuWingDigital($bookCoverSizeKey, $_POST['cnt'], $_POST['outside_wing']);
	//print_r($outsideWingPrice);
		
	if ($_POST['opt_outside_gloss_check'] == "Y")
		$outsideGlossPrice = calcuGlossDigital($bookCoverSizeKey, $_POST['cnt'], $_POST['outside_gloss']);
	
	$response['outside_paper_price'] = $outsidePaperPrice;
	$response['outside_print_price'] = $outsidePrintPrice;
	$response['outside_print_etc_price'] = $outsidePrintEtcPrice;
	//$response['outside_wing_price'] = $outsideWingPrice;
	$response['outside_gloss_price'] = $outsideGlossPrice;
	//$response['outside_sc_price'] = $outsideSCPrice;
	//$response['outside_scb_price'] = $outsideSCBPrice;
			
	
	//*******************************
	//후가공  계산
	//*******************************
	
	//내지 코팅 (내지 종이장수 * 부수)
	if ($_POST['opt_gloss_check'] == "Y")	
		$insideGlossPrice = calcuGlossDigital($_POST['opt_size'], $insidePageTotal * $_POST['cnt'], $_POST['opt_gloss']);
	
	//제본후가공 계산. 제본은 주문 규격으로 계산한다.
	if ($_POST['opt_bind'])
		$bindPrice = calcuBindDigital($_POST['opt_size'], $_POST['cnt'], $_POST['opt_bind']);
			
	if ($_POST['opt_sc_check'] == "Y")
		$SCPrice = calcuSCDigital($bookCoverSizeKey, $_POST['cnt'], $_POST['opt_sc']);
	
	if ($_POST['opt_scb_check'] == "Y")
		$SCBPrice = calcuSCBDigital($bookCoverSizeKey, $_POST['cnt'], $_POST['opt_scb']);
		
	//도무송 
	if ($_POST['opt_domoo_check'] == "Y")		
		$domooPrice = calcuDomooDigital($bookCoverSizeKey, $_POST['cnt'], $_POST['opt_domoo']);
		
	$response['bind_price'] = $bindPrice;
	$response['domoo_price'] = $domooPrice;
	$response['inside_gloss_price'] = $insideGlossPrice;
	
	$response['outside_sc_price'] = $SCPrice;
	$response['outside_scb_price'] = $SCBPrice;	
		
	include_once 'option_calcu_common.php';
	$response['real_cover_size'] = $bookCoverSizeKey;
	
	echo json_encode($response);
?>