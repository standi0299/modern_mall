<?
/*
* @date : 20180704
* @author : chunter
* @brief : 인쇄비 계산을 장단, 페이지당 단가 계산으로 변경함. 기본인쇄, 별색 인쇄 모두 변경.
* @desc : calcuPrintETCDigitalBookInside()	함수 사용하지 않음. 
 * 현재 책자 계산시 calcuPrintETCDigital() 함수를 사용하도록 하고 있다.		20180704		chunter
*/
?>
<? 

//용지 계산
function calcuPaperDigital($size_key, $cnt, $paper_key, $paper_gram)
{
	global $r_ipro_standard_digital, $r_ipro_paper;
	$result = 0;
	
	//기준규격에 대해서 찾기	
	$standard_b2 = $r_ipro_standard_digital[$size_key]['B2'];
	$standard_a3 = $r_ipro_standard_digital[$size_key]['A3'];
	
	$price_b2 = $r_ipro_paper[$paper_key]['paper'][$paper_gram]['B2'];
	$price_a3 = $r_ipro_paper[$paper_key]['paper'][$paper_gram]['A3'];
	
	if (strtolower($price_b2) == "null") $price_b2 = "";
	if (strtolower($price_a3) == "null") $price_a3 = "";
	
	//가격이 0으로 셋팅해도 기준규격으로 생각하고 계산하다.
	if ($price_b2 || $price_b2 === "0")
	{
		$paper_cnt = ceil($cnt / $standard_b2);
		$result = $price_b2 * $paper_cnt;
	} else if ($price_a3) {
		$paper_cnt = ceil($cnt / $standard_a3);
		$result = $price_a3 * $paper_cnt;
	}
		
	return $result;
}


//책자 종이값 구하기.
function calcuBookPaperDigital($size_key, $page_cnt, $cnt, $paper_key, $paper_gram, $print_key)
{
	global $r_ipro_standard_size, $r_ipro_standard_digital, $r_ipro_paper, $r_ipro_paper_dc;
	$result = 0;
	//기준규격에 대해서 찾기.
	$standard_b2 = $r_ipro_standard_digital[$size_key]['B2'];
	$standard_a3 = $r_ipro_standard_digital[$size_key]['A3'];
		
	$dc_ratio = $r_ipro_paper_dc[$paper_key];
	$price_b2 = $r_ipro_paper[$paper_key]['paper'][$paper_gram]['B2'];
	$price_a3 = $r_ipro_paper[$paper_key]['paper'][$paper_gram]['A3'];
	
	//print_r($r_ipro_paper[$paper_key]['paper'][$paper_gram]);
	//print_r($r_ipro_paper);
	
	//양면인 경우 페이지수를 2로 나눈다.	
	if (substr($print_key, 0, 1) == "D")
		$page_cnt = ceil($page_cnt / 2);
	
	if (strtolower($price_b2) == "null") $price_b2 = "";
	if (strtolower($price_a3) == "null") $price_a3 = "";
	
	//가격이 0으로 셋팅해도 기준규격으로 생각하고 계산하다.	
	if ($price_b2 || $price_b2 === "0")
	{
		if ($price_b2 === "0")
			$result = 0;
		else {
			$paper_cnt = ceil($page_cnt / $standard_b2);
			$result = $price_b2 * ($paper_cnt * $cnt);			//실제출력크기 종이 수량 * 부수
		}
	} else if ($price_a3) {
		if ($price_a3 === "0")
			$result = 0;
		else {
			$paper_cnt = ceil($page_cnt / $standard_a3);
			$result = $price_a3 * ($paper_cnt * $cnt);
		}
	}
	//echo "$price_b2\r\n";
	//echo "$standard_b2\r\n";
	//echo "$page_cnt\r\n";
	//echo "$dc_ratio\r\n";
	//echo "$result\r\n";
	//print_r($dc_ratio);
	//print_r($result);
	//if ($dc_ratio > 0) $result = $result - round($result * ($dc_ratio / 100), 0);
	return $result;
}



function calcuPrintDigital($size_key, $cnt, $print_key)
{
	global $r_ipro_print_digital;
	$result = 0;
	//print_r($r_ipro_print_digital[$size_key]);
		
	//가격 계산을 맞는 사이즈를 찾는다. 하지만 사용하지 않는다. 전체규격 테이블을 사용 하기로 결정		20180608		chunter
	$calcuStandartOptSize = getOptionCalcuStandardSize($size_key);
	
	//단면, 양면 구하기
	if (substr($print_key, 0, 1) == "D")
		$side_flag = "2";
	else 
		$side_flag = "1";
	
	if (is_array($r_ipro_print_digital[$size_key]))
	//if (is_array($r_ipro_print_digital[$calcuStandartOptSize['size']]))
	{		
		$calcuData = getCalcuPriceData($r_ipro_print_digital[$size_key], $cnt);
		//$calcuData = getCalcuPriceData($r_ipro_print_digital[$calcuStandartOptSize['size']], $cnt);		
						
		if (is_array($calcuData))
		{			
			if ($print_key == "OC5" || $print_key == "DC6" || $print_key == "DC10")			//컬러
				$result = $calcuData['OC1'] * $side_flag;
			else if ($print_key == "DB7" || $print_key == "OB4" || $print_key == "DB9")	//인디고 흑백
				$result = $calcuData['OB2'] * $side_flag;
			else if ($print_key == "DB8")																								//누베라 흑백
				$result = $calcuData['OB3'] * $side_flag;
			else $result = $calcuData[$print_key] * $side_flag;
			
			//$result = round($result / $calcuStandartOptSize['cnt'], 0);			//가격 계산을 맞는 사이즈를 찾아서 조판수로 나눈다.
		}
		$result = $result * $cnt;				//인쇄비 관리를 장당 단가로 변경한다.				20180704		chunter
	}
	
	return $result;	
}


//디지털 인쇄 할인율 구하기. 전체금액에서 할인을 구하는 방법 변경으로 할인율 계산 함수를 별도로 만듬.		20180720		chunter
function calcuPrintDCDigital($price, $size_key, $print_key)
{
	global $r_ipro_print_dc_digital;
	$result = $price;
		
	//할인율 계산
	if (is_array($r_ipro_print_dc_digital[$size_key]))
	{
		$dc_ratio = $r_ipro_print_dc_digital[$size_key][$print_key];
		//echo $result;
		if ($dc_ratio > 0) $result = $price - round($price * ($dc_ratio / 100), 0);
	}
	
	return $result;
	
}



//별색 계산 공식 (인쇄가격 계산과 달리  양단면 설정값을 받아야한다. 인쇄출력 코드를 추가한다.)
function calcuPrintETCDigital($size_key, $cnt, $print_key, $opt_print_key)
{
	global $r_ipro_print_digital;
	$result = 0;
	
	//가격 계산을 맞는 사이즈를 찾는다. 하지만 사용하지 않는다. 전체규격 테이블을 사용 하기로 결정		20180608		chunter
	$calcuStandartOptSize = getOptionCalcuStandardSize($size_key);
	
	//단면, 양면 구하기
	if (substr($opt_print_key, 0, 1) == "D")
		$side_flag = "2";
	else 
		$side_flag = "1";
			
	if (is_array($r_ipro_print_digital[$size_key]))
	//if (is_array($r_ipro_print_digital[$calcuStandartOptSize['size']]))
	{
		$calcuData = getCalcuPriceData($r_ipro_print_digital[$size_key], $cnt);
		//$calcuData = getCalcuPriceData($r_ipro_print_digital[$calcuStandartOptSize['size']], $cnt);						
		if (is_array($calcuData))
		{
			$result = $calcuData[$print_key] * $side_flag;		
			//$result = round($result / $calcuStandartOptSize['cnt'], 0);			//가격 계산을 맞는 사이즈를 찾아서 조판수로 나눈다.
		}
	}	
	$result = $result * $cnt;				//장당 단가로 계산 변견			20180704		chunter

	return $result;	
}



//책자 인쇄 금액 구하기.		책자 내지는 페이지당 계산.
function calcuPrintDigitalBookInside($size_key, $cnt, $print_key)
{
	global $r_ipro_print_book_inside_digital;
	$result = 0;
	//print_r($r_ipro_print_digital[$size_key]);		
		
	//가격 계산을 맞는 사이즈를 찾는다. 하지만 사용하지 않는다. 전체규격 테이블을 사용 하기로 결정		20180608		chunter
	//$calcuStandartOptSize = getOptionCalcuStandardSize($size_key);
	
	if (substr($print_key, 0, 1) == "D")
		$side_flag = "2";
	else 
		$side_flag = "1";	
	
	//책자 내지는 페이지당 인쇄단가이기 때문에 양면, 양/단면 구분하지 않는다.			20180704		chunter (김기웅 이사에게 확인했음.)
	$side_flag = "1";
		
	if (is_array($r_ipro_print_book_inside_digital[$size_key]))
	//if (is_array($r_ipro_print_book_inside_digital[$calcuStandartOptSize['size']]))
	{
		$calcuData = getCalcuPriceData($r_ipro_print_book_inside_digital[$size_key], $cnt);				
		//$calcuData = getCalcuPriceData($r_ipro_print_book_inside_digital[$calcuStandartOptSize['size']], $total_paper_cnt);
		//print_r($calcuData);
		if (is_array($calcuData))
		{
			if ($print_key == "OC5" || $print_key == "DC6" || $print_key == "DC10")			//컬러
				$result = $calcuData['OC1'] * $side_flag;
			else if ($print_key == "DB7" || $print_key == "OB4" || $print_key == "DB9")	//인디고 흑백
				$result = $calcuData['OB2'] * $side_flag;
			else if ($print_key == "DB8")																								//누베라 흑백
				$result = $calcuData['OB3'] * $side_flag;
			else $result = $calcuData[$print_key] * $side_flag;
			
			/*
			if ($print_key == "I2") $result = $calcuData['I1'] * $side_flag * 2;				//인디고 양면 2도는 인디고 1도 가격 * 2			
			else if ($print_key == "C8") $result = $calcuData['C4'] * $side_flag * 2;						//칼러8도는 가격설정이 없으므로 컬러4도 가격을 곱한다.
			else $result = $calcuData[$print_key] * $side_flag;
			*/			
			//$result = round($result / $calcuStandartOptSize['cnt'], 0);			//가격 계산을 맞는 사이즈를 찾아서 조판수로 나눈다.
		}
		//echo "$total_paper_cnt.<BR>";
		$result = $result * $cnt;			//페이지당 단가		20180704		chunter
	}
	
	return $result;
}

//책자 내지는 페이지당 단가로 계산한다. 양,단면 구분이 없다. 
//추후 사용할수도 있어 코드는 지우지 않음. 현재 사용하지 않음.
//현재 책자 계산시 calcuPrintETCDigital() 함수를 사용하도록 하고 있다.		20180704		chunter
//지류 배열을 $r_ipro_print_book_inside_digital 로 사용해야 하므로 이 함수 사용함			20180724	chunter
function calcuPrintETCDigitalBookInside($size_key, $cnt, $print_key)
{
	global $r_ipro_print_book_inside_digital;
	$result = 0;
				
	if (is_array($r_ipro_print_digital[$size_key]))	
	{
		$calcuData = getCalcuPriceData($r_ipro_print_book_inside_digital[$size_key], $cnt);
		if (is_array($calcuData))
		{
			$result = $calcuData[$print_key];
		}
	}	
	$result = $result * $cnt;		//페이지당 단가		20180704		chunter
		
	return $result;	
}


//책자 내지 인쇄비 할인 계산			20180724		chunter
function calcuPrintDCDigitalBookInside($print_price, $size_key, $print_key)
{
	global $r_ipro_print_book_inside_dc_digital;
	$result = $print_price;
		
	//할인율 계산
	if (is_array($r_ipro_print_book_inside_dc_digital[$size_key]))
	{
		$dc_ratio = $r_ipro_print_book_inside_dc_digital[$size_key][$print_key];
		//echo $result;
		if ($dc_ratio > 0) $result = $result - round($result * ($dc_ratio / 100), 0);
	}
	
	return $result;
}

function calcuGlossDigital($size_key, $cnt, $gloss_key)
{
	global $r_ipro_gloss_digital;
	
	$result = 0;
	$calcuData = getCalcuPriceData($r_ipro_gloss_digital[$size_key], $cnt);
	if (is_array($calcuData)){
		$result = $calcuData[$gloss_key];
	}
	
	return $result * $cnt;
}

//장당 단가로 변경			20180718		chunter
function calcuPunchDigital($size_key, $cnt, $punch_key)
{
	global $r_ipro_punch_digital;
		
	$result = 0;
	$calcuData = getCalcuPriceData($r_ipro_punch_digital[$size_key], $cnt);
	if (is_array($calcuData)){
		$result = $calcuData[$punch_key];
	}
	return $result * $cnt;
}


//단가 테이블 구조.		20180718		chunter
function calcuOshiDigital($size_key, $cnt, $oshi_key)
{
	global $r_ipro_oshi_digital;
	$result = 0;
	$calcuData = getCalcuPriceData($r_ipro_oshi_digital[$size_key], $cnt);
	if (is_array($calcuData)){
		$result = $calcuData[$oshi_key];
	}		
	return $result * $cnt;
}


//단가 테이블 구조			20180718		chunter
function calcuSCDigital($size_key, $cnt, $sc_key)
{
	global $r_ipro_sc_digital;	
	
	$result = 0;
	$calcuData = getCalcuPriceData($r_ipro_sc_digital[$size_key], $cnt);
	if (is_array($calcuData)){
		$result = $calcuData[$sc_key];
	}
	
	return $result * $cnt;
}


//책표지 스코딕스 박 옵션은 array로 넘어온다.		사용자 화면에서 checkbox로 옵션을 선택한다.
//단가 테이블 구조			20180718		chunter
function calcuSCBDigital($size_key, $cnt, $scb_key)
{
	global $r_ipro_scb_digital;	
	
	$result = 0;
	
	if(is_array($scb_key))
		$scb_key_arr = $scb_key;
	else	 	
		$scb_key_arr[] = $scb_key;		
	
	$calcuData = getCalcuPriceData($r_ipro_scb_digital[$size_key], $cnt);
	foreach ($scb_key_arr as $Fvalue) {		
		if (is_array($calcuData)){
			$result += $calcuData[$Fvalue];
		}		
	}
	
	return $result * $cnt;
}


function calcuBindDigital($size_key, $cnt, $bind_key)
{
	global $r_ipro_bind_digital;	
	
	$result = 0;
	$calcuData = getCalcuPriceData($r_ipro_bind_digital[$size_key], $cnt);
	//debug($calcuData);
	if (is_array($calcuData)){
		$result = $calcuData[$bind_key];
	}
		
	return $result * $cnt;
}


function calcuWingDigital($size_key, $cnt, $wing_key)
{
	global $r_ipro_wing_digital;	
	$result = 0;
	$calcuData = getCalcuPriceData($r_ipro_wing_digital[$size_key], $cnt);
	if (is_array($calcuData)){
		$result = $calcuData[$wing_key];
	}
	
	return $result * $cnt;
}


function calcuDomooDigital($size_key, $cnt, $domoo_key)
{
	global $r_ipro_domoo_digital;	
	$result = 0;
	$calcuData = getCalcuPriceData($r_ipro_domoo_digital[$size_key], $cnt);
	if (is_array($calcuData)){
		$result = $calcuData[$domoo_key];
	}
	
	return $result * $cnt;
}

function calcuCutDigital($size_key, $cnt, $cut_key)
{
	global $r_ipro_cut_digital;	
	$result = 0;
	$calcuData = getCalcuPriceData($r_ipro_cut_digital[$size_key], $cnt);
	if (is_array($calcuData)){
		$result = $calcuData[$cut_key];
	}
	
	return $result * $cnt;
}


function calcuBarcodeDigital($size_key, $cnt, $barcode_key)
{
	global $r_ipro_barcode_digital;	
	$result = 0;
	$calcuData = getCalcuPriceData($r_ipro_barcode_digital[$size_key], $cnt);
	if (is_array($calcuData)){
		$result = $calcuData[$barcode_key];
	}
	
	return $result * $cnt;
}

function calcuNumberDigital($size_key, $cnt, $number_key)
{
	global $r_ipro_number_digital;	
	$result = 0;
	$calcuData = getCalcuPriceData($r_ipro_number_digital[$size_key], $cnt);
	if (is_array($calcuData)){
		$result = $calcuData[$number_key];
	}
	
	return $result * $cnt;
}



//옵션가격 테이블에서 옵션키에 해당하는 가격정보 가져오기. 
function getCalcuPriceData($optPriceData, $cnt)
{
	//$result = 0;
	//print_r($optPriceData);
	if (is_array($optPriceData))
	{
		$oldKey = 100000;
		foreach ($optPriceData as $key => $value) {
			if ($key >= $cnt && $oldKey > $key)
			{ 
				$calcuData = $value;
				
				if ($oldKey > $key)
					$oldKey = $key;
			} 
		}
					
		//해당 구간의 가격을 찾지 못했으면 가장 큰값의 가격표에서 가져온다.
		if (!$calcuData)
		{
			$cntMaxKey = 0;		
			foreach ($optPriceData as $key => $value) {
				if ($cntMaxKey <= $key) $cntMaxKey = $key;						
			}
		
			if ($cntMaxKey <= $cnt)	
				$calcuData = $optPriceData[$cntMaxKey];			
		}
	}
	
	return $calcuData;
}

//상품에 사용된 규격중에 가장 맞는 규격 찾기.
function getOptionSizeWithCalcuPriceData($optPriceData, $width, $height)
{
	global $r_ipro_print_code;
	$result = "";

	if (is_array($optPriceData))
	{
		$minSizeTemp = 10000000000;
		
		foreach ($optPriceData as $key => $value) 
		{
			$checkProcFlag = false;
			$sizeArr = explode("*", $r_ipro_print_code[$key]);
            
			if (count($sizeArr) > 1)
			{				
				if ($sizeArr[0] >= $width && $sizeArr[1] >= $height)	$checkProcFlag = true;
				if ($sizeArr[1] >= $width && $sizeArr[0] >= $height)	$checkProcFlag = true;
			
				if ($checkProcFlag && ($minSizeTemp > ($sizeArr[0] * $sizeArr[1])) )
				{							
					$result = $key;
					$minSizeTemp = $sizeArr[0] * $sizeArr[1];			//width * height(면적) 이 가장 작은걸 사용하기 위해서 저장함. 면적이 작은게 가장 작은 사이즈라 생각함.
				}
			}
		}
	}
	
	return $result;
}


//책 표지 커버 구하기. 주문 사이즈 2배에 맞는 용지를 찾기 위해서.
//날개크기, 제본위치에 따른 크기 계산을 새로 만들어서 다시 개발함			20180711	chunter
//$inside_paper_width 내지 전체의 페이지 두께. 책등사이즈를 위해 규격구할때 추가해야함.
//내지가 A계열이면 표지는 A1, A2, A3, A4, A5 중 하나,     내지가 B계열이면 표지는 B2, B3, B4, B5 중 하나			20180727		chunter(김기웅 -확인 사항.)
// --> 바로 윗줄의 내용은 옵셋일경우만 해당됨			20180731		chunter 
function getBookCoverSize($size_key, $opt_bind_type, $outside_wing_width, $inside_paper_width = 0, $opset_flag = 'N')
{
	global $r_ipro_standard_size;
	
	//기준규격에 대해서 찾기.		
	$size_x = $r_ipro_standard_size[$size_key][size_x];
	$size_y = $r_ipro_standard_size[$size_key][size_y];
		
	//주문규격의 첫자를 찾는다.
	$size_key_prefix = substr($size_key, 0, 1);
	//print_r($opt_bind_type,"\r\n");
	if ($opt_bind_type)
	{
		//$opt_bind_type -> BT1 가로, BT2 세로
		//가로 제본은 높이 *2
		if ($opt_bind_type == "BT1")	
			$size_y = ($size_y * 2) + $outside_wing_width + $inside_paper_width;
		//세로 제본은 넓이 *2
		else if ($opt_bind_type == "BT2")
			$size_x = ($size_x * 2) + $outside_wing_width + $inside_paper_width;
			
		$minSizeTemp = 10000000000;
		
		foreach ($r_ipro_standard_size as $key => $value) 
		{
			if ($opset_flag == "Y")
			{
				$checkProcFlag = false;
				//주문규격과 같은 계열의 종이로 규격을 찾도록 한다. (A는 A계열, B는 B계열)		옵셋의 경우만 처리
				if ($size_key_prefix == substr($key, 0, 1))				
					$checkProcFlag = true;
			} else $checkProcFlag = true;
			
			if ($checkProcFlag)
			{
				if (($minSizeTemp > ($value[size_x] * $value[size_y])) && ((($size_x <= $value[size_x]) && ($size_y <= $value[size_y])) || (($size_x <= $value[size_y]) && ($size_y <= $value[size_x]))))
				{							
					$result = $key;
					$minSizeTemp = $value[size_x] * $value[size_y];			//width * height(면적) 이 가장 작은걸 사용하기 위해서 저장함. 면적이 작은게 가장 작은 사이즈라 생각함.
				}
			}
		}
	} else 
		$result = $size_key;			//바인딩 type 이 없을경우 주문사이즈가 표지사이즈.
	
	return $result;
}


//주문 규격을 토대로 실제 종이 규격을 찾는다. 
//디지털 인쇄견적 인쇄비 / 후가공비 계산을 위한 기준 규격과 조판 수량을 배열로 넘겨줌.
function getOptionCalcuStandardSize($size_key)
{
	global $r_ipro_standard_print_digital;
	$result = array("size"=>$size_key, "cnt"=>"1");
	
	$standard_b2 = $r_ipro_standard_print_digital[$size_key]['B2'];
	$standard_a3 = $r_ipro_standard_print_digital[$size_key]['A3'];
		
	if ($standard_b2)
	{
		$result = array("size"=>"B2", "cnt"=>$standard_b2);						
	} else if ($standard_a3) {
		$result = array("size"=>"A3", "cnt"=>$standard_a3);				
	}	
	return $result;
}

?>