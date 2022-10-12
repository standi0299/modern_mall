<?

include "../_header.php";
include "../_left_menu.php";

$m_etc = new M_etc();
//$m_goods = new M_goods();

$mode = ($_GET[eventno]) ? "modEvent" : "addEvent";
$r_goods = array();

//몰 카테고리 분류
/*$cate_data = $m_goods->getCategoryList($cid);
$ca_list = makeCategorySelectOptionTag($cate_data);*/

if ($_GET[eventno]) {
	### 이벤트정보추출
	$data = $m_etc->getEventInfo($cid, $_GET[eventno]);
	/*$data[ea] = explode(",", $data[ea]);
	
	$bannerpath = "../../data/event/$cid/$data[eventno]";
	$is_banner = is_file($bannerpath);
	
	if ($is_banner) {
		$size = getimagesize($bannerpath);
		
		if (is_array($size)) {
			$bannersize = ($size[0] > 800) ? "800" : $size[0] ;
		}
	}
	
	### 연결상품데이터 추출
	$data2 = $m_goods->getEventGoodsInfo($cid, $_GET[eventno]);
	foreach ($data2 as $k=>$v) {
		$max_grpno = $v[grpno];
		$r_goods[$v[grpno]][] = $v;
	}*/
}

/*if (!$data[vtype]) $data[vtype] = 1;
$flag[$data[vtype]] = "id='flag'";*/

$checked[use_comment][$data[use_comment]+0] = "checked";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<!--<style type="text/css">
.obj_rels {width:800px; height:100%; height:130px; border:1px solid #cccccc; margin:10px; padding:5px; overflow-y:scroll;}
.obj_rels ul {list-style:none; margin:0; padding:0;}
.obj_rels li {float:left; font:8pt '돋움'; letter-spacing:-1px; margin-right:10px; width:65px; height:110px; cursor:pointer; margin-bottom:5px; overflow:hidden;}
.obj_rels img {display:block; margin-bottom:5px; border:1px solid #cccccc; width:65px;}
</style>-->

<div id="content" class="content">
   <!-- begin #header -->
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active">
         <?=_("이벤트등록")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("이벤트등록")?> <small><?=_("이벤트 정보를 등록 및 수정하실 수 있습니다.")?></small></h1>
   
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" enctype="multipart/form-data" onsubmit="return submitContents(this);">
   <input type="hidden" name="mode" value="<?=$mode?>" />
   <input type="hidden" name="eventno" value="<?=$_GET[eventno]?>" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("이벤트등록")?></h4>
      </div>

      <div class="panel-body panel-form">
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("이벤트명")?></label>
            <div class="col-md-10">
               <input type="text" class="form-control" name="title" value="<?=$data[title]?>" pt="_pt_txt">
            </div>
         </div>

         <!--<div class="form-group">
            <label class="col-md-2 control-label"><?=_("카테고리")?></label>
            <div class="col-md-10 form-inline">
               <select name="catno[]" class="form-control">
               	  <option value="">+ <?=_("분류 선택")?></option><?=conv_selected_option($ca_list, $data[catno])?>
               </select>
            </div>
         </div>-->
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("이벤트기간")?></label>
            <div class="col-md-3">
               <div class="input-group input-daterange">
               	  <input type="text" class="form-control" name="sdate" placeholder="Date Start" value="<?=$data[sdate]?>">
               	  <span class="input-group-addon"> ~ </span>
               	  <input type="text" class="form-control" name="edate" placeholder="Date End" value="<?=$data[edate]?>">
               </div>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("코멘트")?></label>
            <div class="col-md-10">
               <input type="radio" class="radio-inline" name="use_comment" value="0" <?=$checked[use_comment][0]?>> <?=_("사용안함")?>
      	 	   <input type="radio" class="radio-inline" name="use_comment" value="1" <?=$checked[use_comment][1]?>> <?=_("사용")?>
            </div>
         </div>
         
         <!--<div class="form-group">
            <label class="col-md-2 control-label"><?=_("배너이미지")?></label>
            <div class="col-md-10 form-inline">
               <? if ($is_banner) { ?><img src="<?=$bannerpath?>" style="width:<?=$bannersize?>px;" /><p></p><? } ?>
               <input type="file" class="form-control" name="banner" size="40">
			   <? if ($is_banner) { ?>&nbsp;<input type="checkbox" class="form-control" name="delbanner" /> 배너이미지 삭제<? } ?>
            </div>
         </div>-->
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("썸네일이미지")?></label>
            <div class="col-md-10">
               <input type="file" name="thumb_img" class="form-control">
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("이벤트내용")?></label>
            <div class="col-md-10">
               <textarea id="contents" name="contents" type="editor" style="width:100%;height:500px;"><?=$data[contents]?></textarea>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("관리자메모")?></label>
            <div class="col-md-10">
               <textarea id="adminmemo1" class="form-control" name="adminmemo" style="height:75px;"><?=$data[adminmemo]?></textarea>
            </div>
         </div>
      </div>
   </div>
   
   <!--<div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("상품정렬")?></h4>
      </div>

      <div class="panel-body panel-form">         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("상품개수")?></label>
            <div class="col-md-10 form-inline">
               가로 <input type="text" class="form-control" name="ea[]" value="<?=$data[ea][0]?>" size="4" type2="number"> x 
               세로 <input type="text" class="form-control" name="ea[]" value="<?=$data[ea][1]?>" size="4" type2="number">
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("정렬양식")?></label>
            <div class="col-md-10" id="vSel">
               <input type="hidden" name="vtype" value="<?=$data[vtype]?>">
			   <img <?=$flag[1]?> src="../img/vtype1.gif" onclick="vSel(this,1)" style="cursor:pointer;" />
			   <img <?=$flag[2]?> src="../img/vtype2.gif" onclick="vSel(this,2)" style="cursor:pointer;" />
			   <img <?=$flag[3]?> src="../img/vtype3.gif" onclick="vSel(this,3)" style="cursor:pointer;" />
            </div>
         </div>
      </div>
   </div>
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("상품선택")?></h4>
      </div>

      <div id="list">
      	 <? foreach ($r_goods as $grpno=>$v) { ?>
      	 	<div class="panel-body panel-form" style="border-bottom:5px solid #D6D8DD;">
	      	 	<div class="form-group">
		            <label class="col-md-2 control-label"><?=_("상단이미지")?></label>
		            <div class="col-md-10 form-inline" style="position:relative;">
		               <? $file = "../../data/event/$cid/$data[eventno]_$grpno";
					   	  $is_file = is_file($file);
	
						   if ($is_file) {
							   $size2 = getimagesize($file);
							
							   if (is_array($size2)) {
								   $filesize = ($size2[0] > 800) ? "800" : $size2[0] ;
							   }
						   }
		               if ($is_file) { ?><img src="<?=$file?>" style="width:<?=$filesize?>px;" /><p></p><? } ?>
		               <input type="file" class="form-control" name="file[<?=$grpno?>]" size="40">
					   <? if ($is_file) { ?>&nbsp;<input type="checkbox" class="form-control" name="delFile[<?=$grpno?>]" /> 상단이미지 삭제<? } ?>
		            </div>
		         </div>
		         
		         <div class="form-group">
		            <label class="col-md-2 control-label" style="height:150px;"><a href="javascript:popupLayer('event_goods_popup.php?eventno=<?=$data[eventno]?>&grpno=<?=$grpno?>')"><span class="btn btn-sm btn-primary">상품연결</span></a></label>
		            <div class="col-md-10 obj_rels" id="obj_rels_<?=$grpno?>">
		               <ul>
		               	  <? foreach ($v as $v2) { ?>
		               	  	 <li>
		               	  	 	<input type="hidden" name="r_goodsno[<?=$grpno?>][]" value="<?=$v2[goodsno]?>">
					    		<?=goodsListImg($v2[goodsno], "50", "border:1px solid #CCCCCC", $cid)?>(<?=$v2[goodsno]?>) <?=$v2[goodsnm]?>
					    	</li>
		               	  <? } ?>
		               </ul>
		            </div>
		         </div>
		         
		         <div class="form-group">
		      	 	<label class="col-md-2 control-label"></label>
		      	 	<div class="col-md-10">
		      	 		<a href="javascript:;" onclick="delnode(this.parentNode)"><span class="btn btn-sm btn-danger">삭제</span></a>
		      	 	</div>
		      	 </div>
	      	 </div>
      	 <? } ?>
      </div>
      
      <div id="clone" style="display:none;">
      	 <div class="panel-body panel-form" style="border-bottom:5px solid #D6D8DD;">
	      	 <div class="form-group">
		        <label class="col-md-2 control-label"><?=_("상단이미지")?></label>
		        <div class="col-md-10 form-inline" style="position:relative;">
		           <input type="file" class="form-control file" name="file[]" size="40">
		        </div>
		     </div>
		         
		     <div class="form-group">
		        <label class="col-md-2 control-label" style="height:150px;"><a href="#" class="goods_link"><span class="btn btn-sm btn-primary">상품연결</span></a></label>
		        <div class="col-md-10 obj_rels">
		        </div>
		     </div>
		         
		     <div class="form-group">
		      	 <label class="col-md-2 control-label"></label>
		      	 <div class="col-md-10">
		      	 	<a href="javascript:;" onclick="delnode(this.parentNode)"><span class="btn btn-sm btn-danger">삭제</span></a>
		      	 </div>
		     </div>
	      </div>
      </div>
   </div>-->
   
   <div class="row">
   	  <div class="col-md-12">
   	  	 <p class="pull-right">
   	  	 	<!--<button type="button" class="btn btn-md btn-success" onclick="javascript:clonediv()"><?=_("추가")?></button>-->
   	  	 	<button type="submit" class="btn btn-md btn-primary m-r-15"><?=_("저장")?></button>
   	  	 	<button type="button" class="btn btn-md btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
   	  	 </p>
   	  </div>
   </div>
   </form>
</div>

<? include "../_footer_app_init.php"; ?>

<script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="/js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/smarteditor/editorStart.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/webtoolkit.base64.js"></script>

<script type="text/javascript">
var handleDatepicker = function() {
	$('.input-daterange').datepicker({
		language : 'kor',
		todayHighlight : true,
		autoclose : true,
		todayBtn : true,
		format : 'yyyy-mm-dd',
	});
};

handleDatepicker();

var oEditors = [];
smartEditorInit("contents", true, "editor", true);

function submitContents(formObj) {
	if (sendContents("contents", false)) {
    	try {
    		formObj.contents.value = Base64.encode(formObj.contents.value);
            return form_chk(formObj);
        } catch(e) {
        	alert(e.message);
        	return false;
        }
    }
    return false;
}
</script>

<? include "../_footer_app_exec.php"; ?>