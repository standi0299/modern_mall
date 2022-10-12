<?
ob_start();

include_once "../lib/library.php";

$unique_key = $_REQUEST[unique_key];
setCookie('cartkey',$unique_key,0,'/');
$_COOKIE[cartkey] = $unique_key;

if ($_COOKIE[cartkey]) echo $_COOKIE[cartkey];

ob_end_flush();
?>