<?
include_once "../_header.php";
include_once "../_left_menu.php";
include_once dirname(__FILE__) . "/../../admin/class.stat.php";
include_once dirname(__FILE__) . "/../../pretty/_module_util.php";

$m_order = new M_order();
$m_pretty = new M_pretty();

//메인 요약 정보와 주문관리의 스탭별 데이터가 같도록 수정 / 15.10.26 / kjm
//$count[0] = $m_order -> getListCount($cid, "91");
//$count[1] = $m_order -> getListCount($cid, "92");
$count[0] = $m_pretty -> getListCount_modern($cid, "and paystep = 91 and paymethod = 't'");
//$count[1] = $m_pretty -> getListCount_modern($cid, "and itemstep = 3", "Y");
$count[1] = $m_pretty -> getListCount_modern($cid, "and paystep != 0 and paystep != -1 and c.itemstep = '3'", "Y", "Y");

//PODS 미전송 갯수
//$count[3] = $m_order -> getListCount($cid, "05");
$count[3] = $m_pretty -> getListCount_modern($cid, "and itemstep = 5", "Y");

/*
$count[2] = $m_pretty -> getListCount($cid, "and c.itemstep = '92'");
$count[3] = $m_pretty -> getListCount($cid, "and c.itemstep = '3'");
$count[5] = $m_pretty -> getListCount($cid, "and c.itemstep = '5'");
*/
$m_member = new M_member();
//회원 승인대기 숫자
$count[2] = $m_member->getNotAcceptMemberCount($cid, $addWhere);


$m_mall = new M_mall();
$mall_data = $m_mall -> getInfo($cid);
    
//포인트 안내 경고창 뜨우기      20150420    chunter
//모던 포인트 안내 경고창 사용안함 /20170829/kdk
//if ($mall_data[pretty_point] < 10000)
//   $cfg[pretty_point_notice] = "Y";
      


	$stat = new stat();
	$stat->get_month_ship($cid);

	$month_graph['cnt'] = "[";
	$month_graph['price'] = "[";
	foreach ($stat->r_month as $month){
		if (! $stat->month_ship[$month][total][cnt]) $stat->month_ship[$month][total][cnt] = "0";
   	if (! $stat->month_ship[$month][total][payprice]) $stat->month_ship[$month][total][payprice] = "0";
				
		//$month_graph['cnt'] .= "[" . "\"" . substr($month,5,2) . _("월")."\"," . $stat->month_ship[$month][total][cnt] . "],";
		//$month_graph['price'] .= "[" . "\"" . substr($month,5,2) . _("월")."\"," . $stat->month_ship[$month][total][payprice] . "],";
		$month_graph['cnt'] .= "[\"" . substr($month,5,2) ._("월")."\"," . $stat->month_ship[$month][total][cnt] . "],";
		$month_graph['price'] .= "[\"" . substr($month,5,2) ._("월")."<br>".$stat->month_ship[$month][total][cnt]._("건")."\"," . $stat->month_ship[$month][total][payprice] . "],";
	}
	$month_graph['cnt'] .= "]";
	$month_graph['price'] .= "]";
	
	
	$r_week = array();
	$w = date("w",time());
	$week[0] = date("Y-m-d",strtotime(date("Y-m-d")." -$w days"));
	$week[1] = date("Y-m-d",strtotime($week[0]." -7 days"));
	$week[2] = date("Y-m-d",strtotime($week[1]." -7 days"));
	$week[3] = date("Y-m-d",strtotime($week[2]." -7 days"));
	
	krsort($week);
	foreach ($week as $k=>$v){
		if (!$k){
			$query = "select left(paydt,10) paydt,count(*) cnt,sum(b.payprice) payprice from exm_pay a inner join exm_ord_item b on a.payno = b.payno where cid = '$cid' and itemstep >= 2 and itemstep <= 5 and paydt >= '$v' and paymethod!='t' order by paydt asc";
		} else {
			$query = "select left(paydt,10) paydt,count(*) cnt,sum(b.payprice) payprice from exm_pay a inner join exm_ord_item b on a.payno = b.payno where cid = '$cid' and itemstep >= 2 and itemstep <= 5 and paydt >= '$v' and paydt <= '{$week[$k-1]}' and paymethod!='t' order by paydt asc";
		}	
		$data = $db->fetch($query);
		$r_week[$k][cnt] = $data[cnt];
		$r_week[$k][payprice] = $data[payprice];		
	}


	$week_graph['cnt'] = "[";
	$week_graph['price'] = "[";
	foreach ($r_week as $k=>$v){
		if (! $v[cnt]) $v[cnt] = "0";
   	if (! $v[payprice]) $v[payprice] = "0";
		if (!$k) $k = _("이번주");
		else $k = $k._("주전");		
		$week_graph['cnt'] .= "[" . "\"" .$k."\"," . $v[cnt] . "],";
		$week_graph['price'] .= "[" . "\"" .$k."<BR>".$v[cnt]._("건")."\"," . $v[payprice] . "],";
	}
	$week_graph['cnt'] .= "]";
	$week_graph['price'] .= "]";
	
	
	
	
	$r_paycnt = array();
	for ($i=-6;$i<=0;$i++){
		$r_paycnt[date("Y-m-d",strtotime(date("Y-m-d")." +$i days"))] = array();
	}
	
	$now = date("Y-m-d");
	$query = "select left(paydt,10) paydt,count(*) cnt,sum(b.payprice) payprice from exm_pay a inner join exm_ord_item b on a.payno = b.payno where cid = '$cid' and itemstep >= 2 and itemstep <= 5 and paydt >= adddate('$now',interval -6 day) and paymethod!='t' group by left(paydt,10) order by paydt asc";
	$res = $db->query($query);
	while ($data = $db->fetch($res)){
		$r_paycnt[$data[paydt]][cnt] = $data[cnt];
		$r_paycnt[$data[paydt]][payprice] = $data[payprice];
	}

	$day_graph['cnt'] = "[";
	$day_graph['price'] = "[";
	$rowIndex = 0;
	foreach ($r_paycnt as $k=>$v){
		if (! $v[cnt]) $v[cnt] = "0";
   	if (! $v[payprice]) $v[payprice] = "0";
		
		if ($rowIndex == "0") $k = _("오늘");
		else if ($rowIndex == "1") $k = _("어제");
		else $k = $rowIndex._("일전");
				
		$day_graph['cnt'] .= "[" . "\"" .$k. "\"," . $v[cnt] . "],";
		$day_graph['price'] .= "[" . "\"" .$k. "<br>".$v[cnt]._("건")."\"," . $v[payprice] . "],";
		
		$rowIndex++;
	}
	$day_graph['cnt'] .= "]";
	$day_graph['price'] .= "]";
	
	if (!$cfg[admin_main_board_cnt]) $cfg[admin_main_board_cnt] = "5";
//var data = [ ["1월", 10], ["2월", 8], ["3월", 13], ["4월", 17], ["5월", 19],["6월", 5],["7월", 29],["8월", 39],["9월", 35], ["10월", 9], ["11월", 27], ["12월", 31] ];
?>

<div id="content" class="content">
   <!-- begin breadcrumb -->
   <ol class="breadcrumb pull-right">
      <li><a href="javascript:;">Home</a></li>
      <li class="active"><?=_("관리 화면")?></li>
   </ol>
   <!-- end breadcrumb -->

   <!-- begin page-header -->
   <h1 class="page-header"><?=_("요약 정보")?> <small><?=_("사이트의 전반적인 Summary 를 보여줍니다.")?></small></h1>
   <!-- end page-header -->

   <!-- begin row -->
   <div class="row">
      <!-- begin col-3 -->
      <div class="col-md-3 col-sm-6">
         <div class="widget widget-state bg-red">
            <div class="state-icon"><i class="fa fa-desktop"></i></div>
            <div class="state-info">
               <h4><?=_("승인요청")?></h4>
               <p><?=number_format($count[0])?><?=_("건")?></p>
            </div>
            <div class="state-link"><?=_("승인을 기다리는 주문 수량")?>
               <a href="../order/order_list.php?itemstep=91"><?=_("내역을 보려면 여기를 클릭하세요")?> <i class="fa fa-arrow-circle-o-right"></i></a>
            </div>
         </div>
      </div>
      <!-- end col-3 -->
       
      <!-- begin col-3 -->
      <div class="col-md-3 col-sm-6">
         <div class="widget widget-state bg-aqua">
            <div class="state-icon"><i class="fa fa-gavel"></i></div>
            <div class="state-info">
               <h4><?=_("제작중")?></h4>
               <p><?=number_format($count[1])?><?=_("건")?></p>
            </div>
            <div class="state-link"><?=_("승인완료후 제작업체에 주문이 전달된 수량")?>
               <a href="../order/order_list.php?itemstep=3"><?=_("내역을 보려면 여기를 클릭하세요")?> <i class="fa fa-arrow-circle-o-right"></i></a>
            </div>
         </div>
      </div>
      <!-- end col-3 -->
        
      <!-- begin col-3 -->
      <div class="col-md-3 col-sm-6">
         <div class="widget widget-state bg-blue">
            <div class="state-icon"><i class="fa fa-chain-broken"></i></div>
            <div class="state-info">
               <h4><?=_("발송완료")?></h4>
               <p><?=number_format($count[3])?><?=_("건")?></p>
            </div>
            <div class="state-link"><?=_("발송완료된 수량 입니다.")?>
               <a href="javascript:alert('준비중입니다.');"><?=_("내역을 보려면 여기를 클릭하세요")?> <i class="fa fa-arrow-circle-o-right"></i></a>
            </div>
         </div>
      </div>
      <!-- end col-3 -->
     
      <!-- begin col-3 -->
      <div class="col-md-3 col-sm-6">
         <div class="widget widget-state bg-green-darker">
            <div class="state-icon"><i class="fa fa-users"></i></div>
            <div class="state-info">
               <h4><?=_("회원 승인대기")?></h4>
               <p><?=number_format($count[2])?><?=_("건")?></p>
            </div>
            <div class="state-link"><?=_("회원 가입 승인처리가 되지 않은 수")?>
               <a href="/a/member/member_list.php"><?=_("내역을 보려면 여기를 클릭하세요")?> <i class="fa fa-arrow-circle-o-right"></i></a>
            </div>
         </div>
      </div>
      <!-- end col-3 -->
   </div>
   <!-- end row -->
   
   
<?	if ($chunter_dev == TRUE){	?>   
   <div class="row">
      <!-- begin col-12 -->
      <div class="col-md-6">
         <!-- begin panel -->
         <div class="panel panel-success">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><a href="/a/board/bluepod_notice.php?board_id=bluepod_noti">Bluepod 공지사항</a></h4>
            </div>

            <div class="panel-body">
               <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                     <thead>
                        <tr>                           
                           <th width="80%"><?=_("제목")?></th>
                           <th><?=_("등록일")?></th>
                        </tr>
                     </thead>

                     <tbody>
                     <? 
                     		foreach (getBoardDataFromIlark("bluepod_noti") as $v) { 
                     	?>
                        <tr>
                           <td><a href="/a/board/bluepod_notice.r.php?board_id=<?=$board[board_id]?>&no=<?=$v[no]?>"><?=$v[subject]?></a></td>                           
                           <td><?=substr($v[regdt],0,10)?></td>
                        </tr>
                    <? 
												} 
										?>
                     </tbody>
                  </table>                  
               </div>
            </div>
         </div>
         <!-- end panel -->
      </div>

      <div class="col-md-6">
         <!-- begin panel -->
         <div class="panel panel-warning">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><a href="/a/board/bluepod_notice.php?board_id=bluepod_update">Bluepod 업그레이드</a></h4>
            </div>

            <div class="panel-body">
               <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                     <thead>
                        <tr>                           
                           <th width="80%"><?=_("제목")?></th>
                           <th><?=_("등록일")?></th>
                        </tr>
                     </thead>

                     <tbody>
                     <? 
                     		foreach (getBoardDataFromIlark("bluepod_update") as $v) { 
                     ?>
                     
                        <tr>
                           <td><a href="/a/board/bluepod_notice.r.php?board_id=<?=$board[board_id]?>&no=<?=$v[no]?>"><?=$v[subject]?></a></td>                           
                           <td><?=substr($v[regdt],0,10)?></td>
                        </tr>
                     <? 
												} ?>
                     </tbody>
                  </table>                  
               </div>
            </div>
         </div>
         <!-- end panel -->
      </div>
   </div>
<?	}	?>   
   
   
   
   
   <? if ($super_admin == 1) { ?>
   <div class="row">
      <!-- begin col-12 -->
      <div class="col-md-6">
         <!-- begin panel -->
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><a href="/a/board/cs.php"><?=_("1:1 문의")?></a></h4>
            </div>

            <div class="panel-body">
               <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                     <thead>
                        <tr>
                           <th><?=_("NO")?></th>
                           <th><?=_("제목")?></th>
                           <th><?=_("등록일")?></th>                           
                           <th><?=_("답변여부")?></th>
                        </tr>
                     </thead>

                     <tbody>
                     <? //foreach (getBoardData("cs") as $k => $v){ ?>
                     <? 
                     		$index = 1;
                     		foreach (getBoardData("cs", $cfg[admin_main_board_cnt]) as $v) { 
                     			$status = _("대기");
													if ($v[status] == "2") $status = _("완료"); 
                     	?>
                        <tr>
                           <td><?=$index?></td>
                           <td><a href="/a/board/cs.w.php?no=<?=$v[no]?>"><?=$v[subject]?></a></td>                           
                           <td><?=substr($v[regdt],0,10)?></td>
                           <td><?=$status?></td>
                        </tr>
                    <? 
													$index++;
												} 
										?>
                     </tbody>
                  </table>                  
               </div>
            </div>
         </div>
         <!-- end panel -->
      </div>

      <div class="col-md-6">
         <!-- begin panel -->
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><a href="/a/board/qna.php">QnA</a></h4>
            </div>

            <div class="panel-body">
               <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                     <thead>
                        <tr>
                           <th><?=_("NO")?></th>
                           <th><?=_("제목")?></th>
                           <th><?=_("등록일")?></th>                           
                           <th><?=_("답변여부")?></th>
                        </tr>
                     </thead>

                     <tbody>
                     <? 
                     		$index = 1;
                     		foreach (getBoardData("qna", $cfg[admin_main_board_cnt]) as $v) { 
                     			$status = _("대기");
													if ($v[status] == "2") $status = _("완료"); 
                     ?>
                     
                        <tr>
                           <td><?=$index?></td>
                           <td><a href="/a/board/qna.w.php?no=<?=$v[no]?>"><?=$v[subject]?></a></td>                           
                           <td><?=substr($v[regdt],0,10)?></td>
                           <td><?=$status?></td>
                        </tr>
                     <? 
													$index++;
												} ?>
                     </tbody>
                  </table>                  
               </div>
            </div>
         </div>
         <!-- end panel -->
      </div>
   </div>
   
   
   <div class = "row">
   	<div class="col-md-12">
      <!-- begin panel -->
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("월간 판매")?></h4>
            </div>

            <div class="panel-body">
               <div class="table-responsive">
                  <div id="month-chart" class="height-sm"></div>
               </div>
            </div>
         </div>
      <!-- end panel -->
      </div>
   </div>
   
   <div class = "row">
      <div class="col-md-6">
         <!-- begin panel -->
         <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>                    
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
                </div>
                <h4 class="panel-title"><?=_("주단위 판매")?></h4>
            </div>
            <div class="panel-body">
                <div id="week-chart" class="height-sm"></div>
            </div>
        </div>
         <!-- end panel -->
      </div>
      
      <div class="col-md-6">
         <!-- begin panel -->
         <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>                    
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
                </div>
                <h4 class="panel-title"><?=_("일단위 판매")?></h4>
            </div>
            <div class="panel-body">
                <div id="day-chart" class="height-sm"></div>
            </div>
        </div>
         <!-- end panel -->
      </div>
   </div>

   
   <? } ?>
</div>
<!-- end #content -->

<? include "../_footer.php"; ?>

<script>
//그래프 변수 할당



var monthChartData2 = <?=$month_graph['price']?>;
var weekChartData2 = <?=$week_graph['price']?>;
var dayChartData2 = <?=$day_graph['price']?>;
</script>

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="../assets/plugins/flot/jquery.flot.min.js"></script>
<script src="../assets/plugins/flot/jquery.flot.time.min.js"></script>
<script src="../assets/plugins/flot/jquery.flot.resize.min.js"></script>
<script src="../assets/plugins/flot/jquery.flot.stack.min.js"></script>
<script src="../assets/plugins/flot/jquery.flot.crosshair.min.js"></script>
<script src="../assets/plugins/flot/jquery.flot.categories.js"></script>

<script src="../assets/plugins/flot/jquery.flot.pie.min.js"></script>
<script src="../assets/js/charts.main.js"></script>
<script>
$(document).ready(function() {
   //Dashboard.init();
   Chart.init();
});
</script>