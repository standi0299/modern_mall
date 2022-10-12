<?php
 //require_once "common/dbconn.php";
 $dbconn = mysql_connect("$mysql_host","$mysql_user","$mysql_password") or die("데이터베이스 연결에 실패하였습니다.");
 @mysql_query("set names utf8");
 mysql_select_db($mysql_db, $dbconn);

 $oid = $_GET['P_OID'];  //return_url 뒤에 get방식으로 전달한 주문식별자 (ex: P_RETURN_URL = http://www.inicis.com/mx_rreturn.php?oid=123456)

 if($row[6] == "00") { // 결재가 성공인 경우 DB에 입력
   //echo "결제종류 : " . $paymethod_type . "<br />";
   //echo $row[13]. "원 결제가 성공하였습니다. <br />";

   //if($row[7] =="CARD") echo "승인번호 : " . $row[17];
   if($row[7] =="VBANK") {
     $explode_data = explode('|', $row[14]);
     $aaa = explode('=', $explode_data[0]);
     $bbb = explode('=', $explode_data[1]);
     //echo "입금은행 : " . $row[11]. "<br />";
     //echo "입금계좌 : " . $aaa[1] . "<br />";
     //echo "입금기한 : " . $bbb[1] . "<br />";
   }
   $query = " insert into nux_pay (goods_name, total_price, buy_user, auth_code, pay_type, buy_date, name, email, hp, reg_date) values ('$goodname', '$row[10]', '$row[11]', '$oid', '1', '$oid', '$name', '$email', '$hp',  now()) ";
   $result = mysql_query($query);
  
   if (!$result) die("디비입력에 실패했습니다.");
   else {
     //if ($member['mb_id'] == 'hapyjung') {
     //  echo $query;
     //} else {
       echo("<script>location.href='03_thanks.php'< /script>");
     //}
   }

 } else {
   echo "결제가 실패하였습니다. <br />";
  
 }
 //echo "승인 내역이 존재하지 않습니다. 승인내역 처리가 늦어서 조회되지 않는 경우일 수 있습니다. 개인결제내역 페이지에서 내역을 다시 확인해 주시기 바랍니다.";
 ?>
