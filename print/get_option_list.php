<?
    include_once '../_header.php'; 
	include_once 'lib_print.php';

	$mode = $_POST[mode];
	$goods_no = $_POST[goodsno];
    $goods_type = $_POST[print_goods_type];
	
	//print_r($print_goods_item[$goods_no]);
    
    //debug($print_goods_item[$goods_no]);
    //debug($r_goods_view_mapping[$goods_no]['type']);
    
	$result = getOptionSize($print_goods_item[$goods_no]);
	$result = makeOptionSelectTag($result, $_POST['opt_size']);
	$response[opt_size] = $result;

    //$response[opt_size_cut_x] = "150";
    //$response[opt_size_cut_y] = "100";
    if ($opt_size_cut_x)
		$response[opt_size_cut_x] = $opt_size_cut_x;
	else
		$response[opt_size_cut_x] = $_POST[cut_width];
	
	if ($opt_size_cut_y)
		$response[opt_size_cut_y] = $opt_size_cut_y;
	else
	   $response[opt_size_cut_y] = $_POST[cut_height];    

    //작업사이즈.
    if ($_POST[cut_width])
        $response[opt_size_work_x] = ($_POST[cut_width]+6);
    
    if ($_POST[cut_height])
        $response[opt_size_work_y] = ($_POST[cut_height]+6);

    //낱장
    $result = getOptionPaperGroup($print_goods_item[$goods_no]);
    $result = makeOptionSelectTag($result, $_POST['opt_paper_group']);
    $response[opt_paper_group] = $result;

    $result = getOptionPaper($print_goods_item[$goods_no], $_POST[opt_paper_group]);
    $result = makeOptionSelectTag($result, $_POST['opt_paper']);
    $response[opt_paper] = $result;

    $result = getOptionPaperGram($print_goods_item[$goods_no], $_POST[opt_paper]);
    $result = makeOptionSelectTag($result, $_POST['opt_paper_gram']);
    $response[opt_paper_gram] = $result;

    //사용자 화면 인쇄컬러 옵션 태그 만들기
    $funcName = "printColorClick";
    //function makeOptionInputTag_Print1($optionData, $chkData, $TchkData, $inputName = "opt", $idx = 0, $inputType = "radio", $changeActionFuncName = "")
    $result = makeOptionInputTag_Print1(getOptionPrint($print_goods_item[$goods_no]), $_POST['opt_print1'], $_POST['opt_print3_check'], "opt", "0", "radio", $funcName);
    $response[opt_print1] = $result;
    
    //사용자 화면 양면단면 옵션 태그 만들기
    $result = makeOptionInputTag_Print2(getOptionPrint($print_goods_item[$goods_no]), $_POST['opt_print2']);
    $response[opt_print2] = $result;
    
    //사용자 화면 별색추가 옵션 태그 만들기
    $result = makeOptionCheckTag_Print3(getOptionPrint($print_goods_item[$goods_no]), $_POST['opt_print3']);
    $response[opt_print3] = $result;

    //옵셋 낱장 인쇄(뒷면).
    if ($goods_type == "OS01") {
        //옵셋 낱장 인쇄(뒷면) 옵션 태그 만들기
        $result = makeOptionInputTag_Print4(getOptionPrint($print_goods_item[$goods_no]), $_POST['opt_print4'], $_POST['opt_print4_check'], "opt", "0", "radio", "spotWhiteHide");
        $response[opt_print4] = $result;
    
        //옵셋 낱장 별색추가 옵션 태그 만들기
        $result = makeOptionCheckTag_Print6(getOptionPrint($print_goods_item[$goods_no]), $_POST['opt_print6']);
        $response[opt_print6] = $result;
    }

    foreach ($print_goods_item[$goods_no] as $OptionKey => $OptionValue) {
        if ($OptionKey == "size" || $OptionKey == "paper" || $OptionKey == "print") continue;

        //다이렉트 파일업로드 사용여부, 건수 사용여부 추가.
        if ($OptionKey == "directupload_use" || $OptionKey == "cnt_use")
        {
            $result = $OptionValue;
        }
        else 
        {
            $result = getOptionData($OptionValue, $OptionKey);
    
            if ($OptionKey == "instant")
                $result = makeOptionSelectTag($result, $_POST['opt_'.$OptionKey]);
            else if ($OptionKey == "scb" || $OptionKey == "foil") //체크박스 -스코딕스 박, 박. 
                $result = makeOptionInputTag($result, $OptionKey, $_POST['opt_'.$OptionKey], $OptionKey, "opt_".$OptionKey."[]", "", "checkbox");
            else
                $result = makeOptionInputTag($result, $OptionKey, $_POST['opt_'.$OptionKey], $OptionKey, "opt_".$OptionKey, "", "radio");
        }    
        $response['opt_'.$OptionKey] = $result;
    }

    
    /*
    $result = getOptionPrint($print_goods_item[$goods_no]);
    $result = makeOptionSelectTag($result, $_POST['opt_print']);
    $response[opt_print] = $result;
    

	$result = getOptionGloss($print_goods_item[$goods_no]);
	$result = makeOptionSelectTag($result, $_POST['opt_gloss'], "코팅안함");
	$response[opt_gloss] = $result;


	$result = getOptionPunch($print_goods_item[$goods_no]);
	$result = makeOptionSelectTag($result, $_POST['opt_punch'], "타공없음");
	$response[opt_punch] = $result;


	$result = getOptionOshi($print_goods_item[$goods_no]);
	$result = makeOptionSelectTag($result, $_POST['opt_oshi'], "오시안함");
	$response[opt_oshi] = $result;


    $result = getOptionMissing($print_goods_item[$goods_no]);
    $result = makeOptionSelectTag($result, $_POST['opt_missing'], "미싱안함");
    $response[opt_missing] = $result;


    $result = getOptionRound($print_goods_item[$goods_no]);
    $result = makeOptionSelectTag($result, $_POST['opt_round'], "귀도리안함");
    $response[opt_round] = $result;


    $result = getOptionDomoo($print_goods_item[$goods_no]);
    $result = makeOptionSelectTag($result, $_POST['opt_domoo'], "도무송안함");
    $response[opt_domoo] = $result;


    $result = getOptionBarcode($print_goods_item[$goods_no]);
    $result = makeOptionSelectTag($result, $_POST['opt_barcode'], "바코드안함");
    $response[opt_barcode] = $result;


    $result = getOptionNumber($print_goods_item[$goods_no]);
    $result = makeOptionSelectTag($result, $_POST['opt_number'], "넘버링안함");
    $response[opt_number] = $result;


    $result = getOptionStand($print_goods_item[$goods_no]);
    $result = makeOptionSelectTag($result, $_POST['opt_stand'], "스탠드(미니배너)안함");
    $response[opt_stand] = $result;


    $result = getOptionDangle($print_goods_item[$goods_no]);
    $result = makeOptionSelectTag($result, $_POST['opt_dangle'], "댕글(와블러)안함");
    $response[opt_dangle] = $result;


    $result = getOptionType($print_goods_item[$goods_no]);
    $result = makeOptionSelectTag($result, $_POST['opt_type'], "양면테잎(봉투)안함");
    $response[opt_type] = $result;


    $result = getOptionAddress($print_goods_item[$goods_no]);
    $result = makeOptionSelectTag($result, $_POST['opt_address'], "주소인쇄(봉투)안함");
    $response[opt_address] = $result;


    $result = getOptionUV($print_goods_item[$goods_no]);
    $result = makeOptionSelectTag($result, $_POST['opt_sc'], "스코딕스 부분코팅안함");
    $response[opt_sc] = $result;


    $result = getOptionFoil($print_goods_item[$goods_no]);
    $result = makeOptionSelectTag($result, $_POST['opt_scb'], "스코딕스 (박) 책 표지안함");
    $response[opt_scb] = $result;


    $result = getOptionWing($print_goods_item[$goods_no]);
    $result = makeOptionSelectTag($result, $_POST['opt_wing'], "날개(책자)안함");
    $response[opt_wing] = $result;


    $result = getOptionBind($print_goods_item[$goods_no]);
    $result = makeOptionSelectTag($result, $_POST['opt_bind'], "제본(책자)안함");
    $response[opt_bind] = $result;


    $result = getOptionBindType($print_goods_item[$goods_no]);
    $result = makeOptionSelectTag($result, $_POST['opt_bindtype'], "제본 방향(책자)안함");
    $response[opt_bindtype] = $result;
    

    $result = getOptionInstant($print_goods_item[$goods_no]);
    $result = makeOptionSelectTag($result, $_POST['opt_instant']);
    $response[opt_instant] = $result;
    */

//debug($response);
	echo json_encode($response);
?>