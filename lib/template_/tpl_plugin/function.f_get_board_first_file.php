<?

function f_get_board_first_file($board_id,$no){
	global $db,$cid;

	$query = "select * from exm_board_file where pno = '$no' order by fileno";
	$res = $db->query($query);
	while ($data = $db->fetch($res)){
		
		$src = dirname(__FILE__)."/../../../data/board/$cid/$board_id/$data[filesrc]";
		
		if (is_file($src)){
			$size = getImageSize($src);
			if ($size[1]){
				return "../data/board/$cid/$board_id/$data[filesrc]";
			}
		}
	}

}

?>