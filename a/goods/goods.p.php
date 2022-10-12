<?

include_once "../_pheader.php";

if (!$_GET[type]) $_GET[type] = "test";
list ($goodsno) = $db->fetch("select * from exm_goods where podsno = '$_GET[podsno]'",1);

$checked[type][$_GET[type]] = "checked";

?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("제작대행")?></a>
            </div>
         </div>
      </div>
      
      <form class="form-horizontal form-bordered" action="javascript:pods_editor_view()" onsubmit="return form_chk(this)">
      	<input type="hidden" name="mode" value="<?=$_GET[type]?>"/>
		<input type="hidden" name="podsno" value="<?=$_GET[podsno]?>"/>
		<input type="hidden" name="optionid" value="<?=$_GET[optionid]?>"/>
      	
      	<div class="panel panel-inverse">
          <div class="panel-body panel-form">
         	<div class="panel-body">
         		<div>
         			<table id="data-table" class="table table-bordered">
         				<tbody>
         					<tr>
         						<td><?=_("회원아이디")?></td>
         						<td><input type="text" class="form-control" name="mid" required></td>
         					</tr>
         				</tbody>
         			</table>
         		</div>
         	</div>
         	
         	<div class="row">
         		<div class="col-md-12">
         			<p class="pull-right">
         				<button type="submit" class="btn btn-md btn-primary m-r-15"><?=_("확인")?></button>
         			</p>
         		</div>
         	</div>
          </div>
        </div>
      </form>
   </div>
</div>

<script type="text/javascript">
function pods_editor_view() {
	var podsno = $j("input[name=podsno]").val();
	var optionid = $j("input[name=optionid]").val();
	var mid = $j("input[name=mid]").val();

	if (!optionid) {
		optionid = 1;
	}

	popupLayer('../../module/popup_calleditor.php?mode=proxy&goodsno=<?=$goodsno?>&productid='+podsno+'&optionid='+optionid+"&mid="+mid);

	return;
}
</script>

<? include_once "../_pfooter.php"; ?>