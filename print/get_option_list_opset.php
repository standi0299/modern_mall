<? 
	include_once 'lib_print.php';


	$mode = $_POST[mode];
	$goods_no = $_POST[goods_no];


	$result = getOptionSize($print_goods_item[$goods_no]);
	$result = makeOptionSelectTag($result, $_POST['opt_size']);
	$response[opt_size] = $result;

	
	$result = getOptionPaperGroup($print_goods_item[$goods_no]);
	$result = makeOptionSelectTag($result, $_POST['opt_paper_group']);
	$response[opt_paper_group] = $result;
	
	
	$result = getOptionPaper($print_goods_item[$goods_no], $_POST[opt_paper_group]);
	$result = makeOptionSelectTag($result, $_POST['opt_paper']);
	$response[opt_paper] = $result;
	
	
	$result = getOptionPaperGram($print_goods_item[$goods_no], $_POST[opt_paper]);
	$result = makeOptionSelectTag($result, $_POST['opt_paper_gram']);
	$response[opt_paper_gram] = $result;
	
	
	$result = getOptionPrint($print_goods_item[$goods_no], "print_front");
	$result = makeOptionSelectTag($result, $_POST['opt_print_front']);
	$response[opt_print_front] = $result;
	$result = getOptionPrint($print_goods_item[$goods_no], "print_back");
	$result = makeOptionSelectTag($result, $_POST['opt_print_back'],"없음");
	$response[opt_print_back] = $result;
	
	//인쇄 별색 추가 항목인경우 별색 select 가져온다.
	if ($_POST['opt_print_front'] == "OB4" || $_POST['opt_print_front'] == "OC5" || $_POST['opt_print_front'] == "DB4" || $_POST['opt_print_front'] == "DC5")
	{
		$result = getOptionPrint($print_goods_item[$goods_no], "print_front_etc");
		$result = makeOptionSelectTag($result, $_POST['opt_print_front_etc']);
		$response[opt_print_front_etc] = $result;
	}
	if ($_POST['opt_print_back'] == "OB4" || $_POST['opt_print_back'] == "OC5" || $_POST['opt_print_back'] == "DB4" || $_POST['opt_print_back'] == "DC5")
	{
		$result = getOptionPrint($print_goods_item[$goods_no], "print_back_etc");
		$result = makeOptionSelectTag($result, $_POST['opt_print_back_etc']);
		$response[opt_print_back_etc] = $result;
	}
	
	
	$result = getOptionGloss($print_goods_item[$goods_no]);
	$result = makeOptionSelectTag($result, $_POST['opt_gloss'], "코팅안함");
	$response[opt_gloss] = $result;
	
	
	$result = getOptionPunch($print_goods_item[$goods_no]);
	$result = makeOptionSelectTag($result, $_POST['opt_punch'], "타공없음");
	$response[opt_punch] = $result;
	
	
	$result = getOptionOshi($print_goods_item[$goods_no]);
	$result = makeOptionSelectTag($result, $_POST['opt_oshi'], "오시안함");
	$response[opt_oshi] = $result;
	

	echo json_encode($response);

?>