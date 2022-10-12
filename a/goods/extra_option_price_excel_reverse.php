<?
$viewpage = true;
$podspage = true;
//include "../_header.php";
//include "../lib/class.page.php";
include_once "../lib.php";
include_once "../../lib/extra_option/extra_option_price_proc.php";
include_once '../../lib/PHPExcel.php';
include_once '../../lib/PHPExcel/Writer/Excel2007.php';

set_time_limit(360);
ini_set('memory_limit', '-1');
//테스트용 임시 메모리 제한 풀기 2014.07.10 by kdk

$editorLayer = 1;

//책사상품이면...option_group_type 별로 처리한다.
if ($_GET[goodsno]) {
	//$data = $db -> fetch("select * from exm_goods where goodsno='$_GET[goodsno]'");
	$mGoods = new M_goods();
	$data = $mGoods->getInfo($_GET[goodsno]);
	if ($data) {
		$extra_option = explode('|', $data[extra_option]);
		//항목 분리
		if (count($extra_option) > 0) {
			$extra_product = $extra_option[0];
			$extra_preset = $extra_option[1];
			$extra_price_type = $extra_option[2];
		}
	}
}

$downloadFilename = date('Y-m-d') . "-$cfg_center[center_cid]-$data[goodsno]-$data[goodsnm]-$_GET[filename]"._("옵션가격관리").".xls";

$optionGroupType = $_GET[mode] . "OPTION";
$addWhere = " and option_group_type = '" . $_GET[mode] . "OPTION' ";

//후가공이면...
if ($_GET[mode] == "AFTER") {
	//후가공 코드가 있으면...
	if ($_GET[kind]) {
		$addWhere .= " and option_kind_code = '$_GET[kind]' ";
		$optionKindCode = $_GET[kind];
	}
} else {
	//if($extra_product == "BOOK") { //책자상품이면...option_group_type 별로 처리한다.
	//내지옵션이면...
	if ($_GET[mode] == "F-FIX") {
		$addWhere = " and option_group_type = 'FIXOPTION' ";
		$optionGroupType = "FIXOPTION";
	} elseif ($_GET[mode] == "F-SEL") {
		$addWhere = " and option_group_type = 'SELOPTION' ";
		$optionGroupType = "SELOPTION";
	}
	//}
}
//debug($optionGroupType);
//debug($optionKindCode);
//debug($addWhere);

//자동견적 옵션 등록 상품인지 체크
$extraOption = new ExtraOption();
$extraOption->SetGoodsKind($goodsKind);
$extraOption -> GetOptionDataInDB($_GET[goodsno], $optionGroupType, $goodsKind, $_GET[kind]);


if ($extraOption->GoodsKind == "BOOK")
	include "../../lib/extra_option/_inc_option_book.php";
else {
	include "../../lib/extra_option/_inc_option_card.php";
}

$OptionCnt = array();
$FixOption = array();
$rowIndex = 0;
//엑셀정보를 읽기 위한 rowindex

//옵션 주문 수량 테이블 만들기

//내지의 경우 내지 출력 수량 규칙을 가격 기본 테이블로 셋팅
$PrintCntRule = array();
if ($_GET[mode] == "PAGE")
	$PrintCntRule = $extraOption -> PrintPageCntRuleArr;
else
	$PrintCntRule = $extraOption -> PrintCntRuleArr;

//debug($PrintCntRule);
foreach ($PrintCntRule as $rootKey => $rootValue) {
	if ($rootValue) {
		$rootArr = split('~', $rootValue);
		if (sizeof($rootArr) > 2) {
			$rootArr = $rootArr[0] . "~" . $rootArr[1];
		} else {
			$rootArr = $rootValue;
		}

		$OptionCnt[0][$rootValue]['suply'] = "$rootArr $r_est_item_price_type_title[$extra_price_type]";
		$OptionCnt[0][$rootValue]['sale'] = "";

		if ($cid == $cfg_center[center_cid]) {
			$OptionCnt[1][$rootValue]['suply'] = _("공급가격");
			$OptionCnt[1][$rootValue]['sale'] = _("권장판매가");
		} else {
			$OptionCnt[1][$rootValue]['suply'] = _("공급원가");
			$OptionCnt[1][$rootValue]['sale'] = _("판매가");
		}

	}
}

//debug($OptionCnt);
//exit;

foreach ($extraOption->PrintCntRuleArr as $rootKey => $rootValue) {
	if ($rootValue) {
		$tableCntArr[$rootValue] = "$rootValue";
	}
}

//debug($tableCntArr);
//exit;
if ($cid == $cfg_center[center_cid]) {
	$suply = _("공급가격");
	$sale = _("권장판매가");
} else {
	$suply = _("공급원가");
	$sale = _("판매가");
}

if ($_GET[mode] == "AFTER") {//후가공 옵션
	$mode = $_GET[kind];
} else {
	$mode = $_GET[mode];
}

//$sql = "select * from tb_extra_option_price_info where cid = '$cid' and bid = '$cfg_center[center_cid]' and goodsno='$_GET[goodsno]' and option_group_type='$mode' order by id asc";
//$opt_data = $db -> listArray($sql);
$addWhere = "and option_group_type='$mode' order by id asc";
$mExtraOption = new M_extra_option();
$opt_data = $mExtraOption->getOptionPriceList($cid, $cfg_center[center_cid], $_GET[goodsno], $addWhere);
if ($opt_data) {
	foreach ($opt_data as $key => $value) {
		$tableContentArr[] = array('ItemKey' => $value[ID], 'ItemVal' => str_replace("|", "->", $value[option_item]), 'ItemPrice' => $value[option_price]);
	}
}

if ($_GET[mode] == "AFTER") {//후가공 옵션 테이블 구조 만들기.    후가공은 1차형태로만 존재함.
	$saveFilename = 'after_option_price.xls';
} else {
	$saveFilename = 'fix_option_price.xls';
}

//debug($tableContentArr);
//exit;
//$FixOption[0][0] = "";
//$FixOption[1][0] = "";

$price_type_title = $r_est_item_price_type_title[$extra_price_type];
$price_type_title = str_replace("(", "", $price_type_title);
$price_type_title = str_replace(")", "", $price_type_title);

$FixOption[0][0] = array('suply' => "$price_type_title/"._("옵션"), 'sale' => "");
$FixOption[1][0] = array('suply' => "", 'sale' => "");
foreach ($tableContentArr as $itemKey => $itemValue) {
	//debug($itemKey);
	//debug($itemValue);

	$FixOption[0][] = array('suply' => $itemValue[ItemVal], );
	$FixOption[1][] = array('suply' => $itemValue[ItemPrice], );
}

//debug($FixOption);
//exit;
$xlsSaveOpton = $FixOption;

//debug($xlsDisplayOrderCntCount);
//debug($orderCntCount);
//debug($OptionCnt);
//debug($xlsSaveOpton);
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

//폰트 사이즈 10
$objPHPExcel -> getDefaultStyle() -> getFont() -> setSize(10);

// Add some data
$objPHPExcel -> setActiveSheetIndex(0);

if ($_GET[mode] == "AFTER") {
	$rowHeight = 40;
} else {
	$rowHeight = 80;
}

foreach ($xlsSaveOpton as $itemKey => $itemValue) {
	$xlsRowIndex = 1;
	//debug($itemKey);
	//debug($itemValue);
	foreach ($itemValue as $key => $value) {
		//debug($key);
		//debug($value);

		if ($itemKey == 0) {
			$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName(0) . $xlsRowIndex, $value['suply']);
			if ($key == 0) {
				$objPHPExcel -> getActiveSheet() -> getColumnDimension('A') -> setWidth(50);
				$xlsColIndexChar++;
				$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName($xlsColIndexChar) . $xlsRowIndex, "");

				$objPHPExcel -> getActiveSheet() -> mergeCells('A1:A2');
				$xlsRowIndex++;
			}
			$objPHPExcel -> getActiveSheet() -> getRowDimension($xlsRowIndex) -> setRowHeight(15);

			//1 row height
			//$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight($rowHeight);
		} else {
			if ($key == 0) {
				$xlsRowIndex++;
				continue;
			}

			$xlsPrintCntArr = $extraOption -> getPriceRuleDividePrintCnt($value['suply']);
			//debug($value['suply']);
			//exit;

			if ($key == 1) {
				$xlsColIndexCharLocal = 1;
				foreach ($xlsPrintCntArr as $cntKey => $cntValue) {
					$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName($xlsColIndexCharLocal) . "1", $cntValue[0]);
					$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName($xlsColIndexCharLocal) . "2", $suply);

					$xlsColIndexCharLocal++;
					$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName($xlsColIndexCharLocal) . "1", "");
					$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName($xlsColIndexCharLocal) . "2", $sale);

					$objPHPExcel -> getActiveSheet() -> mergeCells(getExcelColName($xlsColIndexCharLocal - 1) . '1:' . getExcelColName($xlsColIndexCharLocal) . '1');
					$xlsColIndexCharLocal++;
				}
				$xlsRowIndex++;
			}

			//debug($xlsPrintCntArr);
			//exit;
			$xlsColIndexCharLocal = 1;
			foreach ($xlsPrintCntArr as $cntKey => $cntValue) {
				$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName($xlsColIndexCharLocal) . $xlsRowIndex, $cntValue[1]);
				$xlsColIndexCharLocal++;
				$objPHPExcel -> getActiveSheet() -> SetCellValue(getExcelColName($xlsColIndexCharLocal) . $xlsRowIndex, $cntValue[2]);
				$xlsColIndexCharLocal++;
			}

		}
		$xlsRowIndex++;
		//if ($xlsRowIndex == 10) break;
	}
}

//exit;

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