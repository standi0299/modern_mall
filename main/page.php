<?

include "../_header.php";

if ($_GET[type]) {
    $m_config = new M_config();

    $addWhere = "where cid='$cid' and type='$_GET[type]'";
    $data = $m_config->getAddpageInfo($cid, $_GET[type], $addWhere);

    $tpl -> assign('type', $data[msg1]);
}

$tpl->print_('tpl');

?>