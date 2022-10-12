<?
include "../_header.php";
include "../_left_menu.php";

$r_bid = array();
$r_bid = getBusiness();

# 상품정보
$goods = $db->fetch("select * from exm_goods where goodsno = '$_GET[goodsno]'");
$goods_cid = $db->fetch("select * from exm_goods_cid where cid = '$cid' and goodsno = '$_GET[goodsno]'");
foreach ($r_bid as $bid=>$dummy){
   $data = $db->fetch("select * from exm_price where cid = '$cid' and bid = '$bid' and mode = 'goods' and goodsno = '$goods[goodsno]'");
   $goods_bid[$bid][price] = $data[price];
   $goods_bid[$bid][reserve] = $data[reserve];
   $data = $db->fetch("select * from exm_price where cid = '$cid' and bid = '$bid' and mode = 'page' and goodsno = '$goods[goodsno]'");
   $goods_bid_page[$bid][price] = $data[price];
   $goods_bid_page[$bid][reserve] = $data[reserve];
}

# 옵션정보
$opt = $opt_cid = array();
$query = "select * from exm_goods_opt where goodsno = '$goods[goodsno]' order by osort";
$res = $db->query($query);
while ($data = $db->fetch($res)){
   $opt[$data[optno]] = $data;
}
$query = "select * from exm_goods_opt_price where cid = '$cid' and goodsno = '$goods[goodsno]'";
$res = $db->query($query);
while ($data = $db->fetch($res)){
   $opt_cid[$data[optno]] = $data;
   foreach ($r_bid as $bid=>$dummy){
      $tmp = $db->fetch("select * from exm_price where cid = '$cid' and bid = '$bid' and mode = 'opt' and goodsno = '$goods[goodsno]' and optno = '$data[optno]'");
      $opt_bid[$data[optno]][$bid][price] = $tmp[price];
      $opt_bid[$data[optno]][$bid][reserve] = $tmp[reserve];
   }
}

# 추가옵션정보
$r_addopt_bundle = $addopt = $addopt_cid = array();
$query = "select * from exm_goods_addopt_bundle where goodsno = '$goods[goodsno]' order by addopt_bundle_sort";
$res = $db->query($query);
while ($data = $db->fetch($res)){
   $r_addopt_bundle[$data[addopt_bundle_no]] = $data[addopt_bundle_name];
}

$query = "select * from exm_goods_addopt where goodsno = '$goods[goodsno]' order by addopt_sort";
$res = $db->query($query);
while ($data = $db->fetch($res)){
   $addopt[$data[addopt_bundle_no]][$data[addoptno]] = $data;
}

$query = "select * from exm_goods_addopt_price where cid = '$cid' and goodsno = '$goods[goodsno]'";
$res = $db->query($query);
while ($data = $db->fetch($res)){
   $addopt_cid[$data[addoptno]][price] = $data[addopt_aprice];
   $addopt_cid[$data[addoptno]][reserve] = $data[addopt_areserve];

   foreach ($r_bid as $bid=>$dummy){
      $tmp = $db->fetch("select * from exm_price where cid = '$cid' and bid = '$bid' and mode = 'addopt' and goodsno = '$goods[goodsno]' and addoptno = '$data[addoptno]'");
      $addopt_bid[$data[addoptno]][$bid][price] = $tmp[price];
      $addopt_bid[$data[addoptno]][$bid][reserve] = $tmp[reserve];
   }
}

# 인화옵션정보
$printopt = $printopt_cid = array();
$query = "select * from exm_goods_printopt where goodsno = '$goods[goodsno]' order by osort";
$res = $db->query($query);
while ($data = $db->fetch($res)){
   $printopt[$data[printoptnm]] = $data;
}
$query = "select * from exm_goods_printopt_price where cid = '$cid' and goodsno = '$goods[goodsno]'";
$res = $db->query($query);
while ($data = $db->fetch($res)){
   $printopt_cid[$data[printoptnm]] = $data;
   foreach ($r_bid as $bid=>$dummy){
      $tmp = $db->fetch("select * from exm_price where cid = '$cid' and bid = '$bid' and mode = 'printopt' and goodsno = '$goods[goodsno]' and printoptnm = '$data[printoptnm]'");
      $printopt_bid[$data[printoptnm]][$bid][price] = $tmp[price];
      $printopt_bid[$data[printoptnm]][$bid][reserve] = $tmp[reserve];
   }
}

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->
<div id="content" class="content">
   <!-- begin #header -->
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("가격관리")?></h4>
      </div>

      <div class="panel-body panel-form">
         <div class="form-group">
            <div class="col-md-2 form-inline"><?=goodsListImg($goods[goodsno],140)?></div>
            <div class="col-md-10 form-inline">
               <b style="font-size:13pt"><?=$goods[goodsnm]?></b>
               ( <?=_("상품 번호")?> : <b class="blue"> <?=$goods[goodsno]?></b> )
               <div><?=_("센터 판매가")?> : <b class="red" style="font-size:12pt">￦ <?=number_format($goods[price])?></b></div>
               <? if ($goods[pageprice]){ ?>
               <?=_("센터 페이지 추가가격")?> : <b class="red"><?=number_format($goods[pageprice])?></b>
               <? } ?>
               <div class="small red">* <?=_("가격정보를 공란으로 입력시에는,")?><br/> <?=_("센터정보,몰정보 순으로 상위의 정보를 쇼핑몰에서 참조하게 됩니다.")?></div>
            </div>
         </div>
      </div>
   </div>
</div>

<div id="content" class="content">
   <!-- begin #header -->
   <div class="panel panel-inverse">
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("그룹별 상품 판매가/적립금")?></h4>
      </div>

      <div class="panel-body panel-form">
         <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
            <input type="hidden" name="mode" value="aa">

            <div class="panel-body">
               <div class="table-responsive">
                  <table id="data-table" class="table table-striped table-bordered">
                     <thead>
                        <tr>
                           <th><?=_("구분")?></th>
                           <th><?=_("판매가")?></th>
                           <th><?=_("적립금")?></th>
                        </tr>
                     </thead>

                     <tbody>
                        <tr>
                           <td><?=_("몰")?></td>
                           <td><input type="text" class="form-control" name="price_cid" value="<?=$goods_cid[price]?>" pt="_pt_numplus"/></td>
                           <td><input type="text" class="form-control" name="reserve_cid" value="<?=$goods_cid[reserve]?>" pt="_pt_numplus"/></td>
                        </tr>
                        <? foreach ($r_bid as $k=>$v) { ?>
                        <tr>
                           <td><?=$v?></td>
                           <td><input type="text" class="form-control" name="price_bid[<?=$k?>]" value="<?=$goods_bid[$k][price]?>" pt="_pt_numplus"/></td>
                           <td><input type="text" class="form-control" name="reserve_bid[<?=$k?>]" value="<?=$goods_bid[$k][reserve]?>" pt="_pt_numplus"/></td>
                        </tr>
                        <? } ?>
                     </tbody>
                  </table>
               </div>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"></label>
               <div class="col-md-10">
                  <button type="submit" class="btn btn-sm btn-success">
                     <?=_("저 장")?>
                  </button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<script type="text/javascript" src="/js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/smarteditor/editorStart.js" charset="utf-8"></script>

<!--form 전송 취약점 개선 20160128 by kdk-->
<script src="../../js/webtoolkit.base64.js"></script>

<script type="text/javascript">
var oEditors = [];
smartEditorInit("contents", true, "editor", true);

function submitContents(formObj) {
   if (sendContents("contents", false)) {
      try {
         formObj.contents.value = Base64.encode(formObj.contents.value);
            return form_chk(formObj);
      } catch(e) {return false;}
   }
   return false;
}

<script src="../assets/plugins/DataTables-1.9.4/js/jquery.dataTables.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>