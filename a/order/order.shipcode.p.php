<?

include "../_pheader.php";

$m_order = new M_order();

### 배송업체정보추출
$r_shipcomp = get_shipcomp();

$data = $m_order->getOrdItemInfo($_GET[payno], $_GET[ordno], $_GET[ordseq]);

$selected[shipcomp][$data[shipcomp]] = "selected";

?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("송장/수정")?></a>
            </div>
         </div>
      </div>
      
      <form class="form-horizontal form-bordered" method="post" action="indb.php">
      	<input type="hidden" name="mode" value="modify_shipcode"/>
		<input type="hidden" name="payno" value="<?=$_GET[payno]?>"/>
		<input type="hidden" name="ordno" value="<?=$_GET[ordno]?>"/>
		<input type="hidden" name="ordseq" value="<?=$_GET[ordseq]?>"/>
		<input type="hidden" name="page" value="<?=$_GET[page]?>">
      	
      	<div class="panel panel-inverse">
          <div class="panel-body panel-form">
         	<div class="panel-body">
         		<div class="table-responsive">
         			<table id="data-table" class="table table-bordered">
         				<tbody>
         					<tr>
         						<td><?=_("배송업체")?></td>
         						<td>
         							<select name="shipcomp" class="form-control">
					      	 			<option value=""><?=_("선택")?></option>
					      	 			<? foreach ($r_shipcomp as $k=>$v) { ?>
					      	 				<option value="<?=$k?>" <?=$selected[shipcomp][$k]?>><?=$v[compnm]?></option>
					      	 			<? } ?>	
					      	 		</select>
         						</td>
         					</tr>
         					<tr>
         						<td><?=_("운송장번호")?></td>
         						<td><input type="text" class="form-control" name="shipcode" value="<?=$data[shipcode]?>"></td>
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