<?
include "../_header.php";
include "../_left_menu.php";

//list($online) = $db->fetch("select value from exm_config where cid = '$cid' and config = 'online_join_flag'",1);
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active"><?=_("홈페이지 꾸미기")?></li>
   </ol>
   <h1 class="page-header"><?=_("홈페이지 꾸미기")?> <small><?=_("이미지 또는 문구를 사용자가 원하는 형태로 변경하실 수 있습니다.")?></small></h1>
      
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   	<input type="hidden" name="mode" value="basis"/>
      
      <div class="panel panel-inverse">
         <div class="panel-heading">
            <div class="panel-heading-btn">
               <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
               <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
               <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
            </div>
            <h4 class="panel-title"><?=_("홈페이지 꾸미기")?></h4>
         </div>

         <div class="panel-body panel-form">
            <div class="form-group">               
               <div class="col-md-12">                  
                  * <?=_("홈페이지를 꾸미는 방법은 다음과 같습니다.")?><br><br>

                  1. <?=_("인쇄몰 홈페이지에 관리자 아이디로 로그인해주세요.")?><br>
                  2. <?=_("배너 또는 배너영역이라 표시된 항목위로 마우스를 이동해주세요.")?><br>
                  3. <?=_("마우스 커서가 손가락 모양으로 변경되면 눌러주세요.")?><br>
                  4. <?=_("새로운 창이 열리면 수정이 필요한 이미지를 찾아주세요.")?><br>
                  5. <?=_("찾아보기를 누르고 수정된 이미지를 찾은 후 확인을 눌러주세요.")?><br>
                  6. <?=_("수정된 이미지를 적용 후 하단에 확인을 눌러주세요.")?><br>
                  7. <?=_("새로고침(F5)을 하시면 수정된 이미지로 적용된 홈페이지를 보실 수 있습니다.")?>
                  
                  <p class="pull-right">
                     <button type="button" class="btn btn-md btn-primary m-r-15" onclick="homepage_move();"><?=_("인쇄몰 홈페이지 바로가기")?></button>
                  </p>
               </div>               
            </div>
         </div>   
      </div>
   </form>
</div>

<script>
   function homepage_move(){
      window.open('/main/index.php', '_blank');
   }
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>