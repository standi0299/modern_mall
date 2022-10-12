<? 

//모든 옵션 계산
function calcuPaperPr($size_key, $page_cnt, $paper_key, $paper_gram = "")
{
	global $r_ipro_paper_pr;
	$result = 0;
    //debug($r_ipro_paper_pr);

    	$calcuData = $r_ipro_paper_pr[$paper_key][paper];
	if (is_array($calcuData)){
	    if ($paper_gram || $paper_gram == "0")
            $result = $calcuData[$paper_gram][$size_key];
        else 
            $result = $calcuData[$size_key];
	}

    $result = ceil($result * $page_cnt);
	return $result;
}


function calcuPrintPr($size_key, $page_cnt, $print_key, $print_goods_type)
{
	global ${"r_ipro_print_pr_".$print_goods_type};
    $print_data = ${"r_ipro_print_pr_".$print_goods_type};
    
    //debug($size_key.",".$page_cnt.",".$print_key.",".$print_goods_type);
    //debug($print_data);

	$result = 0;

	if (is_array($print_data[$size_key]))
	{
	    //debug($print_data[$size_key]);
		$calcuData = getPrCalcuPriceData($print_data[$size_key], $page_cnt);
		//debug($calcuData);
		if (is_array($calcuData))
		{
			$result = $calcuData[$print_key];
		}
	}
		
	return $result;	
}

function calcuPrintAddPr($print_goods_type, $size_cnt = "1")
{
    global ${"r_ipro_print_pr_addprice_".$print_goods_type};
    $print_data = ${"r_ipro_print_pr_addprice_".$print_goods_type};
    
    //debug($print_goods_type.",".$size_cnt);
    //debug($print_data);

    $result = 0;

    if (is_array($print_data))
    {
        $result = ($print_data[$size_cnt]);
    }
        
    return $result;
}

function calcuCommonOptionPr($size_key, $page_cnt, $opt_key, $opt_type)
{
    global ${"r_ipro_".$opt_type."_pr"};

    $opt_data = ${"r_ipro_".$opt_type."_pr"};
    //debug($opt_data);
    //debug($size_key.",".$page_cnt.",".$opt_key.",".$opt_type);

    $result = 0;
    //$calcuData = getPrCalcuPriceData($r_iro_pr_product[$size_key], $page_cnt);
    if (is_array($opt_data[$size_key][$page_cnt])){
        $result = $opt_data[$size_key][$page_cnt][$opt_key];
    }
    
    return $result;
}

//현수막 실사출력 상품에 사용된 규격중에 가장 맞는 규격 찾기.
function getPrOptionSizeWithCalcuPriceData($width, $height)
{
    global $r_ipro_print_code;
    $result = "";

    $minTemp = 10000000000;
    foreach ($r_ipro_print_code as $key => $value) {
        if (strpos($key, "SPR") === false) continue;
        $sizeArr = explode("*", $value);
        
        $sizeCode[$key] = $value;
        
        //가장 작은 Key. 규격보다 작은 값을 입력하여 적합한 규격이 없을 경우 가장 작은 규격을 사용한다.
        if (($sizeArr[0] * $sizeArr[1]) < $minTemp) {
            $minTemp = ($sizeArr[0] * $sizeArr[1]);
            $minKey = $key;
        }
    }
    //krsort($sizeCode);
    //debug("minTemp:".$minTemp.", minKey:".$minKey);
    //debug($sizeCode);
    //debug("width:".$width."*height:".$height);
    //debug(($width * $height));

    if (is_array($r_ipro_print_code))
    {
        $minSizeKey = array();
        foreach ($sizeCode as $key => $value) {
            //debug("key:".$key);
            $checkProcFlag = false;
            $sizeArr = explode("*", $value);

            if (count($sizeArr) > 1) {
                if ($sizeArr[0] <= $width && $sizeArr[1] <= $height) $checkProcFlag = true;
                //debug("checkProcFlag:".$checkProcFlag);
                if ($checkProcFlag) {
                    $result = $key;
                    $minSizeKey[$key] = $sizeArr[0] * $sizeArr[1];
                }
            }
        }
        //debug($minSizeKey);
        
        //작은 면적들 중에 가장 큰 면적을 리턴함.
        if (is_array($minSizeKey)) {
            $max = 0;
            foreach ($minSizeKey as $key => $val) {
                if ($val > $max) {
                    $max = $val;
                }
            }

            $result = array_search($max, $minSizeKey);
        }
        
        if (!$result) {
            $result = $minKey; //사용자 입력 면적이 규격에 없는 작은 값이면 규격중에 가장 작은 면적을 리턴함.
        }



        /*
        $maxSizeTemp = 0;
        $maxSizeKey = "";
        $minSizeTemp = 10000000000;
        foreach ($r_ipro_print_code as $key => $value) 
        {
            if (strpos($key, "SPR") === false) continue;
            $checkProcFlag = false;
            $sizeArr = explode("*", $value);

            if (count($sizeArr) > 1)
            {
                if ($sizeArr[0] >= $width && $sizeArr[1] >= $height) $checkProcFlag = true;
                if ($sizeArr[1] >= $width && $sizeArr[0] >= $height) $checkProcFlag = true;

                if ($checkProcFlag && ($minSizeTemp > ($sizeArr[0] * $sizeArr[1])) )
                {                           
                    $result = $key;
                    $minSizeTemp = $sizeArr[0] * $sizeArr[1];           //width * height(면적) 이 가장 작은걸 사용하기 위해서 저장함. 면적이 작은게 가장 작은 사이즈라 생각함.
                }
                
                if ($maxSizeTemp <= ($sizeArr[0] * $sizeArr[1])) { //width * height(면적) 이 가장 큰걸 사용하기 위해서 저장함. 면적이 큰게 가장 큰 사이즈라 생각함.
                    $maxSizeTemp = ($sizeArr[0] * $sizeArr[1]);
                    $maxSizeKey = $key;
                }
            }
        }

        if (!$result) $result = $maxSizeKey; //사용자 입력 면적이 규격을 초과할 경우 규격중에 가장 큰 면적을 리턴함.
        */
        
         
    }

    return $result;
}

//옵션가격 테이블에서 옵션키에 해당하는 가격정보 가져오기. 
function getPrCalcuPriceData($optPriceData, $cnt)
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
?>