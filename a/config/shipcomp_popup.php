<?

include_once "../_pheader.php";

if ($_GET[shipno]) {
	$m_config = new M_config();
	
	$addWhere = "where cid='$cid' and shipno='$_GET[shipno]'";
	$data = $m_config->getShipCompInfo($cid, $_GET[shipno], $addWhere);
	
	$checked[isuse][$data[isuse]] = "checked";
}

if (!$data[isuse]) $checked[isuse][0] = "checked";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("택배업체설정")?></a>
            </div>
         </div>
      </div>
      
      <form class="form-horizontal form-bordered" method="post" action="indb.php">
      	<input type="hidden" name="mode" value="shipcomp" />
      	<input type="hidden" name="shipno" value="<?=$data[shipno]?>" />
      	
      	<div class="panel panel-inverse">
          <div class="panel-body panel-form">
         	<div class="panel-body">
         		<div class="table-responsive">
         			<table id="data-table" class="table table-bordered">
         				<tbody>
         					<tr>
         						<td><?=_("택배사명")?></td>
         						<td><input type="text" class="form-control" name="compnm" value="<?=$data[compnm]?>"></td>
         					</tr>
         					<tr>
         						<td><?=_("URL")?></td>
         						<td><input type="text" class="form-control" name="url" value="<?=$data[url]?>"></td>
         					</tr>
         					<tr>
         						<td><?=_("사용여부")?></td>
         						<td><input type="checkbox" data-render="switchery" data-theme="blue" name="isuse" value="1" <?=$checked[isuse][1]?>></td>
         					</tr>
         				</tbody>
         			</table>
         		</div>
         	</div>
         	
         	<div class="row">
         		<div class="col-md-12">
         			<p class="pull-right">
         				<button type="submit" class="btn btn-md btn-primary m-r-15"><?=_("저장")?></button>
         			</p>
         		</div>
         	</div>
          </div>
        </div>
      </form>
   </div>
</div>

<? include "../_footer_app_init.php"; ?>

<script type="text/javascript" src="../assets/plugins/switchery/switchery.min.js"></script>
<script type="text/javascript" src="../assets/js/form-slider-switcher.demo.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	FormSliderSwitcher.init();
});
</script>

<? include_once "../_pfooter.php"; ?>