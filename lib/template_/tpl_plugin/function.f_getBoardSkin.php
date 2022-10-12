<?


function f_getBoardSkin($board_id,$limit=5){

	global $cid,$db;

	$query = "select count(*) as cnt , a.board_skin as board_skin from exm_board_set a, exm_board b where a.cid=b.cid and a.board_id=b.board_id and a.cid = '$cid' and a.board_id = '$board_id'";

	$res = $db->query($query);
  $data = $db->fetch($res);

  return $data[board_skin];
}


?>