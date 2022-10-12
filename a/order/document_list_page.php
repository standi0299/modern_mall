<?
include "../lib.php";

$m_order = new M_order();

$addWhere = "";
$postData = json_decode(base64_decode($_GET[postData]), 1);

if ($postData) {
	$startDate = str_replace("-", "", $postData[start]);
	$endDate = str_replace("-", "", $postData['end']);
	if ($startDate) $addWhere .= " and a.regdt > '{$startDate}'";
	if ($endDate) $addWhere .= " and a.regdt < adddate('{$endDate}',interval 1 day)";	
	
	if ($postData[searchValue] != "") {
		$addWhere .= " and (a.payno = '$postData[searchValue]' or a.mid = '$postData[searchValue]')";
	}
	
	if ($postData[document_type] != "") {
		$addWhere .= " and a.document_type = '$postData[document_type]'";
	}
}

$addQuery = " order by a.regdt desc";
$addQuery .= " limit $_POST[start], $_POST[length]";

$list = $m_order->getDocumentList($cid, $addWhere, $addQuery);
$totalCnt = $m_order->getDocumentListCnt($cid, $addWhere);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
	$pdata = array();
	
	$pdata[] = "<input type=\"checkbox\" name=\"chk[]\" value=\"$value[no]\">";
	$payno_data = "<a href=\"javascript:;\" onclick=\"popup('order_detail_popup.php?payno=$value[payno]',1200,750)\"><b>$value[payno]</b></a>";
	
	$document_data = _("서류발급종류")." : ".$r_document_type[$value[document_type]];
	
	if ($value[mobile] && $value[mobile] != "010--") {
		$document_data .= "<br>"._("핸드폰")." : ".$value[mobile];
	}
	
	if ($value[email] && $value[email] != "@") {
		$document_data .= "<br>"._("이메일")." : ".$value[email];
	}
	
	if ($value[card_num] && $value[card_num] != "---") {
		$document_data .= "<br>"._("카드번호")." : ".$value[card_num];
	}
	
	if ($value[licensee_num] && $value[licensee_num] != "--") {
		$document_data .= "<br>"._("사업자번호")." : ".$value[licensee_num];
	}
	
	if ($value[document_file]) {
		$document_data .= "<br>"._("첨부파일")." : <a href=\"../../data/document/$cid/$value[document_file]\" target=\"_blank\">".$value[document_file]."</a>";
	}
	
	if ($value[mid]) {
		if ($value[mid] != "admin") {
			$mid_data = "<a href=\"javascript:;\" onclick=\"popup('../member/member_detail_popup.php?mode=member_modify&mid=$value[mid]',1100,800)\">$value[name]<br>($value[mid])</a>";
		} else {
			$mid_data = "$value[name]<br>($value[mid])";
		}
	} else {
		$mid_data = _("비회원");
	}
	
	$pdata[] = $payno_data;
	$pdata[] = $document_data;
	$pdata[] = $mid_data;
	$pdata[] = $value[regdt];
	$pdata[] = $value[state]==1 ? 'Y' : 'N';
	
	$psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>