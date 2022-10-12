<?
/*
* @date : 20190130
* @author : kdk
* @brief : 시안관리 기능 추가.
* @request : 태웅.
* @desc : 
* @todo : 
*/

include "../_header.php";

if (!$_GET[guest]) chkMember();
$m_goods = new M_goods();
$m_member = new M_member();
$m_order = new M_order();
$m_cash = new M_cash_receipt();
$m_modern = new M_modern();
$m_print = new M_print();

$r_rid["|self|"] = $cfg[nameSite];
$r_rid_x = get_release();
$r_rid = array_merge($r_rid, $r_rid_x);
### 배송업체정보추출
$r_shipcomp = get_shipcomp();

if (!$_GET[payno] && $_GET[order_code]) {
    $tableName = "exm_ord_upload";
    $addWhere = "where upload_order_code = '$_GET[order_code]'";
    $order_data = $m_order->getOrderInfo($cid, $tableName, $addWhere); 
    $_GET[payno] = $order_data[payno];
}

$tableName2 = "exm_pay";
$addWhere2 = "where payno = '$_GET[payno]'";
$data = $m_order->getOrderInfo($cid, $tableName2, $addWhere2);

if (!$data[payno]) {
    //msg(_("잘못된 접근입니다."), -1);
}

if ($_GET[mobile_type] != "Y" && $cfg[skin_theme] == "P1") {
    $data[receiver_mobile] = explode("-", $data[receiver_mobile]);
    $selected[receiver_mobile][$data[receiver_mobile][0]] = "selected";
}

$query = "select * from exm_ord where payno = '$data[payno]'";
$db->query("set names utf8");
$res = $db->query($query);

//시안요청 주문은 1건임. 
while ($ord = $db->fetch($res)) {
    $data['ord'][$ord[ordno]] = $ord;
    $item = array();
   
    //paymethod를 가져오기 위해 join 추가 / 14.12.29 / kjm
    $data2 = $m_order->getOrderViewList($data[payno], $ord[ordno]);
    foreach ($data2 as $item) {
        
        //category name 출력 2014.11.21 by kdk
        $q = $m_goods->getGoodsCategoryInfo($cid, $item[goodsno]);
        foreach ($q as $k=>$v) {
            switch (strlen($v[catno])) {
                case "3":
                    $item[catnm1] = $v[catnm];
                    break;
                case "6":
                    $item[catnm2] = $v[catnm];
                    break;
                case "9":
                    $item[catnm3] = $v[catnm];
                    break;
                case "12":
                    $item[catnm4] = $v[catnm];
                    break;
            }
        }

        if ($item[ext_json_data]) {
            $ext_arr = json_decode($item[ext_json_data], true);
            $item[msg] = $ext_arr[msg];
            $item[tsidx] = $ext_arr[tsidx];
            $item[tidx] = $ext_arr[tidx];
            $item[tname] = $ext_arr[tname];
            $item[turl] = $ext_arr[turl];
        }
        
        if ($item[est_order_data]) {
            $est_arr = json_decode($item[est_order_data], true);
            $item[order_cnt_select] = $est_arr[order_cnt_select];
            $item[unit_order_cnt] = $est_arr[unit_order_cnt];
        }

        //수량: 항목 제외.
        if ($item[est_order_option_desc]) {
            $desc_arr = explode("<br>", $item[est_order_option_desc]);
            if($desc_arr) {
                foreach ($desc_arr as $key => $val) {
                    if($val && strpos($val, "수량") === false)
                        $est_order_option_desc .= $val."<br>";
                }    
            }
            $item[est_order_option_desc] = $est_order_option_desc;
        }

        //할인 총금액
        $dc_tprice = 0;
        if ($item[dc_member]) $dc_tprice += ($item[dc_member] * $item[ea]);
        if ($item[dc_coupon]) $dc_tprice += ($item[dc_coupon]);
        if ($item[dc_emoney]) $dc_tprice += ($item[dc_emoney]);
        $item[dc_tprice] = $dc_tprice;
        
        //상태값.
        $data[itemstep] = $item[itemstep];

        $data['ord'][$ord[ordno]][item][$item[ordseq]] = $item;
    }
}
//debug($data);

$cashInfo = $m_cash->getInfo($cid, $payno);
switch ($cfg[pg][module]){
   case "kcp":
      $data[receipt_url] = "http://admin.kcp.co.kr/Modules/Sale/Card/ADSA_CARD_BILL_Receipt.jsp?c_trade_no=".$data[pgcode];
      break;
   case "inicis":
   case "inipaystdweb":
      $data[receipt_url] = "https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/mCmReceipt_head.jsp?noTid=".$data[pgcode]."&noMethod=1";
      break;
   case "smartxpay" :
      //$data[receipt_url] = "smartxpay";
      $authcode = md5($cfg[pg][lgd_mid].$data[pgcode].$cfg[pg][lgd_mertkey]);
      $tpl->assign('authcode', $authcode);
      break;
}

//시안요청 조회.
$data[design_data] = "";
$design_data = $m_modern->getDesignPayno($_GET[payno]);
//debug($design_data);

if ($design_data) {
    $design_file = $m_print->getPrintUploadFile($design_data[storageid]);
    //debug($design_file);
    if ($design_file) {
        $design_data[files] = $design_file;
        
        if ($design_data[design_fix]) {
            foreach ($design_file as $key => $val) {
                if ($design_data[design_fix] == $val[id]) {    
                    $data[design_fix_default] = $design_file[$key];
                    break;
                }
            }            
        }
        else $data[design_fix_default] = $design_file[0];
    }
    $data[design_data] = $design_data;
}

//시안요청 댓글 조회.
$data[design_comment] = "";
$design_comment_data = $m_modern->getDesignComment($_GET[payno]);
//debug($design_comment_data);
if ($design_comment_data) {
    $data[design_comment] = $design_comment_data;
}

//debug($data);
//debug($tpl);
$tpl->assign("cash_status", $cashInfo[status]);
$tpl->assign("pg_module", $cfg[pg][module]);
$tpl->assign($data);
$tpl->print_('tpl');

?>