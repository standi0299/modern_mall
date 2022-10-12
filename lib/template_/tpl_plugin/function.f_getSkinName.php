<?

### 사용중인스킨이름 반환함수
function f_getSkinName(){
	
	global $db, $sess, $cid, $cfg;
	
  if ($cfg[skin])
  {
    return $cfg[skin];
  }
  else 
  { 
  	$query = "
  	select value from
  		exm_config
  	where
  		cid			= '$cid'
  		and config	= 'skin'
  	";
  	list($ret) = $db->fetch($query,1);
  	return $ret;
  }
}

?>