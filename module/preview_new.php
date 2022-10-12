<?
header("Pragma: no-cache");
header("Cache: no-cache");
header("Cache-Control: no-cache, must-revalidate");
header("Expires:Mon, 26 Jul 1997 05:00:00 GMT");

$login_offset = true;
$style_not_use = true;
include_once "../_header.php";
include_once "../lib/func.xml.php";

list($podskind, $goodsnm, $pods_use, $preview_app_type) = $db -> fetch("select podskind, goodsnm, pods_use, preview_app_type from exm_goods where goodsno = '$_REQUEST[goodsno]'", 1);

if ($pods_use == "2" || $pods_use == "3") {/* 2.0 상품 */
  $podVersion = '20';
} else {
  $podVersion = '10';
}
		
//2.0 포토북관련 상품만 되도록 처리
if ($podVersion == '20')
{
            
      $clsPods = new PODStation($podVersion);
      $preview = $clsPods->GetPreViewImgToJson($_REQUEST[storageid]);
//debug($preview);
      //setSkinTemplateDefine();      //스킨별, 몰별, 페이지별 설정 파일   20150908
      if (!$preview && $_GET[mobile_type] != "Y") {
				msg(_("미리보기 지원되지 않는 편집입니다."), "close");
      }
			
			
			//cover 타입 결정.
			if ($preview_app_type == "hsoft")
				$cover_display_type = "hard";
			else 
				$cover_display_type = "page";
				
			
			//$preview_app_type = "soft";
			//커버를 먼저 처리한다.			
			$page_cnt = 0;
			$preview_type = "double";
			$bSplitCoverExist = false;
			$cover_loop = array();	
			foreach ($preview as $key => $value) 
			{
				if ($value[type] == "cover_split")
				{
					if (strpos($value[url], "half_2.jpg") !== false)
					{					
						$page_cnt++;
						$value[page_num] = $page_cnt;						
						$cover_loop[] = $value;						
						$bSplitCoverExist = true;			//split 커버가 존재한다.
					}
				}
						
				
				//펼침면은 cover 로 인정하지 않는다. cover_image로 대체.
				if ($value[type] == "cover" && $value[per_page] == "1")
				{				
					$page_cnt++;
					$value[page_num] = $page_cnt;
					$cover_loop[] = $value;
				}
			}
			
			//soft 인경우 커버가 없을 경우 첫번째 커버가 무조건 필요하다.
			if ($preview_app_type == "hsoft" || $preview_app_type == "ssoft") 
			{
				if (count($cover_loop) < 1)
				{
					$cover_loop[] = array("url"=> SERVER_DOMAIN."/skin/{$cfg[skin]}/img/preview_cover.png", "page_num" => "1");
					$page_cnt++;	
				}
			}
								

			$page_loop = array();
			
			//표지 뒷면은 흰페이지. 프롤로그 페이지를 만들어야 하나.. 실제 미리보기에서 넘어오는걸 확인후 처리하자.
			if (count($cover_loop) > 0)
			{			
				//$page_cnt++;
				//$page_loop[] = array("url"=> SERVER_DOMAIN."/skin/{$cfg[skin]}/img/preview_blank.png", "page_num" => $page_cnt);
			}
			
			
			foreach ($preview as $key => $value) 
			{
				$bPageAdd = true;
				if ($value[type] == "cover_split")	$bPageAdd = false;				
				if ($value[type] == "cover" && $value[per_page] == "1") $bPageAdd = false; 
				if ($value[type] == "cover" && $value[per_page] == "2" && $bSplitCoverExist) $bPageAdd = false;			//split 커버가 존재하는 경우 cover 는 추가하지 않는다.
				
				if ($bPageAdd)
				{
					if ($value[per_page] == "1")
					{
						$preview_width = $value[width] * 2; 
						$preview_height = $value[height];
						$preview_type = "one";
					}
					else
					{
						$preview_width = $value[width]; 
						$preview_height = $value[height];
					}
					$preview_page_width = $value[width];
					$preview_thumb_width = $value[width] * 0.2; 
					$preview_thumb_height = $value[height] * 0.2;
					
					$page_cnt++;	
					
					if ($preview_type == "one")
					{
						$value[page_num] = $page_cnt;
					}
					else {
						if ($page_cnt > 2)
							$value[page_num] = $page_cnt * 2 - 2;
						else
							$value[page_num] = $page_cnt;
					}					
					
					if ($preview_type == "one")
					{
						//마지막 장은 표시 설정을 따른다.	
						if ($key + 1 == count($preview))
							$value[display_type] = $cover_display_type;
						else
							$value[display_type] = "page";
					}
					else
						$value[display_type] = "double";
					$page_loop[] = $value;					
				}
			}
			
			//split 커버의 뒷장을 찾는다.			
			//half_1.jpg 가  마지막 뒷장, half_2.jpg 가 처음 앞장 미리보기 파일로 사용하도록 변경함(요청 DNP, 김기웅)			20180103		chunter
			foreach ($preview as $key => $value) 
			{
				if ($value[type] == "cover_split") 
				{
					if (strpos($value[url], "half_1.jpg") !== false)
					{
						$page_cnt++;
						$value[page_num] = $page_cnt;
						$value[display_type] = $cover_display_type;
						$page_loop[] = $value;	
					}
				}
			}
			
						
			
			$resize_ratio = 0;
			if ($preview_app_type == "hsoft" || $preview_app_type == "ssoft")
			{
				if ($preview_type == "one")
				{
					if ($preview_width > 500 || $preview_height > 500)
					{
						$width_ratio = 500 / $preview_page_width;
						$height_ratio = 500 / $preview_height;
						
						$resize_ratio = $width_ratio;
						if ($width_ratio > $height_ratio)
							$resize_ratio = $height_ratio;
					}
				} 				
				else if ($preview_type == "double")
				{
					if ($preview_width > 1000 || $preview_height > 500)
					{
						$width_ratio = 1000 / $preview_page_width;
						$height_ratio = 500 / $preview_height;
						
						$resize_ratio = $width_ratio;
						if ($width_ratio > $height_ratio)
							$resize_ratio = $height_ratio;
					} 
					else 
					{
						$resize_ratio = 1.3;
					}
				}
			}
			
			
			if ($resize_ratio)	
			{	
				$preview_width = $preview_width * $resize_ratio;
				$preview_height = $preview_height * $resize_ratio;
				$preview_page_width = $preview_page_width * $resize_ratio;
				
				$preview_thumb_width = $preview_thumb_width * $resize_ratio;
				$preview_thumb_height = $preview_thumb_height * $resize_ratio;
			}
			//debug($width_ratio);
			//debug($height_ratio);
			
			//debug($preview_height);
			//debug($preview_width);
			//하드커버, 소프트 커버 종류 분류.
			if ($preview_app_type == "hsoft" || $preview_app_type == "ssoft") 
			{
				if ($preview_type == "one")
					$tpl -> define('tpl', "module/preview_soft_one.htm");
				else
					$tpl -> define('tpl', "module/preview_soft_double.htm");
			}
			
			//$preview_width = "735";			
			//$preview_height = 500;
      $tpl -> assign("cover_loop", $cover_loop);
			$tpl -> assign("page_loop", $page_loop);					
      $tpl -> assign("podskind", $podskind);
			
			$tpl -> assign("preview_width", $preview_width);
			$tpl -> assign("preview_height", $preview_height);
			$tpl -> assign("preview_page_width", $preview_page_width);
			$tpl -> assign("preview_thumb_width", $preview_thumb_width);
			$tpl -> assign("preview_thumb_height", $preview_thumb_height);
			$tpl -> assign("page_cnt", $page_cnt);
			$tpl -> assign("cover_display_type", $cover_display_type);
			
			
			$tpl -> assign("css_view_margin_top", 100);
			
      
      $tpl -> print_('tpl',"");
}

?>