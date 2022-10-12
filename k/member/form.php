<?
include "../_header.php";
include "../_left_menu.php";

## 회원그룹 추출
$r_grp = getMemberGrp();
$r_bid = getBusiness();
$r_manager = get_manager("y");

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
         <?=_("그룹관리")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("그룹관리")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("회원수정")?></h4>
            </div>
            
            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" onsubmit="return submitContents(this);">
                  <input type="hidden" name="mode" value="member_join_A">

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("아이디")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="mid" pt="_pt_id" required id="w_mid"> <span id="ret_mid"></span>
                        <input type="hidden" name="check_mid" required id="check_mid" msg='<?=_("아이디를 확인해주세요.")?>'>
                     </div>
                  </div>
                  
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
                     <label class="col-md-2 control-label"><?=_("분류")?></label>
                     <div class="col-md-4 form-inline">
                        <?foreach($r_member_state as $k=>$v){?>
                        <input type="radio" class="form-control" name="state" value="<?=$k?>" <?=$checked[state][$k]?>><?=$v?>
                        <?}?>
                     </div>
                  </div>
         
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("기업그룹")?></label>
                     <div class="col-md-10 form-inline">
                        <select name="bid" class="form-control">
                        <option value=""><?=_("기업그룹선택")?>
                        <?foreach($r_bid as $k=>$v){?>
                        <option value="<?=$k?>" <?=$selected[bid][$k]?>><?=$v?></option>
                        <?}?>
                        </select>
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
                     
                     <label class="col-md-2 control-label"><?=_("성별")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="radio" class="form-control" name="sex" value="m" <?=$checked[sex][m]?>><?=_("남자")?>
                        <input type="radio" class="form-control" name="sex" value="f" <?=$checked[sex][f]?>><?=_("여자")?>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("생년월일")?></label>
                     <div class="col-md-4 form-inline">
                        <select name="calendar" class="form-control">
                        <option value=""><?=_("선택")?>
                        <option value="s" <?=$selected[calendar][s]?>><?=_("양력")?>
                        <option value="l" <?=$selected[calendar][l]?>><?=_("음력")?>
                        </select>&nbsp;
                        <input type="text" class="form-control" name="birth_year" value="<?=$data[birth_year]?>" size=4> <?=_("년")?>
                        <input type="text" class="form-control" name="birth[]" value="<?=substr($data[birth],0,2)?>" size=2> <?=_("월")?>
                        <input type="text" class="form-control" name="birth[]" value="<?=substr($data[birth],2,2)?>" size=2> <?=_("일")?>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("주소")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="zipcode" value="<?=$data[zipcode]?>" readonly>
                        <i class="fa fa-search" style="cursor: pointer;" onclick="javascript:popupZipcode<?=$language_locale?>()"></i><br><br>
                        <input type="text" class="form-control" name="address" value="<?=$data[address]?>" size="50">
                        <input type="text" class="form-control" name="address_sub" value="<?=$data[address_sub]?>" size="50">
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("전화번호")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="phone[]" maxlength="3" size="3" value=<?=$data[phone][0]?>> -
                        <input type="text" class="form-control" name="phone[]" maxlength="4" size="4" value=<?=$data[phone][1]?>> -
                        <input type="text" class="form-control" name="phone[]" maxlength="4" size="4" value=<?=$data[phone][2]?>>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("핸드폰")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="text" class="form-control" name="mobile[]" maxlength="3" size="3" value=<?=$data[mobile][0]?>> -
                        <input type="text" class="form-control" name="mobile[]" maxlength="4" size="4" value=<?=$data[mobile][1]?>> -
                        <input type="text" class="form-control" name="mobile[]" maxlength="4" size="4" value=<?=$data[mobile][2]?>>
                     </div>
                  </div>
                  
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

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("관리자메모")?></label>
                     <div class="col-md-10">
                        <textarea name="memo" class="form-control" rows="15"><?=$data[memo]?></textarea>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
                        <button type="submit" class="btn btn-sm btn-success" <?if($_GET[mode] =="member_modify") { ?> onclick="option_chk()" <?}?> >
                        <? if($_GET[mode] == "member_join") { ?>
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

<script type="text/javascript" src="/js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/smarteditor/editorStart.js" charset="utf-8"></script>

<!--form 전송 취약점 개선 20160128 by kdk-->
<script src="../../js/webtoolkit.base64.js"></script>

<script type="text/javascript">
var oEditors = [];
smartEditorInit("contents", true, "editor", true);

function submitContents(formObj) {
   if (sendContents("contents", false)) {
      try {
         formObj.contents.value = Base64.encode(formObj.contents.value);
            return form_chk(formObj);
      } catch(e) {return false;}
   }
   return false;
}

<script src="../assets/plugins/DataTables-1.9.4/js/jquery.dataTables.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>

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
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>