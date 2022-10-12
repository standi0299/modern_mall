<?
include "../lib.php";

$m_board = new M_board();

$addWhere = "where cid='$cid'";

$postData = json_decode(base64_decode($_GET[postData]), 1);
if ($postData) {	
	if ($postData[catno]) {
		if (is_numeric($postData[catno])) $addWhere .= " and catno like '$postData[catno]%'";
	}
	
	if ($postData[sword]) $addWhere .= " and goodsnm like '%$postData[sword]%'";
}

$order_column_arr = array("no", "no", "", "", "", "hit", "comment", "chk_yn", "emoney", "regdt", "del_ok");
$order_data = $_POST[order][0];
$orderby = " order by " .$order_column_arr[$order_data[column]]. " " .$order_data[dir];

$limit = "limit $_POST[start], $_POST[length]";

$tableName = "exm_edking";

$list = $m_board->getCustomerService($cid, $tableName, $addWhere, $orderby, $limit);
$list_cnt = $m_board->getCustomerService($cid, $tableName, $addWhere);
$totalCnt = count($list_cnt);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

$start_index = $_POST[start] + 1;
foreach ($list as $key => $value) {
   $pdata = array();
	
   if (!$value[chk_yn]) $value[chk_yn] = "N";
   if (!$value[del_ok]) $value[del_ok] = "N";

   $pdata[] = "<input type=\"checkbox\" name=\"chk[]\" value=\"$value[no]\">";
   $pdata[] = "<input type=\"hidden\" name=\"no\" value=\"$value[no]\">".$start_index;
   $pdata[] = goodsListImg($value[goodsno], 50, 50);
   $pdata[] = "<div>"._("주문번호")." : $value[payno]_$value[ordno]_$value[ordseq]</div>
			   <div>"._("상품명")." : <b class=\"red\">[$value[goodsno]]</b> $value[goodsnm]</div>";
   if ($value[mid]) $pdata[] = "<div><a href=\"javascript:;\" onclick=\"popup('../member/member_detail_popup.php?mode=member_modify&mid=$value[mid]',1100,800)\">$value[name]</a></div>($value[mid])";
   else $pdata[] = "<div>$value[name]</div>("._("비회원").")";
   $pdata[] = number_format($value[hit]);
   $pdata[] = number_format($value[comment]);
   $pdata[] = "<b class=\"red\">$value[chk_yn]</b>";
   $pdata[] = number_format($value[emoney]);
   $pdata[] = substr($value[regdt], 0, 10);
   $pdata[] = "<b class=\"notice\">$value[del_ok]</b>";
   
   $psublist[] = $pdata;
   $start_index++;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>