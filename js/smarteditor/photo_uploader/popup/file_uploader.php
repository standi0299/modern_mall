<?php
include "../../../../lib/library.php";

// default redirection
$url = $_REQUEST["callback"].'?callback_func='.$_REQUEST["callback_func"];
$bSuccessUpload = is_uploaded_file($_FILES['Filedata']['tmp_name']);

// SUCCESSFUL
if(bSuccessUpload) {
	$tmp_name = $_FILES['Filedata']['tmp_name'];
	$name = $_FILES['Filedata']['name'];
	
  
  if (!is_uploaded_file($tmp_name)){
    msg('이미지 파일을 업로드해주세요',-1);
    exit;
  }
  
  
	$filename_ext = strtolower(array_pop(explode('.',$name)));
	$allow_file = array("jpg", "png", "bmp", "gif");
	
	if(!in_array($filename_ext, $allow_file)) {
		$url .= '&errstr='.$name;
	} else {
    
    //저장폴더 구조별 폴더 생성해야 한다   20150128  chunter    
    if ($_POST[mode] == "goods")    
      $sub_path = "goods_detail";
		else if ($_POST[mode] == "editor_banner")    
      $sub_path = "editor_banner";
    else
      $sub_path = "editor";
    
		$uploadDir = "../../../../data/$sub_path/";
    $uploadPath = "/data/$sub_path/";
    if(!is_dir($uploadDir)){
      mkdir($uploadDir, 0777);
    }
		        
    //printgroup 내 classic 반영			20160502		chunter  
    if (in_array($cfg[skin], $r_not_make_public_skin))
    {                  
      $uploadDir = "../../../../data/$sub_path/$cid/";
      $uploadPath = "/data/$sub_path/$cid/";
			
			if(!is_dir($uploadDir)){
				mkdir($uploadDir, 0777);
			}
    }   
				
    $fsrc = md5(uniqid(rand(), true));
		$newPath = $uploadDir.$fsrc;
    //$newPath = $uploadDir.urlencode($_FILES['Filedata']['name']);
		
		@move_uploaded_file($tmp_name, $newPath);
		
		$url .= "&bNewLine=true";
    $url .= "&sFileName=".$fsrc;
    $url .= "&sFileURL=".$uploadPath.$fsrc;
    
		//$url .= "&sFileName=".urlencode(urlencode($name));
		//$url .= "&sFileURL=/data/editor/".urlencode(urlencode($name));
	}
}
// FAILED
else {
	$url .= '&errstr=error';
}
	
header('Location: '. $url);
?>