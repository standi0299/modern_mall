<? 
/*
* @date : 20190109
* @author : kdk
* @brief : POD용 (알래스카) 주문관리 리스트 엑셀 파일 저장.
* @request : 
* @desc :
* @todo :
*/

### 회원그룹 추출
$r_grp = getMemberGrp();

### 영업사원정보 추출
$r_manager = get_manager("y");
?>

<style>
tr th {font-weight:bold;text-align:center;}
td {mso-number-format:"@"}
</style>

<table border="1" bordercolor="#cccccc" style="border-collapse:collapse;">
<tr align="center">
    <td><?=_("주문번호")?></td>
    <td><?=_("사업자명")?></td> 
    <td><?=_("사업자번호")?></td>
    <td><?=_("회원명")?></td>
    <td><?=_("아이디")?></td>
    <td><?=_("주문명")?></td>
    <td><?=_("상품명")?></td>
    <td><?=_("주문사양")?></td>
    <td><b class="red"><?=_("주문금액")?></b></td>
    <td><b class="red"><?=_("입금액")?></b></td>
    <td><b class="red"><?=_("선발행입금사용금액")?></b></td>
    <td><b class="red"><?=_("미수금액")?></b></td>
    <td><b class="red"><?=_("영업담당자")?></b></td>
    <td><b class="red"><?=_("주문일시")?></b></td>
    <td><b class="red"><?=_("접수담당자")?></b></td>
    <td><b class="red"><?=_("접수일시")?></b></td>
    <td><b class="red"><?=_("출고담당자")?></b></td>
    <td><b class="red"><?=_("출고일시")?></b></td>
    <td><b class="red"><?=_("진행상태")?></b></td>
    <td><?=_("상태갱신일시")?></td>
    <td><?=_("자동입금처리제외")?></td>
</tr>
<?
while ($data = $db->fetch($res)) {
    //manager_no 영업담당자
    $manager_name = "";
    if ($data[manager_no]) {
        $data[manager_no] = explode(",",$data[manager_no]);

        foreach ($data[manager_no] as $key => $val) {
            foreach($r_manager as $k=>$v) {
                if ($v[mid] == $val) {
                    $manager_name .= $v[name].",";
                }
            }
        }
        if ($manager_name != "") $manager_name = substr($manager_name , 0, -1);
    }
?>
<tr>
    <td><?=$data[payno]?></td>
    <td><?=$data[cust_name]?></td>
    <td><?=($r_grp[$value[grpno]] == "개인") ? $data[resno] : $data[cust_no]?></td>
    <td><?=$data[name]?></td>
    <td><?=$data[mid]?></td>
    <td><?=$data[order_title]?></td>
    <td><?=$data[goodsnm]?></td>
    <td><?=$data[order_data]?></td>
    <td><?=number_format($data[payprice]+$data[vat_price]+$data[ship_price])?></td>
    <td><?=number_format($data[deposit_price])?></td>
    <td><?=number_format($data[pre_deposit_price])?></td>
    <td><?=number_format($data[remain_price])?></td>
    <td><?=$manager_name?></td>
    <td><?=$data[orddt]?></td>
    <td><?=$r_manager[$data[receiptadmin]][name]?></td>
    <td><?=$data[receiptdt]?></td>
    <td><?=$r_manager[$data[deliveryadmin]][name]?></td>
    <td><?=$data[deliverydt]?></td>
    <td><?=$r_pay_status[$data[status]]?></td>
    <td><?=$data[status_date]?></td>
    <td><?=($data[autoproc_flag]) ? "제외" : ""?></td>
</tr>
<? 
} 
?>
</table>