<?
/*
* @date : 20180419
* @author : kdk
* @brief : 자동견적 옵션 할인율 셋팅
* @desc : 후가공옵션 할인율
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

$mode = "item_dc_update";
$title = "할인율설정";
$url = "";

/*
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
*/

$m_print = new M_print();
//규격 정보 조회.
//$size_data = $m_print->getOptionSizeList();

//A1 규격은 나오면 안된다.
$addWhere = " and (opt_prefix = 'A' or opt_prefix = 'B' or opt_prefix = 'C') and (opt_key != '1')";
$size_data = GetSize($addWhere);
	
//debug($size_data);

//아이템 정보 조회.
$addWhere = " and ((opt_prefix = 'ET' and opt_key in ('1','2','3','4')) or (concat(opt_prefix,opt_key) = 'OC1' or concat(opt_prefix,opt_key) = 'OB2' or concat(opt_prefix,opt_key) = 'OB3'))";
$item_data = $m_print->getOptionItemsList($addWhere);
//$item_data = $m_print->getOptionItemsList("and opt_group = '$opt_group'");
//debug($item_data);

//테이블 타이틀 정보.
$itemTitleDataArr = GetItemTitleDataArr($size_data, $item_data);

//테이블 가격 정보.
$itemPriceDataArr = GetItemDcDataArr($opt_mode, $size_data, $item_data);
//debug($itemPriceDataArr);

//테이블 타이틀 tag.
$tableTitleContent = GetItemDcTitleDataTag($itemTitleDataArr);
//debug($tableTitleContent);

//테이블 가격 tag.
if ($bExcelImport) {
    //엑셀에서 가격정보 읽어오기
    $tablePriceContent = GetItemDcDataXlsTag($excelImportFileName);
}
else {
    $tablePriceContent = GetItemDcDataTag($itemPriceDataArr);
}
//debug($tablePriceContent);
?>

<? include '_option_print_html.php'; ?>

<script>
$(".xls_div").hide();
</script>

<? include_once "../_pfooter.php"; ?>