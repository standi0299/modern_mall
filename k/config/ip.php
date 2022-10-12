<?

include "../_header.php";
include "../_left_menu.php";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   <input type="hidden" name="mode" value="limited_ip" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("접속아이피설정")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"></label>
      	 	<div class="col-md-10">
      	 		<div style="font-size:18px;">
      	 			현재접속 IP주소는 <b><?=$_SERVER[REMOTE_ADDR]?></b> 입니다.&nbsp;&nbsp;&nbsp;
      	 			<span id="add_limited_ip"><button type="button" class="btn btn-sm btn-default">현재 IP주소 추가하기</button></span>
      	 		</div>
      	 		<textarea id="limited_ip" class="form-control" name="limited_ip"><?=$cfg[limited_ip]?></textarea>
      	 		<div><span class="warning">[주의]</span> 잘못된 데이터를 입력시에는 접속에 장애가 생길수 있으니, 반드시 확인 후 입력해주세요.</div>
      	 		<div><span class="notice">[설명]</span> 관리자 페이지의 접속을 허용하실 IP를 엔터키로 구분하여 입력하여 주세요.</div>
      	 		<div><span class="notice">[설명]</span> 모든 접속을 허용시에는 값을 모두 지우시고 저장 버튼을 눌러 주세요.</div>
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"></label>
      	 	<div class="col-md-10">
      	 		<button type="submit" class="btn btn-sm btn-success"><?=_("저장")?></button>
      	 	</div>
      	 </div> 
      </div>
   </div>
   </form>
</div>

<script type="text/javascript">
$j(function() {
	$j("#add_limited_ip").click(function() {
		if ($j('[name=limited_ip]').val()) {
			$j('[name=limited_ip]').val('<?=$_SERVER[REMOTE_ADDR]?>\r\n'+ $j('[name=limited_ip]').val())
		} else {
			$j('[name=limited_ip]').val('<?=$_SERVER[REMOTE_ADDR]?>')
		}
	});
});
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>