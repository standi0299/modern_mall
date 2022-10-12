<?

function f_getReviewData($limit=5){
	
	global $db, $sess, $cid;
	
	$query = "
	select
		*
	from
		exm_review
	where
		cid = '$cid'
		and review_deny_admin = 0
		and review_deny_user = 0
	order by no desc
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