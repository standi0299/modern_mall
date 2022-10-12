<?

function f_getBrandNm($brandno){
	
	global $db;
	list($ret) = $db->fetch("select brandnm from exm_brand where brandno = '$brandno'",1);

	return $ret;

}

?>