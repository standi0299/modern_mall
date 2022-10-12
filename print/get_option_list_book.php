<?
    include_once '../_header.php'; 
	include_once 'lib_print.php';

	$mode = $_POST[mode];
	$goods_no = $_POST[goodsno];
    $goods_type = $_POST[print_goods_type];


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
    
    $result = getOptionPaperGroup($print_goods_item[$goods_no], "outside_paper");
    $result = makeOptionSelectTag($result, $_POST['outside_paper_group']);
    $response[outside_paper_group] = $result;

    $result = getOptionPaper($print_goods_item[$goods_no], $_POST[outside_paper_group], "outside_paper");
    $result = makeOptionSelectTag($result, $_POST['outside_paper']);
    $response[outside_paper] = $result;

    $result = getOptionPaperGram($print_goods_item[$goods_no], $_POST[outside_paper], "outside_paper");
    $result = makeOptionSelectTag($result, $_POST['outside_paper_gram']);
    $response[outside_paper_gram] = $result;

    //사용자 화면 인쇄컬러 옵션 태그 만들기
    $result = makeOptionInputTag_Print1(getOptionPrint($print_goods_item[$goods_no], "outside_print"), $_POST['outside_print1'], $_POST['outside_print3_check'], "outside");
    $response[outside_print1] = $result;

    //사용자 화면 양면단면 옵션 태그 만들기
    $result = makeOptionInputTag_Print2(getOptionPrint($print_goods_item[$goods_no], "outside_print"), $_POST['outside_print2'], "outside");
    $response[outside_print2] = $result;
    
    //사용자 화면 별색추가 옵션 태그 만들기
    $result = makeOptionCheckTag_Print3(getOptionPrint($print_goods_item[$goods_no], "outside_print"), $_POST['outside_print3'], "outside");
    $response[outside_print3] = $result;

    //옵셋 책자 인쇄(뒷면).
    if ($goods_type == "OS02") {
        //옵셋 책자 인쇄(뒷면) 옵션 태그 만들기
        $result = makeOptionInputTag_Print4(getOptionPrint($print_goods_item[$goods_no], "outside_print_back"), $_POST['outside_print4'], $_POST['outside_print6_check'], "outside", "1", "radio", "spotWhiteHide");
        $response[outside_print4] = $result;
    
        //옵셋 책자 별색추가 옵션 태그 만들기
        $result = makeOptionCheckTag_Print6(getOptionPrint($print_goods_item[$goods_no], "outside_print_back"), $_POST['outside_print6'], "outside");
        $response[outside_print6] = $result;
    }

    ###내지
    if (is_array($_POST[inside_cnt])) {
        foreach ($_POST[inside_cnt] as $key => $value) {             
            //$paper[$value] = $_POST[$value];
            //$paper[$value] = $_POST["opt_paper_".$value];
            $response[inside_cnt][] = $value;
        }
    }
    
    $insidePaperGroup = getOptionPaperGroup($print_goods_item[$goods_no], "inside_paper");
    if (is_array($_POST[inside_paper_group])) {
        foreach ($_POST[inside_paper_group] as $key => $value) {
            $result = makeOptionSelectTag($insidePaperGroup, $value);
            $response[inside_paper_group][] = $result;
        }
    }
    else {
        $result = makeOptionSelectTag($insidePaperGroup, "");
        $response[inside_paper_group][] = $result;
    }

    if (is_array($_POST[inside_paper])) {
        foreach ($_POST[inside_paper] as $key => $value) {             
            $result = getOptionPaper($print_goods_item[$goods_no], $_POST[inside_paper_group][$key], "inside_paper");
            $result = makeOptionSelectTag($result, $value);
            $response[inside_paper][] = $result;
        }
    }
    else {
        $result = makeOptionSelectTag($result, "");
        $response[inside_paper][] = $result;
    }

    if (is_array($_POST[inside_paper_gram])) {
        foreach ($_POST[inside_paper_gram] as $key => $value) {
            $result = getOptionPaperGram($print_goods_item[$goods_no], $_POST[inside_paper][$key], "inside_paper");
            $result = makeOptionSelectTag($result, $value);
            $response[inside_paper_gram][] = $result;
        }
    }
    else {
        $result = makeOptionSelectTag($result, "");
        $response[inside_paper_gram][] = $result;
    }     

    //사용자 화면 내지 인쇄컬러 옵션 태그 만들기
    if (is_array($_POST[inside_paper_group])) {
        //inside_print1_0
        foreach ($_POST as $ItemKey => $ItemValue) 
        {
            if (strpos($ItemKey, "inside_print1_") !== false)
            {
                $postArr = split('_', $ItemKey);
                $result = makeOptionInputTag_Print1(getOptionPrint($print_goods_item[$goods_no], "inside_print"), $_POST[$ItemKey], $_POST["inside_print3_check_".$postArr[2]], "inside", $postArr[2]);
                $response[$ItemKey] = $result;
            }
        }
    }
    else {
        $result = makeOptionInputTag_Print1(getOptionPrint($print_goods_item[$goods_no], "inside_print"), "", "", "inside");
        $response[inside_print1_0] = $result;
    }
    
    //사용자 화면 내지 양면단면 옵션 태그 만들기
    if (is_array($_POST[inside_paper_group])) {
        //inside_print2_0
        foreach ($_POST as $ItemKey => $ItemValue) 
        {
            if (strpos($ItemKey, "inside_print2_") !== false)
            {
                $postArr = split('_', $ItemKey);
                $result = makeOptionInputTag_Print2(getOptionPrint($print_goods_item[$goods_no], "inside_print"), $_POST[$ItemKey], "inside", $postArr[2]);
                $response[$ItemKey] = $result;
            }
        }        
    }
    else {
        $result = makeOptionInputTag_Print2(getOptionPrint($print_goods_item[$goods_no], "inside_print"), "", "inside");
        $response[inside_print2_0] = $result;
    }
    
    //사용자 화면 내지 별색추가 옵션 태그 만들기
    if (is_array($_POST[inside_paper_group])) {
        //inside_print3_0[]
        foreach ($_POST as $ItemKey => $ItemValue) 
        {
            if (strpos($ItemKey, "inside_print3_") !== false)
            {
                $postArr = split('_', $ItemKey);
                $result = makeOptionCheckTag_Print3(getOptionPrint($print_goods_item[$goods_no], "inside_print"), $_POST[$ItemKey], "inside", $postArr[2]);
                $response[$ItemKey] = $result;
            }
        }
    }
    else {
        $result = makeOptionCheckTag_Print3(getOptionPrint($print_goods_item[$goods_no], "inside_print"), "", "inside");
        //$response[inside_print3][] = $result;
        $response[inside_print3_0] = $result;
    }

    ###간지/면지
    if (is_array($_POST[inpage_cnt])) {
        foreach ($_POST[inpage_cnt] as $key => $value) {             
            //$paper[$value] = $_POST[$value];
            //$paper[$value] = $_POST["opt_paper_".$value];
            $response[inpage_cnt][] = $value;
        }
    }
    
    $inpagePaperGroup = getOptionPaperGroup($print_goods_item[$goods_no], "inpage_paper");
    if (is_array($_POST[inpage_paper_group])) {
        foreach ($_POST[inpage_paper_group] as $key => $value) {
            $result = makeOptionSelectTag($inpagePaperGroup, $value);
            $response[inpage_paper_group][] = $result;
        }
    }
    else {
        $result = makeOptionSelectTag($inpagePaperGroup, "");
        $response[inpage_paper_group][] = $result;
    }

    if (is_array($_POST[inpage_paper])) {
        foreach ($_POST[inpage_paper] as $key => $value) {             
            $result = getOptionPaper($print_goods_item[$goods_no], $_POST[inpage_paper_group][$key], "inpage_paper");
            $result = makeOptionSelectTag($result, $value);
            $response[inpage_paper][] = $result;
        }
    }
    else {
        $result = makeOptionSelectTag($result, "");
        $response[inpage_paper][] = $result;
    }

    if (is_array($_POST[inpage_paper_gram])) {
        foreach ($_POST[inpage_paper_gram] as $key => $value) {
            $result = getOptionPaperGram($print_goods_item[$goods_no], $_POST[inpage_paper][$key], "inpage_paper");
            $result = makeOptionSelectTag($result, $value);
            $response[inpage_paper_gram][] = $result;
        }
    }
    else {
        $result = makeOptionSelectTag($result, "");
        $response[inpage_paper_gram][] = $result;
    }     

    //사용자 화면 간지/면지 인쇄컬러 옵션 태그 만들기
    if (is_array($_POST[inpage_paper_group])) {
        //inpage_print1_0
        foreach ($_POST as $ItemKey => $ItemValue) 
        {
            if (strpos($ItemKey, "inpage_print1_") !== false)
            {
                $postArr = split('_', $ItemKey);
                $result = makeOptionInputTag_Print1(getOptionPrint($print_goods_item[$goods_no], "inpage_print"), $_POST[$ItemKey], $_POST["inpage_print3_check_".$postArr[2]], "inpage", $postArr[2]);
                $response[$ItemKey] = $result;
            }
        }
    }
    else {
        $result = makeOptionInputTag_Print1(getOptionPrint($print_goods_item[$goods_no], "inpage_print"), "", "", "inpage");
        $response[inpage_print1_0] = $result;
    }
    
    //사용자 화면 간지/면지 양면단면 옵션 태그 만들기
    if (is_array($_POST[inpage_paper_group])) {
        //inpage_print2_0
        foreach ($_POST as $ItemKey => $ItemValue) 
        {
            if (strpos($ItemKey, "inpage_print2_") !== false)
            {
                $postArr = split('_', $ItemKey);
                $result = makeOptionInputTag_Print2(getOptionPrint($print_goods_item[$goods_no], "inpage_print"), $_POST[$ItemKey], "inpage", $postArr[2]);
                $response[$ItemKey] = $result;
            }
        }        
    }
    else {
        $result = makeOptionInputTag_Print2(getOptionPrint($print_goods_item[$goods_no], "inpage_print"), "", "inpage");
        $response[inpage_print2_0] = $result;
    }
    
    //사용자 화면 간지/면지 별색추가 옵션 태그 만들기
    if (is_array($_POST[inpage_paper_group])) {
        //inpage_print3_0[]
        foreach ($_POST as $ItemKey => $ItemValue) 
        {
            if (strpos($ItemKey, "inpage_print3_") !== false)
            {
                $postArr = split('_', $ItemKey);
                $result = makeOptionCheckTag_Print3(getOptionPrint($print_goods_item[$goods_no], "inpage_print"), $_POST[$ItemKey], "inpage", $postArr[2]);
                $response[$ItemKey] = $result;
            }
        }
    }
    else {
        $result = makeOptionCheckTag_Print3(getOptionPrint($print_goods_item[$goods_no], "inpage_print"), "", "inpage");
        //$response[inpage_print3][] = $result;
        $response[inpage_print3_0] = $result;
    }


    $outside_gloss = getOptionData($print_goods_item[$goods_no]['outside_gloss'], "outside_gloss");
    $response[outside_gloss] = makeOptionInputTag($outside_gloss, "outside_gloss", $_POST['outside_gloss'], "outside_gloss", "outside_gloss", "", "radio");

    //$outside_sc = getOptionData($print_goods_item[$goods_no]['outside_sc'], "outside_sc");
    //$response[outside_sc] = makeOptionInputTag($outside_sc, "outside_sc", $_POST['outside_sc'], "outside_sc", "outside_sc", "", "radio");

    //$outside_scb = getOptionData($print_goods_item[$goods_no]['outside_scb'], "outside_scb");
    //$response[outside_scb] = makeOptionInputTag($outside_scb, "outside_scb", $_POST['outside_scb'], "outside_scb", "outside_scb", "", "radio");

    $outside_wing = getOptionData($print_goods_item[$goods_no]['outside_wing'], "outside_wing");
    $response[outside_wing] = makeOptionSelectTag($outside_wing, $_POST['outside_wing']);

    foreach ($print_goods_item[$goods_no] as $OptionKey => $OptionValue) {
        if ($OptionKey == "size" || $OptionKey == "paper" || $OptionKey == "print") continue;
        if ((strpos($OptionKey, "outside_") !== false) || (strpos($OptionKey, "inside_") !== false) || (strpos($OptionKey, "inpage_") !== false)) continue; 

        //다이렉트 파일업로드 사용여부, 건수 사용여부 추가.
        if ($OptionKey == "directupload_use" || $OptionKey == "cnt_use")
        {
            $result = $OptionValue;
        }
        else 
        {
            $result = getOptionData($OptionValue, $OptionKey);
    
            if ($OptionKey == "instant" || $OptionKey == "wing")
                $result = makeOptionSelectTag($result, $_POST['opt_'.$OptionKey]);
            else if ($OptionKey == "scb" || $OptionKey == "foil") //체크박스 -스코딕스 박, 박. 
                $result = makeOptionInputTag($result, $OptionKey, $_POST['opt_'.$OptionKey], $OptionKey, "opt_".$OptionKey."[]", "", "checkbox");
            else
                $result = makeOptionInputTag($result, $OptionKey, $_POST['opt_'.$OptionKey], $OptionKey, "opt_".$OptionKey, "", "radio");
        }
        //$response[$OptionKey] = $result;
        $response['opt_'.$OptionKey] = $result;
    }

    

//debug($response);
	echo json_encode($response);
?>