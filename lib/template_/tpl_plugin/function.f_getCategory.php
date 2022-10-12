<?

function f_getCategory($catno){

	global $db,$cid;
	$r_catno = array();
	$ret = "";

	for ($i=0;$i<strlen($catno)/3;$i++){
		$r_catno[] = substr($catno,0,($i+1)*3);
	}

	if (array_notnull($r_catno)){
		$query = "select * from exm_category where cid = '$cid' and catno in (".implode(",",$r_catno).") order by length(catno),sort";
		$res = $db->query($query);
		$ret = array();
		while ($data=$db->fetch($res)){
			$ret[] = $data[catnm];
		}
	}

	return $ret;
}

?>