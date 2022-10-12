<div class="panel-body panel-form">
   <form class="form-horizontal form-bordered" method="post" action="">
      <!--<input type="hidden" name="p_state" value="<?=$_REQUEST[p_state]?>">-->
      <input type="hidden" name="date_buton_type" id="date_buton_type">
      <input type="hidden" name="payno">

      <div class="form-group">
         <label class="col-md-1 control-label"><?=_("주문일")?></label>
         <div class="col-md-3">
            <div class="input-group input-daterange">
               <input type="text" class="form-control" name="start_date" placeholder="Date Start" value="<?=$_POST[start_date]?>" />
               <span class="input-group-addon"> ~ </span>
                  <input type="text" class="form-control" name="end_date" placeholder="Date End" value="<?=$_POST[end_date]?>" />
            </div>
         </div>
         <div class="col-md-5">
            <button type="button" class="btn btn-sm btn-<?=$button_color[today]?>" onclick="regdtOnlyOne('today','start', 'today'); regdtOnlyOne('today','end');">
               <?=_("오늘")?>
            </button>
            <button type="button" class="btn btn-sm btn-<?=$button_color[tdays]?>" onclick="regdtOnlyOne('tdays','start', 'tdays'); regdtOnlyOne('today','end');">
               <?=_("3일")?>
            </button>
            <button type="button" class="btn btn-sm btn-<?=$button_color[week]?>" onclick="regdtOnlyOne('week','start', 'week'); regdtOnlyOne('today','end');">
               <?=_("1주일")?>
            </button>
            <button type="button" class="btn btn-sm btn-<?=$button_color[month]?>" onclick="regdtOnlyOne('month','start', 'month'); regdtOnlyOne('today','end');">
               <?=_("1달")?>
            </button>
            <button type="button" class="btn btn-sm btn-<?=$button_color[all]?>" onclick="regdtOnlyOne('all','start', 'all'); regdtOnlyOne('today','end');">
               <?=_("전체")?>
            </button>
         </div>
         <div class="col-md-2">
            <input type="text" class="form-control" name="s_search" placeholder='<?=_("상품명, 상품번호를 입력하세요")?>' />
         </div>
         <div class="col-md-1">
            <button type="submit" class="btn btn-sm btn-success">
               <?=_("조 회")?>
            </button>
         </div>
      </div>
   </form>
</div>