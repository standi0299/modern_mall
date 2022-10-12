<?

function f_getCategory2($catno){
	global $db,$cid;
	$r_catno = array();
	$ret = array();

	if (!$catno) return;

	/* 하위 카테고리 여부체크 */
	$query = "select * from exm_category where cid = '$cid' and catno like '$catno%' and length(catno) = length('$catno')+3 and hidden=0 order by sort";
	$res = $db->query($query);
	while ($data = $db->fetch($res)){
    //링크 주소 만들기. (db 부하를 줄이자)  20140403    chunter
    $data[category_link_tag] = get_category_anchor_from_arr($data);

		$ret[$data[catno]] = $data[catnm];
		$ret[$data[catno]] = $data;
	}
	if (!count($ret)){
		$query = "select * from exm_category where cid = '$cid' and catno like '$catno%' and length(catno) = length('$catno') and hidden=0 order by sort";
		$res = $db->query($query);
		while ($data = $db->fetch($res)){
		  $data[category_link_tag] = get_category_anchor_from_arr($data);

			$ret[$data[catno]] = $data[catnm];
			$ret[$data[catno]] = $data;
		}
	}
	//debug($ret);
	return $ret;
}

?>