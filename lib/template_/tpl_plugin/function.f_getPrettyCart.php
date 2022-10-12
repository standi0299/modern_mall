<?
function f_getPrettyCart($data, $mode) {
    global $db;
    
    switch ($mode){
        ### 입점사 삭제
        case "goodsnm":
            list($goodsnm) = $db->fetch("select b.goodsnm from exm_cart a
                                        inner join exm_goods b on a.goodsno = b.goodsno
                                        where a.cartno = '$data'",1);
            return $goodsnm;
    
            break;
            
        case "masterTemp":
            list($masterTemp) = $db->fetch("select title from exm_cart where cartno = '$data'",1);
            return $masterTemp;
    
            break;
        }
}
?>