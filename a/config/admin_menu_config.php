<?

include "../_header.php";
include "../_left_menu.php";

	$mode = $_GET[mode];    
  	$admin_id = $_GET[mid];
  
?>  


<div id="content" class="content">
  
  <ol class="breadcrumb pull-right">
    <li><a href="javascript:;">Home</a></li>
    <li class="active"><?=_("메뉴 설정")?></li>
  </ol>
      
  <h1 class="page-header"><?=_("메뉴 설정")?> <small><?=$cid?> >> <?=$admin_id?></small></h1>

      

  <div class="row">
    
    <div class="col-md-12">
      <div class="panel panel-inverse">
        <div class="panel-heading">
          <div class="panel-heading-btn">
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>            
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
          </div>
          <h4 class="panel-title"><?=_("메뉴 설정")?></h4>
        </div>
        
        <div class="panel-body">
          <form class="form-horizontal" method="POST" action="indb.php">
            <input type="hidden" name="mode" value="menu_priv">
            <input type="hidden" name="admin_id" value="<?=$admin_id?>">            
                      
            <div class="form-group">
              <div class="col-md-12">


<?              
  	### 메뉴 정보 추출
  	//$data = $db->fetch("select * from tb_admin_menu where cid='$cid' and admin_cid='$admin_id'");
  	//$data = "!";
  	$data = $db->fetch("select * from exm_admin where cid='$cid' and mid='$admin_id'");
	//debug($data);
  	if ($data[priv]) { 
	    $r_priv = unserialize($data[priv]); //menu_priv
	    
		/*foreach ($r_priv as $key => $val) {
			foreach ($val as $ke=>$va) {
				foreach ($va as $k=>$v) {			
					$priv[$v] = $v;
				}
			}	
		}
		
		debug($priv);*/
  	}
?>


<table id="data-table" class="table table-striped table-bordered">
<?
	foreach ($admin_config[allow_left_menu] as $key => $value) 
	{
		
		$rowspan = 0;
		foreach($value as $rowKey => $rowValue){
			if ($rowKey == 0) continue;			
			foreach ($rowValue as $rowFinalKey => $rowFinalValue){
				if ($rowFinalKey == 0) continue;
				++$rowspan;
		}}
		
		if ($key % 2) $tr_class = "even gradeA";
    if ($key == 0) $tr_class = "odd gradeX";
		
		if (is_array($value))	
		{		
			$nameFolder = $service_menu[$value[0]][display];
			if (!$nameFolder) $nameFolder = $value[0];
		}
		else
			$nameFolder = $service_menu[$value][display];
		
		
		
?>
	<tr class="<?=$tr_class?>">
		<td class="" rowspan="<?=$rowspan?>" width="200">
	    <?=$nameFolder?> <a href="javascript:void(0)" onclick="autoAll(this)">ALL</a>
	
	    <div style="position:relative"><div class="auto_selectbox hidden">
	    <select onchange="autoUpdate(this,'cate1_<?=$nameFolder?>')">
	    <option value=""><?=_("선택해주세요")?>
	    <? foreach ($_r_select as $k3=>$v3){ ?>
	    <option value="<?=$k3?>" style="background-color:<?=$_r_admin_menu_select_color[$k3]?>;"><?=$v3?>
	    <? } ?>
	    </select>
	    </div></div>
		</td>
  
<?
//debug($value);

		if (is_array($value)) {
			
			foreach ($value as $skey => $svalue) {
				if ($skey == 0) continue;
				$rowspan = count($svalue) - 1;
				
				if (is_array($svalue))
				{
					$nameSection = $service_menu[$svalue[0]][display];
					if (!$nameSection) $nameSection = $svalue[0];
				}
				else
					$nameSection = $service_menu[$svalue][display];		
				
				$_nameSection = preg_replace("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $nameSection);			
?>
		<td class="" rowspan="<?=$rowspan?>" width="200">
	    <?=$nameSection?> <a href="javascript:void(0)" onclick="autoAll(this)">ALL</a>
	
	    <div style="position:relative"><div class="auto_selectbox hidden">
	    <select onchange="autoUpdate(this,'cate2_<?=$nameFolder?>_<?=$_nameSection?>')">
	    <option value=""><?=_("선택해주세요")?>
	    <? foreach ($_r_select as $k3=>$v3){ ?>
	    <option value="<?=$k3?>" style="background-color:<?=$_r_admin_menu_select_color[$k3]?>;"><?=$v3?>
	    <? } ?>
	    </select>
	    </div></div>
	
	  </td>


<?
				//debug($svalue);
				//exit;
				if (is_array($svalue)) {
					foreach ($svalue as $subkey => $subvalue) {
						if ($subkey == 0) continue;
						
						$namePage = $service_menu[$subvalue][display];
						$file = $service_menu[$subvalue]['link'];
							
?>

		<td class="" width="200"><?=$namePage?></td>
  	<td class="" style="text-align:center;">
  		<select class="cate1_<?=$nameFolder?> cate2_<?=$nameFolder?>_<?=$_nameSection?>" name="<?=$nameFolder?>[<?=$nameSection?>][<?=$namePage?>]">    
    	<option value="<?=$subvalue?>" style="background-color:<?=$_r_admin_menu_select_color[9]?>;" <?if($r_priv[$nameFolder][$nameSection][$namePage]==$subvalue){?>selected<? } ?>><?=_("사용함")?> </option>
    	<option value="N" style="background-color:<?=$_r_admin_menu_select_color[0]?>;" <?if($r_priv[$nameFolder][$nameSection][$namePage] == "N"){?>selected<? } ?>><?=_("사용하지 않음")?></option>    
  	</select>  
  	</td>
	</tr>



<?
					}
				}		//is_array($svalue)

			}
		}
	}
?>
</table>
     


	                
            </div>
            
            
            
            <div class="form-group">
              <label class="col-md-2 control-label"></label>
              <div class="col-md-10">
                <button type="submit" class="btn btn-sm btn-success"><?=_("저 장")?></button>
                <!--button type="button" class="btn btn-sm btn-error" onclick="history.back();">뒤 로</button-->
              </div>
            </div>
                          
          </form>
        </div>
      </div>
    </div>
    
    
     
    
  </div>


</div>
<!-- end #content -->


<script>
/*** 일괄설정박스 보이기숨기기 ***/
function autoAll(obj){
    var auto_selectbox = $j(".auto_selectbox",obj.parentNode);
    $j("select>option[value='']",auto_selectbox).attr("selected","true"); //초기화
    $j(".auto_selectbox",obj.parentNode).toggleClass("hidden");
}

/*** 일괄적용 ***/
function autoUpdate(obj,target){
    if (!obj.value) return false;
    //console.log(target);
    //console.log(obj);
    //console.log($j("."+target).html());
    
    $j("."+target).each(function(){
      //$j(this).val(obj.value);
      //console.log($j(this)[0]);
      if (obj.value == "9")
        $j(this)[0].selectedIndex = 0;      //사용함.
      else
        $j(this)[0].selectedIndex = 1;      //사용하지 않음.
    })
    $j(obj).parent().addClass("hidden");
}
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>

