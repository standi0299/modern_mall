<?

function f_getBoardData($board_id,$limit=5){

	global $cid,$db;
	$loop = array();

	$query = "select *,date_format(regdt, '%m-%d') as regdt_kids  from exm_board where cid = '$cid' and board_id = '$board_id' order by regdt desc limit $limit";
	$res = $db->query($query);

	while ($data = $db->fetch($res)){
		$loop[] = $data;
	}
	return $loop;

}

?>