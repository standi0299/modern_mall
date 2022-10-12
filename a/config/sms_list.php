<?
include_once "../_header.php";
include_once "../_left_menu.php";
include_once "../../lib/class.sms.ilark.php";

//리스트 검색 조건 만들기
//$addWhere = "";
if ($_POST[start]) $addwhere .= " and regdt >= '$_POST[start] 00:00:00'";
if ($_POST[end]) $addwhere .= " and regdt <= '$_POST[end] 23:59:59'";

$sql = "select * from exm_log_sms where cid='$cid' $addwhere order by no desc limit 0, 10";
$res = $db->query($sql);
while($data = $db->fetch($res)){
   $list[] = $data;
}

list($totalCnt) = $db->fetch("select count(*) as cnt from exm_log_sms where cid='$cid' $addwhere",1);

//날짜 버튼 클릭 후 조회시 버튼 색상 변경
$button_color = array("yesterday" => "inverse","today" => "inverse","tdays" => "inverse","week" => "inverse","month" => "inverse","all" => "inverse");
if ($_POST[date_buton_type]) {
   $button_color[$_POST[date_buton_type]] = "warning";
}


$sms = new SmsIlark();

$postData = base64_encode(json_encode($_POST));


if ($cfg[sms_send_type] == "P")	
	$chargeLink = "popup('" .$sms->chargeUrl."',400,500);";
else
	$chargeLink = "alert('" ._("추후 지원될 예정입니다."). "');";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active"><?=_("SMS 관리")?>
      </li>
   </ol>
   <h1 class="page-header"><?=_("SMS 발송 내역")?> <small><?=_("SMS 발송 내역")?></small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;"  class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("SMS 발송 내역")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" method="post" action="sms_list.php">
                  <!--<input type="hidden" name="p_state" value="<?=$_REQUEST[p_state]?>">-->
                  <input type="hidden" name="date_buton_type" id="date_buton_type">

                  <div class="form-group">
                     <div class="col-md-8">
                        <label class="col-md-2 control-label text-right"><?=_("sms 건수")?> : </label>
                        <label class="col-md-2 control-label text-left"><?=$sms->limit?> <?=_("건")?></label>
                        <button type="button" class="btn btn-sm btn-success" onclick="<?=$chargeLink?>"><?=_("충전하기")?> </button>                        	
                        <button type="button" class="btn btn-sm btn-default" onclick="popup('<?=$sms->logUrl?>',600,600);"><?=_("충전내역")?> </button>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("발송 일자")?></label>
                     <div class="col-md-3">
                        <div class="input-group input-daterange">
                           <input type="text" class="form-control" name="start" placeholder="Date Start" value="<?=$_POST[start]?>" />
                           <span class="input-group-addon"> ~ </span>
                           <input type="text" class="form-control" name="end" placeholder="Date End" value="<?=$_POST[end]?>" />
                        </div>
                     </div>
                     <div class="col-md-4">
                        <button type="button" class="btn btn-sm btn-<?=$button_color[today]?>" onclick="regdtOnlyOne('today','start', 'today'); regdtOnlyOne('today','end');"><?=_("오늘")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[tdays]?>" onclick="regdtOnlyOne('tdays','start', 'tdays'); regdtOnlyOne('today','end');"><?=_("3일")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[week]?>" onclick="regdtOnlyOne('week','start', 'week'); regdtOnlyOne('today','end');"><?=_("1주일")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[month]?>" onclick="regdtOnlyOne('month','start', 'month'); regdtOnlyOne('today','end');"><?=_("1달")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[all]?>" onclick="regdtOnlyOne('all','start', 'all'); regdtOnlyOne('today','end');"><?=_("전체")?>
                        </button>
                     </div>
                     <div class="col-md-4">
                        <button type="submit" class="btn btn-sm btn-success"><?=_("조 회")?></button>
                     </div>
                  </div>
               </form>
            </div>

            <div class="panel-body">
               <div class="table-responsive">
                  <table id="data-table" class="table table-striped table-bordered">
                     <thead>
                        <tr>
                           <th><?=_("번호")?></th>
                           <th><?=_("발송일자")?></th>
                           <th><?=_("받는사람")?></th>
                           <th><?=_("제목")?></th>
                           <th><?=_("건수")?></th>
                        </tr>
                     </thead>

                     <tbody>
                        <?if($list) { ?>
                           <? foreach ($list as $key => $value) { ?>
                              <tr class="<?=$tr_class?>">
                                 <td><?=$key + 1?></td>
                                 <td><?=substr($value[regdt],0,10)?></td>
                                 <td><?=$value[number]?></td>
                                 <td><?=$value[msg]?></td>
                                 <td><?=$value[call]?></td>
                              </tr>
                        <? } } ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
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
         "aLengthMenu": [10, 25, 50],
         "oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
         },
         "aoColumns": [
         { "bSortable": false },
         { "bSortable": true },
         { "bSortable": true },
         { "bSortable": false },
         { "bSortable": false },
         ],
         "processing": true,
         "serverSide": true,
         "deferLoading": <?=$totalCnt?>,
         "ajax": $.fn.dataTable.pipeline( {
            url: './sms_list_page.php?postData=<?=$postData?>',
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