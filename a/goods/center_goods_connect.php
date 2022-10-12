<?
include "../_header.php";
include "../_left_menu.php";

$m_goods = new M_goods();

$limit = "limit 0, 10";
$list = $m_goods->getAdminCenterGoodsConnectList($cid, '', '', $limit);

//센터 카테고리 분류
$c_cate_data = $m_goods->getCategoryList($cfg_center[cid]);
$c_ca_list = makeCategorySelectOptionTag($c_cate_data);

//몰 카테고리 분류
$cate_data = $m_goods->getCategoryList($cid);
$ca_list = makeCategorySelectOptionTag($cate_data);

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
         Center <?=_("상품연결")?>
      </li>
   </ol>

   <h1 class="page-header">Center <?=_("상품연결")?></h1>

   <div class="row">
   	<form name="form" class="form-horizontal form-bordered" method="post">
      <div class="col-md-12">      	
         <div class="panel panel-inverse">            
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title">Center <?=_("상품연결")?></h4>
            </div>

            <div class="panel-body panel-form">
               
                  <input type="hidden" name="mode">
                  <input type="hidden" name="cid" value="<?=$cid?>">

                  <div class="form-group">
                     <div class="col-md-12 form-inline">
                        <select class="form-control" name="catno" label='<?=_("카테고리")?>'>
                           <option value="">+ <?=_("분류 선택")?></option><?=$c_ca_list?>
                        </select>
                        <button type="submit" class="btn btn-sm btn-inverse"><?=_("조회하기")?></button>
                     </div>
                  </div>
                  
                  <div class="panel-body">
                     <div class="table-responsive">
                        <table id="data-table" class="table table-striped table-bordered">
                           <thead>
                              <tr>
                                 <th><a href="javascript:chkBox('chk[]','rev')"><?=_("선택")?></a></th>
                                 <th><?=_("상품번호")?></th>
                                 <th><?=_("이미지")?></th>
                                 <th><?=_("상품명")?></th>
                                 <th><?=_("공급/브랜드")?></th>
                                 <th><?=_("가격")?></th>
                                 <th><?=_("등록일")?></th>
                              </tr>
                           </thead>
                        </table>
                     </div>
                  </div>
               
            </div>
         </div>
         
		<div class="panel panel-inverse"> 
    		<div class="panel-heading">
        		<h4 class="panel-title"><?=_("카테고리 연결 설정")?></h4>
      		</div>

         	<div class="panel-body panel-form">
         		
	            <div class="form-group">
	               <label class="col-md-2 control-label"><?=_("적용상품") ?></label>
	               <div class="col-md-4">
						<input type="radio" name="range" checked/> <?=_("선택상품")?>
						<input type="radio" name="range" /> <?=_("검색상품전체")?>
	               </div>
	               <label class="col-md-6 control-label">
	                  <span class="desc gray" style="margin:10px"><span class="warning">[<?=_("주의")?>]</span> <?=_("대량의 상품을 연결할 경우 페이지 로딩이 늦어질수 있으니 기다려 주시기 바랍니다.")?></span>
				   </label>
	            </div>
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품분류")?></label>
                     <div class="col-md-10 form-inline">
                        <select name="catno2[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("1차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[0][catno])?></select>
                        <select name="catno2[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("2차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[1][catno])?></select>
                        <select name="catno2[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("3차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[2][catno])?></select>
                        <select name="catno2[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("4차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[3][catno])?></select>
                     </div>
                  </div>	            
      			
      		</div>
		</div>
                  
		<div class="form-group">
        	<button type="button" class="btn btn-sm btn-danger" onclick="center_goods_connect();"><?=_("연결하기")?></button>
		</div>
         
      </div>
     </form>
   </div>
</div>
<!-- end #content -->

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>

<script>
function center_goods_connect() {

   var fm = document.form;
   var chk;
   //var myArray = new Array();
   var chkNum = 0;
   var c = document.getElementsByName('chk[]');

   for (var i = 0; i < c.length; i++) {
      if (c[i].checked) {
         chk = true;
         //myArray[k] = c[i].value;
         chkNum++;
      }
   }

   if (chk) {
      if(confirm('<?=_("상품을 연결 하시겠습니까?")?>') == true){
         fm.action = "indb.php";
         fm.mode.value = "link_category";
         fm.submit();
      }
   } else
      alert('<?=_("상품을 선택해주세요.")?>');
}
</script>

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
         { "bSortable": false }
         ],
         "processing": false,
         "serverSide": true,
         "bAutoWidth": false,
         "ajax": $.fn.dataTable.pipeline({
            url: './center_goods_connect_page.php?postData=<?=$postData?>',
            pages: 5 // number of pages to cache
         })
      });
   });
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>