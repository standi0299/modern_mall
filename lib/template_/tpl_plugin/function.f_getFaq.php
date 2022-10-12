<?

function f_getFaq($limit=5){
	
	global $cid,$db;
	
	$query = "select * from exm_faq where cid = '$cid' order by sort limit $limit";
	$res = $db->query($query);
	$ret = array();
	while ($data = $db->fetch($res)){
		$ret[] = $data;
	}
	return $ret;

}

?>