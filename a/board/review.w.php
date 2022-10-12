<?

include "../_header.php";
include "../_left_menu.php";
include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

$tableName = "exm_review";
$bRegistFlag = false;
$orderby = "";
$selectArr = "*";
$whereArr = array("cid" => "$cid", "no" => "$_GET[no]");

$data = SelectInfoTable($tableName, $selectArr, $whereArr, $bRegistFlag, $orderby);

if(!$data[emoney]) $data[emoney] = 0;

if($data[goodsno]) {
	//상품정보
	$tableName = "exm_goods";
	$bRegistFlag = false;
	$orderby = "";
	$selectArr = "goodsnm,price";
	$whereArr = array("goodsno" => "$data[goodsno]");

	$goods = SelectInfoTable($tableName, $selectArr, $whereArr, $bRegistFlag, $orderby);
	//debug($goods);
	if($goods) {
		$data[goodsnm] = $goods[goodsnm]; 
		$data[price] = $goods[price];
	}

	//상품정보
	$tableName = "exm_goods_cid";
	$bRegistFlag = false;
	$orderby = "";
	$selectArr = "clistimg";
	$whereArr = array("cid" => "$cid", "goodsno" => "$data[goodsno]");

	$goodsCid = SelectInfoTable($tableName, $selectArr, $whereArr, $bRegistFlag, $orderby);
	//debug($goodsCid);
	if($goodsCid) {
		$data[clistimg] = $goodsCid[clistimg]; 
	}
}

if ($data[img]) {
	$size = getimagesize("../../data/review/$cid/$data[payno]/$data[ordno]/$data[ordseq]/$data[img]");
	if ($size[0] > 800) $data[img_w] = "800px";
}

if (!$data[review_deny_admin]) $checked[review_deny_admin][0] = "checked";
if (!$data[review_deny_user]) $checked[review_deny_user][0] = "checked";

$checked[review_deny_admin][$data[review_deny_admin]] = "checked";
$checked[review_deny_user][$data[review_deny_user]] = "checked";
$selected[degree][$data[degree]] = "selected";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active">
         <?=_("상품후기")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("상품후기")?> <small><?=_("상품후기 정보를 보실 수 있습니다..")?>.</small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("상품후기 수정")?><h4>
            </div>

            <div class="panel-body panel-form">
            <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" enctype="multipart/form-data" onsubmit="return chkForm(this)">
               <input type="hidden" name="mode" value="review">
               <input type="hidden" name="no" value="<?=$_GET[no]?>"/>
               <input type="hidden" name="rurl" value="<?=$_SERVER[HTTP_REFERER]?>">
               <input type="hidden" name="mid" value="<?=$data[mid]?>">
			   <input type="hidden" name="payno" value="<?=$data[payno]?>">
			   <input type="hidden" name="ordno" value="<?=$data[ordno]?>">
			   <input type="hidden" name="ordseq" value="<?=$data[ordseq]?>">
			   <input type="hidden" name="original_emoney" value="<?=$data[emoney]?>">
			   <input type="hidden" name="original_img" value="<?=$data[img]?>">

               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("후기상품")?></label>
                  <div class="col-md-10">
                  	<div style="float:left; padding-right:10px;">
						<a href="../goods/view.php?goodsno=<?=$data[goodsno]?>" target="_blank"><img src="http://<?=$cfg_center[host]?>/data/goods/<?=$cid?>/s/<?=$data[goodsno]?>/<?=$data[clistimg]?>"></a>
					</div>
                  	<label class="col-md-4 control-label"><?=_("상품명")?> : <?=strip_tags($data[goodsnm])?></label>
                    <label class="col-md-3 control-label"><?=_("판매가격")?> : <b><?=number_format($data[price])?><?=_("원")?></b></label> 
                  </div>
               </div>
               
               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("구분")?></label>
                  <div class="col-md-10">
                     <?=$r_kind[$data[kind]]?>
                  </div>
               </div>
               
               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("주문번호")?></label>
                  <div class="col-md-10">
                     <a href="javascript:;" onclick="popup('../order/order_detail_popup.php?payno=<?=$data[payno]?>',1200,750)"><b><?=$data[payno]?></b></a>_<?=$data[ordno]?>_<?=$data[ordseq]?>
                  </div>
               </div>
               
               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("작성일자")?></label>
                  <div class="col-md-10">
                     <?=$data[regdt]?>
                  </div>
               </div>
               
               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("작성자")?></label>
                  <div class="col-md-10">
                  	 <? if ($value[mid]) { ?>
                     <?=$data[name]?> (<?=$data[mid]?>)
                     <? } else { ?> 
                     <?=$data[name]?> (<?=_("비회원")?>)
                     <? } ?>
                  </div>
               </div>

               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("상품평")?></label>
                  <div class="col-md-10 form-inline">
                     <select name="degree" class="form-control">
						<? foreach ($r_degree as $k => $v) { ?>
						<option value="<?=$k?>" <?=$selected[degree][$k]?>><?=$v?></option>
						<? } ?>
					</select>
                  </div>
               </div>
               
               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("적립금")?></label>
                  <div class="col-md-10 form-inline">
                     <input type="text" name="emoney" class="form-control" value="<?=$data[emoney]?>" maxlength="7" size="8" required type2="number">
                  </div>
               </div>
               
               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("적립일자")?></label>
                  <div class="col-md-10">
                     <? if ($data[emoneydt] > 0) { ?>
						<?=$data[emoneydt]?>
					<? } ?>
                  </div>
               </div>

               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("관리자 노출제한")?></label>
                  <div class="col-md-10">
					<input type="radio" name="review_deny_admin" class="radio-inline" value="0" <?=$checked[review_deny_admin][0]?>> <?=_("노출")?>
					<input type="radio" name="review_deny_admin" class="radio-inline" value="1" <?=$checked[review_deny_admin][1]?>> <?=_("노출안함")?>                    
                  </div>
               </div>

               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("사용자 노출제한")?></label>
                  <div class="col-md-10">
					<input type="radio" name="review_deny_user" class="radio-inline" value="0" <?=$checked[review_deny_user][0]?> disabled> <?=_("노출")?>
					<input type="radio" name="review_deny_user" class="radio-inline" value="1" <?=$checked[review_deny_user][1]?> disabled> <?=_("노출안함")?>               
                  </div>
               </div>

               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("제목")?></label>
                  <div class="col-md-10">
                     <input type="text" name="subject" class="form-control" value="<?=$data[subject]?>">
                  </div>
               </div>
               
               <? if ($data[kind] == "photo") { ?>
               		<div class="form-group">
	                  <label class="col-md-2 control-label"><?=_("이미지")?></label>
	                  <div class="col-md-10 form-inline">
	                  	 <input type="file" name="img" class="form-control" size="40">
	                  	 <? if ($data[img]) { ?>
	                  	 	  <div style="margin-left:5px;display:inline-block;cursor:pointer;" onclick="$j(this).closest('div').next().toggle();"><?=_("이미지보기")?></div>
							  <div style="margin:10px 0 5px;display:none;"><img src="../../data/review/<?=$cid?>/<?=$data[img]?>" style="<?if($data[img_w]) { ?>width:<?=$data[img_w]?><? } ?>"></div>
						 <? } ?>
	                  </div>
	                </div>
			   <? } ?>

               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("내용")?></label>
                  <div class="col-md-10">
                     <textarea name="content" class="form-control" style="height:200px"><?=$data[content]?></textarea>
                  </div>
               </div>

               <!--<div class="form-group">
                  <label class="col-md-2 control-label"><?=_("답변")?></label>
                  <div class="col-md-10">
                     <? if ($data[status]==2){ ?>
                     <div style="text-align:right;">
                        <b><?=$data[admin_name]?></b> <span>(<?=$data[reply_mid]?>)</span><span>: <?=$data[replydt]?></span>
                     </div>
                     <? } ?>
                     <textarea name="reply" style="width:100%;height:200px" required><?=$data[reply]?></textarea>
                  </div>
               </div>-->

               <div class="form-group">
                  <div class="col-md-12">
                     <button type="submit" class="btn btn-sm btn-info">
                        <?=_("확인")?>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
<!-- end #content -->
<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>