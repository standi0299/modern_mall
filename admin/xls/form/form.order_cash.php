<? $db -> query("set names utf8");

$res = $db -> query($query);
$loop = array();

while ($pay = $db -> fetch($res)) {
    unset($data);

	list($cid_info[compnm], $cid_info[sitenm], $cid_info[domain]) = $db -> fetch("select compnm,sitenm,domain from exm_mall where cid = '$pay[cid]'", 1);
	
    if ($pay[mid]) {
        $member = $db -> fetch("select * from exm_member where cid = '$pay[cid]' and mid = '$pay[mid]'");        
    }

	$data[phone] = $member[phone] ? $member[phone] : $member[mobile];
	if(!$pay[email] || $pay[email]=="@") $pay[email] = $member['email'];

	if ($pay[payno]) {
        $pay_info = $db -> fetch("select * from exm_pay where cid = '$pay[cid]' and payno = '$pay[payno]'");        
    }
	
	switch ($pay[document_type]) {
		case "CRD" :
			$data[document_type] = _("현금영수증(소득공제)");
			break;
		case "CRE" :
			$data[document_type] = _("현금영수증(지출증빙)");
			$data[licensee_num]	= $pay[licensee_num];
			break;
		case "TI" :
			$data[document_type] = _("세금계산서");
			$data[licensee_num]	= $pay[licensee_num];
			break;
		default:
			$data[document_type] = "";
			break;
	}
	
	$data[cid]			= $pay[cid];
	$data[compnm]		= $cid_info[compnm];
	$data[business_num]= str_replace("-", "", $cfg[bill_regnumBiz]);
	$data[sitenm]		= $cid_info[sitenm];
	$data[domain]		= $cid_info[domain];
	$data[orddt]		= $pay_info[orddt];
	$data[paydt]		= $pay_info[paydt];
	$data[payprice]		= $pay_info[payprice];

	$data[payorg]		= floor($pay_info[payprice]/1.1);
	$data[payvat]		= $pay_info[payprice]-$data[payorg];
	$data[payfee]		= 0;

	$data[order_name]	= $pay_info[orderer_name];
	$data[mid]			= $pay[mid];
	$data[member_name]	= $member[name];
	$data[payno]		= $pay[payno];
	$data[mobile]		= $pay[mobile];
	$data[email]		= $pay[email];
	$data[card_num]		= $pay[card_num];
	$data[licensee_num]	= $pay[licensee_num];
	$data[regdt]		= $pay[regdt];

	$loop[] = $data;
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
    ?>
    
	<td style="border:thin solid #666666;white-space:nowrap;<?if (!in_array($column_code,$r_num_flds)){?>mso-number-format:'@';<? } ?>"><?=$v[$column_code]?></td>
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