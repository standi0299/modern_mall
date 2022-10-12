<?
header("Pragma: no-cache");
header("Cache: no-cache");
header("Cache-Control: no-cache, must-revalidate");
header("Expires:Mon, 26 Jul 1997 05:00:00 GMT");

$login_offset = true;
//$style_not_use = true;
include_once "../_header.php";
include_once "../lib/nusoap/lib/nusoap.php";
include_once "../lib/func.xml.php";

//list($podskind, $goodsnm) = $db -> fetch("select podskind, goodsnm from exm_goods where goodsno = '$_REQUEST[goodsno]'", 1);
list($podskind, $goodsnm, $pods_use, $preview_app_type) = $db -> fetch("select podskind, goodsnm, pods_use, preview_app_type from exm_goods where goodsno = '$_REQUEST[goodsno]'", 1);
list($previewLink) = $db -> fetch("select preview_link from tb_editor_ext_data where storage_id = '$_REQUEST[storageid]'", 1);
$editdate = time(); //20150630 / minks / wpod 편집상품의 경우 캐시때문에 미리보기 갱신이 안되서 랜덤값대신 추가

/*if ($_GET[mobile_type] != "Y" && $cfg[skin_theme] == "P1") {
	include "./preview_P1.php";
	exit;
}*/

if ($previewLink) {
   $previewLink = str_replace("|", "?editdate=".$editdate."|", $previewLink);
   $loop = explode("|", $previewLink);
   $loop = array_notnull($loop);

   $tpl -> assign("loop", $loop);
   $tpl -> assign("podskind", $podskind);
   $tpl -> print_('tpl');
} else {
   if (in_array($podskind, array(1, 3010, 3011, 3020))) {
      $photo = true;
   }

   if ($photo) {
      //20151110 / minks / 4.0인화 편집기 추가
      if (in_array($podskind, $r_podskind20)) $client = new soapclient("http://" .PODS20_DOMAIN. "/CommonRef/StationWebService/StationWebService.asmx?WSDL", true);
      else $client = new soapclient("http://" .PODS10_DOMAIN. "/StationWebService/StationWebService.asmx?WSDL", true);
      $ret = $client -> call('GetPrintCountResult', array('storageid' => $_GET[storageid]));
      $xxx = explode("|", $ret[GetPrintCountResultResult]);
      if ($xxx[2]) {
         unset($ret);
         $ret = readurl($xxx[2]);
         $info = xml2array2($ret);

         if ($info['print'][image][filename]) {
            $loop[] = $info['print'][image];
         } else {
            $loop = $info['print'][image];
         }
         unset($info['print'][image]);
      }

      if ($loop && $_GET[mobile_type] == "Y") {
	  	  foreach ($loop as $img_k=>$img_v) {
            if ($img_v[thumbnail]) {
               for ($i=0;$i<$img_v[copies][copy_attr][count];$i++) {
                  //20160308 / minks / 현재 모바일 인화 미리보기 클릭시 썸네일 이미지를 출력하고 있어서 미리보기 이미지로 교체
                  $img_url[$i] = strpos($img_v[thumbnail], "/images/thumbnail");
		 			   $img_url[$i] = substr($img_v[thumbnail], 0, $img_url[$i]);
                  //20160930 / minks / 미리보기 이미지 없을 경우 썸네일 이미지 출력
                  $loop[$img_k][preview][$i] = ($img_v[copies][copy][preview]) ? $img_url[$i]."/document/new/".$img_v[copies][copy][preview] : $img_v[thumbnail];
               }
			   }
         }
      }
      if($cfg[skin_theme] == "M2" && $_GET[mobile_type] != "Y") {
         foreach($loop as $key => $val){
            //debug($val[copies][copy_attr][type]);
            if($val[copies]['copy'][title]){
               $print_amount[$val[copies]['copy'][title]][cnt] += $val[copies][copy_attr][count];
               $info[tot_cnt] += $val[copies][copy_attr][count];
            }
			//20190311 / 미리보기 이미지 개선
         // $loop[$img_k][preview][$i] = ($img_v[copies][copy][preview]) ? $img_url[$i]."/document/new/".$img_v[copies][copy][preview] : $img_v[thumbnail];

         $img_url_v = strpos($val[thumbnail], "/images/thumbnail");
         $img_url_v = substr($val[thumbnail], 0, $img_url_v);
         $loop[$key][preview] = ($val[copies][copy][preview]) ? $img_url_v."/document/new/".$val[copies][copy][preview] : $val[thumbnail];

         }
      }
      //debug($aa);
      //debug($info);
      // debug($loop);

      $tpl -> define('tpl', "module/preview.print.htm");
      $tpl -> assign("info", $info);
      $tpl -> assign("loop", $loop);
      if($cfg[skin_theme] == "M2")
         $tpl -> assign("print_amount", $print_amount);
      $tpl -> print_('tpl');

   } else {
		//pod20 상품에 대해서는 미리보기 변경이 가능			20171023		chunter
		if (($preview_app_type == "hsoft" || $preview_app_type == "ssoft") && ($pods_use == "2" || $pods_use == "3") && ($cfg[skin] == "modern"))
		{
			include_once "./preview_new.php";
			exit;
		}

      $storageids_temp = explode('|', $_REQUEST[storageids]);
      if (is_array($storageids_temp)) {
         //역순으로 파라미터가 넘어와서 역순 정렬을 위해
         for ($i = count($storageids_temp) - 1; $i >= 0; $i--) {
            $storageids[] = $storageids_temp[$i];
         }
         $storageids_index = 0;
         foreach ($storageids as $key => $value) {
            if ($_REQUEST[storageid] == $value) {
               if ($key > 0)
                  $storageid_prev = $storageids[$key - 1];
               if ($key < count($storageids) - 1)
                  $storageid_next = $storageids[$key + 1];

               $storageids_index = $key + 1;
            }
         }
         $tpl -> assign("storageid_prev", $storageid_prev);
         $tpl -> assign("storageid_next", $storageid_next);

         $tpl -> assign("storageids_index", $storageids_index);
         $tpl -> assign("storageids_cnt", count($storageids));
      }

      if (in_array($podskind, $r_podskind20)) {/* 2.0 상품 */
        $podVersion = '20';
      } else {
        $podVersion = '10';
      }
      $clsPods = new PODStation($podVersion);
      $loop = $clsPods->GetPreViewImg($_REQUEST[storageid]);

      //setSkinTemplateDefine();      //스킨별, 몰별, 페이지별 설정 파일   20150908
      if (!$loop && $_GET[mobile_type] != "Y") {
	     msg(_("미리보기 지원되지 않는 편집입니다."), "close");
      }

      $preview_link = "http://".$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI']."?storageid=".$_REQUEST[storageid]."&goodsno=".$_REQUEST[goodsno];

			//slide 형태의 미리보기가 개발은 되었으나 적용되지 못하고 있음. pretty 스킨에만 개발적용.			20170729 확인
			//$tpl -> define('tpl', "module/preview_slide.htm");

      $tpl -> assign("loop", $loop);
      $tpl -> assign("podskind", $podskind);
      $tpl -> assign("preview_link", $preview_link);
      $tpl -> print_('tpl',"");
   }
}
?>
