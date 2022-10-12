<?

include "../_pheader.php";
$use_mousewheel = true;

$m_goods = new M_goods();
$r_rels = array();

//몰 카테고리 분류
$cate_data = $m_goods->getCategoryList($cid);
$ca_list = makeCategorySelectOptionTag($cate_data);

### 브랜드추출
$r_brand = get_brand();

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<style type="text/css">
#obj_rels {width:330px; height:225px; margin-left:10px; padding:5px; border:1px solid #cccccc; float:left; overflow-y:scroll; position:relative;}
#obj_rels ul {list-style:none; margin:0; padding:0;}
#obj_rels li {float:left; font:8pt '돋움'; letter-spacing:-1px; margin-right:10px; width:65px; height:110px; cursor:pointer; margin-bottom:5px; overflow:hidden;}
#obj_rels img {display:block; margin-bottom:5px; border:1px solid #cccccc; width:65px;}

#obj_search {width:330px; height:225px; margin-left:10px; padding:5px; border:1px solid #cccccc; float:left; overflow-y:scroll;}
#obj_search ul {list-style:none; margin:0; padding:0;}
#obj_search li {float:left; font:8pt '돋움'; letter-spacing:-1px; margin-right:10px; width:65px; height:110px; cursor:pointer; margin-bottom:5px; overflow:hidden;}
#obj_search img {display:block; margin-bottom:5px; border:1px solid #cccccc; width:65px; }
</style>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content" style="padding:0;">	
   	  <div class="panel panel-inverse" style="margin:0;">
         <div class="panel-body panel-form">
           <form class="form-horizontal form-bordered" name="fm" action="javascript:getfData()">
         	<div class="panel-body" style="padding:0;">
         		<div class="table-responsive" style="overflow:hidden;">
         			<table id="data-table" class="table table-bordered">
         				<tbody>
         					<tr>
         						<td><?=_("카테고리")?></td>
         						<td>
         							<select name="catno[]" class="form-control">
					               	  <option value="">+ <?=_("분류 선택")?></option><?=conv_selected_option($ca_list, "")?>
					               </select>
         						</td>
         						<td><?=_("브랜드")?></td>
         						<td>
         							<select name="brandno" class="form-control">
					               	  <option value="">+ <?=_("브랜드 선택")?></option>
					               	  <? foreach ($r_brand as $k=>$v) { ?>
					               	  	<option value="<?=$k?>"><?=$v?></option>
					               	  <? } ?>
					               </select>
         						</td>
         					</tr>
         					<tr>
         						<td><?=_("상품번호")?></td>
         						<td><input type="text" class="form-control" name="goodsno" type2="number"></td>
         						<td><?=_("상품검색")?></td>
         						<td><input type="text" class="form-control" name="sword"></td>
         					</tr>
         					<tr>
         						<td><?=_("상품가격")?></td>
         						<td colspan="3">
         							<input type="text" class="form-control form-inline" name="price1" pt="_pt_numplus" type2="number" style="width:100px;display:inline-block;"><?=_("원")?> ~  
               						<input type="text" class="form-control form-inline" name="price2" pt="_pt_numplus" type2="number" style="width:100px;display:inline-block;"><?=_("원")?>&nbsp;&nbsp;&nbsp;
               						<label class="checkbox-inline" style="padding-top:0;">
					               		<input type="checkbox" name="runout" value="1"> <span class="warning"><?=_("품절상품제외")?></span>
				                   	</label>
				                   	<label class="checkbox-inline" style="padding-top:0;">
					               		<input type="checkbox" name="nodp" value="1"> <span class="warning"><?=_("미진열상품제외")?></span>
				                   	</label>
				                   	<p class="pull-right"><button type="submit" class="btn btn-xs btn-success"><?=_("검색")?></button></p>
         						</td>
         					</tr>
         				</tbody>
         			</table>
         		</div>
         	 </div>
           </form>
           
           <!-- 연결 상품 박스  ondblclick="enlargeDiv(this)" -->
	       <div id="obj_rels">
	       	  <ul>
	    	  <? foreach ($r_rels as $v) { ?>
	    	  	 <li>
	    	  	 	<input type="hidden" name="r_goodsno[<?=$_GET[grpno]?>][]" value="<?=$v[goodsno]?>">
	    	  	 	<img src="../../data/goods/s/<?=$v[goodsno]?>" width="80"><?=$v[goodsnm]?>
	    	  	 </li>
	    	  <? } ?>
	    	  </ul>
	       </div>
	       
	       <!-- 검색 상품 박스 -->
	       <div id="obj_search" onscroll="chkLimit()">
	    	  <div align="center" class="gray" style="margin-top:100px"><?=_("검색해 주시기 바랍니다.")?></div>
	       </div>
	       
	       <p class="pull-right">
	       	  <button type="button" class="btn btn-md btn-primary m-r-15" onclick="javascript:parent.closeLayer()"><?=_("확인")?></button>
	       </p>  
         </div>
      </div>
   </div>
</div>

<script type="text/javascript">
$j(function() {
	$j('input[type2=number]').css('ime-mode', 'disabled').keypress(function(event) {
		if (event.which && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	});
});

var eof = 0;
var limit = 0;
var pagenum = 40;
var blockData = 0;
var r_goodsno = [];
/*** 기존 관련상품 가져오기 ***/
var obj = parent.$j('#obj_rels_<?=$_GET[grpno]?>').html();

if (obj) {
	$j("ul", '#obj_rels').html($j('#obj_rels_<?=$_GET[grpno]?> ul', parent.document).html());
	$j("input", $('obj_rels').innerHTML).each(function() {
		r_goodsno.push(this.value);
	});
	initRemove();
}

function chkLimit() {
	var remain = $('obj_search').scrollHeight - $('obj_search').scrollTop - 310;
	if (remain == 0) getData();
}

function initMove() {
	var obj = $('obj_search').getElementsByTagName('ul')[0];
	var li = obj.getElementsByTagName('li');
	
	for (var i=0;i<li.length;i++) {
		li[i].ondblclick = moveItem;
	}
}

function initRemove() {
	var obj = $('obj_rels').getElementsByTagName('ul')[0];
	var li = obj.getElementsByTagName('li');
	
	for (var i=0;i<li.length;i++) {
		li[i].ondblclick = removeItem;
	}
}

function moveItem() {
	var obj = $j("ul", '#obj_rels');
	var goodsno = $j("input", this.outerHTML).val();

	for (i=0;i<r_goodsno.length;i++) {
		if (r_goodsno[i] == goodsno) {
			alert('<?=_("이미 등록되었습니다.")?>');
			return 0;
		}
	}
	
	r_goodsno.push(goodsno);

	$j(this.outerHTML).appendTo(obj);
	initRemove();
	syncItem();
}

function removeItem() {
	var goodsno = $j("input", this.outerHTML).val();

	for (i=0;i<r_goodsno.length;i++) {
		if (r_goodsno[i] == goodsno) {
			r_goodsno.splice(i, 1);
		}
	}
	
	$j(this).remove();
	syncItem();
}

function syncItem() {
	parent.$j('#obj_rels_<?=$_GET[grpno]?>').html($('obj_rels').innerHTML);
}

function getfData() {
	var fm = document.fm;
	$('obj_search').innerHTML = "<ul></ul>";
	eof = 0;
	limit = 0;
	getData();
}

function getData() {
	if (eof || blockData) return;
	blockData = 1;
	
	var catno = $j("[name=catno[]]").val();
	var sword = $j("[name=sword]").val();
	var brandno = $j("[name=brandno]").val();
	var goodsno = $j("[name=goodsno]").val();
	var price1 = $j("[name=price1]").val();
	var price2 = $j("[name=price2]").val();
	var runout = ($j("[name=runout]").attr("checked") == true) ? 1 : 0;
	var nodp = ($j("[name=nodp]").attr("checked") == true) ? 1 : 0;
	
	$j.ajax({
		type: "POST",
		url: "indb.php",
		data: "mode=getData&pagenum=" + pagenum + "&limit=" + limit + "&sword=" + sword + "&catno=" + catno + "&brandno=" + brandno + "&goodsno=" + goodsno + "&price1=" + price1 + "&price2=" + price2 + "&runout=" + runout + "&nodp=" + nodp + "&grpno=<?=$_GET[grpno]?>",
		success: function(msg){
			if (!msg) eof = 1;
			var obj = $('obj_search').getElementsByTagName('ul')[0];
			obj.innerHTML += msg;
			initMove();
			limit += pagenum;
			blockData = 0;
		}
	});
}

function truncateSearch() {
	$('obj_search').innerHTML = "";
}

var f_enlargeDiv = 0;

function enlargeDiv(obj) {
	f_enlargeDiv = 1 - f_enlargeDiv;
	
	if (f_enlargeDiv) {	
		obj.style.position = "absolute";
		obj.style.top = 10;
		obj.style.width = "770px";
		obj.style.height = "540px";
	} else {
		obj.style.position = "relative";
		obj.style.top = 0;
		obj.style.width = "287px";
		obj.style.height = "330px";
	}
}
</script>