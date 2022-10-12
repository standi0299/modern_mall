<?
include dirname(__FILE__) . "/../_pheader.php";

if (!$_GET[goodsno]) {
    msg(_("상품 코드가 넘어오지 못했습니다!"), "close");
}

//debug($_GET[goodsno]);
//debug($_GET[preset]);
//debug($_GET[option_kind_index]);
//debug($_GET[target_goodsno]);

$classExtraOption = new M_extra_option();
$res = $classExtraOption->getAdminOptionItemList($cid, $cfg_center[center_cid], $_GET[goodsno], $_GET[option_kind_index]);
?>

<!-- begin #page-loader -->
<div id="page-loader" class="fade in"><span class="spinner"></span></div>
<!-- end #page-loader -->

<div id="page-container" class="page-without-sidebar page-header-fixed">
    <!-- begin #content -->
    <div id="content" class="content">
    	
	    <!-- begin #header -->
        <div id="header" class="header navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("항목 관리")?></a>
                </div>
            </div>
        </div>

        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title"><?=_("항목 관리")?></h4>
            </div>
            <div class="panel-body panel-form">
				<form class="form-horizontal form-bordered">
					<input type="hidden" name="goodsno" value="<?=$_GET[goodsno]?>" />
					<input type="hidden" name="preset" value="<?=$_GET[preset]?>" />
					<input type="hidden" name="option_kind_index" value="<?=$_GET[option_kind_index]?>" />
					<input type="hidden" name="target_goodsno" value="<?=$_GET[target_goodsno]?>" />

                <div class="form-group">
                    <label class="col-md-2 control-label"></label>
                    <div class="col-md-10">
						<table id="data-table" class="table table-striped table-bordered">
			            	<thead>
			                	<tr>
			                		<!--<th class="col-md-2"><?=_("순서변경")?></th>-->
			                   		<th class="col-md-4"><?=_("옵션명")?></th>
			                   		<th class="col-md-6"><?=_("사용여부")?></th>
			                	</tr>
			             	</thead>
			             	<tbody>		            
			             	<?
							foreach ($res as $key => $data) {
								$hidden_data = "<input type='hidden' name='item_name' value='$data[item_name]' />
									<input type='hidden' name='same_price_item_name' value='$data[same_price_item_name]' />
							        <input type='hidden' name='option_kind_code' value='$data[option_kind_code]' />";
							
							    $select_data = "<select name='display_flag' id='display_flag_$data[option_item_index]'>";
							        if($data[display_flag]=="Y") {
							          $select_data .= "<option value='Y' selected>"._("사용")."</option>";
							        }
							        else {
							          $select_data .= "<option value='Y'>"._("사용")."</option>";
							        }
							  
							        if($data[display_flag]=="N") {
							          $select_data .= "<option value='N' selected>"._("사용안함")."</option>";
							        }
							        else {
							          $select_data .= "<option value='N'>"._("사용안함")."</option>";
							        }
									
									$select_data .= "</select>";			             	
			             	?>
			             		<tr>
			             			<!--<td>							            		             				
			             				<a href="javascript:;" onclick='MoveUpItem(this)' class="btn btn-white btn-xs"><i class="fa fa-chevron-up"></i></a>
			             				<a href="javascript:;" onclick='MoveDownItem(this)' class="btn btn-white btn-xs"><i class="fa fa-chevron-down"></i></a>
			             			</td>-->
			             			<td>
			             				<?=$hidden_data?>
			             				<?=$data[item_name]?>
			             			</td>
			             			<td>
			             				<?=$select_data?>
			             				<a href="javascript:;" onclick='updateUseFlag(this)' class="btn btn-primary btn-xs m-r-5"><?=_("적용")?></a>
			             			</td>
			             		</tr>
			             	<?
							}
			             	?>			             
			             	</tbody>
			            </table>
                    </div>
                </div>

            	</form>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label"></label>
            <div class="col-md-9">
                <button type="button" style="margin-bottom: 15px;" class="btn btn-sm btn-default"onclick="window.close();"><?=_("닫  기")?></button>
            </div>
        </div>
    
    </div>
</div>

<script type="text/javascript">
//사용 여부 처리
function updateUseFlag(obj) {
	var val = "";
    if ($(obj).length == 1) {
        var tr = $(obj).closest("tr");
        var itemName = $(tr).find("input[name='item_name']").val();
        var displayFlag = $(tr).find("select[name='display_flag']").val();
        var optionKindCode = $(tr).find("input[name='option_kind_code']").val();
    }

    var goodsNo = $("input[name='goodsno']").val();
    var optionKindIndex = $("input[name='option_kind_index']").val();
    var targetGoodsNo = $("input[name='target_goodsno']").val();


	$('#page-loader').removeClass("hide").addClass('fade in');
    $('#page-container').removeClass("in").addClass('fade');
    
	$j.post("/lib/extra_option/set_extra_option_item_update.php", {
			mode:"use_flag_batch_p",
			goodsno:goodsNo,
			target_goodsno:targetGoodsNo,
			option_kind_index:optionKindIndex,
			option_group_type:"",
			option_kind_code:optionKindCode,
			item_name:itemName,
			display_flag:displayFlag
		}, function(data) {

		if(data == "OK") {
			alert('<?=_("완료되었습니다.")?>');
			document.location.reload();
		}
		else {
			alert('<?=_("실패하였습니다. 다시 시도하시기 바랍니다.")?>[' + data + ']');
			
			$('#page-loader').removeClass("fade in").addClass('hide');
			$('#page-container').removeClass("fade").addClass('in');
		}
	});

}
</script>

<?
include dirname(__FILE__) . "/../_pfooter.php";
?>