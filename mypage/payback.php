<?

include "../_header.php";
include "../lib/class.page.php";

chkMember();

$db_table = "exm_mycs";
$where[] = "cid = '$cid' and mid = '$sess[mid]' and id = 'cs' and category = '9'"; //20190107 / minks / 문의유형이 캐시백인 경우 조회

//20160530 / minks / 모바일에서 사용
if ($_GET[mobile_type] == "Y") {
	foreach ($where as $k => $v) {
		if ($v) $where_v .= $v." and ";
	}

	list($mycs_cnt) = $db->fetch("select count(*) from ".$db_table." where ".substr($where_v, 0, -4), 1);
}

$pg = new Page($_GET[page], $mycs_cnt);
$pg->setQuery($db_table, $where, "regdt desc");
$pg->exec();

$res = &$pg->resource;

while ($data = $db->fetch($res)) {
	$data[subject] = stripslashes($data[subject]);
	$data[content] = stripslashes($data[content]);
	$loop[] = $data;
}

$tpl->assign('pg',$pg);
$tpl->assign('loop',$loop);
$tpl->print_('tpl');

?>