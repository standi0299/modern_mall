<?
include "../lib.php";

$m_member = new M_member();
$m_pretty = new M_pretty();
$r_state = array(_("승인"),_("미승인"),_("차단"));
### 회원그룹 추출
$r_grp = getMemberGrp();

$getData = json_decode(base64_decode($_GET[postData]),1);

//검색 조건
if($getData) {	
	//회원그룹
   if($getData[grp]) $addwhere .= " and grpno = '$getData[grp]'";
    
	//검색어
   if($getData[s_search]){
      $getData[s_search] = trim($getData[s_search]);
      $addwhere .= " and (name like '%$getData[s_search]%' or mid like '%$getData[s_search]%' or email like '%$getData[s_search]%' or replace(phone,'-','') like '%$getData[s_search]%' or replace(mobile,'-','') like '%$getData[s_search]%')";	  
   }
	
	//적립금
   if($getData[emoney_start]) $addwhere .= " and emoney >= '{$getData[emoney_start]}'";
   if($getData[emoney_end]) $addwhere .= " and emoney <= '{$getData[emoney_end]}'";

	//가입일
   if($getData[regdt_start]) {
	   $regdt_start = str_replace("-","",$getData[regdt_start]);
	   $addwhere .= " and date_format(regdt,'%Y%m%d') >= '{$regdt_start}'";
   }
   if($getData[regdt_end]) {
	   $regdt_end = str_replace("-","",$getData[regdt_end]);
	   $addwhere .= " and date_format(regdt,'%Y%m%d') < adddate({$regdt_end},interval 1 day)+0";
   }
	
   //구매일
   if($getData[orddt_start]) {
	   $orddt_start = str_replace("-","",$getData[orddt_start]);
	   $addwhere .= " 
			and (select payno from exm_pay
				where
					cid = '$cid'
					and mid = a.mid
					and date_format(orddt,'%Y%m%d') > '{$orddt_start}'
				limit 1
			) > 0
	   ";
   }
   if($getData[orddt_end]) {
	   $orddt_end = str_replace("-","",$getData[orddt_end]);
	   $addwhere .= " 
			and (select payno from exm_pay
				where
					cid = '$cid'
					and mid = a.mid
					and date_format(orddt,'%Y%m%d') < adddate({$orddt_end},interval 1 day)+0
				limit 1
			) > 0
	   ";	
   }

   //분류
   if($getData[state]) $addwhere .= " and state={$getData[state]}";

   //이메일 수신여부
   if($getData[apply_email]=="0"||$getData[apply_email]=="1") $addwhere .= " and apply_email={$getData[apply_email]}";

   //SMS 수신여부
   if($getData[apply_sms]=="0"||$getData[apply_sms]=="1") $addwhere .= " and apply_sms={$getData[apply_sms]}";

   //성별
   if($getData[sex]) $addwhere .= " and sex='{$getData[sex]}'";

   //결혼여부
   if($getData[married]) $addwhere .= " and married='{$getData[married]}'";
	
   //나이
   if($getData[age_start]) {
	   $year = date("Y");
	   $birth_year[0] = $year + 1 - $getData[age_start];
	   $addwhere .= " and birth_year <= '{$birth_year[0]}'";
   }
   if($getData[age_end]) {
	   $year = date("Y");
	   $birth_year[1] = $year + 1 - $getData[age_end];
	   $addwhere .= " and birth_year >= '{$birth_year[1]}'";
   }
	
   $having = array();
   //구매금액
   if($getData[totpayprice_start]) $having[] = "totpayprice >= '{$getData[totpayprice_start]}'";
   if($getData[totpayprice_end]) $having[] = "totpayprice <= '{$getData[totpayprice_end]}'";
   if ($having){
	   $addwhere .= " having(".implode(" and ",$having).")";
   }
}

$order_column_arr = array("","regdt", "mid", "name", "", "", "", "", "", "", "", "");
$order_data = $_POST[order][0];

$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

//$totalCnt = count($m_pretty -> getMemberListA($cid));
$totalCnt = $m_pretty -> getMemberListA_cnt($cid, $addwhere);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

$limit = "limit $_POST[start], $_POST[length]";
$list = $m_pretty -> getMemberListA($cid, $addwhere, $orderby, $limit);

foreach ($list as $key => $value) {
   $pdata = array();
   $pdata[] = "<input type=\"checkbox\" name=\"chk[]\" value=\"$value[mid]\">";
   $pdata[] = substr($value[regdt],0,10);
   
   $sns = "";
   $sns_data = $m_member->getLogSnsLogin($cid,$value[sns_id]);
   if($sns_data) {
   	$sns = "&nbsp;&nbsp;&nbsp;<img src='../img/$sns_data[sns_type].png' height='20px' alt='$sns_data[sns_id]' />";
   }   
   
   $login = "";
   $login = "<br><a href=\"indb.php?mode=login&mid=". urlencode($value[mid]) ."\" target=\"_blank\" class=\"eng red\">-Login</a>";
   
   $pdata[] = $value[mid].$sns.$login;
   $pdata[] = $value[name];
   $pdata[] = ($value[mobile])?$value[mobile]:$value[phone];
   $pdata[] = $r_grp[$value[grpno]];
   //$pdata[] = "<u onclick=\"popup('orderlist.p.php?mid=$value[mid]',630,420)\" class=\"hand\">".number_format($value[totpayprice])._("원")."</u>";
   
   $pdata[] = "<u onclick=\"popup('member_emoney_detail_popup.php?mid=$value[mid]',1000,800)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[emoney])._("원")."</u>";
   
   $pdata[] = "<u onclick=\"popup('member_detail_popup.php?mode=member_modify&mid=$value[mid]',1100,800)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[totpayprice])._("원")."</u>";
   
   $pdata[] = $r_state[$value[state]];

   if ($value[member_type] == "FIX") 
      $pdata[] = "<font color=red>"._("정액")."</b>";
   else
      $pdata[] = _("일반");

   $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=\"location.href='member_modify.php?mid=$value[mid]';\">"._("수정")."</button>";
   $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-danger\" onclick=\"mid_delete('".$value[mid]."');\">"._("삭제")."</button>";

    if ($cfg[skin_theme] == "M2") {
        $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-default\" onclick=\"popup('/miodio/upload_popup.php?mid=$value[mid]',1000,800);\">"._("사진업로드")."</button>";
    }
    else {
        $pdata[] = "";
    }
    
   $psublist[] = $pdata;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>