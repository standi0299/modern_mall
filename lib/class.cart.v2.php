<?

//*********************


class cartV2 {

	var $db;
	var $cid;
	var $sess;
	var $item;
	var $r_error;
	var $shipprice;
	var $acc_shipprice;
	var $oshipprice;
	var $totshipprice;
	var $itemprice;
	var $grpdc;
	var $dc;
	var $dc_coupon;
	var $totea;
	var $totreserve;
	var $client;
	var $coupon;
	var $addid = array();
	var $addid_direct;	
	var $error_goods;

	function cartV2($cartno=array(),$coupon=array()){
		include_once dirname(__FILE__)."/nusoap/lib/nusoap.php";
      include_once dirname(__FILE__)."/nusoap/lib/class.wsdlcache.php";

		$this->db	= $GLOBALS[db];
		$this->cid	= $GLOBALS[cid];
		$this->sess	= $GLOBALS[sess];
		$this->item  = array();
		$this->r_error = array(
			1	=> _("상품 삭제"),
			2	=> _("샵의 판매중지"),
			3	=> _("품절 및 판매중지"),
			4	=> _("삭제된 상품 옵션"),
			5	=> _("상품 옵션 판매중지"),
			6	=> _("재고부족"),
			7	=> _("편집미완료"),
			8	=> _("편집조회실패"),
			9	=> _("판매가 없음"),
			10	=> _("구매가능한 그룹이 아님"),
			11	=> _("주문완료"),
		);
		$this->coupon = $coupon;
		$this->get_items($cartno);
	}

	#	상품정보 가져오기
	function get_items($cartno=array()){

		global $cfg,$r_podskind20,$soap_port;
    $this_time = $this->get_time();
    $debug_data = "";
		### 최종수정일 2주후 삭제
		if (!$cfg[source_save_days]) $cfg[source_save_days] = 15;

		$this->db->query("delete from exm_cart where cid = '$this->cid' and date_format(updatedt,'%Y-%m-%d') <= adddate(curdate(), interval -$cfg[source_save_days] day)");

		if ($cartno) $cartno = implode(",",$cartno);

		$addwhere = ($this->sess[mid]) ? "and mid = '{$this->sess[mid]}'":"and mid = '' and cartkey = '$_COOKIE[cartkey]'";
		if (!$this->sess[mid] && !$_COOKIE[cartkey]){
			$addwhere = "and 0 = 1";
		}

		if ($cartno) $addwhere2 = "and cartno in ($cartno)";

		/*
		$query = "
		select storageid,b.podskind,b.pods_use
		from
			exm_cart a
			left join exm_goods b on a.goodsno = b.goodsno
		where
			cid = '$this->cid'
			$addwhere
			$addwhere2
		order by cartno desc
		";
		*/ 
		
		#복수 편집기 처리 pods_use, podskind, exm_cart 테이블에서 조회 없으면 exm_goods 테이블 사용 2016.05.23 by kdk
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
		
		$res = $this->db->query($query);
		while ($data = $this->db->fetch($res)){
			if (in_array($data[podskind],$r_podskind20) || $data[pods_use]=="3"){ /* 2.0 상품 */
				$r_storageid20[] = trim($data[storageid]);
			} else {
				$r_storageid[] = trim($data[storageid]);
			}
		}
    
    $debug_data .= "c1 - " . number_format($this->get_time() - $this_time, 4). _("초")."<BR>";

		if ($r_storageid){
		  
      /*
      $soapurl = 'http://podstation.ilark.co.kr/StationWebService/StationWebService.asmx?WSDL';
      $cache = new wsdlcache('/tmp/soap_cache/', 12000);
      $wsdl = $cache->get($soapurl);
      if (is_null($wsdl)) {
        $wsdl = new wsdl($soapurl);
        $cache->put($wsdl);
      }      
      $client = new soapclient($wsdl, true);
      $client->xml_encoding = "UTF-8";
      $client->soap_defencoding = "UTF-8";
      $client->decode_utf8 = false;
      $ret = $client->call('GetMultiOrderInfoResult', array('storageids'=>$r_storageid));
      */ 

      
			$client = new soapclient("http://".PODS10_DOMAIN."/StationWebService/StationWebService.asmx?WSDL",true);
			$client->xml_encoding = "UTF-8";
			$client->soap_defencoding = "UTF-8";
			$client->decode_utf8 = false;

			$r_storageid = trim(implode(",",$r_storageid));
			$ret = $client->call('GetMultiOrderInfoResult',array('storageids'=>$r_storageid));
      

			list($flag) = explode("|",substr($ret[GetMultiOrderInfoResultResult],0,8));
			$ret[GetMultiOrderInfoResultResult] = substr($ret[GetMultiOrderInfoResultResult],strpos($ret[GetMultiOrderInfoResultResult],"STORAGE_ID"));
			$ret[GetMultiOrderInfoResultResult] = explode("|^|",$ret[GetMultiOrderInfoResultResult]);

			if ($ret[GetMultiOrderInfoResultResult]) foreach ($ret[GetMultiOrderInfoResultResult] as $v){
				$v = $this->_ilark_vars(substr($v,8));
				$pod_ret[$v[ID]] = $v;
			}
      $debug_data .= "c2 - " . number_format($this->get_time() - $this_time, 4). _("초")."<BR>";
		}

		if ($r_storageid20){
		  
      //soap 통신이 너무 느리다.. soap 연동 방식을 버리자   20140113  chunter
      $soapurl = 'http://'.PODS20_DOMAIN.'/CommonRef/StationWebService/GetCartCompleteResult.aspx?';      
      foreach ($r_storageid20 as $k=>$v){        
        $ret = sendPostData($soapurl, array('storageid'=>$v));
        $ret = trim(preg_replace("/\<\?.*?\?\>/si","",$ret));      
        
        list($flag,$ret) = explode("|",$ret);
        $pod_ret[$v] = $ret;
      }      
      $debug_data .= "c3 - " . number_format($this->get_time() - $this_time, 4). _("초")."<BR>";			
		}
		//debug($pod_ret);
		
		if ($GLOBALS[sess][grpno]){
			list($this->grpdc) = $this->db->fetch("select grpdc from exm_member_grp where grpno = '{$GLOBALS[sess][grpno]}'",1);
		}

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
		$res = $this->db->query($query);
		while ($data = $this->db->fetch($res)){

			if ($data[goodsno]=="-1"){
				include_once dirname(__FILE__)."/func.xml.php";
				### 견적상품 별개처리
				$data[rid]			= $data[est_rid];
				$data[cost_goods]	= $data[est_cost];
				$data[supply_goods]	= $data[est_supply];
				$data[price]		= $data[est_price];
				$data[goodsnm]		= _("자동견적상품")." : ".$data[est_goodsnm];
				$data[shiptype]		= 1;
				$data[est_order_option_desc_arr] = explode(",",$data[est_order_option_desc]);
				$data[est_opt] = array();
				foreach ($data[est_order_option_desc_arr] as $v){
					$k = substr($v,0,strpos($v,":"));
					$v = substr($v,strpos($v,":")+1);
					$data[est_opt][$k] = $v;
				}
				$data[est_order_option_desc_str] = implode("<br/>",$data[est_order_option_desc_arr]);

			} else {

				# 상품정보 exm_goods 데이터 수집 (상품번호,상품명,공급가,판매가)
				$query = "select rid, goodsno, goodsnm,	sprice, oprice,	optnm1,	optnm2,	price, state,	shiptype,	shipprice, oshipprice, podsno, usestock, totstock, podskind, pageprice, spageprice, opageprice, pods_use
				  from exm_goods where goodsno = '$data[goodsno]'";
				$tmp = $this->db->fetch($query);
				if (!$tmp[goodsno]){
					$data[error] = 1;
					$this->db->query("delete from exm_cart where cid = '$this->cid' and cartno = '$data[cartno]'");
					unset($data);
					continue;
				}

				$data[supply_goods] = $tmp[sprice];
				$data[cost_goods] = $tmp[oprice];
				$data[oprice] = $tmp[oprice];
				//$data[pods_use] = $tmp[pods_use];

	            #복수 편집기 처리 pods_use, podskind, podsno exm_cart 테이블에서 조회한 값이 있으면 exm_goods 값 무시함. 2016.05.23 by kdk
	            if(is_null($data[pods_use]) || $data[pods_use] == '') {
	               $data[pods_use] = $tmp[pods_use];
	            } 
	            if(is_null($data[podskind]) || $data[podskind] == '') {
	             	$data[podskind] = $tmp[podskind];
	            }                
	            if(is_null($data[podsno]) || $data[podsno] == '') {
	             	$data[podsno] = $tmp[podsno];
	            }				
				
				if ($tmp) $data = array_merge($tmp,$data);

				if ($tmp[state] > 0 || ($tmp[usestock] && $tmp[totstock] < 1)) $data[error] = 3;
				# 상품정보 exm_goods_cid 데이터 수집 (판매가 및 샵관련 상품정보)
				$query = "select mall_pageprice,mall_pagereserve,strprice,goodsno,price,reserve,self_deliv,self_dtype,self_dprice,b2b_goodsno from exm_goods_cid where cid = '$this->cid' and goodsno = '$data[goodsno]'";
				$tmp = $this->db->fetch($query);
				if ($tmp[strprice]) $data[error] = 3;
				if (!is_numeric($tmp[price])) unset($tmp[price]);
				else $data[price] = $tmp[price];
				if (!$tmp[goodsno] && !$data[error]) $data[error] = 2;
				if ($tmp) $data = array_merge($tmp,$data);

				$query = "select goodsno from exm_goods_bid where cid = '$this->cid' and goodsno = '$data[goodsno]' limit 1";
				list($bid_chk) = $this->db->fetch($query,1);

				if ($this->sess[bid]){
					if ($bid_chk){
						$query = "select goodsno from exm_goods_bid where cid = '$this->cid' and bid = '{$this->sess[bid]}' and goodsno = '$data[goodsno]' limit 1";
						list($bid_chk2) = $this->db->fetch($query,1);
						if (!$bid_chk2 && !$data[error]){
							$data[error] = 10;
						}
					}
					$data[price] = get_business_goods_price($data[goodsno],$data[price]);
					$data[reserve] = get_business_goods_reserve($data[goodsno],$data[reserve]);
				} else {
					if ($bid_chk && !$data[error]){
						$data[error] = 10;
					}
				}

				$data[enabled_ea] = $data[totstock];

				if ($data[usestock] && $data[totstock] < 1){
					$data[error] = 3;
				} else if ($data[usestock] && $data[totstock]<$data[ea]){
					$data[error] = 6;
				}
				$data[ea_mod_enabled] = true;
				if ($data[storageid]){
					$data[ea_mod_enabled] = false;
					# 편집상태 조회
					$ret = $pod_ret[$data[storageid]];

					# 수량수정가능여부 체크
					if (!in_array($data[podskind],array(1,12,7,27,28,24,35,1,2,3180))){
						$data[ea_mod_enabled] = true;
					}
					if ($data[podskind]=="24" && $ret['COUNT']=="1" && $ret['TOTALCOUNT']=="1" && $ret['PAGE']=="1"){
						$data[ea_mod_enabled] = true;
					}
					if ($data[pods_use]=="3"){
						switch ($ret){
							case "10": case "20":
								$data[error]="7";
								break;
							case "40": case "60": case "70": case "90":
								$data[error]="11";
								break;
						}
					} else if (in_array($data[podskind],array(1,2,3010,3011))){
						$ret[DATA] = str_replace("[","",$ret[DATA]);
						$ret[DATA] = str_replace("]","",$ret[DATA]);
						$ret[DATA] = explode("|",$ret[DATA]);
						foreach ($ret[DATA] as $v){
							unset($printopt);
							$v2 = $this->_ilark_vars($v,",");
							$printopt[printoptnm] = $v2[size];
							$printopt[ea] = $v2[count];
							$data[r_print_opt][] = $printopt;
						}
						$data[printopt] = serialize($data[r_print_opt]);
						$this->db->query("update exm_cart set printopt = '$data[printopt]' where cartno = '$data[cartno]'");
					} else if(in_array($data[podskind],array(12,24))) {
						$data[ea] = $ret[TOTALCOUNT];
						$this->db->query("update exm_cart set ea = '$data[ea]' where cartno = '$data[cartno]'");
						# 포토북의 제작 상태 가져오기
						$ret[PROGRESS] = explode("/",$ret[PROGRESS]);
						if (is_array($ret[PROGRESS])){
							if ($ret[PROGRESS][0]!=$ret[PROGRESS][1]){
								//2013.12.31 / minks / 에러메시지 수정( 0/1 -> 50% )
								//$add_errmsg = $ret[PROGRESS][0]." / ".$ret[PROGRESS][1];
								if($ret[PROGRESS][0]==0){
									$add_errmsg = "0%";
								}
								else{
									$add_errmsg = round($ret[PROGRESS][0] / $ret[PROGRESS][1] * 100)."%";
								}
								$data[error] = 7;
								$this->db->query("update exm_edit set state = 0 where storageid = '$data[storageid]'");
								//2013.12.26 / minks / 편집중일 때만 몇 퍼센트 편집됬는지 진행률을 나타냄
								if($ret[PROGRESS][0]==0){
									$data[PROGRESS] = "0%";
								}
								else{
									$data[PROGRESS] = round($ret[PROGRESS][0] / $ret[PROGRESS][1] * 100)."%";
								}
							}
						}
					} else if(in_array($data[podskind],array(28,3180))) {
						$query = "select optno from exm_goods_opt where goodsno = '$data[goodsno]' and opt1 = '$ret[TOTALCOUNT]'";
						list($data[optno]) = $this->db->fetch($query,1);
						$query = "select cartno from exm_cart where goodsno = '$data[goodsno]' and optno = '$data[optno]' and storageid='$data[storageid]'";
						list($chk_duplicate) = $this->db->fetch($query,1);
						if ($chk_duplicate!=$data[cartno]){
							$this->db->query("delete from exm_cart where cartno = '$chk_duplicate'");
						}
						$this->db->query("update exm_cart set optno = '$data[optno]' where cartno = '$data[cartno]'");
					} else if ($data[podskind]==7){
						if ($ret[STATE]!=30){
							$data[error]="7";
						}
					} else if ($data[podskind]==35){
						$data[ea] = $ret[BOOK];
					} else {
						# 추가페이지 계산
						$ret[DATA] = str_replace("[","",$ret[DATA]);
						$ret[DATA] = str_replace("]","",$ret[DATA]);
						$ret[DATA] = explode(",",$ret[DATA]);

						if (is_array($ret[DATA])) {
  							foreach ($ret[DATA] as $v){
  								$v = explode("=",$v);
  								if ($v[0]=="pagecount") $pagecount=$v[1];
  								if ($v[0]=="editorbase") $basecount=$v[1];
  								if ($v[0]=="inc") $inc = $v[1];
  							}
  						}
  					
						if (is_numeric($pagecount) && is_numeric($basecount)){
							$data[addpage] = $pagecount - $basecount;
						}
						if ($data[addpage]){
							if ($data[mall_pageprice]) $data[pageprice] = $data[mall_pageprice];
							$data[addpage_sprice] = $data[spageprice] * ($data[addpage]/$inc);
							$data[addpage_oprice] = $data[opageprice] * ($data[addpage]/$inc);
							$data[addpage_price] = get_business_goods_addpage_price($data[goodsno],$data[pageprice]) * ($data[addpage]/$inc);
							$data[addpage_reserve] = get_business_goods_addpage_reserve($data[goodsno],$data[mall_pagereserve]) * ($data[addpage]/$inc);
						}
						
						# 포토북의 제작 상태 가져오기
						$ret[PROGRESS] = explode("/",$ret[PROGRESS]);
						if (is_array($ret[PROGRESS])) {
  							if ($ret[PROGRESS][0]!=$ret[PROGRESS][1]){
  								//$add_errmsg = $ret[PROGRESS][0]." / ".$ret[PROGRESS][1];
  								if($ret[PROGRESS][0]==0){
									$add_errmsg = "0%";
								}
								else{
									$add_errmsg = round($ret[PROGRESS][0] / $ret[PROGRESS][1] * 100)."%";
								}
  								$data[error] = 7;
  								$this->db->query("update exm_edit set state = 0 where storageid = '$data[storageid]'");
  							}
        			    }
					}
				}

        $debug_data .= "c4 - " . number_format($this->get_time() - $this_time, 4). _("초")."<BR>";

				if ($data[optno]){
					# 상품옵션정보 exm_goods_opt 데이터 수집 (옵션,옵션가 및 상품정보)
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
					$tmp = $this->db->fetch($query);
					$data[supply_opt] = $tmp[asprice];
					$data[cost_opt] = $tmp[aoprice];

					if (trim($tmp[b2b_goodsno])) $data[b2b_goodsno] = $tmp[b2b_goodsno];

					if ($data[usestock] && $tmp[stock] < 1){
						$data[error] = 3;
					} else if ($data[usestock] && $tmp[stock]<$data[ea]){
						$data[error] = 6;
					} else if (!$tmp[optno] && !$data[error]){
						$data[error] = 4;
					} else if ($tmp[opt_view] && !$data[error]){
						$data[error] = 5;
					}
					if (!$data[optnm1]) $data[optnm1] = _("옵션1");
					if (!$data[optnm2]) $data[optnm2] = _("옵션2");
					if ($tmp[opt1]) $tmp[opt][] = $data[optnm1].":".$tmp[opt1];
					if ($tmp[opt2]) $tmp[opt][] = $data[optnm2].":".$tmp[opt2];
					if (is_array($tmp[opt])) $tmp[opt] = implode(" / ",$tmp[opt]);
								
					if (!$tmp[goodsno]){
						$data[error] = 1;
						$this->db->query("delete from exm_cart where cid = '$this->cid' and cartno = '$data[cartno]'");
						unset($data);
						continue;
					}
					if ($tmp[podsno]) $data[podsno] = $tmp[podsno];

					if ($this->sess[bid]){
						$tmp[aprice] = get_business_goods_opt_price($data[goodsno],$data[optno],$tmp[aprice]);
						$tmp[areserve] = get_business_goods_opt_reserve($data[goodsno],$data[optno],$tmp[areserve]);
					}

					$data = array_merge($tmp,$data);
					$data[enabled_ea] = $tmp[stock];

				}
        $debug_data .= "c5 - " . number_format($this->get_time() - $this_time, 4). _("초")."<BR>";


        //자동견적 주문인경우 처리.     20140128
        if ($data[est_order_data])
        {          
          $data[cost_goods] = $data[est_cost];
          $data[supply_goods] = $data[est_supply];
          $data[price]    = $data[est_price];
          $data[est_order_option_desc_str] = $data[est_order_option_desc];
        }
        
				
				if ($data[printopt]){
					$data[printopt] = unserialize($data[printopt]);
					foreach ($data[printopt] as $k=>$v){

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

						list($data[printopt][$k][print_price],$data[printopt][$k][print_reserve],$data[printopt][$k][supply_printopt],$data[printopt][$k][cost_printopt],$isok) = $this->db->fetch($query,1);
						$data[supply_printopt] += $data[printopt][$k][supply_printopt] * $v[ea];
						$data[cost_printopt] += $data[printopt][$k][cost_printopt] * $v[ea];
						if (!$isok) $data[error] = 4;

						if ($this->sess[bid]){
							$data[printopt][$k][print_price] = get_business_goods_printopt_price($data[goodsno],$v[printoptnm],$data[printopt][$k][print_price]);
							$data[printopt][$k][print_reserve] = get_business_goods_printopt_reserve($data[goodsno],$v[printoptnm],$data[printopt][$k][print_reserve]);
						}

						$data[print_aprice] += $v[ea] * $data[printopt][$k][print_price];
						$data[print_areserve] += $v[ea] * $data[printopt][$k][print_reserve];
					}
				}
				$debug_data .= "c6 - " . number_format($this->get_time() - $this_time, 4). _("초")."<BR>";

				$data[r_addoptno] = $data[addoptno];

				if ($data[addoptno]){
					$data[addoptno] = explode(",",$data[addoptno]);
					foreach ($data[addoptno] as $k=>$v){
						if (!$v) continue;

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
						$tmp = $this->db->fetch($query);
						$data[supply_addopt] += $tmp[addopt_asprice];
						$data[cost_addopt] += $tmp[addopt_aoprice];

						if (!$tmp[goodsno]) $data[error] = 4;
						if ($tmp[addopt_bundle_view]) $data[error] = 5;
						if ($tmp[addopt_view]) $data[error] = 5;

						if ($this->sess[bid]){
							$tmp[addopt_aprice] = get_business_goods_addopt_price($data[goodsno],$v,$tmp[addopt_aprice]);
							$tmp[addopt_areserve] = get_business_goods_addopt_reserve($data[goodsno],$v,$tmp[addopt_areserve]);
						}

						$data[addopt][] = $tmp;
						$data[addopt_aprice] += $tmp[addopt_aprice];
						$data[addopt_areserve] += $tmp[addopt_areserve];

					}
				}
				$debug_data .= "c7 - " . number_format($this->get_time() - $this_time, 4). _("초")."<BR>";

				if ($data[error]) $data[errmsg] = $this->r_error[$data[error]]." ".$add_errmsg;

				if ($data[error] > 0 && $data[error] < 3) $data[rid] = "";

			}

			list($data[release]) = $this->db->fetch("select nicknm from exm_release where rid = '$data[rid]'",1);

			$data[rid2] = $data[rid];
			if ($data[self_deliv]){
				$data[rid] = "|self|";
				$data[release] = $GLOBALS[cfg][nameSite];
			}

			if ($data[rid]!="|self|"){

				if ($data[shiptype]==2){
					$data[rid] = $data[rid]."_no:".$data[cartno];
					$data[release] = $data[release]."<div class='stxt'>["._("개별배송")."]</div>";
				} else if ($data[shiptype]==0){
					list($data[shipprice],$data[oshipprice]) = $this->db->fetch("select shipprice,oshipprice from exm_release where rid = '$data[rid]'",1);
				} else if ($data[shiptype]==1){
					$this->shipfree[$data[rid]] = 1;
				}

			} else {

				if ($data[self_dtype]==2){
					$data[rid] = $data[rid]."_no:".$data[cartno];
					$data[release] = $data[release]."<div class='stxt'>["._("개별배송")."]</div>";
					$data[shipprice] = $data[self_dprice];
				} else if ($data[self_dtype]==0){					
					$data[shipprice] = $GLOBALS[cfg][shipconfig][shipprice];
				} else if ($data[self_dtype]==1){
					$this->shipfree[$data[rid]] = 1;
				}

			}
      $debug_data .= "c8 - " . number_format($this->get_time() - $this_time, 4). _("초")."<BR>";

			$price[$data[rid]] += ($data[price]+$data[aprice]+$data[addopt_aprice]+$data[print_aprice]+$data[addpage_price])*$data[ea];
			if ($this->grpdc){
				$data[grpdc] = round(($data[price]+$data[aprice]+$data[addopt_aprice]+$data[print_aprice]) * $this->grpdc/100,-1);
				$this->dc += $data[grpdc]*$data[ea];
			}

			$data[dc_coupon] = 0;
			if ($this->coupon[$data[cartno]][discount]){
				$data[dc_coupon] = $this->chk_dc_coupon($data,$this->coupon[$data[cartno]][discount],$this->coupon[$data[cartno]][amount][$this->coupon[$data[cartno]][discount]]);
				$this->dc_coupon += $data[dc_coupon];
				$data[dc_couponsetno] = $this->coupon[$data[cartno]][discount];
			}

			$data[reserve_coupon] = 0;
			if ($this->coupon[$data[cartno]][saving]){
				$data[reserve_coupon] = $this->chk_dc_coupon($data,$this->coupon[$data[cartno]][saving]);
				$this->reserve_coupon += $data[reserve_coupon];
				$data[reserve_couponsetno] = $this->coupon[$data[cartno]][saving];
			}

			$data[saleprice] = ($data[price]+$data[aprice]+$data[addopt_aprice]+$data[print_aprice]+$data[addpage_price]) * $data[ea];
			if (!$data[saleprice]){
//				$data[error] = 9;
			}
			$data[payprice] = $data[saleprice] - ($data[grpdc]*$data[ea]) - $data[dc_coupon];
			$data[totreserve] = ($data[reserve]+$data[areserve]+$data[addopt_areserve]+$data[print_areserve]+$data[addpage_reserve])*$data[ea]+$data[reserve_coupon];

			$item[$data[rid]][] = $data;
			if ($data[error]){
				$this->error_goods = true;
			}

			$this->shipprice[$data[rid]] = ($this->shipfree[$data[rid]] == 1) ? 0:$data[shipprice];
			$this->oshipprice[$data[rid]] = $data[oshipprice];
			$this->ordprice[$data[rid]] += ($data[price]+$data[aprice]+$data[addopt_aprice]+$data[print_aprice])*$data[ea];
			
			$this->totea += $data[ea];
			$this->itemprice += $data[saleprice];
			$this->totreserve += $data[totreserve];
      
      $debug_data .= "c9 - " . number_format($this->get_time() - $this_time, 4). _("초")."<BR>";
		}

		if ($this->shipprice){

			foreach ($this->shipprice as $k=>$v){
				if (strpos($k,"_no:")) continue;

				if ($k=="|self|"){
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

				if ($cfg[self_deliv]==1){
					$shiptype = $cfg[self_shiptype];
					$shipconditional = $cfg[self_shipconditional];
					$this->shipprice[$k] = $cfg[self_shipprice];

					switch ($shiptype){
						case "1": $this->shipprice[$k] = 0; break;
						case "3":
							if ($price[$k] >= $shipconditional) $this->shipprice[$k] = 0;
							break;
					}
				}
			}

			$this->totshipprice = array_sum($this->shipprice);
		}
    $debug_data .= "c10 - " . number_format($this->get_time() - $this_time, 4). _("초")."<BR>";
		$this->totprice = $this->itemprice + $this->totshipprice;

		$this->item = $item;

	}

	#########################################################
	#	장바구니 추가										#
	#	$data = array(										#
	#		'index' => array(goodsno,optno,ea,storageid);	#
	#	);													#
	#########################################################
	function add($data){
      
		foreach ($data as $k=>$v){
			$addflds = ($this->sess[mid]) ? ",mid = '{$this->sess[mid]}'":",mid = '', cartkey = '$_COOKIE[cartkey]'";
			
			#복수 편집기 처리 pods_use, podskind, podsno pods_editor.js에서 form에 추가하여 넘기 2016.05.23 by kdk
			if($v[pods_use]) {
				$addflds_pods = ", pods_use = '$v[pods_use]', podsno = '$v[podsno]', podskind = '$v[podskind]'";
			}			
			
			if ($v[addopt]) $v[addopt] = implode(",",$v[addopt]);
			if (!$v[ea]) $v[ea] = 1;

			if ($v[goodsno]=="-1"){

				$query = "
				insert into exm_cart set
					goodsno					= '$v[goodsno]',
					optno					= '$v[optno]',
					ea						= '$v[ea]',
					storageid				= '$v[storageid]',
					cid						= '$this->cid',
					updatedt				= now(),
					est_order_data			= '$v[est_order_data]',
					est_order_option_desc	= '$v[est_order_option_desc]',
					est_file_down_full_path	= '$v[est_file_down_full_path]',
					est_order_type			= '$v[est_order_type]',
					est_cost				= '$v[est_cost]',
					est_supply				= '$v[est_supply]',
					est_price				= '$v[est_price]',
					est_rid					= '$v[est_rid]',
					est_goodsnm				= '$v[est_goodsnm]',
					est_fullpost			= '$v[est_fullpost]',
					est_pods_version		= '$v[est_pods_version]'
					$addflds
				on duplicate key update
					updatedt				= now(),
					est_order_data			= '$v[est_order_data]',
					est_order_option_desc	= '$v[est_order_option_desc]',
					est_file_down_full_path	= '$v[est_file_down_full_path]',
					est_order_type			= '$v[est_order_type]',
					est_cost				= '$v[est_cost]',
					est_supply				= '$v[est_supply]',
					est_price				= '$v[est_price]',
					est_rid					= '$v[est_rid]',
					est_goodsnm				= '$v[est_goodsnm]',
					est_fullpost			= '$v[est_fullpost]',
					est_pods_version		= '$v[est_pods_version]'
				";
				$this->db->query($query);
				$dummy = $this->db->id;

			} else {
				if ($v[storageid]){
					list($chk) = $this->db->fetch("select cartno from exm_cart where cid = '$this->cid' and storageid = '$v[storageid]'",1);
					if ($chk) continue;
				}

				$query = "
				insert into exm_cart set
					goodsno		= '$v[goodsno]',
					optno		= '$v[optno]',
					ea			= '$v[ea]',
					storageid	= '$v[storageid]',
					cid			= '$this->cid',
					addoptno	= '$v[addopt]',
					`title`		= '$v[title]',
					updatedt	= now(),
                    vdp_edit_data = '$v[vdp_edit_data]'
					$addflds
					$addflds_pods
				on duplicate key update
					ea			= '$v[ea]',
					`title`		= '$v[title]',
                    vdp_edit_data = '$v[vdp_edit_data]'
				";
				$this->db->query($query);
				$dummy = $this->db->id;
			}
			if ($dummy){
				$this->addid[] = $dummy;
				$this->addid_direct = $dummy;
			} else if (!$dummy){
				$_addflds = ($this->sess[mid]) ? "and mid = '{$this->sess[mid]}'":"and cartkey = '$_COOKIE[cartkey]'";
				$query = "
				select cartno from exm_cart
				where
					goodsno			= '$v[goodsno]'
					and optno		= '$v[optno]'
					and addoptno	= '$v[addopt]'
					and cid			= '$this->cid'
					$_addflds
				";
				list($dummy) = $this->db->fetch($query,1);
//				$this->addid[] = $dummy;
				$this->addid_direct = $dummy;
			}

			if ($v[storageid]){
				if ($v[goodsno]=="-1"){
					$addflds = ($this->sess[mid]) ? ",mid = '{$this->sess[mid]}'":",mid = '', editkey = '$_COOKIE[cartkey]'";			
					$query = "
					insert into exm_edit set
						goodsno					= '$v[goodsno]',
						optno					= '$v[optno]',
						storageid				= '$v[storageid]',
						cid						= '$this->cid',
						updatedt				= now(),
						est_order_data			= '$v[est_order_data]',
						est_order_option_desc	= '$v[est_order_option_desc]',
						est_file_down_full_path	= '$v[est_file_down_full_path]',
						est_order_type			= '$v[est_order_type]',
						est_cost				= '$v[est_cost]',
						est_supply				= '$v[est_supply]',
						est_price				= '$v[est_price]',
						est_rid					= '$v[est_rid]',
						est_goodsnm				= '$v[est_goodsnm]',
						est_fullpost			= '$v[est_fullpost]',
						est_pods_version		= '$v[est_pods_version]'
						$addflds
					on duplicate key update
						updatedt				= now(),
						est_order_data			= '$v[est_order_data]',
						est_order_option_desc	= '$v[est_order_option_desc]',
						est_file_down_full_path	= '$v[est_file_down_full_path]',
						est_order_type			= '$v[est_order_type]',
						est_cost				= '$v[est_cost]',
						est_supply				= '$v[est_supply]',
						est_price				= '$v[est_price]',
						est_rid					= '$v[est_rid]',
						est_goodsnm				= '$v[est_goodsnm]',
						est_fullpost			= '$v[est_fullpost]',
						est_pods_version		= '$v[est_pods_version]'
					";
					$this->db->query($query);
				} else {
					$addflds = ($this->sess[mid]) ? ",mid = '{$this->sess[mid]}'":",mid = '', editkey = '$_COOKIE[cartkey]'";			
					//pod_group에서 다운로드 카운트를 해야함.est_fullpost 필드 활용 / 16.09.20 / kdk
					$query = "
					insert into exm_edit set
						goodsno		= '$v[goodsno]',
						optno		= '$v[optno]',
						storageid	= '$v[storageid]',
						cid			= '$this->cid',
						addoptno	= '$v[addopt]',
						`title`		= '$v[title]',
						est_fullpost = 'download_count',
						updatedt	= now()
						$addflds
						$addflds_pods
					on duplicate key update
						optno = '$v[optno]',
						`title`		= '$v[title]'
					";
					$this->db->query($query);
				}
			}
		}
		return true;
	}

	function del($cartno){
		$query = "delete from exm_cart where cartno = '$cartno'";
		$this->db->query($query);
	}

	function mod($cartno,$ea){
		$query = "update exm_cart set ea = '$ea' where cartno = '$cartno'";
		$this->db->query($query);
	}

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
	
	function get_time() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
  }


 

	function chk_dc_coupon($item,$couponsetno,$amount=0){

		if (!$couponsetno) return 0;

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
		$data = $this->db->fetch($query);
		if (!$data) return 0;
		
		$coupon_ok = false;

		switch ($data[coupon_range]){
			case "all":

				$coupon_ok = true;

				break;

			case "category":

				list($catno) = $GLOBALS[db]->fetch("select catno from exm_goods_link where cid = '$this->cid' and goodsno = '$item[goodsno]'",1);
				$data[coupon_catno] = explode(",",$data[coupon_catno]);

				foreach ($data[coupon_catno] as $k2=>$v2){
					if ($coupon_ok) break;
					if (substr($catno,0,strlen($v2))==$v2){
						$coupon_ok = true;
					} else {
						$coupon_ok = false;
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

		if (!$coupon_ok) return 0;

		$item[saleprice] = $item[price]+$item[aprice]+$item[addopt_aprice]+$item[print_aprice]+$item[addpage_price]; 
		$item[payprice] = $item[saleprice] -$item[grpdc];

		switch ($data[coupon_way]){

			case "price":

				if ($data[coupon_type]=="discount") $data[coupon_dc] = $data[coupon_price];
				else if ($data[coupon_type]=="saving") $data[coupon_dc] = $data[coupon_price];
				else if ($data[coupon_type] == "coupon_money") $data[coupon_dc] = $amount;
				if ($item[payprice]*$item[ea] <= $data[coupon_dc]) $data[coupon_dc] = $item[payprice]*$item[ea];

				break;

			case "rate":

				if ($data[coupon_type]=="discount") $data[coupon_dc] = round($item[saleprice] * $item[ea] * $data[coupon_rate]/ 100,-1);
				else if ($data[coupon_type]=="saving"){
					$data[coupon_dc] = round(($item[reserve]+$item[areserve]+$item[addopt_areserve]+$item[print_areserve]+$item[addpage_reserve])*$item[ea] * $data[coupon_rate]/ 100,-1);
				}

				if ($data[coupon_price_limit] && $data[coupon_price_limit] < $data[coupon_dc]) $data[coupon_dc] = $data[coupon_price_limit];
				if ($item[payprice]*$item[ea] <= $data[coupon_dc]) $data[coupon_dc] = $item[payprice]*$item[ea];
		
				break;
		}
		return $data[coupon_dc];

	}

}

?>