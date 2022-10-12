<?
include "../_header.php";
include "../_left_menu.php";

$query = "select * from exm_category where cid = '$cid' order by length(catno),sort";
$res = $db -> query($query);
$category = array();
while ($data = $db -> fetch($res)) {
   switch (strlen($data[catno])) {
      case "3" :
         $category[$data[catno]] = $data;
         break;
   }
}
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active"><?=_("메뉴항목 설정")?></li>
   </ol>
   <h1 class="page-header"><?=_("메뉴항목 설정")?> <small><?=_("인쇄몰 사이트에 제공된 메뉴항목을 직접 선택하실 수 있습니다.")?></small></h1>

   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   	<input type="hidden" name="mode" value="category_hidden"/>
      <div class="panel panel-inverse">
         <div class="panel-heading">
            <div class="panel-heading-btn">
               <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
               <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
               <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
            </div>
            <h4 class="panel-title"><?=_("메뉴항목 설정")?></h4>
         </div>

         <div class="panel-body panel-form">
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("메뉴(카테고리) 명칭")?></label>               
               <div class="col-md-10">
               	<? foreach ($category as $key => $value) {
      			      $checkTag = "";
            			if ($value[hidden] == "0") $checkTag = " checked";
               	?>

                	<label class="checkbox-inline"><input type="checkbox" name="catno[]" value="<?=$key?>" <?=$checkTag?> /><?=$value[catnm]?></label>

                  <? } ?>
               </div>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"> </label>
               <label class="col-md-10 control-label"><span class="pull-left"><?=_("체크박스에 체크 표시된 메뉴만 홈페이지에 제공되며, 사용하실 수 있습니다.  사용하지 안는 메뉴는 체크해제 해주세요.")?></span></label>
            </div>
         </div>
      </div>

		<div class="row">
         <div class="col-md-3">
         	<button type="button" class="btn btn-md btn-danger" onclick="category_desc_popup()"><?=_("순서변경")?></button>
         </div>	
         <div class="col-md-9">
	    	<p class="pull-right">
               <button type="submit" class="btn btn-md btn-primary m-r-15"><?=_("저장")?></button>
               <button type="button" class="btn btn-md btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
            </p>
         </div>
      </div>
   </form>
</div>

<script>
//admin 수정 팝업
function category_desc_popup() {
   	popup('category_desc_popup.php',500,550);
}
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>