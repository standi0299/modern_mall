<?

function f_getEventList($limit='')
{
	global $db,$cid;

	if ($limit) $limit = "limit $limit";
	$query = "select * from exm_event where cid = '$cid' order by eventno desc $limit";
	$res = $db->query($query);
	while ($data=$db->fetch($res)){
		if (($data[sdate] && $data[sdate]>date("Y-m-d")) || ($data[edate] && $data[edate]<date("Y-m-d"))){
			$data[msg] = "["._("종료")."]";
		} else {
			$data[msg] = "["._("진행")."]";
			$loop[] = $data;
		}
	}
	return $loop;
}

?>