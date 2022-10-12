<?
include "../_header.php";
include "../lib/class.page.php";

if (!$_GET[guest]) chkMember();

$m_goods = new M_goods();
$list = $m_goods->get_goods_poke_list($cid, $sess[mid]);

//debug($list);

$tpl->assign("list",$list);
$tpl->print_('tpl');

?>