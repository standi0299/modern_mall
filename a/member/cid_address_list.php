<?
include "../_header.php";
include "../_left_menu.php";

//mid가 없는경우 배송지관리에 해당하는 데이터
$where = "cid = '$cid' and mid = ''";
if ($_GET[sword]) $where .= " and concat(addressno,addressnm,receiver_name,receiver_addr,receiver_phone,receiver_mobile) like '%$_GET[sword]%'";

if($_GET[sword]) $param = "sword=".$_GET[sword];

$query = "select * from exm_address where $where limit 0, 10";
$list = $db->listArray($query);

$cnt_query = "select count(*) cnt from exm_address where $where ";
$cntRes = $db->fetch($cnt_query);
$totalCnt = $cntRes[cnt];
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
         <?=_("배송지관리")?>
      </li>
   </ol>
  <h1 class="page-header"><?=_("배송지관리")?> <small><?=_("생성된 배송지관리")?></small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
              <div class="panel-heading-btn">
              	<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                <a href="javascript:;"  class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
              </div>
            	<h4 class="panel-title"><?=_("배송지관리")?></h4>
            </div>

           <div class="panel-body panel-form">
             <form class="form-horizontal form-bordered">
               <div class="form-group">
                 <div class="col-md-12">
                   <div class="col-md-8">
                     <input type="text" class="form-control" name="sword" placeholder="번호,아이디,대리점명,받는사람,주소,전화번호,핸드폰번호를 입력해주세요">
                   </div>
                   <div class="col-md-2">
                     <button type="submit" class="btn btn-sm btn-success" ><?=_("조 회")?></button>
                   </div>
                 </div>
               </div>
               <div class="form-group">
                 <div class="col-md-2">
                   <button type="button" class="btn btn-sm btn-default" onclick="location.href='cid_address_write.php';"><?=_("새 주소 작성하기")?> </button>
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
                          <th><?=_("아이디")?></th>
                          <th><?=_("대리점명")?></th>
                          <th><?=_("받는사람")?></th>
                          <th><?=_("주소")?></th>
                          <th><?=_("전화번호")?></th>
                          <th><?=_("핸드폰번호")?></th>
                          <th><?=_("수정")?></th>
                          <th><?=_("삭제")?></th>
                       </tr>
                    </thead>
                    <tbody>

                          <? if($list) {?>
                           	<? foreach ($list as $key => $value) { ?>
                          <tr>
                             <td><?=$key++?></td>
                             <td><?=$value[addressno]?></td>
                             <td><?=$value[addressnm]?></td>
                             <td><?=$value[receiver_name]?></td>
                             <td><?=$value[receiver_addr]?></td>
                             <td><?=$value[receiver_phone]?></td>
                             <td><?=$value[receiver_mobile]?></td>
                             <td><button type="button" class="btn btn-xs btn-primary" onclick="location.href='cid_address_write.php?addressno=<?=$value[addressno]?>';"><?=_("수정")?></button></td>
                             <td>
                              <button type="button" class="btn btn-xs btn-danger" onclick="address_delete('<?=$value[addressno]?>');"><?=_("삭제")?></button>
                             </td>
                          </tr>
                          	<? } ?>
                       	  <? } ?>
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
        "aaSorting" : [[0, "desc"]],
         "bFilter" : false,
         "aLengthMenu": [10, 25, 50],
         "oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
         },
         "processing": true,
         "serverSide": true,
         "deferLoading": <?=$totalCnt?>,
         "ajax": $.fn.dataTable.pipeline( {
            url: './cid_address_page.php?<?=$param?>',
            pages: 1, // number of pages to cache
            "pageLength": 10
         })
      });
   });
	function address_delete(addressno)
	{
		if (confirm('<?=_("정말 삭제하시겠습니까?")?>'))
	   	{
				location.href="indb.php?mode=address_remove&addressno=" + addressno;
	   	}
	}
</script>
<script src="../js/datatable_page.js"></script>
<? include "../_footer_app_exec.php"; ?>
