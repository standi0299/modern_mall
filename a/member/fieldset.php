<?

/*
* @date : 20180306
* @author : kdk
* @brief : 회원가입항목설정 사용.
* @request : 
* @desc : fieldset 보임 (exm_config)
* @todo :
*/

/*
* @date : 20180227
* @author : kdk
* @brief : 회원가입항목설정 사용안함.
* @request : 
* @desc : fieldset 숨김 (exm_config)
* @todo :
*/

include "../_header.php";
include "../_left_menu.php";
include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

## 회원그룹 추출
$r_grp = getMemberGrp();

$tableName = "exm_member_grp";
$selectArr = "grpno";
$whereArr = array("cid" => "$cid", "base" => '1');
$basegrpData = SelectInfoTable($tableName, $selectArr, $whereArr);
$basegrp = $basegrpData[grpno];

### 회원가입항목 추출
$tableName = "exm_config";
$selectArr = "value";
$whereArr = array("cid" => "$cid", "config" => 'fieldset');
$fieldsetData = SelectInfoTable($tableName, $selectArr, $whereArr);
$fieldset = $fieldsetData[value];
$fieldset = unserialize($fieldset);
if (!$fieldset){
	$fieldset = $r_fieldset;
	$new = "new";
} else {
	$fieldset = array_merge($r_fieldset,$fieldset);
}

$checked[basestate][$cfg[basestate]] = "checked";
$selected[basegrp][$basegrp] = "selected";

if (!$cfg[basestate]) $checked[basestate][0] = "checked";

//sms 인증 추가 		20160302		chunter
$checked[register_sms_auth][0] = "checked";
if ($cfg[register_sms_auth])
	$checked[register_sms_auth][$cfg[register_sms_auth]] = "checked";

if (!$cfg[register_sms_auth_msg])
	$cfg[register_sms_auth_msg] = _("인증번호는 [{인증번호}] 입니다.");	
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active"><?=_("회원가입설정")?></li>
   </ol>
   <h1 class="page-header"><?=_("회원가입설정")?> <small><?=_("회원 가입을 위한 설정과 항목을 등록 및 수정하실 수 있습니다.")?></small></h1>
      
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   	<input type="hidden" name="mode" value="fieldset"/>
	<input type="hidden" name="mode2" value="<?=$new?>"/>

      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("회원가입설정")?></h4>
         </div>

         <div class="panel-body panel-form">
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("회원인증절차")?></label>
               <div class="col-md-4">
                  	<input type="radio" name="basestate" value="0" <?=$checked[basestate][0]?>/> <?=_("인증절차없음")?>
					<input type="radio" name="basestate" value="1" <?=$checked[basestate][1]?>/> <?=_("관리자 인증 후 가입")?>
               </div>
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("가입불가ID")?></label>
               <div class="col-md-4">
                  <textarea class="form-control" name="unableid" cols="50" rows="3"><?=$cfg[unableid]?></textarea>
               </div>
               <label class="col-md-6 control-label">
                  <span class="pull-left"><?=_("[주의] 아이디는 , (콤마)로 구분됩니다")?></span>
			   </label>
            </div>
            <!--
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("회원가입적립금")?></label>
               <div class="col-md-2">
                  <input type="text" class="form-control" name="emoney" value="<?=$cfg[baseemoney]?>"/>
               </div>
               <label class="col-md-8 control-label">
                  <span class="pull-left"><?=_("원   (회원가입시 지급되는 적립금입니다)")?></span>
			   </label>
            </div>
            -->
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("회원가입기본그룹")?></label>
               <div class="col-md-2">
                  <select class="form-control" name="basegrp">
					<?foreach($r_grp as $k=>$v){?>
					<option value="<?=$k?>" <?=$selected[basegrp][$k]?>><?=$v?></option>
					<?}?>
				  </select>
               </div>
               <label class="col-md-8 control-label">
                  <span class="pull-left"><?=_("(회원가입시 기본그룹이 됩니다)")?></span>
			   </label>
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("SMS 인증 사용")?></label>
               <div class="col-md-4">
                  	<input type="radio" name="register_sms_auth" value="0" <?=$checked[register_sms_auth][0]?>/> <?=_("사용하지 않음")?>
					<input type="radio" name="register_sms_auth" value="1" <?=$checked[register_sms_auth][1]?>/> <?=_("사용함")?>
               </div>
               <label class="col-md-6 control-label">
                  <span class="pull-left"><?=_("(SMS 잔여 call 있을경우 발송됩니다.[SMS call 충전 유료])")?></span>
			   </label>
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("SMS 인증 메세지")?></label>
               <div class="col-md-4">
                  <input type="text" class="form-control" name="register_sms_auth_msg" value="<?=$cfg[register_sms_auth_msg]?>"/>
               </div>
               <label class="col-md-6 control-label">
                  <span class="pull-left"><?=_("{인증번호} - 인증번호로 대체")?></span>
			   </label>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-heading">
            <div class="panel-heading-btn">
               <i class="fa fa-exclamation-triangle"></i> (<?=_("개정된 정보통신망법에 의거 주민등록번호는 수집하지 못하게 되어 있습니다. 반드시 확인하시기 바랍니다.")?>)            	
            </div>
            <h4 class="panel-title"><?=_("회원가입항목설정")?>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="delField"/> <?=_("초기화")?></h4>
         </div>

         <div class="panel-body">
            <div class="table-responsive">
               <table class="table table-bordered table-hover">
                  <thead>
                     <tr>
                        <th><?=_("번호")?></th>
                        <th><?=_("필드명")?></th>
                        <th><?=_("사용여부")?></th>                                                      
                        <th><?=_("필수여부")?></th>
                     </tr>
                  </thead>
                  <tbody>
                  <? foreach ($fieldset as $k=>$v) { ?>
                     <tr>
                     <td><?=++$idx?><input type="hidden" name="field[<?=$k?>][index]" value="<?=$idx?>"/></td>
   						<td><?=$r_fieldset[$k][name]?><input type="hidden" name="field[<?=$k?>][name]" value="<?=$r_fieldset[$k][name]?>"/></td>
   						<td><input type="checkbox" name="field[<?=$k?>][use]" <?if($v['use']){?>checked<?}if($v[req]&&$idx<4){?> disabled<?}?>/></td>
   						<td><input type="checkbox" name="field[<?=$k?>][req]" <?if($v[req]){?>checked<?}if($v[req]&&$idx<4){?> disabled<?}?>/></td>
                     </tr>
                  <? } ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>

      <div class="row">
         <div class="col-md-11">
            <p class="pull-right">
	    	      <button type="submit" class="btn btn-md btn-primary m-r-15"><?=_("등록")?></button>
               <button type="button" class="btn btn-md btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
	      	</p>
         </div>
      </div>
   </form>
</div>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>