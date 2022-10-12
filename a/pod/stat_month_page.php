<?
/*
* @date : 20181116
* @author : kdk
* @brief : POD용 (알래스카) 월매출현황.
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

//최근 6개월 총매출금액
//매출 집계된 해당 월의 금액을 클릭하면 거래관리-전체거래 화면을 호출하고 조회 기간을 해당 월 기준으로 설정하여 최초 조회.
if (!$postData[sdt]) $postData[sdt] = "orddt";
$link = "deposit_sales_member_state_tot_popup.php?sdt=$postData[sdt]";

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
       
    //미수금액
    $pdata[] = number_format($value[remain_price]);//

    //선입금액
    $pdata[] = number_format($value[deposit_money]);

    //선발행사용가능금액
    $pdata[] = number_format($value[pre_deposit_money]);
            
    //최근 6개월 총매출금액
    /*$pdata[] = number_format($m_pod->getStatMonth($cid, $postData[sdt], " and mid='".$value[mid]."' and left($postData[sdt],7)='".date("Y-m",strtotime("$postData[sdate] -6 month"))."'"));
    $pdata[] = number_format($m_pod->getStatMonth($cid, $postData[sdt], " and mid='".$value[mid]."'and left($postData[sdt],7)='".date("Y-m",strtotime("$postData[sdate] -5 month"))."'"));
    $pdata[] = number_format($m_pod->getStatMonth($cid, $postData[sdt], " and mid='".$value[mid]."'and left($postData[sdt],7)='".date("Y-m",strtotime("$postData[sdate] -4 month"))."'"));
    $pdata[] = number_format($m_pod->getStatMonth($cid, $postData[sdt], " and mid='".$value[mid]."'and left($postData[sdt],7)='".date("Y-m",strtotime("$postData[sdate] -3 month"))."'"));
    $pdata[] = number_format($m_pod->getStatMonth($cid, $postData[sdt], " and mid='".$value[mid]."'and left($postData[sdt],7)='".date("Y-m",strtotime("$postData[sdate] -2 month"))."'"));
    $pdata[] = number_format($m_pod->getStatMonth($cid, $postData[sdt], " and mid='".$value[mid]."'and left($postData[sdt],7)='".date("Y-m",strtotime("$postData[sdate] -1 month"))."'"));*/

    $pdata[] = "<a class='blue' onclick=\"popup('$link&sdate=".date("Y-m",strtotime("$postData[sdate] -5 month"))."&mid=$value[mid]',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($m_pod->getStatMonth($cid, $postData[sdt], " and mid='".$value[mid]."' and left($postData[sdt],7)='".date("Y-m",strtotime("$postData[sdate] -5 month"))."'"))."</a>";
    $pdata[] = "<a class='blue' onclick=\"popup('$link&sdate=".date("Y-m",strtotime("$postData[sdate] -4 month"))."&mid=$value[mid]',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($m_pod->getStatMonth($cid, $postData[sdt], " and mid='".$value[mid]."'and left($postData[sdt],7)='".date("Y-m",strtotime("$postData[sdate] -4 month"))."'"))."</a>";
    $pdata[] = "<a class='blue' onclick=\"popup('$link&sdate=".date("Y-m",strtotime("$postData[sdate] -3 month"))."&mid=$value[mid]',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($m_pod->getStatMonth($cid, $postData[sdt], " and mid='".$value[mid]."'and left($postData[sdt],7)='".date("Y-m",strtotime("$postData[sdate] -3 month"))."'"))."</a>";
    $pdata[] = "<a class='blue' onclick=\"popup('$link&sdate=".date("Y-m",strtotime("$postData[sdate] -2 month"))."&mid=$value[mid]',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($m_pod->getStatMonth($cid, $postData[sdt], " and mid='".$value[mid]."'and left($postData[sdt],7)='".date("Y-m",strtotime("$postData[sdate] -2 month"))."'"))."</a>";
    $pdata[] = "<a class='blue' onclick=\"popup('$link&sdate=".date("Y-m",strtotime("$postData[sdate] -1 month"))."&mid=$value[mid]',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($m_pod->getStatMonth($cid, $postData[sdt], " and mid='".$value[mid]."'and left($postData[sdt],7)='".date("Y-m",strtotime("$postData[sdate] -1 month"))."'"))."</a>";
    $pdata[] = "<a class='blue' onclick=\"popup('$link&sdate=".date("Y-m",strtotime("$postData[sdate] -0 month"))."&mid=$value[mid]',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($m_pod->getStatMonth($cid, $postData[sdt], " and mid='".$value[mid]."'and left($postData[sdt],7)='".date("Y-m",strtotime("$postData[sdate] -0 month"))."'"))."</a>";
    
    $psublist[] = $pdata;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>