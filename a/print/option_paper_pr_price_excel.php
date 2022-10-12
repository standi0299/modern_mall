<?
/*
* @date : 20180404
* @author : kdk
* @brief : 자동견적 옵션 가격을 엑셀파일로 저장.
* @desc : 후가공옵션
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
$downloadFilename = date('Y-m-d') . "-$cfg_center[center_cid]-"._("paper_pr-가격관리").".xls";

//지류 정보 조회.
$paper_data = GetPaper("and opt_prefix in ('EPR')");
//debug($paper_data);

//가격 정보.
$paperPriceDataArr = GetPaperPrPriceDataArr($paper_data);
//debug($paperPriceDataArr);

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
$objPHPExcel -> getActiveSheet() -> getColumnDimension('C') -> setWidth(20);
$objPHPExcel -> getActiveSheet() -> getColumnDimension('D') -> setWidth(20);
$objPHPExcel -> getActiveSheet() -> getColumnDimension('E') -> setWidth(20);
$objPHPExcel -> getActiveSheet() -> getColumnDimension('F') -> setWidth(20);
$objPHPExcel -> getActiveSheet() -> getColumnDimension('G') -> setWidth(20);
$objPHPExcel -> getActiveSheet() -> getColumnDimension('H') -> setWidth(20);
$objPHPExcel -> getActiveSheet() -> getColumnDimension('I') -> setWidth(20);
$objPHPExcel -> getActiveSheet() -> getColumnDimension('J') -> setWidth(20);
$objPHPExcel -> getActiveSheet() -> getColumnDimension('K') -> setWidth(20);
$objPHPExcel -> getActiveSheet() -> getColumnDimension('L') -> setWidth(20);
$objPHPExcel -> getActiveSheet() -> getColumnDimension('M') -> setWidth(20);

$objPHPExcel -> getActiveSheet() -> getRowDimension(1) -> setRowHeight(30);
$objPHPExcel -> getActiveSheet() -> getRowDimension(2) -> setRowHeight(30);

$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(0) . 1, "지류");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(1) . 1, "");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(2) . 1, "평량 (무게g)");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(3) . 1, "두께 (mm)");

$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(4) . 1, "헤배(1000x1000)\n[SPR1]");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(5) . 1, "A1(841x594)\n[SPR2]");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(6) . 1, "A2(420x594)\n[SPR3]");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(7) . 1, "A3(297x420)\n[SPR4]");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(8) . 1, "A4(210x297)\n[SPR5]");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(9) . 1, "B2(515x728)\n[SPR6]");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(10) . 1, "B3(364x515)\n[SPR7]");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(11) . 1, "B4(257x364)\n[SPR8]");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(12) . 1, "B5(176x250)\n[SPR9]");

$objPHPExcel -> getActiveSheet() -> getStyle(getExcelColName(4) . 1) -> getAlignment() -> setWrapText(TRUE);
$objPHPExcel -> getActiveSheet() -> getStyle(getExcelColName(5) . 1) -> getAlignment() -> setWrapText(TRUE);
$objPHPExcel -> getActiveSheet() -> getStyle(getExcelColName(6) . 1) -> getAlignment() -> setWrapText(TRUE);
$objPHPExcel -> getActiveSheet() -> getStyle(getExcelColName(7) . 1) -> getAlignment() -> setWrapText(TRUE);
$objPHPExcel -> getActiveSheet() -> getStyle(getExcelColName(8) . 1) -> getAlignment() -> setWrapText(TRUE);
$objPHPExcel -> getActiveSheet() -> getStyle(getExcelColName(9) . 1) -> getAlignment() -> setWrapText(TRUE);
$objPHPExcel -> getActiveSheet() -> getStyle(getExcelColName(10) . 1) -> getAlignment() -> setWrapText(TRUE);
$objPHPExcel -> getActiveSheet() -> getStyle(getExcelColName(11) . 1) -> getAlignment() -> setWrapText(TRUE);
$objPHPExcel -> getActiveSheet() -> getStyle(getExcelColName(12) . 1) -> getAlignment() -> setWrapText(TRUE);

$objPHPExcel -> getActiveSheet() -> mergeCells('A1:B1');

$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(0) . 2, "분류");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(1) . 2, "용지명");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(2) . 2, "");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(3) . 2, "");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(4) . 2, "");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(5) . 2, "");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(6) . 2, "");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(7) . 2, "");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(8) . 2, "");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(9) . 2, "");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(10) . 2, "");
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(11) . 2, "");

$objPHPExcel -> getActiveSheet() -> mergeCells('C1:C2');
$objPHPExcel -> getActiveSheet() -> mergeCells('D1:D2');
$objPHPExcel -> getActiveSheet() -> mergeCells('E1:E2');
$objPHPExcel -> getActiveSheet() -> mergeCells('F1:F2');
$objPHPExcel -> getActiveSheet() -> mergeCells('G1:G2');
$objPHPExcel -> getActiveSheet() -> mergeCells('H1:H2');
$objPHPExcel -> getActiveSheet() -> mergeCells('I1:I2');
$objPHPExcel -> getActiveSheet() -> mergeCells('J1:J2');
$objPHPExcel -> getActiveSheet() -> mergeCells('K1:K2');
$objPHPExcel -> getActiveSheet() -> mergeCells('L1:L2');
$objPHPExcel -> getActiveSheet() -> mergeCells('M1:M2');

//debug($paperPriceDataArr);
$xlsRowIndex = 3;

foreach ($paperPriceDataArr as $key => $val) {
    $xlsColIndexChar = 0;
    
    foreach ($val[paper] as $k1 => $v1) {

        $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(0) . $xlsRowIndex, $val[group]."\n[".$key ."_". $k1."]");
        $objPHPExcel -> getActiveSheet() -> getStyle(getExcelColName(0) . $xlsRowIndex) -> getAlignment() -> setWrapText(TRUE);
        
        $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(1) . $xlsRowIndex, $val[name]);

        $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(2) . $xlsRowIndex, $k1);
        $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(3) . $xlsRowIndex, $v1[width]);
        $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(4) . $xlsRowIndex, $v1[SPR1]);
        $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(5) . $xlsRowIndex, $v1[SPR2]);
        $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(6) . $xlsRowIndex, $v1[SPR3]);
        $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(7) . $xlsRowIndex, $v1[SPR4]);        
        $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(8) . $xlsRowIndex, $v1[SPR5]);
        $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(9) . $xlsRowIndex, $v1[SPR6]);
        $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(10) . $xlsRowIndex, $v1[SPR7]);
        $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(11) . $xlsRowIndex, $v1[SPR8]);
        $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(12) . $xlsRowIndex, $v1[SPR9]);

        $xlsRowIndex++;
    }
    
    //옵션그룹(opt_group) merge
    //$objPHPExcel -> getActiveSheet() -> mergeCells($mergeStart . '1:' . $mergeEnd . '1');
    
    //$xlsRowIndex++;
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