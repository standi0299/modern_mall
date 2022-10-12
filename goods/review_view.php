<?

//include "../_pheader.php";
include "../_header.php";

$m_member = new M_member();
$m_goods = new M_goods();
$m_order = new M_order();

$data = $m_member->getReviewInfo($cid, $_GET[payno], $_GET[ordno], $_GET[ordseq]);
$goods_data = $m_goods->getInfo($data[goodsno]);
$order_data = $m_order->getPayInfo($data[payno]);

if($data[img])
   $data[img] = explode("|", $data[img]);

$tpl->assign('goods_data', $goods_data);
$tpl->assign('paydt', $order_data[paydt]);
$tpl->assign($data);
$tpl->print_('tpl');

?>