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

$opt_mode = $_GET[opt_mode];
$opt_group = $_GET[opt_group];
$opt_prefix = $_GET[opt_prefix];
$opt_goods_kind = $_GET[opt_goods_kind];

//$downloadFilename = date('Y-m-d') . "-$cfg_center[center_cid]-$_GET[filename]"._("-옵션가격관리").".xls";
$downloadFilename = date('Y-m-d') . "-$cid-$_GET[filename]"._("-옵션가격관리").".xls";

//단위 타이틀
$unit_title = $r_ipro_opt_mode_paper_unit[$opt_mode];
if (!$unit_title) $unit_title = "수량(장)";

//규격 정보 조회.
//if (($opt_mode == "print_digital" || $opt_mode == "print_book_inside_digital") && $opt_group == "PRINT")			//인쇄비 설정시는 기본 규격만 나온다.		전체규격 하기로 결정		20180608
//	$addWhere = " and (concat(opt_prefix,opt_key) = 'A3' or concat(opt_prefix,opt_key) = 'B2')";


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
//디지털 인쇄의 경우 흑밸, 컬러 인쇄 가격 테이블만 필요               20180517        chunter 
if (($opt_mode == "print_digital" || $opt_mode == "print_book_inside_digital") && $opt_group == "PRINT")
{
	//$addWhere = " and (opt_prefix = 'ET' or opt_key in (1,2,3))";
	//디지털 인쇄와 옵셋인쇄에서 사용하는 별색 코드가 다르다.				20180615
	$addWhere = " and ((opt_prefix = 'ET' and opt_key in ('1','2','3','4')) or (concat(opt_prefix,opt_key) = 'OC1' or concat(opt_prefix,opt_key) = 'OB2' or concat(opt_prefix,opt_key) = 'OB3'))";
}



//1장이사 스티커 도무송의 경우 도무송 사용만 필요               20180523        chunter 
if ($opt_mode == "domoo_sticker_digital" || $opt_mode == "domoo_sticker_other_digital")
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

//아이템 정보 조회.
if ($opt_mode != "print_opset") {
	$item_data = GetItem($opt_group, $addWhere);
	//debug($item_data);
}

//옵셋 인쇄비 설정이면...
if($opt_mode == "print_opset") {
    //규격 정보 조회.
    $size_data = GetOpsetSize($opt_mode);
    //debug($size_data);
    
    //아이템 정보 조회.
    //$item_data = GetItem("PRINT", " and (opt_prefix = 'ET' or opt_key in (1,2))");  
    //디지털 인쇄와 옵셋인쇄에서 사용하는 별색 코드가 다르다.				20180615  
    $addWhere = " and ((opt_prefix = 'ET' and opt_key in ('5','6','7','8','9','10')) or (concat(opt_prefix,opt_key) = 'OC1' or concat(opt_prefix,opt_key) = 'OB2'))";
		$item_data = GetItem("PRINT", $addWhere);
    
    //debug($item_data);
    
//스티커 한장의 경우  도무송 전용 스티커  규격 설정 필요    
} else if($opt_mode == "domoo_sticker_digital" || $opt_mode == "domoo_sticker_square_digital") {
    //규격 정보 조회.
    //$size_data = GetItem("SIZE", " and (opt_prefix = 'SC' or opt_prefix = 'SE' or opt_prefix = 'SR')");
  //도무송 가격 설정 규격은 별도 관리한다.			20180530		chunter
	$size_data = GetItem("CSIZE", " and opt_prefix = 'SCS'");    

//스티커 여러장의  A3 규격만 필요
} else if($opt_mode == "domoo_sticker_other_digital") {
    //규격 정보 조회.
    $size_data = GetItem("SIZE", " and opt_prefix = 'A' and opt_key = '3'");
}

//테이블 타이틀 정보.
$itemTitleDataArr = GetItemTitleDataArr($size_data, $item_data);
//debug($itemTitleDataArr);

//테이블 가격 정보.
$itemPriceDataArr = GetItemPriceDataArr($opt_mode, $size_data, $item_data);
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
$objPHPExcel -> getActiveSheet() -> mergeCells('A1:A2');
$objPHPExcel -> getActiveSheet() -> getRowDimension(1) -> setRowHeight(30);
$objPHPExcel -> getActiveSheet() -> getRowDimension(2) -> setRowHeight(30);
//$objPHPExcel -> getActiveSheet() -> getStyle('A1') -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setARGB('FF000FF');

$xlsColIndexChar = 0;
foreach ($itemTitleDataArr as $itemKey => $itemValue) {
    //debug($itemKey);
    //debug($itemValue);
    
    //옵셋 인쇄비 설정이면...명칭 변경 : 단면4도(앞컬러)=>컬러 1도당 / 20180726 / kdk
    if($opt_mode == "print_opset") {
        $itemValue[value] = str_replace("단면4도(앞컬러)", "컬러 1도당", $itemValue[value]);
    }
    
    $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName($xlsColIndexChar + 1) . 1, $itemValue[value]);
    $mergeStart = getExcelColName($xlsColIndexChar + 1);
    foreach ($itemValue[size] as $key => $val) {
        //debug($key);
        //debug($val);
        //$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName($xlsColIndexChar + 1) . 2, $val[key]."\n[".$itemKey ."_". $val[key]."]");
        
        $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName($xlsColIndexChar + 1) . 2, $val[value]."\n[".$itemKey ."_". $val[key]."]");
        
        //내용 줄 바꿈
        $objPHPExcel -> getActiveSheet() -> getStyle(getExcelColName($xlsColIndexChar + 1) . 2) -> getAlignment() -> setWrapText(TRUE);
        
        $mergeEnd = getExcelColName($xlsColIndexChar + 1);
        $xlsColIndexChar++;
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
            $objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName($xlsColIndexChar + 1) . $xlsRowIndex, $v);
            $xlsColIndexChar++;
        }
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