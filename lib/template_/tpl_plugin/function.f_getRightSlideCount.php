<?

function f_getRightSlideCount($mode, $goodsno = ''){
	global $db,$cid,$sess;

	switch ($mode) {
		case 'pay':
			list($cnt) = $db->fetch("select count(payno) from exm_pay where cid = '$cid' and mid = '$sess[mid]' and paystep not in (0,-1)",1);
         return $cnt;
      break;

      case 'qna' :
         list($cnt) = $db->fetch("select count(no) from exm_mycs where cid = '$cid' and mid = '$sess[mid]' and id = 'cs'",1);
         return $cnt;
      break;
      
      case 'edit' :
         list($cnt) = $db->fetch("select count(a.storageid) from exm_edit a inner join exm_goods b on a.goodsno = b.goodsno where cid = '$cid' and mid = '$sess[mid]' and _hide != 1",1);
         return $cnt;
      break;
      
      case 'wish' :
         list($cnt) = $db->fetch("select count(no) from md_wish_list where cid = '$cid' and mid = '$sess[mid]'",1);
         return $cnt;
      break;
      
      case 'recent_goods' :
         //debug($_COOKIE[today]);
        if ($goodsno)
				{
					list ($chk) = $db->fetch("select goodsno from exm_goods where goodsno = '$goodsno'",1);
				}
				if ($chk) $_COOKIE[today] .= ",".$goodsno."_".$catno;
				if (!$_COOKIE[today]) $_COOKIE[today] = "";
	        
        if($_COOKIE[today]){
            $recent_goods = explode(",", $_COOKIE[today]);
						foreach ($recent_goods as $k => $v) {
							$exp_data = explode("_",$v);
							
							$result[$exp_data[0]] = 1;	
						}
						
            $cnt = count($result);
        } else $cnt = 0;
        return $cnt;
      break;
	}
}

?>