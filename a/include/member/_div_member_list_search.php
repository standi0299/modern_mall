<?
global $cfg, $_query, $r_member_state, $r_receive, $pod_signed, $pod_expired, $_query2;

### 회원그룹 추출
$r_grp = getMemberGrp();

$checked[state][$_POST[state]] = "checked";
$checked[apply_email][$_POST[apply_email]] = "checked";
$checked[apply_sms][$_POST[apply_sms]] = "checked";
$checked[sex][$_POST[sex]] = "checked";
$checked[married][$_POST[married]] = "checked";
$selected[grp][$_POST[grp]] = "selected";
?>
<div class="panel-body panel-form">
   <form class="form-horizontal form-bordered" method="post" action="">
      <input type="hidden" name="date_buton_type" id="date_buton_type">

      <div class="form-group">
         <label class="col-md-1 control-label"><?=_("회원그룹")?></label>
         <div class="col-md-11">
            <div class="input-group input-daterange">
               <select class="form-control" name="grp" style="width:140px;">
				<option value="">전체회원</option>
				<?foreach($r_grp as $k=>$v){?>
				<option value="<?=$k?>" <?=$selected[grp][$k]?>><?=$v?></option>
				<?}?>
			  </select>
            </div>
         </div>
	  </div>
	  <div class="form-group">
         <label class="col-md-1 control-label"><?=_("적립금")?></label>
         <div class="col-md-3">
            <div class="input-group input-daterange">
               <input type="text" class="form-control" name="emoney_start" onkeypress="onlynumber()" placeholder="" value="<?=$_POST[emoney_start]?>"  style="width:210px;" />
               <span class="input-group-addon"> ~ </span>
                  <input type="text" class="form-control" name="emoney_end" onkeypress="onlynumber()" placeholder="" value="<?=$_POST[emoney_end]?>"  style="width:210px;" />
            </div>
         </div>
	  </div>
	  <div class="form-group">         
		 <label class="col-md-1 control-label"><?=_("가입일")?></label>
         <div class="col-md-3">
            <div class="input-group input-daterange">
               <input type="text" class="form-control" name="regdt_start" placeholder="Date Start" value="<?=$_POST[regdt_start]?>" />
               <span class="input-group-addon"> ~ </span>
                  <input type="text" class="form-control" name="regdt_end" placeholder="Date End" value="<?=$_POST[regdt_end]?>" />
            </div>
         </div>
         <div class="col-md-8">
            <button type="button" class="btn btn-sm btn-<?=$button_color[yesterday]?>" onclick="regdtOnlyOne('yesterday','regdt_start', 'yesterday'); regdtOnlyOne('yesterday','regdt_end');">
               <?=_("어제")?>
            </button>
			<button type="button" class="btn btn-sm btn-<?=$button_color[today]?>" onclick="regdtOnlyOne('today','regdt_start', 'today'); regdtOnlyOne('today','regdt_end');">
               <?=_("오늘")?>
            </button>
            <button type="button" class="btn btn-sm btn-<?=$button_color[tdays]?>" onclick="regdtOnlyOne('tdays','regdt_start', 'tdays'); regdtOnlyOne('today','regdt_end');">
               <?=_("3일")?>
            </button>
            <button type="button" class="btn btn-sm btn-<?=$button_color[week]?>" onclick="regdtOnlyOne('week','regdt_start', 'week'); regdtOnlyOne('today','regdt_end');">
               <?=_("1주일")?>
            </button>
            <button type="button" class="btn btn-sm btn-<?=$button_color[month]?>" onclick="regdtOnlyOne('month','regdt_start', 'month'); regdtOnlyOne('today','regdt_end');">
               <?=_("1달")?>
            </button>
            <button type="button" class="btn btn-sm btn-<?=$button_color[all]?>" onclick="regdtOnlyOne('all','regdt_start', 'all'); regdtOnlyOne('today','regdt_end');">
               <?=_("전체")?>
            </button>
         </div>
      </div>
      <div class="form-group">
         <label class="col-md-1 control-label"><?=_("최근구매일")?></label>
         <div class="col-md-3">
            <div class="input-group input-daterange">
               <input type="text" class="form-control" name="orddt_start" placeholder="Date Start" value="<?=$_POST[orddt_start]?>" />
               <span class="input-group-addon"> ~ </span>
                  <input type="text" class="form-control" name="orddt_end" placeholder="Date End" value="<?=$_POST[orddt_end]?>" />
            </div>
         </div>
         <div class="col-md-8">
            <button type="button" class="btn btn-sm btn-white" onclick="regdtOnlyOne('yesterday','orddt_start', 'yesterday'); regdtOnlyOne('yesterday','orddt_end');">
               <?=_("어제")?>
            </button>
			<button type="button" class="btn btn-sm btn-white" onclick="regdtOnlyOne('today','orddt_start', 'today'); regdtOnlyOne('today','orddt_end');">
               <?=_("오늘")?>
            </button>
            <button type="button" class="btn btn-sm btn-white" onclick="regdtOnlyOne('tdays','orddt_start', 'tdays'); regdtOnlyOne('today','orddt_end');">
               <?=_("3일")?>
            </button>
            <button type="button" class="btn btn-sm btn-white" onclick="regdtOnlyOne('week','orddt_start', 'week'); regdtOnlyOne('today','orddt_end');">
               <?=_("1주일")?>
            </button>
            <button type="button" class="btn btn-sm btn-white" onclick="regdtOnlyOne('month','orddt_start', 'month'); regdtOnlyOne('today','orddt_end');">
               <?=_("1달")?>
            </button>
            <button type="button" class="btn btn-sm btn-white" onclick="regdtOnlyOne('all','orddt_start', 'all'); regdtOnlyOne('today','orddt_end');">
               <?=_("전체")?>
            </button>
         </div>
      </div>
      <div class="form-group">
        <label class="col-md-1 control-label"><?=_("분류")?></label>
        <div class="col-md-2">
          <input type="radio" name="state" value="" <?=$checked[state]['']?>> <?=_("전체")?>
          <?foreach($r_member_state as $k=>$v){?>
            <input type="radio" name="state" value="<?=$k?>" <?=$checked[state][$k]?>> <?=$v?>
          <?}?>
        </div>
        <label class="col-md-1 control-label"><?=_("SMS 수신여부")?></label>
        <div class="col-md-2">
          <input type="radio" name="apply_sms" value="" <?=$checked[apply_sms]['']?>> <?=_("전체")?>
          <?foreach($r_receive as $k=>$v){?>
            <input type="radio" name="apply_sms" value="<?=$k?>" <?=$checked[apply_sms][$k]?>> <?=$v?>
          <?}?>
        </div>       
         <label class="col-md-1 control-label"><?=_("이메일 수신여부")?></label>
         <div class="col-md-2">
          <input type="radio" name="apply_email" value="" <?=$checked[apply_email]['']?>> <?=_("전체")?>
          <?foreach($r_receive as $k=>$v){?>
            <input type="radio" name="apply_email" value="<?=$k?>" <?=$checked[apply_email][$k]?>> <?=$v?>
          <?}?>
        </div>
      </div>

	  <!--div class="form-group">
         <label class="col-md-1 control-label"><?=_("이메일 수신여부")?></label>
         <div class="col-md-2">
            <input type="radio" name="apply_email" value="" <?=$checked[apply_email]['']?>> <?=_("전체")?>
			<?foreach($r_receive as $k=>$v){?>
			<input type="radio" name="apply_email" value="<?=$k?>" <?=$checked[apply_email][$k]?>> <?=$v?>
			<?}?>
         </div>
		 <label class="col-md-1 control-label"><?=_("SMS 수신여부")?></label>
         <div class="col-md-2">
            <input type="radio" name="apply_sms" value="" <?=$checked[apply_sms]['']?>> <?=_("전체")?>
			<?foreach($r_receive as $k=>$v){?>
			<input type="radio" name="apply_sms" value="<?=$k?>" <?=$checked[apply_sms][$k]?>> <?=$v?>
			<?}?>
         </div>
      </div>
	  <div class="form-group">
         <label class="col-md-1 control-label"><?=_("성별")?></label>
         <div class="col-md-2">
            <input type="radio" name="sex" value="" <?=$checked[sex]['']?>> <?=_("전체")?>
			<input type="radio" name="sex" value="m" <?=$checked[sex][m]?>> <?=_("남자")?>
			<input type="radio" name="sex" value="f" <?=$checked[sex][f]?>> <?=_("여자")?>
         </div>
		 <label class="col-md-1 control-label"><?=_("결혼여부")?></label>
         <div class="col-md-2">
            <input type="radio" name="married" value="" <?=$checked[married]['']?>> <?=_("전체")?>
			<input type="radio" name="married" value="0" <?=$checked[married][0]?>> <?=_("미혼")?>
			<input type="radio" name="married" value="1" <?=$checked[married][1]?>> <?=_("기혼")?>
         </div>
      </div-->
	  <div class="form-group">
         <label class="col-md-1 control-label"><?=_("나이")?></label>
         <div class="col-md-3">
            <div class="input-group input-daterange">
               <input type="text" class="form-control" name="age_start" onkeypress="onlynumber()" placeholder="" value="<?=$_POST[age_start]?>"  style="width:210px;" />
               <span class="input-group-addon"> ~ </span>
                  <input type="text" class="form-control" name="age_end" onkeypress="onlynumber()" placeholder="" value="<?=$_POST[age_end]?>"  style="width:210px;" />
            </div>
         </div>
	  </div>
	  <div class="form-group">
         <label class="col-md-1 control-label"><?=_("구매금액")?></label>
         <div class="col-md-3">
            <div class="input-group input-daterange">
               <input type="text" class="form-control" name="totpayprice_start" onkeypress="onlynumber()" placeholder="" value="<?=$_POST[totpayprice_start]?>"  style="width:210px;" />
               <span class="input-group-addon"> ~ </span>
                  <input type="text" class="form-control" name="totpayprice_end" onkeypress="onlynumber()" placeholder="" value="<?=$_POST[totpayprice_end]?>"  style="width:210px;" />
            </div>
         </div>
	  </div>
      <div class="form-group">
         <label class="col-md-1 control-label"></label>
         <div class="col-md-8">
             <input type="text" class="form-control" name="s_search" placeholder='<?=_("아이디, 회원명을 입력하세요.")?>' value="<?=$_POST[s_search]?>" />
         </div>
         <div class="col-md-3">
            <button type="submit" class="btn btn-sm btn-success"><?=_("조 회")?></button>
            <button type="button" class="btn btn-sm btn-default" onclick="xls_case('member','<?=$_query?>&pod_signed=<?=$pod_signed?>&pod_expired=<?=$pod_expired?>')"><?=_("엑셀저장")?></button>
			<button type="button" class="btn btn-sm btn-default"><a href="/a/member/member_email_excel.php?query=<?=$_query2?>"><?=_("메일링저장")?></a></button>
         </div>
      </div>
   </form>
</div>