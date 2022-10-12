<?
include "../lib.php";

$window_close = false;
/***/
switch ($_POST[mode]) {/***/

    case "insert_dp_template" :
/*
 * 메인 페이지 템플릿 추천 기능 (템플릿 전시)
 * skin : classic, spring , pod_group
 * 2016.07.28 by kdk
 * */
		$sql = "select max(seq) as seq from tb_template_dp_link where cid='$cid' and dpno='$_POST[dpcode]';";
		//debug($sql);
		list($seq) = $db->fetch($sql,1);

		$sql = "select * from tb_template_dp_link 
			where cid='$cid' 
			and dpno='$_POST[dpcode]' 
			and goodsno='$_POST[goodsno]' 
			and templatename='$_POST[templatename]'
			and img='$_POST[img]'
		";
		//debug($sql);		
		$data = $db->fetch($sql);
		//debug($data);
		if(!$data) {
			$seq++;
			$query = "insert into tb_template_dp_link set
				cid = '$cid',
				dpno = '$_POST[dpcode]',
				goodsno = '$_POST[goodsno]',
				templatename = '$_POST[templatename]',
				img = '$_POST[img]',
				url = '$_POST[url]',
				seq = '$seq';";
			
			//debug($query);
			$db->query($query);
			//$return_data = $db->id;
			//debug($return_data);
			//if ($return_data)
			//	return $return_data;
			//else 
			//	return "-1";
		}
		
		msg(_("저장되었습니다."),"close");
        break;
		
    case "insert_dp_template_ajax" :
/*
 * 메인 페이지 템플릿 추천 기능 (템플릿 전시)
 * skin : classic, spring , pod_group
 * 2016.05.12 by kdk
 * */
      $sql = "select max(seq) as seq from tb_template_dp_link where cid='$cid' and dpno='$_POST[dpcode]';";
      //debug($sql);
      list($seq) = $db->fetch($sql,1);

      $sql = "select * from tb_template_dp_link 
         where cid='$cid' 
         and dpno='$_POST[dpcode]' 
         and goodsno='$_POST[goodsno]' 
         and templatename='$_POST[templateName]'
         and img='$_POST[img]'
      ";
      //debug($sql);    
      $data = $db->fetch($sql);
      //debug($data);
      if(!$data) {
         $seq++;
         $query = "insert into tb_template_dp_link set
            cid = '$cid',
            dpno = '$_POST[dpcode]',
            goodsno = '$_POST[goodsno]',
            templatename = '$_POST[templateName]',
            img = '$_POST[img]',
            url = '$_POST[url]',
            seq = '$seq';";
         
         //debug($query);
         $db->query($query);
         //$return_data = $db->id;
         //debug($return_data);
         //if ($return_data)
         // return $return_data;
         //else 
         // return "-1";
      }
      
      echo "OK";

      exit;
        break;

    case "set_dp_template" :
/*
 * 메인 페이지 템플릿 추천 기능 (템플릿 전시)
 * skin : classic, spring , pod_group
 * 2016.05.12 by kdk
 * */
        //2013.12.06 / minks / exm_goods_dp 테이블에 goodsnm_cut_flag 레코드를 추가
        $query = "insert into exm_goods_dp set cid = '$cid', dpno = '$_POST[dpno]', cells = '$_POST[cells]', `rows` = '$_POST[rows]', listimg_w = '$_POST[listimg_w]', goodsnm_cut_flag = '$_POST[goodsnm_cut_flag]' on duplicate key update cells = '$_POST[cells]', `rows` = '$_POST[rows]', listimg_w = '$_POST[listimg_w]', goodsnm_cut_flag = '$_POST[goodsnm_cut_flag]'";
        $db-> query($query);

        //전체 초기화
        $db->query("delete from tb_template_dp_link where cid = '$cid' and dpno = '$_POST[dpno]'");
        
        if ($_POST[goodsno]) {            

         $idx = 1;
         foreach ($_POST[templatename] as $k => $v) {
            
            $goodsno = $_POST[goodsno][$k];
            $templatename = $_POST[templatename][$k];
            $img = $_POST[img][$k];
            $url = $_POST[url][$k];
            
            //debug($goodsno .":". $templatename .":". $img);
         
            //$query = "update tb_template_dp_link set seq = '$idx' where cid='$cid' and dpno='$_POST[dpno]' and goodsno='$goodsno' and templatename='$templatename' and img='$img';";
            
            $query = "insert into tb_template_dp_link set
               cid = '$cid',
               dpno = '$_POST[dpno]',
               goodsno = '$goodsno',
               templatename = '$templatename',
               img = '$img',
               url = '$url',
               seq = '$idx';";
            
            //debug($query);
            $db->query($query);
            $idx++;
         }
        }

		popupMsgNLocation(_("저장되었습니다."));
        break;

    case "getBrand" :
        $query = "select * from exm_brand order by brandnm";
        $res = $db -> query($query);
        $loop = array();
        while ($data = $db -> fetch($res)) {
            $data[view_rid] = explode("|", $data[view_rid]);
            if (in_array($_POST[rid], $data[view_rid]) || !$data[view_rid][0]) {
                $loop[] = "$data[brandno]|$data[brandnm]($data[brandno])";
            }
        }
        echo "['" . implode("','", $loop) . "']";

        exit ;
        break;

    case "search_goods" :
        include "../../lib/class.page.php";

        $db_table = "exm_goods a inner join exm_goods_cid b on a.goodsno = b.goodsno and b.cid = '$cid'";
        $where[] = "b.cid = '$cid'";
        if ($_POST[goodsno]) {
            $where[] = "a.goodsno = '$_POST[goodsno]'";
        }
        if ($_POST[sword]) {
            $where[] = "concat(goodsnm) like '%$_POST[sword]%'";
        }
        if (is_numeric($_POST[catno])) {
            $db_table .= " left join exm_goods_link c on a.goodsno=c.goodsno";
            $where[] = "c.catno like '$_POST[catno]%'";
            $where[] = "c.cid = '$cid'";
        }
        if ($_POST[brandno]) {
            $where[] = "brandno = '$_POST[brandno]'";
        }
        if ($_POST[runout]) {
            $where[] = "state = 0";
            $where[] = "(usestock=0 or (usestock=1 and totstock > 0))";
        }
        if ($_POST[nodp]) {
            $where[] = "nodp = 0";
        }

        if ($_POST[price1] && $_POST[price2]) {
            $where[count($where) - 1] .= " having(cid_price >= '$_POST[price1]' and cid_price <= '$_POST[price2]')";
        } else if ($_POST[price1]) {
            $where[count($where) - 1] .= " having(cid_price >= '$_POST[price1]')";
        } else if ($_POST[price2]) {
            $where[count($where) - 1] .= " having(cid_price <= '$_POST[price2]')";
        }

        ### 상품 데이터
        $pg = new Page($_POST[page], 20);
        $pg -> field = "
      *,
      if(b.price is null,a.price,b.price) cid_price,
      if(b.price is null,convert(cprice - a.price,signed),convert(cprice - b.price,signed)) dc,
      if(
         b.price is null,
         round(convert(cprice - a.price,signed)/cprice*100),
         round(convert(cprice - b.price,signed)/cprice*100)
      ) dcper
        ";
        $pg -> setQuery($db_table, $where, $_POST[orderby]);
        $pg -> exec();

        $res = &$pg -> resource;

        while ($data = $db -> fetch($res)) {

            echo "<li><table border='1' class='goodsTb'><tr><th>" . goodsListImg($data[goodsno], 50) . "</th><td>";
            echo "<div>"._("상품번호")." : <b class='eng'>" . $data[goodsno] . "</b></div>";
            echo "<div>" . $data[goodsnm] . "</div>";
            echo "<div class='eng'>";
            if ($data[cprice] && $data[cprice] > $data[cid_price]) {
                echo "(<b class='red'>" . $data[dcper] . "%</b>) ";
                echo "<strike class='eng'>" . number_format($data[cprice]) . "</strike> → ";
            }
            echo "<b>" . number_format($data[cid_price]) . "</b>";
            echo "</div>";
            echo "</td></tr></table><input type='hidden' name='goodsno[]' value='" . $data[goodsno] . "'/></li>";
        }

        exit ;
        break;

   case "set_dp" :

      //2013.12.06 / minks / exm_goods_dp 테이블에 goodsnm_cut_flag 레코드를 추가
      $query = "insert into exm_goods_dp set
                  cid              = '$cid',
                  dpno             = '$_POST[dpno]',
                  cells            = '$_POST[cells]',
                  `rows`           = '$_POST[rows]',
                  listimg_w        = '$_POST[listimg_w]',
                  goodsnm_cut_flag = '$_POST[goodsnm_cut_flag]'
                on duplicate key update
                  cells            = '$_POST[cells]',
                  `rows`           = '$_POST[rows]',
                  listimg_w        = '$_POST[listimg_w]',
                  goodsnm_cut_flag = '$_POST[goodsnm_cut_flag]'";
      $db -> query($query);

      ### 기존정보가져오기
      $query = "select * from exm_dp_link where cid = '$cid' and dpno = '$_POST[dpno]'";
      $res = $db -> query($query);
      while ($data = $db -> fetch($res)) {
         $r_goodsno[] = $data[goodsno];
      }

      $del_goodsno = array();
      if (is_array($_POST[goodsno])) {
         if (is_array($r_goodsno))
            $del_goodsno = array_diff($r_goodsno, $_POST[goodsno]);
      } else {
         $del_goodsno = $r_goodsno;
      }

      foreach ($del_goodsno as $goodsno) {
         $db -> query("delete from exm_dp_link where cid = '$cid' and dpno = '$_POST[dpno]' and goodsno = '$goodsno'");
      }
      
      if ($_POST[goodsno])
         foreach ($_POST[goodsno] as $goodsno) {
            $idx++;
            $db -> query("insert into exm_dp_link set cid = '$cid', dpno ='$_POST[dpno]', goodsno = '$goodsno', seq = '$idx' on duplicate key update seq = '$idx'");
         }
   break;

    //printhome 매인 배경 이미지 변경처리.   20140710    chunter
    case "set_banner_backgroud" :
        $query = "insert into exm_config set
                    cid   = '$cid',
                    config  = 'main_backgroud',
                    value = '$_REQUEST[main_backgroud]'
                    on duplicate key 
                    update value = '$_REQUEST[main_backgroud]'";
        $db -> query($query);

        //배경 색생 테마
        $query = "insert into exm_config set
                    cid   = '$cid',
                    config  = 'main_color_theme',
                    value = '$_REQUEST[main_color_theme]'
                    on duplicate key 
                    update value = '$_REQUEST[main_color_theme]'";
        $db -> query($query);

        $_POST[code] = 'main_top_backgroud';
        
        $window_close = true;
    //break;

   case "set_banner" :
      $imgfile = $diffimg = array();

     $dir = "../../data/banner/";
     if (!is_dir($dir)) {
        mkdir($dir, 0707);
        chmod($dir, 0707);
     }

     $dir = "../../data/banner/$cid/";
     if (!is_dir($dir)) {
        mkdir($dir, 0707);
        chmod($dir, 0707);
     }     
     
     $dir = "../../data/banner/$cid/$_POST[code]/";
     if (!is_dir($dir)) {
        mkdir($dir, 0707);
        chmod($dir, 0707);
     }

      $img = ($_POST[image]) ? $_POST[image] : array();
      $img_on = ($_POST[image_on]) ? $_POST[image_on] : array();
      if (is_array($_FILES[img][name]))
         foreach ($_FILES[img][name] as $k => $v) {
            if (!is_uploaded_file($_FILES[img][tmp_name][$k]))
               continue;

            $name = getImageSize($_FILES[img][tmp_name][$k]);

            switch ($name[2]) {
               case "1" :
                  $ext = "gif";
                  break;
               case "2" :
                  $ext = "jpg";
                  break;
               case "3" :
                  $ext = "png";
                  break;
               case "4" :
                  $ext = "swf";
                  break;
               case "13" :
                  $ext = "swf";
                  break;
               default :
                  msg(_("gif,jpg,png,swf 형식의 파일만 업로드가 가능합니다."), -1);
                  break;
            }

            $name = time() . $k . "." . $ext;             
             
            //set_banner_backgroud 배너 이미지 등록시 이미지 리사이징 / 14.07.25 / kjm
            if($_POST[mode] == "set_banner_backgroud"){
               //이미지 350 * 350 리사이징 / 14.07.23 / kjm
               $src = $_FILES[img][tmp_name][$k];
               $folder = $dir . $name;

                 $size = @getimagesize($src);
                 switch ($size[2]) {
                     case 1 :
                         $image = @ImageCreatefromGif($src);
                         break;
                     case 2 :
                         $image = ImageCreatefromJpeg($src);
                         break;
                     case 3 :
                         $image = ImageCreatefromPng($src);
                         break;
                     default :
                         return;
                 }
                 
                 $width = 400;
                 $height = $size[1] / $size[0] * $width;
                 if ($width == $size[0]) {
                     copy($src, $folder);
                     //return;
                 } else {
                   $dst = ImageCreateTruecolor($width, $height);
 
                   //이미지 사이즈만 조절 전체 배경사이즈는 안바뀜
                   Imagecopyresampled($dst, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);

                   ImageJpeg($dst, $folder, 100);
                   ImageDestroy($dst);
                 }
                 ImageDestroy($image);
             }

             else {
               move_uploaded_file($_FILES[img][tmp_name][$k], $dir . $name);
             }
             $img[$k] = $name;
         }

     if ($_POST[spc] == "map") {
         $img = array($img[0]);
         $_POST[url] = array();
         $_POST[active_url] = array();
         $_POST[target] = array();
      
      ### form 전송 취약점 개선 20160706 by kdk
      $_POST[map] = addslashes(urldecode(base64_decode($_POST[map])));
      
         $_POST[spc_desc] = $_POST[map];
     } else {
         $_POST[spc_desc] = @implode("||", $_POST[spc_desc]);
     }

     if (is_array($img))
         foreach ($img as $k => $v) {
             if (is_uploaded_file($_FILES[img_on][tmp_name][$k])) {
                 $name = getImageSize($_FILES[img_on][tmp_name][$k]);
                 switch ($name[2]) {
                     case "1" :
                         $ext = "gif";
                         break;
                     case "2" :
                         $ext = "jpg";
                         break;
                     case "3" :
                         $ext = "png";
                         break;
                     default :
                         msg(_("오버이미지는 gif,jpg,png 형식의 파일만 업로드가 가능합니다."), -1);
                         break;
                 }

                 $name = time() . $k . "_on." . $ext;

                 move_uploaded_file($_FILES[img_on][tmp_name][$k], $dir . $name);

                 $img_on[$k] = $name;
             }
         }

     $b_img = ls($dir);
     $del_img = array_diff($b_img, array_merge($img, $img_on));

     if (is_array($del_img))
         foreach ($del_img as $v) {
             @unlink($dir . $v);
     }

     $img = @implode("||", $img);
     $img_on = @implode("||", $img_on);
     $_POST[url] = @implode("||", $_POST[url]);
     $_POST[active_url] = @implode("||", $_POST[active_url]);
     $_POST[target] = @implode("||", $_POST[target]);
     
     $file_path = "";

     if ($_POST[use_flag])
       $use_flag = @implode("||", $_POST[use_flag]);
     
     $query = "
   insert into exm_banner set
      cid         = '$cid',
      skin     = '$cfg[skin]',
      code     = '$_POST[code]',
      img         = '$img',
      img_on      = '$img_on',
      url         = '$_POST[url]',
      active_url     = '$_POST[active_url]',          
      target      = '$_POST[target]',
      comment     = '$_POST[comment]',
      spc         = '$_POST[spc]',
      spc_desc = '$_POST[spc_desc]',
      slide_speed = '$_POST[slide_speed]',
      file_path   = '$file_path',
      use_flag   = '$use_flag'
   on duplicate key update
      img         = '$img',
      img_on      = '$img_on',
      url         = '$_POST[url]',
      active_url     = '$_POST[active_url]',          
      target      = '$_POST[target]', 
      comment     = '$_POST[comment]',
      spc         = '$_POST[spc]',
      spc_desc = '$_POST[spc_desc]',
      slide_speed = '$_POST[slide_speed]',
      file_path   = '$file_path',
      use_flag   = '$use_flag'
   ";

     $db -> query($query);

     $window_close = true;
     break;
    /***/

   case "set_banner_editer" :
      
      ### form 전송 취약점 개선 20160706 by kdk
      $_POST[edit_spc_desc] = addslashes(urldecode(base64_decode($_POST[edit_spc_desc])));


     $query = "
   insert into exm_banner set
      cid         = '$cid',
      skin     = '$cfg[skin]',
      code     = '$_POST[code]',
      spc         = '$_POST[spc]',
      spc_desc = '$_POST[edit_spc_desc]'
   on duplicate key update
	  spc         = '$_POST[spc]',
      spc_desc = '$_POST[edit_spc_desc]'
   ";

     $db -> query($query);

     $window_close = true;
     break;
    /***/    
    
    
        
    //사용자가 배경 이미지 등록할 수 있도록. 2015.02.24 by kdk
    case "upload_main_backgroud" :
          //저장폴더 구조별 폴더 생성해야 한다   20140623  chunter
          $dir = "../../data/main_backgroud/";
          if (!is_dir($dir)) {
              mkdir($dir, 0707);
              chmod($dir, 0707);
          }
          $dir = "../../data/main_backgroud/$cid/";
          if (!is_dir($dir)) {
              mkdir($dir, 0707);
              chmod($dir, 0707);
          }
  
        if (is_dir($dir)) {
         //chmod($dir, 0707);

         if (is_uploaded_file($_FILES[bgImg][tmp_name])) {
            $name = $_FILES[bgImg][name];
            move_uploaded_file($_FILES[bgImg][tmp_name],$dir.$name);
         }     
        }

        break;

}/***/
if (!$_POST[rurl])
    $_POST[rurl] = $_SERVER[HTTP_REFERER];


if ($window_close)
  popupMsgNLocation(_("저장되었습니다."));
else    
  go($_POST[rurl]);




?>