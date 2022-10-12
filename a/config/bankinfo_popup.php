<?

include_once "../_pheader.php";

if ($_GET[bankno]) {
	$m_config = new M_config();
	
	$addWhere = "where cid='$cid' and bankno='$_GET[bankno]'";
	$data = $m_config->getBankInfo($cid, $_GET[bankno], $addWhere);
	$data[bankinfo] = explode(" ", $data[bankinfo]);
}

?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("계좌설정")?></a>
            </div>
         </div>
      </div>
      
      <form class="form-horizontal form-bordered" method="post" action="indb.php">
      	<input type="hidden" name="mode" value="bankinfo" />
      	<input type="hidden" name="bankno" value="<?=$data[bankno]?>" />
      	
      	<div class="panel panel-inverse">
          <div class="panel-body panel-form">
         	<div class="panel-body">
         		<div>
         			<table id="data-table" class="table table-bordered">
         				<tbody>
         					<tr>
         						<td><?=_("은행명")?></td>
         						<td><input type="text" class="form-control" name="bankinfo[]" value="<?=$data[bankinfo][0]?>"></td>
         					</tr>
         					<tr>
         						<td><?=_("입금계좌")?></td>
         						<td><input type="text" class="form-control" name="bankinfo[]" value="<?=$data[bankinfo][1]?>"></td>
         					</tr>
         					<tr>
         						<td><?=_("예금주명")?></td>
         						<td><input type="text" class="form-control" name="bankinfo[]" value="<?=$data[bankinfo][2]?>"></td>
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