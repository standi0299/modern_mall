<?
include "../lib.php";

$m_etc = new M_etc();

$addWhere = "where a.cid='$cid' and b.itemstep in (2,3,4,5,92)";

$postData = json_decode(base64_decode($_GET[postData]), 1);
if ($postData) {
	$startDate = str_replace("-", "", $postData[start]);
	$endDate = str_replace("-", "", $postData['end']);
	if ($startDate) $addWhere .= " and a.paydt>'{$startDate}'";
	if ($endDate) $addWhere .= " and a.paydt<adddate('{$endDate}',interval 1 day)";	
	
	if ($postData[catno]) {
		if (is_numeric($postData[catno])) $addWhere .= " and b.catno like '$postData[catno]%'";
	}
	
	if ($postData[sword]) $addWhere .= " and b.goodsnm like '%$postData[sword]%'";
}

$orderby = "order by payno desc";

$limit = "limit $_POST[start], $_POST[length]";

$list = $m_etc->getSoldOrder($cid, $addWhere, $orderby, $limit);
$list_cnt = $m_etc->getSoldOrder($cid, $addWhere);
$totalCnt = count($list_cnt);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
   $pdata = array();
	
   if ($value[addpage])	{
   	   $addpageStr[$value[payno]][$value[ordno]][$value[ordseq]] = "<div>"._("추가페이지")." : ".number_format($value[addpage])."</div>";
   }
	
   if ($value[addopt]) {
   	   $value[addopt] = unserialize($value[addopt]);
	   foreach ($value[addopt] as $k=>$v) {
	  	  $addoptStr[$value[payno]][$value[ordno]][$value[ordseq]] .= "<div>$v[addopt_bundle_name] : $v[addoptnm]</div>";
	   }
   }
   if ($value[printopt]) {
   	   $value[printopt] = unserialize($value[printopt]);
   	   foreach ($value[printopt] as $k=>$v) {
	  	  $printoptStr[$value[payno]][$value[ordno]][$value[ordseq]] .= "<div>$v[printoptnm] : $v[ea]</div>";
	   }
   }

   $pdata[] = $value[paydt];
   $pdata[] = $value[payno];
   if ($value[mid]) $pdata[] = "<div>$value[orderer_name]</div>($value[mid])";
   else $pdata[] = "<div>$value[orderer_name]</div>("._("비회원").")";
   $pdata[] = $value[goodsnm];
   $pdata[] = "<div>$value[opt]</div>".$addpageStr[$value[payno]][$value[ordno]][$value[ordseq]].$addoptStr[$value[payno]][$value[ordno]][$value[ordseq]].$printoptStr[$value[payno]][$value[ordno]][$value[ordseq]];
   $pdata[] = number_format($value[ea]);
   $pdata[] = number_format($value[payprice]);
   
   $psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>