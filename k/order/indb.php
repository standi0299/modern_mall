<?
include_once "../lib.php";
include_once dirname(__FILE__) . "/../../pretty/_module_util.php";
//printhome 업로드 주문 20140724   chunter

/***/
switch ($_REQUEST[mode]) {/***/
   case "1to2" :
      include_once "../../lib/nusoap/lib/nusoap.php";
      $client = new soapclient("http://".PODS10_DOMAIN."/StationWebService/StationWebService.asmx?WSDL",true);
      $client->xml_encoding = "UTF-8";
      $client->soap_defencoding = "UTF-8";
      $client->decode_utf8 = false;
   
      $db->start_transaction();
       
      foreach ($_POST[chk] as $v){
         chg_paystep($v,1,2,_("관리자 입금확인 처리"));
   
         $paydata = $db->fetch("select * from exm_pay where payno = '$v'");
   
         $query = "select * from exm_ord_item where payno = '$v'";
         $res = $db->query($query);
         $list = array();
         while ($tmp = $db->fetch($res)){
            $paydata[item][] = $tmp;
         }
      }
      
      $db->end_transaction();
   break;

   case "2to3" :
      foreach ($_POST[chk] as $v){
         list($payno,$ordno,$ordseq) = explode("|",$v);
         chg_itemstep($payno,$ordno,$ordseq,2,3,_("관리자 상품제작중 처리"));
      }
   break;

   case "3to4" :
      foreach ($_POST[chk] as $v){
         list($payno,$ordno,$ordseq) = explode("|",$v);
         chg_itemstep($payno,$ordno,$ordseq,3,4,_("관리자 상품제작중 처리"));
      }
   break;

   case "4to5" :
      $r_shipcomp = get_shipcomp();

      $r_payno = array();
      foreach ($_POST[chk] as $v){
   
         list($payno,$ordno,$ordseq) = explode("|",$v);
         list($itemstep) = $db->fetch("select itemstep from exm_ord_item where payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'",1);
         if ($itemstep!=4) continue;
   
         $shipcode = $_POST[shipcode][$v];
         $shipcomp = $_POST[shipcomp][$v];
         chg_itemstep($payno,$ordno,$ordseq,4,5,_("관리자 출고완료 처리"),$shipcomp,$shipcode);
         $r_payno[$payno][$ordno][$ordseq] = $ordseq;
      }
   
      foreach ($r_payno as $payno=>$ord){
         $paydata = $db->fetch("select * from exm_pay where payno = '$payno'");
   
         foreach ($ord as $ordno=>$seq){
               foreach ($seq as $ordseq=>$item){
               $data = $db->fetch("select * from exm_ord_item where payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'");
               $data[shipcomp] = $r_shipcomp[$data[shipcomp]];
               $paydata[item][] = $data;
            }
         }
       }
   break;

   case "91to92" :
      include_once "../../lib/nusoap/lib/nusoap.php";
      $client = new soapclient("http://".PODS10_DOMAIN."/StationWebService/StationWebService.asmx?WSDL",true);
      $client->xml_encoding = "UTF-8";
      $client->soap_defencoding = "UTF-8";
      $client->decode_utf8 = false;

      $db->start_transaction();

      foreach ($_POST[chk] as $v){
         chg_paystep($v,91,92,_("관리자 승인완료 처리"));
      }

      $db->end_transaction();
   break;

   case "92to3" :
      foreach ($_POST[chk] as $v){
         list($payno,$ordno,$ordseq) = explode("|",$v);
         chg_itemstep($payno,$ordno,$ordseq,2,3,_("관리자 상품제작중 처리"));
      }
   break;
   
   //주문리스트에서 주문상태를 바꿔주기 위해 추가 / 14.02.20 / kjm
   case "stepback_change":
       //기존 변경시스템은 한 주문에 여러개의 상품이 있을때 마지막 상품값만 수정이 되어서 상품순서에 상관없이
       //체크한 상품만 변경되도록 수정 / 14.03.04 / kjm
       //debug($_POST);
       foreach ($_POST[chk] as $k=> $v){
       
       $chkbox = explode("|",$v);
   
       //기존 처리 : 체크한 값에 상관없이 변경시스템 값이 넘어옴
       //변경 후 : 체크한 값에 대한 변경시스템(변경되는 아이템스텝 값) 적용 / 14.04.09 / kjm
       $changeBox = $_POST[changeBox][$v];
       //debug($changeBox); exit;
       list($itemstep) = $db->fetch("select itemstep from exm_ord_item where payno = '$chkbox[0]' and ordno = '$chkbox[1]' and ordseq = '$chkbox[2]'",1);
   
       if ($itemstep!=$chkbox[3]){
           msg(_("주문상태가 다른 요인으로 인해 변경되어있습니다. 확인하시고 다시 진행해주세요."),$_SERVER[HTTP_REFERER]);
           exit;
       }
       //블루팟 상태만 변경하고 podstation, oasis에는 어떤 영향도 미치지 않는다 / 14.10.08 / kjm
       //chg_itemstep 함수 제거, 아이템 상태 업데이트, 로그기록 추가
       $db->query("update exm_ord_item set itemstep = '$changeBox' where payno = '$chkbox[0]' and ordno = '$chkbox[1]' and ordseq = '$chkbox[2]'");
       set_log_step($chkbox[0],$chkbox[1],$chkbox[2],$chkbox[3],$changeBox,$_POST[mid],_('관리자 블루팟 상품 스텝 변경'));
       }
       msg(_("변경되었습니다."),$_SERVER[HTTP_REFERER]);
       exit;

    break;
    
   case "step91to-90":
      chg_paystep($_GET[payno],91,-90,_("관리자 승인반려 처리"));
   
      //결제방식 추출
      list($paymethod) = $db->fetch("select paymethod from exm_pay where payno = '$_GET[payno]'",1);
    
   $db->query("update exm_pay set canceldt = now() where payno = '$_GET[payno]'");

   break;
   
   case "order" :
      //$_POST[zipcode] = implode("-",array_notnull($_POST[zipcode]));

      $query = "
      update exm_pay set
         orderer_name      = '$_POST[orderer_name]',
         payer_name        = '$_POST[payer_name]',
         orderer_phone     = '$_POST[orderer_phone]',
         orderer_mobile    = '$_POST[orderer_mobile]',
         orderer_email     = '$_POST[orderer_email]',
         receiver_name     = '$_POST[receiver_name]',
         receiver_phone    = '$_POST[receiver_phone]',
         receiver_mobile   = '$_POST[receiver_mobile]',
         receiver_zipcode  = '$_POST[zipcode]',
         receiver_addr     = '$_POST[address]',
         receiver_addr_sub = '$_POST[address_sub]',
         request2          = '$_POST[request2]',
         request           = '$_POST[request]',
         return_msg        = '$_POST[return_msg]',
         memo              = '$_POST[memo]'
      where 
         payno = '$_POST[payno]'
      ";
   
      $db->query($query);
   
      msg(_("정상적으로 변경되었습니다."),$_SERVER[HTTP_REFERER]);
   
      exit;
   break;
   
   case "nonmember_order" :

      foreach($_POST[connectName] as $key => $val){
         if($val){
            $db->query("update exm_pay set mid = '$val' where cid = '$cid' and payno = '$key'");
         }
      }
      msg("정상 처리되었습니다",$_SERVER[HTTP_REFERER]);
   break;
   
   case "refund_modify":

      if ($_POST[memo]){
         list($memo) = $db->fetch("select memo from exm_refund where refundno = '$_POST[refundno]'",1);
         if ($memo) $_POST[memo] = $memo."\\r\\n\\r\\n".date("Y-m-d h:i:s")." - ".$sess_admin[mid]."\\r\\n".$_POST[memo];
         else $_POST[memo] = date("Y-m-d h:i:s")." - ".$sess_admin[mid]."\\r\\n".$_POST[memo];
         $addfld = ",memo = '$_POST[memo]'";
      }
   
      $query = "
      update exm_refund set
         cash  = '$_POST[cash]',
         pg    = '$_POST[pg]',
         emoney   = '$_POST[emoney]',
         custom   = '$_POST[custom]'
         $addfld
      where refundno = '$_POST[refundno]'
      ";
      $db->query($query);
   
      msg("수정되었습니다.");
   
      echo "<script>parent.location.reload();</script>";

   exit; break;
   
   case "set_hide":
   
      if (!$_GET[storageid]) break;
   
      if (!$_GET[hide]){
         $data = $db->fetch("select * from exm_edit where storageid = '$_GET[storageid]'");
         if (!$data[storageid]){
            msg("존재하지 않는 편집건입니다.",-1);
            exit;
         }
   
         $dt = strtotime(date("Y-m-d")." -$cfg[source_save_days] days");
         $dt2 = strtotime(substr($data[updatedt],0,10));
         if ($dt > $dt2){
            msg("보관기간이 경과된 건으로 복구가 불가능합니다.",-1);
            exit;
         }
      }
   
      $_hidelog = ($_GET[hide]) ? "admin_del $sess_admin[mid] / ".date("y-m-d h:i:s"):"";
   
      $query = "update exm_edit set _hide = '$_GET[hide]', _hidelog = '$_hidelog' where storageid = '$_GET[storageid]'";
      $db->query($query);

   break;
   
   case "refund_complete":

      $db->start_transaction();
   
      foreach ($_POST[chk] as $refundno){
          
         $refund = $db->fetch("select a.*,b.mid from exm_refund a inner join exm_pay b on a.payno = b.payno where refundno = '$refundno'");
           //debug($refund); exit;
         if ($refund[emoney] && $refund[mid]){
            //set_emoney($refund[mid],"환불에 의한 적립",$refund[emoney],$refund[payno]);
            setAddEmoney($cid, $refund[mid], $refund[emoney], "환불에 의한 적립", $sess_admin[mid], $refund[payno]);
         }
   
         $db->query("update exm_refund set state = 1, complete_admin = '$sess_admin[mid]', completedt = now() where refundno = '$refundno'");
   
         $query = "select * from exm_ord_item where itemstep = 11 and refundno = '$refundno'";
         $res = $db->query($query);
         while ($data = $db->fetch($res)){
            chg_itemstep($data[payno],$data[ordno],$data[ordseq],$data[itemstep],19,"관리자 환불완료처리");
         }
      }
   
      $db->end_transaction();
      $db->close();

   break;
   
   case "step2to1":

		chg_paystep($_GET[payno],2,1,"관리자 미입금확인 처리");
	
		break;
	
	case "step92to91":
	
	    chg_paystep($_GET[payno],92,91,"관리자 승인취소 처리");
	
	    break;

}/***/
if (!$_POST[rurl])
	$_POST[rurl] = $_SERVER[HTTP_REFERER];
go($_POST[rurl]);
?>