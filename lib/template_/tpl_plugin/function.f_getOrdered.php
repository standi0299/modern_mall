<?

function f_getOrdered(){

	global $sess,$cid,$db;

	if (!$sess[mid]) return;

	$query = "
	select sum(y.payprice) ret from 
		exm_pay x
		inner join exm_ord_item y on y.payno = x.payno
	where 
		cid = '$cid'
		and mid = '$sess[mid]'
		and itemstep>4
	";
	list($ret) = $db->fetch($query,1);

	return $ret;

}

?>