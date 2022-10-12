<?
/*
* @date : 20190226
* @author : 변세웅
* @brief : 범아, 결제 방식 (무통장입금, 신용거래) 순서 변경
* @request :
* @desc :범아, 결제 페이지 순서만 변경했음
* @todo : 운영서버 테스트 진행
*/

$podspage = true;
$orderpage = true;
include_once "../_header.php";
include_once "../lib/class.cart.php";

$m_emoney = new M_emoney();
$m_order = new M_order();

//비회원 -> 상품 장바구니 담기 -> 주문 -> 로그인 페이지 이동 -> 로그인 후 주문페이지로 넘어오면
//장바구니 번호가 배열이 아닌 ,로 묶인 형태여서 배열로 잘라준다 / 17.02.07 / kjm
if(!$sess[mid] && $_REQUEST[buyGuest] == 1 || !is_array($_REQUEST[cartno]))
$_REQUEST[cartno] = explode(",", $_REQUEST[cartno]);

foreach($_REQUEST[cartno] as $v){
   list ($chk, $goodsno, $optno, $addoptno) = $db->fetch("select cartno, goodsno, optno, addoptno from exm_cart where cartno = '$v'",1);

	if (!$chk) {
		msg(_("장바구니에 존재하지 않는 상품입니다."),"../order/cart.php");
	}
}

if (($cfg[skin_theme] == "P1" || ($cfg[skin_theme] != "P1" && $cfg[member_system][order_system] != "open")) && !$sess[mid] && !$_GET[buyGuest] && ($_REQUEST[mobile_type] != "Y" || ($_REQUEST[mobile_type] == "Y" && $cfg[mobile_member_use] == "Y"))) {   
	//&로 합치면 나중에 get방식으로 읽어올때 &에서 짤려서 |로 바꿈 / 14.05.09 / kjm
	$cart_qr = implode(",",$_REQUEST[cartno]);

	$_GET[rurl] = "/order/orderpayment.php";
	go("../member/login.php?mode=order&rurl=$_GET[rurl]&cartno=$cart_qr");
	exit;
}


if ($sess[mid]) {
	setExpireEmoney($cid, $sess[mid]);			//유효기간 지난건 사용완료 처리			20180904		chunter
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


if($sess[mid]) 
{ 
   //m_member의 getinfo함수로 대체
   $data = $db->fetch("select * from exm_member where cid = '$cid' and mid = '$sess[mid]'");
   $data[phone] = explode("-",$data[phone]);
   $data[mobile] = explode("-",$data[mobile]);
   
   if ($cfg[skin_theme] == "P1") {
   	  $data[email] = explode("@",$data[email]);
	  $selected[email][$data[email][1]] = "selected";
   }
   
	$basicAddress = $m_order->getBasicAddress($cid, $sess[mid]);
	$basicAddress[receiver_mobile] = explode("-",$basicAddress[receiver_mobile]);
	//debug($basicAddress);
	//20170629 / minks / 적립금 조회를 위해 추가
   $data[myemoney] = $m_emoney->getSumEmoneyLog($cid, $sess[mid]);
}

if (!$data[emoney]) $data[emoney] = 0;

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


//제작사 별로 배송비가 발생하기 때문에 제작사로 cartno를 묶는다 / 16.03.24 / kjm
foreach($cart->item as $kk => $vv){
	foreach($vv as $kkk=>$vvv){
		$rid_cartno[$kk][] = $vvv[cartno];
	}
}

$cfg[pg][paymethod] = $dummy;
$partner_cartno = implode(",", $_REQUEST[cartno]);

//범아, 결제 방식 (무통장입금, 신용거래) 순서 변경
if($cfg_center[center_cid] =="mbumapnp" || $cid =="sss")
{
	$cfg[pg][paymethod] = array_reverse($cfg[pg][paymethod]);
}

//결제 모듈은 miodio에서 사용하는 inicis 중 inipaystdweb 만 적용한다.		20180628		chunter
if ($cfg[pg][module] == "inipaystdweb") 
{
	//if (allowMobilePGCheck()) { //if (($cfg[pg][module]=="inipaystdweb") && isMobile())
	if (isMobile()) 
	{		
		include "./_inc_mobile_pay_inipay.php";
		$tpl->define('pg',"module/pg.{$cfg[pg][module]}.mobile.htm");
	} else {				
		include "./ins.inipaystdweb.php";
		$tpl->define('pg',"module/pg.{$cfg[pg][module]}.htm");
	}
} else 
	$tpl->define('pg',"common/blank.htm");

ob_start();
//include_once "layer.coupon.php";
//$coupon_content = ob_get_contents();  
ob_end_clean();
	
	//내쿠폰 수량 구하기
	$query = "select count(a.coupon_code) as cnt from
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
";
list($my_coupon_cnt) = $db->fetch($query, 1);


//무통장입금 계좌번호 가져오기
$bank_list = $db->listArray("select * from exm_bank where cid = '$cid'");
foreach($bank_list as $k => $v){
   $r_bank[$v[bankno]] = $v[bankinfo];
}


// if($cfg[ssl_use] == "Y" && $cfg[skin_theme] == "M2") {
// 	// 서비스도메인과 현재 접속한 도메인이 같을경우 ssl은 접속한 도메인으로 변경
// 	if(trim($cfg[urlService]) && $_SERVER[HTTP_HOST] == $cfg[urlService])
// 		$ssl_action = "https://".$_SERVER[HTTP_HOST]."/order/indb.php";
// 	// 서비스도메인과 현재 접속한 도메인이 다른경우 ssl은 ssl설정url로 처리
// 	else
// 		$ssl_action = $cfg[ssl_url]."/order/indb.php";
	
//    $tpl->assign('ssl_action', $ssl_action);
// }
//시안요청 정보가 있을경우 payno 업데이트.(시안요청은 1개 주문만 넘어옴) / 20190122 / kdk
//if (count($_POST[cartno]) == 1) {
//    $cart->setDesignUpdate($_POST[cartno][0], $_POST[payno]);    
//}

//수량(매x건) 처리 / 20190207 / kdk
/*
if ($cfg[skin_theme] == "B1") {
    foreach ($cart->item as $key => $val) {
        foreach ($val as $k => $v) {
            if ($v[ext_json_data]) {
                $ext_data = json_decode($v[ext_json_data], 1);

                //시안요청이면...
                //if (array_key_exists('order_inspection', $ext_data)) {
                    //매,건.
                    if ($v[est_order_data]) {
                        $est_arr = json_decode($v[est_order_data], true);
                        $v[order_cnt_select] = $est_arr[order_cnt_select];
                        $v[unit_order_cnt] = $est_arr[unit_order_cnt];
                    }
                //}
                //debug($v);
                $cart->item[$key][$k] = $v;
            }
        }
    }
    //debug($cart->item);
}
*/
if ($cfg[skin_theme] == "B1") {
    
    include "../print/lib_const_print.php";     //옵션 항목 설정 및 가격설정
    
    //시안요청 주문건 장바구니에 노출안함. / 20190118 / kdk
    //debug($cart->item);
    $cart_item = array();
    if (!empty($cart->item)) {
        foreach ($cart->item as $key => $value) {
            foreach ($value as $k => $v) {
                $est_order_option_desc = "";
                //debug($v[extra_option]);

                if ($r_est_print_product[$v[extra_option]]) { //견적상품일 경우.
                    //옵션변경 안함.
                    $v[option_mod_enabled] = "N";

                    //매,건.
                    if ($v[est_order_data]) {
                        $est_arr = json_decode($v[est_order_data], true);
                        //debug($est_arr);
                        $v[order_cnt_select] = $est_arr[opt_page];
                        $v[unit_order_cnt] = $est_arr[cnt];

                        if ($est_arr[opt_size] == "USER") {
                            $est_order_option_desc .= "사이즈:user(". $est_arr[cut_width] ."x". $est_arr[cut_height] . ")<br>";
                        } else {
                            //$est_order_option_desc .= "사이즈:". $est_arr[work_width] ."x". $est_arr[work_height] . "<br>";
                            $est_order_option_desc .= "사이즈:{$r_ipro_pr_standard_size[$est_arr[opt_size]][name]}({$r_ipro_pr_standard_size[$est_arr[opt_size]][size_x]} x {$r_ipro_pr_standard_size[$est_arr[opt_size]][size_y]})<br>";
                        }
                        
                        /*                        
                        if ($est_arr[cut_width] && $est_arr[cut_height]) {
                            $est_order_option_desc .= "사이즈:". $est_arr[cut_width] ."x". $est_arr[cut_height] . "<br>";
                        } else {
                            $est_order_option_desc .= "사이즈:". $est_arr[work_width] ."x". $est_arr[work_height] . "<br>";
                        }
                        */
                        
                        //debug($v[est_order_option_desc]);
                        //제목,규격,수량,메모 항목 제외.
                        if ($v[est_order_option_desc]) {
                            $v[est_order_option_desc] = str_replace("[", "", $v[est_order_option_desc]);
                            $desc_arr = explode("]", $v[est_order_option_desc]);
                            //debug($desc_arr);
                            if ($desc_arr) {
                                foreach ($desc_arr as $kk => $vv) {
                                    if ($vv && strpos($vv, "내지") !== false)
                                        $est_order_option_desc .= str_replace("내지::/", "용지:", $vv) . "<br>";
                                    if ($vv && strpos($vv, "후가공") !== false)
                                        $est_order_option_desc .= str_replace(";", "<br>", str_replace("후가공::", "", $vv));
                                    //if ($vv && strpos($vv, "메모") !== false)
                                        //$est_order_option_desc .= str_replace(";", "<br>", str_replace("메모:", "요청사항:", $vv));
                                }
                                //debug($est_order_option_desc);
                            }
                            $v[est_order_option_desc] = $est_order_option_desc;
                        }                        
                    }                    
                } else {
                    //매,건.
                    if ($v[est_order_data]) {
                        $est_arr = json_decode($v[est_order_data], true);
                        $v[order_cnt_select] = $est_arr[order_cnt_select];
                        $v[unit_order_cnt] = $est_arr[unit_order_cnt];
                    }
    
                    //수량: 항목 제외.
                    if ($v[est_order_option_desc]) {
                        $desc_arr = explode("<br>", $v[est_order_option_desc]);
                        if ($desc_arr) {
                            foreach ($desc_arr as $kk => $vv) {
                                if ($vv && strpos($vv, "수량") === false)
                                $est_order_option_desc .= $vv . "<br>";
                            }
                        }
                        $v[est_order_option_desc] = $est_order_option_desc;
                    }                    
                }

                /*
                if ($v[ext_json_data]) {
                    //$ext_data = json_decode($v[ext_json_data], 1);
                    $cart_item[$key][$k] = $v;
                }
                else {
                    $cart_item[$key][$k] = $v;
                }
                */
                
                $cart_item[$key][$k] = $v;
            }
        }
    }

    $cart->item = $cart_item;
    //debug($cart->item);
}

//절사 표시문구 190530 jtkim
$cutmoney_res = $cart->setCuttingMoneyText();
//절사 설정 190610 jtkim
$cutmoney_cfg = $cart->setCuttingCfg();
//할인 배송 설정 190703 jtkim
$ship_dc = $cart->setShipDcCfg();

//비회원시 동의 문구
$nonmember_agreement = nl2br(getCfg('nonmember_agreement'));

$tpl->assign('nonmember_agreement',$nonmember_agreement);

$tpl->assign(array(
	'cutmoney_flag' => $cutmoney_res[flag][value],     //절사 안내문구 표시 여부
	'cutmoney_text' => $cutmoney_res[text][value],     //절사 안내문구 텍스트
	'cutmoney_money' => $cutmoney_res[money][value],   //절사 금액 표시 여부
	'cutmoney_use' => $cutmoney_cfg[c_use][value],     //절사 여부 (1:사용 2:미사용)
	'cutmoney_type' => $cutmoney_cfg[c_type][value],   //절사금액 (1:1의자리 ,2:10의자리, 3:100의자리)
	'cutmoney_op' => $cutmoney_cfg[c_op][value],       //절사방식 (F:버림처리,C:올림처리,R:반올림처리)
    'cutmoney_mod' => $cutmoney_cfg[c_mod][value],     //나머지
));
$tpl->assign('ship_dc',$ship_dc);
$tpl->assign('partner_cartno',$partner_cartno);
$tpl->assign($data);
$tpl->assign('cart',$cart);
$tpl->assign('rid_cartno', $rid_cartno);
$tpl->assign('basicAddress', $basicAddress);
$tpl->assign('r_bank',$r_bank);
$tpl->assign('my_coupon_cnt',$my_coupon_cnt);
$tpl->print_('tpl');

?>