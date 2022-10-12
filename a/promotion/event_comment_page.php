<?
include "../lib.php";

$m_etc = new M_etc();
$r_hidden_img = array('0'=>'btn_on.gif', '1'=>'btn_off.gif');

$addWhere = "where a.cid = '$cid'";

$postData = json_decode(base64_decode($_GET[postData]), 1);
if ($postData) {
	if ($postData[eventno]) $addWhere .= " and a.eventno = '$postData[eventno]'";
	if ($postData[sword]) $addWhere .= " and concat(a.mid,a.comment) like '%$postData[sword]%'";
	
	if ($postData[start]) $addWhere .= " and a.regdt > '{$postData[start]}'";
	if ($postData['end']) $addWhere .= " and a.regdt < adddate('{$postData[end]}',interval 1 day)";
	
	if ($postData[emoney]) $addWhere .= " and a.emoney = '0'";
	else if ($postData[emoney]) $addWhere .= " and a.emoney != '0'";
	
	if ($postData[hidden]) {
		$tmp = $postData[hidden] - 1;
		$addWhere .= " and a.hidden = '$tmp'";
	}
}

$orderby = "order by a.no desc";

$limit = "limit $_POST[start], $_POST[length]";

$list = $m_etc->getEventCommentList($cid, $addWhere, $orderby, $limit);
$list_cnt = $m_etc->getEventCommentList($cid, $addWhere);
$totalCnt = count($list_cnt);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
   $pdata = array();
	
   $hidden_img = $r_hidden_img[$value[hidden]];
   if ($value[regdt] == "0000-00-00 00:00:00") $value[regdt] = "";

   if ($value[emoney]) $pdata[] = "<div>"._("지급완료")."</div>";
   else $pdata[] = "<input type=\"checkbox\" name=\"chk[]\" value=\"$value[no]\">";
   
   $pdata[] = nl2br($value[comment]);
   $pdata[] = "<div><a href=\"javascript:;\" onclick=\"popup('../member/member_detail_popup.php?mode=member_modify&mid=$value[mid]',1100,800)\">$value[name]</a></div>($value[mid])";
   $pdata[] = $value[regdt];
   $pdata[] = "<input type=\"hidden\" name=\"no\" value=\"$value[no]\"><a href=\"../promotion/event_write.php?eventno=$value[eventno]\">$value[title] ($value[eventno])</a>";
   
   if ($value[emoney]) $pdata[] = number_format($value[emoney]);
   else if (!$value[emoney] && $value[name]) $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=\"popup('event_emoney_popup.php?no=$value[no]', 600, 420)\">"._("적립")."</button>";
   else $pdata[] = "";
   
   $pdata[] = "<img src=\"../img/$hidden_img\" onclick=\"chg_hidden(this, $value[no])\" style=\"cursor:pointer;\">";
   //$pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-success\" onclick=\"popup('event_comment_popup.php?no=$value[no]', 550, 330)\">보기</button>";
   
   $psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>