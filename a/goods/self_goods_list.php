<?
include "../_header.php";
include "../_left_menu.php";

$m_goods = new M_goods();
$r_nodp = array(_("진열"), _("미진열"));

$r_brand = get_brand();
$r_rid = get_release();

$limit = "limit 0, 10";
$list = $m_goods->getAdminSelfGoodsList($cid, '', '', $limit);
$list_cnt = $m_goods->getAdminSelfGoodsList($cid);
$totalCnt = count($list_cnt);


//몰 카테고리 분류
$cate_data = $m_goods->getCategoryList($cid);
$ca_list = makeCategorySelectOptionTag($cate_data);

$postData = base64_encode(json_encode($_POST));

$checked[state][$_POST[state]] = "checked";
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
         <?=_("자체상품리스트")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("자체상품리스트")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("자체상품리스트")?></h4>
            </div>
          
            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" method="post" action="">
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("분류")?></label>
                     <div class="col-md-2">
                        <div class="col-md-10 form-inline">
                           <select name="catno" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("분류 선택")?></option><?=conv_selected_option($ca_list, $_POST[catno])?></select>
                  		</div>
                     </div>
                     
                     <label class="col-md-1 control-label"><?=_("등록일")?></label>
                     <div class="col-md-3">
                        <div class="input-group input-daterange">
                           <input type="text" class="form-control" name="start_date" placeholder="Date Start" value="<?=$_POST[start_date]?>" />
                           <span class="input-group-addon"> ~ </span>
                           <input type="text" class="form-control" name="end_date" placeholder="Date End" value="<?=$_POST[end_date]?>" />
                        </div>
                     </div>
                     
                     <label class="col-md-1 control-label"><?=_("판매여부")?></label>
                     <div class="col-md-3">
                        <div class="col-md-10 form-inline">
                           <input type="radio" name="state" value="" <?=$checked[state][""]?>> <?=_("전체")?>
                           <?foreach($r_goods_state as $k=>$v){?>
										<input type="radio" name="state" value="<?=$k?>" <?=$checked[state][$k]?>> <?=$v?>
									<?}?>
                        </div>
                     </div>
                     
                     <div class="col-md-1">
                        <button type="submit" class="btn btn-sm btn-success">
                           <?=_("조 회")?>
                        </button>
                     </div>
                  </div>
               </form>
            </div>

            <div class="panel-body">
               <div class="table-responsive">
                  <table id="data-table" class="table table-striped table-bordered">
                     <thead>
                        <tr>
                           <th><a href="javascript:chkBox('chk[]','rev')"><?=_("선택")?></a></th>
                           <th><?=_("번호")?></th>
                           <th><?=_("이미지")?></th>
                           <th><?=_("상품명")?></th>
                           <th><?=_("공급/브랜드")?></th>
                           <th><?=_("가격(적립금)")?></th>
                           <!--<th><?=_("재고")?></th>-->
                           <th><?=_("등록일")?></th>
                           <!--<th><?=_("진열")?></th>-->
                           <th><?=_("옵션 이미지 등록")?></th>
                           <th><?=_("복사")?></th>
                           <th><?=_("수정")?></th>
                           <th><?=_("삭제")?></th>
                        </tr>
                     </thead>
                  </table>
               </div>
            </div>
         </div>

      </div>
   </div>
</div>
<!-- end #content -->

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>
<script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

<script>
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
         "sPaginationType" : "bootstrap",
         "aaSorting" : [[1, "desc"]],
         "bFilter" : true,
         "oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
         },
         "aoColumns": [
         { "bSortable": false },
         { "bSortable": true },
         { "bSortable": false },
         { "bSortable": true },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": true },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         ],
         "processing": false,
         "serverSide": true,
         "bAutoWidth": false,
         "ajax": $.fn.dataTable.pipeline({
            url: './self_goods_list_page.php?postData=<?=$postData?>',
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