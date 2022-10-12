<?
include dirname(__FILE__) . "/../_pheader.php";

if (!$_GET[mid]) {
    msg(_("회원 코드가 넘어오지 못했습니다!"), "close");
}

$m_member = new M_member();
$m_order = new M_order();

if ($_GET[mid]) {
	$addwhere = "and mid = '$_GET[mid]'";
    $list = $m_member -> getList($cid, $addwhere);
	
	if($list) $data = $list[0]; 
}
//debug($data);

//회원주문내역 5개 //cid = '$cid' and mid = '$mid' and itemstep in (2, 3, 4, 5, 92)
//$order_list = $m_order -> getList($cid, "and mid='$_GET[mid]' and itemstep in (2, 3, 4, 5, 92) order by cartno desc limit 0,5");
$order_list = $m_order -> getList($cid, "and mid='$_GET[mid]' and paystep in (2, 3, 4, 5, 92) order by payno desc");
//debug($order_list);

//회원문의내역 5개
$sql = "select sql_calc_found_rows * from exm_mycs where id='cs' and cid='$cid' and mid='$_GET[mid]' order by no desc limit 0,5";
//debug($sql);
$board_list = $db -> listArray($sql);
//debug($board_list);
?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
    <!-- begin #content -->
    <div id="content" class="content">
    	
	    <!-- begin #header -->
        <div id="header" class="header navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("회원 정보")?></a>
                </div>
            </div>
        </div>

        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title"><?=_("회원 정보")?></h4>
            </div>
            <div class="panel-body panel-form">
				<form class="form-horizontal form-bordered">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?=_("회원성명")?></label>
                    <div class="col-md-4">
                        <label class="control-label"><?=$data[name]?></label>
                    </div>
                    <label class="col-md-2 control-label"><?=_("회원")?>ID</label>
                    <div class="col-md-4">
                        <label class="control-label"><?=$data[mid]?></label>
                    </div>                    
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label"><?=_("생년월일")?></label>
                    <div class="col-md-4">
                        <label class="control-label"><?=$data[birth_year]?>.<?=substr($data[birth],0,2)?>.<?=substr($data[birth],2,2)?></label>
                    </div>
                    <label class="col-md-2 control-label"><?=_("성별")?>ID</label>
                    <div class="col-md-4">
                        <label class="control-label"><? if ($data[sex] == "m") echo _("남자"); else echo _("여자");?></label>
                    </div>                    
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label"><?=_("주문건수")?></label>
                    <div class="col-md-4">
                        <label class="control-label"><?=number_format($m_member->getTotPayCount($cid, $data[mid]))?></label>
                    </div>
                    <label class="col-md-2 control-label"><?=_("결제금액")?></label>
                    <div class="col-md-4">
                        <label class="control-label"><?=number_format($m_member->getTotPayPrice($cid, $data[mid]))?></label>
                    </div>                    
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label"><?=_("전화번호")?></label>
                    <div class="col-md-4">
                        <label class="control-label"><?=$data[phone]?></label>
                    </div>
                    <label class="col-md-2 control-label"><?=_("핸드폰")?></label>
                    <div class="col-md-4">
                        <label class="control-label"><?=$data[mobile]?></label>
                    </div>                    
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label"><?=_("이메일")?></label>
                    <div class="col-md-4">
                        <label class="control-label"><?=$data[email]?></label>
                    </div>
                    <label class="col-md-2 control-label"><?=_("마케팅동의")?></label>
                    <div class="col-md-4">
                        <label class="control-label"><? if ($data[privacy_flag] == "1") echo _("Y"); else echo _("N");?></label>
                    </div>                    
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label"><?=_("주소")?></label>
                    <div class="col-md-10">
                        <label class="control-label">[<?=$data[zipcode]?>] <?=$data[address]?> <?=$data[address_sub]?></label>
                    </div>
                </div>

            	</form>
            </div>
        </div>
    
		<ul class="nav nav-tabs">
			<li class="active"><a href="#default-tab-1" data-toggle="tab"><?=_("회원주문내역")?></a></li>
			<li class=""><a href="#default-tab-2" data-toggle="tab"><?=_("회원문의내역")?></a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane fade active in" id="default-tab-1">
	            <div class="panel-body">
	               <div class="table-responsive">
	                  <table class="table table-bordered table-hover">
	                     <thead>
	                        <tr>
	                           <th><?=_("주문번호")?></th>
	                           <th><?=_("주문일시")?></th>
	                           <th><?=_("상품명")?></th>                                                      
	                           <th><?=_("회원ID")?></th>
	                           <th><?=_("주문자")?></th>
	                           <th><?=_("수량")?></th>
	                           <th><?=_("판매단가")?></th>
	                           <th><?=_("결제금액")?></th>
	                        </tr>
	                     </thead>
	
	                     <tbody>
	                     <? foreach ($order_list as $k => $value) { ?>
	                        <tr>
	                           <td><a href="javascript:popup('../order/printgroup_popup_order_detail.php?payno=<?=$value[payno]?>&mid=<?=$value[mid]?>',1100,800);"><?=$value[payno]?></a></td>
	                           <td><?=substr($value[orddt],0,10)?></td>
	                           <td><?=$value[goodsnm]?></td>
	                           <td><?=$value[mid]?></td>
	                           <td><?=$value[orderer_name]?></td>
	                           <td><?=$value[ea]?></td>
	                           <td><?=number_format($value[goods_price])?><?=_("원")?></td>                           
	                           <td><?=number_format($value[payprice])?><?=_("원")?></td>
	                        </tr>
	                     <? } ?>
	                     </tbody>
	                  </table>
	               </div>
	            </div>
			</div>
			<div class="tab-pane fade" id="default-tab-2">
				<div class="panel-body">
	               <div class="table-responsive">
	                  <table class="table table-bordered table-hover">
	                     <thead>
	                        <tr>
	                           <th><?=_("문의번호")?></th>
	                           <th><?=_("제목")?></th>
	                           <th><?=_("등록일")?></th>                                                      
	                           <th><?=_("상태")?></th>
	                        </tr>
	                     </thead>
	
	                     <tbody>
	                     <? foreach ($board_list as $k => $value) { ?>
	                        <tr>
	                           <td><?=$value[no]?></td>	                           
	                           <td><?=$value[subject]?></td>
	                           <td><?=$value[regdt]?></td>	                           
	                           <td><?=$value[status]?></td>
	                        </tr>
	                     <? } ?>
	                     </tbody>
	                  </table>
	               </div>
	            </div>
			</div>
		</div>

        <div class="form-group">
            <label class="col-md-3 control-label"></label>
            <div class="col-md-9">
                <button type="button" style="margin-bottom: 15px;" class="btn btn-sm btn-default"onclick="window.close();"><?=_("닫  기")?></button>
            </div>
        </div>
    
    </div>
</div>

<?
include dirname(__FILE__) . "/../_pfooter.php";
?>