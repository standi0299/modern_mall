<?

include "../lib.php";

$m_order = new M_order();

### 출고처 추출
$r_rid = get_release();

$postData = json_decode(base64_decode($_GET[postData]), 1);

$addWhere = "where a.cid='$cid'";

if ($postData) {
	if ($postData[start_orddt]) $addWhere .= " and a.orddt > '{$postData[start_orddt]}'";
	if ($postData[end_orddt]) $addWhere .= " and a.orddt < adddate('{$postData[end_orddt]}',interval 1 day)";
	if ($postData[start_paydt]) $addWhere .= " and (a.paydt > '{$postData[start_paydt]}' or a.confirmdt > '{$postData[start_paydt]}')";
	if ($postData[end_paydt]) $addWhere .= " and (a.paydt < adddate('{$postData[end_paydt]}',interval 1 day) or a.confirmdt < adddate('{$postData[end_paydt]}',interval 1 day))";
	
	if ($postData[step]) {
		$step = implode(",", $postData[step]);
		$addWhere .= " and c.itemstep in ($step)"; 
	} else {
		$addWhere .= " and c.itemstep in (1,2,91,92,3,4,5,-9,-90)";
	}
	
	if ($postData[catno]) {
		if (is_numeric($postData[catno])) $addWhere .= " and c.catno like '$postData[catno]%'";
	}
	
	if ($postData[release]) {
		$addWhere .= " and b.rid = '$postData[release]'";
	}
	
	if ($postData[goods]) {
		$addWhere .= " and (c.goodsno='$postData[goods]' or c.goodsnm like '%$postData[goods]%')";
	}
	
	if ($postData[sword]) {
		$addWhere .= " and concat(a.payno,a.mid,a.orderer_name) like '%$postData[sword]%'";
	}
} else {
	$addWhere .= " and c.itemstep in (1,2,91,92,3,4,5,-9,-90)";
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