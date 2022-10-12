<?
function f_getOrderData($limit=1)
{
    global $db, $sess, $cid;
    //결제실패(itemstep = -1) 상품 제외 / 14.04.23 / kjm
    $query = "
    select
        *,a.payprice
    from
        exm_pay a
        inner join exm_ord b on a.payno = b.payno
        inner join exm_ord_item c on c.payno = a.payno and c.ordno = b.ordno
    where
        a.cid = '$cid' and mid = '$sess[mid]' and itemstep not in (0, -1)
    order by a.payno desc
    limit $limit
    ";

    $res = $db->query($query);
    while ($data=$db->fetch($res)){
        $tmp = array();
        $query = "select goodsnm,opt,sum(ea) ea,count(*) cnt from exm_ord_item where payno='$data[payno]' group by payno";
        $tmp = $db->fetch($query);
        if (!$tmp) continue;
        $payprice = $data[payprice];
        $data = array_merge($data,$tmp);
        $data[payprice] = $payprice;
        $loop[$data[payno]] = $data;
    }
    return $loop;
}
?>