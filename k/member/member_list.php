<?
include "../_header.php";
include "../_left_menu.php";
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
         <?=_("회원관리")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("회원리스트")?> <small><?=_("등록된 회원들의 정보를 보실 수 있습니다.")?></small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("회원 리스트")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="post" action="member_list.php">
                  <input type="hidden" name="mode" value="SelectDel">
                  <input type="hidden" name="flag" value="member">

                  <div class="panel-body">
                     <div class="table-responsive">
                        <table id="data-table" class="table table-striped table-bordered">
                           <thead>
                              <tr>
                                 <th><a href="javascript:chkBox('chk[]','rev')"><?=_("선택")?></a></th>
                                 <th><?=_("가입일")?></th>
                                 <th><?=_("아이디")?></th>
                                 <th><?=_("이름")?></th>
                                 <th><?=_("연락처")?></th>
                                 <th><?=_("그룹")?></th>
                                 <th><?=_("구매금액")?></th>
                                 <th><?=_("분류")?></th>
                                 <th><?=_("구분")?></th>
                                 <th><?=_("수정")?></th>
                                 <th><?=_("삭제")?></th>
                              </tr>
                           </thead>
                        </table>
                     </div>
                     <div class="form-group">
                        <button type="button" class="btn btn-sm btn-success" onClick="location.href='member_form.php';">
                           <?=_("회원등록")?>
                        </button>
                     </div>                     
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- end #content -->

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>

<!-- ================== END PAGE LEVEL JS ================== -->
<!--
<script type="text/javascript">
	document.write('<object id="ActiveLoader" classid="clsid:D1B86222-EACD-4CBB-89B8-D9DEB543CE65"');
	document.write('            codebase="http://podmanage.bluepod.kr/activexDownload/ActiveLoader4PF.cab#version=1,0,0,26"');
	document.write('            standby="Downloading iLarkComm ActiveX Control....."');
	document.write('            width="0" height="0">');
	document.write('<PARAM name="CompanyName" value="ilark">>');
	document.write('<PARAM name="InstallPath" value="ilark/downloader">');
	document.write('</object>');
</script>

<script type="text/javascript">
	function SetActiveXInit(cid, mid, self, folder_id, folder_name) {
		ActiveLoader.Update("http://podmanage.bluepod.kr/activexDownload/");
		ActiveLoader.AppExecute("downloader.exe", " down " + mid + " " + folder_id + " " + cid + " " + folder_name + " http://" + self + "/_ilark/downloader_folderlist_pretty.php http://" + self + "/_ilark/downloader_filelist_pretty.php");
	}
</script>
-->
<script>
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
			"sPaginationType" : "bootstrap",
			"aaSorting" : [[1, "desc"]],
			"bFilter" : true,
			"oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
			},
			"aoColumns": [
         { "bSortable": false },
         { "bSortable": true },
         { "bSortable": true },
         { "bSortable": true },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         ],
			"processing": true,
         "serverSide": true,
         "bAutoWidth": false,
			"ajax": $.fn.dataTable.pipeline({
            url: './member_list_page.php',
            pages: 5 // number of pages to cache
         })
      });
   });
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>