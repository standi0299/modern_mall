<?php
/**
 * cash model
 * 2017.03.23 by chunter
 */

class M_cash_receipt {
   var $db;
   function M_cash_receipt() {
      $this -> db = $GLOBALS[db];
   }

   function getInfo($cid, $payno) {
      $sql = "select * from tb_cash_receipt where cid = '$cid' and payno = '$payno'";
      return $this -> db -> fetch($sql);
   }

   //비회원은 mid가 없으므로 조건에서 제외.
   function setOrderReceiptComplete($cid, $payno, $receipt_ID) {
      $sql = "update exm_pay set receipt_ID='$receipt_ID' where  cid = '$cid' and payno = '$payno'";
      $this -> db -> query($sql);
   }

   function setInsert($cid, $mid = '', $payno, $receipt_money, $status, $status_log, $memo = '') {
      $sql = "INSERT INTO tb_cash_receipt(cid, mid, payno, receipt_money, status, response_log, memo, regist_date) VALUES
              ('$cid', '$mid', '$payno','$receipt_money', '$status', '$status_log', '$memo', now());";
      $this -> db -> query($sql);
      return $this -> db -> id;
   }
   
   function setStatus($cid, $payno, $status, $pglog){
      $sql = "update tb_cash_receipt set status = '$status', response_log = '$pglog' where cid = '$cid' and payno = '$payno'";
      return $this -> db -> query($sql);
   }
}
?>