<?

include "../lib/library.php";

header("Content-Type: file/unknown");
header("Content-Disposition: attachment; filename=bill_orderlist".date("YmdHi").".html");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

include "bill_orderlist.php";

ob_start();
$ret = ob_get_contents();
ob_end_clean();

?>
