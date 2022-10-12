<?
include "../library.php"; 
include "./extra_option_price_proc.php";
$db->query("set names utf8");
   
$return_data = "";
//$return_data = getOptionUnitPrice($cid, $cfg_center[center_cid], $_GET[goodsno], $_GET[unit_cnt]);
$return_data = getOptionUnitPrice($cid, $cfg_center[center_cid], $_REQUEST[goodsno], $_REQUEST[unit_cnt]);

echo $return_data;
?>




