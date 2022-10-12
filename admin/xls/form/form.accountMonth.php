<?

$end_day = date("t", mktime(0, 0, 0, $_POST[month], 1, $_POST[year]));

//해당 월 기간
$startdt = $_POST[year]."-".$_POST[month]."-01";
$enddt = $_POST[year]."-".$_POST[month]."-".$end_day;
$date = "and regdt >= '$startdt' and regdt < adddate('$enddt',interval 1 day)";
$today = date("Y-m-d");

### 정산담당자 추출
$r_manager = get_manager("y");

list($manager, $phone) = $db->fetch("select manager, phone from exm_mall where cid='$cid'",1);

//회원 데이터 추출
$query = "select * from exm_member where cid = '$cid' and mid = '$_POST[mid]'";
$res = $db->query($query);
$loop = array();

while($data = $db->fetch($res)){
    $loop[] = $data;
}
//회원 현재 신용거래2 금액
$creditMember = $loop[0][credit_member];

//당월 결제, 상품 데이터
list($accountCode, $payno) = $db->fetch("select a.account_code, b.account_payno from tb_pay_account a
                            left join tb_pay_account_history b on a.account_code = b.account_code
                            where a.account_year = $_POST[year] and a.account_month = $_POST[month] and a.cid = '$cid' and a.mid = '$_POST[mid]'",1);

$query_p = "select * from exm_pay a
            inner join exm_ord_item b on a.payno = b.payno
            inner join exm_ord_upload c on a.payno = c.payno
            where a.payno in ($payno)";
//inner join exm_ord_upload c on a.payno = c.payno -> 실서버 반영시 추가

$res_p = $db->query($query_p);
$cnt = $db->count;
$cnt2 = $cnt;

$list=array();
while($data_p = $db->fetch($res_p)){
    $list[] = $data_p;
}
//debug($list);
//당월 제작 비용 내역
$query_m = "select * from tb_pay_data where payno in ($payno)";
$res_m = $db->query($query_m);
while($data_m = $db->fetch($res_m)){
    $monthDeposit += $data_m[pay_credit2_deposit];
    $monthCredit += $data_m[pay_credit2_credit];
    $monthEmoney += $data_m[pay_emoney];
}

$monthTotalPrice = $monthDeposit + $monthCredit + $monthEmoney;

//예치금 추가, 사용 내역
$query_d = "select * from tb_deposit_history where cid = '$cid' and mid = '$_POST[mid]' $date";
$res_d = $db->query($query_d);
$depositData = array();
while($data_d = $db->fetch($res_d)){
    if($data_d[pay_flag] == 3 || $data_d[pay_flag] == 4){
        $depositData['charge'] += $data_d[credit2_payprice];
    }else{
        $depositData['use'] += $data_d[credit2_payprice];
    }
}

//적립금 지급, 사용 내역
$query_e = "select * from exm_log_emoney where cid = '$cid' and mid = '$_POST[mid]' $date";
$res_e = $db->query($query_e);
$emoneyData = array();
while($data_e = $db->fetch($res_e)){
    if($data_e[payno]){
        $emoneyData['use'] += $data_e[emoney];
    }else{
        $emoneyData['charge'] += $data_e[emoney];
    }
}
?>

<style>
.xl66
    {mso-style-parent:style0;
    font-family:"Malgun Gothic", monospace;
    mso-font-charset:129;}
.xl148
    {mso-style-parent:style0;
    font-size:20.0pt;
    font-weight:700;
    font-family:"Malgun Gothic", monospace;
    mso-font-charset:129;
    text-align:center;
    border-top:1.0pt solid windowtext;
    border-right:none;
    border-bottom:1.0pt solid windowtext;
    border-left:1.0pt solid windowtext;}
.xl133
    {mso-style-parent:style0;
    font-size:18.0pt;
    font-weight:700;
    font-family:"Malgun Gothic", monospace;
    mso-font-charset:129;
    text-align:center;
    border-top:1.0pt solid windowtext;
    border-right:none;
    border-bottom:1.0pt solid windowtext;
    border-left:none;}
.xl73
    {mso-style-parent:style0;
    font-size:9.0pt;
    font-family:"Malgun Gothic", monospace;
    mso-font-charset:129;
    text-align:center;
    border-top:1.0pt solid windowtext;
    border-right:none;
    border-bottom:1.0pt solid windowtext;
    border-left:none;}
.xl69
    {mso-style-parent:style0;
    font-size:9.0pt;
    font-family:"Malgun Gothic", monospace;
    mso-font-charset:129;
    border-top:1.0pt solid windowtext;
    border-right:none;
    border-bottom:1.0pt solid windowtext;
    border-left:none;}
.xl146
    {mso-style-parent:style0;
    font-size:9.0pt;
    font-family:"Malgun Gothic", monospace;
    mso-font-charset:129;
    text-align:left;
    border-top:1.0pt solid windowtext;
    border-right:none;
    border-bottom:1.0pt solid windowtext;
    border-left:none;}
.xl70
    {mso-style-parent:style0;
    font-size:9.0pt;
    font-family:"Malgun Gothic", monospace;
    mso-font-charset:129;
    border-top:1.0pt solid windowtext;
    border-right:1.0pt solid windowtext;
    border-bottom:1.0pt solid windowtext;
    border-left:none;}
.xl67
    {mso-style-parent:style0;
    font-size:9.0pt;
    font-family:"Malgun Gothic", monospace;
    mso-font-charset:129;}
.xl89
    {mso-style-parent:style0;
    font-size:9.0pt;
    font-family:"Malgun Gothic", monospace;
    mso-font-charset:129;
    text-align:right;}
.xl66
    {mso-style-parent:style0;
    font-family:"Malgun Gothic", monospace;
    mso-font-charset:129;}
.xl117
    {mso-style-parent:style0;
    font-size:9.0pt;
    font-family:"Malgun Gothic", monospace;
    mso-font-charset:129;
    text-align:right;
    border-top:.5pt solid windowtext;
    border-right:none;
    border-bottom:1.0pt solid windowtext;
    border-left:1.0pt solid windowtext;
    background:#F2F2F2;
    mso-pattern:black none;}
.xl115
    {mso-style-parent:style0;
    font-size:9.0pt;
    font-family:"Malgun Gothic", monospace;
    mso-font-charset:129;
    text-align:center;
    border-top:.5pt solid windowtext;
    border-right:.5pt solid windowtext;
    border-bottom:1.0pt solid windowtext;
    border-left:1.0pt solid windowtext;
    background:#F2F2F2;
    mso-pattern:black none;}
</style>

<meta http-equiv="Content-Type" content="application/vnd.ms-excel;charset=utf-8"/>
<table>
    <tr>
        <td rowspan="5000"></td>
    </tr>
    <tr height=49 style='mso-height-source:userset;height:36.95pt'>
        <td colspan=2 class=xl148><?=$_POST[month]?><?=_("월")?></td>
        <td colspan=5 class=xl133><?=_("거래명세서 및 청구서")?></td>
        <td class=xl73><?=_("거래 기간")?>:</td>
        <td class=xl69 colspan=3 style='mso-ignore:colspan'><?=$startdt?> ~ <?=$enddt?></td>
        <td class=xl69 colspan=2 style='mso-ignore:colspan'><?=_("정산코드")?>:</td>
        <td colspan=2 class=xl146><?=$accountCode?></td>
        <td class=xl69><?=_("작성일자")?>:</td>
        <td colspan=2 class=xl146><?=$today?></td>
        <td align=left valign=top>
            <span style='mso-ignore:vglayout;position:absolute;z-index:1;margin-left:71px;margin-top:11px;width:113px;height:31px'>
            </span>
            <span style='mso-ignore:vglayout2'>
                <table cellpadding=0 cellspacing=0>
                    <tr>
                        <td height=49 class=xl69 width=85 style='height:36.95pt;width:64pt'>　</td>
                    </tr>
                </table>
            </span>
        </td>
        <td class=xl70></td>
    </tr>
    
    <tr>
        <td colspan="20" style="text-align: right"><?=_("단위:원")?></td>
    </tr>
    
    <tr style="text-align: center">
        <td colspan="6" style="border-style: solid; border-right-width: thin; background:#D9D9D9"><?=_("공급받는자")?></td>
        <td colspan="6" style="border-style: solid; background:#D9D9D9"><?=_("공급자")?></td>
        <td colspan="4" style="border-style: solid; background:#8DB4E2"><?=_("구분")?></td>
        <td colspan="2" style="border-style: solid; background:#8DB4E2"><?=_("공급가")?></td>
        <td colspan="1" style="border-style: solid; background:#8DB4E2"><?=_("부가세")?></td>
        <td colspan="1" style="border-style: solid; background:#8DB4E2"><?=_("합계")?></td>
    </tr>
    <tr>
        <td colspan="2" style="border-style: solid; background:#D9D9D9; text-align: center"><?=_("회사명")?></td>
        <td colspan="4" style="border-style: solid; text-align: center; font-size:12.0pt; font-weight:700;"><?=$loop[0][name]?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9; text-align: center"><?=_("회사명")?></td>
        <td colspan="5" style="border-style: solid; text-align: center"><?=$cfg[nameComp]?></td>
        <td colspan="2" style="border-style: solid; background:#8DB4E2; text-align: center; font-weight:700;"><?=_("제작비")?></td>
        <td colspan="2" style="border-style: solid; background:#8DB4E2; font-weight: bold"><?=_("당월 제작합계")?></td>
        <td colspan="2" style="border-style: solid; background:#DCE6F1"><?=number_format($monthTotalPrice)?></td>
        <td colspan="1" style="border-style: solid; background:#DCE6F1"><?=number_format(makeSurtax($monthTotalPrice))?></td>
        <td colspan="1" style="border-style: solid; background:#DCE6F1; font-weight:700;"><?=number_format($monthTotalPrice + makeSurtax($monthTotalPrice))?></td>
    </tr>
    <tr>
        <td colspan="2" style="border-style: solid; background:#D9D9D9; text-align: center"><?=_("사업자번호")?></td>
        <td colspan="2" style="border-style: solid"><?=$loop[0][cust_no]?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9; text-align: center"><?=_("대표자")?></td>
        <td colspan="1" style="border-style: solid; text-align: center"><?=$loop[0][cust_ceo]?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9; text-align: center"><?=_("사업자번호")?></td>
        <td colspan="2" style="border-style: solid"><?=$cfg[regnumBiz]?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9; text-align: center"><?=_("대표자")?></td>
        <td colspan="2" style="border-style: solid; text-align: center"><?=$cfg[nameCeo]?></td>
        <td colspan="2" rowspan="4" style="border-style: solid; background:#C5D9F1; text-align: center; font-weight:700;"><?=_("결제금액")?></td>
        <td colspan="2" style="border-style: solid; background:#C5D9F1"><?=_("일반결제")?></td>
        <td colspan="2" style="border-style: solid">-</td>
        <td colspan="1" style="border-style: solid">-</td>
        <td colspan="1" style="border-style: solid">-</td>
    </tr>
    <tr>
        <td colspan="2" style="border-style: solid; background:#D9D9D9; text-align: center"><?=_("담당자명")?></td>
        <td colspan="2" style="border-style: solid"><?=$r_manager[$loop[0][manager_no]][manager_name]?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9; text-align: center"><?=_("핸드폰")?></td>
        <td colspan="1" style="border-style: solid"><?=$loop[0][cust_ceo_phone]?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9; text-align: center"><?=_("정산 담당자")?></td>
        <td colspan="2" style="border-style: solid"><?=$manager?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9; text-align: center"><?=_("핸드폰")?></td>
        <td colspan="2" style="border-style: solid"><?=$phone?></td>
        
        <td colspan="2" style="border-style: solid; background:#C5D9F1"><?=_("예치금")?></td>
        <td colspan="2" style="border-style: solid"><?=number_format($monthDeposit)?></td>
        <td colspan="1" style="border-style: solid"><?=number_format(makeSurtax($monthDeposit))?></td>
        <td colspan="1" style="border-style: solid"><?=number_format($monthDeposit + makeSurtax($monthDeposit))?></td>
    </tr>
    <tr>
        <td colspan="2" style="border-style: solid; background:#D9D9D9; background:#D9D9D9; text-align: center"><?=_("전화번호")?></td>
        <td colspan="2" style="border-style: solid"><?=$loop[0][cust_phone]?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9; text-align: center"><?=_("팩스")?></td>
        <td colspan="1" style="border-style: solid"><?=$loop[0][cust_fax]?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9; text-align: center"><?=_("전화번호")?></td>
        <td colspan="2" style="border-style: solid"><?=$cfg[phoneComp]?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9; text-align: center"><?=_("팩스")?></td>
        <td colspan="2" style="border-style: solid"><?=$cfg[faxComp]?></td>
       
        <td colspan="2" style="border-style: solid; background:#C5D9F1"><?=_("적립금")?></td>
        <td colspan="2" style="border-style: solid"><?=number_format($monthEmoney)?></td>
        <td colspan="1" style="border-style: solid"><?=number_format(makeSurtax($monthEmoney))?></td>
        <td colspan="1" style="border-style: solid"><?=number_format($monthEmoney + makeSurtax($monthEmoney))?></td>
    </tr>
    
    <tr>
        <td colspan="2" rowspan="2" style="border-style: solid; background:#D9D9D9; text-align: center"><?=_("주소")?></td>
        <td colspan="4" rowspan="2" style="border-style: solid">(<?=$loop[0][zipcode]?>) <?=$loop[0][address]?> <?=$loop[0][address_sub]?></td>
        <td colspan="1" rowspan="2" style="border-style: solid; background:#D9D9D9; text-align: center"><?=_("주소")?></td>
        <td colspan="5" rowspan="2" style="border-style: solid"><?=$cfg[address]?></td>
        
        <td colspan="2" style="border-style: solid; background:#C5D9F1"><?=_("당월 결제 합계")?></td>
        <td colspan="2" style="border-style: solid"><?=number_format($monthEmoney + $monthDeposit)?></td>
        <td colspan="1" style="border-style: solid"><?=number_format(makeSurtax($monthEmoney) + makeSurtax($monthDeposit))?></td>
        <td colspan="1" style="border-style: solid"><?=number_format($monthEmoney + makeSurtax($monthEmoney) + $monthDeposit + makeSurtax($monthDeposit))?></td>
    </tr>
    
    <tr>
        <td colspan="2" rowspan="2" style="border-style: solid; background:#E6B8B7; text-align: center; font-weight:700;"><?=_("미수금")?></td>
        <td colspan="2" style="border-style: solid; background:#E6B8B7"><?=_("당월 미수금")?></td>
        <td colspan="2" style="border-style: solid; background:#F2DCDB"><?=number_format($monthCredit)?></td>
        <td colspan="1" style="border-style: solid; background:#F2DCDB"><?=number_format(makeSurtax($monthCredit))?></td>
        <td colspan="1" style="border-style: solid; background:#F2DCDB; color:red; font-size:10.0pt; font-weight:700;"><?=number_format($monthCredit + makeSurtax($monthCredit))?></td>
    </tr>
    
    <tr>
        <td colspan="2" rowspan="2" style="border-style: solid; background:#D9D9D9; text-align: center"><?=_("참고사항")?></td>
        <td colspan="10" rowspan="2" style="border-style: solid"></td>
        
        <td colspan="2" style="border-style: solid; background:#E6B8B7;"><?=_("누적 미수금 합계")?></td>
        <td colspan="2" style='border-style: solid; background:#F2DCDB; mso-number-format:"\#\,\#\#0_;'><?=abs($creditMember + $monthCredit)?></td>
        <td colspan="1" style='border-style: solid; background:#F2DCDB; mso-number-format:"\#\,\#\#0_;'><?=abs(makeSurtax($creditMember + $monthCredit))?></td>
        <td colspan="1" style='color:red; font-size:10.0pt; font-weight:700; border-style: solid; background:#F2DCDB; mso-number-format:"\#\,\#\#0_;'><?=abs($creditMember + $monthCredit + makeSurtax($creditMember + $monthCredit))?></td>
    </tr>
    <tr>
        <td colspan="2" style="border-collapse:collapse; border:1px gray solid; background:#BFBFBF; text-align: center; font-weight:700;"><?=$_POST[month]?><?=_("월")?></td>
        <td colspan="2" style="border-collapse:collapse; border:1px gray solid; background:#BFBFBF; font-weight:700;"><?=_("청구 합계")?></td>
        <td colspan="2" style='border-style: solid; background:#BFBFBF; mso-number-format:"\#\,\#\#0_;'><?=abs($creditMember)?></td>
        <td colspan="1" style='border-style: solid; background:#BFBFBF; mso-number-format:"\#\,\#\#0_;'><?=abs(makeSurtax($creditMember))?></td>
        <td colspan="1" style='border-style: solid; background:#BFBFBF; color:red; font-size:12.0pt; font-weight:700; mso-number-format:"\#\,\#\#0_;'><?=abs($creditMember + makeSurtax($creditMember))?></td>
    </tr>
    <!-- 기본 설정 정보 -->
    
    <tr>
        <td colspan="1" style="border-left: solid; font-weight:700;">><?=_("제작비")?></td>
        <td colspan="19" style="border-right: solid; text-align: right"><?=_("단위:원")?></td>
    </tr>
    <tr style="text-align: center">
        <td colspan="19" style="border-style: solid; background:#D9D9D9"><?=_("주문정보")?></td>
        <!--<td colspan="5" style="border-style: solid; background: #BFBFBF">제작비</td>-->
        <td colspan="1" rowspan="2" style="border-style: solid; background: #BFBFBF"><?=_("제작비합계")?></td>
    </tr>
    <tr style="text-align: center">
        <td colspan="1" style="border-style: solid; background:#D9D9D9"><?=_("제작건")?></td>
        <td colspan="2" style="border-style: solid; background:#D9D9D9"><?=_("주문일")?></td>
        <td colspan="2" style="border-style: solid; background:#D9D9D9"><?=_("결제방법")?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9"><?=_("주문번호")?></td>
        <td colspan="6" style="border-style: solid; background:#D9D9D9"><?=_("제목명(파일명)")?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9"><?=_("옵션1")?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9"><?=_("옵션2")?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9"><?=_("옵션3")?></td>
        <td colspan="2" style="border-style: solid; background:#D9D9D9"><?=_("옵션4")?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9"><?=_("부수")?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9"><?=_("페이지")?></td>
        <!--
        <td colspan="1" style="border-style: solid; background:#BFBFBF">출력</td>
        <td colspan="1" style="border-style: solid; background:#BFBFBF">커버</td>
        <td colspan="1" style="border-style: solid; background:#BFBFBF">옵션3</td>
        <td colspan="1" style="border-style: solid; background:#BFBFBF">옵션4</td>
        <td colspan="1" style="border-style: solid; background:#BFBFBF">옵션5</td>
        -->
    </tr>
    
    <!--데이터 출력 시작-->
    <?foreach($list as $k=>$v) {
        if($v[mid] == "doodoo"){
            $op = str_replace($v[goodsnm].',' , "", $v[upload_order_option_desc]); 
            $doodo_option = explode(',', $op);
            $option[0] = $doodo_option[1];
            $option[1] = $doodo_option[0];
            $option[2] = $doodo_option[2];
            $option[5] = $doodo_option[6];
            $option[6] = $doodo_option[4];
        }else{
            $op = str_replace($v[goodsnm].',' , "", $v[upload_order_option_desc]); 
            $option = explode(',', $op);
        }
    ?>
    <tr style="text-align: center">
        <td colspan="1" style="border-style: solid"><?=$cnt--?><?=_("건")?></td>
        <td colspan="2" style="border-style: solid"><?=$v[orddt]?></td>
        <td colspan="2" style="border-style: solid"><?=$r_paymethod[$v[paymethod]]?></td>
        <td colspan="1" style="mso-number-format:'0_ ';border-style: solid"><?=$v[upload_order_product_code]?></td>
        <td colspan="6" style="border-style: solid"><?=$v[goodsnm]?></td>
        <td colspan="1" style="border-style: solid"><?=$option[1]?></td>
        <td colspan="1" style="border-style: solid"><?=$option[6]?></td>
        <td colspan="1" style="border-style: solid"><?=$option[0]?></td>
        <td colspan="2" style="border-style: solid"><?=$option[2]?></td>
        <td colspan="1" style="border-style: solid"><?=$v[upload_order_cnt]?></td>
        <td colspan="1" style="border-style: solid"><?=$option[5]?></td>
        <!--
        <td colspan="1" style="border-style: solid">출력</td>
        <td colspan="1" style="border-style: solid">커버</td>
        <td colspan="1" style="border-style: solid">옵션3</td>
        <td colspan="1" style="border-style: solid">옵션4</td>
        <td colspan="1" style="border-style: solid">옵션5</td>
        -->
        <td colspan="1" style="border-style: solid; font-weight:700;"><?=number_format($v[saleprice])?></td>
    </tr>
    <? $totSaleprice += $v[saleprice]; }?>
    <!--데이터 출력 끝-->
    <tr>
        <td colspan="19"  class=xl117 style="text-align: right; border-style: solid"><?=_("제작비 합계")?></td>
        <!--
        <td colspan="1" style="border-style: solid; background: #BFBFBF">-</td>
        <td colspan="1" style="border-style: solid; background: #BFBFBF">-</td>
        <td colspan="1" style="border-style: solid; background: #BFBFBF">-</td>
        <td colspan="1" style="border-style: solid; background: #BFBFBF">-</td>
        <td colspan="1" style="border-style: solid; background: #BFBFBF">-</td>
        -->
        <td colspan="1" style="border-style: solid; background: #BFBFBF; font-weight:700;"><?=number_format($totSaleprice)?></td>
    </tr>
    
    <tr>
        <td colspan="1" style="border-left: solid; font-weight:700;">><?=_("배송비")?></td>
        <td colspan="19" style="border-right: solid; text-align: right"><?=_("단위:원")?></td>
    </tr>
    <tr style="text-align: center">
        <td colspan="19" style="border-style: solid; background:#D9D9D9"><?=_("배송정보")?></td>
        <td colspan="1" rowspan="2" style="border-style: solid; background: #BFBFBF"><?=_("배송비")?></td>
    </tr>
    
    <tr style="text-align: center">
        <td colspan="1" style="border-style: solid; background:#D9D9D9"><?=_("제작건")?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9"><?=_("주문일")?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9"><?=_("결제방법")?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9"><?=_("결제번호")?></td>
        <td colspan="2" style="border-style: solid; background:#D9D9D9"><?=_("받는분")?></td>
        <td colspan="2" style="border-style: solid; background:#D9D9D9"><?=_("연락처1")?></td>
        <td colspan="2" style="border-style: solid; background:#D9D9D9"><?=_("연락처2")?></td>
        <td colspan="7" style="border-style: solid; background:#D9D9D9"><?=_("주소")?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9"><?=_("배송방법")?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9"><?=_("옵션1")?></td>
    </tr>
    
    <!--데이터 출력 시작-->
    <?foreach($list as $k2=>$v2){
        if($v2[shipprice]) { ?>
    <tr style="text-align: center">
        <td colspan="1" style="border-style: solid"><?=$cnt2--?><?=_("건")?></td>
        <td colspan="1" style="border-style: solid"><?=$v2[orddt]?></td>
        <td colspan="1" style="border-style: solid"><?=$r_paymethod[$v2[paymethod]]?></td>
        <td colspan="1" style="mso-number-format:'0_ ';border-style: solid"><?=$v2[payno]?></td>
        <td colspan="2" style="border-style: solid"><?=$v2[receiver_name]?></td>
        <td colspan="2" style="border-style: solid"><?=$v2[receiver_phone]?></td>
        <td colspan="2" style="border-style: solid"><?=$v2[receiver_mobile]?></td>
        <td colspan="7" style="border-style: solid"><?=$v2[receiver_addr]?></td>
        <td colspan="1" style="border-style: solid"></td>
        <td colspan="1" style="border-style: solid"></td>
        <td colspan="1" style="border-style: solid; font-weight:700;"><?=number_format($v2[shipprice])?></td>
    </tr>
    <? $totShipprice += $v2[shipprice]; } }?>
    <!--데이터 출력 끝-->
    
    <tr>
        <td colspan="19" class=xl115 style="text-align: center; border-style: solid"><?=_("배송비 합계")?></td>
        <td colspan="1" style="border-style: solid; background: #BFBFBF; font-weight:700;"><?=number_format($totShipprice)?></td>
    </tr>
    
    <tr colspan="20"></tr>
    
    <tr style="text-align: center">
        <td colspan="14" rowspan="4"></td>
        <td colspan="2" style="border-style: solid; background: #BFBFBF"><?=_("구분")?></td>
        <td colspan="2" style="border-style: solid; background: #BFBFBF"><?=_("당월 구매(지급)금액")?></td>
        <td colspan="1" style="border-style: solid; background: #BFBFBF"><?=_("당월 사용 금액")?></td>
        <td colspan="1" style="border-style: solid; background: #BFBFBF"><?=_("잔여금액")?></td>
    </tr>
    <tr>
        <td colspan="2" style="border-style: solid; background: #BFBFBF; text-align: center"><?=_("예치금")?></td>
        <td colspan="2" style="border-style: solid"><?=number_format($depositData['charge'])?></td>
        <td colspan="1" style="border-style: solid"><?=number_format(abs($depositData['use']))?></td>
        <td colspan="1" style="border-style: solid"><?=number_format($depositData['charge']+$depositData['use'])?></td>
    </tr>
    <tr>
        <td colspan="2" style="border-style: solid; background: #BFBFBF; text-align: center"><?=_("적립금")?></td>
        <td colspan="2" style="border-style: solid"><?=number_format($emoneyData['charge'])?></td>
        <td colspan="1" style="border-style: solid"><?=number_format(abs($emoneyData['use']))?></td>
        <td colspan="1" style="border-style: solid"><?=number_format($emoneyData['charge']+$emoneyData['use'])?></td>
    </tr>
    <tr>
        <td colspan="2" style="border-style: solid; background: #BFBFBF; text-align: center"><?=_("합계")?></td>
        <td colspan="2" style="border-style: solid; background:#D9D9D9"><?=number_format($depositData['charge']+$emoneyData['charge'])?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9"><?=number_format(abs($depositData['use']+$emoneyData['use']))?></td>
        <td colspan="1" style="border-style: solid; background:#D9D9D9"><?=number_format(abs($depositData['charge']+$emoneyData['charge']+$depositData['use']+$emoneyData['use']))?></td>
    </tr>
<tr>
</table>
