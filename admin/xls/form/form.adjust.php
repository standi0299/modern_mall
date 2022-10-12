<?//브룩스 정산 관리자 엑셀 저장기능 / 14.01.23 / kjm

//$db->query("set names utf8"); 

$res = $db->query($query);
$loop = array();
while ($data = $db->fetch($res)){
    
    //itmepstep별 주문상태
    if($data[itemstep] == 1) {
        $data[itemstep] = _("주문접수");
    }
    elseif ($data[itemstep] == 2) {
        $data[itemstep] = _("제작대기");
    }
    elseif ($data[itemstep] == 3) {
        $data[itemstep] = _("제작중");
    }
    elseif ($data[itemstep] == 4) {
        $data[itemstep] = _("발송대기");
    }
    elseif ($data[itemstep] == 5) {
        $data[itemstep] = _("출고완료");
    }
    
    //paystep별 입금상태
    if($data[paystep] == 1) {
        $data[paystep] = _("미입금");
    }
    elseif ($data[paystep] == 2) {
        $data[paystep] = _("입금");
    }
    
    //수동입력 자동입력
    if($data[input_flag] == 1){
        $data[input_flag] = _("자동");
    }
    elseif ($data[input_flag] == 2){
        $data[input_flag] = _("수동");
    }
    
    //결제방식
    if($data[paymethod] == c){
        $data[paymethod] = _("카드결제");
    }
    elseif($data[paymethod] == v)
    {
        $data[paymethod] = _("가상계좌");
    }
    elseif($data[paymethod] == o)
    {
        $data[paymethod] = _("계좌이체");
    }
    elseif($data[paymethod] == h)
    {
        $data[paymethod] = _("휴대폰결제");
    }    
    elseif($data[paymethod] == b)
    {
        $data[paymethod] = _("무통장입금");
    }
    elseif($data[paymethod] == t)
    {
        $data[paymethod] = _("신용거래");
    }
    elseif($data[paymethod] == e)
    {
        $data[paymethod] = _("적립금결제");
    }
    
    

	$tmp = array();
    $tmp[no]                                = "";
    
    if($data[input_flag] == _("자동")){
       $tmp[order_name]                     = $data[order_name];
    }
    else {
       $tmp[order_name]                     = _("수동입력");
    }
    
	$tmp[orddt]                             = $data[orddt];
	
    if($data[input_flag] == _("자동")){
       $tmp[goodsnm]                        = $data[folder_location];
    }
    else {
       $tmp[goodsnm]                        = $data[goodsnm];
    }

	$tmp[goods_price]                       = $data[goods_price];
	$tmp[order_file_cnt]                    = $data[order_file_cnt];

    if($data[input_flag] == _("자동")){
        $tmp[goods_pay]                     = $data[addopt_aprice] + ($data[goods_price]*(($data[order_file_cnt] - $data[basic_order_file_cnt])/2));
    } 
    else{
        $tmp[goods_pay]                     = $data[goods_pay];
    }
    
	$tmp[addopt_aprice]                     = $data[addopt_aprice];
	$tmp[ea]                                = $data[ea];

    if($data[input_flag] == _("자동")){
        $tmp[total_price]                   = ($data[addopt_aprice] + ($data[goods_price]*(($data[order_file_cnt] - $data[basic_order_file_cnt])/2)))*$data[ea];
    }
    else{
        $tmp[total_price]                   = $data[total_price];
    }
    
	$tmp[delivery_normal_price]		        = $data[delivery_supply_price];
	$tmp[total_price_delevery_basic_price]  = $tmp[total_price]+$tmp[delivery_normal_price];
    $tmp[dc_emoney]                         = $data[dc_emoney];
    $tmp[dc_member]                         = $data[dc_member];
    $tmp[dc_coupon]                         = $data[dc_coupon];
    $tmp[dc_coupon_name]                    = $data[dc_coupon_name];
    $tmp[dc_coupon_issue_code]              = $data[dc_coupon_issue_code];
    $tmp[payprice]                          = $data[payprice];
    $tmp[itemstep]                          = $data[itemstep];
    $tmp[product_match_name]                = $data[product_match_name];
    
    if($data[shipdt] == "0000-00-00 00:00:00")
    {
        $data[shipdt] = " ";
    }
    $tmp[shipdt]                            = $data[shipdt];
    
    $tmp[shipcomp]                          = $data[shipcomp];
    $tmp[shipcode]                          = $data[shipcode];
    $tmp[paymethod]                         = $data[paymethod];

    $tmp[opt1]                              = $data[opt1];
    $tmp[opt2]                              = $data[opt2];
    $tmp[addopt_str]                        = $data[addopt_str];
    $tmp[printopt_str]                      = $data[printopt_str];
    $tmp[supply_goods]                      = $data[supply_goods];
    $tmp[supply_opt]                        = $data[supply_opt];
    $tmp[price_opt]                         = $data[price_opt];
    $tmp[supply_addopt]                     = $data[supply_addopt];
    $tmp[price_addopt]                      = $data[price_addopt];
    $tmp[supply_printopt]                   = $data[supply_printopt];
    $tmp[price_printopt]                    = $data[price_printopt];
    $tmp[supply_addpage]                    = $data[supply_addpage];
    $tmp[price_addpage]                     = $data[price_addpage];
    $tmp[supply]                            = $data[supply];
    $tmp[input_flag]                        = $data[input_flag];

	$loop[] = $tmp;

    $cnt[$tmp[orddt]][$tmp[ordno]]++;
}
?>
<meta http-equiv="Content-Type" content="application/vnd.ms-excel;charset=utf-8"/>
<table>

<tr>
    <td colspan="5"></td>
    <td colspan="8" style="white-space:nowrap; font-family: 돋움; font-size: 13">
    <?=_("기간총결제금액(총상품금액 + 배송비)")?><br>
    <?=_("기간내입금금액")?><br>
    <?=_("기간내미지금액")?><br>
    <?=_("이월금액")?><br>
    <?=_("함계잔액")?><br>
    </td>
</tr>

<tr>
    <td>&nbsp;</td>
</tr>

<tr>
<? foreach ($columns as $column_code){ ?>
	<th style="border:thin solid #666666;white-space:nowrap;background: #FFFF00; font-family: 돋움; font-size: 13"><?=$r_column[$column_code]?></th>
<? } ?>
</tr>
<? foreach ($loop as $k=>$v){ ?>
<tr>
    <? foreach ($columns as $column_code){
        if (!in_array($column_code,$columns2)) continue;
            $rowspan = 1;
        if (in_array($column_code,$r_rowspan[orddt])){
            $rowspan = array_sum($cnt[$v[orddt]]);
        if ($view_pay[$v[orddt]][$column_code]) continue;
            $view_pay[$v[orddt]][$column_code] = true;
    }
    ?>
    <td
        <?if ($rowspan){?>rowspan="<?=$rowspan?>"<? } ?>
        style="border:thin solid #666666;white-space:nowrap;font-family: 돋움; font-size: 13
            <?if (!in_array($column_code,$r_num_flds)){?>
                mso-number-format:'@'; font-family: 돋움; font-size: 13<? } ?>">
                <!-- no값을 표시하기 위해 $k+1을 $no에 넣어서 뿌려줌 / 14.01.24 / kjm -->
                <?$no = $k+1;?>
                <?$v[no] = $no;?>
                <?=$v[$column_code]?>
    </td>
    <? } ?>
</tr>
<? } ?>
</table>