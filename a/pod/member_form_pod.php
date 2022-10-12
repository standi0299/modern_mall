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

## 회원그룹 추출
$r_grp = getMemberGrp();
$r_bid = getBusiness();

### 영업사원정보 추출
$r_manager = $m_pod->getSalesList($cid);

if (!$data[state]) $checked[state][0] = "checked";
if (!$data[sex]) $checked[sex][m] = "checked";
if (!$data[apply_email]) $checked[apply_email][1] = "checked";
if (!$data[apply_sms]) $checked[apply_sms][1] = "checked";

### 결제방식:credit_member (선결제,후결제)
### 거래상태:rest_flag (승인,정지)
if (!$data[credit_member]) $checked[credit_member][0] = "checked";
if (!$data[rest_flag]) $checked[rest_flag][0] = "checked";
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

   <h1 class="page-header"><?=_("회원등록")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("회원등록")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" onsubmit="return submitContents(this);">
                  <input type="hidden" name="mode" value="member_join_pod">
                  <input type="hidden" name="state" value="0">

                  <div class="form-group">
                     <!--
                     <label class="col-md-2 control-label"><?=_("그룹")?></label>
                     <div class="col-md-4 form-inline">
                        <select name="grp" class="form-control">
                        <option value="">그룹선택
                        <?foreach($r_grp as $k=>$v){?>
                        <option value="<?=$k?>" <?=$selected[grp][$k]?>><?=$v?></option>
                        <?}?>
                        </select>
                     </div>
                     -->

                     <!--
                     <label class="col-md-2 control-label"><?=_("기업그룹")?></label>
                     <div class="col-md-4 form-inline">
                        <select name="bid" class="form-control">
                        <option value=""><?=_("기업그룹선택")?>
                        <?foreach($r_bid as $k=>$v){?>
                        <option value="<?=$k?>" <?=$selected[bid][$k]?>><?=$v?></option>
                        <?}?>
                        </select>
                     </div>
                     -->
                  </div>

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
                        <input type="text" class="form-control" name="mid" pt="_pt_id" required id="w_mid"> <span id="ret_mid"></span>
                        <input type="hidden" name="check_mid" required id="check_mid" msg='<?=_("아이디를 확인해주세요.")?>'>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("비밀번호")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="password" class="form-control" name="password" pt="_pt_pw" onchange="chk_password(this)" required>
                        <div class="desc" style="color:#28a5f9"><span id="vPass"><?=_("비밀번호 변경시에만 입력해주세요")?></span></div>
                     </div>
                     
                     <label class="col-md-2 control-label"><?=_("비밀번호확인")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="password" class="form-control" name="password2" pt="_pt_pw" onchange="chk_password(this)" samewith="password" required>
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
                  
                  <?//if($used[res]) {?>
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("주민등록번호")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="resno[]" maxlength="6" size="6" value=<?=$data[resno][0]?>> -
                        <input type="text" class="form-control" name="resno[]" maxlength="7" size="7" value=<?=$data[resno][1]?>>
                     </div>
                  </div> 
                  <?//}?>

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
                     <label class="col-md-2 control-label"><?=_("관리자메모")?></label>
                     <div class="col-md-3">
                        <textarea name="memo" class="form-control" rows="5"><?=$data[memo]?></textarea>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
                        <button type="submit" class="btn btn-sm btn-success"><?=_("등록")?></button>
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
</script>

<script>
function chk_password(val){ 
   var ret = $('vPass');

   if (!fm.password.value){
      alert('<?=_("비밀번호를 입력해주세요")?>');
      fm.password.focus();
      return false;
   } else if (!_pattern(fm.password)){
      alert('<?=_("비밀번호는 영소문자,숫자,-,_ 를 사용하여 6~20자로 입력해주세요.")?>');
      fm.password.focus();
      fm.password.value = "";
      return false;
   }

   if (!fm.password2.value){
      alert('<?=_("비밀번호 확인을 위해 한번 더 입력해주세요")?>');
      fm.password2.focus();
      return false;
   }
   
   chk_pw = (fm.password.value != fm.password2.value) ? '<?=_("비밀번호 불일치")?>' : '<?=_("비밀번호 일치")?>';
   $j("#vPass").html(chk_pw);

   reset_pw = (fm.password.value != fm.password2.value) ? "" : "chk";

   //비밀번호와 비밀번호확인이 다르면 비밀번호확인 텍스트박스 값을 지워줌 / 14.04.25 / kjm
   if (reset_pw == "") {
      fm.password2.value = '';
      alert('<?=_("비밀번호가 일치하지 않습니다.")?>' + " \n" + '<?=_("다시 입력해주세요.")?>');
   }
}
</script>

<script>
$j(function (){
    $j('input[name=mid]').css('ime-mode','disabled');
    $j('input[name=phone[]]').css('ime-mode','disabled').keypress(function(event){
        if (event.which && (event.which < 48 || event.which > 57)){
            event.preventDefault();
        }
    });
    $j('input[name=mobile[]]').css('ime-mode','disabled').keypress(function(event){
        if (event.which && (event.which < 48 || event.which > 57)){
            event.preventDefault();
        }
    });
    $j('input[name=email[]]').css('ime-mode','disabled');
    $j("#w_mid").blur(function(){
        $j("#check_mid").val("");
        if ($j("#w_mid").val()){
            $j.post("indb.php",{mode:"check_mid",mid:$j("#w_mid").val()},function(data){
                switch (data){
                    case "duplicate":
                        $j("#ret_mid").html('<?=_("이미 등록된 아이디입니다")?>');
                        $j("#w_mid").val("");
                        break;
                    case "good": 
                        $j("#ret_mid").html('<?=_("사용이 가능한 아이디입니다")?>');
                        $j("#check_mid").val("ok");
                        break;
                    case "unable":  $j("#ret_mid").html('<?=_("사용이 불가능한 아이디입니다")?>'); break;
                    case "out":  $j("#ret_mid").html('<?=_("탈퇴한 아이디입니다")?>'); break;
                }
            });
        }
    });
});

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