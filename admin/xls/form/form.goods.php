<?

$res = $db->query($query);
$loop = array();
while ($goods = $db->fetch($res)){

	$goods[img] = explode("||",$goods[img]);
	foreach ($goods[img] as $k=>$v){
		if ($v){
			$goods[img][$k] = "http://$_SERVER[HTTP_HOST]/data/goods/l/$goods[goodsno]/$v";
		}
	}
	if ($goods[listimg]) $goods[listimg] = "http://$_SERVER[HTTP_HOST]/data/goods/s/$goods[goodsno]/$goods[listimg]";
	$category = $db->fetch("select * from exm_goods_link where cid = '$cid' and goodsno = '$goods[goodsno]'");
	$goods[catnm] = getCatnm($category[catno],1);

	list ($goods[rid_compnm],$goods[nicknm]) = $db->fetch("select compnm,nicknm from exm_release where rid = '$goods[rid]'",1);
	list ($goods[brandnm]) = $db->fetch("select brandnm from exm_brand where brandno = '$goods[brandno]'",1);

	$opt = $addopt = $printopt = array();

	if ($goods[useopt]){
		$query = "select * from exm_goods_opt where goodsno = '$goods[goodsno]'";
		$res2 = $db->query($query);
		while ($tmp = $db->fetch($res2)){
			$query = "select aprice from exm_goods_opt_price where cid = '$cid' and goodsno = '$goods[goodsno]' and optno = '$tmp[optno]'";
			$tmp2 = $db->fetch($query);
			if (is_numeric($tmp2[aprice])) $tmp[aprice] = $tmp2[aprice];
			$opt[] = $tmp;
		}
	}

	$query = "select * from exm_goods_addopt_bundle where goodsno = '$goods[goodsno]'";
	$res2 = $db->query($query);
	while ($tmp = $db->fetch($res2)){
		$query = "select * from exm_goods_addopt where goodsno = '$goods[goodsno]' and addopt_bundle_no = '$tmp[addopt_bundle_no]'";
		$res3 = $db->query($query);
		while ($tmp2 = $db->fetch($res3)){
			$tmp2 = array_merge($tmp2,$tmp);

			$query = "select addopt_aprice from exm_goods_addopt_price where cid = '$cid' and goodsno = '$goods[goodsno]' and addoptno = '$tmp2[addoptno]'";
			$tmp3 = $db->fetch($query);
			if (is_numeric($tmp3[addopt_aprice])) $tmp2[addopt_aprice] = $tmp3[addopt_aprice];

			$addopt[] = $tmp2;
		}
	}

	$query = "select * from exm_goods_printopt where goodsno = '$goods[goodsno]'";
	$res2 = $db->query($query);
	while ($tmp = $db->fetch($res2)){
		$query = "select print_price from exm_goods_printopt_price where cid = '$cid' and goodsno = '$goods[goodsno]' and printoptnm = '$tmp[printoptnm]'";
		$tmp2 = $db->fetch($query);
		if (is_numeric($tmp2[print_price])) $tmp[print_price] = $tmp2[print_price];
		$printopt[] = $tmp;
	}

	$r_cnt = array(
		1,
		count($opt),
		count($addopt),
		count($printopt),
	);

	$query = "select
		mall_pageprice,
		mall_pagereserve,
		strprice,
		goodsno,
		price,
		reserve,
		self_deliv,
		self_dtype,
		self_dprice,
		b2b_goodsno,
		csummary,
		csearch_word,
		cmatch_goodsnm,
		cetc1,
		cetc2,
		cetc3
	from
		exm_goods_cid
	where
		cid = '$cid'
		and goodsno = '$data[goodsno]'
	";
	$tmp = $db->fetch($query);
	if (is_numeric($tmp[price])) $goods[price] = $tmp[price];
	if ($tmp[mall_pageprice]) $goods[pageprice] = $tmp[mall_pageprice];

	for ($i=0;$i<max($r_cnt);$i++){
		unset($data);

		$data[goodsno]			= $goods[goodsno];
		$data[img1]				= $goods[img][0];
		$data[img2]				= $goods[img][1];
		$data[img3]				= $goods[img][2];
		$data[img4]				= $goods[img][3];
		$data[img5]				= $goods[img][4];
		$data[listimg]			= $goods[listimg];
		$data[desc]				= htmlspecialchars($goods[desc]);
		$data[catnm1]			= $goods[catnm][0];
		$data[catnm2]			= $goods[catnm][1];
		$data[catnm3]			= $goods[catnm][2];
		$data[catnm4]			= $goods[catnm][3];
		$data[business]			= getBidGoods($goods[goodsno]);
		$data[rid_compnm]		= $goods[rid_compnm];
		$data[nicknm]			= $goods[nicknm];
		$data[goodsnm]			= $goods[goodsnm];
		$data[brandnm]			= $goods[brandnm];
		$data[origin]			= $goods[origin];
		$data[leadtime]			= $goods[leadtime];
		$data[privatecid]		= $goods[privatecid];
		$data[adminmemo]		= $goods[adminmemo];
		$data[podsno]			= $goods[podsno];
		$data[podskindstr]		= $r_podskind[$goods[podskind]];
		$data[statestr]			= $r_goods_state[$goods[state]];
		$data[usestock]			= ($goods[usestock]) ? "Y":"N";
		$data[csummary]			= $tmp[csummary];
		$data[csearch_word]		= $tmp[csearch_word];
		$data[cmatch_goodsnm]	= $tmp[cmatch_goodsnm];
		$data[cetc1]			= $tmp[cetc1];
		$data[cetc2]			= $tmp[cetc2];
		$data[cetc3]			= $tmp[cetc3];
    
    
    $data[shiptypestr] = $r_shiptype[$goods[shiptype]];
    /*    
		switch ($goods[shiptype]){
			case "0":
				$data[shiptypestr] = "일반배송";
				break;
			case "1":
				$data[shiptypestr] = "무료배송";
				break;
			case "2":
				$data[shiptypestr] = "개별배송";
				break;
		}    
    */
    
		if ($goods[shiptype]==2){
			$data[shipprice]		= $goods[shipprice];
			$data[oshipprice]		= $goods[oshipprice];
		}

		if ($goods[reg_cid]==$cid) $data[cost]				= $goods[oprice];
		$data[supply]			= $goods[sprice];
		$data[cprice]			= $goods[cprice];
		$data[price]			= $goods[price];

		if ($goods[reg_cid]==$cid) $data[addpage_cost]		= $goods[opageprice];
		$data[addpage_supply]	= $goods[spageprice];
		$data[addpage_price]	= $goods[pageprice];
		$data[totstock]			= $goods[totstock];
		$data[useopt]			= ($goods[useopt]) ? "Y":"N";
		$data[optnm1]		= $goods[optnm1];
		$data[optnm2]		= $goods[optnm2];
		if ($opt[$i][optno]){
			$data[opt1]			= $opt[$i][opt1];
			$data[opt2]			= $opt[$i][opt2];
			$data[opt_podsno]	= $opt[$i][podsno];
			$data[opt_podoptno]	= $opt[$i][podoptno];
			if ($goods[reg_cid]==$cid) $data[opt_cost]		= $opt[$i][aoprice];
			$data[opt_supply]	= $opt[$i][asprice];
			$data[opt_price]	= $opt[$i][aprice];
			$data[opt_stock]	= $opt[$i][stock];
			$data[opt_view]		= ($opt[$i][opt_view]) ? "N":"Y";
		}

		if ($printopt[$i][printoptnm]){
			$data[printoptnm]		= $printopt[$i][printoptnm];
			$data[print_size]		= $printopt[$i][print_size];
			if ($goods[reg_cid]==$cid) $data[printopt_cost]	= $printopt[$i][print_oprice];
			$data[printopt_supply]	= $printopt[$i][print_sprice];
			$data[printopt_price]	= $printopt[$i][print_price];
		}

		if ($addopt[$i][addoptno]){
			$data[addopt_bundle_no]		= $addopt[$i][addopt_bundle_no];
			$data[addopt_bundle_name]		= $addopt[$i][addopt_bundle_name];
			$data[addopt_bundle_required]	= ($addopt[$i][addopt_bundle_required]) ? "Y":"N";
			$data[addopt_bundle_view]		= ($addopt[$i][addopt_bundle_view]) ? "N":"Y";
			$data[addoptnm]					= $addopt[$i][addoptnm];
			if ($goods[reg_cid]==$cid) $data[addopt_cost]				= $addopt[$i][addopt_aoprice];
			$data[addopt_supply]			= $addopt[$i][addopt_asprice];
			$data[addopt_price]				= $addopt[$i][addopt_aprice];
			$data[addopt_view]				= ($addopt[$i][addopt_view]) ? "N":"Y";
			$cnt2[$addopt[$i][addopt_bundle_no]]++;
		}
		$loop[] = $data;
		$cnt[$goods[goodsno]]++;
	}


}

?>

<meta http-equiv="Content-Type" content="application/vnd.ms-excel;charset=utf-8"/>
<table>
<tr>
<? foreach ($columns as $column_code){ ?>
	<th style="border:thin solid #666666;white-space:nowrap"><?=$r_column[$column_code]?></th>
<? } ?>
</tr>
<?
$view_goods = array();
foreach ($loop as $k=>$v){
?>
<tr>
	<?
	foreach ($columns as $column_code){
		if (!in_array($column_code,$columns2)) continue;

		unset($rowspan);
		if (in_array($column_code,$r_rowspan[goodsno])){
			$rowspan = $cnt[$v[goodsno]];
			if ($view_goods[$v[goodsno]][$column_code]) continue;
			$view_goods[$v[goodsno]][$column_code] = true;
		} else if (in_array($column_code,$r_rowspan[addopt_bundle_no]) && $v[addopt_bundle_no]){
			$rowspan = $cnt2[$v[addopt_bundle_no]];
			if ($view_addopt_bundle[$v[goodsno]][$v[addopt_bundle_no]][$column_code]) continue;
			$view_addopt_bundle[$v[goodsno]][$v[addopt_bundle_no]][$column_code] = true;
		}
	?>
	<td <?if ($rowspan){?>rowspan="<?=$rowspan?>"<?}?> style="border:thin solid #666666;white-space:nowrap;<?if (!in_array($column_code,$r_num_flds)){?>mso-number-format:'@';<?}?>"><?=$v[$column_code]?></td>
	<? } ?>
</tr>
<? } ?>
</table>

<style>
table {border-collapse:collapse;font:9pt 돋움;}
th {border:thin solid #666666;white-space:nowrap}
td {border:thin solid #666666;white-space:nowrap}
</style>