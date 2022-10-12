<?

function f_degreeIcon($no=0, $skin_theme = ''){
	if (!$no) return false;

	global $tpl;
	$cfg[skin] = $tpl->skin;

	$dir = "../skin/{$cfg[skin]}/";

	for ($i=0;$i<$no;$i++){
	   if($skin_theme == "M2" || $skin_theme == "M3") $rtn .= "<u class=\"grade\"></u>\r";
      else $rtn .= "<img src='$dir/img/star.gif'>";
	}

	return $rtn;
}

?>