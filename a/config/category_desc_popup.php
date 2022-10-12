<?
include_once "../_pheader.php";

$query = "select * from exm_category where cid = '$cid' order by length(catno),sort";
$res = $db -> query($query);
$category = array();
while ($data = $db->fetch($res)){
	switch (strlen($data[catno])){
		case "3":
				$category[$data[catno]] = $data;
			break;
		case "6":
				$category[substr($data[catno],0,3)][sub][$data[catno]]= $data;
			break;
		case "9":
				$category[substr($data[catno],0,3)][sub][substr($data[catno],0,6)][sub][$data[catno]] = $data;
			break;
		case "12":
				$category[substr($data[catno],0,3)][sub][substr($data[catno],0,6)][sub][substr($data[catno],0,9)][sub][$data[catno]] = $data;
			break;
	}
}

//debug($category);
?>

<style>
#ord_box {margin-left:5px;margin-right:5px}
</style>

<style>
#treeDiv {border:5px solid #DEDEDE; padding:5px;width:420px;float:left;background:#FFFFFF;}
#desc {border:5px solid #DEDEDE; padding:5px;width:420px;float:left;background:#FFFFFF;margin-top:10px;}
#fm_category {border:5px solid #DEDEDE;width:680px;}
#treeDiv span {cursor:pointer;margin-left:3px;vertical-align:middle;}
.treeInput {height:12px;vertical-align:middle;width:80px;}
.addComplete {cursor:pointer;margin-left:5px;}
</style>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("메뉴항목 순서 설정")?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-body panel-form">
            <form class="form-horizontal form-bordered" method="post" action="indb.php" name="fm">
				<input type="hidden" name="mode" value="category_sort">
				<input type="hidden" name="rurl" value="admin_popup.php?mode=modify&mid=<?=$_GET[mid]?>">
				
                <div class="panel-body">

					<!-- 1차 카테고리 -->
					<div id="treeDiv">
						<div style="border-bottom:1px solid #DEDEDE;padding-bottom:5px;margin-bottom:7px;" align="right">
							<a href="javascript:;"><img src="../../admin/img/bt_up.png" class="hand" id="moveup_btn" /></a>
							<a href="javascript:;"><img src="../../admin/img/bt_down.png" class="hand" id="movedn_btn" style="cursor: hand;" /></a>
							<input type="image" src="../../admin/img/bt_save.png" class="hand" style="vertical-align: middle;" />
						</div>
					
						<span catno="" id="treetop">※<?=_("메뉴항목(전체분류) 순서 설정")?></span>
						<ul id="treeul">
							<? foreach ($category as $k1=>$v1){ ?>
								<li>
									<span catno="<?=$k1?>" _hidden="<?=$v1[hidden]?>"><?=$v1[catnm]?></span>
									<input type="hidden" name="sort[]" value="<?=$k1?>">
									<!-- 2차 카테고리 -->
									<? if ($v1[sub]){ ?>
										<ul>
										<? foreach ($v1[sub] as $k2=>$v2){ ?>
											<li>
												<span catno="<?=$k2?>" _hidden="<?=$v2[hidden]?>"><?=$v2[catnm]?>
													<input type="hidden" name="sort[]" value="<?=$k2?>">
												</span>
													
												<!-- 3차 카테고리 -->
												<? if ($v2[sub]){ ?>
													<ul>
													<? foreach ($v2[sub] as $k3=>$v3){ ?>
														<li>
															<span catno="<?=$k3?>" _hidden="<?=$v3[hidden]?>"><?=$v3[catnm]?>
																<input type="hidden" name="sort[]" value="<?=$k3?>">
															</span>
															
															<!-- 4차 카테고리 -->
															<? if ($v3[sub]){ ?>
																<ul>
																<? foreach ($v3[sub] as $k4=>$v4){ ?>
																	<li>
																		<span catno="<?=$k4?>" _hidden="<?=$v4[hidden]?>"><?=$v4[catnm]?>
																			<input type="hidden" name="sort[]" value="<?=$k4?>">
																		</span>
																	</li>
																<? } ?>
																</ul>
															<? } ?>
															<!-- /4차 카테고리 -->
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

                </div>

			 	<div class="col-md-12">
			        <button type="submit" class="btn btn-sm btn-success"><?=_("확인")?></button>
			        <button type="button" class="btn btn-sm btn-warning" onclick="window.close();"><?=_("취소")?></button>       		
			
			 	</div>
				<p class="pull-right"></p>
            </form>
         </div>
      </div>
      
   </div>
   
</div>

<script>
	//위로이동
	function MoveUpItem(obj) {
	    if ($(obj).length == 1) {
	        var li=$(obj).closest("li");
	        selIdx = $(li).index();
	        if (selIdx > 0) {
	            var p = $(li).parent();
	            $(li).remove();
	            $(p).find('li').eq(selIdx - 1).before(li); 
	        }
	    }
	}
	//아래로이동
	function MoveDownItem(obj) {
	    if ($(obj).length == 1) {
	        var li = $(obj).closest("li");
	        selIdx = $(li).index();
	        if (selIdx < $(li).parent().children().length - 1) {
	            var p = $(li).parent();
	            $(li).remove();
	            $(p).find('li').eq(selIdx).after(li); 
	        }
	    }
	}	
</script>

<script src="../../js/plugin/jquery.treeview.js" type="text/javascript"></script>
<script src="../../js/plugin/jquery.treeview.edit.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../js/plugin/jquery.treeview.css" />
<script type="text/javascript">
var b_category_obj;
var b_category;
var b_target;

$j("[_hidden=1]").css('color','#DEDEDE');

$j(function() {
	$j("#treeul").treeview({
		collapsed: true
	});
	setSpan();
	
	$j("#add_btn").bind("click",function(){

		if ($j(".treeInput").length > 0){
			$j(".treeInput").focus();
			return;
		}
		if (b_category.length>=12){
			alert('<?=_("분류는 4차까지만 추가할 수 있습니다.")?>');
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
		var branches = $j("<li><input type='text' class='treeInput' pcatno='"+b_category+"'/><b class='addComplete'>ok</b>").appendTo(target);
		$j(target).treeview({add: branches});
		$j(".treeInput").focus();
		$j(".addComplete").bind("click",function(){
			var obj = $j(this).parent();
			var _val = $j(this).prev().val();
			var _catno = $j(this).prev().attr("pcatno");
			if (!_val.trim()){
				alert('<?=_("카테고리 명을 입력해주세요.")?>');
				$j(".treeInput").focus();
				return;
			}
			$j.ajax({
				type: "POST",
				url: "indb.php",
				data: "mode=category_add&catno=" + _catno + "&catnm="+_val,
				success: function(ret){
					if (ret=="null"){
						alert('<?=_("카테고리 명을 입력해주세요.")?>');
						$j(".treeInput").focus();
						return;
					}
					obj.html("<span catno="+ret+">"+_val+"</span><input type='hidden' name='sort[]' value='"+ret+"'>");
					setSpan();
				}
			});
		});
		$j("#add_btn").trigger("click");
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

		if (!confirm('<?=_("정말 삭제하시겠습니까?")?>')) return;
		$j.ajax({
			type: "POST",
			url: "indb.php",
			data: "mode=category_del&catno=" + b_category,
			success: function(ret){
				if (ret=="ok"){
					$j("#treeul").treeview({remove: $j(b_category_obj).parent()});
					treeReset();
				} else if (ret=="goods"){
					alert('<?=_("매칭되어있는 상품이 존재하는 분류는 삭제할 수 없습니다.")?>');
					return;
				}
			}
		});

	});
	/* 숨김세팅 */
	$j("#hidden_btn").bind("click",function(){

		if (!chkAdd()) return;
		if (!b_category) return;

		$j.ajax({
			type: "POST",
			url: "indb.php",
			data: "mode=category_hidden&catno=" + b_category,
			success: function(ret){
				if (ret==1) $j(b_category_obj).css('color','#DEDEDE');
				else $j(b_category_obj).css('color','');
			}
		});

	});

	/* 이동세팅 */
	$j("#moveup_btn").bind("click",function(){treemove(-1);});
	$j("#movedn_btn").bind("click",function(){treemove(1);});
	$j(document).bind('keydown', 'down', function(){$j("#movedn_btn").trigger("click"); return false;});
	$j(document).bind('keydown', 'up', function(){$j("#moveup_btn").trigger("click"); return false; });
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
		//document.frm_category.location.href = "category.i.php?catno="+b_category;
		$j("#frm_category").attr("src", "category.i.php?catno="+b_category);
	});
}
/* add 완료여부 체크 */
function chkAdd(){
	if ($j(".treeInput").length > 0){
		alert('<?=_("다른작업을 하시고자 한다면 카테고리 추가를 종료후 이용해주세요.")?>');
		$j(".treeInput").focus();
		return false;
	} else return true;
}

function treeReset(){
	$j("*","#treeul").removeClass();
	$j("#treeul").treeview();
}
</script>

<? include_once "../_pfooter.php"; ?>