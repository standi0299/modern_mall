<?
$podspage = true;
include_once "../_header.php";

$this_time = get_time();
$debug_data = "";

//옵션이 있으면 옵션의 편집코드 값을 넣어준다 / 16.12.30 / kjm
if($_REQUEST[productid]) $_REQUEST[podsno] = $_REQUEST[productid];

$debug_data .= "-4 - " . number_format(get_time() - $this_time, 4). _("초")."<BR>";
//include "../lib/class.cart.php";
//$debug_data .= "-3 - " . number_format(get_time() - $this_time, 4). "초<BR>";
include_once "../lib/class.cart.v2.php";
//include_once "../lib/lib_pods.php";     사용하지 않음     20150403    chunter

$debug_data .= "-2 - " . number_format(get_time() - $this_time, 4). _("초")."<BR>";
include_once "../models/m_member.php";
$debug_data .= "-1 - " . number_format(get_time() - $this_time, 4). _("초")."<BR>";

$cartpage = true;
$cart = new CartV2();

//뒤로, orderlist.php 에서 넘어오는 경우 체크 2014.11.18 by kdk
if ($_REQUEST[storageid]){
   list($storageid) = $db->fetch("select storageid from exm_ord_item where storageid = '$_REQUEST[storageid]'",1);

   if($storageid == $_REQUEST[storageid]) {
      msg(_("뒤로가기 버튼을 사용할 수 없습니다.[이미 처리된 주문입니다.]"));
		echo "<script>parent.location.href = '../mypage/orderlist.php';</script>";
		exit;
   }
}

//회원만 주문할수 있다.
if (!$sess[mid]){
   msg(_("로그인 세션이 유효하지 않습니다."));
   echo "<script>parent.location.href = '../member/login.php?rurl=/mypage/orderlist.php';</script>";
   exit;
}

$step = 2;      //결제 완료??

unset($data);
$debug_data .= "0 - " . number_format(get_time() - $this_time, 4). _("초")."<BR>";
if ($_REQUEST[goodsno]){
	list($podskind, $pods_use) = $db->fetch("select podskind,pods_use from exm_goods where goodsno = '$_REQUEST[goodsno]'",1);
}

if ($_REQUEST[ea] < 1 || strpos($_REQUEST[ea],".")!==false){
	if ($_REQUEST[storageid]){
		$_REQUEST[ea] = 1;
	} else {
		msg(_("수량이 올바르지 않습니다."),-1);
		exit;
	}
}

$debug_data .= "1 - " . number_format(get_time() - $this_time, 4). _("초")."<BR>"; 

if($_REQUEST[pods_use]) $pods_use = $_REQUEST[pods_use];
if($_REQUEST[podskind]) $podskind = $_REQUEST[podskind];
  
/*
$log_dir = "../data/tmp/pods_return/";
if (!is_dir($log_dir)){
   mkdir($log_dir,0707);
	chmod($log_dir,0707);
}
$log_file = date("YmdHis")."_".uniqid();
$fp = fopen($log_dir.$log_file,"w");
fwrite($fp,$_REQUEST[storageid]);
*/

$pods_storageID = "";
if ($pods_use == 2){
  //{"exit_code":"1","view":"editor","user_id":"odyssey","userNum":"","site_id":"ibookcover","uploaded_list":[{"rsid":"IB03420131220323100011", "session_param":"editor=3231&storageid=&pid=cover_layout_W-X176&siteid=ibookcover&userid=odyssey&dp=1&minp=1&maxp=1&opt=1&p_siteid=podgroup&dpcnt=1&sessionparam=sub_option%3a%2cparam%3aeyJnb29kc25vIjoiNDgiLCJvcHRubyI6IiIsImFkZG9wdCI6IiJ9%2cpname%3acover_layout_W-X176&adminmode=Y&", "title":"", "order_count":"", "order_option":""}]}
  //pods 2.0 연동 처리
	$ret = stripslashes($_REQUEST[storageid]);
 	$editor_return_json = $ret;
	$ret = json_decode($ret,1);

	foreach ($ret[uploaded_list] as $k=>$v){
		parse_str($v[session_param],$retdata);

		$retdata[sessionparam] = explode(",",urldecode($retdata[sessionparam]));
		if ($retdata[sessionparam][1]){
			$retdata[sessionparam][1] = str_replace("param:","",$retdata[sessionparam][1]);
		} else if ($retdata[sessionparam][0]){
			$retdata[sessionparam][1] = str_replace("param:","",$retdata[sessionparam][0]);
		}

		$indata = json_decode(base64_decode($retdata[sessionparam][1]),1);

		if (!is_array($indata[addopt])) $indata[addopt] = explode(",",$indata[addopt]);
		$data[$k][goodsno]		= $indata[goodsno];
		$data[$k][storageid]	= $v[rsid];
		$pods_storageID = $data[$k][storageid];      //pods 2.0 storage ID
		$data[$k][optno]		= $indata[optno];
		$data[$k][ea]			= $v[order_count];
		if (!$data[$k][ea])	$data[$k][ea] = 1;
		$data[$k][addopt]		= $indata[addopt];
		$data[$k][title]		= $v[title];
		
		#복수 편집기 처리 pods_use, podskind, podsno pods_editor.js에서 form에 추가하여 넘기 2016.05.23 by kdk  
		if($_REQUEST[pods_use] && $_REQUEST[podskind] && $_REQUEST[podsno]) {
			$data[$k][pods_use] = $_REQUEST[pods_use];
			$data[$k][podskind] = $_REQUEST[podskind];
			$data[$k][podsno] = $_REQUEST[podsno];
      }
   }

   $debug_data .=  "2 - " . number_format(get_time() - $this_time, 4). _("초")."<BR>";

} else if (in_array($podskind,array("3130","3110","3112")) && $_REQUEST[mode2]!="reorder"){

   $_REQUEST[storageid] = stripslashes($_REQUEST[storageid]);
	$_REQUEST[storageid] = json_decode($_REQUEST[storageid],1);

	foreach($_REQUEST[storageid][uploaded_list] as $k=>$v){
		$_data = explode("&",$v[session_param]);

		foreach ($_data as $k2=>$v2){
			$v2 = explode("=",$v2);
			$retdata[$v2[0]] = $v2[1];
		}
		$retdata[sessionparam] = explode(",",$retdata[sessionparam]);
		if ($retdata[sessionparam][1]){
			$retdata[sessionparam][1] = str_replace("param:","",$retdata[sessionparam][1]);
		} else if ($retdata[sessionparam][0]){
			$retdata[sessionparam][1] = str_replace("param:","",$retdata[sessionparam][0]);
		}
		$indata = json_decode(base64_decode($retdata[sessionparam][1]),1);

		if (!is_array($indata[addopt])) $indata[addopt] = explode(",",$indata[addopt]);
		$data[$k][goodsno] = $indata[goodsno];
		$data[$k][storageid] = $v[rsid];
		$data[$k][optno] = $indata[optno];
		$data[$k][ea] = $v[order_count];
		if (!$data[$k][ea]) $data[$k][ea] = 1;
		$data[$k][addopt] = $indata[addopt];
		$data[$k][title] = $v[title];
		
		#복수 편집기 처리 pods_use, podskind, podsno pods_editor.js에서 form에 추가하여 넘기 2016.05.23 by kdk  
		if($_REQUEST[pods_use] && $_REQUEST[podskind] && $_REQUEST[podsno]) {
			$data[$k][pods_use] = $_REQUEST[pods_use];
			$data[$k][podskind] = $_REQUEST[podskind];
			$data[$k][podsno] = $_REQUEST[podsno];
		}			
	}
} else if (in_array($podskind,array("3180","28")) && $_REQUEST[mode2]!="reorder"){

	if ($podskind=="28"){
		list($_REQUEST[storageid]) = array_slice(explode(";",$_REQUEST[storageid]),-1);
		$_REQUEST[storageid] = str_replace("sId=","",$_REQUEST[storageid]);
	}

	$_REQUEST[storageid] = explode("&",$_REQUEST[storageid]);
	if (!is_array($_REQUEST[addopt])) $_REQUEST[addopt] = explode(",",$_REQUEST[addopt]);

	foreach ($_REQUEST[storageid] as $k=>$v){
		$data[$k][goodsno] = $_REQUEST[goodsno];
		$data[$k][storageid] = $v;
		$data[$k][optno] = $_REQUEST[optno];
		$data[$k][ea] = $_REQUEST[ea];
		$data[$k][addopt] = $_REQUEST[addopt];
	}
} else if (in_array($podskind,array("3010","3011","3030","3040","3041","3042","3043","3050","3051","3052","3060")) && $_REQUEST[mode2]!="reorder"){
   //20141016 / minks / 재주문일 경우 제외        
	// pods 2.0 사용여부를 조건으로 변경처리     20140106    chunter
	//if ($pods_use == 2){
   $ret = stripslashes($_REQUEST[storageid]);
  	$ret = json_decode($ret,1);

	foreach ($ret[uploaded_list] as $k=>$v){
      parse_str($v[session_param],$retdata);

		$retdata[sessionparam] = explode(",",urldecode($retdata[sessionparam]));
		if ($retdata[sessionparam][1]){
			$retdata[sessionparam][1] = str_replace("param:","",$retdata[sessionparam][1]);
		} else if ($retdata[sessionparam][0]){
			$retdata[sessionparam][1] = str_replace("param:","",$retdata[sessionparam][0]);
		}

		$indata = json_decode(base64_decode($retdata[sessionparam][1]),1);

		if (!is_array($indata[addopt])) $indata[addopt] = explode(",",$indata[addopt]);
		$data[$k][goodsno]		= $indata[goodsno];
		$data[$k][storageid]	= $v[rsid];
		$data[$k][optno]		= $indata[optno];
		$data[$k][ea]			= $v[order_count];
		if (!$data[$k][ea])	$data[$k][ea] = 1;
		$data[$k][addopt]		= $indata[addopt];
		$data[$k][title]		= $v[title];
		
		#복수 편집기 처리 pods_use, podskind, podsno pods_editor.js에서 form에 추가하여 넘기 2016.05.23 by kdk  
		if($_REQUEST[pods_use] && $_REQUEST[podskind] && $_REQUEST[podsno]) {
			$data[$k][pods_use] = $_REQUEST[pods_use]; 
			$data[$k][podskind] = $_REQUEST[podskind]; 
			$data[$k][podsno] = $_REQUEST[podsno];
		}				
	}
} else {
	$data[0][goodsno] = $_REQUEST[goodsno];
	if ($_REQUEST[optno]){
		if ($_REQUEST[mode2] != "reorder"){
			$_REQUEST[optno] = array_slice($_REQUEST[optno],-1);
			$data[0][optno] = $_REQUEST[optno][0];
		}else{
			$data[0][optno] = $_REQUEST[optno];
			$_REQUEST[addopt] = explode(",",$_REQUEST[addopt]);
		}
	}
	if (!$_REQUEST[ea] || !is_numeric($_REQUEST[ea])) $_REQUEST[ea] = 1;
	list($_REQUEST[storageid]) = explode("&",$_REQUEST[storageid]);

	$data[0][ea] = $_REQUEST[ea];
	$data[0][storageid] = $_REQUEST[storageid];
	$data[0][addopt] = $_REQUEST[addopt];
	
	#복수 편집기 처리 pods_use, podskind, podsno pods_editor.js에서 form에 추가하여 넘기 2016.05.23 by kdk  
	if($_REQUEST[pods_use] && $_REQUEST[podskind] && $_REQUEST[podsno]) {
		$data[0][pods_use] = $_REQUEST[pods_use]; 
		$data[0][podskind] = $_REQUEST[podskind]; 
		$data[0][podsno] = $_REQUEST[podsno];
	}
}

if ($_REQUEST[cartno]){
	foreach ($_REQUEST[cartno] as $k=>$v){
		if (!$v) continue;
		list ($goodsno, $optno, $addoptno) = $db->fetch("select goodsno, optno from exm_edit where cid = '$cid' and storageid = '$k'",1);
		$data[0][storageid] = $k;
		$data[0][goodsno] = $goodsno;
		$data[0][optno] = $optno;
		$data[0][addopt] = (!is_array($_REQUEST[addopt])) ? explode(",",$addoptno):$addoptno;
	}
}

//옵션 자동견적이 있을경우     20140128
if ($_REQUEST[option_json]){      
   $extraOptionData = orderJsonParse($_REQUEST[option_json]);
   $data[0][est_order_data] = $extraOptionData[est_order_data];
   $data[0][est_order_option_desc] = $extraOptionData[est_order_option_desc];
   $data[0][est_price] = $extraOptionData[est_price];
   $data[0][est_order_type] = $_REQUEST[est_order_type];
}

if($pods_use == 3) {//WPOD 편집기 연동 2014.07.14 by kdk

	### 주문 정보(wpodorderjsondata) 가져오기 2014.04.17 by kdk
	if (in_array($podskind,array(1001,1002,1003,1005,1006,1007,1008))) {
      $wpod_ret = readUrlWithcurl('http://podstation20.ilark.co.kr/CommonRef/StationWebService/GetMultiOrderInfoResult.aspx?storageids=' .$_REQUEST[storageid], false);

		$wdata = explode("|^|",$wpod_ret);
		$wpod_ret = _ilark_vars(substr($wdata[2],8));
		$data[0][vdp_edit_data] = $wpod_ret[WDATA];
	}
	//debug($data);				
	//exit;
}
 
//debug($data);
//exit;
$cart->add($data);

###wpod vdp 복수 주문일 경우 by 2014.04.18 kdk
if($_REQUEST[storageids]) {
	$storageid_arr = explode(',',$_REQUEST[storageids]);
	foreach ($storageid_arr as $key => $value) {
		if($value) {	
         $data[0][storageid] = $value;
			
			### 주문 정보(wpodorderjsondata) 가져오기 2014.04.17 by kdk
			if (in_array($podskind,array(1001,1002,1003,1005,1006,1007,1008))) {
            $wpod_ret = readUrlWithcurl('http://podstation20.ilark.co.kr/CommonRef/StationWebService/GetMultiOrderInfoResult.aspx?storageids=' .$data[0][storageid], false);

				$wdata = explode("|^|",$wpod_ret);
				$wpod_ret = _ilark_vars(substr($wdata[2],8));
				$data[0][vdp_edit_data] = $wpod_ret[WDATA];
         }					
			//debug($data);
			//exit;
			$cart->add($data);
		}
	}
}

$cartno[]=$cart->addid_direct;
//debug($cartno);
//exit;
//echo "Cart() <br />";
$debug_data .=  "3 - " . number_format(get_time() - $this_time, 4). _("초")."<BR>";

//주문 처리
$cart = new CartV2($cartno);
//debug($cart);
//exit;  
  
$payprice = $cart->totprice - $cart->dc;

$debug_data .=  "4 - " . number_format(get_time() - $this_time, 4). _("초")."<BR>";
$post = array();
  
//주문 데이타 생성하기
$micro = explode(" ",microtime());
$post[payno] = $micro[1].sprintf("%03d",floor($micro[0]*1000));

$post[paymethod] = "f";     //정액 결제 회원
$post[emoney] = "0";
  
   //회원정보에서 주문정보 만든다.
$m_member = new M_member();
$m_data = $m_member->getInfo($cid, $sess[mid]);
$post[orderer_name] = $m_data[name];
$post[orderer_email] = $m_data[email];
$post[orderer_phone] = $m_data[phone];
$post[orderer_mobile] = $m_data[mobile];
$post[orderer_zipcode] = $m_data[zipcode];
$post[orderer_addr] = $m_data[address];
$post[orderer_addr_sub] = $m_data[address_sub];
$post[payer_name] = $m_data[name];
$post[receiver_name] = $m_data[name];
$post[receiver_phone] = $m_data[email];
$post[receiver_mobile] = $m_data[mobile];
$post[receiver_zipcode] = $m_data[zipcode];
$post[receiver_addr] = $m_data[address];
$post[receiver_addr_sub] = $m_data[address_sub];
$post[request] = '';
$post[request2] = '';
$post[bankinfo] = '';

if(is_array($cart->item)) {
   foreach ($cart->item as $k=>$v){
      foreach ($v as $vv){
         $pg_goodsnm = $vv[goodsnm];
         break;
      }
   }
}

//echo "start_transaction <br />";
$debug_data .=  "5 - " . number_format(get_time() - $this_time, 4). _("초")."<BR>";
$db->start_transaction();

list($chk) = $db->fetch("select payno from exm_pay where payno = '$post[payno]'",1);
if ($chk){
   $micro = explode(" ",microtime());
   $post[payno] = $micro[1].sprintf("%03d",floor($micro[0]*1000));
   }
  
   if (!$chk){
      $escrow = ($post[escrow]) ? 1:0; # 2012-05-24
   ## 결제데이터 insert
   list ($referer) = $db->fetch("select referer from exm_counter_ip where ip = '$_SERVER[REMOTE_ADDR]' and day = curdate()+0",1);
   $query = "
   insert into exm_pay set
   cid               = '$cid',
   payno             = '$post[payno]',
   mid               = '$sess[mid]',
   paystep           = '$step',
   paymethod         = '$post[paymethod]',
   payprice          = '$payprice',
   saleprice         = '$cart->itemprice',
   shipprice         = '$cart->totshipprice',
   dc_member         = '$cart->dc',
   dc_emoney         = '$post[emoney]',
   dc_coupon         = '$cart->dc_coupon',
   orddt             = now(),
   orderer_name      = '$post[orderer_name]',
   orderer_email     = '$post[orderer_email]',
   orderer_phone     = '$post[orderer_phone]',
   orderer_mobile    = '$post[orderer_mobile]',
   orderer_zipcode   = '$post[orderer_zipcode]',
   orderer_addr      = '$post[orderer_addr]',
   orderer_addr_sub  = '$post[orderer_addr_sub]',
   payer_name        = '$post[payer_name]',
   receiver_name     = '$post[receiver_name]',
   receiver_phone    = '$post[receiver_phone]',
   receiver_mobile   = '$post[receiver_mobile]',
   receiver_zipcode  = '$post[receiver_zipcode]',
   receiver_addr     = '$post[receiver_addr]',
   receiver_addr_sub = '$post[receiver_addr_sub]',
   request           = '$post[request]',
   request2          = '$post[request2]',
   escrow            = '$escrow',
   bankinfo          = '$post[bankinfo]',
   referer           = '$referer'
   ";
  
   //echo $query."<BR />";
 
   $db->query($query);
   $debug_data .=  "6 - " . number_format(get_time() - $this_time, 4). _("초")."<BR>";

   $ordno = 0;
   foreach ($cart->item as $k=>$v){
      //debug($v);
         
      $ordno++;
      list($rid) = explode("_no:",$k);
      ## 주문데이터 insert
      $query = "
      insert into exm_ord set
        payno     = '$post[payno]',
        ordno     = '$ordno',
        rid       = '$rid',
        shipprice   = '{$cart->shipprice[$k]}',
        acc_shipprice = '{$cart->acc_shipprice[$k]}',
        ordprice    = '{$cart->ordprice[$k]}'
      ";
      //echo $query."<BR />";
      $db->query($query);
      $debug_data .=  "7 - " . number_format(get_time() - $this_time, 4). _("초")."<BR>";
  
      $ordseq = 0;
      foreach ($v as $v2) {
         //지금 장바구니에 insert 한 주문건만 처리한다.
         //debug($cartno);
         //debug($v2[cartno]);
         //if ($cartno != $v2[cartno]) continue;
         //debug($v2);
         $ordseq++;
         if (!$v2[addoptno]) $v2[addoptno] = array();
        
         if ($v2[addopt]) $v2[addopt] = serialize($v2[addopt]);
         if ($v2[printopt]) $v2[printopt] = serialize($v2[printopt]);

         list($v2[reg_cid],$v2[privatecid]) = $db->fetch("select reg_cid,privatecid from exm_goods where goodsno = '$v2[goodsno]'",1);
         if ($v2[reg_cid] && $v2[reg_cid]==$v2[privatecid]){
            $self = 1;
         } else {
            $self = 0;
         }
  
         $v2[goodsnm] = addslashes($v2[goodsnm]);
         $v2[est_goodsnm] = addslashes($v2[est_goodsnm]);
         $v2[est_order_option_desc] = addslashes($v2[est_order_option_desc]);

         if($pods_use == 3) {//WPOD 편집기 연동 2014.07.14 by kdk
            $pods_storageID = $v2[storageid];
         }
		
         $query = "
         insert into exm_ord_item set
          payno                   = '$post[payno]',
          ordno                   = '$ordno',
          ordseq                  = '$ordseq',
          goodsno                 = '$v2[goodsno]',
          goodsnm                 = '$v2[goodsnm]',
          optno                   = '$v2[optno]',
          opt                     = '$v2[opt]',
          addoptno                = '".implode(",",$v2[addoptno])."',
          addopt                  = '$v2[addopt]',
          printopt                = '$v2[printopt]',
          goods_price             = '$v2[price]',
          aprice                  = '$v2[aprice]',
          addopt_aprice           = '$v2[addopt_aprice]',
          print_aprice            = '$v2[print_aprice]',
          addpage_aprice          = '$v2[addpage_price]',
          addpage                 = '$v2[addpage]',
          goods_reserve           = '$v2[reserve]',
          areserve                = '$v2[areserve]',
          addopt_areserve         = '$v2[addopt_areserve]',
          print_areserve          = '$v2[print_areserve]',
          addpage_areserve        = '$v2[addpage_reserve]',
          coupon_areserve         = '$v2[reserve_coupon]',
          coupon_areservesetno    = '$v2[reserve_couponsetno]',
          saleprice               = '$v2[saleprice]',
          payprice                = '$v2[payprice]',
          ea                      = '$v2[ea]',
          dc_member               = '$v2[grpdc]',
          dc_coupon               = '$v2[dc_coupon]',
          dc_couponsetno          = '$v2[dc_couponsetno]',
          storageid               = '$pods_storageID',
          reserve                 = '$v2[totreserve]',
          cartno                  = '$v2[cartno]',
          itemstep                = '$step',
          item_rid                = '$v2[rid2]',
          est_order_data          = '$v2[est_order_data]',
          est_order_option_desc   = '$v2[est_order_option_desc]',
          est_file_down_full_path = '$v2[est_file_down_full_path]',
          est_order_type          = '$v2[est_order_type]',
          est_cost                = '$v2[est_cost]',
          est_supply              = '$v2[est_supply]',
          est_price               = '$v2[est_price]',
          est_rid                 = '$v2[est_rid]',
          est_goodsnm             = '$v2[est_goodsnm]',
          est_fullpost            = '$v2[est_fullpost]',
          supplyprice_goods       = '$v2[supply_goods]',
          supplyprice_opt         = '$v2[supply_opt]',
          supplyprice_addopt      = '$v2[supply_addopt]',
          supplyprice_printopt    = '$v2[supply_printopt]',
          supplyprice_addpage     = '$v2[addpage_sprice]',
          cost_goods              = '$v2[cost_goods]',
          cost_opt                = '$v2[cost_opt]',
          cost_addopt             = '$v2[cost_addopt]',
          cost_printopt           = '$v2[cost_printopt]',
          cost_addpage            = '$v2[addpage_oprice]',
          selfgoods               = '$self'
         ";
         //echo $query."<BR />";
         //exit;
         $db->query($query);
         $debug_data .=  "8 - " . number_format(get_time() - $this_time, 4). _("초")."<BR>";      
      }   
   }

   $query = "select * from exm_ord_item where payno = '$post[payno]'";
   $res = $db->query($query);
   while ($data = $db->fetch($res)){
      set_pod_pay($data[payno],$data[ordno],$data[ordseq]);      
      //SetPod20PayWithPost($data);   //pods2 주문 완료처리.      //lib/lib_pods.php 파일 사용하지 않음에 따라 주석    20150403    chunter
      
      $debug_data .= "9 - 1 - " . number_format(get_time() - $this_time, 4). _("초")."<BR>";
      //set_acc_desc($data[payno],$data[ordno],$data[ordseq],2);

      if (in_array($podskind, $r_podskind20)){ /* 2.0 상품 */
         $client = "http://podstation20.ilark.co.kr/CommonRef/StationWebService/GetPreViewImg.aspx?storageid=$data[storageid]";    
	    
	      //책등, 종이 타입 정보 가져오기     20131218    //2.0 일때만 가져오기.
	      $pod_data[storageid] = $data[storageid];
	      $soapurl = 'http://podstation20.ilark.co.kr/CommonRef/StationWebService/GetCoverOptionInfo.aspx';
	      //$ret = sendPostData($soapurl, $pod_data);	  
         $ret = readUrlWithcurl($soapurl."?storageid=".$data[storageid], false);

	      $ret = explode("|",$ret);
	      //print_r($ret);    
	      if ($ret[0]=="success"){
            $paper_type = $ret[1];
            $book_spine = $ret[2];
	      }

         $debug_data .= "9 - 3 - " . number_format(get_time() - $this_time, 4). _("초")."<BR>";    
      } else {
         $client = "http://".PODS10_DOMAIN."/StationWebService/GetPreViewImg.aspx?storageid=$data[storageid]";
      }
	
	   $ret[GetPreViewImg] = readurl($client);
	   $ret[GetPreViewImg] = explode("|",$ret[GetPreViewImg]);
	   if (count($ret[GetPreViewImg])>1)
	   {
         if (in_array($podskind, $r_podskind20)){ /* 2.0 상품 */        
            $preview_link = $ret[GetPreViewImg][0];
         } else {
            $preview_link = $ret[GetPreViewImg][1];
         }
      } else
         $preview_link = "";
	
      $query = "insert into tb_editor_ext_data set
                  storage_id = '$data[storageid]',
	               paper_type = '$paper_type',
	               book_spine = '$book_spine',
	               editor_return_json = '$editor_return_json',
	               preview_link = '$preview_link'
	             on duplicate key update
	               paper_type = '$paper_type',
	               book_spine = '$book_spine',
	               editor_return_json = '$editor_return_json',
	               preview_link = '$preview_link'";
      //echo $query."<BR />";
	   
      $db->query($query);
	   $debug_data .= "10 - " . number_format(get_time() - $this_time, 4). _("초")."<BR>";	
   }
   $debug_data .= "9 - 2 - " . number_format(get_time() - $this_time, 4). _("초")."<BR>";
   //exit;
     
   //echo $query."장바구니 삭제 <BR />";
   //장바구니 삭제
   $query = "select * from exm_ord_item where payno = '$post[payno]'";
   $res2 = $db->query($query);
   while ($item = $db->fetch($res2)){
      $db->query("delete from exm_cart where cartno = '$item[cartno]'");
      $debug_data .= "11 - " . number_format(get_time() - $this_time, 4). _("초")."<BR>";
   }
}

$db->end_transaction();
$db->close();
$debug_data .= "12 - " . number_format(get_time() - $this_time, 4). _("초")."<BR>";
//echo $debug_data; 
//exit;
go('/mypage/orderlist.php');

function _ilark_vars($vars,$flag=";"){
   $r = array();
   $div = explode($flag,$vars);
   foreach ($div as $tmp){
      $pos = strpos($tmp,"=");
      list ($k,$v) = array(substr($tmp,0,$pos),substr($tmp,$pos+1));
      $r[$k] = $v;
   }

   return $r;
}  
?>