<?

include "../_header.php"; 
include "../lib/class.page.php";

chkMember();

$r_rid["|self|"] = $cfg[nameSite];
$r_rid_x = get_release();
$r_rid = array_merge($r_rid, $r_rid_x);

$_GET[orddt][0] = str_replace("-", "", $_GET[orddt][0]);
$_GET[orddt][1] = str_replace("-", "", $_GET[orddt][1]);

$db_table = "
	exm_pay a
	inner join exm_ord b on b.payno = a.payno
	inner join exm_ord_item c on c.payno = a.payno and c.ordno = b.ordno";
	
$where[] = "cid = '$cid'";
$where[] = "mid = '$sess[mid]'";
$where[] = "(itemstep = -9 || itemstep = -90)";

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

if ($_GET[sword]) $where[] = "(a.payno = '$_GET[sword]' or c.goodsnm like '%$_GET[sword]%')";
if ($_GET[orddt][0]) $where[] = "orddt > '{$_GET[orddt][0]}'";
if ($_GET[orddt][1]) $where[] = "orddt < adddate('{$_GET[orddt][1]}',interval 1 day)+0";

$pg = new Page($_GET[page]);
$pg->field = "*, a.payprice as sumpayprice";
//$pg->field = "a.*, a.payprice as sumpayprice, a.pods_use, a.podsno as p_podsno,a.podskind, a.goods_group_code, a.podoptno, a.updatedt, a.addoptnm, a.no as edking_no, a.no as review_no ";
$pg->setQuery($db_table, $where, "a.payno desc,b.ordno,c.ordseq");
$pg->exec();
//debug($pg);

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
	if (!$loop[$data[payno]]) $loop[$data[payno]] = $data;
	if (!$loop[$data[payno]]['ord'][$data[ordno]]) $loop[$data[payno]]['ord'][$data[ordno]] = $data;
	$loop[$data[payno]]['ord'][$data[ordno]]['item'][$data[ordseq]] = $data;
	$loop[$data[payno]][rowspan]++;
	$loop[$data[payno]]['ord'][$data[ordno]][rowspan]++;
}

$tpl_orderlist = "module/orderlist.htm";

$tpl->define("orderlist",$tpl_orderlist);
$tpl->assign('loop',$loop);
$tpl->assign('pg',$pg);
$tpl->assign('pg_type','cancellist');
$tpl->print_('tpl');

?>