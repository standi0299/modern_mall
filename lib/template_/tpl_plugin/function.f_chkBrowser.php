<?

function f_chkBrowser() {
   global $cfg;
   if ($_COOKIE[CLICK_ONE_NON_EXECUTE] == "Y") {
      $browser = "notIE";
   
   //ActiveX 사용하지 않을 경우 무조건 EXE 로 실행하기.            20160509    chunter
   } else if($cfg[AX_editor_use] == "N") {
      $browser = "notIE";
   } else {
      $chkBrowser = getBrowser();
   
      if ($chkBrowser[name] == "Internet Explorer")
         $browser = "IE";
      else
         $browser = "notIE";
   }

   return $browser;
}
?>