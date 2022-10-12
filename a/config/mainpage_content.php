<?
include "../_header.php";
include "../_left_menu.php";

$filePath = "../../data/main_content/$cid/";

$data = $db->fetch("select * from md_main_content where cid = '$cid'");
$data[img] = explode("||",$data[img]);
$data[img_t] = explode("||",$data[img_t]);
$data[img_t_on] = explode("||",$data[img_t_on]);
$data[img_b] = explode("||",$data[img_b]);
$data[target] = explode("||",$data[target]);
$data[url] = explode("||",$data[url]);
$data[title] = str_replace("\"", "&quot;", stripslashes($data[title]));
$data[title] = explode("||", $data[title]);
$selected[dg_main_page][$data[dg_main_page]] = " selected";
?>

<script src="/js/ui.widget.js"></script>
<script src="/js/ui.core.js"></script>
<script src="/js/ui.tabs.js"></script>
<script src="/js/ui.mouse.js"></script>
<script src="/js/ui.sortable.js"></script>
	 
<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="/a/config/indb.php" enctype="multipart/form-data">
   <input type="hidden" name="mode" value="main_content" />
      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("메인 화면 컨텐츠")?></h4>
         </div>

         <div class="panel-body panel-form">
            

         	
         	<div class="form-group">
         	 	<label class="col-md-2 control-label">
         	 		<?=_("이미지")?><br><button type="button" class="btn btn-sm btn-info" onclick="add()" id="add_btn"><?=_("추가")?></button>
         	 	</label>
         	 	<div class="col-md-10">

					<span class="notice">
					<?//=_("드래그를 하여 순서를 변경할 수 있습니다.")?> 
					</span>
					
					<div style="margin-left:-10px;list-style:none">
					<ul id="banner_ul">
					<?	
					foreach($data[img] as $k=>$v) {
						$selected[target][$k][$data[target][$k]] = "selected";
					?>
					<li style="cursor:move;">
						
						<table class="bannerTb" width="100%" height="100%">
						<tr valign="middle">
							<td>
								<input type="hidden" name="num[]" value="<?=$k?>"/>
								<input type="hidden" name="image[]" value="<?=$v?>"/>
								<input type="hidden" name="image_t[]" value="<?=$data[img_t][$k]?>"/>
								<input type="hidden" name="image_t_on[]" value="<?=$data[img_t_on][$k]?>"/>
								<input type="hidden" name="image_b[]" value="<?=$data[img_b][$k]?>"/>									
									
								<div class="form-group">
					            	<label class="col-md-2 control-label"><?=_("이미지")?></label>
					               	<div class="col-md-1">
					                	<img src="<?=$filePath."/".$v?>" width="60" height="60">
					               	</div>               
					               	<div class="col-md-6">
					                	<input type="file" class="form-control" name="img[]">
					               	</div>
					               	<div class="col-md-2">
					                	<button type="button" class="btn btn-xs btn-danger del_btn" onclick="remove_(this)" id="del_btn"><?=_("삭제")?></button>
					               	</div>
					            </div>
					            
<? if ($data[dg_main_page] == "istorybook") { ?>					            
					            <div class="form-group">
					               	<label class="col-md-2 control-label"><?=_("썸네일 이미지")?></label>
					               	<div class="col-md-1">
										<? if ($data[img_t][$k]){ ?>
										<img src="<?=$filePath."/".$data[img_t][$k]?>" width="50" height="50">
										<? } ?>
					               	</div>               
					               	<div class="col-md-6">
					                	<input type="file" class="form-control" name="img_t[]">
					               	</div>
					            </div>
					            <div class="form-group">
					               	<label class="col-md-2 control-label"><?=_("썸네일 오버 이미지")?></label>
					               	<div class="col-md-1">
										<? if ($data[img_t_on][$k]){ ?>
										<img src="<?=$filePath."/".$data[img_t_on][$k]?>" width="50" height="50">
										<? } ?>
					               	</div>               
					               	<div class="col-md-6">
					                  	<input type="file" class="form-control" name="img_t_on[]">
					               	</div>
					            </div>
					            <div class="form-group">
					               	<label class="col-md-2 control-label"><?=_("배경 이미지")?></label>
					               	<div class="col-md-1">
										<? if ($data[img_b][$k]){ ?>
										<img src="<?=$filePath."/".$data[img_b][$k]?>" width="50" height="50">
										<? } ?>
					               	</div>               
					               	<div class="col-md-6">
					                  	<input type="file" class="form-control" name="img_b[]">
					               	</div>
					            </div>
<? } else if ($data[dg_main_page] == "P1") { ?>
								<div class="form-group">
					               	<label class="col-md-2 control-label"><?=_("타이틀")?></label>            
					               	<div class="col-md-9">
					                  	<input type="text" name="title[]" class="form-control" value="<?=$data[title][$k]?>"/>
					               	</div>
					            </div>							            
<? } ?>           
					            
					            <div class="form-group">
					               	<label class="col-md-2 control-label"><?=_("링크 URL")?></label>
					               	<div class="col-md-2">
										<select class="form-control" name="target[]">
											<option value="_self" <?=$selected[target][$k][_self]?>><?=_("현재창")?></option>
											<option value="_blank" <?=$selected[target][$k][_blank]?>><?=_("새창으로")?></option>
										</select>
					               	</div>               
					               	<div class="col-md-7">
					                  	<input type="text" name="url[]" class="form-control" value="<?=$data[url][$k]?>"/>
					               	</div>
					            </div>            
							</td>
						</tr>
						</table>
							
					</li>
					<?}?>
					
					</ul>
					</div>
							
         	 	</div>
         	</div>
            
            <div class="form-group">
               <label class="col-md-2 control-label"></label>
         	 	<div class="col-md-10">
         	 		<button type="submit" class="btn btn-sm btn-success"><?=_("저장")?></button>
               </div>
            </div>
         </div>
      </div>   
   </form>
</div>

<script>
function add(){
	var target = $j("#banner_ul");
	var inner = $j("#origin_tb").html();
	var li = document.createElement("li");
	li.innerHTML = inner;
	$j(li).css("cursor","move");
	$j(li).clone().appendTo(target);
	$j("li","#banner_ul").mouseover(function(){
		$j(this).css("background","#DEDEDE");
	});
	$j("li","#banner_ul").mouseout(function(){
		$j(this).css("background","transparent");
	});
}
function remove_(obj){
	$j(obj).parent().parent().parent().parent().parent().remove();
}

$j(window).load(function(){
	$j("li","#banner_ul").mouseover(function(){
		$j(this).css("background","#DEDEDE");
	});
	$j("li","#banner_ul").mouseout(function(){
		$j(this).css("background","transparent");
	});
	$j("td",".bannerTb").css("background","transparent");
});
</script>

<script>//$j("#banner_ul").sortable();</script>

<!-- 카피용 -->
<div id="origin_tb" style="display:none">
<table class="bannerTb" width="100%" height="100%" border="0">
<tr valign="middle">
	<td style="border:0">
		<input type="hidden" name="image[]" value=""/>
		<div class="form-group">
        	<label class="col-md-2 control-label"><?=_("이미지")?></label>
           	<div class="col-md-1">
           	</div>               
           	<div class="col-md-6">
            	<input type="file" class="form-control" name="img[]">
           	</div>
           	<div class="col-md-2">
            	<button type="button" class="btn btn-xs btn-danger del_btn" onclick="remove_(this)" id="del_btn"><?=_("삭제")?></button>
           	</div>
        </div>

<? if ($data[dg_main_page] == "istorybook") { ?>        
        <div class="form-group">
           	<label class="col-md-2 control-label"><?=_("썸네일 이미지")?></label>
           	<div class="col-md-1">
           	</div>               
           	<div class="col-md-6">
            	<input type="file" class="form-control" name="img_t[]">
           	</div>
        </div>
        <div class="form-group">
           	<label class="col-md-2 control-label"><?=_("썸네일 오버 이미지")?></label>
           	<div class="col-md-1">
           	</div>               
           	<div class="col-md-6">
              	<input type="file" class="form-control" name="img_t_on[]">
           	</div>
        </div>
        <div class="form-group">
           	<label class="col-md-2 control-label"><?=_("배경 이미지")?></label>
           	<div class="col-md-1">
           	</div>               
           	<div class="col-md-6">
              	<input type="file" class="form-control" name="img_b[]">
           	</div>
        </div>
<? } else if ($data[dg_main_page] == "P1") { ?>
		<div class="form-group">
           	<label class="col-md-2 control-label"><?=_("타이틀")?></label>               
           	<div class="col-md-9">
              	<input type="text" name="title[]" class="form-control"/>
           	</div>
        </div>
<? } ?>
        
        <div class="form-group">
           	<label class="col-md-2 control-label"><?=_("링크 URL")?></label>
           	<div class="col-md-2">
				<select class="form-control" name="target[]">
					<option value="_self"><?=_("현재창")?></option>
					<option value="_blank"><?=_("새창으로")?></option>
				</select>
           	</div>               
           	<div class="col-md-7">
              	<input type="text" name="url[]" class="form-control"/>
           	</div>
        </div>
	</td>		
</tr>
</table>
</div>

<? include "../_footer_app_init.php"; ?>

<script type="text/javascript" src="../assets/plugins/switchery/switchery.min.js"></script>
<script type="text/javascript" src="../assets/js/form-slider-switcher.demo.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	FormSliderSwitcher.init();
	
   $('input:radio[name="top_logo"]').each(function() {
      if(this.value == "img" && this.checked == true){
         divShow(this, 'img', 'txt');
      } else if(this.value == "txt" && this.checked == true){
         divShow(this, 'txt', 'img');
      }
   });
});

function divShow(obj, type, type_dis) {
   $j("#" + obj.name + "_" + type + "_div").slideDown();
   $j('input','#' + obj.name + "_" + type +  '_div').attr('disabled', false);
   
   $j("#" + obj.name + "_" + type_dis + "_div").slideUp();
   $j('input','#' + obj.name  + "_" + type_dis + '_div').attr('disabled', true);
}

//목록순서 드래그로 변경[03/12]
$(function(){
	$("#banner_ul").sortable();
	$("sortable").disableSelection();
});
</script>

<? include "../_footer_app_exec.php"; ?>