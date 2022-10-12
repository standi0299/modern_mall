<?
/*
* @date : 20181030
* @author : kdk
* @brief : POD용 (알래스카) 주문관리 리스트.
* @request : 
* @desc :
* @todo :
*/

include "../lib.php";

$m_member = new M_member();
$m_pod = new M_pod();
$r_state = array(_("승인"),_("미승인"),_("차단"));

### 회원그룹 추출
$r_grp = getMemberGrp();

### 영업사원정보 추출
$r_manager = $m_pod->getSalesList($cid);

$addwhere="";

/*$search_data = $_POST[search][value];
if ($search_data) {
    $addwhere .= " and (name like '%$search_data%' or mid like '%$search_data%')";
}*/

$postData = json_decode(base64_decode($_GET[postData]), 1);
if ($postData) {
    if(isset($postData[manager_no]) && $postData[manager_no] != "") {
        $addwhere .= " and a.manager_no like '%$postData[manager_no]%'";
    }
    
    if (array_notnull($postData[payprice])){
       if ($postData[payprice][0]+0) $addwhere .= " and a.payprice>='{$postData[payprice][0]}'";
       if ($postData[payprice][1]+0) $addwhere .= " and a.payprice<='{$postData[payprice][1]}'";
    }
    
    if (array_notnull($postData[orddt])){
       if ($postData[orddt][0]) $addwhere .= " and a.orddt>='{$postData[orddt][0]} 00:00:00'";
       if ($postData[orddt][1]) $addwhere .= " and a.orddt<='{$postData[orddt][1]} 23:59:59'";
    }
    
    if (array_notnull($postData[receiptdt])){
       if ($postData[receiptdt][0]) $addwhere .= " and a.receiptdt>='{$postData[receiptdt][0]}'";
       if ($postData[receiptdt][1]) $addwhere .= " and a.receiptdt<='{$postData[receiptdt][1]}'";
    }
    
    if(isset($postData[sword]) && $postData[sword] != "") { //아이디,회원명,사업자명,접수담당자,주문번호,주문명
        $addwhere .= " and concat(a.mid,b.name,b.cust_name,a.receiptadmin,a.payno,a.order_title) like '%$postData[sword]%'";
    }
}
//debug($addwhere);

$order_column_arr = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "a.orddt", "", "", "", "", "", "", "");
$order_data = $_POST[order][0];

$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$totalCnt = $m_pod -> getOrderListCnt($cid, $addwhere);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

$limit = "limit $_POST[start], $_POST[length]";
$list = $m_pod -> getOrderList($cid, $addwhere, $orderby, $limit);

foreach ($list as $key => $value) {
    $pdata = array();

    $pdata[] = "<input type=\"checkbox\" name=\"chk[]\" value=\"$value[payno]\">";
    $pdata[] = "<a href=\"javascript:;\" onclick=\"popup('order_detail_popup.php?payno=$value[payno]',1200,750)\"><b>$value[payno]</b></a>";

    //cust_name 사업자명   
    $pdata[] = $value[cust_name];

    //cust_no 사업자등록번호 / resno 주민등록번호
    if ($r_grp[$value[grpno]] == "개인") {
        $pdata[] = $value[resno] ;
    }    
    else {
       $pdata[] = $value[cust_no] ;
    }

    $pdata[] = $value[name];
    $pdata[] = $value[mid];
    
    //if($value[order_type] == "on-line") {
    //    $pdata[] = "온라인 주문";
    //    $pdata[] = "온라인 주문";
    //    $pdata[] = "온라인 주문";
    //}
    //else {
        $pdata[] = $value[order_title];
        $pdata[] = $value[goodsnm];
        $pdata[] = $value[order_data];
    //}
    
    //주문금액
    $pdata[] = number_format($value[payprice]+$value[vat_price]+$value[ship_price]);
    
    //입금액
    $pdata[] = number_format($value[deposit_price]);

    //선발행입금사용금액
    $pdata[] = number_format($value[pre_deposit_price]);

    //미수금액
    $pdata[] = number_format($value[remain_price]);
    
    //manager_no 영업담당자
    $manager_name = "";
    if ($value[manager_no]) {
        $value[manager_no] = explode(",",$value[manager_no]);

        foreach ($value[manager_no] as $key => $val) {
            foreach($r_manager as $k=>$v) {
                if ($v[mid] == $val) {
                    $manager_name .= $v[name].",";
                }
            }
        }
        if ($manager_name != "") $manager_name = substr($manager_name , 0, -1);
    }
    $pdata[] = $manager_name;

    //주문일시
    $pdata[] = $value[orddt];

    //접수담당자
    $pdata[] = $r_manager[$value[receiptadmin]][name];
    //접수일시
    $pdata[] = $value[receiptdt];    

    //출고담당자
    $pdata[] = $r_manager[$value[deliveryadmin]][name];
    //출고일시
    $pdata[] = ($value[deliverydt]) ? "<a href=\"javascript:;\" onclick=\"popup('factorycert_popup.php?mid=$value[mid]&payno=$value[payno]',1200,750)\"><u>$value[deliverydt]</u></a>" : "";

    //진행상태
    $pdata[] = $r_pay_status[$value[status]];
    
    //상태갱신일시
    $pdata[] = $value[status_date];

    //자동입금처리제외
    //$pdata[] = ($value[autoproc_flag]=="Y") ? "제외" : "";
    $pdata[] = ($value[autoproc_flag]) ? "제외" : "";

    /*
    $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-danger\" onclick=\"popup('../member/sms_popup.php?mobile=$value[mobile]',800,600);\">"._("전송하기")."</button>";
    */
    
    $psublist[] = $pdata;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>