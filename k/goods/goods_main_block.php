<?
include "../_header.php";
include "../_left_menu.php";

include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

###메인화면 상품 블럭 설정###
//debug($cfg);
$tableName = "md_main_block";
$selectArr = "ID,block_code,display_text,order_by";
$whereArr = array("cid" => "$cid"); //, "id" => "$_GET[id]"
$block_data = SelectListTable($tableName, $selectArr, $whereArr, false, "order by order_by asc", "");
//debug($block_data);
$postData = base64_encode(json_encode($_POST));

$checked[state][$_POST[state]] = "checked";
$checked[isdp][$_POST[isdp]] = "checked";
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
         <?=_("메인 상품 진열관리")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("메인 상품 진열관리")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("메인 분류")?></h4>
            </div>
            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" method="post">
	               <div class="form-group">
	                  <label class="col-md-2 control-label"><?=_("메인분류")?></label>
	                  <div class="col-md-3">
	                  	<select name="block_code" class="form-control" onchange="$('form').submit();">
	                  		<?
	                  		foreach ($r_main_block_mapping as $code) {	                  			
							?>
							<option value="<?=$code?>" <?if($code == $_POST[block_code]) {?>selected<?}?>><?=$r_main_block[$code]?></option>
							<?	  
							}
	                  		?>
	                  		
	                  	</select>	                     
	                  </div>
	                  <div class="col-md-3">
	                     <button type="button" class="btn btn-sm btn-info" onclick="popup('goods_main_block_popup.php',1200,700);"><?=_("메인분류관리")?></button>
	                  </div>
					  <label class="col-md-2 control-label"><?=_("")?></label>
	                  <div class="col-md-2">
	                  </div>
	               </div>
	               <div class="form-group">
	                  <label class="col-md-2 control-label"><?=_("상품검색")?></label>
	                  <div class="col-md-3">
	                     <input type="text" class="form-control" name="sword" value="<?=$_POST[sword]?>" placeholder='<?=_("검색어를 입력하세요.")?>' />
	                  </div>
	                  <label class="col-md-2 control-label"><?=_("")?></label>	                  
	                  <div class="col-md-3">
	                  </div>
	                  <div class="col-md-2">	                     
	                  </div>
	               </div>
	               <div class="form-group">
	                  <label class="col-md-2 control-label"><?=_("진열상태")?></label>
	                  <div class="col-md-3">
						<input type="radio" name="isdp" value="" <?=$checked[isdp][""]?>/> <?=_("전체")?>
						<input type="radio" name="isdp" value="0" <?=$checked[isdp][0]?>/> <?=_("진열")?>
						<input type="radio" name="isdp" value="1" <?=$checked[isdp][1]?>/> <?=_("미진열")?>						
	                  </div>
	                  <label class="col-md-2 control-label"><?=_("판매상태")?></label>	                  
	                  <div class="col-md-3">
						<input type="radio" name="state" value="" <?=$checked[state][""]?>/> <?=_("전체")?>
						<?foreach($r_goods_state as $k=>$v){?>
						<input type="radio" name="state" value="<?=$k?>" <?=$checked[state][$k]?>/> <?=$v?>
						<?}?>						
	                  </div>
	                  <div class="col-md-2">	                     
	                  </div>
	               </div>	               

	               <div class="form-group">
	                  <label class="col-md-2 control-label"><?=_("판매가")?></label>
	                  <div class="col-md-2">
						<input type="text" class="form-control" name="price[]" value="<?=$_POST[price][0]?>" onkeypress="onlynumber()"/>
	                  </div>	                  
					  <label class="col-md-2 control-label"><?=_("원 부터 ~")?></label>
	                  <div class="col-md-2">
	                  	<input type="text" class="form-control" name="price[]" value="<?=$_POST[price][1]?>" onkeypress="onlynumber()"/>
	                  </div>	                  
	                  <label class="col-md-2 control-label"><?=_("원 까지")?></label>
	                  <div class="col-md-2">	                     
	                     <button type="submit" class="btn btn-sm btn-inverse"><?=_("조 회")?></button>
	                  </div>
	               </div>
               </form>
            </div>
         </div>
      </div>
   </div>
   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">   
            <div class="panel-heading">
               <!--<div class="panel-heading-btn">
               	  <a href="javascript:addProduct();"><i class="fa fa-plus-square-o"></i> <?=_("상품추가")?></a>
               </div>-->            	
               <h4 class="panel-title"><?=_("상품 진열")?></h4>
            </div>
            <div class="panel-body">
               <div class="table-responsive">
                  <table id="data-table" class="table table-striped table-bordered">
                     <thead>
                        <tr>
                           <!--<th><a href="javascript:chkBox('chk[]','rev')"><?=_("선택")?></a></th>-->
                           <th><?=_("진열순위")?></th>
                           <th><?=_("이미지")?></th>
                           <th><?=_("상품명/상품코드")?></th>
                           <th><?=_("가격")?></th>
                           <th><?=_("등록일")?></th>
                           <th><?=_("진열상태")?></th>
                           <th><?=_("판매상태")?></th>
                           <!--<th><?=_("수정")?></th>
                           <th><?=_("삭제")?></th>-->
                        </tr>
                     </thead>
                  </table>
               </div>
				<div class="form-group">
                	<button type="button" class="btn btn-primary" onClick="addProduct();">
                    	<?=_("상품 추가")?>
                    </button>
                    <button type="button" class="btn btn-danger" onClick="addProduct();">
                    	<?=_("상품 삭제")?>
                    </button>
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
function addProduct(){
	popup('../module/set.dp.php?dpcode='+$('select[name=block_code]').val(),1000,950);
}
</script>

<script>
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
         "sPaginationType" : "bootstrap",
         "aaSorting" : [[0, "asc"]],
         "bFilter" : false,
         "oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
         },
         "aoColumns": [
         { "bSortable": true },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": true },
         { "bSortable": false },
         { "bSortable": false },
         ],
         "processing": true,
         "serverSide": true,
         //"scrollX" : true,
         "ajax": $.fn.dataTable.pipeline({
            url: './goods_main_block_page.php?postData=<?=$postData?>',
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