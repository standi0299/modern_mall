<?

include "../_header.php";

$m_order = new M_order();

$data = $m_order->getPayInfo($_GET[payno]);

if ($data) $tpl->assign($data);
$tpl->print_('tpl');

?>