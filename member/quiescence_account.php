<?

include_once "../_header.php";
include_once "../lib/class.db.mysqli.php";

$Mysqli = new DBMysqli(dirname(__FILE__)."/../conf/conf.db.security.php");

list($email) = $Mysqli->fetch("select email from exm_member where cid = '$cid' and mid = '$_GET[mid]' and center_cid = '$cfg_center[center_cid]'", 1);

$tpl->assign('email',$email);
$tpl->print_('tpl');

?>