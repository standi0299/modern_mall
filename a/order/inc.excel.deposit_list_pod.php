<? 
/*
* @date : 20190109
* @author : kdk
* @brief : POD용 (알래스카) 입금관리 리스트 엑셀 파일 저장.
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
    <td><?=_("사업자명")?></td>
    <td><?=_("거래상태")?></td>
    <td><?=_("영업담당자")?></td>
    <td><?=_("미수금액")?></td>
    <td><?=_("선입금액")?></td>
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
?>
<tr>
    <td><?=$data[mid]?></td>
    <td><?=$data[name]?></td>
    <td><?=$data[cust_name]?></td>
    <td><?=($data[rest_flag])?"정지":"승인"?></td>
    <td><?=$manager_name?></td>
    <td><?=number_format($data[remain_money])?></td>
    <td><?=number_format($data[deposit_money])?></td>
    <td><?=number_format($data[pre_deposit_money])?></td>
</tr>
<? 
} 
?>
</table>