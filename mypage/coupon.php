<?
/*
* @date : 20190201
* @author : kdk
* @brief : B1 테마 발행일(coupon_setdt) 검색 추가.
* @request : 
* @desc : getMyCouponList(addwhere) 함수에 addwhere 파라메타 추가함.
* @todo : 
*/

include "../_header.php";
include "../lib/class.page.php";

chkMember();
$m_emoney = new M_emoney();
$m_member = new M_member();

if ($_GET[mobile_type] != "Y" && $cfg[skin_theme] == "P1") {
	if (!$_GET[coupon_type]) $_GET[coupon_type] = "coupon";
}

$addwhere = "";
if ($_GET[coupon_setdt_start]) $addwhere .= " and b.coupon_setdt>='{$_GET[coupon_setdt_start]} 00:00:00'";
if ($_GET[coupon_setdt_end]) $addwhere .= " and b.coupon_setdt<='{$_GET[coupon_setdt_end]} 23:59:59'";

//20160128 / minks / 적립금 조회를 위해 추가
//$memberdata = f_getMemeberData();
//$myemoney = $m_emoney->getSumEmoneyLog($cid, $sess[mid]);
$m_data = $m_member->getInfo($cid, $sess[mid]);
$myemoney = $m_data[emoney];

//20170629 / minks / 소멸예정 적립금 조회
$emoneydata = getCfg("", "emoney");

//노출되는 소멸예정 일자 오늘만 노출 / kdk / 20180328
$emoneydata[emoney_expire_day] = 1;
$expire_emoney = getExpireEmoneyTotal($cid, $sess[mid], $emoneydata[emoney_expire_day]);
//debug($expire_emoney);

$data = $m_member->getDownloadCouponList($cid, $sess[mid]);
foreach ($data as $k=>$v) {
	//20160128 / minks / 카테고리명 및 상품명 출력
	if ($_GET[mobile_type] == "Y") {
		if ($v[coupon_catno]) {
			$res = $db->query("select * from exm_category where cid='$cid' and catno in ($v[coupon_catno])");
			while ($category = $db->fetch($res)) $v[catnm][] = $category[catnm];
			$v[catnm] = implode("/", $v[catnm]);
		}
	
		if ($v[coupon_goodsno]) {
			$res2 = $db->query("select * from exm_goods where goodsno in ($v[coupon_goodsno])");
			while ($goods = $db->fetch($res2)) $v[goodsnm][] = $goods[goodsnm];
			$v[goodsnm] = implode("/", $v[goodsnm]);
		}
	} else if ($cfg[skin_theme] == "P1") {
		$nowdate = strtotime(date("Y-m-d", time()));
		$v[remain_date] = "";
		
		if ($v[coupon_period_system] == "date") {
			$coupon_period_sdate = strtotime($v[coupon_period_sdate]);
			$coupon_period_edate = strtotime($v[coupon_period_edate]);
			
			if (((($coupon_period_sdate - $nowdate) / 86400) < 0) && ((($coupon_period_edate - $nowdate) / 86400) >= 0)) {
				$v[remain_date] = (($coupon_period_edate - $nowdate) / 86400) + 1;
			}
		} else if ($v[coupon_period_system] == "deadline_date") {
			$coupon_period_deadline_date = strtotime($v[coupon_period_deadline_date]);
			
			if ((($coupon_period_deadline_date - $nowdate) / 86400) >= 0) {
				$v[remain_date] = (($coupon_period_deadline_date - $nowdate) / 86400) + 1;
			}
		}
	}
	
	$downcoupon[] = $v;
}

if ($_GET[mobile_type] != "Y" && $cfg[skin_theme] == "P1") $data2 = $m_member->getAllMyCouponList($cid, $sess[mid]);
else $data2 = $m_member->getMyCouponList($cid, $sess[mid], $addwhere);

foreach ($data2 as $k2=>$v2) {
	//20160128 / minks / 카테고리명 및 상품명 출력
	if ($_GET[mobile_type] == "Y") {
		if ($v2[coupon_catno]) {
			$res = $db->query("select * from exm_category where cid='$cid' and catno in ($v2[coupon_catno])");
			while ($category = $db->fetch($res)) $v2[catnm][] = $category[catnm];
			$v2[catnm] = implode("/", $v2[catnm]);
		}
	
		if ($v2[coupon_goodsno]) {
			$res2 = $db->query("select * from exm_goods where goodsno in ($v2[coupon_goodsno])");
			while ($goods = $db->fetch($res2)) $v2[goodsnm][] = $goods[goodsnm];
			$v2[goodsnm] = implode("/", $v2[goodsnm]);
		}
	} else if ($cfg[skin_theme] == "P1") {
		$nowdate = strtotime(date("Y-m-d", time()));
		$usabledt = strtotime($v2[usabledt]);
		$v2[remain_date] = "";
		
		if ((($usabledt - $nowdate) / 86400) >= 0) {
			$v2[remain_date] = (($usabledt - $nowdate) / 86400) + 1;
		}
	}
	
	$mycoupon[] = $v2;
}

//적립금내역
$db_table = "exm_log_emoney";

$where[] = "mid = '$sess[mid]'";
$where[] = "cid = '$cid'";

$_GET[regdt][0] = str_replace("-", "", $_GET[regdt][0]);
$_GET[regdt][1] = str_replace("-", "", $_GET[regdt][1]);

if ($_GET[regdt][0]) $where[] = "regdt > '{$_GET[regdt][0]}'";
if ($_GET[regdt][1]) $where[] = "regdt < adddate('{$_GET[regdt][1]}',interval 1 day)+0";

if ($_GET[mobile_type] == "Y") {
	foreach ($where as $k => $v) {
		if ($v) $where_v .= $v." and ";
	}

	list($emoney_log_cnt) = $db->fetch("select count(*) from ".$db_table." where ".substr($where_v, 0, -4), 1);
} else {
	$emoney_log_cnt = 10;
}


$pg = new Page($_GET[page], $emoney_log_cnt);
$pg->setQuery($db_table, $where, "regdt desc");
$pg->exec();

$res = &$pg->resource;
while ($data3 = $db->fetch($res)) {
    if ($data3[emoney] == "0") continue;
	$loop[] = $data3;
}
//debug($expire_emoney);
$tpl->assign('mycoupon',$mycoupon);
$tpl->assign('downcoupon',$downcoupon);
$tpl->assign('emoneydata', $emoneydata);
$tpl->assign('myemoney',$myemoney);
$tpl->assign('expire_emoney',$expire_emoney);
$tpl->assign('loop',$loop);
$tpl->assign('pg',$pg);
$tpl->print_('tpl');
?>