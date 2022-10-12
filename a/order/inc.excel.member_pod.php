<? 
/*
* @date : 20181031
* @author : kdk
* @brief : POD용 (알래스카) 회원 리스트 엑셀 파일 저장.
* @request : 
* @desc : 기존 필드 사용 (결제방식:credit_member, 거래상태:rest_flag)
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
    <td rowspan="2"><?=_("번호")?></td>
    <td colspan="15"><?=_("회원 일반 정보")?></td>
    <td colspan="6"><?=_("사업자등록정보")?></td>
    <td><?=_("세금계산서정보")?></td>
</tr>
<tr>
    <td><?=_("아이디")?></td>
    <td><?=_("회원명")?></td>
    <td><?=_("휴대전화")?></td>
    <td><?=_("일반전화")?></td>
    <td><?=_("팩스")?></td>
    <td><?=_("이메일주소")?></td>
    <td><?=_("주민등록번호")?></td>
    <td><?=_("우편번호")?></td>
    <td><?=_("주소")?></td>
    <td><?=_("회원구분")?></td>
    <td><?=_("거래상태")?></td>
    <td><?=_("결제방식")?></td>
    <td><?=_("기본입금방법")?></td>
    <td><?=_("영업담당자")?></td>
    <td><?=_("가입일")?></td>
    <td><?=_("사업자명")?></td>
    <td><?=_("대표자명")?></td>
    <td><?=_("사업자등록번호")?></td>
    <td><?=_("업태")?></td>
    <td><?=_("종목")?></td>
    <td><?=_("사업장주소")?></td>
    <td><?=_("담당자")?>/<?=_("발행이메일")?>/<?=_("휴대전화")?>/<?=_("대표담당자")?></td>
</tr>
<?
$cnt = 1; 
while ($data = $db->fetch($res)) {
    //credit_member 결제방식 (선결제,후결제)
    $credit_member = ($data[credit_member])?"후불제":"선불제";
    //rest_flag 거래상태(승인,정지)
    $rest_flag = ($data[rest_flag])?"정지":"승인";

    //manager_no 영업담당자
    $manager_name = "";
    if ($data[manager_no]) {
        $data[manager_no] = explode(",",$data[manager_no]);

        foreach ($data[manager_no] as $key => $val) {
            foreach($r_manager as $k=>$v) {
                if ($v[manager_no] == $val) {
                    $manager_name .= $v[manager_name].",";
                }
            }
        }
        if ($manager_name != "") $manager_name = substr($manager_name , 0, -1);
    }

?>
<tr>
    <td><?=$cnt?></td>
    <td><?=$data[mid]?></td>
    <td><?=$data[name]?></td>
    <td><?=$data[mobile]?></td>
    <td><?=$data[phone]?></td>
    <td>-</td>
    <td><?=$data[email]?></td>
    <td><?=$data[resno]?></td>
    <td><?=$data[zipcode]?></td>
    <td><?=$data[address]?> <?=$data[address_sub]?></td>
    <td><?=$r_grp[$data[grpno]]?></td>
    <td><?=$rest_flag?></td>
    <td><?=$credit_member?></td>
    <td><?=$data[cust_bank_name]?></td>
    <td><?=$manager_name?></td>
    <td><?=substr($value[regdt],0,10)?></td>
    <td><?=$data[cust_name]?></td>
    <td><?=$data[cust_ceo]?></td>
    <td><?=$data[cust_no]?></td>
    <td><?=$data[cust_type]?></td>
    <td><?=$data[cust_class]?></td>
    <td><?=$data[cust_address]?> <?=$data[cust_address_sub]?></td>
    <td>
        <?
        if ($data[etc1]) {
            $data[etc1] = explode(",",$data[etc1]);
            
            if($data[etc1][3]) {
                echo($data[etc1][0]."/".$data[etc1][1]."/".$data[etc1][2]."/*");
            }                
            else {
                echo($data[etc1][0]."/".$data[etc1][1]."/".$data[etc1][2]);
            }
        }        
        if ($data[etc2]) {
            $data[etc2] = explode(",",$data[etc2]);
            
            if($data[etc2][3]) {
                echo("<br>".$data[etc2][0]."/".$data[etc2][1]."/".$data[etc2][2]."/*");
            }
            else {
                echo("<br>".$data[etc2][0]."/".$data[etc2][1]."/".$data[etc2][2]);
            }
        }
        if ($data[etc3]) {
            $data[etc3] = explode(",",$data[etc3]);
            
            if($data[etc3][3]) {
                echo("<br>".$data[etc3][0]."/".$data[etc3][1]."/".$data[etc3][2]."/*");
            }
            else {
                echo("<br>".$data[etc3][0]."/".$data[etc3][1]."/".$data[etc3][2]);
            }
        }
        if ($data[etc4]) {
            $data[etc4] = explode(",",$data[etc4]);
            
            if($data[etc4][3]) {
                echo("<br>".$data[etc4][0]."/".$data[etc4][1]."/".$data[etc4][2]."/*");
            }
            else {
                echo("<br>".$data[etc4][0]."/".$data[etc4][1]."/".$data[etc4][2]);
            }
        }
        if ($data[etc5]) {
            $data[etc5] = explode(",",$data[etc5]);
            
            if($data[etc5][3]) {
                echo("<br>".$data[etc5][0]."/".$data[etc5][1]."/".$data[etc5][2]."/*");
            }
            else {
                echo("<br>".$data[etc5][0]."/".$data[etc5][1]."/".$data[etc5][2]);
            }
        }
        //if($data[etc1]) echo(str_replace(",", "/", $data[etc1]));
        //if($data[etc2]) echo("<br>".str_replace(",", "/", $data[etc2]));
        //if($data[etc3]) echo("<br>".str_replace(",", "/", $data[etc3]));
        //if($data[etc4]) echo("<br>".str_replace(",", "/", $data[etc4]));
        //if($data[etc5]) echo("<br>".str_replace(",", "/", $data[etc5]));
        ?>
    </td>
</tr>
<? 
    $cnt++;
} 
?>
</table>