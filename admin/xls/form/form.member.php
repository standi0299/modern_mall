<?
set_time_limit(0);

$r_state = array(_("승인"),_("미승인"),_("차단"));
$r_married = array(
	'0' => _('미혼'),
	'1' => _('기혼'),
	);
$r_sex = array(
	'm'	=> _('남자'),
	'f'	=> _('여자'),
	);
$r_calendar = array(
	's'	=> _('양력'),
	'l'	=> _('음력'),
	);
$r_apply_sms = array(
	'0'	=> _('수신안함'),
	'1'	=> _('수신'),
	);
$r_apply_email = array(
	'0'	=> _('수신안함'),
	'1'	=> _('수신'),
	);
$r_cust_tax_type = array(
	'0' => _('사용안함'),
	'1'	=> _('일반과세자'),
	'2'	=> _('법인사업자'),
	);

### 정산담당자 추출
$r_manager = get_manager("y");

### 회원그룹 추출
$r_grp = getMemberGrp();

### 기업그룹 추출
$r_bid = getBusiness();

$res = $db->query($query);

$loop = array();
while ($data = $db->fetch($res)){
	$data[married]	= $r_married[$data[married]];
	$data[grpno]	= $r_grp[$data[grpno]];
	$data[bid]		= $r_bid[$data[bid]];
	$data[state]	= $r_member_state[$data[state]];
	$data[sex]		= $r_sex[$data[sex]];
	$data[calendar]	= $r_calendar[$data[calendar]];
	$data[apply_sms]	= $r_apply_sms[$data[apply_sms]];
	$data[apply_email]	= $r_apply_email[$data[apply_email]];
	$data[manager_no]	= $r_manager[$data[manager_no]][manager_name];
	$data[cust_tax_type]= $r_cust_tax_type[$data[cust_tax_type]];
	$data[buying]		= $data[totpayprice];

	$loop[] = $data;
}

?>

<meta http-equiv="Content-Type" content="application/vnd.ms-excel;charset=utf-8"/>
<table>
<tr>
<? foreach ($columns as $column_code){ ?>
	<th style="border:thin solid #666666;white-space:nowrap"><?=$r_column[$column_code]?></th>
<? } ?>
</tr>
<? foreach ($loop as $v){ ?>
<tr>
	<? foreach ($columns as $column_code){ ?>
	<td style="border:thin solid #666666;white-space:nowrap;mso-number-format:'\@';"><?=$v[$column_code]?></td>
	<? } ?>
</tr>
<? } ?>
</table>

<style>
table {border-collapse:collapse;font:9pt 돋움;}
th {border:thin solid #666666;white-space:nowrap}
td {border:thin solid #666666;white-space:nowrap}
</style>