<?

include "../_header.php";
include "../_left_menu.php";

$mode = "address_write";
if($_GET['addressno']){
	$query = "select * from exm_address where cid = '$cid' and addressno = '$_GET[addressno]'";
	$res = $db->fetch($query);
	$mode = "address_modify";
}
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
         <?=_("주소수정")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("주소수정")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("주소수정")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" >
                  <input type="hidden" name="mode" value="<?=$mode?>">
                  <input type="hidden" name="addressno" value="<?=$_GET['addressno']?>">
                  <input type="hidden" name="cid" value="<?=$cid?>">

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("대리점명")?></label>
                     <div class="col-md-10 form-inline">
                       <input type="text" class="form-control" name="addressnm" value="<?=$res[addressnm]?>" required>
                     </div>
                  </div>

                 <div class="form-group">
                   <label class="col-md-2 control-label"><?=_("받는사람")?></label>
                   <div class="col-md-10 form-inline">
                     <input type="text" class="form-control" name="receiver_name" value="<?=$res[receiver_name]?>" required>
                   </div>
                 </div>

                 <div class="form-group">
                   <label class="col-md-2 control-label"><?=_("전화번호")?></label>
                   <div class="col-md-10 form-inline">
                     <input type="text" class="form-control" name="receiver_phone" value="<?=$res[receiver_phone]?>" required>
                   </div>
                 </div>

                 <div class="form-group">
                   <label class="col-md-2 control-label"><?=_("핸드폰번호")?></label>
                   <div class="col-md-10 form-inline">
                     <input type="text" class="form-control" name="receiver_mobile" value="<?=$res[receiver_mobile]?>" required>
                   </div>
                 </div>

                 <div class="form-group">
                   <label class="col-md-2 control-label"><?=_("우편번호")?></label>
                   <div class="col-md-10 form-inline">
                     <input type="text" class="form-control" name="receiver_zipcode" id="zipcode" value="<?=$res[receiver_zipcode]?>" required>
                     <i class="fa fa-search" style="cursor: pointer;" onclick="javascript:popupZipcode<?=$language_locale?>()"></i><br><br>
                   </div>
                 </div>

                 <div class="form-group">
                   <label class="col-md-2 control-label"><?=_("주소")?></label>
                   <div class="col-md-10 ui-sortable">
                     <input type="text" class="form-control" name="address" value="<?=$res[receiver_addr]?>" required>
                   </div>
                 </div>

                 <div class="form-group">
                   <label class="col-md-2 control-label"><?=_("상세주소")?></label>
                  <div class="col-md-10 ui-sortable">
                    <input type="text" class="form-control" name="receiver_addr_sub" value="<?=$res[receiver_addr_sub]?>" required>
                   </div>
                 </div>

                 <div class="form-group">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
                        <button type="submit" class="btn btn-sm btn-success"  >
                        <? if($mode == "address_write") { ?>
                           <?=_("등록")?>
                        <?} else {?>
                           <?=_("수정")?>
                        <? } ?>
                        </button>
                        <button type="button" class="btn btn-sm btn-default" onclick="location.href='cid_address_list.php'"><?=_("취소")?></button>
                     </div>
                  </div>
               </form>
            </div>
         </div>

      </div>
   </div>
</div>


<script src="../assets/plugins/DataTables-1.9.4/js/jquery.dataTables.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>
