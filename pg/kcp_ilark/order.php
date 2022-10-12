<?
	include_once "./cfg/site_conf_inc.php";
	include_once "../../lib/library.php";
	include_once "../../pretty/_module_util.php";


	if ($_POST[account_pay_method] == "c")
		$pay_method = "100000000000";
	else if ($_POST[account_pay_method] == "v")
	{
		$pay_method = "001000000000";
		if ($_POST[account_point_vbank])
			$_POST[account_point] = (double)$_POST[account_point_vbank];
	}

	//포인트 결제
	if ($_POST[account_type] == "P") {
		$goods_name = "Bluepod Mall 포인트";

		$r_point_price = $r_printgroup_point_price;

		if ($_POST[account_pay_method] == "c")
			$account_money = $r_point_price[$_POST[account_point]];
		else
			$account_money = $_POST[account_point];
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

	if ($_POST[account_pay_method] == "c")
		$pay_method = "100000000000";
	else if ($_POST[account_pay_method] == "v")
		$pay_method = "001000000000";


	if ($cfg[nameComp])
		$buyr_name = $cfg[nameComp];
	else
		$buyr_name = $cid;

?>
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
  <title>*** KCP [AX-HUB Version] ***</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="css/style.css" rel="stylesheet" type="text/css" id="cssLink"/>
	<script type="text/javascript" src='<?=$g_conf_js_url?>'></script> <!-- standi추가 -->
  <script type="text/javascript">
    /****************************************************************/
    /* m_Completepayment  설명                                      */
    /****************************************************************/
    /* 인증완료시 재귀 함수                                         */
    /* 해당 함수명은 절대 변경하면 안됩니다.                        */
    /* 해당 함수의 위치는 payplus.js 보다먼저 선언되어여 합니다.    */
    /* Web 방식의 경우 리턴 값이 form 으로 넘어옴                   */
    /****************************************************************/
    function m_Completepayment( FormOrJson, closeEvent )
    {
      var frm = document.order_info;

      /********************************************************************/
      /* FormOrJson은 가맹점 임의 활용 금지                               */
      /* frm 값에 FormOrJson 값이 설정 됨 frm 값으로 활용 하셔야 됩니다.  */
      /* FormOrJson 값을 활용 하시려면 기술지원팀으로 문의바랍니다.       */
      /********************************************************************/
      GetField( frm, FormOrJson );


      if( frm.res_cd.value == "0000" )
      {
        alert("결제 승인 요청 전,\n\n반드시 결제창에서 고객님이 결제 인증 완료 후\n\n리턴 받은 ordr_chk 와 업체 측 주문정보를\n\n다시 한번 검증 후 결제 승인 요청하시기 바랍니다."); //업체 연동 시 필수 확인 사항.
        /*
			가맹점 리턴값 처리 영역
		*/
        //$formAction = "../kcp_api_page.php";
        //TODO 작업
        frm.submit();
      }
      else
      {
        alert( "[" + frm.res_cd.value + "] " + frm.res_msg.value );        

        closeEvent();
      }
    }
  </script>
  <script type="text/javascript" src="https://pay.kcp.co.kr/plugin/payplus_web.jsp"></script>
  <script type="text/javascript">
    /* 표준웹 실행 */
    function jsf__pay( form )
    {
      //form.pay_method.value="100000000000"; //신용카드
      try
      {
        KCP_Pay_Execute( form );
      }
      catch (e)
      {
        /* IE 에서 결제 정상종료시 throw로 스크립트 종료 */
      }
    }
  </script>
</head>

<body>
<div id="sample_wrap">
  <!-- 주문정보 입력 form : order_info -->
  <form name="order_info" method="post" action="./pp_ax_hub.php" >
    <input type="hidden" name="pay_method" value="<?=$pay_method?>">
    <input type="hidden" name="good_mny"  value="<?=$account_money?>" />
    <input type="hidden" name="good_name" value="<?=$goods_name?>" />
    <input type="hidden" name="account_detail_info" value="<?=$account_detail_info?>" />
    <input type="hidden" name="ordr_idxx" value="<?=$payno?>" />

	  <? /* ============================================================================== */
		  /* =   1. 주문 정보 입력                                                        = */
		  /* = -------------------------------------------------------------------------- = */
		  /* =   결제에 필요한 주문 정보를 입력 및 설정합니다.                            = */
		  /* = -------------------------------------------------------------------------- = */
	  ?>
    <h1>[결제요청] <span> 이 페이지는 결제를 요청하는 페이지입니다.</span></h1>
    <!-- 상단 문구 -->
    <div class="sample">
      <!-- 주문정보 타이틀 -->
      <h2>&sdot; 주문 정보 (* 항목은 필수 항목입니다.)</h2>
      <table class="tbl" cellpadding="0" cellspacing="0">
		  <? /* ============================================================================== */
			  /* =   1-1. 결제 수단 정보 설정                                                 = */
			  /* = -------------------------------------------------------------------------- = */
			  /* =   결제에 필요한 결제 수단 정보를 설정합니다.                               = */
			  /* =                                                                            = */
			  /* =  신용카드 : 100000000000, 계좌이체 : 010000000000, 가상계좌 : 001000000000 = */
			  /* =  포인트   : 000100000000, 휴대폰   : 000010000000, 상품권   : 000000001000 = */
			  /* =  ARS      : 000000000010                                                   = */
			  /* =                                                                            = */
			  /* =  위와 같이 설정한 경우 PayPlus Plugin에서 설정한 결제수단이 표시됩니다.    = */
			  /* =  Payplug Plugin에서 여러 결제수단을 표시하고 싶으신 경우 설정하시려는 결제 = */
			  /* =  수단에 해당하는 위치에 해당하는 값을 1로 변경하여 주십시오.               = */
			  /* =                                                                            = */
			  /* =  예) 신용카드, 계좌이체, 가상계좌를 동시에 표시하고자 하는 경우            = */
			  /* =  pay_method = "111000000000"                                               = */
			  /* =  신용카드(100000000000), 계좌이체(010000000000), 가상계좌(001000000000)에  = */
			  /* =  해당하는 값을 모두 더해주면 됩니다.                                       = */
			  /* =                                                                            = */
			  /* = ※ 필수                                                                    = */
			  /* =  KCP에 신청된 결제수단으로만 결제가 가능합니다.                            = */
			  /* = -------------------------------------------------------------------------- = */
		  ?>
        <tr>
          <th>지불 방법</th>
          <td><?=$r_pay_kind[$_POST[account_pay_method]]?></td>
        </tr>
        <!-- 주문번호(ordr_idxx) -->
        <tr>
          <th>주문 번호</th>
          <td><?=$payno?></td>
        </tr>
        <!-- 상품명(good_name) -->
        <tr>
          <th>상품명</th>
          <td><?=$goods_name?></td>
        </tr>
        <!-- 결제금액(good_mny) - ※ 필수 : 값 설정시 ,(콤마)를 제외한 숫자만 입력하여 주십시오. -->
        <tr>
          <th>결제 금액</th>
          <td><?=number_format($account_money)?> 원</td>
        </tr>
        <!-- 주문자명(buyr_name) -->
        <tr>
          <th>* 주문자명</th>
          <td><input type="text" name="buyr_name" class="w100" value="<?=$buyr_name?>" required></td>
        </tr>
        <!-- 주문자 E-mail(buyr_mail) -->
        <tr>
          <th>* E-mail</th>
          <td><input type="text" name="buyr_mail" class="w200" value="<?=$cfg[emailAdmin]?>" maxlength="30" /></td>
        </tr>
        <!-- 주문자 연락처1(buyr_tel1) -->
        <tr>
          <th>* 전화번호</th>
          <td><input type="text" name="buyr_tel1" class="w100" value="<?=$cfg[phoneComp]?>"/></td>
        </tr>
        <!-- 휴대폰번호(buyr_tel2) -->
        <tr>
          <th>* 휴대폰번호</th>
          <td><input type="text" name="buyr_tel2" class="w100" value="<?=$cfg[mobileAdmin]?>"/></td>
        </tr>
      </table>

      <!-- 결제 요청/처음으로 이미지 -->
      <div class="btnset" id="display_pay_button" style="display:block">
<!--        <input name="" type="submit" class="submit" value="결제요청" onclick="return jsf__pay(document.order_info);"/>-->
        <a href="#none" class="home" onclick="jsf__pay(document.order_info);" >결 제</a>
        <a href="javascript:window.history.back();" class="home">닫기</a>
      </div>
      <!-- Payplus Plug-in 설치 안내 -->
      <!--
		  <div id="display_setup_message" style="display:none">
			 <p class="txt">
			 결제를 계속 하시려면 상단의 노란색 표시줄을 클릭 하시거나 <a href="http://pay.kcp.co.kr/plugin_new/file/KCPUXWizard.exe"><span>[수동설치]</span></a>를 눌러
			 Payplus Plug-in을 설치하시기 바랍니다.
			 [수동설치]를 눌러 설치하신 경우 새로고침(F5)키를 눌러 진행하시기 바랍니다.
		  </p>
		  </div>
		  -->
    </div>
    <div class="footer">
      Copyright (c) KCP INC. All Rights reserved.
    </div>
	  <?
		  /* = -------------------------------------------------------------------------- = */
		  /* =   1. 주문 정보 입력 END                                                    = */
		  /* ============================================================================== */
	  ?>

	  <?
		  /* ============================================================================== */
		  /* =   2. 가맹점 필수 정보 설정                                                 = */
		  /* = -------------------------------------------------------------------------- = */
		  /* =   ※ 필수 - 결제에 반드시 필요한 정보입니다.                               = */
		  /* =   site_conf_inc.php 파일을 참고하셔서 수정하시기 바랍니다.                 = */
		  /* = -------------------------------------------------------------------------- = */
		  // 요청종류 : 승인(pay)/취소,매입(mod) 요청시 사용
	  ?>
    <input type="hidden" name="req_tx"          value="pay" />
    <input type="hidden" name="site_cd"         value="<?=$g_conf_site_cd	?>" />
    <input type="hidden" name="site_name"       value="<?=$g_conf_site_name ?>" />

	  <?
		  /*
	  할부옵션 : Payplus Plug-in에서 카드결제시 최대로 표시할 할부개월 수를 설정합니다.(0 ~ 18 까지 설정 가능)
	  ※ 주의  - 할부 선택은 결제금액이 50,000원 이상일 경우에만 가능, 50000원 미만의 금액은 일시불로만 표기됩니다
	  예) value 값을 "5" 로 설정했을 경우 => 카드결제시 결제창에 일시불부터 5개월까지 선택가능
	  */
	  ?>
    <input type="hidden" name="quotaopt"        value="12"/>

    <!-- 필수 항목 : 결제 금액/화폐단위 -->
    <input type="hidden" name="currency"        value="WON"/>
	  <?
		  /* = -------------------------------------------------------------------------- = */
		  /* =   2. 가맹점 필수 정보 설정 END                                             = */
		  /* ============================================================================== */
	  ?>

	  <?
		  /* ============================================================================== */
		  /* =   3. Payplus Plugin 필수 정보(변경 불가)                                   = */
		  /* = -------------------------------------------------------------------------- = */
		  /* =   결제에 필요한 주문 정보를 입력 및 설정합니다.                            = */
		  /* = -------------------------------------------------------------------------- = */
	  ?>
    <!-- PLUGIN 설정 정보입니다(변경 불가) -->
    <input type="hidden" name="module_type"     value="<?=$module_type ?>"/>
    <!--
	   ※ 필 수
	   필수 항목 : Payplus Plugin에서 값을 설정하는 부분으로 반드시 포함되어야 합니다
	   값을 설정하지 마십시오
	-->
    <input type="hidden" name="res_cd"          value=""/>
    <input type="hidden" name="res_msg"         value=""/>
    <input type="hidden" name="tno"             value=""/>
    <input type="hidden" name="trace_no"        value=""/>
    <input type="hidden" name="enc_info"        value=""/>
    <input type="hidden" name="enc_data"        value=""/>
    <input type="hidden" name="ret_pay_method"  value=""/>
    <input type="hidden" name="tran_cd"         value=""/>
    <input type="hidden" name="bank_name"       value=""/>
    <input type="hidden" name="bank_issu"       value=""/>
    <input type="hidden" name="use_pay_method"  value=""/>

    <!--  현금영수증 관련 정보 : Payplus Plugin 에서 설정하는 정보입니다 -->
    <input type="hidden" name="cash_tsdtime"    value=""/>
    <input type="hidden" name="cash_yn"         value=""/>
    <input type="hidden" name="cash_authno"     value=""/>
    <input type="hidden" name="cash_tr_code"    value=""/>
    <input type="hidden" name="cash_id_info"    value=""/>

    <!-- 2012년 8월 18일 전자상거래법 개정 관련 설정 부분 -->
    <!-- 제공 기간 설정 0:일회성 1:기간설정(ex 1:2012010120120131)  -->
    <input type="hidden" name="good_expr" value="0">

    <!-- 가맹점에서 관리하는 고객 아이디 설정을 해야 합니다.(필수 설정) -->
    <input type="hidden" name="shop_user_id"    value=""/>
    <!-- 복지포인트 결제시 가맹점에 할당되어진 코드 값을 입력해야합니다.(필수 설정) -->
    <input type="hidden" name="pt_memcorp_cd"   value=""/>




    <input type="hidden" name="account_type"   value="<?=$_POST[account_type]?>"/>

    <input type="hidden" name="account_point"   value="<?=$_POST[account_point]?>"/>


    <input type="hidden" name="storage_account_kind"   value="<?=$_POST[storage_account_kind]?>"/>
    <input type="hidden" name="storage_month"   value="<?=$_POST[storage_month]?>"/>
    <input type="hidden" name="storage_size"   value="<?=$_POST[storage_size]?>"/>

	  <? /* = -------------------------------------------------------------------------- = */
		  /* =   3. Payplus Plugin 필수 정보 END                                          = */
		  /* ============================================================================== */
	  ?>

	  <?
		  /* ============================================================================== */
		  /* =   4. 옵션 정보                                                             = */
		  /* = -------------------------------------------------------------------------- = */
		  /* =   ※ 옵션 - 결제에 필요한 추가 옵션 정보를 입력 및 설정합니다.             = */
		  /* = -------------------------------------------------------------------------- = */

		  /* 사용카드 설정 여부 파라미터 입니다.(통합결제창 노출 유무)
	   <input type="hidden" name="used_card_YN"        value="Y"/> */
		  /* 사용카드 설정 파라미터 입니다. (해당 카드만 결제창에 보이게 설정하는 파라미터입니다. used_card_YN 값이 Y일때 적용됩니다.
	   /<input type="hidden" name="used_card"        value="CCBC:CCKM:CCSS"/> */

		  /* 해외카드 구분하는 파라미터 입니다.(해외비자, 해외마스터, 해외JCB로 구분하여 표시)
	   <input type="hidden" name="used_card_CCXX"        value="Y"/> */

		  /* 신용카드 결제시 OK캐쉬백 적립 여부를 묻는 창을 설정하는 파라미터 입니다
	   포인트 가맹점의 경우에만 창이 보여집니다
	   <input type="hidden" name="save_ocb"        value="Y"/> */

		  /* 고정 할부 개월 수 선택
	   value값을 "7" 로 설정했을 경우 => 카드결제시 결제창에 할부 7개월만 선택가능
	   <input type="hidden" name="fix_inst"        value="07"/> */

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

		  /*  가상계좌 은행 선택 파라미터
	   ※ 해당 은행을 결제창에서 보이게 합니다.(은행코드는 매뉴얼을 참조)
	   <input type="hidden" name="wish_vbank_list" value="05:03:04:07:11:23:26:32:34:81:71"/> */

		  /*  가상계좌 입금 기한 설정하는 파라미터 - 발급일 + 3일
	   <input type="hidden" name="vcnt_expire_term" value="3"/> */

		  /*  가상계좌 입금 시간 설정하는 파라미터
	   HHMMSS형식으로 입력하시기 바랍니다
	   설정을 안하시는경우 기본적으로 23시59분59초가 세팅이 됩니다
	   <input type="hidden" name="vcnt_expire_term_time" value="120000"/> */

		  /* 포인트 결제시 복합 결제(신용카드+포인트) 여부를 결정할 수 있습니다.- N 일경우 복합결제 사용안함
	   <input type="hidden" name="complex_pnt_yn" value="N"/>    */

		  /* 현금영수증 등록 창을 출력 여부를 설정하는 파라미터 입니다
	   ※ Y : 현금영수증 등록 창 출력
	   ※ N : 현금영수증 등록 창 출력 안함
	   ※ 주의 : 현금영수증 사용 시 KCP 상점관리자 페이지에서 현금영수증 사용 동의를 하셔야 합니다
	   <input type="hidden" name="disp_tax_yn"     value="Y"/> */

		  /* 결제창에 가맹점 사이트의 로고를 플러그인 좌측 상단에 출력하는 파라미터 입니다
	   업체의 로고가 있는 URL을 정확히 입력하셔야 하며, 최대 150 X 50  미만 크기 지원

	   ※ 주의 : 로고 용량이 150 X 50 이상일 경우 site_name 값이 표시됩니다.
	   <input type="hidden" name="site_logo"       value="" /> */

		  /* 결제창 영문 표시 파라미터 입니다. 영문을 기본으로 사용하시려면 Y로 세팅하시기 바랍니다
	   2010-06월 현재 신용카드와 가상계좌만 지원됩니다
	   <input type='hidden' name='eng_flag'      value='Y'> */

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

		  /* skin_indx 값은 스킨을 변경할 수 있는 파라미터이며 총 7가지가 지원됩니다.
	   변경을 원하시면 1부터 7까지 값을 넣어주시기 바랍니다.

	   <input type='hidden' name='skin_indx'      value='1'> */

		  /* 상품코드 설정 파라미터 입니다.(상품권을 따로 구분하여 처리할 수 있는 옵션기능입니다.)
	   <input type='hidden' name='good_cd'      value=''> */

		  /* = -------------------------------------------------------------------------- = */
		  /* =   4. 옵션 정보 END                                                         = */
		  /* ============================================================================== */
	  ?>

  </form>
</div>
</body>
</html>
