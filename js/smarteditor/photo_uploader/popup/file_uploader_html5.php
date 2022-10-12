<?php
include "../../../../lib/library.php";

 	$sFileInfo = '';
	$headers = array();
	 
	foreach($_SERVER as $k => $v) {
		if(substr($k, 0, 9) == "HTTP_FILE") {
			$k = substr(strtolower($k), 5);
			$headers[$k] = $v;
		} 
	}
	
	$file = new stdClass;
	$file->name = str_replace("\0", "", rawurldecode($headers['file_name']));
	$file->size = $headers['file_size'];
	$file->content = file_get_contents("php://input");
   
	
	$filename_ext = strtolower(array_pop(explode('.',$file->name)));
	$allow_file = array("jpg", "png", "bmp", "gif"); 
	
	if(!in_array($filename_ext, $allow_file)) {
		echo "NOTALLOW_".$file->name;
	} else {
    
    
    //$uploadDir = "../../../../data/$cid/goods_detail/";    
    if ($headers['file_mode'] == "goods")    
      $sub_path = "goods_detail";
		else if ($headers['file_mode'] == "editor_banner")    
      $sub_path = "editor_banner";
    else
      $sub_path = "editor";
    
    $uploadDir = "../../../../data/$sub_path/";
    $uploadPath = "/data/$sub_path/";       
		//$uploadDir = '../../upload/';
		if(!is_dir($uploadDir)){
			mkdir($uploadDir, 0777);
		}		
		
    //저장폴더 구조별 폴더 생성해야 한다   20150128  chunter
    //printgroup 내 classic 반영		20160502		chunter
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
		//$newPath = $uploadDir.iconv("utf-8", "cp949", $file->name);
		
		if(file_put_contents($newPath, $file->content)) {
			$sFileInfo .= "&bNewLine=true";
			$sFileInfo .= "&sFileName=".$fsrc;
      $sFileInfo .= "&sFileURL=".$uploadPath.$fsrc;
      
			//$sFileInfo .= "&sFileName=".$file->name;
			//$sFileInfo .= "&sFileURL=upload/".$file->name;
		}
		
		echo $sFileInfo;
	}
?>