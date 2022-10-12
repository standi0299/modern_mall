<?
/*
* @date : 20180411
* @author : kdk
* @brief : 일반 인쇄 설정 명함,스티커 가격 설정
* @desc :
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
$goodsno = $_GET[goodsno];

$mode = "normal_price_update";
$title = "가격설정";
$url = "option_normal_price_excel.php?opt_mode=$opt_mode&opt_group=$opt_group&opt_prefix=$opt_prefix&filename=$opt_mode";

if($goodsno) $url .= "&goodsno=$goodsno";

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

//debug($print_goods_item[$opt_mode]);
/*
    //일반 명함
    "NM_NAMECARD" => array(
        "size" => Array ("SZ1","SZ2"),
        "paper" => Array ("PLU02" => Array ("221"), "PLU03" => Array ("250")),
        "print" => Array ( "OC1", "DC1" ),
        "gloss" => Array ( "CT1", "CT2", "CT3", "CT4"),
        "round" => Array ( "RD1", "RD2", "RD3" ),
        "domoo" => Array ( "DM2" ),
        "instant" => Array ( "IN2" )        
    ),
    
    "NM_STICKER" => array(
        "size" => Array ("SZ1","SZ2"),
        "paper" => Array ("PLU02" => Array ("221"), "PLU03" => Array ("250")),
        "print" => Array ( "OB1"),
        "gloss" => Array ( "CT1", "CT2"),
        "domoo" => Array ( "DM2" ),     
    ),
*/

//규격 ,아이템 정보 조회.
$print_item = GetNormalItem($opt_group, $goodsno);
//debug($print_item);

//엑셀파일명.
//$filename = $opt_mode ."_". $opt_group;
$filename = $opt_mode;

//테이블 타이틀 정보.
$itemTitleDataArr = GetNormalItemTitleDataArr($print_item);
//debug($itemTitleDataArr);

//테이블 가격 정보.
$itemPriceDataArr = GetNormalItemPriceDataArr($opt_mode, $print_item);
//debug($itemPriceDataArr);

//테이블 타이틀 tag.
$tableTitleContent = GetNormalItemTitleDataTag($itemTitleDataArr, $opt_mode);

//테이블 가격 tag.
if ($bExcelImport) {
    //엑셀에서 가격정보 읽어오기
    $tablePriceContent = GetItemPriceDataXlsTag($excelImportFileName);
}
else {
    $tablePriceContent = GetNormalItemPriceDataTag($itemPriceDataArr);
}
//debug($tablePriceContent);
?>

<? include '_option_print_html.php'; ?>

<? include_once "../_pfooter.php"; ?>