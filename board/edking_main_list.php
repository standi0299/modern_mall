<?

include "../_header.php";
include "../lib/class.page.php";

$where[] = "cid = '$cid'";

$db_table = "exm_edking";

$pg = new Page($_GET[page], 12);
$pg->setQuery($db_table, $where, "no desc");
$pg->exec();

$res = &$pg->resource;

$loop = array();

while ($data = $db->fetch($res)) {
	$loop[] = $data;
}

$tpl->assign('loop',$loop);
$tpl->assign("pg",$pg);
$tpl->print_('tpl');

?>