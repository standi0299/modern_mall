<?
function f_getPrettyTempCopyData($data, $mode) {
    global $db;
    
    switch ($mode){
        
    ### 입점사 삭제
    case "master":
        list($title) = $db->fetch("select title from exm_cart where cartno = '$data'",1);
        return $title;

        break;
        
    case "class":
        list($class) = $db->fetch("select class_name from tb_pretty_class where ID = '$data'",1);
        return $class;
        
        break;
    }
}
?>