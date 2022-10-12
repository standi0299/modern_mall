<? 
/*
* @date : 20190109
* @author : kdk
* @brief : POD용 (알래스카) 회원미수금현황 리스트 엑셀 파일 저장.
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
    <td><?=_("회원구분")?></td>
    <td><?=_("영업담당자")?></td>
    <td><?=_("전화번호")?></td>
    <td><?=_("시작일자")?></td>
    <td><?=_("최종입금일")?></td>
    <td><?=_("약속일자")?></td>
    <td><?=_("약속금액")?></td>
    <td><?=_("미수금액")?></td>
    <td><?=_("선입금액")?></td>
    <td><?=_("현재미수금액")?></td>
    <td><?=_("미수담당자")?></td>
    <td><?=_("비고")?></td>
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
    
    $n_remain_money = number_format($data[remain_money]-$data[deposit_money]);
?>
<tr>
    <td><?=$data[mid]?></td>
    <td><?=$data[name]?></td>
    <td><?=$data[cust_name]?></td>
    <td><?=$r_grp[$data[grpno]]?></td>
    <td><?=$manager_name?></td>
    <td><?=($data[phone]) ? $data[phone] : $data[mobile]?></td>
    <td><?=($data[start_date]) ? substr($data[start_date],0,10) : ""?></td>
    <td><?=($data[final_date]) ? substr($data[final_date],0,10) : ""?></td>
    <td><?=($data[promise_date]) ? substr($data[promise_date],0,10) : ""?></td>
    <td><?=number_format($data[promise_money])?></td>
    <td><?=number_format($data[remain_money])?></td>
    <td><?=number_format($data[deposit_money])?></td>
    <td><?=($n_remain_money>0) ? $n_remain_money : "0"?></td>
    <td><?=$data[remainadmin]?></td>
    <td><?=substr($data[regdt],0,10)?></td>
    <td><?=$data[bigo]?></td>
</tr>
<? 
} 
?>
</table>