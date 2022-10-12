<?
/*
* @date : 20180411
* @author : kdk
* @brief : 자동견적 옵션 가격을 셋팅
* @desc : 후가공옵션
*/
?>
<?
include_once '../_pheader.php';
include_once '../../print/lib_const_print.php';
include_once '../../print/lib_util_print.php';
include "../../lib/class.excel.reader.php";
include 'lib_util_print_admin.php';

//set_time_limit(360);
//ini_set('memory_limit', '-1');
//테스트용 임시 메모리 제한 풀기 2014.07.10 by kdk
$this_time = get_time();

$opt_mode = $_GET[opt_mode];
$opt_group = $_GET[opt_group];
$opt_prefix = $_GET[opt_prefix];
$opt_goods_kind = $_GET[opt_goods_kind];

$mode = "item_price_update";
$title = "가격설정";
$url = "option_items_price_excel.php?opt_mode=$opt_mode&opt_group=$opt_group&opt_prefix=$opt_prefix&filename=$opt_mode&opt_goods_kind=$opt_goods_kind";

//자동견적 가격 엑셀 파일 처리
$bExcelImport = false;
if ($_FILES['excelFile']['name']) {
    $uploaddir = '../../data/excel_temp/';

    if (!is_dir($uploaddir)) {
        mkdir($uploaddir, 0707);
    } else
        @chmod($uploaddir, 0707);
    //$uploadfile = $uploaddir.basename($_FILES['excelFile']['name']);

    $ext = explode(".", $_FILES['excelFile']['name']);

    $name = time() . "." . $ext[1];
    //move_uploaded_file($_FILES[img][tmp_name][$k],$uploaddir.$name);

    move_uploaded_file($_FILES[excelFile][tmp_name], $uploaddir . $name);
    
    //echo '<pre>';
    //if (move_uploaded_file($_FILES[excelFile][tmp_name],$uploaddir.$name)) { //move_uploaded_file($_FILES['excelFile']['tmp_name'], $uploadfile)
    //    echo "파일이 유효하고, 성공적으로 업로드 되었습니다.\n";
    //} else {
    //    print "파일 업로드 공격의 가능성이 있습니다!\n";
    //}

    //echo '자세한 디버깅 정보입니다:';
    //print_r($_FILES);
    //print_r($uploaddir.$name);
    //print "</pre>";
    
    $bExcelImport = true;
    $excelImportFileName = $uploaddir . $name;
}

//규격 정보 조회.
//if (($opt_mode == "print_digital" || $opt_mode == "print_book_inside_digital") && $opt_group == "PRINT")			//인쇄비 설정시는 기본 규격만 나온다.		전체규격 하기로 ㄱ
//	$addWhere = " and (concat(opt_prefix,opt_key) = 'A3' or concat(opt_prefix,opt_key) = 'B2')";

//옵셋 후가공인겨우 A,B 규격만 나온다.
if ($opt_goods_kind == "opset")
{
	$addWhere = " and (opt_prefix = 'A' or opt_prefix = 'B')";
	$size_data = GetSize($addWhere);	
} else {
	$addWhere = " and (opt_prefix = 'A' or opt_prefix = 'B' or opt_prefix = 'C') and (opt_key != '1')";
	$size_data = GetSize($addWhere);
}
//debug($size_data);

$addWhere = "";
//디지털 인쇄의 경우 흑백, 컬러 인쇄 가격 테이블만 필요				20180517		chunter 
//디지털 윤전 추가 / 20181210 / kdk
if (($opt_mode == "print_digital" || $opt_mode == "print_book_inside_digital" || $opt_mode == "print_rotary_digital" || $opt_mode == "print_book_inside_rotary_digital") && $opt_group == "PRINT")
{
	//$addWhere = " and (opt_prefix = 'ET' or opt_key in (1,2,3))";
	//디지털 인쇄와 옵셋인쇄에서 사용하는 별색 코드가 다르다.				20180615
	$addWhere = " and ((opt_prefix = 'ET' and opt_key in ('1','2','3','4')) or (concat(opt_prefix,opt_key) = 'OC1' or concat(opt_prefix,opt_key) = 'OB2' or concat(opt_prefix,opt_key) = 'OB3'))";
}


//1장이사 스티커 도무송의 경우 도무송 사용만 필요				20180523		chunter 
if ($opt_mode == "domoo_sticker_other_digital")
{
	//$addWhere = " and opt_prefix = 'DM' and opt_key='2'";			//의미 없음. 아래에서 그룹으로 처리
}


//일반 후가공들은 있는 경우만 가격 설정한다. 
if ($opt_group == "SC" || $opt_group == "DOMOO" || $opt_group == "CUTTING" || $opt_group == "INSTANT" || $opt_group == "BARCODE" || $opt_group == "NUMBER" || $opt_group == "STAND" || $opt_group == "DANGLE" || $opt_group == "TAPE" || $opt_group == "ADDRESS" || $opt_group == "PRESS" || $opt_group == "UVC")
{
	$addWhere = " and opt_key='2'";
}

//디지털 제본은 제본안함만 빼면된다.
if ($opt_mode == "bind_digital")
	$addWhere = " and opt_key !='7'";

//옵셋 제본은 중철, 무선, 스프링  3가지만 사용
if ($opt_mode == "bind_opset")
	$addWhere = " and (opt_key ='1' or opt_key ='3' or opt_key ='5' or opt_key ='6')";

if($opt_mode == "foil_opset") 
	$addWhere = " and opt_key != '1'";

//현수막,실사출력에 '디자인 작업 포함'만 사용 
if($opt_mode == "design_etc") 
    $addWhere = " and opt_key != '1'";
	
//아이템 정보 조회.
if($opt_mode != "print_opset") {
	$item_data = GetItem($opt_group, $addWhere);
	//debug($item_data);
} 

//옵셋 인쇄비 설정이면...
if($opt_mode == "print_opset") {
    //규격 정보 조회.
    $size_data = GetOpsetSize($opt_mode);
    //debug($size_data);
    
    //아이템 정보 조회.
    //$item_data = GetOpsetItem($opt_mode);
		//$item_data = GetItem("PRINT", " and (opt_prefix = 'ET' or opt_key in (1,2))");		
		//디지털 인쇄와 옵셋인쇄에서 사용하는 별색 코드가 다르다.				20180615
		$addWhere = " and ((opt_prefix = 'ET' and opt_key in ('5','6','7','8','9','10')) or (concat(opt_prefix,opt_key) = 'OC1' or concat(opt_prefix,opt_key) = 'OB2'))";
		$item_data = GetItem("PRINT", $addWhere);		
    //debug($item_data);
    
//스티커 한장의 경우  도무송 전용 스티커  규격 설정 필요    
//사각 스티커도 같은 양식 사용. 		20180626		chunter
} else if($opt_mode == "domoo_sticker_digital" || $opt_mode == "domoo_sticker_square_digital") {
	//규격 정보 조회.
	//$size_data = GetItem("SIZE", " and (opt_prefix = 'SC' or opt_prefix = 'SE' or opt_prefix = 'SR')");
	//도무송 가격 설정 규격은 별도 관리한다.			20180530		chunter
	$size_data = GetItem("CSIZE", " and opt_prefix = 'SCS'");    

//스티커 여러장의  A3 규격만 필요
} else if($opt_mode == "domoo_sticker_other_digital") {
	//규격 정보 조회.
	$size_data = GetItem("SIZE", " and opt_prefix = 'A' and opt_key = '3'");

//현수막 실사출력은 opt_prefix=ET 규격만 사용.
} else if($opt_mode == "print_pr_PR01" || $opt_mode == "print_pr_PR02" || $opt_mode == "coating_pr" || $opt_mode == "processing_pr" || $opt_mode == "design_pr" || $opt_mode == "cut_pr") {
    //규격 정보 조회.
    $size_data = GetItem("SIZE", " and opt_prefix = 'SPR'");

    if($opt_mode == "print_pr_PR01" || $opt_mode == "print_pr_PR02") {
        $addWhere = " and (concat(opt_prefix,opt_key) = 'OC1')";
        $item_data = GetItem("PRINT", $addWhere);
    }
}

//테이블 타이틀 정보.
$itemTitleDataArr = GetItemTitleDataArr($size_data, $item_data);
//debug($itemTitleDataArr);

//테이블 가격 정보.
$itemPriceDataArr = GetItemPriceDataArr($opt_mode, $size_data, $item_data);
//debug($itemPriceDataArr);

//테이블 타이틀 tag.
$tableTitleContent = GetItemTitleDataTag($itemTitleDataArr, $opt_mode);
//debug($tableTitleContent);
//옵셋 인쇄비 설정이면...명칭 변경 : 단면4도(앞컬러)=>컬러 1도당 / 20180726 / kdk
if($opt_mode == "print_opset") {
    $tableTitleContent = str_replace("단면4도(앞컬러)", "컬러 1도당", $tableTitleContent);
}
//debug($tableTitleContent);

//테이블 가격 tag.
if ($bExcelImport) {
    //엑셀에서 가격정보 읽어오기
    $tablePriceContent = GetItemPriceDataXlsTag($excelImportFileName);
}
else {
    $tablePriceContent = GetItemPriceDataTag($itemPriceDataArr);
}
//debug($tablePriceContent);
?>

<? include '_option_print_html.php'; ?>

<? include_once "../_pfooter.php"; ?>