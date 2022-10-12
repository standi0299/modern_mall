<?

include "../_header.php";
include "../_left_menu.php";

$m_goods = new M_goods();
$m_order = new M_order();

### $r_step 가공
foreach ($r_step as $k=>$v) {
	if (($k<1 || $k>5) && $k!=91 && $k!=92 && $k!=-9 && $k!=-90) unset($r_step[$k]);
}

//몰 카테고리 분류
$cate_data = $m_goods->getCategoryList($cid);
$ca_list = makeCategorySelectOptionTag($cate_data);

### 출고처 추출
$r_rid = get_release();

$addWhere = "where a.cid='$cid'";

if ($_POST[start_orddt]) $addWhere .= " and a.orddt > '{$_POST[start_orddt]}'";
if ($_POST[end_orddt]) $addWhere .= " and a.orddt < adddate('{$_POST[end_orddt]}',interval 1 day)";
if ($_POST[start_paydt]) $addWhere .= " and (a.paydt > '{$_POST[start_paydt]}' or  a.confirmdt > '{$_POST[start_paydt]}')";
if ($_POST[end_paydt]) $addWhere .= " and (a.paydt < adddate('{$_POST[end_paydt]}',interval 1 day) or a.confirmdt < adddate('{$_POST[end_paydt]}',interval 1 day))";

if ($_POST[step]) {
	$step = implode(",", $_POST[step]);
	$addWhere .= " and c.itemstep in ($step)"; 
	
	foreach ($_POST[step] as $v) {
		$checked[step][$v] = "checked";
	}
} else {
	$addWhere .= " and c.itemstep in (1,2,91,92,3,4,5,-9,-90)";
}

if (is_array($_POST[catno])) {
	list($_POST[catno]) = array_slice(array_notnull($_POST[catno]), -1);
	if (is_numeric($_POST[catno])) $addWhere .= " and c.catno like '$_POST[catno]%'";
}

if ($_POST[release]) {
	$addWhere .= " and b.rid = '$_POST[release]'";
}

if ($_POST[goods]) {
	$addWhere .= " and (c.goodsno='$_POST[goods]' or c.goodsnm like '%$_POST[goods]%')";
}


if ($_POST[sword]) {
	$addWhere .= " and concat(a.payno,a.mid,a.orderer_name) like '%$_POST[sword]%'";
}

$orderby = "order by a.orddt desc";

$query = $m_order->getOrdItemInfoList($cid, $addWhere, $orderby, "", true);

$selected[release][$_POST[release]] = "selected";

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
         <?=_("상품별주문리스트")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("상품별주문리스트")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("상품별주문리스트")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="POST">
               	  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("주문일")?></label>
                     <div class="col-md-5">
                        <div class="input-group input-daterange">
                           <input type="text" class="form-control" name="start_orddt" placeholder="Date Start" value="<?=$_POST[start_orddt]?>" />
                           <span class="input-group-addon"> ~ </span>
                           <input type="text" class="form-control" name="end_orddt" placeholder="Date End" value="<?=$_POST[end_orddt]?>" />
                        </div>
                     </div>
                     <div class="col-md-6">
                        <button type="button" class="btn btn-sm btn-<?=$button_color[today]?>" onclick="regdtOnlyOne('today','start_orddt','today'); regdtOnlyOne('today','end_orddt');"><?=_("오늘")?></button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[tdays]?>" onclick="regdtOnlyOne('tdays','start_orddt','tdays'); regdtOnlyOne('today','end_orddt');"><?=_("3일")?></button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[week]?>" onclick="regdtOnlyOne('week','start_orddt','week'); regdtOnlyOne('today','end_orddt');"><?=_("1주일")?></button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[month]?>" onclick="regdtOnlyOne('month','start_orddt','month'); regdtOnlyOne('today','end_orddt');"><?=_("1달")?></button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[all]?>" onclick="regdtOnlyOne('all','start_orddt','all'); regdtOnlyOne('today','end_orddt');"><?=_("전체")?></button>
                     </div>
                  </div>
               	
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("입금/승인일")?></label>
                     <div class="col-md-5">
                        <div class="input-group input-daterange">
                           <input type="text" class="form-control" name="start_paydt" placeholder="Date Start" value="<?=$_POST[start_paydt]?>" />
                           <span class="input-group-addon"> ~ </span>
                           <input type="text" class="form-control" name="end_paydt" placeholder="Date End" value="<?=$_POST[end_paydt]?>" />
                        </div>
                     </div>
                     <div class="col-md-6">
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
                     <label class="col-md-1 control-label"><?=_("상품분류")?></label>
                     <div class="col-md-10 form-inline">
		         		 <select name="catno[]" class="form-control">
		         			 <option value="">+ <?=_("분류 선택")?></option><?=conv_selected_option($ca_list, $_POST[catno])?>
		         		 </select>
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
                        <input type="text" name="sword" class="form-control" value="<?=$_POST[sword]?>" size="100" placeholder='<?=_("결제번호,아이디,주문자명을 입력해주세요.")?>'>
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
	hidden_frm.location.href = "indb.php?mode=dnExcel&kind=order_item&query=" + query;
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
            url: 'order_item_list_page.php?postData=<?=$postData?>',
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