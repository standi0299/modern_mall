<?
include "../lib.php";

$m_etc = new M_etc();

$addWhere = "where cid='$cid'";
$addSubWhere = " and coupon_use=1";

$postData = json_decode(base64_decode($_GET[postData]), 1);
if ($postData) {
	if ($postData[start_one] && $postData[end_one]) {
		$addWhere .= " and (coupon_issue_unlimit=1
			or (coupon_issue_sdate>='{$postData[start_one]}' and coupon_issue_sdate<='{$postData[end_one]}')
			or (coupon_issue_edate>='{$postData[start_one]}' and coupon_issue_edate<='{$postData[end_one]}')
			or (coupon_issue_edate>='{$postData[start_one]}' and coupon_issue_sdate<='{$postData[end_one]}' and '{$postData[start_one]}'<='{$postData[end_one]}'))";
	} else if ($postData[start_one]) {
		$addWhere .= " and (coupon_issue_unlimit=1 or coupon_issue_edate>='{$postData[start_one]}')";
	} else if ($postData[end_one]) {
		$addWhere .= " and (coupon_issue_unlimit=1 or coupon_issue_sdate<='{$postData[end_one]}')";
	}
	
	if ($postData[start_two]) {
		$addSubWhere .= " and coupon_usedt>'{$postData[start_two]}'";
	}
	if ($postData[end_two]) {
		$addSubWhere .= " and coupon_usedt<adddate('{$postData[end_two]}',interval 1 day)";
	}
	
	if ($postData[sword]) $addWhere .= " and coupon_name like '%$postData[sword]%'";
}

$orderby = "order by coupon_regdt desc";

$limit = "limit $_POST[start], $_POST[length]";

$list = $m_etc->getSoldCoupon($cid, $addWhere, $orderby, $limit);
$list_cnt = $m_etc->getSoldCoupon($cid, $addWhere);
$totalCnt = count($list_cnt);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
   $pdata = array();

   $addWhere2 = "where cid='$cid' and coupon_code='$value[coupon_code]'";

   $value[coupon_reg_ea] = $m_etc->getSoldCouponCnt($cid, $addWhere2);
   $value[coupon_used_ea] = $m_etc->getSoldCouponCnt($cid, $addWhere2, $addSubWhere);
   
   ### 상태추출 (발행예정,발행중,발행마감)
   $value[state] = "발행중";
   if ($value[coupon_issue_unlimit]) $value[state] = "발행중";
   else if ($value[coupon_issue_sdate] && strtotime($value[coupon_issue_sdate]) > time()) $value[state] = "발행예정";
   else if ($value[coupon_issue_edate] && strtotime($value[coupon_issue_edate])+86400 < time()) $value[state] = "발행마감";

   $pdata[] = $totalCnt-$key;
   $pdata[] = $value[state];
   $pdata[] = $value[coupon_name];
   if ($value[coupon_issue_ea_limit]) $pdata[] = number_format($value[coupon_issue_ea]);
   else $pdata[] = "무제한";
   $pdata[] = number_format($value[coupon_reg_ea]);
   $pdata[] = number_format($value[coupon_used_ea]);
   if ($value[coupon_issue_ea_limit]) $pdata[] = round(($value[coupon_used_ea]/$value[coupon_issue_ea])*100, 2)."%";
   else $pdata[] = "-";
   if ($value[coupon_reg_ea]) $pdata[] = round(($value[coupon_used_ea]/$value[coupon_reg_ea])*100, 2)."%";
   else $pdata[] = "-";
   
   $psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>