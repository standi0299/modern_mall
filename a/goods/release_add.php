<?
include "../_header.php";
include "../_left_menu.php";
$query = "select * from exm_release where rid = '$_GET[rid]'";

$res = $db->query($query);
$data = $db->fetch($res);

$data[phone] = explode("-",$data[phone]);

$data[order_shiptype] = explode(",",$data[ship_method]);
foreach($data[order_shiptype] as $key => $val){
   $checked[order_shiptype][$val] = "checked";
}

$checked[shiptype][$data[shiptype]] = "checked";
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
         <?=_("제작사 추가")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("제작사 추가")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("제작사 추가")?></h4>
            </div>
            
            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" onsubmit="return form_chk(this);">
                  <input type="hidden" name="mode" value="release_add">

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("아이디")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="rid" value="<?=$data[rid]?>" required/>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("회사명")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="compnm" value="<?=$data[compnm]?>" required/>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("별칭")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="nicknm" value="<?=$data[nicknm]?>" required/>
                     </div>
                  </div>

                  <!--<div class="form-group">
                     <label class="col-md-2 control-label"><?=_("대표자명")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="name" value="<?=$data[name]?>"/>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("담당자명")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="manager" value="<?=$data[manager]?>"/>
                     </div>
                  </div>-->

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("연락처")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="phone[]" maxlength="3" size="3" value=<?=$data[phone][0]?>> -
                        <input type="text" class="form-control" name="phone[]" maxlength="4" size="4" value=<?=$data[phone][1]?>> -
                        <input type="text" class="form-control" name="phone[]" maxlength="4" size="4" value=<?=$data[phone][2]?>>
                     </div>
                  </div>

                  <!--<div class="form-group">
                     <label class="col-md-2 control-label"><?=_("주소")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="zipcode" value="<?=$data[zipcode]?>" readonly>
                        <i class="fa fa-search" style="cursor: pointer;" onclick="javascript:popupZipcode()"></i><br><br>
                        <input type="text" class="form-control" name="address" value="<?=$data[address]?>" size="50">
                        <input type="text" class="form-control" name="address_sub" value="<?=$data[address_sub]?>" size="50">
                     </div>
                  </div>-->
                  
                  <? if($cfg[order_shiptype]) { ?>
                  <!--
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("배송방식")?></label>
                     <div class="col-md-10">
                        <? foreach($r_order_shiptype as $k => $v) { ?>
                           <? if($k != 0) { ?>
                           <input type="checkbox" name="order_shiptype[]" value="<?=$k?>" <?=$checked[order_shiptype][$k]?>> <?=$v?>
                           <? } ?>
                        <? } ?>
                     </div>
                  </div>
                  -->
                  <? } ?>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("배송비")?></label>
                     <div class="col-md-10">
                        <?=makeShiptypeRadioTag("shiptype", $checked[shiptype], "2")?><br>

                        <div id="shipping_0" style="display:none" class="form-inline">
                        <?=_("배송비")?> <input type="text" class="form-control" name="shipprice" value="<?=$data[shipprice]?>" disabled/> <?=_("원")?>
                        (<?=_("배송비원가")?> <input type="text" class="form-control" name="oshipprice" value="<?=$data[oshipprice]?>" disabled/> <?=_("원")?>)
                        </div>

                        <div id="shipping_1" style="display:none"><?=_("배송비가")?> <b><?=_("무료")?></b><?=_("입니다 (판매자부담)")?></div>

                        <div id="shipping_3" style="display:none" class="form-inline">
                        <?=_("주문금액이")?> <input type="text" class="form-control" name="shipconditional" value="<?=$data[shipconditional]?>" disabled> <?=_("원 미만일 경우")?>
                        <?=_("배송비")?> <input type="text" class="form-control" name="shipprice" value="<?=$data[shipprice]?>" disabled> <?=_("원")?>
                        (<?=_("배송비원가")?> <input type="text" class="form-control" name="oshipprice" value="<?=$data[oshipprice]?>" disabled/> <?=_("원")?>)
                        </div>
                        <div id="shipping_4" style="display:none"><?=_("배송비가")?> <b><?=_("무료")?></b><?=_("입니다 (구매자부담)")?></div>  
                        <div id="shipping_9" style="display:none"><?=_("배송비가")?> <b><?=_("무료")?></b><?=_("입니다.")?> </div>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("오아시스통신url")?></label>
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="oasis_url" value="<?=$data[oasis_url]?>"/> <?=_("오아시스로의 통신 위한 url입니다.")?>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("관리자메모")?></label>
                     <div class="col-md-10">
                        <textarea name="adminmemo" class="form-control" style="width:500px;height:100px;"><?=$data[adminmemo]?></textarea>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
                        <button type="submit" class="btn btn-sm btn-success" <?if($_GET[mode] =="member_modify") { ?> onclick="option_chk()" <?}?> >
                        <? if($_GET[mode] == "member_join") { ?>
                           <?=_("등록")?>
                        <?} else {?>
                           <?=_("수정")?>
                        <? } ?>
                        </button>
                        <button type="button" class="btn btn-sm btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>


<script>
var oldshipping = "<?=$data[shiptype]?>";

$j(function(){
   $j("[name=shiptype]:radio").click(function(){
      $j("#shipping_"+oldshipping).css("display","none");
      $j("input","#shipping_"+oldshipping).attr("disabled",true);
      oldshipping = $j(this).val();
      $j("#shipping_"+oldshipping).css("display","block");
      $j("input","#shipping_"+oldshipping).attr("disabled",false);
   });
   $j("[name=shiptype]:radio").each(function(){
      if ($j(this).val()=="<?=$data[shiptype]+0?>"){
         $j(this).trigger("click");
      }
   });
});

</script>

<script src="../assets/plugins/DataTables-1.9.4/js/jquery.dataTables.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>