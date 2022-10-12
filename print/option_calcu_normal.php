<?
/*
* @date : 20180720
* @author : chunter
* @brief : 변경된 계산식 개발 완료.
* @desc : 자동계산_가격계산_수식.doc 문서 참고.
*/
/*
* @date : 20180508
* @author : chunter
* @brief : 일반인쇄 계산식. 명함과 스티커 로 분리한다.
* @desc : 계산식 개발후 검증이 필요하고. 검증 완료후 각 계산 파일별 중복 내용 정리가 필요함
*/
?>
<? 
	$calcu_mode = "Y";
	include_once 'lib_print.php';
	
	$mode = $_POST[mode];
	$print_goods_type = $_POST[print_goods_type];			//명함인지, 스티커인지 구분

	//사용자 규격일경우 사이즈에 맞는 종이 규격을 찾는다.
	if ($_POST['opt_size'] == "USER")
	{
		//일반 스티커인경우만 재단사이즈로 가격을 찾아야 하기때문에 재단사이즈로 구한다. 
		$opt_size_key = getOptionSizeWithCalcuPriceData($r_iro_normal_product, $_POST['cut_width'], $_POST['cut_height']);		
	} else {
		$opt_size_key = $_POST['opt_size'];		
	}
	
	
	//인쇄 key 만들기.
	$opt_print_key = getPrintSideCode($_POST['opt_print1'], $_POST['opt_print2']);	
	//print_r($opt_print_key);
	
	$paperPrice = calcuPaperNormal($opt_size_key, $_POST['opt_page'], $_POST['opt_paper'], $_POST['opt_paper_gram']);
	$printPrice = calcuPrintNormal($opt_size_key, $_POST['opt_page'], $opt_print_key);
	
	$printPriceEtc = 0;
	if ($_POST[opt_print3_check] == "Y") {
		//print_r($_POST[opt_print3]);	
		if (is_array($_POST[opt_print3]))
		{				
			foreach ($_POST[opt_print3] as $key => $value) {
				$printPriceEtc += calcuPrintETCNormal($opt_size_key, $_POST['opt_page'], $value, $opt_print_key);	
			}			
		} else {
			$printPriceEtc = calcuPrintETCNormal($opt_size_key, $_POST['opt_page'], $_POST[opt_print3], $opt_print_key);	
		}
	}
		
	
	
	if ($_POST['opt_gloss_check'] == "Y")
		$glossPrice = calcuCommonOptionNormal($opt_size_key, $_POST['opt_page'], $_POST['opt_gloss']);
	if ($_POST['opt_round_check'] == "Y")
		$roundPrice = calcuCommonOptionNormal($opt_size_key, $_POST['opt_page'], $_POST['opt_round']);
	if ($_POST['opt_domoo_check'] == "Y")
		$domooPrice = calcuCommonOptionNormal($opt_size_key, $_POST['opt_page'], $_POST['opt_domoo']);
	if ($_POST['opt_cu_check'] == "Y")
		$domooPrice = calcuCommonOptionNormal($opt_size_key, $_POST['opt_page'], $_POST['opt_cu']);	
	
	$instantPrice = calcuCommonOptionNormal($opt_size_key, $_POST['opt_page'], $_POST['opt_instant']);
	if ($_POST['opt_sc_check'] == "Y")
		$scPrice = calcuCommonOptionNormal($opt_size_key, $_POST['opt_page'], $_POST['opt_sc']);
	if ($_POST['opt_scb_check'] == "Y")
		$scbPrice = calcuSCBNormal($opt_size_key, $_POST['opt_page'], $_POST['opt_scb']);
	
	$paperPrice *= $_POST['cnt'];	
	$printPrice *= $_POST['cnt'];
	$printPriceEtc *= $_POST['cnt'];
	$glossPrice *= $_POST['cnt'];
	$roundPrice *= $_POST['cnt'];
	$domooPrice *= $_POST['cnt'];	
	$instantPrice *= $_POST['cnt'];	
	$scPrice *= $_POST['cnt'];
	$scbPrice *= $_POST['cnt'];
	
	
	$response[paper_price] = $paperPrice;	
	$response[print_price] = $printPrice;
	$response[print_etc_price] = $printPriceEtc;	
	$response[gloss_price] = $glossPrice;
	$response[round_price] = $roundPrice;
	$response[domoo_price] = $domooPrice;	
	$response[instrant_price] = $instantPrice;	
	$response[sc_price] = $scPrice;
	$response[scb_price] = $scbPrice;
		
		
	include_once 'option_calcu_common.php';
	
	echo json_encode($response);

?>