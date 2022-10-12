<?

include_once "../_header.php";
include_once "../lib/class.page.php";
include_once "../models/m_member.php";

if (!$_GET[guest]) chkMember();
$m_goods = new M_goods();
$m_order = new M_order();

$r_rid["|self|"] = $cfg[nameSite];
$r_rid_x = get_release();
$r_rid = array_merge($r_rid, $r_rid_x);
$r_shipcomp = get_shipcomp();
$r_edking_flag = getCfg('edking_flag');
$r_source_save_days = strtotime(date("Y-m-d")." -$cfg[source_save_days] days");

$_GET[orddt][0] = str_replace("-", "", $_GET[orddt][0]);
$_GET[orddt][1] = str_replace("-", "", $_GET[orddt][1]);

//수정일자 표시를 위한 updatedt값을 가져오기 위해 left join 추가 / 14.01.08 / kjm
//20140617 / minks / 옵션값(수량) 가져오기 위해 exm_goods_addopt 테이블을 left join함
$db_table = "
	exm_pay a
	inner join exm_ord b on b.payno = a.payno
	inner join exm_ord_item c on c.payno = a.payno and c.ordno = b.ordno
	left join exm_goods d on d.goodsno = c.goodsno
	left join exm_goods_opt e on e.goodsno = c.goodsno and e.optno = c.optno
	left join tb_editor_ext_data f on c.storageid=f.storage_id
	left join exm_edit g on c.storageid = g.storageid
	left join exm_goods_addopt i on i.addoptno = c.addoptno
	left join exm_edking j on j.cid = a.cid and j.payno = a.payno and j.storageid = c.storageid
	left join exm_review k on k.cid = a.cid and k.payno = a.payno and k.ordno = b.ordno and k.ordseq = c.ordseq";

if (!$_GET[guest]) {
	$db_table .= " inner join exm_member h on h.mid = a.mid and h.cid = a.cid ";
	$where[] = "h.cid = '$cid'";
	$where[] = "h.mid = '$sess[mid]'";
	$orderview_add_link = "";
} else {
	$where[] = "a.cid = '$cid'";
	$orderview_add_link = "&guest=1";
}

if ($_GET[itemstep]) {
	if ($_GET[itemstep] == "3") {
		$where[] = "(c.itemstep = '$_GET[itemstep]' or c.itemstep = '92' or c.itemstep = '2' or c.itemstep = '4')";
	} else if ($_GET[itemstep] != "3") {
		$where[] = "c.itemstep = '$_GET[itemstep]'";
	}
}

$where[] = "(c.visible_flag = 'Y' or c.visible_flag = '')";     //조건 추가   20140117  chunter
$where[] = "(a.paystep not in (0,-1))";     //조건 추가   20180208  kdk

//panyno 조건이 넘어올경우 추가   20140708  chunter
if ($_GET[payno]) $where[] = "a.payno = '$_GET[payno]'";
if ($_GET[sword]) $where[] = "(a.payno = '$_GET[sword]' or c.goodsnm like '%$_GET[sword]%')";
if ($_GET[orddt][0]) $where[] = "orddt > '{$_GET[orddt][0]}'";
if ($_GET[orddt][1]) $where[] = "orddt < adddate('{$_GET[orddt][1]}',interval 1 day)+0";

//수취인,주문인 정보 검색 추가 20210519 jtkim
if ($_GET[search_keyword]) {
	$where[] = " (a.orderer_name like '%$_GET[search_keyword]%' or 
		a.orderer_email like '%$_GET[search_keyword]%' or 
		a.orderer_phone like '%$_GET[search_keyword]%' or 
		a.orderer_mobile like '%$_GET[search_keyword]%' or 
		a.orderer_zipcode like '%$_GET[search_keyword]%' or 
		a.orderer_addr like '%$_GET[search_keyword]%' or
		a.orderer_addr_sub like '%$_GET[search_keyword]%' or
		a.receiver_name like '%$_GET[search_keyword]%' or
		a.receiver_phone like '%$_GET[search_keyword]%' or
		a.receiver_mobile like '%$_GET[search_keyword]%' or
		a.receiver_zipcode like '%$_GET[search_keyword]%' or
		a.receiver_addr like '%$_GET[search_keyword]%' or
		a.receiver_addr_sub like '%$_GET[search_keyword]%') ";
}

//20150713 / minks / 모바일에서 사용
if ($_GET[mobile_type] == "Y") {
	$where[] = "c.itemstep != '0'";

	if ($sess[mid]) {
		$where[] = " a.mid = '$sess[mid]'"; //일반상품도 리스트에 출력
  		//$where[] = " a.mid = g.mid and a.mid = '$sess[mid]'"; //편집한 상품만 리스트에 출력
	} else {
		$where[] = "g.editkey = '$_COOKIE[cartkey]' and a.mid = ''"; //비회원일 경우에 리스트에 일반상품 출력 불가능
	}

	foreach ($where as $k => $v) {
		if ($v) $where_v .= $v." and ";
	}

	list($order_cnt) = $db->fetch("select count(*) from ".$db_table." where ".substr($where_v, 0, -4), 1);
}

$db->query("set names utf8");
$pg = new Page($_GET[page], $order_cnt);

//비회원 조회일때 회원 항목 제외
//20150306 / minks / 가격 출력 수정(총 결제해야할 금액 -> 상품 개당 금액)
//20160201 / minks / 총 결제금액 출력(sumpayprice)
if (!$_GET[guest]) {
	$pg->field = "a.*, a.payprice as sumpayprice, b.*, c.*, d.pods_use, d.podsno as p_podsno,d.podskind, d.goods_group_code, e.podoptno, f.*, g.updatedt, h.*, i.addoptnm, j.no as edking_no, k.no as review_no ";
} else {
	$pg->field = "a.*, a.payprice as sumpayprice, b.*, c.*, d.pods_use, d.podsno as p_podsno,d.podskind, d.goods_group_code, e.podoptno, f.*, g.updatedt, i.addoptnm, j.no as edking_no, k.no as review_no ";
}

if ($_GET[mobile_type] != "Y" && $cfg[skin_theme] == "P1") $pg->setQuery_P1($db_table, $where, "a.payno", "a.payno desc,b.ordno,c.ordseq");
else $pg->setQuery($db_table, $where, "a.payno desc,b.ordno,c.ordseq");
$pg->exec();

$xls_query = substr($pg->query, 0, strrpos($pg->query, " limit"));
//$xls_query = base64_encode($xls_query);

### form 전송 취약점 개선 20160128 by kdk
$xls_query = base64_encode(urlencode($xls_query));
$url_query = "/mypage/indb.php?query=".$xls_query;
$pod_signed = signatureData($cid, $url_query);
$pod_expired = expiresData("20");
### form 전송 취약점 개선 20160128 by kdk

$res = &$pg->resource;

while ($data = $db->fetch($res)) {
	if ($data[addopt]) $data[addopt] = unserialize($data[addopt]);
	if ($data[printopt]) $data[printopt] = unserialize($data[printopt]);

	if ($data[shipcode] && $data[itemstep] >= 5 && $data[itemstep] < 10) {
	   if($cfg[skin_theme] == "M2")
	     $data[strshipcode] = "<a href='".$r_shipcomp[$data[shipcomp]][url].$data[shipcode]."' target='_blank' class='btn_tracking'>배송추적</a>";
      else
		 $data[strshipcode] = "<div class='col'>".$r_shipcomp[$data[shipcomp]][compnm]."<br><a href='".$r_shipcomp[$data[shipcomp]][url].$data[shipcode]."' target='_blank'>".$data[shipcode]."</a></div>";
	}

	#복수 편집기 처리 pods_use, podskind, podsno exm_edit 테이블에서 조회한 값이 있으면 exm_goods 값 무시함. 2016.05.23 by kdk
	$edit_data = $m_order->getEditInfoWithStorageid($data[storageid]);
	$tmp[pods_use] = $edit_data[pods_use];
	$tmp[podskind] = $edit_data[podskind];
	$tmp[podsno] = $edit_data[podsno];

	if($tmp[pods_use]) $data[pods_use] = $tmp[pods_use];
	if($tmp[podskind]) $data[podskind] = $tmp[podskind];
	if($tmp[podsno]) $data[p_podsno] = $tmp[podsno];

	//wpod 편집기 vdp 편집정보 읽어오기      20140422
	if ($data[vdp_edit_data]) {
		//{"@name":"최서영","@position":"과장","@phone":"","@cellphone":"010-0000-0000","@address":"서울 금천구 가산동"}
		$data[vdp_edit_data] = str_replace("\n", "", $data[vdp_edit_data]);
		$data[vdp_edit_data] = str_replace("\r", "", $data[vdp_edit_data]);
		$vdp_edit_data_arr = json_decode($data[vdp_edit_data], true);
		$data[vdp_edit_data_name] = $vdp_edit_data_arr['@name'];
		$data[vdp_edit_data_position] = $vdp_edit_data_arr['@position'];
		$data[vdp_edit_data_department] = $vdp_edit_data_arr['@department'];
	}

	//위에 vdp 편집정보가 없는 경우 다시 읽어오기 2014.11.21 by kdk
	if ($data[editor_return_json]) {
		//{"@name":"최서영","@position":"과장","@phone":"","@cellphone":"010-0000-0000","@address":"서울 금천구 가산동"}
		$vdp_edit_data_arr = json_decode($data[editor_return_json], true);
		$data[editor_return_json_name] = $vdp_edit_data_arr['@name'];
		$data[editor_return_json_position] = $vdp_edit_data_arr['@position'];
		$data[editor_return_json_department] = $vdp_edit_data_arr['@department'];

		if (!$data[title]) {
			$data[title] = $vdp_edit_data_arr[uploaded_list][0][title];
		}

		//편집기 옵션
		$data[order_option] = $vdp_edit_data_arr[uploaded_list][0][order_option];
	}

	//20141110 / minks / 상품명이 없을 경우 상품일련번호로 조회
	if (!$data[goodsnm]) {
		$goods_data = $m_goods->getInfo($data[goodsno]);
		$data[goodsnm] = $goods_data[goodsnm];
	}

	//category name 출력 2014.11.21 by kdk
	$q = $m_goods->getGoodsCategoryInfo($cid, $data[goodsno]);
	foreach ($q as $k=>$v) {
		switch (strlen($v[catno])) {
			case "3":
				$data[catnm1] = $v[catnm];
				break;
			case "6":
				$data[catnm2] = $v[catnm];
				break;
			case "9":
				$data[catnm3] = $v[catnm];
				break;
			case "12":
				$data[catnm4] = $v[catnm];
				break;
		}
	}

	if ($_GET[mobile_type] == "Y" || $cfg[skin_theme] == "P1" || !$cfg[skin_theme]) {
		list($data[ord_bundle]) = $db->fetch("select count(*) from exm_ord_item where payno='$data[payno]'", 1);
	}

	if ($_GET[mobile_type] == "Y") {
		//20160211 / minks / 옵션값만 추출
		if ($data[opt]) {
			$data[opt] = explode(" / ", $data[opt]);
			if ($data[opt][0]) {
				$data[opt][0] = explode(":", $data[opt][0]);
				$data[mobile_opt] = $data[opt][0][1];
			}
			if ($data[opt][1]) {
				$data[opt][1] = explode(":", $data[opt][1]);
				$data[mobile_opt] .= " / ".$data[opt][1][1];
			}
		}
	}

    $data[edkingdt] = ($data[confirmdt]) ? strtotime(substr($data[confirmdt],0,10)) : strtotime(substr($data[paydt],0,10));

	if ($data[est_order_data]) {
		$est_arr = json_decode($data[est_order_data], true);
		$data[order_cnt_select] = $est_arr[order_cnt_select];
		$data[unit_order_cnt] = $est_arr[unit_order_cnt];
	}

	if (!$loop[$data[payno]]) $loop[$data[payno]] = $data;
	if (!$loop[$data[payno]]['ord'][$data[ordno]]) $loop[$data[payno]]['ord'][$data[ordno]] = $data;

	$loop[$data[payno]]['ord'][$data[ordno]]['item'][$data[ordseq]] = $data;
	$loop[$data[payno]][rowspan]++;
	$loop[$data[payno]]['ord'][$data[ordno]][rowspan]++;
}

//회원 타입 조회 return : NOR, FIX --20131219 by kdk
$m_member = new M_member();
$member_type = $m_member->getMemberType($cid, $sess[mid]);

if ($_GET[mobile_type] != "Y" && $cfg[skin_theme] == "P1") {
	list($bankinfo) = $db->fetch("select bankinfo from exm_bank where cid='$cid' order by bankno limit 1", 1);
	if ($bankinfo) $bankinfo = explode(" ", $bankinfo);

	$tpl->assign('bankinfo',$bankinfo);
}

//정액 회원 결제 내역을 조회하여 이용한도를 확인한다. / 16.09.20 / kdk
//$member_fixhistory = $m_member->getFixHistory($cid, $sess[mid]);

function podsImgDB($storageid, $podskind, $preview_link = '') {
	//return $storageid."-".$payno."-".$podskind;
	if (!$preview_link) {
		global $r_podskind20, $db;

		if (in_array($podskind, $r_podskind20)) { /* 2.0 상품 */
			$r_storageid20 = $storageid;
		} else {
			$r_storageid = $storageid;
		}

		if ($r_storageid) {
			$client = "http://" .PODS10_DOMAIN. "/StationWebService/GetPreViewImg.aspx?storageid=$storageid";
		}

		if ($r_storageid20) {
			$client = "http://" .PODS20_DOMAIN. "/CommonRef/StationWebService/GetPreViewImg.aspx?storageid=$storageid";
		}

		//$ret[GetPreViewImg] = readurl($client);     //속도 저하 발생으로 curl 사용으로 변경     20140513  chunter
		$ret[GetPreViewImg] = readUrlWithcurl($client);
		$ret[GetPreViewImg] = explode("|", $ret[GetPreViewImg]);

		if (count($ret[GetPreViewImg]) > 1) {
			$img = "";
			$preview_link = $ret[GetPreViewImg][0];

			//$query = "update tb_editor_ext_data set preview_link = '$preview_link' where storage_id = '$storageid'";
			$m_order->setEditorPreviewLink($storageid, $preview_link);
		} else {
			$return_tag = "";
		}
	}

	if ($preview_link) {
		$img = explode("|", $preview_link);

		if (count($img) > 1) {
			$return_tag = "<img src='$img[0]' width='70' height='50' style='border:1px solid #dedede' onerror='this.src=\"/data/noimg.png\"'/>";
		} else {
			$return_tag = "<img src='$preview_link' width='70' height='50' style='border:1px solid #dedede' onerror='this.src=\"/data/noimg.png\"'/>";
		}
	}
	return $return_tag;
}

//pods 편집 썸네일이미지 조회 -- 20131212 by kdk
function podsImg($preview_link, $storageid, $podskind) {
	//return $storageid."-".$payno."-".$podskind;
	global $r_podskind20;

	if (in_array($podskind, $r_podskind20)) { /* 2.0 상품 */
		$r_storageid20 = $storageid;
	} else {
		$r_storageid = $storageid;
	}

	if ($r_storageid) {
		$client = "http://" .PODS10_DOMAIN. "/StationWebService/GetPreViewImg.aspx?storageid=$storageid";
	}

	if ($r_storageid20) {
		$client = "http://" .PODS20_DOMAIN. "/CommonRef/StationWebService/GetPreViewImg.aspx?storageid=$storageid";
	}

	//$ret[GetPreViewImg] = readurl($client);     //속도 저하 발생으로 curl 사용으로 변경     20140513  chunter
	$ret[GetPreViewImg] = readUrlWithcurl($client);
	$ret[GetPreViewImg] = explode("|",$ret[GetPreViewImg]);

	if (count($ret[GetPreViewImg]) > 1) {
		$img = "";
		$img = $ret[GetPreViewImg][1];
		return "<img src='$img' width='50' height='50' style='border:1px solid #dedede' onerror='this.src=\"/data/noimg.png\"'/>";
	} else {
		return "";
	}
}

//pds image     20131220    chunter
function podsImgNew($storageid, $pods_use) {
	if ($pods_use == "2") { /* 2.0 상품 */
		$client = "http://" .PODS20_DOMAIN. "/CommonRef/StationWebService/GetPreViewImg.aspx?storageid=$storageid";
	} else {
		$client = "http://" .PODS10_DOMAIN. "/StationWebService/GetPreViewImg.aspx?storageid=$storageid";
	}

	$ret[GetPreViewImg] = readurl($client);
	$ret[GetPreViewImg] = explode("|", $ret[GetPreViewImg]);

	if (count($ret[GetPreViewImg]) > 1) {
		$img = "";
		$img = $ret[GetPreViewImg][0];
		return "<img src='$img' width='50' height='50' style='border:1px solid #dedede' onerror='this.src=\"/data/noimg.png\"'/>";
	} else {
		return "";
	}
}

$tpl_orderlist = "module/orderlist.htm";
//debug($loop);
$tpl->define("orderlist",$tpl_orderlist);
$tpl->assign('loop',$loop);
$tpl->assign('pg',$pg);
$tpl->assign('member_type',$member_type);
$tpl->assign('orderview_add_link',$orderview_add_link);
$tpl->assign('pg_type','orderlist');
$tpl->print_('tpl');
?>
