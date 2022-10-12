<?
/*
* @date : 20180629
* @author : chunter
* @brief : M2 주문화면에서 쿠폰 화면을 ajax를 통해 불러온다.
* @request :
* @desc :
* @todo :
*/

include "../_header.php";
include "../lib/class.cart.php";


if ($_POST[mode]=="set_coupon"){

	//중복쿠폰 확인.
	if (is_array($_POST[coupon]))
	{
		foreach ($_POST[coupon] as $key => $value) {
			foreach ($_POST[coupon] as $subkey => $subvalue) {
				if ($key != $subkey)
				{
					if (($value == $subvalue) && (is_numeric($value) && is_numeric($subvalue)))
					{
						msg("1개의 쿠폰이 중복 사용되었습니다. 확인해 주세요.", -1);
						exit;
					}
				}			
			}	
		}
	}	

	//장바구니 넘겨주기 위한 coupon 배열 만들기.
	foreach ($_POST[coupon] as $key => $value) {
		$typeKey = $_POST["ctype"][$key];
		$couponPost[$key][$typeKey] = $value;
	}		

	$_POST[cartno] = explode(",",$_POST[cartno]);
	$cart = new Cart($_POST[cartno],$couponPost);

	echo "<script>parent.window.document.getElementById('input_coupon').value = '".serialize($couponPost)."'</script>";
	echo "<script>parent.window.document.getElementById('dc_coupon').value = '".number_format($cart->dc_coupon)."'</script>";
	echo "<script>parent.window.totprice();</script>";
	echo "<script>parent.closePop('pop-coupon');</script>";
  exit;
}

if ($_GET[coupon]) $_POST = $_GET;
if ($_POST[coupon]) $_POST[coupon] = unserialize(stripslashes($_POST[coupon]));
else $_POST[coupon] = array();

if (!$_POST[cartno]){
	$err_msg = _("쿠폰을 등록하실 주문상품이 올바르지 않습니다");
	exit;
}


if (!$err_msg)
{		
	if (is_array($_POST[cartno]))
		$cartno = implode(",",$_POST[cartno]);
	else 
	{
		$cartno = $_POST[cartno];
		//$_POST[cartno] = explode(",", $_POST[cartno]);
	}
	
	
	//debug($_POST[cartno]);	
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
	                  foreach ($catno as $k3=>$v3) {
                      if ($coupon_ok) break;    
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
	   				} else if ($data[coupon_type]=="sale_code"){
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
}

$tpl->assign('mycoupon',$mycoupon);
$tpl->assign('cartitem',$cartitem);
$tpl->assign('cartno',$cartno);
$tpl->assign('err_msg',$err_msg);
$tpl->print_('tpl');
?>