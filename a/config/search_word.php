<?

include "../_header.php";
include "../_left_menu.php";

$cfg[search_word] = getCfg('search_word');

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   <input type="hidden" name="mode" value="search_word" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("추천 검색어 / 해시태그")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"></label>
      	 	<div class="col-md-10">
      	 		<textarea id="agreement" class="form-control" name="search_word"><?=$cfg[search_word]?></textarea>
      	 		<div><span class="warning">[<?=_("주의")?>]</span> <?=_("구분자는 ',' 입니다.")?></div>
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"></label>
      	 	<div class="col-md-10">
      	 		<button type="submit" class="btn btn-sm btn-success"><?=_("저장")?></button>
      	 	</div>
      	 </div> 
      </div>
   </div>
   </form>
</div>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>