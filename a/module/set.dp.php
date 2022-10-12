<?
include_once "../_pheader.php";
/*
include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

###메인화면 상품 블럭 설정###

$tableName = "md_main_block";
$selectArr = "*";
$whereArr = array("cid" => "$cid"); //, "id" => "$_GET[id]"
$data = SelectListTable($tableName, $selectArr, $whereArr, false, "order by order_by asc", "");

//debug($data);
*/

$r_brand = get_brand();

$query = "
select 
	*,
	if(b.price is null,a.price,b.price) price,
	if(
		b.price is null,
		round(convert(cprice - a.price,signed)/cprice*100),
		round(convert(cprice - b.price,signed)/cprice*100)
	) dcper
from
	exm_goods a
	inner join exm_goods_cid b on a.goodsno = b.goodsno
	inner join exm_dp_link c on a.goodsno = c.goodsno and b.cid = c.cid
where
	c.dpno	= '$_GET[dpcode]'
	and b.cid = '$cid'
	and b.nodp = 0
order by seq
";
$res = $db->query($query);
$loop = array();
while ($data=$db->fetch($res)){
	$loop[] = $data;
}

$data = $db->fetch("select * from exm_goods_dp where cid = '$cid' and dpno = '$_GET[dpcode]'");
?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <div id="content" class="content">
	<form method="post" action="set_indb.php" onsubmit="$j('input').attr('disabled',false);$j('input','#search_ul').attr('disabled',true);">
	<input type="hidden" name="mode" value="set_dp"/>
	<input type="hidden" name="dpno" value="<?=$_GET[dpcode]?>"/>   	
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("진열 상품 관리")?></a>
            </div>
         </div>
      </div>
      
		<div class="row">
		    <div class="col-md-12">
		    	<div class="panel panel-inverse">
		            <div class="panel-heading">
		               <h4 class="panel-title"><?=_("검색")?></h4>
		        	</div>
		        	<div class="panel-body panel-form">
		            	<!--contents-->
							<table class="tb1" id="searchTop">
							<tr>
								<th><?=_("분류")?></th>
								<td colspan="4">
								<div id="category">
								<select name="catno[]"><option value=""><?=_("1차 분류선택")?></option></select>
								<select name="catno[]"><option value=""><?=_("2차 분류선택")?></option></select>
								<select name="catno[]"><option value=""><?=_("3차 분류선택")?></option></select>
								<select name="catno[]"><option value=""><?=_("4차 분류선택")?></option></select>
								</div>
								<script src="../../js/category.js"></script>
								<script>
								var catno = new category('category','<?=$_GET[catno]?>');
								</script>
								</td>
							</tr>
							<tr>
								<th><?=_("브랜드검색")?></th>
								<td valign="top" colspan="4">
								<select name="brandno">
								<option value=""><?=_("브랜드선택")?></option>
								<? foreach ($r_brand as $k=>$v){ ?>
								<option value="<?=$k?>"><?=$v?></option>
								<? } ?>
								</select>
								</td>
							</tr>
							<tr>
								<th><?=_("상품번호")?></th>
								<td valign="top"><input type="text" name="goodsno" size="10"/></td>
								<th><?=_("상품검색")?></th>
								<td valign="top"><input type="text" name="sword" class="w300"/></td>
								<td rowspan="2" width="53">
								<button type="button" class="btn btn-sm btn-inverse" onclick="search_goods(1)"><?=_("검색")?></button>
								</td>
							</tr>
							<tr>
								<th><?=_("가격")?></th>
								<td colspan="3">
								<span class="absmiddle">
								<input type="text" name="price1" size="10" pt="_pt_numplus"/><?=_("원")?> ~
								<input type="text" name="price2" size="10" pt="_pt_numplus"/><?=_("원")?>
								</span>
								
								<span class="red absmiddle"><?=_("품절상품제외")?></span>
								<input type="checkbox" name="runout" value="1" class="absmiddle"/>
								<span class="red absmiddle"><?=_("미진열상품제외")?></span>
								<input type="checkbox" name="nodp" value="1" class="absmiddle"/>
							
								</td>
							</tr>
							</table>		            	
                        <!--contents-->   
		            </div>
				</div>
		    </div>			
	    	<div class="col-md-12">
				<div class="panel panel-inverse">
				    <div class="panel-heading">
				        <h4 class="panel-title"><?=_("진열")?></h4>
				    </div>
				    <div class="panel-body panel-form">
                        <!--contents-->
							<table border="1" bordercolor="#DEDEDE" cellspacing="0" id="dpTb" width="100%">
							<tr>
								<th width="50%" height="30" style="font:bold 13pt 돋움"><?=_("진열상품")?></th>
								<th style="font:bold 13pt 돋움">
								<?=_("검색상품")?>
								<select name="orderby" onchange="search_goods(1)">
								<option value="regdt desc"><?=_("신상품순")?></option>
								<option value="cid_price desc"><?=_("높은가격순")?></option>
								<option value="cid_price asc"><?=_("낮은가격순")?></option>
								<option value="dc desc"><?=_("금액할인순")?></option>
								<option value="dcper desc"><?=_("%할인순")?></option>
								</select>
								</th>
							</tr>
							<tr>
								<td valign="top" class="scroll" height="350">
							
								<div class="scroll goodsDiv" style="overflow-y:scroll;height:100%;padding:5px;">
								<ul id="dp_ul">
								<? foreach ($loop as $k=>$v){ ?>
								<li>
								<table border="1" class="goodsTb">
								<tr>
									<th><?=goodsListImg($v[goodsno],50)?></th>
									<td>
									<div><?=_("상품번호")?> : <b class="eng"><?=$v[goodsno]?></b></div>
									<div><?=$v[goodsnm]?></div>
									<div class="eng">
									<? if ($v[cprice] && $v[cprice] > $v[price]){ ?>
									(<b class="red"><?=$v[dcper]?>%</b>)
									<strike class="eng"><?=number_format($v[cprice])?></strike> →
									<? } ?>
									<b><?=number_format($v[price])?></b>
									</div>
									</td>
								</tr>
								</table>
								<input type="hidden" name="goodsno[]" value="<?=$v[goodsno]?>"/>
								</li>
								<? } ?>
								</ul>
								</div>
								
								</td>
								<td valign="top" height="350">
								<div class="scroll chkScroll goodsDiv" style="overflow-y:scroll;height:350px;padding:5px;">
								<ul id="search_ul"></ul>
								</div>
								</td>
							</tr>
							</table>                        
                        <!--contents-->
				    </div>
				</div>    		
	    	</div>
		    <div class="col-md-12">
		    	<div class="panel panel-inverse">
		            <div class="panel-heading">
		               <h4 class="panel-title"><?=_("설정")?></h4>
		        	</div>
		        	<div class="panel-body panel-form">
		            	<!--contents-->
							<table class="tb1">
							<tr>
								<th><?=_("코드")?></th>
								<td><b><?=$cid?>_<?=$_GET[dpcode]?></b></td>
							</tr>
							<tr>
								<th><?=_("출력")?></th>
								<td>
								<?=_("가로")?> : <input type="text" name="cells" value="<?=$data[cells]?>" size="3" class="_num"/>
								X <?=_("세로")?> : <input type="text" name="rows" value="<?=$data[rows]?>" size="3" class="_num"/>
								<input type="hidden" name="hidden" />
								<!-- 2013.12.06 / minks / 상품명이 20byte이상일 때 상품명을 자르는 상품명자르기 체크박스 추가(체크 O : Y값, 체크 X : defualt값) -->
								<?=_("상품명자르기")?> <input type="checkbox" name="goodsnm_cut_flag" value="Y" class="_num" <? if($data[goodsnm_cut_flag] == "Y") echo " checked"; ?> )  />	
								(<?=_("상품명이 20byte이상일 때 상품명을 자르는 기능")?>)
								</td>
							</tr>
							<tr>
								<th><?=_("이미지너비")?></th>
								<td><input type="text" name="listimg_w" value="<?=$data[listimg_w]?>" size="4" class="_num"/> px</td>
							</tr>
							</table>
                        <!--contents-->   
		            </div>
				</div>
		    </div>			
	    </div>
	    <div class="form-group">
	        <label class="col-md-3 control-label"></label>
	        <div class="col-md-9">
				<button type="submit" class="btn btn-primary"><?=_("저 장")?></button>
	            <button type="button" class="btn btn-default" onclick="opener.parent.location.reload();window.close();"><?=_("닫  기")?></button>
	        </div>
	    </div>
	</form>
   </div>
</div>

<script type="text/javascript" src="../../js/plugin/ui/jquery.hotkeys.js"></script>
<script>
var page=1;
function search_goods(p){
	page = p;
	if (page==1){
		$j("#search_ul").html('');
	}
	var catno = '';
	$j("select","#category").each(function(){
		if (this.value){
			catno = this.value;
		}
	});
	var sword = $j("[name=sword]").val();
	var goodsno = $j("[name=goodsno]").val();
	var brandno = $j("[name=brandno]").val();
	var price1 = $j("[name=price1]").val();
	var price2 = $j("[name=price2]").val();
	var orderby = $j("[name=orderby]").val();
	var runout = ($j("[name=runout]").attr("checked")==true) ? 1:0;
	var nodp = ($j("[name=nodp]").attr("checked")==true) ? 1:0;
	$j.ajax({
		type: "POST",
		url: "set_indb.php",
		data: "mode=search_goods&catno=" + catno + "&brandno=" + brandno + "&sword=" + sword + "&goodsno=" + goodsno + "&price1=" + price1 + "&price2=" + price2 + "&runout=" + runout + "&nodp=" + nodp + "&orderby=" + orderby + "&page=" + p,
		success: function(ret){
			$j("#search_ul").html($j("#search_ul").html()+ret);
			setMove();
		}
	});
}

function chkScroll(obj){	
	if (obj.scrollHeight-obj.scrollTop==360){
		search_goods(page+1);
	}
}

function setMove(){
	$j("li","#search_ul").dblclick(function(){
		var err = false;
		var goodsno = $j("[name=goodsno[]]",$j(this)).val();
		$j("[name=goodsno[]]","#dp_ul").each(function(){
			if ($j(this).val()==goodsno){
				alert('<?=_("이미 등록된 상품입니다.")?>');
				err = true;
			}
		});
		//#6688 appendTo를 prependTo로 변경(아이템을 앞에 추가하기 위해) / 14.10.29 / kjm
		if (!err) $j(this).clone().prependTo("#dp_ul");
		setRemove();
		setclick();
	});
}

function setRemove(){
	$j("li","#dp_ul").dblclick(function(){
		$j(this).remove();
	});
}
$j(function(){
	setRemove();

	$j(".chkScroll").scroll(function(){
		if ($j(this).attr("scrollHeight") - $j(this).scrollTop() <= 360){
			search_goods(page+1);
		}
	});
	setclick();

	$j(document).bind('keydown','up',function(){move_li(-1);return false;});
	$j(document).bind('keydown','down',function(){move_li(1);return false;});
	$j(document).bind('keydown','pageup',function(){move_li("top");return false;});
	$j(document).bind('keydown','pagedown',function(){move_li("bottom");return false;});
	$j("#searchTop").bind('keydown','return',function(){search_goods(1)});

});

var setLi;

function setclick(){
	$j("li","#dp_ul").click(function(){
		if (setLi!=this){
			$j(setLi).css("background","");
		}
		$j(this).css("background","#CCCCCC");
		setLi = this;
	});
}

function move_li(dir){
	if (!setLi){
		return false;
	}

	if (dir=="top"){
		var idx = 0;
		if ( $j("li","#dp_ul").index(setLi)==0){
			return false;
		}
		$j(setLi).insertBefore($j("li:eq(0)","#dp_ul"));
		return false;
	} else if (dir=="bottom"){
		var idx = $j("li","#dp_ul").length-1;
		if ( $j("li","#dp_ul").index(setLi)==idx){
			return false;
		}
		$j(setLi).insertAfter($j("li:eq("+idx+")","#dp_ul"));
		return false;
	}

	var idx = $j("li","#dp_ul").index(setLi) + dir;

	if (idx < 0){
		return;
	}
	if (dir==1){
		$j(setLi).insertAfter($j("li:eq("+idx+")","#dp_ul"));
	} else if (dir==-1){
		$j(setLi).insertBefore($j("li:eq("+idx+")","#dp_ul"));
	}
	return false;
}
</script>

<script>$j("#dp_ul").sortable();</script>

<style>
body {background:#FFFFFF;}
#dpTb {border-collapse:collapse;}
.goodsDiv ul {list-style-type:none;}
.goodsDiv li {padding:1px 0;cursor:pointer;}
.goodsTb {width:100%;border-collapse:collapse;}
.goodsTb th {width:50px;border-collapse:collapse;padding:2px;border:1px solid #DEDEDE;}
.goodsTb th img {border:1px solid #DEDEDE;}
.goodsTb td {font:8pt '돋움';border:1px solid #DEDEDE;padding:2px 5px;}
</style>

<? include_once "../_pfooter.php"; ?>