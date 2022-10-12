<?
include "../_header.php";
include "../_left_menu.php";

if ($cfg[insta_config]) $cfg[insta_config] = unserialize($cfg[insta_config]);

if (!$cfg[insta_config][insta_use]) $cfg[insta_config][insta_use] = "N";
$checked[insta_use][$cfg[insta_config][insta_use]] = "checked";
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
      <input type="hidden" name="mode" value="insta_config" />
   
      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("인스타그램 연동설정")?></h4>
         </div>
  
         <div class="panel-body panel-form">
             <div class="form-group">
                <label class="col-md-2 control-label"><?=_("인스타그램 연동 사용여부")?></label>
                <div class="col-md-10">
                    <input type="radio" class="radio-inline" name="insta_use" value="N" <?=$checked[insta_use][N]?>> <?=_("사용안함")?>
                    <input type="radio" class="radio-inline" name="insta_use" value="Y" <?=$checked[insta_use][Y]?>> <?=_("사용")?>
                </div>
             </div>
         
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("Client ID")?></label>
               <div class="col-md-10">
                  <input type="text" id="client_id" class="form-control" name="client_id" value="<?=$cfg[insta_config][client_id]?>" size="40">
               </div>
            </div>
   
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("Client Secret")?></label>
               <div class="col-md-10">
                  <input type="text" id="client_secret" class="form-control" name="client_secret" value="<?=$cfg[insta_config][client_secret]?>" size="40">
               </div>
            </div>
   
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("access token")?></label>
               <div class="col-md-10">
                  <input type="text" id="access_token" class="form-control" name="access_token" value="<?=$cfg[insta_config][access_token]?>" size="40">
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

</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>