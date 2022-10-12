<?

function f_getCategoryPos($catno,$blt=" > "){
  
	global $db,$cid,$cfg;
	$r_catno = array();

	for ($i=0;$i<strlen($catno)/3;$i++){
		$r_catno[] = substr($catno,0,($i+1)*3);
	}

	if (count($r_catno) < 1) return $ret;

	$query = "select * from exm_category where cid = '$cid' and catno in (".implode(",",$r_catno).") order by length(catno)";
	$res = $db->query($query);
	$ret = array();
	while ($data=$db->fetch($res)){
      //$ret[] = get_category_anchor($data[catno]).$data[catnm]."</a>";
		
		//링크 주소 만들기. (db 부하를 줄이자)  20140403    chunter
		if($cfg[skin_theme] == "M2" || $cfg[skin_theme] == "M3")
         $ret[] = "<li>".get_category_anchor_from_arr($data).$data[catnm]."</a></li>";
      else
         $ret[] = get_category_anchor_from_arr($data).$data[catnm]."</a>";
	}
   
   if($cfg[skin_theme] == "M2" || $cfg[skin_theme] == "M3")
      $blt = "<li>&gt;</li>";
   
	return implode($blt,$ret);
}

?>