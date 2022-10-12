<?

include "../_header.php";
include "../lib/class.page.php";

$m_etc = new M_etc();

$where[] = "eventno='$_GET[eventno]'";
$where[] = "!hidden";

$db_table = "exm_event_comment";

$pg = new Page($_GET[page], 10);
$pg->setQuery($db_table, $where, "regdt desc");
$pg->exec();

$res = $db->query($pg->query);
while ($tmp = $db->fetch($res)) {
	$loop[] = $tmp;
}

### 작성권한체크
$permission_write = ($sess) ? 1 : 0;

if ($sess) {
	$chk = $m_etc->getEventCommentCheck($cid, $sess[mid], $_GET[eventno]);
	if ($chk[eventno]) $permission_write = 0;
}

$tpl->assign('loop',$loop);
$tpl->assign('pg',$pg);
$tpl->print_('tpl');

?>