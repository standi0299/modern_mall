<?
/*
* @date : 20180803
* @author : kdk
* @brief : 알래스카프린트용 옵션 리스트.
* @desc :
*/

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
    $result = makeOptionInputTag_Print1(getOptionPrint($print_goods_item[$goods_no]), $_POST['opt_print1'], $_POST['opt_print3_check'], "opt", "0", "select", $funcName);
    $response[opt_print1] = $result;
    
    //사용자 화면 양면단면 옵션 태그 만들기
    $result = makeOptionInputTag_Print2(getOptionPrint($print_goods_item[$goods_no]), $_POST['opt_print2'], "opt", "0", "select");
    $response[opt_print2] = $result;
    
    //사용자 화면 별색추가 옵션 태그 만들기
    $result = makeOptionCheckTag_Print3(getOptionPrint($print_goods_item[$goods_no]), $_POST['opt_print3'], "opt", "0", "select");
    $response[opt_print3] = $result;
    
    //알래스카 별색 사용여부 처리.
    if ($response[opt_print3] == "<option value=''>선택해주세요.</option>") $response[opt_print3_use] = "N";
    

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
            $result = makeOptionSelectTag($result, $_POST['opt_'.$OptionKey]);
        }    
        $response['opt_'.$OptionKey] = $result;
    }

    //debug($response);exit;
	echo json_encode($response);
?>