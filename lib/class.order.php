<?
/*
* @date : 20180620
* @author : chunter
* @brief : 사용하지 않는 class로 추정됨. 전체 검색에서도 사용 흔적이 없음
* @desc : 사용하는곳 발견시 주석으로 남겨지길 바람.
*/
?>
<?
class order {
   var $db;
   var $cid;
   var $sess;

   function order() {
      $this -> db = $GLOBALS[db];
      $this -> cid = $GLOBALS[cid];
      $this -> sess = $GLOBALS[sess];
   }

   //m_cart의 getCartInfo로 대체
   function selectCartData($cartno){
      $data = $this -> db -> listfetch("select * from exm_cart where cartno = '$cartno'");

      return $data;
   }

   function selGoodsOptMallView($goodsno, $optno){
      $opt_view = $this->db->listfetch("select view from tb_goods_opt_mall_view where cid = '$this->cid' and goodsno = '$goodsno' and optno = '$optno'");

      return $opt_view;
   }

   function selGoodsAddoptMallView($goodsno, $addoptno){
      $addopt_view = $this->db->listfetch("select view from tb_goods_addopt_mall_view where cid = '$this->cid' and goodsno = '$goodsno' and addoptno = '$addoptno'");

      return $opt_view;
   }
   
   function getClassChildData($cartno){
      $query = "select a.cartno, b.master_cartno, b.request_date, d.class_name, d.ID as class_ID, e.child_name, e.ID as child_ID from
                  exm_cart a
                  inner join tb_pretty_cart_mapping b on a.cartno = b.cartno
                  inner join tb_pretty_class d on b.class_ID = d.ID
                  inner join tb_pretty_child e on b.child_ID = e.ID
                where a.cartno in ($cartno);";
      
      return $a;
   }
   
   function getAddress(){
      $data2 = $this -> db -> listfetch("select * from exm_address where cid='$this->cid' and mid='' and use_check='Y'");
   }
   
   function step1(){
      include_once "../../lib/nusoap/lib/nusoap.php";

      $query = "select * from exm_ord_item where payno = '$payno'";
      $res = $db->query($query);
      while ($tmp = $db->fetch($res)){
         set_stock($tmp[goodsno],$tmp[optno],$tmp[ea]*-1);
         if ($tmp[storageid]){
            list($podskind)   = $db->fetch("select podskind from exm_goods where goodsno = '$tmp[storageid]'",1);
            if (in_array($podskind,$r_podskind20)){ /* 2.0 상품 */
               $soap_url   = "http://" .PODS20_DOMAIN. "/CommonRef/StationWebService/StationWebService.asmx?WSDL";
            }
            $client = new soapclient($soap_url,true);
            $ret = $client->call('UpdateStorageDate',array("storageid"=>$tmp[storageid]));
         }
      }
   }

   function step2(){
      $query = "select * from exm_ord_item where payno = '$payno'";
      $res = $db->query($query);
      while ($tmp = $db->fetch($res)){
         set_pod_pay($tmp[payno],$tmp[ordno],$tmp[ordseq]);
         set_acc_desc($tmp[payno],$tmp[ordno],$tmp[ordseq],2);
         if($cfg[skin] != "pretty")
            $db->query("delete from exm_cart where cartno = '$tmp[cartno]'");
         set_stock($tmp[goodsno],$tmp[optno],$tmp[ea]*-1);
      }

      order_sms($payno);
   }

   ### 실데이타 저장
   function a(){
      $db->query("update exm_pay set $qr
                     pglog    = '$pglog',
                     pgcode      = '$pgcode',
                     paystep     = '$step',
                     bankinfo = '$bankinfo'
                  where payno='$payno'"
                );

      $db->query("update exm_ord_item set
                     itemstep    = '$step'
                  where payno='$payno'"
                );
   }

   //쿠폰
   function a(){
      $query = "select * from exm_ord_item where payno = '$payno'";
      $res = $db->query($query);
      while ($tmp = $db->fetch($res)){
         if ($tmp[coupon_areservesetno]){
            $db->query("
            update exm_coupon_set set
               coupon_use     = 1,
               payno       = $payno,
               ordno       = $tmp[ordno],
               ordseq         = $tmp[ordseq],
               coupon_usedt   = now()
            where no = '$tmp[coupon_areservesetno]'
            ");
         }

         if ($tmp[dc_couponsetno]){
            $db->query("
            update exm_coupon_set set
               coupon_use     = 1,
               payno       = $payno,
               ordno       = $tmp[ordno],
               ordseq         = $tmp[ordseq],
               coupon_usedt   = now()
            where no = '$tmp[dc_couponsetno]'
            ");
         }
      }
   }

   //주문 실패시
   function a(){
      $query = "select * from exm_ord_item where payno = '$payno'";
      $res = $db->query($query);
      while ($tmp = $db->fetch($res)){
         if ($tmp[coupon_areservesetno]){
            $db->query("
            update exm_coupon_set set
               coupon_use     = 0,
               payno       = null,
               ordno       = null,
               ordseq         = null,
               coupon_usedt   = null
            where no = '$tmp[coupon_areservesetno]'
            ");
         }

         if ($tmp[dc_couponsetno]){
            $db->query("
            update exm_coupon_set set
               coupon_use     = 0,
               payno       = null,
               ordno       = null,
               ordseq         = null,
               coupon_usedt   = null
            where no = '$tmp[dc_couponsetno]'
            ");
         }

         //블루포토 - 결제 실패 시 맵핑 테이블의 상태를 R로 변경해준다.
         if($cfg[skin] == "pretty")
            $db->query("update tb_pretty_cart_mapping set cart_state = 'R' where cartno = '$tmp[cartno]'");
      }
   }
   
   function a(){
      
   }
   
   function a(){
      
   }
   
   function a(){
      
   }
   
   function a(){
      
   }
   
   function a(){
      
   }
   
   function a(){
      
   }
   
   function a(){
      
   }
   
   function a(){
      
   }
   
   function a(){
      
   }
   
   function a(){
      
   }
   
   function a(){
      
   }
   
   function a(){
      
   }
   
   function a(){
      
   }
   
   function a(){
      
   }
   
   function a(){
      
   }
   
   function a(){
      
   }
   
   function a(){
      
   }
   
   function a(){
      
   }
   
   function a(){
      
   }
}
?>