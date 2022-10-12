<?

function f_get_release_ship_method($rid, $shipcode){
	global $db,$cid,$cfg;

   //개별 배송일 경우 원래 제작사 아이디를 가져온다.
   if(strpos($rid, "_no:")){
      $rid = explode("_no:", $rid);
      $rid = $rid[0];
   }

   //제작사의 착불 방식 가져오기
	list($ship_method) = $db->fetch("select ship_method from exm_release where rid = '$rid'",1);

   $ship_method = explode(",", $ship_method);
//debug($shipcode, $ship_method);
   if(in_array($shipcode, $ship_method))
      return true;
   else
      return false;
}

?>