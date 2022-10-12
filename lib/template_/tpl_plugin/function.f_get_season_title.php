<?

function f_get_season_title(){
	global $cid,$db;
	$m_pretty = new M_pretty();
	//쿠키에 시즌이 있을 때
   if($_COOKIE[season_code]){
      $season_code = $_COOKIE[season_code];
   }else{
      //기본시즌이 있으면 기본시즌을
      //없으면 가장 최근에 등록한 시즌을 보여준다.
      $now = date('Y-m-d');
      $season_code = $m_pretty->getBasicSeasonCode($cid, $now);
   }

   $season_data = $m_pretty->getSeasonData($cid, $season_code);
   $title = $season_data[title];
	return $title;

}

?>