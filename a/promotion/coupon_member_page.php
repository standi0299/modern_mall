<?
include "../lib.php";
include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

###쿠폰리스트###
## 회원별발행리스트
$r_kind = array("on" => _("온라인"), "off" => _("오프라인"));

$getData = json_decode(base64_decode($_GET[postData]),1);

$getData[coupon_setdt][0] = str_replace("-","",$getData[coupon_setdt][0]);
$getData[coupon_setdt][1] = str_replace("-","",$getData[coupon_setdt][1]);
$getData[coupon_usedt][0] = str_replace("-","",$getData[coupon_usedt][0]);
$getData[coupon_usedt][1] = str_replace("-","",$getData[coupon_usedt][1]);

if ($getData[coupon_code]) $code = $getData[coupon_code];
if ($getData[coupon_name]) $name = $getData[coupon_name];
if (is_numeric($getData[coupon_use])) $use = $getData[coupon_use];
if ($getData[mid]) $mid = $getData[mid];

if ($getData[coupon_setdt][0]) $setdt1 = $getData[coupon_setdt][0];
if ($getData[coupon_setdt][1]) $setdt2 = $getData[coupon_setdt][1];
if ($getData[coupon_usedt][0]) $usedt1 = $getData[coupon_usedt][0];
if ($getData[coupon_usedt][1]) $usedt2 = $getData[coupon_usedt][1];

$limit = " limit $_POST[start], $_POST[length]";

$m_etc = new M_etc();
$list = $m_etc -> getCouponMemberList($cid, $_GET[kind], $code, $name, $use, $mid, $setdt1, $setdt2, $usedt1, $usedt2, $limit);

$totalCnt = count($list);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
   	$pdata = array();
	
	if ($value[coupon_type] == "coupon_money") {
		$coupon_able_money = " - "._("잔액")." : ".number_format($value[coupon_able_money]);
	}
	
	if ($_GET[kind] == "off") {
		$r_coupon_issue_code = ($value[coupon_issue_code_history]) ? explode("|",$value[coupon_issue_code_history]) : array($value[coupon_issue_code]);
		
		$coupon_issue_table = "<table class=\"tb1\" style=\"margin-top:5px;\">";
		
		foreach ($r_coupon_issue_code as $coupon_issue_code) {
			$coupon_issue_table .= "<tr>";
			$coupon_issue_table .= "<td>$coupon_issue_code</td>";
			$coupon_issue_table .= "<td width=\"60\" align=\"center\">";
			
			if (!$value[coupon_use] && count($r_coupon_issue_code) > 1) {
				$coupon_issue_table .= "<a href=\"indb.php?mode=del_coupon_money&no=$value[no]&coupon_issue_code=$coupon_issue_code\" onclick=\"return confirm('"._("정말로 삭제하시겠습니까?")."')\"><span class=\"btn btn-xs btn-danger\" style=\"margin:0;\">"._("삭제")."</span></a>";
			}
			
			$coupon_issue_table .= "</td>";
			$coupon_issue_table .= "</tr>";
		}
		
		$coupon_issue_table .= "</table>";			
	}
	
   	$pdata[] = $value[coupon_code];
   	$pdata[] = $value[coupon_name].$coupon_able_money.$coupon_issue_table;
	$pdata[] = substr($value[coupon_setdt],2,14);
	$pdata[] = $value[mid];
   	$pdata[] = ($value[coupon_usedt])? substr($value[coupon_usedt],2,14) : "";
    
	if ($value[coupon_use]) {
    	$pdata[] = _("사용")."&nbsp;<button type=\"button\" class=\"btn btn-xs btn-warning\" onclick=\"coupon_update_confirm('".$value[no]."', '0');\">"._("미사용으로전환")."</button>";
	}	else {
		$pdata[] = _("미사용")."&nbsp;<button type=\"button\" class=\"btn btn-xs btn-info\" onclick=\"coupon_update_confirm('".$value[no]."', '1');\">"._("사용으로전환")."</button>";
	}
		
    $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-danger\" onclick=\"coupon_delete('".$value[no]."');\">"._("삭제")."</button>";

   	$psublist[] = $pdata;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>