<?
include "../_pheader.php";

$preset = $_GET[preset];
$optgt = $_GET[optgt];
//debug($r_est_preset_sub_option_group[$preset]);

switch ($optgt) {
	case 'C-FIX' :
		$optgt_title = _("표지");
		break;
	case 'M-FIX' :
		$optgt_title = _("면지");
		break;
	case 'G-FIX' :
		$optgt_title = _("간지");
		break;
	default :
		$optgt_title = _("내지");
		break;
}

$subOption = array();
if ($r_est_preset_sub_option_group[$preset][$optgt]) {
	$oidx = "";
	foreach ($r_est_preset_sub_option_group[$preset][$optgt] as $key => $value) {
		$vArr = split(',', $value);
		$subOption[$key][$vArr[1]][$vArr[2]] = $key .",".$vArr[1] .",".$vArr[2];
		
		if($oidx == "") $oidx = $key;
	}
}

//자동견적 옵션
$extraOption = new ExtraOption();
$optionKindCodeArr = $extraOption -> GetOptionKind($_GET[goodsno]);
//debug($extraOption->OptionData);
//debug($subOption);
//debug($oidx);

$category = array();
/*
foreach ($subOption as $key1 => $val1) { //1차
	foreach ($extraOption->getOptionDataByOptionKindIndex($key1) as $data1) {
		$category[$data1[item_name]] = $data1;
		$category[$data1[item_name]][sub_kind_index] = $extraOption->getChildOptionKindIndex($subOption[$key1], $key1);
		foreach ($val1 as $key2 => $val2) { //2차
			foreach ($extraOption->GetOptionDataByOptionKindIndexAndParentItemName($key2, $data1[item_name]) as $data2) {
				$category[$data1[item_name]][sub][$data2[item_name]] = $data2;
				$category[$data1[item_name]][sub][$data2[item_name]][sub_kind_index] = $extraOption->getChildOptionKindIndex($subOption[$key1][$key2], $key2);
				foreach ($val2 as $key3 => $val3) { //3차
					foreach ($extraOption->GetOptionDataByOptionKindIndexAndParentItemName($key3, $data2[item_name]) as $data3) {
						$category[$data1[item_name]][sub][$data2[item_name]][sub][$data3[item_name]] = $data3;
					}
				}				
			}
		}
	}
} 
*/
foreach ($subOption as $key1 => $val1) { //1차
	foreach ($extraOption->GetOptionDataByOptionKindIndex($key1) as $data1) {
		$category[$data1[item_name]] = $data1;
		$category[$data1[item_name]][sub_kind_index] = $extraOption->GetChildOptionKindIndex($subOption[$key1], $key1);
		foreach ($val1 as $key2 => $val2) { //2차
			foreach ($extraOption->GetOptionDataByOptionKindIndexAndParentItemName($key2, $data1[option_kind_code], $data1[item_name]) as $data2) {
				$category[$data1[item_name]][sub][$data2[item_name]] = $data2;
				$category[$data1[item_name]][sub][$data2[item_name]][sub_kind_index] = $extraOption->GetChildOptionKindIndex($subOption[$key1][$key2], $key2);
				foreach ($val2 as $key3 => $val3) { //3차
					foreach ($extraOption->GetOptionDataByOptionKindIndexAndParentItemName($key3, $data2[option_kind_code], $data2[item_name]) as $data3) {
						$category[$data1[item_name]][sub][$data2[item_name]][sub][$data3[item_name]] = $data3;
					}
				}				
			}
		}
	}
}
//debug($category);

//용지 사용 여부 설정.
if($_GET[displayData]) {
//debug($_GET[displayData]);
	$display_arr = explode("|", $_GET[displayData]);
//debug($display_arr);

//debug($extraOption->GetOptionKindUse($display_arr[0]));
//debug($extraOption->GetOptionKindUse($display_arr[1]));
//debug($extraOption->GetOptionKindUse($display_arr[2]));

	if ($extraOption->GetOptionKindUse($display_arr[0]) == "Y") {
		$selected1 = "selected";
		$selected2 = "";
		$selected3 = "";
	}
	
	if ($extraOption->GetOptionKindUse($display_arr[1]) == "Y") {
		$selected1 = "";
		$selected2 = "selected";
		$selected3 = "";
	}
	
	if ($extraOption->GetOptionKindUse($display_arr[2]) == "Y") {
		$selected1 = "";
		$selected2 = "";
		$selected3 = "selected";
	}

	$selectTag = "<select name=\"pageUse\" id=\"pageUse\">";
	$selectTag .= "<option value='$display_arr[0]=Y|$display_arr[1]=N|$display_arr[2]=N' $selected1>"._("1차용지 사용함")."</option>";
	$selectTag .= "<option value='$display_arr[0]=Y|$display_arr[1]=Y|$display_arr[2]=N' $selected2>"._("2차용지 사용함")."</option>";
	$selectTag .= "<option value='$display_arr[0]=Y|$display_arr[1]=Y|$display_arr[2]=Y' $selected3>"._("3차용지 사용함")."</option>";
	$selectTag .= "</select>";
}
//debug($selectTag);
?>

<style>
#treeDiv {border:5px solid #DEDEDE; padding:5px;width:260px;float:left;background:#FFFFFF;}
#treeDiv span {cursor:pointer;margin-left:3px;vertical-align:middle;}
#fm_category {border:5px solid #DEDEDE;width:260px;}
#desc {border:5px solid #DEDEDE; padding:5px;width:260px;background:#FFFFFF;margin-top:10px;}
.treeInput {height:12px;vertical-align:middle;width:80px;}
.addComplete {cursor:pointer;margin-left:5px;}
</style>

<table border="0" style="margin: auto;">
<tr>
	<td valign="top">
	<!-- 1차 카테고리 -->
	<form method="post" action="/lib/extra_option/set_extra_option_item_update.php">
	<input type="hidden" name="mode" value="order_indexS3"/>
	<input type="hidden" id="goodsno" name="goodsno" value="<?=$_GET[goodsno]?>"/>
			
	<div id="treeDiv">
	<!-- 용지 사용 여부 설정. -->
	<div style="float: left;"><?=$selectTag;?></div>
	
	<div style="border-bottom:1px solid #DEDEDE;padding-bottom:5px;margin-bottom:7px;" align="right">	
	<img src="../img/bt_copy.png" class="hand" id="add_btn"/>
	<img src="../img/bt_del.png" class="hand" id="del_btn"/>
	<img src="../img/bt_up.png" class="hand" id="moveup_btn"/>
	<img src="../img/bt_down.png" class="hand" id="movedn_btn"/>
	<input type="image" src="../img/bt_save.png" class="hand"/>
	</div>
	<span catno="0" id="treetop" option_kind_index="<?=$oidx?>" >※ <?=$optgt_title?> <?=_("용지")?></span>
	<ul id="treeul">
	<? foreach ($category as $k1=>$v1) { ?>
	<li>
		<span catno="1" option_kind_index="<?=$v1[option_kind_index]?>" item_index="<?=$v1[option_item_index]?>" sub_kind_index="<?=$v1[sub_kind_index]?>" item_name="<?=$v1[item_name]?>" option_group_type="<?=$v1[option_group_type]?>" option_kind_code="<?=$v1[option_kind_code]?>"><?=$v1[item_name]?></span>
		<input type="hidden" name="sort[]" value="<?=$v1[option_kind_index]?>|<?=$v1[option_kind_code]?>|<?=$v1[parent_item_name]?>|<?=$v1[item_name]?>">
		<? if ($v1[sub]) { ?>
		<ul>
		<? foreach ($v1[sub] as $k2=>$v2) { ?>
		<li>
			<span catno="2" option_kind_index="<?=$v2[option_kind_index]?>" item_index="<?=$v2[option_item_index]?>" sub_kind_index="<?=$v2[sub_kind_index]?>" item_name="<?=$v2[item_name]?>" option_group_type="<?=$v1[option_group_type]?>" option_kind_code="<?=$v2[option_kind_code]?>"><?=$v2[item_name]?></span>
			<input type="hidden" name="sort[]" value="<?=$v2[option_kind_index]?>|<?=$v2[option_kind_code]?>|<?=$v2[parent_item_name]?>|<?=$v2[item_name]?>">
			<!-- 3차 카테고리 -->
			<? if ($v2[sub]) { ?>
			<ul>
			<? foreach ($v2[sub] as $k3=>$v3) { ?>
			<li>
				<span catno="3" option_kind_index="<?=$v3[option_kind_index]?>" item_index="<?=$v3[option_item_index]?>" sub_kind_index="<?=$v3[sub_kind_index]?>" item_name="<?=$v3[item_name]?>" option_group_type="<?=$v1[option_group_type]?>" option_kind_code="<?=$v3[option_kind_code]?>"><?=$v3[item_name]?></span>
				<input type="hidden" name="sort[]" value="<?=$v3[option_kind_index]?>|<?=$v3[option_kind_code]?>|<?=$v3[parent_item_name]?>|<?=$v3[item_name]?>">
			</li>
			<? } ?>
			</ul>
			<? } ?>
			<!-- /3차 카테고리 -->
		</li>
		<? } ?>
		</ul>
		<? } ?>
		<!-- /2차 카테고리 -->
	</li>
	<? } ?>
	</ul>
	</div>
	<!-- /1차 카테고리 -->

	</form>
	
	<!-- 키/아이콘 설명 -->
	<div id="desc">
	<table>
	<tr>
		<td align="center" width="20"><img src="../img/bt_copy.png" align="absmiddle"/></td>
		<td>: <?=_("신규등록")?></td>
	</tr>
	<tr>
		<td align="center"><img src="../img/bt_del.png" align="absmiddle"/></td>
		<td>: <?=_("삭제")?></td>
	</tr>
	<tr>
		<td align="center"><img src="../img/bt_down.png" align="absmiddle"/></td>
		<td>: <?=_("위치를 아래로 설정")?></td>
	</tr>
	<tr>
		<td align="center"><img src="../img/bt_up.png" align="absmiddle"/></td>
		<td>: <?=_("위치를 위로 설정")?></td>
	</tr>
	<tr>
		<td align="center"><img src="../img/bt_save.png" align="absmiddle"/></td>
		<td>: <?=_("정렬순서 저장")?></td>
	</tr>
	</table>
	</div>
	</td>
	<td valign="top">
	<div id="fm_category">

		<form method="post" onsubmit="return chkForm(this)">
	
		<table class="tb1">
		<tr>
			<th><?=_("항목 명")?></th>
			<td>
			<input type="text" id="update_name" name="update_name" required/>
			<input type="hidden" id="goodsno" name="goodsno" value="<?=$_GET[goodsno]?>"/>
			<input type="hidden" id="preset" name="preset" value="<?=$_GET[preset]?>"/>
			<input type="hidden" id="old_item_name" name="old_item_name"/>
			<input type="hidden" id="option_kind_index" name="option_kind_index"/>
			<input type="hidden" id="item_index" name="item_index"/>
			<input type="hidden" id="sub_kind_index" name="sub_kind_index"/>
			<input type="hidden" id="option_group_type" name="option_group_type"/>
			<input type="hidden" id="option_kind_code" name="option_kind_code"/>
			</td>
		</tr>
		</table>
		
		<div class="btn">
			<img src="../img/bt_apply_l.png" class="hand" id="mod_btn"/>
		</div>
		
		</form>
	
	</div>
	</td>
</tr>
<tr>
	<td colspan="2" align="center"><img src="../img/bt_submit_l.png" class="hand" id="clo_btn"/></td>
</tr>
</table>

<script src="../../js/plugin/jquery.treeview.js" type="text/javascript"></script>
<script src="../../js/plugin/jquery.treeview.edit.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../js/plugin/jquery.treeview.css" />
<script type="text/javascript">
var b_category_obj;
var b_category;
var b_target;

$j(function() {
	$j("#treeul").treeview({
		collapsed: false
	});
	setSpan();

	/* 닫기세팅 */
	$j("#clo_btn").bind("click",function(){
		//if (!confirm('정말 수정하시겠습니까?')) return;
		opener.parent.location.reload();
 		window.close();
	});
	
	/* 등록세팅 */
	$j("#add_btn").bind("click",function(){
		if ($j(".treeInput").length > 0){
			$j(".treeInput").focus();
			return;
		}
		if (b_category>=3){
			alert('<?=_("용지는 3차까지만 추가할 수 있습니다.")?>');
			return;
		}
		$j(b_category_obj).parent().find(">.expandable-hitarea").trigger("click");
		if (!b_category_obj) $j("#treetop").trigger("click");
		if (b_category_obj==$("treetop")) var target = $j("#treeul");
		else {
			var target = $j("ul",$j(b_category_obj).parent());
			if (!target.length){
				target = $j("<ul></ul>").appendTo($j(b_category_obj).parent());
			}
		}
		if (target.length > 1) target = target[0];
		b_target = target;

		var goodsNo = $j('#goodsno').val();
		var preSet = $j('#preset').val();
    	var idx = $j('#option_kind_index').val();
    	var code = $j('#option_kind_code').val();
    	var ogt = $j('#option_group_type').val();
    	var sub_idx = $j('#sub_kind_index').val();
		var parent_item_name = $j('#old_item_name').val();

		if(b_category == 0) { //1차
			parent_item_name = "";
		}else { //2차,3차이면...
			idx = sub_idx;
			sub_idx = "";
		}

	  	var addParam = "&preset=" + preSet + "&option_kind_index=" + idx + "&option_kind_code=" + code + "&option_group_type=" + ogt + "&sub_option_kind_index=" + sub_idx + "&parent_item_name=" + parent_item_name;

		var branches = $j("<li><input type='text' class='treeInput' pcatno='"+b_category+"'/><b class='addComplete'>ok</b>").appendTo(target);
		$j(target).treeview({add: branches});
		$j(".treeInput").focus();
		$j(".addComplete").bind("click",function(){
			var obj = $j(this).parent();
			var _val = $j(this).prev().val();
			if (!_val.trim()){
				alert('<?=_("항목 명을 입력해주세요.")?>');
				$j(".treeInput").focus();
				return;
			}
			$j.ajax({
				type: "POST",
				url: "/lib/extra_option/set_extra_option_data.php",
				data: "goodsno=" + goodsNo + addParam + "&f_item_name="+_val,
				success: function(ret){
					if (ret=="null"){
						alert('<?=_("항목 명을 입력해주세요.")?>');
						$j(".treeInput").focus();
						return;
					}
					//obj.html("<span catno="+ret+"  >"+_val+"</span><input type='hidden' name='sort[]' value='"+ret+"'>");
					alert('<?=_("추가 되었습니다.")?>');
					//setSpan();
					document.location.reload();
				}
			});
		});
		$j("#add_btn").trigger("click");
	});

	/* 수정세팅 */
	$j("#mod_btn").bind("click",function(){
    	var input = $j('#update_name').val();
		var goodsNo = $j('#goodsno').val();
    	var old_item_name = $j('#old_item_name').val();
    	var idx = $j('#option_kind_index').val();
    	var item_idx = $j('#item_index').val();
    	var sub_idx = $j('#sub_kind_index').val();

	  	var addParam = "&option_kind_index=" + idx + "&option_item_index=" + item_idx + "&sub_kind_index=" + sub_idx + "&update_name=" + input + "&old_item_name=" + old_item_name;

		if (!confirm('<?=_("정말 수정하시겠습니까?")?>')) return;
		$j.ajax({
			type: "POST",
			url: "/lib/extra_option/set_extra_option_item_update.php",
			data: "mode=itemNameUpdate&goodsno=" + goodsNo + addParam,
			success: function(ret){
				if (ret=="OK") document.location.reload();//setSpan();
			}
		});
	});

	/* 삭제세팅 */
	$j("#del_btn").bind("click",function(){
		if ($j(".treeInput").length > 0){
			$j(b_target).treeview({
				remove: $j('.treeInput').parent()
			});
			treeReset();
			return;
		}

		var goodsNo = $j('#goodsno').val();
    	var val = $j('#old_item_name').val();
    	var idx = $j('#option_kind_index').val();
    	var ogt = $j('#option_group_type').val();
    	var okc = $j('#option_kind_code').val();
    	var flag = "Y";

	  	var addParam = "&option_kind_index=" + idx + "&option_group_type=" + ogt + "&option_kind_code=" + okc + "&val=" + val + "&flag=" + flag;
	  	
		if (!confirm('<?=_("정말 삭제하시겠습니까?")?>')) return;
		$j.ajax({
			type: "POST",
			url: "/lib/extra_option/set_extra_option_item_update.php",
			data: "mode=regist_flag&goodsno=" + goodsNo + addParam,
			success: function(ret){
				if (ret=="OK") $j("#treeul").treeview({remove: $j(b_category_obj).parent()});
				treeReset();
			}
		});
	});

	/* 이동세팅 */
	$j("#moveup_btn").bind("click",function(){treemove(-1);});
	$j("#movedn_btn").bind("click",function(){treemove(1);});
	//$j(document).bind('keydown', 'down', function(){$j("#movedn_btn").trigger("click"); return false;});
	//$j(document).bind('keydown', 'up', function(){$j("#moveup_btn").trigger("click"); return false; });
	
	//용지 1,2,3차 사용여부.
	$j("#pageUse").change(function(){
		if (!confirm('<?=_("용지 사용 설정을 변경 하시겠습니까?")?>')) return;
		//alert($j(this).val());
		var goodsNo = $j('#goodsno').val();
		var arr_idx = $j(this).val().split('|');
		var intCount = 0;
		
		for (var i = 0; i < arr_idx.length; i++) {
			if (arr_idx[i]) {
				//alert(arr_idx[i]);
				
				var arr_val = arr_idx[i].split('=');
				//alert(arr_val[0]);
				//alert(arr_val[1]);
				$j.ajax({
					type: "POST",
					url: "/lib/extra_option/set_extra_option_item_update.php",
					data: "mode=display_flag&goodsno=" + goodsNo + "&option_kind_index=" + arr_val[0] + "&flag=" + arr_val[1],
					success: function(ret){
						if (ret=="OK") intCount++;
					}
				});

			}
		}
		
		setTimeout(function() {
			if(intCount > 0) {
				alert('<?=_("완료되었습니다.")?>');
				document.location.reload();
			}
			else {
				alert('<?=_("실패하였습니다. 다시 시도하시기 바랍니다.")?>');
			}
		}, 1500); 
		
	});

});

/* 이동함수 */
function treemove(dir){
	if (!chkAdd()) return;
	if (!b_category) return;

	var ul = $j(b_category_obj).closest('ul');
	var lis = $j(">li",ul);
	var lislen = lis.length;

	var obj1 = $j(b_category_obj);
	var idx_1 = lis.index(obj1.closest('li'));
	var idx_2 = idx_1+dir;
	
	if (dir==1 && idx_2 >= lislen) return;
	else if (dir==-1 && idx_2 < 0) return;

	var xxx = $j(">li:eq("+idx_1+")",ul).html();

	$j(">li:eq("+idx_1+")",ul).html($j(">li:eq("+idx_2+")",ul).html());
	$j(">li:eq("+idx_2+")",ul).html(xxx);
	setSpan();
	$j("*","#treeul").removeClass();
	$j("#treeul").treeview();
	$j(">span",$j(">li:eq("+idx_2+")",ul)).trigger("click");
}

/* tree link 보정 */
function setSpan(){
	$j("#treeDiv span").bind("click",function(){
		if (!chkAdd()) return;
		b_category_obj = this;
		$j("#treeul span").css("font-weight","normal");
		$j(this).css("font-weight","bold");
		b_category = $j(this).attr("catno");

		$j("#update_name").val($j(this).attr("item_name"));
		$j("#old_item_name").val($j(this).attr("item_name"));
		$j("#option_kind_index").val($j(this).attr("option_kind_index"));
		$j("#item_index").val($j(this).attr("item_index"));
		$j("#sub_kind_index").val($j(this).attr("sub_kind_index"));
		$j("#option_group_type").val($j(this).attr("option_group_type"));
		$j("#option_kind_code").val($j(this).attr("option_kind_code"));
	});
}
/* add 완료여부 체크 */
function chkAdd(){
	if ($j(".treeInput").length > 0){
		alert('<?=_("다른작업을 하시고자 한다면 카테고리 추가를 종료후 이용해주세요.")?>' + $j(".treeInput").length);
		$j(".treeInput").focus();
		return false;
	} else return true;
}

function treeReset(){
	$j("*","#treeul").removeClass();
	$j("#treeul").treeview();

	$j("#update_name").val("");
	$j("#old_item_name").val("");
	$j("#option_kind_index").val("");
	$j("#item_index").val("");
	$j("#sub_kind_index").val("");
	$j("#option_group_type").val("");
	$j("#option_kind_code").val("");
}
</script>

<? include "../_pfooter.php"; ?>