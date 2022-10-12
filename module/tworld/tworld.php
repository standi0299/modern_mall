<?
include "../../_header.php";
include "./config.php";

$dc_partnership = $_GET[dc_partnership];

$tpl->assign('dc_partnership',$dc_partnership);
$tpl->print_('tpl');
?>