<?
/*
* @date : 20180508
* @author : chunter
* @brief : 책자 옵셋 인쇄 계산식.
* @desc : 계산식 개발후 검증이 필요하고. 검증 완료후 각 계산 파일별 중복 내용 정리가 필요함
*/
?>
<? 
	$calcu_mode = "Y";
	include_once 'lib_print.php';

	$mode = $_POST[mode];
	
	//필수값 체크
	$bCalcuFlag = true;
	if (! $_POST['opt_size']) $bCalcuFlag = false;	
	//if (! $_POST['opt_page']) $bCalcuFlag = false;
	
	if (!$bCalcuFlag)
	{
		$response['sale_price'] = 0;
		echo json_encode($response);
		exit;
	}
	
	
	
	$insidePageWidthTotal = 0;		//책등두께를 구하기 위해 내지 페이지의 지류 두께
	$insideYuenTotal = 0;					//후가공 계산을 위해 내지 총 연 수량.
	$insidePageCntTotal = 0;			//제본 계산을 위한 내지 전체 장수.
	$insideDeaTotal = 0;					//중철제본 계산을 위한 내지 전체 대수.
	
	$inside_paper_size = getOpsetPaperSize($_POST['opt_size']);
	
	
	//*******************************
	//내지 계산
	//*******************************
	foreach ($_POST['inside_page'] as $key => $value) 
	{		
		$inside_print1 = "inside_print1_" . $key;
		$inside_print2 = "inside_print2_" . $key;
		$inside_print3 = "inside_print3_" . $key;
		$inside_print3_check = "inside_print3_check_" . $key;
		
		
		
		if ($_POST[$inside_print3_check] == "Y") 
			$inside_etc_color_cnt = count($_POST[$inside_print3]);
		
		$inside_print_key = getPrintSideCode($_POST[$inside_print1], $_POST[$inside_print2]);
		$opset_inside_dea = getOpsetPrintDEA($_POST['opt_size'], $_POST['inside_page'][$key], $inside_print_key);		//인쇄 대수 구하기
		$opset_inside_prefix_print_key = makePrefixOpsetPrintKey($_POST[$inside_print1], $_POST[$inside_print2], $inside_etc_color_cnt, 0);
		$opset_inside_yuen = getOpsetBookPrintYuen($_POST['opt_size'], $_POST['inside_page'][$key], $_POST['cnt'], $opset_inside_prefix_print_key, $opset_inside_dea);		//옵션  연 구하기.
		$insideYuenTotal += $opset_inside_yuen;
		$insideDeaTotal += $opset_inside_dea;
		
		//echo "{$_POST['inside_page'][$key]}\r\n";
		//echo "$inside_paper_size\r\n";		
		//echo "$inside_print_key\r\n";
		//echo "$opset_inside_dea\r\n";
		//echo "$opset_inside_yuen\r\n";
				
		$insideCTPPrice[] = calcuCTPOpset($inside_print_key, $opset_inside_dea, $inside_etc_color_cnt);
				
		if ($_POST['inside_paper'][$key])
		{
			//$insidePaperPrice[] = calcuBookPaperOpset($_POST['opt_size'], $_POST['inside_paper'][$key], $_POST['inside_paper_gram'][$key], $opset_inside_yuen);			
			$paperPrice = calcuPaperOpset($_POST['opt_size'], $_POST['inside_paper'][$key], $_POST['inside_paper_gram'][$key], $opset_inside_yuen);
			$insidePaperPrice[] = calcuPaperDCOpset($paperPrice, $_POST['inside_paper'][$key]);			
			
			//제본 계산을 위한 내지 총 장수를 구한다.
			if (substr($inside_print_key, 0, 1) == "D")
				$inside_page_cnt = $_POST['inside_page'][$key] / 2;			//장당 두께이기 때문에 양면은 /2 처리.
			else
				$inside_page_cnt = $_POST['inside_page'][$key];
			$insidePageCntTotal += $inside_page_cnt;
				
			//책등 두께를 위한 지류 두께 구하기			20180712	chunter
			if ($r_ipro_paper[$_POST['inside_paper'][$key]]['paper'][$_POST['inside_paper_gram'][$key]]['width'])
			{
				$insidePageWidthTotal += ($r_ipro_paper[$_POST['inside_paper'][$key]]['paper'][$_POST['inside_paper_gram'][$key]]['width'] * $inside_page_cnt);
			}
		}	
		
		if ($inside_print_key)
		{
			$insidePrintPrice[] = calcuPrintOpset($inside_paper_size, $opset_inside_yuen, $inside_print_key);
		}	
		 
		//별색 계산. 별색은 여러개 선택이 가능함.
		$optPrintPriceEtc = 0;
		if ($_POST[$inside_print3_check] == "Y") 
		{			
 			if (is_array($_POST[$inside_print3]))
			{
				foreach ($_POST[$inside_print3] as $pkey => $pvalue) {
					$optPrintPriceEtc += calcuPrintETCOpset($inside_paper_size, $opset_inside_yuen, $pvalue, $inside_print_key);
				}			
			} else {
				if ($_POST[$inside_print3])
					$optPrintPriceEtc = calcuPrintETCOpset($inside_paper_size, $opset_inside_yuen, $_POST[$inside_print3], $inside_print_key);
			}
			//print_r($_POST[opt_print3]);
			//print_r($printPriceEtc);
		}
		$insidePrintPriceEtc[] = $optPrintPriceEtc;
	}
	
	$insidePaperPriceTotal = 0;
	$insidePrintPriceTotal = 0;
	$insidePrintEtcPriceTotal = 0;
	$insideCTPPriceTotal = 0;
	if (is_array($insidePaperPrice))
	{
		foreach ($insidePaperPrice as $key => $value) {
			$insidePaperPriceTotal += $value;
			$insidePrintPriceTotal += $insidePrintPrice[$key];
			$insidePrintEtcPriceTotal += $insidePrintPriceEtc[$key];
			$insideCTPPriceTotal += $insideCTPPrice[$key];
		}
	}
	
	$response['inside_paper_price'] = $insidePaperPriceTotal;	
	$response['inside_print_price'] = $insidePrintPriceTotal;
	$response['inside_print_etc_price'] = $insidePrintEtcPriceTotal;
	$response['inside_ctp_price'] = $insideCTPPriceTotal;
	
	
	
	
	//*******************************
	//간지 계산
	//*******************************		
	foreach ($_POST['inpage_page'] as $key => $value) 
	{		
		$inpage_print1 = "inpage_print1_" . $key;
		$inpage_print2 = "inpage_print2_" . $key;
		$inpage_print3 = "inpage_print3_" . $key;
		$inpage_print3_check = "inpage_print3_check_" . $key;
		
		if ($_POST[$inpage_print3_check] == "Y") 
			$inpage_etc_color_cnt = count($_POST[$inpage_print3]);
		
		$inpage_print_key = getPrintSideCode($_POST[$inpage_print1], $_POST[$inpage_print2]);
		$opset_inpage_dea = getOpsetPrintDEA($_POST['opt_size'], $_POST['inpage_page'][$key], $inpage_print_key);		//인쇄 대수 구하기
		$opset_inpage_prefix_print_key = makePrefixOpsetPrintKey($_POST[$inpage_print1], $_POST[$inpage_print2], $inpage_etc_color_cnt, 0);		
		$opset_inpage_yuen = getOpsetBookPrintYuen($_POST['opt_size'], $_POST['inpage_page'][$key], $_POST['cnt'], $opset_inpage_prefix_print_key, $opset_inpage_dea);		//옵션  연 구하기.		
		$insideYuenTotal += $opset_inpage_yuen;
		$insideDeaTotal += $opset_inside_dea;
				
		$inpageCTPPrice[] = calcuCTPOpset($inpage_print_key, $opset_inpage_dea, $inpage_etc_color_cnt);
				
		if ($_POST['inpage_paper'][$key])
		{
			$paperPrice = calcuPaperOpset($_POST['opt_size'], $_POST['inpage_paper'][$key], $_POST['inpage_paper_gram'][$key], $opset_inpage_yuen);
			$inpagePaperPrice[] = calcuPaperDCOpset($paperPrice, $_POST['inpage_paper'][$key]);			
			
			
			//제본 계산을 위한 내지 총 장수를 구한다.
			if (substr($inpage_print_key, 0, 1) == "D")
				$inpage_page_cnt = $_POST['inpage_page'][$key] / 2;			//장당 두께이기 때문에 양면은 /2 처리.
			else
				$inpage_page_cnt = $_POST['inpage_page'][$key];
			$insidePageCntTotal += $inside_page_cnt;
			
			//책등 두께를 위한 지류 두께 구하기			20180712	chunter
			if ($r_ipro_paper[$_POST['inpage_paper'][$key]]['paper'][$_POST['inpage_paper_gram'][$key]]['width'])
			{
				$insidePageWidthTotal += ($r_ipro_paper[$_POST['inpage_paper'][$key]]['paper'][$_POST['inpage_paper_gram'][$key]]['width'] * $inpage_page_cnt);
			}
		}	
		
		if ($inpage_print_key)
		{
			$inpagePrintPrice[] = calcuPrintOpset($inside_paper_size, $opset_inpage_yuen, $inpage_print_key);
		}
	
		 
		//별색 계산. 별색은 여러개 선택이 가능함.
		$optPrintPriceEtc = 0;
		if ($_POST[$inpage_print3_check] == "Y") 
		{			
 			if (is_array($_POST[$inpage_print3]))
			{
				foreach ($_POST[$inpage_print3] as $pkey => $pvalue) {
					$optPrintPriceEtc += calcuPrintETCOpset($inside_paper_size, $opset_inpage_yuen, $pvalue, $inpage_print_key);
				}			
			} else {
				if ($_POST[$inpage_print3])
					$optPrintPriceEtc = calcuPrintETCOpset($inside_paper_size, $opset_inpage_yuen, $_POST[$inpage_print3], $inpage_print_key);
			}
			//print_r($_POST[opt_print3]);
			//print_r($printPriceEtc);
		}
		$inpagePrintPriceEtc[] = $optPrintPriceEtc;
	}
	
	$inpagePaperPriceTotal = 0;
	$inpagePrintPriceTotal = 0;
	$inpagePrintEtcPriceTotal = 0;
	$inpageCTPPriceTotal = 0;
	if (is_array($inpagePaperPrice))
	{
		foreach ($inpagePaperPrice as $key => $value) {
			$inpagePaperPriceTotal += $value;
			$inpagePrintPriceTotal += $inpagePrintPrice[$key];
			$inpagePrintEtcPriceTotal += $inpagePrintPriceEtc[$key];
			$inpageCTPPriceTotal += $inpageCTPPrice[$key];
		}
	}
	
	$response['inpage_paper_price'] = $inpagePaperPriceTotal;	
	$response['inpage_print_price'] = $inpagePrintPriceTotal;
	$response['inpage_print_etc_price'] = $inpagePrintEtcPriceTotal;
	$response['inpage_ctp_price'] = $inpageCTPPriceTotal;
		
	//echo "$inside_paper_size\r\n";
	//echo "$insideYuenTotal\r\n";	
	
		
	
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
	
	
	//인쇄 key 만들기. 앞면, 뒷면을 별도 인쇄 처리한다. 각각 처리하므로 단면으로 처리.
	$opt_print_key_front = getPrintSideCode($_POST['outside_print1'], "O");
	$opt_print_key_back = getPrintSideCode($_POST['outside_print4'], "O");
		
	$etc_color_front_cnt = count($_POST[outside_print3]);
	$etc_color_back_cnt = count($_POST[outside_print6]);
	
	
	$opset_cover_dea = 1; 			//커버의 대 수량은 무조건 1	
	$bookCoverSizeKey = getBookCoverSize($_POST['opt_size'], $opt_bind_type, $outside_wing_width, $insidePageWidthTotal, "Y");			//지류값 계산시 표지 사이즈 * 2 해당하는 규격을 찾는다.	제본 type, 날개 크기로 영향을 받는다.
	$opset_cover_paper_size = getOpsetPaperSize($bookCoverSizeKey);	
	$opset_cover_prefix_print_key = makePrefixOpsetPrintKey($_POST['outside_print1'], $_POST['outside_print4'], $etc_color_front_cnt, $etc_color_back_cnt);
	$opset_cover_yuen = getOpsetPrintYuen($bookCoverSizeKey, $_POST['cnt'], $opset_cover_prefix_print_key, $opset_cover_dea);		//옵션  연 구하기.
	
	//echo "{$_POST['cnt']}\r\n";
	//echo "$bookCoverSizeKey\r\n";
	//echo "$opset_cover_paper_size\r\n";
	//echo "$opset_cover_prefix_print_key\r\n";
	//echo "$opset_cover_yuen\r\n";
	
		
	$outsidePaperPrice = calcuPaperOpset($bookCoverSizeKey, $_POST['outside_paper'], $_POST['outside_paper_gram'], $opset_cover_yuen);	
	//echo "$outsidePaperPrice\r\n";
	
	$frontCTPPrice = calcuCTPOpset($opt_print_key_front, $opset_cover_dea, $etc_color_front_cnt);
	$backCTPPrice = calcuCTPOpset($opt_print_key_back, $opset_cover_dea, $etc_color_back_cnt);
	$outsideCTPPrice = $frontCTPPrice + $backCTPPrice;
			
	//인쇄비는 앞면, 뒷면, 별색 을 각각 구한다.
	$printPriceFront = calcuPrintOpset($opset_cover_paper_size, $opset_cover_yuen, $opt_print_key_front);		//인쇄비는 용지크기로 구한다.
	$printPriceBack = calcuPrintOpset($opset_cover_paper_size, $opset_cover_yuen, $opt_print_key_back);		//인쇄비는 용지크기로 구한다.
	//echo "$printPriceFront\r\n";
	
	//앞면 별색 인쇄비
	$printPriceFrontEtc = 0;
	if ($_POST[outside_print3_check] == "Y") 
	{	
		if (is_array($_POST[outside_print3]))
		{
			foreach ($_POST[outside_print3] as $pkey => $pvalue) {
				$printPriceFrontEtc += calcuPrintETCOpset($opset_cover_paper_size, $opset_cover_yuen, $pvalue, $opt_print_key_front);	
			}			
		} else {
			$printPriceFrontEtc = calcuPrintETCOpset($opset_cover_paper_size, $opset_cover_yuen, $_POST[outside_print3], $opt_print_key_front);	
		}
		//print_r($_POST[outside_print3]);
		//print_r($printPriceEtc);
		//echo "$printPriceFrontEtc\r\n";
	}
	
	//뒷면 별색 인쇄비
	$outsidePrintEtcPrice = 0;
	if ($_POST[outside_print6_check] == "Y") 
	{	
		if (is_array($_POST[outside_print6]))
		{
			foreach ($_POST[outside_print6] as $pkey => $pvalue) {
				$outsidePrintEtcPrice += calcuPrintETCOpset($opset_cover_paper_size, $opset_cover_yuen, $pvalue, $opt_print_key_back);	
			}			
		} else {
			$outsidePrintEtcPrice = calcuPrintETCOpset($opset_cover_paper_size, $opset_cover_yuen, $_POST[outside_print6], $opt_print_key_back);	
		}
		//print_r($_POST[opt_print3]);
		//print_r($printPriceEtc);
	}
		
	
	if ($_POST['opt_outside_gloss_check'] == "Y")
		$outsideGlossPrice = calcuGlossOpset($bookCoverSizeKey, $opset_cover_yuen, $_POST['outside_gloss']);
		
	$response['outside_paper_price'] = $outsidePaperPrice;
	$response['outside_print_price'] = $printPriceFront + $printPriceBack;
	$response['outside_print_etc_price'] = $printPriceFrontEtc + $outsidePrintEtcPrice;
	$response['outside_ctp_price'] = $outsideCTPPrice;	
	$response['outside_gloss_price'] = $outsideGlossPrice;
	
	
	//내지 코팅
	if ($_POST['opt_gloss_check'] == "Y")	
		$insideGlossPrice = calcuGlossOpset($_POST['opt_size'], $insideYuenTotal, $_POST['opt_gloss']);
	
	//형압, 박, 부분 UV 3가지는 계산식이 같다.			20180719		chunter
	//형압 계산
	if ($_POST['opt_press_check'] == "Y")
	{
		$opt_mm2 = 0;
		if ($_POST['opt_press_width'] && $_POST['opt_press_height'])
			$opt_mm2 = $_POST['opt_press_width'] * $_POST['opt_press_height'];	
		if ($opt_mm2 > 0)	
			$pressPrice = calcuPressOpset($_POST['opt_size'], $opset_cover_yuen, $opt_mm2, $_POST['opt_press']);
	}
	
	//박 계산
	if ($_POST['opt_foil_check'] == "Y")
	{
		$opt_mm2 = 0;
		if ($_POST['opt_foil_width'] && $_POST['opt_foil_height'])
			$opt_mm2 = $_POST['opt_foil_width'] * $_POST['opt_foil_height'];
		if ($opt_mm2 > 0)
		{
			//박은 멀티 선택이 가능하다.
			$foilPrice = 0;
			foreach ($_POST['opt_foil'] as $fkey => $fvalue) {
				$foilPrice += calcuFoilOpset($_POST['opt_size'], $opset_cover_yuen, $opt_mm2, $fvalue);	
			}			
		}
	}
	
	//부분 UV 계산
	if ($_POST['opt_uvc_check'] == "Y")
	{
		$opt_mm2 = 0;
		if ($_POST['opt_uvc_width'] && $_POST['opt_uvc_height'])
			$opt_mm2 = $_POST['opt_uvc_width'] * $_POST['opt_uvc_height'];
		if ($opt_mm2 > 0)
			$uvcPrice = calcuUVCOpset($_POST['opt_size'], $opset_cover_yuen, $opt_mm2, $_POST['opt_uvc']);
	}
	
	//도무송
	if ($_POST['opt_domoo_check'] == "Y")
	{
		$opt_mm2 = 0;
		if ($_POST['opt_domoo_width'] && $_POST['opt_domoo_height'])
			$opt_mm2 = $_POST['opt_domoo_width'] * $_POST['opt_domoo_height'];
		if ($opt_mm2 > 0)
			$domooPrice = calcuDomooOpset($_POST['opt_size'], $opset_cover_yuen, $opt_mm2, $_POST['opt_domoo']);
	}
	
	
	//제본 계산
	$first_paper_gram = $_POST['inside_paper_gram'][0];			//제본 계산시 사용될 첫번째 내지 지류 평량
	if ($_POST['opt_bind_check'] == "Y" && $first_paper_gram)
	{
		//무선 제본 (BD1)
		if ($_POST['opt_bind'] == "BD1")
		{
			$bindPrice = calcuBindBD1Opset($insidePageCntTotal, $_POST['cnt'], $first_paper_gram);
		}
		
		//중철 제본 (BD3)
		if ($_POST['opt_bind'] == "BD3")
		{
			$bindPrice = calcuBindBD3Opset($insidePageCntTotal, $_POST['cnt'], $first_paper_gram, $insideDeaTotal);
		}
	}
		
	
	if ($_POST['opt_holding_check'] == "Y")
		$holdingPrice = calcuHoldingOpset($_POST['opt_size'], $opset_cover_yuen, $_POST['opt_holding']);
		
		
	$response[inside_gloss_price] = $insideGlossPrice;	
	$response[press_price] = $pressPrice;
	$response[foil_price] = $foilPrice;
	$response[uvc_price] = $uvcPrice;	
	$response[domoo_price] = $domooPrice;	
	$response[bind_price] = $bindPrice;
	$response[holding_price] = $holdingPrice;	
	
	include_once 'option_calcu_common.php';
		
	$response['opset_inpage_dea'] = $opset_inpage_dea;
	$response['opset_inpage_yuen'] = $opset_inpage_yuen;
	$response['real_cover_size'] = $bookCoverSizeKey;
	$response['cover_yuen'] = $opset_cover_yuen;
	$response['inside_yuen'] = $insideYuenTotal;
	$response['inside_dea'] = $insideDeaTotal;
	
	echo json_encode($response);

?>