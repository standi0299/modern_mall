<?
$unit_data = $adminExtraOption -> getOptionUnitPrice($cid, $cfg_center[center_cid], $_GET[goodsno]);
//debug($unit_data);

$unit_cnt_rule = $unit_data[unit_cnt_rule];
$unit_cnt_price = $unit_data[unit_cnt_price];

//debug($unit_cnt_rule);
//debug($unit_cnt_price);
//exit;

$colCount = 0;
$orderCntColCount = 0;

$tableHeader = "";
$tableContent = "";
$tableCntHeader = "";
$tableCntContent = "";

$UnitCntRuleArr = explode(";", $unit_cnt_rule);
//debug($UnitCntRuleArr);
//exit;

if ($cid == $cfg_center[center_cid]) {
	$tableHeaderTitle = _("공급가격 / 권장판매가");
	$msg = "<span>"._("공급가격 – 센터에서 분양몰에 공급하는 가격 / 권장판매가 – 분양몰에서 소비자에게 판매하는 실 판매가로 분양몰에서 변경이 가능")."</span>";
}	
else {
	$tableHeaderTitle = _("공급원가 / 판매가");
}

if ($extra_preset == "100110" || $extra_preset == "100112") {//책자 스튜디오 견적 프리셋1 || 책자 견적 프리셋3
	$tableHeaderTitle = _("공급할인 / 권장할인");
	$msg = "<span>"._("할인입력 – 백분율을 기준으로 소수로 등록. 예) 15% 할인 시 0.15 입력.")." (15 / 100 = 0.15)</span>";
}

$tableCntHeader .= "<th>"._("수량(건수)")."</th><th>$tableHeaderTitle</th>\r\n";

//옵션 주문 수량 테이블 만들기
foreach ($UnitCntRuleArr as $rootKey => $rootValue) {
	if ($rootValue) {
		$rootArr = split('~', $rootValue);
		if (sizeof($rootArr) > 2) {
			$rootArr = $rootArr[0] . "~" . $rootArr[1];
		} else {
			$rootArr = $rootValue;
		}

		//$tableCntHeader .= "<th>$rootArr $r_est_item_price_type_title[$extra_price_type]<br />$tableHeaderTitle
		//$tableCntHeader .= "<th>$rootArr<br />$tableHeaderTitle
		// <input type='hidden' name='print_cnt_$rootValue' value='$rootValue' size='5'>
		//</th>\r\n";

		$tableCntContent .= "<tr><td align='center'>$rootArr</td>\r\n";

		//$supply_price = $UnitCntPriceArr[$rootValue][supply_price];
		//$sale_price = $UnitCntPriceArr[$rootValue][sale_price];

		//debug($rootValue);
		$priceData = getPriceSection($unit_cnt_price, $rootValue);
		//debug($priceData);
		$supply_price = $priceData[0];
		$sale_price = $priceData[1];

		if (!$supply_price || $supply_price == -1)
			$supply_price = "0";
		if (!$sale_price || $sale_price == -1)
			$sale_price = "0";

		if ($bExcelImport) {
			$tableCntContent .= "<td align='center'>
	        <input type='text' name='supply_price_$rootValue' value='[supply_price]_$rootValue' size='4'> / 
	        <input type='text' name='sale_price_$rootValue' value='[sale_price]_$rootValue' size='4'>
	        </td></tr>\r\n";
		} else {
			$tableCntContent .= "<td align='center'>
	        <input type='text' name='supply_price_$rootValue' value='$supply_price' size='4'> / 
	        <input type='text' name='sale_price_$rootValue' value='$sale_price' size='4'>
	        </td></tr>\r\n";
		}

		$orderCntColCount++;
	}
}

//debug($UnitCntRuleArr);
//debug($tableCntHeader);
//debug($tableCntContent);
//debug($orderCntColCount);
//exit;

//엑셀에서 가격정보 읽어오기
$xlsRowIndex = 0;
//엑셀정보를 읽기 위한 rowindex
$xlsStartColIndex = $colCount + 1;
//엑셀 읽어올때 처음부터 읽은 Col index
$sheet = 0;

if ($bExcelImport) {

	$xlsOptionPrice = array();

	$ext = substr(strrchr($excelImportFileName, "."), 1);
	$ext = strtolower($ext);

	if ($ext == "xlsx") {
		// Reader Excel 2007 file
		include "../../lib/PHPExcel.php";
		$objReader = PHPExcel_IOFactory::createReader("Excel2007");
		$objReader -> setReadDataOnly(true);
		//$objReader->setReadFilter( new MyReadFilter() );

		$objPHPExcel = $objReader -> load($excelImportFileName);
		$objPHPExcel -> setActiveSheetIndex(0);
		$objWorksheet = $objPHPExcel -> getActiveSheet();
		$xlsData = $objPHPExcel -> getActiveSheet() -> toArray(null, true, true, true);
		//debug($xlsData);
		//debug('count($xlsData): '.count($xlsData));
		foreach ($xlsData as $key => $value) {
			if ($key == 2) {
				foreach ($value as $k => $v) {
					$v = ($v == '') ? '' : addslashes(htmlentities($v));
					if ($v !== '')
						$outs_inner[] = $v;
				}
			}
		}
		$xlsOptionPrice[] = $outs_inner;
	} else {
		// Reader Excel 2003 file
		//$xlsData = new Spreadsheet_Excel_Reader("unit_price.xls");
		$xlsData = new Spreadsheet_Excel_Reader($excelImportFileName);

		for ($row = 2; $row <= $xlsData -> rowcount($sheet); $row++) {
			$outs_inner = array();
			for ($col = 1; $col <= $xlsData -> colcount($sheet); $col++) {
				if (!$xlsData -> sheets[$sheet]['cellsInfo'][$row][$col]['dontprint']) {
					$val = $xlsData -> val($row, $col, $sheet);
					$val = ($val == '') ? '' : addslashes(htmlentities($val));

					//$outs_inner[] = "{$val}"; # Quote or not? #
					//if (($orderCntColCount *2) > sizeof($outs_inner))
					$outs_inner[] = $val;
				}
			}
			//$xlsPrice[] = implode(',', $outs_inner);
			$xlsOptionPrice[] = $outs_inner;
		}
	}
	//debug($xlsOptionPrice);

	/*
	 //$xlsData = new Spreadsheet_Excel_Reader("unit_price.xls");
	 $xlsData = new Spreadsheet_Excel_Reader($excelImportFileName);

	 $xlsOptionPrice = array();
	 for($row=2; $row<=$xlsData->rowcount($sheet); $row++)
	 {
	 $outs_inner = array();
	 for($col=1; $col<=$xlsData->colcount($sheet); $col++)
	 {
	 if(!$xlsData->sheets[$sheet]['cellsInfo'][$row][$col]['dontprint'])
	 {
	 $val = $xlsData->val($row, $col, $sheet);
	 $val = ($val=='') ? '' : addslashes(htmlentities($val));

	 //$outs_inner[] = "{$val}"; # Quote or not? #
	 //if (($orderCntColCount *2) > sizeof($outs_inner))
	 $outs_inner[] = $val;
	 }
	 }
	 //$xlsPrice[] = implode(',', $outs_inner);
	 $xlsOptionPrice[] = $outs_inner;
	 }
	 //debug($xlsRowIndex);
	 //debug($xlsOptionPrice);
	 */

	//엑셀에서 읽어온 가격 정보 input 태그의 value 값으로 지정하기.
	$col = sizeof($xlsOptionPrice[$xlsRowIndex]) - 1;
	krsort($UnitCntRuleArr);
	//큰순으로 정렬변경(key 정렬) 2014.07.18 kdk
	//debug($xlsRowIndex);

	foreach ($UnitCntRuleArr as $key => $value) {
		if ($value && $value != "") {
			//debug("value:".$value);
			$rootArr = split('~', $value);
			if (sizeof($rootArr) > 2) {
				$rootArr = $rootArr[0] . "~" . $rootArr[1];
			} else {
				$rootArr = $value;
			}

			$v = $rootArr;
			//debug("v:".$v);
			foreach ($xlsOptionPrice as $k2 => $v2) {
				if ($v2[0] == $v) {
					//debug("v:".$v);
					//debug("".$v2[1]);
					//debug("".$v2[2]);

					$tableCntContent = str_replace("[supply_price]_$value", $v2[1], $tableCntContent);
					$tableCntContent = str_replace("[sale_price]_$value", $v2[2], $tableCntContent);
				}
			}

			/*
			 $tableCntContent = str_replace("[supply_price]_$value", $xlsOptionPrice[$xlsRowIndex][$col-1], $tableCntContent);
			 $tableCntContent = str_replace("[sale_price]_$value", $xlsOptionPrice[$xlsRowIndex][$col], $tableCntContent);
			 $col=$col-2;
			 */
		}
	}

	//debug($tableCntContent);
	//exit;
}

$debug_data .= "6 - " . number_format(get_time() - $this_time, 4) . _("초")."<BR>";
//실제 출력..
//$optionGroupCode = $rootValue[option_code];
//debug($tableHeader);
//debug($tableCntHeader);
//echo "<a href='view_option_excel.php?mode=FIX&goodsno=$_GET[goodsno]'>필수옵션 엑설 저장</a> ";
//echo "<a href='view_option_excel.php?mode=NOR&goodsno=$_GET[goodsno]'>후가공옵션 엑설 저장</a>";
//echo "<form name='frm1' action='indb_option.php' method='post'>";
echo "<input type='hidden' name='mode' value='price_update'>";
echo "<input type='hidden' name='goodsno' value='$_GET[goodsno]'>";
echo "<div class='addoptbox_div3'>";
echo "<div class='btn'>$msg&nbsp;&nbsp;<img src='../img/bt_excelupload_s.png' alt='"._("엑셀 불러오기")."' style='cursor:pointer;' onclick='openFile(event,this,1);' /><a href='extra_option_unit_price_excel.php?mode=$_GET[mode]&goodsno=$_GET[goodsno]'><img src='../img/bt_exceldown_s.png' alt='"._("엑셀 저장")."' /></a></div>";
echo "<table class='addoptbox3'>";
echo "<tr>" . $tableCntHeader . "</tr>";
echo $tableCntContent;
echo "</table>";
echo "<div class='btn2'><img src='../img/bt_excelupload_s.png' alt='"._("엑셀 불러오기")."' style='cursor:pointer;' onclick='openFile(event,this,1);' /><a href='extra_option_unit_price_excel.php?mode=$_GET[mode]&goodsno=$_GET[goodsno]'><img src='../img/bt_exceldown_s.png' alt='"._("엑셀 저장")."' /></a></div>";
echo "</div>";
echo "<br /><br />";

$debug_data .= "7 - " . number_format(get_time() - $this_time, 4) . _("초")."<BR>";
//echo $debug_data;
?>