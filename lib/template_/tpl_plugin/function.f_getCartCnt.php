<?

function f_getCartCnt(){

	global $db;

	if ($GLOBALS[sess][mid]) list($ret) = $db->fetch("select count(*) from exm_cart where cid = '$GLOBALS[cid]' and mid = '{$GLOBALS[sess][mid]}'",1);
	else list($ret) = $db->fetch("select count(*) from exm_cart where cid = '$GLOBALS[cid]' and cartkey = '{$_COOKIE[cartkey]}'",1);

	return $ret;

}

?>