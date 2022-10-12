<?

include_once "../_header.php";
include_once "../lib/class.page.php";
include_once "../models/m_member.php";

if (!$_GET[guest]) chkMember();

$db_table = "
	md_wish_list a
	left join exm_goods b on a.goodsno=b.goodsno";

$where[] = "a.cid='$cid'";
$where[] = "a.mid='$sess[mid]'";

$db->query("set names utf8");
$pg = new Page($_GET[page]);
$pg->field = "a.*,b.goodsnm,(select group_concat(catnm separator '>') from exm_category where cid=a.cid and (catno=left(a.catno,3) or catno=left(a.catno,6) or catno=left(a.catno,9) or catno=left(a.catno,12))) as catnm";
$pg->setQuery($db_table, $where, "a.no desc");
$pg->exec();

$res = &$pg->resource;

while ($data = $db->fetch($res)) {
	$list[] = $data;
}

$tpl->assign('list',$list);
$tpl->assign('pg',$pg);
$tpl->print_('tpl');

?>