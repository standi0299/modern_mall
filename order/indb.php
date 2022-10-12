<?
/*
* @date : 20190219
* @author : kdk
* @brief : P1테마 장바구니 수량 변경 추가(ajax)
* @request : 기술지원팀 (PIXSTORY)
* @desc : cart.P1.htm set_cart_ea() 에서 호출.
*/

/*
* @date : 20180717
* @author : chunter
* @brief : pods10 연동안 잔재가 남아 있음. (편집기 상품이 아닌경우 속도가 느려지는 문제 있었음.)
* @desc : soup 호출 부분도 변경함.
*/
?>
<?
include_once "../lib/library.php";
//include_once dirname(__FILE__) . "/../models/m_studio.php";
include_once "../lib/class.cart.php";

//$m_studio = new M_studio();

if($_POST[mode] == "set_open_gallery"){

   $sql = "update exm_edit set open_gallery = 'Y' where storageid = '$_POST[storageid]'";
   $db->query($sql);

   $sql = "insert into md_gallery set
            cid = '$cid',
            mid = '$sess[mid]',
            storageid = '$_POST[storageid]',
            regdt = now()
          ";
   $db->query($sql);

   list($goodsnm) = $db->fetch("select b.goodsnm from exm_edit a inner join exm_goods b on a.goodsno=b.goodsno where a.cid='$cid' and a.mid='$sess[mid]' and a.storageid='$_POST[storageid]'", 1);

   //적립금 지급처리.
   setAddOpenGallery($cid, $sess[mid], $goodsnm);

   echo "ok";
   exit;
}

//P1테마 장바구니 수량 변경. / 20190219 / kdk.
if ($_POST[mode] == "setcartea") {
    $cart = new Cart($_POST[cartno]);

    $db->start_transaction();
    try {
        $cart->mod($_POST[cartno], $_POST[ea]);

        $db->query("commit");
        $response["result"] = "OK";
    } catch(Exception $e) {
        $db->query("rollback");
        $response["result"] = "FAIL";
    }
    $db->end_transaction();

    echo json_encode($response);
    exit;
}

//P1 절사 금액 호출 / 20190610 / jtkim
if ($_POST[mode] == "setcuttingmoney"){
   $cart = new Cart();
   $result['cutmoney'] = $cart -> setCuttingMoney($_POST[cutmoney]);
   $response[cut_flag] = "N";
   if(is_array($result['cutmoney'])){
      $response[cut_flag] = "1";
      $response[cutmoney] = $result['cutmoney']['res_num'];
      $response[cutmoney_mod] = $result['cutmoney']['res_mod'];
   }else{
      $response[cut_flag] = "2";
      $response[cutmoney] = $result['cutmoney'];
      $response[cutmoney_mod] = 0;
   }
   echo json_encode($response);
   exit;

}

//장바구니 선택된 주문만 가격정보 조회. 20180622 kdk.
if ($_POST[mode] == "getcartprice") {
    $_POST[cartno] = explode(",", $_POST[cartno]);

    $cart = new Cart($_POST[cartno]);

    $response[totprice] = number_format(($cart->itemprice + $cart->totshipprice - $cart->dc - $cart->dc_coupon))  ."원";

    $response[itemprice] = number_format($cart->itemprice) ."원";
    $response[dcprice] = number_format($cart->dc + $cart->dc_coupon) ."원";
    $response[shipprice] = number_format($cart->totshipprice) ."원";
	 $response[goodsprice] = number_format($cart->itemprice - $cart->dc - $cart->dc_coupon) ."원";
    //debug($cart->totemoney);
    $response[emoney] = number_format($cart->totemoney) ."P";



    //20181115 / minks / P1테마일 경우만 선택한 cartno가 쿠폰 사용가능한지 체크
    if ($cfg[skin_theme] == "P1") {
		if (is_array($cart->item)) {
			foreach ($cart->item as $v) {
				foreach ($v as $v2) {
					$cartitem[] = $v2;
				}
			}
		}

		if (is_array($cartitem)) {
			$m_member = new M_member();
			$coupon_ok = false;

			$couponlist = $m_member->getMyCouponList($cid, $sess[mid]);

			foreach ($cartitem as $v3) {
				if ($coupon_ok) break;

				foreach ($couponlist as $v4) {
					$coupon_dc = 0;

					if ($coupon_ok) break;

					switch ($v4[coupon_range]) {
						case "all":
							$coupon_ok = true;
						break;

						case "category":
							list($catno) = $db->fetch("select group_concat(catno order by cat_index separator ',') from exm_goods_link where cid = '$cid' and goodsno = '$v3[goodsno]'", 1);
							$catno = ($catno) ? explode(",", $catno) : array();
							$coupon_catno = ($v4[coupon_catno]) ? explode(",", $v4[coupon_catno]) : array();

							foreach ($coupon_catno as $v5) {
								if ($coupon_ok) break;

								foreach ($catno as $v6) {
									if (substr($v6, 0, strlen($v5)) == $v5) {
										$coupon_ok = true;
									}
								}
							}
						break;

						case "goods":
							$coupon_goodsno = ($v4[coupon_goodsno]) ? explode(",", $v4[coupon_goodsno]) : array();

							if (in_array($v3[goodsno], $coupon_goodsno)) {
								$coupon_ok = true;
							}
						break;
					}

					if (!$coupon_ok) continue;

					switch ($v4[coupon_way]) {
						case "price":
							if ($v4[coupon_type] == "discount") $coupon_dc = $v4[coupon_price];
							else if ($v4[coupon_type] == "saving") $coupon_dc = $v4[coupon_price];

							if ($v3[payprice] <= $coupon_dc) $coupon_dc = $v3[payprice];
						break;

						case "rate":
							if ($v4[coupon_type] == "discount") {
								$coupon_dc = round($v3[saleprice] * $v4[coupon_rate] / 100, -1);

								if ($v4[coupon_price_limit] && $v4[coupon_price_limit] < $coupon_dc) $coupon_dc = $v4[coupon_price_limit];
								if ($v3[payprice] <= $coupon_dc) $coupon_dc = $v3[payprice];
							} else if ($v4[coupon_type] == "saving") {
								$coupon_dc = round($v3[totreserve] * $v4[coupon_rate] / 100, -1);

								if ($v4[coupon_price_limit] && $v4[coupon_price_limit] < $coupon_dc) $coupon_dc = $v4[coupon_price_limit];
								if ($v3[payprice] <= $coupon_dc) $coupon_dc = $v3[payprice];
							}
						break;
					}

					if ($coupon_dc <= 0 && $v4[coupon_type] != "coupon_money") {
						$coupon_ok = false;
						continue;
					}

					if ($v4[coupon_min_ordprice] && $v3[payprice] < $v4[coupon_min_ordprice]) {
						$coupon_ok = false;
						continue;
					}

					$coupon_ok = true;
				}
			}

			$response[couponuse] = ($coupon_ok) ? "Y" : "N";
		}
    }

    echo json_encode($response);

    exit;
}


//장바구니 옵션 수량 변경 처리    20140531    chunter
if ($_POST[mode] == "cartmodupdate") {//wpod 편집기에만 해당 2014.05.21 by kdk
   $query = "update exm_cart set ea = '$_POST[ea]' where cartno = '$_POST[cartno]'";
   $db -> query($query);
   list($cartEaSum) = $db -> fetch("select sum(ea) from exm_cart where cid='$cid' and mid = '$sess[mid]'", 1);
   echo $cartEaSum;
   exit;
}

//장바구니에서 추가 옵션 변경 처리(basic스킨) / 14.07.04 / kjm
if ($_POST[mode] == "cartaddoptupdate") {
   $addoptno = implode(",", $_POST[addoptno]);

   $query = "update exm_cart set addoptno = '$addoptno' where cartno = '$_POST[cartno]'";
   $db -> query($query);

   if ($_POST[showMessage] == "Y")
      msgNpopClose(_("변경되었습니다."), $_SERVER['HTTP_REFERER']);
   if ($_POST[showPrice] == "Y") {
      //옵션의 전체 가격만 구한다.
      echo getGoodsAddOptionPrice($addoptno);
   }
   exit;
}

//장바구니에서 추가 옵션 변경 처리(biz_card스킨)    20140602    chunter
if ($_POST[mode] == "cartaddoptupdate_wpod") {
   $addoptno = $_POST[addoptno];
   $query = "update exm_cart set addoptno = '$addoptno' where cartno = '$_POST[cartno]'";
   $db -> query($query);
   if ($_POST[showMessage] == "Y")
      msgNpopClose(_("변경되었습니다."));
   if ($_POST[showPrice] == "Y") {
      //옵션의 전체 가격만 구한다.
      echo getGoodsAddOptionPrice($addoptno);
   }
   exit;
}

//20150605 / minks / 각 주문당 정산담당자 수정
if ($_POST[mode] == "cartmanagernoupdate_wpod") {
   $query = "update exm_cart set cart_manager_no = '$_POST[managerno]' where cartno = '$_POST[cartno]'";
   $db -> query($query);
   exit;
}

##############################################################################################################################
///podgroup_editupdate과 통합 2014.11.19 by kdk
/*if ($_POST[mode] == "wpodeditupdate") {//wpod 편집기에만 해당 2014.05.21 by kdk
    $db -> query("update exm_cart set updatedt = now() where storageid = '$_POST[storageid]'");
    $db -> query("update exm_edit set updatedt = now() where storageid = '$_POST[storageid]'");

    $wpod_ret = readUrlWithcurl('http://" .PODS20_DOMAIN. "/CommonRef/StationWebService/GetMultiOrderInfoResult.aspx?storageids=' . $_POST[storageid], false);
    $wdata = explode("|^|", $wpod_ret);
    $wpod_ret = _ilark_vars(substr($wdata[2], 8));
    $db -> query("update exm_cart set vdp_edit_data = '$wpod_ret[WDATA]' where storageid = '$_POST[storageid]'");
    //debug($wpod_ret[WDATA]);

    exit ;
}*/
if ($_POST[mode] == "editupdate") {
   $goods_options = json_decode(base64_decode($_POST[data]),1);

   $json_goods_options = json_encode($goods_options[uploaded_list][0][goods_options]);

   if($_POST[data]){
      $db->query("update tb_editor_ext_data set editor_return_json = '$json_goods_options' where storage_id = '$_POST[storageid]'");
   }

   //20160810 / minks / pc편집인지 모바일편집인지 구분
   if ($_POST[mobile_type] == "Y") $mobile_edit_flag = 1;
   else $mobile_edit_flag = 0;

   $db -> query("update exm_cart set updatedt = now(),mobile_edit_flag='$mobile_edit_flag' where storageid = '$_POST[storageid]'");
   $db -> query("update exm_edit set updatedt = now() where storageid = '$_POST[storageid]'");

   if ($_POST[mobile_type] == "Y") echo "ok";

   exit;
}
if ($_POST[mode] == "podgroup_editupdate" || $_POST[mode] == "wpodeditupdate") {//편집 수정 완료 후 재합성 처리 20140103 by kdk

   $db -> query("update exm_cart set updatedt = now() where storageid = '$_POST[storageid]'");
   $db -> query("update exm_edit set updatedt = now(), est_fullpost='download_count' where storageid = '$_POST[storageid]'"); //편집 수정 완료 후 다운로드 카운트를 해야함.est_fullpost 필드 활용 / 16.09.20 / kdk

   include "../lib/func.xml.php";

   if($_POST[mode] == "wpodeditupdate") {
      if($_POST[storageid]){
         $goods_options = json_decode(base64_decode($_POST[storageid]),1);

         $json_goods_options = json_encode($goods_options[uploaded_list][0][goods_options]);
         $db->query("update tb_editor_ext_data set editor_return_json = '$json_goods_options' where storage_id = '$goods_options[storageId]'");
      }

      $item = $db -> fetch("select * from exm_ord_item where storageid = '$_POST[storageid]'");
      $_POST[payno] = $item[payno];
      $_POST[ordno] = $item[ordno];
      $_POST[ordseq] = $item[ordseq];

       //장바구니에서 편집데이터 생성. insert_cartno가 있는경우 해당 cartno에 storageid 업데이트 210713 jtkim
       $sessionParamDecode = json_decode(urldecode(base64_decode($_POST[storageid])),1);
       $_sessionParamArray = array();
       $__sessionParamArray = array();
       parse_str(urldecode($sessionParamDecode['uploaded_list'][0]['session_param']),$_sessionParamArray);
       $__sessionParamArray = explode(',',$_sessionParamArray['sessionparam']);
       if(count($__sessionParamArray) > 0){
           forEach($__sessionParamArray as $k){
               if(strpos($k,'insert_cartno:') !== false){
                   $findCartno = substr($k,14,strlen($k));
                   $findCart = $db->fetch("select * from exm_cart where cid='$cid' and storageid= '' and cartno = '$findCartno'");
                   // debug($findCart);
                   if(is_array($findCart)){
                       $db->query("update exm_cart 
                            set storageid = '$sessionParamDecode[storageId]'
                            where cid='$cid' and storageid= '' and cartno = '$findCartno'");
                   }
               }
           }
       }

       exit;
   }else {
      $item = $db -> fetch("select * from exm_ord_item where payno = '$_POST[payno]' and ordno = '$_POST[ordno]' and ordseq = '$_POST[ordseq]'");
   }

   //pods 주문전송 orderStateCode 업데이트 (편집:30=>완료:40) / 16.07.13 / kdk
   set_pod_pay($_POST[payno],$_POST[ordno],$_POST[ordseq]);

   $release = $db -> fetch("select * from exm_release where rid = '$item[item_rid]'");
   $url = trim($release[oasis_url]) . "/oasis_remake.aspx";

   if (!$url) {
      msg(_("해당 상품의 제작사정보내의 오아시스 url 이 설정되어 있지 않습니다."));
      exit;
   }

   $returl = $url . "?order_code=$item[payno]&order_product_code=$item[storageid]&user_id=$sess[mid]";

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $returl);     // 접속할 URL 주소
   curl_setopt($ch, CURLOPT_HEADER, 0);       // 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
   curl_setopt($ch, CURLOPT_TIMEOUT, 30);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   $ret = curl_exec($ch);
   $ret = iconv("EUC-KR", "UTF-8", $ret);
   $ret = trim(preg_replace("/\<\?.*?\?\>/si", "", $ret));
   $ret = xml2Array($ret);

   switch ($ret[result][response_attr][response_id]) {
      case "ERROR" :
         msg($ret[result][response]);
         exit; break;
      case "SUCCESS" :
         $db->query("update exm_ord_item set itemstep = 2 where payno='$_POST[payno]' and ordno='$_POST[ordno]' and ordseq='$_POST[ordseq]' and storageid='$_POST[storageid]'");
         msg($ret[result][response]);
         exit; break;
      default :
         msg(_("오아시스와의 통신에러입니다."));
         exit; break;
   }
   exit;
}

if ($_POST[mode]=="module_exec") {
   list($chk) = $db->fetch("select storageid from exm_module where storageid = '$_POST[storageid]'", 1);
   if ($chk) {
      echo "yes";
   } else {
      $db->query("insert into exm_module set storageid = '$_POST[storageid]', state = '1' on duplicate key update state = '1'");
      echo "no";
   }
   exit;
}

if ($_POST[mode]=="module_end") {
   $db->query("delete from exm_module where storageid = '$_POST[storageid]'");
   $db->query("update exm_cart set updatedt = now() where storageid = '$_POST[storageid]'");
   exit;
}
##############################################################################################################################
$this_time = get_time();
$post = unserialize(base64_decode($_POST[orderinfo]));

if (!$_POST[payno]) {
   msg(_("결제번호가 생성되지 않았습니다."), -1);
   exit;
}

if ($_POST[coupon]) {
   $_POST[coupon] = stripslashes($_POST[coupon]);
   $coupon = unserialize($_POST[coupon]);
}

if ($_POST[sale_code_coupon]){
   $coupon[sale_code_coupon] = $_POST[sale_code_coupon];
}

//추가 배송비 계산때문에 receiver_zipcode 코드를 넘겨야 한다.        20171229    chunter
if ($post[receiver_zipcode]) $coupon[receiver_zipcode] = $post[receiver_zipcode];

$cart = new Cart($post[cartno], $coupon, '', $_POST[dc_partnership]);
$payprice = $cart->totprice - $cart->dc - $cart->dc_coupon - $cart->dc_sale_code_coupon - $cart->dc_partnership - $post[emoney];

if ($post[mid] && $post[mid] != $sess[mid]) {
   msg(_("로그인 세션이 유효하지 않습니다."));
   echo "<script>parent.location.href = '../order/cart.php';</script>";
   exit;
}
$debug_data .= debug_time($this_time);
if ($post[emoney]) {
   /*
   list($emoney) = $db->fetch("select emoney from exm_member where cid = '$cid' and mid = '$sess[mid]'", 1);
   if ($emoney < $post[emoney] || $cart->totprice - $cart->dc < $post[emoney]) {
      msg(_("적립금이 유효하지 않습니다"));
      echo "<script>parent.location.reload();</script>";
      exit;
   }
   */
   $result = setPayNUseEmoney($cid, $sess[mid], $post[emoney], $cart->totprice - $cart->dc - $cart->dc_coupon - $cart->dc_partnership, '', true);
   if($result[code] != "00") {
      msg(_("$result[msg]"));
      echo "<script>parent.location.reload();</script>";
      exit;
   }
}
$debug_data .= debug_time($this_time);
if ($_POST[dc_partnership]){
   if (!$_POST[dc_partnership_tworld_no]){
      msg("제휴할인이 유효하지 않습니다");
      echo "<script>parent.location.reload();</script>";
      exit;
   }

   list($point) = $db->fetch("select totPoint from exm_dc_partnership_tworld where no = '$_POST[dc_partnership_tworld_no]'",1);
   if ($_POST[dc_partnership]>$point || $_POST[dc_partnership]>$cart->max_dc_partnership){
      msg("제휴할인이 유효하지 않습니다");
      echo "<script>parent.location.reload();</script>";
      exit;
   }
}
$debug_data .= debug_time($this_time);
$post[receiver_mobile] = implode("-", $post[receiver_mobile]);

//20151231 / minks / 모바일 주문일 경우 주문자 정보가 없을 경우 오아시스에서 불편이 있으므로 주문자 정보를 수령자 정보와 같도록 수정
if ($_POST[mobile_type] == "Y") {
   if ($sess[mid]) {
      $post[orderer_mobile] = implode("-", $post[orderer_mobile]);
   } else {
      $post[orderer_name] = $post[receiver_name];
      $post[orderer_mobile] = $post[receiver_mobile];
   }
} else {
   $post[orderer_phone] = implode("-", $post[orderer_phone]);
   $post[orderer_mobile] = implode("-", $post[orderer_mobile]);
   //$post[orderer_zipcode] = implode("-", $post[orderer_zipcode]);
   $post[receiver_phone] = implode("-", $post[receiver_phone]);
}

switch ($post[paymethod]) {
   case "e": $step = 2; break;
   case "b": $step = 1; break;
   case "t": $step = 91; break;

	 //신용거래2
   case "d":
		 //구매 가능한 상태인지 체크한다.			//추가 개발 사항			20180921	chunter



		 $step = 2;
		 break;
   default : $step = 0; break;
}

//쿠폰이나 적립금으로 전체 금액을 결제한경우 무통장입금도 step 이 2가 된다.
//논리가 맞는지 생각중. P2 테마에서도 해당 기능이 필요한데... 적립금 결제라는 새로운 결제수단이 필요한지..				20180914		chunter
if ($payprice == 0)
{
	//if ($post[paymethod] == "b") $step = 2;
}

$pg_goods_cnt = 0;
foreach ($cart->item as $k => $v) {
   foreach ($v as $vv) {
      $pg_goodsnm = $vv[goodsnm];
      if (count($v) > 1) $pg_goodsnm .= "외".count($v) - 1 ."건";
      break;
   }
   foreach ($v as $vv) {
      $pg_goods_cnt += $vv[ea];
   }
}

list($chk) = $db->fetch("select payno from exm_pay where payno = '$_POST[payno]'", 1);
if ($chk) {
   echo "<script>alert('"._("결제번호 중복오류")."\\r\\n"._("결제번호를 다시발급합니다.")."\\r\\n"._("페이지가 새로고침 되면 다시 시도해주세요.")."')</script>";
   echo "<script>parent.location.reload();</script>";
   exit;
}
$debug_data .= debug_time($this_time);
//주문 중복 접수 체크 / 15.08.28 / kjm
$cartno = implode(",", $_POST[cartno]);

$storageOverlapChk = $db->fetch("select payno from exm_ord_item a
                                 inner join exm_cart b on a.storageid = b.storageid
                                 where b.cartno in ($cartno) and b.storageid != '' and a.itemstep >= 1");

if ($storageOverlapChk[payno]) {
    echo "<script>alert('"._("보관함코드 중복오류")."\\r\\n"._("이미 주문 처리된 상품이 존재합니다.")."')</script>";
    echo "<script>parent.location.href = '../order/cart.php';</script>";
    exit;
}

$db->start_transaction();

if (!$chk) {

   $post[request] = str_replace("'", "''", $post[request]);
   $post[request2] = str_replace("'", "''", $post[request2]);

   $post[request] = strip_tags($post[request]);
   $post[request2] = strip_tags($post[request2]);

   $escrow = ($post[escrow]) ? 1 : 0; # 2012-05-24

    ###알래스카용 주문입력 및 선입금액,선발행입금액 사용 등록.
    //모던 선입금,선발행입금 기능 사용 여부.(알래스카) / 20181210 / kdk
    if ($cfg[pod_deposit_money_flag] == "Y") {
        $m_pod = new M_pod();

        //주문 제목,주문사양,메모
        //debug(count($cart->item));
        //debug($cart->item);
        foreach ($cart->item as $key => $val) {
            foreach ($val as $item_list) {
                $p_order_title = addslashes($item_list[title]);
                $p_order_data = addslashes($item_list[est_order_option_desc]);
                $p_goodsno = $item_list[goodsno];
                $p_goodsnm = "";

                if(count($val) > 1) {
                    $p_goodsnm = $item_list[goodsnm] ." "._("외")." ". (count($val)-1) ._("건");
                }
                else {
                    $p_goodsnm = $item_list[goodsnm];
                }
            }
        }

        //선입금액,선발행입금액 조회. / 20181205 / kdk
        if($post[dmoney]) {
            $dmoney = $m_pod->getDepositMoney($cid, $sess[mid]);
            //debug("dmoney:".$dmoney);
            if ($dmoney < $post[dmoney]){
                msg("선입금액이 유효하지 않습니다");
                echo "<script>parent.location.reload();</script>";
                exit;
            }
        }

        if($post[pdmoney]) {
            $pdmoney = $m_pod->getPreDepositMoney($cid, $sess[mid]);
            //debug("pdmoney:".$pdmoney);
            if ($pdmoney < $post[pdmoney]){
                msg("선발행입금액이 유효하지 않습니다");
                echo "<script>parent.location.reload();</script>";
                exit;
            }
        }

        if($post[dmoney] || $post[pdmoney]) {
            if ($cart->totprice - $cart->dc - $cart->dc_sale_code_coupon < $post[dmoney] + $post[pdmoney]){
                msg("선입금액 또는 선발행입금액이 유효하지 않습니다");
                echo "<script>parent.location.reload();</script>";
                exit;
            }
        }

        //선입금액,선발행입금액 로그 등록.
        if($post[dmoney] > 0) {
            //로그저장(선입금사용).
            $log[cid] = $cid;
            $log[mid] = $sess[mid];
            $log[admin] = "";
            $log[memo] = "[$_POST[payno]][선입금액]사용.";
            $log[deposit_money] = "-$post[dmoney]";
            $log[pre_deposit_money] = "0";
            $log[payno] = $_POST[payno];
            $log[status] = "2";
            //debug($log);
            $m_pod->depositHistoryInsert($log);
        }

        if($post[pdmoney] > 0) {
            //로그저장(선발행입금액사용).
            $log[cid] = $cid;
            $log[mid] = $sess[mid];
            $log[admin] = "";
            $log[memo] = "[$_POST[payno]][선발행입금액]사용.";
            $log[deposit_money] = "0";
            $log[pre_deposit_money] = "-$post[pdmoney]";
            $log[payno] = $_POST[payno];
            $log[status] = "2";
            //debug($log);
            $m_pod->depositHistoryInsert($log);
        }

        $payprice = $cart->totprice - $cart->dc - $cart->dc_coupon - $cart->dc_sale_code_coupon - $cart->dc_partnership - $post[emoney] - $post[dmoney] - $post[pdmoney];

        //주문저장 pod_pay data.
        $podpay[cid] = $cid;
        $podpay[mid] = $sess[mid];
        $podpay[payno] = $_POST[payno];
        $podpay[admin] = "";
        $podpay[payprice] = "$cart->totprice - $cart->dc - $cart->dc_coupon - $cart->dc_partnership";
        $podpay[deposit_price] = "$post[dmoney]";
        $podpay[pre_deposit_price] = "$post[pdmoney]";
        $podpay[vat_price] = "0";
        $podpay[ship_price] = "$cart->totshipprice";
        $podpay[remain_price] = "$payprice"; //미수금
        $podpay[manager_no] = "";
        $podpay[autoproc_flag] = "0";
        $podpay[memo] = $post[request2];
        $podpay[status] = "1";
        $podpay[order_title] = "$p_order_title";
        $podpay[order_data] = "$p_order_data";
        $podpay[goodsno] = "$p_goodsno";
        $podpay[goodsnm] = "$p_goodsnm";
        $podpay[extopt_flag] = "0";
        $podpay[order_type] = "on-line";
        //debug($podpay);
        $m_pod->setPodPayInsert($podpay);
    }
$debug_data .= debug_time($this_time);

   ## 결제데이터 insert
   list($referer) = $db->fetch("select referer from exm_counter_ip where ip = '$_SERVER[REMOTE_ADDR]' and day = curdate()+0", 1);
   $query = "
   insert into exm_pay set
      cid               = '$cid',
      payno             = '$_POST[payno]',
      mid               = '$sess[mid]',
      paystep           = '$step',
      paymethod         = '$post[paymethod]',
      payprice          = '$payprice',
      saleprice         = '$cart->itemprice',
      shipprice         = '$cart->totshipprice',
      dc_member         = '$cart->dc',
      dc_emoney         = '$post[emoney]',
      dc_coupon         = '$cart->dc_coupon',
      dc_sale_code_coupon = '$cart->dc_sale_code_coupon',
      orddt             = now(),
      orderer_name      = '$post[orderer_name]',
      orderer_email     = '$post[orderer_email]',
      orderer_phone     = '$post[orderer_phone]',
      orderer_mobile    = '$post[orderer_mobile]',
      orderer_zipcode   = '$post[orderer_zipcode]',
      orderer_addr      = '$post[orderer_addr]',
      orderer_addr_sub  = '$post[orderer_addr_sub]',
      payer_name        = '$_POST[payer_name]',
      receiver_name     = '$post[receiver_name]',
      receiver_phone    = '$post[receiver_phone]',
      receiver_mobile   = '$post[receiver_mobile]',
      receiver_zipcode  = '$post[receiver_zipcode]',
      receiver_addr     = '$post[receiver_addr]',
      receiver_addr_sub = '$post[receiver_addr_sub]',
      request           = '$post[request]',
      request2          = '$post[request2]',
      escrow            = '$escrow',
      bankinfo          = '$_POST[bankinfo]',
      referer           = '$referer',
      dc_partnership_type = '$_POST[dc_partnership_type]',
      dc_partnership      = '$_POST[dc_partnership]',
      dc_partnership_tworld_no = '$_POST[dc_partnership_tworld_no]',
      order_skin =	'{$cfg[skin]}'
   ";
   $db->query($query);

   $ordno = 0;
   //debug($cart->item);
   foreach ($cart->item as $k => $v) { $ordno++;
      list($rid) = explode("_no:", $k);

      $order_shiptype = $cart->shipfree[$k];      //제작사별 주문 배송 방법을 ord_item 에 넣어준다      20141202    chunter

      ## 주문데이터 insert
      $query = "
      insert into exm_ord set
         payno          = '$_POST[payno]',
         ordno          = '$ordno',
         rid            = '$rid',
         shipprice      = '{$cart->shipprice[$k]}',
         acc_shipprice  = '{$cart->acc_shipprice[$k]}',
         order_shiptype = '$order_shiptype',
         ordprice       = '{$cart->ordprice[$k]}'
      ";
      $db -> query($query);

      $packageId = ""; //패키지 정보 ID. / 2017.12.20 / kdk
      $pakseq = 0;
      $ordseq = 0;
      foreach ($v as $v2) { $ordseq++;
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
         $query = "
         insert into exm_ord_item set
            payno                = '$_POST[payno]',
            ordno                = '$ordno',
            ordseq               = '$ordseq',
            goodsno              = '$v2[goodsno]',
            goodsnm              = '$v2[goodsnm]',
            optno                = '$v2[optno]',
            opt                  = '$v2[opt]',
            addoptno             = '" . implode(",", $v2[addoptno]) . "',
            addopt               = '$v2[addopt]',
            printopt             = '$v2[printopt]',
            goods_price          = '$v2[price]',
            aprice               = '$v2[aprice]',
            addopt_aprice        = '$v2[addopt_aprice]',
            print_aprice         = '$v2[print_aprice]',
            addpage_aprice       = '$v2[addpage_price]',
            addpage              = '$v2[addpage]',
            goods_reserve        = '$v2[reserve]',
            areserve             = '$v2[areserve]',
            addopt_areserve      = '$v2[addopt_areserve]',
            print_areserve       = '$v2[print_areserve]',
            addpage_areserve     = '$v2[addpage_reserve]',
            coupon_areserve      = '$v2[reserve_coupon]',
            coupon_areservesetno = '$v2[reserve_couponsetno]',
            saleprice            = '$v2[saleprice]',
            payprice             = '$v2[payprice]',
            ea                   = '$v2[ea]',
            dc_member            = '$v2[grpdc]',
            dc_coupon            = '$v2[dc_coupon]',
            dc_couponsetno       = '$v2[dc_couponsetno]',
            storageid            = '$v2[storageid]',
            reserve              = '$v2[totreserve]',
            cartno               = '$v2[cartno]',
            itemstep             = '$step',
            item_rid             = '$v2[rid2]',
            
            `title`              = '$v2[title]',
            
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
            est_pods_version        = '$v2[est_pods_version]',
            est_order_memo          = '$v2[est_order_memo]',
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
            vdp_edit_data           = '$v2[vdp_edit_data]',
            catno                   = '$v2[catno]',
            selfgoods               = '$self',
            package_order_code      = '$package_order_code',
            ext_json_data           = '$v2[ext_json_data]'
         ";
         //debug($query);exit;
         $db -> query($query);

         if ($step != 0) {
            if ($v2[dc_couponsetno]) {
               $query = "
               select coupon_type,coupon_price
               from
                  exm_coupon_set a
                  inner join exm_coupon b on b.cid = '$cid' and b.coupon_code = a.coupon_code
               where
                  a.no = '$v2[dc_couponsetno]'
               ";
               list ($coupon_type,$coupon_price) = $db->fetch($query,1);

               if ($coupon_type=="coupon_money"){
                  $v2[dc_coupon] = $v2[dc_coupon]+0;
                  $coupon_price = $coupon_price+0;
                  $db->query("
                     update exm_coupon_set set
                        coupon_able_money = coupon_able_money - $v2[dc_coupon],
                        payno          = '$_POST[payno]',
                        ordno          = '$ordno',
                        ordseq            = '$ordseq',
                        coupon_usedt      = now()
                     where no = '$v2[dc_couponsetno]'
                  ");
                  $db->query("
                     update exm_coupon_set set
                        coupon_use        = 1
                     where
                        no = '$v2[dc_couponsetno]'
                        and coupon_able_money <= 0
                  ");
               } else {
                  $db->query("
                     update exm_coupon_set set
                        coupon_use    = 1,
                        payno         = '$_POST[payno]',
                        ordno         = '$ordno',
                        ordseq        = '$ordseq',
                        coupon_usedt  = now()
                     where no = '$v2[dc_couponsetno]'
                  ");
               }
            }

            if ($v2[reserve_couponsetno]) {
               $db->query("
                  update exm_coupon_set set
                     coupon_use    = 1,
                     payno         = '$_POST[payno]',
                     ordno         = '$ordno',
                     ordseq        = '$ordseq',
                     coupon_usedt  = now()
                  where no = '$v2[reserve_couponsetno]'
               ");
            }

            //프로모션 코드 사용 등록         20171019    chunter
            if ($_POST[sale_code_coupon]) {
              $addColumn2 = "set
                     cid               = '$cid',
                     coupon_code       = '$_POST[sale_code_coupon]',
                     mid               = '$sess[mid]',
                     coupon_able_money = '{$cart->dc_sale_code_coupon}',
                     coupon_use    = 1,
                     payno         = '$_POST[payno]',
                     ordno         = '$ordno',
                     ordseq        = '$ordseq',
                     coupon_usedt  = now(),
                     coupon_setdt  = now()";
                     $m_etc = new M_etc();
                     $m_etc->setCouponSetInfo("", $addColumn2);
            }
         }
      }
   }
$debug_data .= debug_time($this_time);

   if ($step==2) {
      $db->query("update exm_pay set paydt = now() where payno = '$_POST[payno]'");

      $query = "select * from exm_ord_item where payno = '$_POST[payno]'";
      $res = $db->query($query);
      while ($data = $db->fetch($res)) {
         set_pod_pay($data[payno], $data[ordno], $data[ordseq]);
         set_acc_desc($data[payno], $data[ordno], $data[ordseq], 2);
      }
$debug_data .= debug_time($this_time);
      order_sms($_POST[payno]);
   } else if ($step==1 || $step==91) {

      $query = "select * from exm_ord_item where payno = '$_POST[payno]'";
      $res = $db->query($query);
      while ($data = $db->fetch($res)) {
         if ($data[storageid])
         {
            list($podskind, $pods_use) = $db->fetch("select podskind,pods_use from exm_goods where goodsno = '$data[goodsno]'", 1);

            if ($podskind) {/* 2.0 상품 */
            	$podsApi = new PODStation('20');
							$podsApi->UpdateStorageDate($data[storageid]);
            }
         }
      }
   }

   if ($step==2 || $step==1 || $step==91) {
      $query = "select * from exm_ord_item where payno = '$_POST[payno]'";
      $res = $db->query($query);
      while ($data = $db->fetch($res)) {
         set_stock($data[goodsno], $data[optno], $data[ea] * -1);
      }
   }
$debug_data .= debug_time($this_time);
   switch ($post[paymethod]) {

      case "c": case "v": case "o": case "m": case "h": case "ve": case "oe":

         //모바일 결제와 일반결제로 나누어서 처리한다. APP에서 결제가 넘어오면    mobile_type=>Y 20160106    chunter
         if ($_POST[mobile_type] == "Y") {
            //LG 결제의 경우 모바일에 따라 호출하는 스크립트가 다르다        20160104    chunter
            if ($cfg[pg][module]=="smartxpay") {
               echo "<script>parent.launchCrossPlatform();</script>";
            } else {
            		if ($_POST[iso_type] == "Y")
								{
									$iso_result['result'] = "OK";
									echo json_encode($iso_result);
									//echo "<script>parent._settle();</script>";
									//echo "<body onload='parent._settle();')</body>";
								} else {
									//kcp, inicis 는 같은 함수 호출
              		echo "<script>parent._settle();</script>";
								}
            }
         } else {
            if (allowMobilePGCheck()) {
              if ($cfg[pg][module]=="smartxpay")
              	echo "<script>parent.launchCrossPlatform();</script>";
              else
							{
              	echo "<script>parent._settle();</script>";
							}
							echo "<script>parent._settle();</script>";
            } else {

               if ($cfg[pg][module]=="inicis")
                  include "ins.inipay.php";
               else if ($cfg[pg][module]=="lg")
                  echo "<script>parent.doPay_ActiveX();</script>";
               else if ($cfg[pg][module]=="smartxpay")
                  echo "<script>parent.launchCrossPlatform();</script>";
               else if ($cfg[pg][module]=="easypay80")
                   echo "<script>parent.f_cert();parent._maskhide();</script>";
            	else
               	echo "<script>parent._settle();parent._maskhide();</script>"; //#mask_payment,#loadingImg hide 처리. / 20190115 / kdk
            }
         }

         break;

         //kakaopay 결제 수단 추가           20180306    chunter
         case "k":
            $goods_name = $pg_goodsnm;
            $qty = $pg_goods_cnt;
            include_once dirname(__FILE__)."/../pg/kakaopay/pay_order.php";
            if ($response[code] == "200")
         echo "<script>parent._settle('{$response[redirect_url]}');</script>";
            else
               echo "<script>alert(\"{$response[err_msg]}\");</script>";
            break;


			//신용거래2 결제 프로세스 추가. 미수,적립금 사용내역만 업데이트 처리. step=2 로 설정해서 그대로 처리.		20180921		chunter
			case "d" :
      	//미수금 변경.
      	if ($payprice > 0)
				{
          $member = new M_member();
					$member -> updateMemberAccountData($cid, $sess[mid], $payprice);
          $member -> insertDepositHistory($cid, $sess[mid], $_POST[payno], $payprice, "1", "", _("사용자 미수거래 결제"));
				}

         //debug($depositHistory); exit;
      default :
         //tb_pay_data 데이터 입력
         //$m_studio -> pay_data($_POST[payno], $cid, $sess[mid], '', $post[paymethod], '', '', '', '', '', $payprice, N);

         if ($post[emoney] > 0) {
            //set_emoney($post[mid], _("상품구입시 사용"), -$post[emoney], $_POST[payno]);
            setPayNUseEmoney($cid, $post[mid], $post[emoney], $cart->itemprice, $_POST[payno]);
         }

         $data = $db->fetch("select * from exm_pay where payno = '$_POST[payno]'");
         $query = "select * from exm_ord_item where payno = '$_POST[payno]'";
         $res = $db->query($query);
         while ($tmp = $db->fetch($res)) {
         		$db->query("delete from exm_cart where cartno = '$tmp[cartno]'");
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

            if($data[paymethod] == "v" || $data[paymethod] == "b"){
               autoSms(_("접수내역"), $data[orderer_mobile], $data);
               kakao_alimtalk_send($data[orderer_mobile],$post[mid],_("접수내역"), $data);
            }
            else{
               autoSms(_("주문접수"), $data[orderer_mobile], $data);
               kakao_alimtalk_send($data[orderer_mobile],$post[mid],_("주문접수"), $data);
            }

         }

       if ($_POST[mobile_type] == "Y") echo "<script>parent.location.replace('payend.php?payno=$_POST[payno]&mobile_type=Y');</script>";
       else echo "<script>parent.location.replace('payend.php?payno=$_POST[payno]');</script>";
      break;
   }
}

$db->end_transaction();
$db->close();
//echo $debug_data;
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
