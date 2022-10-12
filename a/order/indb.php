<?

/*
* @date : 20190124
* @author : kdk
* @brief : 시안관리 기능 추가.
* @request : 태웅.
* @desc : mode:design_draft
* @todo : 
*/

/*
* @date : 20180122 (20180122)
* @author : kdk
* @brief : 주문취소 기능 추가. (case "step1to-9":)
* @request : 김기웅 이사.
* @desc : 기존 무통장에서만 사용하던 취소기능을 다른 결제방식에도 사용.(리스트 페이지 명칭 변경 입금전 취소->취소리스트.) 
* @todo : 
*/

include_once "../lib.php";
include_once dirname(__FILE__) . "/../../pretty/_module_util.php";
//printhome 업로드 주문 20140724   chunter

include "../pod/lib_util_pod_admin.php";

$m_order = new M_order();

/***/
switch ($_REQUEST[mode]) {/***/
    //시안요청 댓글 등록.    
    case "design_addComment":

        $db->start_transaction();
        try {

            $m_modern = new M_modern();
            
            ### form 전송 취약점 개선 20160128 by kdk
            $_POST[comment] = addslashes(base64_decode($_POST[comment]));
            
            $data = array(
                "cid" => "$cid",
                "mid" => "$_POST[mid]",
                "name" => "$_POST[name]",
                "payno" => "$_POST[payno]",
                "comment" => "$_POST[comment]",
                "admin" => "1"
            );
            //debug($data);exit;

            $m_modern->setDesignCommentInsert($data);

            $db->query("commit");
        } catch(Exception $e) {
            $db->query("rollback");
        }
        $db->end_transaction();
        
        msg(_("정상적으로 저장되었습니다."), $_SERVER[HTTP_REFERER]);
        exit;break;
        
    break;
    exit;
    
    //시안관리
    case "design_draft":

        $db->start_transaction();
        try {

            $m_modern = new M_modern();
            
            $bItemStepChg = false;
            $bStorageidUpd = false;
            
            $payno = $_POST[payno];
            $mid = $_POST[mid]; 
            $storageid = $_POST[storageid];
            if (!$storageid) 
            {
                $storageid = CreateStorageId(false);
                $bStorageidUpd = true;
            }
            
            //debug($payno);
            //debug($mid);
            //debug($storageid);
            
            ### form 전송 취약점 개선 20160128 by kdk
            $_POST[response_comment] = addslashes(base64_decode($_POST[response_comment]));
            
            $data = array(
                "response_comment" => "$_POST[response_comment]"
            );

            if ($bStorageidUpd) {
                $data["storageid"] = $storageid;
            }

            $m_modern->setDesignDraftUpdate($payno, $data);
            
            //파일저장.
            //debug($_FILES[file3]);    
            //파일저장.(http://files.ilark.co.kr/portal_upload/estm/file/jqupload.aspx)
            if ($_FILES[file1])
            {
                //파일업로드 및 DB저장.
                SaveFileDesign($_FILES[file1], $storageid, $payno, $mid);
                
                $bItemStepChg = true;
            }            

            if ($_FILES[file2])
            {
                //파일업로드 및 DB저장.
                SaveFileDesign($_FILES[file2], $storageid, $payno, $mid);
                
                $bItemStepChg = true;
            }
            
            if ($_FILES[file3])
            {
                //파일업로드 및 DB저장.
                SaveFileDesign($_FILES[file3], $storageid, $payno, $mid);
                
                $bItemStepChg = true;
            }
            
            if ($bItemStepChg) {
                //상태변경.    
                $m_modern->setDesignDraftStateUpdate($payno, "82");
                    
                //payno 만 있을 경우
                list($itemstep,$ordno,$ordseq) = $db->fetch("select itemstep,ordno,ordseq from exm_ord_item where payno = '$payno'",1);

                if ($itemstep == "81") {
                    //상태변경.
                    chg_itemstep($payno,$ordno,$ordseq,81,82,_("관리자 시안 검수요청 처리"));
                }
            }


            
            $db->query("commit");
        } catch(Exception $e) {
            $db->query("rollback");
        }
        $db->end_transaction();
        
        msg(_("정상적으로 저장되었습니다."), $_SERVER[HTTP_REFERER]);
        exit;break;
    
    case "inspection_file_delete":
        
        try 
        {
            $soapurl = "http://files.ilark.co.kr/portal_upload/estm/file/jqdelete.aspx?";
            //debug($soapurl);
            $param = "storage_code=". $_REQUEST['storage_code'] ."&center_id=". $cfg_center[center_cid] ."&mall_id=". $cid ."&file_name=". $_REQUEST['file_name'];
            //debug($soapurl.$param);
     
            $ret = readUrlWithcurl($soapurl.$param, false);
            //debug($ret);
            
            if ($ret) {
                if (strpos($ret, "true") !== false) {
                    
                    //db 삭제.
                    $m_print = new M_print();
                    $m_print->deletePrintUploadFile($_REQUEST['id']);
                    
                    echo("OK");
                }
                else {
                    echo("FAIL");
                }
            }
            else {
                echo("FAIL");
            }
            
            //msg(_("삭제하였습니다."),$_SERVER[HTTP_REFERER]);
            
            exit;
            break;
        } 
        catch(Exception $e) 
        {
            echo("FAIL");
            exit;
            break;
        }
        
        exit;break;
        
    case "inspection_upload":
        $m_print = new M_print();
        //debug($_POST);

        $optionData = array();
        foreach ($_POST as $ItemKey => $ItemValue) {
            //debug($ItemKey);
            if ($ItemKey == "mode" || $ItemKey == "storageid" || $ItemKey == "payno" || $ItemKey == "cartno" || $ItemKey == "proc_admin_id" || $ItemKey == "oasis_router_print_direction" || $ItemKey == "request2") continue;

            if (strpos($ItemKey, "_") !== false) {
                $postArr = split('_', $ItemKey);
                //debug($postArr);
                $optionData[$postArr[2]][$postArr[0]] = $ItemValue;
                $optionData[$postArr[2]]['file_type'] = $postArr[1]."_".$postArr[2];
            }
        }
        //debug($optionData);
        //debug($_FILES[file]);

        //파일저장.(http://files.ilark.co.kr/portal_upload/estm/file/jqupload.aspx)        
        foreach ($_FILES[file][name] as $key => $file) {

            if ($file) {
                $optionData[$key]['upload_file_name'] = $_FILES[file]['name'][$key];

                $tmp_name = $_FILES[file]['tmp_name'][$key];
                $filename = $_FILES[file]['name'][$key];
                $filetype = $_FILES[file]['type'][$key];
 
                $data = '@'.$tmp_name. ';filename='.$filename.';type='.$filetype;

                $post   = array('file' => $data,'storage_code' => $_REQUEST[storageid],'center_id' => $cfg_center[center_cid],'mall_id' => $cid);

                $timeout = 30; 
                $curl    = curl_init(); 
        
                curl_setopt($curl, CURLOPT_URL, 'http://files.ilark.co.kr/portal_upload/estm/file/jqupload.aspx'); 
                curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post);

                $ret = curl_exec($curl); 

                //$ret = "{\"files\":[{\"name\":\"thumb-1f6fe105079c4de5848b1a6766ce7f07_1511495778_67_840x840.jpg\",\"size\":\"136369\", \"type\":\"\",\"url\":\"http://files.ilark.co.kr/es_storage/mcdev/mmdev/201807/20180704/20180704-351063/thumb-1f6fe105079c4de5848b1a6766ce7f07_1511495778_67_840x840.jpg\",\"thumbnailUrl\":\"\",\"deleteUrl\":\"http://files.ilark.co.kr/portal_upload/estm/file/jqdelete.aspx?storage_code=20180704-351063¢er_id=mcdev&mall_id=mmdev&file_name=thumb-1f6fe105079c4de5848b1a6766ce7f07_1511495778_67_840x840.jpg\",\"deleteType\":\"GET\"}]}";
                
                //debug($ret);
                if ($ret) {
                    $ret_data = json_decode($ret,1);
                    //debug($ret_data[files][0]);

                    $optionData[$key]['server_file_name'] = $ret_data[files][0]['name'];
                    $optionData[$key]['file_size'] = $ret_data[files][0]['size'];
                    $optionData[$key]['server_path'] = $ret_data[files][0]['url'];
                }

                curl_close ($curl); 
            }
        }    
        //debug($optionData);

        //저장.
        foreach ($optionData as $key => $value) {
            if ($value[server_path]) {
                //debug($value);
                
                $fdata = $m_print->getPrintUploadFileWithFileType($_REQUEST[storageid], $value[file_type]);
                //debug($fdata);
                
                if($fdata) {
                    $m_print->updatePrintUploadFile($_REQUEST[storageid], $value[upload_file_name], $value[file_size], $value[server_path], $value[file_type], $value[machine], $value[folder]);
                }
                else {
                    $m_print->insertPrintUploadFile($_REQUEST[storageid], $value[upload_file_name], $value[file_size], $value[server_path], $value[file_type], $value[machine], $value[folder]);
                }
                                
                //$m_print->setPrintUploadFile($_REQUEST[storageid], $value[upload_file_name], $value[file_size], $value[server_path], $value[file_type], $value[machine], $value[folder]);
            }    
        }

        //-exm_ord_item.proc_admin_id. 
        //-exm_ord_item.oasis_router_print_direction.
        $m_print->setOrdItemEtcInfoWithPrint($_REQUEST[cartno], $sess_admin[mid], $_REQUEST[oasis_router_print_direction]);
        
        //-exm_pay.request2.payno
        $m_print->setPayRequest2WithPrint($_REQUEST[payno], $_REQUEST[request2]); 

        msg(_("완료되었습니다."),$_SERVER[HTTP_REFERER]);
        exit; break;
		
	case "tax_paper_point":
		
		requestTaxPaperToIlark($_REQUEST[account_price], $_REQUEST[account_date]);
		
		msg(_("정상적으로 신청되었습니다."), 'close');
		
	exit; break;
        
    case "reset_pod":

        set_pod_pay($_REQUEST[payno],$_REQUEST[ordno],$_REQUEST[ordseq]);
    
        msg(_("재전송하였습니다."),$_SERVER[HTTP_REFERER]);

    exit; break;

   case "1to2" :
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
         //list($payno,$ordno,$ordseq) = explode("|",$v);
         //chg_itemstep($payno,$ordno,$ordseq,2,3,_("관리자 상품제작중 처리"));
         
         //payno|ordno|ordseq 가 있을 경우.
         if(count(explode("|",$v)) > 1){
            list($payno,$ordno,$ordseq) = explode("|",$v);
            chg_itemstep($payno,$ordno,$ordseq,2,3,_("관리자 상품제작중 처리"));
         }
         else {
            //payno 만 있을 경우
            $query = "select * from exm_ord_item where payno = '$v'";
            $res = $db->query($query);
            while ($data = $db->fetch($res)){
               chg_itemstep($data[payno],$data[ordno],$data[ordseq],2,3,_("관리자 상품제작중 처리"));
            }
         }
      }
   break;

   case "3to4" :
      foreach ($_POST[chk] as $v){
         //list($payno,$ordno,$ordseq) = explode("|",$v);
         //chg_itemstep($payno,$ordno,$ordseq,3,4,_("관리자 상품제작중 처리"));
          
        //payno|ordno|ordseq 가 있을 경우.
        if(count(explode("|",$v)) > 1){
             list($payno,$ordno,$ordseq) = explode("|",$v);
             chg_itemstep($payno,$ordno,$ordseq,3,4,_("관리자 상품제작중 처리"));
        }
        else {
            //payno 만 있을 경우
            $query = "select * from exm_ord_item where payno = '$v'";
            $res = $db->query($query);
            while ($data = $db->fetch($res)){
                chg_itemstep($data[payno],$data[ordno],$data[ordseq],3,4,_("관리자 상품제작중 처리"));
            }
        }
      }
   break;

   case "4to5" :
      $r_shipcomp = get_shipcomp();

      $r_payno = array();

      foreach ($_POST[chk] as $v){

          if (!$_POST[shipcode][$v]) {
              list($shipcode,$shipcomp) = $db->fetch("select shipcode,shipcomp from exm_ord_item where payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'",1);
          }
          else {
              $shipcode = $_POST[shipcode][$v];
              $shipcomp = $_POST[shipcomp][$v];
          }
          

          //payno|ordno|ordseq 가 있을 경우.
          if(count(explode("|",$v)) > 1){
             list($payno,$ordno,$ordseq) = explode("|",$v);
             list($itemstep) = $db->fetch("select itemstep from exm_ord_item where payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'",1);
             if ($itemstep!=4) continue;

             chg_itemstep($payno,$ordno,$ordseq,4,5,_("관리자 출고완료 처리"),$shipcomp,$shipcode);
             $r_payno[$payno][$ordno][$ordseq] = $ordseq;
          }
          else {
            //payno 만 있을 경우
            $query = "select * from exm_ord_item where payno = '$v'";
            $res = $db->query($query);
            while ($data = $db->fetch($res)){
                if ($data[itemstep]!=4) continue;

                chg_itemstep($data[payno],$data[ordno],$data[ordseq],4,5,_("관리자 출고완료 처리"),$shipcomp,$shipcode);
                $r_payno[$data[payno]][$data[ordno]][$data[ordseq]] = $data[ordseq];
            } 
          }
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
		 
		 //20190118 / minks / 배송방법 추가
		 list($ordershiptype) = $db->fetch("select order_shiptype from exm_ord where payno='$payno' order by ordno limit 1", 1);
		 $paydata[ordershiptype] = ($cfg[skin_theme] == "P1" && $ordershiptype == 1) ? $r_order_shiptype[0] : $r_order_shiptype[$ordershiptype];
		 
		 autoMail("shipping", $paydata[orderer_email], $paydata);
		 
		 //관리자에게 보내기
		 autoMailAdmin("admin_shipping", $cfg[email1], $data);
		 
         autoSms("발송완료", $paydata[orderer_mobile], $paydata);
        
         kakao_alimtalk_send($paydata[orderer_mobile],$paydata[mid],_("발송완료"), $paydata);
      }
   break;

   case "91to92" :
      $db->start_transaction();

      foreach ($_POST[chk] as $v){
         chg_paystep($v,91,92,_("관리자 승인완료 처리"));
      }

      $db->end_transaction();
   break;

   case "92to3" :
      foreach ($_POST[chk] as $v){
         //list($payno,$ordno,$ordseq) = explode("|",$v);
         //chg_itemstep($payno,$ordno,$ordseq,2,3,_("관리자 상품제작중 처리"));
         
        //payno|ordno|ordseq 가 있을 경우.
        if(count(explode("|",$v)) > 1){
             list($payno,$ordno,$ordseq) = explode("|",$v);
             chg_itemstep($payno,$ordno,$ordseq,2,3,_("관리자 상품제작중 처리"));
        }
        else {
            //payno 만 있을 경우
            $query = "select * from exm_ord_item where payno = '$v'";
            $res = $db->query($query);
            while ($data = $db->fetch($res)){
               chg_itemstep($data[payno],$data[ordno],$data[ordseq],2,3,_("관리자 상품제작중 처리"));
            }
         }         
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
       $db->query("update exm_ord_item set itemstep = '$changeBox',shipdt = now() where payno = '$chkbox[0]' and ordno = '$chkbox[1]' and ordseq = '$chkbox[2]'");
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
      msg(_("정상 처리되었습니다"),$_SERVER[HTTP_REFERER]);
   break;

case "refund_create":

    $db->start_transaction();

    if ($_POST[memo]){
        $_POST[memo] = date("Y-m-d h:i:s")." - ".$sess_admin[mid]."\\r\\n".$_POST[memo];
        $addfld = ",memo = '$_POST[memo]'";
    }

    $query = "
    insert into exm_refund set
        payno           = '$_POST[payno]',
        request_admin   = '$sess_admin[mid]',
        cash            = '$_POST[cash]',
        pg              = '$_POST[pg]',
        emoney          = '$_POST[emoney]',
        custom          = '$_POST[custom]',
        memo            = '$_POST[memo]',
        requestdt       = now()
    ";
    $db->query($query);
    $refundno = $db->id;

    foreach ($_POST[ea] as $k=>$v){
        list($ordno,$ordseq) = explode("|",$k);
        
        $data = $db->fetch("select * from exm_ord_item where payno = '$_POST[payno]' and ordno = '$ordno' and ordseq = '$ordseq'");

        if ($data[ea] != $v){
            # 수량이 다를경우 아이텝분리
            list($in_ordseq) = $db->fetch("select max(ordseq)+1 from exm_ord_item where payno = '$_POST[payno]' and ordno = '$ordno'",1);
            $in_ordseq = sprintf("%04d",$in_ordseq);

            $saleprice = ($data[saleprice]/$data[ea])*$v;
            $data[b_saleprice] = $data[saleprice] - $saleprice;
            $payprice = $saleprice - ($data[dc_member]*$v);
            $data[b_payprice] = $data[payprice] - $payprice;

            $reserve = ($data[goods_reserve]+$data[areserve]+$data[addopt_areserve]+$data[print_areserve])*$v;
            $data[b_reserve] = $data[reserve] - $reserve;

            $query = "
            update exm_ord_item set
                saleprice   = '$data[b_saleprice]',
                payprice    = '$data[b_payprice]',
                reserve     = '$data[b_reserve]',
                ea          = ea - $v
            where
                payno       = '$_POST[payno]'
                and ordno   = '$ordno'
                and ordseq  = '$ordseq'
            ";
            $db->query($query);

            $query = "
            insert into exm_ord_item set
                payno           = '$_POST[payno]',
                ordno           = '$ordno',
                ordseq          = '$in_ordseq',
                goodsno         = '$data[goodsno]',
                goodsnm         = '$data[goodsnm]',
                optno           = '$data[optno]',
                opt             = '$data[opt]',
                addoptno        = '$data[addoptno]',
                printopt        = '$data[printopt]',
                goods_price     = '$data[goods_price]',
                aprice          = '$data[aprice]',
                addopt_aprice   = '$data[addopt_aprice]',
                print_aprice    = '$data[print_aprice]',
                goods_reserve   = '$data[goods_reserve]',
                areserve        = '$data[areserve]',
                addopt_areserve = '$data[addopt_areserve]',
                print_areserve  = '$data[print_areserve]',
                saleprice       = '$saleprice',
                payprice        = '$payprice',
                ea              = '$v',
                dc_member       = '$data[dc_member]',
                storageid       = '$data[storageid]',
                pods_trans      = '$data[pods_trans]',
                pods_trans_msg  = '$data[pods_trans_msg]',
                review          = '$data[review]',
                reserve         = '$reserve',
                cartno          = '$data[cartno]',
                itemstep        = '$data[itemstep]',
                refundno        = '$refundno',
                refund_ordseq   = '$ordseq'
            ";
            $db->query($query);
            $ordseq = $in_ordseq;

        } else {
            $db->query("update exm_ord_item set refundno = '$refundno' where ordseq = '$ordseq'");
        }

        chg_itemstep($_POST[payno],$ordno,$ordseq,$data[itemstep],11,"관리자 환불접수처리");

    }

    $db->end_transaction();
    $db->close();

    echo "<script>alert('"._("환불데이터 저장이 완료되었습니다.")."');parent.location.reload();parent.closeLayer();</script>";

    exit;
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
   
      msg(_("수정되었습니다."), $_SERVER[HTTP_REFERER]);
   
      //echo "<script>parent.location.reload();</script>";

   exit; break;
   
   case "set_hide":
   
      if (!$_GET[storageid]) break;
   
      if (!$_GET[hide]){
         $data = $db->fetch("select * from exm_edit where storageid = '$_GET[storageid]'");
         if (!$data[storageid]){
            msg(_("존재하지 않는 편집건입니다."),-1);
            exit;
         }
   
         $dt = strtotime(date("Y-m-d")." -$cfg[source_save_days] days");
         $dt2 = strtotime(substr($data[updatedt],0,10));
         if ($dt > $dt2){
            msg(_("보관기간이 경과된 건으로 복구가 불가능합니다."),-1);
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
            setAddEmoney($cid, $refund[mid], $refund[emoney], _("환불에 의한 적립"), $sess_admin[mid], $refund[payno]);
         }
   
         $db->query("update exm_refund set state = 1, complete_admin = '$sess_admin[mid]', completedt = now() where refundno = '$refundno'");
   
         $query = "select * from exm_ord_item where itemstep = 11 and refundno = '$refundno'";
         $res = $db->query($query);
         while ($data = $db->fetch($res)){
            chg_itemstep($data[payno],$data[ordno],$data[ordseq],$data[itemstep],19,_("관리자 환불완료처리"));
         }
      }
   
      $db->end_transaction();
      $db->close();

   break;
   
   case "step2to1":

		chg_paystep($_GET[payno],2,1,_("관리자 미입금확인 처리"));
	
		break;
	
	case "step92to91":
	
	    chg_paystep($_GET[payno],92,91,_("관리자 승인취소 처리"));
	
	    break;

    case "step1to-9":

        chg_paystep($_GET[payno],1,-9,_("관리자 취소 처리"));
        $db->query("update exm_pay set canceldt = now() where payno = '$_GET[payno]'");

        break;

	case "dnExcel":

	if (!$_GET[query] || !$_GET[kind]) exit;

	### form 전송 취약점 개선 20160128 by kdk
	if (isset($_GET[pod_signed]) && isset($_GET[pod_expired])) {
		if (exp_compare($_GET[pod_expired])) {
			$url_query = $_SERVER[PHP_SELF]."?query=".$_GET[query];
			$signedData = signatureData($cid, $url_query);
			
			if (sig_compare($signedData, $_GET[pod_signed])) {
				$query = urldecode(base64_decode($_GET[query]));
			} else {
				msg(_("서버키 검증에 실패했습니다."), $_SERVER[HTTP_REFERER]);
				exit;
			}
		} else {
			msg(_("서버키가 만료되었습니다."), $_SERVER[HTTP_REFERER]);
			exit;
		}
	} else {
		$query = urldecode(base64_decode($_GET[query]));
	}
	### form 전송 취약점 개선 20160128 by kdk	
	
	header("Content-Type: application/vnd.ms-excel"); 
	header("Content-Disposition: attachment; filename=$_GET[kind].".date("YmdHi").".xls");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
	header("Pragma: public");
	echo "<meta http-equiv=content-type content='text/html; charset=UTF-8'>";

	//$res = $db->query(base64_decode($_GET[query]));
	$res = $db->query($query);

   //if ($_SERVER[SERVER_ADDR] == "210.220.150.30" && $_GET[kind] == "invoice") //유니큐브(포토큐브) 112.175.54.251->210.220.150.30 / 송장출력
	if ($_SERVER[SERVER_ADDR] == "115.68.51.151" && $_GET[kind] == "invoice")
        include "inc.excel.$_GET[kind].fotocube.php";
    else 
        include "inc.excel.$_GET[kind].php";

	exit; break;

	case "modify_shipcode":
		$_POST[shipcode] = trim($_POST[shipcode]);
		$db->query("update exm_ord_item set shipcomp = '$_POST[shipcomp]', shipcode = '$_POST[shipcode]' where payno = '$_POST[payno]' and ordno = '$_POST[ordno]' and ordseq = '$_POST[ordseq]'");
		
		echo "<script>alert('"._("수정되었습니다.")."');this.close();opener.location='$_POST[page].php?payno=$_POST[payno]';</script>";
		
	exit; break;

	case "order_cash":
		### 현금영수증 완료 처리	20191217	kkwon
		$data = $m_order->setUpdateDocumentInfo($cid, $_POST[bno]);
		if($data){
			msg(_("처리가 완료되었습니다."), $_SERVER[HTTP_REFERER]);
			exit;
		}else{
			msg(_("처리 오류"), $_SERVER[HTTP_REFERER]);
			exit;
		}
		exit; break;

}/***/

if (!$_POST[rurl])
	$_POST[rurl] = $_SERVER[HTTP_REFERER];
go($_POST[rurl]);
?>