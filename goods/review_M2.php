<?

include "../_header.php";
include "../lib/class.page.php";

$db_table = "
   exm_pay a
   inner join exm_ord b on b.payno = a.payno
   inner join exm_ord_item c on c.payno = a.payno
   left join exm_review d on c.payno = d.payno and c.ordno = d.ordno and c.ordseq = d.ordseq";

$where[] = "a.cid = '$cid'";
$where[] = "a.mid = '$sess[mid]'";
$where[] = "c.itemstep = '5'";
$where[] = "d.payno is null";

$pg = new Page();
//$pg->field = "a.*,c.goodsnm";
$pg->field = "a.*,c.ordno, c.ordseq,c.goodsnm,c.catno,c.goodsno,(select group_concat(catnm separator '>') from exm_category where cid=a.cid and (catno=left(c.catno,3) or catno=left(c.catno,6) or catno=left(c.catno,9) or catno=left(c.catno,12))) as catnm";
$pg->setQuery($db_table, $where);
$pg->exec();

$res = &$pg->resource;

while ($tmp = $db->fetch($res)) {
	$loop[] = $tmp;
}





$db_table = "
   exm_review a
   inner join exm_pay b on b.cid = a.cid and b.payno = a.payno
   inner join exm_ord c on c.payno = a.payno and c.ordno = a.ordno
   inner join exm_ord_item d on d.payno = a.payno and d.ordno = a.ordno and d.ordseq = a.ordseq and d.goodsno = a.goodsno";

$where_a[] = "a.cid = '$cid'";
$where_a[] = "a.review_deny_admin = '0'";
$where_a[] = "d.itemstep = '5'";
$where_a[] = "a.mid = '$sess[mid]'";

$pg_my = new Page($_GET[page], 10);
//$pg->field = "a.*,d.goodsnm";
$pg_my->field = "a.*,b.paydt,d.goodsnm,d.catno,(select group_concat(catnm separator '>') from exm_category where cid=a.cid and (catno=left(d.catno,3) or catno=left(d.catno,6) or catno=left(d.catno,9) or catno=left(d.catno,12))) as catnm";
$pg_my->setQuery($db_table, $where_a, "a.no desc");
$pg_my->exec();

$res = $pg_my->resource;
while ($tmp = $db->fetch($res)) {
   $my_review_list[] = $tmp;
}

$tpl->assign('loop',$loop);
$tpl->assign('my_review_list',$my_review_list);
$tpl->assign('pg',$pg);
$tpl->assign('pg_my',$pg_my);
$tpl->print_('tpl');

?>