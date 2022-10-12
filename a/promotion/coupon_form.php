<?
include "../_header.php";
include "../_left_menu.php";
include_once dirname(__FILE__) . "/../../lib2/db_common.php";
include_once dirname(__FILE__) . "/../../models/m_common.php";

## 쿠폰등록
$r_kind = array("on" => _("온라인"), "off" => _("오프라인"));

$category = array();

if ($_GET[coupon_code]) {
	$tableName = "exm_coupon";
	$bRegistFlag = false;
	$selectArr = "*";
	$whereArr = array("cid" => "$cid", "coupon_code" => "$_GET[coupon_code]");

	$data = SelectInfoTable($tableName, $selectArr, $whereArr, $bRegistFlag, $orderby);

	if ($data[coupon_range] == "category" && $data[coupon_catno]) {

		$data[coupon_catno] = explode(",", $data[coupon_catno]);

		foreach ($data[coupon_catno] as $v) {
			$category[$v] = getCatnm($v);
		}
	}

	if ($data[coupon_range] == "goods" && $data[coupon_goodsno]) {
		//쿠폰에 연결된 상품 데이터를 가져오기 위해 쿼리 변경 / 14.06.18 / kjm
		### 연결상품데이터 추출
		$link_goods = array();
		$coupon_code = explode(',', $data[coupon_goodsno]);
		foreach ($coupon_code as $k => $v) {
			$tableName = "exm_goods";
			$bRegistFlag = false;
			$selectArr = "goodsnm";
			$whereArr = array("goodsno" => "$v");

			$link_data = SelectInfoTable($tableName, $selectArr, $whereArr, $bRegistFlag, $orderby);
			//$link_data =  $db->fetch("select goodsnm from exm_goods where goodsno = '$v'");
			$link_goods[$v][goodsnm] = $link_data[goodsnm];
			$link_goods[$v][goodsno] = $v;
		}
	}

	$modify = "1";
	$data[chk_code] = 1;
}

if (!$data) {
	$data[coupon_type] = "discount";
	$data[coupon_way] = "price";
	$data[coupon_period_system] = "date";
	$data[coupon_issue_ea_limit] = ($_GET[kind] == "on") ? "0" : "1";
	$data[coupon_range] = "all";
	$data[coupon_kind] = $_GET[kind];
	$data[coupon_issue_system] = ($_GET[kind] == "on") ? "admin" : "download";
}

$checked[coupon_type][$data[coupon_type]] = "checked";
$checked[coupon_way][$data[coupon_way]] = "checked";
$checked[coupon_issue_system][$data[coupon_issue_system]] = "checked";
$checked[coupon_issue_ea_limit][$data[coupon_issue_ea_limit]] = "checked";
$checked[coupon_issue_unlimit][$data[coupon_issue_unlimit]] = "checked";
$checked[coupon_period_system][$data[coupon_period_system]] = "checked";
$checked[coupon_range][$data[coupon_range]] = "checked";

//기간 연장 쿠폰의 연장기간      20131204    chunter
if ($data[fix_extension_date]) {
	$fix_extension_date_arr = explode(",", $data[fix_extension_date]);
	$checked[fix_extension_date_month][$fix_extension_date_arr[0]] = "selected";
	$checked[fix_extension_date_day][$fix_extension_date_arr[1]] = "selected";
}
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<style type="text/css">
#obj_rels {width:800px; height:100%; height:130px; border:1px solid #cccccc; margin:10px; padding:5px; overflow-y:scroll;}
#obj_rels ul {list-style:none; margin:0; padding:0;}
#obj_rels li {float:left; font:8pt '돋움'; letter-spacing:-1px; margin-right:10px; width:65px; height:110px; cursor:pointer; margin-bottom:5px; overflow:hidden;}
#obj_rels img {display:block; margin-bottom:5px; border:1px solid #cccccc; width:65px;}
</style>

<div id="content" class="content">
   <!-- begin #header -->
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active"><?=$r_kind[$_GET[kind]]?> <?=_("쿠폰등록") ?></li>
   </ol>
   <h1 class="page-header"><?=$r_kind[$_GET[kind]]?> <?=_("쿠폰등록") ?> <small><?=_("쿠폰을 등록 및 수정하실 수 있습니다.") ?></small></h1>
      
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
      <input type="hidden" name="mode" value="coupon"/>
      <input type="hidden" name="coupon_code" value="<?=$data[coupon_code] ?>"/>
      <input type="hidden" name="coupon_kind" value="<?=$data[coupon_kind] ?>"/>
      <input type="hidden" name="modify" value="<?=$modify ?>"/>
      <input type="hidden" name="rurl" value="<?=$_SERVER[HTTP_REFERER] ?>"/>
      <input type="hidden" id="chk_code" name="chk_code" required msg='<?=_("쿠폰코드를 확인해주세요.")?>' value="<?=$data[chk_code] ?>"/>

      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=$r_kind[$_GET[kind]] ?> <?=_("쿠폰 등록") ?></h4>
         </div>
         <div class="panel-body panel-form">
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("쿠폰명") ?></label>
               <div class="col-md-4">
                  <input type="text" class="form-control" name="coupon_name" value="<?=$data[coupon_name] ?>" required pt="_pt_txt"/>
               </div>
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("쿠폰코드") ?></label>
               <div class="col-md-2">
                    <? if ($data[coupon_code]){ ?>
                        <b><?=$data[coupon_code] ?></b>
                    <? } else { ?>
                        <input type="text" class="form-control" name="coupon_code" required pt="_pt_coupon" onblur="chkCode(this)" placeholder='<?=_("쿠폰코드를 입력해주세요.")?>'/>
                    <? } ?>
               </div>
               <label class="col-md-8 control-label">
                  <span class="pull-left"><?=_("영문 혹은 숫자로 구성된 6~10자의 문자") ?></span>
			   </label>
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("쿠폰종류") ?></label>
               <div class="col-md-6">
                    <input type="radio" name="coupon_type" value="discount" onclick="setting_coupon_way('1');" <?=$checked[coupon_type][discount] ?>/><?=_("할인")?>
                    <input type="radio" name="coupon_type" value="saving" onclick="setting_coupon_way('1');" <?=$checked[coupon_type][saving] ?>/><?=_("추가적립")?>
                    <input type="radio" name="coupon_type" value="coupon_money" onclick="setting_coupon_way('3');" <?=$checked[coupon_type][coupon_money]?>/><?=_("분할정액쿠폰")?>   
                    <input type="radio" name="coupon_type" value="sale_code" onclick="setting_coupon_way('4');" <?=$checked[coupon_type][sale_code]?>/><?=_("프로모션코드")?>(모바일 불가)
                    <? if ($_GET[kind]=="off"){ ?>
                        <br><input type="radio" name="coupon_type" value="point_save" onclick="setting_coupon_way('3');" <?=$checked[coupon_type][point_save] ?>/><?=_("적립금전환")?>  
                        <input type="radio" name="coupon_type" value="fix_date" onclick="setting_coupon_way('2');" <?=$checked[coupon_type][fix_date] ?>/><?=_("정액회원 기간연장")?>
                    <? } ?> 
               </div>
               
               
               
               <label class="col-md-4 control-label">
                    <span class="pull-left"><?=_("- 할인 : 결제시 상품금액을 할인합니다.") ?></span><br>
                    <span class="pull-left"><?=_("- 추가적립 : 결제후 적립금 지급시 추가적립됩니다.") ?></span><br>
                    <span class="pull-left"><?=_("- 분할정액쿠폰 : 등록된 쿠폰 금액에 한하여 사용자 분할 사용")?></span>
                    <? if ($_GET[kind]=="off"){ ?>  
                    	<br><span class="pull-left"><?=_("- 적립금전환 : 금액만큼 적립금이 적립됩니다.") ?></span>
                        <br><span class="pull-left"><?=_("- 정액회원 기간연장 : 정액회원의 사용 기간을 연장합니다.") ?></span>
                    <? } ?> 
			   </label>
            </div>
             <? if ($_GET[kind]=="off" && $data[coupon_code]){ ?>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("오프라인쿠폰발행") ?></label>
               <div class="col-md-2">
               	<span class="pull-left">
                    <? if (!$data[coupon_off_issue]){ ?>
                    <?=_("미발행")?> - <b class="stxt"><a href="indb.php?mode=coupon_offline_issue&coupon_code=<?=$data[coupon_code] ?>" onclick="return confirm('<?=_("오프라인쿠폰")?>(<?=$data[coupon_issue_ea] ?><?=_("장")?>)<?=_("을 생성하시겠습니까?")?> \n<?=_("생성후에는 발급수량을 수정하실수가 없습니다")?>')&&alert('<?=_("생성시에 장시간이 소요될수 있습니다.")?>')"><?=_("발행하기")?></a></b>
                    <? } else { ?>
                    <?=_("발행완료")?>
                    <? } ?>
                </span>
               </div>
               <label class="col-md-8 control-label">
                  <span class="pull-left"><?=_("쿠폰식별번호 : ") ?> <b class="eng"><?=$data[coupon_offcode] ?></b></span>
			      </label>
            </div>
             <? } ?>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("관리자메모") ?></label>
               <div class="col-md-4">
                  <textarea class="form-control" name="adminmemo" cols="50" rows="3"><?=$data[adminmemo] ?></textarea>
               </div>
               <label class="col-md-6 control-label">
                  <span class="pull-left"></span>
			   </label>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("할인/적립 설정") ?></h4>
         </div>
         <div class="panel-body panel-form">
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("할인/적립 방식") ?></label>
               <div class="col-md-4">
                <input id="coupon_way_price" type="radio" name="coupon_way" value="price" onclick="setting_form('coupon_way','price')" <?=$checked[coupon_way][price] ?>/><?=_("정액")?>
                <input id="coupon_way_rate" type="radio" name="coupon_way" value="rate" onclick="setting_form('coupon_way','rate')" <?=$checked[coupon_way][rate] ?>/><?=_("정률")?>
                <? if ($_GET[kind]=="off"){ ?>  
                    <input id="coupon_way_fdate" type="radio" name="coupon_way" value="fdate" onclick="setting_form('coupon_way','fdate')" <?=$checked[coupon_way][fdate] ?>/><?=_("기간연장")?>
                <? } ?>
               </div>
            </div>
            <div class="form-group coupon_way" coupon_way="price">
               <label class="col-md-2 control-label"><?=_("할인/적립 금액") ?></label>
               <div class="col-md-4 form-inline">
                    <input type="text" class="form-control" name="coupon_price" pt="_pt_numplus" size="10" value="<?=$data[coupon_price] ?>"/> <?=_("원")?>
               </div>
               <label class="col-md-6 control-label">
                  <span class="pull-left"><?=_("- 정액 : 입력된 금액만큼 할인/적립 합니다.") ?></span>
			   </label>
            </div>
            <div class="form-group coupon_way" coupon_way="rate">
               <label class="col-md-2 control-label"><?=_("할인/적립률") ?></label>
               <div class="col-md-4 form-inline">
                  <input type="text" class="form-control" name="coupon_rate" pt="_pt_numplus" size="3" value="<?=$data[coupon_rate] ?>"/> %
                  <?=_("최대")?> <input type="text" class="form-control" name="coupon_price_limit" pt="_pt_numplus" size="10" value="<?=$data[coupon_price_limit] ?>"/> <?=_("까지 할인가능")?>
               </div>
               <label class="col-md-6 control-label">
                  <span class="pull-left"><?=_("- 정률 : 입력된 백분율만큼 할인/적립 합니다.") ?></span>
               </label>              
            </div>
            <div class="form-group coupon_way" coupon_way="fdate">
               <label class="col-md-2 control-label"><?=_("정액회원 연장 기간") ?></label>
               <div class="col-md-4 form-inline">
                  <select class="form-control" name="coupon_fix_month">
                  <? for($i=0; $i < 13; $i++) { ?>      
                    <option value="<?=$i ?>"  <?=$checked[fix_extension_date_month][$i] ?>><?=$i ?></option>
                  <? } ?>
                  </select><?=_("개월")?>
                
                  <select class="form-control" name="coupon_fix_day">      
                  <? for($i=0; $i < 32; $i++) { ?>      
                    <option value="<?=$i ?>" <?=$checked[fix_extension_date_day][$i] ?>><?=$i ?></option>
                  <? } ?>
                  </select><?=_("일")?>
               </div> 
               <label class="col-md-6 control-label">
                  <span class="pull-left"><?=_("- 기간연장 : 기간 연장 기간만큼 정액회원으로 사용 가능합니다.") ?></span>
			   </label>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("발급/사용제한 설정") ?></h4>
         </div>
         <div class="panel-body panel-form">
            <div class="form-group" id="pnl_visible1">
               <label class="col-md-2 control-label"><?=_("발급방식") ?></label>
               <div class="col-md-10">
                <? if ($_GET[kind]=="on"){ ?>
                    <input type="radio" name="coupon_issue_system" value="admin" <?=$checked[coupon_issue_system][admin] ?>/><?=_("관리자지급")?>
                    <input type="radio" name="coupon_issue_system" value="download_write" <?=$checked[coupon_issue_system][download_write]?>/><?=_("회원입력다운로드")?>                    
                    <input type="radio" name="coupon_issue_system" value="mem_register" <?=$checked[coupon_issue_system][mem_register]?>/><?=_("회원가입시")?>                    
                <? } ?>
                    <input type="radio" name="coupon_issue_system" value="download" <?=$checked[coupon_issue_system][download] ?>/><?=_("회원다운로드")?>
               </div>
            </div>
            
            <div class="form-group" id="pnl_visible2">
               <label class="col-md-2 control-label"><?=_("발급기간") ?></label>
               <div class="col-md-3 form-inline">
                  <div class="input-group input-daterange">
                     <input type="text" class="form-control" name="coupon_issue_sdate" placeholder="Date Start" value="<?=$data[coupon_issue_sdate] ?>"/>
                     <span class="input-group-addon"> ~ </span>
                     <input type="text" class="form-control" name="coupon_issue_edate" placeholder="Date Start" value="<?=$data[coupon_issue_edate] ?>"/>
                  </div>
               </div>
               <label class="col-md-7 control-label">
               	   <span class="pull-left"><input type="checkbox" name="coupon_issue_unlimit" value="1" onclick="$('[name=coupon_issue_sdate]').attr('disabled',this.checked);$('[name=coupon_issue_edate]').attr('disabled',this.checked);" <?=$checked[coupon_issue_unlimit][1] ?>> <?=_("무제한")?></span>
               </label>
            </div>
            
            <div class="form-group" id="pnl_visible3">
               <label class="col-md-2 control-label"><?=_("발급제한") ?></label>
               <div class="col-md-4">
                  <? if ($_GET[kind]=="on"){ ?>
                     <input type="radio" name="coupon_issue_ea_limit" value="0" onclick="setting_form('coupon_issue_ea_limit','0')" <?=$checked[coupon_issue_ea_limit][0] ?>/><?=_("무한")?>
                  <? } ?>
                  <input type="radio" name="coupon_issue_ea_limit" value="1" onclick="setting_form('coupon_issue_ea_limit','1')" <?=$checked[coupon_issue_ea_limit][1] ?>/><?=_("유한")?> 
               </div>
               <label class="col-md-6 control-label">

			   </label>
            </div>
            <div class="form-group" id="pnl_visible4">
               <label class="col-md-2 control-label"><?=_("발급수량") ?></label>
               <div class="col-md-4 form-inline">
                  <? if ($_GET[kind]=="on"){ ?>
                     <span class="coupon_issue_ea_limit" coupon_issue_ea_limit="0"/><?=_("무제한")?></span>
                  <? } ?>
                  
                  <? if (!$data[coupon_off_issue]){ ?>
                     <span class="coupon_issue_ea_limit" coupon_issue_ea_limit="1"><input type="text" class="form-control" name="coupon_issue_ea" pt="_pt_numplus" size="10" value="<?=$data[coupon_issue_ea] ?>"/> <?=_("장")?></span>
                  <? } else { ?>
                     <b><?=$data[coupon_issue_ea] ?></b> <?=_("장")?>
                     <input type="hidden" name="coupon_issue_ea" value="<?=$data[coupon_issue_ea] ?>"/>
                     <div class="desc red"><?=_("이미 발행이 완료된 쿠폰은 수량수정이 불가능 합니다.")?></div>
                  <? } ?>
               </div>
               <label class="col-md-6 control-label">
                  <span class="pull-left"></span>
			   </label>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("사용제한 주문금액") ?></label>
               <div class="col-md-3 form-inline">
                   <input type="text" class="form-control" name="coupon_min_ordprice" pt="_pt_numplus" value="<?=$data[coupon_min_ordprice] ?>" size="10"/> <?=_("원")?>
               </div>
               <label class="col-md-7 control-label">
                  <span class="pull-left"><?=_("- 주문총상품가(할인전가)가 입력된 금액 이상일 경우 사용이 가능합니다.") ?></span>
			   </label>
            </div>

            <div class="form-group" id="pnl_visible5">
               <label class="col-md-2 control-label"><?=_("사용기간제도") ?></label>
               <div class="col-md-4">
                  <input type="radio" name="coupon_period_system" value="date" onclick="setting_form('coupon_period_system','date')" <?=$checked[coupon_period_system]['date'] ?>/><?=_("기간제")?>
                  <input type="radio" name="coupon_period_system" value="deadline" onclick="setting_form('coupon_period_system','deadline')" <?=$checked[coupon_period_system][deadline] ?>/><?=_("마감제")?>
                  <input type="radio" name="coupon_period_system" value="deadline_date" onclick="setting_form('coupon_period_system','deadline_date')" <?=$checked[coupon_period_system][deadline_date] ?>/><?=_("마감일제")?>
               </div>
               <label class="col-md-6 control-label">
                  <span class="pull-left"></span>
			   </label>
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("사용기간") ?></label>
               <div class="col-md-4 form-inline">
                  <div class="input-group input-daterange">
                     <input type="text" class="form-control" name="coupon_period_sdate" placeholder="Date Start" value="<?=$data[coupon_period_sdate] ?>"/>
                     <span class="input-group-addon"> ~ </span>
                     <input type="text" class="form-control" name="coupon_period_edate" placeholder="Date End" value="<?=$data[coupon_period_edate] ?>"/>
                  </div>
               </div>
               <label class="col-md-6 control-label">
                  <span class="pull-left"><?=_("- 입력된 기간내에 사용이 가능합니다.") ?></span>
			   </label>
            </div>
            <div class="form-group" id="pnl_visible6">
               <label class="col-md-2 control-label"><?=_("사용마감") ?></label>
               <div class="col-md-10 form-inline">
                   <?=_("발급일(다운로드)로 부터")?> <input type="text" class="form-control" name="coupon_period_deadline" size="3" class="calendar" value="<?=$data[coupon_period_deadline] ?>"/> <?=_("일 이내에 사용가능이 가능합니다.")?>
               </div>
            </div>
            <div class="form-group" id="pnl_visible7">
               <label class="col-md-2 control-label"><?=_("사용마감일") ?></label>
               <div class="col-md-10 form-inline">
                   <?=_("발급(다운로드)일")?> ~ <input type="text" class="form-control" name="coupon_period_deadline_date" size="10" class="calendar" value="<?=$data[coupon_period_deadline_date] ?>"/> <?=_("까지 사용이 가능합니다.")?>
               </div>
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("적용대상") ?></label>
               <div class="col-md-4">
                  <input type="radio" name="coupon_range" onclick="setting_form('coupon_range','all')" value="all" <?=$checked[coupon_range][all] ?>/><?=_("전상품")?>
                  <input type="radio" name="coupon_range" onclick="setting_form('coupon_range','category')" value="category" <?=$checked[coupon_range][category] ?>/><?=_("분류")?>
                  <input type="radio" name="coupon_range" onclick="setting_form('coupon_range','goods')" value="goods" <?=$checked[coupon_range][goods] ?>/><?=_("개별상품")?>
               </div>
               <label class="col-md-6 control-label">
                  <span class="pull-left"></span>
			   </label>
            </div>
            <div class="form-group coupon_range" coupon_range="category">
               <label class="col-md-2 control-label"><?=_("적용카테고리") ?></label>
               <div class="col-md-6" id="catno">
                  <select name="catno[]" size="5"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("1차 분류 선택")?></option></select>
                  <select name="catno[]" size="5"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("2차 분류 선택")?></option></select>
                  <select name="catno[]" size="5"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("3차 분류 선택")?></option></select>
                  <select name="catno[]" size="5"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("4차 분류 선택")?></option></select>
               </div>
               <script src="../../js/category.js"></script>
               <script>var catno = new category('catno', '');</script>

               <div class="col-md-2">
                  <button type="button" class="btn btn-info btn-xs m-r-5" onclick="set_catno()"><?=_("분류등록") ?></button>
               </div>
               <div class="col-md-2" id="coupon_catno">
                   <? foreach ($category as $k=>$v){ ?>
                   <div class='copuon_catno'>
                       <span class="btn btn-danger btn-icon btn-circle btn-xs" onclick='$(this).parent().remove();'><i class="fa fa-times"></i></span>
                       <span class='absmiddle'><?=$v ?></span><input type='hidden' name='coupon_catno[]' value='<?=$k ?>'/>
                   </div>
                   <? } ?>
               </div>
            </div>

            <div class="form-group coupon_range" coupon_range="goods">
               <label class="col-md-2 control-label"><?=_("적용상품") ?><br>
               	  <button type="button" class="btn btn-info btn-xs m-r-5" style="margin-right:-1px !important;" onclick="popupLayer('coupon_form_popup.php?no=<?=$_GET[coupon_code] ?>')"><?=_("상품연결") ?></button>
               </label>
               <div class="col-md-4" id="obj_rels">
               	  <ul>
               	  	 <? if ($link_goods) {
                        foreach($link_goods as $k=>$v) { ?>
                           <li>
                           	  <?=goodsListImg($v[goodsno], "50", "border:1px solid #CCCCCC", $cid) ?>(<?=$v[goodsno] ?>)<?=$v[goodsnm] ?>
                           	  <input type="hidden" name="r_goodsno[][]" value="<?=$v[goodsno] ?>">
                           </li>
                     <? }} ?>
                  </ul>
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

<script>
jQuery(function() {
	$("#obj_rels ul").sortable();
}); 
</script>

<script>
jQuery(document).ready(function() {

<?
if ($data) {
//수정모드에서 쿠폰 타입별 화면 선택을 처리하자..
	if ($data[coupon_type] == "sale_code")	echo "setting_coupon_way('4');";
	else if ($data[coupon_type] == "discount" || $data[coupon_type] == "saving")	echo "setting_coupon_way('1');";
	else if ($data[coupon_type] == "coupon_money" || $data[coupon_type] == "point_save")	echo "setting_coupon_way('3');";
	else if ($data[coupon_type] == "fix_date")	echo "setting_coupon_way('2');";
?>

	setting_form('coupon_way','<?=$data[coupon_way]?>');
	$('#coupon_way_<?=$data[coupon_way]?>').attr("checked",true);
	
  setting_form('coupon_period_system','<?=$data[coupon_period_system] ?>');
	setting_form('coupon_issue_ea_limit','<?=$data[coupon_issue_ea_limit] ?>');
	setting_form('coupon_range','<?=$data[coupon_range] ?>');
	
	if (<?=$data[coupon_issue_unlimit] + 0 ?> == 1) {
		$("[name=coupon_issue_sdate]").attr('disabled', true);
		$("[name=coupon_issue_edate]").attr('disabled', true);
	} else {
		$("[name=coupon_issue_sdate]").attr('disabled', false);
		$("[name=coupon_issue_edate]").attr('disabled', false);
	}

<?	}	?>
});

function setting_form(classname,way) {
    $("."+classname+"["+classname+"!="+way+"]").hide();
    $("input","."+classname+"["+classname+"!="+way+"]").attr("disabled",true);
    $("."+classname+"["+classname+"="+way+"]").show();
    $("input","."+classname+"["+classname+"="+way+"]").attr("disabled",false);
}

//정액 회원 기간 선택하기 설정    20131204  chunter
function setting_coupon_way(mode){  
  if (mode == "2")
  {    
    $("#coupon_way_price").attr('disabled',true);
    $('#coupon_way_rate').attr('disabled',true);  
    $('#coupon_way_fdate').attr("disabled",false);
    $('#coupon_way_fdate').attr("checked",true);
    setting_form('coupon_way','fdate');
  } else if(mode == "3") {
    $('#coupon_way_rate').attr("disabled",true);  
    $('#coupon_way_fdate').attr('disabled',true);
    $('#coupon_way_price').attr("disabled",false);
    $('#coupon_way_price').attr("checked",true);
    setting_form('coupon_way','price');    
  } else {    
    $('#coupon_way_rate').attr("disabled",false);  
    $('#coupon_way_fdate').attr('disabled',true);
    $('#coupon_way_price').attr("disabled",false);
    $('#coupon_way_price').attr("checked",true);
    setting_form('coupon_way','price');
  }
  
  if (mode == "4")
  {
  	//$('#pnl_visible1').hide(300);
  	//$('#pnl_visible2').hide(300);
  	//$('#pnl_visible3').hide(300);
  	//$('#pnl_visible4').hide(300);
  	//$('#pnl_visible5').hide(300);
  	//$('#pnl_visible6').hide(300);
  	//$('#pnl_visible7').hide(300);  	
  } else {
  	$('#pnl_visible1').show(300);
  	$('#pnl_visible2').show(300);
  	$('#pnl_visible3').show(300);
  	$('#pnl_visible4').show(300);
  	$('#pnl_visible5').show(300);
  	$('#pnl_visible6').show(300);
  	$('#pnl_visible7').show(300);
  	
  }
}

function set_catno(){
   var select = $j("select","#catno");
   var val;
   var str = [];
   var i = 0;
   select.each(function() {
      if ($j(this).val()) {
         val = $j(this).val();
         var idx = this.selectedIndex;
         str[i] = $j("option:eq("+idx+")",this).text();
      }
      i++;
   });
    
   if (!val) return;
   if ($j("input[value="+val+"]",".copuon_catno").length) return;

   str = str.join(" > ");
   var div = document.createElement("div");
   $j(div).html("<div class='copuon_catno'><span class=\"btn btn-danger btn-icon btn-circle btn-xs\" onclick='$(this).parent().remove();'><i class=\"fa fa-times\"></i></span> <span class='absmiddle'>"+str+"</span><input type='hidden' name='coupon_catno[]' value='"+val+"'/></div>");
   $j(div).appendTo("#coupon_catno");
}

function chkCode(obj){
   var code = obj.value;
   if (!code){
      $j("#coupon_code_div").html('<?=_("쿠폰코드를 입력해주세요.")?>');
      $j("#chk_code").val('');
   } else {
      if (!_pattern(obj)){
         $j("#coupon_code_div").html('<?=_("쿠폰코드의 입력형식이 올바르지 않습니다.")?>');
         $j("#chk_code").val('');
      } else {
         $j.ajax({
            type: "POST",
            url: "indb.php",
            data: "mode=chk_coupon_code&coupon_code=" + code,
            success: function(ret){
               if (ret=="duplicate"){
                  $j("#coupon_code_div").html('<?=_("이미 사용중인 쿠폰코드 입니다.")?>');
                  $j("#chk_code").val('');
               } else {
                  $j("#coupon_code_div").html('<?=_("사용가능한 쿠폰코드 입니다.")?>');
                  $j("#chk_code").val('1');
               }
            }
         });
      }
   }
}
</script>

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