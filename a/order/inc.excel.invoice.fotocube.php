<? 

### 출고처 추출
$r_rid = get_release(); 

$r_orderinfo = array();
while ($tmp=$db->fetch($res)){
    ### 상품명가공
    $goodsnm = $tmp[goodsnm];
    if ($tmp[est_order_option_desc]) $goodsnm .= str_replace(",","||",$tmp[est_order_option_desc]);
    if ($tmp[opt]) $goodsnm .= "||$tmp[opt]";
    if ($tmp[addopt]){
        foreach (unserialize($tmp[addopt]) as $v){
            $goodsnm .= "||$v[addopt_bundle_name]:$v[addoptnm]";
        }
    }
    if ($tmp[printopt]){
        foreach (unserialize($tmp[printopt]) as $v){
            $goodsnm .= "||$v[printoptnm]:$v[ea]";
        }
    }
    $goodsnm .= "||수량:$tmp[ea]";
    $tmp[goodsnm] = $goodsnm;
    $tmp[shipmethodnm] = ($tmp[shipmethod]=='quick') ? _("오토바이배송") : _("택배");

    if ($r_orderinfo[$tmp[payno]."_".$tmp[ordno]]){
        $r_orderinfo[$tmp[payno]."_".$tmp[ordno]][goodsnm] .= " ".$goodsnm;
    } else {
        $r_orderinfo[$tmp[payno]."_".$tmp[ordno]] = $tmp;
    }
}
?>

<style>
td {mso-number-format:"@"}
br {mso-data-placement:same-cell;}
</style>

<table border=1 bordercolor=#cccccc style="border-collapse:collapse;font:8pt tahoma">
<tr>
    <th><?=_("이름")?></th>
    <th><?=_("우편번호")?></th>
    <th><?=_("주소1")?></th>
    <th><?=_("고객센터")?></th>
    <th><?=_("전화번호")?></th>
    <th><?=_("배송방법")?></th>
    <th><?=_("결제번호")?></th>
    <th><?=_("주문번호")?></th>
    <th><?=_("상품명")?></th>
    <th><?=_("운임")?></th>
    <th><?=_("사이트")?></th>
    <th><?=_("아이디")?></th>
</tr>
<? foreach ($r_orderinfo as $v){ ?>
<tr>
    <td><?=$v[receiver_name]?> (<?=$v[orderer_name]?>)</td>
    <td><?=$v[receiver_zipcode]?></td>
    <td><?=$v[receiver_addr]?> <?=$v[receiver_addr_sub]?></td>
    <td>1544-4790</td>
    <td><?=$v[receiver_mobile]?></td>
    <td><?=$v[shipmethodnm]?></td>
    <td><?=$v[payno]?></td>
    <td><?=$v[ordno]?></td>
    <td><?=strcut($v[goodsnm],175,".. ***"._("추가확인")."***")?></td>
    <td><?=$r_paymethod[$v[paymethod]]?></td>
    <td>fotocube</td>
    <td>
        <? if ($v[mid]) { ?>
            (<?=$v[mid]?>)
        <? } else { ?>
            (<?=_("비회원")?>)
        <? } ?>
    </td>
</tr>
<? } ?>
</table>