<?

set_time_limit(0);
include "../lib.php";
include "../../lib/excel.reader.php";

if (!$_POST[shipcomp]){
	msg("배송업체를 선택해주세요"); exit;
}
if (!is_uploaded_file($_FILES[file][tmp_name])){
	msg("파일을 업로드해주세요"); exit;
}

echo "
<style>
body {padding:0;margin:0;font:9pt tahoma}
/*** Table CSS #02 (List) ***/
table.tb2 {
	width: 947px; margin: 0; padding: 0;
	border-collapse: collapse;
}
table.tb2 td{
	padding: 6px 5px;
	border: solid #D7D7D7;
	border-width: 0 1px 1px 1px;
	font: 9pt tahoma,굴림;
}
table.tb2 .c1 {padding-left:10px}
table.tb2 .p0 {padding:0;}
</style>
";
echo "<table class='tb2'>";
$succeed = $failed = 0;
//debug($_FILES);
if (substr(strrchr($_FILES[file][name],"."),1) == "xls"){
	$xls = new Spreadsheet_Excel_Reader();
   $xls->setOutputEncoding('utf-8');     // 인코딩 변경     20150728    chunter
	$xls->read($_FILES[file][tmp_name]);
	foreach ($xls->sheets[0][cells] as $idx=>$data){
		$flds = "";

		if ($idx==1){
			$r_keys = $data;
			$cnt_flds = count($r_keys);
			continue;
		} else {
			for ($i=1;$i<=$cnt_flds;$i++) if ($data[$i]==null) $data[$i] = "";
		}
		ksort($data);
		$data = array_combine($r_keys,$data);
        //debug($data);
		list ($payno) = $db->fetch("select payno from exm_pay where payno = '".$data[주문번호]."' and cid = '$cid'",1);
		list ($step4) = $db->fetch("select count(*) from exm_ord_item where payno = '$payno' and itemstep = '4'",1);

		if (!$payno) $flds = "<b>주문번호</b>가 존재하지 않습니다. ";
		if ($payno && !$step4)	$flds .= "<b>발송대기</b>상태가 아닙니다. ";
		if ($payno && !$data[송장번호]) $flds .= "<b>송장번호</b>가 존재하지 않습니다.";

		$data = array_map("addslashes",$data);

		if ($payno && $step4 && $data[송장번호]){
			$query = "select * from exm_ord_item where payno = '$payno' and itemstep = '4'";
			$res = $db->query($query);
			while ($tmp = $db->fetch($res)){
				chg_itemstep($tmp[payno],$tmp[ordno],$tmp[ordseq],4,5,"관리자 출고완료(송장업로드) 처리",$_POST[shipcomp],$data[송장번호]);
			}
			$succeed++;
		}else{
			$failed++;
		}

		$vidx = $idx-1;
		echo "<tr align='center'><td width='65' style='border-left:0px'>".$vidx."</td><td width='116'>".$data['주문번호']."</td><td width='124'>".$data['송장번호']."</td><td align='left' style='border-right:0px'>".$flds."</td></tr>";
		
		echo "<script>scroll(0,999999);</script>";
		flush();
	}
}else if (substr(strrchr($_FILES[file][name],"."),1) == "csv"){
	$idx = 0; $fp = fopen ($_FILES[file][tmp_name],"r");

	while ($data = fgetcsv ($fp, 32768, ",")) { 
		
		foreach ($data as $k=>$v){
			$data[$k] = iconv("EUC-KR","UTF-8",$v);
		}
		$idx++;
		if ($idx==1){
			$r_keys = $data;
			$cnt_flds = count($r_keys);
			continue;
		} else {
			for ($i=0;$i<$cnt_flds;$i++) if (!$data[$i]) $data[$i] = "";
		}

		$data = array_combine($r_keys,$data);

		list ($payno) = $db->fetch("select payno from exm_pay where payno = '".$data['주문번호']."' and cid = '$cid'",1);
		list ($step4) = $db->fetch("select count(*) from exm_ord_item where payno = '$payno' and itemstep = '4'",1);

		if (!$payno) $flds = "<b>주문번호</b>가 존재하지 않습니다. ";
		if ($payno && !$step4)	$flds .= "<b>발송대기</b>상태가 아닙니다. ";
		if ($payno && !$data[송장번호]) $flds .= "<b>송장번호</b>가 존재하지 않습니다.";

		$data = array_map("addslashes",$data);

		if ($payno && $step4 && $data[주문번호] && $data[송장번호]){
			$query = "select * from exm_ord_item where payno = '$payno' and itemstep = '4'";
			$res = $db->query($query);
			while ($tmp = $db->fetch($res)){
				chg_itemstep($tmp[payno],$tmp[ordno],$tmp[ordseq],4,5,"관리자 출고완료(송장업로드) 처리",$_POST[shipcomp],$data[송장번호]);
			}
			$succeed++;
		}else{ 
			$failed++;
		}
		$vidx = $idx-1;
		echo "<tr align='center'><td width='65' style='border-left:0px'>".$vidx."</td><td width='116'>".$data['주문번호']."</td><td width='124'>".$data['송장번호']."</td><td align='left' style='border-right:0px'>" .$flds. "</td></tr>";
		
		echo "<script>scroll(0,999999);</script>";
		flush();
		}
}
echo "</table><div style='clear:both;text-align:center;padding-top:10px;width'><li style='list-style:none'>- 운송장번호 업로드가 완료되었습니다. (성공 <b>".number_format($succeed)."</b>건 / 실패 <b>".number_format($failed)."</b>건) -</li></div>";

echo "<script>scroll(0,999999);</script>";
flush();

?>