<?
/*
* @date : 20181101
* @author : kdk
* @brief : pod관련 함수.
* @request : 
* @desc :
* @todo :
*/

include "../lib.php";
include "lib_util_pod_admin.php";

$m_pod = new M_pod();
$m_config = new M_config();
$m_mall = new M_mall();
$m_etc = new M_etc();

/* order_list_pod.php 에서 호출 */
switch ($_POST[mode]) {
    case "91to92" : //관리자 승인완료 처리
        $db->start_transaction();

        foreach ($_POST[chk] as $v){
            chg_paystep($v,91,92,_("관리자 승인완료 처리"));
        }

        $db->end_transaction();
        break;

    case "92to3" : //관리자 상품제작중 처리 : 접수완료
        $db->start_transaction();
        foreach ($_POST[chk] as $v){
            //list($payno,$ordno,$ordseq) = explode("|",$v);
            //chg_itemstep($payno,$ordno,$ordseq,2,3,_("관리자 상품제작중 처리"));
        
            //접수완료 변경.
            $m_pod->setPodPayReceiptUpdate($cid,$v,"2",$_POST[receipt_dt],$_POST[receiptadmin],$sess_admin[mid]); //'2' => "접수완료"

            chg_paystep($v,91,92,_("관리자 승인완료 처리")); //관리자 승인완료 처리
            
            $query = "select * from exm_ord_item where payno = '$v'";
            $res = $db->query($query);
            while ($data = $db->fetch($res)){
                chg_itemstep($data[payno],$data[ordno],$data[ordseq],2,3,_("관리자 상품제작중 처리"));
            }
   
        }
        
        $db->end_transaction();
        break;
}

/* order_detail_popup.php 에서 호출 */
switch ($_POST[mode]) {
    case "order" :
        //$db->start_transaction();
        try {

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
            
            //if (!$_POST[receiptdt]) $_POST[receiptdt] = "0000-00-00";
            //if (!$_POST[deliverydt]) $_POST[deliverydt] = "0000-00-00";
            if (!$_POST[extopt_flag]) $_POST[extopt_flag] = "0";

            //주문저장 pod_pay data.
            $podpay[cid] = $cid;
            $podpay[mid] = $_POST[mid];
            $podpay[payno] = $_POST[payno];
            $podpay[payprice] = $_POST[payprice];
            $podpay[deposit_price] = $_POST[deposit_price];
            $podpay[pre_deposit_price] = $_POST[pre_deposit_price];
            $podpay[vat_price] = $_POST[vat_price];
            $podpay[ship_price] = $_POST[ship_price];
            $podpay[remain_price] = $_POST[remain_price];
            $podpay[manager_no] = $_POST[manager_no];
            $podpay[receiptdt] = $_POST[receiptdt];
            $podpay[receiptadmin] = $_POST[receiptadmin];
            $podpay[deliverydt] = $_POST[deliverydt];
            $podpay[deliveryadmin] = $_POST[deliveryadmin];
            $podpay[autoproc_flag] = $_POST[autoproc_flag];
            $podpay[memo] = $_POST[memo];
            $podpay[update_mid] = $sess_admin[mid];
            $podpay[order_title] = $_POST[order_title];
            $podpay[order_data] = $_POST[order_data];
            $podpay[extopt_flag] = $_POST[extopt_flag];
            $podpay[order_type] = $_POST[order_type];
            //주문저장 pod_pay.
            //debug($podpay);
            $m_pod->setPodPayUpdate($podpay);
            
            if ($_POST[status_origin] != $_POST[status]) {
                //주문상태변경.
                $m_pod->setPodPayStatusUpdate($cid, $_POST[mid], $_POST[payno], $podpay[status]);
            }            
            
            //파일업로드 및 DB저장.
            //debug($_FILES[file]);
            if ($_POST[storageid]) {
                $storageid = $_POST[storageid];
            }
            else {
                $storageid = CreateStorageId();
            }
        
            //파일저장.(http://files.ilark.co.kr/portal_upload/estm/file/jqupload.aspx)
            if ($_FILES[file])
            {
                SaveFile($_FILES, $storageid, $_POST[payno], $_POST[mid]);
            }
//exit;    
            msg(_("정상적으로 변경되었습니다."),$_SERVER[HTTP_REFERER]);
        
            //$db->query("commit");
        } catch(Exception $e) {
            //$db->query("rollback");
            msg(_("오류가 발생했습니다."),$_SERVER[HTTP_REFERER]);
            exit;
        }
        //$db->end_transaction();

       exit;
	   break;
       
    case "modify_shipcode":
        $_POST[shipcode] = trim($_POST[shipcode]);
        $db->query("update exm_ord_item set shipcomp = '$_POST[shipcomp]', shipcode = '$_POST[shipcode]' where payno = '$_POST[payno]' and ordno = '$_POST[ordno]' and ordseq = '$_POST[ordseq]'");
        
        msg(_("정상적으로 변경되었습니다."),$_SERVER[HTTP_REFERER]);
        
        exit;
        break;
}

/* deposit_list.php 에서 호출 */
switch ($_POST[mode]) {
    case "member_deposit_proc" :
        
        if (!trim($_POST[mid])) {
            msg(_("아이디가 입력되지 않았습니다."), -1);
            exit ;
        }

        $db->start_transaction();
        try {
            ###선택회원일괄입금처리.            
            $mids = explode(",", $_POST[mid]);
            if ($mids) {
                foreach ($mids as $key => $val) {
                    //사용가능한 선입금 조회.
                    $depositMoney = $m_pod->getDepositMoney($cid, $val);
                    //$depositMoney = 200000;
                    //debug($depositMoney);
                    
                    if ($depositMoney <= 0) continue;
                    
                    //주문리스트 조회 (미수금(remain_price>0)이 있고 자동입금처리제외(autoproc_flag=0)가 아닌 주문) pod_pay.
                    $pay_list = $m_pod->getPodPayList($cid, $val, "and remain_price>0 and autoproc_flag=0", "order by orddt");
                    
                    if ($pay_list) {
                        foreach ($pay_list as $k => $v) {
                            if ($depositMoney <= 0) continue;
                            //debug($v[remain_price]);
                            if ($depositMoney >= $v[remain_price]) {
                                $depositMoney = $depositMoney - $v[remain_price];
                                $remain_price = 0;
                                $deposit_price = $v[remain_price];
                            }
                            else {                       
                                $remain_price = $v[remain_price] - $depositMoney;
                                $deposit_price = $depositMoney;
                                $depositMoney = 0;
                            }

                            //debug("depositMoney : ".$depositMoney . ",deposit_price : ".$deposit_price . ",remain_price : ".$remain_price);
                            
                            //입금처리.
                            $podpay[cid] = $cid;
                            $podpay[mid] = $val;
                            $podpay[payno] = $v[payno];
                            $podpay[deposit_price] = $deposit_price; //선입금.
                            $podpay[remain_price] = $remain_price; //미수금.
                            //debug($podpay);
                            $m_pod->setPodPayDepositProc($podpay);
            
                            //로그저장(선입금사용).
                            $log[cid] = $cid;
                            $log[mid] = $val;
                            $log[admin] = $sess_admin[mid];
                            $log[memo] = "[$v[payno]][선입금액]사용.";
                            $log[deposit_money] = "-$deposit_price";
                            $log[pre_deposit_money] = "0";
                            $log[payno] = $v[payno];
                            $log[status] = "2";
                            //debug($log);
                            $m_pod->depositHistoryInsert($log);
                        }    
                    }
                    else {
                        continue;
                    }                    
                }
            }

            $db->query("commit");
        } catch(Exception $e) {
            $db->query("rollback");
            echo "FAIL";
        }
        $db->end_transaction();

        echo "OK";
        exit;
        
        //msg(_("정상적으로 저장되었습니다."), $_SERVER[HTTP_REFERER]);        
        break;
}

/* remain_member_form_popup.php 에서 호출  */
switch ($_POST[mode]) {
    case "remain_member_form_popup" :
        
        if (!trim($_POST[mid])) {
            msg(_("아이디가 입력되지 않았습니다."), -1);
            exit ;
        }

        $db->start_transaction();
        try {
            //회원미수금현황조회.
            $ret = $m_pod->getMemberRemainStatus($cid, $_POST[mid]);

            $data[cid] = $cid;
            $data[mid] = $_POST[mid];
            $data[start_date] = $_POST[start_date];
            $data[promise_date] = $_POST[promise_date];
            $data[promise_money] = $_POST[promise_money];
            $data[remainadmin] = $_POST[remainadmin];
            $data[memo] = $_POST[memo];
            
            if ($ret) {
                //회원미수금현황수정.
                $data[update_mid] = $sess_admin[mid];

                $m_pod->setMemberRemainStatusUpdate($data);
            }
            else {
                //회원미수금현황등록.            
                $m_pod->setMemberRemainStatusInsert($data);
            }

            $db->query("commit");
        } catch(Exception $e) {
            $db->query("rollback");
        }
        $db->end_transaction();

        msg(_("정상적으로 저장되었습니다."), $_SERVER[HTTP_REFERER]);
        break;
}

/* order_form.php 에서 호출  */
switch ($_POST[mode]) {
    case "order_form_pod" :
        
        if (!trim($_POST[mid])) {
            msg(_("아이디가 입력되지 않았습니다."), -1);
            exit;
        }

        $db->start_transaction();
        try {

            //회원정보 조회.
            $m_member = new M_member();
            $member = $m_member -> getInfo($cid, $_POST[mid]);
            //debug($member);
            
            //회원 데이터가 있으면 로그인
            if($member) {
                $pay[orderer_name]      = $member[name];
                $pay[payer_name]        = $member[name];
                $pay[orderer_phone]     = $member[phone];
                $pay[orderer_mobile]    = $member[mobile];
                $pay[orderer_email]     = $member[email];
                $pay[receiver_name]     = $member[name];
                $pay[receiver_phone]    = $member[phone];
                $pay[receiver_mobile]   = $member[mobile];
                $pay[receiver_zipcode]  = $member[zipcode];
                $pay[receiver_addr]     = $member[address];
                $pay[receiver_addr_sub] = $member[address_sub];
            }
            else {
                msg(_("회원정보가 없습니다."), -1);
                exit;
            }

            $goodsno = $_POST[goodsno];
            $goodsnm = $_POST[goodsnm];
            
            //상품정보 하드코딩.
            //$goodsno = "1837";
            //$goodsnm = "오프라인주문상품";
            
            $totpayprice = 0;//총주문금액 (공급가액+부가세).
            $payprice = 0; //공급가액.
            $ship_price = 0; //배송비.
            $vat_price = 0; //부가세.
            $pre_deposit_price = 0;//선발행입금 사용액.
            
            if(isset($_POST[payprice])) {
                $payprice = $_POST[payprice];
            }

            if(isset($_POST[ship_price])) {
                $ship_price = $_POST[ship_price];
            }
            
            if(isset($_POST[vat_price])) {
                $vat_price = $_POST[vat_price];
            }

            if(isset($_POST[pre_deposit_price])) {
                $pre_deposit_price = $_POST[pre_deposit_price];
            }

            if ($pre_deposit_price < 0) {
                msg(_("선발행입금사용금액은 (-)를 입력할 수 없습니다."), -1);
                exit;
            }
            
            $totpayprice = $payprice + $ship_price + $vat_price;//총주문금액 (공급가액+배송비+부가세).
            
            //주문저장 exm_pay.
            //payno.
            $payno = CreatePayno();
            if (!$payno) {
                msg(_("결제번호가 생성되지 않았습니다."), -1);
                exit;
            }
            else {
                list($chk) = CheckPayno($payno);
                if ($chk) {
                   msg(_("결제번호 중복오류")."\\r\\n"._("결제번호를 다시발급합니다."), -1);
                   exit;
                }
            }

            list($reg_cid, $privatecid, $rid) = $db -> fetch("select reg_cid,privatecid,rid from exm_goods where goodsno = '$goodsno'", 1);
            if ($reg_cid && $reg_cid == $privatecid) {
                $selfgoods = 1;
            } else {
                $selfgoods = 0;
            }

            ### form 전송 취약점 개선 20160128 by kdk
            $_POST[memo] = addslashes(base64_decode($_POST[memo]));

            $pay[cid] = $cid; //상점아이디
            $pay[payno] = $payno; //결제번호
            $pay[mid] = $_POST[mid]; //회원아이디
            $pay[paystep] = "2"; //결제스텝 1:미입금2:입금
            $pay[paymethod] = "t"; //결제수단 t:신용거래
            $pay[payprice] = $totpayprice; //결제액
            $pay[saleprice] = $totpayprice; //매출액
            $pay[shipprice] = $ship_price; //배송비총액
            $pay[request2] = $_POST[memo]; //추가메모
            //debug($pay);

            $ord[payno] = $payno; //결제번호
            $ord[ordno] = "1"; //주문번호
            $ord[rid] = $rid; //출고처아이디
            $ord[ordprice] = $totpayprice; //주문금액
            $ord[shipprice] = $ship_price; //배송비
            $ord[order_shiptype] = ""; //주문배송방법
            
            $item[payno] = $payno; //결제번호
            $item[ordno] = "1"; //주문번호
            $item[ordseq] = "1"; //아이템번호
            $item[goodsno] = $goodsno; //상품번호
            $item[goodsnm] = $goodsnm; //상품명
            $item[saleprice] = $totpayprice; //상품판매가
            $item[payprice] = $totpayprice; //판매가
            $item[ea] = "1"; //판매량
            $item[storageid] = ""; //편집코드
            $item[itemstep] = "2"; //아이템스텝
            $item[item_rid] = $rid; //공급사아이디
            $item[order_inspection] = ""; //
            $item[selfgoods] = $selfgoods; //
            $item[est_order_option_desc] = $_POST[memo]; //주문사양

            $ret = SetPayOrdItem($pay, $ord, $item);
            if (!$ret) {
                msg(_("주문 정보 생성에 오류가 발했습니다. 다시 시도 해주세요."), -1);
                exit;
            }

            //주문저장 pod_pay data.
            $podpay[cid] = $cid;
            $podpay[mid] = $_POST[mid];
            $podpay[payno] = $payno;
            $podpay[admin] = $sess_admin[mid];
            $podpay[payprice] = "$payprice";
            $podpay[deposit_price] = "0";
            $podpay[pre_deposit_price] = "$pre_deposit_price";
            $podpay[vat_price] = "$vat_price";
            $podpay[ship_price] = "$ship_price";
            $podpay[remain_price] = $totpayprice-$pre_deposit_price; //미수금
            $podpay[manager_no] = $_POST[manager_no];
            $podpay[autoproc_flag] = $_POST[autoproc_flag];
            $podpay[memo] = "";
            $podpay[status] = "1";
            $podpay[order_title] = $_POST[order_title];
            $podpay[order_data] = $_POST[memo];
            $podpay[goodsno] = $goodsno;
            $podpay[goodsnm] = $goodsnm;
            $podpay[extopt_flag] = $_POST[extopt_flag];
            $podpay[order_type] = $_POST[order_type];
            
            //선발행입금액을 사용할 경우  data.
            if($pre_deposit_price > 0) {
                //주문저장 pod_pay data.
                //$podpay[remain_price] = $totpayprice-$pre_deposit_price; //미수금    

                //로그 data.
                $log[cid] = $cid;
                $log[mid] = $_POST[mid];
                $log[admin] = $sess_admin[mid];
                $log[memo] = "[$payno][선발행입금액]사용.";
                $log[deposit_money] = "0";
                $log[pre_deposit_money] = "-$pre_deposit_price";
                $log[payno] = $payno;
                $log[status] = "2";
            }
            
            //주문저장 pod_pay.
            $m_pod->setPodPayInsert($podpay);
            
            //파일저장.
            //debug($_FILES[file]);
    
            //파일저장.(http://files.ilark.co.kr/portal_upload/estm/file/jqupload.aspx)
            if ($_FILES[file])
            {
                $m_print = new M_print();
                $storageid = CreateStorageId();
                
                //파일업로드 및 DB저장.
                SaveFile($_FILES, $storageid, $payno, $mid);
            }            
            
            //로그저장.
            if($log) {
                $m_pod->depositHistoryInsert($log);
            }
                    
            $db->query("commit");
        } catch(Exception $e) {
            $db->query("rollback");
        }
        $db->end_transaction();
        
        msg(_("정상적으로 저장되었습니다."), $_SERVER[HTTP_REFERER]);
        break;
}

/* deposit_date_update_popup.php / deposit_form_popup.php 에서 호출  */
switch ($_POST[mode]) {
        
    //입금정보 수정.
    case "deposit_date_update" :
        
        if (!trim($_POST[mid])) {
            msg(_("아이디가 입력되지 않았습니다."), -1);
            exit ;
        }

        $db->start_transaction();
        try {

            $tot_deposit_money = 0;//(선)선입금.
            $deposit_money = 0; //(선)선입금(입금액-선발행입금액).
            $pre_deposit_money = 0; //선발행입금액.
            
            if($_POST[deposit_money]) {
                $tot_deposit_money = $_POST[deposit_money];
                $deposit_money = $_POST[deposit_money];
            }
            
            if($_POST[pre_deposit_money]) $pre_deposit_money = $_POST[pre_deposit_money];
            
            if($pre_deposit_money>0) {
                $deposit_money = $deposit_money - $pre_deposit_money;
            }
            
            //입금저장.
            $data[cid] = $cid;
            $data[mid] = $_POST[mid];
            $data[no] = $_POST[no];
            $data[memo] = $_POST[memo];
            $data[deposit_date] = $_POST[deposit_date];
            $data[deposit_method] = $_POST[deposit_method];
            $data[tot_deposit_money] = $tot_deposit_money;
            $data[deposit_money] = $deposit_money;
            $data[pre_deposit_money] = $pre_deposit_money;
            $data[cashreceipt_date] = $_POST[cashreceipt_date];
            $data[taxbill_date] = $_POST[taxbill_date];
            $data[update_mid] = $sess_admin[mid];
            $m_pod->setDepositMoneyDateUpdate($data);

            $db->query("commit");
        } catch(Exception $e) {
            $db->query("rollback");
        }
        $db->end_transaction();

        msg(_("정상적으로 저장되었습니다."), $_SERVER[HTTP_REFERER]);
        break;

    //입금정보 등록.
    case "deposit_form_popup" :
        
        if (!trim($_POST[mid])) {
            msg(_("아이디가 입력되지 않았습니다."), -1);
            exit ;
        }

        $db->start_transaction();
        try {

            $tot_deposit_money = 0;//(선)선입금.
            $deposit_money = 0; //(선)선입금(입금액-선발행입금액).
            $pre_deposit_money = 0; //선발행입금액.
            
            if($_POST[deposit_money]) {
                $tot_deposit_money = $_POST[deposit_money];
                $deposit_money = $_POST[deposit_money];
            }
            
            if($_POST[pre_deposit_money]) $pre_deposit_money = $_POST[pre_deposit_money];

            if ($pre_deposit_money < 0) {
                msg(_("선발행입금액은 (-)를 입력할 수 없습니다."), -1);
                exit;
            }
            
            if($pre_deposit_money>0) {
                $deposit_money = $deposit_money - $pre_deposit_money;
            }
            
            //입금저장.
            $data[cid] = $cid;
            $data[mid] = $_POST[mid];
            $data[admin] = $sess_admin[mid];
            $data[memo] = $_POST[memo];
            //$data[regdt] = $_POST[regdt];
            $data[deposit_date] = $_POST[deposit_date];
            $data[deposit_method] = $_POST[deposit_method];
            $data[tot_deposit_money] = $tot_deposit_money;
            $data[deposit_money] = $deposit_money;
            $data[pre_deposit_money] = $pre_deposit_money;
            $data[cashreceipt_date] = $_POST[cashreceipt_date];
            $data[taxbill_date] = $_POST[taxbill_date];
            $m_pod->setDepositMoneyInsert($data);
            
            //로그저장.
            $log[cid] = $cid;
            $log[mid] = $_POST[mid];
            $log[admin] = $sess_admin[mid];
            $log[memo] = $_POST[memo];
            $log[deposit_money] = $deposit_money;
            $log[pre_deposit_money] = $pre_deposit_money;
            $log[payno] = "";
            $log[status] = "1";
            $m_pod->depositHistoryInsert($log);    
            
            //주문내역 입금처리.
            //입금관리 리스트에서 처리함.            
        
            $db->query("commit");
        } catch(Exception $e) {
            $db->query("rollback");
        }
        $db->end_transaction();

        msg(_("정상적으로 저장되었습니다."), $_SERVER[HTTP_REFERER]);
        break;
}

/* admin_pod.php / admin_write_pod.php 에서 호출  */
switch ($_POST[mode]) {
    case "write" :
        if (!trim($_POST[mid])) {
            msg(_("아이디가 입력되지 않았습니다."), -1);
            exit ;
        }

        $addWhere = "where cid='$cid' and mid='$_POST[mid]'";
        $data = $m_config -> getAdminInfo($cid, $_POST[mid], $addWhere);

        if ($data[mid]) {
            msg(_("이미 등록된 아이디 입니다."), -1);
            exit ;
        }
        $password = passwordCommonEncode($_POST[password]);
        $addColumn = "set cid='$cid',mid='$_POST[mid]',password='$password',name='$_POST[name]',redirect_url='$_POST[redirect_url]',regdt=now()";
        $m_config -> setAdminInfo($_POST[mode], $addColumn);

        msgNlocationReplace(_("저장 완료되었습니다."), "admin_pod.php");

        break;

    case "modify" :
        if ($_POST[password]){
           $password = passwordCommonEncode($_POST[password]);
            $addColumn = "set password='$password',name='$_POST[name]',redirect_url='$_POST[redirect_url]'";
        }
        else
            $addColumn = "set name='$_POST[name]',redirect_url='$_POST[redirect_url]'";

        if ($_POST[super]) $addColumn .= ",super='$_POST[super]'";

        $addWhere = "where cid='$cid' and mid='$_POST[mid]'";
        $m_config -> setAdminInfo($_POST[mode], $addColumn, $addWhere);

        msgNlocationReplace(_("저장 완료되었습니다."), "admin_pod.php");

        break;
}

/* admin_pod.php 에서 호출  */
switch ($_GET[mode]) {
    //관리자 삭제.
    case "delete" :
        if ($_GET[mid] == "admin") {
            msg(_("최고관리자는 삭제할 수 없습니다."), -1);
            exit ;
        }        
        $m_config->delAdminInfo($cid, $_GET[mid]);

        msgNlocationReplace(_("정상적으로 처리되었습니다."), "admin_pod.php");

        break;
}

/* manager_list.php / manager_join.php 에서 호출  (사용안함) */
switch ($_POST[mode]) {
   ###영업사원(지사) 가입
   case "manager_join" :
      if(is_array($_POST[manager_phone]))
         $_POST[manager_phone] = implode("-", $_POST[manager_phone]);

      $query = "insert into exm_manager set
                 cid              = '$cid',
                 manager_name     = '$_POST[manager_name]',
                 manager_phone    = '$_POST[manager_phone]',
                 regdt            = now()
              ";

      $db -> query($query);
      break;

   case "manager_modify" :
      if($_POST[manager_phone])
         $_POST[manager_phone] = implode("-", $_POST[manager_phone]);

      $query = "update exm_manager set
                  manager_name     = '$_POST[manager_name]',
                  manager_phone    = '$_POST[manager_phone]'
                  where cid = '$cid' and manager_no = '$_POST[mno]'
               ";
      //debug($query); exit;
      $db -> query($query);
      break;
}

/* member_list_pod.php / member_form_pod.php / member_modify_pod.php 에서 호출  */
switch ($_POST[mode]) {
    case "delMember" :
        
        $sql = "select * from exm_member where cid='$cid' and mid='$_GET[mid]'";
        $data = $db -> fetch($sql);
        if($data) {
            $sql1 = "insert into exm_member_out set 
                        cid='$cid',
                        mid='$data[mid]',
                        name='',
                        email='',
                        regdt='$data[regdt]',
                        outdt=now()";
            $db -> query($sql1);

            $query = "delete from exm_member where cid = '$cid' and mid = '$_GET[mid]'";
            $db -> query($query);
        }        
        
        break;
            
    case "member_join_pod" :
        
        if($_POST[resno]) $_POST[resno] = implode("-", array_notnull($_POST[resno]));
        if($_POST[email]) $_POST[email] = implode("@", array_notnull($_POST[email]));
        if($_POST[phone]) $_POST[phone] = implode("-", array_notnull($_POST[phone]));
        if($_POST[mobile]) $_POST[mobile] = implode("-", array_notnull($_POST[mobile]));

        if($_POST[cust_no]) $_POST[cust_no] = @implode("-", array_notnull($_POST[cust_no]));
        if($_POST[cust_ceo_phone]) $_POST[cust_ceo_phone] = @implode("-", array_notnull($_POST[cust_ceo_phone]));
        if($_POST[cust_phone]) $_POST[cust_phone] = @implode("-", array_notnull($_POST[cust_phone]));
        if($_POST[cust_fax]) $_POST[cust_fax] = @implode("-", array_notnull($_POST[cust_fax]));

        if($_POST[manager_no]) $_POST[manager_no] = @implode(",", array_notnull($_POST[manager_no]));
        
        if($_POST[etc1]) $_POST[etc1] = @implode(",", array_notnull($_POST[etc1]));
        if($_POST[etc2]) $_POST[etc2] = @implode(",", array_notnull($_POST[etc2]));
        if($_POST[etc3]) $_POST[etc3] = @implode(",", array_notnull($_POST[etc3]));
        if($_POST[etc4]) $_POST[etc4] = @implode(",", array_notnull($_POST[etc4]));
        if($_POST[etc5]) $_POST[etc5] = @implode(",", array_notnull($_POST[etc5]));
        
        $password = passwordCommonEncode($_POST[password]);
        
        ### form 전송 취약점 개선 20160128 by kdk
        $_POST[memo] = addslashes(base64_decode($_POST[memo]));
        
        $query = "insert into exm_member set
                  cid         = '$cid',
                  mid         = '$_POST[mid]',
                  password    = '$password',
                  regdt       = now(),
                  sort        = -UNIX_TIMESTAMP(),
                  name        = '$_POST[name]',
                  resno       = '$_POST[resno]',
                  sex         = '$_POST[sex]',
                  calendar    = '$_POST[calendar]',
                  birth_year  = '$_POST[birth_year]',
                  birth       = '$_POST[birth]',
                  email       = '$_POST[email]',
                  apply_email = '$_POST[apply_email]',
                  zipcode     = '$_POST[zipcode]',
                  address     = '$_POST[address]',
                  address_sub = '$_POST[address_sub]',
                  phone       = '$_POST[phone]',
                  mobile      = '$_POST[mobile]',
                  state       = '$_POST[state]',
                  grpno       = '$_POST[grp]',
                  bid         = '$_POST[bid]',

                  credit_member = '$_POST[credit_member]',
                  rest_flag = '$_POST[rest_flag]',
                  etc1 = '$_POST[etc1]',
                  etc2 = '$_POST[etc2]',
                  etc3 = '$_POST[etc3]',
                  etc4 = '$_POST[etc4]',
                  etc5 = '$_POST[etc5]',
                  cust_bank_name = '$_POST[cust_bank_name]',

                  married            = '$_POST[married]',
                  cust_name          = '$_POST[cust_name]',
                  cust_type          = '$_POST[cust_type]',
                  cust_class         = '$_POST[cust_class]',
                  cust_tax_type      = '$_POST[cust_tax_type]',
                  cust_no            = '$_POST[cust_no]',
                  cust_ceo           = '$_POST[cust_ceo]',
                  cust_ceo_phone     = '$_POST[cust_ceo_phone]',
                  cust_zipcode       = '$_POST[cust_zipcode]',
                  cust_address       = '$_POST[cust_address]',
                  cust_address_sub   = '$_POST[cust_address_sub]',
                  cust_address_en    = '$_POST[cust_address_en]',
                  cust_phone         = '$_POST[cust_phone]',
                  cust_fax           = '$_POST[cust_fax]',                  
                  manager_no         = '$_POST[manager_no]',
                  memo        = '$_POST[memo]'
      ";

        $db -> query($query);
        break;

    case "member_modify_pod" :
            
        if($_POST[resno]) $_POST[resno] = implode("-", array_notnull($_POST[resno]));
        if($_POST[email]) $_POST[email] = implode("@", array_notnull($_POST[email]));
        if($_POST[phone]) $_POST[phone] = implode("-", array_notnull($_POST[phone]));
        if($_POST[mobile]) $_POST[mobile] = implode("-", array_notnull($_POST[mobile]));

        if($_POST[cust_no]) $_POST[cust_no] = @implode("-", array_notnull($_POST[cust_no]));
        if($_POST[cust_ceo_phone]) $_POST[cust_ceo_phone] = @implode("-", array_notnull($_POST[cust_ceo_phone]));
        if($_POST[cust_phone]) $_POST[cust_phone] = @implode("-", array_notnull($_POST[cust_phone]));
        if($_POST[cust_fax]) $_POST[cust_fax] = @implode("-", array_notnull($_POST[cust_fax]));

        if($_POST[manager_no]) $_POST[manager_no] = @implode(",", array_notnull($_POST[manager_no]));
        
        if($_POST[etc1]) $_POST[etc1] = @implode(",", array_notnull($_POST[etc1]));
        if($_POST[etc2]) $_POST[etc2] = @implode(",", array_notnull($_POST[etc2]));
        if($_POST[etc3]) $_POST[etc3] = @implode(",", array_notnull($_POST[etc3]));
        if($_POST[etc4]) $_POST[etc4] = @implode(",", array_notnull($_POST[etc4]));
        if($_POST[etc5]) $_POST[etc5] = @implode(",", array_notnull($_POST[etc5]));
        
        if ($_POST[password]){
           $password = passwordCommonEncode($_POST[password]);
            $fld = "password = '$password',";
        }
        
        ### form 전송 취약점 개선 20160128 by kdk
        $_POST[memo] = addslashes(base64_decode($_POST[memo]));

        $query = "update exm_member set
                  $fld
                  name        = '$_POST[name]',
                  resno       = '$_POST[resno]',
                  sex         = '$_POST[sex]',
                  calendar    = '$_POST[calendar]',
                  birth_year  = '$_POST[birth_year]',
                  birth       = '$_POST[birth]',
                  email       = '$_POST[email]',
                  apply_email = '$_POST[apply_email]',
                  apply_sms   = '$_POST[apply_sms]',
                  zipcode     = '$_POST[zipcode]',
                  address     = '$_POST[address]',
                  address_sub = '$_POST[address_sub]',
                  phone       = '$_POST[phone]',
                  mobile      = '$_POST[mobile]',
                  state       = '$_POST[state]',
                  grpno       = '$_POST[grp]',
                  bid         = '$_POST[bid]',

                  credit_member = '$_POST[credit_member]',
                  rest_flag = '$_POST[rest_flag]',
                  etc1 = '$_POST[etc1]',
                  etc2 = '$_POST[etc2]',
                  etc3 = '$_POST[etc3]',
                  etc4 = '$_POST[etc4]',
                  etc5 = '$_POST[etc5]',
                  cust_bank_name = '$_POST[cust_bank_name]',
                  
                  married            = '$_POST[married]',
                  cust_name          = '$_POST[cust_name]',
                  cust_type          = '$_POST[cust_type]',
                  cust_class         = '$_POST[cust_class]',
                  cust_tax_type      = '$_POST[cust_tax_type]',
                  cust_no            = '$_POST[cust_no]',
                  cust_ceo           = '$_POST[cust_ceo]',
                  cust_ceo_phone     = '$_POST[cust_ceo_phone]',
                  cust_zipcode       = '$_POST[cust_zipcode]',
                  cust_address       = '$_POST[cust_address]',
                  cust_address_sub   = '$_POST[cust_address_sub]',
                  cust_address_en    = '$_POST[cust_address_en]',
                  cust_phone         = '$_POST[cust_phone]',
                  cust_fax           = '$_POST[cust_fax]',                  
                  manager_no         = '$_POST[manager_no]',                  
                  memo               = '$_POST[memo]'
               where cid = '$cid' and mid = '$_POST[mid]'
               ";

        $db -> query($query);
        break;
}

/* deposit_member_state_popup.php 에서 호출  */
switch ($_GET[mode]) {
        
    //입금정보 삭제.
    case "deposit_delete" :
        
        if (!trim($_GET[no])) {
            msg(_("필수정보가 없습니다."), -1);
            exit ;
        }

        $db->start_transaction();
        try {
            //입금정보 삭제.
            $m_pod->setDepositMoneyDelete($_GET[no]);

            $db->query("commit");
        } catch(Exception $e) {
            $db->query("rollback");
        }
        $db->end_transaction();

        msg(_("정상적으로 처리되었습니다."), $_SERVER[HTTP_REFERER]);
        break;
}

if (!$_POST[rurl])
	$_POST[rurl] = $_SERVER[HTTP_REFERER];
go($_POST[rurl]);
?>