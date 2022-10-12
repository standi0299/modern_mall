<?
/*
* @date : 20181030
* @author : kdk
* @brief : POD용 (알래스카) 입금관리 리스트.
* @request : 
* @desc :
* @todo :
*/

include "../lib.php";

$m_member = new M_member();
$m_pod = new M_pod();
$r_state = array(_("승인"),_("미승인"),_("차단"));

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
    
    if (array_notnull($postData[deposit_money])){
       if ($postData[deposit_money][0]+0) $addwhere .= " and (select sum(deposit_money) from pod_deposit_money where cid = a.cid and mid = a.mid)>='{$postData[deposit_money][0]}'";
       if ($postData[deposit_money][1]+0) $addwhere .= " and (select sum(deposit_money) from pod_deposit_money where cid = a.cid and mid = a.mid)<='{$postData[deposit_money][1]}'";
    }
    
    if (array_notnull($postData[pre_deposit_money])){
       if ($postData[pre_deposit_money][0]+0) $addwhere .= " and (select sum(pre_deposit_money) from pod_deposit_money where cid = a.cid and mid = a.mid)>='{$postData[pre_deposit_money][0]}'";
       if ($postData[pre_deposit_money][1]+0) $addwhere .= " and (select sum(pre_deposit_money) from pod_deposit_money where cid = a.cid and mid = a.mid)<='{$postData[pre_deposit_money][1]}'";
    }
    
    if (array_notnull($postData[remain_money])){
       if ($postData[remain_money][0]+0) $addwhere .= " and (select sum(remain_price) from pod_pay where cid = a.cid and mid = a.mid)>='{$postData[remain_money][0]}'";
       if ($postData[remain_money][1]+0) $addwhere .= " and (select sum(remain_price) from pod_pay where cid = a.cid and mid = a.mid)<='{$postData[remain_money][1]}'";
    }
}
//debug($addwhere);

$order_column_arr = array("","mid", "", "", "", "", "", "", "", "", "", "");
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

    $pdata[] = "<input type=\"checkbox\" name=\"chk[]\" value=\"$value[mid]\">";
    $pdata[] = $value[mid];
    $pdata[] = $value[name];
   
    //cust_name 사업자명   
    $pdata[] = $value[cust_name];

    //rest_flag 거래상태(승인,정지)
    $pdata[] = ($value[rest_flag])?"정지":"승인";

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
   
    //미수금
    $pdata[] = number_format($value[remain_money]);
    
    //선입금액
    $pdata[] = number_format($value[deposit_money]);

    //선발행입금액
    $pdata[] = number_format($value[pre_deposit_money]);

    $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=\"popup('deposit_member_state_popup.php?mid=$value[mid]',1200,750);\">"._("조회")."</button>";
    $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-danger\" onclick=\"depositForm('$value[mid]');\">"._("등록")."</button>";
    $psublist[] = $pdata;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>