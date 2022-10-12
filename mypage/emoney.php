<?

include "../_header.php";
include "../lib/class.page.php";

chkMember();
$m_emoney = new M_emoney();
$m_member = new M_member();

//20160128 / minks / 적립금 조회를 위해 추가
//$memberdata = f_getMemeberData();
$myemoney = $m_emoney->getSumEmoneyLog($cid, $sess[mid]);

//20170629 / minks / 소멸예정 적립금 조회
$emoneydata = getCfg("", "emoney");

//노출되는 소멸예정 일자 오늘만 노출 / kdk / 20180328
$emoneydata[emoney_expire_day] = 1;
$expire_emoney = getExpireEmoneyTotal($cid, $sess[mid], $emoneydata[emoney_expire_day]);
//debug($expire_emoney);


//적립금내역
$db_table = "exm_log_emoney";

$where[] = "mid = '$sess[mid]'";
$where[] = "cid = '$cid'";

$_GET[regdt][0] = str_replace("-", "", $_GET[regdt][0]);
$_GET[regdt][1] = str_replace("-", "", $_GET[regdt][1]);

if ($_GET[regdt][0]) $where[] = "regdt > '{$_GET[regdt][0]}'";
if ($_GET[regdt][1]) $where[] = "regdt < adddate('{$_GET[regdt][1]}',interval 1 day)+0";

$pg = new Page($_GET[page], 10);
$pg->setQuery($db_table, $where, "regdt desc");
$pg->exec();

$res = &$pg->resource;
while ($data3 = $db->fetch($res)) {
    if ($data3[emoney] == "0") continue;
	$loop[] = $data3;
}

$tpl->assign('emoneydata', $emoneydata);
$tpl->assign('myemoney',$myemoney);
$tpl->assign('expire_emoney',$expire_emoney);
$tpl->assign('loop',$loop);
$tpl->assign('pg',$pg);
$tpl->print_('tpl');

?>