<?

include "../_header.php";

function f_get_delivery($goodsno){
global $db,$cid;

	$data = $db->fetch("select shiptype,shipprice,rid,self_deliv,self_dtype,self_dprice from exm_goods a inner join exm_goods_cid b on a.goodsno = b.goodsno where a.goodsno = '$goodsno' and b.cid = '$cid'");
	
	if (!$data[self_deliv]){
		if ($data[shiptype]==0){
			list($data[shipconditional],$data[r_shiptype],$data[release],$data[shipprice],$data[oshipprice]) = $db->fetch("select shipconditional,shiptype,nicknm,shipprice,oshipprice from exm_release where rid = '$data[rid]'",1);
		}
	} else {
		$data[shiptype] = $data[self_dtype];
		global $cfg;
		if ($data[shiptype]==0){
			$data[shipconditional] = $cfg[shipconfig][shipconditional];
			$data[r_shiptype] = $cfg[shipconfig][shiptype];
			$data[release] = $cfg[nameSite];
			$data[shipprice] = $cfg[shipconfig][shipprice];
			$data[oshipprice] = 0;
		}
	}
	return $data;
}

$query = "
select
	*,if(b.price is null,a.price,b.price) price,
	if(b.`desc` ='',a.`desc`,b.`desc`) `desc`
from
	exm_goods a
	inner join exm_goods_cid b on a.goodsno = b.goodsno
	inner join exm_category c on b.cid = c.cid
where
	a.goodsno = '$_GET[goodsno]'
	and b.cid = '$cid'
	and c.catno = '$_GET[catno]'
";
$data = $db->fetch($query);

if ($sess[bid]) $data[price] = get_business_goods_price($data[goodsno],$data[price]);
if ($sess[bid]) $data[reserve] = get_business_goods_reserve($data[goodsno],$data[reserve]);
list($data[brand]) = $db->fetch("select brandnm from exm_brand where brandno = '$data[brandno]'",1);
list($data[release]) = $db->fetch("select nicknm from exm_release where rid = '$data[rid]'",1);

$shipret = f_get_delivery($data[goodsno]);
if ($shipret[shiptype]==0){
	if ($shipret[r_shiptype]==0){
		$data[shipprice] = number_format($shipret[shipprice]);
	} else if ($shipret[r_shiptype]==1 || $shipret[r_shiptype]==4){  //"무료배송" ||  착불배송;
		$data[shipprice] = $r_shiptype[$shipret[r_shiptype]];   
	} else {
		$data[shipprice] = number_format($shipret[shipprice])._("원")." (".number_format($shipret[shipconditional])._("원 이상일 경우 주문금액이 무료").")";
	}
} else if ($shipret[shiptype]==1  || $shipret[shiptype]==4){  //"무료배송" ||  착불배송;
	$data[shipprice] = $r_shiptype[$shipret[shiptype]];   
} else if ($shipret[shiptype]==2){
	$data[shipprice] = _("개별배송비")." : ".number_format($shipret[shipprice]);
}

if ($_GET[optno]){
	# 상품옵션정보 exm_goods_opt 데이터 수집 (옵션,옵션가 및 상품정보)
	$query = "
	select
		a.*,
		if(b.aprice is null,a.aprice,b.aprice) aprice,
		areserve
	from
		exm_goods_opt a
		left join exm_goods_opt_price b on b.cid = '$cid' and a.goodsno = b.goodsno and a.optno = b.optno
	where
		a.goodsno = '$data[goodsno]'
		and a.optno = '$_GET[optno]'
	";
	$tmp = $db->fetch($query);

	if (!$data[optnm1]) $data[optnm1] = _("옵션1");
	if (!$data[optnm2]) $data[optnm2] = _("옵션2");
	if ($tmp[opt1]) $tmp[opt][] = $data[optnm1].":".$tmp[opt1];
	if ($tmp[opt2]) $tmp[opt][] = $data[optnm2].":".$tmp[opt2];
	if (is_array($tmp[opt])) $tmp[opt] = implode(" / ",$tmp[opt]);

	if ($sess[bid]){
		$tmp[aprice] = get_business_goods_opt_price($data[goodsno],$data[optno],$tmp[aprice]);
		$tmp[areserve] = get_business_goods_opt_reserve($data[goodsno],$data[optno],$tmp[areserve]);
	}
	$data[opt] = $tmp[opt];
	$data[price_opt] = $tmp[aprice];
	$data[reserve_opt] = $tmp[areserve];
}

if ($_GET[addopt]){
	$_GET[addopt] = explode(",",$_GET[addopt]);
	foreach ($_GET[addopt] as $k=>$v){
		if (!$v) continue;

		$query = "
		select
			a.*,
			if(b.addopt_aprice is null,a.addopt_aprice,b.addopt_aprice) addopt_aprice,
			addopt_areserve,
			c.*
		from
			exm_goods_addopt a
			left join exm_goods_addopt_price b on b.cid = '$cid' and a.addoptno = b.addoptno
			inner join exm_goods_addopt_bundle c on a.goodsno = c.goodsno and a.addopt_bundle_no = c.addopt_bundle_no
		where
			a.goodsno = '$data[goodsno]'
			and a.addoptno = '$v'
		";
		$tmp = $db->fetch($query);

		if ($sess[bid]){
			$tmp[addopt_aprice] = get_business_goods_addopt_price($data[goodsno],$v,$tmp[addopt_aprice]);
			$tmp[addopt_areserve] = get_business_goods_addopt_reserve($data[goodsno],$v,$tmp[addopt_areserve]);
		}
		$data[price_addopt] += $tmp[addopt_aprice];
		$data[reserve_addopt] += $tmp[addopt_areserve];
		$data[addopt][] = $tmp[addopt_bundle_name].":".$tmp[addoptnm];
	}
	if (is_array($data[addopt_str])) $data[addopt_str] = implode(" / ",$data[addopt_str]);
}
//if ($data[opt_str]) $data[goodsnm] = $data[goodsnm]."<br/>($data[opt_str])";
//if ($data[addopt_str]) $data[goodsnm] = $data[goodsnm]."<br/>($data[addopt_str])";
$data[price_total] = $data[price] + $data[price_opt] + $data[price_addopt];
$data[reserve_total] = $data[reserve] + $data[reserve_opt] + $data[reserve_addopt];

$data[price] = number_format($data[price]);
$data[reserve] = number_format($data[reserve]);
$data[price_total] = number_format($data[price_total]);
$data[reserve_total] = number_format($data[reserve_total]);
$data[price_opt] = number_format($data[price_opt]);
$data[price_addopt] = number_format($data[price_addopt]);
$data[reserve_opt] = number_format($data[reserve_opt]);
$data[reserve_addopt] = number_format($data[reserve_addopt]);
//기본 인트로
$cfg[pod_intro] = str_replace("{goodsnm}",$data[goodsnm],$cfg[pod_intro]);
$cfg[pod_intro] = str_replace("{price}",$data[price],$cfg[pod_intro]);
$cfg[pod_intro] = str_replace("{reserve}",$data[reserve],$cfg[pod_intro]);
$cfg[pod_intro] = str_replace("{goodsno}",$data[goodsno],$cfg[pod_intro]);
$cfg[pod_intro] = str_replace("{release}",$data[release],$cfg[pod_intro]);
$cfg[pod_intro] = str_replace("{brand}",$data[brand],$cfg[pod_intro]);
$cfg[pod_intro] = str_replace("{shipprice}",$data[shipprice],$cfg[pod_intro]);
$cfg[pod_intro] = str_replace("{leadtime}",$data[leadtime],$cfg[pod_intro]);
$cfg[pod_intro] = str_replace("{ea}",$_GET[ea],$cfg[pod_intro]);
$cfg[pod_intro] = str_replace("{price_opt}",$data[price_opt],$cfg[pod_intro]);
$cfg[pod_intro] = str_replace("{price_addopt}",$data[price_addopt],$cfg[pod_intro]);
$cfg[pod_intro] = str_replace("{reserve_opt}",$data[reserve_opt],$cfg[pod_intro]);
$cfg[pod_intro] = str_replace("{reserve_addopt}",$data[reserve_addopt],$cfg[pod_intro]);
$cfg[pod_intro] = str_replace("{opt}",$data[opt],$cfg[pod_intro]);
$cfg[pod_intro] = str_replace("{addopt}",$data[addopt],$cfg[pod_intro]);
$cfg[pod_intro] = str_replace("{price_total}",$data[price_total],$cfg[pod_intro]);
$cfg[pod_intro] = str_replace("{reserve_total}",$data[reserve_total],$cfg[pod_intro]);
//카테고리별 인트로
$data[introhtml] = str_replace("{goodsnm}",$data[goodsnm],$data[introhtml]);
$data[introhtml] = str_replace("{price}",$data[price],$data[introhtml]);
$data[introhtml] = str_replace("{reserve}",$data[reserve],$data[introhtml]);
$data[introhtml] = str_replace("{goodsno}",$data[goodsno],$data[introhtml]);
$data[introhtml] = str_replace("{release}",$data[release],$data[introhtml]);
$data[introhtml] = str_replace("{brand}",$data[brand],$data[introhtml]);
$data[introhtml] = str_replace("{shipprice}",$data[shipprice],$data[introhtml]);
$data[introhtml] = str_replace("{leadtime}",$data[leadtime],$data[introhtml]);
$data[introhtml] = str_replace("{ea}",$_GET[ea],$data[introhtml]);
$data[introhtml] = str_replace("{price_opt}",$data[price_opt],$data[introhtml]);
$data[introhtml] = str_replace("{price_addopt}",$data[price_addopt],$data[introhtml]);
$data[introhtml] = str_replace("{reserve_opt}",$data[reserve_opt],$data[introhtml]);
$data[introhtml] = str_replace("{reserve_addopt}",$data[reserve_addopt],$data[introhtml]);
$data[introhtml] = str_replace("{opt}",$data[opt],$data[introhtml]);
$data[introhtml] = str_replace("{addopt}",$data[addopt],$data[introhtml]);
$data[introhtml] = str_replace("{price_total}",$data[price_total],$data[introhtml]);
$data[introhtml] = str_replace("{reserve_total}",$data[reserve_total],$data[introhtml]);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<head>
<script language="javascript">

	function enableButtons() 
	{
		var btn1 = document.getElementById("btn1");
		var btn2 = document.getElementById("btn2");

		btn1.disabled = false;
		btn2.disabled = false;
	}
	
	function disableButtons() 
	{
		var btn1 = document.getElementById("btn1");
		var btn2 = document.getElementById("btn2");

		btn1.disabled = true;
		btn2.disabled = true;
	}
	
	function setProgress1(message, range, pos) 
	{
		//전체 파일 관련 프로그래스바 처리로직
//		document.getElementById("setProgress1").innerHTML += "<div>"+message+"/"+range+"/"+pos+"</div>";
	}
	
	function setProgress2(message, range, pos) 
	{
		//개별 파일 관련 프로그래스바 처리로직
//		document.getElementById("setProgress2").innerHTML += "<div>"+message+"/"+range+"/"+pos+"</div>";
	}

	function quitEditor() 
	{
		//취소 등의 경우 편집기에게 종료시키라는 명령을 전달
 		window.external.QuitEditor();
	}

</script>
<style type="text/css">
body {
    overflow:hidden;
    border: 0px none;
}
</style>
</head>

<body style="padding:0;margin:0;">
<? //2014.01.14 / minks / catno값이 있고 is_intro가 1일 때 카테고리별 인트로가 적용되고 아닐 때는 기본 인트로가 적용됨 ?>
<? if($_GET[catno] && $data[is_intro] == '1') { //8453이슈로 스타일 막음 / 16.02.26 / kjm ?>
   <!--<div style="width:1011px;height:586px;overflow:hidden">-->
      <?=$data[introhtml]?>
   <!--</div>-->
<? } else { ?>
   <!--<div style="width:1011px;height:586px;overflow:hidden">-->
      <?=$cfg[pod_intro]?>
   <!--</div>-->
<? } ?>
</body>