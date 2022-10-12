<?
include "../_header.php";
include "../_left_menu.php";

## 회원그룹 추출
$r_grp = getMemberGrp();
$r_bid = getBusiness();

//정산담당자 추출
$r_manager = getManager();
$m_order = new M_order();

### 결제방법색상
$r_paymethod_color = array(
   'c' => 'red',
   'b' => 'blue',
   'e' => 'green',
   'v' => 'sky',
   'o' => 'orange',
   'h' => 'pink'
);

$r_rid = get_release();

if($_GET[itemstep]) $_POST[itemstep] = $_GET[itemstep];

$postData = base64_encode(json_encode($_POST));
?>

<input type="hidden" name="manager_no" id="manager_no" value="<?=$manager_no?>">

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
<? include "order_list_header.php"; ?>
<? include "order_list_chart.php"; ?>
</div>
<!-- end #content -->

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>
<script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

<!-- ================== END PAGE LEVEL JS ================== -->
<script>
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
         "sPaginationType" : "bootstrap",
         "aaSorting" : [[1, "desc"]],
         "bFilter" : false,
         "aLengthMenu": [10, 25, 50, 100, 300],
         "oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
         },
         "aoColumns": [
         <? if($_GET[itemstep] && $_GET[itemstep] != 5) { ?>
         { "bSortable": false },
         <? } ?>
         { "bSortable": false },
         { "bSortable": false },
         ],
         "processing": true,
         "serverSide": true,
         <? if($_GET[itemstep] && $count[$_GET[itemstep]] == 0) { ?>
         "deferLoading": 0,
         <? } ?>
         "bAutoWidth": false,
         "ajax": $.fn.dataTable.pipeline( {
            url: './order_list_chart_page.php?postData=<?=$postData?>',
            pages: 3 // number of pages to cache
         })
      });
   });

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
</script>

<script src="../js/datatable_page.js"></script>

<script>
   function order_proc(){
      var fm = document.fm;
      var chk;
      var mode = "";
      var chkNum = 0;
      var c = document.getElementsByName('chk[]');

      for (var i = 0; i < c.length; i++) {
         if (c[i].checked) {
            chk = true;
            chkNum++;
         }
      }

      var step = "<?=$_GET[itemstep]?>";

      if(step == 1) mode = "1to2";
      else if(step == 2) mode = "2to3";
      else if(step == 3) mode = "3to4";
      else if(step == 4) mode = "4to5";
      else if(step == 91) mode = "91to92";
      else if(step == 92) mode = "92to3";

      if (chk) {
         if(confirm('<?=_("상품 진행단계를 다음 단계로 변경하시겠습니까?")?>') == true) {
            fm.mode.value = mode;
            fm.submit();
         }
      } else
         alert('<?=_("주문을 선택해주세요.")?>');
   }
</script>

<? include "../_footer_app_exec.php"; ?>