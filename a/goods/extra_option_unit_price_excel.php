<?
$viewpage = true;
$podspage = true;
//include "../_header.php";
//include "../lib/class.page.php";
include "../lib.php";
include "../../lib/extra_option/extra_option_price_proc.php";
include '../../lib/PHPExcel.php';
//include '../lib/PHPExcel/Writer/Excel2007.php';

set_time_limit(360);
ini_set('memory_limit', '-1');
//테스트용 임시 메모리 제한 풀기 2014.07.10 by kdk

$editorLayer = 1;

//$downloadFilename = date('Y-m-d'). '_수량_건수_가격정보.xls';

if ($_GET[goodsno]) {
	//$data = $db -> fetch("select * from exm_goods where goodsno='$_GET[goodsno]'");
	$mGoods = new M_goods();
	$data = $mGoods->getInfo($_GET[goodsno]);
	if (!$data) {
		msg(_("상품데이터가 존재하지 않습니다."), -1);
		exit ;
	}
}

if ($data) {
	$extra_option = explode('|', $data[extra_option]);
	//항목 분리
	if (count($extra_option) > 0) {
		$extra_product = $extra_option[0];
		$extra_preset = $extra_option[1];
		$extra_price_type = $extra_option[2];
		$goodsKind = $extra_product;
	}
}

$downloadFilename = date('Y-m-d') . "-$cfg_center[center_cid]-$data[goodsno]-$data[goodsnm]-"._("수량건수가격관리").".xls";

$adminExtraOption = new M_extra_option();
$unit_data = $adminExtraOption -> getOptionUnitPrice($cid, $cfg_center[center_cid], $_GET[goodsno]);
//debug($unit_data);

$unit_cnt_rule = $unit_data[unit_cnt_rule];
$unit_cnt_price = $unit_data[unit_cnt_price];

//debug($unit_cnt_rule);
//debug($unit_cnt_price);
//exit;

$OptionCnt = array();
$rowIndex = 0;
//엑셀정보를 읽기 위한 rowindex

//옵션 주문 수량 테이블 만들기

//내지의 경우 내지 출력 수량 규칙을 가격 기본 테이블로 셋팅
$PrintCntRule = array();
$PrintCntRule = explode(";", $unit_cnt_rule);

/*
 foreach ($PrintCntRule as $rootKey => $rootValue)
 {
 if ($rootValue)
 {
 $rootArr = split('~', $rootValue);
 if (sizeof($rootArr) > 2) {
 $rootArr = $rootArr[0]."~". $rootArr[1];
 }
 else {
 $rootArr = $rootValue;
 }

 //$OptionCnt[0][$rootValue]['suply'] = "$rootArr $r_est_item_price_type_title[$extra_price_type]";
 $OptionCnt[0][$rootValue]['suply'] = "$rootArr";
 $OptionCnt[0][$rootValue]['sale'] = "";

 if($cid == $cfg_center[center_cid]) {
 $OptionCnt[1][$rootValue]['suply'] = "공급가격";
 $OptionCnt[1][$rootValue]['sale'] = "권장판매가";
 }
 else {
 $OptionCnt[1][$rootValue]['suply'] = "공급원가";
 $OptionCnt[1][$rootValue]['sale'] = "판매가";
 }
 }
 }
 */

if ($cid == $cfg_center[center_cid]) {
	//$OptionCnt[0]['suply'] = "공급가격";
	//$OptionCnt[0]['sale'] = "권장판매가";

	$OptionCnt[0] = array('cnt' => _('수량(건수)'), 'suply' => _('공급가격'), 'sale' => _('권장판매가'));

} else {
	//$OptionCnt[0]['suply'] = "공급원가";
	//$OptionCnt[0]['sale'] = "판매가";

	$OptionCnt[0] = array('cnt' => _('수량(건수)'), 'suply' => _('공급원가'), 'sale' => _('판매가'));
}

//debug($unit_cnt_price);
if ($unit_cnt_price) {
	foreach (explode(";", $unit_cnt_price) as $key => $value) {
		if ($value) {
			$price_arr = explode("~", $value);

			if (sizeof($price_arr) > 3) {
				$OptionCnt[] = array('cnt' => $price_arr[0] . "~" . $price_arr[1], 'suply' => $price_arr[2], 'sale' => $price_arr[3]);
				//$OptionCnt[2][$price_arr[0]."~".$price_arr[1]] = array('cnt' => '수량(건수)', 'suply' => $price_arr[2], 'sale' => $price_arr[3]);
			} else {
				$OptionCnt[] = array('cnt' => $price_arr[0], 'suply' => $price_arr[1], 'sale' => $price_arr[2]);
				//$OptionCnt[2][$price_arr[0]] = array('suply' => $price_arr[1], 'sale' => $price_arr[2]);
			}
		}
	}
}

//debug($OptionCnt);
//exit;

$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel -> getProperties() -> setCreator("chunter");
$objPHPExcel -> getProperties() -> setLastModifiedBy("chunter");
$objPHPExcel -> getProperties() -> setTitle(_("블루팟 자동견적 옵션 가격 설정 문서"));
$objPHPExcel -> getProperties() -> setSubject(_("블루팟 자동견적 옵션 가격 설정 문서"));
//$objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

$objPHPExcel -> getDefaultStyle() -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel -> getDefaultStyle() -> getAlignment() -> setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
// Add some data
$objPHPExcel -> setActiveSheetIndex(0);

$xlsRowIndex = 1;
foreach ($OptionCnt as $itemKey => $itemValue) {
	$xlsColIndexChar = 0;

	/*
	 debug("itemKey:".$itemKey);
	 debug("xlsRowIndex:".$xlsRowIndex);
	 debug(getExcelColName($xlsColIndexChar) . $xlsRowIndex);
	 debug($OptionCnt[$itemKey]);
	 */
	//exit;

	foreach ($OptionCnt[$itemKey] as $cntKey => $cntValue) {
		//debug("itemKey:".$itemKey);
		/*
		 debug("---------------------");
		 debug("xlsRowIndex:".$xlsRowIndex);
		 debug("xlsColIndexChar:".$xlsColIndexChar);
		 debug(getExcelColName($xlsColIndexChar) . $xlsRowIndex);
		 debug("cntKey:".$cntKey);
		 debug("cntValue:".$cntValue);
		 debug("---------------------");
		 */
		//exit;

		$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName($xlsColIndexChar) . $xlsRowIndex, $cntValue);

		/*
		 $objPHPExcel->getActiveSheet()->SetCellValue(getExcelColName($xlsColIndexChar) . $xlsRowIndex, $cntValue['suply']);
		 $xlsColIndexChar++;

		 if ($xlsRowIndex == 1)
		 {
		 $objPHPExcel->getActiveSheet()->mergeCells(getExcelColName($xlsColIndexChar-1) . '1:' .getExcelColName($xlsColIndexChar). '1');
		 $xlsColIndexChar++;
		 } else {
		 $objPHPExcel->getActiveSheet()->SetCellValue(getExcelColName($xlsColIndexChar) . $xlsRowIndex, $cntValue['sale']);
		 $xlsColIndexChar++;
		 }

		 if ($xlsRowIndex == 2)
		 $objPHPExcel->getActiveSheet()->mergeCells(getExcelColName($xlsColIndexChar) . '1:' .getExcelColName($xlsColIndexChar). '1');
		 */

		$xlsColIndexChar++;
	}

	$xlsRowIndex++;
	//echo "<BR>";
}

//exit;
$saveFilename = 'after_option_price.xls';

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

// Save Excel 2007 file
$downloadFilename = str_replace('.xls', '.xlsx', $downloadFilename);
$saveFilename = str_replace('.xls', '.xlsx', $saveFilename);
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
//$objWriter->save($saveFilename);
$objWriter -> save($filedir . $saveFilename);

//$objWriter->save('php://output');

//$objWriter->disconnectWorksheets();
unset($objWriter);

$downloadFilename = urlencode($downloadFilename);
//iconv("UTF-8","cp949//IGNORE", $downloadFilename);
if (file_exists($saveFilename)) {
	header('Content-Description: File Transfer');
	header('Content-Transfer-Encoding: binary');
	header('Content-Disposition: attachment;filename=' . $downloadFilename);
	header('Content-Type: application/vnd.ms-excel;charset=UTF-8;');
	header('Content-Length: ' . filesize($saveFilename));
	header('Pragma: no-cache');
	header('Expires: 0');
	ob_clean();
	flush();
	@readfile($saveFilename);
}
?>