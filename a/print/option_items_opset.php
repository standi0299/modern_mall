<?
/*
* @date : 20180723
* @author : kdk
* @brief : 자동견적 옵션 가격을 셋팅
* @desc : 후가공옵션 (옵셋)
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

$opt_mode2 = "";
switch ($opt_mode) {
	case 'domoo_opset':
		$opt_mode2 = "domoo_mm2_opset";
		break;
    case 'press_opset':
        $opt_mode2 = "press_mm2_opset";
        break;
    case 'foil_opset':
        $opt_mode2 = "foil_mm2_opset";
        break;
    case 'uvc_opset':
        $opt_mode2 = "uvc_mm2_opset";
        break;
    case 'bind_BD1_opset':
        $opt_mode2 = "bind_BD1";
        break;
    case 'bind_BD3_opset':
        $opt_mode2 = "bind_BD3";
        break;
}

$mode = "item_price_update";
$title = "가격설정";
$url = "option_items_opset_excel.php?opt_mode=$opt_mode&opt_group=$opt_group&opt_prefix=$opt_prefix&filename=$opt_mode&opt_goods_kind=$opt_goods_kind";

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

//규격 정보 조회.(사용안함)

//후가공들은 있는 경우만 가격 설정한다.
if($opt_group == "BIND") {
    if ($opt_mode == "bind_BD1_opset")    
        $addWhere = " and opt_key = '1'";
    else if ($opt_mode == "bind_BD3_opset") 
        $addWhere = " and opt_key = '3'";
}
else if($opt_group == "DOMOO" || $opt_group == "BARCODE" || $opt_group == "NUMBER" || $opt_group == "STAND" || $opt_group == "DANGLE" || $opt_group == "TAPE" || $opt_group == "ADDRESS" || $opt_group == "INSTANT" || $opt_group == "PRESS" || $opt_group == "FOIL" || $opt_group == "UVC")
    $addWhere = " and opt_key != '1'";
//debug($addWhere);

//아이템 정보 조회.
$item_data = GetItem($opt_group, $addWhere);
//debug($item_data);

//테이블 타이틀 정보.
$itemTitleDataArr = GetOpsetItemTitleDataArr($item_data);
//debug($itemTitleDataArr);

//테이블 가격 정보.
$itemPriceDataArr = GetOpsetItemPriceDataArr($opt_mode, $item_data);
//debug($itemPriceDataArr);

//테이블 타이틀 tag.
$tableTitleContent = GetOpsetItemTitleDataTag($itemTitleDataArr, $opt_mode);
//debug($tableTitleContent);

//테이블 가격 tag.
if ($bExcelImport) {
    //엑셀에서 가격정보 읽어오기
    $tablePriceContent = GetOpsetItemPriceDataXlsTag($excelImportFileName);
}
else {
    $tablePriceContent = GetOpsetItemPriceDataTag($itemPriceDataArr);
}
//debug($tablePriceContent);

//옵셋 후가공 예외.
if ($opt_mode == "domoo_opset" || $opt_mode == "press_opset" || $opt_mode == "foil_opset" || $opt_mode == "uvc_opset") {
    if (file_exists("../../data/print/goods_items/". $cid ."_". $opt_mode2 .".php"))
    {
        include_once "../../data/print/goods_items/". $cid ."_". $opt_mode2 .".php";
        $file_data = json_decode(${"r_ipro_".$opt_mode2}, 1);
    }
}
else if ($opt_mode == "bind_BD1_opset" || $opt_mode == "bind_BD3_opset") 
{
    if (file_exists("../../data/print/goods_items/". $cid ."_". $opt_mode2 ."_default.php"))
    {
        include_once "../../data/print/goods_items/". $cid ."_". $opt_mode2 ."_default.php";
        $default_data = json_decode(${"r_ipro_".$opt_mode2."_default"}, 1);
        
        include_once "../../data/print/goods_items/". $cid ."_". $opt_mode2 ."_page_gram.php";
        $page_gram_data = json_decode(${"r_ipro_".$opt_mode2."_page_gram"}, 1);
    }    
}


?>

<? include '_option_print_html.php'; ?>

<? include_once "../_pfooter.php"; ?>