<?

include "../_header.php";
include "../_left_menu.php";

$m_board = new M_board();
$m_goods = new M_goods();
$r_bottommenu = array("edking_best"=>"베스트수정","edking_emoney"=>"적립금발급","edking_delete"=>"일괄삭제");

//몰 카테고리 분류
$cate_data = $m_goods->getCategoryList($cid);
$ca_list = makeCategorySelectOptionTag($cate_data);

$addWhere = "where cid='$cid'";
if (is_array($_POST[catno])) {
	list($_POST[catno]) = array_slice(array_notnull($_POST[catno]), -1);
	$addWhere .= " and catno like '$_POST[catno]%'";
}
if ($_POST[sword]) $addWhere .= " and goodsnm like '%$_POST[sword]%'";

$orderby = "order by no desc";

$limit = "limit 10";

$tableName = "exm_edking";

$list = $m_board->getCustomerService($cid, $tableName, $addWhere, $orderby, $limit);
$list_cnt = $m_board->getCustomerService($cid, $tableName, $addWhere);
$totalCnt = count($list_cnt);

$postData = base64_encode(json_encode($_POST));

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
         <?=_("편집왕리스트")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("편집왕리스트")?> <small><?=_("편집왕리스트 관리")?>.</small></h1>

   <div class="row">
      <div class="col-md-12">
		   <div class="panel panel-inverse"> 
		      <div class="panel-heading">
		         <h4 class="panel-title"><?=_("편집왕리스트")?></h4>
		      </div>
		      
		      <div class="panel-body panel-form">
		         <form class="form-horizontal form-bordered" method="post" action="edking_list.php">
			         <div class="form-group">
			         	<label class="col-md-1 control-label"><?=_("카테고리")?></label>
			         	<div class="col-md-10 form-inline">
			         		<select name="catno[]" class="form-control">
				               <option value="">+ <?=_("분류 선택")?></option><?=conv_selected_option($ca_list, $_POST[catno])?>
				            </select>
			         	</div>
			         </div>
			         
			         <div class="form-group">
			         	<label class="col-md-1 control-label"><?=_("상품명")?></label>
			         	<div class="col-md-10 form-inline">
			         		<input type="text" class="form-control" name="sword" value="<?=$_POST[sword]?>" size="40">
			         	</div>
			         	<div class="col-md-1">
			         		<button type="submit" class="btn btn-sm btn-success"><?=_("조 회")?></button>
			         	</div>
			         </div>
			     </form>
		         
		         <div class="panel-body">
		         	<form name="form1" method="post" action="indb.php">
		         		<input type="hidden" name="mode">
			         	<div class="table-responsive">
			         		<table id="data-table" class="table table-striped table-bordered">
			         			<thead>
			         				<tr>
			         					<th><a href="javascript:chkBox('chk[]','rev')"><?=_("선택")?></a></th>
			         					<th><?=_("번호")?></th>
			         					<th><?=_("이미지")?></th>
			         					<th><?=_("주문번호")?></th>
			         					<th><?=_("주문자")?></th>
			         					<th><?=_("조회수")?></th>
			         					<th><?=_("추천수")?></th>
			         					<th><?=_("베스트")?></th>
			         					<th><?=_("적립금")?></th>
			         					<th><?=_("등록일자")?></th>
			         					<th><?=_("삭제요청")?></th>
			         				</tr>
			         			</thead>
			         			<tbody>
			         				<? if($list) {
			         				 	foreach ($list as $key => $value) {
			         				 	   if (!$value[chk_yn]) $value[chk_yn] = "N";
										   if (!$value[del_ok]) $value[del_ok] = "N"; ?>
			         				 	<tr>
			         				 		<td><input type="checkbox" name="chk[]" value="<?=$value[no]?>"></td>
			         				 		<td><input type="hidden" name="no" value="<?=$value[no]?>"><?=$key+1?></td>
			         				 		<td><?=goodsListImg($value[goodsno], 50, 50)?></td>
			         				 		<td>
			         				 			<div>주문번호 : <?=$value[payno]?>_<?=$value[ordno]?>_<?=$value[ordseq]?></div>
			         				 			<div>상품명 : <b class="red">[<?=$value[goodsno]?>]</b> <?=$value[goodsnm]?></div>
			         				 		</td>
			         				 		<td>
			         				 			<? if ($value[mid]) { ?>
			         				 			<div><a href="javascript:;" onclick="popup('../member/member_detail_popup.php?mode=member_modify&mid=<?=$value[mid]?>',1100,800)"><?=$value[name]?></a></div>(<?=$value[mid]?>)
			         				 			<? } else { ?>
			         				 			<div><?=$value[name]?></div>(비회원)
			         				 			<? } ?>
			         				 		</td>
			         				 		<td><?=number_format($value[hit])?></td>
			         				 		<td><?=number_format($value[comment])?></td>
			         				 		<td><b class="red"><?=$value[chk_yn]?></b></td>
			         				 		<td><?=number_format($value[emoney])?></td>
			         				 		<td><?=substr($value[regdt], 0, 10)?></td>
			         				 		<td><b class="notice"><?=$value[del_ok]?></b></td>
			                            </tr>
			                        <? }} ?>
			                    </tbody>
			         		</table>
			         	</div>
		         	</form>
		         </div>
		      </div>
		   </div>
      </div>
   </div>
   
   <div class="panel-body panel-form">
   	  <div class="form-group">
   	  	  <div class="col-md-12 form-inline">
   	  	  	 <ul class="nav nav-tabs">
   	  	  	 	<? foreach ($r_bottommenu as $k=>$v) { ?>
   	  	  	 		<li class=""><a href="#default-tab-<?=$k?>" data-toggle="tab"><?=$v?></a></li>
   	  	  	 	<? } ?>
   	  	  	 </ul>
   	  	  	 <div class="tab-content">
   	  	  	 	<div class="m-t-10">
   	  	  	 		<input type="radio" class="radio-inline" name="range" checked> 선택항목
   	  	  	 		<input type="radio" class="radio-inline" name="range"> 검색항목
   	  	  	 		<p><div><span class="warning">[주의]</span> 변경시 복구가 불가능하므로 주의하시기 바랍니다.</div>
   	  	  	 	</div>
   	  	  	 	
   	  	  	 	<? foreach ($r_bottommenu as $k=>$v) { ?>
   	  	  	 		<div class="tab-pane" id="default-tab-<?=$k?>">
   	  	  	 			<form method="post" action="indb.php" onsubmit="return submitContents(this);">
   	  	  	 				<input type="hidden" name="mode" value="bottom_<?=$k?>">
   	  	  	 				<input type="hidden" name="range">
   	  	  	 				<input type="hidden" name="bno">
   	  	  	 				
   	  	  	 				<? if ($k == "edking_emoney") { ?>
        					<p><div>적립금액 <input type="text" class="form-control" name="emoney" required size="8" maxlength="7" type2="number">원을 적립합니다.</div>
        					<div><span class="notice">[설명]</span> 적립금을 삭감하실때는 적립금액에 - 를 넣으시면 됩니다.</div>
        					<? } ?>
   	  	  	 				
	   	  	  	 			<div class="text-right m-b-0">
	   	  	  	 				<button type="submit" class="btn btn-sm btn-success"><?=_("확인")?></button>
	   	  	  	 			</div>
   	  	  	 			</form>
   	  	  	 		</div>
   	  	  	 	<? } ?>
   	  	  	 </div>
		 </div>
      </div>
   </div>
</div>

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>

<script type="text/javascript">
$j(function() {
	$j(".nav-tabs>li:first").addClass("active");
	$j(".tab-pane:first").addClass("active in");
	
	$j('input[type2=number]').css('ime-mode', 'disabled').keypress(function(event) {
		if (event.which && event.which != 45 && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	});
});

/* Table initialisation */
$(document).ready(function() {
	$('#data-table').dataTable({
		"sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
		"sPaginationType" : "bootstrap",
		"aaSorting" : [[0, "desc"]],
		"bFilter" : false,
		"aLengthMenu": [10, 25, 50],
		"oLanguage" : {
			"sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
		},
		"aoColumns": [
		{ "bSortable": false },
		{ "bSortable": true },
		{ "bSortable": false },
		{ "bSortable": false },
		{ "bSortable": false },
		{ "bSortable": true },
		{ "bSortable": true },
		{ "bSortable": true },
		{ "bSortable": true },
		{ "bSortable": true },
		{ "bSortable": true },
		],
		"processing": true,
		"serverSide": true,
		"deferLoading": <?=$totalCnt?>,
		"bAutoWidth": false,
		"ajax": $.fn.dataTable.pipeline({
			url: 'edking_list_page.php?postData=<?=$postData?>',
			pages: 5 //number of pages to cache
		})
	});
});

function submitContents(f) {
	if (!form_chk(f)) {
        return false;
    }

	var tmp = [];
	f.range.value = (document.getElementsByName('range')[0].checked) ? 'selmember' : 'allmember';

	if (f.range.value == 'allmember') {
		var no = document.getElementsByName('no');
		
		for (var i=0; i<no.length; i++) {
			tmp[i] = no[i].value;
		}
		
		if (tmp[0]) {
			f.bno.value = tmp;
		} else {
			alert("검색된 항목이 없습니다.", -1);
			return false;
		}
	} else if (f.range.value == 'selmember') {
		var c = document.getElementsByName('chk[]');
		
		for (var i=0;i<c.length;i++) {
			if (c[i].checked) tmp[tmp.length] = c[i].value;
		}
		
		if (tmp[0]) {
			f.bno.value = tmp;
		} else {
			alert('선택된 항목이 없습니다.', -1);
			return false;
		}
	}
	
	if (!confirm("수정 및 삭제된 내용은 복구가 불가능합니다.\n계속하시겠습니까?")) {
		return false;
	}
}
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>