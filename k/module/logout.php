<?

include "../lib.php";

setCookie('sess_admin','',0,'/');

if (!$_GET[rurl]) $_GET[rurl] = $_SERVER[HTTP_REFERER];
go($_GET[rurl]);

?>