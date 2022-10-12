<?

/*
* @date : 20190122
* @author : kdk
* @brief : 시안요청 조회,등록,수정 (cart.php,order.php,payment.php).
* @request : 태웅
* @desc : function getDesign(),setDesignInsert,setDesignUpdate()
* @todo :
*/

/*
* @date : 20180411
* @author : kdk
* @brief : 장바구니 유실 오류 발생 확인.
* @request : 유니큐브
* @desc : function del($cartno)
* @todo : if($cartno) {} 추가함.
*/

/*
* @date : 20180321
* @author : kjm
* @brief : T멤버십 할인 추가
* @request : 유니큐브
* @desc :
* @todo :
*/

/*
* @date : 20180321
* @author : minks
* @brief : 상품에 다중 카테고리가 설정됐을 경우 사용가능한 쿠폰 정보를 못가져오는 현상 수정
* @request :
* @desc :
* @todo :
*/

/*
* @date : 20180314
* @author : kdk
* @brief : 3055편집기일때 커버옵션정보를 가져온다.
* @request :
* @desc :
* @todo :
*/

/*
* @date : 20171220 (20180108)
* @author : kdk
* @brief : 패키지상품 관련. 일반상품 보관함코드 생성 및 장바구니 업데이트. CreateStorageId() 함수 추가.
* @request :
* @desc : class.pods.php / class.pods.api.php 보관함코드 생성 관련 연동 추가됨.
* @todo : 충분한 테스트를 진행해야 하며 결제 모듈의 이상유무 면밀히 검토해야함.
*/

class cart {

   var $db;
   var $cid;
   var $sess;
   var $item;
   var $r_error;
   var $shipprice;
   var $acc_shipprice;
   var $oshipprice;
   var $totshipprice;
	var $shipping_extra_price = 0;				//추가 배송비			20171229		chunter
   var $itemprice;
   var $grpdc;
   var $dc;
   var $dc_coupon;
   var $dc_sale_code_coupon;
   var $totea;
   var $totreserve;
   var $client;
   var $coupon;
   var $addid = array();
   var $addid_direct;
   var $error_goods;
   var $self_shipprice_flag = false;
   var $totemoney;
   var $dc_partnership;    //제휴할인신청금액

   var $order_shiptype;       //rid 별 주문 배송방법 저장하기    20141201  chunter

   var $editlistCardNo = array();
   //편집보관함에서 넘어온 편집정보를 장바구니 테이블에서 찾은 정보 저장     20140404    chunter

   var $bGetPodsStorageInfoData;     //pods 연동 데이타 처리할 것인지. 단순 가격정보 가져올경우 연동이 필요없다.      20150709    chunter

   var $podsApi;   //pods API Class			20160804		chunter

   var $cfg_dc_price;     //  20190703    jtkim

   function cart($cartno = array(), $coupon = array(), $bPodsStorageInfo = true, $dc_partnership=""){
      $text = '@cartno[]=';
      $text1 = '|cartno[]=';

      //비회원 주문시 get방식으로 넘어오는 cartno값을 분리 / 14.05.09 / kjm
		//로그인 하지않고 상품 편집 후 장바구니에서 주문할 때 로그인 하는 방식

		if (strpos($_REQUEST[cartno][0], $text)) {
         $cartno = $_REQUEST[cartno];
         $cartno = explode("@cartno[]=", $cartno[0]);
      }
      //로그인 하지않고 상품 편집 후 장바구니에서 주문할 때 비회원주문으로 주문하는 방식
		elseif (strpos($_REQUEST[cartno][0], $text1)) {
         $cartno = $_REQUEST[cartno];
         $cartno = explode("|cartno[]=", $cartno[0]);
      }

      $this -> db = $GLOBALS[db];
		$this -> cid = $GLOBALS[cid];
		$this -> sess = $GLOBALS[sess];
		$this -> item = array();
		$this -> r_error = array(1 => _("상품 삭제"), 2 => _("샵의 판매중지"), 3 => _("품절 및 판매중지"), 4 => _("삭제된 상품 옵션"), 5 => _("상품 옵션 판매중지"), 6 => _("재고부족"), 7 => _("편집미완료"), 8 => _("편집조회실패"), 9 => _("판매가 없음"), 10 => _("구매가능한 그룹이 아님"), 11 => _("주문완료"), );
		$this -> coupon = $coupon;

      $this->dc_partnership = $dc_partnership;

		$this -> bGetPodsStorageInfoData = $bPodsStorageInfo;
		$this -> podsApi = new PODStation('20');

		$this -> get_items($cartno, $packageFlag);
   }

   #상품정보 가져오기
   function get_items($cartno = array()) {
      //debug($cartno);
      global $cfg, $cfg_center, $r_podskind20, $soap_port, $r_cover_type;

//      $cfg_emoney = getCfg("", "emoney");

		$goodsList = array();
		$goodsCidList = array();
		$goodsBidList = array();
		$releaseList = array();
		$goodsOptList = array();

		$m_cart = new M_cart();
		$m_goods = new M_goods();
		$m_etc = new M_etc();

      $this_time = get_time();
      ### 최종수정일 2주후 삭제
      if (!$cfg[source_save_days])
         $cfg[source_save_days] = 15;
      //debug($cfg[source_save_days]);

      //$this -> db -> query("delete from exm_cart where cid = '$this->cid' and date_format(updatedt,'%Y-%m-%d') <= adddate(curdate(), interval -$cfg[source_save_days] day)");

      $m_cart->oldDataDelete($this->cid, $cfg[source_save_days]);

      if (is_array($cartno))
         $cartno = implode(",", $cartno);
	  $debug_data .= debug_time($this_time);
      $addwhere = ($this -> sess[mid]) ? "and mid = '{$this->sess[mid]}'" : "and mid = '' and cartkey = '$_COOKIE[cartkey]'";
      if (!$this -> sess[mid] && !$_COOKIE[cartkey]) {
         $addwhere = "and 0 = 1";
      }

      //패키지 정보 구분하여 가져오기. / 18.01.02 / kdk
      $addwhere .= " and package_flag != '1'";
      //debug($addwhere);

      /*
      if ($cartno)
         $addwhere2 = "and cartno in ($cartno)";

      #복수 편집기 처리 pods_use, podskind, exm_cart 테이블에서 조회 없으면 exm_goods 테이블 사용 2016.03.16 by kdk
      $query = "
      select storageid,
         case
         when a.podskind = '' then b.podskind
         when a.podskind is null then b.podskind
         else a.podskind
         end as 'podskind',
         case
         when a.pods_use = '' then b.pods_use
         when a.pods_use is null then b.pods_use
         else a.pods_use
         end as 'pods_use'
         #b.podskind,b.pods_use
      from
         exm_cart a
         left join exm_goods b on a.goodsno = b.goodsno
      where
         cid = '$this->cid'
         $addwhere
         $addwhere2

      order by cartno desc
      ";
      //debug($query);
      $res = $this -> db -> query($query);
      while ($data = $this -> db -> fetch($res)) {
      */

      $cartList = $m_cart->getCartListWithPODKind($this->cid, $cartno, $addwhere);

		foreach ($cartList as $key => $data) {
         //debug($data);
			if (in_array($data[c_podskind], $r_podskind20) || $data[c_pods_use] == "3") {/* 2.0 상품 */
            $r_storageid20[] = trim($data[storageid]);
			} else {
			   $r_storageid[] = trim($data[storageid]);
			}
      }
      //debug($r_storageid);
      //debug($r_storageid20);
		$debug_data .= debug_time($this_time);
      if ($r_storageid20) {
         $this->podsApi->setVersion('20');				//pods2 버젼으로 변경
    		foreach ($r_storageid20 as $k => $v) {
            /*
            $ret = readUrlWithcurl('http://'.PODS20_DOMAIN.'/CommonRef/StationWebService/GetMultiOrderInfoResult.aspx?storageids=' . $v, false);
            list($flag, $ret, $imgData) = explode("|^|", $ret);
            $imgData = explode(";", $imgData);
            $pageData= $imgData[5];

            $ret = "";
            $ret[DATA] = str_replace("DATA=", "", $pageData);

            $pod_ret[$v] = $ret;
            */

            //$pod_ret[$v] = $this->podsApi->GetMultiOrderInfoResultPageData($v);
            $pod_ret[$v] = $this->podsApi->GetMultiOrderInfoResultAllData($v);
         }
      }
$debug_data .= debug_time($this_time, "1");
      //debug($pod_ret);

      // 그룹 할인 적용시 cid 조건 추가   191213   jtkim
      if ($GLOBALS[sess][grpno]) {
         list($this -> grpdc) = $this -> db -> fetch("select grpdc from exm_member_grp where grpno = '{$GLOBALS[sess][grpno]}' and cid = '{$GLOBALS[cid]}' ", 1);
      }
      // echo $this->grpdc;
      /*
      $query = "
      select *
      from
          exm_cart
      where
          cid = '$this->cid'
          $addwhere
          $addwhere2
      order by cartno desc
      ";

      $res = $this -> db -> query($query);
      while ($data = $this -> db -> fetch($res))
      */

      foreach ($cartList as $key => $data) {
         //debug($data);
         if ($data[goodsno] == "-1") {
            //구 자동견적 상품 . 사용하지 않음			20160804		chunter
         } else {
        	   //기존 조회한 상품을 다시 조회 하지 않기위해 list 를 만들어 저장하고 그곳에서 조회한다.			20160804		chunter
            if (! array_key_exists($data[goodsno],$goodsList))
        		  $goodsList[$data[goodsno]] = $m_goods->getInfo($data[goodsno]);

        	   $tmp = $goodsList[$data[goodsno]];
            //debug($tmp);

            if (!$tmp[goodsno]) {
               $data[error] = 1;
               //$this -> db -> query("delete from exm_cart where cid = '$this->cid' and cartno = '$data[cartno]'");
				  $m_cart->delete($this->cid, $data[cartno]);
				  unset($data);
				  continue;
            }

			   $data[supply_goods] = $tmp[sprice];
			   $data[cost_goods] = $tmp[oprice];
			   $data[oprice] = $tmp[oprice];
			   //$data[pods_use] = $tmp[pods_use];

			   #복수 편집기 처리 pods_use, podskind, podsno exm_cart 테이블에서 조회한 값이 있으면 exm_goods 값 무시함. 2016.03.16 by kdk
			   if(is_null($data[pods_use]) || $data[pods_use] == '') {
			      $data[pods_use] = $tmp[pods_use];
			   }
			   if(is_null($data[podskind]) || $data[podskind] == '') {
   			   $data[podskind] = $tmp[podskind];
			   }
			   if(is_null($data[podsno]) || $data[podsno] == '') {
			      $data[podsno] = $tmp[podsno];
			   }

   			//20150811 / minks / 모바일일 경우 편집기 실행을 위해 siteid, p_siteid를 조회
   			if (!$cfg[podsiteid]) $cfg[podsiteid] = $cfg_center[podsiteid];
   			$podsiteid = $cfg[podsiteid];

   			if ($tmp[pods_useid]!="center" && $tmp[podsno]) {
   				if (! $self_podsiteid)
   					list($self_podsiteid) = $this->db->fetch("select self_podsiteid from exm_mall where cid = '$this->cid'",1);
   				if ($self_podsiteid) $podsiteid = $self_podsiteid;
   			}

   			$data[siteid] = $podsiteid;
   			$data[p_siteid] = $cfg_center[podsiteid];
   			if ($self_podsiteid) $data[p_siteid] = $podsiteid;

            if ($tmp) $data = array_merge($tmp, $data);

            if ($tmp[state] > 0 || ($tmp[usestock] && $tmp[totstock] < 1))
               $data[error] = 3;

			$data[title] = str_replace("\"", "&quot;", stripslashes($data[title]));

			$categoryArr = $m_goods->getGoodsCategoryInfo($this->cid, $data[goodsno]);
			foreach ($categoryArr as $cat_k=>$cat_v) {
				switch (strlen($cat_v[catno])) {
					case "3":
						$data[catno1] = $cat_v[catno];
						$data[catnm1] = $cat_v[catnm];
						break;
					case "6":
						$data[catnm2] = $cat_v[catnm];
						break;
					case "9":
						$data[catnm3] = $cat_v[catnm];
						break;
					case "12":
						$data[catnm4] = $cat_v[catnm];
						break;
				}
			}

            //debug("###################");
            //debug($data);

            # 상품정보 exm_goods_cid 데이터 수집 (판매가 및 샵관련 상품정보)
   		   //$query = "select mall_pageprice,mall_pagereserve,strprice,goodsno,price,reserve,self_deliv,self_dtype,self_dprice,b2b_goodsno from exm_goods_cid where cid = '$this->cid' and goodsno = '$data[goodsno]'";
            //$tmp = $this -> db -> fetch($query);
   			//DB 조회를 최소화 하기 위해 list 에 보관			20160804		chunter
   			if (! array_key_exists($data[goodsno],$goodsCidList))
               $goodsCidList[$data[goodsno]] = $m_goods->getGoodsCidInfo($this->cid, $data[goodsno]);
            $tmp = $goodsCidList[$data[goodsno]];

            if ($tmp[strprice])
               $data[error] = 3;
   		   if (!is_numeric($tmp[price]))
   				unset($tmp[price]);
   		   else
   				$data[price] = $tmp[price];
   		   if (!$tmp[goodsno] && !$data[error])
   				$data[error] = 2;
   		   if ($tmp)
               $data = array_merge($tmp, $data);

            //$query = "select goodsno from exm_goods_bid where cid = '$this->cid' and goodsno = '$data[goodsno]' limit 1";
            //list($bid_chk) = $this -> db -> fetch($query, 1);
            //DB 조회를 최소화 하기 위해 list 에 보관			20160804		chunter

            if (! array_key_exists($data[goodsno],$goodsBidList))
            $goodsBidList[$data[goodsno]] = $m_goods->getGoodsBidInfo($this->cid, "", $data[goodsno]);
            $bid_chk = $goodsBidList[$data[goodsno]];

            if ($this -> sess[bid]) {
               if ($bid_chk) {
                  //$query = "select goodsno from exm_goods_bid where cid = '$this->cid' and bid = '{$this->sess[bid]}' and goodsno = '$data[goodsno]' limit 1";
   					//list($bid_chk2) = $this -> db -> fetch($query, 1);
   					$bid_chk2 = $m_goods->getGoodsBidInfo($this->cid, $this->sess[bid], $data[goodsno]);
   					if (!$bid_chk2 && !$data[error]) {
                     $data[error] = 10;
   					}
   				}
   				$data[price] = get_business_goods_price($data[goodsno], $data[price]);
   				$data[reserve] = get_business_goods_reserve($data[goodsno], $data[reserve]);
            } else {
               if ($bid_chk && !$data[error]) {
            	  $data[error] = 10;
             	}
            }

   			$data[enabled_ea] = $data[totstock];

   			if ($data[usestock] && $data[totstock] < 1) {
   				$data[error] = 3;
   			} else if ($data[usestock] && $data[totstock] < $data[ea]) {
   				$data[error] = 6;
   			}

   			$data[ea_mod_enabled] = true;

   			// 장바구니에서 pods 상품인데 storageid가 없는경우 편집 미완료 error 210714 jtkim
   			if($data['pods_use'] > 0){
   			    if(!$data[storageid]){
   			    	$data[error] = "7";
			        $add_errmsg = "0%";
		        }
		    }

   			if ($data[storageid])
            {
               $data[ea_mod_enabled] = false;
   				# 편집상태 조회
   				$ret = $pod_ret[$data[storageid]];
   				# 수량수정가능여부 체크
   				if (!in_array($data[podskind], array(1, 12, 7, 27, 28, 24, 35, 1, 2, 3010, 3180, 3041, 3020))) {
   					$data[ea_mod_enabled] = true;
   				}

               //멀티펜시편집기일 때 페이지 수량이 1일 때만 장바구니 수량 변경이 가능하도록 설정
   				if (($data[podskind] == "24" || $data[podskind] == "3041" || $data[podskind] == "3043") && $ret['PAGE'] == "1") {
   					$data[ea_mod_enabled] = false;
   				}

   				if ($data[pods_use] == "3") {
   					switch ($ret) {
   					  case "10" :
   						case "20" :
   							$data[error] = "7";
   					    break;
   						case "40" :
   						case "60" :
   						case "70" :
   						case "90" :
   							$data[error] = "11";
   					    break;
   					}

                    //편집상태확인.미오디오 피규어 편집기 해당 정보를 임의로 생성한다.(미오디오 피규어 편집기는 pods와 연동 안함.) / 20181001 / kdk
                    if ($data[podskind] == 99999) {
                        $ext_json_data = json_decode($data[ext_json_data],1);

                        $ret[STORAGE_ID] = $data[storageid];
                        $ret[STATE] = "30";
                        $ret[COUNT] = "1";
                        $ret[PAGE] = "1";
                        $ret[ID] = $data[storageid];

                        $ret[PROGRESS] = $ext_json_data[mask_count];

                        $r_progress = explode("/", $ext_json_data[mask_count]);

                        if ($r_progress[0] != $r_progress[1]) {
                            if ($r_progress[0] == 0) {
                                $data[add_errmsg] = "0%";
                                $data[edit_progress] = "0%";
                            } else {
                                $data[add_errmsg] = round($r_progress[0] / $r_progress[1] * 100) . "%";
                                $data[edit_progress] = round($r_progress[0] / $r_progress[1] * 100) . "%";
                            }
                            $data[error] = "7";
                        }

                    }

                } else if (in_array($data[podskind], array(1, 2, 3010, 3011, 3020))) {
   					$ret[DATA] = str_replace("[", "", $ret[DATA]);
   					$ret[DATA] = str_replace("]", "", $ret[DATA]);
   					$ret[DATA] = explode("|", $ret[DATA]);

   					foreach ($ret[DATA] as $v) {
   						unset($printopt);
   						$v2 = $this -> _ilark_vars($v, ",");
   						$printopt[printoptnm] = $v2[size];
   						$printopt[ea] = $v2[count];
   						$data[r_print_opt][] = $printopt;
   					}
   					$data[printopt] = serialize($data[r_print_opt]);
   					//$this -> db -> query("update exm_cart set printopt = '$data[printopt]' where cartno = '$data[cartno]'");
   					$m_cart->updateCartOneField("printopt", $data[printopt], $data[cartno]);

               } else if (in_array($data[podskind], array(12, 24))) {
   					$data[ea] = $ret[TOTALCOUNT];
   					//$this -> db -> query("update exm_cart set ea = '$data[ea]' where cartno = '$data[cartno]'");
   					$m_cart->updateCartOneField("ea", $data[ea], $data[cartno]);

   					# 포토북의 제작 상태 가져오기
   					// 해당 소스를 경선씨가 삭제후 아래에서 처리하도록 했으나 도균씨가 pods20 에 대해서 처리하도록 변경하면서 해당 코드가 누락되어 버그 발생.. 해당 소스 원복함 20140220  chunter
   					$ret[PROGRESS] = explode("/", $ret[PROGRESS]);

   					if ($ret[PROGRESS][0] != $ret[PROGRESS][1]) {
   						$add_errmsg = $ret[PROGRESS][0] . " / " . $ret[PROGRESS][1];
   						$data[error] = 7;
   						//$this -> db -> query("update exm_edit set state = 0 where storageid = '$data[storageid]'");
   						$m_cart->updateEditOneField("state", "0", $data[storageid]);
   					}
   				} else if (in_array($data[podskind], array(28, 3180))) {

                  /*
   					$query = "select optno from exm_goods_opt where goodsno = '$data[goodsno]' and opt1 = '$ret[TOTALCOUNT]'";
   					list($data[optno]) = $this -> db -> fetch($query, 1);
   					$query = "select cartno from exm_cart where goodsno = '$data[goodsno]' and optno = '$data[optno]' and storageid='$data[storageid]'";
   					list($chk_duplicate) = $this -> db -> fetch($query, 1);
   					if ($chk_duplicate != $data[cartno]) {
   						//$this -> db -> query("delete from exm_cart where cartno = '$chk_duplicate'");
   						$m_cart->delete($this->cid, $chk_duplicate);
   					}
   					*/
                  $m_cart->checkDuplicateCartNoAndDelete($data[goodsno], $ret[TOTALCOUNT], $data[storageid], $data[cartno]);

   					//$this -> db -> query("update exm_cart set optno = '$data[optno]' where cartno = '$data[cartno]'");
   					$m_cart->updateCartOneField("optno", $data[optno], $data[cartno]);
               } else if ($data[podskind] == 7) {
                  if ($ret[STATE] != 30) {
                     $data[error] = "7";
   					}
   			   } else if ($data[podskind] == 35) {
   				  $data[ea] = $ret[BOOK];
               } else {
                  # 추가페이지 계산

   					if ($ret[DATA]) {
                     $ret[DATA] = str_replace("[", "", $ret[DATA]);
                     $ret[DATA] = str_replace("]", "", $ret[DATA]);
   					   $ret[DATA] = explode(",", $ret[DATA]);

                     //변수를 초기화 하지 않아 추가 페이지와 상관없는 상품들이 추가 페이지 다음에 있으면 추가 페이지가 계산되어 초기화 해준다 / 17.02.09 / kjm
                     $pagecount = "";
                     $basecount = "";
					 $totalcount = "";

   						if (is_array($ret[DATA])) {
	                        foreach ($ret[DATA] as $v) {
	                           $v = explode("=", $v);

	   								if ($v[0] == "pagecount")
	   									$pagecount = $v[1];
	   								if ($v[0] == "editorbase")
	   									$basecount = $v[1];
	   								if ($v[0] == "inc")
	   									$inc = $v[1];
	   								if ($v[0] == "per")
	   									$per = $v[1];
	   								if ($v[0] == "totalcount")
										$totalcount = $v[1];
	   					   	}
   						}

   						if (is_numeric($pagecount) && is_numeric($basecount)) {
   							$data[pagecount] = $pagecount;
   							$data[addpage] = ($pagecount - $basecount);
						}

						if (is_numeric($totalcount)) {
							$data[totalcount] = $totalcount;
						}

   						if ($data[addpage]) {
   							if ($data[mall_pageprice])
   								$data[pageprice] = $data[mall_pageprice];
   							$data[addpage_sprice] = $data[spageprice] * ($data[addpage] / $inc);
   							$data[addpage_oprice] = $data[opageprice] * ($data[addpage] / $inc);
   							$data[addpage_price] = get_business_goods_addpage_price($data[goodsno], $data[pageprice]) * ($data[addpage] / $inc);
   							$data[addpage_reserve] = get_business_goods_addpage_reserve($data[goodsno], $data[mall_pagereserve]) * ($data[addpage] / $inc);
   						}
                  }

                  # 포토북의 제작 상태 가져오기
                  //최초 활성화된 코드였으나 1월경 주석처리로 변경되었던 코드를 다시 원복함. 주석처리한 이유는 찾지 못했음   20140625  chunter
			         $ret[PROGRESS] = explode("/", $ret[PROGRESS]);

                  if (is_array($ret[PROGRESS])) {
						   if ($ret[PROGRESS][0] != $ret[PROGRESS][1]) {
                        //$add_errmsg = $ret[PROGRESS][0]." / ".$ret[PROGRESS][1];
                        if ($ret[PROGRESS][0] == 0) {
                           $data[add_errmsg] = "0%";
						         $data[edit_progress] = "0%";
						      } else {
				    	         $data[add_errmsg] = round($ret[PROGRESS][0] / $ret[PROGRESS][1] * 100) . "%";
								   $data[edit_progress] = round($ret[PROGRESS][0] / $ret[PROGRESS][1] * 100) . "%";
							   }
							   //debug($add_errmsg);
							   $data[error] = "7";
							   //$this -> db -> query("update exm_edit set state = 0 where storageid = '$data[storageid]'");
							   $m_cart->updateEditOneField("state", "0", $data[storageid]);
                     }
                  }

                  # 편집 상태 가져오기 20140122 by kdk
                  if (in_array($data[podskind], array(3030, 3040, 3041, 3042, 3043, 3050, 3051, 3052, 3060, 3110, 3112, 3053, 3054, 3055))) {
                     if ($this->bGetPodsStorageInfoData) {
                        /*
                        $ret = readUrlWithcurl('http://'.PODS20_DOMAIN.'/CommonRef/StationWebService/GetMultiOrderInfoResult.aspx?storageids=' . $data[storageid], false);
                        list($flag) = explode("|", substr($ret, 0, 8));

                        $ret2[GetMultiOrderInfoResultResult] = substr($ret, strpos($ret, "STORAGE_ID"));
   					      $ret2[GetMultiOrderInfoResultResult] = explode("|^|", $ret2[GetMultiOrderInfoResultResult]);

   					      if ($ret2[GetMultiOrderInfoResultResult])
   					      foreach ($ret2[GetMultiOrderInfoResultResult] as $v) {
   					      $v = $this -> _ilark_vars(substr($v, 8));
   					      $pod_ret2[$v[ID]] = $v;
   					      }
   						   */

   						   //위에서 PODS2.0 연동한 데이터가 있으면 연동한 값을 사용하고
   						   //없으면 새로 연동을 한다 / 17.01.18 / kjm
   						   if (in_array($data[storageid], $pod_ret)){
   						      $pod_ret2[$data[storageid]] = $pod_ret[$data[storageid]];
   						   } else {
      							$this->podsApi->setVersion('20');				//pods2 버젼으로 변경
      							$ret = $this->podsApi->GetMultiOrderInfoResultAllData($data[storageid]);
      							$pod_ret2[$ret[ID]] = $ret;
      						}

                        //debug($pod_ret2);
   						 	//debug($pod_ret2[$data[storageid]][STATE]);
   							switch ($pod_ret2[$data[storageid]][STATE]) {
   								case "10" :
   								case "20" :
   									$pod_prog[PROGRESS] = explode("/", $pod_ret2[$data[storageid]][PROGRESS]);
   									if (is_array($pod_prog[PROGRESS])) {
   	   								if ($pod_prog[PROGRESS][0] != $pod_prog[PROGRESS][1]) {
   											if ($pod_prog[PROGRESS][0] == 0) {
   												$add_errmsg = "0%";
   												$data[edit_progress] = "0%";
   											} else {
   												$add_errmsg = round($pod_prog[PROGRESS][0] / $pod_prog[PROGRESS][1] * 100) . "%";
   												$data[edit_progress] = round($pod_prog[PROGRESS][0] / $pod_prog[PROGRESS][1] * 100) . "%";
   											}
   										}
   									}

   									$data[error] = "7";
   									//$this -> db -> query("update exm_edit set state = 0 where storageid = '$data[storageid]'");
   									$m_cart->updateEditOneField("state", "0", $data[storageid]);
   	    						break;
   	 							case "40" :
   	 							case "60" :
   								case "70" :
   								case "90" :
                              $data[error] = "11";
   									$data[edit_progress] = "100%";
   									break;
                        }

                        //20160219 / minks / 4.0멀티팬시편집기 수정모드로 호출시 총 수량 변경
                        //수량이 1인 경우 장바구니에서 수량 변경이 가능하도록 수정 / 17.01.19 / kjm
                        //편집기 매수를 장바구니 수량으로 처리할지 안함(상품 가격 구성). / 20190214 / kdk
						// 편집기에서 수량이 1일때 수량 변경 안되는 문제발생 -> $pod_ret2[$data[storageid]][COUNT] != 1에서 아래처럼 변경 /20220209 standi
						 if ($pod_ret2[$data[storageid]][COUNT] < 1) { // standi 추가함
							 $pod_ret2[$data[storageid]][COUNT]==1;
						 }
      						if (in_array($data[podskind], array(3041,3043)) && $pod_ret2[$data[storageid]][COUNT] > 0) {
	                           $data[ea] = $pod_ret2[$data[storageid]][COUNT];
	                           //$this -> db -> query("update exm_cart set ea = '$data[ea]' where cartno = '$data[cartno]'");

	                           $m_cart->updateCartOneField("ea", $data[ea], $data[cartno]);
	                        }

							//pixstory에서 미리보기가 있을 경우 첫번째 이미지를 출력해달라고 요청
							//속도가 느려 제거함
							/*if ($cfg[skin_theme] == "P1") {
								$preview_ret = $this->podsApi->GetPreViewImg($data[storageid]);

								if (count($preview_ret) > 0) {
									$data[preview_img] = "<img src='$preview_ret[0]' style='display:none;' onerror='this.src=\"/data/noimg.png\"'/>";
								}
							}*/
                        //debug($add_errmsg);
      					}//$this->bGetPodsStorageInfoData end

      					//3055편집기일때 상품가격과, 추가 페이지 가격은 따로 가져온다.
      					if($data[podskind] == 3055){
                        //$ext_data = $this->db->fetch("select * from tb_editor_ext_data where storage_id = '$data[storageid]'");

                        //$ext_json_data = json_decode($ext_data[editor_return_json],1);

                        $ext_json_data = $m_goods->get_editor_ext_data($data[storageid]);

                        $cover_range_data = $this->db->fetch("select * from md_cover_range_option where cover_id = '$ext_json_data[cover_range_id]'");
                        //$cover_range_data = $m_goods->getCoverRangeDataWithCoverID($ext_json_data[cover_range_id]);
                        //debug($cover_range_data);

                        $data[cover_range_data] = $cover_range_data[cover_range]."/".$r_cover_type[$cover_range_data[cover_type]]."/".$cover_range_data[cover_paper_name]."/".$cover_range_data[cover_coating_name];
                        //debug($data[cover_range_data]);

                        $data[cover_id] = $cover_range_data[cover_id];
                        //debug($data);
      						//3055편집기일때 상품가격과, 추가 페이지 가격은 따로 가져온다.

      						//커버별로 설정된 상품가격
      						$data[price] = $cover_range_data[cover_goods_price];

      						//추가 페이지가 존자 할 때 계산한다.
      						if($ext_json_data[page_count] - $ext_json_data[page_base] > 0)
                           $data[addpage_price] = ($ext_json_data[page_count] - $ext_json_data[page_base]) * $cover_range_data[cover_page_addprice];
                     }
                  }
               }
            }

$debug_data .= debug_time($this_time, "2");
            //wpod 편집기 vdp 편집정보 읽어오기      20140422
			   if (in_array($data[podskind], array(1001, 1002)) && $data[vdp_edit_data]) {
               //{"@name":"최서영","@position":"과장","@phone":"","@cellphone":"010-0000-0000","@address":"서울 금천구 가산동"}
               $data[vdp_edit_data] = str_replace("\n", "", $data[vdp_edit_data]);
	   			$data[vdp_edit_data] = str_replace("\r", "", $data[vdp_edit_data]);
				   $vdp_edit_data_arr = json_decode($data[vdp_edit_data], true);
				   $data[vdp_edit_data_desc] = $vdp_edit_data_arr['@name'] . "," . $vdp_edit_data_arr['@position'] . "," . $vdp_edit_data_arr['@cellphone'];
				   //20140430 / minks / 장바구니에 주문자와 부서명 필요해서 추가함
				   $data[vdp_name] = $vdp_edit_data_arr['@name'];
				   $data[vdp_department] = $vdp_edit_data_arr['@department'];
            }

            //자동견적 주문인경우 처리.     20140128
			   //상품 그룹군이 인쇄견적, 스튜디오견적 일때만 타도록 수정 - 8900번 이슈 / 16.10.13 / kjm
			   if ($data[est_order_data] && ($data[goods_group_code] == 40 || $data[goods_group_code] == 30 || $data[goods_group_code] == 20 || $data[goods_group_code] == 60)) { //인터프로견적상품 추가 / 20180703 / kdk
				   $data[cost_goods] = $data[est_cost];
				   $data[supply_goods] = $data[est_supply];
				   if($data[est_price] > 0) $data[price] = $data[est_price]; //금액이 있을 경우만 변경 / 16.08.09 / kdk
				   $data[est_order_option_desc_str] = $data[est_order_option_desc];

				   //자동견적 수량 변경 불가
				   if($data[goods_group_code] == 30 || $data[goods_group_code] == 20) {
				   	$data[ea_mod_enabled] = false;
				   }
            }

$debug_data .= debug_time($this_time);
            //견적 옵션 문자열 정리. 2015.07.03 by kdk
		      if ($data[est_order_option_desc_str])
					$data[est_order_option_desc_str] = $this->OptionDesc($data[est_order_option_desc_str]);

              if (strpos($data[extra_option], "100114") !== false ) {
                  $data[est_order_option_desc_str] = $data[est_order_option_desc];
              }

                if($data[goods_group_code] == 60) //인터프로 견적일경우.
                {
                    //$data[est_order_option_desc_str] = optionDescStr($data[est_order_option_desc]);
                }

            if ($data[optno]) {
               # 상품옵션정보 exm_goods_opt 데이터 수집 (옵션,옵션가 및 상품정보)
					/*
					$query = "
					select
					    a.*,
					    if(b.aprice is null,a.aprice,b.aprice) aprice,
					    areserve,
					    asprice,
					    b.b2b_optno b2b_goodsno
					from
					    exm_goods_opt a
					    left join exm_goods_opt_price b on b.cid = '$this->cid' and a.goodsno = b.goodsno and a.optno = b.optno
					where
					    a.goodsno = '$data[goodsno]'
					    and a.optno = '$data[optno]'
					";

					$tmp = $this -> db -> fetch($query);
					 */
					//DB 조회를 최소화 하기 위해 list 에 보관			20160805		chunter
					$optListKey = $data[goodsno]."-".$data[optno];
					if (! array_key_exists($optListKey,$goodsOptList))
						$goodsOptList[$optListKey] = $m_goods->getGoodsPriceWithOpt($this->cid, $data[goodsno], $data[optno]);
					$tmp = $goodsOptList[$optListKey];

					$data[supply_opt] = $tmp[asprice];
					$data[cost_opt] = $tmp[aoprice];

					if (trim($tmp[b2b_goodsno]))
						$data[b2b_goodsno] = $tmp[b2b_goodsno];

					if ($data[usestock] && $tmp[stock] < 1) {
						$data[error] = 3;
					} else if ($data[usestock] && $tmp[stock] < $data[ea]) {
						$data[error] = 6;
					} else if (!$tmp[optno] && !$data[error]) {
						$data[error] = 4;
					} else if ($tmp[opt_view] && !$data[error]) {
						$data[error] = 5;
					}
					if (!$data[optnm1])
						$data[optnm1] = _("옵션1");
					if (!$data[optnm2])
						$data[optnm2] = _("옵션2");
					if ($tmp[opt1]) {
						$tmp[opt][] = $data[optnm1] . ":" . $tmp[opt1];
						$tmp[mobile_opt][] = $tmp[opt1];
					}
					if ($tmp[opt2]) {
						$tmp[opt][] = $data[optnm2] . ":" . $tmp[opt2];
						$tmp[mobile_opt][] = $tmp[opt2];
					}
					if (is_array($tmp[opt])) {
						$tmp[opt] = implode(" / ", $tmp[opt]);
						$tmp[mobile_opt] = implode(" / ", $tmp[mobile_opt]);
					}

					if (!$tmp[goodsno]) {
						$data[error] = 1;
						//$this -> db -> query("delete from exm_cart where cid = '$this->cid' and cartno = '$data[cartno]'");
						$m_cart->delete($this->cid, $data[cartno]);
						unset($data);
						continue;
					}
					if ($tmp[podsno])
						$data[podsno] = $tmp[podsno];

					if ($this -> sess[bid]) {
					  $tmp[aprice] = get_business_goods_opt_price($data[goodsno], $data[optno], $tmp[aprice]);
					  $tmp[areserve] = get_business_goods_opt_reserve($data[goodsno], $data[optno], $tmp[areserve]);
					}

					$data = array_merge($tmp, $data);
               $data[enabled_ea] = $tmp[stock];
            }
$debug_data .= debug_time($this_time);

            if ($data[printopt]) {
               $data[printopt] = unserialize($data[printopt]);
					foreach ($data[printopt] as $k => $v) {

                  /*
					  	$query = "
                     select
                        if(b.print_price is null,a.print_price,b.print_price) print_price,
							   print_reserve,
							   print_sprice,
							   print_oprice,
							   a.goodsno
                     from
                        exm_goods_printopt a
                        left join exm_goods_printopt_price b on b.cid = '$this->cid' and a.goodsno = b.goodsno and a.printoptnm = b.printoptnm
							where
                        a.goodsno = '$data[goodsno]'
                        and a.printoptnm = '$v[printoptnm]'
                  ";

						list($data[printopt][$k][print_price], $data[printopt][$k][print_reserve], $data[printopt][$k][supply_printopt], $data[printopt][$k][cost_printopt], $isok) = $this -> db -> fetch($query, 1);
                  */

                  $printOpt = $m_goods->getGoodsPrintOpt($this->cid, $data[goodsno], $v[printoptnm]);
                  $data[printopt][$k][print_price] = $printOpt[print_price];
	               $data[printopt][$k][print_reserve] = $printOpt[print_reserve];
	               $data[printopt][$k][supply_printopt] = $printOpt[print_sprice];
	               $data[printopt][$k][cost_printopt] = $printOpt[print_oprice];
	               $isok = $data[goodsno];

                  $data[supply_printopt] += $data[printopt][$k][supply_printopt] * $v[ea];
						$data[cost_printopt] += $data[printopt][$k][cost_printopt] * $v[ea];
                  if (!$isok)
						   $data[error] = 4;

						if ($this -> sess[bid]) {
                     $data[printopt][$k][print_price] = get_business_goods_printopt_price($data[goodsno], $v[printoptnm], $data[printopt][$k][print_price]);
							$data[printopt][$k][print_reserve] = get_business_goods_printopt_reserve($data[goodsno], $v[printoptnm], $data[printopt][$k][print_reserve]);
						}

						$data[print_aprice] += $v[ea] * $data[printopt][$k][print_price];
						$data[print_areserve] += $v[ea] * $data[printopt][$k][print_reserve];
               }
            }
$debug_data .= debug_time($this_time);

				$data[r_addoptno] = $data[addoptno];

				if ($data[addoptno]) {
               $data[addoptno] = explode(",", $data[addoptno]);
					foreach ($data[addoptno] as $k => $v) {
				    if (!$v)
				  		continue;
				    /*
				    $query = "
							select
							    a.*,
							    if(b.addopt_aprice is null,a.addopt_aprice,b.addopt_aprice) addopt_aprice,
							    addopt_areserve,
							    addopt_asprice,
							    addopt_aoprice,
							    c.*
							from
							    exm_goods_addopt a
							    left join exm_goods_addopt_price b on b.cid = '$this->cid' and a.addoptno = b.addoptno
							    inner join exm_goods_addopt_bundle c on a.goodsno = c.goodsno and a.addopt_bundle_no = c.addopt_bundle_no
							where
							    a.addoptno = '$v'
							";
						$tmp = $this -> db -> fetch($query);
						*/
						$tmp = $m_goods->getGoodsAddOpt($this->cid, $v);

						$data[supply_addopt] += $tmp[addopt_asprice];
						$data[cost_addopt] += $tmp[addopt_aoprice];

						if (!$tmp[goodsno])
							$data[error] = 4;
						if ($tmp[addopt_bundle_view])
							$data[error] = 5;
						if ($tmp[addopt_view])
							$data[error] = 5;

						if ($this -> sess[bid]) {
							$tmp[addopt_aprice] = get_business_goods_addopt_price($data[goodsno], $v, $tmp[addopt_aprice]);
							$tmp[addopt_areserve] = get_business_goods_addopt_reserve($data[goodsno], $v, $tmp[addopt_areserve]);
						}

						$data[addopt][] = $tmp;
						$data[addopt_aprice] += $tmp[addopt_aprice];
                  $data[addopt_areserve] += $tmp[addopt_areserve];
               }
            }
$debug_data .= debug_time($this_time);

            if ($data[error])
					$data[errmsg] = $this -> r_error[$data[error]] . " " . $add_errmsg;

				if ($data[error] > 0 && $data[error] < 3)
					$data[rid] = "";
         }

         //list($data[release], $release_cid) = $this -> db -> fetch("select nicknm, cid from exm_release where rid = '$data[rid]'", 1);
			//DB 조회를 최소화 하기 위해 list 에 보관			20160804		chunter
			if (! array_key_exists($data[rid],$releaseList))
            $releaseList[$data[rid]] = $m_etc->getReleaseInfo($data[rid]);
			$data[release] = $releaseList[$data[rid]][nicknm];
			$release_cid = $releaseList[$data[rid]][cid];

			$data[rid2] = $data[rid];
			if ($data[self_deliv]) {
			$data[rid] = "|self|";
				$data[release] = $GLOBALS[cfg][nameSite];
			}

			if($release_cid) {
				if ($data[rid] != "|self|") {
				//주문 배송 방법이 착불, 방문수령인 경우 배송이 0원 처리      20141201    chunter
   				if(($data[order_shiptype] == 4 || $data[order_shiptype] == 5 || $data[order_shiptype] == 9) && $cfg[order_shiptype]) {
      	   		//착불, 방문수령인데 개별배송인 경우 개별배송이라고 표시 해주고 배송비를 0원 처리한다 / 16.03.24 / kjm
			 			//debug($data[shiptype]);
			 			if ($data[shiptype] == 2) {
   			    		$data[rid] = $data[rid] . "_no:" . $data[cartno];
							$data[release] = $data[release] . "<div class='stxt'>["._("개별배송")."]</div>";
							$data[shipprice] = 0;
			   		}

			   		$this -> shipfree[$data[rid]] = $data[order_shiptype];
			   		$self_shipprice_flag = true;
					} else {
                  if ($data[shiptype] == 2) {
                     $data[rid] = $data[rid] . "_no:" . $data[cartno];
							//debug($data[rid]);
							$data[release] = $data[release] . "<div class='stxt'>["._("개별배송")."]</div>";
						} else if ($data[shiptype] == 0) {
							//list($data[shipprice], $data[oshipprice]) = $this -> db -> fetch("select shipprice,oshipprice from exm_release where rid = '$data[rid]'", 1);
							//DB 조회를 최소화 하기 위해 list 에 보관			20160804		chunter
							if (! array_key_exists($data[rid],$releaseList))
                        $releaseList[$data[rid]] = $m_etc->getReleaseInfo($data[rid]);

							$data[shipprice] = $releaseList[$data[rid]][shipprice];
							$data[oshipprice] = $releaseList[$data[rid]][oshipprice];

						} else if ($data[shiptype] == 1) {
							$this -> shipfree[$data[rid]] = 1;
						} else if ($data[shiptype] == 4) {      //착불배송    20140923  chunter
							$this -> shipfree[$data[rid]] = 4;
						}
					}
            } else {
               //주문 배송 방법이 착불, 방문수령인 경우 배송이 0원 처리      20141201    chunter
					if(($data[order_shiptype] == 4 || $data[order_shiptype] == 5 || $data[order_shiptype] == 9) && $cfg[order_shiptype]) {
                  //착불, 방문수령인데 개별배송인 경우 개별배송이라고 표시 해주고 배송비를 0원 처리한다 / 16.03.24 / kjm
						if ($data[shiptype] == 2) {
						   $data[rid] = $data[rid] . "_no:" . $data[cartno];
							//debug($data[rid]);
							$data[release] = $data[release] . "<div class='stxt'>["._("개별배송")."]</div>";
							$data[shipprice] = 0;
						}

						$this -> shipfree[$data[rid]] = $data[order_shiptype];
						$self_shipprice_flag = true;
               } else {
						if ($data[self_dtype] == 2) {
							$data[rid] = $data[rid] . "_no:" . $data[cartno];
                     $data[release] = $data[release] . "<div class='stxt'>["._("개별배송")."]</div>";
						   $data[shipprice] = $data[self_dprice];
						} else if ($data[self_dtype] == 0) {
                     $data[shipprice] = $GLOBALS[cfg][shipconfig][shipprice];
						} else if ($data[self_dtype] == 1) {
							$this -> shipfree[$data[rid]] = 1;
						} else if ($data[self_dtype] == 4) {    //착불배송    20140923  chunter
							$this -> shipfree[$data[rid]] = 4;
						}
					}
				}
         } else {
            if ($data[rid] != "|self|") {
				//주문 배송 방법이 착불, 방문수령인 경우 배송이 0원 처리      20141201    chunter

					if(($data[order_shiptype] == 4 || $data[order_shiptype] == 5 || $data[order_shiptype] == 9) && $cfg_center[order_shiptype]) {
						//착불, 방문수령인데 개별배송인 경우 개별배송이라고 표시 해주고 배송비를 0원 처리한다 / 16.03.24 / kjm
						//debug($data[shiptype]);
						if ($data[shiptype] == 2) {
							$data[rid] = $data[rid] . "_no:" . $data[cartno];
							$data[release] = $data[release] . "<div class='stxt'>["._("개별배송")."]</div>";
							$data[shipprice] = 0;
						}

						$this -> shipfree[$data[rid]] = $data[order_shiptype];
					} else {
						if ($data[shiptype] == 2) {
							$data[rid] = $data[rid] . "_no:" . $data[cartno];
							//debug($data[rid]);
							$data[release] = $data[release] . "<div class='stxt'>["._("개별배송")."]</div>";
						} else if ($data[shiptype] == 0) {
							//list($data[shipprice], $data[oshipprice]) = $this -> db -> fetch("select shipprice,oshipprice from exm_release where rid = '$data[rid]'", 1);
							//DB 조회를 최소화 하기 위해 list 에 보관			20160804		chunter
							if (! array_key_exists($data[rid],$releaseList))
								$releaseList[$data[rid]] = $m_etc->getReleaseInfo($data[rid]);

							$data[shipprice] = $releaseList[$data[rid]][shipprice];
							$data[oshipprice] = $releaseList[$data[rid]][oshipprice];

						} else if ($data[shiptype] == 1) {
							$this -> shipfree[$data[rid]] = 1;
						} else if ($data[shiptype] == 4) {      //착불배송    20140923  chunter
							$this -> shipfree[$data[rid]] = 4;
						}
					}
				} else {
               //주문 배송 방법이 착불, 방문수령인 경우 배송이 0원 처리      20141201    chunter
					if(($data[order_shiptype] == 4 || $data[order_shiptype] == 5 || $data[order_shiptype] == 9) && $cfg_center[order_shiptype]) {
						//착불, 방문수령인데 개별배송인 경우 개별배송이라고 표시 해주고 배송비를 0원 처리한다 / 16.03.24 / kjm
                  if ($data[shiptype] == 2) {
							$data[rid] = $data[rid] . "_no:" . $data[cartno];
							//debug($data[rid]);
							$data[release] = $data[release] . "<div class='stxt'>["._("개별배송")."]</div>";
							$data[shipprice] = 0;
                  }

                  $this -> shipfree[$data[rid]] = $data[order_shiptype];
               } else {
						if ($data[self_dtype] == 2) {
							$data[rid] = $data[rid] . "_no:" . $data[cartno];
							$data[release] = $data[release] . "<div class='stxt'>["._("개별배송")."]</div>";
							$data[shipprice] = $data[self_dprice];
	  				} else if ($data[self_dtype] == 0) {
  						$data[shipprice] = $GLOBALS[cfg][shipconfig][shipprice];
						} else if ($data[self_dtype] == 1) {
							$this -> shipfree[$data[rid]] = 1;
						} else if ($data[self_dtype] == 4) {    //착불배송    20140923  chunter
							$this -> shipfree[$data[rid]] = 4;
						}
					}
				}
         }
$debug_data .= debug_time($this_time);
			$this -> dc = 0;  //변수 초기화 / 20191220 / kkwon
			$price[$data[rid]] += ($data[price] + $data[aprice] + $data[addopt_aprice] + $data[print_aprice] + $data[addpage_price]) * $data[ea];
			if ($this -> grpdc) {
				$data[grpdc] = round(($data[price] + $data[aprice] + $data[addopt_aprice] + $data[print_aprice] + $data[addpage_price]) * $this -> grpdc / 100, -1);
				$this -> dc += $data[grpdc] * $data[ea];
			}
         $this->cfg_dc_price[$data[rid]] = $price[$data[rid]];
			$data[dc_coupon] = 0;

			if ($this -> coupon[$data[cartno]][discount]) {
				$data[dc_coupon] = $this -> chk_dc_coupon($data, $this -> coupon[$data[cartno]][discount],$this->coupon[$data[cartno]][amount][$this->coupon[$data[cartno]][discount]]);

				$this -> dc_coupon += $data[dc_coupon];
				$data[dc_couponsetno] = $this -> coupon[$data[cartno]][discount];
      }

      if ($this -> coupon[$data[cartno]][sale_code]) {
				$data[dc_coupon] = $this -> chk_dc_coupon($data, $this -> coupon[$data[cartno]][sale_code],$this->coupon[$data[cartno]][amount][$this->coupon[$data[cartno]][sale_code]]);

				$this -> dc_coupon += $data[dc_coupon];
				$data[dc_couponsetno] = $this -> coupon[$data[cartno]][sale_code];
			}

			$data[reserve_coupon] = 0;
			if ($this -> coupon[$data[cartno]][saving]) {
				$data[reserve_coupon] = $this -> chk_dc_coupon($data, $this -> coupon[$data[cartno]][saving]);
				$this -> reserve_coupon += $data[reserve_coupon];
				$data[reserve_couponsetno] = $this -> coupon[$data[cartno]][saving];
			}

			//결제 금액 계산시 수량에 오류가 있을수 있어 초기화 진행     20140410    chunter
			if ($data[ea] == "" || $data[ea] == 0)
				$data[ea] = 1;

			if($goods_each_price) {
				$data[price] = $goods_each_price;
				$data[saleprice] = $goods_each_price * $data[ea];
				$data[payprice] = $data[saleprice];
			} else {
				$data[saleprice] = ($data[price] + $data[aprice] + $data[addopt_aprice] + $data[print_aprice] + $data[addpage_price]) * $data[ea];
				$data[payprice] = $data[saleprice] - ($data[grpdc] * $data[ea]) - $data[dc_coupon];
			}

         //패키지 상품 가격 처리 (대표 상품 가격만 사용). / 18.01.02 / kdk
         if($data[package_flag] != "0" && $data[package_parent_cartno] != "0") {
            //debug($data[package_flag]);
            $data[price] = 0;
            $data[saleprice] = 0;
            $data[payprice] = 0;
         }

         $data[payprice] = $data[saleprice] - ($data[grpdc]*$data[ea]) - $data[dc_coupon];



         // 멤버십 할인 이전 후 포토큐브 조건 추가
         if ( $this->cid == "fotocube2019" ){
            if ($data[goodsno] == 5802){
               $this->max_dc_partnership += floor(($data[saleprice]-($data[grpdc]*$data[ea]))*0.1); //제휴할인최대적용가능금액
               $data[payprice] -= $this->dc_partnership;
            }
         }else{
            ### 제휴할인최대적용가능금액계산
            if ( $data[goodsno] == 4708 || $data[goodsno] == 5313  ){
               $this->max_dc_partnership += floor(($data[saleprice]-($data[grpdc]*$data[ea]))*0.1); //제휴할인최대적용가능금액
            }

            ### 제휴할인유효성체크 & 적용
            if (($data[goodsno] == 4708 || $data[goodsno] == 5313) && $this->dc_partnership){
               $data[payprice] -= $this->dc_partnership;
            }
         }

         if( !$this->max_dc_partnership ) $this->max_dc_partnership = 0;


         /*
         //아래 함수로 통합 / 18.09.14 / kjm
         //기본값이 G    //G : 상품 할인 판매가격, A : 실제 결제금액
         if ($cfg_emoney['emoney_send_type'] == "A")
            $proc_price = $data['payprice'];
         else
            $proc_price = $data['saleprice'];
         //debug($cfg);

         //$cfg['emoney_send_ratio'] = 1;

         $add_emoney = 0;
         if ($cfg_emoney['emoney_send_ratio'])
            $add_emoney += $proc_price * $cfg_emoney['emoney_send_ratio'] / 100;


         if($GLOBALS[sess][grpno]){
            $m_member = new M_member();

            $grp_emoney_ratio = $m_member->getGrpScInfo($GLOBALS[sess][grpno]);
            $add_emoney += $proc_price * $grp_emoney_ratio / 100;
         }

         //절사, 1:1자리, 10:10자리 기본값 0
         if ($cfg_emoney['emoney_send_type'] == "1")
            $add_emoney =  floor($add_emoney / 10) * 10;
         if ($data['emoney_send_type'] == "10")
            $add_emoney =  floor($add_emoney / 100) * 100;

         $data[emoney] = $add_emoney;

         $this->totemoney += $add_emoney;
         */
         //debug($data);


         //장바구니 절사된금액에 따라 적립금 계산 2019 6 3 jtkim
         $data['payprice'] = $this -> setCuttingMoney($data['payprice']);
         $data['saleprice'] = $this -> setCuttingMoney($data['saleprice']);

         //절사금액 나머지 계산시 배열로 리턴받아서 배열안의 값으로 변경
         if(is_array($data['payprice'])) {
            $data['payprice'] = $data['payprice']['res_num'];
         }

         if(is_array($data['saleprice'])) {
            $data['org_saleprice'] = $data['saleprice'];
            $data['saleprice'] = $data['saleprice']['res_num'];
         }
         $data['cut_display'] = $this->setCuttingMoneyText();

         //$data['ship_dc'] = $this->setShipDcCfg();

         $add_emoney = calcuOrderTotalEmonay($data['payprice'], $data['saleprice']);

         //적립금 정책 미사용시 적립금 0원으로 설정     19.12.17 jtkim
         if($cfg[emoney_use_flag] != "Y") $add_emoney = 0;

         $data[emoney] = $add_emoney;
         $this->totemoney += $add_emoney;
         /*
         //기존 개발 소스
         //백인미 팀장과 통화 후 포토큐브의 원래 소스로 변경 / 18.03.21 / kjm
         ### 제휴할인최대적용가능금액계산
         $this->max_dc_partnership += floor(($data[saleprice] - ($data[grpdc] * $data[ea]) - $data[dc_coupon])*0.1); //제휴할인최대적용가능금액

         ### 제휴할인유효성체크 & 적용
         if ($this->dc_partnership){
            $data[payprice] -= $this->dc_partnership;
         }
        */

			if (!$data[saleprice]) {
				//$data[error] = 9;
			}

			$data[totreserve] = ($data[reserve] + $data[areserve] + $data[addopt_areserve] + $data[print_areserve] + $data[addpage_reserve]) * $data[ea] + $data[reserve_coupon];

			$item[$data[rid]][] = $data;

			if ($data[error]) {
				$this -> error_goods = true;
			}

			$this -> shipprice[$data[rid]] = ($this -> shipfree[$data[rid]] == 1) ? 0 : $data[shipprice];
			$this -> oshipprice[$data[rid]] = $data[oshipprice];
			$this -> ordprice[$data[rid]] += ($data[price] + $data[aprice] + $data[addopt_aprice] + $data[print_aprice]) * $data[ea];

			$this -> totea += $data[ea];

//list($goods_each_price) = $this -> db->fetch("select goods_each_price from tb_member_goods_mapping where cid = '$data[cid]' and mid = '$data[mid]' and goodsno = '$data[goodsno]'",1);
			//$goods_each_price  -> pretty 스킨에서만 사용하는 가격.			20171017		chunter
			if($goods_each_price){
				$goods_each_price = $goods_each_price * $data[ea];
				$this -> itemprice += $goods_each_price;
			}
			else
				$this -> itemprice += $data[saleprice];

			$this -> totreserve += $data[totreserve];
		}
$debug_data .= debug_time($this_time);

      //프로모션 코드 할인 처리				20171019			chunter
      $this -> dc_sale_code_coupon = 0;
		if ($this -> coupon[sale_code_coupon]) {
			$promotion_result = calcuPromotionCodeSale($this -> coupon[sale_code_coupon], $cartno, $item);

         //리턴값이 -1이면 할당을 안한다 / 18.03.28 / kjm
         if($promotion_result[sale_code_price] != "-1"){
            $this -> dc_sale_code_coupon = $promotion_result[sale_code_price];
         }

         //debug($this -> dc_sale_code_coupon);
			//exit;
      }

         if ($this -> shipprice) {
            foreach ($this->shipprice as $k => $v) {
               if (strpos($k, "_no:"))
                  continue;

               if ($k == "|self|") {
                  $shiptype = $GLOBALS[cfg][shipconfig][shiptype];
                  $shipconditional = $GLOBALS[cfg][shipconfig][shipconditional];
               } else {
                  //list($shiptype, $shipconditional) = $this -> db -> fetch("select shiptype,shipconditional from exm_release where rid = '$k'", 1);
                  //DB 조회를 최소화 하기 위해 list 에 보관			20160804		chunter
                  if (! array_key_exists($k,$releaseList))
                     $releaseList[$k] = $m_etc->getReleaseInfo($k);


                  $shiptype = $releaseList[$k][shiptype];
                  $shipconditional = $releaseList[$k][shipconditional];
               }

               switch ($shiptype) {

                  case "1" :
                  case "4" :      //착불
                     $this -> shipprice[$k] = 0;
                     break;
                  case "3" :
                     if ($price[$k] >= $shipconditional) {
                        $this -> shipprice[$k] = 0;
                        $this -> shipfree[$k] = 1;      //총 주문 금액이 넘었으므로 무료 배송으로 변경한다.   20141202    chunter
                     }
                  break;
               }

               $this -> acc_shipprice[$k] = $this -> shipprice[$k];

               if ($cfg[self_deliv] == 1) {
                  $shiptype = $cfg[self_shiptype];
                  $shipconditional = $cfg[self_shipconditional];
                  //몰의 자체배송을 사용 하는데 상품의 배송 수단이 착불수단일 경우 배송비를 0원 처리한다 / 16.04.19 / kjm
                  if($self_shipprice_flag)
                     $this -> shipprice[$k] = 0;
                  else
                     $this -> shipprice[$k] = $cfg[self_shipprice];
                  switch ($shiptype) {

                     case "1" :
                     case "4" :      //착불
                     $this -> shipprice[$k] = 0;
                     break;
                     case "3" :
                        if ($price[$k] >= $shipconditional) {
                           $this -> shipprice[$k] = 0;
                           $this -> shipfree[$k] = 1;      //무료 배송으로 처리한다.   20141202    chunter
                        }
                        break;
                  }
               }
            }

            $this -> totshipprice = array_sum($this -> shipprice);
         }


		//추가 배송비 계산하기				20171229			chunter
		if ($this->coupon[receiver_zipcode])
		{
			$shipping_extra_result = calcuShippingExtraPrice($this->coupon[receiver_zipcode], $cartno);
			if ($shipping_extra_result[shipping_extra_price] > 0) {
				$this->shipping_extra_price = $shipping_extra_result[shipping_extra_price];
			}
		}
		$this->totshipprice += $this->shipping_extra_price;			//추가 배송비를 통합배송비에 더한다..		통합 배송비와 분리할지는 실제 서비스 적용시 정해야 한다.		20171229		chunter
      $this->totprice = $this->itemprice + $this->totshipprice;

      $this->item = $item;

$debug_data .= debug_time($this_time, true);
//echo $debug_data;
   }


   #########################################################
   #   장바구니 추가                                        #
   #   $data = array(                                      #
   #       'index' => array(goodsno,optno,ea,storageid);   #
   #   );                                                  #
   #########################################################
   function add($data) {
      foreach ($data as $k => $v) {
         if ($v[sessdata]) $this -> sess = $v[sessdata];

         $addflds = ($this -> sess[mid]) ? ",mid = '{$this->sess[mid]}'" : ",mid = '', cartkey = '$_COOKIE[cartkey]'";

         #복수 편집기 처리 pods_use, podskind, podsno pods_editor.js에서 form에 추가하여 넘기 2016.03.16 by kdk
         if($v[pods_use]) {
            $addflds_pods = ", pods_use = '$v[pods_use]', podsno = '$v[podsno]', podskind = '$v[podskind]'";
         }

         if (is_array($v[addopt])) {
            if ($v[addopt])
               $v[addopt] = implode(",", $v[addopt]);
         }

         if (!$v[ea])
            $v[ea] = 1;
         if ($v[title])
            $v[title] = str_replace("'", "''", $v[title]);

         if ($v[est_order_memo])
            $v[est_order_memo] = str_replace("'", "''", $v[est_order_memo]);

         if ($v[goodsno] == "-1") {
   	     //구 자동견적 처리내용. 현재 내용 없음			20160804		chunter
         } else {
            if ($v[storageid]) {
               list($chk) = $this -> db -> fetch("select cartno from exm_cart where cid = '$this->cid' and storageid = '$v[storageid]'", 1);
               if ($chk) {
                  $this -> editlistCardNo[] = $chk;
                  continue;
               }
            }

            //자동 견적 주문도 장바구니 담는다.       chunter
            $query = "
            insert into exm_cart set
               goodsno                 = '$v[goodsno]',
               optno                   = '$v[optno]',
               ea                      = '$v[ea]',
               storageid               = '$v[storageid]',
               cid                     = '$this->cid',
               addoptno                = '$v[addopt]',
               `title`                 = '$v[title]',
               est_order_data          = '$v[est_order_data]',
               est_order_option_desc   = '$v[est_order_option_desc]',
               est_order_type          = '$v[est_order_type]',
               est_cost                = '$v[est_cost]',
               est_supply              = '$v[est_supply_price]',
               est_price               = '$v[est_price]',
               est_goodsnm             = '$v[est_goodsnm]',
               est_order_memo          = '$v[est_order_memo]',
               vdp_edit_data           = '$v[vdp_edit_data]',
               catno                   = '$v[catno]',
               ext_json_data           = '$v[ext_json_data]',
               updatedt                = now()
               $addflds
               $addflds_pods
            on duplicate key update
               ea                      = '$v[ea]',
               `title`                 = '$v[title]',
               est_order_data          = '$v[est_order_data]',
               est_order_option_desc   = '$v[est_order_option_desc]',
               est_order_type          = '$v[est_order_type]',
               est_cost                = '$v[est_cost]',
               est_supply              = '$v[est_supply_price]',
               est_price               = '$v[est_price]',
               est_goodsnm             = '$v[est_goodsnm]',
               est_order_memo          = '$v[est_order_memo]',
               vdp_edit_data           = '$v[vdp_edit_data]',
               ext_json_data           = '$v[ext_json_data]',
               catno                   = '$v[catno]'
            ";
            //debug($query);
            //exit;
            $this -> db -> query($query);
            $dummy = $this -> db -> id;
         }
         if ($dummy) {
            $this -> addid[] = $dummy;
            $this -> addid_direct = $dummy;
         } else if (!$dummy) {
            $_addflds = ($this -> sess[mid]) ? "and mid = '{$this->sess[mid]}'" : "and cartkey = '$_COOKIE[cartkey]'";
            $query = "
            select cartno from exm_cart
            where
               goodsno         = '$v[goodsno]'
               and optno       = '$v[optno]'
               and addoptno    = '$v[addopt]'
               and cid         = '$this->cid'
               $_addflds
            ";
            list($dummy) = $this -> db -> fetch($query, 1);
            //$this->addid[] = $dummy;
            $this -> addid_direct = $dummy;
         }

         if ($v[storageid]) {
            if ($v[goodsno] == "-1") {
               //구 자동견적 처리내용. 현재 내용 없음			20160804		chunter
            } else {
               $addflds = ($this -> sess[mid]) ? ",mid = '{$this->sess[mid]}'" : ",mid = '', editkey = '$_COOKIE[cartkey]'";
               $query = "
               insert into exm_edit set
                  goodsno       = '$v[goodsno]',
                  optno         = '$v[optno]',
                  storageid     = '$v[storageid]',
                  cid           = '$this->cid',
                  addoptno      = '$v[addopt]',
                  `title`       = '$v[title]',
                  catno         = '$v[catno]',
                  ext_json_data = '$v[ext_json_data]',
                  updatedt      = now()
                  $addflds
                  $addflds_pods
               on duplicate key update
                  optno         = '$v[optno]',
                  `title`       = '$v[title]',
                  catno         = '$v[catno]',
                  ext_json_data = '$v[ext_json_data]'
               ";

               $this -> db -> query($query);
            }
         }
      }
      return true;
   }

   function del($cartno) {
      if($cartno) {
          $query = "delete from exm_cart where cartno = '$cartno'";
          $this -> db -> query($query);

          //패키지상품 관련 처리 / 2017.12.20 / kdk
          $query = "delete from exm_cart where package_parent_cartno = '$cartno'";
          $this -> db -> query($query);
      }
   }

   #########################################################
   #견적의뢰 추가
   #########################################################
   function add_extra($data) {

      //편집정보 없음 exm_edit 저장안함.
      foreach ($data as $k => $v) {
         $addflds = ($this -> sess[mid]) ? ",mid = '{$this->sess[mid]}'" : ",mid = '', cartkey = '$_COOKIE[cartkey]'";

         if (is_array($v[addopt])) {
            if ($v[addopt])
               $v[addopt] = implode(",", $v[addopt]);
         }
         if (!$v[ea])
            $v[ea] = 1;
         if ($v[title])
            $v[title] = str_replace("'", "''", $v[title]);

         if ($v[est_order_memo])
            $v[est_order_memo] = str_replace("'", "''", $v[est_order_memo]);

         if ($v[storageid]) {
            list($chk) = $this -> db -> fetch("select cartno from tb_extra_cart where cid = '$this->cid' and storageid = '$v[storageid]'", 1);
            if ($chk) {
               $this -> editlistCardNo[] = $chk;
               continue;
            }
         }

         $query = "
         insert into tb_extra_cart set
            goodsno               = '$v[goodsno]',
            optno                 = '$v[optno]',
            ea                    = '$v[ea]',
            storageid             = '$v[storageid]',
            cid                   = '$this->cid',
            addoptno              = '$v[addopt]',
            `title`               = '$v[title]',
            est_order_data        = '$v[est_order_data]',
            est_order_option_desc = '$v[est_order_option_desc]',
            est_order_type        = '$v[est_order_type]',
            est_cost              = '$v[est_cost]',
            est_supply            = '$v[est_supply_price]',
            est_price             = '$v[est_price]',
            est_order_memo        = '$v[est_order_memo]',
            order_name            = '$v[order_name]',
            order_cname           = '$v[order_cname]',
            order_phone           = '$v[order_phone]',
            order_mobile          = '$v[order_mobile]',
            order_email           = '$v[order_email]',
            order_sns             = '$v[order_sns]',
            updatedt              = now()
            $addflds
         on duplicate key update
            ea                    = '$v[ea]',
            `title`               = '$v[title]',
            est_order_data        = '$v[est_order_data]',
            est_order_option_desc = '$v[est_order_option_desc]',
            est_order_type        = '$v[est_order_type]',
            est_cost              = '$v[est_cost]',
            est_supply            = '$v[est_supply_price]',
            est_price             = '$v[est_price]',
            est_order_memo        = '$v[est_order_memo]',
            order_name            = '$v[order_name]',
            order_cname           = '$v[order_cname]',
            order_phone           = '$v[order_phone]',
            order_mobile          = '$v[order_mobile]',
            order_email           = '$v[order_email]',
            order_sns             = '$v[order_sns]'
         ";
         //echo $query;
         //exit;

         $this -> db -> query($query);
         $dummy = $this -> db -> id;

         if ($dummy) {
            $this -> addid[] = $dummy;
            $this -> addid_direct = $dummy;
         } else if (!$dummy) {
            $_addflds = ($this -> sess[mid]) ? "and mid = '{$this->sess[mid]}'" : "and cartkey = '$_COOKIE[cartkey]'";
            $query = "
            select cartno from tb_extra_cart
            where
               goodsno         = '$v[goodsno]'
               and optno       = '$v[optno]'
               and addoptno    = '$v[addopt]'
               and cid         = '$this->cid'
               $_addflds10
            ";
            list($dummy) = $this -> db -> fetch($query, 1);
            //$this->addid[] = $dummy;
            $this -> addid_direct = $dummy;
         }
      }
      return true;
   }

   #########################################################
   #견적의뢰 삭제
   #########################################################
   function del_extra($cartno) {

      //$query = "delete from tb_extra_cart where cartno = '$cartno'";
      $query = "update tb_extra_cart set order_status='c', est_cancel_datedt=now() where cartno = '$cartno'";
      $this -> db -> query($query);
   }

   #########################################################
   #견적의뢰 -> 장바구니 이동 완료 처리
   #########################################################
   function order_extra($cartno) {
      $query = "update tb_extra_cart set order_status='o' where cartno = '$cartno'";
      $this -> db -> query($query);
   }

   function mod($cartno, $ea) {
      global $db, $client, $soap_port;

      $query = "update exm_cart set ea = '$ea' where cartno = '$cartno'";
      //debug($query); exit;
      $this -> db -> query($query);

      //멀티팬시편집기 장바구니에서 주문 수량 변경 / 14.03.04 / kjm
      $data = $this -> db -> fetch("select a.storageid, a.ea, b.podskind from exm_cart a
                                    left join exm_goods b on a.goodsno = b.goodsno where a.cartno = '$cartno'");

      //편집기 종류가 멀티팬시편집기 일때 연동안 호출 / 14.03.05 / kjm
      if ($data[podskind] == 24) {
         /*
         //pods 연동안 주소
         $soap_url = "http://".PODS10_DOMAIN."/StationWebService/StationWebService.asmx?WSDL";

         //pods에 넘겨줄 데이터
         $pod_data[orderId] = $data[storageid];
         $pod_data[count] = $data[ea];

         include_once dirname(__FILE__) . "/../lib/nusoap/lib/nusoap.php";
         $client = new soapclient($soap_url, true);
         $client -> xml_encoding = "UTF-8";
         $client -> soap_defencoding = "UTF-8";
         $client -> decode_utf8 = false;
         $ret = $client -> call('SetOrderCountInfo', $pod_data);
         $ret = explode("|", $ret[SetOrderCountInfoResult]);
         */

         $this->podsApi->setVersion();
         $ret = $this->podsApi->SetOrderCountInfo($data[storageid], $data[ea]);
         $r_ret = array("success" => 1, "fail" => -1);
      }
      //debug($data[podskind]);
      //exit;
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

   //전적 옵션 수정하기..     20140205    chunter
   function extraOptionUpdate($v) {
      //자동 견적 주문도 장바구니 담는다.
      $query = "
      update exm_cart set
         est_order_data        = '$v[est_order_data]',
         est_order_option_desc = '$v[est_order_option_desc]',
         est_order_type        = '$v[est_order_type]',
         est_cost              = '$v[est_cost]',
         est_supply            = '$v[est_supply]',
         est_price             = '$v[est_price]',
         title                 = '$v[title]',
         est_order_memo       = '$v[est_order_memo]',
         updatedt  = now() where cartno = '$v[cartno]'";
         //echo $query;
         //exit;
         $this -> db -> query($query);
   }

   //미오디오 피규어 편집정보만 수정 처리. / 20181001 / kdk
   function extraJsonUpdate($v) {
      //자동 견적 주문도 장바구니 담는다.
      $query = "
      update exm_cart set
         ext_json_data = '$v[ext_json_data]',
         updatedt  = now() where storageid = '$v[storageid]'";
         //echo $query;
         //exit;
         $this -> db -> query($query);
   }

   function chk_dc_coupon($item, $couponsetno,$amount=0) {
      if (!$couponsetno)
         return 0;

      $query = "
      select
         a.*,b.coupon_setdt,b.no,
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
         inner join exm_coupon_set b on a.cid = b.cid and a.coupon_code = b.coupon_code and mid = '{$this->sess[mid]}'
      where
         a.cid = '$this->cid'
         and b.no = '$couponsetno'
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
      $data = $this -> db -> fetch($query);

      if (!$data)
         return 0;

      $coupon_ok = false;

      switch ($data[coupon_range]) {
         case "all" :
            $coupon_ok = true;

         break;

         case "category" :
            list($catno) = $GLOBALS[db] -> fetch("select group_concat(catno order by cat_index separator ',') from exm_goods_link where cid = '$this->cid' and goodsno = '$item[goodsno]'", 1);
            $data[coupon_catno] = explode(",", $data[coupon_catno]);
            $catno = ($catno) ? explode(",", $catno) : array();

            foreach ($data[coupon_catno] as $k2 => $v2) {
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

         case "goods" :
            $data[coupon_goodsno] = explode(",", $data[coupon_goodsno]);
            if (in_array($item[goodsno], $data[coupon_goodsno])) {
               $coupon_ok = true;
            } else {
               $coupon_ok = false;
            }
          break;
      }

      if (!$coupon_ok)
         return 0;

      $item[saleprice] = $item[price] + $item[aprice] + $item[addopt_aprice] + $item[print_aprice] + $item[addpage_price];
      $item[payprice] = $item[saleprice] - $item[grpdc];

      switch ($data[coupon_way]) {
         case "price" :
            if ($data[coupon_type] == "discount")
               $data[coupon_dc] = $data[coupon_price];
            else if ($data[coupon_type] == "sale_code")
               $data[coupon_dc] = $data[coupon_price];
            else if ($data[coupon_type] == "saving")
               $data[coupon_dc] = $data[coupon_price];
            else if ($data[coupon_type]=="coupon_money")
               $data[coupon_dc] = $amount;
            if ($item[payprice] * $item[ea] <= $data[coupon_dc])
               $data[coupon_dc] = $item[payprice] * $item[ea];

         break;

         case "rate" :
            if ($data[coupon_type] == "discount")
               $data[coupon_dc] = round($item[saleprice] * $item[ea] * $data[coupon_rate] / 100, -1);
            else if ($data[coupon_type] == "sale_code")
               $data[coupon_dc] = round($item[saleprice] * $item[ea] * $data[coupon_rate] / 100, -1);
            else if ($data[coupon_type] == "saving") {
               $data[coupon_dc] = round(($item[reserve] + $item[areserve] + $item[addopt_areserve] + $item[print_areserve] + $item[addpage_reserve]) * $item[ea] * $data[coupon_rate] / 100, -1);
            }

            if ($data[coupon_price_limit] && $data[coupon_price_limit] < $data[coupon_dc])
               $data[coupon_dc] = $data[coupon_price_limit];
            if ($item[payprice] * $item[ea] <= $data[coupon_dc])
               $data[coupon_dc] = $item[payprice] * $item[ea];
         break;
      }
      return $data[coupon_dc];
   }

   //장바구니 담신 상품의 주문배송방법 변경하기     20141201    chunter
   function OrderShipytpeUpdate($rid_cartno, $order_ship_type, $mid) {
      //$query = "select cartno from exm_cart a, exm_goods b where a.goodsno = b.goodsno and cid = '$this->cid' and mid = '$mid' and b.rid='$rid'";
      //cartno로 업데이트 진행 / 16.03.24 / kjm
      $this -> db -> query("update exm_cart set order_shiptype = '$order_ship_type' where cid = '$this->cid' and cartno in ($rid_cartno)");

      msg(_("변경 완료되었습니다."));

      /*
      $res = $this -> db -> query($query);
      while ($temp = $this -> db -> fetch($res)) {
         if ($temp[cartno]) {

         }
      }
      */
   }

   //견적 옵션 문자열 정리. 2015.07.03 by kdk
   function OptionDesc($str) {
      $opt = "";
      $estOrderOptionDesc = formatEstOrderOptionDesc($str);
      //debug($estOrderOptionDesc);

      if($estOrderOptionDesc[_("수량")]){
         $opt .= "<div>"._("수량").":";
            foreach ($estOrderOptionDesc[_("수량")] as $k => $v) {
               if($v) $opt .= $v;
            }
         $opt .= "</div>";
      }

      if($estOrderOptionDesc[_("규격")]){
         $opt .= "<div>"._("규격").":";
            foreach ($estOrderOptionDesc[_("규격")] as $k => $v) {
               if($v) $opt .= $v;
            }
         $opt .= "</div>";
      }

      if($estOrderOptionDesc[_("표지")]){
         $opt .= "<div>"._("표지").":";
            foreach ($estOrderOptionDesc[_("표지")] as $k => $v) {
               if($v) $opt .= "<div style='padding-left:30px'>".$v."</div>";
            }
         $opt .= "</div>";
      }

      if($estOrderOptionDesc[_("내지")]){
         $opt .= "<div>"._("내지").":";
            foreach ($estOrderOptionDesc[_("내지")] as $k => $v) {
               if($v) $opt .= "<div style='padding-left:30px'>".$v."</div>";
            }
         $opt .= "</div>";
      }

      if($estOrderOptionDesc[_("추가내지")]){
         foreach ($estOrderOptionDesc[_("추가내지")] as $k => $v) {
            $opt .= "<div>"._("추가내지").":";
               foreach ($v as $k2 => $v2) {
                  if($v2) $opt .= "<div style='padding-left:30px'>".$v2."</div>";
               }
            $opt .= "</div>";
         }
      }

      if($estOrderOptionDesc[_("면지")]){
         $opt .= "<div>"._("면지").":";
            foreach ($estOrderOptionDesc[_("면지")] as $k => $v) {
               if($v) $opt .= "<div style='padding-left:30px'>".$v."</div>";
            }
         $opt .= "</div>";
      }

      if($estOrderOptionDesc[_("간지")]){
         $opt .= "<div>"._("간지").":";
            foreach ($estOrderOptionDesc[_("간지")] as $k => $v) {
               if($v) $opt .= "<div style='padding-left:30px'>".$v."</div>";
            }
         $opt .= "</div>";
      }

      if($estOrderOptionDesc[_("옵션")]){
         $opt .= "<div>"._("옵션").":";
            foreach ($estOrderOptionDesc[_("옵션")] as $k => $v) {
               if($v) $opt .= "<div style='padding-left:30px'>".$v."</div>";
            }
         $opt .= "</div>";
      }

      if($estOrderOptionDesc[_("후가공")]){
         $opt .= "<div>"._("후가공").":";
            foreach ($estOrderOptionDesc[_("후가공")] as $k => $v) {
               if($v) $opt .= "<div style='padding-left:30px'>".$v."</div>";
            }
         $opt .= "</div>";
      }
      return $opt;
   }

   //일반상품 보관함코드 생성 및 장바구니 업데이트. / 20180108 / kdk
   function CreateStorageId($siteProductCode = '') {
      global $db,$client,$soap_port;
      $result = "";
      $siteCode = "";

      # podstation siteid
      if (!$cfg[podsiteid])
         $cfg[podsiteid] = $GLOBALS[cfg_center][podsiteid];

      # 자체편집상품일경우
      $siteCode = $cfg[podsiteid];
      if ($g_data[pods_useid] != "center") {
         list($dummy) = $db->fetch("select self_podsiteid from exm_mall where cid = '$this->cid'", 1);
         if ($dummy)
            $siteCode = $dummy;
      }

      $pod_data = array(
         "siteCode" => $siteCode,
         "productSiteCode" => $siteCode,
         "siteProductCode" => $siteProductCode,
         "orderUserId" => $this -> sess[mid],
         "subOption" => "",
         "oasisMakingOption" => "",
         "oasisImpositionOption" => "",
         "oasisUserImpositionOption" => ""
      );

      $this->podsApi->setVersion('20');
      $result = $this->podsApi->SetCreateStorageIdData($pod_data);

      return $result;
   }


    //시안요청 조회. / 20190122 / kdk
    function getDesign($cartno) {
        $m_modern = new M_modern();

        return $m_modern->getDesignCartno($cartno);
    }

    //시안요청 등록. / 20190122 / kdk
    function setDesignInsert($data) {
        //md_design_draft
        $m_modern = new M_modern();

        //($sess[mid]) ? " and mid = '{$sess[mid]}'"
        $d_data = array(
            "cartno" => $this->addid_direct,
            "cid" => $this->cid,
            "mid" => $this->sess[mid],
            "storageid" => "$data[storageid]",
            "est_order_data" => "$data[est_order_data]",
            "est_order_option_desc" => "$data[est_order_option_desc]",
            "est_file_upload_json" => "$data[est_file_upload_json]",
            "ext_json_data" => "$data[ext_json_data]"
        );

        return $m_modern->setDesignDraftInsert($d_data);
    }

    //시안요청 수정. / 20190122 / kdk
    function setDesignUpdate($cartno,$payno) {
        //md_design_draft
        $m_modern = new M_modern();

        $cnt = $m_modern->getDesignCartno($cartno);

        if ($cnt == 1) {
            $m_modern->setDesignDraftPaynoUpdate($cartno, $payno);
        }
    }

    // 주문,견적 금액 절사 몰설정 갖고오기 20190610 jtkim
    function setCuttingCfg(){
      $cutmoney = new M_config();
      //절사여부 (1:사용 2:미사용)
      $c_use = $cutmoney->getConfigInfo($this->cid,"cutmoney_use");
      //절사금액 (1:1의자리 ,2:10의자리, 3:100의자리)
      $c_type = $cutmoney->getConfigInfo($this->cid,"cutmoney_type");
      //절사방식 (F:버림처리,C:올림처리,R:반올림처리)
      $c_op = $cutmoney->getConfigInfo($this->cid,"cutmoney_op");
      //나머지 계산 flag
      $c_mod = 1;
      //절사 실행
      return array(
         "c_use" => $c_use[value],
         "c_type" => $c_type[value],
         "c_op" => $c_op[value],
         "c_mod" => $c_mod,
      );
    }
    // 주문,견적 금액 절사 기능 20190529
    function setCuttingMoney($totalprice){
      $cut_cfg = $this->setCuttingCfg();
      return getDigitNumCutting($totalprice,$cut_cfg[c_type],$cut_cfg[c_op],$cut_cfg[c_use],$cut_cfg[c_mod]);
    }

    // 주문,견적 금액 절사 표시문구 20190530 jtkim
    function setCuttingMoneyText(){
      $cutmoney_display = new M_config();
      $c_display=array();
      //절사 금액 표시 여부
      $c_display['money']=$cutmoney_display->getConfigInfo($this->cid,"cutmoney_display_money");
      //절사 안내문구 표시 여부
      $c_display['flag']=$cutmoney_display->getConfigInfo($this->cid,"cutmoney_display");
      //절사 안내문구 텍스트
      $c_display['text']=$cutmoney_display->getConfigInfo($this->cid,"cutmoney_display_text");
      return $c_display;
    }

    //할인 배송 사용     jtkim    190628
    function setShipDcCfg(){
      //할인 여부 확인
      $sql_shipCfg = "select * from exm_config where cid = '$this->cid' and config_group = 'ship_cfg'";
      $sql_arr = $this->db->listArray($sql_shipCfg);
      foreach($sql_arr as $k => $v){
         switch($v[config]){
            // 할인 시 배송 설정
            case "ship_cfg_dc" : $ship_dc_cfg["ship_cfg_dc"] = $v[value];
            break;
            // 배송 설정 타입
            case "ship_cfg_type" : $ship_dc_cfg["ship_cfg_type"] = $v[value];
            break;
            default : break;
         }
      }

      if( $ship_dc_cfg["ship_cfg_type"] == "N" && $ship_dc_cfg["ship_cfg_dc"] == 1){
         $res['ship_dc_data'] = $this->setShipDcData($this -> shipprice);
         $res['ship_dc_cfg']['ship_cfg_dc'] = $ship_dc_cfg["ship_cfg_dc"];
         $res['ship_dc_cfg']['ship_cfg_type'] = $ship_dc_cfg["ship_cfg_type"];
         return $res;
      }else{
            return;
      }
    }

    //할인 배송 계산 20190703 jtkim
    function setShipDcData($price_arr){
       if($price_arr){
         foreach ($price_arr as $k => $v) {
            if (strpos($k, "_no:"))
               continue;

            $m_etc = new M_etc();
            $releaseList[$k] = $m_etc->getReleaseInfo($k);

            $dc_type = "r";
            $r_shiptype = $releaseList[$k][shiptype];
            $r_shipconditional = $releaseList[$k][shipconditional];
            $r_shipprice = $releaseList[$k][shipprice];

            if ($GLOBALS[cfg][self_deliv] == 1) {
               $dc_type = "s";
               $s_shiptype = $GLOBALS[cfg][self_shiptype];
               $s_shipconditional = $GLOBALS[cfg][self_shipconditional];
               $s_shipprice = $GLOBALS[cfg][self_shipprice];
            }


            //debug($releaseList[$k]);
            // 배송비 할인 계산 변수
            // 제작사
            if($dc_type == "r"){
               $res[$dc_type][$releaseList[$k][rid]]["rid"]=$releaseList[$k][rid];
               $res[$dc_type][$releaseList[$k][rid]]["release"]=$releaseList[$k][nicknm];
               $res[$dc_type][$releaseList[$k][rid]]["shiptype"]=$r_shiptype;
               $res[$dc_type][$releaseList[$k][rid]]["shipprice"]=$r_shipprice;
               $res[$dc_type][$releaseList[$k][rid]]["shipconditional"]=$r_shipconditional;
               $res[$dc_type][$releaseList[$k][rid]]["orderprice"]=$this->cfg_dc_price[$k];
            // 자체배송비
            }else if($dc_type == "s"){
               $res[$dc_type][$releaseList[$k][rid]]["rid"]=$releaseList[$k][rid];
               $res[$dc_type][$releaseList[$k][rid]]["release"]=$releaseList[$k][nicknm];
               $res[$dc_type][$releaseList[$k][rid]]["self_shipprice_flag"]=$this->self_shipprice_flag;
               $res[$dc_type][$releaseList[$k][rid]]["shiptype"]=$s_shiptype;
               $res[$dc_type][$releaseList[$k][rid]]["shipprice"]=$s_shipprice;
               $res[$dc_type][$releaseList[$k][rid]]["shipconditional"]=$s_shipconditional;
               $res[$dc_type][$releaseList[$k][rid]]["orderprice"]=$this->cfg_dc_price[$k];
            }
            //같은 제작사 상품의 갯수
            if($releaseList[$k]["rid"] == $res[$dc_type][$releaseList[$k][rid]]["rid"]){
               $res[$dc_type][$releaseList[$k][rid]]["cnt"]++;
            }else{
               $res[$dc_type][$releaseList[$k][rid]]["cnt"] = 1;
            }

         }
       }else{
          return;
       }
       return $res;
    }

    // 배송 정책 설정  20190627 jtkim
   function shipCfgMall($next,$list,$data_arr){
            global $cfg;
            list($s_data[self_deliv],$s_data[shiptype],$s_data[shipconditional],$s_data[shipprice],$s_data[rid]) = array($cfg[self_deliv],$cfg[self_shiptype],$cfg[self_shipconditional],$cfg[self_shipprice],$data_arr[0]);
            $s_data['ship_cfg_name'] = 'mall';
            //다음 순서찾기
            $now_value = array_values($list);
            $next_value = $now_value[array_search("M",$now_value)+1];

            //몰정책 사용
            if($s_data[self_deliv]==1){
               switch($s_data[shiptype]){
                  //일반배송
                  case "0"	:
                  //조건부배송
                  case "3"	:
                     return $s_data;
                     break;
                  //무료배송
                  case "1"	:
                     //상위정책 (무료배송적용)
                     if($next == "U"){
                        $s_data[shipprice]=0;
                        return $s_data;
                     //하위정책 (상품정책)
                     }else if($next == "D" && $next_value == "G"){
                        return $this->shipCfgGood($next,$list,$data_arr);
                     //하위정책 (제작사정책)
                     }else if($next == "D" && $next_value == "R"){
                        return $this->shipCfgRelease($next,$list,$data_arr);
                     //마지막 순서 (몰정책)
                     }else{
                        $s_data[shipprice]=0;
                        return $s_data;
                     }
                     break;
               }
            //몰정책 미사용 하위순위 있으면 하위순위로
            }else{
               if($next_value =="G"){
                  return $this->shipCfgGood($next,$list,$data_arr);
               }else if($next_value == "R"){
                  return $this->shipCfgRelease($next,$list,$data_arr);
               }else{
                  return $s_data;
               }
            }
      }

   function shipCfgRelease($next,$list,$data_arr){
            global $db;
            list($s_data[shipconditional],$s_data[shiptype],$s_data[rid],$s_data[shipprice],$s_data[oshipprice]) = $db->fetch("select shipconditional,shiptype,nicknm,shipprice,oshipprice from exm_release where rid = '$data_arr[0]'",1);
            $s_data['ship_cfg_name'] = 'release';
            //다음 순서찾기
            $now_value = array_values($list);
            $next_value = $now_value[array_search("R",$now_value)+1];
            switch($s_data[shiptype]){
               //일반배송
               case "0"	:
               //조건부배송
               case "3"	:
                  return $s_data;
                  break;
               //무료배송
               case "1"	:
                  //상위정책 (무료배송적용)
                  if($next == "U"){
                     $s_data[shipprice]=0;
                     return $s_data;
                  //하위정책 (몰정책)
                  }else if($next == "D" && $next_value == "M"){
                     return $this->shipCfgMall($next,$list,$data_arr);
                  //하위정책 (제작사정책)
                  }else if($next == "D" && $next_value == "G"){
                     return $this->shipCfgGood($next,$list,$data_arr);
                  //마지막 순서 (상품정책)
                  }else{
                     $s_data[shipprice]=0;
                     return $s_data;
                  }
                  break;

            }
      }

   function shipCfgGood($next,$list,$data_arr){
         list($s_data[rid],$s_data[shiptype],$s_data[shipprice],$s_data[self_deliv],$s_data[self_dtype],$s_data[self_dprice])  = $data_arr;
         // /array($data[rid],$data[shiptype],$data[shipprice],$data[self_deliv],$data[self_dtype],$data[self_dprice]);
         $s_data['ship_cfg_name'] = 'good';
         //다음 순서찾기
         $now_value = array_values($list);
         $next_value = $now_value[array_search("G",$now_value)+1];
         switch($s_data[shiptype]){
            // 배송타입(제작사 정책)
            case "0"	:
               return $this->shipCfgRelease($next,$list,$data_arr);
               break;

            //배송타입(무료배송)
            case "1"	:
               //상위정책 (무료배송적용)
               if($next == "U"){
                  $s_data[shipprice]=0;
                  return $s_data;
               //하위정책 (몰정책)
               }else if($next == "D" && $next_value == "M"){
                  return $this->shipCfgMall($next,$list,$data_arr);
               //하위정책 (제작사정책)
               }else if($next == "D" && $next_value == "R"){
                  return $this->shipCfgRelease($next,$list,$data_arr);
               //마지막 순서 (상품정책)
               }else{
                  $s_data[shipprice]=0;
                  return $s_data;
               }
               break;

            // 배송타입(개별배송)
            case "2"	:
               return $s_data;
               break;
         }
      }
   }
?>
