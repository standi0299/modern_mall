<?
include "../_header.php";
include "../_left_menu.php";

//리스트 검색 조건 만들기
//$addWhere = "";
if ($_POST[start])
   $addwhere .= " and regist_date >= '$_POST[start] 00:00:00'";
if ($_POST[end])
   $addwhere .= " and regist_date <= '$_POST[end] 23:59:59'";

if($_POST[select])
   $addwhere .= " and account_flag = '$_POST[select]'";

$sql = "select * from tb_pretty_account_point_history where cid='$cid' $addwhere order by ID desc limit 0, 10";
$res = $db -> query($sql);
while ($data = $db -> fetch($res)) {
   $list[] = $data;
}
//debug($list);
//$self = $_SERVER["HTTP_HOST"];

list($totalCnt) = $db->fetch("select count(*) as cnt from tb_pretty_account_point_history where cid='$cid' $addwhere",1);

//날짜 버튼 클릭 후 조회시 버튼 색상 변경
$button_color = array("yesterday" => "inverse", "today" => "inverse", "tdays" => "inverse", "week" => "inverse", "month" => "inverse", "all" => "inverse");
if ($_POST[date_buton_type]) {
   $button_color[$_POST[date_buton_type]] = "warning";
}

$selected[account][$_POST[select]] = "selected";

$postData = base64_encode(json_encode($_POST));

$m_mall = new M_mall();
$mall_data = $m_mall -> getInfo($cid);
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active"><?=_("포인트 관리")?>
      </li>
   </ol>
   <h1 class="page-header"><?=_("포인트 내역")?> <small><?=_("포인트 사용 내역")?></small></h1>
    
   <div class="row">
      <div class="col-md-12 col-sm-6">
         <div class="widget widget-state bg-aqua-darker"><?=_("포인트")?> : <?=number_format($mall_data[pretty_point])?> Point
            <button type="button" class="btn btn-sm btn-inverse" onclick="popup('/k/module/point_account.php',550,650);"><?=_("충전하기")?></button>
            <button type="button" class="btn btn-sm btn-inverse" onclick=""><?=_("이용안내")?></button>
         </div>
      </div>
   </div>

<?	include "./_inc_point_list.php";	?>
   
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
         "aaSorting" : [[0, "desc"]],
         "aLengthMenu": [10, 25, 50, 100],
         "bFilter" : false,
         "oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
         },
         "aoColumns": [
         { "bSortable": true },
         { "bSortable": true },
         { "bSortable": true },
         { "bSortable": true },
         { "bSortable": true },
         { "bSortable": true },
         { "bSortable": true },
         ],
         "processing": true,
         "serverSide": true,
         "deferLoading": <?=$totalCnt?>,
         "ajax": $.fn.dataTable.pipeline( {
            url: './point_list_page.php?postData=<?=$postData?>',
            pages: 5 // number of pages to cache
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
   
   //data table page ajax로 처리하기.
   $.fn.dataTable.pipeline = function ( opts ) {
      var conf = $.extend( {
         pages: 5,     // number of pages to cache
         url: '',      // script url
         data: null,   // function or object with parameters to send to the server
         method: 'POST' // Ajax HTTP method
      }, opts );
      var cacheLower = -1;
      var cacheUpper = null;
      var cacheLastRequest = null;
      var cacheLastJson = null;

      return function ( request, drawCallback, settings ) {
         var ajax          = false;
         var requestStart  = request.start;
         var drawStart     = request.start;
         var requestLength = request.length;
         var requestEnd    = requestStart + requestLength;
         if ( settings.clearCache ) {
            ajax = true;
            settings.clearCache = false;
         }
         else if ( cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper ) {
            ajax = true;
         }
         else if ( JSON.stringify( request.order )   !== JSON.stringify( cacheLastRequest.order ) ||
                   JSON.stringify( request.columns ) !== JSON.stringify( cacheLastRequest.columns ) ||
                   JSON.stringify( request.search )  !== JSON.stringify( cacheLastRequest.search )
         ) {
            ajax = true;
         }
         cacheLastRequest = $.extend( true, {}, request );
         if ( ajax ) {
            if ( requestStart < cacheLower ) {
               requestStart = requestStart - (requestLength*(conf.pages-1));
               if ( requestStart < 0 ) {
                  requestStart = 0;
               }
            }

            cacheLower = requestStart;
            cacheUpper = requestStart + (requestLength * conf.pages);

            request.start = requestStart;
            request.length = requestLength*conf.pages;
            if ( $.isFunction ( conf.data ) ) {
               var d = conf.data( request );
               if ( d ) {
                  $.extend( request, d );
               }
            }
            else if ( $.isPlainObject( conf.data ) ) {
               $.extend( request, conf.data );
            }
            settings.jqXHR = $.ajax( {
               "type":     conf.method,
               "url":      conf.url,
               "data":     request,
               "dataType": "json",
               "cache":    false,
               "success":  function ( json ) {
                  cacheLastJson = $.extend(true, {}, json);
                  if ( cacheLower != drawStart ) {
                     json.data.splice( 0, drawStart-cacheLower );
                  }
                  json.data.splice( requestLength, json.data.length );
                  drawCallback( json );
               }
            });
         }
         else {
            json = $.extend( true, {}, cacheLastJson );
            json.draw = request.draw; // Update the echo for each response
            json.data.splice( 0, requestStart-cacheLower );
            json.data.splice( requestLength, json.data.length );
            drawCallback(json);
         }
      }
   };
</script>

<? include "../_footer_app_exec.php"; ?>