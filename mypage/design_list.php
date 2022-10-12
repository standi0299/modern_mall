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

if($_GET[orddt_start]) $orddt_start = str_replace("-", "", $_GET[orddt_start]);
if($_GET[orddt_end]) $orddt_end = str_replace("-", "", $_GET[orddt_end]);

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

/*if ($_GET[itemstep]) {
	if ($_GET[itemstep] == "3") {
		$where[] = "(c.itemstep = '$_GET[itemstep]' or c.itemstep = '92' or c.itemstep = '2' or c.itemstep = '4')";
	} else if ($_GET[itemstep] != "3") {
		$where[] = "c.itemstep = '$_GET[itemstep]'";
	}
}*/

$where[] = "(c.itemstep = '81' or c.itemstep = '82' or c.itemstep = '83')";

$where[] = "(c.visible_flag = 'Y' or c.visible_flag = '')";     //조건 추가   20140117  chunter
$where[] = "(a.paystep not in (0,-1))";     //조건 추가   20180208  kdk

//panyno 조건이 넘어올경우 추가   20140708  chunter
if ($_GET[payno]) $where[] = "a.payno = '$_GET[payno]'"; 
if ($_GET[sword]) $where[] = "(a.payno = '$_GET[sword]' or c.goodsnm like '%$_GET[sword]%')";
if ($orddt_start) $where[] = "orddt > '{$orddt_start}'";
if ($orddt_end) $where[] = "orddt < adddate('{$orddt_end}',interval 1 day)+0";

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

	if ($data[est_order_data]) {
		$est_order_data_arr = json_decode($data[est_order_data], true);
        $data[est_order_cnt_data] = $est_order_data_arr[order_cnt_select] ."매<br>x ".$est_order_data_arr[unit_order_cnt]."건";//1매 x 1건
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
	
	if (!$loop[$data[payno]]) $loop[$data[payno]] = $data;
	if (!$loop[$data[payno]]['ord'][$data[ordno]]) $loop[$data[payno]]['ord'][$data[ordno]] = $data;
	
	$loop[$data[payno]]['ord'][$data[ordno]]['item'][$data[ordseq]] = $data;
	$loop[$data[payno]][rowspan]++;
	$loop[$data[payno]]['ord'][$data[ordno]][rowspan]++;
}

//debug("1".$data);
$tpl->assign('loop',$loop);
$tpl->assign('pg',$pg);
$tpl->assign('member_type',$member_type);
$tpl->assign('orderview_add_link',$orderview_add_link);
$tpl->assign('pg_type','orderlist');
$tpl->print_('tpl');

?>