<?
global $cfg, $r_paymethod, $r_step, $_query, $pod_signed, $pod_expired;

### 주문상태
$r_step_paymethod = array(91,92,81,82,83);

//debug($_POST);
//debug($r_paymethod);
//debug($r_step);
//debug($cfg[pg][paymethod]);

if ($_POST[paymethod]){
    foreach ($_POST[paymethod] as $v){
        $checked[paymethod][$v] = "checked";
    }

}

if ($_POST[step]){
    foreach ($_POST[step] as $v){
        $checked[step][$v] = "checked";
    }
}

$checked[pods_trans][$_POST[pods_trans]] = "checked";

//debug($_query);
//debug($pod_signed);
//debug($pod_expired);

?>
<div class="panel-body panel-form">
   <form class="form-horizontal form-bordered" method="post" action="">
      <!--<input type="hidden" name="p_state" value="<?=$_REQUEST[p_state]?>">-->
      <input type="hidden" name="date_buton_type" id="date_buton_type">
      <input type="hidden" name="payno">

      <div class="form-group">
         <label class="col-md-1 control-label"><?=_("주문일")?></label>
         <div class="col-md-3">
            <div class="input-group input-daterange">
               <input type="text" class="form-control" name="orddt_start" placeholder="Date Start" value="<?=$_POST[orddt_start]?>" />
               <span class="input-group-addon"> ~ </span>
                  <input type="text" class="form-control" name="orddt_end" placeholder="Date End" value="<?=$_POST[orddt_end]?>" />
            </div>
         </div>
         <div class="col-md-8">
            <button type="button" class="btn btn-sm btn-<?=$button_color[today]?>" onclick="regdtOnlyOne('today','orddt_start', 'today'); regdtOnlyOne('today','orddt_end');">
               <?=_("오늘")?>
            </button>
            <button type="button" class="btn btn-sm btn-<?=$button_color[tdays]?>" onclick="regdtOnlyOne('tdays','orddt_start', 'tdays'); regdtOnlyOne('today','orddt_end');">
               <?=_("3일")?>
            </button>
            <button type="button" class="btn btn-sm btn-<?=$button_color[week]?>" onclick="regdtOnlyOne('week','orddt_start', 'week'); regdtOnlyOne('today','orddt_end');">
               <?=_("1주일")?>
            </button>
            <button type="button" class="btn btn-sm btn-<?=$button_color[month]?>" onclick="regdtOnlyOne('month','orddt_start', 'month'); regdtOnlyOne('today','orddt_end');">
               <?=_("1달")?>
            </button>
            <button type="button" class="btn btn-sm btn-<?=$button_color[all]?>" onclick="regdtOnlyOne('all','orddt_start', 'all'); regdtOnlyOne('today','orddt_end');">
               <?=_("전체")?>
            </button>
         </div>
      </div>
      <div class="form-group">
         <label class="col-md-1 control-label"><?=_("입금/승인일")?></label>
         <div class="col-md-3">
            <div class="input-group input-daterange">
               <input type="text" class="form-control" name="paydt_start" placeholder="Date Start" value="<?=$_POST[paydt_start]?>" />
               <span class="input-group-addon"> ~ </span>
                  <input type="text" class="form-control" name="paydt_end" placeholder="Date End" value="<?=$_POST[paydt_end]?>" />
            </div>
         </div>
         <div class="col-md-8">
            <button type="button" class="btn btn-sm btn-white" onclick="regdtOnlyOne('today','paydt_start', 'today'); regdtOnlyOne('today','paydt_end');">
               <?=_("오늘")?>
            </button>
            <button type="button" class="btn btn-sm btn-white" onclick="regdtOnlyOne('tdays','paydt_start', 'tdays'); regdtOnlyOne('today','paydt_end');">
               <?=_("3일")?>
            </button>
            <button type="button" class="btn btn-sm btn-white" onclick="regdtOnlyOne('week','paydt_start', 'week'); regdtOnlyOne('today','paydt_end');">
               <?=_("1주일")?>
            </button>
            <button type="button" class="btn btn-sm btn-white" onclick="regdtOnlyOne('month','paydt_start', 'month'); regdtOnlyOne('today','paydt_end');">
               <?=_("1달")?>
            </button>
            <button type="button" class="btn btn-sm btn-white" onclick="regdtOnlyOne('all','paydt_start', 'all'); regdtOnlyOne('today','paydt_end');">
               <?=_("전체")?>
            </button>
         </div>
      </div>
      <div class="form-group">
         <label class="col-md-1 control-label"><?=_("출고일")?></label>
         <div class="col-md-3">
            <div class="input-group input-daterange">
               <input type="text" class="form-control" name="shipdt_start" placeholder="Date Start" value="<?=$_POST[shipdt_start]?>" />
               <span class="input-group-addon"> ~ </span>
                  <input type="text" class="form-control" name="shipdt_end" placeholder="Date End" value="<?=$_POST[end_date]?>" />
            </div>
         </div>
         <div class="col-md-8">
            <button type="button" class="btn btn-sm btn-white" onclick="regdtOnlyOne('today','shipdt_start', 'today'); regdtOnlyOne('today','shipdt_end');">
               <?=_("오늘")?>
            </button>
            <button type="button" class="btn btn-sm btn-white" onclick="regdtOnlyOne('tdays','shipdt_start', 'tdays'); regdtOnlyOne('today','shipdt_end');">
               <?=_("3일")?>
            </button>
            <button type="button" class="btn btn-sm btn-white" onclick="regdtOnlyOne('week','shipdt_start', 'week'); regdtOnlyOne('today','shipdt_end');">
               <?=_("1주일")?>
            </button>
            <button type="button" class="btn btn-sm btn-white" onclick="regdtOnlyOne('month','shipdt_start', 'month'); regdtOnlyOne('today','shipdt_end');">
               <?=_("1달")?>
            </button>
            <button type="button" class="btn btn-sm btn-white" onclick="regdtOnlyOne('all','shipdt_start', 'all'); regdtOnlyOne('today','shipdt_end');">
               <?=_("전체")?>
            </button>
         </div>
      </div>
      <div class="form-group">
         <label class="col-md-1 control-label"><?=_("결제방법")?></label>
         <div class="col-md-11">
            <!--
            <?foreach($cfg[pg][paymethod] as $k=>$v){?>
                <input type="checkbox" name="paymethod[]" value="<?=$v?>" <?=$checked[paymethod][$v]?>/><?=$r_paymethod[$v]?>
            <?}?>
            -->
            <?foreach($r_paymethod as $k=>$v){?>
                <input type="checkbox" name="paymethod[]" value="<?=$k?>" <?=$checked[paymethod][$k]?>/><?=$v?>
            <?}?>
         </div>
      </div>
      <div class="form-group">
         <label class="col-md-1 control-label"><?=_("주문상태")?></label>
         <div class="col-md-11">
            <?foreach($r_step as $k=>$v){?>
                <input type="checkbox" name="step[]" value="<?=$k?>" <?=$checked[step][$k]?>/><?=$v?>
            <?}?>
         </div>
      </div>
      <div class="form-group">
         <label class="col-md-1 control-label">PODs<?=_("전송")?></label>
         <div class="col-md-3">
            <input type="radio" name="pods_trans" value="" checked/> <?=_("전체")?>
            <input type="radio" name="pods_trans" value="0" <?=$checked[pods_trans][0]?>/> <?=_("미전송")?>
            <input type="radio" name="pods_trans" value="1" <?=$checked[pods_trans][1]?>/> <?=_("전송")?>
         </div>
         <div class="col-md-8">
            <div class="desc"><?=_("전체가 아닌 사항을 선택시에 미전송/전송 아이템이 속한 주문묶음을 조회하게 됩니다.")?></div>
         </div>
      </div>
      <div class="form-group">
         <label class="col-md-1 control-label"><?=_("검색어")?></label>
         <div class="col-md-8">
             <input type="text" class="form-control" name="s_search" placeholder='<?=_("아이디, 주문자명, 입금자명, 수령자명, 상품명, 상품번호, 주문번호를 입력하세요.")?>' value="<?=$_POST[s_search]?>" />
         </div>
         <div class="col-md-3">
            <button type="submit" class="btn btn-sm btn-success"><?=_("조 회")?></button>
            <button type="button" class="btn btn-sm btn-default" onclick="xls_case('order_original','<?=$_query?>&pod_signed=<?=$pod_signed?>&pod_expired=<?=$pod_expired?>')"><?=_("엑셀저장")?></button>
         </div>
      </div>
      <div class="form-group">

      </div>
   </form>
</div>
