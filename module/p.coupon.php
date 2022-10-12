<?

/*
* @date : 20180321
* @author : minks
* @brief : 상품에 다중 카테고리가 설정됐을 경우 사용가능한 쿠폰 정보를 못가져오는 현상 수정
* @request :
* @desc :
* @todo :
*/

include "../_header.php";
include "../lib/class.cart.php";


if ($_POST[mode]=="set_coupon"){
	$_POST[cartno] = explode(",",$_POST[cartno]);

	$cart = new Cart($_POST[cartno],$_POST[coupon]);
	
	if ($_POST[mobile_type] == "Y") {
		echo "<script>parent.window.document.getElementById('input_coupon').value = '".serialize($_POST[coupon])."'</script>";
		echo "<script>parent.window.document.getElementById('dc_coupon').value = '".str_replace(",", "", number_format($cart->dc_coupon))."'</script>";
		echo "<script>parent.window.totprice('dc_coupon');</script>";
	} else {
		echo "<script>parent.opener.document.getElementById('input_coupon').value = '".serialize($_POST[coupon])."'</script>";
		echo "<script>parent.opener.document.getElementById('dc_coupon').value = '".str_replace(",", "", number_format($cart->dc_coupon))."'</script>";
      //echo "<script>parent.opener.document.getElementById('dc_partnership').value = '0'</script>";
		echo "<script>parent.opener.totprice();</script>";
		echo "<script>parent.window.close();</script>";
	}
   
   exit;
}

if ($_GET[coupon]) $_GET[coupon] = unserialize(stripslashes($_GET[coupon]));
else $_GET[coupon] = array();

if (!$_GET[cartno]){
	msg(_("쿠폰을 등록하실 주문상품이 올바르지 않습니다"),"close");
	exit;
}

$cartno = explode(",",$_GET[cartno]);

$cart = new Cart($cartno);

if (is_array($cart->item)){
	foreach ($cart->item as $v){
		foreach ($v as $vv){
			$cartitem[] = $vv;
		}
	}
}

$query = "
select
	a.*,b.coupon_setdt,b.no,b.coupon_used_money,b.coupon_able_money,
	if (
	coupon_period_system='date',coupon_period_edate,
		if(
		coupon_period_system='deadline',
		left(adddate(coupon_setdt,interval coupon_period_deadline-1 day),10),
		coupon_period_deadline_date
		)
	) usabledt
from
	exm_coupon a
	inner join exm_coupon_set b on a.cid = b.cid and a.coupon_code = b.coupon_code and mid = '$sess[mid]'
where
	a.cid = '$cid'
	and coupon_use = 0
	and
	(
		(
		coupon_period_system = 'date'
		and coupon_period_sdate <= curdate()
		and coupon_period_edate >= curdate()
		) or
		(
		coupon_period_system = 'deadline'
		and adddate(coupon_setdt,interval coupon_period_deadline day) >= adddate(curdate(),interval 1 day)
		) or
		(
		coupon_period_system = 'deadline_date'
		and coupon_period_deadline_date >= curdate()
		) 
	)
order by coupon_setdt desc
";
$res = $db->query($query);
$coupon = array();
while ($data = $db->fetch($res)){
	$coupon[] = $data;
}

if (is_array($cartitem)){
   foreach ($cartitem as $k=>$item){
   	foreach ($coupon as $data){
   
   		$coupon_ok = false;

   		switch ($data[coupon_range]){
   			case "all":
   
   				$coupon_ok = true;

				break;

   			case "category":
   
               list($catno) = $db->fetch("select group_concat(catno order by cat_index separator ',') from exm_goods_link where cid = '$cid' and goodsno = '$item[goodsno]'",1);
               $data[coupon_catno] = explode(",",$data[coupon_catno]);
               $catno = ($catno) ? explode(",", $catno) : array();

               foreach ($data[coupon_catno] as $k2=>$v2){
                  if ($coupon_ok) break;
                  foreach ($catno as $k3=>$v3) {
                     if (substr($v3,0,strlen($v2))==$v2) {
                        $coupon_ok = true;
                     } else {
                        $coupon_ok = false;
                     }
                  }
               }
   
               break;

   			case "goods":

   				$data[coupon_goodsno] = explode(",",$data[coupon_goodsno]);
   				if (in_array($item[goodsno],$data[coupon_goodsno])){
   					$coupon_ok = true;
   				} else {
   					$coupon_ok = false;
   				}

				break;
   		}
         $mycoupon[$data[no]] = $data;
   
   		if (!$coupon_ok) continue;
         
   		switch ($data[coupon_way]){
   
   			case "price":

   				if ($data[coupon_type]=="discount") $data[coupon_dc] = $data[coupon_price];
   				else if ($data[coupon_type]=="saving") $data[coupon_dc] = $data[coupon_price];
   				if ($item[payprice] <= $data[coupon_dc]) $data[coupon_dc] = $item[payprice];

				break;

   			case "rate":
   
   				if ($data[coupon_type]=="discount"){
   					$data[coupon_dc] = round($item[saleprice] * $data[coupon_rate]/ 100,-1);
   					if ($data[coupon_price_limit] && $data[coupon_price_limit] < $data[coupon_dc]) $data[coupon_dc] = $data[coupon_price_limit];
   					if ($item[payprice] <= $data[coupon_dc]) $data[coupon_dc] = $item[payprice];
   				} else if ($data[coupon_type]=="saving"){
   					$data[coupon_dc] = round($item[totreserve] * $data[coupon_rate]/ 100,-1);
   					if ($data[coupon_price_limit] && $data[coupon_price_limit] < $data[coupon_dc]) $data[coupon_dc] = $data[coupon_price_limit];
   					if ($item[payprice] <= $data[coupon_dc]) $data[coupon_dc] = $item[payprice];
   				}

				break;
   		}

   		if ($data[coupon_dc] <= 0 && $data[coupon_type]!="coupon_money") continue;

   		if ($data[coupon_min_ordprice] && $item[payprice] < $data[coupon_min_ordprice]) continue;

   		$cartitem[$k][coupon][$data[coupon_type]][] = $data;
         
   		$mycoupon[$data[no]] = $data;
   	}
   }
}

if ($item)
   $tpl->assign($item);

$tpl->assign('mycoupon',$mycoupon);
$tpl->assign('cartitem',$cartitem);
$tpl->print_('tpl');
?>