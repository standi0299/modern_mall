<?
/*
* @date : 20180404
* @author : kdk
* @brief : 일반 인쇄 설정 명함,스티커 가격을 엑셀파일로 저장.
* @desc : 
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
$goodsno = $_GET[goodsno];

//엑셀파일명.
//$downloadFilename = date('Y-m-d') . "-$cfg_center[center_cid]-$_GET[filename]"._("-옵션가격관리").".xls";
$downloadFilename = date('Y-m-d') . "-$cid-$_GET[filename]"._("-옵션가격관리").".xls";

//규격 ,아이템 정보 조회.
$print_item = GetNormalItem($opt_group, $goodsno);
//debug($print_item);

//테이블 타이틀 정보.
$itemTitleDataArr = GetNormalItemTitleDataArr($print_item);
//debug($itemTitleDataArr);

//테이블 가격 정보.
$itemPriceDataArr = GetNormalItemPriceDataArr($opt_mode, $print_item);
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

$objPHPExcel -> getActiveSheet() -> getColumnDimension('A') -> setWidth(20);
$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(0) . 1, "수량(장)");
$objPHPExcel -> getActiveSheet() -> mergeCells('A1:A2');
$objPHPExcel -> getActiveSheet() -> getRowDimension(1) -> setRowHeight(50);
$objPHPExcel -> getActiveSheet() -> getRowDimension(2) -> setRowHeight(50);
//$objPHPExcel -> getActiveSheet() -> getStyle('A1') -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setARGB('FF000FF');

$xlsColIndexChar = 0;
foreach ($itemTitleDataArr as $k1 => $v1) {
    //debug($itemKey);
    //debug($itemValue);

    foreach ($v1 as $k2 => $v2) {
    		$optGroupName = getOptionKeyToOptionName($k1);
					if ($optGroupName) $optGroupName = "[$optGroupName]";
        $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName($xlsColIndexChar + 1) . 1, $v2[value].$optGroupName. ($k2=="0" ? "" : "\n".$k2));
        $objPHPExcel -> getActiveSheet() -> getStyle(getExcelColName($xlsColIndexChar + 1) . 1) -> getAlignment() -> setWrapText(TRUE);
        
        $mergeStart = getExcelColName($xlsColIndexChar + 1);
        
        foreach ($v2[size] as $k3 => $v3) {
            //debug($k3);
            //debug($v3);            
            $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName($xlsColIndexChar + 1) . 2, $v3[value]."\n[".$k1 ."_". $k2 ."_". $v3[key]."]");
            //내용 줄 바꿈
            $objPHPExcel -> getActiveSheet() -> getStyle(getExcelColName($xlsColIndexChar + 1) . 2) -> getAlignment() -> setWrapText(TRUE);
            
            $mergeEnd = getExcelColName($xlsColIndexChar + 1);
            $xlsColIndexChar++;
        }        
    }
    
    //옵션그룹(opt_group) merge
    $objPHPExcel -> getActiveSheet() -> mergeCells($mergeStart . '1:' . $mergeEnd . '1');
}

//debug($itemPriceDataArr);
$xlsRowIndex = 3;
$xlsColIndex = 1;
foreach ($itemPriceDataArr as $itemKey => $itemValue) {
    $xlsColIndexChar = 0;
	//debug($itemKey);
	//debug($itemValue);	
    $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName($xlsColIndexChar) . $xlsRowIndex, $itemKey);
    
    foreach ($itemValue as $key => $val) {
        //debug($key);
        //debug($val);
        foreach ($val as $k => $v) {
            //debug($k);
            //debug($v);
            foreach ($v as $k1 => $v1) {
                $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName($xlsColIndexChar + 1) . $xlsRowIndex, $v1);
                $xlsColIndexChar++;
            }
        }
    }

	$xlsRowIndex++;
}
//debug($xlsRowIndex);
//debug($xlsColIndexChar);

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