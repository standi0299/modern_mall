<?
/*
* @date : 20190326
* @author : kdk
* @brief : 현수막 실사출력 추가
* @desc : 계산식 검증 필요.
*/
?>
<? 
	$calcu_mode = "Y";
	include_once 'lib_print.php';

    //debug($print_goods_item[$_POST[goodsno]]);

	$mode = $_POST[mode];
	$print_goods_type = $_POST[print_goods_type];			//명함인지, 스티커인지 구분
	
	$opt_size_width = 0;
	$opt_size_height = 0;
	
    //사용자 규격일경우 사이즈에 맞는 종이 규격을 찾는다.
    if ($_POST['opt_size'] == "USER") {
        //재단사이즈로 가격을 찾아야 하기때문에 재단사이즈로 구한다. 
        $opt_size_key = getPrOptionSizeWithCalcuPriceData($_POST['cut_width'], $_POST['cut_height']);
        //debug("opt_size_key: ".$opt_size_key);
    } else {
        $opt_size_key = $_POST['opt_size'];
    }
//debug($opt_size_key);
//debug($r_ipro_print_code[$opt_size_key]);

    //규격정보에서 width,height 정보를 찾는다.
    if ($r_ipro_print_code[$opt_size_key]) {
        $sizeArr = explode("*", $r_ipro_print_code[$opt_size_key]);
        $opt_size_width = $sizeArr[0];
        $opt_size_height = $sizeArr[1];
        
        if ($_POST['opt_size'] != "USER") { //1mm당 단가 계산을 위해 규격에 설정된 mm를 담는다. 
            $_POST['cut_width'] = $sizeArr[0];
            $_POST['cut_height'] = $sizeArr[1];
        }
    }
            
//debug("opt_size : ". $opt_size_width ."x". $opt_size_height);
//debug("use_size : ". $_POST['cut_width'] ."x". $_POST['cut_height']);

    //계산 방식 확인 (규격별 단가,1mm당 단가)
    if ($cfg[extra_use]) {
        $extra_use = json_decode($cfg[extra_use],1);
    }   

//debug($extra_use[extra_print_pr_price_type]);
    if ($extra_use[extra_print_pr_price_type] == "MM") { //1mm당 단가
        //debug(getPr1mmPrice($_POST['opt_paper'], "paper1mm"));
        //debug(getPr1mmPrice($_POST['opt_paper'], "print1mm"));
        //debug(getPr1mmPrice($_POST['opt_paper'], "coating1mm"));
        
        //용지비(mm)
        $paperPrice = (($_POST['cut_width'] * $_POST['cut_height']) * getPr1mmPrice($_POST['opt_paper'], "paper1mm"));
        $paperPrice *= $_POST['opt_page'];
        //debug($paperPrice);
        
        //인쇄비(mm)
        $printPrice = (($_POST['cut_width'] * $_POST['cut_height']) * getPr1mmPrice($_POST['opt_paper'], "print1mm"));
        $printPrice *= $_POST['opt_page'];
        //debug($printPrice);

        //로그정보.
        $userData[user] = $_POST['cut_width']."*".$_POST['cut_height'];
        $userData[paper1mm] = getPr1mmPrice($_POST['opt_paper'], "paper1mm");
        $userData[paperPrice] = $paperPrice;
        $userData[print1mm] = getPr1mmPrice($_POST['opt_paper'], "print1mm");
        $userData[printPrice] = $printPrice;
                
        //코팅비(mm)
        if ($_POST['opt_coating_check'] == "Y" && $_POST['opt_coating'] != "ECT1") { //ECT1 없음이 아니면...
            $coatingPrice = (($_POST['cut_width'] * $_POST['cut_height']) * getPr1mmPrice($_POST['opt_paper'], "coating1mm"));
            $coatingPrice *= $_POST['opt_page'];
            //debug($coatingPrice);
            
            //양면은 두배 처리.
            //if ($_POST['opt_coating'] == "ECT5" || $_POST['opt_coating'] == "ECT6") { //현재 양면 옵션 없음.
                //$coatingPrice *= 2;
            //}            
            
            //로그정보.
            $userData[coating1mm] = getPr1mmPrice($_POST['opt_paper'], "coating1mm");
            $userData[coatingPrice] = $coatingPrice;
        }
    } else { //규격별 단가
        //사용자 규격일경우 사이즈에 맞는 종이 규격을 찾는다.
        if ($_POST['opt_size'] == "USER") {
            //재단사이즈로 가격을 찾아야 하기때문에 재단사이즈로 구한다. 
            $opt_size_key = getPrOptionSizeWithCalcuPriceData($_POST['cut_width'], $_POST['cut_height']);
            //debug("opt_size_key: ".$opt_size_key);
            
            //추가 인쇄비 확인.
            //debug($r_ipro_print_code[$opt_size_key]);
            $userSize = (int)($_POST['cut_width'] * $_POST['cut_height']);
            
            if ($r_ipro_print_code[$opt_size_key]) {
                $sizeArr = explode("*", $r_ipro_print_code[$opt_size_key]);
                $optSize = (int)($sizeArr[0] * $sizeArr[1]);
            }
            //debug("optSize: ".$optSize);
            if ($userSize > $optSize) {
                $addSize = (int)($userSize - $optSize);
                //debug("addSize: ".$addSize);
                $addPrice = calcuPrintAddPr($print_goods_type);
                $printAddPrice = ($addPrice * $addSize);
                //debug("printAddPrice:".$printAddPrice);
            }
    
            //로그정보.
            $userData[user] = $_POST['cut_width']."*".$_POST['cut_height'];
            $userData[user_size] = $userSize;
            $userData[search_size_key] = $opt_size_key ." : ". $r_ipro_print_code[$opt_size_key];
            $userData[search_size] = $optSize;
        
            $userData[add_size] = $addSize . "[add_size = (user_size - search_size)]";
            $userData[add_price] = $printAddPrice;
        } else {
            $opt_size_key = $_POST['opt_size'];
        }
        //debug($opt_size_key);
        
        //인쇄 key 만들기.
        //$opt_print_key = getPrintSideCode($_POST['opt_print1'], $_POST['opt_print2']);
        $opt_print_key = "OC1"; 
        //debug($opt_print_key);
        
        $paperPrice = calcuPaperPr($opt_size_key, $_POST['opt_page'], $_POST['opt_paper'], "0");
        //debug($paperPrice);
        $printPrice = calcuPrintPr($opt_size_key, $_POST['opt_page'], $opt_print_key, $print_goods_type);
        //debug($printPrice);
    
        if ($_POST['opt_coating_check'] == "Y") {
            $coatingPrice = calcuCommonOptionPr($opt_size_key, $_POST['opt_page'], $_POST['opt_coating'], "coating");
        }           
    }
    
    //후가공 옵션.
	if ($_POST['opt_cut_check'] == "Y")
		$cutPrice = calcuCommonOptionPr($opt_size_key, $_POST['opt_page'], $_POST['opt_cut'], "cut");
	
	//디자인비는 1회성 비용으로 처리.(수량에 관계없이)
	if ($_POST['opt_design_check'] == "Y")
		$designPrice = calcuCommonOptionPr($opt_size_key, "1", $_POST['opt_design'], "design"); //가격입력시 수량 "1"에 해당하는 금액으로 사용.
    
	if ($_POST['opt_processing_check'] == "Y")
		$processingPrice = calcuCommonOptionPr($opt_size_key, $_POST['opt_page'], $_POST['opt_processing'], "processing");
	
    //debug($coatingPrice);
    //debug($cutPrice);
    //debug($designPrice);
    //debug($processingPrice);
    
	$paperPrice *= $_POST['cnt'];
	$printPrice *= $_POST['cnt'];
    $printAddPrice *= $_POST['cnt'];

	$coatingPrice *= $_POST['cnt'];
	$cutPrice *= $_POST['cnt'];
	$designPrice *= $_POST['cnt'];//디자인비는 1회성 비용으로 처리.(수량에 관계없이)
	$processingPrice *= $_POST['cnt'];

	$response[paper_price] = ceil($paperPrice); //소수점 올림.
	$response[print_price] = ceil($printPrice); //소수점 올림.
    $response[print_add_price] = ceil($printAddPrice); //소수점 올림.
	
	$response[coating_price] = ceil($coatingPrice); //소수점 올림.
	$response[cut_price] = ceil($cutPrice); //소수점 올림.
	$response[design_price] = ceil($designPrice); //소수점 올림.
	$response[processing_price] = ceil($processingPrice); //소수점 올림.
    
    $response[paper_price] -= ($response[paper_price] % 100); //10단위 절사.
    $response[print_price] -= ($response[print_price] % 100); //10단위 절사.
    $response[print_add_price] -= ($response[print_add_price] % 100); //10단위 절사.

    $response[coating_price] -= ($response[coating_price] % 100); //10단위 절사.
    $response[cut_price] -= ($response[cut_price] % 100); //10단위 절사.
    $response[design_price] -= ($response[design_price] % 100); //10단위 절사.
    $response[processing_price] -= ($response[processing_price] % 100); //10단위 절사.

    $response[user_data] = $userData;

	include_once 'option_calcu_common.php';
    //debug($response);
	echo json_encode($response);
?>