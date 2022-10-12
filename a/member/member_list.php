<?
include "../_header.php";
include "../_left_menu.php";

$m_pretty = new M_pretty();

### 쿠폰 가져오기
$query = "
select a.*,(select count(*) from exm_coupon_set x where x.cid = '$cid' and x.coupon_code = a.coupon_code) issued
from
    exm_coupon a
where
    a.cid = '$cid'
    and coupon_kind = 'on'
    and (
        coupon_issue_unlimit = 1 or
        (
            coupon_issue_sdate <= curdate()
            and coupon_issue_edate >= curdate()
        )
    )
    and (
        (
            coupon_issue_ea_limit=1
            and (select count(*) from exm_coupon_set x where x.cid = a.cid and x.coupon_code = a.coupon_code) < coupon_issue_ea
        )
        or coupon_issue_ea_limit=0
    )
order by coupon_regdt desc
";

$res = $db->query($query);

$coupon = array();
while ($data = $db->fetch($res)){
    $coupon[] = $data;
}

//회원그룹
if ($_POST[grp]) $addwhere .= " and grpno = '$_POST[grp]'";

//검색 조건 쿼리 만들기
if($_POST[s_search]){
    $_POST[s_search_c] = str_replace('-','',trim($_POST[s_search]));
	
    $addwhere .= " and (name like '%$_POST[s_search_c]%' or mid like '%$_POST[s_search_c]%' or email like '%$_POST[s_search_c]%' or replace(phone,'-','') like '%$_POST[s_search_c]%' or replace(mobile,'-','') like '%$_POST[s_search_c]%')";
}	

//적립금
if($_POST[emoney_start]) $addwhere .= " and emoney >= '{$_POST[emoney_start]}'";
if($_POST[emoney_end]) $addwhere .= " and emoney <= '{$_POST[emoney_end]}'";

//가입일
if($_POST[regdt_start]) {
   $regdt_start = str_replace("-","",$_POST[regdt_start]);
   $addwhere .= " and date_format(regdt,'%Y%m%d') >= '{$regdt_start}'";
}
if($_POST[regdt_end]) {
   $regdt_end = str_replace("-","",$_POST[regdt_end]);
   $addwhere .= " and date_format(regdt,'%Y%m%d') < adddate({$regdt_end},interval 1 day)+0";
}
//구매일
if($_POST[orddt_start]) {
   $orddt_start = str_replace("-","",$_POST[orddt_start]);
   $addwhere .= " 
		and (select payno from exm_pay
			where
				cid = '$cid'
				and mid = a.mid
				and date_format(orddt,'%Y%m%d') > '{$orddt_start}'
			limit 1
		) > 0
   ";
}
if($_POST[orddt_end]) {
   $orddt_end = str_replace("-","",$_POST[orddt_end]);
   $addwhere .= " 
		and (select payno from exm_pay
			where
				cid = '$cid'
				and mid = a.mid
				and date_format(orddt,'%Y%m%d') < adddate({$orddt_end},interval 1 day)+0
			limit 1
		) > 0
   ";	
}
//분류
if($_POST[state]) $addwhere .= " and state={$_POST[state]}";

//이메일 수신여부
if($_POST[apply_email]=="0"||$_POST[apply_email]=="1") $addwhere .= " and apply_email={$_POST[apply_email]}";

//SMS 수신여부
if($_POST[apply_sms]=="0"||$_POST[apply_sms]=="1") $addwhere .= " and apply_sms={$_POST[apply_sms]}";

//성별
if($_POST[sex]) $addwhere .= " and sex='{$_POST[sex]}'";

//결혼여부
if($_POST[married]) $addwhere .= " and married='{$_POST[married]}'";

//나이
if($_POST[age_start]) {
   $year = date("Y");
   $birth_year[0] = $year + 1 - $_POST[age_start];
   $addwhere .= " and birth_year <= '{$birth_year[0]}'";
}
if($_POST[age_end]) {
   $year = date("Y");
   $birth_year[1] = $year + 1 - $_POST[age_end];
   $addwhere .= " and birth_year >= '{$birth_year[1]}'";
}
$having = array();

/************************************** 전용 엑셀 다운로드 by kkwon 2020.11.23 **/
$_query2 = $m_pretty->getMemberListA2($cid, $addwhere, "", FALSE, TRUE);
$_query2 = base64_encode(urlencode($_query2));
/**************************************************************************/


//구매금액
if($_POST[totpayprice_start]) $having[] = "totpayprice >= '{$_POST[totpayprice_start]}'";
if($_POST[totpayprice_end]) $having[] = "totpayprice <= '{$_POST[totpayprice_end]}'";
if ($having){
   $addwhere .= " having(".implode(" and ",$having).")";
}
$_query = $m_pretty->getMemberListA($cid, $addwhere, "", FALSE, TRUE);
//debug($_query);
### form 전송 취약점 개선 20160128 by kdk
$_query = base64_encode(urlencode($_query));
$url_query = "/admin/xls/indb.php?query=".$_query;
//debug($url_query);
$pod_signed = signatureData($cid, $url_query);
$pod_expired = expiresData("20");

$postData = base64_encode(json_encode($_POST));
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
<? include "member_list_header.php"; ?>
<? include "member_list_chart.php"; ?>
</div>

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
                                 <th><?=_("적립금")?></th>
                                 <th><?=_("구매금액")?></th>
                                 <th><?=_("분류")?></th>
                                 <th><?=_("구분")?></th>
                                 <th><?=_("수정")?></th>
                                 <th><?=_("삭제")?></th>
                                 <th>
                                    <? if ($cfg[skin_theme] == "M2") { ?>    
                                     <?=_("필름스캔")?>
                                    <? } ?>    
                                 </th>                                 
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

        <div class="panel panel-inverse"> 
            <div class="panel-heading">
                <h4 class="panel-title"><?=_("추가 설정")?></h4>
            </div>
            <div class="panel-body">
                <div class="form-group">
                   <label class="col-md-2 control-label"><?=_("적용 회원") ?></label>
                   <div class="col-md-4">
                        <input type="radio" name="range" checked/> <?=_("선택회원")?>
                        <input type="radio" name="range" /> <?=_("검색회원전체")?>
                   </div>
                   <label class="col-md-6 control-label">
                      <span class="desc gray" style="margin:10px"><span class="warning">[<?=_("주의")?>]</span> <?=_("변경시 복구가 불가능하므로 주의하시기 바랍니다.")?></span>
                   </label>
                </div>
            </div>
        </div>

         <div class="panel panel-inverse">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#default-tab-1" data-toggle="tab"><?=_("쿠폰발급")?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade active in" id="default-tab-1">

                    <div id="bottom_price">
                        <form method="POST" action="indb.php" onsubmit="return get_param(this)">
                        <input type="hidden" name="mode" value="bottom_coupon"/>
                        <input type="hidden" name="range"/>
                        <input type="hidden" name="mquery"/>

                        <div>
                           <select name="couponno" label='<?=_("쿠폰")?>' required onchange="view_coupon_info(this.value)">
                           <option value=""><?=_("쿠폰발급하기")?>
                           <? foreach ($coupon as $v){ ?>
                           <option value="<?=$v[coupon_code]?>"><?=$v[coupon_name]?>
                           <? } ?>
                           </select>
                           <div id="coupon_info_txt" class="desc"></div>
                        </div>

                        <div class="btn">
                           <button type="submit" class="btn btn-primary m-r-5 m-b-5"><?=_("확인")?></button>
                        </div>

                        </form>
                    </div>
                </div>
            </div>
         </div>

      </div>
   </div>
</div>
<!-- end #content -->

<script src="../../js/webtoolkit.base64.js"></script>
<script>
function view_coupon_info(val){
    if (val){
        $j("#coupon_info_txt").html("<?=_('발급전 쿠폰정보를 정확히 확인해 주시기바랍니다')?> <a href='../promotion/coupon_form.php?kind=on&coupon_code="+val+"' class='stxt' target='_blank'><b class='red'><?=_('정보보기')?></b></a>");
    } else {
        $j("#coupon_info_txt").html("");
    }
}

function get_param(f)
{
    if (!form_chk(f)){
        return false;
    }

    var tmp = [];
    var cnt_member = 0;
    f.range.value = (document.getElementsByName('range')[0].checked) ? 'selmember' : 'allmember';

    if (f.range.value=='allmember'){

        var goodsno = document.getElementsByName('chk[]');
        for (var i=0; i<goodsno.length; i++){
            tmp[i] = goodsno[i].value;
        }
        f.mquery.value = Base64.encode("select * from exm_member where cid = '<?=$cid?>' and mid in ('" + tmp.join("','") + "')");
        cnt_member = <?=$pg->recode[total]?>+0;


    } else if (f.range.value=='selmember'){

        var c = document.getElementsByName('chk[]');
        for (var i=0;i<c.length;i++){
            if (c[i].checked) tmp[tmp.length] = c[i].value;
        }
        if (tmp[0]){
            f.mquery.value = Base64.encode("select * from exm_member where cid = '<?=$cid?>' and mid in ('" + tmp.join("','") + "')");
            cnt_member = tmp.length;
        }else{
            alert('<?=_("선택된 회원이 없습니다.")?>',-1);
            return false;
        }
    }
}
</script>

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
			"bFilter" : false,
			"pageLength": 10,
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
         { "bSortable": false },
         { "bSortable": false },
         ],
		 "processing": false,
         "serverSide": true,
         "bAutoWidth": false,
		 "ajax": $.fn.dataTable.pipeline({
				url: './member_list_page.php?postData=<?=$postData?>',
				pages: 1 // number of pages to cache
         })
      });
   });
</script>

<script>
   function mid_delete(mid){
      if(confirm('<?=_("정말 삭제하시겠습니까?")?>') == true){
         location.href = "indb.php?mode=delMember&mid="+mid;
      }
   }
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>