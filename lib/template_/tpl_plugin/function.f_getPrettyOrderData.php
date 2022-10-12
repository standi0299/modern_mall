<?
function f_getPrettyOrderData($data, $mode) {
    global $db;
    
    switch ($mode){
        case "cnt":
            list($cnt) = $db->fetch("select count(payno) as cnt from exm_ord_item where payno = '$data'",1);
            //debug($cnt);
            return $cnt;

            break;

        case "payprice":
            list($payprice) = $db->fetch("select payprice from exm_pay where payno = '$data'",1);

            return $payprice;

            break;

        case "saleprice":
            list($saleprice) = $db->fetch("select saleprice from exm_pay where payno = '$data'",1);

            return $saleprice;

            break;
            
        case "shipprice":
            list($shipprice) = $db->fetch("select shipprice from exm_pay where payno = '$data'",1);

            return $shipprice;

            break;
        }
}
?>