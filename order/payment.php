<?
$podspage = true;
include_once "../_header.php";
include_once "../lib/class.cart.php";

if ($_POST[_sslData]) {
   $ssl = 1;
   $_POST = dec($_POST[_sslData]);
}

$_POST[request] = strip_tags($_POST[request]);

foreach ($_POST as $k=>$v) {
   if (is_string($v)){
      $_POST[$k] = strip_tags($v);
   }
}

if ($_POST[coupon]){
   $_POST[coupon] = stripslashes($_POST[coupon]);
   $coupon = unserialize($_POST[coupon]);
}

if ($_POST[sale_code_coupon]){
   $coupon[sale_code_coupon] = $_POST[sale_code_coupon];
}


if (!$_POST[cartno]){
   msg(_("주문하실 상품을 선택해주세요"),"../order/cart.php");
}

if ($_POST[paymethod]=="b"){
   $query = "select * from exm_bank where cid = '$cid'";
   $res = $db->query($query);
   while ($data = $db->fetch($res)){
      $r_bank[$data[bankno]] = $data[bankinfo];
   }
   $tpl->assign('r_bank',$r_bank);
}

//추가 배송비 계산때문에 receiver_zipcode 코드를 넘겨야 한다.			20171229		chunter
if ($_POST[receiver_zipcode]) $coupon[receiver_zipcode] = $_POST[receiver_zipcode];

$micro = explode(" ",microtime());
$_POST[payno] = $micro[1].sprintf("%03d",floor($micro[0]*1000));
$cart = new Cart($_POST[cartno],$coupon, true, $_POST[dc_partnership]);

if (!$cart->item){
   msg(_("주문하실 상품을 선택해주세요"),"../order/cart.php");
}

//블루포토에서 관리자 로그인 상태로 주문을 하면 주문이 된다 / 15.12.30 / kjm
if(!$ici_admin || !$cfg[ici_admin_order]){
   if ($cart->error_goods){
      msg(_("주문이 불가능한 상품이 존재합니다."),"../order/cart.php");
      exit;
   }
}

foreach ($cart->item as $k=>$v){
   foreach ($v as $vv){
      $pg_goodsnm = $vv[goodsnm];
      break;
   }
}

//$_POST[payprice] = round($cart->totprice - $cart->dc - $cart->dc_coupon - $cart->dc_sale_code_coupon - $cart->dc_partnership - $_POST[emoney]);

### 선입금액,선발행입금액 사용 추가.
$_POST[payprice] = round($cart->totprice - $cart->dc - $cart->dc_coupon - $cart->dc_sale_code_coupon - $cart->dc_partnership - $_POST[emoney] - $_POST[dmoney] - $_POST[pdmoney]);

### 선입금액,선발행입금액 사용 추가.
if ($_POST[dmoney] || $_POST[pdmoney]){
    //선입금액,선발행입금액 조회. / 20181205 / kdk    
    $m_pod = new M_pod();
    $dmoney = $m_pod->getDepositMoney($cid, $sess[mid]);
//debug("dmoney:".$dmoney);
    $pdmoney = $m_pod->getPreDepositMoney($cid, $sess[mid]);
//debug("pdmoney:".$pdmoney);

   if ($dmoney < $_POST[dmoney]){
      msg(_("선입금액이 유효하지 않습니다"),$_SERVER[HTTP_REFERER]);
      exit;
   }

   if ($pdmoney < $_POST[pdmoney]){
      msg(_("선발행입금액이 유효하지 않습니다"),$_SERVER[HTTP_REFERER]);
      exit;
   }

   if ($cart->totprice - $cart->dc - $cart->dc_sale_code_coupon < $_POST[dmoney] + $_POST[pdmoney]){
      msg(_("선입금액 또는 선발행입금액이 유효하지 않습니다"),$_SERVER[HTTP_REFERER]);
      exit;
   } else if ($cart->totprice - $cart->dc == $_POST[dmoney] + $_POST[pdmoney]){
      $_POST[paymethod] = "e";
   }   
}

if ($_POST[emoney]){
   list($emoney) = $db->fetch("select emoney from exm_member where cid = '$cid' and mid = '$sess[mid]'",1);
   if ($emoney < $_POST[emoney] || $cart->totprice - $cart->dc - $cart->dc_sale_code_coupon < $_POST[emoney]){
      msg(_("적립금이 유효하지 않습니다"),$_SERVER[HTTP_REFERER]);
      exit;
   } else if ($cart->totprice - $cart->dc == $_POST[emoney]){
      $_POST[paymethod] = "e";
   }
}

$_POST[escrow] = substr($_POST[paymethod],1);
$_POST[paymethod] = substr($_POST[paymethod],0,1);

switch ($_POST[paymethod]){
   case "c": case "v": case "o": case "m": case "h":
   $selected[paymethod][$_POST[paymethod]] = "selected";
   break;
}

if ($cfg[pg][module] && $cfg[pg][module] != "no") {
	//모바일 결제와 일반 결제로 구분한다.
    if($_POST[mobile_type] == "Y") {
    	//LG mobile 결제		20150104			chunter
    	if ($cfg[pg][module]=="smartxpay") {
    		$_POST[lgd_timestamp] = time();
    		include "./_inc_mobile_pay_lg.php";
		}
		//이니시스 mobile 결제			20160106		chunter
		else if ($cfg[pg][module]=="inicis" || $cfg[pg][module] == "inipaystdweb") {
			include "./_inc_mobile_pay_inipay.php";
		}
		
		//lg 모듈은 통합 모듈이라 별도 mobile.htm 을 구분하지 않는다.			20161017		chunter
		if ($cfg[pg][module] == "smartxpay")
			$tpl->define('pg',"module/pg.{$cfg[pg][module]}.htm");
		else
			$tpl->define('pg',"module/pg.{$cfg[pg][module]}.mobile.htm");
	} else {
		if ($_POST[paymethod] == "c" && $cfg[pg][module] == "tenpay") {
			include "./ins.tenpay.php";
			$_POST[tenpay_url] = $tenpayurl;
		}
		
		//결제 수단에 kakaopay 추가				20180306		chunter 
		if ($_POST[paymethod] == "k") {
			$tpl->define('pg',"module/pg.kakaopay.htm");
		} else {

			//모바일 결제 가능하게 테스트 처리중.... 개발중...    20160921    chunter
			if (($cfg[pg][module] == "inicis" || $cfg[pg][module] == "inipaystdweb" || $cfg[pg][module] == "kcp") && allowMobilePGCheck()) { //if (($cfg[pg][module]=="inipaystdweb") && isMobile())
				if ($cfg[pg][module] == "inicis" || $cfg[pg][module] == "inipaystdweb")
				include "./_inc_mobile_pay_inipay.php";
				$tpl->define('pg',"module/pg.{$cfg[pg][module]}.mobile.htm");
			} else {
				//inipay standart web 결제      20160920    chunter
				if (($_POST[paymethod] == "c" || $_POST[paymethod] == "v" || $_POST[paymethod] == "o") && $cfg[pg][module] == "inipaystdweb") {
					include "./ins.inipaystdweb.php";
				} else if ($cfg[pg][module]=="smartxpay") {
					$_POST[lgd_timestamp] = time();
					include "./_inc_mobile_pay_lg.php";
				}

				$tpl->define('pg',"module/pg.{$cfg[pg][module]}.htm");
			}

			if ($cfg[pg][module] == "lg") {
				$_POST[lgd_timestamp]   = time();
				$_POST[lgd_hashdata] = md5($cfg[pg][lgd_mid].$_POST[payno].$_POST[payprice].$_POST[lgd_timestamp].$cfg[pg][lgd_mertkey]);

				$r_quotaopt = array();
				$r_quotaopt[] = 0;
				for ($i=2;$i<=$cfg[pg][quotaopt];$i++) $r_quotaopt[] = $i;
				$cfg[pg][LGD_INSTALLRANGE] = implode(":",$r_quotaopt);
			}
		}
	}
}

//모바일의 경우 비회원 주문이 있으므로 orderer_name 빈값일수 있다. 	pg.inipaystdweb.mobile.htm 에서 빈값이면 오류처리됨.			20181019		chunter
if (!$_POST['orderer_name']) 
	$_POST['orderer_name'] = $_POST['receiver_name'];
if (is_array($_POST[receiver_mobile])) 
	$_POST['orderer_phone_number'] = implode("-", $_POST[receiver_mobile]);

$_POST[orderinfo] = base64_encode(serialize($_POST));

if (!$_POST['emoney']) $_POST['emoney'] = "0";

//모던 선입금,선발행입금 기능 사용 여부.(알래스카) / 20181210 / kdk
if ($cfg[pod_deposit_money_flag] == "Y") {
    $_POST[paymethod] = "t";
    if (!$_POST['dmoney']) $_POST['dmoney'] = "0";
    if (!$_POST['pdmoney']) $_POST['pdmoney'] = "0";
    
    $tpl->define('tpl', 'order/payment_alaska.htm');
}

$tpl->assign('cart',$cart);
$tpl->assign($_POST);
$tpl->print_('tpl');
?>