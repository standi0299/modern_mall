<?
include "../_header.php";

$loop = getGoodsAddOption($_GET[goodsno]);

$addoptGroup = getAddoptGroup($_GET[cartno]);

$tpl->assign("loop",$loop);
$tpl->assign("data",$addoptGroup);
$tpl->print_('tpl');
?>