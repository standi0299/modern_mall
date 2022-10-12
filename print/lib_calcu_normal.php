<? 

//모든 옵션 계산
function calcuPaperNormal($size_key, $page_cnt, $paper_key, $paper_gram)
{
	global $r_iro_normal_product;
	$result = 0;
	$calcuData = getCalcuPriceData($r_iro_normal_product[$size_key], $page_cnt);
	if (is_array($calcuData)){
		$result = $calcuData[$paper_key][$paper_gram];
	}
		
	return $result;
}


function calcuPrintNormal($size_key, $page_cnt, $print_key)
{
	global $r_iro_normal_product;
	$result = 0;
	
	//단면, 양면 구하기
	if (substr($print_key, 0, 1) == "D")
		$side_flag = "2";
	else 
		$side_flag = "1";
	
	
	if (is_array($r_iro_normal_product[$size_key]))
	{
		$calcuData = getCalcuPriceData($r_iro_normal_product[$size_key], $page_cnt);		
		//debug($calcuData);				
		if (is_array($calcuData))
		{
			//양면, 단면, 흑백, 칼라  그대로 가격테이블 입력.				20180625		chunter			 	
			$result = $calcuData[$print_key];
							
			/*				
			if ($print_key == "OC5" || $print_key == "DC6" || $print_key == "DC10")			//컬러
				$result = $calcuData['OC1'] * $side_flag;
			else if ($print_key == "DB7" || $print_key == "OB4" || $print_key == "DB9")	//인디고 흑백
				$result = $calcuData['OB2'] * $side_flag;
			else if ($print_key == "DB8")																								//누베라 흑백
				$result = $calcuData['OB3'] * $side_flag;
			else $result = $calcuData[$print_key] * $side_flag;
			*/
		}
	}
		
	return $result;	
}


//별색 계산 공식 . 인쇄출력 코드를  받는다. 양단면 결정위해 
function calcuPrintETCNormal($size_key, $cnt, $print_key, $opt_print_key)
{
	global $r_iro_normal_product;
	$result = 0;
	
	//단면, 양면 구하기
	if (substr($opt_print_key, 0, 1) == "D")
		$side_flag = "2";
	else 
		$side_flag = "1";
	//print_r($r_iro_normal_product);		
	if (is_array($r_iro_normal_product[$size_key]))
	{
		$calcuData = getCalcuPriceData($r_iro_normal_product[$size_key], $cnt);						
		if (is_array($calcuData))
			$result = $calcuData[$print_key] * $side_flag;		
		//$result = $result * $cnt;
	}
	return $result;	
}


//scb 는 다중 옵션이라 배열 처리한다.
function calcuSCBNormal($size_key, $page_cnt, $opt_key)
{
	global $r_iro_normal_product;	
	$result = 0;
	
	if(is_array($opt_key))
		$scb_key_arr = $opt_key;
	else	 	
		$scb_key_arr[] = $opt_key;
	
	$calcuData = getCalcuPriceData($r_iro_normal_product[$size_key], $page_cnt);
	if (is_array($calcuData))
	{		
		foreach ($scb_key_arr as $optvalue) {
			$result += $calcuData[$optvalue];	
		}
	}		
	return $result;
}



function calcuCommonOptionNormal($size_key, $page_cnt, $opt_key)
{
	global $r_iro_normal_product;	
	$result = 0;
	$calcuData = getCalcuPriceData($r_iro_normal_product[$size_key], $page_cnt);
	if (is_array($calcuData)){
		$result = $calcuData[$opt_key];
	}
		
	return $result;
}

?>