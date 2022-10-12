<?

function f_getMycs($limit=1){
	
	global $db, $sess, $cid;
	
	$query = "select * from exm_mycs where cid = '$cid' and id = 'cs' and mid = '$sess[mid]' order by no desc limit $limit";
	$res = $db->query($query);
	$ret = array();
	while ($data = $db->fetch($res)){
		$ret[] = $data;
	}
	return $ret;

}

?>