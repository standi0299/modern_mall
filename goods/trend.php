<?
include "../_header.php";
//검색 리스트.

$query = "select *, count(b.goods_like) as cnt from exm_goods a
            inner join md_goods_like b on a.goodsno = b.goodsno
          where b.cid = '$cid' and b.goods_like = 'Y'  group by a.goodsno order by cnt desc
         ";

$list = $db->listArray($query);

$tpl->assign("list",$list);
$tpl->print_('tpl');
?>