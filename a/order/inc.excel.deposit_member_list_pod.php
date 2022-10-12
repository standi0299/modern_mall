<? 
/*
* @date : 20190109
* @author : kdk
* @brief : POD용 (알래스카) 거래관리 리스트 엑셀 파일 저장.
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
    <td><?=_("아이디")?></td>
    <td><?=_("회원명")?></td>
    <td><?=_("회원구분")?></td>
    <td><?=_("결제방식")?></td>
    <td><?=_("사업자명")?></td>
    <td><?=_("영업담당자")?></td>
    <td><?=_("거래상태")?></td>                                                                  
    <td><?=_("미수금액")?></td>
    <td><?=_("선입금액")?></td>
    <td><?=_("현재미수금액")?></td>
    <td><?=_("현재선입금액")?></td>
    <td><?=_("선발행입금액")?></td>    
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

    //현재미수금
    //* 현재미수금 : 미수금 - 선입금이 양수인 경우
    $n_remain_money = number_format($value[remain_money]-$value[deposit_money]);
    $pdata[] = ($n_remain_money>0) ? $n_remain_money : "0" ;
    
    //현재선입금
    //* 현재선임급 : 선입금 - 미수금이 양수인 경우
    $n_deposit_money = number_format($value[deposit_money]-$value[remain_money]);
    $pdata[] = ($n_deposit_money>0) ? $n_deposit_money : "0" ;
?>
<tr>
    <td><?=$data[mid]?></td>
    <td><?=$data[name]?></td>
    <td><?=$r_grp[$data[grpno]]?></td>
    <td><?=($data[credit_member])?"후불제":"선불제"?></td>
    <td><?=$data[cust_name]?></td>
    <td><?=$manager_name?></td>
    <td><?=($data[rest_flag])?"정지":"승인"?></td>
    <td><?=number_format($data[remain_money])?></td>
    <td><?=number_format($data[deposit_money])?></td>
    <td><?=($n_remain_money>0) ? $n_remain_money : "0"?></td>
    <td><?=($n_deposit_money>0) ? $n_deposit_money : "0"?></td>
    <td><?=number_format($data[pre_deposit_money])?></td>
</tr>
<? 
} 
?>
</table>