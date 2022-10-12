<?
/*
* @date : 20181116
* @author : kdk
* @brief : POD용 (알래스카) 매출현황.
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
$addwhere2="";

/*$search_data = $_POST[search][value];
if ($search_data) {
    $addwhere .= " and (name like '%$search_data%' or mid like '%$search_data%')";
}*/

$postData = json_decode(base64_decode($_GET[postData]), 1);
if ($postData) {
    if (array_notnull($postData[receiptdt])){ //접수일시
       if ($postData[receiptdt][0]) $addwhere2 .= " and receiptdt>='{$postData[receiptdt][0]}'";
       if ($postData[receiptdt][1]) $addwhere2 .= " and receiptdt<='{$postData[receiptdt][1]}'";
    }
    
    if(isset($postData[sword]) && $postData[sword] != "") { //아이디,회원명,사업자명
        $addwhere .= " and concat(mid,name,cust_name) like '%$postData[sword]%'";
    }
   
    if(isset($postData[credit_member]) && $postData[credit_member] != "") {
        $addwhere .= " and credit_member = '$postData[credit_member]'";
    }
    
    if(isset($postData[rest_flag]) && $postData[rest_flag] != "") {
        $addwhere .= " and rest_flag = '$postData[rest_flag]'";
    }
}
//debug($addwhere);

$order_column_arr = array("", "regdt", "", "", "", "", "", "", "", "", "");
$order_data = $_POST[order][0];

$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

//$totalCnt = count($m_pretty -> getMemberListA($cid));
$totalCnt = $m_pod -> getStatSalesListCnt($cid, $addwhere);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

$limit = "limit $_POST[start], $_POST[length]";
$list = $m_pod -> getStatSalesList($cid, $addwhere, $addwhere2, $orderby, $limit);

foreach ($list as $key => $value) {
    $pdata = array();

    $pdata[] = $totalCnt--;
    $pdata[] = $value[mid];
    $pdata[] = $value[name];
    
    //cust_name 사업자명   
    $pdata[] = $value[cust_name];
   
    //credit_member 결제방식 (선결제,후결제)
    $pdata[] = ($value[credit_member])?"후불제":"선불제";

    //rest_flag 거래상태(승인,정지)
    $pdata[] = ($value[rest_flag])?"정지":"승인";
       
    //주문금액
    $pdata[] = "<a class='blue' onclick=\"popup('stat_sales_payprice_popup.php?mid=$value[mid]&postData=$_GET[postData]',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[payprice])."</a>";
    
    //입금액
    $pdata[] = "<a class='blue' onclick=\"popup('stat_sales_depositprice_popup.php?mid=$value[mid]&postData=$_GET[postData]',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[deposit_price])."</a>";
    
    //미수금액
    $pdata[] = "<a class='blue' onclick=\"popup('stat_sales_remainprice_popup.php?mid=$value[mid]&postData=$_GET[postData]',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[remain_price])."</a>";
        
    //선입금액
    $pdata[] = number_format($value[deposit_money]);
    
    //선발행사용가능금액
    $pdata[] = "<a class='blue' onclick=\"popup('stat_sales_predepositprice_popup.php?mid=$value[mid]&postData=$_GET[postData]',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[pre_deposit_money])."</a>";
    
    $psublist[] = $pdata;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>