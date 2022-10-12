<?

function f_getCategoryCnt($catno=0){
	if (!$catno) return;

	global $db,$cid;

	$query = "
	select count(*) from
		exm_goods a
		inner join exm_goods_cid b on a.goodsno = b.goodsno
		inner join exm_goods_link c on a.goodsno = c.goodsno and b.cid = c.cid
	where
		b.cid = '$cid'
		and c.catno like '$catno%'
		and b.nodp = 0
		and state < 2
	";
	list($ret) = $db->fetch($query,1);

	return $ret;
}

?>