<?
include "../_header.php";
include "../_left_menu.php";

$query = "select * from exm_category where cid = '$cid' order by length(catno),sort";
$res = $db->query($query);
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
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<!-- begin #content -->
<div id="content" class="content">
   <!-- begin page-header -->
	<h1 class="page-header"><?=_("μνλΆλ₯")?> <small></small></h1>
	<!-- end page-header -->

	<!-- begin row -->
	<div class="row">
		<!-- begin col-6 -->
		<div class="col-md-3">
			<!-- begin panel -->
			<div class="panel panel-inverse">
            <div class="panel-body">
               <form method="post" action="indb.php" target="hidden_frm" class="form-horizontal">
                  <input type="hidden" name="mode" value="category_sort"/>
                  <div class="form-group">
                     <div class="col-md-12">
                        <div id="treeDiv">
                           <div align="right">
                              <!--
                              <img src="../../admin/img/bt_copy.png" class="hand" id="add_btn"/>
                              <img src="../../admin/img/bt_del.png" class="hand" id="del_btn"/>
                              <img src="../../admin/img/bt_up.png" class="hand" id="moveup_btn"/>
                              <img src="../../admin/img/bt_down.png" class="hand" id="movedn_btn"/>
                              <img src="../../admin/img/bt_hidden.png" class="hand" id="hidden_btn"/>
                              <input type="image" src="../../admin/img/bt_save.png"/>
                              -->
                              
                              <a class="btn btn-success btn-icon btn-circle"><i class="fa fa-plus" id="add_btn"></i></a>
                              <a class="btn btn-success btn-icon btn-circle"><i class="fa fa-times" id="del_btn"></i></a>
                              <a class="btn btn-success btn-icon btn-circle"><i class="fa fa-long-arrow-up" id="moveup_btn"></i></a>
                              <a class="btn btn-success btn-icon btn-circle"><i class="fa fa-long-arrow-down" id="movedn_btn"></i></a>
                              <a class="btn btn-success btn-icon btn-circle"><i class="fa fa-lock" id="hidden_btn"></i></a>
                              <button type="submit" class="btn btn-warning btn-icon btn-circle"><i class="fa fa-save"></i></button>

                           </div>

                           <span catno="" id="treetop">β» <?=_("μ μ²΄λΆλ₯")?></span>
                           <ul id="treeul">
                              <? foreach ($category as $k1=>$v1) { ?>
                              <li><span catno="<?=$k1?>" _hidden="<?=$v1[hidden]?>" style="cursor:pointer"><?=$v1[catnm]?></span><input type="hidden" name="sort[]" value="<?=$k1?>"><? if ($v1[sub]){ ?><ul>
                                 <? foreach ($v1[sub] as $k2=>$v2) { ?>
                                 <li><span catno="<?=$k2?>" _hidden="<?=$v2[hidden]?>" style="cursor:pointer"><?=$v2[catnm]?><input type="hidden" name="sort[]" value="<?=$k2?>"></span>
                                    <!-- 3μ°¨ μΉ΄νκ³ λ¦¬ -->
                                    <? if ($v2[sub]) { ?>
                                    <ul>
                                    <? foreach ($v2[sub] as $k3=>$v3) { ?>
                                    <li>
                                       <span catno="<?=$k3?>" _hidden="<?=$v3[hidden]?>" style="cursor:pointer"><?=$v3[catnm]?><input type="hidden" name="sort[]" value="<?=$k3?>"></span>
                                       <!-- 4μ°¨ μΉ΄νκ³ λ¦¬ -->
                                       <? if ($v3[sub]) { ?>
                                       <ul>
                                       <? foreach ($v3[sub] as $k4=>$v4) { ?>
                                       <li><span catno="<?=$k4?>" _hidden="<?=$v4[hidden]?>" style="cursor:pointer"><?=$v4[catnm]?></span><input type="hidden" name="sort[]" value="<?=$k4?>"></li>
                                       <? } ?>
                                       </ul>
                                       <? } ?>
                                       <!-- /4μ°¨ μΉ΄νκ³ λ¦¬ -->
                                    </li>
                                    <? } ?>
                                    </ul>
                                    <? } ?>
                                    <!-- /3μ°¨ μΉ΄νκ³ λ¦¬ -->
                                 </li>
                                 <? } ?>
                                 </ul>
                                 <? } ?>
                                 <!-- /2μ°¨ μΉ΄νκ³ λ¦¬ -->
                              </li>
                              <? } ?>
                           </ul>
                        </div>

                        <table>
                        <tr>
                           <td align="center" width="20"><i class="fa fa-plus"></i></td>
                           <td>: <?=_("λΆλ₯ μ κ·λ±λ‘")?></td>
                        </tr>
                        <tr>
                           <td align="center"><i class="fa fa-times"></i></td>
                           <td>: <?=_("λΆλ₯ μ­μ ")?></td>
                        </tr>
                        <tr>
                           <td align="center"><i class="fa fa-long-arrow-up"></i></td>
                           <td>: <?=_("λΆλ₯μμΉλ₯Ό μλ‘ μ€μ ")?></td>
                        </tr>
                        <tr>
                           <td align="center"><i class="fa fa-long-arrow-down"></i></td>
                           <td>: <?=_("λΆλ₯μμΉλ₯Ό μλλ‘ μ€μ ")?></td>
                        </tr>
                        <tr>
                           <td align="center"><i class="fa fa-lock"></i></td>
                           <td>: <?=_("λΆλ₯ μ¨κΉ")?></td>
                        </tr>
                        <tr>
                           <td align="center"><i class="fa fa-save"></i></td>
                           <td>: <?=_("μ λ ¬μμ μ μ₯")?></td>
                        </tr>
                        </table>

                     </div>
                  </div>
               </form>
            </div>
         </div>
         <!-- end panel -->
      </div>
		<!-- end col-6 -->
		
		<!-- begin col-6 -->
      <div class="col-md-9">
         <!-- begin panel -->
         <div class="panel panel-inverse">
            <div class="panel-body">
               <form class="form-horizontal">
                  <div class="form-group">
                     <div class="col-md-12">
                         <div id="fm_category">
                         	<iframe id="frm_category" name="frm_category" frameborder="0" width="100%" height="900" src="goods_category_sub.php"></iframe>
                         </div>
                     </div>
                  </div>
               </form>
            </div>
         </div>
         <!-- end panel -->
      </div>
      <!-- end col-6 -->
	</div>
	<!-- end row -->
</div>
<!-- end #content -->
<!-- begin scroll to top btn -->
<a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
<!-- end scroll to top btn -->

<!-- end #content -->

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
         alert('<?=_("λΆλ₯λ 4μ°¨κΉμ§λ§ μΆκ°ν  μ μμ΅λλ€.")?>');
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
      var branches = $j("<li><input type='text' class='treeInput' pcatno='"+b_category+"'/><b class='addComplete' style='cursor:pointer'>ok</b>").appendTo(target);
      $j(target).treeview({add: branches});
      $j(".treeInput").focus();
      $j(".addComplete").bind("click",function(){
         var obj = $j(this).parent();
         var _val = $j(this).prev().val();
         var _catno = $j(this).prev().attr("pcatno");
         if (!_val.trim()){
            alert('<?=_("μΉ΄νκ³ λ¦¬ λͺμ μλ ₯ν΄μ£ΌμΈμ.")?>');
            $j(".treeInput").focus();
            return;
         }
         $j.ajax({
            type: "POST",
            url: "indb.php",
            data: "mode=category_add&catno=" + _catno + "&catnm="+_val,
            success: function(ret){
               if (ret=="null"){
                  alert('<?=_("μΉ΄νκ³ λ¦¬ λͺμ μλ ₯ν΄μ£ΌμΈμ.")?>');
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

   /* μ­μ μΈν */
   $j("#del_btn").bind("click",function(){

      if ($j(".treeInput").length > 0){
         $j(b_target).treeview({
            remove: $j('.treeInput').parent()
         });
         treeReset();
         return;
      }

      if (!confirm('<?=_("μ λ§ μ­μ νμκ² μ΅λκΉ?")?>')) return;
      $j.ajax({
         type: "POST",
         url: "indb.php",
         data: "mode=category_del&catno=" + b_category,
         success: function(ret){
            if (ret=="ok"){
               $j("#treeul").treeview({remove: $j(b_category_obj).parent()});
               treeReset();
            } else if (ret=="goods"){
               alert('<?=_("λ§€μΉ­λμ΄μλ μνμ΄ μ‘΄μ¬νλ λΆλ₯λ μ­μ ν  μ μμ΅λλ€.")?>');
               return;
            }
         }
      });
   });
   /* μ¨κΉμΈν */
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

   /* μ΄λμΈν */
   $j("#moveup_btn").bind("click",function(){treemove(-1);});
   $j("#movedn_btn").bind("click",function(){treemove(1);});
   //$j(document).bind('keydown', 'down', function(){$j("#movedn_btn").trigger("click"); return false;});
   //$j(document).bind('keydown', 'up', function(){$j("#moveup_btn").trigger("click"); return false;});
});

/* μ΄λν¨μ */
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

/* tree link λ³΄μ  */
function setSpan(){
   $j("#treeDiv span").bind("click",function(){
      if (!chkAdd()) return;
      b_category_obj = this;
      $j("#treeul span").css("font-weight","normal");
      $j(this).css("font-weight","bold");
      b_category = $j(this).attr("catno");
      //document.frm_category.location.href = "category.i.php?catno="+b_category;
      $j("#frm_category").attr("src", "goods_category_sub.php?catno="+b_category);
   });
}
/* add μλ£μ¬λΆ μ²΄ν¬ */
function chkAdd(){
   if ($j(".treeInput").length > 0){
      alert('<?=_("λ€λ₯Έμμμ νμκ³ μ νλ€λ©΄ μΉ΄νκ³ λ¦¬ μΆκ°λ₯Ό μ’λ£ν μ΄μ©ν΄μ£ΌμΈμ.")?>');
      $j(".treeInput").focus();
      return false;
   } else return true;
}

function treeReset(){
   $j("*","#treeul").removeClass();
   $j("#treeul").treeview();
}
</script>

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>

<? include "../_footer_app_exec.php"; ?>