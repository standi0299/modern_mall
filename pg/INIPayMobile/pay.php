<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
 <meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
 <meta name="viewport" content="width=device-width"/>
 <title>INIpayMobile 결제</title>

 <style>
   body, tr, td { font-size:10pt; font-family:돋움,verdana; color:#433F37; line-height:19px; }
   table { border:none; }
   img { border:none; border:0; }
 </style>

 <script type="application/x-javascript">
 addEventListener("load", function() {
   setTimeout(updateLayout, 0);
 }, false);
  
 var currentWidth = 0;
     
 function updateLayout() {
   if (window.innerWidth != currentWidth) {
     currentWidth = window.innerWidth;
     var orient = currentWidth == 320 ? "profile" : "landscape";
     document.body.setAttribute("orient", orient);
     setTimeout(function() {
       window.scrollTo(0, 1);
     }, 100);            
   }
 }

 setInterval(updateLayout, 400);
 </script>

 <script language=javascript>
 window.name = "BTPG_CLIENT";
 var width = 330;
 var height = 480;
 var xpos = (screen.width - width) / 2;
 var ypos = (screen.width - height) / 2;
 var position = "top=" + ypos + ",left=" + xpos;
 var features = position + ", width=320, height=550";
 var date = new Date();
 var date_str = "testoid_"+date.getFullYear()+""+date.getMinutes()+""+date.getSeconds();
 if( date_str.length != 16 ) {
   for( i = date_str.length ; i < 16 ; i++ ) {
     date_str = date_str+"0";
   }
 }
 function setOid() {
   document.ini.P_OID.value = ""+date_str;
 }

 function on_app() {
   var order_form = document.ini;
   var paymethod;
   if(order_form.paymethod.value == "wcard")
     paymethod = "CARD";
   else if(order_form.paymethod.value == "mobile")
     paymethod = "HPP";
   else if(order_form.paymethod.value == "vbank")
     paymethod = "VBANK";
   else if(order_form.paymethod.value == "culture")
     paymethod = "CULT";
   else if(order_form.paymethod.value == "hpmn")
     paymethod = "HPMN";    

   param = "";
   param = param + "mid=" + order_form.P_MID.value + "&";
   param = param + "oid=" + order_form.P_OID.value + "&";
   param = param + "price=" + order_form.P_AMT.value + "&";
   param = param + "goods=" + order_form.P_GOODS.value + "&";
   param = param + "uname=" + order_form.P_UNAME.value + "&";
   param = param + "mname=" + order_form.P_MNAME.value + "&";
   param = param + "mobile=000-111-2222" + order_form.P_MOBILE.value + "&";
   param = param + "paymethod=" + paymethod + "&";
   param = param + "noteurl=" + order_form.P_NOTI_URL.value + "&";
   param = param + "ctype=1" + "&";
   param = param + "returl=" + "&";
   param = param + "email=" + order_form.P_EMAIL.value;
   var ret = location.href="INIpayMobile://" + encodeURI(param);
 }

 function on_web() {
   var order_form = document.ini;
   var paymethod = order_form.paymethod.value;
   var wallet = window.open("", "BTPG_WALLET", features);
  
   if (wallet == null)  {
     if ((webbrowser.indexOf("Windows NT 5.1")!=-1) && (webbrowser.indexOf("SV1")!=-1)) {    // Windows XP Service Pack 2
       alert("팝업이 차단되었습니다. 브라우저의 상단 노란색 [알림 표시줄]을 클릭하신 후 팝업창 허용을 선택하여 주세요.");
     }
     else {
       alert("팝업이 차단되었습니다.");
     }
     return false;
   }
  
   order_form.target = "BTPG_WALLET";
   order_form.action = "https://mobile.inicis.com/smart/" + paymethod + "/";
   order_form.submit();
 }

 function onSubmit() {
   var order_form = document.ini;
   var inipaymobile_type = order_form.inipaymobile_type.value;
   if( inipaymobile_type == "app" )
     return on_app();
   else if( inipaymobile_type == "web" )
     return on_web();
 }
 </script>

 <?php
 $hp = $_POST["hp1"] . '-' . $_POST["hp2"] . '-' . $_POST["hp3"];  // 앞에서 넘어온 전화번호
 $email = $_POST["email"];  // 앞에서 넘어온 주문자 이메일
 $P_OID = time();  // 주문번호

 $price = $POST["price"];     // 앞페이지의 장바구니 또는 앞에서 넘어온 제품가격
 $P_AMT = $price;

 $name = $_POST["name"];  // 앞페이지에서 넘어온 주문자 이름
 $P_UNAME = $name;
 $P_UNAME = iconv("UTF-8","EUC-KR",$P_UNAME);  // 앞페이지가 utf-8 인경우 사용

 $product   = $_POST["product"];  // 앞에서 넘어온 주문 상품명
 $P_GOODS = $product;
 $P_GOODS = iconv("UTF-8","EUC-KR",$P_GOODS);  // 앞페이지가 utf-8 인경우 사용

 $P_NOTI = "";  // 기타주문정보 , 800byte 이내 , 사용법: 변수명=변수값,변수명=변수값

 $P_MID = "INIpayTest";    //상점ID  ,  테스트: INIpayTest
 $P_MNAME = "해피정닷컴";  // 상점이름

 // $P_NEXT_URL : VISA3D (가상계좌를 제외한 기타지불수단) , 인증결과를 브라우저에서 해당 URL로 POST 합니다.
 // $P_NEXT_URL ="https://mobile.inicis.com/smart/testmall/next_url_test.php";
 $P_NEXT_URL ="http://m.happyjung.com.kr/m/pay_save.php";

 // $P_NOTI_URL : 가상계좌, ISP 인증 및 결제 후 상점의 결제 수신 서버URL로 결제 결과를 통보합니다.
 // $P_NOTI_URL = "http://ts.inicis.com/~esjeong/mobile_rnoti/rnoti.php";
 $P_NOTI_URL = "http://m.happyjung.com/m/INIpaypay_save.php";

 // $P_RETURN_URL : ISP 인증 및 결제 완료 후 상점으로 이동하기 위한 APP URL 또는 상점 홈페이지 URL
 // $P_RETURN_URL = "http://ts.inicis.com/~esjeong/mobile_rnoti/rnoti.php";
 $P_RETURN_URL = "http://m.happyjung.com/m/pay_save.php";
 ?>
 </head>

 <body onload="setOid()" topmargin="0"  leftmargin="0" marginwidth="0" marginheight="0">
 <form id="form1" name="ini" method="post" action="" >
 <table border="0" cellpadding="0" cellspacing="0">
   <tr>
     <td height="69" align="center" style="color:#ffffff; font-size:16px; font-weight:bold;">INIpay Mobile 결제요청</td>
   </tr>
   <tr>
     <td>
       주문자명: <input type="text" name="name" value="<?php echo $name; ?>" /><br />
       상품이름: <input type="text" name="goods" value="<?php echo $product; ?>" /><br />
       휴대전화: <input type="text" name="hp" value="<?php echo $hp; ?>" /><br />
       제품가격: <input type="text" name="price" value="<?php echo $price; ?>" />
     </td>
   </tr>
   <tr>
     <td height="39" align="center" onClick="javascript:onSubmit();"><img src="INIpayMobile/btn_confirm.png" width="55" height="29" /></td>
   </tr>
 </table>

   <!-- 이니시스 카드결재관련 -->


   <input type="hidden" name="inipaymobile_type" value="web" />
   <input type="hidden" name="P_OID" value="<? echo $P_OID;?>" />
   <input type="hidden" name="P_GOODS" value="<?php echo $P_GOODS; ?>" />
   <input type="hidden" name="P_AMT" value="<?php echo $P_AMT; ?>" />
   <input type="hidden" name="P_UNAME" value="<?php echo $P_UNAME; ?>" />
   <input type="hidden" name="P_MNAME" value="<?php echo $P_MNAME; ?>" />
   <input type="hidden" name="P_MOBILE" value="<?php echo $P_MOBILE; ?>" />
   <input type="hidden" name="P_EMAIL" value="<?php echo $P_EMAIL; ?>" />
   <input type="hidden" name="paymethod" value="wcard" />
   <input type="hidden" name="P_MID" value="<?php echo $P_MID; ?>" />
   <input type="hidden" name="P_NEXT_URL" value="<?php echo $P_NEXT_URL; ?>" />
   <input type="hidden" name="P_NOTI_URL" value="<?php echo $P_NOTI_URL; ?>" />
   <input type="hidden" name="P_RETURN_URL" value="<?php echo $P_RETURN_URL; ?>" />
   <input type="hidden" name="P_HPP_METHOD" value="1" />
 </form>
 </body>
 </html>
