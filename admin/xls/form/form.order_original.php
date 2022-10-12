<? $db -> query("set names utf8");
$r_brand = get_brand();

$res = $db -> query($query);
$loop = array();
while ($pay = $db -> fetch($res)) {

    list($cid_info[compnm], $cid_info[sitenm], $cid_info[domain]) = $db -> fetch("select compnm,sitenm,domain from exm_mall where cid = '$pay[cid]'", 1);

    if ($pay[mid]) {
        $member = $db -> fetch("select * from exm_member where cid = '$pay[cid]' and mid = '$pay[mid]'");
        $manager = $db -> fetch("select a.* from exm_manager a inner join exm_member b on a.manager_no = b.manager_no where a.cid = '$pay[cid]' and b.cid = '$pay[cid]' and b.mid = '$pay[mid]'");
    }

    $query2 = "select * from exm_ord where payno = '$pay[payno]'";
    $res2 = $db -> query($query2);
    while ($ord = $db -> fetch($res2)) {

        list($ord[rid_compnm], $ord[nicknm]) = $db -> fetch("select compnm,nicknm from exm_release where rid = '$ord[rid]'", 1);
        $query3 = "select * from exm_ord_item where payno = '$pay[payno]' and ordno = '$ord[ordno]'";
        $res3 = $db -> query($query3);

        while ($item = $db -> fetch($res3)) {

            unset($data);

            list($item[brandno], $item[podskind], $item[goods_group_code]) = $db -> fetch("select brandno,podskind,goods_group_code from exm_goods where goodsno = '$item[goodsno]'", 1);
            list($item[est_order_option_desc]) = $db -> fetch("select est_order_option_desc from md_design_draft where payno='$pay[payno]'", 1);
            if (!$item[podskind])
                $item[podskind] = "";

            if ($item[dc_couponsetno]) {
                $item[coupon_info] = $db -> fetch("select * from exm_coupon_set where no = '$item[dc_couponsetno]'");

                list($item[coupon_info][coupon_name]) = $db -> fetch("select coupon_name from exm_coupon where cid = '$pay[cid]' and coupon_code = '{$item[coupon_info][coupon_code]}'", 1);
            }
            list($item[cmatch_goodsnm], $item[b2b_goodsno], $item[csummary], $item[csearch_word], $item[cresolution], $item[cgoods_size], $item[cetc1], $item[cetc2], $item[cetc3]) = $db -> fetch("select cmatch_goodsnm, b2b_goodsno, csummary, csearch_word, cresolution, cgoods_size, cetc1, cetc2, cetc3 from exm_goods_cid where cid = '$cid' and goodsno = '$item[goodsno]'", 1);

            $data[cid] = $pay[cid];
            $data[compnm] = $cid_info[compnm];
            $data[sitenm] = $cid_info[sitenm];
            $data[domain] = $cid_info[domain];
            $data[payno] = $pay[payno];
            $data[ordno] = $ord[ordno];
            $data[ordseq] = $item[ordseq];
            $data[itemstepstr] = $r_step[$item[itemstep]];
            $data[payprice] = $pay[totalprice];
            $data[reserve] = $item[reserve];
            $data[paid] = ($pay[paydt]) ? "Y" : "N";
            if ($pay[paystep] == 1)
                $data[paid] = "N";
            $data[paymethod] = $pay[paymethod];
            $data[paymethodstr] = $r_paymethod[$pay[paymethod]];
            $data[bankinfo] = $pay[bankinfo];
            $data[pgcode] = $pay[pgcode];
            $data[pglog] = $pay[pglog];
            $data[escrow] = ($pay[escrow]) ? "Y" : "N";
            $data[goodsno] = $item[goodsno];
            $data[goodsnm] = $item[goodsnm];
            $data[cmatch_goodsnm] = $item[cmatch_goodsnm];
            $data[brandnm] = $r_brand[$item[brandno]];
            $data[podskind] = $item[podskind];
            $item[opt] = explode(" / ", $item[opt]);
            foreach ($item[opt] as $k => $v) {
                $v = explode(":", $v);
                $item[optstr][$k] = $v[1];
            }
            $data[opt1] = $item[optstr][0];
            $data[opt2] = $item[optstr][1];
            if ($item[addopt]) {
                $item[addopt] = unserialize($item[addopt]);
                if (is_array($item[addopt])) {
                    foreach ($item[addopt] as $k => $v) {
                        $item[addopt_str][$k] = $v[addopt_bundle_name] . ":" . $v[addoptnm];
                    }
                    $item[addopt_str] = implode(" / ", $item[addopt_str]);
                }
            }
            $data[addopt_str] = $item[addopt_str];

            if ($item[printopt]) {
                $item[printopt] = unserialize($item[printopt]);
                if (is_array($item[printopt])) {
                    foreach ($item[printopt] as $k => $v) {
                        $item[pirntopt_str][$k] = $v[printoptnm] . ":" . $v[ea];
                    }
                    $item[pirntopt_str] = implode(" / ", $item[pirntopt_str]);
                }
            }
            $data[pirntopt_str] = $item[pirntopt_str];
            $data[addpage] = $item[addpage];
            $data[rid] = $ord[rid];
            $data[rid_compnm] = $ord[rid_compnm];
            $data[nicknm] = $ord[nicknm];
            $data[ea] = $item[ea];
            $data[cost_goods] = $item[cost_goods];
            $data[supply_goods] = $item[supplyprice_goods];
            $data[price_goods] = $item[goods_price];
            $data[cost_opt] = $item[cost_opt];
            $data[supply_opt] = $item[supplyprice_opt];
            $data[price_opt] = $item[aprice];
            $data[cost_addopt] = $item[cost_addopt];
            $data[supply_addopt] = $item[supplyprice_addopt];
            $data[price_addopt] = $item[addopt_aprice];
            $data[cost_printopt] = $item[cost_printopt];
            $data[supply_printopt] = $item[supplyprice_printopt];
            $data[price_printopt] = $item[print_aprice];
            $data[cost_addpage] = $item[cost_addpage];
            $data[supply_addpage] = $item[supplyprice_addpage];
            $data[price_addpage] = $item[addpage_aprice];
            $data[cost] = ($data[cost_goods] + $data[cost_opt] + $data[cost_addopt] + $data[cost_printopt] + $data[cost_addpage]) * $data[ea];
            $data[supply] = ($data[supply_goods] + $data[supply_opt] + $data[supply_addopt] + $data[supply_printopt] + $data[supply_addpage]) * $data[ea];
            $data[price] = ($data[price_goods] + $data[price_opt] + $data[price_addopt] + $data[price_printopt] + $data[price_addpage]) * $data[ea];
            $data[dc_member] = $item[dc_member] * $item[ea];
            $data[dc_coupon] = $item[dc_coupon];
            $data[dc_couponsetno] = $item[dc_couponsetno];
            $data[dc_coupon_code] = $item[coupon_info][coupon_code];
            $data[dc_coupon_name] = $item[coupon_info][coupon_name];
            $data[dc_coupon_issue_code] = $item[coupon_info][coupon_issue_code];
            $data[dc_emoney] = $pay[dc_emoney];
            $data[storageid] = $item[storageid];
            $data[pods_trans] = ($item[pods]) ? "T" : "F";
            $data[shipprice] = $ord[shipprice];
            $data[add_shipprice] = $pay[payshipprice]-$ord[shipprice];
            $data[shipcomp] = $item[shipcomp];
            list($item[shipcompnm], $item[shipcomp_oasis]) = $db -> fetch("select compnm,oasis_num from exm_shipcomp where shipno = '$item[shipcomp]'", 1);
            $data[shipcompnm] = $item[shipcompnm];
            $data[shipcomp_oasis] = $item[shipcomp_oasis];
            $data[shipcode] = $item[shipcode];
            $data[orderer_name] = $pay[orderer_name];
            $data[payer_name] = $pay[payer_name];
            $data[orderer_phone] = $pay[orderer_phone];
            $data[orderer_mobile] = $pay[orderer_mobile];
            $data[orderer_email] = $pay[orderer_email];
            $data[receiver_name] = $pay[receiver_name];
            $data[receiver_phone] = $pay[receiver_phone];
            $data[receiver_mobile] = $pay[receiver_mobile];
            $data[receiver_zipcode] = $pay[receiver_zipcode];
            $data[receiver_addr] = $pay[receiver_addr];
            $data[receiver_addr_sub] = $pay[receiver_addr_sub];
            $data[request2] = $pay[request2];
            $data[request] = $pay[request];
            $data[memo] = $pay[memo];
            $data[orddt] = $pay[orddt];
            $data[paydt] = ($pay[paymethod] == "t") ? $pay[confirmdt] : $pay[paydt];
            $data[confirmadmin] = $pay[confirmadmin];
            $data[shipdt] = $item[shipdt];
            $data[canceldt] = $pay[canceldt];
            $data[mid] = $pay[mid];
            $data[manager_name] = $manager[manager_name];
            $data[manager_email] = $manager[manager_email];
            $data[manager_phone] = $manager[manager_phone];
            $data[manager_mobile] = $manager[manager_mobile];
            $data[manager_dep] = $manager[manager_dep];
            $data[cust_name] = $member[cust_name];
            $data[cust_type] = $member[cust_type];
            $data[cust_class] = $member[cust_class];
            switch ($member[cust_tax_type]) {
                case "1" :
                    $data[cust_tax_type] = _("일반과세자");
                    break;
                case "2" :
                    $data[cust_tax_type] = _("법인사업자");
                    break;
            }
            $data[cust_no] = $member[cust_no];
            $data[cust_ceo] = $member[cust_ceo];
            $data[cust_ceo_phone] = $member[cust_ceo_phone];
            $data[cust_zipcode] = $member[cust_zipcode];
            $data[cust_address] = $member[cust_address] . " " . $member[cust_address_sub];
            $data[cust_address_en] = $member[cust_address_en];
            $data[cust_phone] = $member[cust_phone];
            $data[cust_fax] = $member[cust_fax];
            $data[cust_bank_name] = $member[cust_bank_name];
            $data[cust_bank_no] = $member[cust_bank_no];
            $data[cust_bank_owner] = $member[cust_bank_owner];
            $data[etc1] = $member[etc1];
            $data[etc2] = $member[etc2];
            $data[etc3] = $member[etc3];
            $data[etc4] = $member[etc4];
            $data[etc5] = $member[etc5];

            $data[b2b_goodsno] = $item[b2b_goodsno];
            $data[csummary] = $item[csummary];
            $data[csearch_word] = $item[csearch_word];
            $data[cresolution] = $item[cresolution];
            $data[cgoods_size] = $item[cgoods_size];
            $data[cetc1] = $item[cetc1];
            $data[cetc2] = $item[cetc2];
            $data[cetc3] = $item[cetc3];

            $goods_group_code_name = "";
            switch($item[goods_group_code]){
                case 10 : $goods_group_code_name = "일반상품"; break;
                case 20 : $goods_group_code_name = "스튜디오견적"; break;
                case 30 : $goods_group_code_name = "인쇄견적"; break;
                case 40 : $goods_group_code_name = "결제전용상품"; break;
                case 50 : $goods_group_code_name = "패키지상품"; break;
                case 60 : $goods_group_code_name = "견적상품(인터프로)"; break;                
            }
            $data[goods_group_code] = $goods_group_code_name;
            $data[est_order_option_desc] = $item[est_order_option_desc];

            $data[completedt] = $pay[completedt];

            $loop[] = $data;

            $cnt[$data[payno]][$data[ordno]]++;
        }
    }
}
?>

<meta http-equiv="Content-Type" content="application/vnd.ms-excel;charset=utf-8"/>
<table>
<tr>
<? foreach ($columns as $column_code){ ?>
	<th style="border:thin solid #666666;white-space:nowrap;background: #FFFF00;"><?=$r_column[$column_code]?></th>
<? } ?>
</tr>
<? foreach ($loop as $k=>$v){ ?>
<tr>
	<?
	foreach ($columns as $column_code){
		if (!in_array($column_code,$columns2)) continue;
		$rowspan = 1;
        if (in_array($column_code,$r_rowspan[payno])){
            $rowspan = array_sum($cnt[$v[payno]]);
            if ($view_pay[$v[payno]][$column_code]) continue;
            $view_pay[$v[payno]][$column_code] = true;
        } else if (in_array($column_code,$r_rowspan[nicknm])){
            $rowspan = $cnt[$v[payno]][$v[ordno]];
            if ($view_ord[$v[payno]][$v[ordno]][$column_code]) continue;
            $view_ord[$v[payno]][$v[ordno]][$column_code] = true;
        }

    ?>

    
	<td <?if ($rowspan){?>rowspan="<?=$rowspan?>"<? } ?> style="border:thin solid #666666;white-space:nowrap;<?if (!in_array($column_code,$r_num_flds)){?>mso-number-format:'@';<? } ?>"><?=$v[$column_code]?></td>
	<? } ?>
</tr>
<? } ?>
</table>

<style>
	table {
		border-collapse: collapse;
		font: 9pt 돋움;
	}
	th {
		border: thin solid #666666;
		white-space: nowrap
	}
	td {
		border: thin solid #666666;
		white-space: nowrap
	}
</style>