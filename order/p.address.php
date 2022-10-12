<?
include "../_header.php"; chkMember();
include "../lib/class.page.php";

$where[] = "cid = '$cid'";
$where[] = "mid = '$sess[mid]'";

$db_table = "exm_address";
$pg = new Page($_GET[page],5);

$pg->setQuery($db_table,$where);
$pg->exec();
$res = &$pg->resource;
while ($tmp = $db->fetch($res)){
	$tmp[r_receiver_phone]	= explode("-",$tmp[receiver_phone]);
	$tmp[r_receiver_mobile]	= explode("-",$tmp[receiver_mobile]);
	$tmp[r_receiver_zipcode]= $tmp[receiver_zipcode];

	$tmp[r_mobile]			= $r_mobile;	// 전화번호 앞자리
	$tmp[r_phone]			= $r_phone;		// 핸드폰 앞자리
	$tmp[selected][phone][$tmp[r_receiver_phone][0]]	= "selected";	// selected
	$tmp[selected][mobile][$tmp[r_receiver_mobile][0]]	= "selected";	// selected

	$loop[] = $tmp;
}

if ($language_locale == "ja_JP") $tpl->define('jp_add_script',"order/jp_order_script.htm");
else $tpl->define('jp_add_script',"main/blank.htm");

$tpl->assign('pg',$pg);
$tpl->assign('loop',$loop);
$tpl->print_('tpl');
?>