<?

/*
* @date : 201809281
* @author : chunter
* @brief : 메일 발송시 시간이 오래걸려서 비동기 방식으로 메일 발송.
* @desc : mailSendAsyncWithLogID(). 비동기식으로 /_ilark/mail_send_async_proc.php 호출한다.
*/

/*
* @date : 20180611
* @author : kjm
* @brief : PODS 연동 SetOrderInfo3만 사용하도록 수정
* @desc : 경선씨 확인 후 수정 - 연동안을 최신연동안 1개만 사용
*/

/*
* @date : 20180425
* @author : chunter
* @brief : sms 발송을 발송여부 설정에 따라 발송 처리.
* @desc : sms_use_flag 설정은 센터의 몰관리에서 설정한다.
*/


/*
* @date : 20180405
* @author : kjm
* @brief : 3055편집기 내지 데이터 PODS 전달 기능 추가
* @desc : set_pod_pay() -> SetImpositionOption()함수
*/

/*
* @date : 20180105 (20180105)
* @author : kdk
* @brief : 패키지 상품 관련 연동 추가.
* @request :
* @desc :
* @todo :
*/
?>
<?

//추가 배송비 계산하기			20171229		chunter
function calcuShippingExtraPrice($zipcode, $cartno)
{
	global $db;
	$result[shipping_extra_price] = 0;
	if (is_array($cartno)) $cartno = implode(",",$cartno);

	if ($cartno)
	{
		$query = "select distinct b.rid from exm_cart a, exm_goods b where a.cartno in ($cartno) and a.goodsno=b.goodsno";
		$dataList = $db->listFetch($query);
		foreach ($dataList as $key => $value) {
			$where[] = "'{$value}'";
		}

		if ($where)
		{
			$sqlWhere = implode(",",$where);
			$query = "select * from tb_release_extra_shipping where rid in ($sqlWhere)";
            //$dataList = $db->listFetch($query);
            $dataList = $db->listArray($query); //배열로 수정. / 20190222 / kdk
			foreach ($dataList as $key => $value)
			{
                if ($zipcode == $value[zipcode])
				{
					if (is_numeric($value[add_price])) $result[shipping_extra_price] += $value[add_price];
				}
			}
		} else {
			$result[shipping_extra_msg] = _("출고처 설정이 없습니다.");
		}
	} else {
		$result[shipping_extra_msg] = _("잘못된 접근입니다.");
	}
	if ($result[shipping_extra_price] > 0) $result[shipping_extra_price_tag] = number_format($result[shipping_extra_price]);
	return $result;
}

//프로모션 코드 할인 체크 및 계산			20171019		chunter
function calcuPromotionCodeSale($sale_code, $cartno, $cartList = '')
{
   global $db,$cid;

	$result[sale_code_price] = "-1";
	$result[sale_code_msg] = "";

	$query = "select *  from exm_coupon where coupon_code = '{$sale_code}'";
	$data = $db->fetch($query);

	if (!$data) $result[sale_code_msg] = _("유효한 코드가 아닙니다.");
	if ($data[coupon_period_sdate] > TODAY_Y_M_D || $data[coupon_period_edate] < TODAY_Y_M_D)	$result[sale_code_msg] = TODAY_Y_M_D;

	if ($result[sale_code_msg] == "")
	{
      if (!$cartList)
		{
         if (! is_array($cartno))
				$cartno = explode(",",$cartno);
			include_once dirname(__FILE__)."/class.cart.php";
			$cart = new Cart($cartno);

			if (is_array($cart->item))
				$cartList = $cart->item;
		}

		if (is_array($cartList))
		{
			foreach ($cartList as $v){
				foreach ($v as $vv){
					$cartitem[] = $vv;
				}
			}
		}

      $item_payprice = 0;
		if (is_array($cartitem))
		{
         foreach ($cartitem as $k=>$item)
  	      {
            $coupon_ok = false;

	   		switch ($data[coupon_range])
	   		{
	   			case "all":
	   				$coupon_ok = true;
	   				break;

	   			case "category":
	   				list($catno) = $db->fetch("select catno from exm_goods_link where cid = '$cid' and goodsno = '$item[goodsno]'",1);
	   				$data[coupon_catno] = explode(",",$data[coupon_catno]);

	   				foreach ($data[coupon_catno] as $k2=>$v2)
	   				{
	   					if (substr($catno,0,strlen($v2))==$v2){
	   						$coupon_ok = true;
	   					} else {
	   						$coupon_ok = false;
	   					}

							if (!$coupon_ok) break;
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

				//할인이 적용되는 상품이 있을경경우 for exit
	   		if (!$coupon_ok) break;

	   		$item_payprice += $item[payprice];
	   	}

	   	if (!$coupon_ok)
	   		$result[sale_code_msg] = _("적용할수 없는 상품이 있습니다.");
			else {
	   		switch ($data[coupon_way]){
	   			case "price":
	   				if ($data[coupon_type]=="discount") $sale_code_price = $data[coupon_price];
	   				if ($item_payprice <= $sale_code_price) $sale_code_price = $item_payprice;
	   				break;

	   			case "rate":
	   				$sale_code_price = round($item_payprice * $data[coupon_rate]/ 100,-1);
	   				if ($data[coupon_price_limit] && $data[coupon_price_limit] < $sale_code_price) $sale_code_price = $data[coupon_price_limit];
	   				break;
	   		}

				if ($item_payprice <= $sale_code_price) $sale_code_price = $item_payprice;			//할인금액이 결제금액보다 클경우 결제금액만 할인된다.
	   		if ($data[coupon_min_ordprice] && $item_payprice < $data[coupon_min_ordprice]) $result[sale_code_msg] = _("최소 주문 금액은")." {$data[coupon_min_ordprice]} "._("입니다.");

	   		$result[sale_code_price] = $sale_code_price;
	   	}
		} else
			$result[sale_code_msg] = _("장바구니 상품이 없습니다.");
 	}

	return $result;
}


/* 적립금지급 */
//현재 사용하지 않는 함수임.해당 함수를 사용하는곳이 있다면  변경해야함.			20180913		chunter
//적립금 적립시는 setAddEmoney()사용, 사용은 setPayNUseEmoney() 사용
function set_emoney($mid,$memo,$emoney,$status='',$payno='',$ordno='',$ordseq=''){
	global $cid,$sess,$sess_admin;

   $_mid = ($sess_admin[mid]) ? $sess_admin[mid]:$sess[mid];
	$m_order = new M_order();

	//cid값을 전역변수로 가져오는게 아니라 해당 주문의 cid값을 가져온다
	//점보에서 호출하는 페이지가 1개라 모두 같은 cid값이 들어가서 수정 / 15.12.21 / kjm
	//주문상품의 경우 주문데이터의 cid를 가져오고 관리자 수동지급의 경우 해당 몰의 cid를 사용한다 / 15.12.28 / kjm
	if ($payno)
	{
    	$payinfo = $m_order->getPayInfo($payno);
   	if($payinfo[cid])
     	   $cid = $payinfo[cid];
	}

	$m_order->setEmoneyLogInsert($cid, $mid, $memo, $emoney, $_mid, $status, $payno, $ordno, $ordseq);

	$m_member = new M_member();
	$m_member->setEmoneyUpdate($cid, $mid, $emoney);
}

/* 적립금지급 */
/*function set_emoney($mid,$memo,$emoney,$payno='',$ordno='',$ordseq='',$status='1'){
   global $cid,$sess,$sess_admin;

   $_mid = ($sess_admin[mid]) ? $sess_admin[mid]:$sess[mid];
   $m_order = new M_order();

   //cid값을 전역변수로 가져오는게 아니라 해당 주문의 cid값을 가져온다
   //점보에서 호출하는 페이지가 1개라 모두 같은 cid값이 들어가서 수정 / 15.12.21 / kjm
   //주문상품의 경우 주문데이터의 cid를 가져오고 관리자 수동지급의 경우 해당 몰의 cid를 사용한다 / 15.12.28 / kjm
   if ($payno) {
      $payinfo = $m_order->getPayInfo($payno);
	   if($payinfo[cid])
         $cid = $payinfo[cid];
   }

   $m_order->setEmoneyLogInsert($cid, $mid, $memo, $emoney, $_mid, $payno, $ordno, $ordseq, $status);

   $m_emoney = new M_emoney();
   $m_emoney->setEmoneyUpdate($cid, $mid);
}*/

function set_log_step($payno,$ordno,$ordseq,$from,$to,$admin,$memo){
    $m_order = new M_order();
    $m_order->setStepLogInsert($payno, $ordno, $ordseq, $from, $to, $admin, $memo);
}

/* 스텝변경 func _new by TARAKA */
function chg_paystep($payno,$from,$to,$memo) {
   global $sess,$sess_admin,$cfg,$cid;

   $m_order = new M_order();
   $m_order->setPayStepUpdate($payno, $to);

   if ($from==1 && $to==2) {
      $m_order->setPayDateUpdateFromAdmin($payno, "mall_" .$sess_admin[mid]);
   }

   if ($from==91 && $to==92)
      $m_order->setConfirmDateUpdateFromAdmin($payno, 'mall_' .$sess_admin[mid]);

   if ($from==1 && $to==-9) {
      $p_data = $m_order->getPayInfo($payno);
      $mid = $p_data[mid];
      //$dc_emoney = $p_data[dc_emoney];
      //if ($mid && $dc_emoney) set_emoney($mid,$memo,$dc_emoney,$payno);
	  if ($mid) orderCancelEmoney($mid,$payno,$memo);
   }

   $res = $m_order->getOrdItemList($payno);
   foreach ($res as $key => $data) {
      chg_itemstep($payno,$data[ordno],$data[ordseq],$from,$to,$memo,'','');
   }
}


function chg_itemstep($payno,$ordno,$ordseq,$from,$to,$memo='',$shipcomp='',$shipcode=''){
   global $db,$sess,$sess_admin,$cid;
   $m_order = new M_order();
   $o_data = $m_order->getOrdItemInfo($payno, $ordno, $ordseq);
   $from = $o_data[itemstep];
   $reserve = $o_data[reserve];
   $ea = $o_data[ea];

   $coupon_areservesetno = $o_data[coupon_areservesetno];
   $dc_couponsetno = $o_data[dc_couponsetno];
   $dc_coupon = $o_data[dc_coupon];

   $goodsno = $o_data[goodsno];
   $optno = $o_data[optno];

   if ($from==$to){
      return;
   }

   if ($from!=5 && $to==5){
      if($shipcomp && $shipcode){
         $addqr = ", shipcomp = '$shipcomp', shipcode = '$shipcode', shipdt = now()";
      }else{
         $addqr = "";
      }

      $o_data = $m_order->getPayInfo($payno);
      $mid = $o_data[mid];

      //set_emoney($mid,_("상품구매"),$reserve,$payno,$ordno,$ordseq);

      //setAddEmoney($cid, $mid, $reserve, _("상품구매"), '', $payno, $ordno, $ordseq,'');

   } else if ($from==1 && $to==2){
      set_pod_pay($payno,$ordno,$ordseq);
   } else if ($from==91 && $to==92){
      set_pod_pay($payno,$ordno,$ordseq);
   } else if ((($from==1 || $from==2) && $to==-9) || (($from==91 || $from==92) && $to==-90)){
   	  $o_data = $m_order->getPayInfo($payno);

      /*
      if ($coupon_areservesetno){
         $m_etc = new M_etc();
         $m_etc->setCouponSetInfo($coupon_areservesetno);
      }

      if ($dc_couponsetno){
         $m_etc = new M_etc();
         $m_etc->setCouponSetInfo($dc_couponsetno);
      }
      */

      if ($coupon_areservesetno){
         $m_etc = new M_etc();

         $addColumn = "set
                        coupon_use   = 0,
                        payno        = null,
                        ordno        = null,
                        ordseq       = null,
                        coupon_usedt = null";
         $addWhere = "where no = '$coupon_areservesetno'";
   		$m_etc->setCouponSetInfo($coupon_areservesetno, $addColumn, $addWhere);
         /*
         $db->query("
         update exm_coupon_set set
             coupon_use      = 0,
             payno           = null,
             ordno           = null,
             ordseq          = null,
             coupon_usedt    = null
         where no = '$coupon_areservesetno'
         ");
         */
      }

      if ($dc_couponsetno){
         $m_etc = new M_etc();

         $addWhere2 = "where no = '$dc_couponsetno'";
         $c_data = $m_etc->getCouponSetInfo($cid, $addWhere2);

         if ($c_data[coupon_type] == "coupon_money") {
     	      $addColumn2 = "set
               coupon_use        = 0,
               coupon_able_money = coupon_able_money + $dc_coupon";
         } else {
     	      $addColumn2 = "set
               coupon_use   = 0,
               payno        = null,
               ordno        = null,
               ordseq       = null,
               coupon_usedt = null";
         }

         $m_etc->setCouponSetInfo($dc_couponsetno, $addColumn2, $addWhere2);
         /*
         $db->query("
         update exm_coupon_set set
             coupon_use      = 0,
             payno           = null,
             ordno           = null,
             ordseq          = null,
             coupon_usedt    = null
         where no = '$dc_couponsetno'
         ");
         */
      }

	  //취소 자동 메일 보내기
      autoMail("order_canc", $o_data[orderer_email],$o_data);
      kakao_alimtalk_send($o_data[orderer_mobile],$o_data[mid],_("주문취소"), $o_data);
   }

   # 재고변경
   $paystep_arr = array(91,92,1,2,3,4,5);
   if (!in_array($from,$paystep_arr) && in_array($to,$paystep_arr)){
      set_stock($goodsno,$optno,$ea*-1);
   }
   if (in_array($from,$paystep_arr) && !in_array($to,$paystep_arr)){
      set_stock($goodsno,$optno,$ea);
   }

   # 정산레코드 생성
   $paystep_arr2 = array(92,2,3,4,5);
   if (!in_array($from,$paystep_arr2) && in_array($to,$paystep_arr2)){
      set_acc_desc($payno,$ordno,$ordseq,2);
   }
   if (in_array($from,$paystep_arr2) && !in_array($to,$paystep_arr2)){
      set_acc_desc($payno,$ordno,$ordseq,-2);
   }

   if ($from!=5 && $to==5){
      set_acc_desc($payno,$ordno,$ordseq,5);
   }
   if ($from==5 && $to!=5){
      set_acc_desc($payno,$ordno,$ordseq,-5);
   }

   $m_order->setOrdItemStepUpdate($payno, $ordno, $ordseq, $to, $addqr);

   $_mid = ($sess_admin[mid]) ? $sess_admin[mid]:$sess[mid];
   set_log_step($payno,$ordno,$ordseq,$from,$to,$_mid,$memo);
}


//주문 검수 완료 처리 및 pods 주문 데이타 전송 처리     20150615    chunter
function setOrderInspectionNPodsTranc($payno, $ordno, $ordseq)
{
   $m_order = new M_order();
   $m_order->setOrderInspectionUpdate($payno, $ordno, $ordseq);

   set_pod_pay($payno,$ordno,$ordseq);
}


function set_pod_pay($payno,$ordno,$ordseq){
   //PODS1.0을 사용하는지 확인하기위해 글로벌변수 $r_podskind 추가 / 14.02.17 / kjm
   global $cid,$db,$client,$soap_port,$r_podskind,$r_podskind20, $cfg, $r_shiptype_pods_deliveryKind, $cfg_center;

   $m_order = new M_order();
   $pay = $m_order->getPayInfo($payno);
   $ordInfo = $m_order->getOrdInfo($payno, $ordno);
   $data = $m_order->getOrdItemInfo($payno, $ordno, $ordseq);
  //debug($data);
   //주문 검수 기능에 따라 모든 주문은 오아시스로 넘기지 않고 대기한다.      20150615    chunter
   //검수 사용중이고  주문 승인처리 했을경우만 pods 연동 처리한다.
   //자동 주문검수 사용하지 않을 경우는 자동 검수 완료 처리한다.
   $bPodsTrans = false;
   if($cfg[order_inspection] == "Y") {
      //검수 사용중이고 검수 완료인경우 는 처리
      if($data[order_inspection] == "Y") {
         $bPodsTrans = true;
      }
   } else {
      if($data[order_inspection] != "N") { //시안 요청일 경우 PODs 연동을 하지 않는다. / 20190123 / kdk
        $m_order->setOrderInspectionUpdate($payno, $ordno, $ordseq);
        $bPodsTrans = true;
      }

   }

   if ($bPodsTrans == false)
      return false;

   //추가 옵션 정보 만들어서 pods 연동시 추가함.     20140603    chunter
   $addoptstr = implode("^", getGoodsAddOptionValue($data[addoptno]));

   $m_goods = new M_goods();
   $g_data = $m_goods->getInfo($data[goodsno]);
   $podskind = $g_data[podskind];
   $pods_use = $g_data[pods_use];

   if($podskind == "3055"){

      $podsApi = new PODStation('20');
      $m_order = new M_order();
      $m_goods = new M_goods();

      $editor_json_data = $m_order->getEditorJsonData($data[storageid]);
      $editor_json_data = json_decode($editor_json_data,1);

      $cover_range_data = $m_goods->getCoverRangeStandardData($editor_json_data[cover_range_id]);

      $cover_param[storageid] = $data[storageid];
      $cover_param[inside_width] = $cover_range_data[inside_width];
      $cover_param[inside_height] = $cover_range_data[inside_height];

      $cover_param[cover_width] = $cover_range_data[width];
      $cover_param[cover_height] = $cover_range_data[height];


      if($addoptstr)
         $oasis_descripion_data = explode("^", $addoptstr);

      $covar_range_opt_data = $m_goods->getCoverRangeDataWithCoverID($editor_json_data[cover_range_id]);

      $oasis_descripion_data[] = $covar_range_opt_data[cover_range];
      $oasis_descripion_data[] = $covar_range_opt_data[cover_type];
      $oasis_descripion_data[] = $covar_range_opt_data[cover_paper_name];
      $oasis_descripion_data[] = $covar_range_opt_data[cover_coating_name];

      $addoptstr = implode("^", $oasis_descripion_data);
   }

   //견적상품으로 파일 직접 업로드 한 경우는 PODS 와 연동처리 하지 않는다.      //chunter   20130129
   if ($data[est_order_type] == "UPLOAD") return;

   //장바구니에 담기 위해 임시부여된 storageid는 PODS 와 연동처리 하지 않는다. / 20190130 / kdk
   if (strpos($data[storageid], "_temp_") !== FALSE) return;

   //패키지 상품 관련 연동 추가함. / 20180105 / kdk
   if ($data[package_order_code]) {
      //오아시스 옵션 데이터(패키지정보) 넘겨주기
      if(!$podskind) $podskind = 9999;
      $packageCode = explode("_",$data[package_order_code]);
      $pods_option_data[package_name] = $data[est_goodsnm]; //패키지상품명
      $pods_option_data[package_group_code] = $payno."_".$packageCode[0]; //패키지그룹
      $pods_option_data[package_order_code] = $payno."_".$data[package_order_code]; //패키지주문번호
   }

   if (!$data[storageid]) return;
   if (in_array($podskind,$r_podskind20)){ /* 2.0 상품 */

      //오아시스 주문옵션 정보가 있을경우 처리   20150911  chunter
      $podsApi = new PODStation('20');       //pods 연동 클래스 선언
      if (is_array($pods_option_data)) {
         $podsApi->SetMakingOption($data[storageid], $pods_option_data);
      }

      $soap_url   = "http://".PODS20_DOMAIN."/CommonRef/StationWebService/StationWebService.asmx?WSDL";

      $pod_data[storageId]        = $data[storageid];                                     // 보관함 코드, 필수 (22자 미만)
      $pod_data[orderUserId]      = $pay[mid]."^".$cid;                                   // 서비스 사이트에서 사용하는 사용자 아이디,  선택 (50자 미만)
      //$pod_data[siteOrderCode]  = $data[payno]."_".$data[ordno]."_".$data[ordseq];      // 서비스 사이트에서 생성된 주문번호, 필수 (50자 미만)
      $pod_data[siteOrderCode]    = $data[payno];                                         // 서비스 사이트에서 생성된 주문번호, 필수 (50자 미만)
      $pod_data[orderCount]       = $data[ea];                                            // 주문 수량 , 필수 (숫자)
      $pod_data[orderCost]        = $data[payprice]/$data[ea];                            // 개당 금액
      $pod_data[orderTotalCost]   = $data[payprice];                                      // 총 주문 금액
      $pod_data[orderDate]        = trim(str_replace("-","",substr($pay[orddt],0,10)));   // 주문 일자  , 필수 (8자 , yyyymmdd 형식 ex) ”20090417” )
      $pod_data[orderTime]        = trim(str_replace(":","",substr($pay[orddt],10,10)));  // 주문 시간  , 필수 (6자 , hhmmss 형식 ex) ”171210” )
      $pod_data[orderUserName]    = $pay[orderer_name];                                   // 주문자 이름 , 필수 (50자 미만)
      $pod_data[orderUserPost]    = str_replace("-","",$pay[orderer_zipcode]);            // 주문자 우편번호 , 선택 (6자)
      $pod_data[orderUserAddr1]   = trim($pay[orderer_addr]);                                   // 주문자 주소1 , 선택 (100자 미만)
      $pod_data[orderUserAddr2]   = trim($pay[orderer_addr_sub]);                               // 주문자 주소2 , 선택 (50자 미만)
      $pod_data[orderUserTel]     = $pay[orderer_phone];                                  // 주문자 전화번호 , 선택 (20자 미만)
      $pod_data[orderUserHp]      = $pay[orderer_mobile];                                 // 주문자 휴대전화 , 선택 (20자 미만)
      $pod_data[orderUserEmail]   = $pay[orderer_email];                                  // 주문자 이메일주소 , 선택 (50자 미만)
      $pod_data[receiverName]     = $pay[receiver_name];                                  // 수취인 이름  , 필수 (50자 미만)
      $pod_data[receiverPost]     = str_replace("-","",$pay[receiver_zipcode]);           // 수취인 우편번호  , 필수 (6자)
      $pod_data[receiverAddr1]    = trim($pay[receiver_addr]);                                  // 수취인 주소1  , 필수 (100자 미만)
      $pod_data[receiverAddr2]    = trim($pay[receiver_addr_sub]);                              // 수취인 주소2  , 필수 (50자 미만)
      $pod_data[receiverTel]      = $pay[receiver_phone];                                 // 수취인 전화번호 , 필수 (20자 미만)
      $pod_data[receiverHp]       = $pay[receiver_mobile];                                // 수치인 휴대전화 , 필수 (20자 미만)
      $pod_data[receiverMemo]     = $pay[request];                                        // 배송메모 , 선택 (100자 미만)
      $pod_data[adminMemo]        = "";                                                   // 관리자메모  , 선택 (100자 미만)
      $pod_data[receiverMemo2]    = $pay[request2];                                       // 추가메모  , 선택 (100자 미만)

      if ($ordInfo[order_shiptype]) $shipType = $r_shiptype_pods_deliveryKind[$ordInfo[order_shiptype]]; //배송 방법을 pods로 넘긴다.      20141202  chunter
      if ($shipType) $pod_data[deliveryKind] = $shipType;   // 배송방법 , 선택 (2자)
         else $pod_data[deliveryKind]  = "";  //20141014 / minks / null값일 경우 오류 발생

      if ($addoptstr)
         $pod_data[subOption]     = $addoptstr; // 추가 옵션    20140610  chunter

      $soapMethod = "SetOrderInfo3";
      //$soapMethodOption = "SetOrderInfoOption";
   }

   //include_once dirname(__FILE__)."/../lib/nusoap/lib/nusoap.php";
   //$client = new soapclient($soap_url,true);
   //$client = new nusoap_client($soap_url,true);
   //$client->xml_encoding = "UTF-8";
   //$client->soap_defencoding = "UTF-8";
   //$client->decode_utf8 = false;

   //20150210 / minks / pods1.0과 pods2.0의 함수명이 다르므로 $soapMethodOption."Result" 로 리턴값 확인함.
   /*
   if ($addoptstr) {
      if ($soapMethodOption == "SetOrderInfo2Option")
         $ret = $podsApi->SetOrderInfo2Option($pod_data);
      else if ($soapMethodOption == "SetOrderInfoOption")
         $ret = $podsApi->SetOrderInfoOption($pod_data);
   } else {
      if ($soapMethod == "SetOrderInfo2")
         $ret = $podsApi->SetOrderInfo2($pod_data);
      else if ($soapMethod == "SetOrderInfo3")
         $ret = $podsApi->SetOrderInfo($pod_data);
   }*/

   //최신 연동안만 호출해도 된다. / 18.06.11 / kjm
   $podsApi = new PODStation('20');       //pods 연동 클래스 선언
   $ret = $podsApi->SetOrderInfo($pod_data);

   $r_ret = array("success"=>1,"fail"=>-1);

   if ($ret[0]=="success"){
      $m_order->setEditStateUpdate($data[storageid], '9');
   }

   if (preg_match("/"._("주문정보가 이미 전송")."/si",$ret[1],$dummy)){
      $ret[0] = "success";
   }

   $m_order->setOrdItemPodsStateUpdate($payno, $ordno, $ordseq, $r_ret[$ret[0]], $ret[1]);

   //#########################################################
   //선정산 데이타 암호화 및 pods 전달하기.    20161124    chunter
   if ($cfg[before_account_flag] == "Y" && (in_array($podskind, $r_podskind20)) || $podskind === "") {
      $dc_emoney = "0";
      $dc_coupon = "0";
      $shipprice = "0";

      //$ordseq 가 1인경우는 제작사별 첫번째 상품인경우 배송비를 구해온다. 배송비는 제작사별 청구되므로
      if ($ordseq == "1") {
      //$ordInfo = $m_order->getOrdInfo($payno, $ordno);
         $shipprice = $ordInfo[shipprice];
      }

      //$ordno와 $ordseq 가 1인경우 첫번째 주문건에 대해서만 적립금, 쿠폰사용금액 차감을 사용한다.
      if ($ordno == "1" && $ordseq == "1") {
         $dc_emoney = $pay[dc_emoney];
         $dc_coupon = $pay[dc_coupon];
      }

      $edit_page_cnt = 0;
      //4.0 인화편집기 인화 주문 수량 구하기
      if (in_array($podskind, array(3010, 3011))) {
         $printoptArr = unserialize($data[printopt]);
         foreach ($printoptArr as $key => $value) {
            $edit_page_cnt += $value[ea];
         }
      }

      //팩키지 상품인경우 처음 상품 처리시만 정산 정보를 넘긴다...   팩키지 전체 상품이 $ordseq=0 이고 실제 주문상품은 $ordseq 가 1부터 시작한다.        //20171212     chunter
      $bAccount_data_trans = true;
      if ($data[package_order_code])
      {
         if ($ordseq == "1")
            $bAccount_data_trans = true;
         else
            $bAccount_data_trans = false;
      }

      if ($bAccount_data_trans)
      {
         $pod_data[mem_id] = $pay[mid];         //주문자 ID 설정 추가.
         $account_encrypt_data = setBeforeAccountDataEncrypt($pod_data, $podskind, $dc_emoney, $dc_coupon, $shipprice, $edit_page_cnt);
         //debug($account_encrypt_data);

         if ($account_encrypt_data) {
            $param = array(
               "storageid" => $data[storageid],
               "calculationData" => $account_encrypt_data,
               "calculationProcess" => $cfg[whos_account_call]
            );

            $podsApi = new PODStation('20');
            $podsRtn = $podsApi->SetOrderInfoCalculationData($param);
         }
      }
   }


   if($podskind == "3055"){
      $podsRtn = $podsApi->SetImpositionOption($cover_param);
   }

   //#########################################################
}


//선정산 데이타 암호화 처리 (podmanage 를 이용)		20161123		chunter
function setBeforeAccountDataEncrypt($pod_data, $podskind, $pay_emoney, $pay_coupon, $pay_delivery, $edit_page_cnt)
{
	global $cfg, $cfg_center, $cid;
	$url = "http://podmanage.bluepod.kr/_account/set_account_data_encrypt.php";

	$param = array(
		"mode" => "set_data_encrypt",
		"center_cid" => $cfg_center[center_cid],
		"mall_cid" => $cid,
		"podmanage_access_code" => $cfg[podmanage_access_code],
		"service_kind_type" => "bluepod",
		"order_code" => $pod_data[siteOrderCode],
		"storage_id" => $pod_data[storageId],
		"editor_no" => $podskind,
		"pay_price" => $pod_data[orderCost],				//개당가격.
		"pay_emoney" => $pay_emoney,
		"pay_coupon" => $pay_coupon,
		"pay_delivery" => $pay_delivery,
		"edit_page_cnt" => $edit_page_cnt,
		"order_cnt" => $pod_data[orderCount],
		"mem_id" => $pod_data[mem_id],
	);

	$rtnJson = sendPostData($url, $param);
	$JsonArr = json_decode($rtnJson, true);

	if ($JsonArr[status] == "OK")
		return $JsonArr[encrypt_text];
	else
		return "";
}

//pods 으로 대량 주문 처리하기 (bizcard 함수 분리, pods 연동만 같고 다른 내용이 많다. 추후 함칠수 있는지 보장.)				20151118		chunter
function set_pod_pay_all_batch($payno,$ordno,$ordseq) {
   global $cid, $r_podskind20, $cfg, $r_shiptype_pods_deliveryKind;
   $m_order = new M_order();
   $m_goods = new M_goods();
   $pay = $m_order->getPayInfo($payno);

   $storageidsArr = array();

   $res = $m_order->getOrdItemList($payno);
   foreach ($res as $key => $data) {

      //주문 검수 기능에 따라 모든 주문은 오아시스로 넘기지 않고 대기한다.      20150615    chunter
	   //검수 사용중이고  주문 승인처리 했을경우만 pods 연동 처리한다. 자동 주문검수 사용하지 않을 경우는 자동 검수 완료 처리한다.
	   $bPodsTrans = false;
	   if($cfg[order_inspection] == "Y") {
         //검수 사용중이고 검수 완료인경우 는 처리
	      if($data[order_inspection] == "Y") {
	           $bPodsTrans = true;
	      }
      } else {
          if($data[order_inspection] != "N") { //시안 요청일 경우 PODs 연동을 하지 않는다. / 20190123 / kdk
            $m_order->setOrderInspectionUpdate($payno, $data[ordno], $data[ordseq]);
	        $bPodsTrans = true;
          }
      }

      if ($bPodsTrans == false)
         continue;

      //pods 편집기 번호를 가져온다..	여러상품을 묶어서 주문하기 때문에 상품별로 가져온다.
      if ($data[goodsno] == "") {
         if (! $podskind[$data[goodsno]]) {
            $g_data = $m_goods->getInfo($data[goodsno]);
            $podskind[$data[goodsno]] = $g_data[podskind];
         }
      }

      //견적상품으로 파일 직접 업로드 한 경우는 PODS 와 연동처리 하지 않는다.      //chunter   20130129
      if ($data[est_order_type] == "UPLOAD") continue;

      if ($data[storageid]) {
         if (in_array($podskind[$data[goodsno]],$r_podskind20)) {
            //추가 옵션 정보 만들어서 pods 연동시 추가함.     20140603    chunter
		      $addoptstr = implode("^", getGoodsAddOptionValue($data[addoptno]));

            $pod_data20[$data[storageid]][storageId]        = $data[storageid];                                     // 보관함 코드, 필수 (22자 미만)
            $pod_data20[$data[storageid]][orderUserId]      = $pay[mid];                                            // 서비스 사이트에서 사용하는 사용자 아이디,  선택 (50자 미만)
         	//$pod_data20[siteOrderCode]  = $data[payno]."_".$data[ordno]."_".$data[ordseq];                        // 서비스 사이트에서 생성된 주문번호, 필수 (50자 미만)
            $pod_data20[$data[storageid]][siteOrderCode]    = $data[payno];                                         // 서비스 사이트에서 생성된 주문번호, 필수 (50자 미만)
            $pod_data20[$data[storageid]][orderCount]       = $data[ea];                                            // 주문 수량 , 필수 (숫자)
            $pod_data20[$data[storageid]][orderCost]        = $data[payprice]/$data[ea];                            // 개당 금액
            $pod_data20[$data[storageid]][orderTotalCost]   = $data[payprice];                                      // 총 주문 금액
            $pod_data20[$data[storageid]][orderDate]        = trim(str_replace("-","",substr($pay[orddt],0,10)));   // 주문 일자  , 필수 (8자 , yyyymmdd 형식 ex) ”20090417” )
            $pod_data20[$data[storageid]][orderTime]        = trim(str_replace(":","",substr($pay[orddt],10,10)));  // 주문 시간  , 필수 (6자 , hhmmss 형식 ex) ”171210” )
            $pod_data20[$data[storageid]][orderUserName]    = $pay[orderer_name];                                   // 주문자 이름 , 필수 (50자 미만)
            $pod_data20[$data[storageid]][orderUserPost]    = str_replace("-","",$pay[orderer_zipcode]);            // 주문자 우편번호 , 선택 (6자)
            $pod_data20[$data[storageid]][orderUserAddr1]   = trim($pay[orderer_addr]);                                   // 주문자 주소1 , 선택 (100자 미만)
            $pod_data20[$data[storageid]][orderUserAddr2]   = trim($pay[orderer_addr_sub]);                               // 주문자 주소2 , 선택 (50자 미만)
            $pod_data20[$data[storageid]][orderUserTel]     = $pay[orderer_phone];                                  // 주문자 전화번호 , 선택 (20자 미만)
            $pod_data20[$data[storageid]][orderUserHp]      = $pay[orderer_mobile];                                 // 주문자 휴대전화 , 선택 (20자 미만)
            $pod_data20[$data[storageid]][orderUserEmail]   = $pay[orderer_email];                                  // 주문자 이메일주소 , 선택 (50자 미만)
            $pod_data20[$data[storageid]][receiverName]     = $pay[receiver_name];                                  // 수취인 이름  , 필수 (50자 미만)
            $pod_data20[$data[storageid]][receiverPost]     = str_replace("-","",$pay[receiver_zipcode]);           // 수취인 우편번호  , 필수 (6자)
            $pod_data20[$data[storageid]][receiverAddr1]    = trim($pay[receiver_addr]);                                  // 수취인 주소1  , 필수 (100자 미만)
            $pod_data20[$data[storageid]][receiverAddr2]    = trim($pay[receiver_addr_sub]);                              // 수취인 주소2  , 필수 (50자 미만)
            $pod_data20[$data[storageid]][receiverTel]      = $pay[receiver_phone];                                 // 수취인 전화번호 , 필수 (20자 미만)
            $pod_data20[$data[storageid]][receiverHp]       = $pay[receiver_mobile];                                // 수치인 휴대전화 , 필수 (20자 미만)
            $pod_data20[$data[storageid]][receiverMemo]     = $pay[request]." / ".$pay[request2];                   // 배송메모 / 추가메모 , 선택 (100자 미만), 20141107 / minks / 추가메모 추가
            $pod_data20[$data[storageid]][adminMemo]        = "";

            if ($data[shiptype]) $shipType = $r_shiptype_pods_deliveryKind[$data[shiptype]];    //배송 방법을 pods로 넘긴다.      20140923  chunter
            if ($shipType) $pod_data20[$data[storageid]][deliveryKind]     = $shipType;         // 배송방법 , 선택 (2자)
            else $pod_data20[$data[storageid]][deliveryKind]	= "";                            //20141014 / minks / null값일 경우 오류 발생

            if ($addoptstr)
               $pod_data20[$data[storageid]][sub_option] = $addoptstr;                      // 추가 옵션    20140610  chunter
            if ($pods_master_option)
               $pod_data20[$data[storageid]][master_option] = $pods_master_option;          //오아시스 제작옵션

            $storageidsArr20[] = "'$data[storageid]'";
         } else {
            //pods 1 은 대량 주문 처리가 없다..
        	   set_pod_pay($data[payno],$data[ordno],$data[ordseq]);
         }
      }
   }

   if (count($pod_data20) > 0) {
      $paramData = json_encode($pod_data20);

      $podsApi = new PODStation('20');
		$ret = $podsApi->SetBPOrderinfo(urlencode($paramData));

	   $r_ret = array("success" => 1, "fail" => -1);
      if ($ret[0] == "success") {
         if (preg_match("/"._("주문정보가 이미 전송")."/si", $ret[1], $dummy)) {
            $ret[0] = "success";
	    	}

         $storageids = implode(",", $storageidsArr20);
         $m_order->setEditStateNPodsTransUpdateWithStorageids($storageids, '9', $r_ret[$ret[0]], $ret[1]);
      }
   }
}

### 이메일 로그
function emailLog($data,$cnt=1)
{
    global $cid;

    $data[contents] = addslashes($data[contents]);
    $data[to]       = (is_array($data[to])) ? implode(";",$data[to]):$data[to];

    ### 이메일 전송 로그
    $e_etc = new M_etc();
    $logID = $e_etc->setEmailLogInsert($cid, $data[to], $data[subject], $data[contents], $cnt);
		return $logID;
}

### 치환코드 파싱
function parseCode($str,$data){
   extract($data);
   $str = preg_replace("/{([a-zA-Z_]+)}/","{\$$1}",$str);
   eval("\$str = \"$str\";");
   return $str;
}

### 자동메일발송
function autoMail($type,$to,$data=''){
   global $cfg,$cid;

   $m_config = new M_config();
   $c_data = $m_config->getCenterConfigInfo('center_cid');
   $center_cid = $c_data[value];

   include_once dirname(__FILE__)."/../lib/class.mail.php";
   include_once dirname(__FILE__)."/../lib/template_/Template_.class.php";

   $m_etc = new M_etc();
   $automsg = $m_etc->getAutoMsgInfo($cid, $type, 'mail');

   if (!$automsg[send]) return;
   if (!$to) return;

   $mail = new Mail($params);
   $headers['Name']    = $cfg[nameSite];
   $headers['From']    = $cfg[emailAdmin];
   $headers['Subject'] = $automsg[subject];
   $headers['To']      = $to;

   $fp = fopen(dirname(__FILE__)."/../conf/email/tpl.$type.php","w");
   fwrite($fp,$automsg[msg1]);
	 fclose($fp);

   $tpl = new Template_;
   $tpl->template_dir = dirname(__FILE__)."/../conf/email";
   $tpl->compile_dir  = dirname(__FILE__)."/../_compile/";
   $tpl->define('tpl',"tpl.$type.php");

   if (!$data[nameSite]) $data[nameSite] = $cfg[nameSite];
   if ($data){
      $headers['Subject'] = parseCode($headers['Subject'],$data);
      $tpl->assign($data);
   }

   $tpl->assign($cfg);
   $contents = $tpl->fetch('tpl');
   //$tpl->print_('tpl');
   $log_mail_no = emailLog(array("to"=>$headers['To'],"subject"=>$headers['Subject'],"contents"=>$contents),1);
	 mailSendAsyncWithLogID($log_mail_no);

   //$mail->send($headers, $contents);			//mailSendAsyncWithLogID() 로 대체 		20180928		chunter
}



//기존 자동메일과 합쳐도 될 듯
//나누는게 나중에 수정할때 더 좋을 것 같다 / 16.11.04 / kjm
### 자동관리자 메일발송
function autoMailAdmin($type,$to,$data=''){
   global $cfg,$cid;

   $m_config = new M_config();
   $c_data = $m_config->getCenterConfigInfo('center_cid');
   $center_cid = $c_data[value];

   include_once dirname(__FILE__)."/../lib/class.mail.php";
   include_once dirname(__FILE__)."/../lib/template_/Template_.class.php";

   //추가됨
   ### 추가관리자 메일추출
   $cfg[email2] = explode("|", $cfg[email2]);

   $m_etc = new M_etc();
   $automsg = $m_etc->getAutoMsgInfo($cid, $type, 'mail');

   $r_target[0] = $cfg[email1];

   $to = $r_target[0];

   if (!$automsg[send]) return;
   if (!$to) return;

   $mail = new Mail($params);
   $headers['Name']    = $cfg[nameSite];
   $headers['From']    = $cfg[emailAdmin];
   $headers['Subject'] = $automsg[subject];
   $headers['To']      = $to;

   $fp = fopen(dirname(__FILE__)."/../conf/email/tpl.$type.php","w");
   fwrite($fp,$automsg[msg1]);
	 fclose($fp);

   $tpl = new Template_;
   $tpl->template_dir = dirname(__FILE__)."/../conf/email";
   $tpl->compile_dir  = dirname(__FILE__)."/../_compile/";
   $tpl->define('tpl',"tpl.$type.php");

   if (!$data[nameSite]) $data[nameSite] = $cfg[nameSite];
   if ($data){
      $headers['Subject'] = parseCode($headers['Subject'],$data);
      $tpl->assign($data);
   }

   $tpl->assign($cfg);
   $contents = $tpl->fetch('tpl');

   //$tpl->print_('tpl');
   $log_mail_no = emailLog(array("to"=>$headers['To'],"subject"=>$headers['Subject'],"contents"=>$contents),1);
	 mailSendAsyncWithLogID($log_mail_no);
   //$mail->send($headers, $contents);			//mailSendAsyncWithLogID() 로 대체 			20180928		chunter

   //추가됨
   ### 추가관리자에게도발송
   if (array_notnull($cfg[email2]) && $automsg[send_add_admin]){
      foreach ($cfg[email2] as $val){
         $headers['To'] = $val;

         $log_mail_no = emailLog(array("to"=>$headers['To'],"subject"=>$headers['Subject'],"contents"=>$contents),1);
				 mailSendAsyncWithLogID($log_mail_no);
         //$mail->send($headers, $contents);		//mailSendAsyncWithLogID() 로 대체 			20180928		chunter
      }
   }
}
### 게시판자동메일발송
function autoMailBoard($type,$to,$data=''){
	global $cfg,$cid;

	$m_config = new M_config();
	$c_data = $m_config->getCenterConfigInfo('center_cid');
	$center_cid = $c_data[value];

	include_once dirname(__FILE__)."/../lib/class.mail.php";
	include_once dirname(__FILE__)."/../lib/template_/Template_.class.php";

	$m_etc = new M_etc();
	$automsg = $m_etc->getAutoMsgInfo($cid, $type, 'mail_board');

	if (!$automsg[send]) return;
	if (!$to) return;

	$mail = new Mail($params);
	$headers['Name']    = $cfg[nameSite];
	$headers['From']    = $cfg[emailAdmin];
	$headers['Subject'] = $automsg[subject];
	$headers['To']      = $to;

	$fp = fopen(dirname(__FILE__)."/../conf/email/tpl.$type.php","w");
	fwrite($fp,$automsg[msg1]);
	fclose($fp);

	$tpl = new Template_;
	$tpl->template_dir = dirname(__FILE__)."/../conf/email";
	$tpl->compile_dir  = dirname(__FILE__)."/../_compile/";
	$tpl->define('tpl',"tpl.$type.php");

	if (!$data[nameSite]) $data[nameSite] = $cfg[nameSite];
	if ($data){
		$headers['Subject'] = parseCode($headers['Subject'],$data);
		$tpl->assign($data);
	}

	$tpl->assign($cfg);
	$contents = $tpl->fetch('tpl');
	//$tpl->print_('tpl');
	$log_mail_no = emailLog(array("to"=>$headers['To'],"subject"=>$headers['Subject'],"contents"=>$contents),1);
	mailSendAsyncWithLogID($log_mail_no);

	### 추가 수신 메일주소가 있는경우 전송
	if ($automsg[send_add_admin]){
		$headers['To'] = $automsg[send_add_admin];

		$log_mail_no = emailLog(array("to"=>$headers['To'],"subject"=>$headers['Subject'],"contents"=>$contents),1);
		mailSendAsyncWithLogID($log_mail_no);
	}

	//$mail->send($headers, $contents);			//mailSendAsyncWithLogID() 로 대체 		20180928		chunter
}


### ilark전용 자동메일발송
function autoMailIlark($to,$data=''){
   global $cfg,$cid;

   $m_config = new M_config();
   $c_data = $m_config->getCenterConfigInfo('center_cid');
   $center_cid = $c_data[value];

   include_once dirname(__FILE__)."/../lib/class.mail.php";
   include_once dirname(__FILE__)."/../lib/template_/Template_.class.php";

   if (!$to) return;

   $mail = new Mail($params);
   $headers['Name']    = $cfg[nameSite];
   $headers['From']    = $cfg[emailAdmin];
   $headers['Subject'] = $data[subject];
   $headers['To']      = $to;

   $contents = $data[contents];
   $log_mail_no = emailLog(array("to"=>$headers['To'],"subject"=>$headers['Subject'],"contents"=>$contents), 1);
   mailSendAsyncWithLogID($log_mail_no);

   //$mail->send($headers, $contents);			//mailSendAsyncWithLogID() 로 대체 		20180928		chunter
}


//메일을 비동기 방식으로 보낸다.		로그 ID를 넘겨서 로그 내용으로 보낸다.			20180928		chunter
function mailSendAsyncWithLogID($log_mail_no)
{
	$uri = "http://{$_SERVER['HTTP_HOST']}/_ilark/mail_send_async_proc.php";
	$params[log_mail_no] = $log_mail_no;
	//echo $uri;
	curl_post_async($uri, $params);
}



### 자동문자발송
###autoSms("발송타입(회원가입)","받는전화번호","템플릿치환코드배열")

function autoSms($type,$to='',$arr=''){
   global $cfg, $cid;

   ### 유효성체크
   if (!$cfg[smsAdmin]) return;

   ### 추가관리자번호추출
   $cfg[mobile2] = explode("|", $cfg[mobile2]);

   ### 자동SMS 정보추출
   $m_etc = new M_etc();
   $r_automsg = $m_etc->getAutoMsgInfo($cid, $type, 'sms');

   if ($arr){
      $arr = array_merge($cfg,$arr);
   } else {
      $arr = $cfg;
   }

   $arr[payprice] = number_format($arr[payprice]);

   // 치환문자 배송업체 송장번호 추가 200427 jtkim
   if($arr[item][0][shipcomp][compnm]){
      $r_automsg[msg1] = str_replace("{.shipcomp}",$arr[item][0][shipcomp][compnm],$r_automsg[msg1]);
      $r_automsg[msg2] = str_replace("{.shipcomp}",$arr[item][0][shipcomp][compnm],$r_automsg[msg2]);
   }

   if($arr[item][0][shipcode]){
      $r_automsg[msg1] = str_replace("{.shipcode}",$arr[item][0][shipcode],$r_automsg[msg1]);
      $r_automsg[msg2] = str_replace("{.shipcode}",$arr[item][0][shipcode],$r_automsg[msg2]);
   }

   $r_automsg[msg1] = parseCode($r_automsg[msg1],$arr);
   $r_automsg[msg2] = parseCode($r_automsg[msg2],$arr);

   ### 발송대상체크
   ### $r_target[0] : 고객에게발송
   ### $r_target[1] : 관리자에게도발송
   ### $r_target[2] : 추가관리자에게도발송

   for ($i=0;$i<3;$i++){
      $r_target[$i] = $r_automsg[send]&pow(2,$i);
   }

   ### 고객에게발송
   if ($r_target[0] && $to) sendSms($to,$r_automsg[msg1]);

   ### 관리자에게도발송
   if ($r_target[1] && $cfg[mobile1]) sendSms($cfg[mobile1],$r_automsg[msg2]);

   ### 추가관리자에게도발송
   if ($r_target[2] && array_notnull($cfg[mobile2])){
      foreach ($cfg[mobile2] as $number){
         sendSms($number,$r_automsg[msg2]);
      }
   }
}

### SMS 발송
function sendSms($to,$msg,$from=""){
	global $cfg;

	//sms 사용시만 발송한다.sms 발송설정은 센터에서		20180425	chunter
	if ($cfg[sms_use_flag] != "N")
	{
		include_once dirname(__FILE__)."/../lib/class.sms.php";
   		$sms = new Sms();

		if (!$from) $from = $cfg[smsAdmin];
		$sms->from = $from;

		$sms->to = $to;
		$sms->send($msg);
	}
}

function set_acc_history($payno,$ordno,$ordseq,$flag=1){
   global $cid,$sess,$sess_admin;

   if (!$payno || !$ordno || !$ordseq){
      return;
   }

   $m_order = new M_order();
   $data = $m_order->getOrdItemInfo($payno, $ordno, $ordseq);

   $o_data = $m_order->getPayInfo($data[payno]);
   $data[cid] = $o_data[cid];
   $data[paydt] = $o_data[paydt];
   $data[paymethod] = $o_data[paymethod];

   if ($data[selfgoods]) return;

   $month = date("Ym");
   $data[goodsnm] = addslashes($data[goodsnm]);

   $m_order->setAccHistoryInsert($cid, $sess_admin[mid], $data, $month, $flag);

   $tmp = $m_order->getOrdInfo($payno, $ordno);

   if ($tmp[acc_shipprice]) {
      $m_order->setAccDpriceInsert($cid, $sess_admin[mid], $data, $tmp, $month);
   }
}

function set_acc_desc($payno,$ordno,$ordseq,$kind){
   global $cid,$db,$sess,$sess_admin;

   if (!$payno || !$ordno || !$ordseq || !$kind){
      return;
   }

   $m_order = new M_order();
   $data = $m_order->getOrdItemInfo($payno, $ordno, $ordseq);

   $o_data = $m_order->getPayInfo($data[payno]);
   $data[cid] = $o_data[cid];
   $data[paydt] = $o_data[paydt];
   $data[paymethod] = $o_data[paymethod];
   $data[mid] = $o_data[mid];

   if ($data[selfgoods]) return;

   $flag = $kind/abs($kind);

   $month = date("Ym");
   $data[goodsnm] = addslashes($data[goodsnm]);
   $r_comment = array(
      2   => _("결제"),
      5   => _("출고"),
      -2  => _("출고전 취소"),
      -5  => _("출고후 취소"),
      9   => _("배송비발생"),
   );
   $comment = $r_comment[$kind];

   $m_order->setAccDescInsertFullData($cid, $sess_admin[mid], $flag, $kind, $comment, $data);

   $o_data = $m_order->getAccDescInfo($data[cid], $data[payno], $data[ordno], $data[item_rid], '9');
   $chk_ship = $o_data[no];

   $tmp = $m_order->getOrdInfo($payno, $ordno);

   if (($tmp[acc_shipprice] || $tmp[shipprice]) && !$chk_ship && $kind == 5) {
      $m_order->setAccDescInsert($cid, $sess_admin[mid], '1', '9', $comment, $data, $tmp);
   }
}

function order_sms($payno=""){
	global $db,$cid,$cfg_center,$cfg;

	if (!$payno) return;

	//sms 사용시만 발송한다.		20180425	chunter
	if ($cfg[sms_use_flag] != "N")
	{
  		@include_once dirname(__FILE__)."/class.sms.php";
   		$sms = new Sms();
	}
	@include_once dirname(__FILE__)."/class.mail.php";
	$mail = new Mail($param);

	  ### 공급사추출
	  $m_order = new M_order();
	  $o_data = $m_order->getOrdGroupConcatInfo($payno);
	  $r_rid = $o_data[rid];

	$r_rid = array_unique(explode(",",$r_rid));

	foreach ($r_rid as $v){
		### 공급사정보추출
		$m_goods = new M_goods();
		$data = $m_goods->getReleaseInfo($v);

		### 공급사에게 메일발송
		if (trim($cfg_center[email]) && $data[order_email] && trim($data[email])){
			if ($cfg_center[centernm]){
				$msg = "[$cfg_center[centernm]] "._("새로운 주문이 발생하였습니다.");
			} else {
				$msg = _("새로운 주문이 발생하였습니다.");
			}

			  $headers['Name']    = $cfg_center[centernm];
			  $headers['From']    = $cfg_center[email];
			  $headers['Subject'] = $msg;
			  $headers['To']      = $data[email];
			  $mail->send($headers, $msg);
		}

		## 공급사에게 SMS발송
		//sms 사용시만 발송한다.(sms 발송설정은 센터에서)		20180425	chunter
		if ($cfg[sms_use_flag] != "N")
		{
			if (trim($cfg_center[phone]) && $data[order_sms] && trim($data[phone])){
				$sms->from = trim(str_replace("-","",$cfg_center[phone]));
				$sms->to = trim(str_replace("-","",$data[phone]));
				if ($cfg_center[centernm]){
					$msg = "[$cfg_center[centernm]] "._("새로운 주문이 발생하였습니다.");
				} else {
					$msg = _("새로운 주문이 발생하였습니다.");
				}
				$sms->send($msg);
			}
		}
	}
}

   function kakao_alimtalk_send($from,$mid,$code,$arr = array()){
      global $cid,$db,$cfg;

      if(!$cfg['alimtalk_api_key'] || !$cfg['alimtalk_api_id'])
      {
         return;
      }

      include_once dirname(__FILE__) . "/../lib/class.kakao.alimtalk.aligo.php";

      $kakao_aligo = new KakaoAlimtalkAligo();

      $mapping_qry = "select k_code from exm_alimtalk_mapping
      where cid='$cid'
      and code = '$code' 
      and group_code='aligo'";

      $aligo_template_code = $db->fetch($mapping_qry);
      $aligo_template_code = $aligo_template_code[k_code];
      $kakao_log_data = array(
         "cid" => $cid,
         "mid" => $mid,
         "number" => $from,
         "msg" => "",
         "cat_number" => "",
         "catnm" => "",
         "regdt" => date("Y-m-d H:i:s"),
         "sender_result" => ""
      );

      if ($arr){
         $arr = array_merge($cfg,$arr);
      } else {
         $arr = $cfg;
      }

      $arr[payprice] = number_format($arr[payprice]);

      // 치환문자 배송업체 송장번호 추가 200427 jtkim
      if($arr[item][0][shipcomp][compnm]){
         $arr["shipcompnm"] = $arr[item][0][shipcomp][compnm];
      }

      if($arr[item][0][shipcode]){
         $arr["shipcode"] = $arr[item][0][shipcode];
      }

      if($mid){
         $arr["name"] = $mid;
      }

      // 1. template mapping code 체크
      if($aligo_template_code){
         $kakao_aligo_template = $kakao_aligo->alimtalk_aligo_get_template($aligo_template_code);

         // 2. aligo template load 체크
         if($kakao_aligo_template['code'] == 0 && empty($kakao_aligo_template['list']) == false ){
            // 3. aligo 전송
            $kakao_aligo_send = $kakao_aligo->auto_alimtalk_aligo($from,$kakao_aligo_template['list'][0]['templtCode'],$arr);

            if($kakao_aligo_send['result'] == "success"){
               $kakao_log_data["msg"] = $kakao_aligo_send["msg"];
               $kakao_log_data["sender_result"] = "send success : ".$kakao_aligo_send["return_msg"];
            }else{
               $kakao_log_data["msg"] = $kakao_aligo_send["msg"];
               $kakao_log_data["sender_result"] = "send fail : ".$kakao_aligo_send["return_msg"];
            }

            $kakao_log_data["cat_number"] = $kakao_aligo_template['list'][0]['templtCode'];
            $kakao_log_data["catnm"] = $kakao_aligo_template['list'][0]['templtName'];

         }else{
            $kakao_log_data["sender_result"] = "fail : Aligo Template load error > code : ".$aligo_template_code;
         }
      }else{
         $kakao_log_data["sender_result"] = "fail : NO mapping code > $code ";
      }

      // kakao_alimtalk log insert
      $now_date = date("Y-m-d H:i:s");

      $kakao_log = "insert into exm_log_kakao set
      cid = '$kakao_log_data[cid]',
      mid = '$kakao_log_data[mid]',
      number = '$kakao_log_data[number]',
      msg = '$kakao_log_data[msg]',
      cat_number = '$kakao_log_data[cat_number]',
      catnm = '$kakao_log_data[catnm]',
      regdt = '$now_date',
      sender_result = '$kakao_log_data[sender_result]'
      ";

      $db->query($kakao_log);
   }

?>
