<?

include "../_header.php";
include "../_left_menu.php";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("택배업체관리")?></h4>
      </div>
      
      <div class="panel-body">
         <div class="table-responsive">
            <table id="data-table" class="table table-hover table-bordered">
               <thead>
                  <tr>
                     <th><?=_("번호")?></th>
                     <th><?=_("택배업체명")?></th>
                     <th><?=_("배송추적 URL")?></th>
                     <th><?=_("사용여부")?></th>
                     <th><?=_("수정")?></th>
                     <th><?=_("삭제")?></th>
                  </tr>
               </thead>
            </table>
               
            <div class="form-group">
               <div class="col-md-12">
                  <button type="button" class="btn btn-sm btn-success" onclick="popup('shipcomp_popup.php', 630, 405)"><?=_("택배업체추가")?></button>
               </div>
            </div>
         </div>
      </div>
   </div>
   
   <div class="panel-body panel-form">
      <div class="form-group">
      	 <div class="col-md-12">
      	 	<div><span class="notice">[<?=_("설명")?>]</span> <?=_("몰에서 사용하시는 택배사의 배송추적 정보를 입력하시면, 주문관리와 연동됩니다.")?></div>
      	 	<div><span class="notice">[<?=_("설명")?>]</span> <?=_("택배사별 배송조회 URL은 택배사의 시스템변경에 의해 변경될수 있으니 해당 택배사 홈페이지에서 조회하시기 바랍니다.")?></div>
      	 	<div>
      	 	   <span class="notice">[<?=_("설명")?>]</span> <?=_("택배사별 배송조회 URL")?><br />
      	 	   <div class="textIndent"><?=_("KGB택배")?> : http://www.kgbls.co.kr/sub5/trace.asp?f_slipno=</div>
      	 	   <div class="textIndent"><?=_("대한통운택배")?> : https://www.doortodoor.co.kr/parcel/doortodoor.do?fsp_action=PARC_ACT_002&fsp_cmd=retrieveInvNoACT&invc_no=</div>
      	 	   <div class="textIndent"><?=_("로젠택배")?> : http://d2d.ilogen.com/d2d/delivery/invoice_tracesearch_quick.jsp?slipno=</div>
      	 	   <div class="textIndent"><?=_("옐로우캡")?> : http://www.yellowcap.co.kr/custom/inquiry_result.asp?invoice_no=</div>
      	 	   <div class="textIndent"><?=_("우체국택배")?> : http://service.epost.go.kr/trace.RetrieveRegiPrclDeliv.postal?sid1=</div>
      	 	   <div class="textIndent"><?=_("일양택배")?> : http://www.ilyanglogis.com/functionality/tracking_result.asp?hawb_no=</div>
      	 	   <div class="textIndent"><?=_("한진택배")?> : http://www.hanjin.co.kr/Delivery_html/inquiry/result_waybill.jsp?wbl_num=</div>
      	 	   <div class="textIndent"><?=_("현대택배")?> : http://www.hlc.co.kr/hydex/jsp/tracking/trackingViewCus.jsp?InvNo=</div>
      	 	   <div class="textIndent"><?=_("CVSnet 편의점택배")?> : http://was.cvsnet.co.kr/_ver2/board/ctod_status.jsp?invoice_no=</div>
      	 	   <div class="textIndent"><?=_("CJ GLS")?> : http://nexs.cjgls.com/web/service02_01.jsp?slipno=</div>
      	 	   <div class="textIndent"><?=_("동부익스프레스")?> : http://www.dongbuexpress.co.kr/Html/Delivery/DeliveryCheckView.jsp?item_no=</div>
      	 	   <div class="textIndent"><?=_("용마로지스")?> : http://yeis.yongmalogis.co.kr/trace/etrace_yongma.asp?OrdCode=</div>
      	 	   <div class="textIndent"><?=_("이노지스택배")?> : http://www.innogis.net/trace02.asp?invoice=</div>
      	 	</div>
      	 </div>
      </div>
   </div>
</div>

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>

<script type="text/javascript">
/* Table initialisation */
$(document).ready(function() {
	$('#data-table').dataTable({
		"sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
		"sPaginationType" : "bootstrap",
		"bFilter" : true,
		"oLanguage" : {
			"sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
		},
		"aoColumns": [
		{ "bSortable": false },
		{ "bSortable": false },
		{ "bSortable": false },
		{ "bSortable": false },
		{ "bSortable": false },
		{ "bSortable": false },
		],
		"processing": false,
		"serverSide": true,
		"bAutoWidth": false,
		"ajax": $.fn.dataTable.pipeline({
			url: 'shipcomp_page.php?postData=<?=$postData?>',
			pages: 5 //number of pages to cache
		})
	});
});
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>