<?

function f_get_release($rid){
	global $db,$cid;

   
   //개별 배송일 경우 원래 제작사 아이디를 가져온다.
   if(strpos($rid, "_no:")){
      $rid = explode("_no:", $rid);
      $rid = $rid[0];
   }

   //제작사의 착불 방식 가져오기
	list($data) = $db->fetch("select cid from exm_release where rid = '$rid'",1);
   
   return $data;
}

?>