<?
    /* ============================================================================== */
    /* =   PAGE : 결제 요청 PAGE                                                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   이 페이지는 Payplus Plug-in을 통해서 결제자가 결제 요청을 하는 페이지    = */
    /* =   입니다. 아래의 ※ 필수, ※ 옵션 부분과 매뉴얼을 참조하셔서 연동을        = */
    /* =   진행하여 주시기 바랍니다.                                                = */
    /* = -------------------------------------------------------------------------- = */
    /* =   연동시 오류가 발생하는 경우 아래의 주소로 접속하셔서 확인하시기 바랍니다.= */
    /* =   접속 주소 : http://kcp.co.kr/technique.requestcode.do			        = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2013   KCP Inc.   All Rights Reserverd.                   = */
    /* ============================================================================== */
?>
<?
    /* ============================================================================== */
    /* =   환경 설정 파일 Include                                                   = */
    /* = -------------------------------------------------------------------------- = */
    /* =   ※ 필수                                                                  = */
    /* =   테스트 및 실결제 연동시 site_conf_inc.jsp 파일을 수정하시기 바랍니다.    = */
    /* = -------------------------------------------------------------------------- = */

	
	include_once "./site_conf_inc.php";
	include_once dirname(__FILE__) ."/../../../lib/library.php";
	include_once dirname(__FILE__) ."/../../../pretty/_module_util.php";

?>
<?
    /* = -------------------------------------------------------------------------- = */
    /* =   환경 설정 파일 Include END                                               = */
    /* ============================================================================== */
?>
<?
    /* kcp와 통신후 kcp 서버에서 전송되는 결제 요청 정보 */
	$req_tx          = $_POST[ "req_tx"         ]; // 요청 종류         
	$res_cd          = $_POST[ "res_cd"         ]; // 응답 코드         
	$tran_cd         = $_POST[ "tran_cd"        ]; // 트랜잭션 코드     
	$ordr_idxx       = $_POST[ "ordr_idxx"      ]; // 쇼핑몰 주문번호   
	$good_name       = $_POST[ "good_name"      ]; // 상품명            
	$good_mny        = $_POST[ "good_mny"       ]; // 결제 총금액       
	$buyr_name       = $_POST[ "buyr_name"      ]; // 주문자명          
	$buyr_tel1       = $_POST[ "buyr_tel1"      ]; // 주문자 전화번호   
	$buyr_tel2       = $_POST[ "buyr_tel2"      ]; // 주문자 핸드폰 번호
	$buyr_mail       = $_POST[ "buyr_mail"      ]; // 주문자 E-mail 주소
	$use_pay_method  = $_POST[ "use_pay_method" ]; // 결제 방법         
	$ipgm_date       = $_POST[ "ipgm_date"      ]; // 가상계좌 마감시간 
	$enc_info        = $_POST[ "enc_info"       ]; // 암호화 정보       
	$enc_data        = $_POST[ "enc_data"       ]; // 암호화 데이터     
	$van_code        = $_POST[ "van_code"       ];
	$cash_yn         = $_POST[ "cash_yn"        ];
	$cash_tr_code    = $_POST[ "cash_tr_code"   ];
	/* 기타 파라메터 추가 부분 - Start - */
	
	$account_type    = $_POST["account_type"];
	$account_point    = $_POST["account_point"];
	$storage_account_kind    = $_POST["storage_account_kind"];
	$storage_month    = $_POST["storage_month"];
	$account_detail_info    = $_POST["account_detail_info"];
	
	
	/* 기타 파라메터 추가 부분 - End -   */

	$tablet_size     = "1.0"; // 화면 사이즈 고정
		

	//debug($_POST);
	//포인트 결제
	if ($_POST[account_type] == "P") {
	   $goods_name = "Bluepod Mall 포인트";
	
	   $r_point_price = $r_printgroup_point_price;
	
	   $account_money = $r_point_price[$_POST[account_point]];
	}
	
	//스토리지 결제
	else if ($_POST[account_type] == "S") {
	   $goods_name = "Bluepod Mall 스토리지";
	   $account_money = calcuStorageAccountPrice($cid, $_POST[storage_size], $_POST[storage_month], $_POST[storage_account_kind]);
	   $account_detail_info = calcuStorageAccountPrice($cid, $_POST[storage_size], $_POST[storage_month], $_POST[storage_account_kind], true);
	}
	
	//printgroup 서비스 결제
	else if ($_POST[account_type] == "E") {
	   $goods_name = "Bluepod Mall 서비스";
	   $account_money = calcuServiceAccountPrice($cid, $_POST[storage_month]);
	   $account_detail_info = calcuServiceAccountPrice($cid, $_POST[storage_month], true);
	}
	
	//가상계좌 입금 처리를 위한 입력 데이타 저장하기..      20150513    chunter
	$micro = explode(" ", microtime());
	$payno = date("YmdHis", $micro[1]) . sprintf("%03d", floor($micro[0] * 1000));
	$query = "insert into tb_pretty_account_payno set
	             payno             = '$payno',
	             cid               = '$cid',
	             mid               = '$sess[mid]',
	             account_money     = '$account_money',
	             account_point     = '$_POST[account_point]',
	             storage_size      = '$_POST[storage_size]',
	             storage_month     = '$_POST[storage_month]',
	             storage_account_kind = '$_POST[storage_account_kind]',
	             account_type      = '$_POST[account_type]',
	             account_detail_info      = '$account_detail_info',
	             paymethod      = '$_POST[account_pay_method]',
	             regist_date            = now()";
	
	$db->query($query);
	
	$paymethod = $_POST[account_pay_method];
	
	if ($cfg[nameComp])
		$buyr_name = $cfg[nameComp];
	else 
		$buyr_name = $cid;

	
?>

<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="euc-kr">
<title>*** KCP [AX-HUB Version] ***</title>

<meta http-equiv="Cache-Control" content="No-Cache">
<meta http-equiv="Pragma" content="No-Cache">

<meta name="viewport" content="width=device-width, user-scalable=<?=$tablet_size?>, initial-scale=<?=$tablet_size?>, maximum-scale=<?=$tablet_size?>, minimum-scale=<?=$tablet_size?>">

<link href="./css/style_mobile.css" rel="stylesheet" type="text/css" id="cssLink"/>

<!-- 거래등록 하는 kcp 서버와 통신을 위한 스크립트-->
<script type="text/javascript" src="js/approval_key.js"></script>

<script type="text/javascript">
  var controlCss = "css/style_mobile.css";
  var isMobile = {
    Android: function() {
      return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
      return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
      return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
      return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
      return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
      return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
  };

  if( isMobile.any() )
    document.getElementById("cssLink").setAttribute("href", controlCss);
</script>
<script type="text/javascript">
   /* 주문번호 생성 예제 */
  function init_orderid()
  {
    var today = new Date();
    var year  = today.getFullYear();
    var month = today.getMonth() + 1;
    var date  = today.getDate();
    var time  = today.getTime();

    if (parseInt(month) < 10)
      month = "0" + month;

    if (parseInt(date) < 10)
      date  = "0" + date;

    var order_idxx = "TEST" + year + "" + month + "" + date + "" + time;
    var ipgm_date  = year + "" + month + "" + date;

    //document.order_info.ordr_idxx.value = order_idxx;
    document.order_info.ipgm_date.value = ipgm_date;
  }

   /* kcp web 결제창 호츨 (변경불가) */
  function call_pay_form()
  {
    var v_frm = document.order_info;

    document.getElementById("sample_wrap").style.display = "none";
    document.getElementById("layer_all").style.display  = "block";

    v_frm.target = "frm_all";
    //v_frm.action = PayUrl;
            
    // 인코딩 방식에 따른 변경  -- Start 
		if(v_frm.encoding_trans == undefined) 
		{ 
		  v_frm.action = PayUrl; 
		} 
		else 
		{ 
		  if(v_frm.encoding_trans.value == "UTF-8") 
		  { 
		      v_frm.action = PayUrl.substring(0,PayUrl.lastIndexOf("/")) + "/jsp/encodingFilter/encodingFilter.jsp";		           
		      v_frm.PayUrl.value = PayUrl; 
		  } 
		  else 
		  { 
		   v_frm.action = PayUrl; 
		  } 
		} 
  	// 인코딩 방식에 따른 변경  -- End 
  
    

    if (v_frm.Ret_URL.value == "")
    {
	  /* Ret_URL값은 현 페이지의 URL 입니다. */
	  	alert("연동시 Ret_URL을 반드시 설정하셔야 됩니다.");
      return false;
    }
    else
    {
      v_frm.submit();
    }
  }

   /* kcp 통신을 통해 받은 암호화 정보 체크 후 결제 요청 (변경불가) */
  function chk_pay()
  {
    self.name = "tar_opener";
    var pay_form = document.pay_form;

    if (pay_form.res_cd.value == "3001" )
    {
      alert("사용자가 취소하였습니다.");
      pay_form.res_cd.value = "";
    }
    else if (pay_form.res_cd.value == "3000" )
    {
      alert("30만원 이상 결제를 할 수 없습니다.");
      pay_form.res_cd.value = "";
    }

    document.getElementById("sample_wrap").style.display = "block";
    document.getElementById("layer_all").style.display  = "none";

    if (pay_form.enc_info.value)
      pay_form.submit();
  }

  function jsf__chk_type()
  {
    if ( document.order_info.ActionResult.value == "card" )
    {
      document.order_info.pay_method.value = "CARD";
    }
    else if ( document.order_info.ActionResult.value == "acnt" )
    {
      document.order_info.pay_method.value = "BANK";
    }
    else if ( document.order_info.ActionResult.value == "vcnt" )
    {
      document.order_info.pay_method.value = "VCNT";
    }
    else if ( document.order_info.ActionResult.value == "mobx" )
    {
      document.order_info.pay_method.value = "MOBX";
    }
    else if ( document.order_info.ActionResult.value == "ocb" )
    {
      document.order_info.pay_method.value = "TPNT";
      document.order_info.van_code.value = "SCSK";
    }
    else if ( document.order_info.ActionResult.value == "tpnt" )
    {
      document.order_info.pay_method.value = "TPNT";
      document.order_info.van_code.value = "SCWB";
    }
    else if ( document.order_info.ActionResult.value == "scbl" )
    {
      document.order_info.pay_method.value = "GIFT";
      document.order_info.van_code.value = "SCBL";
    }
    else if ( document.order_info.ActionResult.value == "sccl" )
    {
      document.order_info.pay_method.value = "GIFT";
      document.order_info.van_code.value = "SCCL";
    }
    else if ( document.order_info.ActionResult.value == "schm" )
    {
      document.order_info.pay_method.value = "GIFT";
      document.order_info.van_code.value = "SCHM";
    }
  }

	
	function CloseWindow()
	{
		window.close();
		self.close();
		window.opener = window.location.href; self.close();
		window.open('about:blank', '_self').close();
	}  
</script>
</head>

<body onload="jsf__chk_type();init_orderid();chk_pay();">
<div id="sample_wrap">
<form name="order_info" method="post">
	<input type="hidden" name="ordr_idxx" value="<?=$payno?>">
	<input type="hidden" name="good_name" value="<?=$goods_name?>">
	<input type="hidden" name="good_mny" value="<?=$account_money?>">
	<input type="hidden" name="kcp_log" value="">
	
  <!-- 타이틀 -->
  <h1>[결제요청] <span>이 페이지는 결제를 요청하는 화면입니다.</span></h1>

  <div class="sample">

    <!-- 상단 문구 -->
    <p>
      이 페이지는 결제를 요청하는 페이지입니다
	</p>

    <!-- 주문 정보 -->
    <h2>&sdot; 주문 정보</h2>
    <table class="tbl" cellpadding="0" cellspacing="0">
      <tr>
        <th>지불 방법</th>
        <td>
            <select name="ActionResult" onchange="jsf__chk_type();">                
                <? if ($paymethod=='c') {?><option value="card">신용카드</option><?	}	?>                
                <? if ($paymethod=='v') {?><option value="vcnt">가상계좌</option><?	}	?>        
            </select>
        </td>
      </tr>
      <tr>
        <th>주문 번호</th>
        <td><?=$payno?></td>
      </tr>
      <tr>
        <th>상품명</th>
        <td><?=$goods_name?></td>
      </tr>
      <tr>
        <th>결제 금액</th>
        <td><?=$account_money?></td>
      </tr>
      <tr>
        <th>주문자명</th>
        <td><input type="text" name="buyr_name" class="w100" value="<?=$buyr_name?>"></td>
      </tr>
      <tr>
        <th>E-mail</th>
        <td><input type="text" name="buyr_mail" class="w200" value="<?=$cfg[emailAdmin]?>"></td>
      </tr>
      <tr>
        <th>전화번호</th>
        <td><input type="text" name="buyr_tel1" class="w100" value="<?=$cfg[phoneComp]?>"></td>
      </tr>
      <tr>
        <th>휴대폰번호</th>
        <td><input type="text" name="buyr_tel2" class="w100" value="<?=$cfg[mobileAdmin]?>"></td>
      </tr>
    </table>

    <!-- 결제 요청/처음으로 이미지 -->
	<div class="footer">
		<b>※ 결제완료전까지 창을 닫거나 새로고침 하지 마세요. ※</b>
	</div>
	<div class="btnset" id="display_pay_button" style="display:block">
      <input name="" type="button" class="submit" value="결제요청" onclick="kcp_AJAX();">      
      <a href="javascript:CloseWindow();" class="home">닫기</a>
    </div>
  </div>
  <!--footer-->
  <div class="footer">
    Copyright (c) KCP INC. All Rights reserved.
  </div>
  <!--//footer-->
  
  <!-- 공통정보 -->
  <input type="hidden" name="req_tx"          value="pay">                           <!-- 요청 구분 -->
  <input type="hidden" name="shop_name"       value="<?= $g_conf_site_name ?>">      <!-- 사이트 이름 --> 
  <input type="hidden" name="site_cd"         value="<?= $g_conf_site_cd   ?>">      <!-- 사이트 코드 -->
  <input type="hidden" name="currency"        value="410"/>                          <!-- 통화 코드 -->
  <input type="hidden" name="eng_flag"        value="N"/>                            <!-- 한 / 영 -->
  <!-- 결제등록 키 -->
  <input type="hidden" name="approval_key"    id="approval">
  <!-- 인증시 필요한 파라미터(변경불가)-->
  <input type="hidden" name="escw_used"       value="N">
  <input type="hidden" name="pay_method"      value="">
  <input type="hidden" name="van_code"        value="<?=$van_code?>">
  <!-- 신용카드 설정 -->
  <input type="hidden" name="quotaopt"        value="12"/>                           <!-- 최대 할부개월수 -->
  <!-- 가상계좌 설정 -->
  <input type="hidden" name="ipgm_date"       value=""/>
  <!-- 가맹점에서 관리하는 고객 아이디 설정을 해야 합니다.(필수 설정) -->
  <input type="hidden" name="shop_user_id"    value=""/>
  <!-- 복지포인트 결제시 가맹점에 할당되어진 코드 값을 입력해야합니다.(필수 설정) -->
  <input type="hidden" name="pt_memcorp_cd"   value=""/>
  <!-- 현금영수증 설정 -->
  <input type="hidden" name="disp_tax_yn"     value="Y"/>
  
  <input type="hidden" name="encoding_trans" value="UTF-8" />
  <input type="hidden" name="PayUrl">
  
  <!-- 리턴 URL (kcp와 통신후 결제를 요청할 수 있는 암호화 데이터를 전송 받을 가맹점의 주문페이지 URL) -->
  <input type="hidden" name="Ret_URL"         value="http://<?=$_SERVER[HTTP_HOST]?>/pg/kcp_ilark/mobile/pp_ax_hub.php">
  <!-- 화면 크기조정 -->
  <input type="hidden" name="tablet_size"     value="<?=$tablet_size?>">


<?
    /* ============================================================================== */
    /* =   옵션 정보                                                                = */
    /* = -------------------------------------------------------------------------- = */
    /* =   ※ 옵션 - 결제에 필요한 추가 옵션 정보를 입력 및 설정합니다.             = */
    /* = -------------------------------------------------------------------------- = */
	/* 카드사 리스트 설정
	예) 비씨카드와 신한카드 사용 설정시
	<input type="hidden" name='used_card'    value="CCBC:CCLG">

    /*  무이자 옵션
            ※ 설정할부    (가맹점 관리자 페이지에 설정 된 무이자 설정을 따른다)                             - "" 로 설정
            ※ 일반할부    (KCP 이벤트 이외에 설정 된 모든 무이자 설정을 무시한다)                           - "N" 로 설정
            ※ 무이자 할부 (가맹점 관리자 페이지에 설정 된 무이자 이벤트 중 원하는 무이자 설정을 세팅한다)   - "Y" 로 설정
    <input type="hidden" name="kcp_noint"       value=""/> */

    /*  무이자 설정
            ※ 주의 1 : 할부는 결제금액이 50,000 원 이상일 경우에만 가능
            ※ 주의 2 : 무이자 설정값은 무이자 옵션이 Y일 경우에만 결제 창에 적용
            예) 전 카드 2,3,6개월 무이자(국민,비씨,엘지,삼성,신한,현대,롯데,외환) : ALL-02:03:04
            BC 2,3,6개월, 국민 3,6개월, 삼성 6,9개월 무이자 : CCBC-02:03:06,CCKM-03:06,CCSS-03:06:04
    <input type="hidden" name="kcp_noint_quota" value="CCBC-02:03:06,CCKM-03:06,CCSS-03:06:09"/> */

	/* KCP는 과세상품과 비과세상품을 동시에 판매하는 업체들의 결제관리에 대한 편의성을 제공해드리고자, 
	   복합과세 전용 사이트코드를 지원해 드리며 총 금액에 대해 복합과세 처리가 가능하도록 제공하고 있습니다
	   복합과세 전용 사이트 코드로 계약하신 가맹점에만 해당이 됩니다
       상품별이 아니라 금액으로 구분하여 요청하셔야 합니다
	   총결제 금액은 과세금액 + 부과세 + 비과세금액의 합과 같아야 합니다. 
	   (good_mny = comm_tax_mny + comm_vat_mny + comm_free_mny)
	
	    <input type="hidden" name="tax_flag"       value="TG03">  <!-- 변경불가	   -->
	    <input type="hidden" name="comm_tax_mny"   value=""    >  <!-- 과세금액	   --> 
        <input type="hidden" name="comm_vat_mny"   value=""    >  <!-- 부가세	   -->
	    <input type="hidden" name="comm_free_mny"  value=""    >  <!-- 비과세 금액 --> */
    /* = -------------------------------------------------------------------------- = */
    /* =   옵션 정보 END                                                            = */
    /* ============================================================================== */
?>

</form>
</div>

<!-- 스마트폰에서 KCP 결제창을 레이어 형태로 구현-->
<div id="layer_all" style="position:absolute; left:0px; top:0px; width:100%;height:100%; z-index:1; display:none;">
    <table height="100%" width="100%" border="-" cellspacing="0" cellpadding="0" style="text-align:center">
        <tr height="100%" width="100%">
            <td>
                <iframe name="frm_all" frameborder="0" marginheight="0" marginwidth="0" border="0" width="100%" height="100%" scrolling="auto"></iframe>
            </td>
        </tr>
    </table>
</div>
<form name="pay_form" method="post" action="pp_ax_hub.php">
    <input type="hidden" name="req_tx"         value="<?=$req_tx?>">               <!-- 요청 구분          -->
    <input type="hidden" name="res_cd"         value="<?=$res_cd?>">               <!-- 결과 코드          -->
    <input type="hidden" name="tran_cd"        value="<?=$tran_cd?>">              <!-- 트랜잭션 코드      -->
    <input type="hidden" name="ordr_idxx"      value="<?=$ordr_idxx?>">            <!-- 주문번호           -->
    <input type="hidden" name="good_mny"       value="<?=$good_mny?>">             <!-- 휴대폰 결제금액    -->
    <input type="hidden" name="good_name"      value="<?=$good_name?>">            <!-- 상품명             -->
    <input type="hidden" name="buyr_name"      value="<?=$buyr_name?>">            <!-- 주문자명           -->
    <input type="hidden" name="buyr_tel1"      value="<?=$buyr_tel1?>">            <!-- 주문자 전화번호    -->
    <input type="hidden" name="buyr_tel2"      value="<?=$buyr_tel2?>">            <!-- 주문자 휴대폰번호  -->
    <input type="hidden" name="buyr_mail"      value="<?=$buyr_mail?>">            <!-- 주문자 E-mail      -->
	<input type="hidden" name="cash_yn"		   value="<?=$cash_yn?>">              <!-- 현금영수증 등록여부-->
    <input type="hidden" name="enc_info"       value="<?=$enc_info?>">
    <input type="hidden" name="enc_data"       value="<?=$enc_data?>">
    <input type="hidden" name="use_pay_method" value="<?=$use_pay_method?>">
    <input type="hidden" name="cash_tr_code"   value="<?=$cash_tr_code?>">

    <!-- 추가 파라미터 -->
	
</form>
</body>
</html>
