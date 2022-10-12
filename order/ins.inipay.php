<?

/* INIsecurepaystart.php
*
* 이니페이 웹페이지 위변조 방지기능이 탑재된 결제요청페이지.
* 코드에 대한 자세한 설명은 매뉴얼을 참조하십시오.
* <주의> 구매자의 세션을 반드시 체크하도록하여 부정거래를 방지하여 주십시요.
*
* http://www.inicis.com
* Copyright (C) 2006 Inicis Co., Ltd. All rights reserved.
*/

/**************************************************************************************************
0. 세션 시작
**************************************************************************************************/

session_start(); //주의:파일 최상단에 위치시켜주세요!!

/**************************************************************************************************
1. 라이브러리 인클루드 *
**************************************************************************************************/

require($_SERVER["DOCUMENT_ROOT"]."/pg/INIpay50/libs/INILib.php");

/**************************************************************************************************
2. INIpay50 클래스의 인스턴스 생성
**************************************************************************************************/

$inipay = new INIpay50;

/**************************************************************************************************
3. 암호화 대상/값 설정
**************************************************************************************************/

$inipay->SetField("inipayhome",$_SERVER["DOCUMENT_ROOT"]."/pg/INIpay50");
$inipay->SetField("type", "chkfake");		// 고정 (절대 수정 불가)
$inipay->SetField("debug", "true");			// 로그모드("true"로 설정하면 상세로그가 생성됨)
$inipay->SetField("enctype","asym");		// asym:비대칭, symm:대칭(현재 asym으로 고정)

/**************************************************************************************************
 admin 은 키패스워드 변수명입니다. 수정하시면 안됩니다. 1111의 부분만 수정해서 사용하시기 바랍니다.
 키패스워드는 상점관리자 페이지(https://iniweb.inicis.com)의 비밀번호가 아닙니다. 주의해 주시기 바랍니다.
 키패스워드는 숫자 4자리로만 구성됩니다. 이 값은 키파일 발급시 결정됩니다.
 키패스워드 값을 확인하시려면 상점측에 발급된 키파일 안의 readme.txt 파일을 참조해 주십시오.
**************************************************************************************************/

$inipay->SetField("admin",$cfg[pg][admin]);		// 키패스워드(키발급시 생성, 상점관리자 패스워드와 상관없음)
$inipay->SetField("checkopt", "false");			// base64함:false, base64안함:true(현재 false로 고정)

/**************************************************************************************************
 필수항목	: mid, price, nointerest, quotabase
 추가가능	: INIregno, oid
 주의		:
 추가가능한 항목중 암호화 대상항목에 추가한 필드는 반드시 hidden 필드에선 제거하고 SESSION이나 DB를 이용해 다음페이지(INIsecureresult.php)로 전달/셋팅되어야 합니다.
**************************************************************************************************/

if ($post[escrow]){
	$inipay->SetField("mid",$cfg[pg][mid_e]);	// 상점아이디
} else {
	$inipay->SetField("mid",$cfg[pg][mid]);	// 상점아이디
}
$inipay->SetField("price",$payprice);	// 가격
$inipay->SetField("nointerest","no");	//무이자여부(no:일반, yes:무이자)

### 할부기간 $inipay->SetField("quotabase", "선택:일시불:2개월:3개월:6개월");
$r_quotaCard = array('선택','일시불');
for ($i=2;$i<=$cfg[pg][quotaopt];$i++) $r_quotaCard[] = $i."개월";
$cfg[pg][quotaCard] = implode(":",$r_quotaCard);
$cfg[pg][quotaCard] = iconv("UTF-8","EUC-KR",$cfg[pg][quotaCard]);
$inipay->SetField("quotabase", $cfg[pg][quotaCard]);

/**************************************************************************************************
 4. 암호화 대상/값을 암호화함
**************************************************************************************************/

$inipay->startAction();

/**************************************************************************************************
5. 암호화 결과
**************************************************************************************************/

if( $inipay->GetResult("ResultCode") != "00" ) {
	echo $inipay->GetResult("ResultMsg");
	exit(0);
}

/**************************************************************************************************
* 6. 세션정보 저장  *
**************************************************************************************************/

if ($post[escrow]){
	$_SESSION['INI_MID']	= $cfg[pg][mid_e];					//상점ID
} else {
	$_SESSION['INI_MID']	= $cfg[pg][mid];					//상점ID
}
$_SESSION['INI_ADMIN']	= $cfg[pg][admin];					//키패스워드(키발급시생성, 상점관리자패스워드와상관없음)
$_SESSION['INI_PRICE']	= $payprice;						//가격
$_SESSION['INI_RN']		= $inipay->GetResult("rn");			//고정(절대수정불가)
$_SESSION['INI_ENCTYPE']= $inipay->GetResult("enctype");	//고정(절대수정불가)

$r_gopaymethod = array(
	'c'		=> 'Card',
	'o'		=> 'DirectBank',
	'oe'	=> 'DirectBank',
	'v'		=> 'VBank',
	've'	=> 'VBank',
	'h'		=> 'HPP',
	/*'b'	=> '무통장입금',*/
	/*'e'	=> '적립금결제',*/
	/*'ve'=> '에스크로(가상계좌)',*/
	/*'oe'=> '에스크로(계좌이체)',*/
	/*'t'	=> '신용거래',*/
	);

$selected[gopaymethod][$r_gopaymethod[substr($post[paymethod],0,1)]] = "selected";

?>

<script language=javascript src="http://plugin.inicis.com/pay61_secuni_cross.js"></script>
<script language=javascript>StartSmartUpdate();</script>

<!-------------------------------------------------------------------------------------------------
* 웹SITE 가 https를 이용하면 https://plugin.inicis.com/pay61_secunissl_cross.js 사용 
* 웹SITE 가 Unicode(UTF-8)를 이용하면 http://plugin.inicis.com/pay61_secuni_cross.js 사용
* 웹SITE 가 https, unicode를 이용하면 https://plugin.inicis.com/pay61_secunissl_cross.js 사용  
-------------------------------------------------------------------------------------------------->

<!-------------------------------------------------------------------------------------------------
상단 자바스크립트는 지불페이지를 실제 적용하실때 지불페이지 맨위에 위치시켜 적용하여야 만일에 발생할수 있는 플러그인 오류를 미연에 방지할 수 있습니다.
 
<script language=javascript src="http://plugin.inicis.com/pay61_secuni_cross.js"></script>
<script language=javascript>StartSmartUpdate();	// 플러그인 설치(확인)</script>
-------------------------------------------------------------------------------------------------->

<script language=javascript>
var openwin;

function pay(frm){

	/**************************************************************************************************
	MakePayMessage()를 호출함으로써 플러그인이 화면에 나타나며, Hidden Field 에 값들이 채워지게 됩니다.
	일반적인 경우, 플러그인은 결제처리를 직접하는 것이아니라, 중요한 정보를 암호화 하여 Hidden Field의 값들을 채우고 종료하며, 다음 페이지인 INIsecureresult.php로 데이터가 포스트 되어 결제 처리됨을 유의하시기 바랍니다.
	**************************************************************************************************/

	if(document.ini.clickcontrol.value == "enable"){
		// 필수항목 체크 (상품명, 상품가격, 구매자명, 구매자 이메일주소, 구매자 전화번호)
		if (document.ini.goodname.value == ""){ alert("상품명이 빠졌습니다. 필수항목입니다."); return false; }
		if (document.ini.buyername.value == ""){ alert("구매자명이 빠졌습니다. 필수항목입니다."); return false; }
		if (document.ini.buyeremail.value == ""){ alert("구매자 이메일주소가 빠졌습니다. 필수항목입니다."); return false; }
		if (document.ini.buyertel.value == ""){ alert("구매자 전화번호가 빠졌습니다. 필수항목입니다."); return false; }
		 // 플러그인 설치유무 체크
		if ((navigator.userAgent.indexOf("MSIE")>=0 || navigator.appName=='Microsoft Internet Explorer')
			&& (document.INIpay==null || document.INIpay.object==null)){
			alert("\n이니페이 플러그인 128이 설치되지 않았습니다. \n\n안전한 결제를 위하여 이니페이 플러그인 128의 설치가 필요합니다.\n\n다시 설치하시려면 Ctrl + F5키를 누르시거나 메뉴의 [보기/새로고침]을 선택하여 주십시오.");
			return false;
		}

		/**************************************************************************************************
		플러그인이 참조하는 각종 결제옵션을 이곳에서 수행할 수 있습니다.
		(자바스크립트를 이용한 동적 옵션처리)
		**************************************************************************************************/
		
		if (MakePayMessage(frm)){
			//disable_click();
			//openwin = window.open("childwin.html","childwin","width=299,height=149");		
			return true;
		} 

		if (IsPluginModule()){	//plugin타입 체크
			alert("결제를 취소하셨습니다."); return false;
		}
	} else {
		return false;
	}
}
</script>

<script>
function MM_reloadPage(init){	//reloads the window if Nav4 resized
	if (init==true) with (navigator) {
		if ((appName=="Netscape")&&(parseInt(appVersion)==4)){
			 document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage;
		}
	} else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}

MM_reloadPage(true);

function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
</script>

<form name="ini" method="post" action="/pg/INIpay50/INIsecureresult.php" onSubmit="return pay(this)" target="_parent">

<table width="100%" border="1" cellspacing="0" cellpadding="0">
<tr>
	<td>결제방법</td>
	<td>

	<select name="gopaymethod">
	<option value="">[ 결제방법을 선택하세요. ]
	<option value="Card"           <?=$selected[gopaymethod][Card]?>>신용카드 결제
	<option value="VCard"          <?=$selected[gopaymethod][VCard]?>>인터넷안전 결제 
	<option value="DirectBank"     <?=$selected[gopaymethod][DirectBank]?>>실시간 은행계좌이체 
	<option value="HPP"            <?=$selected[gopaymethod][HPP]?>>핸드폰 결제
	<option value="PhoneBill"      <?=$selected[gopaymethod][PhoneBill]?>>받는전화결제 
	<option value="Ars1588Bill"    <?=$selected[gopaymethod][Ars1588Bill]?>>1588 전화 결제 
	<option value="VBank"          <?=$selected[gopaymethod][VBank]?>>무통장 입금 
	<option value="OCBPoint"       <?=$selected[gopaymethod][OCBPoint]?>>OK 캐쉬백포인트 결제
	<option value="Culture"        <?=$selected[gopaymethod][Culture]?>>문화상품권 결제
	<option value="kmerce"         <?=$selected[gopaymethod][kmerce]?>>K-merce 상품권 결제
	<option value="TeenCash"       <?=$selected[gopaymethod][TeenCash]?>>틴캐시 결제
	<option value="dgcl"           <?=$selected[gopaymethod][dgcl]?>>게임문화 상품권 결제
	<option value="BCSH"           <?=$selected[gopaymethod][BCSH]?>>도서문화 상품권 결제
	<option value="OABK"           <?=$selected[gopaymethod][OABK]?>>미니뱅크 결제
	<option value="onlycard"       <?=$selected[gopaymethod][onlycard]?>>신용카드 결제(전용메뉴) 
	<option value="onlyisp"        <?=$selected[gopaymethod][onlyisp]?>>인터넷안전 결제 (전용메뉴) 
	<option value="onlydbank"      <?=$selected[gopaymethod][onlydbank]?>>실시간 은행계좌이체 (전용메뉴) 
	<option value="onlycid"        <?=$selected[gopaymethod][onlycid]?>>신용카드/계좌이체/무통장입금(전용메뉴) 
	<option value="onlyvbank"      <?=$selected[gopaymethod][onlyvbank]?>>무통장입금(전용메뉴) 
	<option value="onlyhpp"        <?=$selected[gopaymethod][onlyhpp]?>>핸드폰 결제(전용메뉴) 
	<option value="onlyphone"      <?=$selected[gopaymethod][onlyphone]?>>전화 결제(전용메뉴) 
	<option value="onlyocb"        <?=$selected[gopaymethod][onlyocb]?>>OK 캐쉬백 결제 - 복합결제 불가능(전용메뉴) 
	<option value="onlyocbplus"    <?=$selected[gopaymethod][onlyocbplus]?>>OK 캐쉬백 결제- 복합결제 가능(전용메뉴) 
	<option value="onlyculture"    <?=$selected[gopaymethod][onlyculture]?>>문화상품권 결제(전용메뉴) 
	<option value="onlykmerce"     <?=$selected[gopaymethod][onlykmerce]?>>K-merce 상품권 결제(전용메뉴)
	<option value="onlyteencash"   <?=$selected[gopaymethod][onlyteencash]?>>틴캐시 결제(전용메뉴)
	<option value="onlydgcl"       <?=$selected[gopaymethod][onlydgcl]?>>게임문화 상품권 결제(전용메뉴)
	<option value="onlypoint"      <?=$selected[gopaymethod][onlypoint]?>>LGmyPoint
	<option value="onlybcsh"       <?=$selected[gopaymethod][onlybcsh]?>>도서문화 상품권 결제(전용메뉴)
	<option value="onlyoabk"       <?=$selected[gopaymethod][onlyoabk]?>>미니뱅크 결제(전용메뉴)
	</select>
	
	</td>
</tr>
<tr>
	<td>상품명</td>
	<td><input type="text" name="goodname" value="<?=$pg_goodsnm?>"></td>
</tr>
<tr>
	<td>성명</td>
	<td><input type="text" name="buyername" value="<?=$post[orderer_name]?>"></td>
</tr>
<tr>
	<td>전자우편</td>
	<td><input type="text" name="buyeremail" value="<?=$post[orderer_email]?>"></td>
</tr>
<!-----------------------------------------------------------------------------------------------------
※ 주의 ※
보호자 이메일 주소입력 받는 필드는 소액결제(핸드폰 , 전화결제)
중에  14세 미만의 고객 결제시에 부모 이메일로 결제 내용통보하라는 정통부 권고 사항입니다. 
다른 결제 수단을 이용시에는 해당 필드(parentemail)삭제 하셔도 문제없습니다.
------------------------------------------------------------------------------------------------------->
<tr>
	<td>보호자 전자우편</td>
	<td><input type="text" name="parentemail" value="parents@parents.com"></td>
</tr>
<tr>
	<td>이동전화</td>
	<td><input type="text" name="buyertel" value="<?=$post[orderer_mobile]?>"></td>
</tr>
<tr>
	<td>
	전자우편과 이동전화번호를 입력받는 것은 고객님의 결제성공 내역을 E-MAIL 또는 SMS 로알려드리기 위함이오니 반드시 기입하시기 바랍니다.
	</td>
</tr>
</table>

<!-- 기타설정 ------------------------------------------------------------------------------------------>
<input type="hidden" name="currency" value="WON">

<!--현금영수증 필드-->
<!--
<input type="hidden" name="acceptmethod" value="{ ? _cfg.pg.cash_receipt == '1' } receipt { : } no_receipt { / }" />
<input type="hidden" name="no_receipt" value="{ ? _cfg.pg.cash_receipt == '1' } Y { : } N { / }">
-->
<!-----------------------------------------------------------------------------------------------------
	SKIN : 플러그인 스킨 칼라 변경 기능 - 6가지 칼라(ORIGINAL, GREEN, ORANGE, BLUE, KAKKI, GRAY)
	HPP : 컨텐츠 또는 실물 결제 여부에 따라 HPP(1)과 HPP(2)중 선택 적용(HPP(1):컨텐츠, HPP(2):실물).
	Card(0): 신용카드 지불시에 이니시스 대표 가맹점인 경우에 필수적으로 세팅 필요 ( 자체 가맹점인 경우에는 카드사의 계약에 따라 설정) - 자세한 내용은 메뉴얼  참조.
	OCB : OK CASH BAG 가맹점으로 신용카드 결제시에 OK CASH BAG 적립을 적용하시기 원하시면 "OCB" 세팅 필요 그 외에 경우에는 삭제해야 정상적인 결제 이루어짐.
	no_receipt : 은행계좌이체시 현금영수증 발행여부 체크박스 비활성화 (현금영수증 발급 계약이 되어 있어야 사용가능)
------------------------------------------------------------------------------------------------------->

<input type="hidden" name="acceptmethod" value="HPP(2):Card(0):OCB:<?if($cfg[pg][cash_receipt]){?>va_receipt<?}else{?>receipt<?}?>:cardpoint">

<!-----------------------------------------------------------------------------------------------------
	상점 주문번호 : 무통장입금 예약(가상계좌 이체),전화결재 관련 필수필드로 반드시 상점의 주문번호를 페이지에 추가해야 합니다.
	결제수단 중에 은행 계좌이체 이용 시에는 주문 번호가 결제결과를 조회하는 기준 필드가 됩니다.
	상점 주문번호는 최대 40 BYTE 길이입니다.
	주의:절대 한글값을 입력하시면 안됩니다.
------------------------------------------------------------------------------------------------------->

<input type="hidden" name="oid" value="<?=$post[payno]?>">

<!-----------------------------------------------------------------------------------------------------
	플러그인 좌측 상단 상점 로고 이미지 사용
	이미지의 크기 : 90 X 34 pixels
	플러그인 좌측 상단에 상점 로고 이미지를 사용하실 수 있으며,
	주석을 풀고 이미지가 있는 URL을 입력하시면 플러그인 상단 부분에 상점 이미지를 삽입할수 있습니다.
------------------------------------------------------------------------------------------------------->

<!--input type=hidden name=ini_logoimage_url  value="http://[사용할 이미지주소]"-->

<!-----------------------------------------------------------------------------------------------------
	좌측 결제메뉴 위치에 이미지 추가
	이미지의 크기 : 단일 결제 수단 - 91 X 148 pixels, 신용카드/ISP/계좌이체/가상계좌 - 91 X 96 pixels
	좌측 결제메뉴 위치에 미미지를 추가하시 위해서는 담당 영업대표에게 사용여부 계약을 하신 후
	주석을 풀고 이미지가 있는 URL을 입력하시면 플러그인 좌측 결제메뉴 부분에 이미지를 삽입할수 있습니다.
------------------------------------------------------------------------------------------------------->

<!--input type=hidden name=ini_menuarea_url value="http://[사용할 이미지주소]"-->

<!-----------------------------------------------------------------------------------------------------
	플러그인에 의해서 값이 채워지거나, 플러그인이 참조하는 필드들
	삭제/수정 불가
	uid 필드에 절대로 임의의 값을 넣지 않도록 하시기 바랍니다.
------------------------------------------------------------------------------------------------------->

<input type="hidden" name="ini_encfield" value="<?php echo($inipay->GetResult("encfield")); ?>">
<input type="hidden" name="ini_certid" value="<?php echo($inipay->GetResult("certid")); ?>">
<input type="hidden" name="quotainterest" value="">
<input type="hidden" name="paymethod" value="">
<input type="hidden" name="cardcode" value="">
<input type="hidden" name="cardquota" value="">
<input type="hidden" name="rbankcode" value="">
<input type="hidden" name="reqsign" value="DONE">
<input type="hidden" name="encrypted" value="">
<input type="hidden" name="sessionkey" value="">
<input type="hidden" name="uid" value="">
<input type="hidden" name="sid" value="">
<input type="hidden" name="version" value="4000">
<input type="hidden" name="clickcontrol" value="enable">

</form>

<script>
pay(document.ini);
document.ini.submit();
</script>