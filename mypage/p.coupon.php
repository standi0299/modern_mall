<?

include "../_header.php";

$m_etc = new M_etc();
$m_goods = new M_goods();

$data = $m_etc->getCouponInfo($cid, $_GET[coupon_code]);

if ($data[coupon_range] == "category") {
	$data[coupon_catno] = explode(",", $data[coupon_catno]);
} else if ($data[coupon_range] == "goods") {
	$data[coupon_goodsno] = explode(",", $data[coupon_goodsno]);
	
	foreach ($data[coupon_goodsno] as $v) {
		$tmp = $m_goods->getCouponGoodsInfo($cid, $v);
		$tmp[ret_price] = $tmp[price];
		
		if ($data[coupon_type] == "discount") {
			switch ($data[coupon_way]) {
				case "price":
					$tmp[ret_price] = $tmp[price] - $data[coupon_price];
					break;
				case "rate":
					$tmp[ret_price] = round($tmp[price]*((100-$data[coupon_rate])/100), -1);
					break;
			}
		}
		
		if ($tmp[ret_price] < 0) $tmp[ret_price] = 0;
		
		$goods[] = $tmp;
	}
}

if ($_GET[no]) {
	$addWhere = "where no = '$_GET[no]'";
	$have = $m_etc->getCouponSetInfo($cid, $addWhere);
	
	if ($have[coupon_setdt] && $data[coupon_period_deadline]) {
		$data[coupon_period_deadline_dt] = date("Y-m-d", strtotime($have[coupon_setdt]." + ".($data[coupon_period_deadline]-1)." days"));
	}
}

$tpl->assign("goods",$goods);
$tpl->assign($data);
$tpl->print_('tpl');

?>