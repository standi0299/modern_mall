<?
/*
* @date : 20180726
* @author : chunter
* @brief : 변경된 계산식 개발 완료.
* @desc : 자동계산_가격계산_수식.doc 문서 참고.
 
* @date : 20180530
* @author : chunter
* @brief : 옵셋 낱장 인쇄 계산식. 스티커 제외
* @desc : 계산식 개발후 검증이 필요하고.
*/
?>
<?
	$calcu_mode = "Y"; 
	include_once 'lib_print.php';

	$mode = $_POST[mode];
	//debug($r_ipro_ctp_opset);	
	//인쇄 key 만들기. 앞면, 뒷면을 별도 인쇄 처리한다. 각각 처리하므로 단면으로 처리.
	$opt_print_key_front = getPrintSideCode($_POST['opt_print1'], "O");
	$opt_print_key_back = getPrintSideCode($_POST['opt_print4'], "O");
	
	$etc_color_front_cnt = 0;
	$etc_color_back_cnt = 0;
	if ($_POST[opt_print3_check] == "Y") 
		$etc_color_front_cnt = count($_POST[opt_print3]);	
	if ($_POST[opt_print6_check] == "Y") 
		$etc_color_back_cnt = count($_POST[opt_print6]);
	
	$opset_dea = 1;		//인쇄 대수 구하기		//낱장인쇄는 무조건 대수는 1이다.
	$opset_paper_size = getOpsetPaperSize($_POST['opt_size']);	
	$prefix_print_key = makePrefixOpsetPrintKey($_POST['opt_print1'], $_POST['opt_print4'], $etc_color_front_cnt, $etc_color_back_cnt);
	$opset_yuen = getOpsetPrintYuen($_POST['opt_size'], $_POST['opt_page'], $prefix_print_key, $opset_dea);		//옵션  연 구하기.
	
	//print_r($opset_paper_size);
	//$paperPrice = calcuPaperOpset($_POST['opt_size'], $_POST['opt_page'], $_POST['opt_paper'], $_POST['opt_paper_gram'], $prefix_print_key, $opset_dea);
	$paperPrice = calcuPaperOpset($_POST['opt_size'], $_POST['opt_paper'], $_POST['opt_paper_gram'], $opset_yuen);
	//전체 금액에서 할인한다.
	$paperPrice *= $_POST['cnt'];
	$paperPrice = calcuPaperDCOpset($paperPrice, $_POST['opt_paper']);	
	
	$frontCTPPrice = calcuCTPOpset($opt_print_key_front, $opset_dea, $etc_color_front_cnt);
	$backCTPPrice = calcuCTPOpset($opt_print_key_back, $opset_dea, $etc_color_back_cnt);
	$CTPPrice = $frontCTPPrice + $backCTPPrice;
		
	//인쇄비는 앞면, 뒷면, 별색 을 각각 구한다.
	$printPriceFront = calcuPrintOpset($opset_paper_size, $opset_yuen, $opt_print_key_front);		//인쇄비는 용지크기로 구한다.
	$printPriceBack = calcuPrintOpset($opset_paper_size, $opset_yuen, $opt_print_key_back);		//인쇄비는 용지크기로 구한다.
	
	//앞면 별색 인쇄비
	$printPriceFrontEtc = 0;
	if ($_POST[opt_print3_check] == "Y") 
	{	
		if (is_array($_POST[opt_print3]))
		{
			foreach ($_POST[opt_print3] as $pkey => $pvalue) {
				$printPriceFrontEtc += calcuPrintETCOpset($opset_paper_size, $opset_yuen, $pvalue, $opt_print_key_front);	
			}			
		} else {
			$printPriceFrontEtc = calcuPrintETCOpset($opset_paper_size, $opset_yuen, $_POST[opt_print3], $opt_print_key_front);	
		}
		//print_r($_POST[opt_print3]);
		//print_r($printPriceEtc);
	}
	
	//뒷면 별색 인쇄비
	$printPriceBackEtc = 0;
	if ($_POST[opt_print6_check] == "Y") 
	{	
		if (is_array($_POST[opt_print6]))
		{
			foreach ($_POST[opt_print6] as $pkey => $pvalue) {
				$printPriceBackEtc += calcuPrintETCOpset($opset_paper_size, $opset_yuen, $pvalue, $opt_print_key_back);	
			}			
		} else {
			$printPriceBackEtc = calcuPrintETCOpset($opset_paper_size, $opset_yuen, $_POST[opt_print6], $opt_print_key_back);	
		}
		//print_r($_POST[opt_print3]);
		//print_r($printPriceEtc);
	}	
	
		
	if ($_POST['opt_gloss_check'] == "Y")
		$glossPrice = calcuGlossOpset($_POST['opt_size'], $opset_yuen, $_POST['opt_gloss']);
	
	if ($_POST['opt_punch_check'] == "Y")
		$punchPrice = calcuPunchOpset($_POST['opt_size'], $opset_yuen, $_POST['opt_punch']);
		
	if ($_POST['opt_oshi_check'] == "Y")
		$oshiPrice = calcuOshiOpset($_POST['opt_size'], $opset_yuen, $_POST['opt_oshi']);
		
	if ($_POST['opt_cutting_check'] == "Y")
		$cuttingPrice = calcuCuttingOpset($_POST['opt_size'], $opset_yuen, $_POST['opt_cutting']);
	
	if ($_POST['opt_holding_check'] == "Y")
		$holdingPrice = calcuHoldingOpset($_POST['opt_size'], $opset_yuen, $_POST['opt_holding']);
	
	//형압 계산
	if ($_POST['opt_press_check'] == "Y")
	{
		$opt_mm2 = 0;
		if ($_POST['opt_press_width'] && $_POST['opt_press_height'])
			$opt_mm2 = $_POST['opt_press_width'] * $_POST['opt_press_height'];	
		if ($opt_mm2 > 0)	
			$pressPrice = calcuPressOpset($opset_yuen, $opt_mm2, $_POST['opt_press']);
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
				$foilPrice += calcuFoilOpset($opset_yuen, $opt_mm2, $fvalue);	
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
			$uvcPrice = calcuUVCOpset($opset_yuen, $opt_mm2, $_POST['opt_uvc']);
	}
	
	//도무송
	if ($_POST['opt_domoo_check'] == "Y")
	{
		$opt_mm2 = 0;
		if ($_POST['opt_domoo_width'] && $_POST['opt_domoo_height'])
			$opt_mm2 = $_POST['opt_domoo_width'] * $_POST['opt_domoo_height'];
		if ($opt_mm2 > 0)
			$domooPrice = calcuDomooOpset($opset_yuen, $opt_mm2, $_POST['opt_domoo']);
	}
	
	
	
	
	$response[paper_price] = $paperPrice;		
	$response[print_price] = ($printPriceFront + $printPriceBack) * $_POST[cnt];
	$response[print_etc_price] = ($printPriceFrontEtc + $printPriceBackEtc) * $_POST[cnt];
	
	$response[ctp_price] = $CTPPrice * $_POST[cnt];
	$response[gloss_price] = $glossPrice * $_POST[cnt];
	$response[punch_price] = $punchPrice * $_POST[cnt];
	$response[oshi_price] = $oshiPrice * $_POST[cnt];	
	$response[cutting_price] = $cuttingPrice * $_POST[cnt];
	$response[holding_price] = $holdingPrice * $_POST[cnt];
	
	$response[foil_price] = $foilPrice * $_POST[cnt];
	$response[press_price] = $pressPrice * $_POST[cnt];
	$response[uvc_price] = $uvcPrice * $_POST[cnt];
	$response[domoo_price] = $domooPrice * $_POST[cnt];
		
	include_once 'option_calcu_common.php';
	
	$response[opt_print_key_front] = $opt_print_key_front;
	$response[opt_print_key_back] = $opt_print_key_back;
	
	$response[yeon] = $opset_yuen;
	$response[opset_paper_size] = $opset_paper_size;
	
	$response[printPriceFront] = $printPriceFront;
	$response[printPriceBack] = $printPriceBack;
	
	echo json_encode($response);
?>