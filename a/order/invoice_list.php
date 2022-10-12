<?

include "../_header.php";
include "../_left_menu.php";

$m_order = new M_order();

### $r_step 가공
foreach ($r_step as $k=>$v) {
	if (($k<2 || $k>5) && $k!=92) unset($r_step[$k]);
}

### 출고처 추출
$r_rid = get_release();

if (!$_POST[start_orddt] && $_POST[start_ordtime]) unset($_POST[start_ordtime]);
if (!$_POST[end_orddt] && $_POST[end_ordtime]) unset($_POST[end_ordtime]);
if (!$_POST[start_paydt] && $_POST[start_paytime]) unset($_POST[start_paytime]);
if (!$_POST[end_paydt] && $_POST[end_paytime]) unset($_POST[end_paytime]);

$addWhere = "where a.cid='$cid'";

if ($_POST[start_orddt]) {
	$start_orddt = ($_POST[start_ordtime]) ? "{$_POST[start_orddt]} {$_POST[start_ordtime]}:00:00" : "{$_POST[start_orddt]} 00:00:00";
	$addWhere .= " and a.orddt > '{$start_orddt}'";
}
if ($_POST[end_orddt]) {
	$end_orddt = ($_POST[end_ordtime]) ? "{$_POST[end_orddt]} {$_POST[end_ordtime]}:59:59" : "{$_POST[end_orddt]} 23:59:59";
	$addWhere .= " and a.orddt <= '{$end_orddt}'";
}
if ($_POST[start_paydt]) {
	$start_paydt = ($_POST[start_paytime]) ? "{$_POST[start_paydt]} {$_POST[start_paytime]}:00:00" : "{$_POST[start_paydt]} 00:00:00";
	$addWhere .= " and (a.paydt > '{$start_paydt}' or a.confirmdt > '{$start_paydt}')";
}
if ($_POST[end_paydt]) {
	$end_paydt = ($_POST[end_paytime]) ? "{$_POST[end_paydt]} {$_POST[end_paytime]}:59:59" : "{$_POST[end_paydt]} 23:59:59";
	$addWhere .= " and (a.paydt <= '{$end_paydt}' or a.confirmdt <= '{$end_paydt}')";
}

if ($_POST[step]) {
	$step = implode(",", $_POST[step]);
	$addWhere .= " and c.itemstep in ($step)"; 
	
	foreach ($_POST[step] as $v) {
		$checked[step][$v] = "checked";
	}
} else {
	$addWhere .= " and c.itemstep in (2,92,3,4,5)";
}

if ($_POST[order_shiptype]) {
	$order_shiptype = implode(",", $_POST[order_shiptype]);
	if (in_array(0, $_POST[order_shiptype])) $addWhere .= " and (b.order_shiptype in ($order_shiptype) or b.order_shiptype is null or b.order_shiptype='')";
	else $addWhere .= " and b.order_shiptype in ($order_shiptype)";
	
	foreach ($_POST[order_shiptype] as $v) {
		$checked[order_shiptype][$v] = "checked";
	}
}

if ($_POST[release]) {
	$addWhere .= " and b.rid = '$_POST[release]'";
}

if ($_POST[goods]) {
	$addWhere .= " and (c.goodsno='$_POST[goods]' or c.goodsnm like '%$_POST[goods]%')";
}

if ($_POST[sword]) {
	$addWhere .= " and concat(a.payno,a.orderer_name,a.payer_name,a.receiver_name) like '%$_POST[sword]%'";
}

$orderby = "order by a.orddt desc";

$query = $m_order->getOrdItemInfoList($cid, $addWhere, $orderby, "", true);

$selected[release][$_POST[release]] = "selected";
$selected[start_ordtime][$_POST[start_ordtime]] = $selected[end_ordtime][$_POST[end_ordtime]] = "selected";
$selected[start_paytime][$_POST[start_paytime]] = $selected[end_paytime][$_POST[end_paytime]] = "selected";

$postData = base64_encode(json_encode($_POST));

### form 전송 취약점 개선 20160128 by kdk
$_query = base64_encode(urlencode($query));
$url_query = "/a/order/indb.php?query=".$_query;
$pod_signed = signatureData($cid, $url_query);
$pod_expired = expiresData("20");
### form 전송 취약점 개선 20160128 by kdk

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
         <?=_("송장출력리스트")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("송장출력리스트")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("송장출력리스트")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="POST">
               	  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("주문일")?></label>
                     <div class="col-md-7">
                        <div class="input-group input-daterange">
                           <input type="text" class="form-control" name="start_orddt" placeholder="Date Start" value="<?=$_POST[start_orddt]?>" />
                           <span class="input-group-addon">
                           	   <select name="start_ordtime">
								   <option value=""><?=_("선택")?>
								   <? foreach(range(0,23) as $v) { $time = sprintf("%02d",$v); ?>
								   <option value="<?=$time?>" <?=$selected[start_ordtime][$time]?>><?=$time?>
								   <? } ?>
							   </select> <?=_("시")?> <b>00</b><?=_("분")?> <b>00</b><?=_("초")?>
                           </span>
                           <span class="input-group-addon" style="border-left:1px solid white !important;"> ~ </span>
                           <input type="text" class="form-control" name="end_orddt" placeholder="Date End" value="<?=$_POST[end_orddt]?>" />
                           <span class="input-group-addon">
                           	   <select name="end_ordtime">
								   <option value=""><?=_("선택")?>
								   <? foreach(range(0,23) as $v) { $time = sprintf("%02d",$v); ?>
								   <option value="<?=$time?>" <?=$selected[end_ordtime][$time]?>><?=$time?>
								   <? } ?>
							   </select> <?=_("시")?> <b>59</b><?=_("분")?> <b>59</b><?=_("초")?>
                           </span>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <button type="button" class="btn btn-sm btn-<?=$button_color[today]?>" onclick="regdtOnlyOne('today','start_orddt','today'); regdtOnlyOne('today','end_orddt');"><?=_("오늘")?></button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[tdays]?>" onclick="regdtOnlyOne('tdays','start_orddt','tdays'); regdtOnlyOne('today','end_orddt');"><?=_("3일")?></button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[week]?>" onclick="regdtOnlyOne('week','start_orddt','week'); regdtOnlyOne('today','end_orddt');"><?=_("1주일")?></button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[month]?>" onclick="regdtOnlyOne('month','start_orddt','month'); regdtOnlyOne('today','end_orddt');"><?=_("1달")?></button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[all]?>" onclick="regdtOnlyOne('all','start_orddt','all'); regdtOnlyOne('today','end_orddt');"><?=_("전체")?></button>
                     </div>
                  </div>
               	
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("입금/승인일")?></label>
                     <div class="col-md-7">
                        <div class="input-group input-daterange">
                           <input type="text" class="form-control" name="start_paydt" placeholder="Date Start" value="<?=$_POST[start_paydt]?>" />
                           <span class="input-group-addon">
                           	   <select name="start_paytime">
								   <option value=""><?=_("선택")?>
								   <? foreach(range(0,23) as $v) { $time = sprintf("%02d",$v); ?>
								   <option value="<?=$time?>" <?=$selected[start_paytime][$time]?>><?=$time?>
								   <? } ?>
							   </select> <?=_("시")?> <b>00</b><?=_("분")?> <b>00</b><?=_("초")?>
                           </span>
                           <span class="input-group-addon" style="border-left:1px solid white !important;"> ~ </span>
                           <input type="text" class="form-control" name="end_paydt" placeholder="Date End" value="<?=$_POST[end_paydt]?>" />
                           <span class="input-group-addon">
                           	   <select name="end_paytime">
								   <option value=""><?=_("선택")?>
								   <? foreach(range(0,23) as $v) { $time = sprintf("%02d",$v); ?>
								   <option value="<?=$time?>" <?=$selected[end_paytime][$time]?>><?=$time?>
								   <? } ?>
							   </select> <?=_("시")?> <b>59</b><?=_("분")?> <b>59</b><?=_("초")?>
                           </span>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <button type="button" class="btn btn-sm btn-<?=$button_color[today]?>" onclick="regdtOnlyOne('today','start_paydt','today'); regdtOnlyOne('today','end_paydt');"><?=_("오늘")?></button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[tdays]?>" onclick="regdtOnlyOne('tdays','start_paydt','tdays'); regdtOnlyOne('today','end_paydt');"><?=_("3일")?></button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[week]?>" onclick="regdtOnlyOne('week','start_paydt','week'); regdtOnlyOne('today','end_paydt');"><?=_("1주일")?></button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[month]?>" onclick="regdtOnlyOne('month','start_paydt','month'); regdtOnlyOne('today','end_paydt');"><?=_("1달")?></button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[all]?>" onclick="regdtOnlyOne('all','start_paydt','all'); regdtOnlyOne('today','end_paydt');"><?=_("전체")?></button>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("주문상태")?></label>
                     <div class="col-md-10 form-inline">
                     	<? foreach ($r_step as $k=>$v) { ?>
						<input type="checkbox" name="step[]" class="checkbox-inline" value="<?=$k?>" <?=$checked[step][$k]?>> <?=$v?>
						<? } ?>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("배송방식")?></label>
                     <div class="col-md-10 form-inline">
                        <? foreach($r_shiptype as $k=>$v) { ?>
				            <input type="checkbox" name="order_shiptype[]" class="checkbox-inline" value="<?=$k?>" <?=$checked[order_shiptype][$k]?>> <?=$v?>
				        <? } ?>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("출고처")?></label>
                     <div class="col-md-10 form-inline">
                        <select name="release" class="form-control">
							<option value=""><?=_("전체")?></option>
							<? foreach ($r_rid as $k=>$v) { ?>
							<option value="<?=$k?>" <?=$selected[release][$k]?>><?=$v?></option>
							<? } ?>
						</select>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("주문상품")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" name="goods" class="form-control" value="<?=$_POST[goods]?>" size="40" placeholder='<?=_("상품번호,상품명을 입력해주세요.")?>'>
                     </div>
                  </div>
                  
                  <div class="form-group">
                  	 <label class="col-md-1 control-label"><?=_("검색")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" name="sword" class="form-control" value="<?=$_POST[sword]?>" size="100" placeholder='<?=_("결제번호,주문자명,입금자명,받는자명을 입력해주세요.")?>'>
                     </div>
                  </div>

                  <div class="form-group">
                    <label class="col-md-1 control-label"></label>
                    <div class="col-md-10">
                        <button type="submit" class="btn btn-sm btn-success"><?=_("검색")?></button>
                        <button type="button" class="btn btn-sm btn-default" onclick="dnExcel()"><?=_("엑셀저장")?></button>
                    </div>
                  </div>
                  
                  <div class="form-group">
                     <div class="col-md-12 form-inline">
                        <table id="data-table" class="table table-striped table-bordered">
                           <thead>
                              <tr>
                                 <th><?=_("결제번호")?><br><?=_("결제방식")?></th>
                                 <th><?=_("주문일")?><br><?=_("입금/승인일")?></th>
                                 <th><?=_("주문자명")?></th>
                                 <th><?=_("이미지")?></th>
                                 <th><?=_("주문상품")?></th>
                                 <th><?=_("수량")?></th>
                                 <th><?=_("판매가")?></th>
                                 <th><?=_("출고처")?><br><?=_("주문상태")?></th>
                              </tr>
                           </thead>
                        </table>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>
<script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

<!-- ### 엑셀데이타추출용쿼리문 -->
<input type="hidden" name="_query" value="<?=$_query?>&pod_signed=<?=$pod_signed?>&pod_expired=<?=$pod_expired?>">

<script>
function dnExcel() {
	var query = document.getElementsByName("_query")[0].value;
	hidden_frm.location.href = "indb.php?mode=dnExcel&kind=invoice&query=" + query;
}
</script>

<!-- ================== END PAGE LEVEL JS ================== -->
<script>
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
         "sPaginationType" : "bootstrap",
         "bFilter" : false,
         "aLengthMenu": [10, 25, 50, 100],
         "oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
         },
         "aoColumns": [
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         ],
         "processing": false,
         "serverSide": true,
         "bAutoWidth": false,
         "ajax": $.fn.dataTable.pipeline({
            url: 'invoice_list_page.php?postData=<?=$postData?>',
            pages: 5 // number of pages to cache
         })
      });
   });
   
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

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>