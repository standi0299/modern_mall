<?


$r_goodsnm = array();
$r_rid = get_release();

$res = $db->query($query);
while ($data = $db->fetch($res)){
	$data[printopt] = unserialize($data[printopt]);

	foreach ($data[printopt] as $v){

		if (!$r_goodsnm[$data[goodsno]]){
			list($r_goodsnm[$data[goodsno]]) = $db->fetch("select goodsnm from exm_goods where goodsno = '$data[goodsno]'",1);
		}
		if (!$r_goodsnm[$data[goodsno]]){
			$r_goodsnm[$data[goodsno]] = $data[goodsnm];
		}
		$r_goodsnm[$data[goodsno]] = trim($r_goodsnm[$data[goodsno]]);
		if (!is_array($loop[$data[cid]][$data[goodsno]."|^|".$data[rid]][$v[printoptnm]][plus])) $loop[$data[cid]][$data[goodsno]."|^|".$data[rid]][$v[printoptnm]][plus] = array();
		/*if (!is_array($loop[$data[cid]][$data[goodsno]."|^|".$data[rid]][$v[printoptnm]][minus])) $loop[$data[cid]][$data[goodsno]."|^|".$data[rid]][$v[printoptnm]][minus] = array();
		if (!is_array($loop[$data[cid]][$data[goodsno]."|^|".$data[rid]][$v[printoptnm]][minus_ext])) $loop[$data[cid]][$data[goodsno]."|^|".$data[rid]][$v[printoptnm]][minus_ext] = array();

		if ($data[flag] > 0){*/
			$loop[$data[cid]][$data[goodsno]."|^|".$data[rid]][$v[printoptnm]][plus][] = $v[ea];
		/*} else {
			if ($_POST[dt1] && str_replace("-","",$data[paydt]) < str_replace("-","",$_POST[dt1])){
				$loop[$data[cid]][$data[goodsno]."|^|".$data[rid]][$v[printoptnm]][minus_ext][] = $v[ea];
			} else if ($_POST[dt2] && str_replace("-","",$data[paydt]) > str_replace("-","",$_POST[dt2])){
				$loop[$data[cid]][$data[goodsno]."|^|".$data[rid]][$v[printoptnm]][minus_ext][] = $v[ea];
			} else {
				$loop[$data[cid]][$data[goodsno]."|^|".$data[rid]][$v[printoptnm]][minus][] = $v[ea];
			}
		}*/

		$loop[$data[cid]][$data[goodsno]."|^|".$data[rid]][$v[printoptnm]][] = $v[ea];
	}
}

foreach ($loop as $k=>$v){
	foreach ($v as $k2=>$v2){
		foreach ($v2 as $k3=>$v3){

			unset($data);

			$dummy = explode("|^|",$k2);

			$query = "
			select
				a.print_oprice,
				a.print_sprice,
				if(b.print_price is null,a.print_price,b.print_price) print_price
			from
				exm_goods_printopt a
				left join exm_goods_printopt_price b on a.goodsno = b.goodsno and a.printoptnm = b.printoptnm and b.cid = '$k'
			where
				a.goodsno = '$dummy[0]'
				and a.printoptnm = '$k3'
			";
			list($cost,$supplyprice,$price) = $db->fetch($query,1);

			$data[goodsno] = trim($dummy[0]);
			$data[goodsnm] = trim($r_goodsnm[$dummy[0]]);
			$data[rid_compnm] = trim($r_rid[$dummy[1]]);
			$data[printoptnm] = trim($k3);
			$data[cost_printopt] = trim($cost);
			$data[supplyprice_printopt] = trim($supplyprice);
			//$data[price_printopt] = trim($price);
			$data[print_aprice] = trim($price);
			$data[ea] = number_format(array_sum($v3[plus]));
			$data[sum_price_printopt] = number_format(trim($price*array_sum($v3[plus])));
			/*$data[ea_minus] = number_format(-array_sum($v3[minus]));
			$data[sum_price_printopt_minus] = number_format(-trim($price*array_sum($v3[minus])));
			$data[ea_minus_ext] = number_format(-array_sum($v3[minus_ext]));
			$data[sum_price_printopt_minus_ext] = number_format(-trim($price*array_sum($v3[minus_ext])));
			
			$data[ea_total] = number_format(array_sum($v3[plus])-array_sum($v3[minus])-array_sum($v3[minus_ext]));
			$data[sum_price_printopt_total] = number_format(trim($price*array_sum($v3[plus]))-trim($price*array_sum($v3[minus]))-trim($price*array_sum($v3[minus_ext])));*/
			$data[ea_total] = number_format(array_sum($v3[plus]));
			$data[sum_price_printopt_total] = number_format(trim($price*array_sum($v3[plus])));
			
			$cnt[$data[goodsno]]++;

			$ret[] = $data;
		}
	}
}

unset($loop);
$loop = $ret;

?>

<meta http-equiv="Content-Type" content="application/vnd.ms-excel;charset=utf-8"/>
<table>
<tr>
<? foreach ($columns as $column_code){ ?>
	<th style="border:thin solid #666666;white-space:nowrap"><?=$r_column[$column_code]?></th>
<? } ?>
</tr>
<? foreach ($loop as $k=>$v){ ?>
<tr align="center">
	<?
	foreach ($columns as $column_code){
		$rowspan="1";
		if (in_array($column_code,$r_rowspan[goodsno])){
			$rowspan = $cnt[$v[goodsno]];
			if ($view_goodsno[$v[goodsno]][$column_code]) continue;
			$view_goodsno[$v[goodsno]][$column_code] = true;
		}
	?>
	<td <?if ($rowspan){?>rowspan="<?=$rowspan?>"<?}?> style="border:thin solid #666666;white-space:nowrap;<?if (!in_array($column_code,$r_num_flds)){?>mso-number-format:'@';<?}?>"><?=$v[$column_code]?></td>
	<? } ?>
</tr>
<? } ?>
</table>