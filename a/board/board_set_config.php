<?
include "../_header.php";
include "../_left_menu.php";


/* 스킨목록가져오기 */
$dir = "../../skin/$cfg[skin]/board/";
debug($dir);
$r_skin = ls($dir);

/* 레이아웃가져오기 */
### 레이아웃 파일
$tmp = ls("../../skin/{$cfg[skin]}/layout");
foreach ($tmp as $v){
	$div = explode(".",$v);
	$layout[$div[0]][] = $div[1];
}
## 회원그룹 추출
$r_grp = getMemberGrp(1);

if (!$_GET[board_id]){

	#기본값 설정
	$data = array(
		"board_skin"			=> "board", //default
		"board_width"			=> "100%",
		"align"					=> "left",
		"writer_type"			=> "mid",
		"limit_new"				=> 24,
		"limit_hot"				=> 100,
		"on_reply"				=> 0,
		"on_comment"			=> 0,
		"on_category"			=> 0,
		"on_secret"				=> 0,
		"on_email"				=> 0,
		"on_file"				=> 0,
		"on_subject_deco"		=> 0,
		"permission_list"		=> 0,
		"permission_read"		=> 0,
		"permission_write"		=> 0,
		"permission_reply"		=> 0,
		"permission_comment"	=> 0,
		"use_sms_write"			=> 0,
		"use_sms_reply"			=> 0,
	);
	$mode = "board_create";
		
	$data[filter_text] = $_board_default_filter_text;
	$data[name_close] = $_board_default_name_close;
	
} else {
	$m_board = new M_board();
	$data = $m_board -> getBoardSetInfo($cid, $_GET[board_id]);
	//$data = $db->fetch("select * from exm_board_set where cid = '$cid' and board_id = '$_GET[board_id]'");
	if (!$data[board_id]){
		msg(_("존재하지 않는 게시판입니다."),-1);
		exit;
	}
	$mode = "board_modify";
}

if (!$data[layout_top]) $data[layout_top] = "default";
if (!$data[layout_left]) $data[layout_left] = "default";
if (!$data[layout_bottom]) $data[layout_bottom] = "default";

if ($data[board_width]) {
	if (strpos($data[board_width],"%")){
		$data[board_width] = explode("%",$data[board_width]);
		$data[board_width][1] = "%";
	}	
	else {
		if (strpos($data[board_width],"px")){
			$data[board_width] = explode("px",$data[board_width]);
			$data[board_width][1] = "px";
		}
	}
}

$selected[board_skin][$data[board_skin]] = "selected";
$selected[board_align][$data[board_align]] = "selected";
$checked[writer_type][$data[writer_type]] = "checked";
$checked[on_reply][$data[on_reply]] = "checked";
$checked[on_comment][$data[on_comment]] = "checked";
$checked[on_category][$data[on_category]] = "checked";
$checked[on_secret][$data[on_secret]] = "checked";
$checked[on_email][$data[on_email]] = "checked";
$checked[on_file][$data[on_file]] = "checked";
$checked[use_sms_write][$data[use_sms_write]] = $checked[use_sms_reply][$data[use_sms_reply]] = "checked";

$selected[limit_filecount][$data[limit_filecount]] = "selected";
$selected[permission_list][$data[permission_list]] = "selected";
$selected[permission_read][$data[permission_read]] = "selected";
$selected[permission_write][$data[permission_write]] = "selected";
$selected[permission_reply][$data[permission_reply]] = "selected";
$selected[permission_comment][$data[permission_comment]] = "selected";
$selected[layout_top][$data[layout_top]] = "selected";
$selected[layout_left][$data[layout_left]] = "selected";
$selected[layout_bottom][$data[layout_bottom]] = "selected";

//20150116    chunter
$checked[remote_connect][$data[remote_connect]] = "checked";
$checked[ip_select][$data[ip_select]] = "checked";
$checked[use_filter][$data[use_filter]] = "checked";
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
      <li>
         <a href="javascript:;"><?=_("게시판 관리")?></a>
      </li>
      <li class="active">
         <?=_("게시판관리")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("게시판 관리")?> <small><?=_("생성된 게시판 관리")?>.</small></h1>
   
   
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" onsubmit="return form_chk(this)">
   	<input type="hidden" name="mode" value="<?=$mode?>"/>
   <div class="panel panel-inverse">
      <div class="panel-heading">
        <h4 class="panel-title"><?=_("기본설정")?></h4>
      </div>

      <div class="panel-body panel-form">

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("게시판 ID")?></label>
               <div class="col-md-4">                  
               	<? if ($data[board_id]){ ?>
               	<b><?=$data[board_id]?></b>
               	<input type="hidden" name="board_id" value="<?=$data[board_id]?>"/>
								<? } else { ?>
                  <input type="text" class="form-control" name="board_id" pt="_pt_board_id" onblur="chk_id(this)" required/>
                  <input type="hidden" name="chk_board_id" msg='<?=_("게시판 아이디가 유효하지 않습니다.")?>' required/>
                <? } ?>                  
               </div>  
               <label class="col-md-6 control-label" id="txt_board_id">
                  <span class="pull-left">- <?=_("영소문자로 시작하는 3~20자의 영소문자/숫자/-/_ 의 문자열을 입력해주세요.")?></span> 
							</label>             
            </div>

            <div class="form-group">
              <label class="col-md-2 control-label"><?=_("게시판명")?></label>
              <div class="col-md-4">
                  <input type="text" class="form-control" name="board_name" value="<?=$data[board_name]?>" />
              </div>
              <label class="col-md-6 control-label">
                  <span class="pull-left">- <?=_("쌍따옴표(\"),역슬래시(\) 는 사용이 불가합니다.")?></span> 
							</label>
               
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("게시판스킨")?></label>
               <div class="col-md-4">
                  <select name="board_skin" class="form-control" required>
									<? foreach ($r_skin as $v){ ?>
									<option value="<?=$v?>" <?=$selected[board_skin][$v]?>/><?=$v?>
									<? } ?>
									</select>
               </div>
               <label class="col-md-6 control-label">
                  <span class="pull-left">- <?=_("게시판에 사용될 스킨을 선택해주세요.")?></span> 
							</label>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("게시판너비")?></label>
               <div class="col-md-2">
                  <input type="text" class="form-control" name="board_width[]" pt="_pt_numplus" value="<?=$data[board_width][0]?>">
               </div>
               
               <div class="col-md-2">               
               <select name="board_width[]" class="form-control">
								<option value="%" <?if($data[board_width][1]=="%"){?>selected<?}?>>%
								<option value="px" <?if($data[board_width][1]=="px"){?>selected<?}?>>px
							 </select>
							 </div>
							 
               <label class="col-md-6 control-label">
                  <span class="pull-left">- <?=_("게시판의 너비를 입력해주세요. % 혹은 px 이 가능합니다. (권장:100%)")?></span>
							</label>
            </div>
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("게시판정렬")?></label>
               <div class="col-md-2">
                  <input type="text" class="form-control" name="board_width[]" pt="_pt_numplus" value="<?=$data[board_width][0]?>">
               </div>
               
               <div class="col-md-2">               
               <select name="board_align" class="form-control">
								<option value="left" <?=$selected[board_align][left]?>/><?=_("좌측정렬")?>
								<option value="center" <?=$selected[board_align][center]?>/><?=_("중앙정렬")?>
								<option value="right" <?=$selected[board_align][right]?>/><?=_("우측정렬")?>
							 </select>
							 </div>
							 
               <label class="col-md-6 control-label">
                  <span class="pull-left">- <?=_("사용자페이지의 출력범위 내에서의 게시판의 위치를 지정해주세요. (권장:좌측정렬)")?></span>
							</label>
            </div>
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("작성자표시방식")?></label>
                              
               <div class="col-md-4">
                  <label class="radio-inline">
                      <input type="radio" name="writer_type" value="mid" <?=$checked[writer_type][mid]?>/> <?=_("아이디")?>
                  </label>
                  <label class="radio-inline">
                      <input type="radio" name="writer_type" value="name" <?=$checked[writer_type][name]?> /> <?=_("이름")?>
                  </label>
							 </div>
							 
               <label class="col-md-6 control-label">
                  <span class="pull-left">- <?=_("게시판 작성자의 노출방식입니다. 비회원의 경우 이름이 노출됩니다.")?></span>
							</label>
            </div>               
            
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("SMS안내")?></label>
               <div class="col-md-4">
                  <label class="col-md-4 control-label"><?=_("글등록")?></label>                  
                  <div class="col-md-8">		              
		                  <label class="radio-inline"> 
		                      <input type="radio" name="use_sms_write" value="0" <?=$checked[use_sms_write][0]?>/> <?=_("사용안함")?>
		                  </label>
		                  <label class="radio-inline">
		                      <input type="radio" name="use_sms_write" value="1" <?=$checked[use_sms_write][1]?> /> <?=_("사용함")?>
		                  </label>		              
							 		</div>							 
							 		
							 		<label class="col-md-4 control-label"><?=_("답글등록")?></label>                  
                  <div class="col-md-8">		              
		                  <label class="radio-inline"> 
		                      <input type="radio" name="use_sms_reply" value="0" <?=$checked[use_sms_reply][0]?>/> <?=_("사용안함")?>
		                  </label>
		                  <label class="radio-inline">
		                      <input type="radio" name="use_sms_reply" value="1" <?=$checked[use_sms_reply][1]?> /> <?=_("사용함")?>
		                  </label>		              
							 		</div>
               </div>
							 
               <label class="col-md-6 control-label">
                  <span class="pull-left">- <?=_("SMS전송설정은 메세지관리 > 자동 SMS관리에서 설정하실 수 있습니다.")?></span>
							</label>
            </div>
         
      </div>
   </div>
   
   
   
   
   <div class="panel panel-inverse">
      <div class="panel-heading">
        <h4 class="panel-title"><?=_("리스트설정")?></h4>
      </div>

      <div class="panel-body panel-form">        
            

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("제목출력길이")?></label>
               <div class="col-md-2">
                  <input type="text" class="form-control" name="subject_length" pt="_pt_numplus" value="<?=$data[subject_length]?>"/>                  
               </div>               
               <div class="col-md-6">
               	<p class="pull-left">- <?=_("목록에서 출력될 제목의 길이를 넘게되면 \"..\" 로 보여집니다. (최대:255byte)")?><BR>               	
               	- <?=_("사용을 원하지 않으면 0 혹은 빈값을 입력해주세요.")?><br>
               	- <?=_("한글:3Byte, 영문:1Byte (ex:12byte 로 설정시 한글은 4글자까지 보여집니다.)")?></p>
               </div>               
            </div>
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("한페이지당 게시물")?></label>
               <div class="col-md-2">
                  <input type="text" class="form-control" name="num_per_page" pt="_pt_numplus" value="<?=$data[num_per_page]?>"/>                  
               </div>
               <label class="col-md-6 control-label">
               	<span class="pull-left"><?=_("0 혹은 빈값을 입력시, 기본값 20개로 고정되어 출력 됩니다. (최대:255)")?></span>               	
               </label>
            </div>
            
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("갤러리형 스킨설정")?></label>
               <div class="col-md-6">
                  <div class="col-md-12">
                  <label class="col-md-4 control-label"><?=_("리스트이미지 크기")?></label>                  
                  <div class="col-md-2">		                  
		              	<input type="text" class="form-control" name="gallery_w" value="<?=$data[gallery_w]?>" pt="_pt_numplus"/>
		              </div> 
		              <div class="col-md-2">
		              	<input type="text" class="form-control" name="gallery_h" value="<?=$data[gallery_h]?>" pt="_pt_numplus" />
		              </div>
		              <label class="col-md-4 control-label"><span class="pull-left">(<?=_("너비*길이")?>)</span></label>
		              </div>

									<div class="col-md-12">
							 		<label class="col-md-4 control-label"><?=_("한행에")?></label>                  
                	<div class="col-md-4">
                		<input type="text" class="form-control" name="gallery_num" value="<?=$data[gallery_num]?>" pt="_pt_numplus"/>
                	</div>
	                <label class="col-md-4 control-label"><span class="pull-left"><?=_("개의 이미지 출력")?></span></label>
	                </div>							 		
               </div>
            </div>
            
            
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("NEW ICON 출력기준")?></label>
               <div class="col-md-10">                  
                  <label class="col-md-2 control-label"><?=_("현재로 부터")?></label>                  
                  <div class="col-md-1">		                  
		              	<input type="text" class="form-control" name="limit_new" value="<?=$data[limit_new]?>" pt="_pt_numplus"/>
		              </div>
		              <label class="col-md-3 control-label"><span class="pull-left"><?=_("시간내에 작성된 글")?></span></label>		              
	                <label class="col-md-6 control-label"><span class="pull-left">- <?=_("최대:999, 사용을 원하지 않으면 0 혹은 빈값을 입력해주세요.")?></span></label>	                							 		
               </div>
            </div>
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("HOT ICON 출력기준")?></label>
               <div class="col-md-10">                  
                  <label class="col-md-2 control-label"><?=_("작성후")?></label>                  
                  <div class="col-md-1">		                  
		              	<input type="text" class="form-control" name="limit_hot" value="<?=$data[limit_hot]?>" pt="_pt_numplus"/>
		              </div>
		              <label class="col-md-3 control-label"><span class="pull-left"><?=_("번 이상 조회된 글")?></span></label>		              
	                <label class="col-md-6 control-label"><span class="pull-left">- <?=_("최대:99999999, 사용을 원하지 않으면 0 혹은 빈값을 입력해주세요.")?></span></label>	                							 		
               </div>
            </div>
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("레이아웃")?></label>
               <div class="col-md-10">                  
                  <label class="col-md-1 control-label"><?=_("상단")?></label>                  
                  <div class="col-md-2">		                  
		              	<select name="layout_top" class="form-control">
											<? foreach ($layout[top] as $v){ ?>
											<option value="<?=$v?>" <?=$selected[layout_top][$v]?>><?=$v?>
											<? } ?>
										 </select>
		              </div> 
		              <label class="col-md-1 control-label"><?=_("좌측")?></label>                  
                  <div class="col-md-2">		                  
		              	<select name="layout_left" class="form-control">
											<? foreach ($layout[left] as $v){ ?>
											<option value="<?=$v?>" <?=$selected[layout_left][$v]?>><?=$v?>
											<? } ?>
										 </select>
		              </div> 
		              <label class="col-md-1 control-label"><?=_("하단")?></label>                  
                  <div class="col-md-2">		                  
		              	<select name="layout_bottom" class="form-control">
											<? foreach ($layout[bottom] as $v){ ?>
											<option value="<?=$v?>" <?=$selected[bottom][$v]?>><?=$v?>
											<? } ?>
										 </select>
		              </div>
              	</div>
            </div>
            
             
         
      </div>
  	</div>
  	
  	
  	
  	
  	<div class="panel panel-inverse">
      <div class="panel-heading">
        <h4 class="panel-title"><?=_("작성화면설정")?></h4>
      </div>

      <div class="panel-body panel-form">        
            

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("제목꾸미기")?></label>
               <div class="col-md-3">
                  <label class="checkbox-inline"><input type="checkbox" name="on_subject_deco[]" value="1" <?if($data[on_subject_deco]&1){ echo "checked"; }?> /><?=_("굵기")?></label>              
                  <label class="checkbox-inline"><input type="checkbox" name="on_subject_deco[]" value="2" <?if($data[on_subject_deco]&2){ echo "checked"; }?> /><?=_("색상")?></label>
                  <label class="checkbox-inline"><input type="checkbox" name="on_subject_deco[]" value="4" <?if($data[on_subject_deco]&4){ echo "checked"; }?> /><?=_("크기")?></label>
               </div>               
               <label class="col-md-7 control-label">
               	<span class="pull-left">- <?=_("글작성시 제목을 꾸밉니다.(리스트,읽기페이지에 간단한 텍스트 태그로 꾸며져 출력됩니다.)")?></span>
               </label>               
            </div>
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("답글")?></label>
               <div class="col-md-3">
                  <label class="checkbox-inline"><input type="radio" name="on_reply" value="0" <?=$checked[on_reply][0]?> /><?=_("미사용")?></label>              
                  <label class="checkbox-inline"><input type="radio" name="on_reply" value="1" <?=$checked[on_reply][1]?> /><?=_("사용")?></label>                  
               </div>               
               <label class="col-md-7 control-label">
               	<span class="pull-left">- <?=_("미사용시 게시판의 답글 기능이 차단됩니다. (기존 답글 데이터는 노출됩니다.)")?></span>
               </label>               
            </div>
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("코멘트")?></label>
               <div class="col-md-3">
                  <label class="checkbox-inline"><input type="radio" name="on_comment" value="0" <?=$checked[on_comment][0]?> /><?=_("미사용")?></label>              
                  <label class="checkbox-inline"><input type="radio" name="on_comment" value="1" <?=$checked[on_comment][1]?> /><?=_("사용")?></label>                  
               </div>               
               <label class="col-md-7 control-label">
               	<span class="pull-left">- <?=_("미사용시 게시판의 코멘트 기능이 차단됩니다. (기존 코멘트 데이터는 삭제되지 않으나 노출을 막습니다.)")?></span>
               </label>               
            </div>
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("email입력")?></label>
               <div class="col-md-3">
                  <label class="checkbox-inline"><input type="radio" name="on_email" value="0" <?=$checked[on_email][0]?> /><?=_("미사용")?></label>              
                  <label class="checkbox-inline"><input type="radio" name="on_email" value="1" <?=$checked[on_email][1]?> /><?=_("사용")?></label>                  
               </div>               
               <label class="col-md-7 control-label">
               	<span class="pull-left">- <?=_("작성시 email을 필수로 입력받습니다. (미사용시 기존 게시물의 이메일은 삭제되지 않으나 출력되지 않습니다.)")?></span>
               </label>               
            </div>
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("파일업로드")?></label>
               <div class="col-md-3">
                  <label class="checkbox-inline"><input type="radio" name="on_file" value="0" <?=$checked[on_file][0]?> onclick="$j('#file_upload_div1').hide();$j('#file_upload_div2').hide();" /><?=_("미사용")?></label>              
                  <label class="checkbox-inline"><input type="radio" name="on_file" value="1" <?=$checked[on_file][1]?> onclick="$j('#file_upload_div1').show();$j('#file_upload_div2').show();" /><?=_("사용")?></label>                  
               </div>               
               <label class="col-md-7 control-label">
               	<span class="pull-left">- <?=_("게시판작성시 파일첨부를 받습니다. (미사용시 첨부기능을 막으나 기존의 파일데이터는 노출됩니다.)")?></span>
               </label>               
            </div> 
            
            
            <div class="form-group" <?if(!$data[on_file]){?>style="display:none"<?}?> id="file_upload_div1">
               <label class="col-md-2 control-label"><?=_("업로드제한수량")?></label>
               <div class="col-md-3">
                  <select name="limit_filecount" class="form-control">
									<? for ($i=1;$i<6;$i++){ ?>
									<option value="<?=$i?>" <?=$selected[limit_filecount][$i]?>/><?=$i?><?=_("개")?>
									<? } ?>
									</select>                
               </div>               
               <label class="col-md-7 control-label">
               	<span class="pull-left">- <?=_("파일첨부시의 가능한 최대 첨부량을 선택합니다.")?></span>
               </label>               
            </div>
            <div class="form-group" <?if(!$data[on_file]){?>style="display:none"<?}?> id="file_upload_div2">
               <label class="col-md-2 control-label"><?=_("업로드용량제한")?></label>
               <div class="col-md-2">
                  <input type="text" class="form-control" name="limit_filesize" value="<?=$data[limit_filesize]?>" pt="_pt_numplus"/>               
               </div>
               <label class="col-md-1 control-label">
               	<span class="pull-left">KByte</span>
               </label>
                              
               <label class="col-md-7 control-label">
               	<span class="pull-left">- <?=_("파일첨부시 파일당 최대용량을 제한합니다. (미입력시 또는 최대값을 초과하는 경우 서버의 정책에 따르게 됩니다.)")?></span>
               </label>               
            </div>
         
      </div>
  	</div>
  	
  	
  	<div class="panel panel-inverse">
      <div class="panel-heading">
        <h4 class="panel-title"><?=_("분류 및 권한설정")?></h4>
      </div>

      <div class="panel-body panel-form">        
            

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("분류")?></label>
               <div class="col-md-3">
                  <label class="checkbox-inline"><input type="radio" name="on_category" value="0" <?=$checked[on_category][0]?> onclick="$j('#category_div').hide();" /><?=_("미사용")?></label>              
                  <label class="checkbox-inline"><input type="radio" name="on_category" value="1" <?=$checked[on_category][1]?> onclick="$j('#category_div').show();" /><?=_("사용")?></label> 
               </div>               
               <div class="col-md-7">
               	<span class="pull-left">- <?=_("게시판의 분류기능을 사용합니다. (미사용시 기존 게시물의 분류에 관한 데이터는 삭제되지 않으나 노출을 막습니다.)")?></span>
               </div>               
            </div>
            
            <div class="form-group" <?if(!$data[on_category]){?>style="display:none"<?}?> id="category_div">
               <label class="col-md-2 control-label"><?=_("분류입력")?></label>
               <div class="col-md-3">
                  <input type="text" name="category" value="<?=$data[category]?>" pt="_pt_txt" class="form-control"/>
               </div>               
               <div class="col-md-7">
               	<span class="pull-left">- <?=_("사용하실 분류를 쉼표로 구분하여 입력(ex:분류1,분류2,분류3)해주세요. 쌍따옴표(\"),역슬래시(\) 는 사용이 불가합니다.")?></span>
               </div>               
            </div>
            
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("비밀글")?></label>
               <div class="col-md-8">
                  <label class="checkbox-inline"><input type="radio" name="on_secret" value="0" <?=$checked[on_secret][0]?> /><?=_("사용-일반글기본")?></label>              
                  <label class="checkbox-inline"><input type="radio" name="on_secret" value="1" <?=$checked[on_secret][1]?> /><?=_("사용-비밀글기본")?></label>                  
                  <label class="checkbox-inline"><input type="radio" name="on_secret" value="2" <?=$checked[on_secret][2]?> /><?=_("모두일반글")?></label>
                  <label class="checkbox-inline"><input type="radio" name="on_secret" value="3" <?=$checked[on_secret][3]?> /><?=_("모두비밀글")?></label>
               </div>     
            </div>
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("사용권한")?></label>
               <div class="col-md-10">                  
                  
		              
		              <table class="table table-striped">
	<tr>
		<th><?=_("리스트")?></th>
		<th><?=_("읽기")?></th>
		<th><?=_("쓰기")?></th>
		<th><?=_("답글쓰기")?></th>
		<th><?=_("댓글쓰기")?></th>
	</tr>
	<tr>
		<td>
		<select class="form-control" name="permission_list"/>
		<option value="0" <?=$selected[permission_list][0]?>/><?=_("무제한")?>
		<? foreach ($r_grp as $v){ ?>
		<option value="<?=$v[grplv]?>" <?=$selected[permission_list][$v[grplv]]?>/> <?=$v[grpnm]?> (Lv:<?=$v[grplv]?>)
		<? } ?>
		<option value="99" <?=$selected[permission_list][99]?>><?=_("관리자")?>
		</select>
		</td>	
		<td>
		<select class="form-control" name="permission_read"/>
		<option value="0" <?=$selected[permission_read][0]?>/><?=_("무제한")?>
		<? foreach ($r_grp as $v){ ?>
		<option value="<?=$v[grplv]?>" <?=$selected[permission_read][$v[grplv]]?>/> <?=$v[grpnm]?> (Lv:<?=$v[grplv]?>)
		<? } ?>
		<option value="99" <?=$selected[permission_read][99]?>><?=_("관리자")?>
		</select>
		</td>
		<td>
		<select class="form-control" name="permission_write"/>
		<option value="0" <?=$selected[permission_write][0]?>/><?=_("무제한")?>
		<? foreach ($r_grp as $v){ ?>
		<option value="<?=$v[grplv]?>" <?=$selected[permission_write][$v[grplv]]?>/> <?=$v[grpnm]?> (Lv:<?=$v[grplv]?>)
		<? } ?>
		<option value="99" <?=$selected[permission_write][99]?>><?=_("관리자")?>
		</select>
		</td>
		<td>
		<select class="form-control" name="permission_reply"/>
		<option value="0" <?=$selected[permission_reply][0]?>/><?=_("무제한")?>
		<? foreach ($r_grp as $v){ ?>
		<option value="<?=$v[grplv]?>" <?=$selected[permission_reply][$v[grplv]]?>/> <?=$v[grpnm]?> (Lv:<?=$v[grplv]?>)
		<? } ?>
		<option value="99" <?=$selected[permission_reply][99]?>><?=_("관리자")?>
		</select>
		</td>
		<td>
		<select class="form-control" name="permission_comment"/>
		<option value="0" <?=$selected[permission_comment][0]?>/><?=_("무제한")?>
		<? foreach ($r_grp as $v){ ?>
		<option value="<?=$v[grplv]?>" <?=$selected[permission_comment][$v[grplv]]?>/> <?=$v[grpnm]?> (Lv:<?=$v[grplv]?>)
		<? } ?>
		<option value="99" <?=$selected[permission_comment][99]?>><?=_("관리자")?>
		</select>
		</td>
	</tr>
	</table>
		              
		            <span class="pull-left">- <?=_("게시판의 이용권한을 제한합니다. 하위레벨의 그룹선택시, 상위그룹이 함께 권한을 갖습니다.")?></span>  
              	</div>
              	
            </div>
      </div>
  	</div>
  	
  	
  	
  	
  	
  	
  	<div class="panel panel-inverse">
      <div class="panel-heading">
        <h4 class="panel-title"><?=_("공통 출력 설정")?></h4>
      </div>

      <div class="panel-body panel-form">        
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("게시판 상단 HTML")?></label>
               <div class="col-md-10">
               		<textarea class="form-control" name="header_html" rows="5"><?=$data[header_html]?></textarea>
               		<span class="pull-left"><?=_("게시판의 상단에 출력될 HTML")?></span>   
               </div>
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("게시판 하단 HTML")?></label>
               <div class="col-md-10">
               		<textarea class="form-control" name="footer_html" rows="5"><?=$data[footer_html]?></textarea>
               		<span class="pull-left"><?=_("게시판의 하단에 출력될 HTML")?></span>
               </div>
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("기본 입력 내용 HTML")?></label>
               <div class="col-md-10">
                    <textarea class="form-control" name="content_html" rows="5"><?=$data[content_html]?></textarea>
                    <span class="pull-left"><?=_("게시글 입력시 기본으로 등록될 내용 HTML")?></span>
               </div>
            </div>
      </div>
  	</div>
  	
  	
  	
  	
  	
  	<div class="panel panel-inverse">
      <div class="panel-heading">
        <h4 class="panel-title"><?=_("게시판 접근 설정")?></h4>
      </div>

      <div class="panel-body panel-form">        
            

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("불량단어 필터링 여부")?></label>
               <div class="col-md-3">
                  <label class="checkbox-inline"><input type="radio" name="use_filter" value="0" <?=$checked[use_filter][0]?> /><?=_("사용하지 않음")?></label>              
                  <label class="checkbox-inline"><input type="radio" name="use_filter" value="1" <?=$checked[use_filter][1]?> /><?=_("사용")?></label> 
               </div>         
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("필터링 될 불량단어")?></label>
               <div class="col-md-10">
               		<textarea class="form-control" name="filter_text" rows="2"><?=$data[filter_text]?></textarea>
               		<span class="pull-left">- <?=_("구분은 ,(쉼표) 입니다. 정확하게 구분하세요.")?> <br>- <?=_("게시판 등록시 작성된 제목과 내용중에서 필터링 됩니다.")?></span>
               </div>
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("등록 이름 차단")?></label>
               <div class="col-md-10">
               		<textarea class="form-control" name="name_close" rows="2"><?=$data[name_close]?></textarea>
               		<span class="pull-left">- <?=_("구분은 ,(쉼표) 입니다. 정확하게 구분하세요.")?> <br>- <?=_("게시판 등록시 작성된 이름에서 필터링 됩니다.")?> <br>- <?=_("최대 255 글자.")?></span>
               </div>
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("외부 글 등록")?></label>
               <div class="col-md-3">
                  <label class="checkbox-inline"><input type="radio" name="remote_connect" value="0" <?=$checked[remote_connect][0]?> /><?=_("허용")?></label>              
                  <label class="checkbox-inline"><input type="radio" name="remote_connect" value="1" <?=$checked[remote_connect][1]?> /><?=_("허용하지 않음")?></label> 
               </div>         
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("IP 접근기능")?></label>
               <div class="col-md-6">
                  <label class="checkbox-inline"><input type="radio" name="ip_select" value="0" <?=$checked[ip_select][0]?> /><?=_("사용안함")?></label>              
                  <label class="checkbox-inline"><input type="radio" name="ip_select" value="1" <?=$checked[ip_select][1]?> /><?=_("IP 접근 불가 사용")?></label> 
                  <label class="checkbox-inline"><input type="radio" name="ip_select" value="2" <?=$checked[ip_select][2]?> /><?=_("IP 접근 허용 사용")?></label>
               </div>         
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("IP 접근 불가")?></label>
               <div class="col-md-10">
               		<textarea class="form-control" name="ip_close" rows="2"><?=$data[ip_close]?></textarea>
               		<span class="pull-left">- <?=_("구분은 ,(쉼표) 입니다. 정확하게 구분하세요.")?></span>
               </div>
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("IP 접근 허용")?></label>
               <div class="col-md-10">
               		<textarea class="form-control" name="ip_connect" rows="2"><?=$data[ip_connect]?></textarea>
               		<span class="pull-left">- <?=_("구분은 ,(쉼표) 입니다. 정확하게 구분하세요.")?></span>
               </div>
            </div>
      </div>
  	</div>
  	
 	
  	
  
   
		<div class="row">
	    <div class="col-md-11">
	    	<p class="pull-right">
	    	<button type="submit" class="btn btn-md btn-primary m-r-15"><?=_("등록")?></button>
	      <button type="button" class="btn btn-md btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
	      </p>
	  	</div>
	 	</div>
 	
 	</form>
</div>




<script>
function chk_id(obj){

	var board_id = obj.value;
	if (!board_id){
		$('txt_board_id').innerHTML = '- <?=_("영소문자로 시작하는 3~20자의 영소문자/숫자/-/_ 의 문자열을 입력해주세요.")?>';
		document.fm.chk_board_id.value = '';
		return;
	}
	if (!_pattern(obj)){
		$('txt_board_id').innerHTML = '- <?=_("영소문자로 시작하는 3~20자의 영소문자/숫자/-/_ 의 문자열을 입력해주세요.")?>';
		document.fm.chk_board_id.value = '';
		return;
	}

	$j.ajax({
		type: "POST",
		url: "indb.php",
		data: "mode=chk_board_id&board_id=" + board_id,
		success: function(ret){
			switch (ret){

				case "duplicate":

					$('txt_board_id').innerHTML = '-<?=_("이미 사용중인 게시판 ID 입니다.")?>';
					document.fm.chk_board_id.value = '';

					break;

				case "ok":

					$('txt_board_id').innerHTML = '-<?=_("사용이 가능한 게시판 ID 입니다.")?>';
					document.fm.chk_board_id.value = '1';

					break;
			}
		}
	});
}

$j(function(){
	_pt_set();
});
</script>


<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>