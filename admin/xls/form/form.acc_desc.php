<? $db->query("set names utf8"); 
$r_rid = get_release();
$res = $db->query($query);
$loop = array();
while ($data = $db->fetch($res)){
	$tmp = array();

	if ($data[opt]){
		$data[opt] = explode(" / ",$data[opt]);
		foreach ($data[opt] as $k=>$v){
			$v = explode(":",$v);
			$data[optstr][$k] = $v[1];
        }
	}

	if ($data[addopt]){
		$data[addopt] = unserialize($data[addopt]);
		if (is_array($data[addopt])){
			foreach ($data[addopt] as $k=>$v){
				$data[addopt_str][$k] = $v[addopt_bundle_name].":".$v[addoptnm];
			}
			$data[addopt_str] = implode(" / ",$data[addopt_str]);
		}
	}
    
	if ($data[printopt]){
        $data[printopt] = unserialize($item[printopt]);
        if (is_array($data[printopt])){
            foreach ($data[printopt] as $k=>$v){
                $data[pirntopt_str][$k] = $v[printoptnm].":".$v[ea];
            }
            $data[pirntopt_str] = implode(" / ",$data[pirntopt_str]);
        }
    }
	/*$data[cost] = (($data[cost_goods] + $data[cost_opt] + $data[cost_addopt] + $data[cost_printopt] + $data[cost_addpage])*$data[ea] + $data[cost_ship])*$data[flag];
	$data[supplyprice] = (($data[supplyprice_goods] + $data[supplyprice_opt] + $data[supplyprice_addopt] + $data[supplyprice_printopt] + $data[supplyprice_addpage])*$data[ea] + $data[supplyprice_ship])*$data[flag];
	$data[price] = (($data[price_goods] + $data[price_opt] + $data[price_addopt] + $data[price_printopt] + $data[price_addpage])*$data[ea] + $data[price_ship])*$data[flag];*/
	
	//몰 별 정산률 추출 / 14.04.29 / kjm
	$accper = $db->fetch("select accper from exm_mall where cid = '$cid'");

	$data[cost] = ($data[cost_goods] + $data[cost_opt] + $data[cost_addopt] + $data[cost_printopt] + $data[cost_addpage])*$data[ea] + $data[oshipprice]; //원가
	
	//공급가를 정산 %에 맞춰서 계산 / 14.04.29 / kjm
	$data[supplyprice] = (($data[supplyprice_goods] + $data[supplyprice_opt] + $data[supplyprice_addopt] + $data[supplyprice_printopt] + $data[supplyprice_addpage])*$data[ea] + $data[shipprice]) * $accper[accper] / 100; //공급가
	$data[price] = ($data[goods_price] + $data[aprice] + $data[addopt_aprice] + $data[print_aprice] + $data[addpage_aprice])*$data[ea] + $data[shipprice]; //판매가

	if ($data[mid]){
		$member = $db->fetch("select * from exm_member where cid = '$data[cid]' and mid = '$data[mid]'");
		$manager = $db->fetch("select a.* from exm_manager a inner join exm_member b on a.manager_no = b.manager_no where a.cid = '$data[cid]' and b.cid = '$data[cid]' and b.mid = '$data[mid]'");
	}
	list($data[podskind]) = $db->fetch("select podskind from exm_goods where goodsno = '$data[goodsno]'",1);
	if (!$data[podskind]) $data[podskind] = "";

	list ($data[paydt]) = $db->fetch("select left(if(paymethod='t',confirmdt,paydt),10) paydt from exm_pay where payno = '$data[payno]'",1);
	//list ($data[cmatch_goodsnm]) = $db->fetch("select cmatch_goodsnm from exm_goods_cid where cid = '$cid' and goodsno = '$data[goodsno]'",1);

    list($data[cmatch_goodsnm], $data[b2b_goodsno], $data[csummary], $data[csearch_word], $data[cresolution], $data[cgoods_size], $data[cetc1], $data[cetc2], $data[cetc3]) = 
    $db -> fetch("select cmatch_goodsnm, b2b_goodsno, csummary, csearch_word, cresolution, cgoods_size, cetc1, cetc2, cetc3 from exm_goods_cid where cid = '$cid' and goodsno = '$data[goodsno]'", 1);

	$tmp[rid]				= $data[item_rid];
	$tmp[rid_compnm]		= $r_rid[$data[item_rid]];
	$tmp[payno]				= $data[payno];
	$tmp[ordno]				= $data[ordno];
	$tmp[ordseq]			= $data[ordseq];
	//$tmp[kind]			= $data[kind];
	//$tmp[kindstr]			= $r_acckind[$data[kind]];
	//$tmp[dt]				= $data[dt];
	$tmp[orddt]				= $data[orddt];
	$tmp[paydt]				= $data[paydt];
	$tmp[goodsno]			= $data[goodsno];
	$tmp[cmatch_goodsnm]	= $data[cmatch_goodsnm];
	$tmp[goodsnm]			= $data[goodsnm];
	$tmp[podskind]			= $data[podskind];
	$tmp[opt1]				= $data[optstr][0];
	$tmp[opt2]				= $data[optstr][1];
	$tmp[addopt_str]		= $data[addopt_str];
	$tmp[printopt_str]		= $data[printopt_str];
	$tmp[addpage]			= $data[addpage];
	$tmp[ea]				= $data[ea];
	/*$tmp[supply_goods]	= $data[supplyprice_goods]*$data[flag];
	$tmp[supply_opt]		= $data[supplyprice_opt]*$data[flag];
	$tmp[supply_addopt]		= $data[supplyprice_addopt]*$data[flag];
	$tmp[supply_printopt]	= $data[supplyprice_printopt]*$data[flag];
	$tmp[supply_addpage]	= $data[supplyprice_addpage]*$data[flag];
	$tmp[supply_ship]		= $data[supplyprice_ship]*$data[flag];*/
	
	//정산률에 맞춰서 공급가 설정 / 14.04.29 / kjm
	$tmp[supply_goods]		= $data[supplyprice_goods] * $accper[accper] / 100;
	$tmp[supply_opt]		= $data[supplyprice_opt];
	$tmp[supply_addopt]		= $data[supplyprice_addopt];
	$tmp[supply_printopt]	= $data[supplyprice_printopt];
	$tmp[supply_addpage]	= $data[supplyprice_addpage];
	$tmp[shipprice]			= $data[shipprice];
	$tmp[supply]		    = $data[supplyprice];
	/*$tmp[price_goods]		= $data[price_goods]*$data[flag];
	$tmp[price_opt]			= $data[price_opt]*$data[flag];
	$tmp[price_addopt]		= $data[price_addopt]*$data[flag];
	$tmp[price_printopt]	= $data[price_printopt]*$data[flag];
	$tmp[price_addpage]		= $data[price_addpage]*$data[flag];
	$tmp[price_ship]		= $data[price_ship]*$data[flag];*/
	$tmp[goods_price]		= $data[goods_price];
	$tmp[aprice]			= $data[aprice];
	$tmp[addopt_aprice]		= $data[addopt_aprice];
	$tmp[print_aprice]		= $data[print_aprice];
	$tmp[addpage_aprice]	= $data[addpage_aprice];
	$tmp[shipprice]			= $data[shipprice];
	$tmp[price]				= $data[price];
	$tmp[paymethod]			= $r_paymethod[$data[paymethod]];
	//$tmp[state]			= ($data[unitno])? "기생성":"미생성";
	$tmp[mid]				= $data[mid];
	$tmp[manager_name]      = $manager[manager_name];
	$tmp[manager_email]     = $manager[manager_email];
	$tmp[manager_phone]     = $manager[manager_phone];
	$tmp[manager_mobile]    = $manager[manager_mobile];
	$tmp[manager_dep]       = $manager[manager_dep];
	$tmp[cust_name]         = $member[cust_name];
	$tmp[cust_type]         = $member[cust_type];
	$tmp[cust_class]        = $member[cust_class];
	switch ($tmp[cust_tax_type]){
		case "1":
			$tmp[cust_tax_type] = _("일반과세자");
				break;
		case "2":
			$tmp[cust_tax_type] = _("법인사업자");
				break;
	}
	$tmp[cust_no]           = $member[cust_no];
	$tmp[cust_ceo]          = $member[cust_ceo];
	$tmp[cust_ceo_phone]    = $member[cust_ceo_phone];
	$tmp[cust_zipcode]      = $member[cust_zipcode];
	$tmp[cust_address]      = $member[cust_address]." ".$member[cust_address_sub];
	$tmp[cust_address_en]   = $member[cust_address_en];
	$tmp[cust_phone]        = $member[cust_phone];
	$tmp[cust_fax]          = $member[cust_fax];
	$tmp[cust_bank_name]    = $member[cust_bank_name];
	$tmp[cust_bank_no]      = $member[cust_bank_no];
	$tmp[cust_bank_owner]   = $member[cust_bank_owner];
	$tmp[etc1]              = $member[etc1];
	$tmp[etc2]              = $member[etc2];
	$tmp[etc3]              = $member[etc3];
	$tmp[etc4]              = $member[etc4];
	$tmp[etc5]              = $member[etc5];
    
    $tmp[b2b_goodsno]  = $data[b2b_goodsno];
    $tmp[csummary]     = $data[csummary];
    $tmp[csearch_word] = $data[csearch_word];
    $tmp[cresolution]  = $data[cresolution];
    $tmp[cgoods_size]  = $data[cgoods_size];
    $tmp[cetc1]        = $data[cetc1];
    $tmp[cetc2]        = $data[cetc2];
    $tmp[cetc3]        = $data[cetc3];
	
	$loop[] = $tmp;
}
?>

<meta http-equiv="Content-Type" content="application/vnd.ms-excel;charset=utf-8"/>
<table>
<tr>
<? foreach ($columns as $column_code){ ?>
	<th style="border:thin solid #666666;white-space:nowrap"><?=$r_column[$column_code]?></th>
<? } ?>
</tr>
<? foreach ($loop as $k=>$v){ ?>
<tr>
	<? foreach ($columns as $column_code){ ?>
	<td style="border:thin solid #666666;white-space:nowrap;
	<?if (!in_array($column_code,$r_num_flds)){?>
	    mso-number-format:'@';<?}?>">
	    <?=$v[$column_code]?>
	</td>
	<? } ?>
</tr>
<? } ?>
</table>