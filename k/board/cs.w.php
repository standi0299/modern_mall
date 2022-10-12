<?
include "../_header.php";
include "../_left_menu.php";

$m_pretty = new M_pretty();

$data = $m_pretty->mycsModify($cid, $_GET[no]);
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
         <?=_("1:1문의")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("1:1문의")?> <small><?=_("1:1문의 관리")?>.</small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("1:1문의 수정")?><h4>
            </div>

            <div class="panel-body panel-form">
            <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" enctype="multipart/form-data">
               <input type="hidden" name="mode" value="cs">
               <input type="hidden" name="no" value="<?=$_GET[no]?>"/>
               
               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("문의유형")?></label>
                  <div class="col-md-10">
                     <? if ($data[category]) { ?><?=$r_cs_category[$data[category]]?><? } ?>
                  </div>
               </div>
               
               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("주문번호")?></label>
                  <div class="col-md-10">
                     <? if($data[payno]) { ?><?=$data[payno]?><? } ?>
                  </div>
               </div>

               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("문의일시")?></label>
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
               
               <? if($data[img]) {
               		$filepath = "../../data/board/$cid/cs/$data[img]";
					$size = getimagesize($filepath); 
			   ?>
               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("첨부파일")?></label>
                  <div class="col-md-10">
                     <? if (is_array($size)) {
                     	  if ($size[0] > 800) $data[img_w] = "800px";
                     ?>
                     <img src="<?=$filepath?>" style="<?if($data[img_w]) { ?>width:<?=$data[img_w]?><? } ?>">
					 <? } else { ?>
					 <a href="<?=$filepath?>">파일보기</a>
					 <? } ?>
                  </div>
               </div>
               <? } ?>

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