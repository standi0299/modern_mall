<?
function f_getKidsClassNm($class_code){
    global $db,$cid;
    
    list($class_name) = $db->fetch("select class_name from tb_kids_class where class_code = '$class_code' and cid = '$cid'",1);
    
    return $class_name;
}
?>