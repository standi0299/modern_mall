<?
/*
* @date : 20181030
* @author : kdk
* @brief : POD용 (알래스카) 회원 리스트.
* @request : 
* @desc : 기존 필드 사용 (결제방식:credit_member, 거래상태:rest_flag)
* @todo :
*/

include "../lib.php";

$m_member = new M_member();
$m_pretty = new M_pretty();
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
        $addwhere .= " and manager_no like '%$postData[manager_no]%'";
    }
    
    if(isset($postData[grpno]) && $postData[grpno] != "") {
        $addwhere .= " and grpno = '$postData[grpno]'";
    }
    
    if(isset($postData[credit_member]) && $postData[credit_member] != "") {
        $addwhere .= " and credit_member = '$postData[credit_member]'";
    }
    
    if(isset($postData[rest_flag]) && $postData[rest_flag] != "") {
        $addwhere .= " and rest_flag = '$postData[rest_flag]'";
    }
    
    if(isset($postData[sword]) && $postData[sword] != "") { //아이디,회원명,사업자명,사업자/주민등록번호,일반전화,휴대전화
        $addwhere .= " and concat(mid,name,cust_name,resno,cust_no,phone,mobile) like '%$postData[sword]%'";
    }
}
//debug($addwhere);

$order_column_arr = array("mid", "", "", "", "", "", "", "", "", "", "", "", "", "regdt");
$order_data = $_POST[order][0];

$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

//$totalCnt = count($m_pretty -> getMemberListA($cid));
//$totalCnt = $m_pretty -> getMemberListA_cnt($cid, $addwhere);
$totalCnt = $m_pod -> getMemberListCnt($cid, $addwhere);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

$limit = "limit $_POST[start], $_POST[length]";
//$list = $m_pretty -> getMemberListA($cid, $addwhere, $orderby, $limit);
$list = $m_pod -> getMemberList($cid, $addwhere, $orderby, $limit);

foreach ($list as $key => $value) {
    $pdata = array();

    $sns = "";
    $sns_data = $m_member->getLogSnsLogin($cid,$value[sns_id]);
    if($sns_data) {
   	    $sns = "&nbsp;&nbsp;&nbsp;<img src='../img/$sns_data[sns_type].png' height='20px' alt='$sns_data[sns_id]' />";
    }   
   
    $login = "";
    $login = "<br><a href=\"../member/indb.php?mode=login&mid=". urlencode($value[mid]) ."\" target=\"_blank\" class=\"eng red\">-Login</a>";
   
    $pdata[] = $value[mid].$sns.$login;
    //$pdata[] = $value[name];
    $pdata[] = "<a href='member_modify_pod.php?mid=$value[mid]'>".$value[name]."</a>";
    $pdata[] = $r_grp[$value[grpno]];
   
    //cust_name 사업자명   
    $pdata[] = $value[cust_name];
    
    //cust_no 사업자등록번호 / resno 주민등록번호
    //if ($r_grp[$value[grpno]] == "개인") {
    if ($value[grpno] == "1") {
        if($value[resno]) {
            $value[resno] = substr($value[resno],0,6)."-".substr($value[resno],7,1)."******";
        }
        $pdata[] = $value[resno];
    }    
    else {
       $pdata[] = $value[cust_no];
    }
    
    $pdata[] = $value[mobile];
    $pdata[] = $value[phone];
   
    //credit_member 결제방식 (선결제,후결제)
    $pdata[] = ($value[credit_member])?"후불제":"선불제";
    //rest_flag 거래상태(승인,정지)
    $pdata[] = ($value[rest_flag])?"정지":"승인";

    //$pdata[] = "<u onclick=\"popup('orderlist.p.php?mid=$value[mid]',630,420)\" class=\"hand\">".number_format($value[totpayprice])."</u>";
   
    //미수금
    $pdata[] = number_format($value[remain_money]);
   
    //회원입금액 - 현재(조회) 월 기준 입금 총액.
    $pdata[] = "<a onclick=\"popup('deposit_member_state_popup.php?mid=$value[mid]',1100,800)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($m_pod->getTotDepositMoney($cid, $value[mid], " and left(deposit_date,7)='".date("Y-m")."'"))."</a>";

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

    $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-danger\" onclick=\"popup('../member/sms_popup.php?mobile=$value[mobile]',800,600);\">"._("전송하기")."</button>";
    $pdata[] = substr($value[regdt],0,10);
    $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=\"location.href='order_form.php?mid=$value[mid]';\">"._("주문접수")."</button>";
    $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=\"location.href='member_modify_pod.php?mid=$value[mid]';\">"._("수정")."</button>";
    $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-danger\" onclick=\"mid_delete('".$value[mid]."');\">"._("삭제")."</button>";

    $psublist[] = $pdata;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>