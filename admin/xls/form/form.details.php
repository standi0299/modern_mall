<?
$r_rid = get_release();

$res = $db->query($list_query);

$loop = array();
$price = 0;
while ($data = $db->fetch($res)){
    //debug($data);
    
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
    /*
    $tmp = array();
    //$tmp[no]                        = "";
    $tmp[orddt]                     = $data[orddt];
    $tmp[goodsnm]                   = $data[folder_location];
    $tmp[goods_price]               = $data[goods_price];
    $tmp[order_file_cnt]            = $data[order_file_cnt];
    $tmp[goods_pay]                 = $data[goods_price]*(($data[order_file_cnt]-$data[basic_order_file_cnt])/2);
    $tmp[addopt_aprice]             = $data[addopt_aprice];
    $tmp[ea]                        = $data[ea];
    $tmp[total_price]               = $data[ea]*($data[addopt_aprice] + $tmp[goods_pay]);
    $tmp[delivery_supply_price]     = $data[delivery_supply_price];
    $tmp[itemstep]                  = $data[itemstep];
    */
    
    $loop[] = $data;
    $price += $data[payprice];
}

//숫자를 한글로 변경해주는 함수. 일단 붙여둠 사용 여부 미지수 / 14.09.11 / kjm
Function getConvertNumberToKorean($_number)
{
    // 0부터 9까지의 한글 배열
    $number_arr = array('',_('일'),_('이'),_('삼'),_('사'),_('오'),_('육'),_('칠'),_('팔'),_('구'));

    // 천자리 이하 자리 수의 한글 배열
    $unit_arr1 = array('',_('십'),_('백'),_('천'));

    // 만자리 이상 자리 수의 한글 배열
    $unit_arr2 = array('',_('만'),_('억'),_('조'),_('경'),_('해'));

    // 결과 배열 초기화
    $result = array();

    // 인자값을 역순으로 배열한 후, 4자리 기준으로 나눔
    $reverse_arr = str_split(strrev($_number), 4);

    foreach($reverse_arr as $reverse_idx=>$reverse_number){
        // 1자리씩 나눔
        $convert_arr = str_split($reverse_number);
        $convert_idx = 0;

        foreach($convert_arr as $split_idx=>$split_number){
            // 해당 숫자가 0일 경우 처리되지 않음
            if(!empty($number_arr[$split_number])){ 
                // 0부터 9까지 한글 배열과 천자리 이하 자리 수의 한글 배열을 조합하여 글자 생성
                $result[$result_idx] = $number_arr[$split_number].$unit_arr1[$split_idx];

                // 반복문의 첫번째에서는 만자리 이상 자리 수의 한글 배열을 앞 전 배열에 연결하여 조합
                if(empty($convert_idx)) $result[$result_idx] .= $unit_arr2[$reverse_idx];   
                ++$convert_idx;
            }

            ++$result_idx;
        }
    }

    // 배열 역순으로 재정렬 후 합침
    $result = implode('', array_reverse($result));

    // 결과 리턴
    return $result;
}
?>

<html>

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>

<table align="center">
<tr align="center">
    <td></td>
    <td><font size="6" color="blue"><?=_("거 래 명 세 서")?></font></td>
</tr>
</table>
<br>
<table align="center" cellpadding="0" cellspacing="0" >
<? foreach ($list as $v)?>
<tr>
    <th width="150px" colspan="2" style="background: #EAEAEA"><?=_("상호")?></th>
    <td width="250px" colspan="1">&nbsp;<?=$list[cust_name]?></td>

    <th width="100px" rowspan="6" style=" #666666"><?=_("공급자")?></th>
    <td width="150px" colspan="2" style="border:thin #666666; background: #EAEAEA; text-align: center"><?=_("등록번호")?></td>
    <td width="200px" colspan="3">111-81-26181</td>
</tr>

<tr>
    <th colspan="2" style="background: #EAEAEA"><?=_("주소")?></th>
    <td colspan="1">&nbsp;<?=$list[address]." ".$list[address_sub]?></td>  
    
    <td colspan="2" style="background: #EAEAEA; text-align: center"><?=_("상 호")?></td>
    <td colspan="3"><?=_("한국학술정보㈜")?></td>
</tr>
<tr>
    <th colspan="2" style="background: #EAEAEA"><?=_("전화")?></th>
    <td colspan="1">&nbsp;<?=$list[phone]?></td> 
    
    <td colspan="2" style="background: #EAEAEA; text-align: center"><?=_("대표이사")?></td>
    <td colspan="3"><?=_("채 종 준")?></td>
</tr>
<tr rowsapn="3">
    <th colspan="2" rowspan="3" style="background: #EAEAEA"><?=_("담당자")?></th>
    <td colspan="1"><br>&nbsp;<?=$list[name]?></td>
    
    <td colspan="2" style="background: #EAEAEA; text-align: center"><?=_("사업장")?></td>
    <td colspan="3"><?=_("경기 파주 교하읍 문발리")?> 513-5</td>
</tr>
<tr>
    <td colspan="1">&nbsp;<?=$list[order_mobile]?></td>
    
    <td colspan="2" style="background: #EAEAEA; text-align: center"><?=_("업 태")?></td>
    <td colspan="3"><?=_("서비스 제조")?></td>
</tr>
<tr>
    <td colspan="1">&nbsp;<?=$list[order_email]?></td>

    <td colspan="2" style="background: #EAEAEA; text-align: center"><?=_("종 목")?></td>
    <td colspan="3"><?=_("인쇄, 출판, DB서비스")?></td>
</tr>
<tr>
    <td><br><br></td>
</tr>
<tr>
    <td colspan="2" align="center"><?=_("총 금 액")?></td>
    <td colspan="1" align="center" style="color: red; font-weight: bold"><?=_("일금")?></font></td>
    <td colspan="3" style="border-right:2px solid black; color: red; font-weight: bold" align="center"><?=getConvertNumberToKorean($price)?> <?=_("원정")?></td>
    <td></td>
    <td colspan="4" align="left" style="color: red; font-weight: bold"> ￦<?=number_format($price)?> </td>
</tr>
</table>

<tr>
    <td colspan="11">&nbsp;</td>
</tr>

<table style="width:850px" align="center" id="list" class="tb2">
<!-- 타이틀 -->
<tr>
<? foreach ($columns as $column_code){ ?>
	<th class="_orderby" name="<?=$colunm_code?>" height="20px"style="white-space:nowrap;background: #6B66FF; font-size: 12px; color: white; font-family: 돋움;">
	    <?=$r_column[$column_code]?></th>
<? } ?>
</tr>

<!-- 내용 -->
<? foreach ($loop as $k=>$v){?>
<tr>
	<? foreach ($columns as $column_code){?>
    	<td style="white-space:nowrap; font-family: 돋움
    	<?if (!in_array($column_code,$r_num_flds))
    	{?>
    	    mso-number-format:'@'; font-family: 돋움; text-align: center<?}?>">
    	    <!-- no값을 표시하기 위해 $k+1을 $no에 넣어서 뿌려줌 / 14.01.24 / kjm -->
    	    <?
    	       $no = $k+1;
    	       $v[no] = $no;
    	    ?>
    	    <?=$v[$column_code]?>
	<? } ?>
</tr>
<? } ?>
</table>
<br>

<tr>
    <td colspan="11">&nbsp;</td>
</tr>
<!--
<table align="center">
<tr>
<td width="500px" style="white-space:nowrap; font-family: 돋움;">
<b>-입금은행-<br>
계좌번호 : 기업은행 001-1444446-04-016 브룩스<br>
연 락 처 : 브룩스 관리팀 김소라 실장 031-940-1119</b>
</td>

<td width="300px" style="white-space:nowrap; font-family: 돋움">기간총결제금액(총상품금액 + 배송비)<br>
기간내입금금액<br>
기간내미지금액<br>
이월금액<br>
합계잔액<br>
</p>
</td>
</tr>
</table>
-->
<br>
<table align="center">
<tr>
    <td colspan="3" style="font-family: 돋움; font-size: 13px">
        <script>
        function print_hidden()
        {
            var test = document.getElementById("printDiv");
            //test.style.visibility="hidden";
            print();
        }
        </script>

        <DIV ID="printDiv"><input type="button" value="인쇄" onclick="javascript:print_hidden()"><br>
        <?=_("인쇄 시 페이지 번호, 날짜, URL 제거방법")?> ↓<br>
        <?=_("마우스 오른쪽버튼 -> 인쇄 미리 보기 -> 페이지 설정(톱니바퀴 모양) -> 머리글/바닥 글 항목을 비어있음 설정")?><br>
        <?=_("이 안내문은 인쇄되지 않습니다.")?><br>
        </DIV>
    </td>
</tr>
</table>
</html>

<script>
$j(function(){

    $j("img:first","#tabmenu").attr("src","../img/layout/bottom_menu_"+$j("img:first","#tabmenu").attr("bottom_menu")+"_on.png");
    $j("img","#tabmenu").click(function(){
        $j(this).attr("src","../img/layout/bottom_menu_"+$j(this).attr("bottom_menu")+"_on.png");
        $j("img","#tabmenu").not(this).each(function(){
            $j(this).attr("src","../img/layout/bottom_menu_"+$j(this).attr("bottom_menu")+".png");
        });
    });

    $j("#tabs").tabs();
    _set_orderby("<?=$_GET[orderby]?>");
});

function get_param(f)
{

    if (!form_chk(f)){
        return false;
    }

    var tmp = [];
    var cnt_member = 0;
    f.range.value = (document.getElementsByName('range')[0].checked) ? 'selmember' : 'allmember';

    if (f.range.value=='allmember'){

        var goodsno = document.getElementsByName('chk[]');
        for (var i=0; i<goodsno.length; i++){
            tmp[i] = goodsno[i].value;
        }
        f.mquery.value = "select * from exm_member where cid = '<?=$cid?>' and mid in ('" + tmp.join("','") + "')";
        cnt_member = <?=$pg->recode[total]?>+0;


    } else if (f.range.value=='selmember'){

        var c = document.getElementsByName('chk[]');
        for (var i=0;i<c.length;i++){
            if (c[i].checked) tmp[tmp.length] = c[i].value;
        }
        if (tmp[0]){
            f.mquery.value = "select * from exm_member where cid = '<?=$cid?>' and mid in ('" + tmp.join("','") + "')";
            cnt_member = tmp.length;
        }else{
            alert('<?=_("선택된 회원이 없습니다.")?>',-1);
            return false;
        }
    }
}

function chkLength(obj){
    str = obj.value;
    obj.parentNode.getElementsByTagName('span')[0].innerHTML = chkByte(str);
    if (chkByte(str)>80){
        alert('<?=_("80byte까지만 입력이 가능합니다")?>');
        obj.value = strCut(str,80);
    }
}

function chkByte(str)
{
    var length = 0;
    for(var i = 0; i < str.length; i++)
    {
        if(escape(str.charAt(i)).length >= 4)
            length += 2;
        else
            if(escape(str.charAt(i)) != "%0D")
                length++;
    }
    return length;
}
</script>