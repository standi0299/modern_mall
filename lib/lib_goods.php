<?

function getGoodsAddOption($goodsno) {
	global $cid;
   $m_goods = new M_goods();
   $res = $m_goods -> getGoodsAddOptList($goodsno);
   foreach ($res as $key => $tmp) {
      //debug($res);

      //상점별 옵션 노출여부 설정 변경 2014.10.23 by kdk
		list($addopt_bundle_view) = $m_goods->getGoodsAddOptViewMall("select view from tb_goods_addopt_bundle_mall_view where cid = '$cid' and goodsno = '$goodsno' and addopt_bundle_no = '$tmp[addopt_bundle_no]';");
		//debug("$tmp[addopt_bundle_no] : ".$addopt_bundle_view);
		if($addopt_bundle_view == "1") continue;

      $res2 = $m_goods -> getGoodsAddOptPriceList($m_goods -> cid, $tmp[addopt_bundle_no]);
      $data[r_addopt][$tmp[addopt_bundle_no]] = $tmp;

      foreach ($res2 as $key => $tmp2) {
         //debug($res2);

         //상점별 옵션 노출여부 설정 변경 2014.10.23 by kdk
			list($addopt_view) = $m_goods->getGoodsAddOptViewMall("select view from tb_goods_addopt_mall_view where cid = '$cid' and goodsno = '$goodsno' and addoptno = '$tmp2[addoptno]';");
			//debug("$tmp2[addoptno] : ".$addopt_view);
			if($addopt_view == "1") continue;

         $data[r_addopt][$tmp[addopt_bundle_no]][addopt][] = $tmp2;
      }
   }
   return $data;
}

//추가옵션 그룹별 옵션값을 가져온다 / 17.02.02 / kjm
function getAddoptGroup($cartno){
   global $db;
   list($addoptno) = $db->fetch("select addoptno from exm_cart where cartno = '$cartno'",1);
   $addoptno = explode(",", $addoptno);
   foreach($addoptno as $k => $v){
      $addopt_data = $db->fetch("select * from exm_goods_addopt where addoptno = '$v'");
      $data[$addopt_data[addopt_bundle_no]] = $addopt_data[addoptno];
   }

   return $data;
}


//옵션의 가격만 구한다.
function getGoodsAddOptionPrice($addoptno)
{
   global $cid;
   $m_goods = new M_goods();
   $addoptnoArr = explode(",",$addoptno);

   $addopt_aprice = 0;
   foreach ($addoptnoArr as $k=>$v)
   {
      if (!$v) continue;
      $tmp = $m_goods->getGoodsAddOpt($cid, $v);
      $addopt_aprice += $tmp[addopt_aprice];
   }

   return $addopt_aprice;
}


function getGoodsAddOptionValue($addoptno)
{
   global $cid;
   $m_goods = new M_goods();
   $addoptnoArr = explode(",",$addoptno);

   $addopt_value = array();
   foreach ($addoptnoArr as $k=>$v)
   {
      if (!$v) continue;
      $tmp = $m_goods->getGoodsAddOpt($cid, $v);
      $addopt_value[] = $tmp[addoptnm];
   }

   return $addopt_value;
}


//장바구니 상품 링크 처리 (템플릿 리스트 사용시 템플릿셋아이디, 템플릿아이디 확인하여 링크 생성) / 06.08.02 / kdk
function getViewLinkWithTemplate($goodsno, $tsid, $tid) {
	global $db, $cfg, $cfg_center, $cid;

	$link = "../goods/view.php?goodsno=$goodsno";
	//debug($goodsno);
	//debug($tsid);
	//debug($tid);
		if($tsid && $tid) {
		   list($pods_useid, $podsno) = $db->fetch("select pods_useid, podsno from exm_goods where goodsno = '$goodsno'",1);
			list($catno) = $db->fetch("select catno from exm_goods_link where cid='$cid' and goodsno='$goodsno'", 1);
			list($url) = $db->fetch("select url from exm_category where cid='$cid' and is_url='1' and url like '%goodsno=$goodsno%'", 1);
			//debug($url);
			if($url) {
				$u = explode("?", $url);

				if($u[0] == "/goods/list_template.php") {
					$soapurl = 'http://' .PODS20_DOMAIN. '/WebService/temp_product_list2.aspx?';
				}
				else {
					$soapurl = 'http://' .PODS20_DOMAIN. '/WebService/tempset_product_list.aspx?';
				
					//복수 편집기 실행 관련 2016.03.16 kdk
					//spring , pod_group 스킨 우선 적용.
					if ($cfg[skin] == "spring" || $cfg[skin] == "pod_group" || $cfg[skin] == "classic") {
						$soapurl = 'http://' .PODS20_DOMAIN. '/WebService/tempset_product_list2.aspx?';
					}
				}
			}
			//debug($soapurl);

			//자체상품일 경우 몰의 pods id를 가져오고 센터상품일 경우 센터의 podsid를 가져온다 / 17.02.21 / kjm
			if($pods_useid == "mall") list($podsiteid) = $db->fetch("select self_podsiteid from exm_mall where cid = '$cid'",1);
         else {
            $podsiteid = $cfg_center[podsiteid];
            $p_siteid = $cfg_center[podsiteid];
         }

			/*
			# podstation siteid
			if (!$cfg[podsiteid]) $cfg[podsiteid] = $cfg_center[podsiteid];
			$podsiteid = $cfg[podsiteid];

			# 센터상품일경우
			// $cfg_center[podsiteid] 센터ID를 p_siteid 파라메타에 추가로 사용 20140206 kdk
			//debug($val);
			if ($val[pods_useid]=="center" && $val[podsno]){
			    $p_siteid = $cfg_center[podsiteid];
			}
         */
			# PODs2 파라메타
			$siteid=$podsiteid;
			//$spid=$val[podsno];
			//$val 변수는 존재하지 않아 따로 podsno값을 가져와서 담아준다 / 17.02.21 / kjm
         $spid=$podsno;
			$categoryidx = "0";
			$pagesize = "100";
			$pagenum = "1";
			$param = "siteid=$siteid&p_siteid=$p_siteid&spid=$spid&categoryidx=$categoryidx&pagesize=$pagesize&pagenum=$pagenum";
			//debug($soapurl.$param);
			$surl = base64_encode($soapurl.$param);
			
			$link = "../goods/view.php?catno=$catno&goodsno=$goodsno&templateSetIdx=$tsid&templateIdx=$tid&url=$surl";
		}

	return $link;
}

//진열 상품 검색 / 16.08.03 / kdk
 function getDisplayLink($cid, $dpno, $mode="") {
 	global $cid;

 	$m_goods = new M_goods();
 	$data = "";

	if($mode == "template") {
		$data = $m_goods->getTemplateDpLinkList($cid, $dpno);
	}
	else {
		$data = $m_goods->getDpLinkList($cid, $dpno);
	}

	//debug($data);
	return $data;
}

?>