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
        $addwhere .= " and concat(a.mid,a.name,cust_name) like '%$postData[sword]%'";
    }
    
    if(isset($postData[manager_no]) && $postData[manager_no] != "") {
        $addwhere .= " and manager_no like '%$postData[manager_no]%'";
    }
    
    if(isset($postData[promise_date_yn]) && $postData[promise_date_yn] != "") {
        $addwhere .= " and promise_date !='' and promise_date is not null";
    }
}
//debug($addwhere);

$order_column_arr = array("", "mid", "", "", "", "", "", "", "", "", "remain_money", "", "", "remain_money", "", "");
$order_data = $_POST[order][0];

$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

//$totalCnt = count($m_pretty -> getMemberListA($cid));
$totalCnt = $m_pod -> getMemberRemainListCnt($cid, $addwhere);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

$limit = "limit $_POST[start], $_POST[length]";
$list = $m_pod -> getMemberRemainList($cid, $addwhere, $orderby, $limit);

foreach ($list as $key => $value) {
    $pdata = array();

    $pdata[] = $totalCnt--;
    $pdata[] = "<u class='blue' onclick=\"popup('remain_member_form_popup.php?mid=$value[mid]',700,650)\" class=\"hand\" style=\"cursor:pointer;\">". $value[mid] ."</u>";
    $pdata[] = $value[name];

    //cust_name 사업자명   
    $pdata[] = $value[cust_name];
    $pdata[] = $r_grp[$value[grpno]];
   
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

    $pdata[] = ($value[phone]) ? $value[phone] : $value[mobile];
    
    //시작일
    $pdata[] = ($value[start_date]) ? substr($value[start_date],0,10) : "";
    
    //최종입금일
    $pdata[] = ($value[final_date]) ? substr($value[final_date],0,10) : "";

    //약속일자
    $pdata[] = ($value[promise_date]) ? substr($value[promise_date],0,10) : "";

    //약속금액
    $pdata[] = number_format($value[promise_money]);
    
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
    //$n_deposit_money = number_format($value[deposit_money]-$value[remain_money]);
    //$pdata[] = ($n_deposit_money>0) ? $n_deposit_money : "0" ;
    
    //선발행입금액
    //* 선발행입금분 : 입금과 동시에 사전에 세금계산서를 발행한 금액으로 고객이 주문결제에 사용할 때 계산서 발행 금액에서 제외되어야 함 (업무상 부가세는 차감하고 나머지 금액만 주문에 사용 가능, 미리 부가세 처리를 해야 하기 때문에 부가세를 차감하는 것임, 주로 관공서에서 필요)    
    //$pdata[] = number_format($value[pre_deposit_money]);

    //미수담당자
    $pdata[] = $value[remainadmin];
    
    //비고
    $pdata[] = $value[bigo];
    
    $psublist[] = $pdata;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>