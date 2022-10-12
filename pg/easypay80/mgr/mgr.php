<?
    /* -------------------------------------------------------------------------- */
    /* ::: IP ���� ����                                                           */
    /* -------------------------------------------------------------------------- */
    $req_ip = $_SERVER['REMOTE_ADDR']; // [�ʼ�]��û�� IP
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

        /*  ���������� Ȯ�� */
        if( !frm_mgr.EP_mall_id.value ) {
            alert("������ ���̵� �Է��ϼ���!!");
            frm_mgr.EP_mall_id.focus();
            return;
        }
        /*  �������� Ȯ�� */
        if( !frm_mgr.org_cno.value ) {
            alert("PG�ŷ���ȣ�� �Է��ϼ���!!");
            frm_mgr.org_cno.focus();
            return;
        }

        if( !frm_mgr.req_id.value ) {
            alert("��û��ID�� �Է��ϼ���!!");
            frm_mgr.req_id.focus();
            return;
        }

        if( frm_mgr.mgr_txtype.value == "60" || frm_mgr.mgr_txtype.value == "62") {

            if(frm_mgr.mgr_subtype.value == "RF01"){
                alert("ȯ�ҿ�û�� �Է��ϼ���!!");
                frm_mgr.mgr_subtype.focus();
                return;
            }

            if(!frm_mgr.mgr_amt.value){
                alert("ȯ�ұݾ��� �Է��ϼ���!!");
                frm_mgr.mgr_amt.focus();
                return;
            }

            if(!frm_mgr.mgr_bank_cd.value){
                alert("ȯ�������ڵ带 �Է��ϼ���!!");
                frm_mgr.mgr_bank_cd.focus();
                return;
            }

            if(!frm_mgr.mgr_account.value){
                alert("ȯ�Ұ��¹�ȣ�� �Է��ϼ���!!");
                frm_mgr.mgr_account.focus();
                return;
            }

            if(!frm_mgr.mgr_depositor.value){
                alert("ȯ�ҿ����ָ� �Է��ϼ���!!");
                frm_mgr.mgr_depositor.focus();
                return;
            }
        }

        if( frm_mgr.mgr_txtype.value == "31" || frm_mgr.mgr_txtype.value == "32") {

            if(!frm_mgr.mgr_amt.value){
                alert("��ұݾ��� �Է��ϼ���!!");
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

<input type="hidden" name="EP_tr_cd"  value="00201000">           <!-- [�ʼ�]�ŷ�����(�����Ұ�) -->
<input type="hidden" name="req_ip"    value="<?=$req_ip?>">       <!-- [�ʼ�]��û�� IP -->
<input type="hidden" name="req_id"    value="">                   <!-- [�ɼ�]��û�� ID -->
<input type="hidden" name="mgr_telno" value="">                   <!-- [�ɼ�]ȯ�Ұ��� ����ó -->

<table border="0" width="910" cellpadding="10" cellspacing="0">
<tr>
    <td>
    <!-- title start -->
    <table border="0" width="900" cellpadding="0" cellspacing="0">
    <tr>
        <td height="30" bgcolor="#FFFFFF" align="left">&nbsp;<img src="../img/arow3.gif" border="0" align="absmiddle">&nbsp;�Ϲ� > <b>����</b></td>
    </tr>
    <tr>
        <td height="2" bgcolor="#2D4677"></td>
    </tr>
    </table>
    <!-- title end -->

    <!-- mallinfo start -->
    <table border="0" width="900" cellpadding="0" cellspacing="0">
    <tr>
        <td height="30" bgcolor="#FFFFFF">&nbsp;<img src="../img/arow2.gif" border="0" align="absmiddle">&nbsp;<b>����������</b>(*�ʼ�)</td>
    </tr>
    </table>

    <table border="0" width="900" cellpadding="0" cellspacing="1" bgcolor="#DCDCDC">
    <tr height="25">
        <td bgcolor="#EDEDED" width="150">&nbsp; *������ID</td>
        <td bgcolor="#FFFFFF" width="750" colspan="3">&nbsp;<input type="text" id="EP_mall_id" name="EP_mall_id" value="T5102001" size="50" maxlength="8" class="input_F"></td>
    </tr>
    </table>
    <!-- mallinfo end -->

    <!-- mgr start -->
    <table border="0" width="900" cellpadding="0" cellspacing="0">
    <tr>
        <td height="30" bgcolor="#FFFFFF">&nbsp;<img src="../img/arow2.gif" border="0" align="absmiddle">&nbsp;<b>��������</b>(*�ʼ�)</td>
    </tr>
    </table>
    <table border="0" width="900" cellpadding="0" cellspacing="1" bgcolor="#DCDCDC">
    <tr height="25">
        <td bgcolor="#EDEDED" width="150">&nbsp;*�ŷ�����</td>
        <td bgcolor="#FFFFFF" width="300" >&nbsp;
            <select name="mgr_txtype" class="input_F">
                <option value="20" >���Կ�û</option>
                <option value="31" >�κи������</option>
                <option value="32" >�κн������</option>
                <option value="33" >������ü�κ����</option>
                <option value="40" selected>������</option>
                <option value="60" >ȯ��</option>
                <option value="62" >�κ�ȯ��</option>
            </select>
        </td>
        <td bgcolor="#EDEDED" width="150">&nbsp;*���漼�α���</td>
        <td bgcolor="#FFFFFF" width="300" >&nbsp;
            <select name="mgr_subtype" class="input_A">
                <option value="RF01" >�Ϲ�ȯ��(60)_ȯ�ҿ�û</option>
            </select>
        </td>
    </tr>
    <tr height="25">
        <!-- [�ʼ�] PG�ŷ���ȣ -->
        <td bgcolor="#EDEDED" width="150">&nbsp;*PG�ŷ���ȣ</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" name="org_cno" size="50" class="input_F"></td>
        <!-- [�ɼ�] ��û��ID -->
        <td bgcolor="#EDEDED" width="150">&nbsp;��û��ID</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" name="req_id" size="50" class="input_A"></td>
    </tr>
    <tr height="25">
        <!-- [�ɼ�] ������� -->
        <td bgcolor="#EDEDED" width="150">&nbsp;�������</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" name="mgr_msg" size="50" class="input_A"></td>
        <!-- [�ɼ�] �ݾ� -->
        <td bgcolor="#EDEDED" width="150">&nbsp;�ݾ�</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" name="mgr_amt" size="50" class="input_A"></td>
    </tr>
    <tr height="25">
        <!-- [�ɼ�] ������� -->
        <td bgcolor="#EDEDED" width="150">&nbsp;�����ڵ�</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" name="mgr_bank_cd" size="50" class="input_A"></td>
        <!-- [�ɼ�] ������� -->
        <td bgcolor="#EDEDED" width="150">&nbsp;���¹�ȣ</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" name="mgr_account" size="50" class="input_A"></td>
    </tr>
    <tr height="25">
        <!-- [�ɼ�] ������� -->
        <td bgcolor="#EDEDED" width="150">&nbsp;�����ָ�</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" name="mgr_depositor" size="50" class="input_A"></td>
        <!-- [�ɼ�] ������� -->
        <td bgcolor="#EDEDED" width="150">&nbsp;����ó</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" name="mgr_telno" size="50" class="input_A"></td>
    </tr>
    </table>
    <!-- mgr Data END -->

    <table border="0" width="900" cellpadding="0" cellspacing="0">
    <tr>       <td height="30" align="center" bgcolor="#FFFFFF"><input type="button" value="�� ��" class="input_D" style="cursor:hand;" onclick="javascript:f_submit();"></td>
    </tr>
    </table>
    </td>
</tr>
</table>
</form>
</body>
</html>