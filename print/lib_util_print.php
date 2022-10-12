<?
/*
* @date : 20190404
* @author : kdk
* @brief : 용지비,인쇄비,코팅비 1mm당 단가 조회.
* @desc :
*/
 
/*
* @date : 20180628
* @author : kdk
* @brief : 옵셋상품 별 수량을 설정. => getPrintPage().
* @desc :
*/

//용지비,인쇄비,코팅비 1mm단가 조회.
function getPr1mmPrice($paper_key, $price_key, $paper_gram = "0") {
    global $r_ipro_paper_pr_price_1mm;
    $result = 0;
    
    $width = 1000;
    $height = 1000;
    
    if ($r_ipro_paper_pr_price_1mm) {
        //debug($r_ipro_paper_pr_price_1mm);
        $result = $r_ipro_paper_pr_price_1mm[$paper_key][paper][$paper_gram][$price_key];
        //debug($result);
    }

    return $result;
}

//지류에서 주문 가능한 규격인지를 확인한다.
//20180718			chunter 
function checkPaperInOrderSize($size_key, $paper_key, $paper_gram)
{
	global $r_ipro_standard_digital, $r_ipro_paper;
	$result = false;
	
	//기준규격에 대해서 찾기	
	$standard_b2 = $r_ipro_standard_digital[$size_key]['B2'];
	$standard_a3 = $r_ipro_standard_digital[$size_key]['A3'];
	
	$price_b2 = $r_ipro_paper[$paper_key]['paper'][$paper_gram]['B2'];
	$price_a3 = $r_ipro_paper[$paper_key]['paper'][$paper_gram]['A3'];
	
	if (strtolower($price_b2) == "null") $price_b2 = "";
	if (strtolower($price_a3) == "null") $price_a3 = "";
	
	//둘다 있을경우 아무상관없이 가격계산 가능.
	if ($standard_b2 && $standard_a3)
		$result = true;
	else if($standard_b2 && !$standard_a3)
	{
		//b2만 있을경우	지류의 b계열 가격이 있어야 사용가능
		if ($price_b2 || $price_b2 === "0")
			$result = true;		
	}
	else if(!$standard_b2 && $standard_a3)
	{
		//a3만 있을경우	지류의 a계열 가격이 있어야 사용가능
		if ($price_a3 || $price_a3 === "0")
			$result = true;
	}
		
	return $result;
}


//인쇄 코드 구하기, 양면/단면/흑백/칼라
function getPrintSideCode($opt_print1, $opt_print2)
{
	$opt_print_key = "";
	if ($opt_print1 && $opt_print2)
	{
		$opt_print_key = $opt_print2.$opt_print1;		
		if ($opt_print_key == "OC")	$opt_print_key .= "1";		//단면 컬러
		if ($opt_print_key == "OB")	$opt_print_key .= "2";		//단면 흑백
		if ($opt_print_key == "ON")	$opt_print_key = "OB3";		//누베라 단면 1도(흑백)
			
		if ($opt_print_key == "DC")	$opt_print_key .= "6";		//양면 컬러	
		if ($opt_print_key == "DB")	$opt_print_key .= "7";		//양면 흑백
		if ($opt_print_key == "DN")	$opt_print_key = "DB8";		//누베라 양면 칼러
	}
	
	return $opt_print_key;
}


//디지탈 인쇄에서 주문규격에 해단 인쇄 종이 규격을 찾는다.
function getPrintPaperKeyDigital($paper_key, $paper_gram)
{
	global $r_ipro_paper;
	$price_b2 = $r_ipro_paper[$paper_key]['paper'][$paper_gram]['B2'];
	$price_a3 = $r_ipro_paper[$paper_key]['paper'][$paper_gram]['A3'];
	
	if (strtolower($price_b2) == "null") $price_b2 = "";
	if (strtolower($price_a3) == "null") $price_a3 = "";
	
	//가격이 0으로 셋팅해도 기준규격으로 생각하고 계산하다.
	if ($price_b2 || $price_b2 === "0")
	{
		$result = "B2";
	} else if ($price_a3) {
		$result = "A3";
	}	
	return $result;
}


//종이 규격에 document가 몇개 들어갈수 있는지 계산한다.
function getFitCountInPaper($paper_key, $document_x, $document_y)
{
	global $r_ipro_standard_size;
	$result = 0;
	$remain = array("x"=>0,"y"=>0);
	
	if (!$document_x || !$document_y)
		return 0;
	
	$paper_x = $r_ipro_standard_size[$paper_key][size_x];
	$paper_y = $r_ipro_standard_size[$paper_key][size_y];
	
	//echo "$paper_x <BR>";
	//echo "$paper_y <BR>";
	//echo "$document_x <BR>";
	//echo "$document_y <BR>";
	//가로축으로 체크해서 종이안에 들가가면 그냥 처리. 들어가지 않는다면 document 를 회전하여 처리
	if ($paper_x < $document_x)
	{	
		$temp = $document_x;
		$document_x = $document_y;
		$document_y = $temp;		
		//echo "document_y <BR>";
	}				
		
	
	if ($paper_x >= $document_x)
	{
		$remain[x] = $paper_x - $document_x;
		$remain[y] = $paper_y - $document_y;
		$result++;
		//print_r($remain);
		
		//echo "<BR> $result <BR>";
		while ($remain[y] > 0)
		{			
			//가로축으로  계산.
			while($remain[x] > 0)
			{
				$remain[x] = $remain[x] - $document_x;
				//echo "1-";
				//print_r($remain);
				$result++;	
				//echo "<BR> $result <BR>";				
			}
		
			//밑으로 남는 공간에 들어갈수 있는지 계산한다. 상자가 크다면  break, 남은영역이 크다면  x축에서 새로 시작.		
			if ($remain[y] < $document_y)
			{
				//echo "<BR> remain <BR>";
				
				//영역이 작다면 상자를 회전해서 들어갈수 있는지 계산. 들어갈수 있다면 회전처리.
				if ($remain[y] > $document_x && $paper_x > $document_y)
				{
					$temp = $document_x;
					$document_x = $document_y;
					$document_y = $temp;
					
					$remain[x] = $paper_x - $document_x;
					$remain[y] = $remain[y] - $document_y;
					//echo "2-";
					//print_r($remain);				
				} else 				
					break;
			} else {
				//Y축으로 새로 계산하기 때문에 $remain_x값을 초기화 해야 한다. 
				$remain[x] = $paper_x - $document_x;
				$remain[y] = (int)$remain[y] - (int)$document_y;
				//echo "3-";
				//print_r($remain);
			}
			
			//잘못된 계산이 나올수 있으므로 안전장치. 
			if ($result > 100) {
				$result = 1;
				break;
			}	
		}
	}		
	return $result;	
}


//비규격에 대해서 알맞는 종이 크기를 구한다.
function getPaperWithDocumentSize($document_x, $document_y)
{
	global $r_ipro_standard_size;
	$result = "";
	
	$old_paper_area = "";
	foreach ($r_ipro_standard_size as $key => $value) {
		//가로나 세로중 둘다 크기가 크다면  안맞는 사이즈.
		if ($document_x > $value[size_x] && $document_x > $value[size_y])
			continue;
		
		if ($document_y > $value[size_x] && $document_y > $value[size_y])
			continue;
		
		//면적으로 계산해서 가장 작은 사이즈를 찾는다.
		if ($old_paper_area == "" || ($old_paper_area > $value[size_x] * $value[size_y]))
		{		
			$result = $key;
		}
		$old_paper_area = $value[size_x] * $value[size_y];		
	}
	
	return $result;	
}


//비규격 주문의 스티커 규격 찾기
function getStickerPrintOptSizeWithCustom($document_x, $document_y)
{
	global $r_ipro_print_code;
	$result = "";	
	$old_paper_area = "";
	
	$diffSize = $document_y;
	if ($document_x > $document_y)
		$diffSize = $document_x;
		
	
	$cntMaxKey = 0;		
	foreach ($r_ipro_print_code as $key => $value) {
		$opt_prefix = substr($key, 0, 3);
		if ($opt_prefix == "SCS")
		{
			if ($cntMaxKey <= $value) $cntMaxKey = $value;
		}						
	}				
		
	$oldKey = 100000;
	foreach ($r_ipro_print_code as $key => $value) {
		$opt_prefix = substr($key, 0, 3);
		if ($opt_prefix == "SCS")
		{
			if ($value >= $diffSize && $oldKey > $value)
			{ 
				$result = $key;
				
				if ($oldKey > $value)
					$oldKey = $value;				
			}
		} 
	}
	
	
	//가장 근접한 규격을 찾지 못했으면 가장 큰값이  맞는 규격이다.
	if (!$result)
	{
		if ($cntMaxKey >= $diffSize)	
			$result = $cntMaxKey;			
	}
		
	
	//echo $result . "<Br>";
	return $result;	
}

//비규격 주문의 스티커 규격 찾기
function getStickerPrintOptSize($document_x, $document_y)
{
	global $r_ipro_print_code;
	$result = "";
	//print_r($r_ipro_print_code);
	$old_paper_area = "";
	foreach ($r_ipro_print_code as $key => $value) 
	{				
		$opt_prefix = substr($key, 0, 2);
		
		//스티커 규격만 검사한다.
		if ($opt_prefix == "SO" || $opt_prefix == "SE" || $opt_prefix == "SR")
		{
			$value_size = explode("*", $value); 
			
			//가로나 세로중 둘다 크기가 크다면  안맞는 사이즈.
			if ($document_x > $value_size[0] && $document_x > $value_size[1])
				continue;
			
			if ($document_y > $value_size[0] && $document_y > $value_size[1])
				continue;
			
			//면적으로 계산해서 가장 작은 사이즈를 찾는다.
			if ($old_paper_area == "" || ($old_paper_area > $value_size[0] * $value_size[1]))
			{		
				$result = $key;
			}
			$old_paper_area = $value_size[0] * $value_size[1];
		}		
	}
	//echo $result . "<Br>";
	return $result;	
}



function getOptionSize($goodsData)
{
	global $r_ipro_print_code, $r_ipro_standard_size;

	foreach ($goodsData[size] as $value) {
		if ($value == "USER")
			$result[$value] = "사이즈 직접입력";
		else
            $result[$value] = "{$r_ipro_print_code[$value]}";
			//$result[$value] = "{$r_ipro_print_code[$value]} ({$r_ipro_print_code[$value]})";
			//$result[$value] = "{$r_ipro_standard_size[$value][name]} ({$r_ipro_standard_size[$value][size_x]} x {$r_ipro_standard_size[$value][size_y]})";
	}

	return $result;
	//return json_decode($result);
}

//현수막,실사출력 규격 정보
function getOptionSizePr($goodsData)
{
    global $r_ipro_print_code, $r_ipro_pr_standard_size;

    foreach ($goodsData[size] as $value) {
        if ($value == "USER")
            $result[$value] = "사이즈 직접입력";
        else
            //$result[$value] = "{$r_ipro_print_code[$value]}";
            $result[$value] = "{$r_ipro_pr_standard_size[$value][name]} ({$r_ipro_pr_standard_size[$value][size_x]} x {$r_ipro_pr_standard_size[$value][size_y]})";
    }

    return $result;
    //return json_decode($result);
}

function getOptionPaperGroup($goodsData, $optKey = "paper")
{
	global $r_ipro_print_code, $r_ipro_paper;

	foreach ($goodsData[$optKey] as $key => $value) {
		if ($r_ipro_paper[$key][group])
			$result[$r_ipro_paper[$key][group]] = "{$r_ipro_paper[$key][group]}";
	}
	return $result;
	//return json_decode($result);
}


function getOptionPaper($goodsData, $group, $optKey = "paper")
{
	global $r_ipro_print_code, $r_ipro_paper;

	foreach ($goodsData[$optKey] as $key => $value) {
		if ($group == $r_ipro_paper[$key][group])
			$result[$key] = "{$r_ipro_paper[$key][name]}";
	}
	return $result;
	//return json_decode($result);
}

# 현수막 실사출력
function getOptionPaperPr($goodsData, $group = "", $optKey = "paper")
{
    global $r_ipro_print_code, $r_ipro_paper_pr;

    foreach ($goodsData[$optKey] as $key => $value) {
        if ($group) {
            if ($group == $r_ipro_paper_pr[$key][group])
                $result[$key] = "{$r_ipro_paper_pr[$key][name]}";
        } else {
            $result[$key] = "{$r_ipro_paper_pr[$key][name]}";
        }   
    }
    return $result;
    //return json_decode($result);
}

function getOptionPaperPr1mm($goodsData, $group = "", $optKey = "paper")
{
    global $r_ipro_print_code, $r_ipro_paper_pr_price_1mm;

    foreach ($goodsData[$optKey] as $key => $value) {
        if ($group) {
            if ($group == $r_ipro_paper_pr_price_1mm[$key][group])
                $result[$key] = "{$r_ipro_paper_pr_price_1mm[$key][name]}";
        } else {
            $result[$key] = "{$r_ipro_paper_pr_price_1mm[$key][name]}";
        }   
    }
    return $result;
    //return json_decode($result);
}

function getOptionPaperGram($goodsData, $paper_key, $optKey = "paper")
{
	global $r_ipro_print_code, $r_ipro_paper;

	if (is_array($r_ipro_paper[$paper_key]['paper']))
	{
		//상품에 설정한 gram 과 같은 항목만 나오게 한다.					
		foreach ($goodsData[$optKey][$paper_key] as $gvalue) 
		{
			foreach ($r_ipro_paper[$paper_key]['paper'] as $pkey => $pvalue) 
			{			
				if ($pkey = $gvalue)
				{
					$result[$pkey] = "{$pkey}g";
					break;
				}
			}	
		}		
	}
	
	return $result;
	//return json_decode($result);
}


function getOptionPrint($goodsData, $optKey = "print")
{
	global $r_ipro_print_code;

    if (!$goodsData[$optKey]) return "";

	//print_r($optKey);	
	foreach ($goodsData[$optKey] as $value) {
		$result[$value] = "{$r_ipro_print_code[$value]}";
	}
	return $result;
	//return json_decode($result);
}


function getOptionGloss($goodsData, $optKey = "gloss")
{
	global $r_ipro_print_code;

    if (!$goodsData[$optKey]) return "";
    
	foreach ($goodsData[$optKey] as $value) {
		$result[$value] = "{$r_ipro_print_code[$value]}";
	}
	return $result;
	//return json_decode($result);
}


function getOptionUV($goodsData, $optKey = "sc")
{
	global $r_ipro_print_code;

    if (!$goodsData[$optKey]) return "";

	foreach ($goodsData[$optKey] as $value) {
		$result[$value] = "{$r_ipro_print_code[$value]}";
	}
	return $result;
	//return json_decode($result);
}


function getOptionFoil($goodsData, $optKey = "scb")
{
	global $r_ipro_print_code;

    if (!$goodsData[$optKey]) return "";
    
	foreach ($goodsData[$optKey] as $value) {
		$result[$value] = "{$r_ipro_print_code[$value]}";
	}
	return $result;
	//return json_decode($result);
}


function getOptionPunch($goodsData)
{
	global $r_ipro_print_code;

    if (!$goodsData['punch']) return "";

	foreach ($goodsData['punch'] as $value) {
		$result[$value] = "{$r_ipro_print_code[$value]}";
	}
	return $result;
	//return json_decode($result);
}


function getOptionOshi($goodsData)
{
	global $r_ipro_print_code;

    if (!$goodsData['oshi']) return "";

	foreach ($goodsData['oshi'] as $value) {
		$result[$value] = "{$r_ipro_print_code[$value]}";
	}
	return $result;
	//return json_decode($result);
}


function getOptionBookWing($goodsData)
{
	global $r_ipro_print_code;

    if (!$goodsData['outside_wing']) return "";
        
	foreach ($goodsData['outside_wing'] as $value) {
		$result[$value] = "{$r_ipro_print_code[$value]}";
	}
	return $result;
	//return json_decode($result);
}


function getOptionBind($goodsData)
{
	global $r_ipro_print_code;

    if (!$goodsData['bind']) return "";
    
	foreach ($goodsData['bind'] as $value) {
		$result[$value] = "{$r_ipro_print_code[$value]}";
	}
	return $result;
	//return json_decode($result);
}

function getOptionBindType($goodsData)
{
	global $r_ipro_print_code;

    if (!$goodsData['bind_type']) return "";
    
	if (is_array($goodsData['bind_type']))
	{
		foreach ($goodsData['bind_type'] as $value) {
			$result[$value] = "{$r_ipro_print_code[$value]}";
		}
	}
	return $result;
	//return json_decode($result);
}


//옵셋 인쇄 판수 구하기.
function getOpsetPrintDEA($size_key, $cnt, $print_key)
{
	global $r_ipro_standard_opset;
	$result = 0;
	//기준규격에 대해서 찾기	
	$standard_b2 = $r_ipro_standard_opset[$size_key]['B2'];
	$standard_a1 = $r_ipro_standard_opset[$size_key]['A1'];
	
	//양면의 경우 페이지 / 2 처리.
	if (substr($print_key, 0, 1) == "D")
		$cnt = ceil($cnt / 2);
	
	//대 구하기
	if ($standard_b2 > 0)	
		$result = ceil($cnt / $standard_b2);
	else if ($standard_a1)
		$result = ceil($cnt / $standard_a1);

	return $result;
}

//옵셋 연 수량 구하기		// {A1-500, B2-1000}
function getOpsetPrintYuen($size_key, $cnt, $print_key, $opset_dea)
{
	global $r_ipro_standard_opset, $r_ipro_lose_opset;
	$result = 0;
	//기준규격에 대해서 찾기	
	$standard_b2 = $r_ipro_standard_opset[$size_key]['B2'];
	$standard_a1 = $r_ipro_standard_opset[$size_key]['A1'];
			
	//대당 로스율 (단면, 양면, 컬러, 흑백  구분한다.)	
	if ($print_key)
	{
		$print_type1 = substr($print_key, 0, 1);
		$print_type2 = substr($print_key, 1, 1);
		$lose_cnt = $r_ipro_lose_opset[$print_type1][$print_type2];
	}	
		
	$lose_cnt = $lose_cnt * $opset_dea;
	//print_r($lose_cnt."\r\n");	
	//종이수량 구하기
	if ($standard_b2 > 0)	
	{
		$paper_cnt = ceil($cnt / $standard_b2);
		$paper_cnt_yuen = ($paper_cnt + $lose_cnt) / 1000;			//연 수량 구하기 
	}
	else if ($standard_a1)
	{
		$paper_cnt = ceil($cnt / $standard_a1);
		//print_r($paper_cnt."\r\n");
		$paper_cnt_yuen = ($paper_cnt + $lose_cnt) / 500;			//연 수량 구하기
	}	
	
	$result = $paper_cnt_yuen;
	return $result;
}


function getOpsetBookPrintYuen($size_key, $page_cnt, $cnt, $print_key, $opset_dea)
{
	global $r_ipro_standard_opset, $r_ipro_lose_opset;
	$result = 0;
	//기준규격에 대해서 찾기	
	$standard_b2 = $r_ipro_standard_opset[$size_key]['B2'];
	$standard_a1 = $r_ipro_standard_opset[$size_key]['A1'];
		
	//대당 로스율 (단면, 양면, 컬러, 흑백  구분한다.)	
	if ($print_key)
	{
		$print_type1 = substr($print_key, 0, 1);
		$print_type2 = substr($print_key, 1, 1);	
		$lose_cnt = $r_ipro_lose_opset[$print_type1][$print_type2];
	}
	$lose_cnt = $lose_cnt * $opset_dea;
	//print_r($lose_cnt."\r\n");
	
	//양면은 페이지 / 2
	if (substr($print_key, 0, 1) == "D")
		$page_cnt = ceil($page_cnt / 2);
	//print_r($lose_cnt."\r\n");		
	//종이수량 구하기
	if ($standard_b2 > 0)	
	{
		$paper_cnt = ceil(($paper_cnt * $cnt) / $standard_b2);
		$paper_cnt_yuen = ($paper_cnt + $lose_cnt) / 1000;			//연 수량 구하기
	}		
	else if ($standard_a1)
	{
		$paper_cnt = ceil(($paper_cnt * $cnt) / $standard_a1);
		$paper_cnt_yuen = ($paper_cnt + $lose_cnt) / 500;			//연 수량 구하기
	}	
			
	$result = $paper_cnt_yuen;

	return $result;
}


//옵셋 인쇄 사용할 용지 규격 구하기.
function getOpsetPaperSize($size_key)
{
	global $r_ipro_standard_opset;
	
	//기준규격에 대해서 찾기	
	$standard_b2 = $r_ipro_standard_opset[$size_key]['B2'];
	$standard_a1 = $r_ipro_standard_opset[$size_key]['A1'];
	
	if ($standard_b2 > 0)
		return "B2";
	else if ($standard_a1 > 0)
		return "A1";
	else
		return "";
}


//앞면, 뒷면 print_key로 양단면, 칼라, 흑백 에 맞는 print_key 만들기
function makePrefixOpsetPrintKey($print_key_front, $print_key_back, $etc_color_front_cnt, $etc_color_back_cnt)
{
	//별색이 있으면 칼라
	if ($etc_color_front_cnt > 0)
		$result = "OC";			//별색 있으면  단면컬러
	else
		$result = "O".$print_key_front;			//별색 없으면 단면 인쇄색상으로 
	
	//뒷면이 있다면 양면
	if ($print_key_back)
	{
		$result = "D";			//뒷면이 있다면 일단 양면 print_key
		//별색이 하나라도 있으면 컬러.
		if ($etc_color_front_cnt > 0 || $etc_color_back_cnt > 0)
			$result .= "C";
		else {
			//앞뒷면중 하나라도 컬러면 컬러로 구분.
			if ($print_key_front == "C" || $print_key_back == "C")
				$result .= "C";
			else
				$result .= "B";
		}
	}
	
	return $result;
}

//사용자 화면 select 옵션 태그 만들기
function makeOptionSelectTag($optionData, $selData, $nothingText = "선택해주세요.")
{
	$result = "";
	//$result = "<select>";
	if ($nothingText)
		$result .= "<option value=''>$nothingText</option>";
	if (is_array($optionData))
	{
		foreach ($optionData as $key => $value) 
		{
			$selected = "";
			if ($key == $selData) $selected = " selected";
			$result .= "<option value='$key' $selected>$value</option>";
		}
	}
	//$result .= "</select>";
	return $result;
}


function makeOptionCheckTag($optionData, $selData, $tagName, $addStype = '')
{
	$result = "";
	//$result = "<select>";	
	if (is_array($optionData))
	{
		foreach ($optionData as $key => $value) 
		{
			$selected = "";
			if (is_array($selData))
			{
				foreach ($selData as $selkey => $selvalue) {
					if ($key == $selvalue) $selected = " checked";
				}
			} 
			else 
			{
				if ($key == $selData) $selected = " checked";
			}
			$result .= "<input type='checkbox' name='$tagName' value='$key' $selected $addStype onclick='calcuPrice();'>$value";
		}
	}
	//$result .= "</select>";
	return $result;
}


//사용자 화면 옵션 태그 만들기
function makeOptionInputTag($optionData, $optionKey, $selData, $tagId, $tagName, $nothingText = "없음", $inputType = "radio")
{
    $result = "";

    $arrImg = array(
        '없음' => "empty.png", // 
        '있음' => "matt.png", // 
               
        '무광단면' => "matt.png", //코팅
        '무광양면' => "matt.png", //코팅
        '유광단면' => "gloss.png", //코팅
        '유광양면' => "gloss.png", //코팅
        
        '스코딕스(부분UV)안함' => "empty.png", //스코딕스
        '스코딕스(부분UV)' => "matt.png", //스코딕스
        
        '유광' => "gloss.png", //스코딕스 박      
        '금박' => "sco_g.png", //스코딕스 박
        '은박' => "sco_s.png", //스코딕스 박
        '청박' => "sco_b.png", //스코딕스 박
        '적박' => "sco_r.png", //스코딕스 박
        '녹박' => "sco_g2.png", //스코딕스 박
        '먹박' => "sco_b2.png", //스코딕스 박
        '홀로그램박' => "sco_h.png", //스코딕스 박
       
        '2귀도리(우상좌하)' => "matt.png",//귀도리
        '2귀도리(좌상우하)' => "matt.png",//귀도리
        '4귀도리' => "matt.png",//귀도리
        
        '무선' => "matt.png", //제본
        '무선날개' => "matt.png", //제본
        '중철' => "matt.png", //제본
        '링제본' => "matt.png", //제본
        '스프링제본(PVC커버투명)' => "matt.png", //제본
        '스프링제본(반투명)' => "matt.png", //제본
        '제본안함' => "empty.png", //제본
        
        '가로' => "angle_h.png", //제본방향
        '세로' => "angle_v.png", //제본방향
        
        '날개없음' => "empty.png", //날개(책자) 
        '날개있음' => "matt.png", //날개(책자)
        
        '재단' => "matt.png", //재단
        '재단없음' => "empty.png", //재단
        
        '1줄' => "matt.png", //미싱,오시
        '2줄' => "matt.png", //미싱,오시
        '3줄' => "matt.png", //미싱,오시
        '4줄' => "matt.png", //미싱,오시
        
        '1구' => "matt.png", //타공
        '2구' => "matt.png", //타공
        '3구' => "matt.png", //타공
        '4구' => "matt.png", //타공
    );

    if ($nothingText)
        $result .= "<input type='$inputType' id='".$tagId."_0' name='$tagName' checked onclick='calcuPrice();'>
            <label for='".$tagId."_0'>
                <div class='btn_box'>
                    <img src='/skin/modern/assets/interpro/img/empty.png' alt='없음'>
                    <p>없음</p>
                </div>
            </label>";

    //첫번째  $key값 찾기.
    $arr_keys = array_keys($optionData);
    $first_key = $arr_keys[0];

    if (is_array($optionData))
    {
        $row = 1;
        foreach ($optionData as $key => $value) 
        {
            //없음 항목 제외.
            //모든 항목 표기하도록 수정. / 20180711 / kdk
            //if (strpos($value, "없음") !== FALSE) continue;
            
            $checked = "";
            //if ($key == $selData) $checked = " checked";
            if ($selData) {
                if ($key == $selData) $checked = " checked"; //선택값이 있으면...    
            }
            else {
                //if ($first_key == $key) $checked = " checked"; //선택값이 없으면 첫번째 항목을...
            }
        
            $id = $tagId."_".$row;

            $img = "matt.png";
            if ($arrImg[$value]) $img = $arrImg[$value];

            $result .= "<input type='$inputType' id='$id' name='$tagName' value='$key' $checked onclick='calcuPrice();'>
            <label for='$id'>
                <div class='btn_box'>
                    <img src='/skin/modern/assets/interpro/img/".$img."' alt='$value'>
                    <p>$value</p>
                </div>
            </label>";
            
            $row++;
        }
    }
    //$result .= "</select>";
    return $result;
}

//사용자 화면 인쇄컬러 옵션 태그 만들기
function makeOptionInputTag_Print1($optionData, $chkData, $TchkData, $inputName = "opt", $idx = 0, $inputType = "radio", $changeActionFuncName = "")
{
    //opt_prefix 두번째 자리 문자열이 "C"면 컬러, "B"면 흑백, "T"면 별색추가. 값이  "ET4"면 백색추가.
    //흑백(누베라) 추가. 
    $result = "";
    
    //낱장 인쇄컬러 백색 클릭시 처리.
    $func = "";
    if ($changeActionFuncName) $func = "$changeActionFuncName(this);";

    //출력 순서를 위해서...    
    $arrData = array('0' => 'C','1' => 'B','2' => 'I','3' => 'N','4' => 'W','5' => 'T');

    if (is_array($optionData))
    {
        foreach ($optionData as $key => $value) 
        {
            $k = substr($key, 1, 1);
            $data[substr($key, 1, 1)] = substr($key, 1, 1);

            if ($key == "OB3" || $key == "DB8") $data['N'] = "N"; //흑백(누베라) 추가

            if ($inputName == "opt" && $key == "ET4") {//백색 추가
                $data['W'] = "W";
            }
        }
    }

    foreach ($arrData as $key => $val) {
        if (!array_key_exists($val, $data)) {
            //echo "The '$val' element is in the array;";
            unset($arrData[$key]);
        }
    }

    if ($inputName == "opt") 
    {    
        $arrOption = array(
            'C' => array('id' => "m_color01", 'name' => $inputName."_print1", 'for' => "m_color01", 'img' => "color.png", 'txt' => "컬러", 'func' => $func), 
            'B' => array('id' => "m_color02", 'name' => $inputName."_print1", 'for' => "m_color02", 'img' => "color_b.png", 'txt' => "흑백", 'func' => $func),
            
            'N' => array('id' => "m_color03", 'name' => $inputName."_print1", 'for' => "m_color03", 'img' => "color_b.png", 'txt' => "흑백(누베라)", 'func' => $func),
            
            'W' => array('id' => "w_color01", 'name' => $inputName."_print1", 'for' => "w_color01", 'img' => "spot_w.png", 'txt' => "백색", 'func' => $func),
            
            'T' => array('id' => "s_color01", 'name' => $inputName."_print3_check", 'for' => "s_color01", 'img' => "add_s_color.png", 'txt' => "별색추가", 'func' => "print3ColorClick(this);")        
        );
    }
    else 
    {
        $name = $inputName."_print1";
        $id = "_".$inputName."_print1";

        if ($inputName == "inside" || $inputName == "inpage")
            $tagFix = "_".$idx;
                
        $funcName = "spot_color_sec_".$inputName."_".$idx;

        $arrOption = array(
            'C' => array('id' => "m_color01".$id.$tagFix, 'name' => $name.$tagFix, 'for' => "m_color01".$id.$tagFix, 'img' => "color.png", 'txt' => "컬러", 'func' => "spotColorSecHide('$funcName','s_color01$name');"), 
            'B' => array('id' => "m_color02".$id.$tagFix, 'name' => $name.$tagFix, 'for' => "m_color02".$id.$tagFix, 'img' => "color_b.png", 'txt' => "흑백", 'func' => "spotColorSecHide('$funcName','s_color01$name');"),
            
            'N' => array('id' => "m_color03".$id.$tagFix, 'name' => $name.$tagFix, 'for' => "m_color03".$id.$tagFix, 'img' => "color_b.png", 'txt' => "흑백(누베라)", 'func' => "spotColorSecHide('$funcName','s_color01$name');"),
            
            'T' => array('id' => "s_color01".$id.$tagFix, 'name' => $inputName."_print3_check".$tagFix, 'for' => "s_color01".$id.$tagFix, 'img' => "add_s_color.png", 'txt' => "별색추가", 'func' => "spotColorSecShow(this,'$funcName');")
        );
    }

    //select
    if ($inputType == "select") {
        $result = "<option value=''>선택해주세요.</option>";
    }   
    
    //첫번째  $key값 찾기.
    $arr_keys = array_keys($arrData);
    $first_key = $arr_keys[0];

    foreach ($arrData as $key => $val) 
    {
        $value = $val;
        $checked = "";

        if ($chkData) {
            if ($val == $chkData) $checked = " checked"; //선택값이 있으면...    
        }
        else {
            if ($inputType != "select") {
                if ($first_key == $key) $checked = " checked"; //선택값이 없으면 첫번째 항목을...
            }
        }

        if ($val == "T") {
            $input = "checkbox"; //별색은 체크박스.
            $value = "Y";

            if ($TchkData == "Y") $checked = " checked";
        }
        else {
            if ($val == "W") $value = "W"; //별색 ,백색 예외처리. / 다시 W값 전달.

            $input = $inputType;
        }

        if ($input == "select") {
            $selected = "";
            if ($checked == " checked") $selected = " selected";
            $result .= "<option value='".$value."' $selected>".$arrOption[$val][txt]."</option>";
        }
        else if ($input == "radio") {            
            $result .= "<input type='$input' name='".$arrOption[$val][name]."' id='".$arrOption[$val][id]."' value='".$value."' $checked onclick=\"calcuPrice();".$arrOption[$val]['func']."\">
                <label for='".$arrOption[$val]['for']."'>
                    <div class='btn_box'>
                        <img src='/skin/modern/assets/interpro/img/".$arrOption[$val][img]."' alt='".$arrOption[$val][txt]."'>
                        <p>".$arrOption[$val][txt]."</p>
                    </div>
                </label>            
            ";
        }
    }
    
    return $result;
}

//사용자 화면 양면단면 옵션 태그 만들기
function makeOptionInputTag_Print2($optionData, $chkData, $inputName = "opt", $idx = 0, $inputType = "radio")
{
    //opt_prefix 첫번째 자리 문자열이 "O"면 단면, "D"면 양면. 
    $result = "";

    if (is_array($optionData))
    {
        foreach ($optionData as $key => $value) 
        {
            $k = substr($key, 0, 1);

            if ($k == "O" || $k == "D")
                $data[substr($key, 0, 1)] = substr($key, 0, 1);
        }
    }

    if ($inputName == "opt") 
    {    
        $arrOption = array(
            'D' => array('id' => "side01", 'name' => $inputName."_print2", 'for' => "side01", 'img' => "matt.png", 'txt' => "양면"), 
            'O' => array('id' => "side02", 'name' => $inputName."_print2", 'for' => "side02", 'img' => "matt.png", 'txt' => "단면")
        );
    }
    else 
    {
        $checkboxTagNamePostFix = "_".$idx; //"[]";
		if ($inputName == "outside")
			$checkboxTagNamePostFix = "";			//표지는 배열형식이 아니다.
					
        $arrOption = array(
            'D' => array('id' => "side01_".$inputName."_print2$checkboxTagNamePostFix", 'name' => $inputName."_print2$checkboxTagNamePostFix", 'for' => "side01_".$inputName."_print2$checkboxTagNamePostFix", 'img' => "matt.png", 'txt' => "양면"), 
            'O' => array('id' => "side02_".$inputName."_print2$checkboxTagNamePostFix", 'name' => $inputName."_print2$checkboxTagNamePostFix", 'for' => "side02_".$inputName."_print2$checkboxTagNamePostFix", 'img' => "matt.png", 'txt' => "단면")
        );
    }

    //select
    if ($inputType == "select") {
        $result = "<option value=''>선택해주세요.</option>";
    }
    
    //첫번째  $key값 찾기.
    $arr_keys = array_keys($data);
    $first_key = $arr_keys[0];

    if (is_array($data))
    {
        $row = 1;
        foreach ($data as $key => $value) 
        {
            $checked = "";

            if ($chkData) {
                if ($key == $chkData) $checked = " checked"; //선택값이 있으면...    
            }
            else {
                if ($inputType != "select") {
                    if ($first_key == $key) $checked = " checked"; //선택값이 없으면 첫번째 항목을...
                }
            }
        
            if ($inputType == "select") {
                $selected = "";
                if ($checked == " checked") $selected = " selected";
                $result .= "<option value='".$value."' $selected>".$arrOption[$key][txt]."</option>";
            }
            else if ($inputType == "radio") {                
                $result .= "<input type='$inputType' name='".$arrOption[$key][name]."' id='".$arrOption[$key][id]."' value='".$key."' $checked onclick='calcuPrice();'>
                    <label for='".$arrOption[$key]['for']."'>
                        <div class='btn_box'>
                            <img src='/skin/modern/assets/interpro/img/".$arrOption[$key][img]."' alt='".$arrOption[$key][txt]."'>
                            <p>".$arrOption[$key][txt]."</p>
                        </div>
                    </label>            
                ";
            }
            $row++;
        }
    }
    
    //$result .= "</select>";
    return $result;
}

//사용자 화면 별색추가 옵션 태그 만들기
function makeOptionCheckTag_Print3($optionData, $chkData, $inputName = "opt", $idx = 0, $inputType = "checkbox")
{
    //opt_prefix 첫번째 자리 문자열이 "E"면 별색.

    if (is_array($optionData))
    {
        if ($inputName == "opt") 
        {    
            $name = $inputName."_print3[]";
        }
        else 
        {
            if ($inputName == "inside" || $inputName == "inpage") 
                $name = $inputName."_print3_".$idx."[]";
            else    
                $name = $inputName."_print3[]";

            $id = "_".$inputName."_print3_".$idx;
        }
        
        //select
        if ($inputType == "select")
            $result = "<option value=''>선택해주세요.</option>";  
        
        $row = 1;
        foreach ($optionData as $key => $value) 
        {
            if (substr($key, 0, 1) != "E") continue;

            $checked = "";
            if (is_array($chkData)) {
                if(in_array($key, $chkData)) $checked = " checked";
            }
            else {
                if ($key == $chkData) $checked = " checked";
            }
            
            $class = "";
            if ($key == "ET4") $class = "class='s_white'"; //백색.
           

            if ($inputType == "select") {
                $selected = "";
                if ($checked == " checked") $selected = " selected";
                $result .= "<option value='".$key."' $selected>".$value."</option>";
            }
            else {
                //spot_c_01
                //별색은 다중 선택이 가능하기 때문에 태그 이름을 배열로 만든다.
                //별색  이미지 matt.png 로 수정. spot_$row.png => matt.png
                $result .= "<input type='checkbox' name='".$name."' id='spot0".$row.$id."' value='".$key."' $checked onclick='calcuPrice();'>
                    <label for='spot0".$row.$id."' $class>
                        <div class='btn_box'>
                            <img src='/skin/modern/assets/interpro/img/matt.png' alt='$value'>
                            <p>$value</p>
                        </div>
                    </label>
                ";
            }
            $row++;
        }
    }

    //$result .= "</select>";
    return $result;
}

//옵셋 낱장 인쇄(뒷면) 옵션 태그 만들기
function makeOptionInputTag_Print4($optionData, $chkData, $TchkData, $inputName = "opt", $idx = 0, $inputType = "radio", $changeActionFuncName = "")
{
    //opt_prefix 두번째 자리 문자열이 "C"면 컬러, "B"면 흑백, "T"면 별색추가. 값이  "ET4"면 백색추가. 
    $result = "";
    
    //낱장 인쇄컬러 백색 클릭시 처리.
    $func = "";
    if ($changeActionFuncName) $func = "$changeActionFuncName(this);";

    //출력 순서를 위해서...    
    $arrData = array('0' => 'C','1' => 'B','2' => 'W','3' => 'T');

    if (is_array($optionData))
    {
        foreach ($optionData as $key => $value) 
        {
            $k = substr($key, 1, 1);
            $data[substr($key, 1, 1)] = substr($key, 1, 1);

            if ($inputName == "opt" && $key == "ET4") {//백색 추가
                $data['W'] = "W";
            }
        }
    }

    foreach ($arrData as $key => $val) {
        if (!array_key_exists($val, $data)) {
            //echo "The '$val' element is in the array;";
            unset($arrData[$key]);
        }
    }

    $name = "_".$inputName."_".$idx;
    $funcName = "spot_color_sec".$name;

    if ($inputName == "opt") 
    {    
        $arrOption = array(
            'C' => array('id' => "m_04", 'name' => $inputName."_print4", 'for' => "m_04", 'img' => "color.png", 'txt' => "컬러", 'func' => $func), 
            'B' => array('id' => "m_05", 'name' => $inputName."_print4", 'for' => "m_05", 'img' => "color_b.png", 'txt' => "흑백", 'func' => $func),
            
            'W' => array('id' => "w_04", 'name' => $inputName."_print4", 'for' => "w_04", 'img' => "spot_w.png", 'txt' => "백색", 'func' => $func),
            
            'T' => array('id' => "s_04", 'name' => $inputName."_print6_check", 'for' => "s_04", 'img' => "add_s_color.png", 'txt' => "별색추가", 'func' => "spotColorSecShow(this,'$funcName');")        
        );
    }
    else 
    {
        $checkboxTagNamePostFix = "[]";
        if ($inputName == "outside")
            $checkboxTagNamePostFix = "";           //표지는 배열형식이 아니다.                
                
        $arrOption = array(
            'C' => array('id' => "m_04".$name, 'name' => $inputName."_print4$checkboxTagNamePostFix", 'for' => "m_04".$name, 'img' => "color.png", 'txt' => "컬러", 'func' => "spotColorSecHide('$funcName','s_04$name');"), 
            'B' => array('id' => "m_05".$name, 'name' => $inputName."_print4$checkboxTagNamePostFix", 'for' => "m_05".$name, 'img' => "color_b.png", 'txt' => "흑백", 'func' => "spotColorSecHide('$funcName','s_04$name');"),
            'T' => array('id' => "s_04".$name, 'name' => $inputName."_print6_check$checkboxTagNamePostFix", 'for' => "s_04".$name, 'img' => "add_s_color.png", 'txt' => "별색추가", 'func' => "spotColorSecShow(this,'$funcName');")
        );
    }

    //첫번째  $key값 찾기.
    $arr_keys = array_keys($arrData);
    $first_key = $arr_keys[0];
    
    foreach ($arrData as $key => $val) 
    {
        $value = $val;
        $checked = "";

        if ($chkData) {
            if ($val == $chkData) $checked = " checked"; //선택값이 있으면...    
        }
        else {
            if ($first_key == $key) $checked = " checked"; //선택값이 없으면 첫번째 항목을...
        }

        if ($val == "T") {
            $input = "checkbox"; //별색은 체크박스.
            $value = "Y";

            if ($TchkData == "Y") $checked = " checked";
        }
        else {
            if ($val == "W") $value = "W"; //별색 ,백색 예외처리. / 다시 W값 전달.

            $input = $inputType;
        }

        $result .= "<input type='$input' name='".$arrOption[$val][name]."' id='".$arrOption[$val][id]."' value='".$value."' $checked onclick=\"calcuPrice();".$arrOption[$val]['func']."\">
            <label for='".$arrOption[$val]['for']."'>
                <div class='btn_box'>
                    <img src='/skin/modern/assets/interpro/img/".$arrOption[$val][img]."' alt='".$arrOption[$val][txt]."'>
                    <p>".$arrOption[$val][txt]."</p>
                </div>
            </label>            
        ";
    }
    
    //$result .= "</select>";
    return $result;
}

//옵셋 낱장 별색추가 옵션 태그 만들기
function makeOptionCheckTag_Print6($optionData, $chkData, $inputName = "opt", $idx = 0)
{
    //opt_prefix 첫번째 자리 문자열이 "E"면 별색.

    if (is_array($optionData))
    {
        $inputName = $inputName."_print6[]";
        
        if ($inputName != "opt")
            $name = "_".$inputName."_".$idx;
        
        $row = 1;
        foreach ($optionData as $key => $value) 
        {
            if (substr($key, 0, 1) != "E") continue;

            $checked = "";
            if (is_array($chkData)) {
                if(in_array($key, $chkData)) $checked = " checked";
            }
            else {
                if ($key == $chkData) $checked = " checked";
            }
            
            $class = "";
            if ($key == "ET4") $class = "class='b_s_white'"; //백색.

            //spot_c_01
            //별색은 다중 선택이 가능하기 때문에 태그 이름을 배열로 만든다.
            //별색  이미지 matt.png 로 수정. spot_$row.png => matt.png
            $result .= "<input type='checkbox' name='".$inputName."' id='b_spot0".$row.$name."' value='".$key."' $checked  onclick='calcuPrice();'>
                <label for='b_spot0".$row.$name."' $class>
                    <div class='btn_box'>
                        <img src='/skin/modern/assets/interpro/img/matt.png' alt='$value'>
                        <p>$value</p>
                    </div>
                </label>
            ";
            $row++;
        }
    }
    
    //$result .= "</select>";
    return $result;
}

function getOptionMissing($goodsData)
{
    global $r_ipro_print_code;

    if (!$goodsData['missing']) return "";

    foreach ($goodsData['missing'] as $value) {
        $result[$value] = "{$r_ipro_print_code[$value]}";
    }
    return $result;
    //return json_decode($result);
}

function getOptionRound($goodsData)
{
    global $r_ipro_print_code;

    if (!$goodsData['round']) return "";

    foreach ($goodsData['round'] as $value) {
        $result[$value] = "{$r_ipro_print_code[$value]}";
    }
    return $result;
    //return json_decode($result);
}

function getOptionDomoo($goodsData)
{
    global $r_ipro_print_code;

    if (!$goodsData['domoo']) return "";

    foreach ($goodsData['domoo'] as $value) {
        $result[$value] = "{$r_ipro_print_code[$value]}";
    }
    return $result;
    //return json_decode($result);
}

function getOptionBarcode($goodsData)
{
    global $r_ipro_print_code;

    if (!$goodsData['barcode']) return "";

    foreach ($goodsData['barcode'] as $value) {
        $result[$value] = "{$r_ipro_print_code[$value]}";
    }
    return $result;
    //return json_decode($result);
}

function getOptionNumber($goodsData)
{
    global $r_ipro_print_code;

    if (!$goodsData['number']) return "";

    foreach ($goodsData['number'] as $value) {
        $result[$value] = "{$r_ipro_print_code[$value]}";
    }
    return $result;
    //return json_decode($result);
}

function getOptionStand($goodsData)
{
    global $r_ipro_print_code;

    if (!$goodsData['stand']) return "";

    foreach ($goodsData['stand'] as $value) {
        $result[$value] = "{$r_ipro_print_code[$value]}";
    }
    return $result;
    //return json_decode($result);
}

function getOptionDangle($goodsData)
{
    global $r_ipro_print_code;

    if (!$goodsData['dangle']) return "";

    foreach ($goodsData['dangle'] as $value) {
        $result[$value] = "{$r_ipro_print_code[$value]}";
    }
    return $result;
    //return json_decode($result);
}

function getOptionType($goodsData)
{
    global $r_ipro_print_code;

    if (!$goodsData['type']) return "";

    foreach ($goodsData['type'] as $value) {
        $result[$value] = "{$r_ipro_print_code[$value]}";
    }
    return $result;
    //return json_decode($result);
}

function getOptionAddress($goodsData)
{
    global $r_ipro_print_code;

    if (!$goodsData['address']) return "";

    foreach ($goodsData['address'] as $value) {
        $result[$value] = "{$r_ipro_print_code[$value]}";
    }
    return $result;
    //return json_decode($result);
}

function getOptionWing($goodsData)
{
    global $r_ipro_print_code;

    if (!$goodsData['wing']) return "";

    foreach ($goodsData['wing'] as $value) {
        $result[$value] = "{$r_ipro_print_code[$value]}";
    }
    return $result;
    //return json_decode($result);
}

function getOptionInstant($goodsData)
{
    global $r_ipro_print_code;

    if (!$goodsData['instant']) return "";

    foreach ($goodsData['instant'] as $value) {
        $result[$value] = "{$r_ipro_print_code[$value]}";
    }
    return $result;
    //return json_decode($result);
}


function getOptionData($goodsData, $optionName)
{
    global $r_ipro_print_code;

    if (!$goodsData) return "";

    foreach ($goodsData as $value) {
        $result[$value] = "{$r_ipro_print_code[$value]}";
    }
    return $result;
    //return json_decode($result);
}


//비규격 주문의 스티커 규격 찾기 (원형,타원형,라운트)
function getStickerPrintOptSizeWithPrefixCustom($prefix, $document_x, $document_y)
{
    global $r_ipro_print_code;
    $result = "";
    
    $diffSize = $document_y;
    if ($document_x > $document_y)
        $diffSize = $document_x;
    
    $cntMaxKey = 0;
    foreach ($r_ipro_print_code as $key => $value) {
        $opt_prefix = substr($key, 0, 2);
        if ($opt_prefix == $prefix)
        {
            if ($cntMaxKey <= $value) $cntMaxKey = $value;
        }                       
    }               
        
    $oldKey = 100000;
    foreach ($r_ipro_print_code as $key => $value) {
        $opt_prefix = substr($key, 0, 2);
        if ($opt_prefix == $prefix)
        {
            if ($value >= $diffSize && $oldKey > $value)
            { 
                $result = $key;
                
                if ($oldKey > $value)
                    $oldKey = $value;
            }
        } 
    }
    
    
    //가장 근접한 규격을 찾지 못했으면 가장 큰값이  맞는 규격이다.
    if (!$result)
    {
        if ($cntMaxKey >= $diffSize)    
            $result = $cntMaxKey;
    }
    
    //echo $result . "<Br>";
    return $result;
}

function getPrintPage($code)
{
    if (!$code) return "";

    if ($code == "OPO") //옵셋-포스터상품코드
    {
        //수량: 100~1000매까지 100장단위, 1000매~100000매까지 500장 단위
        for ($i=100; $i < 1000; $i += 100) { 
            $result[$i] = $i;
        }

        for ($i=1000; $i <= 100000; $i += 500) { 
            $result[$i] = $i;
        }        
    }
    else if ($code == "OJD") //옵셋-전단지,리플렛상품코드 
    {
        //수량:200,300,500,700,1000,1300,1500,1800,2000,2000~10000(500장),10000~50000(1000장),50000~150000(5000장)
        $result[200] = "200";
        $result[300] = "300";
        $result[500] = "500";
        $result[700] = "700";
        $result[1000] = "1000";
        $result[1300] = "1300";
        $result[1500] = "1500";
        $result[1800] = "1800";

        for ($i=2000; $i < 10000; $i += 500) { 
            $result[$i] = $i;
        }
                
        for ($i=10000; $i < 50000; $i += 1000) { 
            $result[$i] = $i;
        }

        for ($i=50000; $i <= 150000; $i += 5000) { 
            $result[$i] = $i;
        }        
    }
    else if ($code == "OBO") //옵셋-책자,단행본,카탈로그,브로슈어 상품코드
    {
        //수량: 50,100~1000(100단위),1000~2000(200단위),2000~5000(500단위),5000~20000(1000단위),20000~50000(2000단위)
        $result[50] = "50";
        
        for ($i=100; $i < 1000; $i += 100) { 
            $result[$i] = $i;
        }

        for ($i=1000; $i < 2000; $i += 200) { 
            $result[$i] = $i;
        }
        
        for ($i=2000; $i < 5000; $i += 500) { 
            $result[$i] = $i;
        }

        for ($i=5000; $i < 20000; $i += 1000) { 
            $result[$i] = $i;
        }
        
        for ($i=20000; $i <= 50000; $i += 2000) { 
            $result[$i] = $i;
        }        
    }
    else if ($code == "PR") //현수막 실사출력 상품코드
    {
        //수량: 1~99(1단위),100~1000(100단위),1000~2000(200단위),2000~5000(500단위),5000~20000(1000단위),20000~50000(2000단위)
        //수량: 100~1000매까지 100장단위, 1000매~100000매까지 500장 단위
        for ($i=1; $i < 100; $i += 1) { 
            $result[$i] = $i;
        }

        for ($i=100; $i < 1000; $i += 100) { 
            $result[$i] = $i;
        }

        for ($i=1000; $i < 2000; $i += 200) { 
            $result[$i] = $i;
        }
        
        for ($i=2000; $i < 5000; $i += 500) { 
            $result[$i] = $i;
        }

        for ($i=5000; $i < 20000; $i += 1000) { 
            $result[$i] = $i;
        }
        
        for ($i=20000; $i <= 50000; $i += 2000) { 
            $result[$i] = $i;
        }        
    }

    return $result;
}

?>