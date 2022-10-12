<?

include "../_header.php";
include "../_left_menu.php";

//20170309 / minks / 아이콘 추가
$dir = "../../data/icon/$cid/";
$file = array();
$r_icon = array();
$iconno = 1;

if (is_dir($dir)) {
	$dir_handle = opendir($dir);
	
	while (($file = readdir($dir_handle)) != false) {
		if ($file != "." && $file != ".." && is_file($dir."/".$file)) {
			$r_icon[] = $file;
		}
	}
	
	closedir($dir_handle);
}

sort($r_icon);
$lasticon = end($r_icon);
$iconno = str_replace("icon_", "", substr($lasticon, 0, strrpos($lasticon, "."))) + 1;

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("아이콘관리")?></h4>
      </div>
      
      <div class="panel-body">
         <div class="table-responsive">
            <table id="data-table" class="table table-hover table-bordered">
               <thead>
                  <tr>
                     <th><?=_("아이콘")?></th>
                     <th><?=_("수정")?></th>
                     <th><?=_("삭제")?></th>
                  </tr>
               </thead>
               <tbody>              	
                  <? foreach ($r_icon as $k=>$v) { ?>
                  <tr align="center">
                     <td align="left"><img src="../../data/icon/<?=$cid?>/<?=$v?>" /></td>
                     <td width="50">
                        <button type="button" class="btn btn-xs btn-primary" onclick="popup('icon_popup.php?icon_filename=<?=$v?>', 650, 330);"><?=_("수정")?></button>
                     </td>
                     <td width="50">
                        <a href="indb.php?mode=icon_del&icon_filename=<?=$v?>" onclick="return confirm('<?=_("정말로 삭제하시겠습니까?")?>')">
                           <span class="btn btn-xs btn-danger"><?=_("삭제")?></span>
                        </a>
                     </td>
                  </tr>
                  <? } ?>
               </tbody>
            </table>
               
            <div class="form-group">
               <div class="col-md-12">
                  <button type="button" class="btn btn-sm btn-success" onclick="popup('icon_popup.php?iconno=<?=$iconno?>', 630, 285)"><?=_("아이콘추가")?></button>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>