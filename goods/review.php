<?

include "../_header.php";
include "../lib/class.page.php";

if ($cfg[skin_theme] == "P1") {
	$db_table = "
		exm_review a
		inner join exm_goods b on a.goodsno = b.goodsno";
} else {
	$db_table = "
		exm_review a
		inner join exm_pay b on b.cid = a.cid and b.payno = a.payno
		inner join exm_ord c on c.payno = a.payno and c.ordno = a.ordno
		inner join exm_ord_item d on d.payno = a.payno and d.ordno = a.ordno and d.ordseq = a.ordseq and d.goodsno = a.goodsno";
	
	$where[] = "d.itemstep = '5'";
	
	if ($_GET[search]) $where[] = "d.goodsnm like '%$_GET[search]%'";
}

$where[] = "a.cid = '$cid'";
$where[] = "a.review_deny_admin = '0'";

if ($_GET[type] == "mypage") {
	$where[] = "a.mid = '$sess[mid]'";
} else {
	$where[] = "a.review_deny_user = '0'";
}

$pg = new Page($_GET[page]);
//$pg->field = "a.*,d.goodsnm";

if ($cfg[skin_theme] == "P1") {
	$pg->field = "a.*,b.goodsnm";
} else {
	$pg->field = "a.*,b.paydt,d.goodsnm,d.catno,(select group_concat(catnm separator '>') from exm_category where cid=a.cid and (catno=left(d.catno,3) or catno=left(d.catno,6) or catno=left(d.catno,9) or catno=left(d.catno,12))) as catnm";
}

$pg->setQuery($db_table, $where, "a.no desc");
$pg->exec();

$res = &$pg->resource;

while ($tmp = $db->fetch($res)) {
	$degree_icon = "";
	
	for ($i=0; $i<$tmp[degree]; $i++) {
		$degree_icon .= "★";
	}
	
	$tmp[degree] = $degree_icon;
	$tmp[name] = mb_substr($tmp[name], 0, -2, "utf-8")."**";
	$tmp[content_line] = explode("\n", $tmp[content]);
	
	for ($j=0; $j<2; $j++) {
		if (strlen($tmp[content_line][$j]) > 200) $tmp[content_line][$j] = mb_substr($tmp[content_line][$j], 0, 70, "utf-8")."...";
	}
	
	$loop[] = $tmp;
}

$tpl->assign('loop',$loop);
$tpl->assign('pg',$pg);
$tpl->print_('tpl');

?>