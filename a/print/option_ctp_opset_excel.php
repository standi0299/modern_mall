<?
/*
* @date : 20180404
* @author : kdk
* @brief : 자동견적 옵션 가격을 엑셀파일로 저장.
* @desc : CTP(판비)
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

//엑셀파일명.
$downloadFilename = date('Y-m-d') . "-$cfg_center[center_cid]-$_GET[filename]"._("-옵션가격관리").".xls";

//가격 정보.
$priceDataArr = GetCtpOpsetDataArr();
//debug($priceDataArr);

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

//col width 20
$objPHPExcel -> getActiveSheet() -> getColumnDimension('A') -> setWidth(20);
$objPHPExcel -> getActiveSheet() -> getColumnDimension('B') -> setWidth(20);

$objPHPExcel -> getActiveSheet() -> getRowDimension(1) -> setRowHeight(30);

$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(0) . 1, "판");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(1) . 1, "가격");

//debug($priceDataArr);
$xlsRowIndex = 2;

foreach ($priceDataArr as $key => $val) {
    $xlsColIndexChar = 0;

    $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(0) . $xlsRowIndex, $val[cnt]);
    $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(1) . $xlsRowIndex, $val[val]);
}
//debug($xlsRowIndex);
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