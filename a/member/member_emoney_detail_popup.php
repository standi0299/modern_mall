<?
include dirname(__FILE__) . "/../_pheader.php";

if (!$_GET[mid]) {
    msg(_("회원 코드가 넘어오지 못했습니다!"), "close");
}

if($cid=="fotocube2019"){ //포토뷰브 조건 달리	20200109	kkwon
	$sql = "select sql_calc_found_rows * from exm_log_emoney where cid='$cid' and mid='$_GET[mid]' order by no asc";
}else{
	$sql = "select sql_calc_found_rows * from exm_log_emoney where cid='$cid' and mid='$_GET[mid]' order by no desc";
}

//debug($sql);
$list = $db -> listArray($sql);
//debug($list);

$data = getCfg("", "emoney");
if (!$data[emoney_expire_day]) $data[emoney_expire_day] = "365";
?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
    	
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("적립금 정보")?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-heading">
             <h4 class="panel-title"><?=_("적립금 지급")?></h4>
         </div>
         <div class="panel-body panel-form">
   			<form class="form-horizontal form-bordered" method="POST" action="indb.php" onsubmit="return chkForm(this)&&confirm('<?=_("적립금을 지급하시겠습니까?")?>')">
   			<input type="hidden" name="mode" value="emoney">

   			<input type="hidden" name="cid" value="<?=$cid?>">
   			<input type="hidden" name="mid" value="<?=$_GET[mid]?>">
   			<input type="hidden" name="admin" value="<?=$sess_admin[mid]?>">

               <div class="form-group">
                  <!--
                  <label class="col-md-2 control-label"><?=_("상태")?></label>
                  <div class="col-md-2">
                     <select name="status" class="form-control" required>
                        <option value="">선택</option>
   				            <? foreach ($r_emoney_status as $k=>$v) { ?>
                              <option value="<?=$k?>"><?=$v?></option>
   					         <? } ?>
   			         </select>
                  </div>
                  -->
                  <label class="col-md-2 control-label"><?=_("적립금")?></label>
                  <div class="col-md-2">
                     <input type="text" name="emoney" class="form-control" required maxlength="7">
                  </div>
               </div>
               
               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("유효기간")?></label>

                  <div class="col-md-10 form-inline">
                     <input type="text" name="expire_day" class="form-control" value="<?=$data[emoney_expire_day]?>" required>일 - 유효기간은 최대 7,000일 까지 가능합니다.
                  </div>
               </div>

               <div class="form-group">
                  <label class="col-md-2 control-label"><?=_("내용")?></label>
                  <div class="col-md-8">
                     <input type="text" name="memo" class="form-control" required>
                  </div>
               </div>

               <div class="form-group">
                  <label class="col-md-2 control-label"></label>
                  <div class="col-md-9">
                      <button type="submit" class="btn btn-sm btn-primary"><?=_("저 장")?></button>
                  </div>
               </div>
            </form>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("적립금 내역")?></h4>
         </div>
         
         <div class="panel-body">
            <div class="table-responsive">
               <table class="table table-bordered table-hover">
                  <thead>
                     <tr>
                        <th><?=_("번호")?></th>
                        <th><?=_("날짜")?></th>
                        <th><?=_("적립상태")?></th>
                        <th><?=_("적립금액")?></th>
                        <? if($cid=="fotocube2019"){ ?>
						<th><?=_("잔여적립금")?></th>
						<?}?>
                        <th><?=_("내역")?></th>
                        <!--<th><?=_("담당자")?></th>-->
                     </tr>
                  </thead>

                  <tbody>
                  <? 
                  	$i = count($list);
					$Remoney = 0;
                  	foreach ($list as $k => $value) {
						if($cid=="fotocube2019"){
							if($value[status] == "1" || $value[status] == "4"){  //적립/환불
								$Remoney += $value[emoney];
							}else if($value[status] == "2"){ //사용
								$Remoney -= $value[emoney];
							}else if($value[status] == "3"){ //만료

							}
						}
                  ?>
                     <tr>
                        <td><?=$i?></td>
                        <td><?=$value[regdt]?></td>
                        <td><?=$r_emoney_status[$value[status]]?></td>
                        <td><?=number_format($value[emoney])?><?=_("원")?></td>                           
                        <? if($cid=="fotocube2019"){ ?>
						<td><?=number_format($Remoney)?><?=_("원")?></td>
						<? } ?>
                        <td>
                        	<?=$value[memo]?>
                        	<? if ($value[status] == "2" && $value[payno]) { ?>
                        		[<?=$value[payno]?>]
                        	<? } ?>
                        </td>
                        <!--<td><?=$value[admin]?></td>-->
                     </tr>
                  <?
                     $i--;
                  	}
                  ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>

      <div class="form-group">
         <label class="col-md-3 control-label"></label>
         <div class="col-md-9">
            <button type="button" style="margin-bottom: 15px;" class="btn btn-sm btn-default" onclick="window.close();"><?=_("닫  기")?></button>
         </div>
      </div>
   </div>
</div>
<? include dirname(__FILE__) . "/../_pfooter.php"; ?>