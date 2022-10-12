<?

include "../_header.php";
include "../lib/class.page.php";

$m_board = new M_board();

### 이달의 편집왕
$data_best = $m_board->getBestEdkingInfo($cid);
if ($data_best[catnm]) $data_best[goodsnm] = $data_best[catnm].$data_best[goodsnm];

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

$tpl->assign('data_best',$data_best);
$tpl->assign('loop',$loop);
$tpl->assign("pg",$pg);
$tpl->print_('tpl');

?>