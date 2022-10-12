<? 
//용지 계산
function calcuPaperSticker($paperCnt, $paper_key, $paper_gram)
{
	global $r_ipro_paper, $r_ipro_paper_dc;
	$result = 0;
			
	$dc_ratio = $r_ipro_paper_dc[$paper_key];			//지류 할인율
	$price_b2 = $r_ipro_paper[$paper_key]['paper'][$paper_gram]['B2'];
	$price_a3 = $r_ipro_paper[$paper_key]['paper'][$paper_gram]['A3'];
		
	if ($price_b2)
	{		
		$result = $price_b2 * $paperCnt;
	} else if ($price_a3) {		
		$result = $price_a3 * $paperCnt;
	}
		
	return $result;
}


function calcuPrintSticker($size_key, $cnt, $print_key)
{
	global $r_ipro_print_digital;
	$result = 0;
	//print_r($r_ipro_print_digital[$size_key]);
	//$paper_cnt = getOptionStandardPaperCnt($size_key, $cnt);
	
	
	//단면, 양면 구하기
	if (substr($print_key, 0, 1) == "D")
		$side_flag = "2";
	else 
		$side_flag = "1";
	
	
	if (is_array($r_ipro_print_digital[$size_key]))
	{
		$calcuData = getCalcuPriceData($r_ipro_print_digital[$size_key], $cnt);		
						
		if (is_array($calcuData))
		{			
			if ($print_key == "OC5" || $print_key == "DC6" || $print_key == "DC10")			//컬러
				$result = $calcuData['OC1'] * $side_flag;
			else if ($print_key == "DB7" || $print_key == "OB4" || $print_key == "DB9")	//인디고 흑백
				$result = $calcuData['OB2'] * $side_flag;
			else if ($print_key == "DB8")																								//누베라 흑백
				$result = $calcuData['OB3'] * $side_flag;
			else $result = $calcuData[$print_key] * $side_flag;
		}
		
		$result = $result * $cnt;
	}
	$result = $result * $cnt;				//인쇄비 관리를 장당 단가로 변경한다.				20180724		chunter
	return $result;
	
}


//1장 이상  또는 1장 출력에 대한 스티커 도무송 계산
function calcuDomooSticker($size_key, $cnt, $domoo_key, $paperCnt)
{
	global $r_ipro_domoo_sticker_digital, $r_ipro_domoo_sticker_other_digital;	
	$result = 0;
	
	if ($paperCnt > 1)
		$calcuData = getCalcuPriceData($r_ipro_domoo_sticker_other_digital[$size_key], $cnt);
	else
		$calcuData = getCalcuPriceData($r_ipro_domoo_sticker_digital[$size_key], $cnt);
	if (is_array($calcuData)){
		$result = $calcuData[$domoo_key];
	}
	
	return $result * $cnt;
}


?>