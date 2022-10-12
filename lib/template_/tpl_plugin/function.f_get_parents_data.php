<?

function f_get_parents_data($parents){
	global $db,$cid,$cfg;
	if($parents == 'f') $data = _("어머니");
   else $data = _("아버지");
	
   return $data;
}

?>