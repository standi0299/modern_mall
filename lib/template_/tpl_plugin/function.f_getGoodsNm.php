<?
function f_getGoodsNm($cartno){
    //debug($cartno);
    //exit;
	global $db,$cid;
	$query = "select b.goodsnm, a.modify_goodsnm from tb_kids_cart_class_mapping a
	           inner join exm_goods b on a.goodsno = b.goodsno where a.master_cartno = '$cartno' group by master_cartno";
    $data = $db->fetch($query);

    if($data[modify_goodsnm]) $goodsnm = $data[modify_goodsnm].'_'.$data[goodsnm];
    else $goodsnm = $data[goodsnm];

	return $goodsnm;
}
?>