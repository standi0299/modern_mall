<?
function f_getKidsChildNm($child_code){
    global $db,$cid;
    
    list($child_name) = $db->fetch("select child_name from tb_kids_child where child_code = '$child_code'",1);
    
    return $child_name;
}
?>