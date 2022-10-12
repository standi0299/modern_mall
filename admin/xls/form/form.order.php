<? $db -> query("set names utf8");
$r_brand = get_brand();

$res = $db -> query($query);
$loop = array();
while ($pay = $db -> fetch($res)) {

    list($cid_info[compnm], $cid_info[sitenm], $cid_info[domain]) = $db -> fetch("select compnm,sitenm,domain from exm_mall where cid = '$pay[cid]'", 1);

    if ($pay[mid]) {
        $member  = $db -> fetch("select * from exm_member where cid = '$pay[cid]' and mid = '$pay[mid]'");
        $manager = $db -> fetch("select a.* from exm_manager a inner join exm_member b on a.manager_no = b.manager_no where a.cid = '$pay[cid]' and b.cid = '$pay[cid]' and b.mid = '$pay[mid]'");
    }
    
    //아직 테스트중
    //루프를 여러번 돌지 않고 $pay의 데이터를 이용해서 모든 값을 지정
    //엑셀 출력시 조회한 상품에 대해서만 출력되도록 조정
    //값이 제대로 다 출력되는지 확인이 필요함 / 14.04.25 / kjm
    
    /*기존소스
    $query2 = "select * from exm_ord where payno = '$pay[payno]'";
    $res2 = $db -> query($query2);
    while ($ord = $db -> fetch($res2)) {
    */
        list($ord[rid_compnm], $ord[nicknm]) = $db -> fetch("select compnm,nicknm from exm_release where rid = '$pay[rid]'", 1);
        
        /*기존소스
        $query3 = "select * from exm_ord_item where payno = '$pay[payno]' and ordno = '$pay[ordno]'";
        $res3 = $db -> query($query3);
        while ($item = $db -> fetch($res3)) {
        */
            unset($data);

            list($item[brandno], $item[podskind]) = $db -> fetch("select brandno,podskind from exm_goods where goodsno = '$pay[goodsno]'", 1);
            if (!$item[podskind])
                $item[podskind] = "";

            if ($item[dc_couponsetno]) {
                $item[coupon_info] = $db -> fetch("select * from exm_coupon_set where no = '$pay[dc_couponsetno]'");

                list($item[coupon_info][coupon_name]) = $db -> fetch("select coupon_name from exm_coupon where cid = '$pay[cid]' and coupon_code = '{$pay[coupon_info][coupon_code]}'", 1);
            }
            list($item[cmatch_goodsnm], $item[b2b_goodsno], $item[csummary], $item[csearch_word], $item[cresolution], $item[cgoods_size], $item[cetc1], $item[cetc2], $item[cetc3]) = 
            $db -> fetch("select cmatch_goodsnm, b2b_goodsno, csummary, csearch_word, cresolution, cgoods_size, cetc1, cetc2, cetc3 from exm_goods_cid where cid = '$cid' and goodsno = '$pay[goodsno]'", 1);

            $data[cid] = $pay[cid];
            $data[compnm] = $cid_info[compnm];
            $data[sitenm] = $cid_info[sitenm];
            $data[domain] = $cid_info[domain];
            $data[payno] = $pay[payno];
            $data[ordno] = $pay[ordno];
            $data[ordseq] = $pay[ordseq];
            $data[itemstepstr] = $r_step[$pay[itemstep]];
            $data[payprice] = $pay[payprice];
            $data[reserve] = $pay[reserve];
            $data[paid] = ($pay[paydt]) ? "Y" : "N";
            if ($pay[paystep] == 1)
                $data[paid] = "N";
            $data[paymethod] = $pay[paymethod];
            $data[paymethodstr] = $r_paymethod[$pay[paymethod]];
            $data[bankinfo] = $pay[bankinfo];
            $data[pgcode] = $pay[pgcode];
            $data[pglog] = $pay[pglog];
            $data[escrow] = ($pay[escrow]) ? "Y" : "N";
            $data[goodsno] = $pay[goodsno];
            $data[goodsnm] = $pay[goodsnm];
            $data[cmatch_goodsnm] = $item[cmatch_goodsnm];
            $data[brandnm] = $r_brand[$item[brandno]];
            $data[podskind] = $item[podskind];
            $item[opt] = explode(" / ", $pay[opt]);
            foreach ($item[opt] as $k => $v) {
                $v = explode(":", $v);
                $item[optstr][$k] = $v[1];
            }
            $data[opt1] = $item[optstr][0];
            $data[opt2] = $item[optstr][1];
            if ($pay[addopt]) {
                $pay[addopt] = unserialize($pay[addopt]);
                if (is_array($pay[addopt])) {
                    foreach ($pay[addopt] as $k => $v) {
                        $pay[addopt_str][$k] = $v[addopt_bundle_name] . ":" . $v[addoptnm];
                    }
                    $pay[addopt_str] = implode(" / ", $pay[addopt_str]);
                }
            }
            $data[addopt_str] = $pay[addopt_str];

            if ($pay[printopt]) {
                $pay[printopt] = unserialize($pay[printopt]);
                if (is_array($pay[printopt])) {
                    foreach ($pay[printopt] as $k => $v) {
                        $pay[pirntopt_str][$k] = $v[printoptnm] . ":" . $v[ea];
                    }
                    $pay[pirntopt_str] = implode(" / ", $pay[pirntopt_str]);
                }
            }
            $data[pirntopt_str] = $pay[pirntopt_str];
            $data[addpage] = $pay[addpage];
            $data[rid] = $pay[rid];
            $data[rid_compnm] = $ord[rid_compnm];
            $data[nicknm] = $ord[nicknm];
            $data[ea] = $pay[ea];
            $data[cost_goods] = $pay[cost_goods];
            $data[supply_goods] = $pay[supplyprice_goods];
            $data[price_goods] = $pay[goods_price];
            $data[cost_opt] = $pay[cost_opt];
            $data[supply_opt] = $pay[supplyprice_opt];
            $data[price_opt] = $pay[aprice];
            $data[cost_addopt] = $pay[cost_addopt];
            $data[supply_addopt] = $pay[supplyprice_addopt];
            $data[price_addopt] = $pay[addopt_aprice];
            $data[cost_printopt] = $pay[cost_printopt];
            $data[supply_printopt] = $pay[supplyprice_printopt];
            $data[price_printopt] = $pay[print_aprice];
            $data[cost_addpage] = $pay[cost_addpage];
            $data[supply_addpage] = $pay[supplyprice_addpage];
            $data[price_addpage] = $pay[addpage_aprice];
            $data[cost] = ($data[cost_goods] + $data[cost_opt] + $data[cost_addopt] + $data[cost_printopt] + $data[cost_addpage]) * $data[ea];
            $data[supply] = ($data[supply_goods] + $data[supply_opt] + $data[supply_addopt] + $data[supply_printopt] + $data[supply_addpage]) * $data[ea];
            $data[price] = ($data[price_goods] + $data[price_opt] + $data[price_addopt] + $data[price_printopt] + $data[price_addpage]) * $data[ea];
            $data[dc_member] = $pay[dc_member] * $pay[ea];
            $data[dc_coupon] = $pay[dc_coupon];
            $data[dc_couponsetno] = $pay[dc_couponsetno];
            $data[dc_coupon_code] = $item[coupon_info][coupon_code];
            $data[dc_coupon_name] = $item[coupon_info][coupon_name];
            $data[dc_coupon_issue_code] = $item[coupon_info][coupon_issue_code];
            $data[dc_emoney] = $pay[dc_emoney];
            $data[storageid] = $pay[storageid];
            $data[pods_trans] = ($pay[pods]) ? "T" : "F";
            $data[shipprice] = $pay[shipprice];
            $data[shipcomp] = $pay[shipcomp];
            list($pay[shipcompnm], $pay[shipcomp_oasis]) = $db -> fetch("select compnm,oasis_num from exm_shipcomp where shipno = '$pay[shipcomp]'", 1);
            $data[shipcompnm] = $pay[shipcompnm];
            $data[shipcomp_oasis] = $pay[shipcomp_oasis];
            $data[shipcode] = $pay[shipcode];
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
            $data[shipdt] = $pay[shipdt];
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
            
            $data[b2b_goodsno]  = $item[b2b_goodsno];
            $data[csummary]     = $item[csummary];
            $data[csearch_word] = $item[csearch_word];
            $data[cresolution]  = $item[cresolution];
            $data[cgoods_size]  = $item[cgoods_size];
            $data[cetc1]        = $item[cetc1];
            $data[cetc2]        = $item[cetc2];
            $data[cetc3]        = $item[cetc3];
			
			//20141110 / minks / 명함 신청자 부서&이름 추가
			if($pay[vdp_edit_data]){
				$pay[vdp_edit_data] = str_replace("\n", "", $pay[vdp_edit_data]);
	   			$pay[vdp_edit_data] = str_replace("\r", "", $pay[vdp_edit_data]);
				$vdp_edit_data_arr = json_decode($pay[vdp_edit_data], true);
				$pay[vdp_name] = $vdp_edit_data_arr['@name'];
	   			$pay[vdp_department] = $vdp_edit_data_arr['@department']; 
			}
			$data[vdp_name] = $pay[vdp_name];
			$data[vdp_department] = $pay[vdp_department];

            $loop[] = $data;

            $cnt[$data[payno]][$data[ordno]]++;
       // }
    //}
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