<?

function f_brandnm($brandno,$flds="brandnm"){
	global $db;

	list($data[brandnm],$data[brandnm2]) = $db->fetch("select brandnm,brandnm2 from exm_brand where brandno = '$brandno'",1);

	$ret = $data[brandnm];
	if ($flds=="brandnm2" && $data[brandnm2]) $ret = $data[brandnm2];

	if ($ret) return "[".$ret."]";
	else return;
}

?>