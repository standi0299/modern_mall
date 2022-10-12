<?
function f_get_goods_list($catno)
{
	global $db,$cfg,$cid,$sess;
	$loop = array();
 
    $where = ($sess[bid]) ? "(bid.bid is null or bid.bid = '$sess[bid]')":"bid.bid is null";  
    $where .= " and b.nodp = 0";
    $where .= " and state < 2";
    $where .= " and c.catno = '$catno'";
    
    if($cfg[skin] == "pretty"){
        list($memberGoods) = $db->fetch("select goodsnos from tb_goods_member_mapping where cid = '$cid' and mid = '$sess[mid]'",1);
        //debug($memberGoods);
        if($memberGoods) $where .= " and a.goodsno in ($memberGoods)";
    }
    
	$query = "select a.*, b.* from
		exm_goods a
		inner join exm_goods_cid b on b.cid = '$cid' and b.goodsno = a.goodsno
    inner join exm_category_link c on c.cid = '$cid' and c.goodsno = a.goodsno
    left join exm_goods_bid bid on bid.cid = '$cid' and bid.goodsno = a.goodsno
	where
    $where
	order by c.catno";
    
	$res = $db->query($query);
	while ($data=$db->fetch($res)){
	
		if(strlen($data[goodsnm]) > 20){			
			$data[goodsnm] = mb_substr($data[goodsnm], 0, 13, "UTF-8");
		}
		else{
			$data[goodsnm] = $data[goodsnm];
		}
		
		$loop[] = $data;
	}
  return $loop;
}

?>