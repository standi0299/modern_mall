<?
/*
* @date : 20180724
* @author : chunter
* @brief : 변경된 계산식 개발 완료.
* @desc : 자동계산_가격계산_수식.doc 문서 참고.
*/

/*
* @date : 20180508
* @author : chunter
* @brief : 낱장인쇄 계산식. 스티커 제외
* @desc : 계산식 개발후 검증이 필요하고. 검증 완료후 각 계산 파일별 중복 내용 정리가 필요함
*/
?>
<? 
	$calcu_mode = "Y";
	include_once 'lib_print.php';

	$mode = $_POST[mode];
	
	//if (!$_POST['cnt']) $_POST['cnt'] = 1;
	
	//인쇄 key 만들기.
	$opt_print_key = getPrintSideCode($_POST['opt_print1'], $_POST['opt_print2']);
	
	//필수값 체크
	$bCalcuFlag = true;
	if (! $_POST['opt_size']) $bCalcuFlag = false;
	if (! $_POST['opt_page']) $bCalcuFlag = false;
	
	
	//사용자 규격일경우 사이즈에 맞는 종이 규격을 찾는다.
	if ($_POST['opt_size'] == "USER")
	{
		$opt_size_key = getPaperWithDocumentSize($_POST['work_width'], $_POST['work_height']);		
	} else {
		$opt_size_key = $_POST['opt_size'];		
	}
		
	//print_r($opt_print_key);

	if ($bCalcuFlag)
	{		
		$paperPrice = calcuPaperDigital($opt_size_key, $_POST['opt_page'], $_POST['opt_paper'], $_POST['opt_paper_gram']);
		$paperPrice *= $_POST['cnt'];
		
		$printPrice = calcuPrintDigital($opt_size_key, $_POST['opt_page'], $opt_print_key);
		$printPrice *= $_POST['cnt'];

		//전체 금액에서 할인을 한다.
		$printPrice = calcuPrintDCDigital($printPrice, $opt_size_key, $opt_print_key);	
				
			
		//별색 계산. 별색은 여러개 선택이 가능함.
		$printPriceEtc = 0;
		if ($_POST[opt_print3_check] == "Y") 
		{	
			if (is_array($_POST[opt_print3]))
			{
				foreach ($_POST[opt_print3] as $key => $value) {
					$printPriceEtcTemp = calcuPrintETCDigital($opt_size_key, $_POST['opt_page'], $value, $opt_print_key);
					
					//전체 금액에서 할인을 한다.
					$printPriceEtcTemp *= $_POST['cnt'];					
					$printPriceEtc += calcuPrintDCDigital($printPriceEtcTemp, $opt_size_key, $value);
				}			
			} else {
				$printPriceEtcTemp = calcuPrintETCDigital($opt_size_key, $_POST['opt_page'], $_POST[opt_print3], $opt_print_key);
				
				//전체 금액에서 할인을 한다.
				$printPriceEtcTemp *= $_POST['cnt'];					
				$printPriceEtc = calcuPrintDCDigital($printPriceEtcTemp, $opt_size_key, $_POST[opt_print3]);	
			}
			//print_r($_POST[opt_print3]);
			//print_r($printPriceEtc);
		}
		
		//백색 인쇄 있을경우 별도 별색 처리한다.
		if ($_POST['opt_print1'] == "W")
		{
			$printPriceEtcTemp = calcuPrintETCDigital($opt_size_key, $_POST['opt_page'], "ET1", $opt_print_key);				
			//전체 금액에서 할인을 한다.
			$printPriceEtcTemp *= $_POST['cnt'];					
			$printPriceEtc += calcuPrintDCDigital($printPriceEtcTemp, $opt_size_key, "ET1");
		}
		
		
				
		
		//후가공 가격 계산을 위한 구간은 부수*건수로 정한다. 낱장인쇄의 후가공은 장당 단가이다.
		$optionCaluPage = $_POST['opt_page'] * $_POST['cnt'];
		
		if ($_POST['opt_gloss_check'] == "Y")
			$glossPrice = calcuGlossDigital($opt_size_key, $optionCaluPage, $_POST['opt_gloss']);
		if ($_POST['opt_punch_check'] == "Y")
			$punchPrice = calcuPunchDigital($opt_size_key, $optionCaluPage, $_POST['opt_punch']);
		if ($_POST['opt_oshi_check'] == "Y")
			$oshiPrice = calcuOshiDigital($opt_size_key, $optionCaluPage, $_POST['opt_oshi']);
		
		if ($_POST['opt_sc_check'] == "Y")
			$scPrice = calcuSCDigital($opt_size_key, $optionCaluPage, $_POST['opt_sc']);
		if ($_POST['opt_scb_check'] == "Y")
			$scbPrice = calcuSCBDigital($opt_size_key, $optionCaluPage, $_POST['opt_scb']);		
		if ($_POST['opt_cut_check'] == "Y")
			$cutPrice = calcuCutDigital($opt_size_key, $optionCaluPage, $_POST['opt_cut']);
		if ($_POST['opt_domoo_check'] == "Y")
			$domooPrice = calcuDomooDigital($opt_size_key, $optionCaluPage, $_POST['opt_domoo']);		
		if ($_POST['opt_barcode_check'] == "Y")	
			$barcodePrice = calcuBarcodeDigital($opt_size_key, $optionCaluPage, $_POST['opt_barcode']);
		if ($_POST['opt_number_check'] == "Y")
			$numberPrice = calcuNumberDigital($opt_size_key, $optionCaluPage, $_POST['opt_number']);
	}
	
	$response[paper_price] = $paperPrice;	
	$response[print_price] = $printPrice;
	$response[print_etc_price] = $printPriceEtc;	
	
	$response[gloss_price] = $glossPrice;
	$response[punch_price] = $punchPrice;
	$response[oshi_price] = $oshiPrice;	
	$response[sc_price] = $scPrice;
	$response[scb_price] = $scbPrice;
	$response[cut_price] = $cutPrice;
	$response[domoo_price] = $domooPrice;
	$response[barcode_price] = $barcodePrice;
	$response[number_price] = $numberPrice;
	
	
	include_once 'option_calcu_common.php';
	
	echo json_encode($response);

?>