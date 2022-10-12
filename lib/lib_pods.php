<?
function SetPod20PayWithPost($ord_item_data){
    global $db,$r_podskind20;

    $data = $ord_item_data;
    $payno = $data[payno];
    $ordno = $data[ordno];
    $ordseq = $data[ordseq];

    $pay    = $db->fetch("select * from exm_pay where payno = '$payno'");
    //$data   = $db->fetch("select * from exm_ord_item where payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'");
    list($podskind) = $db->fetch("select podskind from exm_goods where goodsno = '$data[goodsno]'",1);
        
    # 업로드 상품일경우
    if ($data[goodsno]=="-2"){
        list($upload_order_code,$upload_order_product_code,$upload_pods_site_id) = $db->fetch("select upload_order_code,upload_order_product_code,upload_pods_site_id from exm_ord_upload where payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'",1);
        $xx = readurl("http://studio.ilark.co.kr/public/set_order_status_update.aspx?pods_site_id=".$upload_pods_site_id."&order_code=".$upload_order_code."&order_product_code=".$upload_order_product_code."&order_status=03");
        return;
    }
  
    if (!$data[storageid]) return;

    if (in_array($podskind,$r_podskind20)){ /* 2.0 상품 */

      $soap_url = "http://".PODS20_DOMAIN."/CommonRef/StationWebService/SetOrderInfo.aspx";

      $pod_data[storageId]      = $data[storageid];                                     // 보관함 코드, 필수 (22자 미만)
      $pod_data[orderUserId]    = $pay[mid];                                            // 서비스 사이트에서 사용하는 사용자 아이디,  선택 (50자 미만)
      //$pod_data[siteOrderCode]  = $data[payno]."_".$data[ordno]."_".$data[ordseq];    // 서비스 사이트에서 생성된 주문번호, 필수 (50자 미만)
      $pod_data[siteOrderCode]  = $data[payno];                                         // 서비스 사이트에서 생성된 주문번호, 필수 (50자 미만)
      $pod_data[orderCount]     = $data[ea];                                            // 주문 수량 , 필수 (숫자)
      $pod_data[orderCost]      = $data[payprice]/$data[ea];                            // 개당 금액
      $pod_data[orderTotalCost] = $data[payprice];                                      // 총 주문 금액
      $pod_data[orderDate]      = trim(str_replace("-","",substr($pay[orddt],0,10)));   // 주문 일자  , 필수 (8자 , yyyymmdd 형식 ex) ”20090417” ) 
      $pod_data[orderTime]      = trim(str_replace(":","",substr($pay[orddt],10,10)));  // 주문 시간  , 필수 (6자 , hhmmss 형식 ex) ”171210” )
      $pod_data[orderUserName]  = $pay[orderer_name];                                   // 주문자 이름 , 필수 (50자 미만)
      $pod_data[orderUserPost]  = str_replace("-","",$pay[orderer_zipcode]);            // 주문자 우편번호 , 선택 (6자)
      $pod_data[orderUserAddr1] = trim($pay[orderer_addr]);                                   // 주문자 주소1 , 선택 (100자 미만)
      $pod_data[orderUserAddr2] = trim($pay[orderer_addr_sub]);                               // 주문자 주소2 , 선택 (50자 미만)
      $pod_data[orderUserTel]   = $pay[orderer_phone];                                  // 주문자 전화번호 , 선택 (20자 미만)
      $pod_data[orderUserHp]    = $pay[orderer_mobile];                                 // 주문자 휴대전화 , 선택 (20자 미만)
      $pod_data[orderUserEmail] = $pay[orderer_email];                                  // 주문자 이메일주소 , 선택 (50자 미만)
      $pod_data[receiverName]   = $pay[receiver_name];                                  // 수취인 이름  , 필수 (50자 미만)
      $pod_data[receiverPost]   = str_replace("-","",$pay[receiver_zipcode]);           // 수취인 우편번호  , 필수 (6자)
      $pod_data[receiverAddr1]  = trim($pay[receiver_addr]);                                  // 수취인 주소1  , 필수 (100자 미만)
      $pod_data[receiverAddr2]  = trim($pay[receiver_addr_sub]);                              // 수취인 주소2  , 필수 (50자 미만)
      $pod_data[receiverTel]    = $pay[receiver_phone];                                 // 수취인 전화번호 , 필수 (20자 미만)
      $pod_data[receiverHp]     = $pay[receiver_mobile];                                // 수치인 휴대전화 , 필수 (20자 미만)
      $pod_data[receiverMemo]   = $pay[request];                                        // 배송메모 , 선택 (100자 미만)
      $pod_data[adminMemo]      = "";                                                   // 관리자메모  , 선택 (100자 미만)
      
      
      if ($data[shiptype]) $shipType = $r_shiptype_pods_deliveryKind[$data[shiptype]];      //배송 방법을 pods로 넘긴다.      20140923  chunter
      if ($shipType) $pod_data[deliveryKind]   = $shipType;                         // 배송방법 , 선택 (2자)
	  else $pod_data[deliveryKind]   = "";											//20141014 / minks / null값일 경우 오류 발생
      
      
      $ret[SetOrderInfoResult] = sendPostData($soap_url, $pod_data);
      
      $ret = explode("|",$ret[SetOrderInfoResult]);
      $r_ret = array("success"=>1,"fail"=>-1);
      if ($ret[0]=="success"){
        $query = "update exm_edit set state = 9 where storageid = '$data[storageid]'";
        $db->query($query);
      }
      if (preg_match("/"._("주문정보가 이미 전송")."/si",$ret[1],$dummy)){
        $ret[0] = "success";
      }
     
      $db->query("update exm_ord_item set pods_trans='{$r_ret[$ret[0]]}',pods_trans_msg='$ret[1]' where payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'");


        $pod_data[storageId]      = $data[storageid];                                       // 보관함 코드, 필수 (22자 미만)
        $pod_data[orderUserId]    = $pay[mid];                                              // 서비스 사이트에서 사용하는 사용자 아이디,  선택 (50자 미만)
        //$pod_data[siteOrderCode]  = $data[payno]."_".$data[ordno]."_".$data[ordseq];      // 서비스 사이트에서 생성된 주문번호, 필수 (50자 미만)
        $pod_data[siteOrderCode]  = $data[payno];                                           // 서비스 사이트에서 생성된 주문번호, 필수 (50자 미만)
        $pod_data[orderCount]     = $data[ea];                                              // 주문 수량 , 필수 (숫자)
        $pod_data[orderCost]      = $data[payprice]/$data[ea];                              // 개당 금액
        $pod_data[orderTotalCost] = $data[payprice];                                        // 총 주문 금액
        $pod_data[orderDate]      = trim(str_replace("-","",substr($pay[orddt],0,10)));     // 주문 일자  , 필수 (8자 , yyyymmdd 형식 ex) ”20090417” )
        $pod_data[orderTime]      = trim(str_replace(":","",substr($pay[orddt],10,10)));    // 주문 시간  , 필수 (6자 , hhmmss 형식 ex) ”171210” )
        $pod_data[orderUserName]  = $pay[orderer_name];                                     // 주문자 이름 , 필수 (50자 미만)
        $pod_data[orderUserPost]  = str_replace("-","",$pay[orderer_zipcode]);              // 주문자 우편번호 , 선택 (6자)
        $pod_data[orderUserAddr1] = trim($pay[orderer_addr]);                                     // 주문자 주소1 , 선택 (100자 미만)
        $pod_data[orderUserAddr2] = trim($pay[orderer_addr_sub]);                                 // 주문자 주소2 , 선택 (50자 미만)
        $pod_data[orderUserTel]   = $pay[orderer_phone];                                    // 주문자 전화번호 , 선택 (20자 미만)
        $pod_data[orderUserHp]    = $pay[orderer_mobile];                                   // 주문자 휴대전화 , 선택 (20자 미만)
        $pod_data[orderUserEmail] = $pay[orderer_email];                                    // 주문자 이메일주소 , 선택 (50자 미만)
        $pod_data[receiverName]   = $pay[receiver_name];                                    // 수취인 이름  , 필수 (50자 미만)
        $pod_data[receiverPost]   = str_replace("-","",$pay[receiver_zipcode]);             // 수취인 우편번호  , 필수 (6자)
        $pod_data[receiverAddr1]  = trim($pay[receiver_addr]);                                    // 수취인 주소1  , 필수 (100자 미만)
        $pod_data[receiverAddr2]  = trim($pay[receiver_addr_sub]);                                // 수취인 주소2  , 필수 (50자 미만)
        $pod_data[receiverTel]    = $pay[receiver_phone];                                   // 수취인 전화번호 , 필수 (20자 미만)
        $pod_data[receiverHp]     = $pay[receiver_mobile];                                  // 수치인 휴대전화 , 필수 (20자 미만)
        $pod_data[receiverMemo]   = $pay[request];                                          // 배송메모 , 선택 (100자 미만)
        $pod_data[adminMemo]      = "";                                                     // 관리자메모  , 선택 (100자 미만)
        $pod_data[deliveryKind]   = "";                                                     // 배송방법 , 선택 (2자)        
            
        $ret[SetOrderInfoResult] = sendPostData($soap_url, $pod_data);
        $ret = explode("|",$ret[SetOrderInfoResult]);
        $r_ret = array("success"=>1,"fail"=>-1);
        if ($ret[0]=="success"){
            $query = "update exm_edit set state = 9 where storageid = '$data[storageid]'";
            $db->query($query);
        }
        if (preg_match("/"._("주문정보가 이미 전송")."/si",$ret[1],$dummy)){
            $ret[0] = "success";
        }
        $db->query("update exm_ord_item set pods_trans='{$r_ret[$ret[0]]}',pods_trans_msg='$ret[1]' where payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'");
    }
}
?>