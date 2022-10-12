<?
/*
* @date : 20181030
* @author : kdk
* @brief : POD용 (알래스카) 거래관리 리스트.
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
    if(isset($postData[sword]) && $postData[sword] != "") { //아이디,회원명,사업자명
        $addwhere .= " and concat(mid,name,cust_name) like '%$postData[sword]%'";
    }
    
    if(isset($postData[manager_no]) && $postData[manager_no] != "") {
        $addwhere .= " and manager_no like '%$postData[manager_no]%'";
    }
    
    if(isset($postData[credit_member]) && $postData[credit_member] != "") {
        $addwhere .= " and credit_member = '$postData[credit_member]'";
    }
    
    if(isset($postData[rest_flag]) && $postData[rest_flag] != "") {
        $addwhere .= " and rest_flag = '$postData[rest_flag]'";
    }
}
//debug($addwhere);

$order_column_arr = array("", "mid", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
$order_data = $_POST[order][0];

$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

//$totalCnt = count($m_pretty -> getMemberListA($cid));
$totalCnt = $m_pod -> getMemberListCnt($cid, $addwhere);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

$limit = "limit $_POST[start], $_POST[length]";
$list = $m_pod -> getMemberList($cid, $addwhere, $orderby, $limit);

foreach ($list as $key => $value) {
    $pdata = array();

    $pdata[] = $totalCnt--;
    $pdata[] = $value[mid];
    
    //$pdata[] = $value[name];
    $pdata[] = "<u class='blue' onclick=\"popup('deposit_sales_member_state_popup.php?mid=$value[mid]',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">". $value[name] ."</u>";
    
    $pdata[] = $r_grp[$value[grpno]];
   
    //credit_member 결제방식 (선결제,후결제)
    $pdata[] = ($value[credit_member])?"후불제":"선불제";

    //cust_name 사업자명   
    $pdata[] = $value[cust_name];
    
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

    //rest_flag 거래상태(승인,정지)
    $pdata[] = ($value[rest_flag])?"정지":"승인";
       
    //미수금
    $pdata[] = number_format($value[remain_money]);
    
    //선입금액
    $pdata[] = number_format($value[deposit_money]);

    //현재미수금
    //* 현재미수금 : 미수금 - 선입금이 양수인 경우
    $n_remain_money = number_format($value[remain_money]-$value[deposit_money]);
    $pdata[] = ($n_remain_money>0) ? $n_remain_money : "0" ;
    
    //현재선입금
    //* 현재선임급 : 선입금 - 미수금이 양수인 경우
    $n_deposit_money = number_format($value[deposit_money]-$value[remain_money]);
    $pdata[] = ($n_deposit_money>0) ? $n_deposit_money : "0" ;
    
    //선발행입금액
    //* 선발행입금분 : 입금과 동시에 사전에 세금계산서를 발행한 금액으로 고객이 주문결제에 사용할 때 계산서 발행 금액에서 제외되어야 함 (업무상 부가세는 차감하고 나머지 금액만 주문에 사용 가능, 미리 부가세 처리를 해야 하기 때문에 부가세를 차감하는 것임, 주로 관공서에서 필요)
    
    $pdata[] = number_format($value[pre_deposit_money]);

    $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=\"popup('deposit_sales_member_state_tot_popup.php?mid=$value[mid]',1200,750);\">"._("상세보기")."</button>";
    $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=\"popup('deposit_member_state_popup.php?mid=$value[mid]',1200,750);\">"._("현황보기")."</button>";
    //$pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=\"alert('준비중입니다.');\">"._("보기")."</button>";
    //$pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=\"alert('준비중입니다.');\">"._("보기")."</button>";
    $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=\"popup('receipt_popup.php?mid=$value[mid]',1200,750);\">"._("보기")."</button>";
    $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=\"popup('bill_popup.php?mid=$value[mid]',1200,750);\">"._("보기")."</button>";
    $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-danger\" onclick=\"popup('../member/sms_popup.php?mobile=$value[mobile]',800,600);\">"._("전송하기")."</button>";
    
    $psublist[] = $pdata;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>