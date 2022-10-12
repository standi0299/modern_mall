<?
include dirname(__FILE__) . "/../_pheader.php";

$m_goods = new M_goods();
$goods_data = $m_goods->getInfo($_GET[goodsno]);

if($goods_data[opt1_display] == "img") $checked[opt1_display][img] = "checked";
else $checked[opt1_display][text] = "checked";

if($goods_data[opt2_display] == "img") $checked[opt2_display][img] = "checked";
else $checked[opt2_display][text] = "checked";

//옵션 가져오기
$opt_data = $db->listArray("select * from exm_goods_opt where goodsno = '$_GET[goodsno]'");
//debug($opt_data);

foreach($opt_data as $key => $val){
   $opt_arr_first[$val[opt1]] = "1st_".$val[opt1]."_".$val[optno];
   $opt_arr_sec[$val[opt1]."-".$val[opt2]] = "2st_".$val[opt2]."_".$val[optno];
}

if($opt_arr_first && $opt_arr_sec)
   $opt_data = array_merge($opt_arr_first, $opt_arr_sec);
/*
foreach($opt_data as $k => $v){
   foreach($opt_img_list as $kk => $vv){
      if($vv[opt1] == $k && $vv[opt2] == '')
         $checked[$v][$vv[opt_view]] = "checked";
   }
}
*/
//추가 옵션 가져오기
$addopt_data = $m_goods->getGoodsAddOptList($_GET[goodsno]);

foreach($addopt_data as $key => $val){
   $addopt_data[$key][addopt] = $m_goods->getGoodsAddOptNoList($val[addopt_bundle_no]);
   
   if($val[addopt_display] == "img") $checked[addopt_display][$val[addopt_bundle_no]][img] = "checked";
   else $checked[addopt_display][$val[addopt_bundle_no]][text] = "checked";
}

?>

<!-- begin #page-loader -->
<div id="page-loader" class="fade in"><span class="spinner"></span></div>
<!-- end #page-loader -->

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
    	
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("옵션 이미지 등록")?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("항목 관리")?></h4>
         </div>
         <div class="panel-body panel-form">
   			<form action="indb.php" method="post" class="form-horizontal form-bordered" enctype="multipart/form-data">
   			   <input type="hidden" name="mode" value="set_opt_img" />
   			   <input type="hidden" name="goodsno" value="<?=$_GET[goodsno]?>" />

               <div class="form-group">
                  <label class="col-md-2 control-label">
                     옵션
                  </label>
                  <div class="col-md-10">
                     <?if($goods_data[optnm1]) { ?>
                        <?=$goods_data[optnm1]?>
                        <input type="radio" name="opt_view_type[opt1]" value="text" <?=$checked[opt1_display][text]?>>텍스트
                        <input type="radio" name="opt_view_type[opt1]" value="img" <?=$checked[opt1_display][img]?>>이미지
                        <br>
                     <? } ?>
                     
                     <?if($goods_data[optnm2]) { ?>
                        <?=$goods_data[optnm2]?>
                        <input type="radio" name="opt_view_type[opt2]" value="text" <?=$checked[opt2_display][text]?>>텍스트
                        <input type="radio" name="opt_view_type[opt2]" value="img" <?=$checked[opt2_display][img]?>>이미지
                     <? } ?>
                  </div>
                  
                  <label class="col-md-2 control-label">
                     추가 옵션
                  </label>
                  <div class="col-md-10">
                     <?foreach($addopt_data as $key => $val) { ?>
                        <?=$val[addopt_bundle_name]?>
                        <input type="radio" name="addopt_view_type[<?=$val[addopt_bundle_no]?>]" value="text" <?=$checked[addopt_display][$val[addopt_bundle_no]][text]?>>텍스트
                        <input type="radio" name="addopt_view_type[<?=$val[addopt_bundle_no]?>]" value="img" <?=$checked[addopt_display][$val[addopt_bundle_no]][img]?>>이미지
                        <br>
                     <? } ?>
                  </div>
               </div>

   				<div class="form-group">
                  <label class="col-md-2 control-label">
                     <!--<input type="radio" name="opt_view_type" value="text" checked>텍스트
                     <input type="radio" name="opt_view_type" value="img">이미지-->
                  </label>
                  <div class="col-md-10">
                     <table id="data-table" class="table table-striped table-bordered">
                        <thead>
                           <tr>
                              <th class="col-md-4"><?=_("옵션명")?></th>
                              <th class="col-md-4"><?=_("이미지")?></th>
                              <!--<th class="col-md-2"><?=_("형식 설정")?></th>-->
                           </tr>
                        </thead>
                        <tbody>
                           <?
                              foreach ($opt_data as $key => $val) {
                                 $exp_data = explode("_", $val);
                           ?>
                           <tr>
                              <td>
                                 <?=$key?>
                              </td>
                              <td>
                                 <input type="file" class="form-control" name="opt_img[<?=$val?>]">
                              </td>
                              <!--
                              <td>
                                 <? if($exp_data[0] == "1st") { ?>
                                 <input type="radio" name="opt_view_type[<?=$val?>]" value="text" <?=$checked[$val][text]?>>텍스트
                                 <input type="radio" name="opt_view_type[<?=$val?>]" value="img" <?=$checked[$val][img]?>>이미지
                                 <? } ?>
                              </td>
                              -->
                           </tr>
                           <? } ?>
                        </tbody>
                     </table>
                  </div>
               </div>

               <div class="form-group">
                  <label class="col-md-2 control-label">
                     <!--<input type="radio" name="addopt_view_type" value="text" checked>텍스트
                     <input type="radio" name="addopt_view_type" value="img">이미지-->
                  </label>
                  <div class="col-md-10">
                     <table id="data-table" class="table table-striped table-bordered">
                        <thead>
                           <tr>
   	                   		<th class="col-md-4"><?=_("추가 옵션명")?></th>
   	                   		<th class="col-md-6"><?=_("이미지")?></th>
   	                	   </tr>
   	             	   </thead>
                        <tbody>
      	             	   <?
      						      foreach ($addopt_data as $key => $val) {
      						         foreach ($val[addopt] as $k => $v) {
                           ?>
         	             		<tr>
                                 <td>
                                    <?=$val[addopt_bundle_name]?> : <?=$v[addoptnm]?>
         	             			</td>
         	             			<td>
         	             				<input type="file" class="form-control" name="addopt_img[<?=$val[addopt_bundle_no]?>_<?=$v[addoptno]?>]">
         	             			</td>
         	             			<!--
         	             			<td>
                                    <input type="radio" name="opt_view_type[<?=$val[addopt_bundle_no]?>_<?=$v[addoptno]?>]" value="text" <?=$checked[$val[addopt_bundle_no]."_".$v[addoptno]][text]?>>텍스트
                                    <input type="radio" name="opt_view_type[<?=$val[addopt_bundle_no]?>_<?=$v[addoptno]?>]" value="img" <?=$checked[$val[addopt_bundle_no]."_".$v[addoptno]][img]?>>이미지
                                 </td>
                                 -->
         	             		</tr>
   		             	<? } } ?>
                        </tbody>
                     </table>
                  </div>
               </div>
               
               <button type="submit" style="margin-bottom: 15px;" class="btn btn-sm btn-default"/><?=_("등 록")?></button>
               <button type="button" style="margin-bottom: 15px;" class="btn btn-sm btn-default"onclick="window.close();"><?=_("닫 기")?></button>
            </form>
         </div>
      </div>      
   </div>
</div>

<? include dirname(__FILE__) . "/../_pfooter.php"; ?>