<?

include_once "../_pheader.php";

$cfg[cstime] = getCfg('cstime');

?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("운영시간설정")?></a>
            </div>
         </div>
      </div>
      
      <form class="form-horizontal form-bordered" method="post" action="indb.php">
      	<input type="hidden" name="mode" value="cstime" />
      	
      	<div class="panel panel-inverse">
          <div class="panel-body panel-form">
         	<div class="panel-body">
         		<div class="table-responsive">
         			<table id="data-table" class="table table-bordered">
         				<tbody>
         					<tr>
         						<td><?=_("운영시간")?></td>
         						<td>
         							<textarea id="cstime" class="form-control" name="cstime" style="width:450px;"><?=$cfg[cstime]?></textarea>
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

<script type="text/javascript">
$j("#cstime").keyup(function() {
	var row = $j("#cstime").val().split("\n").length;
	var maxRow = 3;
	
	if (row > maxRow) {
		alert("3줄까지만 입력이 가능합니다.");
		var modifyText = $j("#cstime").val().split("\n").slice(0, maxRow);
		$j("#cstime").val(modifyText.join("\n"));
	}
});
</script>

<? include_once "../_pfooter.php"; ?>