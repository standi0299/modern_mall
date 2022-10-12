<?
include "../_header.php";
include "../_left_menu.php";
$m_goods = new M_goods();

$query = "select count(*) from exm_release where cid = '$cid'";
list ($chk) = $db->fetch($query,1);
if ($chk < 1){
   msg(_("등록된 제작사가 없습니다."),-1);
   exit;
}

$fkey = md5(crypt(''));
$ls = array();
$r_bcatno = array();
$r_rid = get_release(1);
$r_opt = array();
$r_printopt = array();
$r_brand = get_brand();
$r_addtion_goods_kind = array("recomand", "relation");

//20170308 / minks / 아이콘 추가
$dir = "../../data/icon/$cid/";
$file = array();
$r_icon = array();

if (is_dir($dir)) {
	$dir_handle = opendir($dir);

	while (($file = readdir($dir_handle)) != false) {
		if ($file != "." && $file != ".." && is_file($dir."/".$file)) {
			$r_icon[] = $file;
		}
	}
	closedir($dir_handle);
} 
sort($r_icon);

$data[shiptype] = 0;

$checked[podskind][$data[podskind]+0] = $checked[state][$data[state]+0] = $checked[usestock][$data[usestock]+0] = $checked[shiptype][$data[shiptype]+0] = "checked";
$checked[useopt][$data[useopt]+0] = "checked";
$checked[input_str][$data[input_str]+0] = $checked[input_file][$data[input_file]+0] = "checked";
$selected[rid][$data[rid]] = "selected";
$selected[privatecid][$data[privatecid]] = "selected";
$selected[brandno][$data[brandno]] = "selected";

list($mall_info[podsid],$mall_info[siteid]) = $db->fetch("select self_podsid,self_podsiteid from exm_mall where cid = '$cid'",1);

$checked[pods_use][$data[pods_use]+0] = "checked";
if (!$data[pods_use]){
   if ($data[podskind] && in_array($data[podskind],$r_podskind20)){
      $checked[pods_use][2]   = "checked";
      $is_podskind20       = "true";
   } else if ($data[podskind]){
      $checked[pods_use][1]   = "checked";
   }
}

//상세 가로보기(견적) 추가 2016.04.14 by kdk
$horizon_view_flag = $data[horizon_view_flag];
if($data[horizon_view_flag])
   $checked[horizon_view_flag] = "checked";

$checked[goods_group_code][$data[goods_group_code]] = "checked";
if (!$data[goods_group_code]){
   $checked[goods_group_code][10]   = "checked";
}

//센터 카테고리 분류
$c_cate_data = $m_goods->getCategoryList($cfg_center[cid]);
$c_ca_list = makeCategorySelectOptionTag($c_cate_data);

//몰 카테고리 분류
$cate_data = $m_goods->getCategoryList($cid);
$ca_list = makeCategorySelectOptionTag($cate_data);

foreach ($_r_mdn_goodslist_extra_kind as $_r_k=>$_r_v) {
   if (in_array($_r_k, $r_addtion_goods_kind)) {   	  	  
   	  $r_addtion_goods_data[$_r_k] = $_r_v;
	  $r_addtion_goods_data[$_r_k][flag] = "N";
	  $checked[$_r_k."_flag"][N] = "checked";
   }
}
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/base/jquery-ui.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->
<div id="content" class="content">
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active">
         <?=_("자체상품등록")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("자체상품등록")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("자체상품등록")?></h4>
            </div>
            
            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" enctype="multipart/form-data" onsubmit="return submitContents(this);">
                  <input type="hidden" name="mode" value="self_goods_regist">
                  <input type="hidden" name="privatecid" value="<?=$cid?>"/>
                  
                  <div class="form-group">
                     <div class="col-md-12"> > > <?=_("상품 기본정보")?></div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("센터분류")?></label>
                     <div class="col-md-10 form-inline">
                        <select name="ccatno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("1차 분류 선택")?></option><?=conv_selected_option($c_ca_list, $CcatLinkNo[0][catno])?></select>
                        <select name="ccatno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("2차 분류 선택")?></option><?=conv_selected_option($c_ca_list, $CcatLinkNo[1][catno])?></select>
                        <select name="ccatno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("3차 분류 선택")?></option><?=conv_selected_option($c_ca_list, $CcatLinkNo[2][catno])?></select>
                        <select name="ccatno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("4차 분류 선택")?></option><?=conv_selected_option($c_ca_list, $CcatLinkNo[3][catno])?></select>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품분류")?></label>
                     <div class="col-md-10 form-inline">
                        <select name="catno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("1차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[0][catno])?></select>
                        <select name="catno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("2차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[1][catno])?></select>
                        <select name="catno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("3차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[2][catno])?></select>
                        <select name="catno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("4차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[3][catno])?></select>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품종류")?></label>
                     <div class="col-md-10 form-inline">
                        <? foreach ($r_goods_group_code as $k=>$v){ if($k == 10) { ?>
                        <input type="radio" name="goods_group_code" value="<?=$k?>" onclick="click_group_div('<?=$k?>')" <?=$checked[goods_group_code][$k]?>><span class="absmiddle"><?=$v?></span>
                        <? } } ?>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품코드")?></label>
                     <div class="col-md-2">
                        <b style="margin-right:30px;"><?=($_GET[goodsno])?$_GET[goodsno]:_("자동생성");?></b>
                        <? if ($cid=="exmall"){ ?>
                        <?=_("업체코드")?> : <input type="text" class="form-control" name="shop_code" value="<?=$data[shop_code]?>"/>
                        <? } ?>
                     </div>
                  </div>

                  <!--
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("맵핑 상품코드")?></label>
                     <div class="col-md-2">
                        <input type="text" class="form-control" name="mapping_goodsno" value="<?=$data[mapping_goodsno]?>"/>
                     </div>
                  </div>
                  -->

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품명")?></label>
                     <div class="col-md-2">
                        <input type="text" class="form-control" name="goodsnm" value="<?=$data[goodsnm]?>" required pt="_pt_txt"/>
                     </div>
                  </div>
         
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("제작사")?></label>
                     <div class="col-md-2">
                        <select name="rid" class="form-control" required>
                        <option value=""/> <?=_("제작사선택")?>
                        <? foreach ($r_rid as $k=>$v){ ?>
                        <option value="<?=$k?>" <?=$selected[rid][$k]?>/><?=$v?>
                        <? } ?>
                        </select>
                     </div>
                  </div>                  
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("브랜드")?></label>
                     <div class="col-md-2">
                        <select name="brandno" class="form-control">
                        <option value=""/> <?=_("브랜드선택")?>
                        <? foreach ($r_brand as $k=>$v){ ?>
                        <option value="<?=$k?>" <?=$selected[brandno][$k]?>/><?=$v?>
                        <? } ?>
                        </select>
                     </div>
                  </div>

                  <!-- ==============================상세설명 JSON형식 (몰)============================== -->                  
                  <div class="form-group">
                     <div class="col-md-12">
                        > > <?=_("상품설명(몰)")?>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
			            <div class="table-responsive">
			               <table class="table table-bordered table-hover">
			                  <thead>
			                     <tr>
			                        <th width="200px"><?=_("필드명")?></th>
			                        <th><?=_("내용")?></th>                                                      
			                     </tr>
			                  </thead>
			                  <tbody>			                  
			                  <?
			                  for ($i=0; $i < 5; $i++) { 
			                  ?>
			                     <tr>
			                     	<td>
			                     		<input type="text" class="form-control" name="goods_desc[<?=$i?>][name]" value="<?=$data[goods_desc][$i][name]?>"/>
			                     	</td>
			   						<td>
			   							<input type="text" class="form-control" name="goods_desc[<?=$i?>][value]" value="<?=$data[goods_desc][$i][value]?>"/>
			   						</td>			   					
			                     </tr>
			                  <?
							  }
			                  ?>
			                  </tbody>
			               </table>
			            </div>
                     </div>
                  </div>
         
                  <!--<div class="form-group">
                     <label class="col-md-2 control-label"><?=_("원산지")?></label>
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="origin" value="<?=$data[origin]?>"/>
                     </div>
                  </div>
                                    
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("제조사")?></label>         
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="maker" value="<?=$data[maker]?>"/>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("제작기간안내")?></label>
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="leadtime" value="<?=$data[leadtime]?>"/>
                        <div class="desc"><?=_("표기하지 않을 경우 노출되지 않습니다.")?></div>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("권장해상도")?></label>
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="resolution" value="<?=$data[resolution]?>">
                     </div>
                  </div>-->

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품사이즈")?></label>
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="goods_size" value="<?=$data[goods_size]?>">
                     </div>
                  </div>


                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("관리자메모")?></label>
                     <div class="col-md-10">
                        <textarea name="adminmemo" class="form-control" style="width:98%;height:50px;overflow:visible" pt="_pt_txt"><?=$data[adminmemo]?></textarea>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("PodStation편집코드")?></label>
                     <div class="col-md-10">
                        <input type="radio" name="pods_use" value="0" class="absmiddle" style="height:12px" onclick="click_pods_div(0)" <?=$checked[pods_use][0]?>><span class="absmiddle"><?=_("일반상품")?></span>
                        <!--<input type="radio" name="pods_use" value="1" class="absmiddle" style="height:12px" onclick="click_pods_div(1)" <?=$checked[pods_use][1]?>><span class="absmiddle">PODs1.0편집기</span>-->
                        <input type="radio" name="pods_use" value="2" class="absmiddle" style="height:12px" onclick="click_pods_div(2)" <?=$checked[pods_use][2]?>><span class="absmiddle"><?=_("PODs2.0편집기")?></span>
                        <input type="radio" name="pods_use" value="3" class="absmiddle" style="height:12px" onclick="click_pods_div(3)" <?=$checked[pods_use][3]?>><span class="absmiddle"><?=_("PODs2.0 w편집기 사용")?></span>

                        <select name="pods_useid" class="absmiddle">
                        <option value="center" /> <?=_("센터아이디사용")?>
                        <option value="mall" <? if ($data[pods_useid]=="mall"){?>selected<?}?> /> <?=_("몰아이디사용")?>
                        </select>
                       
                        <div id="pods_div" <?if(!$data[podskind]){?>style="display:none"<?}?>>
                           <table class="tb1" style="margin:5px 0;">
                              <tr style="height:40px;">
                                 <th><?=_("상품코드")?></th>
                                 <td width="170">
                                 <input type="text" class="form-control" name="goods_podsno" value="<?=$data[podsno]?>" id="podsno" <?if($data[podsno]=="1"){?> readonly<?}?>/>
                                 <div id="podskind_str" style="margin-top:5px;"><?=$r_podskind[$data[podskind]]?></div>
                                 </td>
                                 <td>
                                    <span class="btn btn-danger btn-icon btn-circle" onclick="_get_productid(1,'')" <?if($is_podskind20 || $data[pods_use]=="3"){?>style="display:none"<?}?>> <i class="fa fa-bars"></i></span>
                                    
                                    <!--<img id="btn_get_productid" src="../img/bt_check_s.png" onclick="_get_productid(1,'')" class="absmiddle hand" <?if($is_podskind20 || $data[pods_use]=="3"){?>style="display:none"<?}?>/>-->
                                    <span id="pods_userdataurl" style="display: none;">
                                       <input type="checkbox" name="pods_userdataurl_flag" class="absmiddle" style="width:11px;" value="1" <?if($data[pods_userdataurl_flag]){?>checked<?}?>> <?=_("사용자정보 연동(명함편집기)")?>
                                    </span>
                                 </td>
                              </tr>
                              <tr>
                                 <th><?=_("상품분류")?></th>
                                 <td><input type="text" class="form-control" name="podskind" id="podskind" value="<?=$data[podskind]?>" id="podskind" readonly/></td>
                                 <td>
                                 <div class="stxt red">- <?=_("PODs1.0편집기 자동입력")?></div>
                                 <div class="stxt red">- <?=_("PODs2.0편집기 수동입력")?>:
                                 <? foreach ($r_podskind20 as $v){ ?>
                                 <?=$v?>
                                 <? } ?>
                                 </div>
                                 </td>
                              </tr>
                              <tr style="height:40px;">
                                 <th><?=_("기본페이지수")?></th>
                                 <td><input type="text" class="form-control" id="defaultpage" name="defaultpage" value="<?=$data[defaultpage]?>" <?if(!$is_podskind20){?>disabled<?}?>></td>
                                 <td><span class="stxt red">- <?=_("PODs2.0편집기 사용시 숫자입력")?></span></td>
                              </tr>
                              <tr style="height:40px;">
                                 <th><?=_("최소페이지수")?></th>
                                 <td><input type="text" class="form-control" id="minpage" name="minpage" value="<?=$data[minpage]?>" <?if(!$is_podskind20){?>disabled<?}?>></td>
                                 <td><span class="stxt red">- <?=_("PODs2.0편집기 사용시 숫자입력")?></span></td>
                              </tr>
                              <tr style="height:40px;">
                                 <th><?=_("최대페이지수")?></th>
                                 <td><input type="text" class="form-control" id="maxpage" name="maxpage" value="<?=$data[maxpage]?>" <?if(!$is_podskind20){?>disabled<?}?>></td>
                                 <td><span class="stxt red">- <?=_("PODs2.0편집기 사용시 숫자입력")?></span></td>
                              </tr>
                              <tr style="height:40px;" id="vidiobook_link_tr">
                                 <th><?=_("비디오북 링크 사용여부")?></th>
                                 <td>
                                    <select class="form-control" name="vidiobook_link" id="vidiobook_link">
                                    <option value="Y" <?if($data[vidiobook_link]!="N"){?>selected<?}?>><?=_("사용함")?></option> 
                                    <option value="N" <?if($data[vidiobook_link]=="N"){?>selected<?}?>><?=_("사용하지 않음")?></option>
                                    </select>
                                 </td>
                                 <td>
                                    <span class="stxt red">- <?=_("비디오북 편집기만 설정가능(편집기번호 : 25, 31,32)")?></span>
                                 </td>
                              </tr>
                           </table>
                        </div>
                     </div>
                  </div>

                  <!--<div class="form-group">
                     <label class="col-md-2 control-label"><?=_("간략설명")?></label>
                     <div class="col-md-10">
                     	<textarea name="summary" class="form-control" style="width:98%;height:50px;overflow:visible;"><?=$data[summary]?></textarea>
                     	<div><span class="notice">[설명]</span> 엔터로 구분하여 표시합니다.</div>
                     </div>
                  </div>-->

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("해시태그")?></label>
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="hash_tag"  value="<?=$data[hash_tag]?>"/>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품검색어")?></label>
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="search_word"  value="<?=$data[search_word]?>"/>
                     </div>
                  </div>

                  <!--
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("매칭상품명")?></label>
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="match_goodsnm"  value="<?=$data[match_goodsnm]?>"/>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품특이사항1")?></label>         
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="etc1"  value="<?=$data[etc1]?>"/>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품특이사항2")?></label>
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="etc2"  value="<?=$data[etc2]?>"/>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품특이사항3")?></label>         
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="etc3"  value="<?=$data[etc3]?>"/>
                     </div>
                  </div>
                  -->

                  <script>
                  function click_pods_div(mode){
                     if (mode != "<?=$data[pods_use]?>") {
                        $j('#podsno').val('');
                        $j('#podskind').val('');
                        $j('#podskind_str').html('');
                        $j("#defaultpage").val('');
                        $j('#minpage').val('');
                        $j("#maxpage").val('');
                     }

                     $j("#defaultpage").attr("disabled",true).attr("readonly",true).removeAttr("required");
                     $j("#minpage").attr("disabled",true).attr("readonly",true).removeAttr("required");
                     $j("#maxpage").attr("disabled",true).attr("readonly",true).removeAttr("required");
                  
                     if (mode=="0"){
                        $j('#pods_div').slideUp();
                     }
                     if (mode=="1"){
                        $j("#btn_get_productid").css("display","inline");
                        $j("#vidiobook_link_tr").css("display","");
                        $j("#podsno").attr("readonly",true);
                        $j("#podskind").attr("readonly",true);
                        $j('#pods_div').slideDown()
                     }
                     if (mode=="2" || mode=="3"){
                        $j("#btn_get_productid").css("display","none");
                        $j("#vidiobook_link_tr").css("display","none");
                        $j("#podsno").removeAttr("readonly");
                        $j("#podskind").removeAttr("readonly");
                        if (mode == "2"){
                            $j("#defaultpage").removeAttr("disabled").attr("required","required").removeAttr("readonly");
                           $j("#minpage").removeAttr("disabled").attr("required","required").removeAttr("readonly");
                           $j("#maxpage").removeAttr("disabled").attr("required","required").removeAttr("readonly");
                        }
                        $j('#pods_div').slideDown();
                     }
                  
                     //wpod 명함 편집기 연동시 userDataUrl 연동 여부 by 2014.04.16 kdk
                     //유치원 시즌2(prtty) 메크로 데이터 연동 여부 / 15.06.15 / kjm
                     //달력편집기 로고 연동으로 인해 1.0 사용 / 15.09.15 / kjm
                     if(mode == "3" || mode == "2" || mode == "1") {
                        $j("#pods_userdataurl").show();
                        $j("#input_div").hide();
                     } else {
                        $j("#pods_userdataurl").hide();
                        $j("#input_div").show();
                     }
                  }
                  
                  <?if($data[pods_use] != "0"){?>
                    click_pods_div(<?=$data[pods_use]?>);
                  <?}?>
                  
                  </script>
                  
                  <script>
                  
                  $j(function(){
                     $j("input[type=radio][name=pods_use]").click(function(){
                        if ($j(this).val()=="0"){
                           $j("#pods_div").slideUp();
                           $j("select","#pods_div").attr("disabled",true);
                           $j("input","#pods_div").attr("disabled",true);
                        } else {
                           $j("#pods_div").slideDown();
                           $j("select","#pods_div").attr("disabled",false);
                           $j("input","#pods_div").attr("disabled",false);
                        }
                     });

                     $j("input[type=radio][name=privatecid][checked]").trigger("click");
                     $j('#category2_tr').show();
                     $j("select[name=pods_useid]").val("mall");
                     $j("select[name=pods_useid]").hide();

                     //면적당 계산 여부 추가 / 16.11.03 / kdk
                     $j("input[type=checkbox][name=price_type_size_flag]").click(function(){
                        var extra_price_type = $j("input[type=checkbox][name=price_type_size_flag]").is(":checked") ? "SIZE" : "CNT";
                        $j("input:[name=extra_price_type]").val(extra_price_type);
                     });
                  });

                  var product_id_mode = 0;
                  var product_id_obj;
                  function _get_productid(mode,obj){
                     product_id_mode = mode;
                     product_id_obj = obj;

                     if ($j("select[name=pods_useid]").val()=="center"){
                        var siteid = "<?=$cfg_center[podsid]?>";
                        var userid = "<?=$cfg_center[podsid]?>";
                     } else {
                        var siteid = "<?=$mall_info[siteid]?>";
                        var userid = "<?=$mall_info[podsid]?>";
                     }
                     var actionurl = "http://<?=$_SERVER[HTTP_HOST]?>/_sync/get_productid.php";
                     var url = "http://<?=PODS10_DOMAIN?>/ProductInfo30/code_mapping.aspx?siteid="+siteid+"&userid="+userid+"&actionurl="+actionurl+"&addinfo=Y";

                     popupLayer(url,'',770,450);
                  }
                  </script>
                  
                  <? if ($r_icon) { ?>
                     <div class="form-group">
                  		<label class="col-md-2 control-label"><?=_("아이콘")?></label>         
                  		<div class="col-md-10">
                  			<table class="iconTable">
         							<tr>
         							<? foreach ($r_icon as $k => $v){ ?>
         								<td align="center" valign="top">
         									<input type="checkbox" name="icon_filename[]" value="<?=$v?>" />
         									<img src="../../data/icon/<?=$cid?>/<?=$v?>" />
         								</td>
         							<? } ?>
         							</tr>
      							</table>
                  		</div>
                  	</div>
                  <? } ?>
				  
                  <div class="form-group" id="input_div">
                     <label class="col-md-2 control-label"><?=_("추가기능")?></label>         
                     <div class="col-md-10">
                        <input type="checkbox" name="input_str" value="1" <?=$checked[input_str][1]?>/> 문구입력 사용<br>
                        <input type="checkbox" name="input_file" value="1" <?=$checked[input_file][1]?>/> 파일업로드 사용
                     </div>
                  </div>


                  <!-- ==============================판매 정보============================== -->

                  <div class="form-group">
                     <div class="col-md-12"> > > <?=_("판매 정보")?></div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("판매여부")?></label>
                     <div class="col-md-2">
                        <? foreach ($r_goods_state as $k=>$v){ ?>
                        <input type="radio" name="state" class="form-contorl" value="<?=$k?>" <?=$checked[state][$k]?>/> <?=$v?>
                        <? } ?>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("재고연동")?></label>                    

                     <div class="col-md-10">
                        <input type="radio" name="usestock" value="0" class="form-contorl" <?=$checked[usestock][0]?>/> 재고와 상관없이 무제한으로 판매(재고관리 안함)
                        <input type="radio" name="usestock" value="1" class="form-contorl" <?=$checked[usestock][1]?>/> 재고가 있을 경우만 판매(출고완료시 자동감소)
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("배송비")?></label>
                     <div class="col-md-5">
                        <?=makeShiptypeRadioTag("shiptype", $checked[shiptype], "3")?>
                        <div id="shipping_0" style="display:none">제작사의 배송정책을 따릅니다.</div>
                        <div id="shipping_1" style="display:none">배송비가 <b>무료</b>입니다 (판매자부담)</div>
                        <div id="shipping_2" style="display:none">
                        개별배송비 <input type="text" name="shipprice" value="<?=$data[shipprice]?>" class="form-control-inline" pt="_pt_numplus"/> 원 
                        (배송비원가 <input type="text" name="oshipprice" value="<?=$data[oshipprice]?>" class="form-control-inline" pt="_pt_numplus"/> 원)
                        </div>
                        <div id="shipping_4" style="display:none">배송비가 <b>무료</b>입니다 (구매자부담)</div>
                     </div>
                  </div>

                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("진열불가")?></label>
                     <div class="col-md-2">
                        <input type="checkbox" name="nodp" value="1" <?=$checked[nodp][1]?>> <span class="desc">(<?=_("체크시 진열되지 않습니다.")?>)</span>
                     </div>
                  </div>



                  <!-- ==============================가격/재고 관리============================== -->

                  <div class="form-group">
                     <div class="col-md-12"> > > <?=_("가격/재고 관리")?></div>
                  </div>

                  <div class="form-group form-inline">
                     <label class="col-md-2 control-label"><?=_("원가")?></label>
                     <div class="col-md-2">
                        <input type="text" class="form-control" name="oprice" value="<?=$data[oprice]?>" pt="_pt_numplus"/>
                     </div>

                     <label class="col-md-2 control-label"><?=_("공급가")?></label>
                     <div class="col-md-2">
                        <input type="text" class="form-control" name="sprice" value="<?=$data[sprice]?>" pt="_pt_numplus"/>
                     </div>
                  </div>
                  
                  <div class="form-group form-inline">
                     <label class="col-md-2 control-label"><?=_("권장소비자가")?></label>
                     <div class="col-md-2">
                        <input type="text" class="form-control" name="cprice" value="<?=$data[cprice]?>" pt="_pt_numplus"/>
                     </div>
            
                     <label class="col-md-2 control-label"><?=_("판매가")?></label>
                     <div class="col-md-2">
                        <input type="text" class="form-control" name="price" value="<?=$data[price]?>" pt="_pt_numplus"/>
                     </div>
                  </div>
            
                  <div class="form-group form-inline">
                     <label class="col-md-2 control-label"><?=_("페이지추가가격")?></label>
                     <div class="col-md-10">
                        <?=_("원가")?> : <input type="text" class="form-control" name="opageprice" value="<?=$data[opageprice]?>" pt="_pt_numplus" size="10"/>
                        <?=_("공급가")?> : <input type="text" class="form-control" name="spageprice" value="<?=$data[spageprice]?>" pt="_pt_numplus" size="10"/>
                        <?=_("판매가")?> : <input type="text" class="form-control" name="pageprice" value="<?=$data[pageprice]?>" pt="_pt_numplus" size="10"/>
                        <span class="desc"><?=_("정해진 페이지량을 초과하여 편집시에 추가되는 가격입니다.(ex : 가변포토북)")?></span>
                     </div>
                  </div>
                  
                  <div class="form-group form-inline">
                     <label class="col-md-2 control-label"><?=_("총재고")?></label>
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="totstock" value="<?=$data[totstock]?>" pt="_pt_num"/>
                        <span class="desc"><?=_("옵션을 사용하는 경우 임의 수정이 불가능해집니다.")?></span>
                     </div>
                  </div>
                  
                  
                  
                  <!-- ==============================옵션별 가격/재고 관리============================== -->
                  
                  <div class="form-group">
                     <div class="col-md-12"> > > <?=_("옵션별 가격/재고 관리")?> [<?=_("명함상품은 반드시 1차옵션에 추가금액을 넣어주셔야 편집기와 주문금액이 연동됩니다. / 옵션추가 > center관리자 에서 등록하세요")?>]</span></div>
                  </div>
         
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("옵션유무")?></label>
                     <div class="col-md-10">
                        <input type="radio" class="radio-inline" name="useopt" value="0" <?=$checked[useopt][0]?>/>
                        <?=_("옵션사용안함")?>
                        <input type="radio" class="radio-inline" name="useopt" value="1" <?=$checked[useopt][1]?>/>
                        <?=_("옵션사용")?>
                        <b class="desc hand" id="easy_setopt" id="mod_opt_btn">[<?=_("쉬운세팅")?>]</b>
                     </div>
                  </div>
                  
                  <div class="form-group" id="setopt_div">
                     <label class="col-md-2 control-label"><?=_("옵션제어")?></label>
                     <div class="col-md-10 form-inline">
                        <b><?=_("1차 옵션명")?></b> : <input type="text" class="form-control" name="optnm1" value="<?=$data[optnm1]?>"/>
                        <span class="desc"><?=_("선택되어야할 옵션의 이름을 정해주세요. 공란일 경우 사용자에게는 '옵션1' 로 노출이 됩니다.")?></span><br><br>
                        <b><?=_("2차 옵션명")?></b> : <input type="text" class="form-control" name="optnm2" value="<?=$data[optnm2]?>"/>
                        <span class="desc"><?=_("선택되어야할 옵션의 이름을 정해주세요. 공란일 경우 사용자에게는 '옵션2' 로 노출이 됩니다.")?></span><br><br>
                        <span class="btn btn-warning btn-icon btn-circle" onclick="add_more_opt()"><i class="fa fa-plus"></i></span>
                        <span class="btn btn-warning btn-icon btn-circle" onclick="remove_opt()"><i class="fa fa-minus"></i></span>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
                        <table id="opt_tb" class="table table-hover table-bordered">
                           <thead>
                              <th><?=_("제어")?></th>
                              <th><?=_("1차옵션")?></th>
                              <th><?=_("2차옵션")?></th>
                              <th><?=_("편집코드")?></th>
                              <th><?=_("편집옵션코드")?></th>
                              <th><?=_("추가가격")?></th>
                              <th><?=_("옵션추가공급가")?></th>
                              <th><?=_("추가원가")?></th>
                              <th><?=_("권장소비자가")?></th>
                              <th><?=_("재고")?></th>
                              <th><?=_("노출여부")?></th>
                              <?if($_GET[goodsno]){?><th><?=_("편집기")?></th><?}?>
                           </thead>
                           
                           <tbody>
                              <? foreach ($r_opt as $k=>$v){ ?>
                              <tr align="center">
                                 <td><input type="radio" name="dummy"/></td>
                                 <td><input type="text" class="form-control" name="opt1[<?=$v[optno]?>]" value="<?=$v[opt1]?>" pt="_pt_txt"/></td>
                                 <td><input type="text" class="form-control" name="opt2[<?=$v[optno]?>]" value="<?=$v[opt2]?>" pt="_pt_txt"/></td>
                                 <td>
                                    <input type="text" name="podsno[<?=$v[optno]?>]" value="<?=$v[podsno]?>" />
                                    <a class="btn btn-success btn-icon btn-circle" onclick="_get_productid(0,$j(this).prev())"><i class="fa fa-repeat"></i></a>
                                 </td>
                                 <td><input type="text" class="form-control" name="podoptno[<?=$v[optno]?>]" value="<?=$v[podoptno]?>" /></td>
                                 <td><input type="text" class="form-control" name="aprice[<?=$v[optno]?>]" value="<?=$v[aprice]?>" pt="_pt_numplus"/></td>
                                 <td><input type="text" class="form-control" name="asprice[<?=$v[optno]?>]" value="<?=$v[asprice]?>" pt="_pt_numplus"/></td>
                                 <td><input type="text" class="form-control" name="aoprice[<?=$v[optno]?>]" value="<?=$v[aoprice]?>" pt="_pt_numplus"/></td>
                                 <td><input type="text" class="form-control" name="opt_cprice[<?=$v[optno]?>]" value="<?=$v[opt_cprice]?>" pt="_pt_numplus"/></td>
                                 <td><input type="text" class="form-control" name="stock[<?=$v[optno]?>]" value="<?=$v[stock]?>" pt="_pt_numplus"/></td>
                                 <td>
                                 <select class="form-control" name="opt_view[<?=$v[optno]?>]">
                                 <option value="0">Y
                                 <option value="1" <?if($v[opt_view]){?>selected<?}?>>N
                                 </select>
                                 </td>
                                 <?if($_GET[goodsno]){?><td><a href="javascript:void(0);" onclick="popupLayer('../../module/popup_calleditor.php?mode=edit&goodsno=<?=$data[goodsno]?>&productid=<?=$data[podsno]?>&optionid=<?=$v[podoptno]?>')"><?=_("호출")?></a></td><?}?>
                              </tr>
                              <? } ?>
                           </tbody>
                        </table>
                     </div>
                  </div>
                  
                  
                  
                  <!-- ==============================인화 옵션============================== -->
                  
                  <div class="form-group">
                     <div class="col-md-12"> > > <?=_("인화옵션")?></span></div>
                  </div>
                  
                  <div class="form-group" id="setopt_div">
                     <label class="col-md-2 control-label"><?=_("인화옵션설정")?></label>
                     <div class="col-md-10 form-inline">
                        <span class="desc"><?=_("인화상품의 경우 인화종류별 금액을 설정해주셔야 올바른 주문을 접수받으실 수 있습니다.")?></span><br><br>
                        <span class="desc"><?=_("인화옵션명은 PodStation에 등록된 인화옵션명과 동일하게 입력해주셔야 합니다.")?></span><br><br>
                        <span class="btn btn-warning btn-icon btn-circle" onclick="add_printopt()"><i class="fa fa-plus"></i></span>
                        <span class="btn btn-warning btn-icon btn-circle" onclick="remove_printopt()"><i class="fa fa-minus"></i></span>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
                        <table id="printopt_tb" class="table table-hover table-bordered" <? if (!$r_printopt) { ?>style="display:none"<? } ?>>
                           <thead>
                              <th><?=_("제어")?></th>
                              <th><?=_("옵션명")?></th>
                              <th><?=_("인화옵션크기")?></th>
                              <th><?=_("인화옵션판매가")?></th>
                              <th><?=_("인화옵션공급가")?></th>
                              <th><?=_("인화옵션원가")?></th>
                           </thead>
                           
                           <tbody>
                              <? foreach ($r_printopt as $k=>$v) { ?>
                              <tr align="center">
                                 <td><input type="radio" name="dummy_printopt"/></td>
                                 <td><input type="text" class="form-control" name="printoptnm[]" value="<?=$v[printoptnm]?>" pt="_pt_txt"/></td>
                                 <td><input type="text" class="form-control" name="print_size[]" value="<?=$v[print_size]?>" pt="_pt_txt"/></td>
                                 <td><input type="text" class="form-control" name="print_price[]" value="<?=$v[print_price]?>" pt="_pt_numplus"/></td>
                                 <td><input type="text" class="form-control" name="print_sprice[]" value="<?=$v[print_sprice]?>" pt="_pt_numplus"/></td>
                                 <td><input type="text" class="form-control" name="print_oprice[]" value="<?=$v[print_oprice]?>" pt="_pt_numplus"/></td>
                              </tr>
                              <? } ?>
                           </tbody>
                        </table>
                     </div>
                  </div>
                  
                  
                  
                  <!-- ==============================추가 옵션============================== -->
                  
                  <div class="form-group">
                     <div class="col-md-12"> > > <?=_("추가옵션")?></div>
                  </div>
         
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("추가옵션설정")?></label>
                     <div class="col-md-10">
                        <span type="button" class="btn btn-primary" onclick="add_addoptbox()"><?=_("옵션추가")?></span><br><br>

                        <div id="addoptbox_div">
                        
                        <? if ($r_addopt_bundle) foreach ($r_addopt_bundle as $k=>$v){ ?>
                        <div class="addoptbox_div" addoptbox_idx="<?=$k?>">
                        <table class="addoptbox" class="table table-striped table-bordered">
                        <tr>
                           <th><?=_("추가옵션묶음명")?></th>
                           <td>
                           <input type="text" name="addopt_bundle_name[<?=$k?>]" class="w200" value="<?=$v[addopt_bundle_name]?>" pt="_pt_txt" required/>
                           <span class="absmiddle stxt red"><?=_("필수")?></span>
                           <input type="checkbox" name="addopt_bundle_required[<?=$k?>]" class="absmiddle" style="width:11px;" value="1" <?if($v[addopt_bundle_required]){?>checked<?}?>>
                           </td>
                           <td width="105" align="center">
                           <select name="addopt_bundle_view[<?=$k?>]"><option value="0"><?=_("노출")?><option value="1" <?if($v[addopt_bundle_view]){?>selected<?}?>><?=_("숨김")?></select>
                           </td>
                           <td width="124" align="center">
                           <a class="btn btn-success btn-icon btn-circle" onclick="add_addopt(this)"><i class="fa fa-plus"></i></a>
                           <a class="btn btn-success btn-icon btn-circle" onclick="remove_addoptbox(this)"/><i class="fa fa-times"></i></a>
                           </td>
                        </tr>
                        </table>
                     
                        <table class="table table-striped table-bordered">
                        <tr>
                           <th><?=_("옵션명")?></th>
                           <th><?=_("옵션판매가")?></th>
                           <th><?=_("옵션공급가")?></th>
                           <th><?=_("옵션원가")?></th>
                           <th><?=_("권장소비자가")?></th>
                           <th><?=_("맵핑옵션")?></th>
                           <th><?=_("노출여부")?></th>
                           <th><?=_("삭제")?></th>
                        </tr>
                        <? foreach ($v[addopt] as $k2=>$v2){ ?>
                        <tr align="center">
                           <td><input type="text" class="form-control" name="addoptnm[<?=$k?>][<?=$k2?>]" value="<?=$v2[addoptnm]?>" pt="_pt_txt"/></td>
                           <td><input type="text" class="form-control" name="addopt_aprice[<?=$k?>][<?=$k2?>]" value="<?=$v2[addopt_aprice]?>" pt="_pt_numplus"/></td>
                           <td><input type="text" class="form-control" name="addopt_asprice[<?=$k?>][<?=$k2?>]" value="<?=$v2[addopt_asprice]?>" pt="_pt_numplus"/></td>
                           <td><input type="text" class="form-control" name="addopt_aoprice[<?=$k?>][<?=$k2?>]" value="<?=$v2[addopt_aoprice]?>" pt="_pt_numplus"/></td>
                           <td><input type="text" class="form-control" name="addopt_cprice[<?=$k?>][<?=$k2?>]" value="<?=$v2[addopt_cprice]?>" pt="_pt_numplus"/></td>
                           <td><input type="text" class="form-control" name="addopt_mapping_no[<?=$k?>][<?=$k2?>]" value="<?=$v2[addopt_mapping_no]?>"/></td>
                           <td><select class="form-control" name="addopt_view[<?=$k?>][<?=$k2?>]"><option value="0"><?=_("노출")?><option value="1" <?if($v2[addopt_view]){?>selected<?}?>><?=_("숨김")?></select></td>
                           <td><a class="btn btn-success btn-icon btn-circle" onclick="remove_addopt(this)"/><i class="fa fa-times"></i></a></td>
                        </tr>
                        <? } ?>
                        </table>
                     
                        </div>
                        <? } ?>
                        
                        </div>
                     </div>
                  </div>
                  
                  
                  
                  
                  
                  <!-- ==============================이미지설정============================== -->
                  
                  <div class="form-group">
                     <div class="col-md-12">
                        > > <?=_("이미지 설정")?>
                     </div>
                  </div>

                  <div class="form-group">
                     <?
                        for ($i=0;$i<5;$i++){
                     ?>
                     <div class="form-group">
                        <label class="col-md-2 control-label"><?=_("상세")?><?=$i+1?></label>
                        <div class="col-md-6">
                           <input type="file" class="form-control" name="img[]">                          
                        </div>
                        <div class="col-md-2">
                           <input type="radio" name="thumbnail" value="<?=$i?>" onclick="$j('#listimg').attr('disabled',true)"> <span><?=_("리스트 이미지 생성")?></span>
                        </div>
                     </div>

                     <? } ?>


                     <div class="form-group">
                        <label class="col-md-2 control-label"><?=_("리스트")?></label>
                        <div class="col-md-6">
                           <input type="file" class="form-control" name="listimg" id="listimg">
                        </div>
                        <div class="col-md-2">
                           <input type="radio" name="thumbnail" value="" onclick="$j('#listimg').attr('disabled',false)" checked>
                           <span><?=_("리스트 이미지 수동 적용")?></span>
                        </div>
                     </div>
                     
                     <div class="form-group">
                        <label class="col-md-2 control-label"><?=_("리스트 와이드")?></label>
                        <div class="col-md-6">
                           <input type="file" class="form-control" name="listimg_w" id="listimg_w">
                        </div>
                        <div class="col-md-2">
                        </div>
                     </div>                     
                     
                  </div>
                  
                  <!-- ==============================상세설명(몰)============================== -->
                  
                  <div class="form-group">
                     <div class="col-md-12">
                        > > <?=_("상세설명(몰)")?>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
                        <textarea name="desc" style="width:100%;height:400px" id="desc" type="editor"><?=stripslashes($data[desc])?></textarea><p>
                     </div>
                  </div>


				  <!-- ==============================추천상품,연관상품============================== -->
                  
                  <? foreach ($r_addtion_goods_data as $r_k=>$r_v) { ?>
                     <div class="form-group">
	                     <div class="col-md-12">
	                        > > <?=$r_v[display]?>
	                     </div>
	                  </div>
	                  
	                  <div class="form-group">
	                     <label class="col-md-2 control-label">사용여부</label>
	                     <div class="col-md-10">
	                     	<input type="radio" class="radio-inline" name="<?=$r_k?>_flag" value="N" <?=$checked[$r_k."_flag"][N]?>> 사용안함
	      	 				<input type="radio" class="radio-inline" name="<?=$r_k?>_flag" value="Y" <?=$checked[$r_k."_flag"][Y]?>> 사용
	                     </div>
	                  </div>
	                  
	                  <div class="form-group notView" id="<?=$r_k?>_div">
	                     <label class="col-md-2 control-label"></label>
	                     <div class="col-md-10 form-inline">
	                     	<section class="srel">
							    <div class="compare_wrap">
							        <section class="compare_left">
							            <h3>등록된 전체상품 목록</h3>
							            <span class="srel_pad">
							            	<label for="<?=$r_k?>_catno" class="sound_only">상품분류</label>
							            	<select id="<?=$r_k?>_catno" class="form-control" name="<?=$r_k?>_catno" style="width:35%;">
							            		<option value="">+ <?=_("분류 선택")?></option>
							            		<?=conv_selected_option($ca_list, "")?>
							            	</select>
							                <label for="<?=$r_k?>_name" class="sound_only">상품명</label>
							                <input type="text" id="<?=$r_k?>_name" class="form-control" name="<?=$r_k?>_name" style="width:45%;">
							                <button type="button" id="btn_<?=$r_k?>_search" class="btn btn-sm btn-success">검색</button>
							            </span>
							            <div id="<?=$r_k?>" class="srel_list">
							                <p>등록된 상품이 없습니다.</p>
							            </div>
							            
							            <script>
							            $j(function() {
							                $j("#btn_<?=$r_k?>_search").click(function() {
							                    var catno = $j("#<?=$r_k?>_catno").val();
							                    var goodsnm = $j.trim($("#<?=$r_k?>_name").val());
							                    var $kind = $j("#<?=$r_k?>");
							
							                    if (catno == "" && goodsnm == "") {
							                        $kind.html("<p>등록된 상품이 없습니다.</p>");
							                        return false;
							                    }
							
							                    $j("#<?=$r_k?>").load(
							                        "indb.php",
							                        { mode:"addtion_goods_list", kind: "<?=$r_k?>", catno: catno, goodsnm: goodsnm }
							                    );
							                });
							
							                $(document).on("click", "#<?=$r_k?> .add_item", function() {
							                    // 이미 등록된 상품인지 체크
							                    var $li = $j(this).closest("li");
							                    var goodsno = $li.find("input:hidden").val();
							                    var reg_goodsno;
							                    var dup = false;
							                    
							                    $j("#reg_<?=$r_k?> input[name='<?=$r_k?>_select_goodsno[]']").each(function() {
							                        reg_goodsno = $j(this).val();
							                        
							                        if(goodsno == reg_goodsno) {
							                            dup = true;
							                            return false;
							                        }
							                    });
							
							                    if (dup) {
							                        alert("이미 선택된 상품입니다.");
							                        return false;
							                    }
							
							                    var cont = "<li>" + $li.html().replace("add_item", "del_item").replace("추가", "삭제").replace("<?=$r_k?>_goodsno[]", "<?=$r_k?>_select_goodsno[]") + "</li>";
							                    var count = $j("#reg_<?=$r_k?> li").size();
							
							                    if (count > 0) $j("#reg_<?=$r_k?> li:last").after(cont);
							                    else $j("#reg_<?=$r_k?>").html("<ul id=\"<?=$r_k?>_sortable\">" + cont + "</ul>");
							                    
							                    $j("#<?=$r_k?>_sortable").sortable();
							                    
							                    $li.remove();
							                });
							
							                $(document).on("click", "#reg_<?=$r_k?> .del_item", function() {
							                    if (!confirm("상품을 삭제하시겠습니까?")) return false;
							                    $j(this).closest("li").remove();
							
							                    var count = $j("#reg_<?=$r_k?> li").size();
							                    if (count < 1) $("#reg_<?=$r_k?>").html("<p>선택된 상품이 없습니다.</p>");
							                });
							            });
							            </script>
							        </section>
							
							        <section class="compare_right">
							            <h3>선택된 <?=$r_v[display]?> 목록</h3>
							            <span class="srel_pad"></span>
							            <div id="reg_<?=$r_k?>" class="srel_sel">
							                <p>선택된 상품이 없습니다.</p>
							            </div>
							        </section>
							    </div>
							</section>
							<div><span class="notice">[설명]</span> 선택된 상품 목록의 순서를 변경하시려면 드래그해주시면 됩니다.</div>
	                     </div>
	                  </div>
                  <? } ?>
                  

                  <div class="form-group">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
                        <button type="submit" class="btn btn-sm btn-success"><?=_("등록")?></button>
                        <button type="button" class="btn btn-sm btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>


<!--
<script src="../assets/plugins/DataTables-1.9.4/js/jquery.dataTables.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>
-->
<script type="text/javascript" src="/js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/smarteditor/editorStart.js" charset="utf-8"></script>
<script src="../../js/webtoolkit.base64.js"></script>
<script src="../../js/jquery-ui.js"></script>
<script src="../../js/jquery.ui.touch.js"></script>

<script type="text/javascript">
$j(function() {
	<? foreach ($r_addtion_goods_data as $r_k=>$r_v) { ?>
		$j("[name=<?=$r_k?>_flag][value=<?=$r_v[flag]?>]").trigger("click");
			
		$j("#reg_<?=$r_k?>").load(
			"indb.php",
			{ mode:"addtion_goods_select", kind: "<?=$r_k?>", kind2: "I", addtion_key : "<?=$data[goodsno]?>" },
			function(response, status, xhr) {
				$j("#<?=$r_k?>_sortable").sortable();
			}
		);
	<? } ?>
});

<? foreach ($r_addtion_goods_data as $r_k=>$r_v) { ?>
	$j("[name=<?=$r_k?>_flag]").click(function() {
		if ($j(this).val() == "Y") {
			$j("#<?=$r_k?>_div").show();
		} else {
			$j("#<?=$r_k?>_div").hide();
		}
	});
<? } ?>

var oEditors = [];
smartEditorInit("desc", true, "goods", true);
function submitContents(formObj) {
    if (sendContents("desc", false))
    {
        /*try {
	         formObj.desc.value = Base64.encode(formObj.desc.value);
            return form_chk(formObj);
        } catch(e) {return false;}*/
        try {
        	var desc = $j("#desc").val();
        	$j("#desc").val(Base64.encode(desc));
            return form_chk(formObj);
        } catch(e) {
        	return false;
        }       
    }
    return false;
}
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>

<? include "goods_r.js.php"; ?>