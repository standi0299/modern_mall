<?

function f_popup(){

	global $db,$cid,$cfg;
	
	//20171211 / minks / 팝업 적용할 스킨 체크(m_default : mobile / 그 외 스킨 : pc)
	$skintype = ($cfg[skin] == "m_default") ? "mobile" : "pc";
	
	$div = explode(",",$_COOKIE[mainpopup]);

	$query = "select * from exm_popup where cid = '$cid' and state = 1 and skintype like '%$skintype%' and sdt <= curdate() and edt >= curdate()";
	$res = $db->query($query);
	$ret = array();
	while ($data=$db->fetch($res)){

		if (!in_array("mainpopup_".$data[popupno],$div)) $ret[] = $data;
	}

	return $ret;
}

?>