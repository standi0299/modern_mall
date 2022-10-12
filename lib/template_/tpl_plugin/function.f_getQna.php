<?

function f_getQna($limit=1){
	
	global $db, $sess, $cid;
	
	$query = "
	select 
		a.*,b.*,c.goodsnm
	from
		exm_mycs a
		left join exm_goods_cid b on a.goodsno=b.goodsno
		left join exm_goods c on b.goodsno = c.goodsno
	where
		a.cid = '$cid' and id = 'qna' and mid = '$sess[mid]'
	order by a.no desc
	limit $limit
	";
	
	$res = $db->query($query);
	$ret = array();
	while ($data = $db->fetch($res)){
		$ret[] = $data;
	}
	return $ret;

}

?>