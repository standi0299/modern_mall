<?

function f_get_category_sub($catno){

	global $db,$cid,$cfg;
	$loop = array();
	$depth = strlen($catno)/3 + 1;
	$query = "select * from exm_category where cid = '$cid' and catno like '$catno%' and length(catno) >= $depth*3 and hidden=0 order by length(catno),sort";
	$res = $db->query($query);
	while ($data=$db->fetch($res)){
		if (strlen($data[catno])==6) $loop[$data[catno]] = $data;
		else if (strlen($data[catno])==9) $loop[substr($data[catno],0,6)][sub][$data[catno]] = $data;
	}
	return $loop;

}

?>