<?

$loc = "운영관리>결제서비스관리";
include "../_header.php";

if ($cfg[pg][paymethod]) foreach ($cfg[pg][paymethod] as $k=>$v){
	$checked[paymethod][$v] = "checked";
}
if ($cfg[pg][e_paymethod]) foreach ($cfg[pg][e_paymethod] as $k=>$v){
	$checked[e_paymethod][$v] = "checked";
}

if($cfg[min_orderprice])
   $checked[use_min_orderprice][Y] = "checked";
else
   $checked[use_min_orderprice][N] = "checked";

$selected[module][$cfg[pg][module]] = "selected";
$selected[lgd_buyeremail][$cfg[pg][lgd_buyeremail]] = "selected";

//$checked[escrow][$cfg[pg][escrow]] = $checked[cash_receipt][$cfg[pg][cash_receipt]+0] = "checked";
//debug($selected[module][tenpay]);


//kcp와 이니시스를 동시에 같은 radio 이름을 써서 문제가 발생. PG사별 check 값으로 저장한다.      20141209    chunter
$checked[escrow][$cfg[pg][module]][$cfg[pg][escrow]] = $checked[cash_receipt][$cfg[pg][cash_receipt]+0] = "checked";
//debug($checked[escrow][kcp][1]);

if (!$cfg[pg][paymethod]) $cfg[pg][paymethod] = array();
if (in_array("b",$cfg[pg][paymethod])) $checked[paymethod_bank][1] = "checked";
else $checked[paymethod_bank][0] = "checked";

if (in_array("t",$cfg[pg][paymethod])) $checked[paymethod_credit][1] = "checked";
else if (in_array("d",$cfg[pg][paymethod])) $checked[paymethod_credit][2] = "checked";
else $checked[paymethod_credit][0] = "checked";

### 입금계좌
$query = "select * from exm_bank where cid = '$cid'";
$res = $db->query($query);
$bank = array();
while ($data = $db->fetch($res)){
	$bank[] = $data;
}

//alipay 통화 선택값 		20160310		chunter
$selected[ali_currency][$cfg[pg][ali_currency]] = "selected";
?>

<form method="post" action="indb.php" onsubmit="return form_chk_pg(this)&&confirm('수정사항은 쇼핑몰 운영에 즉시 반영됩니다.\r\n적용하시겠습니까?')">
<input type="hidden" name="mode" value="pg"/>

<div class="stit">기본설정</div>
<table class="tb1">
<tr>
    <th>무통장입금 사용여부</th>
    <td>
    <input type="radio" name="paymethod_bank" value="1" <?=$checked[paymethod_bank][1]?> class="absmiddle"/><span class="absmiddle">사용</span>
    <input type="radio" name="paymethod_bank" value="0" <?=$checked[paymethod_bank][0]?> class="absmiddle"/><span class="absmiddle">미사용</span>
    </td>
    <th>현금영수증 사용여부</th>
    <td>
    <input type="radio" name="cash_receipt" value="1" <?=$checked[cash_receipt][1]?> class="absmiddle"/><span class="absmiddle">사용</span>
    <input type="radio" name="cash_receipt" value="0" <?=$checked[cash_receipt][0]?> class="absmiddle"/><span class="absmiddle">미사용</span>
    </td>
</tr>
<tr>
    <th>신용거래 사용여부</th>
    <td colspan="3">
    <input type="radio" name="paymethod_credit" value="1" <?=$checked[paymethod_credit][1]?> class="absmiddle" onclick="$j('#emoney_ratio').hide()"/><span class="absmiddle">신용거래1 사용</span>
    <!--<input type="radio" name="paymethod_credit" value="2" <?=$checked[paymethod_credit][2]?> class="absmiddle" onclick="$j('#emoney_ratio').show()"/><span class="absmiddle">신용거래2 사용</span> -->
    <input type="radio" name="paymethod_credit" value="0" <?=$checked[paymethod_credit][0]?> class="absmiddle" onclick="$j('#emoney_ratio').hide()"/><span class="absmiddle">미사용</span>
    <div <?if($checked[paymethod_credit][2]){?> style="display:display" <?} else {?> style="display:none" <?}?> id="emoney_ratio">
        적립금 결제 비율 : <input type="text" name="emoney_ratio" style="text-align: right" value="<?=$cfg[pg][emoney_ratio]?>" />%
    </div>
    </td>
</tr>
<tr>
   <th>최소주문금액 사용여부</th>
   <td colspan="3">
      <input type="radio" name="use_min_orderprice" value="Y" <?=$checked[use_min_orderprice][Y]?> class="absmiddle" onclick="$j('#min_orderprice').show()"/><span class="absmiddle"> 사용</span>
      <input type="radio" name="use_min_orderprice" value="N" <?=$checked[use_min_orderprice][N]?> class="absmiddle" onclick="$j('#min_orderprice').hide()"/><span class="absmiddle"> 미사용</span>
      <div <?if($checked[use_min_orderprice][Y]){?> style="display:display" <?} else {?> style="display:none" <?}?> id="min_orderprice">
        최소 주문금액 : <input type="text" name="min_orderprice" value="<?=number_format($cfg[min_orderprice])?>" />원
    </div>
   </td>
</tr>
</table><br/>

<div class="stit">무통장 입금계좌 설정</div>
<table class="tb22">
<tr>
	<th>번호</th>
	<th>입금계좌</th>
	<th>수정</th>
	<th>삭제</th>
</tr>
<? foreach ($bank as $v){ ?>
<tr align="center">
	<td width="80"><?=$v[bankno]?></td>
	<td align="left"><?=$v[bankinfo]?></td>
	<td width="50"><img src="../img/bt_mod.png" onclick="popup('bankinfo.p.php?bankno=<?=$v[bankno]?>',630,200)" class="hand"></td>
	<td width="50"><a href="indb.php?mode=delBank&bankno=<?=$v[bankno]?>" onclick="return confirm('정말로 삭제하시겠습니까?')"><img src="../img/bt_del.png"></td>
</tr>
<? } ?>
</table>

<div class="btn">
<img src="../img/bt_new_l.png"  onclick="popup('bankinfo.p.php?mode=addBank',630,200)" class="hand">
</div>

<div class="stit">PG설정</div>
<table class="tb1" id="kcp_tb">
<tr>
	<th>PG사 선택</th>
	<td>
	<select name="module" required onchange="swap_module(this.value)">
	<option value="no"/> PG사용안함 </option>

<?	foreach ($r_pg_kind as $key => $value) { ?>
		<option value="<?=$key?>" <?=$selected[module][$key]?>/><?=$value?></option>
<? } ?>	
	</select>
<?	if ($r_pg_kind[kcp])	{ ?><a href="http://kcp.co.kr/technique.info.do" id="kcp_url" class="pg_url" target="_blank">KCP 바로가기</a><?	} ?>
<?	if ($r_pg_kind[inicis])	{ ?>	<a href="http://www.inicis.com" id="inicis_url" class="pg_url" target="_blank">이니시스 바로가기</a><? } ?>
	</td>
</tr>
</table>

<div id="no_box" class="swapbox" align="center" style="padding:50px;background:#FFFFFF;">
PG 사용 안함
</div>

<table class="tb1 swapbox" id="kcp_box" style="border-top:0;">
<tr>
	<th>PG결제수단</th>
	<td>
	<input type="checkbox" name="paymethod[]" value="c" <?=$checked[paymethod][c]?> class="absmiddle"/><span class="absmiddle">신용카드</span>
	<input type="checkbox" name="paymethod[]" value="o" <?=$checked[paymethod][o]?> class="absmiddle"/><span class="absmiddle">계좌이체</span>
	<input type="checkbox" name="paymethod[]" value="v" <?=$checked[paymethod][v]?> class="absmiddle"/><span class="absmiddle">가상계좌</span>
	<input type="checkbox" name="paymethod[]" value="h" <?=$checked[paymethod][h]?> class="absmiddle"/><span class="absmiddle">휴대폰</span>
	
	<span class="stxt warning">(반드시 KCP와 계약한 결제수단만 체크하세요.)</span>
	</td>
</tr>
<tr>
	<th>KCP Code</th>
	<td>
	<input type="text" name="code" value="<?=$cfg[pg][code]?>" class="w300" required/>
	</td>
</tr>
<tr>
	<th>KCP key</th>
	<td>
	<input type="text" name="key" value="<?=$cfg[pg]['key']?>" class="w300" required/>
	</td>
</tr>
<tr>
	<th>할부허용기간</th>
	<td>
	<input type="text" name="quotaopt" value="<?=$cfg[pg][quotaopt]?>" required size="2" pt="_pt_numplus"/> 개월까지
	<div class="desc">
	 할부옵션 : Payplus Plug-in에서 카드결제시 최대로 표시할 할부개월 수를 설정합니다.(0 ~ 18 까지 설정 가능)<br/>
    <div class="warning">※ 주의  - 할부 선택은 결제금액이 50,000원 이상일 경우에만 가능, 50000원 미만의 금액은 일시불로만 표기됩니다.</div>
	예) value 값을 "5" 로 설정했을 경우 => 카드결제시 결제창에 일시불부터 5개월까지 선택가능
	</div>
	</td>
</tr>
<tr>
	<th>가상계좌 입금통보 URL</th>
	<td>
	<b>http://<?=$_SERVER[HTTP_HOST]?>/pg/kcp/pay_return.php</b>
	<div class="desc warning">KCP 가맹점 관리자 페이지 > 상점정보관리 > 정보변경 > 공통URL정보  > 공통URL 변경후 에 위주소를 반드시 넣으셔야 합니다. [인코딩설정 : UTF-8] </div>
	</td>
</tr>
<tr>
	<th>에스크로사용여부</th>
	<td>
	<input type="radio" name="escrow" value="0" <?=$checked[escrow][kcp][0]?>>미사용
	<input type="radio" name="escrow" value="1" <?=$checked[escrow][kcp][1]?>>사용
	<span class="stxt warning">(반드시 KCP와의 계약내용과 일치하게 설정해주세요.)</span>
	</td>
</tr>
<tr>
	<th>에스크로 결제수단</th>
	<td>

	<input type="checkbox" name="e_paymethod[]" value="ve" <?=$checked[e_paymethod][ve]?>/> 가상계좌
	<span style="display:none">
	<input type="checkbox" name="e_paymethod[]" value="oe"/> 계좌이체
	</span>
	<span class="stxt warning">(반드시 KCP와 계약한 결제수단만 체크하세요.)</span>
	</td>
</tr>
<tr>
	<th>PG별도계약 무이자</th>
	<td>
	<input type="radio" name="kcp_noint" value="1" onclick="$j('#kcp_noint_str').show()"/> 사용
	<input type="radio" name="kcp_noint" value="0" onclick="$j('#kcp_noint_str').hide()" checked/> 미사용
	<div style="margin-top:5px;display:none" id="kcp_noint_str">무이자 코드 : <input type="text" name="kcp_noint_str"/></div>
	</td>
</tr>
<tr>
	<th>스킨타입</th>
	<td>
	<select name="kcp_skin_indx">
	<? for ($i=1;$i<=7;$i++){ ?>
	<option value="<?=$i?>"/> SKIN.<?=$i?>
	<? } ?>
	</select>
	</td>
</tr>
<tr>
	<th>로고이미지</th>
	<td>
	<input type="file" name="kcp_site_logo" class="w300"/>
	<span class="desc">( 150px×50px )</div>
	</td>
</tr>
</table>

<table class="tb1 swapbox" id="inicis_box" style="border-top:0;">
<tr>
	<th>상점아이디</th>
	<td>
	<input type="text" name="mid" value="<?=$cfg[pg][mid]?>" class="w300" required/>
	</td>
</tr>
<tr>
	<th>에스크로 상점아이디</th>
	<td>
	<input type="text" name="mid_e" value="<?=$cfg[pg][mid_e]?>" class="w300"/>
	</td>
</tr>
<tr>
	<th>키패스워드</th>
	<td>
	<input type="text" name="admin" value="<?=$cfg[pg][admin]?>" class="w300" required/>
	</td>
</tr>
<tr>
	<th>PG결제수단</th>
	<td>
	<input type="checkbox" name="paymethod[]" value="c" <?=$checked[paymethod][c]?> class="absmiddle"/><span class="absmiddle">신용카드</span>
	<input type="checkbox" name="paymethod[]" value="o" <?=$checked[paymethod][o]?> class="absmiddle"/><span class="absmiddle">계좌이체</span>
	<input type="checkbox" name="paymethod[]" value="v" <?=$checked[paymethod][v]?> class="absmiddle"/><span class="absmiddle">가상계좌</span>
	<input type="checkbox" name="paymethod[]" value="h" <?=$checked[paymethod][h]?> class="absmiddle"/><span class="absmiddle">휴대폰</span>
	<span class="stxt warning">(반드시 이니시스와 계약한 결제수단만 체크하세요.)</span>
	</td>
</tr>
<tr>
	<th>할부허용기간</th>
	<td>
	<input type="text" name="quotaopt" value="<?=$cfg[pg][quotaopt]?>" required size="2" pt="_pt_numplus"/> 개월까지
	<div class="desc">
	 할부옵션 : Payplus Plug-in에서 카드결제시 최대로 표시할 할부개월 수를 설정합니다.(0 ~ 18 까지 설정 가능)<br/>
    <div class="warning">※ 주의  - 할부 선택은 결제금액이 50,000원 이상일 경우에만 가능, 50000원 미만의 금액은 일시불로만 표기됩니다.</div>
	예) value 값을 "5" 로 설정했을 경우 => 카드결제시 결제창에 일시불부터 5개월까지 선택가능
	</div>
	</td>
</tr>
<tr>
	<th>가상계좌 입금통보 URL</th>
	<td>
	<b>http://<?=$_SERVER[HTTP_HOST]?>/pg/INIpay50/vacctionput.php</b>
	<div class="desc warning">이니시스 <a href="https://iniweb.inicis.com" target="_blank">상점관리자 페이지</a> > 거래내역 > 거래조회 > 가상계좌 > 입금통보방식선택 메뉴에서 위주소를 반드시 넣으셔야 합니다.</div>
	</td>
</tr>
<tr>
	<th>에스크로사용여부</th>
	<td>
	<input type="radio" name="escrow" value="0" <?=$checked[escrow]['inicis'][0]?>>미사용
	<input type="radio" name="escrow" value="1" <?=$checked[escrow]['inicis'][1]?>>사용
	<span class="stxt warning">(반드시 이니시스와의 계약내용과 일치하게 설정해주세요.)</span>
	</td>
</tr>
<tr>
	<th>에스크로 결제수단</th>
	<td>

	<input type="checkbox" name="e_paymethod[]" value="ve" <?=$checked[e_paymethod][ve]?>/> 가상계좌
	<span style="display:none">
	<input type="checkbox" name="e_paymethod[]" value="oe"/> 계좌이체
	</span>
	<span class="stxt warning">(반드시 이니시스와 계약한 결제수단만 체크하세요.)</span>
	</td>
</tr>
</table>


<table class="tb1 swapbox" id="inipaystdweb_box" style="border-top:0;">
<tr>
	<th>상점아이디</th>
	<td>
	<input type="text" name="mid" value="<?=$cfg[pg][mid]?>" class="w300" required/>
	</td>
</tr>
<tr>
	<th>에스크로 상점아이디</th>
	<td>
	<input type="text" name="mid_e" value="<?=$cfg[pg][mid_e]?>" class="w300"/>
	</td>
</tr>
<tr>
	<th>Sign Key</th>
	<td>
	<input type="text" name="inipay_sign_key" value="<?=$cfg[pg][inipay_sign_key]?>" class="w300" required/>
	<div class="warning">
	* signkey 발급 방법 : 관리자 페이지의 상점정보 > 계약정보 > 부가정보의 웹결제  signkey 생성 조회 버튼 클릭 후 팝업창에서 생성 버튼 클릭 후 해당 값을 반영하시기 바랍니다.
	</div>   
	</td>
</tr>
<tr>
	<th>PG결제수단</th>
	<td>
	<input type="checkbox" name="paymethod[]" value="c" <?=$checked[paymethod][c]?> class="absmiddle"/><span class="absmiddle">신용카드</span>
	<input type="checkbox" name="paymethod[]" value="o" <?=$checked[paymethod][o]?> class="absmiddle"/><span class="absmiddle">계좌이체</span>
	<input type="checkbox" name="paymethod[]" value="v" <?=$checked[paymethod][v]?> class="absmiddle"/><span class="absmiddle">가상계좌</span>
	<input type="checkbox" name="paymethod[]" value="h" <?=$checked[paymethod][h]?> class="absmiddle"/><span class="absmiddle">휴대폰</span>
	<span class="stxt warning">(반드시 이니시스와 계약한 결제수단만 체크하세요.)</span>
	</td>
</tr>
<tr>
	<th>할부허용기간</th>
	<td>
	<input type="text" name="quotaopt" value="<?=$cfg[pg][quotaopt]?>" required size="2" pt="_pt_numplus"/> 개월까지
	<div class="desc">
	 할부옵션 : Payplus Plug-in에서 카드결제시 최대로 표시할 할부개월 수를 설정합니다.(0 ~ 18 까지 설정 가능)<br/>
    <div class="warning">※ 주의  - 할부 선택은 결제금액이 50,000원 이상일 경우에만 가능, 50000원 미만의 금액은 일시불로만 표기됩니다.</div>
	예) value 값을 "5" 로 설정했을 경우 => 카드결제시 결제창에 일시불부터 5개월까지 선택가능
	</div>
	</td>
</tr>
<tr>
	<th>가상계좌 입금통보 URL</th>
	<td>
	<b>http://<?=$_SERVER[HTTP_HOST]?>/pg/INIPayStdWeb/vacctionput.php</b>
	<div class="desc warning">이니시스 <a href="https://iniweb.inicis.com" target="_blank">상점관리자 페이지</a> > 거래내역 > 거래조회 > 가상계좌 > 입금통보방식선택 메뉴에서 위주소를 반드시 넣으셔야 합니다.</div>
	</td>
</tr>
<tr>
	<th>에스크로사용여부</th>
	<td>
	<input type="radio" name="escrow" value="0" <?=$checked[escrow]['inicis'][0]?>>미사용
	<input type="radio" name="escrow" value="1" <?=$checked[escrow]['inicis'][1]?>>사용
	<span class="stxt warning">(반드시 이니시스와의 계약내용과 일치하게 설정해주세요.)</span>
	</td>
</tr>
<tr>
	<th>에스크로 결제수단</th>
	<td>

	<input type="checkbox" name="e_paymethod[]" value="ve" <?=$checked[e_paymethod][ve]?>/> 가상계좌
	<span style="display:none">
	<input type="checkbox" name="e_paymethod[]" value="oe"/> 계좌이체
	</span>
	<span class="stxt warning">(반드시 이니시스와 계약한 결제수단만 체크하세요.)</span>
	</td>
</tr>
</table>


<table class="tb1 swapbox" id="lg_box" style="border-top:0;">
<tr>
	<th>상점아이디</th>
	<td>
	<input type="text" name="lgd_mid" value="<?=$cfg[pg][lgd_mid]?>" class="w300" required/>
	</td>
</tr>
<tr>
	<th>상점키</th>
	<td>
	<input type="text" name="lgd_mertkey" value="<?=$cfg[pg][lgd_mertkey]?>" class="w300" required/>
	</td>
</tr>
<tr>
	<th>스킨</th>
	<td>
	<select name="lgd_custom_skin">
	<option value="red" <?=$selected[lgd_custom_skin][red]?>>red
	<option value="blue" <?=$selected[lgd_custom_skin][blue]?>>blue
	<option value="cyan" <?=$selected[lgd_custom_skin][cyan]?>>cyan
	<option value="green" <?=$selected[lgd_custom_skin][green]?>>green
	<option value="yellow" <?=$selected[lgd_custom_skin][yellow]?>>yellow
	</select>
	</td>
</tr>
<tr>
	<th>PG결제수단</th>
	<td>
	<input type="checkbox" name="paymethod[]" value="c" <?=$checked[paymethod][c]?> class="absmiddle"/><span class="absmiddle">신용카드</span>
	<input type="checkbox" name="paymethod[]" value="o" <?=$checked[paymethod][o]?> class="absmiddle"/><span class="absmiddle">계좌이체</span>
	<input type="checkbox" name="paymethod[]" value="v" <?=$checked[paymethod][v]?> class="absmiddle"/><span class="absmiddle">가상계좌</span>
	<input type="checkbox" name="paymethod[]" value="h" <?=$checked[paymethod][h]?> class="absmiddle"/><span class="absmiddle">휴대폰</span>
	<span class="stxt warning">(반드시 LG유를러스에서 계약한 결제수단만 체크하세요.)</span>
	</td>
</tr>
<tr>
	<th>할부허용기간</th>
	<td>
	<input type="text" name="quotaopt" value="<?=$cfg[pg][quotaopt]?>" required size="2" pt="_pt_numplus"/> 개월까지
	<div class="desc">
	 할부옵션 : Payplus Plug-in에서 카드결제시 최대로 표시할 할부개월 수를 설정합니다.(0 ~ 18 까지 설정 가능)<br/>
    <div class="warning">※ 주의  - 할부 선택은 결제금액이 50,000원 이상일 경우에만 가능, 50000원 미만의 금액은 일시불로만 표기됩니다.</div>
	예) value 값을 "5" 로 설정했을 경우 => 카드결제시 결제창에 일시불부터 5개월까지 선택가능
	</div>
	</td>
</tr>
</table>

<table class="tb1 swapbox" id="easypay80_box" style="border-top:0;">
<tr>
	<th>상점아이디</th>
	<td>
	<input type="text" name="mid" value="<?=$cfg[pg][mid]?>" class="w300" required/>
	</td>
</tr>
<tr>
	<th>PG결제수단</th>
	<td>
	<input type="checkbox" name="paymethod[]" value="c" <?=$checked[paymethod][c]?> class="absmiddle"/><span class="absmiddle">신용카드</span>
	<input type="checkbox" name="paymethod[]" value="o" <?=$checked[paymethod][o]?> class="absmiddle"/><span class="absmiddle">계좌이체</span>
	<input type="checkbox" name="paymethod[]" value="v" <?=$checked[paymethod][v]?> class="absmiddle"/><span class="absmiddle">가상계좌</span>
	<input type="checkbox" name="paymethod[]" value="h" <?=$checked[paymethod][h]?> class="absmiddle"/><span class="absmiddle">휴대폰</span>
	<span class="stxt warning">(반드시 이지페이와 계약한 결제수단만 체크하세요.)</span>
	</td>
</tr>

<tr>
	<th>가상계좌 입금통보 URL</th>
	<td>
	<b>http://<?=$_SERVER[HTTP_HOST]?>/pg/easypay80/easypay_vbank_noti.php</b>
	<div class="desc warning">상점관리자 페이지 입금통보방식선택 메뉴에서 위주소를 반드시 넣으셔야 합니다.</div>
	</td>
</tr>
</table>


<table class="tb1 swapbox" id="alipay_box" style="border-top:0;">

<tr>
	<th>Partner code</th>
	<td>
	<input type="text" name="ali_partner" value="<?=$cfg[pg][ali_partner]?>" class="w300" required/>
	2088로 시작하는 16자리 숫자.
	</td>
</tr>
<tr>
	<th>Key</th>
	<td>
	<input type="text" name="ali_key" value="<?=$cfg[pg][ali_key]?>" class="w300" required/>
	숫자와 문자로 구성된 32자.
	</td>
</tr>
<tr>
	<th>통화</th>
	<td>
	<select name="ali_currency">
	<option value="USD" <?=$selected[ali_currency][USD]?>>US 달러
	<option value="CNY" <?=$selected[ali_currency][CNY]?>>CN 위안
	</select>
	<input type="checkbox" name="paymethod[]" value="c" checked="true" readonly="true"  style="display:none"/>
	</td>
</tr>
</table>

<table class="tb1 swapbox" id="tenpay_box" style="border-top:0;">
<input type="hidden" name="paymethod[]" value="c" />
<tr>
	<th>APP ID</th>
	<td>
	<input type="text" name="ten_appid" value="<?=$cfg[pg][ten_appid]?>" class="w300" required/>
	숫자와 문자로 구성.
	</td>
</tr>

<tr>
	<th>사업자 ID</th>
	<td>
	<input type="text" name="ten_mchid" value="<?=$cfg[pg][ten_mchid]?>" class="w300" required/>
	</td>
</tr>

<tr>
	<th>Key</th>
	<td>
	<input type="text" name="ten_key" value="<?=$cfg[pg][ten_key]?>" class="w300" required/>
	</td>
</tr>

<tr>
	<th>APPSECRET</th>
	<td>
	<input type="text" name="ten_appsecret" value="<?=$cfg[pg][ten_appsecret]?>" class="w300" required/>
	숫자와 문자로 구성된 32자.
	</td>
</tr>

</table>


<div class="btn">
<input type="image" src="../img/bt_submit_l.png"/>
</div>

</form>

<script>

function form_chk_pg(fm){

	/* 선택된 결제수단 */
	if (!($j("[name=paymethod_bank][value=1][checked=true]").length + $j("[name=paymethod_credit][value=1][checked=true]").length + $j("[name=paymethod_credit][value=2][checked=true]").length + $j("input[type=checkbox][name=paymethod[]][checked=true]").length)){
		alert("하나 이상의 결제수단이 선택되어야합니다.");
		return false;
	}
	if (!form_chk(fm)){
		return false;
	}

	if (fm["escrow"][1].checked==true){
		var obj = fm["e_paymethod[]"];
		var ret = false;
		for (var i=0;i<obj.length;i++){
			if (obj[i].checked==true){
				ret = true;
			}
		}
		if (!ret){
			alert("에스크로가 활성화 되어있습니다.하나이상의 에스크로 결제수단을 을 선택해주세요.");
			return false;
		}
	}
	return true;
}

$j(function(){
	swap_module("<?=$cfg[pg][module]?>");
});

function swap_module(module){
	$j('.swapbox').hide();
	$j('input','.swapbox').attr("disabled",true);
	$j("#"+module+"_box").show();
	$j('input',"#"+module+"_box").attr("disabled",false);
	$j('.pg_url').hide();
	$j("#"+module+"_url").show();
}

</script>

<div class="desc" style="border:2px solid #DEDEDE;padding:10px;">
- KCP 정산관리자 페이지 바로가기 : <a href="https://admin8.kcp.co.kr/" target="_blank">https://admin8.kcp.co.kr</a><br/><br/>
- KCP 기술지원 페이지 바로가기 : <a href="http://kcp.co.kr/technique.info.do" target="_blank">http://kcp.co.kr/technique.info.do</a><br/><br/><br/><br/>

- 이니시스 정산관리자 페이지 바로가기 : <a href="https://iniweb.inicis.com/" target="_blank">https://iniweb.inicis.com/</a><br/><br/>
- 이니시스 기술지원 페이지 바로가기 : <a href="https://www.inicis.com/blog/archives/481" target="_blank">https://www.inicis.com/blog/archives/481</a>
</div>

<?include "../_footer.php";?>