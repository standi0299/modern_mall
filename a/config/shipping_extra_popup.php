<?

include_once "../_pheader.php";

if ($_GET[zipcode]) {
	$m_config = new M_config();
	$addWhere = "";
	$data = $m_config->getShippingExtraInfo($_GET[rid], $_GET[zipcode], $addWhere);
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
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("추가금액설정")?></a>
            </div>
         </div>
      </div>
      
      <form class="form-horizontal form-bordered" method="post" action="indb.php">
      	<input type="hidden" name="mode" value="shipping_extra" />
      	<input type="hidden" name="rid" value="<?=$_GET[rid]?>" />      	
      	
      	<div class="panel panel-inverse">
          <div class="panel-body panel-form">
         	<div class="panel-body">
         		<div class="table-responsive">
         			<table id="data-table" class="table table-bordered">
         				<tbody>
         					<tr>
         						<td><?=_("우편번호")?></td>
         						<td><input type="text" class="form-control" name="zipcode" value="<?=$data[zipcode]?>"></td>
         					</tr>
         					<tr>
         						<td><?=_("주소")?></td>
         						<td><input type="text" class="form-control" name="address" value="<?=$data[address]?>"></td>
         					</tr>
         					<tr>
         						<td><?=_("추가금액")?></td>
         						<td><input type="text" class="form-control" name="add_price" value="<?=$data[add_price]?>"></td>
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

<? include_once "../_pfooter.php"; ?>