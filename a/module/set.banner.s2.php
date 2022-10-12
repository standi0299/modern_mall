<?

include "../_pheader.php";

$code = $_GET[code];		//배너 코드
$banner_type = $_GET[banner_type];		//배너 타입
$add_css = $_GET[add_css];		//추가 CSS
$add_type = $_GET[add_type];			//배너 추가 가능

//if (!$banner_type) $banner_type = "img|text|edit";			//기본값 처리
if ($banner_type != "text") $banner_type = "img|text|edit";			//기본값 처리
//$banner_type = "img|text|edit";	//기본값 처리 기본이미지배너, 텍스트배너, 에디터배너 상시 노출

if (!$add_type) $add_type = "N";			//기본값 처리 

$banner_type_arr = explode("|", $banner_type);
$banner_type_img = false;
$banner_text_img = false;
$banner_edit_img = false;
foreach ($banner_type_arr as $value) {
	if ($value == "img") $banner_type_img = true;
	if ($value == "text") $banner_text_img = true;
	if ($value == "edit") $banner_edit_img = true;
}
//debug($banner_type_arr);
if ($_COOKIE[skin])
   $cfg[skin] = $_COOKIE[skin];

$data = $db->fetch("select * from exm_banner where cid = '$cid' and skin = '$cfg[skin]' and code = '$_GET[code]'");

if ($data[spc]=="map"){
   $data[map] = $data[spc_desc];
}

$data[img] = explode("||",$data[img]);
$data[img_on] = explode("||",$data[img_on]); 
$data[target] = explode("||",$data[target]);
$data[url] = explode("||",$data[url]);
$data[spc_desc] = explode("||",$data[spc_desc]);

$checked[spc][$data[spc]] = "checked";
?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
	<div id="content" class="content">
		<div id="header" class="header navbar navbar-default navbar-fixed-top">
        	<div class="container-fluid">
            	<div class="navbar-header">
               		<a href="javascript:;" class="navbar-brand"><span class="navbar-logo"></span><?=_("배너 관리")?></a>
            	</div>
         	</div>
      	</div>      
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs nav-tabs-inverse nav-justified">
					<? if ($banner_type_img) { ?><li class="<?if($_GET[banner_type] == "" || $_GET[banner_type] == "img") {?>active<?}?>"><a href="#default-tab-1" data-toggle="tab"><?=_("기본 이미지 배너")?></a></li><?}?>
					<? if ($banner_text_img) { ?><li class="<?if($_GET[banner_type] == "text") {?>active<?}?>"><a href="#default-tab-2" data-toggle="tab"><?=_("텍스트 배너")?></a></li><?}?>
					<? if ($banner_edit_img) { ?><li class="<?if($_GET[banner_type] == "edit") {?>active<?}?>"><a href="#default-tab-3" data-toggle="tab"><?=_("에디터 배너")?></a></li><?}?>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade" id="default-tab-1">
						<!--tab-1-->
						<form method="POST" action="set_indb.php" enctype="multipart/form-data" onsubmit="return get_param(this)">
						<input type="hidden" name="mode" value="set_banner"/>

						<table class="tb1">
						<tr>
							<th><?=_("코드")?></th>
							<td><b><?=$_GET[code]?></b><input type="hidden" name="code" value="<?=$_GET[code]?>"/></td>
						</tr>
						<tr>
							<th><?=_("특수기능")?></th>
							<td>
							<input type="radio" name="spc" value="" class="absmiddle" style="width:10px" checked/><span class="absmiddle stxt"><?=_("일반 (이미지 또는 텍스트문장을 입력. 동시 등록된 경우 이미지가 우선 출력됩니다.)")?></span><br/>
							<input type="radio" name="spc" value="map" class="absmiddle" style="width:10px" <?=$checked[spc][map]?>/><span class="absmiddle stxt"><?=_("이미지맵 (1개의 이미지만을 사용하실 수 있습니다.)")?></span>
							</td>
						</tr>
						<tr>
							<th><?=_("설명글")?></th>
							<td><input type="text" name="comment" value="<?=$data[comment]?>" class="w300"/></td>
						</tr>
						<? if ($add_type == "Y") { ?>	
						<tr>
							<th><?=_("슬라이드속도")?></th>
							<td><input type="text" name="slide_speed" value="<?=$data[slide_speed]?>" size="1"><br><?=_("슬라이드 배너 일 경우 (초단위 입력 미입력 혹은 0일 경우 5초로 적용됩니다.)")?></td>
						</tr>
						<? } ?>
						<tr>
							<th><p><?=_("배너이미지")?></p>
						  
						  <? 
						    if ($_GET[code] != 'top_menu_banner' && $add_type == "Y") { ?>	  
							  <button type="button" class="btn btn-xs btn-info" onclick="add()" id="add_btn"><?=_("추가")?></button></th>
						  <?  } ?>
							<td>
							<span class="desc" style="margin-left:85px;color:#01BBD6">
							<?=_("드래그를 하여 순서를 변경할 수 있습니다.")?> <?if($img_w){?>(<?=_("사이즈")?> <?=$img_w?>px X <?=$img_h?>px)<?}?> 
							</span>
							<div style="margin-left:-10px;list-style:none">
							<ul id="banner_ul">
							<?	
							foreach($data[img] as $k=>$v){
								$selected[target][$k][$data[target][$k]] = "selected";
							?>
							<li style="cursor:move;">
							<table class="bannerTb" height="100%">
							<tr valign="middle">
								<td width="60" style="border:0">
						      <?
						    
                           $filePath = "../../data/banner/$cid/$data[code]";    
   						    
   						      //배너 저장 폴더 데이타가 있을 경우 몰별로 저장위치를 구분하도록 변경된 구조임.      20140623    chunter
   						      if ($data[file_path])
   						      $filePath = "../.." .$data[file_path];
   						      //debug($filePath."/".$v);
   						    
   								if($v && is_file($filePath."/".$v)){
   									$size = getImageSize($filePath."/".$v);
   									switch($size[2]){   										
   										case "4": case "13":
						
								?>
								<script>embed("<?=$filePath."/".$v?>",60,60)</script>
								<?
                           break;
									default:
								?>
								<img src="<?=$filePath."/".$v?>" width="60" height="60">
								<? if ($data[img_on][$k]){ ?>
								<img src="<?=$filePath."/".$data[img_on][$k]?>" width="50" height="50">
								<? } ?>
								<?
                              break;
									   }
                           }
								?>
						
								<input type="hidden" name="num[]" value="<?=$k?>"/>
								<input type="hidden" name="image[]" value="<?=$v?>"/>
								<input type="hidden" name="image_on[]" value="<?=$data[img_on][$k]?>"/>
								</td>
								<td style="border:0">
								<input type="file" name="img[]" style="width:100%"/><br/>
								<span class="stxt"><?=_("오버이미지")?></span> <input type="file" name="img_on[]" style="width:100%"/><br/>
								<? if (substr($_GET[code],0,4)!="_sys"){ ?>
								<select name="target[]">
								<option value="_self" <?=$selected[target][$k][_self]?>><?=_("현재창")?></option>
								<option value="_blank" <?=$selected[target][$k][_blank]?>><?=_("새창으로")?></option>
								</select>
								<input type="text" name="url[]" style="width:100%;margin-top:3px" value="<?=$data[url][$k]?>"/>
								<? } ?>
								
								<? if ($data[spc]!="map") { ?>
								<textarea style="margin:2px 0;width:100%;" name="spc_desc[]"><?=$data[spc_desc][$k]?></textarea>
								<?  } ?>
								</td>
								<td style="border:0">
									<button type="button" class="btn btn-xs btn-danger del_btn" onclick="remove_(this)" id="del_btn"><?=_("삭제")?></button>
								</td>
							</tr>
							</table>
							</li>
							<?}?>
							</ul>
							</div>
						
							</td>
						</tr>
						<tr id="spc_tr_map" class="spc_tr" style="display:none">
							<th><?=_("이미지맵태그")?></th>
							<td>
							<div><?=htmlspecialchars("<map name=\"bannermap_$_GET[code]\">")?></div>
							<textarea style="margin:2px 0;width:100%;" name="map"><?=$data[map]?></textarea>
							<div><?=htmlspecialchars("</map>")?></div>
							<div class="desc red"><?=_("이미지맵태그의 area 태그만을 입력해주세요. map태그는 프로그램에 의해 자동 출력됩니다.")?></div>
							</td>
						</tr>
						</table>
						<p class="text-right m-b-0">
							<button type="submit" class="btn btn-primary"><?=_("저장")?></button>
						</p>
						</form>
						<!--tab-1-->
					</div>
					<div class="tab-pane fade" id="default-tab-2">
						<!--tab-2-->
						<form method="POST" action="set_indb.php" enctype="multipart/form-data" onsubmit="return chkForm(this)">
						<input type="hidden" name="mode" value="set_banner"/>
						<input type="hidden" name="spc" value="text"/>

						<table class="tb1">
						<tr>
							<th><?=_("코드")?></th>
							<td><b><?=$_GET[code]?></b><input type="hidden" name="code" value="<?=$_GET[code]?>"/></td>
						</tr>
						<tr>
							<th><?=_("설명글")?></th>
							<td><input type="text" name="comment" value="<?=$data[comment]?>" class="w300"/></td>
						</tr>

						<tr>
							<th><?=_("내용")?></th>
							<td>	
							<?
							foreach($data[img] as $k=>$v){
								$selected[target][$k][$data[target][$k]] = "selected";
							?>
								<input type="hidden" name="num[]" value="<?=$k?>"/>
								<input type="hidden" name="image[]" value="<?=$v?>"/>
								<input type="hidden" name="image_on[]" value="<?=$data[img_on][$k]?>"/>
								<textarea style="margin:2px 0;width:550px;" name="spc_desc[]" rows="5"><?=$data[spc_desc][$k]?></textarea>
							<?}?>
							</td>
						</tr>
						</table>
						<p class="text-right m-b-0">
							<button type="submit" class="btn btn-primary"><?=_("저장")?></button>
						</p>
						</form>
						<!--tab-2-->
					</div>
					<div class="tab-pane fade active in" id="default-tab-3">
						<!--tab-3-->
						<form class="form-horizontal form-bordered" name="fm" method="POST" action="set_indb.php" onsubmit="return submitContents(this);">
						<input type="hidden" name="spc" value="edit"/>
						<input type="hidden" name="mode" value="set_banner_editer" />
						<table class="tb1">
						<tr>
							<th><?=_("코드")?></th>
							<td><b><?=$_GET[code]?></b><input type="hidden" name="code" value="<?=$_GET[code]?>"/></td>
						</tr>
						<tr>
							<td colspan="2">
								<textarea id="edit_spc_desc" name="edit_spc_desc" type="editor" style="width:100%;height:300px;"><?=$data[spc_desc][$k]?></textarea>
							</td>
						</tr>
						</table>		
						<p class="text-right m-b-0">
							<button type="submit" class="btn btn-primary"><?=_("저장")?></button>
						</p>		
						</form>
						<!--tab-3-->
					</div>
				</div>
			</div>
	    </div>
	    <div class="form-group">
         <label class="col-md-3 control-label"></label>
         <div class="col-md-9">
            <button type="button" style="margin-bottom: 15px;" class="btn btn-default" onclick="opener.parent.location.reload();window.close();"><?=_("닫  기")?></button>
         </div>
      </div>
   </div>
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

	$j("input[type=radio][name=spc]").click(function(){
		var spc = $j(this).val();
		var tr_id = "spc_tr_"+spc;
		$j(".spc_tr").hide();
		if (spc){
			$j("#"+tr_id).show();
		}
		switch (spc){
			case "map":

				$j("li:gt(0)","#banner_ul").hide();
				$j("select[name='target[]']").hide();
				$j("input[name='url[]']").hide();
				$j("#add_btn").hide();
				$j(".del_btn").hide();

				break;
			default:

				$j("li:gt(0)","#banner_ul").show();
				$j("select[name='target[]']").show();
				$j("input[name='url[]']").show();
				$j("#add_btn").show();
				$j(".del_btn").show();

				break;
		}
	});
	$j("input[type=radio][name=spc]:checked").trigger("click");
	
	//탭보이기.
	if ("<?=$banner_type_img?>" == "1") {
		$('a[href="#default-tab-1"]').closest('li').attr('class','active');
		$('a[href="#default-tab-2"]').closest('li').attr('class','');
		$('a[href="#default-tab-3"]').closest('li').attr('class','');
		
		$('#default-tab-1').attr('class','tab-pane fade active in');
		$('#default-tab-2').attr('class','tab-pane fade');
		$('#default-tab-3').attr('class','tab-pane fade');
	}

	if ("<?=$banner_text_img?>" == "1") {
		if ("<?=$banner_type_img?>" != "1") {
			$('a[href="#default-tab-1"]').closest('li').attr('class','');
			$('a[href="#default-tab-2"]').closest('li').attr('class','active');
			$('a[href="#default-tab-3"]').closest('li').attr('class','');
			
			$('#default-tab-1').attr('class','tab-pane fade');
			$('#default-tab-2').attr('class','tab-pane fade active in');
			$('#default-tab-3').attr('class','tab-pane fade');
		}
	}

	if ("<?=$banner_edit_img?>" == "1") {
		$('a[href="#default-tab-1"]').closest('li').attr('class','');
		$('a[href="#default-tab-2"]').closest('li').attr('class','');
		$('a[href="#default-tab-3"]').closest('li').attr('class','active');
		
		$('#default-tab-1').attr('class','tab-pane fade');
		$('#default-tab-2').attr('class','tab-pane fade');
		$('#default-tab-3').attr('class','tab-pane fade active in');
	}
});
</script>

<script>$j("#banner_ul").sortable();</script>

<!-- 카피용 -->
<div id="origin_tb" style="display:none">
<table class="bannerTb" height="100%">
<tr valign="middle">
	<td width="60" style="border:0">
	</td>
	<td style="border:0">
	<input type="hidden" name="image[]" value=""/>
	<input type="file" name="img[]" style="width:100%"/><br/>
	<span class="stxt"><?=_("오버이미지")?></span> <input type="file" name="img_on[]" style="width:100%"/><br/>
	<? if (substr($_GET[code],0,4)!="_sys"){ ?>
	<select name="target[]">
	<option value="_self"><?=_("현재창")?></option>
	<option value="_blank"><?=_("새창으로")?></option>
	</select>
	<input type="text" name="url[]" style="width:100%;margin-top:3px" value=""/>
	<? } ?>
	<textarea style="margin:2px 0;width:100%;" name="spc_desc[]"></textarea>
	</td>
	<td style="border:0">
		<button type="button" class="btn btn-xs btn-danger del_btn" onclick="remove_(this)" id="del_btn"><?=_("삭제")?></button>
	</td>
</tr>
</table>
</div>

<script type="text/javascript" src="/js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/smarteditor/editorStart.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/webtoolkit.base64.js"></script>

<script type="text/javascript">
var oEditors = [];
smartEditorInit("edit_spc_desc", true, "editor_banner", true);

function submitContents(formObj) {
	if (sendContents("edit_spc_desc", false)) {
    	try {
    		formObj.edit_spc_desc.value = Base64.encode(formObj.edit_spc_desc.value);
            return form_chk(formObj);
        } catch(e) {
        	alert(e.message);
        	return false;
        }
    }
    return false;
}
</script>

<script>
function get_param(f){
	try {
		f.map.value = Base64.encode(f.map.value);
	    return chkForm(f);
	}
	catch(e) {
	    alert(e.message);
	    return false;
	}
}
</script>


<? include "../_pfooter.php"; ?>