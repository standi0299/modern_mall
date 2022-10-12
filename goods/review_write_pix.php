<?

//include "../_pheader.php";
include "../_header.php";

chkMember();
$m_member = new M_member();
$m_order = new M_order();

$list_payno = $db->listArray("select a.payno, b.ordno, b.ordseq, b.goodsnm, b.catno, b.goodsno from exm_pay a
                              inner join exm_ord_item b on a.payno = b.payno
                              where a.cid = '$cid' and a.mid ='$sess[mid]' and a.mid != '' 
                              and a.payno not in (select payno from exm_review where cid = '$cid' and mid ='$sess[mid]')
                              and b.itemstep = '5' group by a.payno order by a.payno desc");

$tpl->assign('list_payno',$list_payno);
$tpl->assign('review',$review);
$tpl->print_('tpl');

?>