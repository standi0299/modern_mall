<?
include "../_header.php";

$query = "select a.*, b.title, c.*, d.goodsnm, d.goodsno from md_jjim a
            inner join exm_edit b on a.storageid = b.storageid
            inner join md_gallery c on a.storageid = c.storageid
            inner join exm_goods d on b.goodsno = d.goodsno
            where a.cid = '$cid' and a.mid = '$sess[mid]'";
$list = $db->listArray($query);
$tpl->assign('list',$list);
$tpl->print_('tpl');
?>