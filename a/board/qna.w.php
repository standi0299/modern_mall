<?
include "../_header.php";
include "../_left_menu.php";
include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

$m_pretty = new M_pretty();

$data = $m_pretty->mycsModify($cid, $_GET[no]);

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

//debug($data[clistimg]);
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
         <?=_("상품문의")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("상품문의")?> <small><?=_("상품문의 정보를 보실 수 있습니다.")?></small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("상품문의 수정")?><h4>
            </div>

            <div class="panel-body panel-form">
            <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" enctype="multipart/form-data">
               <input type="hidden" name="mode" value="qna">
               <input type="hidden" name="no" value="<?=$_GET[no]?>"/>

               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("문의상품")?></label>
                  <div class="col-md-10">
                  	<div style="float:left; padding-right:10px;">
						<a href="/goods/view.php?goodsno=<?=$data[goodsno]?>" target="_blank"><img src="http://<?=$cfg_center[host]?>/data/goods/<?=$cid?>/s/<?=$data[goodsno]?>/<?=$data[clistimg]?>"></a>
					</div>
                  	<label class="col-md-4 control-label"><?=_("상품명")?> : <?=strip_tags($data[goodsnm])?></label>
                    <label class="col-md-3 control-label"><?=_("판매가격")?> : <b><?=number_format($data[price])?><?=_("원")?></b></label> 
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
                     <?=$data[name]?> (<?=$data[mid]?>)
                  </div>
               </div>

               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("답변여부")?></label>
                  <div class="col-md-10">
                     <?=$r_cs[$data[status]]?></b>
                  </div>
               </div>

               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("제목")?></label>
                  <div class="col-md-10">
                     <?=$data[subject]?>
                  </div>
               </div>

               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("내용")?></label>
                  <div class="col-md-10">
                     <?=$data[content]?>
                  </div>
               </div>

               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("답변")?></label>
                  <div class="col-md-10">
                     <? if ($data[status]==2){ ?>
                     <div style="text-align:right;">
                        <b><?=$data[admin_name]?></b> <span>(<?=$data[reply_mid]?>)</span><span>: <?=$data[replydt]?></span>
                     </div>
                     <? } ?>
                     <textarea name="reply" style="width:100%;height:200px" required><?=$data[reply]?></textarea>
                  </div>
               </div>

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