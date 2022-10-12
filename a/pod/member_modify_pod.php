<?
/*
* @date : 20181030
* @author : kdk
* @brief : POD용 (알래스카) 회원가입항목설정.
* @request : 기존 필드 사용 (결제방식:credit_member, 거래상태:rest_flag), 세금계산서담당자 (etc1,etc2,etc3,etc4,etc5)
* @desc : fieldset 사용 (exm_config), manager_no 정산담당자번호INT(11) => (영업담당자)VARCHAR(50)
* @todo :
*/

include "../_header.php";
include "../_left_menu.php";
include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

$m_pod = new M_pod();

## 회원가입항목
$cfg[fieldset] = getCfg("fieldset");
$cfg[fieldset] = unserialize($cfg[fieldset]);

if (is_array($cfg[fieldset])) {
    foreach ($cfg[fieldset] as $k=>$v) {
        if ($v[req] == 1) $required[$k] = "required";
        if ($v['use'] == 1) $used[$k] = "used";
    }
}

### 선발행입금액 정보 조회.
$deposit_money = $m_pod->getDepositMoney($cid, $_GET[mid]);

### 미수금 정보 조회.
$remain_money = $m_pod->getRemainMoney($cid, $_GET[mid]);
    
## 회원그룹 추출
$r_grp = getMemberGrp();

### 영업사원정보 추출
$r_manager = $m_pod->getSalesList($cid);

### 회원정보 추출
$tableName = "exm_member";
$selectArr = "*";
$whereArr = array("cid" => "$cid", "mid" => "$_GET[mid]");
$data = SelectInfoTable($tableName, $selectArr, $whereArr);

$data[email] = explode("@",$data[email]);
$data[phone] = explode("-",$data[phone]);
$data[mobile] = explode("-",$data[mobile]);

if (!$data[state]) $checked[state][0] = "checked";
//if (!$data[sex]) $checked[sex][m] = "checked";
if (!$data[apply_email]) $checked[apply_email][0] = "checked";
if (!$data[apply_sms]) $checked[apply_sms][0] = "checked";

$selected[grp][$data[grpno]] = "selected";
$selected[bid][$data[bid]] = "selected";
$selected[calendar][$data[calendar]] = "selected";

if ($data[manager_no]) {
    $data[manager_no] = explode(",",$data[manager_no]);

    foreach ($data[manager_no] as $key => $val) {
        $checked[manager_no][$val] = "checked";
    }    
}

if ($data[etc1]) $data[etc1] = explode(",",$data[etc1]);
if ($data[etc2]) $data[etc2] = explode(",",$data[etc2]);
if ($data[etc3]) $data[etc3] = explode(",",$data[etc3]);
if ($data[etc4]) $data[etc4] = explode(",",$data[etc4]);
if ($data[etc5]) $data[etc5] = explode(",",$data[etc5]);

//list($tmp[0], $tmp[1]) = array(substr($data[birth], 0, 2), sprintf("%02d", substr($data[birth], 2)));
//$data[birth] = $tmp;
$data[resno] = explode("-", $data[resno]);

$data[cust_no] = explode("-", $data[cust_no]);
$data[cust_ceo_phone] = explode("-", $data[cust_ceo_phone]);
$data[cust_phone] = explode("-", $data[cust_phone]);
$data[cust_fax] = explode("-", $data[cust_fax]);

//$checked[married][$data[married]] = "checked";
$selected[cust_tax_type][$data[cust_tax_type]] = "selected";
$selected[cust_ceo_phone][$data[cust_ceo_phone][0]] = "selected";
$selected[cust_phone][$data[cust_phone][0]] = "selected";
$selected[cust_fax][$data[cust_fax][0]] = "selected";

### 결제방식:credit_member (선결제,후결제)
### 거래상태:rest_flag (승인,정지)
$checked[credit_member][$data[credit_member]] = "checked";
$checked[rest_flag][$data[rest_flag]] = "checked";
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->
<div id="content" class="content">
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active">
         <?=_("회원수정")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("회원수정")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("회원수정")?></h4>
            </div>
            
            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" onsubmit="return submitContents(this);">
                  <input type="hidden" name="mode" value="member_modify_pod">
                  <input type="hidden" name="mid" value="<?=$_GET[mid]?>">
                  <input type="hidden" name="state" value="0">

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("그룹")?></label>
                     <div class="col-md-4 form-inline">
                        <select name="grp" class="form-control">
                        <option value=""><?=_("그룹선택")?></option>
                        <?foreach($r_grp as $k=>$v){?>
                        <option value="<?=$k?>" <?=$selected[grp][$k]?>><?=$v?></option>
                        <?}?>
                        </select>
                     </div>

                     <!--<label class="col-md-2 control-label"><?=_("분류")?></label>
                     <div class="col-md-4 form-inline">
                        <?foreach($r_member_state as $k=>$v){?>
                        <input type="radio" class="form-control" name="state" value="<?=$k?>" <?=$checked[state][$k]?>><?=$v?>
                        <?}?>
                     </div>-->
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("아이디")?></label>
                     <div class="col-md-10 form-inline">
                        <b><?=$_GET[mid]?></b>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("비밀번호")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="password" class="form-control" name="password" pt="_pt_pw" onchange="chk_password(this)">
                        <div class="desc" style="color:#28a5f9"><span id="vPass"><?=_("비밀번호 변경시에만 입력해주세요")?></span></div>
                     </div>
                     
                     <label class="col-md-2 control-label"><?=_("비밀번호확인")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="password" class="form-control" name="password2" pt="_pt_pw" onchange="chk_password(this)" samewith="password">
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("이름")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="text" class="form-control" name="name" value="<?=$data[name]?>" required>
                     </div>
                  </div>

                  <?//if($used[email]) {?>
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("이메일")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="text" class="form-control" name="email[]" value="<?=$data[email][0]?>"> @
                        <input type="text" class="form-control" name="email[]" value="<?=$data[email][1]?>">
                     </div>
                     
                     <label class="col-md-2 control-label"><?=_("이메일수신동의")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="radio" class="form-control" name="apply_email" value="1" <?=$checked[apply_email][1]?>><?=_("수신")?>
                        <input type="radio" class="form-control" name="apply_email" value="0" <?=$checked[apply_email][0]?>><?=_("수신안함")?>
                     </div>
                  </div>
                  <?//}?>
                  
                  <?//if($used[mobile]) {?>
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("핸드폰")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="text" class="form-control" name="mobile[]" maxlength="3" size="3" value=<?=$data[mobile][0]?>> -
                        <input type="text" class="form-control" name="mobile[]" maxlength="4" size="4" value=<?=$data[mobile][1]?>> -
                        <input type="text" class="form-control" name="mobile[]" maxlength="4" size="4" value=<?=$data[mobile][2]?>>
                     </div>
                     
                     <label class="col-md-2 control-label"><?=_("SMS수신동의")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="radio" class="form-control" name="apply_sms" value="1" <?=$checked[apply_sms][1]?>><?=_("수신")?>
                        <input type="radio" class="form-control" name="apply_sms" value="0" <?=$checked[apply_sms][0]?>><?=_("수신안함")?>
                     </div>                     
                  </div>
                  <?//}?>
                  
                  <?//if($used[address]) {?>
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("주소")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="zipcode" id="zipcode" value="<?=$data[zipcode]?>" readonly>
                        <i class="fa fa-search" style="cursor: pointer;" onclick="javascript:popupZipcode<?=$language_locale?>()"></i><br><br>
                        <input type="text" class="form-control" name="address" value="<?=$data[address]?>" size="50">
                        <input type="text" class="form-control" name="address_sub" value="<?=$data[address_sub]?>" size="50">
                     </div>
                  </div>
                  <?//}?>
                  
                  <?//if($used[phone]) {?>
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("전화번호")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="phone[]" maxlength="3" size="3" value=<?=$data[phone][0]?>> -
                        <input type="text" class="form-control" name="phone[]" maxlength="4" size="4" value=<?=$data[phone][1]?>> -
                        <input type="text" class="form-control" name="phone[]" maxlength="4" size="4" value=<?=$data[phone][2]?>>
                     </div>
                  </div>                  
                  <?//}?>
                  
                  <?if($data[grpno] == "1") {?>
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("주민등록번호")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="resno[]" maxlength="6" size="6" value=<?=$data[resno][0]?>> -
                        <input type="text" class="form-control" name="resno[]" maxlength="7" size="7" value=<?=$data[resno][1]?>>                         
                         
                        <!--<?if($data[resno]) {?>
                            <?=substr($data[resno],0,6)?> - <?=substr($data[resno],6,1)?>******
                        <?} else {?>
                            <span class="gray"><?=_("입력된 주민등록번호가 없습니다.")?></span>    
                        <?}?>-->
                     </div>
                  </div> 
                  <?}?>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("거래상태")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="radio" class="form-control" name="rest_flag" value="0" <?=$checked[rest_flag][0]?>><?=_("승인")?>
                        <input type="radio" class="form-control" name="rest_flag" value="1" <?=$checked[rest_flag][1]?>><?=_("정지")?>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("결제방식")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="radio" class="form-control" name="credit_member" value="0" <?=$checked[credit_member][0]?>><?=_("선결제")?>
                        <input type="radio" class="form-control" name="credit_member" value="1" <?=$checked[credit_member][1]?>><?=_("후결제")?>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("기본입금방법")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="text" class="form-control" name="cust_bank_name" value="<?=$data[cust_bank_name]?>">
                     </div>
                  </div>                                    

                  <?if($used[cust_name]) {?>
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("사업자명")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="text" class="form-control" name="cust_name" value="<?=$data[cust_name]?>">
                     </div>
                  </div>
                  <?}?>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("세금계산서담당자")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="etc1[]" size="5" placeholder='이름' value=<?=$data[etc1][0]?>>
                        <input type="text" class="form-control" name="etc1[]" size="20" placeholder='이메일' value=<?=$data[etc1][1]?>>
                        <input type="text" class="form-control" name="etc1[]" size="10" placeholder='전화번호' value=<?=$data[etc1][2]?>>
                        <input type="checkbox" name="etc1[]" value="1" <?if($data[etc1][3]){?>checked<?}?> class="form-control chk" onclick='chkbox();' /> <?=_("대표설정")?><br><br>
                        <input type="text" class="form-control" name="etc2[]" size="5" placeholder='이름' value=<?=$data[etc2][0]?>>
                        <input type="text" class="form-control" name="etc2[]" size="20" placeholder='이메일' value=<?=$data[etc2][1]?>>
                        <input type="text" class="form-control" name="etc2[]" size="10" placeholder='전화번호' value=<?=$data[etc2][2]?>>
                        <input type="checkbox" name="etc2[]" value="1" <?if($data[etc2][3]){?>checked<?}?> class="form-control chk" onclick='chkbox();' /> <?=_("대표설정")?><br><br>
                        <input type="text" class="form-control" name="etc3[]" size="5" placeholder='이름' value=<?=$data[etc3][0]?>>
                        <input type="text" class="form-control" name="etc3[]" size="20" placeholder='이메일' value=<?=$data[etc3][1]?>>
                        <input type="text" class="form-control" name="etc3[]" size="10" placeholder='전화번호' value=<?=$data[etc3][2]?>>
                        <input type="checkbox" name="etc3[]" value="1" <?if($data[etc3][3]){?>checked<?}?> class="form-control chk" onclick='chkbox();' /> <?=_("대표설정")?><br><br>
                        <input type="text" class="form-control" name="etc4[]" size="5" placeholder='이름' value=<?=$data[etc4][0]?>>
                        <input type="text" class="form-control" name="etc4[]" size="20" placeholder='이메일' value=<?=$data[etc4][1]?>>
                        <input type="text" class="form-control" name="etc4[]" size="10" placeholder='전화번호' value=<?=$data[etc4][2]?>>
                        <input type="checkbox" name="etc4[]" value="1" <?if($data[etc4][3]){?>checked<?}?> class="form-control chk" onclick='chkbox();' /> <?=_("대표설정")?><br><br>
                        <input type="text" class="form-control" name="etc5[]" size="5" placeholder='이름' value=<?=$data[etc5][0]?>>
                        <input type="text" class="form-control" name="etc5[]" size="20" placeholder='이메일' value=<?=$data[etc5][1]?>>
                        <input type="text" class="form-control" name="etc5[]" size="10" placeholder='전화번호' value=<?=$data[etc5][2]?>>
                        <input type="checkbox" name="etc5[]" value="1" <?if($data[etc5][3]){?>checked<?}?> class="form-control chk" onclick='chkbox();' /> <?=_("대표설정")?>
                     </div>
                  </div>

                  <?if($used[cust_type]) {?>
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("업태")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="text" class="form-control" name="cust_type" value="<?=$data[cust_type]?>">
                     </div>
                  </div>                  
                  <?}?>
                  
                  <?if($used[cust_class]) {?>
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("업종")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="text" class="form-control" name="cust_class" value="<?=$data[cust_class]?>">
                     </div>
                  </div>                  
                  <?}?>
                  
                  <?if($used[cust_tax_type]) {?>
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("사업자등록유형")?></label>
                     <div class="col-md-4 form-inline">
                        <select name="cust_tax_type" class="form-control">
                        <option value=""><?=_("선택")?>
                        <option value="1" <?=$selected[cust_tax_type]['1']?>><?=_("일반과세자")?>
                        <option value="2" <?=$selected[cust_tax_type]['2']?>><?=_("법인사업자")?>
                        </select>
                     </div>
                  </div> 
                  <?}?>
                  
                  <?if($used[cust_no]) {?>
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("사업자등록번호")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="cust_no[]" maxlength="3" size="3" value=<?=$data[cust_no][0]?>> -
                        <input type="text" class="form-control" name="cust_no[]" maxlength="2" size="2" value=<?=$data[cust_no][1]?>> -
                        <input type="text" class="form-control" name="cust_no[]" maxlength="5" size="5" value=<?=$data[cust_no][2]?>>
                     </div>
                  </div> 
                  <?}?>
                  
                  <?if($used[cust_ceo]) {?>
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("대표자명")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="text" class="form-control" name="cust_ceo" value="<?=$data[cust_ceo]?>">
                     </div>
                  </div>
                  <?}?>
                  
                  <?if($used[cust_ceo_phone]) {?>
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("대표자연락처")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="cust_ceo_phone[]" maxlength="3" size="3" value=<?=$data[cust_ceo_phone][0]?>> -
                        <input type="text" class="form-control" name="cust_ceo_phone[]" maxlength="4" size="4" value=<?=$data[cust_ceo_phone][1]?>> -
                        <input type="text" class="form-control" name="cust_ceo_phone[]" maxlength="4" size="4" value=<?=$data[cust_ceo_phone][2]?>>
                     </div>
                  </div>
                  <?}?>
                  
                  <?if($used[cust_address]) {?>
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("사업장 주소")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="cust_zipcode" id="cust_zipcode" value="<?=$data[cust_zipcode]?>" readonly>
                        <i class="fa fa-search" style="cursor: pointer;" onclick="javascript:popupZipcode<?=$language_locale?>('zipcode_return_cust')"></i><br><br>
                        <input type="text" class="form-control" name="cust_address" value="<?=$data[cust_address]?>" size="50">
                        <input type="text" class="form-control" name="cust_address_sub" value="<?=$data[cust_address_sub]?>" size="50">
                     </div>
                  </div>
                  <?}?>
                  
                  <?if($used[cust_address_en]) {?>
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("사업장 영문주소")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="text" class="form-control" name="cust_address_en" value="<?=$data[cust_address_en]?>">
                     </div>
                  </div>
                  <?}?>
                  
                  <?if($used[cust_phone]) {?>
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("사업장 전화번호")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="cust_phone[]" maxlength="3" size="3" value=<?=$data[cust_phone][0]?>> -
                        <input type="text" class="form-control" name="cust_phone[]" maxlength="4" size="4" value=<?=$data[cust_phone][1]?>> -
                        <input type="text" class="form-control" name="cust_phone[]" maxlength="4" size="4" value=<?=$data[cust_phone][2]?>>
                     </div>
                  </div>
                  <?}?>
                  
                  <?if($used[cust_fax]) {?>
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("사업장 팩스번호")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="cust_fax[]" maxlength="3" size="3" value=<?=$data[cust_fax][0]?>> -
                        <input type="text" class="form-control" name="cust_fax[]" maxlength="4" size="4" value=<?=$data[cust_fax][1]?>> -
                        <input type="text" class="form-control" name="cust_fax[]" maxlength="4" size="4" value=<?=$data[cust_fax][2]?>>
                     </div>
                  </div>
                  <?}?>
                  
                  <?//if($used[manager_no]) {?>
                  <div class="form-group">    
                     <label class="col-md-2 control-label"><?=_("영업담당자")?></label>
                     <div class="col-md-4 form-inline">
                        <?foreach($r_manager as $k=>$v){?>
                            <input type="checkbox" name="manager_no[]" value="<?=$v[mid]?>" <?=$checked[manager_no][$v[mid]]?> class="form-control" /> <?=$v[name]?>
                        <?}?>
                     </div>
                  </div>
                  <?//}?>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("미수금액")?></label>
                     <div class="col-md-10 form-inline">
                        <b><?=number_format($remain_money)._("원")?></b>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("선입금액")?></label>
                     <div class="col-md-10 form-inline">
                        <b><?=number_format($deposit_money)._("원")?></b>
                     </div>
                  </div>
                                    
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("가입일")?></label>
                     <div class="col-md-4 form-inline">
                        <?=$data[regdt]?>
                        <?
                           $m_member = new M_member();
						   $sns_data = $m_member->getLogSnsLogin($cid,$data[sns_id]);
						   if($sns_data) {
						   	echo ("<br><img src='../img/$sns_data[sns_type].png' height='20px' alt='$sns_data[sns_id]' />");
							echo ("&nbsp;&nbsp;id : $sns_data[sns_id] / name : $sns_data[sns_name] / email : $sns_data[sns_email] / nickname : $sns_data[sns_nickname]");
						   }
                        ?>
                     </div>
                     
                     <label class="col-md-2 control-label"><?=_("로그인 정보")?></label>
                     <div class="col-md-4 form-inline">
                        <?=$data[cntlogin]?><?=_("회")?> (<?=_("최종로그인시간")?> <?=$data[lastlogin]?>)
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("관리자메모")?></label>
                     <div class="col-md-3">
                        <textarea name="memo" class="form-control" rows="5"><?=$data[memo]?></textarea>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
                        <button type="submit" class="btn btn-sm btn-success" <?if($_GET[mode] =="member_modify_pod") { ?> onclick="option_chk()" <?}?> >
                        <? if($_GET[mode] == "member_join_pod") { ?>
                           <?=_("등록")?>
                        <?} else {?>
                           <?=_("수정")?>
                        <? } ?>
                        </button>
                        <button type="button" class="btn btn-sm btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>

<!--form 전송 취약점 개선 20160128 by kdk-->
<script src="../../js/webtoolkit.base64.js"></script>

<script type="text/javascript">
function submitContents(formObj) {
    try {
        formObj.memo.value = Base64.encode(formObj.memo.value);
        return form_chk(formObj);
    } catch(e) {return false;}
}

//세금계산서담당자 대표설정은 하나만 선택한다.
function chkbox() {
    if ($(".chk:checked").length > 1) {
        alert('<?=_("대표설정은 1개만 가능합니다.")?>');
        $(".chk").each(function() {
            $(this).prop("checked",false);
        });
    }
}
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>