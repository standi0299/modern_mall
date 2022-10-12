<?

include_once "../_pheader.php";

if ($_GET[no]) {
	$m_order = new M_order();
	
	$data = $m_order->getBigorderInfo($cid, $_GET[no]);
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
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("대량구매문의보기")?></a>
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
         						<td><?=_("대량구매문의종류")?></td>
         						<td><?=$r_bigorder_type[$data[category]]?></td>
         					</tr>
         					<tr>
         						<td><?=_("상품명")?></td>
         						<td><?=$data[goodsnm]?></td>
         					</tr>
         					<? if ($data[category] == "quotation") { ?>
         					<tr>
         						<td><?=_("수량")?></td>
         						<td><?=$data[ea]?></td>
         					</tr>
         					<? } ?>
         					<tr>
         						<td><?=_("학교(단체명)")?></td>
         						<td><?=$data[request_company]?></td>
         					</tr>
         					<tr>
         						<td><?=_("담당자")?></td>
         						<td><?=$data[request_name]?></td>
         					</tr>
         					<tr>
         						<td><?=_("연락처")?></td>
         						<td><?=$data[request_mobile]?></td>
         					</tr>
         					<tr>
         						<td><?=_("이메일")?></td>
         						<td><?=($data[request_email] != "@") ? $data[request_email] : ""?></td>
         					</tr>
         					<tr>
         						<td><?=_("주소")?></td>
         						<td>
         							<?=$data[request_zipcode]?><br>
         							<?=$data[request_address]?><br>
         							<?=$data[request_address_sub]?>
         						</td>
         					</tr>
         					<tr>
         						<td><?=_("요청사항")?></td>
         						<td><?=stripslashes(str_replace("\n", "<br>", $data[content]))?></td>
         					</tr>
         					<tr>
         						<td><?=_("문의요청일")?></td>
         						<td><?=$data[regdt]?></td>
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