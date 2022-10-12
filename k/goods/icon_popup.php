<?

include_once "../_pheader.php";

?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("아이콘설정")?></a>
            </div>
         </div>
      </div>
      
      <form class="form-horizontal form-bordered" method="post" action="indb.php" enctype="multipart/form-data">
      	<input type="hidden" name="mode" value="icon_upload" />
      	<input type="hidden" name="iconno" value="<?=$_GET[iconno]?>" />
		<input type="hidden" name="icon_filename" value="<?=$_GET[icon_filename]?>" />
      	
      	<div class="panel panel-inverse">
          <div class="panel-body panel-form">
         	<div class="panel-body">
         		<div class="table-responsive">
         			<table id="data-table" class="table table-bordered">
         				<tbody>
         					<tr>
         						<td><?=_("아이콘")?></td>
         						<td>
         							<? if ($_GET[icon_filename]) { ?>
									<div style="margin-bottom:8px;"><img src="../../data/icon/<?=$cid?>/<?=$_GET[icon_filename]?>"></div>
									<? } ?>
									<input type="file" name="iconimg" required>
         						</td>
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

<? include_once "../_pfooter.php"; ?>