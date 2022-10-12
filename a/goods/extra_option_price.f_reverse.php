<?
if (!$extraOption -> OptionData) {
	$extraOption -> GetOptionDataInDB($_GET[goodsno], $optionGroupType, $extraOption->GoodsKind, $_GET[kind]);
}
//itemPriceType
$extra_price_type = $extraOption -> GetItemPriceType($optionGroupType, $optionKindCode);

$debug_data .= "2 - " . number_format(get_time() - $this_time, 4) . _("초")."<BR>";
//debug($extraOption->GoodsKind);
//debug($extraOption->OptionALLData);
//debug($goodsKind);
//exit;

if ($extraOption->GoodsKind == "BOOK") {
	include "../../lib/extra_option/_inc_option_book.php";
}
else {
    include "../../lib/extra_option/_inc_option_card.php";
}

//책자 프리셋3(100112) 표지 필수,선택 후가공 수량은 표지수량을 사용한다. / 2017.05.11 / kdk.
if ($extraOption->Preset == "100112" && $_GET[mode] == "AFTER") {
	$extraOption->SetCoverPrintCntRule($_GET[goodsno]);
}

foreach ($extraOption->PrintCntRuleArr as $rootKey => $rootValue) {
	if ($rootValue) {
		if ($cid == $cfg_center[center_cid])
			$tableHeaderTitle = _("공급가격/권장판매가");
		else
			$tableHeaderTitle = _("공급원가/판매가");

		$rootArr = split('~', $rootValue);
		if (sizeof($rootArr) > 2) {
			$rootArr = $rootArr[0] . "~" . $rootArr[1];
		} else {
			$rootArr = $rootValue;
		}

		$tableCntHeader = "$rootArr
        <input type='hidden' name='print_cnt_$rootValue' value='$rootValue' size='5'>";

		$tableCntContent = "<input type='text' name='supply_price_[option_name]_$rootValue' value='[supply_option_price_value]' size='4'> / 
        <input type='text' name='sale_price_[option_name]_$rootValue' value='[sale_option_price_value]' size='4'>";

		$tableCntArr[$rootArr] = array('Cnt' => $rootArr, 'CntVal' => $rootValue, 'CntHeader' => $tableCntHeader, 'CntContent' => $tableCntContent);
	}
}

//debug($extraOption->PrintCntRuleArr);
//exit;

//엑셀에서 가격정보 읽어오기
$xlsRowIndex = 0;
//엑셀정보를 읽기 위한 Row index
$xlsStartColIndex = 2;
//엑셀 읽어올때 처음부터 읽은 Col index
$sheet = 0;

//debug($AfterOptionDisplayData);

//$bExcelImport = true;
//$excelImportFileName = '../../data/excel_temp/1426656089.xlsx';
//엑셀 가격정보 읽어오기
if ($bExcelImport) {
	$xlsOptionPrice = array();

	$ext = substr(strrchr($excelImportFileName, "."), 1);
	$ext = strtolower($ext);

	if ($ext == "xlsx") {
		$rowIndex = 2;

		// Reader Excel 2007 file
		include_once "../../lib/PHPExcel.php";

		$objReader = PHPExcel_IOFactory::createReader("Excel2007");
		$objReader -> setReadDataOnly(true);

		$objReader -> canRead($excelImportFileName);
		$objPHPExcel = $objReader -> load($excelImportFileName);

		$objPHPExcel -> setActiveSheetIndex(0);
		$objWorksheet = $objPHPExcel -> getActiveSheet();
		$xlsData = $objPHPExcel -> getActiveSheet() -> toArray(null, true, true, true);
		//debug('count($xlsData): '.count($xlsData));
		$xlsStartColIndex = 1;
		foreach ($xlsData as $key => $value) {
			if ($key > 2) {
				//debug("col count: ".count($value));
				for ($col = $xlsStartColIndex; $col <= count($value); $col++) {
					$val = $value[getExcelColName($col)];
					//debug(getExcelColName($col));
					//debug("fir: ".$val);
					//$val = ($val=='') ? '' : addslashes(htmlentities($val));
					//$val = addslashes(htmlentities($val));
					$val = ($val == '') ? '0' : addslashes(htmlentities($val));
					//debug("sec: ".$val);
					if ($val !== '')
						$outs_inner[] = $val;
				}
			}
			$xlsOptionPrice[] = $outs_inner;
			unset($outs_inner);
		}
	} else {

		$rowIndex = 0;
		// Reader Excel 2003 file
		//$xlsData = new Spreadsheet_Excel_Reader("option_price.xls");
		$xlsData = new Spreadsheet_Excel_Reader($excelImportFileName);
		for ($row = 3; $row <= $xlsData -> rowcount($sheet); $row++) {
			$outs_inner = array();
			for ($col = $xlsStartColIndex; $col <= $xlsData -> colcount($sheet); $col++) {
				if (!$xlsData -> sheets[$sheet]['cellsInfo'][$row][$col]['dontprint']) {
					$val = $xlsData -> val($row, $col, $sheet);
					//$val = ($val=='') ? '' : addslashes(htmlentities($val));
					$val = ($val == '') ? '0' : addslashes(htmlentities($val));

					//$outs_inner[] = "{$val}"; # Quote or not? #
					//if (($orderCntColCount *2) > sizeof($outs_inner))
					$outs_inner[] = $val;
				}
			}
			//$xlsPrice[] = implode(',', $outs_inner);
			$xlsOptionPrice[] = $outs_inner;
		}
	}

	//debug($xlsStartColIndex);
	//debug($xlsRowIndex);
	//debug($xlsData);
	//debug($xlsOptionPrice);
	//exit;
}
//debug($AfterOptionDisplayData);
//exit;

$debug_data .= "6 - " . number_format(get_time() - $this_time, 4) . _("초")."<BR>";

//echo "<form name='frm1' action='indb_option.php' method='post'>";
echo "<input type='hidden' name='mode' value='price_update_s2'>";
echo "<input type='hidden' name='goodsno' value='$_GET[goodsno]'>";
echo "<input type='hidden' name='mode2' value='$_GET[mode]'>";
echo "<input type='hidden' name='kind' value='$_GET[kind]'>";
echo "<input type='hidden' name='goodskind' value='$extraOption->GoodsKind'>";
echo "<input type='hidden' name='filename' value='$_GET[filename]'>";
echo "<input type='hidden' name='all_delete' value='$_GET[all_delete]'>";
echo "<input type='hidden' name='mode3' value='$mode3'>";

if ($cid == $cfg_center[center_cid]) {
	$msg = "<span>"._("공급가격 – 센터에서 분양몰에 공급하는 가격 / 권장판매가 – 분양몰에서 소비자에게 판매하는 실 판매가로 분양몰에서 변경이 가능")."</span>";
}

echo "<div class='addoptbox_div3'>";

if ($bPriceInsertFlag) {
	echo "<div class='btn'>$msg&nbsp;&nbsp;<img src='../img/bt_excelupload_s.png' alt='"._("엑셀 불러오기")."' style='cursor:pointer;' onclick='priceIsNull(1);' />";
	echo "<a href='javascript:priceIsNull();'><img src='../img/bt_exceldown_s.png' alt='"._("엑셀 저장")."' /></a>";
} else {
	echo "<div class='btn'>$msg&nbsp;&nbsp;<img src='../img/bt_excelupload_s.png' alt='"._("엑셀 불러오기")."' style='cursor:pointer;' onclick='openFile(event);' />";
	echo "<a href='extra_option_price_excel_reverse.php?goodsno=$_GET[goodsno]&mode=$_GET[mode]&kind=$_GET[kind]&filename=$_GET[filename]&docUse=$_GET[docUse]&ptType=$_GET[ptType]'><img src='../img/bt_exceldown_s.png' alt='"._("엑셀 저장")."' /></a>";
}

echo "</div>";

echo "<table style='table-layout: fixed; width: 100%' class='addoptbox3' id='postswrapper'><tbody>";

//echo "<tr>".$tableHeader.$tableCntHeader ."</tr>";
//echo $tableContent;

$price_type_title = $r_est_item_price_type_title[$extra_price_type];
$price_type_title = str_replace("(", "", $price_type_title);
$price_type_title = str_replace(")", "", $price_type_title);

echo "<tr>";
echo "<th style='padding:5px 0; width:120px;'>$price_type_title/"._("옵션")."</th>";

foreach ($tableCntArr as $key => $val) {
	echo "<th style='padding:5px 0; width:120px;'>";
	echo $val[CntHeader] . "<br/>" . $tableHeaderTitle;
	echo "</th>";
}
echo "</tr>";

//debug($tableCntArr);
//debug($InsertExtraOptionTable);
//exit;
//
if (!$bExcelImport)
	$PriceTotalTable = $extraOption -> getPriceTotalTable($InsertExtraOptionTable);

$displayTag = "";
$displayIndex = 1;
$cookieIndex = 1;

$rowIndex = 2;
foreach ($InsertExtraOptionTable as $itemKey => $itemValue) {
	$displayTag .= "<tr>";
	$displayTag .= "<th style='padding:5px 0;'>";
	$displayTag .= "<input type='hidden' name='option_item_$itemKey' value='$itemValue[option_item]'>";
	$displayTag .= str_replace("|", "->", $itemValue[option_item]);
	$displayTag .= "</th>";

	//if (!$bExcelImport)
	//  $xlsPrintCntArr = $extraOption->getPriceRuleDividePrintCnt($InsertExtraOptionTable[$itemKey][option_price]);
	$colIndex = 0;
	foreach ($tableCntArr as $key => $val) {
		if ($InsertExtraOptionTable[$itemKey][option_price] == "0") {
			$priceData[0] = '0';
			$priceData[1] = '0';
		} else {
			if ($bExcelImport) {
				$priceData[0] = $xlsOptionPrice[$rowIndex][$colIndex++];
				$priceData[1] = $xlsOptionPrice[$rowIndex][$colIndex++];
			} else {
				$priceData = $PriceTotalTable[$rowIndex - 2][$key];
			}
		}

		$tagData = str_replace("[option_name]", $itemKey, $val[CntContent]);
		$tagData = str_replace("[supply_option_price_value]", $priceData[0], $tagData);
		$tagData = str_replace("[sale_option_price_value]", $priceData[1], $tagData);

		$displayTag .= "<td>" . $tagData . "</td>";

	}
	$displayTag .= "</tr>";

	$rowIndex++;

	$displayIndex++;

	if ($displayIndex > $PAGE_ROW_MAX) {
		echo $displayTag;
		//$_SESSION['EP_'.$cookieIndex] = $displayTag;
		$cookieIndex++;
		$displayIndex = 1;
		$displayTag = "";
	}
}

//if ($displayIndex <= $PAGE_ROW_MAX)
//  $_SESSION['EP_'.$cookieIndex] = $displayTag;

$debug_data .= "7 - " . number_format(get_time() - $this_time, 4) . _("초")."<BR>";
//echo $debug_data;
//echo $_SESSION['EP_1'];
echo $displayTag;
?>

</tbody>
</table>
<div id="loadmoreajaxloader" style="display:none;"><center><img src="../img/loading_s.gif" /></center></div>

</div>
<br /><br />
