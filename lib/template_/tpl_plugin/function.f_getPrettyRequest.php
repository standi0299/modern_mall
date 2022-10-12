<? //사용하지 않음 / 16.05.31 / kjm
function f_getPrettyRequest($data, $mode) {
    global $db;
    
    switch ($mode){
        ### 입점사 삭제
        case "requestDate":
            list($requestDate) = $db->fetch("select request_date from tb_pretty_cart_mapping where cartno = '$data'",1);
            return $requestDate;

            break;

        case "className":
            list($className) = $db->fetch("select b.class_name from tb_pretty_cart_mapping a 
                                           inner join tb_pretty_class b on a.class_ID = b.ID
                                           where a.cartno = '$data'",1);
            return $className;
    
            break;
            
        case "childName":
            list($childName) = $db->fetch("select b.child_name from tb_pretty_cart_mapping a 
                                           inner join tb_pretty_child b on a.child_ID = b.ID
                                           where a.cartno = '$data'",1);
            return $childName;

            break;
            
        case "goodsnm":
            list($goodsnm) = $db->fetch("select b.goodsnm from exm_cart a
                                        inner join exm_goods b on a.goodsno = b.goodsno
                                        where a.cartno = '$data'",1);
            return $goodsnm;
    
            break;
            
        case "title":
            list($title) = $db->fetch("select title from exm_cart where cartno = '$data'",1);
            return $title;
    
            break;
            
        case "orddt":
            list($orddt) = $db->fetch("select orddt from exm_pay where payno = '$data'",1);
            return $orddt;
    
            break;

        case "returnMsg":
            list($returnMsg) = $db->fetch("select return_msg from exm_pay where payno = '$data'",1);
            return $returnMsg;
    
            break;
        }
}
?>