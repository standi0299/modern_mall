<?
function f_get_goods_list_for_pretty() {
   global $db,$cfg,$cid,$sess;

   $m_pretty = new M_pretty();
   $mappingData = $m_pretty -> getMemberGoodsMapping($cid, $sess[mid], $_COOKIE[season_code]);
   foreach($mappingData as $val){
      $goodsno[] = $val[goodsno];
   }
   
   if($goodsno)
      $goodsno = implode(",", $goodsno);

   if ($goodsno) {
      $loop = array();

      $where = ($sess[bid]) ? "(bid.bid is null or bid.bid = '$sess[bid]')":"bid.bid is null";
      $where .= " and b.nodp = 0";
      $where .= " and state < 2";
      $where .= " and a.goodsno in ($goodsno)";

      $query = "select max(c.catno) as max_catno, a.*, c.* from
                 exm_goods a
                 inner join exm_goods_cid b on b.cid = '$cid' and b.goodsno = a.goodsno
                 inner join exm_category_link c on c.cid = '$cid' and c.goodsno = a.goodsno
                 inner join exm_category d on c.catno = d.catno and d.cid = '$cid'
                 left join exm_goods_bid bid on bid.cid = '$cid' and bid.goodsno = a.goodsno
                where
                 $where
                 group by a.goodsno
                 order by d.sort, a.goodsno";
      $res = $db->query($query);
      while ($data=$db->fetch($res)) {
         $category_arr[$data[max_catno]] = $data[max_catno];

         /*
         //길이 제한 삭제 / 15.09.21 / kjm
         if(strlen($data[goodsnm]) > 40){
            $data[goodsnm] = mb_substr($data[goodsnm], 0, 30, "UTF-8");
         } else {
            $data[goodsnm] = $data[goodsnm];
         }
         */
         $loop[$data[max_catno]][] = $data;
      }
      if ($category_arr) {
         foreach ($category_arr as $key => $value) {
            $data[goods_data] = $loop[$key];
            $data[catno] = $key;
            //전체 카테고리 스텝을 표현하도록 변경        20150713    chunter
            $data[cate_full_name] = getCatnm($key);
            $category[$key] = $data;
         }
         //debug($category);
         return $category;
      }
   }
}
?>