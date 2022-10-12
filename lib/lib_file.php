<?
//폴더채 파일 복사
function fileCopy($odir, $ndir) {
   if (filetype($odir) === 'dir') {
      clearstatcache();

      if ($fp = @opendir($odir)) {
         while (false !== ($ftmp = readdir($fp))) {
            if (($ftmp !== ".") && ($ftmp !== "..") && ($ftmp !== "")) {
               if (filetype($odir . '/' . $ftmp) === 'dir') {
                  clearstatcache();

                  @mkdir($ndir . '/' . $ftmp);
                  echo($ndir . '/' . $ftmp . "<br />\n");
                  set_time_limit(0);
                  fileCopy($odir . '/' . $ftmp, $ndir . '/' . $ftmp);
               } else {
                  copy($odir . '/' . $ftmp, $ndir . '/' . $ftmp);
               }
            }
         }
      }
      if (is_resource($fp)) {
         closedir($fp);
      }
   } else {
      echo $ndir . "<br />\n";
      copy($odir, $ndir);
   }
}// end func

function getFileListULTag($centerid, $mallid, $orderid) {
   $finfo_tag = "";
   $filesinfo = $this -> resultFileList($centerid, $mallid, $orderid);
   if ($filesinfo && "fail" != substr($filesinfo, 0, 4)) {
      //$furls = split("\|", $filesinfo);
      $furls = explode("|", $filesinfo);
      foreach ($furls as $furl) {
         if ($furl) {
            $lasts = strrpos($furl, "/");
            $finfo_tag .= "<li><a href='" . $furl . "' target='_blank'>" . substr($furl, $lasts + 1, strlen($furl) - $lasts) . "</a></li>";
         }
      }
      if ($finfo_tag)
         $finfo_tag = "<ul class='file_list_ul'>" . $finfo_tag . "</ul>";
   }
   return $finfo_tag;
}

function resultFileList($centerid, $mallid, $orderid) {
   global $est_config;
   try {
      $m_order = new m_estimate_order();
      $orderinfo = $m_order -> get_info($orderid);
      if (!$orderinfo)
         return "fail|" . _("주문정보가 없습니다.");

      if ($centerid != $orderinfo['center_ID'] || $mallid != $orderinfo['mall_ID']) {
         return "fail|" . _("주문정보의 사이트 정보가 부정확합니다.");
      }

      if ($orderinfo['order_type'] != 'UPLOAD') {
         return "fail|" . _("수동 파일 업로드를 통한 주문이 아닙니다.");
      }

      $spcode = $orderinfo['storage_code'];
      $product_ID = $orderinfo['product_ID'];
      if (!$product_ID)
         return "fail|" . _("주문정보에 상품코드가 없습니다.");

      $m_product = new m_estimate_product($db_e);
      $product = $m_product -> get_info($product_ID);
      if (!$product)
         return "fail|" . _("상품정보가 없습니다.");

      $url_file_list = $product['url_file_list'];

      if (!$url_file_list) {
         //자동견적 자체 스토리지에서 얻기
         $subpath = getSubPath($centerid, $mallid, $spcode);
         $http_path = getFullUrlPath($subpath);
         if ($subpath) {
            $default_path = getLocalPath($subpath);

            if (is_dir($default_path)) {
               $files = directory_map($default_path);
               $info = "";
               if (!empty($files))
                  foreach ($files as $fname) {
                     if (strtolower($fname) != "thumbs.db") {
                        if ($est_config['charset'] != $est_config['server_charset'])
                           $info .= $http_path . iconv($est_config['server_charset'], $est_config['charset'], $fname) . "|";
                        else
                           $info .= $http_path . $fname . "|";
                     }
                  }
               return $info;
            } else
               return "fail|" . _("폴더가 없습니다.") . "($subpath)";
         } else
            return "fail|" . _("폴더정보가 없습니다.") . "($subpath)";
      } else {
         //web 요청으로 얻기
         $postargs = array();
         $postargs['storage_code'] = $spcode;
         $postargs['center_id'] = $centerid;
         $postargs['mall_id'] = $mallid;

         $rst_send = sendPostData($url_file_list, $postargs);

         return $rst_send;
      }
   } catch(Exception $ex) {
      return "fail|" . _("파일 정보를 얻을 수 없습니다.") . "(" . $ex -> getMessage() . ")";
   }
}

/* 파일 수동 업로드 */
function saveUploadFile($centerid, $mallid, $spcode = null) {
   global $est_config;
   if (!$spcode)
      return null;

   $path_root = $est_config["storage"];
   $path_sub = "/" . $centerid . "/" . $mallid . "/" . substr($spcode, 0, 6) . "/" . substr($spcode, 0, 8) . "/" . $spcode . "/";
   $path = $path_root . $path_sub;

   $config['upload_path'] = getLocalPath(str_replace("/", "\\", $path_sub));
   $config['sess_match_useragent'] = FALSE;
   $config['allowed_types'] = '*';
   $config['remove_spaces'] = true;
   $config['overwrite'] = false;
   //echo $config['upload_path'];
   $arry_types = $est_config["C_ALLOWED_TYPES"];

   $config['max_size'] = '61440';
   //kb (60MB = 61440 kb ) , //php.ini upload_max_filesize 수정 필요
   // 		$config['max_width']  = '1024';
   // 		$config['max_height']  = '768';

   $CI -> load -> library('upload', $config);

   $fieldFix = "files_";
   $err_list = array();

   if (!is_dir($config['upload_path'])) {
      mkdir($config['upload_path'], 0777, true);
   }

   for ($i = 0; $i < 10; $i++) {
      $fieldName = $fieldFix . $i;
      if (!empty($_FILES[$fieldName]['tmp_name'])) {
         //$fName = iconv($CI->config->item("server_charset"),$CI->config->item("charset"),$_FILES[$fieldName]['name']);
         $fName = $_FILES[$fieldName]['name'];
         $ext = pathinfo($fName, PATHINFO_EXTENSION);

         if (in_array(strtolower($ext), $arry_types)) {
            $CI -> upload -> initialize($config);
            if (!$CI -> upload -> do_upload($fieldName)) {
               array_push($err_list, $fName . "(" . $CI -> upload -> display_errors('', '') . ")");
            }
         } else
            array_push($err_list, $fName . "(" . _("업로드 가능한 파일 종류가 아닙니다.") . ")");
      }
   }

   if (!empty($err_list))
      return array("result" => false, "spcode" => $spcode, "path" => $path, "msg" => implode(",", $err_list));
   else
      return array("result" => true, "spcode" => $spcode, "path" => $path);
}

function getPathSplit($path) {
   if (!$path)
      return null;
   $idx = strpos($path, "/", 1);
   if (!$idx || $idx < 1)
      return null;

   //root : /storage ,subpath : /aaa/bbb
   return array("root" => substr($path, 0, $idx), "subpath" => substr($path, $idx));
}

function getLocalPath($subpath) {
   //str_replace("/","\\" , $subpath) ;
   global $est_config;
   return $est_config['storage_path'] . $subpath;
}

function getFullUrlPath($subpath) {
   global $est_config;
   return "http://" . $_SERVER['HTTP_HOST'] . $est_config['storage'] . $subpath;
}

function getSubPath($centerid, $mallid, $spcode) {
   return "/" . $centerid . "/" . $mallid . "/" . substr($spcode, 0, 6) . "/" . substr($spcode, 0, 8) . "/" . $spcode . "/";
}
?>