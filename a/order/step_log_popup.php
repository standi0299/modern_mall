<?

include "../_pheader.php";
$use_mousewheel = true;

$query = "select * from exm_log_step where payno = '$_GET[payno]' and ordno = '$_GET[ordno]' and ordseq = '$_GET[ordseq]' order by idx";
$res = $db->query($query);

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content" style="padding:0;">	
   	  <div class="panel panel-inverse" style="margin:0;">
         <div class="panel-body panel-form">
         	<div class="panel-body" style="padding:0;">
         		<div class="table-responsive" style="overflow:hidden;">
         			<table id="data-table" class="table table-bordered">
         				<tbody>
         					<tr>
         						<td>날짜</td>
         						<td>변경전</td>
         						<td>변경후</td>
         						<td>센터/몰</td>
         						<td>관리자아이디</td>
         						<td>코멘트</td>
         					</tr>
         					
         					<? while ($data = $db->fetch($res)){ ?>
	         					<tr>
	         						<td><?=$data[regdt]?></td>
	         						<td><?=$r_step[$data[from]]?></td>
	         						<td><?=$r_step[$data[to]]?></td>
	         						<td><?=($data[center]) ? "센터" : "몰"?></td>
	         						<td><?=$data[admin]?></td>
	         						<td><?=$data[memo]?></td>
	         					</tr>
         					<? } ?>
         				</tbody>
         			</table>
         		</div>
         	 </div>
         </div>
      </div>
   </div>
</div>
