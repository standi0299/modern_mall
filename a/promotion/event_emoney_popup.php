<?

include_once "../_pheader.php";

if ($_GET[no]) {
	$m_etc = new M_etc();
	
	$data = $m_etc->getEventCommentInfo($cid, $_GET[no]);
	
	if ($data[regdt] == "0000-00-00 00:00:00") $data[regdt] = "";
}

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
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("적립금")?></a>
            </div>
         </div>
      </div>
      
      <form class="form-horizontal form-bordered" method="post" action="indb.php">
      	  <input type="hidden" name="mode" value="bottom_event_emoney">
		  <input type="hidden" name="bno" value="<?=$_GET[no]?>">
		  <input type="hidden" name="is_popup" value="1">
      	  
	      <div class="panel panel-inverse">
	          <div class="panel-body panel-form">
	         	<div class="panel-body">
	         		<div class="table-responsive">
	         			<table id="data-table" class="table table-bordered">
	         				<tbody>
	         					<tr>
	         						<td><?=_("이벤트명 (번호)")?></td>
	         						<td><?=$data[title]?> (<?=$data[eventno]?>)</td>
	         					</tr>
	         					<tr>
	         						<td><?=_("작성자")?></td>
	         						<td><?=$data[name]?> (<?=$data[mid]?>)</td>
	         					</tr>
	         					<tr>
	         						<td><?=_("작성일자")?></td>
	         						<td><?=$data[regdt]?></td>
	         					</tr>
	         					<tr>
	         						<td><?=_("내용")?></td>
	         						<td><?=nl2br($data[comment])?></td>
	         					</tr>
	         					<tr>
	         						<td><?=_("적립금")?></td>
	         						<td>
	         							<? if (!$data[emoney] && $data[name]) { ?>
	         							<input type="text" class="form-control" name="emoney" required size="8" maxlength="7" type2="number" style="width:100px;display:inline-block;"> <?=_("원")?>
	         							<? } ?>
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
$j(function() {
	$j('input[type2=number]').css('ime-mode', 'disabled').keypress(function(event) {
		if (event.which && event.which != 45 && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	});
});
</script>

<? include "../_footer_app_init.php"; ?>
<? include_once "../_pfooter.php"; ?>