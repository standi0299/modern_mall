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
         <?=_("갤러리")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("갤러리")?> <small><?=_("공개된 갤러리 편집 내용을 베스트로 설정할 수 있다.")?></small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("갤러리")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="post" action="indb.php">
                  <input type="hidden" name="mode" value="change_gallery_flag">

                  <div class="panel-body">
                     <div class="table-responsive">
                        <table id="data-table" class="table table-striped table-bordered">
                           <thead>
                              <tr>
                                 <th><a href="javascript:chkBox('chk[]','rev')"><?=_("선택")?></a></th>
                                 <th><?=_("등록일")?></th>
                                 <th><?=_("아이디")?></th>
                                 <th><?=_("타이틀명")?></th>
                                 <th><?=_("조회수")?></th>
                                 <!--<th><?=_("댓글")?></th>-->
                                 <th><?=_("찜")?></th>
                                 <th><?=_("상태")?></th>
                                 <th><?=_("메인노출")?></th>
                              </tr>
                           </thead>
                        </table>
                     </div>
                     <div class="form-group">
                        <button type="submit" class="btn btn-sm btn-success">
                           <?=_("상태 변경")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-default" onclick="selectMainFlag();">
                        	<?=_("메인노출 변경")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteGallery();">
                           <?=_("갤러리 삭제")?>
                        </button>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>

<form name="fm2" method="post" action="indb.php">
	<input type="hidden" name="mode" value="change_gallery_main_flag">
	<input type="hidden" name="storageid">
</form>
<!-- end #content -->

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>

<script>
/* Table initialisation */
$(document).ready(function() {
   $('#data-table').dataTable({
     "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
     "sPaginationType" : "bootstrap",
     "aaSorting" : [[1, "desc"]],
     "bFilter" : true,
     "aLengthMenu": [10, 25, 50, 100],
     "oLanguage" : {
        "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
     },
     "aoColumns": [
     { "bSortable": false },
     { "bSortable": true },
     { "bSortable": true },
     { "bSortable": true },
     { "bSortable": true },
     { "bSortable": true },
     { "bSortable": false },
     { "bSortable": false },
     ],
     "processing": false,
     "serverSide": true,
     "bAutoWidth": false,
     "ajax": $.fn.dataTable.pipeline( {
        url: 'gallery_page.php',
        pages: 5 // number of pages to cache
      })
   });
});

function selectMainFlag() {
	var fm = document.fm2;
	var c = document.getElementsByName('chk[]');
	var chk = false;
	var cnt = 0;
	
	for (var i=0; i<c.length; i++) {
		if (c[i].checked) {
			fm.storageid.value = c[i].value;
			chk = true;
			cnt++;
		}
	}
	
	if (chk) {
		if (cnt == 1) {
			var flag_chk = fm.storageid.value.split('|');
			
			if (flag_chk[1] == "best") {
				fm.submit();
			} else {
				alert('<?=_("베스트 상태의 편집을 선택해주시기 바랍니다.")?>');
				return false;
			}
		} else {
			alert('<?=_("한 항목만 선택해주시기 바랍니다.")?>');
			return false;
		}
	} else {
		alert('<?=_("메인에 노출할 편집을 선택해주시기 바랍니다.")?>');
		return false;
	}
}

function deleteGallery() {
	var fm = document.fm;
	var c = document.getElementsByName('chk[]');
	var chk = false;
	
	for (var i=0; i<c.length; i++) {
		if (c[i].checked) {
			chk = true;
		}
	}
	
	if (chk) {
		if (confirm('<?=_("정말 선택한 갤러리를 삭제하시겠습니까?")?>')) {
			fm.mode.value = "delete_gallery";
			fm.submit();
		} else {
			return false;
		}
	} else {
		alert('<?=_("삭제할 갤러리를 선택해주시기 바랍니다.")?>');
		return false;
	}
}
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>