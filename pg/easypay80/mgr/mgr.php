<?
    /* -------------------------------------------------------------------------- */
    /* ::: IP 정보 설정                                                           */
    /* -------------------------------------------------------------------------- */
    $req_ip = $_SERVER['REMOTE_ADDR']; // [필수]요청자 IP
?>

<html>
<head>  
<title>KICC EASYPAY 8.0 SAMPLE</title>
<meta name="robots" content="noindex, nofollow"> 
<meta http-equiv="content-type" content="text/html; charset=euc-kr">
<link href="../css/style.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../js/default.js" type="text/javascript"></script>
<script type="text/javascript">

    function f_submit() {

        var frm_mgr = document.frm_mgr;

        var bRetVal = false;

        /*  가맹점정보 확인 */
        if( !frm_mgr.EP_mall_id.value ) {
            alert("가맹점 아이디를 입력하세요!!");
            frm_mgr.EP_mall_id.focus();
            return;
        }
        /*  변경정보 확인 */
        if( !frm_mgr.org_cno.value ) {
            alert("PG거래번호를 입력하세요!!");
            frm_mgr.org_cno.focus();
            return;
        }

        if( !frm_mgr.req_id.value ) {
            alert("요청자ID를 입력하세요!!");
            frm_mgr.req_id.focus();
            return;
        }

        if( frm_mgr.mgr_txtype.value == "60" || frm_mgr.mgr_txtype.value == "62") {

            if(frm_mgr.mgr_subtype.value == "RF01"){
                alert("환불요청을 입력하세요!!");
                frm_mgr.mgr_subtype.focus();
                return;
            }

            if(!frm_mgr.mgr_amt.value){
                alert("환불금액을 입력하세요!!");
                frm_mgr.mgr_amt.focus();
                return;
            }

            if(!frm_mgr.mgr_bank_cd.value){
                alert("환불은행코드를 입력하세요!!");
                frm_mgr.mgr_bank_cd.focus();
                return;
            }

            if(!frm_mgr.mgr_account.value){
                alert("환불계좌번호를 입력하세요!!");
                frm_mgr.mgr_account.focus();
                return;
            }

            if(!frm_mgr.mgr_depositor.value){
                alert("환불예금주명를 입력하세요!!");
                frm_mgr.mgr_depositor.focus();
                return;
            }
        }

        if( frm_mgr.mgr_txtype.value == "31" || frm_mgr.mgr_txtype.value == "32") {

            if(!frm_mgr.mgr_amt.value){
                alert("취소금액을 입력하세요!!");
                frm_mgr.mgr_amt.focus();
                return;
            }

        }

        frm_mgr.submit();
    }
</script>
</head>
<body>
<form name="frm_mgr" method="post" action="../easypay_request.php">

<input type="hidden" name="EP_tr_cd"  value="00201000">           <!-- [필수]거래구분(수정불가) -->
<input type="hidden" name="req_ip"    value="<?=$req_ip?>">       <!-- [필수]요청자 IP -->
<input type="hidden" name="req_id"    value="">                   <!-- [옵션]요청자 ID -->
<input type="hidden" name="mgr_telno" value="">                   <!-- [옵션]환불계좌 연락처 -->

<table border="0" width="910" cellpadding="10" cellspacing="0">
<tr>
    <td>
    <!-- title start -->
    <table border="0" width="900" cellpadding="0" cellspacing="0">
    <tr>
        <td height="30" bgcolor="#FFFFFF" align="left">&nbsp;<img src="../img/arow3.gif" border="0" align="absmiddle">&nbsp;일반 > <b>변경</b></td>
    </tr>
    <tr>
        <td height="2" bgcolor="#2D4677"></td>
    </tr>
    </table>
    <!-- title end -->

    <!-- mallinfo start -->
    <table border="0" width="900" cellpadding="0" cellspacing="0">
    <tr>
        <td height="30" bgcolor="#FFFFFF">&nbsp;<img src="../img/arow2.gif" border="0" align="absmiddle">&nbsp;<b>가맹점정보</b>(*필수)</td>
    </tr>
    </table>

    <table border="0" width="900" cellpadding="0" cellspacing="1" bgcolor="#DCDCDC">
    <tr height="25">
        <td bgcolor="#EDEDED" width="150">&nbsp; *가맹점ID</td>
        <td bgcolor="#FFFFFF" width="750" colspan="3">&nbsp;<input type="text" id="EP_mall_id" name="EP_mall_id" value="T5102001" size="50" maxlength="8" class="input_F"></td>
    </tr>
    </table>
    <!-- mallinfo end -->

    <!-- mgr start -->
    <table border="0" width="900" cellpadding="0" cellspacing="0">
    <tr>
        <td height="30" bgcolor="#FFFFFF">&nbsp;<img src="../img/arow2.gif" border="0" align="absmiddle">&nbsp;<b>변경정보</b>(*필수)</td>
    </tr>
    </table>
    <table border="0" width="900" cellpadding="0" cellspacing="1" bgcolor="#DCDCDC">
    <tr height="25">
        <td bgcolor="#EDEDED" width="150">&nbsp;*거래구분</td>
        <td bgcolor="#FFFFFF" width="300" >&nbsp;
            <select name="mgr_txtype" class="input_F">
                <option value="20" >매입요청</option>
                <option value="31" >부분매입취소</option>
                <option value="32" >부분승인취소</option>
                <option value="33" >계좌이체부분취소</option>
                <option value="40" selected>즉시취소</option>
                <option value="60" >환불</option>
                <option value="62" >부분환불</option>
            </select>
        </td>
        <td bgcolor="#EDEDED" width="150">&nbsp;*변경세부구분</td>
        <td bgcolor="#FFFFFF" width="300" >&nbsp;
            <select name="mgr_subtype" class="input_A">
                <option value="RF01" >일반환불(60)_환불요청</option>
            </select>
        </td>
    </tr>
    <tr height="25">
        <!-- [필수] PG거래번호 -->
        <td bgcolor="#EDEDED" width="150">&nbsp;*PG거래번호</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" name="org_cno" size="50" class="input_F"></td>
        <!-- [옵션] 요청자ID -->
        <td bgcolor="#EDEDED" width="150">&nbsp;요청자ID</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" name="req_id" size="50" class="input_A"></td>
    </tr>
    <tr height="25">
        <!-- [옵션] 변경사유 -->
        <td bgcolor="#EDEDED" width="150">&nbsp;변경사유</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" name="mgr_msg" size="50" class="input_A"></td>
        <!-- [옵션] 금액 -->
        <td bgcolor="#EDEDED" width="150">&nbsp;금액</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" name="mgr_amt" size="50" class="input_A"></td>
    </tr>
    <tr height="25">
        <!-- [옵션] 변경사유 -->
        <td bgcolor="#EDEDED" width="150">&nbsp;은행코드</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" name="mgr_bank_cd" size="50" class="input_A"></td>
        <!-- [옵션] 변경사유 -->
        <td bgcolor="#EDEDED" width="150">&nbsp;계좌번호</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" name="mgr_account" size="50" class="input_A"></td>
    </tr>
    <tr height="25">
        <!-- [옵션] 변경사유 -->
        <td bgcolor="#EDEDED" width="150">&nbsp;예금주명</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" name="mgr_depositor" size="50" class="input_A"></td>
        <!-- [옵션] 변경사유 -->
        <td bgcolor="#EDEDED" width="150">&nbsp;연락처</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" name="mgr_telno" size="50" class="input_A"></td>
    </tr>
    </table>
    <!-- mgr Data END -->

    <table border="0" width="900" cellpadding="0" cellspacing="0">
    <tr>       <td height="30" align="center" bgcolor="#FFFFFF"><input type="button" value="변 경" class="input_D" style="cursor:hand;" onclick="javascript:f_submit();"></td>
    </tr>
    </table>
    </td>
</tr>
</table>
</form>
</body>
</html>