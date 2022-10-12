<?
include "../lib.php";
include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

$getData = json_decode(base64_decode($_GET[postData]), 1);

$addQuery = '';
if ($getData[stype] && $getData[sword]) $addQuery .= " and $getData[stype] like '%$getData[sword]%'";
if ($getData[start]) $addQuery .= " and regdt >= '$getData[start] 00:00:00'";
if ($getData[end]) $addQuery .= " and regdt <= '$getData[end] 23:59:59'";
if ($getData[catno]) {
	if (is_numeric($getData[catno])) $addQuery .= " and catno like '$getData[catno]%'";
}
if ($getData[kind]) $addQuery .= " and kind = '$getData[kind]'";
if ($getData[emoney][0]) $addQuery .= " and emoney >= '{$getData[emoney][0]}'";
if ($getData[emoney][1]) $addQuery .= " and emoney <= '{$getData[emoney][1]}'";

$order_column_arr = array("no", "no", "", "", "", "degree", "emoney", "regdt", "", "", "");
$order_data = $_POST[order][0];
$orderby = " order by " .$order_column_arr[$order_data[column]]. " " .$order_data[dir];
$addQuery .= $orderby;

$tableName = "exm_review a";
$bRegistFlag = false;
$selectArr = "a.*,(select goodsnm from exm_goods where goodsno=a.goodsno) goodsnm";
$whereArr = array("cid" => "$cid");

$limit = " limit $_POST[start], $_POST[length]";

$list = SelectListTable($tableName, $selectArr, $whereArr, $bRegistFlag, $addQuery, $limit);
$totalCnt = count(SelectListTable($tableName, $selectArr, $whereArr, $bRegistFlag, $addQuery));

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

$start_index = $_POST[start] + 1;
foreach ($list as $key => $value) {
   $pdata = array();
   
   $review_deny_user = ($value[review_deny_user]) ? "<b class='red'>N</b>" : "<b class='green'>Y</b>";
   $review_deny_admin = ($value[review_deny_admin]) ? "<b class='red'>N</b>" : "<b class='green'>Y</b>";
   
   $review_answer = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=location.href=\"review.w.php?no=".$value[no]."\">"._("수정")."</button>";
   $review_delete = "<button type=\"button\" class=\"btn btn-xs btn-danger\" onclick=\"review_delete('".$value[no]."','".$value[img]."');\">"._("삭제")."</button>";
   $review_main_flag = ($value[main_flag] == "Y") ? _("노출") : _("노출안함");
   
   $pdata[] = "<input type=\"checkbox\" name=\"chk[]\" value=\"$value[no]\">";
   $pdata[] = "<input type=\"hidden\" name=\"no\" value=\"$value[no]\">".$start_index;
   $pdata[] = goodsListImg($value[goodsno],50,50);
   $pdata[] = "<div>"._("주문번호")." : $value[payno]_$value[ordno]_$value[ordseq]</div>
			   <div>"._("상품명")." : <b class=\"red\">[$value[goodsno]]</b> $value[goodsnm]</div>
			   <div>"._("제목")." : $value[subject]</div>";
			   
   if ($value[mid]) $pdata[] = "<div><a href=\"javascript:;\" onclick=\"popup('../member/member_detail_popup.php?mode=member_modify&mid=$value[mid]',1100,800)\">$value[name]</a></div>($value[mid])";
   else $pdata[] = "<div>$value[name]</div>("._("비회원").")";
   $pdata[] = $r_degree[$value[degree]];
   $pdata[] = number_format($value[emoney]);
   $pdata[] = substr($value[regdt],0,10);
   $pdata[] = "<div>"._("사용자").":". $review_deny_user ."</div>
   			   <div>"._("관리자").":". $review_deny_admin ."</div>";
   $pdata[] = $review_answer;
   $pdata[] = $review_delete;
   $pdata[] = $review_main_flag;
   
   $psublist[] = $pdata;
   $start_index++;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>