<?
/*
* @date : 20190402
* @author : kdk
* @brief : 범아(태웅) 롯데리아 우측 메뉴에 사용
* @desc : 
*/
function f_getOrderCnt($type='1')
{
	global $db,$sess,$cid;
    
    $m_pretty = new M_pretty();

    if (!$sess) return 0;

    if($type == "1") {
        //주문완료
        $addWhere = " and a.mid='$sess[mid]' and (select ordseq from exm_ord_item where payno = a.payno and itemstep in (2,92) limit 1) > 0 ";
    }
    else if ($type=="2") {
        //시안접수
        $addWhere = " and a.mid='$sess[mid]' and (select ordseq from exm_ord_item where payno = a.payno and itemstep in (81) limit 1) > 0 ";
    } else if ($type=="3") {
        //제작중
        return $m_pretty->getListCount_modern($cid, " and a.mid='$sess[mid]' and paystep != 0 and paystep != -1 and c.itemstep = '3'", "Y", "Y");        
    } else if ($type=="4") {
        //배송중
        return $m_pretty->getListCount_modern($cid, " and a.mid='$sess[mid]' and itemstep = 4", "Y");
    } else if ($type=="5") {
        //배송완료      
        return $m_pretty->getListCount_modern($cid, " and a.mid='$sess[mid]' and itemstep = 5", "Y");
    }

    if ($type == "1" || $type == "2") {
        $sql = "select a.payno from exm_pay a
            inner join exm_ord b on a.payno = b.payno
            inner join exm_ord_item c on a.payno = c.payno and b.ordno = c.ordno
            where a.cid = '$cid' $addWhere group by a.payno";
//debug($sql);
        return count($db->listArray($sql,1));
    }    
}
?>