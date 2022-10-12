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
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("코멘트")?></a>
            </div>
         </div>
      </div>
      
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
         				</tbody>
         			</table>
         		</div>
         	</div>
          </div>
      </div>
   </div>
</div>

<? include "../_footer_app_init.php"; ?>
<? include_once "../_pfooter.php"; ?>