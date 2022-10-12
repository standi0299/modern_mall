<?
include "../_header.php";
include "../_left_menu.php";
include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

## 회원그룹 추출
$r_grp = getMemberGrp();

### 회원정보 추출
$tableName = "exm_member";
$selectArr = "*";
$whereArr = array("cid" => "$cid", "mid" => "$_GET[mid]");
$data = SelectInfoTable($tableName, $selectArr, $whereArr);

$data[email] = explode("@",$data[email]);
$data[phone] = explode("-",$data[phone]);
$data[mobile] = explode("-",$data[mobile]);

if (!$data[state]) $checked[state][0] = "checked";
if (!$data[sex]) $checked[sex][m] = "checked";
if (!$data[apply_email]) $checked[apply_email][0] = "checked";

$selected[grp][$data[grpno]] = "selected";
$selected[bid][$data[bid]] = "selected";
$selected[birthtype][$data[calendar]] = "selected";
$selected[calendar][$data[calendar]] = "selected";
$selected[manager_no][$data[manager_no]] = "selected";

$checked[state][$data[state]] = "checked";
$checked[sex][$data[sex]] = "checked";

$checked[apply_email][$data[apply_email]] = "checked";
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
                  <input type="hidden" name="mode" value="member_modify_A">
                  <input type="hidden" name="mid" value="<?=$_GET[mid]?>">

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("아이디")?></label>
                     <div class="col-md-10 form-inline">
                        <b><?=$_GET[mid]?></b>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("그룹")?></label>
                     <div class="col-md-4 form-inline">
                        <select name="grp" class="form-control">
                        <option value="">그룹선택
                        <?foreach($r_grp as $k=>$v){?>
                        <option value="<?=$k?>" <?=$selected[grp][$k]?>><?=$v?></option>
                        <?}?>
                        </select>
                     </div>

                     <label class="col-md-2 control-label"><?=_("분류")?></label>
                     <div class="col-md-4 form-inline">
                        <?foreach($r_member_state as $k=>$v){?>
                        <input type="radio" class="form-control" name="state" value="<?=$k?>" <?=$checked[state][$k]?>><?=$v?>
                        <?}?>
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

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>