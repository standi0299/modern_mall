<?

function f_get_degree($num, $mode){
	global $db,$cid,$cfg;
   $a = "";
   if($mode == "M2" || $mode == "M3"){
      for($i = 0; $i < $num; $i++){
         $a .= "<span class=\"grade\"></span>\n";
      }
   } else if($mode == "alaska"){
      for($i = 0; $i < $num; $i++){
         $a .= "<u class=\"grade\"></u>\n";
      }
   }
   
   //debug($a);
   return $a;

}

?>