<?

// ##################################################
// 20140530   chunter
//wpod - 비즈카드 몰 전용 장바구니
//쿠폰, 적립금, Pods 연동 없음.
// sql 쿼리들 model 로 분리
//###################################################

class cartWpod {
    
    var $m_cart;
    var $m_goods;
    var $cid;
    var $sess;
    var $item;
    var $r_error;
    var $shipprice;
    var $acc_shipprice;
    var $oshipprice;
    var $totshipprice;
    var $itemprice;
    var $totea;
    var $client;
    var $addid = array();
    var $addid_direct;
    var $error_goods;
    var $error_code;
    var $goodsAddOption = array();
    var $editlistCardNo = array();        //편집보관함에서 넘어온 편집정보를 장바구니 테이블에서 찾은 정보 저장     20140404    chunter

    function cartWpod($cmd = '', $cartno=array()){
        $this->m_cart = new M_cart();
        $this->m_goods = new M_goods();
        $this->db   = $GLOBALS[db];
        $this->cid  = $GLOBALS[cid];
        $this->sess = $GLOBALS[sess];
        $this->item  = array();
        $this->r_error = $GLOBALS[r_cart_error];

        if ($cmd == "list")
          $this->get_items($cartno);
    }

    #   상품정보 가져오기
    function get_items($cartno=array())
    {
      global $cfg;

      if ($cartno) $cartno = implode(",",$cartno);

      $cartPublicData = array();
      $i = 0;
      //사용자 cart list 가져오기
      $res = $this->m_cart->getUserList($this->cid, $this->sess[mid], $cartno);
      foreach ($res as $key => $data)
      {
         # 상품정보 exm_goods 데이터 수집 (상품번호,상품명,공급가,판매가), 삭제된 상품은 장바구니에서 지워준다.
         $tmp = $this->m_goods->getInfo($data[goodsno]);
         if (!$tmp[goodsno])
         {
            $data[error] = 1;
            $this->m_cart->delete($this->cid, $data[cartno]);
            unset($data);
            continue;
         }

         $data[supply_goods] = $tmp[sprice];
         $data[cost_goods] = $tmp[oprice];
         $data[oprice] = $tmp[oprice];
         $data[pods_use] = $tmp[pods_use];

         if ($tmp) $data = array_merge($tmp,$data);

         if ($tmp[state] > 0 || ($tmp[usestock] && $tmp[totstock] < 1)) 
            $data[error] = 3;     //"품절 및 판매중지"

          # 상품정보 exm_goods_cid 데이터 수집 (판매가 및 샵관련 상품정보)
          $tmp = $this->m_goods->getGoodsCidInfo($this->cid, $data[goodsno]);
          if ($tmp[strprice]) $data[error] = 3;       //"품절 및 판매중지"
          if (!is_numeric($tmp[price])) 
            unset($tmp[price]);
          else 
            $data[price] = $tmp[price];
          if (!$tmp[goodsno] && !$data[error]) $data[error] = 2;    //"샵의 판매중지"
          if ($tmp) $data = array_merge($tmp,$data);
            
          $data[enabled_ea] = $data[totstock];
          if ($data[usestock] && $data[totstock] < 1){
            $data[error] = 3;     //"품절 및 판매중지"
          } else if ($data[usestock] && $data[totstock]<$data[ea]){
            $data[error] = 6;     //"재고부족"
          }
            
          //제작사별 배송 금액, 주문 금액 구분하기. 하지만 상품이 하나라 한번만 한다.
          list($data[release]) = $this->db->fetch("select nicknm from exm_release where rid = '$data[rid]'",1);
          if ($data[self_deliv])
          {
            $data[rid] = "|self|";
            $data[release] = $GLOBALS[cfg][nameSite];
          }

          if ($data[rid]!="|self|")
          {
            if ($data[shiptype]==2)
            {
               $data[rid] = $data[rid]."_no:".$data[cartno];
               $data[release] = $data[release]."<div class='stxt'>["._("개별배송")."]</div>";
            } 
            else if ($data[shiptype]==0)
            {
               list($data[shipprice],$data[oshipprice]) = $this->db->fetch("select shipprice,oshipprice from exm_release where rid = '$data[rid]'",1);
            } 
            else if ($data[shiptype]==1)
            {
               $this->shipfree[$data[rid]] = 1;
            }
            else if ($data[shiptype] == 4) {      //착불배송    20140923  chunter
               $this -> shipfree[$data[rid]] = 1;
            }
         } else {
            if ($data[self_dtype]==2)
            {
               $data[rid] = $data[rid]."_no:".$data[cartno];
               $data[release] = $data[release]."<div class='stxt'>["._("개별배송")."]</div>";
               $data[shipprice] = $data[self_dprice];
            } 
            else if ($data[self_dtype]==0)
            {
               $data[shipprice] = $GLOBALS[cfg][shipconfig][shipprice];
            }
            else if ($data[self_dtype]==1)
            {
              $this->shipfree[$data[rid]] = 1;
            }
         }
          
         $data[r_addopt] = $this->getGoodsAddOption($data[goodsno]);
         $cartPublicData = $data;

         //상품 관련 공통 데이타는 복사한다.
         $data[r_addopt]     = $cartPublicData[r_addopt];
         $data[error]        = $cartPublicData[error];
         $data[supply_goods] = $cartPublicData[supply_goods];
         $data[cost_goods]   = $cartPublicData[cost_goods];
         $data[oprice]       = $cartPublicData[oprice];
         $data[pods_use]     = $cartPublicData[pods_use];
         $data[price]        = $cartPublicData[price];
         $data[enabled_ea]   = $cartPublicData[enabled_ea];
         $data[rid]          = $cartPublicData[rid];
         $data[release]      = $cartPublicData[release];
         $data[shipprice]    = $cartPublicData[shipprice];

         $data[ea_mod_enabled] = true;
        if ($data[storageid])
        {            
          # 편집상태 조회
          $ret = readUrlWithcurl('http://' .PODS20_DOMAIN. '/CommonRef/StationWebService/GetCartCompleteResult.aspx?storageid=' .$v);            
              
          # 수량수정가능여부 체크  (수량 수정 불가능 하고 옵션에서 매수 변경하도록 처리)
          //$data[ea_mod_enabled] = true; 어떻게 할지 고민중    20140531
                
          if ($data[pods_use]=="3")
          {
            switch ($ret){
              case "10": case "20":
                $data[error]="7";     //"편집미완료"
                break;
              case "40": case "60": case "70": case "90":
                $data[error]="11";    //"주문완료"
                break;
            }
          }

          //wpod 편집기 vdp 편집정보 읽어오기      20140422
          if ($data[vdp_edit_data])
          {
            //{"@name":"최서영","@position":"과장","@phone":"","@cellphone":"010-0000-0000","@address":"서울 금천구 가산동"}
            $data[vdp_edit_data] = str_replace("\n", "", $data[vdp_edit_data]);
	   		$data[vdp_edit_data] = str_replace("\r", "", $data[vdp_edit_data]);
            $vdp_edit_data_arr = json_decode($data[vdp_edit_data], true);
            $data[vdp_edit_data_desc] = $vdp_edit_data_arr['@name'] ."," .$vdp_edit_data_arr['@position']. "," .$vdp_edit_data_arr['@cellphone'];
            //20140430 / minks / 장바구니에 주문자와 부서명 필요해서 추가함
            $data[vdp_name] = $vdp_edit_data_arr['@name'];
            $data[vdp_department] = $vdp_edit_data_arr['@department'];
          }
          //debug($data[vdp_edit_data]);
        }
        
        $data[r_addoptno] = $data[addoptno];
        if ($data[addoptno])
        {
          $data[addoptno] = explode(",",$data[addoptno]);
          foreach ($data[addoptno] as $k=>$v)
          {
            if (!$v) continue;
            $tmp = $this->m_goods->getGoodsAddOpt($this->cid, $v);

            $data[supply_addopt] += $tmp[addopt_asprice];
            $data[cost_addopt] += $tmp[addopt_aoprice];

            if (!$tmp[goodsno]) $data[error] = 4;
            if ($tmp[addopt_bundle_view]) $data[error] = 5;
            if ($tmp[addopt_view]) $data[error] = 5;

            $data[addopt][] = $tmp;
            $data[addopt_aprice] += $tmp[addopt_aprice];
            $data[addopt_areserve] += $tmp[addopt_areserve];
          }
        }

        //가격 계산 (기본가격 + 옵션 가격) * 수량
        $price[$data[rid]] += ($data[price]+$data[addopt_aprice])*$data[ea];
        if ($this->grpdc)
        {
          $data[grpdc] = round(($data[price]+$data[addopt_aprice]) * $this->grpdc/100,-1);
          $this->dc += $data[grpdc]*$data[ea];
        }

        $data[dc_coupon] = 0;
        $data[reserve_coupon] = 0;

        $data[saleprice] = ($data[price]+$data[addopt_aprice]) * $data[ea];
        $data[payprice] = $data[saleprice] - ($data[grpdc]*$data[ea]);
        
		//20150506 / minks / bizcard 장바구니에서 주문 순서
		$i++;
		$data[ordindex] = $i;
		
		//20150605 / minks / 주문자의 정산담당자
		list($manager_no) = $this->db->fetch("select manager_no from exm_member where cid='$this->cid' and mid='{$this->sess[mid]}'",1);
		list($cart_manager_no) = $this->db->fetch("select cart_manager_no from exm_cart where storageid = '$data[storageid]'",1);
		
		//20150605 / minks / 각 주문에 정산담당자 할당(우선순위 : 주문 정산담당자 > 회원 정산담당자)
		if ($cart_manager_no) $data[cart_manager_no] = $cart_manager_no;
		else $data[cart_manager_no] = $manager_no;

      $data[manager] = $this->getManagerSelect($this->cid);

      $item[$data[rid]][] = $data;

      if ($data[error]) {
         $this->error_goods = true;
      }

      $this->shipprice[$data[rid]] = ($this->shipfree[$data[rid]] == 1) ? 0:$data[shipprice];
      $this->oshipprice[$data[rid]] = $data[oshipprice];
      $this->ordprice[$data[rid]] += ($data[price]+$data[addopt_aprice])*$data[ea];
      
      $this->totea += $data[ea];
      $this->itemprice += $data[saleprice];
      $this->totreserve += $data[totreserve];
      }

      if ($this->shipprice)
      {
        foreach ($this->shipprice as $k=>$v)
        {
          if (strpos($k,"_no:")) continue;
          
          if ($k=="|self|")
          {
            $shiptype = $GLOBALS[cfg][shipconfig][shiptype];
            $shipconditional = $GLOBALS[cfg][shipconfig][shipconditional];
          } else {
            list($shiptype,$shipconditional) = $this->db->fetch("select shiptype,shipconditional from exm_release where rid = '$k'",1);
          }

          switch ($shiptype){
            case "1": $this->shipprice[$k] = 0; break;
            case "3":
              if ($price[$k] >= $shipconditional) $this->shipprice[$k] = 0;
              break;
          }
          $this->acc_shipprice[$k] = $this->shipprice[$k];

          if ($cfg[self_deliv]==1)
          {
            $shiptype = $cfg[self_shiptype];
            $shipconditional = $cfg[self_shipconditional];
            $this->shipprice[$k] = $cfg[self_shipprice];

            switch ($shiptype)
            {
              case "1": $this->shipprice[$k] = 0; break;
              case "3":
                if ($price[$k] >= $shipconditional) $this->shipprice[$k] = 0;
                break;
            }
          }
        }
        $this->totshipprice = array_sum($this->shipprice);
      }
      $this->totprice = $this->itemprice + $this->totshipprice;
      $this->item = $item;
    }

    #########################################################
    #   장바구니 추가                                        #
    #   $data = array(                                      #
    #       'index' => array(goodsno,optno,ea,storageid);   #
    #   );                                                  #
    #########################################################
    function add($data)
    {
      foreach ($data as $k=>$v)
      {
        $addflds = ($this->sess[mid]) ? ",mid = '{$this->sess[mid]}'":",mid = '', cartkey = '$_COOKIE[cartkey]'";
        if ($v[addopt]) $v[addopt] = implode(",",$v[addopt]); //20161028 / minks / 추가옵션이 있으면 콤마로 묶음 -> 안묶으면 db에 'Array'가 저장됨
        if (!$v[ea]) $v[ea] = 1;
        if ($v[title])
          $v[title] = preg_replace("'", "''", $v[title]);

          if ($v[storageid])
          {
            list($chk) = $this->db->fetch("select cartno from exm_cart where cid = '$this->cid' and storageid = '$v[storageid]'",1);
            if ($chk) 
            {
              $this->editlistCardNo[] = $chk;
              continue;
            }
          }
          
          //자동 견적 주문도 장바구니 담는다.       chunter
          $query = "insert into exm_cart set
                    goodsno               = '$v[goodsno]',
                    optno                 = '$v[optno]',
                    ea                    = '$v[ea]',
                    storageid             = '$v[storageid]',
                    cid                   = '$this->cid',
                    addoptno              = '$v[addopt]',
                    `title`               = '$v[title]',                    
                    vdp_edit_data         = '$v[vdp_edit_data]',
                    est_order_data        = '$v[est_order_data]',
                    est_order_option_desc = '$v[est_order_option_desc]',
                    est_order_type        = '$v[est_order_type]',
                    est_cost              = '$v[est_cost]',
                    est_supply            = '$v[est_supply_price]',
                    est_price             = '$v[est_price]',
                    est_order_memo        = '$v[est_order_memo]',
                    updatedt              = now()
                    $addflds
                on duplicate key update
                    ea                    = '$v[ea]',
                    `title`               = '$v[title]',
                    vdp_edit_data         = '$v[vdp_edit_data]',                    
                    est_order_data        = '$v[est_order_data]',
                    est_order_option_desc = '$v[est_order_option_desc]',
                    est_order_type        = '$v[est_order_type]',
                    est_cost              = '$v[est_cost]',
                    est_supply            = '$v[est_supply_price]',
                    est_price             = '$v[est_price]',
                    `est_order_memo`      = '$v[est_order_memo]'
                ";

          $this->db->query($query);
          $dummy = $this->db->id;

          if ($dummy)
          {
            $this->addid[] = $dummy;
            $this->addid_direct = $dummy;
          }
          else if (!$dummy)
          {
            $_addflds = ($this->sess[mid]) ? "and mid = '{$this->sess[mid]}'":"and cartkey = '$_COOKIE[cartkey]'";
            $query = "
                select cartno from exm_cart
                where
                    goodsno      = '$v[goodsno]'
                    and optno    = '$v[optno]'
                    and addoptno = '$v[addopt]'
                    and cid      = '$this->cid'
                    $_addflds
                ";
            list($dummy) = $this->db->fetch($query,1);
//          $this->addid[] = $dummy;
            $this->addid_direct = $dummy;
          }
        }
        return true;
    }


    //추가 옵션 변경하기
    function updateAddOption($cartno, $addopt)
    {      
      $query = "update exm_cart set addoptno = '$addopt' where cartno = '$cartno'";
      $this->db->query($query);
    }

    function del($cartno){
        $query = "delete from exm_cart where cartno = '$cartno'";
        $this->db->query($query);
    }

    function mod($cartno,$ea)
    {
        global $db,$client;

        $query = "update exm_cart set ea = '$ea' where cartno = '$cartno'";

        $this->db->query($query);
        
        //멀티팬시편집기 장바구니에서 주문 수량 변경 / 14.03.04 / kjm
        $data = $this->db->fetch("select a.storageid, a.ea, b.podskind from exm_cart a
                            left join exm_goods b on a.goodsno = b.goodsno where a.cartno = '$cartno'");

        //편집기 종류가 멀티팬시편집기 일때 연동안 호출 / 14.03.05 / kjm
        if ($data[podskind] == 24)
        {
            //pods 연동안 주소
            $soap_url = "http://".PODS10_DOMAIN."/StationWebService/StationWebService.asmx?WSDL";
            
            //pods에 넘겨줄 데이터
            $pod_data[orderId]  = $data[storageid];
            $pod_data[count]    = $data[ea];

            include_once dirname(__FILE__)."/../lib/nusoap/lib/nusoap.php";
            $client = new soapclient($soap_url,true);
            $client->xml_encoding = "UTF-8";
            $client->soap_defencoding = "UTF-8";
            $client->decode_utf8 = false;
            $ret = $client->call('SetOrderCountInfo',$pod_data);
            $ret = explode("|",$ret[SetOrderCountInfoResult]);
            $r_ret = array("success"=>1,"fail"=>-1);
        }
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
        est_order_memo        = '$v[est_order_memo]',
        updatedt              = now()
        where cartno = '$v[cartno]'";
        //echo $query;
        //exit;
      $this -> db -> query($query);
    }


    function getGoodsAddOption($goodsno)
    {      
      $res = $this->m_goods->getGoodsAddOptList($goodsno);
      foreach ($res as $key => $tmp) {
        $res2 = $this->m_goods->getGoodsAddOptPriceList($this->cid, $tmp[addopt_bundle_no]);
        $result[$tmp[addopt_bundle_no]] = $tmp;
                
        foreach ($res2 as $key => $tmp2) {
          $result[$tmp[addopt_bundle_no]][addopt][] = $tmp2;
        }
      }
      return $result;  
    }

	//20150605 / minks / 정산담당자 정보 조회
	function getManagerSelect($cid)
    {
      $query = "select * from exm_manager where cid='$cid' and valid='y'";
      $res = $this -> db -> listArray($query);
      foreach ($res as $key => $tmp) {      
        $result[] = $tmp;
      }
      return $result;  
    }
    
}
?>