<?php
/**
 * cart model
 * 2014.04.09 by chunter
 */

class M_cart {
   var $db;
   function M_cart() {
      $this -> db = $GLOBALS[db];
   }

   function getCartInfo($cid, $cartno) {
      $sql = "select * from exm_cart where cid = '$cid' and cartno = '$cartno'";
      return $this -> db -> fetch($sql);
   }
	 
	 function getCartInfoWithStorageid($cid, $mid, $storageid) {
	 		$addwhere = ($mid) ? "and mid = '{$mid}'" : "";
      $sql = "select * from exm_cart where cid = '$cid' and storageid = '$storageid' $addwhere";
      return $this -> db -> fetch($sql);
   }

   function oldDataDelete($cid, $save_days) {
      $sql = "delete from exm_cart where cid = '$cid' and date_format(updatedt,'%Y-%m-%d') <= adddate(curdate(), interval -$save_days day)";
      $this -> db -> query($sql);
   }

   function delete($cid, $cartno) {
      $sql = "delete from exm_cart where cid = '$cid' and cartno = '$cartno'";
      $this -> db -> query($sql);
   }

   function getUserList($cid, $mid, $cartno) {
      $addwhere = ($mid) ? "and mid = '{$mid}'" : "and mid = '' and cartkey = '$_COOKIE[cartkey]'";
      if (!$mid && !$_COOKIE[cartkey]) {
         $addwhere = "and 0 = 1";
      }

      if ($cartno)
         $addwhere2 = "and cartno in ($cartno)";

      $sql = "select *
      from
          exm_cart 
      where
          cid = '$cid'
          $addwhere
          $addwhere2
      order by cartno desc
      ";
      //echo $sql;
      return $this -> db -> listArray($sql);
   }

   function getUserListWithGoods($cid, $mid, $cartno) {

      $addwhere = ($mid) ? "and mid = '{$mid}'" : "and mid = '' and cartkey = '$_COOKIE[cartkey]'";
      if (!$mid && !$_COOKIE[cartkey]) {
         $addwhere = "and 0 = 1";
      }

      if ($cartno)
         $addwhere2 = "and cartno in ($cartno)";

      $sql = "
        select storageid,b.podskind,b.pods_use
        from
            exm_cart a
            left join exm_goods b on a.goodsno = b.goodsno
        where
            cid = '$cid'
            $addwhere
            $addwhere2
        order by cartno desc
        ";

      return $this -> db -> listArray($sql);
   }

   function updateCartOneField($fieldName, $data, $cartno) {
      $sql = "update exm_cart set $fieldName = '$data' where cartno = '$cartno'";
      $this -> db -> query($sql);
   }

   function updateEditOneField($fieldName, $data, $storageid) {
      $sql = "update exm_edit set $fieldName = '$data' where storageid = '$storageid'";
      $this -> db -> query($sql);
   }

   function checkDuplicateCartNoAndDelete($goodsno, $TOTALCOUNT, $storageid, $cartno) {
      $sql = "select optno from exm_goods_opt where goodsno = '$goodsno' and opt1 = '$TOTALCOUNT'";
      list($optno) = $this -> db -> fetch($sql, 1);
      $sql = "select cartno from exm_cart where goodsno = '$goodsno' and optno = '$optno' and storageid='$storageid'";
      list($chk_duplicate) = $this -> db -> fetch($sql, 1);
      if ($chk_duplicate != $cartno) {
         $this -> db -> query("delete from exm_cart where cartno = '$chk_duplicate'");
      }
   }

   //wpod 관련 편집정보 조회     20140602    chunter
   function getEditorExtInfoList($g_storageid) {
      $sql = "select * from tb_editor_ext_data where g_storage_id='$g_storageid' order by storage_id asc";
      //echo $sql;
      return $this -> db -> listArray($sql);
   }

   //비회원 장바구니 회원으로 변경    20150213  chunter
   function updateGuestCartToUserCart($cid, $mid, $cartkey) {
      $sql = "update exm_cart set mid = '$mid' where cid = '$cid' and mid = '' and cartkey = '$cartkey' and cartkey != ''";
      $this -> db -> query($sql);
   }

   //비회원 편집보관함 회원으로 변경     20150213  chunter
   function updateGuestEditToUserEdit($cid, $mid, $cartkey) {
      $sql = "update exm_edit set mid = '$mid' where cid = '$cid' and mid = '' and editkey = '$cartkey' and editkey != ''";
      $this -> db -> query($sql);
   }

   //장바구니에 담긴 상품들의 상품별 갯수 / 15.10.08 / kjm
   function getCartGoodsCount($cartno) {
      $sql = "select sum(ea) as cnt, goodsno from exm_cart where cartno in ($cartno) group by goodsno";
      return $this -> db -> listArray($sql);
   }

   //장바구니에서 pods 버젼 정보 내역 가져오기
   function getCartListWithPODKind($cid, $cartno, $addwhere = '') {
      if ($cartno)
         $addwhere2 = "and cartno in ($cartno)";

      $sql = "select a.*, 
               case 
               when a.podskind = '' then b.podskind
               when a.podskind is null then b.podskind
               else a.podskind
               end as 'c_podskind',
               case 
               when a.pods_use = '' then b.pods_use
               when a.pods_use is null then b.pods_use
               else a.pods_use
               end as 'c_pods_use'
               #b.podskind,b.pods_use
               from
              exm_cart a
               left join exm_goods b on a.goodsno = b.goodsno
              where
               cid = '$cid'
               $addwhere
               $addwhere2
              order by cartno desc";
      return $this -> db -> listArray($sql);
   }

   //bluephoto 장바구니 리스트 구하기
   function getBluephotoCartListWithPODKind($cid, $cartno, $addwhere = '') {
      if ($cartno)
         $addwhere2 = "and cartno in ($cartno)";

      #복수 편집기 처리 pods_use, podskind, exm_cart 테이블에서 조회 없으면 exm_goods 테이블 사용 2016.03.16 by kdk
      $sql = "select a.*,
               case 
			      when a.podskind = '' then b.podskind
			      when a.podskind is null then b.podskind
			      else a.podskind
      			end as 'c_podskind',
      			case 
      			when a.pods_use = '' then b.pods_use
      			when a.pods_use is null then b.pods_use
      			else a.pods_use
      			end as 'c_pods_use'
              from
               tb_pretty_cart_mapping a
               left join exm_goods b on a.goodsno = b.goodsno
         	  where
               cid = '$cid'
               $addwhere
               $addwhere2          
         	  order by cartno desc";
      return $this -> db -> listArray($sql);
   }

   function setGoodsOptions($storageid, $goods_options) {
      $json_goods_options = json_encode($goods_options);
      $query = "insert into tb_editor_ext_data set storage_id = '$storageid', editor_return_json = '$json_goods_options'";
      $this -> db -> query($query);
   }
   
   function getCartCnt($cid, $mid = '', $cartKey = ''){
      if($mid)
         list($cart_cnt) = $this->db->fetch("select count(cartno) from exm_cart where cid = '$cid' and mid = '$mid'",1);
      else if($cartKey){
         list($cart_cnt) = $this->db->fetch("select count(cartno) from exm_cart where cid = '$cid' and mid = '' and cartkey = '$cartKey'",1);
      }
      
      return $cart_cnt;
   }

}
?>