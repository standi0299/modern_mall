<?
include "../_header.php";
include "../_left_menu.php";
include_once dirname(__FILE__) . "/../../lib2/db_common.php";
include_once dirname(__FILE__) . "/../../models/m_common.php";

## 출석 체크 이벤트

$tableName = "md_attend_event";
$bRegistFlag = false;
$selectArr = "*";
$whereArr = array("cid" => "$cid");

$data = SelectInfoTable($tableName, $selectArr, $whereArr, $bRegistFlag, $orderby);

if (!$data) {
	$data[etype] = "0";
}

$checked[etype][$data[etype]] = "checked";
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
      <li class="active"><?=_("출석 체크 이벤트") ?></li>
   </ol>
   <h1 class="page-header"><?=_("출석 체크 이벤트") ?> <small><?=_("출석 체크 이벤트 정보를 등록 및 수정하실 수 있습니다.") ?></small></h1>
      
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   	<input type="hidden" name="mode" value="attend_event"/>

      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("출석 체크 이벤트 설정") ?></h4>
         </div>
         <div class="panel-body panel-form">
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("진행 방법") ?></label>
               <div class="col-md-4">
                  <input type="radio" name="etype" value="0" <?=$checked[etype][0] ?> /><?=_("수동 진행")?>
                  <input type="radio" name="etype" value="1" <?=$checked[etype][1] ?> /><?=_("자동 진행")?>    
               </div>
               <label class="col-md-6 control-label">
                  <span class="pull-left"><?=_("- 수동 진행 : 관리자가 진행을 선택합니다.") ?></span><br>
                  <span class="pull-left"><?=_("- 자동 진행 : 자동으로 진행합니다.") ?></span><br>
			      </label>
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("이벤트 명") ?></label>
               <div class="col-md-4">
                  <input type="text" class="form-control" name="title" value="<?=$data[title] ?>" required pt="_pt_txt"/>
               </div>
            </div>
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("이벤트 기간")?></label>
               <div class="col-md-4">
                  <div class="input-group input-daterange">
                     <input type="text" class="form-control" name="sdate" placeholder="시작일" value="<?=$data[sdate]?>" required />
                     <span class="input-group-addon"> ~ </span>
                     <input type="text" class="form-control" name="edate" placeholder="종료일" value="<?=$data[edate]?>" required />
                  </div>
               </div>
               <label class="col-md-6 control-label">
                  <span class="pull-left"><?=_("- 입력된 기간내에 사용이 가능합니다.") ?></span>
			      </label>
            </div>            
                        
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("출석 체크 혜택") ?></label>
               <div class="col-md-4 form-inline">
                  <input type="text" class="form-control" name="emoney" pt="_pt_numplus" size="10" value="<?=$data[emoney] ?>" required /> <?=_("적립금 지급") ?>
               </div>
               <label class="col-md-6 control-label">
                  <span class="pull-left"><?=_("- 입력된 금액만큼 적립 합니다.") ?></span>
			      </label>
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("추가 혜택 설정") ?></label>
               <div class="col-md-4 form-inline">
                  <input type="text" class="form-control" name="count_tot" value="<?=$data[count_tot] ?>" size="5" /> <?=_("누적횟수 참여") ?> / 
                  <input type="text" class="form-control" name="count_seq" value="<?=$data[count_seq] ?>" size="5" /> <?=_("연속 횟수 참여") ?>
               </div>
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("추가 혜택") ?></label>
               <div class="col-md-4 form-inline">
                    <input type="text" class="form-control" name="add_emoney" pt="_pt_numplus" size="10" value="<?=$data[add_emoney] ?>"/> <?=_("적립금 지급") ?>
               </div>
               <label class="col-md-6 control-label">
                  <span class="pull-left"><?=_("- 입력된 금액만큼 적립 합니다.") ?></span>
			      </label>
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("적립금 유효기간") ?></label>
               <div class="col-md-4 form-inline">
                  <?=_("발급(적립) 일로 부터") ?> ~ <input type="text" class="form-control" name="emoney_expire_date" size="10" value="<?=$data[emoney_expire_date] ?>" required /> <?=_("일 까지 사용이 가능합니다.") ?>
               </div>
               <label class="col-md-6 control-label">
                  <span class="pull-left"><?=_("") ?></span>
			      </label>
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("이벤트 참여 메시지") ?></label>
               <div class="col-md-4">
                  <textarea class="form-control" name="msg1" cols="50" rows="3"><?=$data[msg1] ?></textarea>
               </div>
               <label class="col-md-6 control-label">
                  <span class="pull-left"></span>
			   </label>
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("추가 혜택 성공 메시지") ?></label>
               <div class="col-md-4">
                  <textarea class="form-control" name="msg2" cols="50" rows="3"><?=$data[msg2] ?></textarea>
               </div>
               <label class="col-md-6 control-label">
                  <span class="pull-left"></span>
			      </label>
            </div>            
         </div>
      </div>

      <div class="row">
         <div class="col-md-11">
            <p class="pull-right">
               <button type="submit" class="btn btn-md btn-primary m-r-15"><?=_("등록") ?></button>
               <button type="button" class="btn btn-md btn-default" onclick="javascript:history.back()"><?=_("취소") ?></button>
   	      </p>
         </div>
      </div>
   </form>
</div>
<? include "../_footer_app_init.php"; ?>

<script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script>
   var handleDatepicker = function() {
      $('.input-daterange').datepicker({
         language : 'kor',
         todayHighlight : true,
         autoclose : true,
         todayBtn : true,
         format : 'yyyy-mm-dd',
      });
   };

   handleDatepicker();
</script>

<? include "../_footer_app_exec.php"; ?>