<?
$podspage = true;
$orderpage = true;
include_once "../_header.php";
include_once "../lib/class.cart.php";

$m_emoney = new M_emoney();

if (!$_REQUEST[cartno]) {
   msg(_("주문하실 상품을 선택해주세요"),"../order/cart.php");
   exit;
}

if ($sess[mid]) {
	setExpireEmoney($cid, $sess[mid]);			//유효기간 지난건 사용완료 처리			20180904		chunter
}

//비회원 -> 상품 장바구니 담기 -> 주문 -> 로그인 페이지 이동 -> 로그인 후 주문페이지로 넘어오면
//장바구니 번호가 배열이 아닌 ,로 묶인 형태여서 배열로 잘라준다 / 17.02.07 / kjm
if(!$sess[mid] && $_REQUEST[buyGuest] == 1 || !is_array($_REQUEST[cartno]))
   $_REQUEST[cartno] = explode(",", $_REQUEST[cartno]);

foreach($_REQUEST[cartno] as $v){
   list ($chk, $goodsno, $optno, $addoptno) = $db->fetch("select cartno, goodsno, optno, addoptno from exm_cart where cartno = '$v'",1);
   
   if (!$chk) {
      //msg(_("장바구니에 존재하지 않는 상품입니다."),"../order/cart.php");
   }
  
   ###상점별 옵션 노출여부 설정 변경에 따라 장바구니에서 주문하기 시 옵션 사용여부 체크 2014.10.29 by kdk
   //옵션
   list($opt_view) = $db->fetch("select view from tb_goods_opt_mall_view where cid = '$cid' and goodsno = '$goodsno' and optno = '$optno' ",1);
   if($opt_view == "1") {
      //msg(_("장바구니에 존재하지 않는 상품입니다.[옵션]"),"../order/cart.php");
   }

   //추가옵션
   list($addopt_view) = $db->fetch("select view from tb_goods_addopt_mall_view where cid = '$cid' and goodsno = '$goodsno' and addoptno = '$addoptno' ",1);
   if($addopt_view == "1") {
      //msg(_("장바구니에 존재하지 않는 상품입니다.[추가옵션]"),"../order/cart.php");
   }
}

if (($cfg[member_system][order_system] == "close" || $cfg[member_system][order_system] == "edit_close") && !$sess[mid] && !$_GET[buyGuest] && ($_REQUEST[mobile_type] != "Y" || ($_REQUEST[mobile_type] == "Y" && $cfg[mobile_member_use] == "Y"))) {   
   //&로 합치면 나중에 get방식으로 읽어올때 &에서 짤려서 |로 바꿈 / 14.05.09 / kjm
   $cart_qr = implode(",",$_REQUEST[cartno]);

   $_GET[rurl] = "/order/order.php";

   go("../member/login.php?mode=order&rurl=$_GET[rurl]&cartno=$cart_qr");
   exit;
}

$cart = new Cart($_REQUEST[cartno]);

//쿠폰조외에서 사용하는 파라미터 이므로 모든 스킨에서 공통 사용      20150616    chunter
$cartno = implode(",",$_REQUEST[cartno]);

//배송방법 (주문시 변경하는 경우. 착불)      20141201    chunter
if ($_REQUEST[mode] == "order_shiptype_update") {
   $cart->OrderShipytpeUpdate($_REQUEST[rid_cartno], $_REQUEST[order_shiptype], $sess[mid]);

   //주문배송방법 변경후 다시 불러오기..
   $cart = new Cart($_REQUEST[cartno]);
}

//블루포토에서 관리자 로그인 상태로 주문을 하면 주문이 된다 / 15.12.30 / kjm
if(!$ici_admin || !$cfg[ici_admin_order]){
   if ($cart->error_goods){
      //msg(_("주문이 불가능한 상품이 존재합니다."),"../order/cart.php");
      exit;
   }
}


if($sess[mid]) {
   //20170629 / minks / 적립금 조회를 위해 추가
   $myemoney = $m_emoney->getSumEmoneyLog($cid, $sess[mid]);
    
   //m_member의 getinfo함수로 대체
   $data = $db->fetch("select * from exm_member where cid = '$cid' and mid = '$sess[mid]'");
    
   //20140411 / minks / 기본 배송지 정보를 가져옴
   $data2 = $db->fetch("select * from exm_address where cid='$cid' and mid='' and use_check='Y'",1);

   $data[phone] = explode("-",$data[phone]);
   $data[mobile] = explode("-",$data[mobile]);

   $data[cust_ceo_phone] = explode("-",$data[cust_ceo_phone]);

   $data2[receiver_phone] = explode("-",$data2[receiver_phone]);
   $data2[receiver_mobile] = explode("-",$data2[receiver_mobile]);
}

//20150804 / minks / 이전 주문의 배송지 정보를 가져옴
if ($_REQUEST[mobile_type] == "Y") {
	$query3 = "select a.* from exm_pay a
               inner join exm_ord b on a.payno=b.payno
               inner join exm_ord_item c on a.payno=c.payno and b.ordno=c.ordno
               left join exm_edit d on c.storageid=d.storageid
              where a.cid='$cid' and (c.visible_flag = 'Y' or c.visible_flag = '') and c.itemstep != '0'";

	if ($sess[mid]) $query3 .= " and a.mid='$sess[mid]'";
	else $query3 .= " and d.editkey='$_COOKIE[cartkey]' and a.mid=''";

	$query3 .= " order by a.orddt desc limit 0,1";
	$data3 = $db->fetch($query3);

	$data3[receiver_mobile] = explode("-",$data3[receiver_mobile]);

	//20150810 / minks / 총 결제 금액
	$data3[payprice] = round($cart->totprice - $cart->dc - $cart->dc_coupon - $cart->dc_sale_code_coupon - $_POST[emoney]);

	//20150812 / minks / 무통장입금시 입금계좌 조회
	$query2 = "select * from exm_bank where cid = '$cid'";
	$res2 = $db->query($query2);
	while ($data4 = $db->fetch($res2)) {
      $r_bank[$data4[bankno]] = $data4[bankinfo];
	}

	$tpl->assign('data3',$data3);
	$tpl->assign('r_bank',$r_bank);
}

if (!$data[emoney]) $data[emoney] = 0;
$selected[phone][$data[phone][0]] = "selected";
$selected[mobile][$data[mobile][0]] = "selected";
$selected[cust_ceo_phone][$data[cust_ceo_phone][0]] = "selected";

if (!$cfg[pg][paymethod] || !is_array($cfg[pg][paymethod])){
   $cfg[pg][paymethod] = array();
}

if (!$cfg[pg][e_paymethod] || !is_array($cfg[pg][e_paymethod])){
   $cfg[pg][e_paymethod] = array();
}

$cfg[pg][paymethod] = array_merge($cfg[pg][paymethod],$cfg[pg][e_paymethod]);

$dummy = array();

if ($data[bid]){
   list($corrected_paymethod) = $db->fetch("select corrected_paymethod from exm_business where cid = '$cid' and bid = '$data[bid]'",1);

   $corrected_paymethod = explode(",",$corrected_paymethod);
   if (!is_array($cfg[pg][paymethod])) $cfg[pg][paymethod] = array();
   $corrected_paymethod = array_diff($corrected_paymethod,array_diff($corrected_paymethod,$cfg[pg][paymethod]));
   foreach ($cfg[pg][e_paymethod] as $v){
      if (in_array(substr($v,0,1),$corrected_paymethod)){
         $corrected_paymethod[] = $v;
      }
   }
   $cfg[pg][paymethod] = array();
   $cfg[pg][paymethod] = $corrected_paymethod;
}

foreach ($r_paymethod as $k=>$v){
   if (in_array($k,$cfg[pg][paymethod])) $dummy[$k] = $k;
}

if (($_SERVER[REMOTE_ADDR]=="210.96.184.229" || strpos($_SERVER[SERVER_ADDR], "192.168.0.") > -1) && $ici_admin) {
   $tpl->assign('admin_tag',"<script>
      function adminInput(){
      document.fm.receiver_name.value='"._("아이락")."';
      document.fm['receiver_mobile[0]'].value='010';
      document.fm['receiver_mobile[1]'].value='111';
      document.fm['receiver_mobile[2]'].value='1234';
      document.fm.receiver_zipcode.value='123-456';
      document.fm.receiver_addr.value='"._("서울 금천구 가산동")."';
      document.fm.receiver_addr_sub.value='123-4567';
      }
      </script><input type='button' onclick='adminInput();' value='"._("테스트 정보 입력")."'>");
}

//제작사 별로 배송비가 발생하기 때문에 제작사로 cartno를 묶는다 / 16.03.24 / kjm
if (is_array($cart->item))
{
	foreach($cart->item as $kk => $vv){
	   foreach($vv as $kkk=>$vvv){
	      $rid_cartno[$kk][] = $vvv[cartno];
	   }
	}
}

$cfg[pg][paymethod] = $dummy;

if ($cfg[skin] == "m_default") $tpl->define('itembox',"module/itembox.htm");
if ($language_locale == "ja_JP") $tpl->define('jp_add_script',"order/jp_order_script.htm");
else $tpl->define('jp_add_script',"main/blank.htm");
//$tpl->define('jp_add_script',"order/jp_order_script.htm");

//debug($data);

if($cfg[skin_theme] == "I1"){
   $url = "http://www.i-scream.co.kr/api/iscreamMemberBriefInfo?m_id=".$sess[mid];
   $ret = RestCurl($url, $json, $http_status);
   $mem_data = json_decode($ret,1);
   
   $mem_data[memberName] = urldecode($mem_data[memberName]);
   $mem_data[memberEmail] = urldecode($mem_data[memberEmail]);
   $mem_data[memberSchoolAddr] = urldecode($mem_data[memberSchoolAddr]);
   $mem_data[memberSchoolName] = urldecode($mem_data[memberSchoolName]);
   //debug($mem_data);
   
   $data[mid]     = $mem_data[memberId];
   $data[name]    = $mem_data[memberName];
   $data[address] = $mem_data[memberSchoolAddr];
   $data[address_sub] = "";
   $data[phone]   = explode("-", $mem_data[memberSchoolTel]);
   $data[mobile]  = explode("-", $mem_data[memberPhone]);
   $data[email]   = $mem_data[memberEmail];
   $data[zipcode] = $mem_data[memberSchoolPost];
}
//debug($data);

if($cfg[ssl_use] == "Y") {
   $ssl_action = $cfg[ssl_url]."/order/payment.php";
   $tpl->assign('ssl_action', $ssl_action);
}

//모던 선입금,선발행입금 기능 사용 여부.(알래스카) / 20181210 / kdk
if ($cfg[pod_deposit_money_flag] == "Y") {
    //선입금액,선발행입금액 조회. / 20181205 / kdk    
    $m_pod = new M_pod();
    $dmoney = $m_pod->getDepositMoney($cid, $sess[mid]);
//debug("dmoney:".$dmoney);
    $pdmoney = $m_pod->getPreDepositMoney($cid, $sess[mid]);
//debug("pdmoney:".$pdmoney);
    $tpl->define('tpl', 'order/order_alaska.htm');
}

$partner_cartno = implode(",", $_REQUEST[cartno]);
$tpl->assign('partner_cartno',$partner_cartno);

$tpl->assign($data);
$tpl->assign('data2',$data2);
$tpl->assign('cart',$cart);
$tpl->assign('rid_cartno', $rid_cartno);
$tpl->assign('myemoney', $myemoney);
$tpl->assign('dmoney', $dmoney);
$tpl->assign('pdmoney', $pdmoney);
$tpl->print_('tpl');
?>