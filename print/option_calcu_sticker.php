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
* @brief : 낱장인쇄 스티커 전용 계산식.
* @desc : 계산식 개발후 검증이 필요하고. 검증 완료후 각 계산 파일별 중복 내용 정리가 필요함
*/
?>
<? 
	$calcu_mode = "Y";
	include_once 'lib_print.php';

	$mode = $_POST[mode];
	
	//인쇄 key 만들기.
	$opt_print_key = getPrintSideCode($_POST['opt_print1'], $_POST['opt_print2']);
		
	//필수값 체크
	$bCalcuFlag = true;
	if (! $_POST['opt_size']) $bCalcuFlag = false;
	if (! $_POST['opt_page']) $bCalcuFlag = false;
	if (! $_POST['cnt']) $bCalcuFlag = false;
	
	
	if ($bCalcuFlag)
	{		
		//사용자 규격일경우 사이즈에 맞는 종이 규격을 찾는다.
		if ($_POST['opt_size'] == "USER")
		{
			$work_width = $_POST['work_width'];
			$work_height = $_POST['work_height'];
		} else {
			$work_size = explode("*", $r_ipro_print_code[$opt_size_key]);
			$work_width = $work_size[0];
			$work_height = $work_size[1];			
			
			$work_width = $_POST['work_width'];
			$work_height = $_POST['work_height'];
			//$opt_size_key = $_POST['opt_size']; 
		}
		
		//도무송 가격 처리를 위해 스티커 주문에 가장 맞는 출력 규격 찾기
		if ($work_width && $work_height)
		{
			//$opt_size_key = getStickerPrintOptSize($work_width, $work_height);
			$opt_domoo_size_key = getStickerPrintOptSizeWithCustom($work_width, $work_height);			
		}		
		
		//사용자 입력 규격의 경우 비규격 사이즈가  width, height 설정한다.
		if ($_POST['opt_size'] == "USER")
			$work_width = $work_height = $r_ipro_print_code[$opt_domoo_size_key];
			
		$opt_document_size_key = "A3";		//스티커는 기본 A3로만 처리한다.		//A3 안에 몇장 들어가는가..		
		$fitCount = getFitCountInPaper($opt_document_size_key, $work_width, $work_height);
			
		//사용자 규격은 해당 규격에 몇개 들어가는지에 따라 수량을 조절한다.
		$paperFitCnt = ceil($_POST['opt_page'] / $fitCount);
			
		//print_r($opt_size_key);				
		$paperPrice = calcuPaperSticker($paperFitCnt, $_POST['opt_paper'], $_POST['opt_paper_gram']);		
		$paperPrice *= $_POST['cnt'];
		
		$printPrice = calcuPrintSticker($opt_document_size_key, $paperFitCnt, $opt_print_key);
		$printPrice *= $_POST['cnt'];		
		//전체 금액에서 할인을 한다.
		$printPrice = calcuPrintDCDigital($printPrice, $opt_document_size_key, $opt_print_key);
		
			
		//별색 계산. 별색은 여러개 선택이 가능함.
		$printPriceEtc = 0;
		if ($_POST[opt_print3_check] == "Y") {	
			if (is_array($_POST[opt_print3]))
			{
				foreach ($_POST[opt_print3] as $key => $value) {
					$printPriceEtcTemp = calcuPrintETCDigital($opt_document_size_key, $paperFitCnt, $value, $opt_print_key);	
					
					//전체 금액에서 할인을 한다.
					$printPriceEtcTemp *= $_POST['cnt'];					
					$printPriceEtc += calcuPrintDCDigital($printPriceEtcTemp, $opt_document_size_key, $value);
				}			
			} else {
				$printPriceEtcTemp = calcuPrintETCDigital($opt_document_size_key, $paperFitCnt, $_POST[opt_print3], $opt_print_key);
				
				//전체 금액에서 할인을 한다.
				$printPriceEtcTemp *= $_POST['cnt'];					
				$printPriceEtc = calcuPrintDCDigital($printPriceEtcTemp, $opt_document_size_key, $_POST[opt_print3]);	
			}			
		}
		
		//후가공은 A3 기준으로 가격을 계산한다.
		//후가공 가격 계산을 위한 구간은 부수*건수로 정한다. 후가공은 장당 단가이다.
		$optionCaluPage = $paperFitCnt * $_POST['cnt'];
		
		if ($_POST['opt_gloss_check'] == "Y")
			$glossPrice = calcuGlossDigital($opt_document_size_key, $optionCaluPage, $_POST['opt_gloss']);
		if ($_POST['opt_oshi_check'] == "Y")
			$oshiPrice = calcuOshiDigital($opt_document_size_key, $optionCaluPage, $_POST['opt_oshi']);
		
		if ($_POST['opt_sc_check'] == "Y")
			$scPrice = calcuSCDigital($opt_document_size_key, $optionCaluPage, $_POST['opt_sc']);
		if ($_POST['opt_scb_check'] == "Y")
			$scbPrice = calcuSCBDigital($opt_document_size_key, $optionCaluPage, $_POST['opt_scb']);
		if ($_POST['opt_cut_check'] == "Y")
			$cutPrice = calcuCutDigital($opt_document_size_key, $optionCaluPage, $_POST['opt_cut']);
		
		//스티커의 경우 도무송 계산이 다르다.
		//도무송은 A3 가 아닌 주문 규격에 맞는 계산을 찾는다.
		if ($_POST['opt_domoo_check'] == "Y")
		{			
			//자유형 스티커 계산.
			if ($_POST['print_goods_type'] == "DG04")
			{			
				//print_r($opt_size_key);						
				if ($paperFitCnt > 1)
				{
					//$domooPrice = calcuDomooSticker($opt_domoo_size_key, $optionCaluPage, $_POST['opt_domoo'], $paperFitCnt);
					//종이가 1장이상인 경우 실제 출력 종리를 규격으로 넘겨야 한다.			20180813	
					$domooPrice = calcuDomooSticker($opt_document_size_key, $optionCaluPage, $_POST['opt_domoo'], $paperFitCnt);					
					$domooPrice *= $paperFitCnt;		//실제 종류수량 단가.
				}
				else
				{
					//$fitCount - 하리꾸미 갯수
					$domooPrice = calcuDomooSticker($opt_domoo_size_key, $fitCount, $_POST['opt_domoo'], $paperFitCnt);
					$domooPrice *= $_POST['opt_page'];		//주문수량(부) 단가.
				}
			} else {
				//사각형 스티커 계산(주문수량)			//단가 계산이 아님.. 구간 참조임.
				$domooPrice = calcuDomooSticker($opt_domoo_size_key, $_POST['opt_page'], $_POST['opt_domoo'], 0);
			}
		}
	}
	
	$response[paper_price] = $paperPrice;	
	$response[print_price] = $printPrice;
	$response[print_etc_price] = $printPriceEtc;
	$response[domoo_price] = $domooPrice * $_POST['cnt'];;
		
	$response[gloss_price] = $glossPrice * $_POST['cnt'];	
	$response[oshi_price] = $oshiPrice * $_POST['cnt'];	
	$response[sc_price] = $scPrice * $_POST['cnt'];
	$response[scb_price] = $scbPrice * $_POST['cnt'];
	$response[cut_price] = $cutPrice * $_POST['cnt'];
	
		
		
	include_once 'option_calcu_common.php';
	
	$response[paperFitCnt] = $paperFitCnt;
	$response[opt_domoo_size_key] = $opt_domoo_size_key;
	echo json_encode($response);

?>