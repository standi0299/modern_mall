<? 


//용지 계산		
function calcuPaperOpset($size_key, $paper_key, $paper_gram, $paper_yuen)
{
	global $r_ipro_standard_opset, $r_ipro_paper;
	$result = 0;
	//기준규격에 대해서 찾기	
	$standard_b2 = $r_ipro_standard_opset[$size_key]['B2'];
	$standard_a1 = $r_ipro_standard_opset[$size_key]['A1'];
	
	//기준 용지 가격 구하기
	if ($standard_b2 > 0)
		$paper_price = $r_ipro_paper[$paper_key]['paper'][$paper_gram]['OB2'];
	if ($standard_a1 > 0)
		$paper_price = $r_ipro_paper[$paper_key]['paper'][$paper_gram]['OA1'];

	//print_r($cnt."/");
	//print_r($standard_b2."/");
	//print_r($paper_cnt."/");	
	
	$result = ceil($paper_price * $paper_yuen);
	//print_r($result);
	return $result;
}

//할인 계산
function calcuPaperDCOpset($paper_price, $paper_key)
{
	global $r_ipro_paper_dc;
	$result = $paper_price;
			
	$dc_ratio = $r_ipro_paper_dc[$paper_key];		//할인율	
	//print_r($result);	
	if ($dc_ratio > 0) $result = $paper_price - round($paper_price * ($dc_ratio / 100), 0);
	return $result;
}


//사용하지 않음. calcuPaperOpset() 함수를 공통적으로 사용함. 			20180730	chunter
function calcuBookPaperOpset($size_key, $paper_key, $paper_gram, $paper_yuen)
{
	global $r_ipro_standard_opset, $r_ipro_paper, $r_ipro_lose_opset, $r_ipro_paper_dc;
	$result = 0;
	//기준규격에 대해서 찾기	
	$standard_b2 = $r_ipro_standard_opset[$size_key]['B2'];
	$standard_a1 = $r_ipro_standard_opset[$size_key]['A1'];
		
	$dc_ratio = $r_ipro_paper_dc[$paper_key];		//할인율
	//기준 용지 가격 구하기
	if ($standard_b2 > 0)
		$paper_price = $r_ipro_paper[$paper_key]['paper'][$paper_gram]['OB2'];
	if ($standard_a1 > 0)
		$paper_price = $r_ipro_paper[$paper_key]['paper'][$paper_gram]['OA1'];
		
	//대당 로스율 (단면, 양면, 컬러, 흑백  구분한다.)
	$print_type[] = substr($print_key, 0, 1);
	$print_type[] = substr($print_key, 1, 1);
	if ($print_key)	
		$lose_cnt = $r_ipro_lose_opset[$print_type[0]][$print_type[1]];
	$lose_cnt = $lose_cnt * $opset_dea;
	
		
	$result = ceil($paper_price * $paper_yuen);
		
	if ($dc_ratio > 0) $result = $result - round($result * ($dc_ratio / 100), 0);
	return $result;
}




//옵셋 인쇄비 구하기. 
//별색도 같이 처리 한다.
function calcuPrintOpset($size_key, $opset_yuen, $print_key)
{
	global $r_ipro_print_opset;
	$result = 0;
	//print_r($r_ipro_print_digital[$size_key]);
	//$paper_cnt = getOptionStandardPaperCnt($size_key, $cnt);
	//print_r($r_ipro_print_opset[$size_key]);
	
	if (substr($print_key, 0, 1) == "D")
		$side_flag = "2";
	else 
		$side_flag = "1";
	
	
	if (is_array($r_ipro_print_opset[$size_key]))
	{
		$calcuData = getCalcuPriceData($r_ipro_print_opset[$size_key], $opset_yuen);
		
		//print_r($calcuData);
		if (is_array($calcuData))
		{
			if ($print_key == "OC1" || $print_key == "OC5" || $print_key == "DC6" || $print_key == "DC10")			//컬러	(도당 단가라서 * 4)
				$result = $calcuData['OC1'] * $side_flag * 4;
			
			else if ($print_key == "OB2" || $print_key == "DB7" || $print_key == "OB4" || $print_key == "DB9")	//인디고 흑백
				$result = $calcuData['OB2'] * $side_flag;
			
			else if ($print_key == "OB3" || $print_key == "DB8")																								//누베라 흑백				
				$result = $calcuData['OB3'] * $side_flag;
			
			else $result = $calcuData[$print_key] * $side_flag;
		}
	}	
	$result = $result * $opset_yuen;		//연당 단가..
		
	return $result;	
}

//별색 계산.
function calcuPrintETCOpset($size_key, $opset_yuen, $print_key, $opt_print_key)
{
	global $r_ipro_print_opset;
	$result = 0;
	//print_r($r_ipro_print_opset);
	//단면, 양면 구하기
	if (substr($opt_print_key, 0, 1) == "D")
		$side_flag = "2";
	else 
		$side_flag = "1";
			
	if (is_array($r_ipro_print_opset[$size_key]))
	{
		$calcuData = getCalcuPriceData($r_ipro_print_opset[$size_key], $opset_yuen);			
		if (is_array($calcuData))
			$result = $calcuData[$print_key] * $side_flag;		
	}
		
	return $result;	
}


//CTP 판수 구하기 			//$print_DEA -> 대 수량
function calcuCTPOpset($print_key, $print_DEA, $etc_color_cnt)
{
	global $r_ipro_ctp_opset;
	$result = 0;
		
	//판수 구하기	 (단면, 양면, 컬러, 흑백  구분한다.)
	$print_type1 = substr($print_key, 0, 1);
	$print_type2 = substr($print_key, 1, 1);
	
	if ($print_type1 == "O")		//단면
		$opset_pan_ea = 1;
	if ($print_type1 == "D")		//양면
		$opset_pan_ea = 2;
	
	if ($print_type2 == "C")		//칼러
		$opset_pan_ea *= 4;
	
	//별색 추가인 경우  색상 수량만큼 더한다.
	if ($etc_color_cnt  > 0)
		$opset_pan_ea += $etc_color_cnt;
	
	$opset_pan = $opset_pan_ea * $print_DEA;
	$result = $r_ipro_ctp_opset[1][val] * $opset_pan;
	
	return $result;
}


//코팅 가격구하기.
function calcuGlossOpset($size_key, $opset_yuen, $gloss_key)
{
	global $r_ipro_gloss_opset;
	
	$result = 0;
	$calcuData = getCalcuPriceData($r_ipro_gloss_opset[$size_key], $opset_yuen);
	if (is_array($calcuData)){
		$result = $calcuData[$gloss_key];
	}
	$result = $result * $opset_yuen;			//연당 단가
	return $result;
}

function calcuPunchOpset($size_key, $opset_yuen, $punch_key)
{
	global $r_ipro_punch_opset;
	
	$result = 0;
	$calcuData = getCalcuPriceData($r_ipro_punch_opset[$size_key], $opset_yuen);
	if (is_array($calcuData)){
		$result = $calcuData[$punch_key];
	}
	$result = $result * $opset_yuen;			//연당 단가
	return $result;
}


function calcuOshiOpset($size_key, $opset_yuen, $oshi_key)
{
	global $r_ipro_oshi_opset;
	
	$result = 0;
	$calcuData = getCalcuPriceData($r_ipro_oshi_opset[$size_key], $opset_yuen);
	if (is_array($calcuData)){
		$result = $calcuData[$oshi_key];
	}
	$result = $result * $opset_yuen;			//연당 단가
	return $result;
}


function calcuSCOpset($size_key, $opset_yuen, $sc_key)
{
	global $r_ipro_sc_opset;
	
	$result = 0;
	$calcuData = getCalcuPriceData($r_ipro_sc_opset[$size_key], $opset_yuen);
	if (is_array($calcuData)){
		$result = $calcuData[$sc_key];
	}
	$result = $result * $opset_yuen;			//연당 단가	
	return $result;
}


//scb 은 checkbox로 넘어오므로 여러개 선택될수 있다.
function calcuSCBOpset($size_key, $opset_yuen, $scb_key)
{
	global $r_ipro_scb_opset;
	
	$result = 0;
	//print_r($r_ipro_gloss_digital[$size_key]);
	if (is_array($r_ipro_sc_opset[$size_key]))
	{		
		foreach ($scb_key as $scbKey) 
		{			
			$calcuData = getCalcuPriceData($r_ipro_sc_opset[$size_key], $opset_yuen);
			if (is_array($calcuData)){
				$scbPrice = $calcuData[$scbKey];
			}
			$scbPrice = $scbPrice * $opset_yuen;			//연당 단가
			$result += $scbPrice;
		}			
		
	}
	return $result;
}

//무선 제본 가격 계산.
function calcuBindBD1Opset($insideTotalPage, $cnt, $first_paper_gram, $bind_key)
{
	global $r_ipro_bind_BD1_opset, $r_ipro_bind_BD1_default, $r_ipro_bind_BD1_page_gram;
	
	$result = $r_ipro_bind_BD1_opset[$cnt][$bind_key] * $cnt;		//장수당 단가
	
	//기본 페이지를 초가할경우 초과 제본 가격을 계산한다.
	if ($insideTotalPage > $r_ipro_bind_BD1_default['page'])
	{
		if ($first_paper_gram <= 120)
			$first_paper_gram_price = $r_ipro_bind_BD1_page_gram['120g'];
		else if ($first_paper_gram >= 150)
			$first_paper_gram_price = $r_ipro_bind_BD1_page_gram['150g'];
		else
			$first_paper_gram_price = $r_ipro_bind_BD1_page_gram['140g'];
				
		$result += (ceil(($insideTotalPage - $r_ipro_bind_BD1_default['page'])  / 16 ) * 16) * $first_paper_gram_price * $cnt;
	}
		
		
	return $result;
}


//중철 제본 가격
function calcuBindBD3Opset($insideTotalPage, $cnt, $first_paper_gram, $insideTotalPageDea)
{
	global $r_ipro_bind_BD3_default, $r_ipro_bind_BD3_page_gram;
	
	$result = $r_ipro_bind_BD3_default['price'];		//기본가격
	
	//기본 가격 구조를 넘어갈경우 초과 가격 계산한다.
	if ($insideTotalPage> $r_ipro_bind_BD3_default['page'] || $cnt > $r_ipro_bind_BD3_default['cnt'])
	{
		if ($first_paper_gram <= 120)
			$first_paper_gram_price = $r_ipro_bind_BD3_page_gram['120g'];
		else if ($first_paper_gram >= 150)
			$first_paper_gram_price = $r_ipro_bind_BD3_page_gram['150g'];
		else
			$first_paper_gram_price = $r_ipro_bind_BD3_page_gram['140g'];
		
		$result += $first_paper_gram_price * $insideTotalPageDea * $cnt;
	}
	
	return $result;	
}



function calcuCuttingOpset($size_key, $opset_yuen, $sc_key)
{
	global $r_ipro_cutting_opset;
	
	$result = 0;
	$calcuData = getCalcuPriceData($r_ipro_cutting_opset[$size_key], $opset_yuen);
	if (is_array($calcuData)){
		$result = $calcuData[$sc_key];
	}
		
	return $result;
}


//박 계산
//CTP 가격 + 동판 가격 + 작업비용(연당 단가)
function calcuFoilOpset($opset_yuen, $opset_mm2, $foil_key)
{
	global $r_ipro_foil_opset, $r_ipro_foil_mm2_opset, $r_ipro_ctp_opset;
	
	$result = $r_ipro_ctp_opset[1][val];			//기본 CTP 판비	
	$result += $r_ipro_foil_mm2_opset[$foil_key][1] * $opset_mm2;			//동판 가격

	$calcuData = getCalcuPriceData($r_ipro_foil_opset, $opset_yuen);
	if (is_array($calcuData)){
		$result += ($calcuData[$foil_key] * $opset_yuen);
	}
	return $result;
}

//형압 
//CTP 가격 + 동판 가격 + 작업비용(연당 단가)
function calcuPressOpset($opset_yuen, $opset_mm2, $press_key)
{
	global $r_ipro_press_opset, $r_ipro_press_mm2_opset, $r_ipro_ctp_opset;
//print_r($r_ipro_ctp_opset);
//print_r($r_ipro_press_mm2_opset);
	$result = $r_ipro_ctp_opset[1][val];			//기본 CTP 판비
	$result += $r_ipro_press_mm2_opset[$press_key][1] * $opset_mm2;			//동판 가격
	
	//작업비용 구하기.
	$calcuData = getCalcuPriceData($r_ipro_press_opset, $opset_yuen);
	if (is_array($calcuData)){
		$result += ($calcuData[$press_key] * $opset_yuen);
	}
		
	return $result;
}

//부분UV
//CTP 가격 + 동판 가격 + 작업비용(연당 단가)
function calcuUVCOpset($opset_yuen, $opset_mm2, $uvc_key)
{
	global $r_ipro_uvc_opset, $r_ipro_uvc_mm2_opset, $r_ipro_ctp_opset;
		
	$result = $r_ipro_ctp_opset[1][val];			//기본 CTP 판비
	$result += $r_ipro_uvc_mm2_opset[$uvc_key][1] * $opset_mm2;			//동판 가격
	
	//작업비용 구하기.
	$calcuData = getCalcuPriceData($r_ipro_uvc_opset, $opset_yuen);
	if (is_array($calcuData)){
		$result += ($calcuData[$uvc_key] * $opset_yuen);
	}
		
	return $result;
}

//도무송
//동판 가격 + 작업비용(연당 단가)
function calcuDomooOpset($opset_yuen, $opset_mm2, $domoo_key)
{
	global $r_ipro_domoo_opset, $r_ipro_domoo_mm2_opset;
	
	$result = $r_ipro_domoo_mm2_opset[$domoo_key][1] * $opset_mm2;			//동판 가격	
	//작업비용 구하기.
	$calcuData = getCalcuPriceData($r_ipro_domoo_opset, $opset_yuen);
	if (is_array($calcuData)){
		$result += ($calcuData[$domoo_key] * $opset_yuen);
	}
		
	return $result;
}


//접지
function calcuHoldingOpset($size_key, $opset_yuen, $sc_key)
{
	global $r_ipro_holding_opset;
	
	$result = 0;
	$calcuData = getCalcuPriceData($r_ipro_holding_opset[$size_key], $opset_yuen);
	if (is_array($calcuData)){
		$result = $calcuData[$sc_key];
	}
		
	return $result;
}



?>