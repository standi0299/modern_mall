<?

include "../lib.php";

$m_order = new M_order();

### 출고처 추출
$r_rid = get_release();

$postData = json_decode(base64_decode($_GET[postData]), 1);

$addWhere = "where a.cid='$cid'";

if ($postData) {
	if ($postData[start_orddt]) {
		$start_orddt = ($postData[start_ordtime]) ? "{$postData[start_orddt]} {$postData[start_ordtime]}:00:00" : "{$postData[start_orddt]} 00:00:00";
		$addWhere .= " and a.orddt > '{$start_orddt}'";
	}
	if ($postData[end_orddt]) {
		$end_orddt = ($postData[end_ordtime]) ? "{$postData[end_orddt]} {$postData[end_ordtime]}:59:59" : "{$postData[end_orddt]} 23:59:59";
		$addWhere .= " and a.orddt <= '{$end_orddt}'";
	}
	if ($postData[start_paydt]) {
		$start_paydt = ($postData[start_paytime]) ? "{$postData[start_paydt]} {$postData[start_paytime]}:00:00" : "{$postData[start_paydt]} 00:00:00";
		$addWhere .= " and (a.paydt > '{$start_paydt}' or a.confirmdt > '{$start_paydt}')";
	}
	if ($postData[end_paydt]) {
		$end_paydt = ($postData[end_paytime]) ? "{$postData[end_paydt]} {$postData[end_paytime]}:59:59" : "{$postData[end_paydt]} 23:59:59";
		$addWhere .= " and (a.paydt <= '{$end_paydt}' or a.confirmdt <= '{$end_paydt}')";
	}
	
	if ($postData[step]) {
		$step = implode(",", $postData[step]);
		$addWhere .= " and c.itemstep in ($step)"; 
	} else {
		$addWhere .= " and c.itemstep in (2,92,3,4,5)";
	}
	
	if ($postData[order_shiptype]) {
		$order_shiptype = implode(",", $postData[order_shiptype]);
		if (in_array(0, $postData[order_shiptype])) $addWhere .= " and (b.order_shiptype in ($order_shiptype) or b.order_shiptype is null or b.order_shiptype='')";
		else $addWhere .= " and b.order_shiptype in ($order_shiptype)";
	}
	
	if ($postData[release]) {
		$addWhere .= " and b.rid = '$postData[release]'";
	}
	
	if ($postData[goods]) {
		$addWhere .= " and (c.goodsno='$postData[goods]' or c.goodsnm like '%$postData[goods]%')";
	}
	
	if ($postData[sword]) {
		$addWhere .= " and concat(a.payno,a.orderer_name,a.payer_name,a.receiver_name) like '%$postData[sword]%'";
	}
} else {
	$addWhere .= " and c.itemstep in (2,92,3,4,5)";
}

$orderby = "order by a.orddt desc";

$limit = "limit $_POST[start], $_POST[length]";

$list = $m_order->getOrdItemInfoList($cid, $addWhere, $orderby, $limit, false);
$list_cnt = $m_order->getOrdItemInfoList($cid, $addWhere);
$totalCnt = count($list_cnt);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
	$pdata = array();
	
	if ($value[addopt]) $value[addopt] = unserialize($value[addopt]);
	if ($value[printopt]) $value[printopt] = unserialize($value[printopt]);
	
	$goodsinfo = "<div>".$value[goodsnm]." (".number_format($value[goods_price]*$value[ea]).")</div>";
	if ($value[opt]) {
		$goodsinfo .= "<div style=\"color:blue;\">".$value[opt]." (+".number_format($value[aprice]*$value[ea]).")</div>";
	}
	if ($value[addopt]) {
		foreach ($value[addopt] as $k=>$v) {
			$goodsinfo .= "<div style=\"color:green;\">".$v[addopt_bundle_name].":".$v[addoptnm]." (+".number_format($v[addopt_aprice]*$value[ea]).")</div>";
		}
	}
	if ($value[printopt]) {
		foreach ($value[printopt] as $k=>$v) {
			$goodsinfo .= "<div style=\"color:red;\">".$v[printoptnm]." (+".number_format($v[print_price]*$v[ea]).")</div>";
		}
	}
	if ($value[addpage_aprice]) {
		$goodsinfo .= "<div>"._("추가페이지").":".$value[addpage]." (+".number_format($value[addpage_aprice]*$value[ea]).")</div>";
	}
	if ($value[dc_member]) {
		$goodsinfo .= "<div>"._("회원할인")." (-".number_format($value[dc_member]*$value[ea]).")</div>";
	}
	if ($value[dc_coupon]) {
		$goodsinfo .= "<div>"._("쿠폰할인")." (-".number_format($value[dc_coupon]).")</div>";
	}
	
	$pdata[] = "<b><a href=\"javascript:;\" onclick=\"popup('order_detail_popup.php?payno=$value[payno]',1200,750)\">$value[payno]</a>_$value[ordno]_$value[ordseq]</b><br>".$r_paymethod[$value[paymethod]];
	if ($value[paymethod] == "t") $pdata[] = $value[orddt]."<br>".$value[confirmdt];
	else $pdata[] = $value[orddt]."<br>".$value[paydt];
	if ($value[mid]) $pdata[] = "<div><a href=\"javascript:;\" onclick=\"popup('../member/member_detail_popup.php?mode=member_modify&mid=$value[mid]',1100,800)\"><b>$value[orderer_name]</b></a></div>($value[mid])";
	else $pdata[] = "<div><b>$value[orderer_name]</b></div>("._("비회원").")";
	$pdata[] = goodsListImg($value[goodsno], 50, 50);
	$pdata[] = "<div><b>"._("상품번호")." : $value[goodsno] / "._("공급사")." : ".$r_rid[$value[item_rid]]."</b></div>".$goodsinfo;
	$pdata[] = $value[ea];
	$pdata[] = number_format($value[payprice]);
	$pdata[] = $r_rid[$value[rid]]."<br><b>".$r_step[$value[itemstep]]."</b>";
	
	$psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)

?>