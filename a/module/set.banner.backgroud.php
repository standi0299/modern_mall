<?
  include "../_pheader.php"; 
  
  //$codes = "'ph_main_text_1', 'ph_main_text_2', 'ph_main_img'";
  $codes = "'main_top_backgroud'";
  $data = $db->fetch("select * from exm_banner where cid = '$cid' and skin = '$cfg[skin]' and code in ($codes)");
  
  $data[img] = explode("||",$data[img]);
  $data[img_on] = explode("||",$data[img_on]);
  $data[target] = explode("||",$data[target]);
  $data[url] = explode("||",$data[url]);
  $data[spc_desc] = explode("||",$data[spc_desc]);
?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <div id="content" class="content">
	<form method="POST" action="set_indb.php" enctype="multipart/form-data" onsubmit="return chkForm(this)">
	<input type="hidden" name="mode" value="set_banner_backgroud"/>
	<input type="hidden" name="skin" value="<?=$cfg[skin]?>"/>   	
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("메인 페이지 배경 관리")?></a>
            </div>
         </div>
      </div>
      
		<div class="row">
	    	<div class="col-md-12">
				<div class="panel panel-inverse">
				    <div class="panel-heading">
				        <h4 class="panel-title"><?=_("메인 페이지 배경 관리")?></h4>
				    </div>
				    <div class="panel-body">

<table class="tb1">
<tr>
	<th><?=_("코드")?></th>
	<td><b><?=$_GET[code]?></b><input type="hidden" name="code" value="<?=$_GET[code]?>"/></td>
</tr>

<!--//배너별 DESC 추가하여 노출 2015.02.24 by kdk-->  
<tr>
	<th><?=_("설명글")?></th>
	<td><input type="text" name="comment" value="<?=$data[comment]?>" class="w300"/></td>
</tr>

<tr>
  <th>
  	<p><?=_("배너이미지")?></p>  
    <button type="button" class="btn btn-xs btn-primary" onclick="add()" id="add_btn"><?=_("추가")?></button>
  </th>
  <td>
    
    <span class="desc" style="margin-left:85px;color:#01BBD6">
       <?=_(" 드래그를 하여 순서를 변경할 수 있습니다")?>. <?if($img_w){?>(<?=_("사이즈")?> <?=$img_w?>px X <?=$img_h?>px)<?}?> 
    </span>
    <div style="margin-left:-10px;list-style:none">
    <ul id="banner_ul">
<?  
    foreach($data[img] as $k=>$v){
      $selected[target][$k][$data[target][$k]] = "selected";
?>
      <li style="cursor:move;">
      <table class="bannerTb" height="100%">
      <tr valign="middle">
        <td width="60" style="border:0">
<?
    
      $filePath = "../../data/banner/$cid/$data[code]";    
    
      //배너 저장 폴더 데이타가 있을 경우 몰별로 저장위치를 구분하도록 변경된 구조임.      20140623    chunter
      if ($data[file_path])
        $filePath = "../.." .$data[file_path];
      //debug($filePath."/".$v);
    
      if($v && is_file($filePath."/".$v))
      {
        $size = getImageSize($filePath."/".$v);
        switch($size[2]) 
        {        
          case "4": case "13":
?>
        <script>embed("<?=$filePath."/".$v?>",60,60)</script>
<?
            break;
          default:
?>
        <img src="<?=$filePath."/".$v?>" width="60" height="60">  
<?
            break;
        }
      }
    
      $textIndex = $k * 2;
?>

    <input type="hidden" name="num[]" value="<?=$k?>"/>
    <input type="hidden" name="image[]" value="<?=$v?>"/>    
    </td>
    <td style="border:0">
      <span class="stxt"><?=_("상단")?> Text</span> :
    <textarea style="margin:2px 0;width:100%;" name="spc_desc[]"><?=$data[spc_desc][$textIndex]?></textarea>
    <br><span class="stxt"><?=_("하단")?> Text</span> :
    <textarea style="margin:2px 0;width:100%;" name="spc_desc[]"><?=$data[spc_desc][$textIndex + 1]?></textarea>
    <br><span class="stxt"><?=_("이미지")?></span> :
    <input type="file" name="img[]" style="width:100%"/>
    </td>
    <td style="border:0">
    	<button type="button" class="btn btn-xs btn-danger del_btn" onclick="remove_(this)"><?=_("삭제")?></button>    	
    </td>
  </tr>
  </table>
  </li>
  <?}?>
  </ul>
  </div>

  </td>
</tr>

<tr>
	<th><p><?=_("배경 이미지")?></p></th>
	<td>  
	 <table height="100%" style="table-layout:fixed; word-break:break-all;">
	 			  
<?
	$fileNames = array();

    $filePath = "../../data/main_backgroud";
	if (is_dir($filePath)) {
    	$fileNames1 = getFileNames($filePath);

	    foreach ($fileNames1 as $key => $value) 
	    {
	    	//확장자 체크하여 폴더 제외.
   			if (strpos($value, ".") !== FALSE)
	    		$fileNames[] = array('name' => $value, 'path' => $filePath);
		}
	}

	$UseFilePath = "../../data/main_backgroud/$cid";
	if (is_dir($UseFilePath)) {
    	$fileNames2 = getFileNames($UseFilePath);

	    foreach ($fileNames2 as $key => $value) 
	    {
	    	//확장자 체크하여 폴더 제외.
   			if (strpos($value, ".") !== FALSE)
	    		$fileNames[] = array('name' => $value, 'path' => $UseFilePath);
		}
	}

    foreach ($fileNames as $key => $value) 
    {	
      if($value && is_file($value[path]."/".$value[name]))
      {        
        if ($value[name] == $cfg[main_backgroud])
          $checkTag = "checked";
        else
          $checkTag = "";
        
        if ($key == 0 || $key%5 == 0) echo "<tr valign=\"middle\">";
?>        
       <td width="80" style="border:1">
       	<img src="<?=$value[path]."/".$value[name]?>" width="80" height="40">
       	<br><input type="radio" name="main_backgroud" value="<?=$value[name]?>" <?=$checkTag?> ><?=$value[name]?></td>
<?          
        if ($key%5 == 4) echo "</tr>";
      }
    }
?>
    </table>
    
<!--//사용자가 배경 이미지 등록할 수 있도록. 2015.02.24 by kdk-->
    <table height="100%">
	<tr>
		<td style="border:0">
			<span class="stxt"><?=_("이미지")?></span> : <input type="file" id="bgImg" name="bgImg" style="width:100%"/>
		</td>
		<td style="border:0">
			<button type="button" class="btn btn-xs btn-primary" onclick="bgimgAdd()" id="add_btn"><?=_("추가")?></button>
		</td>
	</tr>
    </table>
  </td>
</tr>


<tr>
  <th><p><?=_("색상 테마")?></p></th>
  <td>
   <table height="100%">      
<?
    $rowIndex = 0;
    $selectText = '';

    $arr_main_color = array();
    $arr_main_color = $r_main_color;

    foreach ($arr_main_color as $key => $value) 
    {
      if ($key == $cfg[main_color_theme])
        $checkTag = "checked";
      else
        $checkTag = "";

      if ($rowIndex == 0 || $rowIndex%5 == 0) {
        echo "<tr valign=\"middle\">";
        $selectText = "<tr valign=\"middle\">";
      }      
      $selectText .= "<td><input type='radio' name='main_color_theme' value='$key' $checkTag>$key</td>";
?>        
      <td width="120" height="30" style="border:0;background: <?=$value?>;"> </td>
<?          
      if ($rowIndex%5 == 4) 
      {
        echo "</tr>";
        echo $selectText ."</tr>";
        $selectText = '';
      }        
      $rowIndex++;
    }
    echo $selectText ."</tr>";    
?>
    </table>
  </td>
</tr>
</table>

				    </div>
				</div>    		
	    	</div>
	    </div>
	    <div class="form-group">
	        <label class="col-md-3 control-label"></label>
	        <div class="col-md-9">
	        	<button type="submit" class="btn btn-primary"><?=_("저 장")?></button>
	            <button type="button" class="btn btn-default"onclick="opener.parent.location.reload();window.close();"><?=_("닫  기")?></button>
	        </div>
	    </div>
	</form>	    
   </div>
</div>

<script>
//사용자가 배경 이미지 등록할 수 있도록. 2015.02.24 by kdk
function bgimgAdd() {
	//document.forms[0].
	var img = $j("#bgImg").val();
	if (img.length>0){
		document.forms[0].mode.value = "upload_main_backgroud";
		document.forms[0].submit();
	}
	else {
		alert('<?=_("이미지를 선택하십시오.")?>');
		$j("#bgImg").focus();
	}
}

function add(){
  var target = $j("#banner_ul");
  var inner = $j("#origin_tb").html();
  var li = document.createElement("li");
  li.innerHTML = inner;
  $j(li).css("cursor","move");
  $j(li).clone().appendTo(target);
  $j("li","#banner_ul").mouseover(function(){
    $j(this).css("background","#DEDEDE");
  });
  $j("li","#banner_ul").mouseout(function(){
    $j(this).css("background","transparent");
  });
}
function remove_(obj){
  $j(obj).parent().parent().parent().parent().parent().remove();
}

$j(window).load(function(){
  $j("li","#banner_ul").mouseover(function(){
    $j(this).css("background","#DEDEDE");
  });
  $j("li","#banner_ul").mouseout(function(){
    $j(this).css("background","transparent");
  });
  $j("td",".bannerTb").css("background","transparent");

  $j("input[type=radio][name=spc]").click(function(){
    var spc = $j(this).val();
    var tr_id = "spc_tr_"+spc;
    $j(".spc_tr").hide();
    if (spc){
      $j("#"+tr_id).show();
    }
    switch (spc){
      case "map":

        $j("li:gt(0)","#banner_ul").hide();
        $j("select[name='target[]']").hide();
        $j("input[name='url[]']").hide();
        $j("#add_btn").hide();
        $j(".del_btn").hide();

        break;
      default:

        $j("li:gt(0)","#banner_ul").show();
        $j("select[name='target[]']").show();
        $j("input[name='url[]']").show();
        $j("#add_btn").show();
        $j(".del_btn").show();

        break;
    }
  });
  $j("input[type=radio][name=spc]:checked").trigger("click");

});

</script>

<!-- 카피용 -->
<div id="origin_tb" style="display:none">
<table class="bannerTb" height="100%">
<tr valign="middle">
  <td width="60" style="border:0">
  </td>
  <td style="border:0">
  <input type="hidden" name="image[]" value=""/>  
  <span class="stxt"><?=_("상단")?> Text</span> : <textarea style="margin:2px 0;width:100%;" name="spc_desc[]"></textarea>
  <br><span class="stxt"><?=_("하단")?> Text</span> : <textarea style="margin:2px 0;width:100%;" name="spc_desc[]"></textarea>
  <br><span class="stxt"><?=_("이미지")?></span> : <input type="file" name="img[]" style="width:100%"/><br/>
  </td>
  <td style="border:0">
  	<button type="button" class="btn btn-xs btn-danger del_btn" onclick="remove_(this)"><?=_("삭제")?></button>
  </td>
</tr>
</table>
</div>

<? include "../_pfooter.php"; ?>