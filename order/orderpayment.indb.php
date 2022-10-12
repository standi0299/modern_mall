<?

/*
* @date : 20190123
* @author : kdk
* @brief : 시안요청 정보가 있을경우 payno 업데이트.(시안요청은 1개 주문만 넘어옴).
* @brief : 시안 제작요청, 주문 검수 사용, 승인요청 처리  flag 추가 .(itemstep = '81', order_inspection = 'N', order_allow_chk = '0')
* @request : 태웅
* @desc :
* @todo :
*/

include_once "../lib/library.php";
//include_once dirname(__FILE__) . "/../models/m_studio.php";
include_once "../lib/class.cart.php";

//$m_studio = new M_studio();


//$post = unserialize(base64_decode($_POST[orderinfo]));
$post = $_POST;
//if($_POST[mode] == "orderpayment")

if($_POST[mode] == "getCalcEmoney"){
   $totemoeny = calcuOrderTotalEmonay($_POST['payprice'], $_POST['saleprice']);
   echo $totemoeny;
   exit;
}

if(!$post[paymethod]) $post[paymethod] = $_POST[paymethod];
if(!$post[emoney]) $post[emoney] = $_POST[emoney];

$micro = explode(" ",microtime());
$_POST[payno] = $micro[1].sprintf("%03d",floor($micro[0]*1000));


if (!$_POST[payno]) {
	echo resultJson("09", "결제번호가 생성되지 않았습니다.", "history.back();");
	exit;
}

if ($_POST[coupon]) {
   $_POST[coupon] = stripslashes($_POST[coupon]);
   $coupon = unserialize($_POST[coupon]);
}

if ($_POST[sale_code_coupon]) {
   $coupon[sale_code_coupon] = $_POST[sale_code_coupon];
}

//추가 배송비 계산때문에 receiver_zipcode 코드를 넘겨야 한다.			20171229		chunter
if ($post[receiver_zipcode]) $coupon[receiver_zipcode] = $post[receiver_zipcode];

$cart = new Cart($post[cartno], $coupon);

// 할인 적용 배송비
if($post[totshipprice]){
	$cart->totshipprice = $post[totshipprice];
}

if (($cfg[skin_theme] == "P1" || $cfg[skin_theme] == "B1") && ($_POST[shiptype] == "5" || $_POST[shiptype] == "9")) {
	$payprice = $cart->totprice - $cart->dc - $cart->dc_coupon - $cart->dc_sale_code_coupon - $post[emoney] - $cart->totshipprice;
	$totshipprice = 0;
} else {
	$payprice = $cart->totprice - $cart->dc - $cart->dc_coupon - $cart->dc_sale_code_coupon - $post[emoney];
	$totshipprice = $cart->totshipprice;
}

if($post[totsaleprice]){
	$payprice = $post[totsaleprice];
}
if ($post[mid] && $post[mid] != $sess[mid]) {
	echo resultJson("09", "로그인 세션이 유효하지 않습니다.", "location.href = '/order/cart.php';");
	exit;
}

if ($post[emoney]) {
	$result = setPayNUseEmoney($cid, $sess[mid], $post[emoney], $cart->itemprice, '', true);
	if($result[code] != "00") {
		echo resultJson("09", $result[msg], "location.reload();");
   		exit;
	}

	//결제금액이 없을 경우 적립금결제로 설정
	if ($payprice == 0) $post[paymethod] = "e";
}

if ($_POST[dc_partnership]){
   if (!$_POST[dc_partnership_tworld_no]){
	echo resultJson("09","제휴할인이 유효하지 않습니다","location.reload();");
      exit;
   }

   list($point) = $db->fetch("select totPoint from exm_dc_partnership_tworld where no = '$_POST[dc_partnership_tworld_no]'",1);
   if ($_POST[dc_partnership]>$point){
	echo resultJson("09","제휴할인이 유효하지 않습니다","location.reload();");
      exit;
   }
}

if($post[receiver_mobile])
	$post[receiver_mobile] = implode("-", $post[receiver_mobile]);

if($post[orderer_phone])
	$post[orderer_phone] = implode("-", $post[orderer_phone]);

if($post[orderer_mobile])
	$post[orderer_mobile] = implode("-", $post[orderer_mobile]);
//$post[orderer_zipcode] = implode("-", $post[orderer_zipcode]);

if($post[receiver_phone])
   $post[receiver_phone] = implode("-", $post[receiver_phone]);

if($cfg[skin_theme]  == "P1" && $post[orderer_email])
   $post[orderer_email] = implode("@", $post[orderer_email]);

switch ($post[paymethod]) {
   case "e": $step = 2; break;
   case "b": $step = 1; break;
   case "t": $step = 91; break;
   default : $step = 0; break;
}

$pg_goods_cnt = 0;
foreach ($cart->item as $k => $v) {
   foreach ($v as $vv) {
      $pg_goodsnm = $vv[goodsnm];
      if (count($v) > 1) $pg_goodsnm .= "외".count($v) - 1 ._("건");
      break;
   }

	foreach ($v as $vv) {
      $pg_goods_cnt += $vv[ea];
	}
}

list($chk) = $db->fetch("select payno from exm_pay where payno = '$_POST[payno]'", 1);
if ($chk) {
	echo resultJson("09", "결제번호 중복오류입니다. 결제버튼을 다시 눌러주세요.", "");
	exit;
}

//주문 중복 접수 체크 / 15.08.28 / kjm
$cartno = implode(",", $_POST[cartno]);

$storageOverlapChk = $db->fetch("select payno from exm_ord_item a
                              inner join exm_cart b on a.storageid = b.storageid
                              where b.cartno in ($cartno) and b.storageid != '' and a.itemstep >= 1");

if ($storageOverlapChk[payno]) {
	echo resultJson("09", "보관함코드 중복오류.\r\n이미 주문 처리된 상품이 존재합니다.\r\n고객센터로 문의주세요.", "location.href = '/order/cart.php';");
   exit;
}

switch ($post[paymethod])
{
   case "c": case "v": case "o": case "m": case "h": case "ve": case "oe":

   //if (allowMobilePGCheck())
	if (isMobile())
   {
      if ($cfg[pg][module]=="smartxpay")
         $result['action'] = "launchCrossPlatform();";
      else
			$result['action'] = "_settle();";
	} else {
		if ($cfg[pg][module]=="lg")
			$result['action'] = "doPay_ActiveX();";
		else if ($cfg[pg][module]=="smartxpay")
			$result['action'] = "launchCrossPlatform();";
   else
      $result['action'] = "_settle();";
   }

	if ($cfg[pg][module]=="inipaystdweb")
	{
		$_POST[payprice] = $payprice;
		if (isMobile())
		{
			$result[pay_client_type] = "MB";
		} else {
			$result[pay_client_type] = "PC";
			include "./ins.inipaystdweb.php";

			$result['timestamp'] = $_POST[timestamp];
			$result['signature'] = $_POST[signature];
			$result['mKey'] = $_POST[mKey];
		}
	}
	break;
}

$result['code'] = "01";
$result['payno'] = $_POST[payno];
$db->start_transaction();

if (!$chk)
{
	// $post[request] = str_replace("'", "''", $post[request]);
    // $post[request2] = str_replace("'", "''", $post[request2]);

	// \' \" 처리 210427 jtkim
	$post[request] = addslashes($post[request]);
	$post[request2] = addslashes($post[request2]);

	$post[request] = strip_tags($post[request]);
	$post[request2] = strip_tags($post[request2]);

	if ($post[paymethod] == "ve" || $post[paymethod] == "oe") $escrow = 1;
	else $escrow = 0;

	if ($cfg[skin_theme] == "P1" && ($post[paymethod] == "v" || $post[paymethod] == "ve")) $escrow = ($post[escrow]) ? 1 : 0; # 2012-05-24

	## 결제데이터 insert
	list($referer) = $db->fetch("select referer from exm_counter_ip where ip = '$_SERVER[REMOTE_ADDR]' and day = curdate()+0", 1);
	$query = "
	insert into exm_pay set
      cid					= '$cid',
		payno				   = '$_POST[payno]',
 		mid					= '$sess[mid]',
 		paystep				= '$step',
 		paymethod			= '$post[paymethod]',
 		payprice			   = '$payprice',
 		saleprice			= '$cart->itemprice',
 		shipprice			= '$totshipprice',
 		dc_member			= '$cart->dc',
 		dc_emoney			= '$post[emoney]',
 		dc_coupon			= '$cart->dc_coupon',
 		dc_sale_code_coupon = '$cart->dc_sale_code_coupon',
 		orddt				   = now(),
 		orderer_name		= '$post[orderer_name]',
 		orderer_email		= '$post[orderer_email]',
 		orderer_phone		= '$post[orderer_phone]',
 		orderer_mobile		= '$post[orderer_mobile]',
 		orderer_zipcode	= '$post[orderer_zipcode]',
 		orderer_addr		= '$post[orderer_addr]',
 		orderer_addr_sub	= '$post[orderer_addr_sub]',
 		payer_name			= '$_POST[payer_name]',
 		receiver_name		= '$post[receiver_name]',
 		receiver_phone		= '$post[receiver_phone]',
 		receiver_mobile	= '$post[receiver_mobile]',
 		receiver_zipcode	= '$post[receiver_zipcode]',
 		receiver_addr		= '$post[receiver_addr]',
 		receiver_addr_sub	= '$post[receiver_addr_sub]',
 		request				= '$post[request]',
 		request2			   = '$post[request2]',
 		escrow				= '$escrow',
 		bankinfo			   = '$_POST[bankinfo]',
		referer				= '$referer',
		dc_partnership_type = '$_POST[dc_partnership_type]',
		dc_partnership      = '$_POST[dc_partnership]',
		dc_partnership_tworld_no = '$_POST[dc_partnership_tworld_no]',
 		order_skin =	'{$cfg[skin]}' 		
	";
	//debug($query);
	$db->query($query);

	$ordno = 0;
	//debug($cart->item);
	// echo "ship_dc_list";
	// echo "$ship_dc_list_obj";
	foreach ($cart->item as $k => $v)
	{
		$ordno++;
		list($rid) = explode("_no:", $k);

		if($post[ship_dc_cnt] != 0){
			for($i=1; $i<($post[ship_dc_cnt]+1); $i++){
				if($post[rid_.$i] == $k){
					$cart->shipprice[$k] = $post[rid_shipprice_.$i];
				}
			}
		}

		if ($cfg[skin_theme] == "P1" || $cfg[skin_theme] == "B1") {
			$order_shiptype = $_POST[shiptype];

			if ($order_shiptype == "5" || $order_shiptype == "9") {
				$ord_shipprice = 0;
				$acc_shipprice = 0;
			} else {
				$ord_shipprice = $cart->shipprice[$k];
				$acc_shipprice = $cart->acc_shipprice[$k];
			}
		} else {
			$order_shiptype = $cart->shipfree[$k];      //제작사별 주문 배송 방법을 ord_item 에 넣어준다      20141202    chunter
			$ord_shipprice = $cart->shipprice[$k];
			$acc_shipprice = $cart->acc_shipprice[$k];
        }
		//debug("k");debug($k);
		//debug("ord_shipprice");debug($ord_shipprice);
      ## 주문데이터 insert
      $query = "insert into exm_ord set
			payno		  	   = '$_POST[payno]',
			ordno			   = '$ordno',
			rid				   = '$rid',
			shipprice		   = '$ord_shipprice',
			acc_shipprice	   = '$acc_shipprice',
			order_shiptype 	   = '$order_shiptype',
			ordprice		   = '{$cart->ordprice[$k]}'
		";
	  //debug($query);
	  //exit;
      $db -> query($query);

      $packageId = ""; //패키지 정보 ID. / 2017.12.20 / kdk
      $pakseq = 0;
      $ordseq = 0;
      foreach ($v as $v2) {
         $ordseq++;
         if (!$v2[addoptno]) $v2[addoptno] = array();
         if ($v2[addopt]) $v2[addopt] = serialize($v2[addopt]);
         if ($v2[printopt]) $v2[printopt] = serialize($v2[printopt]);

         list($v2[reg_cid], $v2[privatecid]) = $db->fetch("select reg_cid,privatecid from exm_goods where goodsno = '$v2[goodsno]'", 1);
         if ($v2[reg_cid] && $v2[reg_cid]==$v2[privatecid]) {
        	   $self = 1;
         } else {
            $self = 0;
         }

         $v2[goodsnm] = addslashes($v2[goodsnm]);
         $v2[est_goodsnm] = addslashes($v2[est_goodsnm]);
         $v2[est_order_option_desc] = addslashes($v2[est_order_option_desc]);
         $v2[title] = addslashes($v2[title]);

         $package_order_code = ""; //패키지상품 주문 코드. / 2017.12.20 / kdk
         if($v2[package_flag] == "2") {
     	      if($v2[package_parent_cartno] == "0") {//대표상품
               //패키지 정보 ID.
               list($packageId) = $db->fetch("select ID from tb_goods_addtion_item where cid='$cid' and addtion_key_kind='P' and addtion_key='$v2[goodsno]'", 1);

               $package_order_code = $packageId."_0";
               $pakseq = 0;
     	      }
            else {//하위상품
               $pakseq++;
               $package_order_code = $packageId."_".$pakseq;
            }
         }

         //debug($v2);
         //exit;
         $query = "insert into exm_ord_item set
				payno					   = '$_POST[payno]',
 				ordno					   = '$ordno',
 				ordseq					= '$ordseq',
 				goodsno					= '$v2[goodsno]',
 				goodsnm					= '$v2[goodsnm]',
 				optno					   = '$v2[optno]',
 				opt						= '$v2[opt]',
 				addoptno				   = '" . implode(",", $v2[addoptno]) . "',
 				addopt					= '$v2[addopt]',
 				printopt				   = '$v2[printopt]',
 				goods_price				= '$v2[price]',
 				aprice					= '$v2[aprice]',
 				addopt_aprice			= '$v2[addopt_aprice]',
 				print_aprice			= '$v2[print_aprice]',
 				addpage_aprice			= '$v2[addpage_price]',
 				addpage					= '$v2[addpage]',
 				goods_reserve			= '$v2[reserve]',
 				areserve				   = '$v2[areserve]',
 				addopt_areserve		= '$v2[addopt_areserve]',
 				print_areserve			= '$v2[print_areserve]',
 				addpage_areserve		= '$v2[addpage_reserve]',
 				coupon_areserve		= '$v2[reserve_coupon]',
 				coupon_areservesetno	= '$v2[reserve_couponsetno]',
 				saleprice				= '$v2[saleprice]',
 				payprice				   = '$v2[payprice]',
 				ea						   = '$v2[ea]',
 				dc_member				= '$v2[grpdc]',
 				dc_coupon				= '$v2[dc_coupon]',
 				dc_couponsetno			= '$v2[dc_couponsetno]',
 				storageid				= '$v2[storageid]',
 				reserve					= '$v2[totreserve]',
 				cartno					= '$v2[cartno]',
 				itemstep				   = '$step',
 				item_rid				   = '$v2[rid2]',

 				`title`              = '$v2[title]',

 				est_order_data			   = '$v2[est_order_data]',
 				est_order_option_desc	= '$v2[est_order_option_desc]',
 				est_file_down_full_path	= '$v2[est_file_down_full_path]',
 				est_order_type			   = '$v2[est_order_type]',
 				est_cost				      = '$v2[est_cost]',
 				est_supply				   = '$v2[est_supply]',
 				est_price				   = '$v2[est_price]',
 				est_rid					   = '$v2[est_rid]',
 				est_goodsnm				   = '$v2[est_goodsnm]',
 				est_fullpost			   = '$v2[est_fullpost]',
 				est_pods_version        = '$v2[est_pods_version]',
 				est_order_memo          = '$v2[est_order_memo]',
 				supplyprice_goods		   = '$v2[supply_goods]',
 				supplyprice_opt			= '$v2[supply_opt]',
 				supplyprice_addopt		= '$v2[supply_addopt]',
 				supplyprice_printopt	   = '$v2[supply_printopt]',
 				supplyprice_addpage		= '$v2[addpage_sprice]',
 				cost_goods				   = '$v2[cost_goods]',
 				cost_opt				      = '$v2[cost_opt]',
 				cost_addopt				   = '$v2[cost_addopt]',
 				cost_printopt			   = '$v2[cost_printopt]',
 				cost_addpage			   = '$v2[addpage_oprice]',
 				vdp_edit_data           = '$v2[vdp_edit_data]',
 				catno                   = '$v2[catno]',
 				selfgoods				   = '$self',
 				package_order_code      = '$package_order_code',
 				ext_json_data           = '$v2[ext_json_data]'
         ";

			//debug($query);
			$db -> query($query);

            //시안요청일 경우. / 20190123 / kdk
            if ($post[design_draft_flag] == "Y")
            {
                //debug($post);
                //debug($post[cartno][0]);
                //debug($_POST[payno]);
                //debug($v2[cartno]);
                //debug($cart->item);

                //시안요청 정보가 있을경우 payno 업데이트.(시안요청은 1개 주문만 넘어옴) / 20190122 / kdk
                $cart->setDesignUpdate($v2[cartno], $_POST[payno]);

                //시안 제작요청, 주문 검수 사용, 승인요청 처리  flag 추가 / 20190123 / kdk
                //itemstep = '81', order_inspection = 'N', order_allow_chk = '0'
                $query = "update exm_ord_item set
                     itemstep = '81',
                     order_inspection = 'N',
                     order_allow_chk = '0'
                     where payno = '$_POST[payno]'
                     and ordno = '$ordno'
                     and ordseq = '$ordseq'";
                //debug($query);
                $db->query($query);
            }
            //exit;

         if ($step != 0)
         {
            if ($v2[dc_couponsetno])
  	         {
               $query = "select coupon_type,coupon_price from exm_coupon_set a
                           inner join exm_coupon b on b.cid = '$cid' and b.coupon_code = a.coupon_code
                           where a.no = '$v2[dc_couponsetno]'";
       	      list ($coupon_type,$coupon_price) = $db->fetch($query,1);

               if ($coupon_type=="coupon_money")
               {
                  $v2[dc_coupon] = $v2[dc_coupon]+0;
                  $coupon_price = $coupon_price+0;
                  $db->query("update exm_coupon_set set
                                 coupon_able_money = coupon_able_money - $v2[dc_coupon],
                                 payno          = '$_POST[payno]',
                                 ordno          = '$ordno',
                                 ordseq            = '$ordseq',
                                 coupon_usedt      = now()
                                 where no = '$v2[dc_couponsetno]'");

                  $db->query("update exm_coupon_set set
          		                  coupon_use        = 1
                              where no = '$v2[dc_couponsetno]' and coupon_able_money <= 0");
					 } else {
                  $db->query("update exm_coupon_set set
          		                  coupon_use    = 1,
                   					payno         = '$_POST[payno]',
                   					ordno         = '$ordno',
                   					ordseq        = '$ordseq',
                   					coupon_usedt  = now()
               				     	where no = '$v2[dc_couponsetno]'
           	      ");
               }
            }

            if ($v2[reserve_couponsetno])
            {
               $db->query("update exm_coupon_set set
                              coupon_use    = 1,
               					payno         = '$_POST[payno]',
               					ordno         = '$ordno',
               					ordseq        = '$ordseq',
               					coupon_usedt  = now()
               					where no = '$v2[reserve_couponsetno]'
               ");
            }

            //프로모션 코드 사용 등록			20171019		chunter
            if ($_POST[sale_code_coupon])
            {
               $addColumn2 = "set
                                 cid					= '$cid',
               						coupon_code			= '$_POST[sale_code_coupon]',
               						mid					= '$sess[mid]',
               						coupon_able_money	= '{$cart->dc_sale_code_coupon}',
               						coupon_use    = 1,
                					   payno         = '$_POST[payno]',
                					   ordno         = '$ordno',
                					   ordseq        = '$ordseq',
                					   coupon_usedt  = now(),
               						coupon_setdt		= now()";
               $m_etc = new M_etc();
					$m_etc->setCouponSetInfo("", $addColumn2);
				}
			}
      }
	}

	if ($step==2)
	{
      $db->query("update exm_pay set paydt = now() where payno = '$_POST[payno]'");

      $query = "select * from exm_ord_item where payno = '$_POST[payno]'";
      $res = $db->query($query);
      while ($data = $db->fetch($res))
      {
         set_pod_pay($data[payno], $data[ordno], $data[ordseq]);
         set_acc_desc($data[payno], $data[ordno], $data[ordseq], 2);
      }

      order_sms($_POST[payno]);
   } else if ($step==1 || $step==91) {
      $query = "select * from exm_ord_item where payno = '$_POST[payno]'";
      $res = $db->query($query);
      while ($data = $db->fetch($res))
      {
         if ($data[storageid])
         {
            list($podskind, $pods_use) = $db->fetch("select podskind,pods_use from exm_goods where goodsno = '$data[goodsno]'", 1);
            if ($podskind) {
        	      $podsApi = new PODStation('20');
					$podsApi->UpdateStorageDate($data[storageid]);
            }
         }
      }
   }

 	if ($step==2 || $step==1 || $step==91)
 	{
      $query = "select * from exm_ord_item where payno = '$_POST[payno]'";
      $res = $db->query($query);
      while ($data = $db->fetch($res)) {
         set_stock($data[goodsno], $data[optno], $data[ea] * -1);
      }
	}

	//배송정보 나의 주소록에 추가할 경우
	if ($post[add_my_address] == "Y") {
		$query2 = "insert into exm_address set 
			cid = '$cid',
			mid = '$sess[mid]',
			addressnm = '$post[receiver_name]',
			receiver_name = '$post[receiver_name]',
			receiver_phone = '$post[receiver_phone]',
			receiver_mobile = '$post[receiver_mobile]',
			receiver_zipcode = '$post[receiver_zipcode]',
			receiver_addr = '$post[receiver_addr]',
			receiver_addr_sub = '$post[receiver_addr_sub]'";

		$db->query($query2);
	}

	//현금영수증 체크할 경우 > 따로 처리 변경 (orderpayment.document.php)
	/*
	if ($post[chk_document_info] == "Y") {
		$m_member = new M_member();

		if ($post[document_type] == "1" && $post[document_type2] == "0") {
			$document_type = "CRD";
			if ($post[document_number]) $document_mobile = implode("-", $post[document_number]);
		} else if ($post[document_type] == "2" && $post[document_type2] == "0") {
			$document_type = "CRD";
			if ($post[document_card_num]) $document_card_num = implode(",", $post[document_card_num]);
		} else if ($post[document_type] == "3" && $post[document_type2] == "0") {
			$document_type = "CRE";
			if ($post[document_number]) $document_licensee_num = implode("-", $post[document_number]);
		} else if ($post[document_type] == "4" && $post[document_type2] == "0") {
			$document_type = "CRE";
			if ($post[document_card_num]) $document_card_num = implode(",", $post[document_card_num]);
		} else if ($post[document_type] == "5") {
			$document_type = "TI";
			$fname = "";

			$dir = "../data/document/";
			if (!is_dir($dir)) {
				mkdir($dir, 0707);
				chmod($dir, 0707);
			}

			$dir = "../data/document/$cid/";
			if (!is_dir($dir)) {
				mkdir($dir, 0707);
				chmod($dir, 0707);
			}

			//첨부파일 업로드
			if ($_FILES[document_file][tmp_name]) {
				$ext = substr(strrchr($_FILES[document_file][name], "."), 1);
				$fname = time().rand(0, 9999).".".$ext;

				move_uploaded_file($_FILES[document_file][tmp_name], $dir.$fname);
			}
		}else if($post[document_type2] == "1"){


			$document_licensee_num = $post[document_licensee_num];
			$document_type = "TI";
			$fname = "";

			$dir = "../data/document/";
			if (!is_dir($dir)) {
				mkdir($dir, 0707);
				chmod($dir, 0707);
			}

			$dir = "../data/document/$cid/";
			if (!is_dir($dir)) {
				mkdir($dir, 0707);
				chmod($dir, 0707);
			}

			//첨부파일 업로드
			if ($_FILES[document_file][tmp_name]) {
				$ext = substr(strrchr($_FILES[document_file][name], "."), 1);
				$fname = time().rand(0, 9999).".".$ext;

				move_uploaded_file($_FILES[document_file][tmp_name], $dir.$fname);
			}
		}

		if ($document_type) {
			$m_member->setDocumentInfo($cid, $sess[mid], $document_type, $_POST[payno], $document_mobile, "", $document_card_num, $document_licensee_num, $fname, "");
		}
	}

	*/

 	switch ($post[paymethod])
 	{
      case "c": case "v": case "o": case "m": case "h": case "ve": case "oe":
      break;

		//kakaopay 결제 수단 추가				20180306		chunter
		case "kp":
			$goods_name = $pg_goodsnm;
			$qty = $pg_goods_cnt;
			include_once dirname(__FILE__)."/../pg/kakaopay/pay_order.php";
			if ($response[code] == "200")
			{
				$result['action'] = "{$response[redirect_url]}";
			}	else {
				echo resultJson("09", "{$response[err_msg]}", "");
				exit;
			}
      break;

		default :
    	//tb_pay_data 데이터 입력
      //$m_studio -> pay_data($_POST[payno], $cid, $sess[mid], '', $post[paymethod], '', '', '', '', '', $payprice, N);

      if ($post[emoney] > 0) {
         //set_emoney($post[mid], _("상품구입시 사용"), -$post[emoney], $_POST[payno]);
         setPayNUseEmoney($cid, $post[mid], $post[emoney], $cart->itemprice, $_POST[payno]);
    	}

      $query = "select * from exm_ord_item where payno = '$_POST[payno]'";
      $res2 = $db->query($query);
      while ($item = $db->fetch($res2)) {
         $db->query("delete from exm_cart where cartno = '$item[cartno]'");
      }

      $data = $db->fetch("select * from exm_pay where payno = '$_POST[payno]'");
      $query = "select * from exm_ord_item where payno = '$_POST[payno]'";
      $res = $db->query($query);
      while ($tmp = $db->fetch($res)) {
      	$data[item][] = $tmp;
      }

	  //20190118 / minks / 배송방법 추가
	  list($ordershiptype) = $db->fetch("select order_shiptype from exm_ord where payno='$_POST[payno]' order by ordno limit 1", 1);
	  $data[ordershiptype] = ($cfg[skin_theme] == "P1" && $ordershiptype == 1) ? $r_order_shiptype[0] : $r_order_shiptype[$ordershiptype];

      if ($step==2) {
         autoMail("payment", $data[orderer_email], $data);

         //관리자에게 보내기
         autoMailAdmin("admin_payment",$cfg[email1],$data);

		 autoSms(_("입금확인"), $data[orderer_mobile], $data);
		 kakao_alimtalk_send($data[orderer_mobile],$post[mid],_("입금확인"), $data);
	  } else {
         autoMail("order", $data[orderer_email], $data);

         //관리자에게 보내기
         autoMailAdmin("admin_order",$cfg[email1],$data);

		 if($data[paymethod] == "v" || $data[paymethod] == "b")
		 {
			autoSms(_("접수내역"), $data[orderer_mobile], $data);
			kakao_alimtalk_send($data[orderer_mobile],$post[mid],_("접수내역"), $data);
		 }else{
			autoSms(_("주문접수"), $data[orderer_mobile], $data);
			kakao_alimtalk_send($data[orderer_mobile],$post[mid],_("주문접수"), $data);
		 }

      }

//$cfg[ssl_use] = "Y";
      if($cfg[ssl_use] == "Y" /* && $cfg[skin_theme] == "M2" */){
         $result['action'] = "location.replace('http://".$_SERVER[HTTP_HOST]."/order/payend.php?payno=$_POST[payno]');";
      }else{
         $result['action'] = "location.replace('payend.php?payno=$_POST[payno]');";
      }
   }
}

if($_POST[save_paymethod] == "on"){
   $m_member = new M_member();
   $m_member->savePaymethodMember($cid, $sess[mid], $_POST[paymethod]);
}

$db->end_transaction();
$db->close();

$result['goodname'] = $pg_goodsnm;
$result['totprice'] = $payprice;

$result['buyername'] = $post['orderer_name'];
$result['buyertel'] = $post['orderer_mobile'];
$result['buyeremail'] = $post['orderer_email'];

if (!$result['buyername']) $result['buyername'] = $post['receiver_name'];
if (!$result['buyertel']) $result['buyertel'] = $post['receiver_mobile'];

if ($result[pay_client_type] == "PC")
{
	if ($post[paymethod] == "c")	$gopaymethod = "Card";
   if ($post[paymethod] == "oe") $gopaymethod = "DirectBank";
	if ($post[paymethod] == "ve") $gopaymethod = "VBank";
	if ($post[paymethod] == "v")	$gopaymethod = "VBank";
} else {
	if ($post[paymethod] == "c")	$gopaymethod = "wcard";//모바일 신용카드
	if ($post[paymethod] == "oe") $gopaymethod = "bank";//실시간 계좌이체
	if ($post[paymethod] == "ve")	$gopaymethod = "vbank";
	if ($post[paymethod] == "v")	$gopaymethod = "vbank";//가상계좌
	if ($post[paymethod] == "m")	$gopaymethod = "mobile";//핸드폰
}
$result['gopaymethod'] = $gopaymethod;

echo json_encode($result);
exit;

function resultJson($code, $msg, $action)
{
	$result[code] = "09";
	$result[msg] = $msg;

	if ($action)
		$result[action] = $action;
	else
		$result[action] = "location.reload();";

	return json_encode($result);
}

function _ilark_vars($vars, $flag = ";") {
   $r = array();
   $div = explode($flag, $vars);
   foreach ($div as $tmp) {
      $pos = strpos($tmp, "=");
      list($k, $v) = array(substr($tmp, 0, $pos), substr($tmp, $pos + 1));
      $r[$k] = $v;
   }
   return $r;
}
?>
