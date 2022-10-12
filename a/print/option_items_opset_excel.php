<?
/*
* @date : 20180723
* @author : kdk
* @brief : 자동견적 옵션 가격을 엑셀파일로 저장.
* @desc : 후가공옵션(옵셋)
*/
?>
<?
include_once "../lib.php";
include_once '../../print/lib_const_print.php';
include_once '../../print/lib_util_print.php';
include_once '../../lib/PHPExcel.php';
include_once '../../lib/PHPExcel/Writer/Excel2007.php';
include 'lib_util_print_admin.php';

set_time_limit(360);
ini_set('memory_limit', '-1');
//테스트용 임시 메모리 제한 풀기 2014.07.10 by kdk
$this_time = get_time();

$opt_mode = $_GET[opt_mode];
$opt_group = $_GET[opt_group];
$opt_prefix = $_GET[opt_prefix];
$opt_goods_kind = $_GET[opt_goods_kind];

//$downloadFilename = date('Y-m-d') . "-$cfg_center[center_cid]-$_GET[filename]"._("-옵션가격관리").".xls";
$downloadFilename = date('Y-m-d') . "-$cid-$_GET[filename]"._("-옵션가격관리").".xls";

//단위 타이틀
//$unit_title = $r_ipro_opt_mode_paper_unit[$opt_mode];
if (!$unit_title) $unit_title = "연당(1mm당)";

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
if ($opt_mode != "print_opset") {
	$item_data = GetItem($opt_group, $addWhere);
	//debug($item_data);
}

//테이블 타이틀 정보.
$itemTitleDataArr = GetOpsetItemTitleDataArr($item_data);
//debug($itemTitleDataArr);

//테이블 가격 정보.
$itemPriceDataArr = GetOpsetItemPriceDataArr($opt_mode, $item_data);
//debug($itemPriceDataArr);

$xlsSaveOpton = $sizePriceArr;
$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel -> getProperties() -> setCreator("chunter");
$objPHPExcel -> getProperties() -> setLastModifiedBy("chunter");
$objPHPExcel -> getProperties() -> setTitle(_("블루팟 자동견적 옵션 가격 설정 문서"));
$objPHPExcel -> getProperties() -> setSubject(_("블루팟 자동견적 옵션 가격 설정 문서"));
//$objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

$objPHPExcel -> getDefaultStyle() -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel -> getDefaultStyle() -> getAlignment() -> setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

//폰트 사이즈 10
$objPHPExcel -> getDefaultStyle() -> getFont() -> setSize(10);

// Add some data
$objPHPExcel -> setActiveSheetIndex(0);

$objPHPExcel -> getActiveSheet() -> getColumnDimension('A') -> setWidth(15);
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(0) . 1, $unit_title);
//$objPHPExcel -> getActiveSheet() -> mergeCells('A1:A2');
$objPHPExcel -> getActiveSheet() -> getRowDimension(1) -> setRowHeight(30);
//$objPHPExcel -> getActiveSheet() -> getRowDimension(2) -> setRowHeight(30);
//$objPHPExcel -> getActiveSheet() -> getStyle('A1') -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setARGB('FF000FF');

$xlsColIndexChar = 0;
foreach ($itemTitleDataArr as $itemKey => $itemValue) {
    //debug($itemKey);
    //debug($itemValue);

    $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName($xlsColIndexChar + 1) . 1, $itemValue[value]."\n[".$itemKey."]");

    $xlsColIndexChar++;
    //옵션그룹(opt_group) merge
    //$objPHPExcel -> getActiveSheet() -> mergeCells($mergeStart . '1:' . $mergeEnd . '1');
}

//debug($itemPriceDataArr);exit;
$xlsRowIndex = 2;
$xlsColIndex = 1;
foreach ($itemPriceDataArr as $itemKey => $itemValue) {
    $xlsColIndexChar = 0;
	//debug($itemKey);
	//debug($itemValue);
    $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName($xlsColIndexChar) . $xlsRowIndex, $itemKey);
    
    foreach ($itemValue as $key => $val) {
        //debug($key);
        //debug($val);
        $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName($xlsColIndexChar + 1) . $xlsRowIndex, $val);
        $xlsColIndexChar++;
    }

	$xlsRowIndex++;
}
//debug($xlsRowIndex);
//debug($xlsColIndexChar);
//exit;

$saveFilename = 'option_price.xls';
//파일 권한 바꾸기
@chmod($saveFilename, 0777);

// Rename sheet
$objPHPExcel -> getActiveSheet() -> setTitle('Sheet1');

// Save Excel 2003 file
//$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
//$objWriter->save($saveFilename);

// Save Excel 2007 file
$filedir = '../../data/excel_temp/';

//폴더가 없을 경우 오류가 발생한다.
if (!is_dir($filedir)) {
	mkdir($filedir, 0777);
	chmod($filedir, 0777);
}

$downloadFilename = str_replace('.xls', '.xlsx', $downloadFilename);
$saveFilename = str_replace('.xls', '.xlsx', $saveFilename);
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter -> save($filedir . $saveFilename);

//$objWriter->save('php://output');
//$objWriter->disconnectWorksheets();
unset($objWriter);
//exit;
$downloadFilename = urlencode($downloadFilename);
//iconv("UTF-8","cp949//IGNORE", $downloadFilename);
if (file_exists($filedir . $saveFilename)) {
	header('Content-Description: File Transfer');
	header('Content-Transfer-Encoding: binary');
	header('Content-Disposition: attachment;filename=' . $downloadFilename);
	header('Content-Type: application/vnd.ms-excel;charset=UTF-8;');
	header('Content-Length: ' . filesize($filedir . $saveFilename));
	header('Pragma: no-cache');
	header('Expires: 0');
	ob_clean();
	flush();
	@readfile($filedir . $saveFilename);
}
?>