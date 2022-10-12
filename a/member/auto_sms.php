<?
include "../_header.php";
include "../_left_menu.php";
include_once dirname(__FILE__) . "/../../lib2/db_common.php";
include_once dirname(__FILE__) . "/../../models/m_common.php";

$cfg[phone] = explode("-", $cfg[phone]);
$cfg[mobile1] = explode("-", $cfg[mobile1]);
$cfg[mobile2] = explode("|", $cfg[mobile2]);

for ($i=0; $i<count($cfg[mobile2]); $i++){
   $cfg[mobile2][$i] = explode("-", $cfg[mobile2][$i]);
}
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
      <li class="active"><?=_("자동SMS 관리")?></li>
   </ol>
   <h1 class="page-header"><?=_("자동SMS 관리")?> <small><?=_("소비자에게 유형별 SMS를 자동으로 보내실 수 있습니다.")?></small></h1>
   
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   	<input type="hidden" name="mode" value="autosms"/>

      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("자동SMS 관리")?></h4>
         </div>

         <div class="panel-body panel-form">            
            <div class="form-group">
               <div class="col-md-12 form-inline"> - <?=_("SMS를 보내시려면 포인트 충전이 되어 있어야 합니다.")?></div>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("담당자 핸드폰")?></label>
               <div class="col-md-10 form-inline">
                  <input type="text" name="mobile1[]" class="form-control" value="<?=$cfg[mobile1][0]?>" maxlength="3"> -
                  <input type="text" name="mobile1[]" class="form-control" value="<?=$cfg[mobile1][1]?>" maxlength="4"> -
                  <input type="text" name="mobile1[]" class="form-control" value="<?=$cfg[mobile1][2]?>" maxlength="4">
               </div>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("담당자추가 핸드폰")?></label>
               <div class="col-md-10 form-inline">
                  <?for ($i=0; $i<=count($cfg[mobile2]); $i++){?>
                     <div>
                     <? if($i==count($cfg[mobile2])) { ?>
                     <div id="addmobile" style="list-style:none;"></div>
                     <input type="text" name="mobile2[1][]" class="form-control" value="<?=$cfg[mobile2][$i][0]?>" maxlength="3"> -
                     <input type="text" name="mobile2[2][]" class="form-control" value="<?=$cfg[mobile2][$i][1]?>" maxlength="4"> -
                     <input type="text" name="mobile2[3][]" class="form-control" value="<?=$cfg[mobile2][$i][2]?>" maxlength="4">
                     <span class="btn btn-primary btn-icon btn-circle btn-xs" onclick="add()"><i class="fa fa-plus"></i></span><br><br>                     
                     <? } else { ?>
                     <input type="text" name="mobile2[1][]" class="form-control" value="<?=$cfg[mobile2][$i][0]?>" maxlength="3"> -
                     <input type="text" name="mobile2[2][]" class="form-control" value="<?=$cfg[mobile2][$i][1]?>" maxlength="4"> -
                     <input type="text" name="mobile2[3][]" class="form-control" value="<?=$cfg[mobile2][$i][2]?>" maxlength="4">
                     <span class="btn btn-danger btn-icon btn-circle btn-xs" onclick="remove(this)"><i class="fa fa-times"></i></span><br><br>
                     <? } ?>
                     </div>
                  <? } ?>
               </div>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("설정")?></label>
               <div class="col-md-10 form-inline">

                  <?
                  foreach ($r_title as $k=>$v){
                  	$tableName = "exm_automsg";
					$bRegistFlag = false;
					$selectArr = "msg1, msg2, send ";
					$whereArr = array("cid" => "$cid", "catnm" => "sms", "type" => "$v");

					$data = SelectInfoTable($tableName, $selectArr, $whereArr, $bRegistFlag, $orderby);
					if($data) {
						$sms_msg1[$k] = $data[msg1]; 
						$sms_msg2[$k] = $data[msg2];
						$send[$k] = $data[send];
					}
					
                     //list ($sms_msg1[$k],$sms_msg2[$k],$send[$k]) = $db->fetch("select msg1, msg2, send from exm_automsg where cid = '$cid' and catnm = 'sms' and type = '$v'",1);

                     for ($i=0;$i<3;$i++){
                        if ($send[$k]&pow(2,$i)) $checked[send][$k][pow(2,$i)] = "checked";
                     }

                     if ($k%2==0 && $k != 0){?> <br><br><br> <?}?>
                     <div class="col-md-6">
                        <br>
                        <div class="col-md-12 alert-info">
                           <label class="control-label">
                              <h5>
                                 <?=$v?>
                                 <? if($v == _("접수내역")) { ?> - <?=_("가상계좌, 무통장 접수 시")?> <?}?>
                                 <? if($v == _("주문접수")) { ?> - <?=_("가상계좌, 무통장을 제외한 주문")?> <?}?>
                              </h5>
                           </label><br>
                        </div><br><br>

                        <div class="col-md-6 alert-info">
                           <textarea name="sms_msg[<?=$k?>][]" rows="6" class="form-control" onkeyup="chkSmsByte(this)"><?=$sms_msg1[$k]?></textarea><br>
                           <span id="byte">0 </span> byte<br>

                           <input type="checkbox" class="control-label" name="send[<?=$k?>][]" value="1" <?=$checked[send][$k][1]?>/> <?=_("고객에게 자동발송")?><br><br><br>
                        </div>

                        <div class="col-md-6 alert-info">
                           <textarea name="sms_msg[<?=$k?>][]" rows="6" class="form-control" onkeyup="chkSmsByte(this)"><?=$sms_msg2[$k]?></textarea><br>
                           <span id="byte">0 </span> byte<br>

                           <input type="checkbox" class="control-label" name="send[<?=$k?>][]" value="2" <?=$checked[send][$k][2]?>/> <?=_("관리자에게도 발송")?></br>
                           <input type="checkbox" class="control-label" name="send[<?=$k?>][]" value="4" <?=$checked[send][$k][4]?>/> <?=_("추가관리자에게도 발송")?><br><br>
                        </div>
                     </div>
                  <?}?>

               </div>
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

   <div class="panel panel-inverse">
      <div class="panel-body panel-form">
         <div class="form-group">
            <div class="col-md-12">
               <br>
               <?=_("[확인] SMS 보내기 서비스 이용 시 일정 포인트를 소모합니다. 또한 충전 포인트 잔액이 부족할 경우 전송이 안될 수 있습니다.")?><br><br> 
               - <?=_("포인트 잔액을 확인 후 이용해주세요.")?>
               <br><br>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
function add(){
   var target = $j("#addmobile");
   var inner = $j("#origin_tb").html();
   var div = document.createElement("div");
   div.innerHTML = inner;
   $j(div).clone().appendTo(target);
}

function remove(obj){
   $j(obj).parent().remove();
}
</script>

<!-- 카피용 -->
<div id="origin_tb" style="display:none;padding-left:-10px">
<input type="text" name="mobile2[1][]" class="form-control" maxlength="3"> -
<input type="text" name="mobile2[2][]" class="form-control" maxlength="4"> -
<input type="text" name="mobile2[3][]" class="form-control" maxlength="4">
<span class="btn btn-danger btn-icon btn-circle btn-xs" onclick="remove(this)"><i class="fa fa-times"></i></span><br><br>
</div>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>