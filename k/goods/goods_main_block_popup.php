<?
include_once "../_pheader.php";
include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

###메인화면 상품 블럭 설정###

$tableName = "md_main_block";
$selectArr = "*";
$whereArr = array("cid" => "$cid"); //, "id" => "$_GET[id]"
$data = SelectListTable($tableName, $selectArr, $whereArr, false, "order by order_by asc", "");
//debug($data);
if ($data) {
	foreach ($data as $key => $value) {
		$blockArr[$value[block_code]] = $value;
	}
	//debug($blockArr);
}
?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("메인 분류 관리")?></a>
            </div>
         </div>
      </div>
      
		<div class="row">
	    	<div class="col-md-5">
				<div class="panel panel-inverse">
				    <div class="panel-heading">
				        <h4 class="panel-title"><?=_("메인 분류")?></h4>
				    </div>
				    <div class="panel-body">
				    <form class="form-horizontal" method="post" action="indb.php">
                        <input type="hidden" name="mode" value="main_block_orderby" />	
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                	<th>#</th>
                                    <th><?=_("순서")?></th>
                                    <th><?=_("코드")?></th>
                                    <th><?=_("분류명")?></th>
                                </tr>
                            </thead>
                            <tbody>
                            	<!--
                            	<?
                            	foreach ($r_main_block as $key => $value) {
                            		$val = $blockArr[$key];
								?>
                                <tr>
                                	<td>
                                		<input type="hidden" name="id[]" value="<?=$val[ID]?>" />
                                		<a href="javascript:;" onclick='MoveUpItem(this);'><i class="fa fa-2x fa-toggle-up"></i></a>
                                		<a href="javascript:;" onclick='MoveDownItem(this);'><i class="fa fa-2x fa-toggle-down"></i></a>
                                	</td>
                                    <td><?=$val[order_by]?></td>
                                    <td><a href="javascript:;" onclick="block_update('<?=$val[ID]?>','<?=$key?>','<?=$val[display_text]?>','<?=$val[order_by]?>','<?=$val[display_data]?>','<?=$val[display_img]?>', '<?=$val[state]?>');"><?=$key?></a></td>
                                    <td><a href="javascript:;" onclick="block_update('<?=$val[ID]?>','<?=$key?>','<?=$val[display_text]?>','<?=$val[order_by]?>','<?=$val[display_data]?>','<?=$val[display_img]?>', '<?=$val[state]?>');"><?=$value?></a></td>
                                </tr>
								<?	
								}
                            	?>
                            	-->
                            	
                            	<?
								if ($data) {
									foreach ($data as $key => $val) {
                            	?>
                                <tr>
                                	<td>
                                		<input type="hidden" name="id[]" value="<?=$val[ID]?>" />
                                		<a href="javascript:;" onclick='MoveUpItem(this);'><i class="fa fa-2x fa-toggle-up"></i></a>
                                		<a href="javascript:;" onclick='MoveDownItem(this);'><i class="fa fa-2x fa-toggle-down"></i></a>
                                	</td>
                                    <td><?=$val[order_by]?></td>
                                    <td><a href="javascript:;" onclick="block_update('<?=$val[ID]?>','<?=$val[block_code]?>','<?=$val[display_text]?>','<?=$val[order_by]?>','<?=$val[display_data]?>','<?=$val[display_img]?>', '<?=$val[state]?>');"><?=$val[block_code]?></a></td>
                                    <td><a href="javascript:;" onclick="block_update('<?=$val[ID]?>','<?=$val[block_code]?>','<?=$val[display_text]?>','<?=$val[order_by]?>','<?=$val[display_data]?>','<?=$val[display_img]?>', '<?=$val[state]?>');"><?=$val[display_text]?></a></td>
                                </tr>
								<?
									}	
								}
                            	?>
                            	                            	
                            </tbody>                            
                        </table>
                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-xs btn-info"><?=_("순서저장")?></button>
                            </div>
                        </div>
                    </form>                            
				    </div>
				</div>    		
	    	</div>
	    	<div class="col-md-7">
				<div class="panel panel-inverse">
				    <div class="panel-heading">
				        <h4 class="panel-title"><?=_("상세 정보")?></h4>
				    </div>
				    <div class="panel-body">
                        <form class="form-horizontal" method="post" action="indb.php" enctype="multipart/form-data">
                        	<input type="hidden" name="mode" value="main_block" />
                        	<input type="hidden" name="id" />
                            <div class="form-group">
                                <label class="col-md-3 control-label"><?=_("예시 이미지")?></label>
                                <div class="col-md-9">
                                    <span><?=_("예시 이미지 입니다.")?></span>						            
						            <br>
						            <img name="ex_img" src="../../data/main_block/main_block_01.png"/>
                                </div>
                            </div>                        	
                            <div class="form-group">
                                <label class="col-md-3 control-label"><?=_("코드")?></label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="block_code" placeholder="<?=_("분류 코드를 입력하세요.")?>" readonly />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label"><?=_("분류 표시명")?></label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="display_text" placeholder="<?=_("분류 표시명을 입력하세요.")?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label"><?=_("분류 표시 순서")?></label>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="order_by" readonly />
                                </div>
                                <div class="col-md-7">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label"><?=_("사용 여부")?></label>
                                <div class="col-md-9">
                                    <label class="radio-inline">
                                        <input type="radio" name="state" value="0" checked />
                                        <?=_("사용")?>
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="state" value="1" />
                                        <?=_("사용안함")?>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label"><?=_("분류 표시 방법")?></label>
                                <div class="col-md-9">
                                    <label class="radio-inline">
                                        <input type="radio" name="display_data" value="1" onclick="$('#dImg').hide();" checked />
                                        <?=_("텍스트")?>
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="display_data" value="2" onclick="$('#dImg').show();" />
                                        <?=_("이미지")?>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group" id="dImg" style="display: none;">
                                <label class="col-md-3 control-label"><?=_("분류 이미지")?></label>
                                <div class="col-md-9">
                                    <input type="file" class="form-control" name="display_img" />
                                    <br>
						            <span><?=_("사용 가능한 이미지는 52px ＊52px 이하 사이즈의 png 파일이여야 합니다.")?></span>						            
						            <br>
						            <img name="dis_img" src="../../skin/modern/img/noimg.png"/>
						            <input type="checkbox" name="del_display_img_flag" class="absmiddle" style="width:10px;"/>
						            <input type="hidden" name="del_display_img" />
						            <span><?=_("현재 등록된 파일 삭제하기")?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label"></label>
                                <div class="col-md-9">
                                    <button type="submit" class="btn btn-sm btn-primary"><?=_("저 장")?></button>
                                </div>
                            </div>
                        </form>				        
				    </div>
				</div>
	    	</div>    	
	    </div>	
	   	
	    <div class="form-group">
	        <label class="col-md-3 control-label"></label>
	        <div class="col-md-9">
	            <button type="button" style="margin-bottom: 15px;" class="btn btn-default"onclick="window.close();"><?=_("닫  기")?></button>
	        </div>
	    </div>	
   	
   </div>
</div>

<script>
//수정
function block_update(bId,bCode,bText,bOrder,bType,bImg,bState) {
	$("input[name=id]").val(bId);
	$("input[name=block_code]").val(bCode);
	$("input[name=display_text]").val(bText);
	//$("select[name=order_by]").val(bOrder);
	$("input[name=order_by]").val(bOrder);
	$("input:radio[name=display_data]:radio[value="+ bType +"]").prop("checked", true);
	$("input:radio[name=state]:radio[value="+ bState +"]").prop("checked", true);
	$("img[name=ex_img]").attr("src","../../data/main_block/"+bCode+".png");
	
	if(bType == "2") {
		$("#dImg").show();
	}
	else {
		$("#dImg").hide();
	}	
	
	if(bImg == "") {
		$("img[name=dis_img]").attr("src","../../skin/modern/img/noimg.png");
		$("input[name=del_display_img]").val("");
	}
	else {
		var path = "../../data/main_block/<?=$cid?>/" + bImg;
		$("img[name=dis_img]").attr("src",path);
		$("input[name=del_display_img]").val(bImg);
	}
}
</script>

<script>
	//위로이동
	function MoveUpItem(obj) {
	    if ($(obj).length == 1) {
	        var tr=$(obj).closest("tr");
	        selIdx = $(tr).index();
	        if (selIdx > 0) {
	            var p = $(tr).parent();
	            $(tr).remove();
	            $(p).find('tr').eq(selIdx - 1).before(tr); 
	        }
	    }
	}
	//아래로이동
	function MoveDownItem(obj) {
	    if ($(obj).length == 1) {
	        var tr = $(obj).closest("tr");
	        selIdx = $(tr).index();
	        if (selIdx < $(tr).parent().children().length - 1) {
	            var p = $(tr).parent();
	            $(tr).remove();
	            $(p).find('tr').eq(selIdx).after(tr); 
	        }
	    }
	}	
</script>

<? include_once "../_pfooter.php"; ?>