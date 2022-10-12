<?

include "../_header.php";
include "../lib/class.page.php";

$db_table = "exm_mycs";
$where[] = "id = 'qna'";
$where[] = "cid = '$cid'";
$where[] = "goodsno = '$_GET[goodsno]'";


$pg = new Page($_GET[page]);
$pg->setQuery($db_table,$where,"","order by no desc");
$pg->exec();

$res = &$pg->resource;
while($tmp=$db->fetch($res)){
	$tmp[content] = $tmp[content];
	$tmp[regdt] = substr($tmp[regdt],0,10);
	$loop[] = $tmp;
}

$tpl->assign('loop',$loop);
$tpl->assign('pg',$pg);
$tpl->print_('tpl');

?>