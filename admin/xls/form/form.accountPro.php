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
.xl70
    {mso-style-parent:style0;
    color:windowtext;
    font-size:9.0pt;
    font-family:굴림체, monospace;
    mso-font-charset:0;
    text-align:center;
    vertical-align:middle;
    border:.5pt solid silver;
    background:#FABF8F;
    mso-pattern:black none;
    white-space:normal;}
.xl74
    {mso-style-parent:style0;
    color:windowtext;
    font-size:9.0pt;
    font-family:굴림체, monospace;
    mso-font-charset:0;
    text-align:center;
    vertical-align:middle;
    border:.5pt solid silver;
    background:#92CDDC;
    mso-pattern:black none;
    white-space:normal;}
.xl75
    {mso-style-parent:style0;
    color:windowtext;
    font-size:9.0pt;
    font-family:굴림, monospace;
    mso-font-charset:129;
    text-align:center;
    vertical-align:middle;
    border:.5pt solid silver;
    background:#92CDDC;
    mso-pattern:black none;
    white-space:normal;}
.xl69
    {mso-style-parent:style0;
    color:windowtext;
    font-size:9.0pt;
    font-family:굴림체, monospace;
    mso-font-charset:0;
    text-align:center;
    vertical-align:middle;
    border:.5pt solid silver;
    background:#E6B8B7;
    mso-pattern:black none;
    white-space:normal;}
.xl67
    {mso-style-parent:style0;
    color:windowtext;
    font-size:9.0pt;
    font-family:굴림체, monospace;
    mso-font-charset:0;
    text-align:center;
    vertical-align:middle;
    border:.5pt solid silver;
    background:white;
    mso-pattern:black none;
    white-space:normal;}
.xl68
    {mso-style-parent:style0;
    color:white;
    font-size:8.0pt;
    font-family:굴림체, monospace;
    mso-font-charset:0;
    text-align:left;
    vertical-align:top;
    border:.5pt solid silver;
    background:gray;
    mso-pattern:black none;
    white-space:normal;}
</style>

<meta http-equiv="Content-Type" content="application/vnd.ms-excel;charset=utf-8"/>
<table>
    <tr height=25 style='mso-height-source:userset;height:18.75pt'>
    <td height=25 class=xl70 width=131 style='height:18.75pt;width:98pt'><?=_("회사코드")?></td>
    <td class=xl70 width=131 style='border-left:none;width:98pt'><?=_("사업장코드")?></td>
    <td class=xl70 width=131 style='border-left:none;width:98pt'><?=_("부서코드")?></td>
    <td class=xl70 width=131 style='border-left:none;width:98pt'><?=_("사원코드")?></td>
    <td class=xl70 width=131 style='border-left:none;width:98pt'><?=_("창고코드")?></td>
    <td class=xl70 width=131 style='border-left:none;width:98pt'><?=_("장소코드")?></td>
    <td class=xl74 width=131 style='border-left:none;width:98pt'><?=_("출고일자")?></td>
    <td class=xl74 width=182 style='border-left:none;width:137pt'><?=_("고객코드")?></td>
    <td class=xl75 width=131 style='border-left:none;width:98pt'><?=_("업체명")?></td>
    <td class=xl70 width=131 style='border-left:none;width:98pt'><?=_("거래구분")?></td>
    <td class=xl70 width=131 style='border-left:none;width:98pt'><?=_("과세구분")?></td>
    <td class=xl70 width=131 style='border-left:none;width:98pt'><?=_("단가구분")?></td>
    <td class=xl70 width=131 style='border-left:none;width:98pt'><?=_("품번")?></td>
    <td class=xl70 width=131 style='border-left:none;width:98pt'><?=_("출고수량")?></td>
    <td class=xl69 width=131 style='border-left:none;width:98pt'><?=_("출고단가")?></td>
    <td class=xl74 width=131 style='border-left:none;width:98pt'><?=_("출고공급가액")?></td>
    <td class=xl74 width=131 style='border-left:none;width:98pt'><?=_("출고부가세")?></td>
    <td class=xl74 width=131 style='border-left:none;width:98pt'><?=_("출고합계액")?></td>
    <td class=xl70 width=131 style='border-left:none;width:98pt'><?=_("프로젝트코드")?></td>
    <td class=xl75 width=131 style='border-left:none;width:98pt'><?=_("신규정산")?></td>
    <td class=xl67 width=131 style='border-left:none;width:98pt'><?=_("담당자코드")?></td>
    <td class=xl74 width=131 style='border-left:none;width:98pt'><?=_("주문번호")?></td>
    <td class=xl70 width=131 style='border-left:none;width:98pt'><?=_("재고수량")?></td>
    <td class=xl67 width=131 style='border-left:none;width:98pt'><?=_("비고(품목하단)")?></td>
    <td class=xl74 width=131 style='border-left:none;width:98pt'><?=_("비고(건)")?></td>
</tr>
<tr height=25 style='mso-height-source:userset;height:18.75pt'>
    <td height=25 class=xl70 width=131 style='height:18.75pt;border-top:none;width:98pt'>CO_CD</td>
    <td class=xl70 width=131 style='border-top:none;border-left:none;width:98pt'>DIV_CD</td>
    <td class=xl70 width=131 style='border-top:none;border-left:none;width:98pt'>DEPT_CD</td>
    <td class=xl70 width=131 style='border-top:none;border-left:none;width:98pt'>EMP_CD</td>
    <td class=xl70 width=131 style='border-top:none;border-left:none;width:98pt'>WH_CD</td>
    <td class=xl70 width=131 style='border-top:none;border-left:none;width:98pt'>LC_CD</td>
    <td class=xl74 width=131 style='border-top:none;border-left:none;width:98pt'>ISU_DT</td>
    <td class=xl74 width=182 style='border-top:none;border-left:none;width:137pt'>TR_CD</td>
    <td class=xl74 width=131 style='border-top:none;border-left:none;width:98pt'>　</td>
    <td class=xl70 width=131 style='border-top:none;border-left:none;width:98pt'>SO_FG</td>
    <td class=xl70 width=131 style='border-top:none;border-left:none;width:98pt'>VAT_FG</td>
    <td class=xl70 width=131 style='border-top:none;border-left:none;width:98pt'>UMVAT_FG</td>
    <td class=xl70 width=131 style='border-top:none;border-left:none;width:98pt'>ITEM_CD</td>
    <td class=xl70 width=131 style='border-top:none;border-left:none;width:98pt'>SO_QT</td>
    <td class=xl69 width=131 style='border-top:none;border-left:none;width:98pt'>ISU_UM</td>
    <td class=xl74 width=131 style='border-top:none;border-left:none;width:98pt'>ISUG_AM</td>
    <td class=xl74 width=131 style='border-top:none;border-left:none;width:98pt'>ISUV_AM</td>
    <td class=xl74 width=131 style='border-top:none;border-left:none;width:98pt'>ISUH_AM</td>
    <td class=xl70 width=131 style='border-top:none;border-left:none;width:98pt'>PJT_CD</td>
    <td class=xl74 width=131 style='border-top:none;border-left:none;width:98pt'>　</td>
    <td class=xl67 width=131 style='border-top:none;border-left:none;width:98pt'>PLN_CD</td>
    <td class=xl74 width=131 style='border-top:none;border-left:none;width:98pt'>SO_NB</td>
    <td class=xl70 width=131 style='border-top:none;border-left:none;width:98pt'>ISU_QT</td>
    <td class=xl67 width=131 style='border-top:none;border-left:none;width:98pt'>REMARK_DC_D</td>
    <td class=xl74 width=131 style='border-top:none;border-left:none;width:98pt'>REMARK_DC</td>
</tr>
<tr height=60 style='mso-height-source:userset;height:45.0pt'>
  <td height=60 class=xl68 width=131 style='height:45.0pt;border-top:none;width:98pt'><?=_("타입 : 문자")?><br>
    <?=_("길이")?> : 4<br>
    <?=_("필수")?> : False<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 문자")?><br>
    <?=_("길이")?> : 4<br>
    <?=_("필수")?> : False<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 문자")?><br>
    <?=_("길이")?> : 4<br>
    <?=_("필수")?> : False<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 문자")?><br>
    <?=_("길이")?> : 10<br>
    <?=_("필수")?> : False<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 문자")?><br>
    <?=_("길이")?> : 4<br>
    <?=_("필수")?> : True<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 문자")?><br>
    <?=_("길이")?> : 4<br>
    <?=_("필수")?> : True<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 문자")?><br>
    <?=_("길이")?> : 8<br>
    <?=_("필수")?> : True<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
  <td class=xl68 width=182 style='border-top:none;border-left:none;width:137pt'><?=_("타입 : 문자")?><br>
    <?=_("길이")?> : 10<br>
    <?=_("필수")?> : True<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'>　</td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 문자")?><br>
    <?=_("길이")?> : 1<br>
    <?=_("필수")?> : True<br>
    <?=_("설명")?> : 0. <?=_("국내거래")?>(DOMESTIC), 1. LOCAL L/C, 2. <?=_("구매승인서")?>, 3. MASTER L/C, 4. T/T, 5. D/A, 6. D/P</td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 문자")?><br>
    <?=_("길이")?> : 1<br>
    <?=_("필수")?> : True<br>
    <?=_("설명")?> : 0.<?=_("매출과세")?> 1.<?=_("수출영세")?> 2.<?=_("매출면세")?> 3.<?=_("매출기타")?></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 문자")?><br>
    <?=_("길이")?> : 1<br>
    <?=_("필수")?> : True<br>
    <?=_("설명")?> : 0.<?=_("부가세미포함")?> 1.<?=_("부가세포함")?></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 문자")?><br>
    <?=_("길이")?> : 30<br>
    <?=_("필수")?> : True<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 숫자")?><br>
    <?=_("길이")?> : 17,6<br>
    <?=_("필수")?> : True<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 숫자")?><br>
    <?=_("길이")?> : 17,4<br>
    <?=_("필수")?> : False<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 숫자")?><br>
    <?=_("길이")?> : 17,4<br>
    <?=_("필수")?> : False<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 숫자")?><br>
    <?=_("길이")?> : 17,4<br>
    <?=_("필수")?> : False<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 숫자")?><br>
    <?=_("길이")?> : 17,4<br>
    <?=_("필수")?> : False<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 문자")?><br>
    <?=_("길이")?> : 10<br>
    <?=_("필수")?> : False<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'>　</td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 문자")?><br>
    <?=_("길이")?> : 5<br>
    <?=_("필수")?> : False<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 문자")?><br>
    <?=_("길이")?> : 12<br>
    <?=_("필수")?> : False<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 숫자")?><br>
    <?=_("길이")?> : 17,6<br>
    <?=_("필수")?> : True<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 문자")?><br>
    <?=_("길이")?> : 60<br>
    <?=_("필수")?> : False<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
  <td class=xl68 width=131 style='border-top:none;border-left:none;width:98pt'><?=_("타입 : 문자")?><br>
    <?=_("길이")?> : 60<br>
    <?=_("필수")?> : False<br>
    <?=_("설명")?> :<span style='mso-spacerun:yes'>&nbsp;</span></td>
 </tr>
 
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl71 style='height:15.0pt;border-top:none'>1000</td>
  <td class=xl71 style='border-top:none;border-left:none'>1000</td>
  <td class=xl71 style='border-top:none;border-left:none'>5008</td>
  <td class=xl71 style='border-top:none;border-left:none'>1125</td>
  <td class=xl83 style='border-top:none;border-left:none'>1000</td>
  <td class=xl83 style='border-top:none;border-left:none'>100</td>
  <td class=xl81 style='border-top:none;border-left:none'><font class="font0"><?=$enddt?></font></td>
  <td class=xl81 style='border-top:none;border-left:none'><?=$loop[0][cust_no]?></td>
  <td class=xl82 style='border-top:none;border-left:none'><?=$loop[0][name]?></td>
  <td class=xl83 style='border-top:none;border-left:none'>0</td>
  <td class=xl83 style='border-top:none;border-left:none'>0</td>
  <td class=xl83 style='border-top:none;border-left:none'>0</td>
  <td class=xl83 style='border-top:none;border-left:none'>5000000003</td>
  <td class=xl83 style='border-top:none;border-left:none'>1</td>
  <td class=xl65 style='border-top:none;border-left:none'></td>
  <td class=xl77 style='border-top:none;border-left:none;mso-number-format:"\#\,\#\#0_;'><?=abs($creditMember)?></td>
  <td class=xl78 style='border-top:none;border-left:none;mso-number-format:"\#\,\#\#0_;'><?=abs(makeSurtax($creditMember))?></td>
  <td class=xl77 style='border-top:none;border-left:none;mso-number-format:"\#\,\#\#0_;'><?=abs($creditMember + makeSurtax($creditMember))?></td>
  <td class=xl71 style='border-top:none;border-left:none'>418-2</td>
  <td class=xl76 style='border-top:none;border-left:none'>1</td>
  <td class=xl65 style='border-top:none;border-left:none'>　</td>
  <td class=xl79 style="mso-number-format:'0_ '";><?=$accountCode?></td>
  <td class=xl72 style='border-top:none'>1</td>
  <td class=xl65 style='border-top:none;border-left:none'>　</td>
  <td class=xl80 style='border-top:none;border-left:none'></td>
 </tr>

<tr>
</table>
