<?
include "../lib.php";
include "../../conf/conf.db.php";

$m_goods = new M_goods();

switch ($_GET[mode]) {

   case "listimg" :
      
      list($img, $listimg) = $db -> fetch("select img, listimg from exm_goods where goodsno = '$_GET[goodsno]'", 1);

      readurl("http://$cfg_center[host]/_sync/initimg.php?mode=listimg&cid=$cid&goodsno=$_GET[goodsno]&listimg_w=$cfg[listimg_w]");
      $db -> query("update exm_goods_cid set clistimg = '$listimg' where cid = '$cid' and goodsno = '$_GET[goodsno]'");

      break;

   case "img" :
      
      list($img, $listimg) = $db -> fetch("select img, listimg from exm_goods where goodsno = '$_GET[goodsno]'", 1);

      readurl("http://$cfg_center[host]/_sync/initimg.php?mode=img&cid=$cid&goodsno=$_GET[goodsno]&img_w=$cfg[img_w]");
      $db -> query("update exm_goods_cid set cimg = '$img' where cid = '$cid' and goodsno = '$_GET[goodsno]'");

      break;

   case "delselfGoods" :
      if (!$_POST[chk])
         $_POST[chk][0] = $_GET[goodsno];

      for ($i = 0; $i < sizeof($_POST[chk]); $i++) {
         ### 이미지 삭제
         readurl("http://$cfg_center[host]/_sync/img_r.php?mode=del&cid=$cid&goodsno={$_POST[chk][$i]}");

         $db -> query("delete from exm_goods_cid where goodsno = '{$_POST[chk][$i]}'");
         $db -> query("delete from exm_goods_link where goodsno = '{$_POST[chk][$i]}'");
         $db -> query("delete from exm_goods where goodsno = '{$_POST[chk][$i]}'");
         $m_goods -> delAddtionGoodsItem($cid, "I", $_POST[chk][$i]);
      }
      set_category_link($_POST[chk]);
      break;

   case "delGoods" :
      if (!$_POST[chk])
         $_POST[chk][0] = $_GET[goodsno];

      for ($i = 0; $i < sizeof($_POST[chk]); $i++) {
         ### 이미지 삭제
         readurl("http://$cfg_center[host]/_sync/img.php?mode=del&cid=$cid&goodsno={$_POST[chk][$i]}");

         $db -> query("delete from exm_goods_cid where cid = '$cid' and goodsno = '{$_POST[chk][$i]}'");
         $db -> query("delete from exm_goods_link where cid = '$cid' and goodsno = '{$_POST[chk][$i]}'");
         $m_goods -> delAddtionGoodsItem($cid, "I", $_POST[chk][$i]);

         //속도 저하 문제로 저장된 옵션 데이타를 json 삭제 처리한다. 2014.10.08 by kdk
         $file_name = "../../data/goods_option/{$_POST[chk][$i]}_option.json";
         if (file_exists($file_name)) {
            unlink($file_name);
         }
      }

      set_category_link($_POST[chk]);
      break;

   ### 상품복사
   case "copyGoods" :

      /* 상품데이터복사*/
      $data = $db -> fetch("select * from exm_goods where goodsno = '$_GET[goodsno]'");
      $data = array_map("addslashes", $data);
      unset($data[goodsno]);
      unset($data[regdt]);
      unset($data[hit]);
      unset($data[shop_id]);
      unset($data[shop_code]);

      $goodsGroupCode = $data[goods_group_code];

      foreach ($data as $k => $v) {
         $flds[] = "`$k` = '$v'";
      }
      $flds = implode(",\n", $flds);

      $query = "
      insert into exm_goods set
         $flds,
         regdt = now()
      ";

      $db -> query($query);
      $goodsno = $db -> id;
      $db -> query("update exm_goods set shop_code = goodsno where goodsno = '$goodsno'");

      /* 분류정보복사 */
      $query = "select * from exm_goods_link where cid = '$cid' and goodsno = '$_GET[goodsno]'";
      $res = $db -> query($query);
      while ($data = $db -> fetch($res)) {
         unset($flds);

         $data[goodsno] = $goodsno;

         foreach ($data as $k => $v) {
            $flds[] = "`$k` = '$v'";
         }
         $flds = implode(",\n", $flds);
         $query = "
         insert into exm_goods_link set
            $flds
         ";
         $db -> query($query);
      }

      /* 옵션정보복사 */
      $query = "select * from exm_goods_opt where goodsno = '$_GET[goodsno]'";
      $res = $db -> query($query);
      while ($data = $db -> fetch($res)) {
         unset($flds);
         unset($data[optno]);

         $data[goodsno] = $goodsno;

         foreach ($data as $k => $v) {
            $flds[] = "`$k` = '$v'";
         }
         $flds = implode(",\n", $flds);
         $query = "
         insert into exm_goods_opt set
            $flds
         ";
         $db -> query($query);
      }

      /* 추가옵션정보복사 */
      $query = "select * from exm_goods_addopt_bundle where goodsno = '$_GET[goodsno]'";
      $res = $db -> query($query);
      while ($data = $db -> fetch($res)) {
         unset($flds);
         $r_addopt_bundle_no = $data[addopt_bundle_no];
         unset($data[addopt_bundle_no]);

         $data[goodsno] = $goodsno;

         foreach ($data as $k => $v) {
            $flds[] = "`$k` = '$v'";
         }
         $flds = implode(",\n", $flds);
         $query = "
         insert into exm_goods_addopt_bundle set
            $flds
         ";
         $db -> query($query);
         $addopt_bundle_no = $db -> id;
         $query = "select * from exm_goods_addopt where goodsno = '$_GET[goodsno]' and `addopt_bundle_no` = '$r_addopt_bundle_no'";
         $res2 = $db -> query($query);
         while ($tmp = $db -> fetch($res2)) {
            unset($flds);
            unset($tmp[addoptno]);
            $tmp[goodsno] = $goodsno;
            $tmp[addopt_bundle_no] = $addopt_bundle_no;
            foreach ($tmp as $k => $v) {
               $flds[] = "`$k` = '$v'";
            }
            $flds = implode(",\n", $flds);
            $query = "
            insert into exm_goods_addopt set
               $flds
            ";
            $db -> query($query);
         }
      }

      /* 인화옵션정보복사 */
      $query = "select * from exm_goods_printopt where goodsno = '$_GET[goodsno]'";
      $res = $db -> query($query);
      while ($data = $db -> fetch($res)) {
         unset($flds);

         $data[goodsno] = $goodsno;

         foreach ($data as $k => $v) {
            $flds[] = "`$k` = '$v'";
         }
         $flds = implode(",\n", $flds);
         $query = "
         insert into exm_goods_printopt set
            $flds
         ";
         $db -> query($query);
      }

      /* 견적상품 옵션/가격 복사 */
      if ($goodsGroupCode == "30" || $goodsGroupCode == "20") {
         $adminExtraOption = new M_extra_option();
         //몰인경우 센터에서 상품을 복사한다.
         $db -> start_transaction();
         try {
            $adminExtraOption -> CopyMasterExtraOption($cid, $cfg_center[center_cid], $goodsno, "", $_GET[goodsno]);
            $adminExtraOption -> CopyMasterExtraAfterOption($cid, $cfg_center[center_cid], $goodsno, $_GET[goodsno]);
            $adminExtraOption -> CopyMasterExtraOptionUse($cid, $cfg_center[center_cid], $goodsno, $_GET[goodsno]);
            $adminExtraOption -> CopyOrderCntExtraOption($cid, $cfg_center[center_cid], $goodsno, $_GET[goodsno]);

            //가격 정보를 복사한다.
            //$adminExtraOption->CopyPriceExtraOption($cid, $cfg_center[center_cid], $goodsno, $_GET[goodsno]);

            //자동견적 가격 테이블 시즌2
            $adminExtraOption -> CopyPriceExtraOptionS2($cid, $cfg_center[center_cid], $goodsno, $_GET[goodsno]);

            //수량(건수) 가격 정보를 복사한다. 2014.12.23 by kdk
            $adminExtraOption -> CopyUnitPriceExtraOption($cid, $cfg_center[center_cid], $goodsno, $_GET[goodsno]);

            $db -> query("commit");
         } catch(Exception $e) {
            $db -> query("rollback");
         }
      }
      break;

   //아이콘 삭제
   case "icon_del" :
      $iconpath = "../../data/icon/$cid/$_GET[icon_filename]";
      if ($_GET[icon_filename] && file_exists($iconpath))
         @unlink($iconpath);

      $m_goods -> setGoodsIcon($cid, $_GET[icon_filename]);

      break;
}

switch ($_POST[mode]) {

   case "after_help_delete_ajax" :
      $item = explode(',', $_POST[goodsno]);
      //,항목 분리
      //debug($item);
      foreach ($item as $goodsno) {
         if ($goodsno) {
            $db -> query("delete from tb_extra_option_after_help where cid = '$cid' and goodsno = '$goodsno' and option_kind_code = '$_POST[option_kind_code]'");
         }
      }

      echo "success";

      exit ;
      break;

   case "after_help" :
      $dataArr = array();
      $codeArr = array();

      if (is_array($_POST))
         foreach ($_POST['code'] as $k => $v) {
            $codeArr[$k] = $v;
         }

      //debug($codeArr);
      //debug($_FILES);

      $uploaddir = "../../data/goods_detail/$cid/";
      //debug($uploaddir);

      if (!is_dir($uploaddir))
         mkdir($uploaddir, 0707);
      else
         @chmod($uploaddir, 0707);

      if (!is_dir($uploaddir)) {
         msg(_("폴더 생성에 실패했습니다.") . "\\n " . _("관리자에게 문의주세요."), -1);
         exit ;
      }

      foreach ($_FILES[file][tmp_name] as $key => $file) {
         //debug($idx);
         //debug($file);
         if ($file) {
            $file_name = date(ymdHis) . "-" . $_POST[catno] . "-" . $codeArr[$key] . "-" . $_FILES[file]['name'][$key];
            //debug($file_name);

            if (move_uploaded_file($file, $uploaddir . $file_name)) {
               $file_url = "http://" . $_SERVER[HTTP_HOST] . "/" . str_replace("../", "", $uploaddir) . $file_name;
               //debug($file_url);
               $dataArr[] = array("option_kind_code" => $codeArr[$key], "url" => $file_url);
            }
         }
      }
      //debug($dataArr);

      if ($_POST[goodsno]) {
         $target_goodsno = explode(',', $_POST[goodsno]);
         //항목 분리
         //debug($target_goodsno);
         foreach ($target_goodsno as $goodsno) {
            if ($goodsno) {
               //debug($goodsno);
               foreach ($dataArr as $data) {
                  //debug($data);
                  $query = "
				    	insert into tb_extra_option_after_help set
				    		cid			= '$cid',
				    		goodsno		= '$goodsno',
				    		option_kind_code = '$data[option_kind_code]',
				    		url			= '$data[url]'
				    	on duplicate key update
				    		option_kind_code = '$data[option_kind_code]',
				    		url		= '$data[url]'
				    	";
                  //debug($query);
                  $db -> query($query);
               }
            }
         }
      }

      break;

   case "goods_modify" :

      #기업그룹별 노출설정
      if (is_array($_POST[bid])) {
         $_POST[bid] = array_map("trim", $_POST[bid]);
         foreach ($_POST[bid] as $v) {
            $query = "
            insert into exm_goods_bid set
               cid      = '$cid',
               bid      = '$v',
               goodsno  = '$_POST[goodsno]'
            on duplicate key update
               bid      = '$v',
               goodsno  = '$_POST[goodsno]'
            ";
            $db -> query($query);
         }
         $_POST[bid] = implode("','", $_POST[bid]);
         $query = "delete from exm_goods_bid where cid = '$cid' and bid not in ('$_POST[bid]') and goodsno = '$_POST[goodsno]'";
         $db -> query($query);
      } else {
         $query = "delete from exm_goods_bid where cid = '$cid' and goodsno = '$_POST[goodsno]'";
         $db -> query($query);
      }

      # 카테고리
      //다중 카테고리로 변경        20160328    chunter
      if ($_POST[catno]) {
         foreach ($_POST[catno] as $key => $value) {
            $cat_index = $key + 1;
            if ($value) {
               $query = "
               insert into exm_goods_link set
                  cid = '$cid',
                  goodsno = '$_POST[goodsno]',
                  catno = '$value',
                  sort = -unix_timestamp(),
                  cat_index = $cat_index
               on duplicate key update
                  catno = '$value'
               ";
            } else {
               $query = "delete from exm_goods_link where cid = '$cid' and goodsno = '$_POST[goodsno]' and cat_index = $cat_index";
            }
            $db -> query($query);
         }
      } else {
         $db -> query("delete from exm_goods_link where cid = '$cid' and goodsno = '$_POST[goodsno]'");
      }

      ###상점별 옵션 노출여부 설정 변경 2014.10.23 by kdk
      $db -> query("delete from tb_goods_opt_mall_view where cid = '$cid' and goodsno = '$_POST[goodsno]'");
      $db -> query("delete from tb_goods_addopt_bundle_mall_view where cid = '$cid' and goodsno = '$_POST[goodsno]'");
      $db -> query("delete from tb_goods_addopt_mall_view where cid = '$cid' and goodsno = '$_POST[goodsno]'");

      if ($_POST[opt_view]) {//옵션 항목
         foreach ($_POST[opt_view] as $k => $v) {
            if ($v == "1") {
               $query = "insert into tb_goods_opt_mall_view (cid, goodsno, optno, view) values('$cid', '$_POST[goodsno]', '$k', '$v');";
               $db -> query($query);
            }
         }
      }

      if ($_POST[addopt_bundle_view]) {//추가옵션묶음
         foreach ($_POST[addopt_bundle_view] as $k => $v) {
            if ($v == "1") {
               $query = "insert into tb_goods_addopt_bundle_mall_view (cid, goodsno, addopt_bundle_no, view) values('$cid', '$_POST[goodsno]', '$k', '$v');";
               $db -> query($query);
            }
         }
      }

      if ($_POST[addopt_view]) {//추가옵션항목
         foreach ($_POST[addopt_view] as $k => $v) {
            if ($v == "1") {
               $query = "insert into tb_goods_addopt_mall_view (cid, goodsno, addoptno, view) values('$cid', '$_POST[goodsno]', '$k', '$v');";
               $db -> query($query);
            }
         }
      }

      ### 옵션 추가가격, 적립금 수정
      if ($_POST[optno]) {

         foreach ($_POST[optno] as $k => $v) {
            if (is_numeric($_POST[aprice][$v])) {
               $query = "
               insert into exm_goods_opt_price set
                  cid = '$cid',
                  goodsno = '$_POST[goodsno]',
                  optno = '$v',
                  aprice = '{$_POST[aprice][$v]}',
                  mall_opt_cprice = '{$_POST[mall_opt_cprice][$v]}',
                  b2b_optno = '{$_POST[b2b_optno][$v]}'
               on duplicate key update
                  aprice = '{$_POST[aprice][$v]}',
                  mall_opt_cprice = '{$_POST[mall_opt_cprice][$v]}',
                  b2b_optno = '{$_POST[b2b_optno][$v]}'
               ";
            } else {

               $query = "
               insert into exm_goods_opt_price set
                  cid = '$cid',
                  goodsno = '$_POST[goodsno]',
                  optno = '$v',
                  aprice = null,
                  mall_opt_cprice = '{$_POST[mall_opt_cprice][$v]}',
                  b2b_optno = '{$_POST[b2b_optno][$v]}'
               on duplicate key update
                  aprice = null,
                  mall_opt_cprice = '{$_POST[mall_opt_cprice][$v]}',
                  b2b_optno = '{$_POST[b2b_optno][$v]}'
               ";
            }
            $db -> query($query);

            if (is_numeric($_POST[areserve][$v])) {
               $query = "
               insert into exm_goods_opt_price set
                  cid = '$cid',
                  goodsno = '$_POST[goodsno]',
                  optno = '$v',
                  areserve = '{$_POST[areserve][$v]}'
               on duplicate key update
                  areserve = '{$_POST[areserve][$v]}'
               ";
            } else {
               $query = "update exm_goods_opt_price set areserve = null where cid = '$cid' and goodsno = '$_POST[goodsno]' and optno = '$v'";
            }
            $db -> query($query);
         }
      }
      
      # 인화옵션 가격,적립금
	  if ($_POST[printoptnm]){
		  foreach ($_POST[printoptnm] as $k=>$v){
			  if (is_numeric($_POST[mall_print_price][$v])){
				  $query = "
				  insert into exm_goods_printopt_price set
					  cid = '$cid',
					  goodsno = '$_POST[goodsno]',
					  printoptnm = '$v',
					  print_price = '{$_POST[mall_print_price][$v]}'
				  on duplicate key update
					  print_price = '{$_POST[mall_print_price][$v]}'
				  ";
			  } else {
				  $query = "update exm_goods_printopt_price set print_price = null where cid = '$cid' and goodsno = '$_POST[goodsno]' and printoptnm = '$v'";
			  }
			  $db->query($query);
	
			  if (is_numeric($_POST[mall_print_reserve][$v])){
				  $query = "
				  insert into exm_goods_printopt_price set
					  cid = '$cid',
					  goodsno = '$_POST[goodsno]',
					  printoptnm = '$v',
					  print_reserve = '{$_POST[mall_print_reserve][$v]}'
				  on duplicate key update
					  print_reserve = '{$_POST[mall_print_reserve][$v]}'
				  ";
			  } else {
				  $query = "update exm_goods_printopt_price set print_reserve = null where cid = '$cid' and goodsno = '$_POST[goodsno]' and printoptnm = '$v'";
			  }
			  $db->query($query);
		  }
	  }

      # 추가옵션 가격,적립금
      if (is_array($_POST[addopt_aprice]))
         foreach ($_POST[addopt_aprice] as $k => $v) {
            foreach ($v as $k2 => $v2) {
               if (is_numeric($v2)) {
                  $query = "
               insert into exm_goods_addopt_price set
                  cid                = '$cid',
                  goodsno            = '$_POST[goodsno]',
                  addoptno           = '$k2',
                  addopt_aprice      = '$v2',
                  mall_addopt_cprice = '{$_POST[mall_addopt_cprice][$k][$k2]}'
               on duplicate key update
                  addoptno               = '$k2',
                      addopt_aprice      = '$v2',
                      mall_addopt_cprice = '{$_POST[mall_addopt_cprice][$k][$k2]}'
               ";
               } else {
                  //on duplicate key update의 값을 &v2에서 &k2로 변경하였습니다.(165번째 줄)
                  $query = "
               insert into exm_goods_addopt_price set
                  cid                = '$cid',
                  goodsno            = '$_POST[goodsno]',
                  addoptno           = '$k2',
                  addopt_aprice      = null,
                  mall_addopt_cprice = '{$_POST[mall_addopt_cprice][$k][$k2]}'
               on duplicate key update
                  addoptno           = '$k2',
                  addopt_aprice      = null,
                  mall_addopt_cprice = '{$_POST[mall_addopt_cprice][$k][$k2]}'
               ";
               }

               $db -> query($query);

               if (is_numeric($_POST[addopt_areserve][$k][$k2])) {
                  $query = "
               insert into exm_goods_addopt_price set
                  cid            = '$cid',
                  goodsno        = '$_POST[goodsno]',
                  addoptno    = '$k2',
                  addopt_areserve   = '{$_POST[addopt_areserve][$k][$k2]}'
               on duplicate key update
                  addopt_areserve   = '{$_POST[addopt_areserve][$k][$k2]}'
               ";
               } else {
                  $query = "update exm_goods_addopt_price set addopt_areserve = null where cid = '$cid' and goodsno = '$_POST[goodsno]' and addoptno = '$k2'";
               }
               $db -> query($query);
            }
         }

      ### form 전송 취약점 개선 20160128 by kdk
      $_POST[content] = addslashes(base64_decode($_POST[content]));

      # 상세설명
      $_POST[mall_desc] = trim($_POST[content]);

      ### 이미지
      $dirS = "../../data/tmp/s/";
      $dirL = "../../data/tmp/l/";
      $dirS_c = "/public_html/data/goods/$cid/s/$_POST[goodsno]/";
      $dirL_c = "/public_html/data/goods/$cid/l/$_POST[goodsno]/";
      $img = array("", "", "", "", "");

      #list ($r_listimg) = $db->fetch("select listimg from exm_goods where goodsno = '$_POST[goodsno]'",1);
      list($r_clistimg) = $db -> fetch("select clistimg from exm_goods_cid where cid = '$cid' and goodsno = '$_POST[goodsno]'", 1);
      #list ($r_img) = $db->fetch("select img from exm_goods where goodsno = '$_POST[goodsno]'",1);
      list($r_cimg) = $db -> fetch("select cimg from exm_goods_cid where cid = '$cid' and goodsno = '$_POST[goodsno]'", 1);

      # 리스트와이드 이미지  / 2017.05.29
      list($r_clistimg_w) = $db -> fetch("select clistimg_w from exm_goods_cid where cid = '$cid' and goodsno = '$_POST[goodsno]'", 1);

      $r_img = explode("||", $r_img);
      $r_cimg = explode("||", $r_cimg);

      //207~217 라인이 주석처리 되어 있어서 상품정보(리스트 이미지 생성) 수정 시 ftp 관련 에러가 발생하여 주석 제거 / 14.10.10 / kjm
      # FTP 접속
      $ftp = ftp_connect($cfg_center[host]);
      ftp_login($ftp, $db_user, $db_pass);
      @ftp_mkdir($ftp, "/public_html/data/goods/$cid/");
      @ftp_mkdir($ftp, "/public_html/data/goods/$cid/s/");
      @ftp_mkdir($ftp, "/public_html/data/goods/$cid/s/$_POST[goodsno]/");
      @ftp_mkdir($ftp, "/public_html/data/goods/$cid/l/");
      @ftp_mkdir($ftp, "/public_html/data/goods/$cid/l/$_POST[goodsno]/");
      # FTP 디렉토리 변경
      ftp_chdir($ftp, $dirS_c);

      # 리스트 이미지 삭제 / 2012-01-10 검수완료
      if ($_POST[dellistimg]) {
         $ret = @ftp_delete($ftp, $r_clistimg);
         if ($ret)
            $db -> query("update exm_goods_cid set clistimg = '' where cid = '$cid' and goodsno = '$_POST[goodsno]'");
      }

      # 리스트 이미지 등록
      if (is_uploaded_file($_FILES[listimg][tmp_name])) {

         # 임시파일을 위한 디렉토리 체크
         if (!is_dir("../../data/tmp/")) {
            mkdir("../../data/tmp/", 0707);
            chmod("../../data/tmp/", 0707);
         }
         if (!is_dir($dirS)) {
            mkdir($dirS, 0707);
            chmod($dirS, 0707);
         }

         # 이미지 확장자 결정
         $info = getImageSize($_FILES[listimg][tmp_name]);
         switch($info[2]) {
            case "1" :
               $ext = ".gif";
               break;
            case "2" :
               $ext = ".jpg";
               break;
            case "3" :
               $ext = ".png";
               break;
         }

         # 파일명 생성
         $fsrc = time() . $ext;

         # 임시 파일 생성
         thumbnail($_FILES[listimg][tmp_name], $dirS . $fsrc, $cfg[listimg_w]);

         # DB 업데이트
         $db -> query("update exm_goods_cid set clistimg = '$fsrc' where cid = '$cid' and goodsno = '$_POST[goodsno]'");

         # FTP를 통한 이미지 수정
         @ftp_put($ftp, $fsrc, $dirS . $fsrc, FTP_BINARY);

         # 기존 이미지 삭제
         @ftp_delete($ftp, $r_clistimg);

         # 임시 파일 삭제
         @unlink($dirS . $r_clistimg);
      }

      # 리스트와이드 이미지 삭제 / 2017.05.29
      if ($_POST[dellistimg_w]) {
         $ret = @ftp_delete($ftp, $r_clistimg);
         if ($ret)
            $db -> query("update exm_goods_cid set clistimg_w = '' where cid = '$cid' and goodsno = '$_POST[goodsno]'");
      }
      # 리스트와이드 이미지 등록 / 2017.05.29
      if (is_uploaded_file($_FILES[listimg_w][tmp_name])) {

         # 임시파일을 위한 디렉토리 체크
         if (!is_dir("../../data/tmp/")) {
            mkdir("../../data/tmp/", 0707);
            chmod("../../data/tmp/", 0707);
         }
         if (!is_dir($dirS)) {
            mkdir($dirS, 0707);
            chmod($dirS, 0707);
         }

         # 이미지 확장자 결정
         $info = getImageSize($_FILES[listimg_w][tmp_name]);
         switch($info[2]) {
            case "1" :
               $ext = ".gif";
               break;
            case "2" :
               $ext = ".jpg";
               break;
            case "3" :
               $ext = ".png";
               break;
         }

         # 파일명 생성
         $fsrc = time() . $ext;

         # 임시 파일 생성
         thumbnail($_FILES[listimg_w][tmp_name], $dirS . $fsrc, "1200");

         # DB 업데이트
         $db -> query("update exm_goods_cid set clistimg_w = '$fsrc' where cid = '$cid' and goodsno = '$_POST[goodsno]'");

         # FTP를 통한 이미지 수정
         @ftp_put($ftp, $fsrc, $dirS . $fsrc, FTP_BINARY);

         # 기존 이미지 삭제
         @ftp_delete($ftp, $r_clistimg_w);

         # 임시 파일 삭제
         @unlink($dirS . $r_clistimg_w);
      }

      # 상세 이미지
      foreach ($img as $k => $v) {
         $img[$k] = $r_cimg[$k];
      }

      # FTP 디렉토리 변경
      ftp_chdir($ftp, $dirL_c);

      ### 상세설명이미지+리스트이미지 수정
      if (is_array($_FILES[img][tmp_name])) {
         $_FILES[img][tmp_name] = array_notnull($_FILES[img][tmp_name]);

         # 임시파일을 위한 디렉토리 체크
         if (!is_dir("../../data/tmp/")) {
            mkdir("../../data/tmp/", 0707);
            chmod("../../data/tmp/", 0707);
         }
         if (!is_dir($dirL)) {
            mkdir($dirL, 0707);
            chmod($dirL, 0707);
         }

         foreach ($_FILES[img][tmp_name] as $k => $v) {

            if (!is_uploaded_file($v))
               continue;

            # 이미지 확장자 결정
            $info = GetImageSize($v);
            switch($info[2]) {
               case "1" :
                  $ext = ".gif";
                  break;
               case "2" :
                  $ext = ".jpg";
                  break;
               case "3" :
                  $ext = ".png";
                  break;
            }
            if (!$ext)
               continue;
            if ($img[$k])
               @ftp_delete($ftp, $img[$k]);

            $fsrc = time() . $k . $ext;
            thumbnail($v, $dirL . $fsrc, $cfg[img_w]);

            $img[$k] = $fsrc;

            if ($r_cimg[$k])
               @ftp_delete($ftp, $r_cimg[$k]);

            @ftp_put($ftp, $fsrc, $dirL . $fsrc, FTP_BINARY);
            @unlink($dirL . $fsrc);
         }
      }

      if (is_array($img)) {

         $img[$_POST[thumbnail]];
         if (is_numeric($_POST[thumbnail]) && $img[$_POST[thumbnail]]) {

            # 해당파일을 임시파일로 다운로드
            $fsrc = $img[$_POST[thumbnail]];

            $fp = fopen($dirS . $fsrc, "w");
            @ftp_fget($ftp, $fp, $fsrc, FTP_BINARY);
            thumbnail($dirS . $fsrc, $dirS . $fsrc, $cfg[listimg_w]);

            # FTP 디렉토리 변경
            ftp_chdir($ftp, $dirS_c);

            # FTP를 통한 이미지 수정
            @ftp_put($ftp, $fsrc, $dirS . $fsrc, FTP_BINARY);

            # 기존 이미지 삭제
            list($r_clistimg) = $db -> fetch("select clistimg from exm_goods_cid where cid = '$cid' and goodsno = '$_POST[goodsno]'", 1);
            @ftp_delete($ftp, $r_clistimg);

            @unlink($dirS . $fsrc);
            $db -> query("update exm_goods_cid set clistimg = '$fsrc' where cid = '$cid' and goodsno = '$_POST[goodsno]'");
         }

         if ($_POST[delimg]) {
            foreach ($_POST[delimg] as $v) {
               @ftp_delete($ftp, $img[$v]);
               $img[$v] = "";
            }
         }

         $img = array_slice($img, 0, 5);

         $img = implode("||", $img);
         echo"a";
         $db -> query("update exm_goods_cid set cimg = '$img' where cid = '$cid' and goodsno = '$_POST[goodsno]'");

      }

      ftp_close($ftp);

      //아이콘 추가
      if (is_array($_POST[icon_filename]))
         $icon_filename = implode("||", $_POST[icon_filename]);
      else
         $icon_filename = "";

      //상세설명 JSON 추가
	  if ($_POST[goods_desc])
	  	$goods_desc = serialize($_POST[goods_desc]);
	  else
	  	$goods_desc = "";

      if (!is_numeric($_POST[price]))
         $_POST[price] = "null";

      if (!$_POST[self_deliv]) {
         $_POST[self_dtype] = 0;
         $_POST[self_dprice] = 0;
      } else {
         if ($_POST[self_dtype] != 2) {
            $_POST[self_dprice] = 0;
         }
      }

      foreach ($_r_mdn_goodslist_extra_kind as $r_k => $r_v) {
         if ($_POST[$r_k . "_flag"]) {
            if ($_POST[$r_k . "_select_goodsno"])
               $_POST[$r_k . "_select_goodsno"] = implode(",", $_POST[$r_k . "_select_goodsno"]);
            $m_goods -> setAddtionGoodsItem($cid, "I", $_POST[goodsno], $r_k, $_POST[$r_k . "_select_goodsno"], $_POST[$r_k . "_flag"]);
         }
      }

      $query = "
      update exm_goods_cid set
         price             	= $_POST[price],
         strprice          	= '$_POST[strprice]',
         reserve           	= '$_POST[reserve]',
         `desc`            	= '$_POST[mall_desc]',
         nodp              	= '$_POST[nodp]',
         mall_pageprice    	= '$_POST[mall_pageprice]',
         mall_pagereserve  	= '$_POST[mall_pagereserve]',
         mall_cprice       	= '$_POST[mall_cprice]',
         self_deliv        	= '$_POST[self_deliv]',
         self_dtype        	= '$_POST[self_dtype]',
         self_dprice       	= '$_POST[self_dprice]',
         b2b_goodsno       	= '$_POST[b2b_goodsno]',
         goodsnm_deco      	= '$_POST[goodsnm_deco]',
         goodsnm_deco_out  	= '$_POST[goodsnm_deco_out]',
         csummary          	= '$_POST[csummary]',
         csearch_word      	= '$_POST[csearch_word]',
         cmatch_goodsnm    	= '$_POST[cmatch_goodsnm]',
         cetc1             	= '$_POST[cetc1]',
         cetc2             	= '$_POST[cetc2]',
         cetc3             	= '$_POST[cetc3]',
         exp               	= '$_POST[exp]',
         cresolution       	= '$_POST[cresolution]',
         cgoods_size       	= '$_POST[cgoods_size]',
         icon_filename		= '$icon_filename',
         goods_desc			= '$goods_desc'
      where
         cid = '$cid'
         and goodsno = '$_POST[goodsno]'
      ";
      $db -> query($query);

      set_category_link(array($_POST[goodsno]));

      break;

   case "self_goods_regist" :
   case "self_goods_modify" :
   
      if ($_POST[mode] == "self_goods_regist") {
         if ($_POST[shop_code]) {
            list($chk) = $db -> fetch("select shop_code from exm_goods where shop_id = '$_POST[shop_id]' and shop_code = '$_POST[shop_code]'", 1);
            if ($chk) {
               msg(_("동일 코드 존재"), -1);
               exit ;
            }
         }
         $db -> query("insert into exm_goods set regdt=now()");
         $_POST[goodsno] = $db -> id;
         
         $db -> query("update exm_goods set shop_code=goodsno where goodsno = '$_POST[goodsno]'");
      } else {
         $r_optno = array();
         $query = "select * from exm_goods_opt where goodsno = '$_POST[goodsno]'";
         $res = $db -> query($query);
         while ($data = $db -> fetch($res)) {
            $r_optno[] = $data[optno];
         }
      }
      
      if ($_POST[ccatno]) {
         //$_POST[ccatno] = array_notnull($_POST[ccatno]);
         //list ($_POST[ccatno]) = array_slice($_POST[ccatno],-1);

         foreach ($_POST[ccatno] as $key => $value) {
            $cat_index = $key + 1;
            if ($value) {
               $query = "
                  insert into exm_goods_link set
                     cid = '$cfg[center_cid]',
                     goodsno = '$_POST[goodsno]',
                     catno = '$value',
                     sort = -unix_timestamp(),
                     cat_index = $cat_index
                  on duplicate key update
                     catno = '$value'
                  ";
            } else {
               $query = "delete from exm_goods_link where cid = '$cfg[center_cid]' and goodsno = '$_POST[goodsno]' and cat_index = $cat_index";
            }
            $db -> query($query);
         }
      } else {
         $db -> query("delete from exm_goods_link where cid = '$cfg[center_cid]' and goodsno = '$_POST[goodsno]'");
      }

      //자체상품 등록 시 상품분류 코드를 db에 입력하기위해 추가 / 14.06.27 / kjm
      if ($_POST[catno]) {
         //다중 카테고리 처리. 선택값이 없을 경우 해당순위는 삭제         20160224    chunter
         foreach ($_POST[catno] as $key => $value) {
            $cat_index = $key + 1;
            if ($value) {
               $query = "insert into exm_goods_link set
                           cid     = '$cid',
                           catno   = '$value',
                           goodsno = '$_POST[goodsno]',
                           sort    = -unix_timestamp(),
                           cat_index = $cat_index
                         on duplicate key update
                           catno   = '$value'
                        ";
            } else {
               $query = "delete from exm_goods_link where cid = '$cid' and goodsno = '$_POST[goodsno]' and cat_index = $cat_index";
            }
            //debug($query);
            $db -> query($query);
         }
      } else {
         $db -> query("delete from exm_goods_link where cid = '$cid' and goodsno = '$_POST[goodsno]'");
      }

      ### form 전송 취약점 개선 20160128 by kdk
      $_POST[desc] = addslashes(base64_decode($_POST[desc]));

      //앞뒤 공백을 등록할때부터 제거한다 / 16.12.22 / kjm
      $_POST[goods_podsno] = trim($_POST[goods_podsno]);

      //아이콘 추가
      if (is_array($_POST[icon_filename]))
         $icon_filename = implode("||", $_POST[icon_filename]);
      else
         $icon_filename = "";

      //상세설명 JSON 추가
	  if ($_POST[goods_desc])
	  	$goods_desc = serialize($_POST[goods_desc]);
	  else
	  	$goods_desc = "";
         
      ###  상품정보업데이트
      $flds = "
            goodsnm               = '$_POST[goodsnm]',
            origin                = '$_POST[origin]',
            maker                 = '$_POST[maker]',
            price                 = '$_POST[price]',
            sprice                = '$_POST[sprice]',
            cprice                = '$_POST[cprice]',
            oprice                = '$_POST[oprice]',
            totstock              = '$_POST[totstock]',
            usestock              = '$_POST[usestock]',
            state                 = '$_POST[state]',
            shiptype              = '$_POST[shiptype]',
            shipprice             = '$_POST[shipprice]',
            oshipprice            = '$_POST[oshipprice]',
            `desc`                = '$_POST[desc]',
            adminmemo             = '$_POST[adminmemo]',
            podskind              = '$_POST[podskind]',
            podsno                = '$_POST[goods_podsno]',
            vidiobook_link        = '$_POST[vidiobook_link]',
            useopt                = '$_POST[useopt]',
            rid                   = '$_POST[rid]',
            opageprice            = '$_POST[opageprice]',
            spageprice            = '$_POST[spageprice]',
            pageprice             = '$_POST[pageprice]',
            privatecid            = '$_POST[privatecid]',
            leadtime              = '$_POST[leadtime]',
            reg_cid               = '$cid',
            pods_use              = '$_POST[pods_use]',
            pods_useid            = '$_POST[pods_useid]',
            summary               = '$_POST[summary]',
            search_word           = '$_POST[search_word]',
            match_goodsnm         = '$_POST[match_goodsnm]',
            etc1                  = '$_POST[etc1]',
            etc2                  = '$_POST[etc2]',
            etc3                  = '$_POST[etc3]',
            defaultpage           = '$_POST[defaultpage]',
            minpage               = '$_POST[minpage]',
            maxpage               = '$_POST[maxpage]',
            brandno               = '$_POST[brandno]',
            resolution            = '$_POST[resolution]',
            goods_size            = '$_POST[goods_size]',
            pods_userdataurl_flag = '$_POST[pods_userdataurl_flag]',
            update_date           = now(),
            mapping_goodsno       = '$_POST[mapping_goodsno]',
            share_category_review_flag = '$_POST[share_category_review_flag]',
			input_str			  = '$_POST[input_str]',
			input_file			  = '$_POST[input_file]',
			hash_tag			  = '$_POST[hash_tag]',
			icon_filename		  = '$icon_filename',
			goods_desc			  = '$goods_desc'
         ";

      if ($_POST[useopt] && $_POST[opt1]) {
         $flds .= "
            ,optnm1     = '$_POST[optnm1]',
            optnm2      = '$_POST[optnm2]'
            ";

         $r_optno = array();
         $query = "select * from exm_goods_opt where goodsno = '$_POST[goodsno]'";
         $res = $db -> query($query);
         while ($data = $db -> fetch($res)) {
            $r_optno[] = $data[optno];
         }

         $r_optkey = array_keys($_POST[opt1]);
         $r_del_opt = array_diff($r_optno, $r_optkey);
         $r_del_opt = implode(",", $r_del_opt);
         if ($r_del_opt)
            $db -> query("delete from exm_goods_opt where optno in ($r_del_opt)");

         foreach ($_POST[opt1] as $k => $v) {
            $osort++;
            list($chk) = $db -> fetch("select optno from exm_goods_opt where goodsno = '$_POST[goodsno]' and optno = '$k'", 1);
            if ($chk) {
               $query = "
                  update exm_goods_opt set
                     podsno      = '{$_POST[podsno][$k]}',
                     podoptno = '{$_POST[podoptno][$k]}',
                     opt1     = '{$_POST[opt1][$k]}',
                     opt2     = '{$_POST[opt2][$k]}',
                     aprice      = '{$_POST[aprice][$k]}',
                     asprice     = '{$_POST[asprice][$k]}',
                     aoprice     = '{$_POST[aoprice][$k]}',
                     opt_cprice  = '{$_POST[opt_cprice][$k]}',
                     stock    = '{$_POST[stock][$k]}',
                     opt_view = '{$_POST[opt_view][$k]}',
                     osort    = '$osort'
                  where
                     optno    = '$chk'
                  ";
            } else {
               $query = "
                  insert into exm_goods_opt set
                     podsno      = '{$_POST[podsno][$k]}',
                     podoptno    = '{$_POST[podoptno][$k]}',
                     goodsno     = '$_POST[goodsno]',
                     opt1        = '{$_POST[opt1][$k]}',
                     opt2        = '{$_POST[opt2][$k]}',
                     aprice      = '{$_POST[aprice][$k]}',
                     asprice     = '{$_POST[asprice][$k]}',
                     aoprice     = '{$_POST[aoprice][$k]}',
                     opt_cprice  = '{$_POST[opt_cprice][$k]}',
                     stock       = '{$_POST[stock][$k]}',
                     opt_view    = '{$_POST[opt_view][$k]}',
                     osort       = '$osort'
                  ";
            }
            $db -> query($query);
         }
      } else {
         $flds .= "
            ,optnm1     = '',
            optnm2      = ''
            ";
         $db -> query("delete from exm_goods_opt where goodsno = '$_POST[goodsno]'");
      }

      ### 추가옵션
      if (is_array($_POST[addopt_bundle_name]))
         foreach ($_POST[addopt_bundle_name] as $k => $v) {

            $addopt_bundle[$k][addopt_bundle_name] = $v;
            $addopt_bundle[$k][addopt_bundle_required] = $_POST[addopt_bundle_required][$k] + 0;
            $addopt_bundle[$k][addopt_bundle_view] = $_POST[addopt_bundle_view][$k];

            if (is_array($_POST[addoptnm][$k])) {
               foreach ($_POST[addoptnm][$k] as $k2 => $v2) {
                  $addopt_bundle[$k][addopt][$k2][addoptnm] = $v2;
                  $addopt_bundle[$k][addopt][$k2][addopt_aprice] = $_POST[addopt_aprice][$k][$k2];
                  $addopt_bundle[$k][addopt][$k2][addopt_asprice] = $_POST[addopt_asprice][$k][$k2];
                  $addopt_bundle[$k][addopt][$k2][addopt_aoprice] = $_POST[addopt_aoprice][$k][$k2];
                  $addopt_bundle[$k][addopt][$k2][addopt_cprice] = $_POST[addopt_cprice][$k][$k2];
                  $addopt_bundle[$k][addopt][$k2][addopt_view] = $_POST[addopt_view][$k][$k2];
                  $addopt_bundle[$k][addopt][$k2][addopt_mapping_no] = $_POST[addopt_mapping_no][$k][$k2];
               }
            } else {
               unset($addopt_bundle[$k]);
            }
         }

      if (is_array($addopt_bundle)) {

         $addopt_bundle_sort = 0;
         $r_addopt_bundle_no = array();

         foreach ($addopt_bundle as $k => $v) {

            if (substr($k, 0, 2) == "b_") {
               $addopt_bundle_no = str_replace("b_", "", $k);
               $query = "
                  update exm_goods_addopt_bundle set
                     goodsno              = '$_POST[goodsno]',
                     addopt_bundle_name      = '$v[addopt_bundle_name]',
                     addopt_bundle_required  = '$v[addopt_bundle_required]',
                     addopt_bundle_view      = '$v[addopt_bundle_view]',
                     addopt_bundle_sort      = '$addopt_bundle_sort'
                  where
                     goodsno              = '$_POST[goodsno]'
                     and addopt_bundle_no = '$addopt_bundle_no'
                  ";
               $db -> query($query);

            } else {

               $query = "
                  insert into exm_goods_addopt_bundle set
                     goodsno              = '$_POST[goodsno]',
                     addopt_bundle_name      = '$v[addopt_bundle_name]',
                     addopt_bundle_required  = '$v[addopt_bundle_required]',
                     addopt_bundle_view      = '$v[addopt_bundle_view]',
                     addopt_bundle_sort      = '$addopt_bundle_sort'
                  ";

               $db -> query($query);
               $addopt_bundle_no = $db -> id;
            }

            $r_addopt_bundle_no[] = $addopt_bundle_no;

            $addopt_bundle_sort++;

            $addopt_sort = 0;
            $r_addoptno = array();
            foreach ($v[addopt] as $k2 => $v2) {

               if (substr($k2, 0, 2) == "b_") {

                  $addoptno = str_replace("b_", "", $k2);
                  $query = "
                     update exm_goods_addopt set
                        addoptnm       = '$v2[addoptnm]',
                        addopt_aprice     = '$v2[addopt_aprice]',
                        addopt_asprice    = '$v2[addopt_asprice]',
                        addopt_aoprice    = '$v2[addopt_aoprice]',
                        addopt_cprice     = '$v2[addopt_cprice]',
                        addopt_mapping_no = '$v2[addopt_mapping_no]',
                        addopt_view       = '$v2[addopt_view]',
                        addopt_sort       = '$addopt_sort'
                     where
                        goodsno           = '$_POST[goodsno]'
                        and addopt_bundle_no = '$addopt_bundle_no'
                        and addoptno      = '$addoptno'
                     ";
                  $db -> query($query);
               } else {
                  $query = "
                     insert into exm_goods_addopt set
                         goodsno             = '$_POST[goodsno]',
                        addopt_bundle_no  = '$addopt_bundle_no',
                        addoptnm       = '$v2[addoptnm]',
                        addopt_aprice     = '$v2[addopt_aprice]',
                        addopt_asprice    = '$v2[addopt_asprice]',
                        addopt_aoprice    = '$v2[addopt_aoprice]',
                        addopt_cprice     = '$v2[addopt_cprice]',
                        addopt_mapping_no = '$v2[addopt_mapping_no]',
                        addopt_view       = '$v2[addopt_view]',
                        addopt_sort       = '$addopt_sort'
                     ";
                  $db -> query($query);
                  $addoptno = $db -> id;
               }
               $addopt_sort++;
               $r_addoptno[] = $addoptno;
            }

            $r_addoptno = implode(",", $r_addoptno);
            if ($r_addoptno) {
               $query = "
                  delete from
                     exm_goods_addopt
                  where
                     goodsno              = '$_POST[goodsno]'
                     and addopt_bundle_no = '$addopt_bundle_no'
                     and addoptno not in ($r_addoptno);
                  ";
               $db -> query($query);
            } else {
               $query = "
                  delete from
                     exm_goods_addopt
                  where
                     goodsno              = '$_POST[goodsno]'
                     and addopt_bundle_no = '$addopt_bundle_no'
                     ";
               $db -> query($query);
            }
         }

         $r_addopt_bundle_no = implode(",", $r_addopt_bundle_no);
         if ($r_addopt_bundle_no) {
            $query = "
               delete from
                  exm_goods_addopt_bundle
               where
                  goodsno  = '$_POST[goodsno]'
                  and addopt_bundle_no not in ($r_addopt_bundle_no);
               ";
            $db -> query($query);
         } else {
            $query = "
               delete from
                  exm_goods_addopt_bundle
               where
                  goodsno  = '$_POST[goodsno]'
               ";
            $db -> query($query);
            $query = "
               delete from
                  exm_goods_addopt
               where
                  goodsno  = '$_POST[goodsno]'
               ";
            $db -> query($query);
         }
      } else {
         $query = "
            delete from
               exm_goods_addopt_bundle
            where
               goodsno  = '$_POST[goodsno]'
            ";
         $db -> query($query);
         $query = "delete from
                     exm_goods_addopt
                   where
                     goodsno  = '$_POST[goodsno]'
               ";
         $db -> query($query);
      }
      
      $r_printoptnm = array();
	  $query = "select * from exm_goods_printopt where goodsno = '$_POST[goodsno]'";
	  $res = $db->query($query);
	  while ($data = $db->fetch($res)){
		  $r_printoptnm[] = $data[printoptnm];
	  }
	  if (!$_POST[printoptnm]) $_POST[printoptnm] = array();
	  $r_del_printopt = array_diff($r_printoptnm,$_POST[printoptnm]);

	  foreach ($r_del_printopt as $k=>$v){
		  $query = "delete from exm_goods_printopt where goodsno = '$_POST[goodsno]' and printoptnm = '$v'";
		  $db->query($query);
	  }
	  foreach ($_POST[printoptnm] as $k=>$v){
		  if (!$v) continue;
		  $query = "
		  insert into exm_goods_printopt set
			  goodsno			= '$_POST[goodsno]',
			  printoptnm		= '{$_POST[printoptnm][$k]}',
			  print_price		= '{$_POST[print_price][$k]}',
			  print_sprice	= '{$_POST[print_sprice][$k]}',
			  print_oprice	= '{$_POST[print_oprice][$k]}',
			  osort			= '$k',
			  print_size		= '{$_POST[print_size][$k]}'
		  on duplicate key update
			  printoptnm		= '{$_POST[printoptnm][$k]}',
			  print_price		= '{$_POST[print_price][$k]}',
			  print_sprice	= '{$_POST[print_sprice][$k]}',
			  print_oprice	= '{$_POST[print_oprice][$k]}',
			  osort			= '$k',
			  print_size		= '{$_POST[print_size][$k]}'
		  ";
		  $db->query($query);
	  }
      
      $query = "update exm_goods set
                  $flds
                where goodsno = '$_POST[goodsno]'
               ";

      $data = $db -> query($query);

      ### 이미지
      $dirS = "../../data/tmp/s/";
      $dirL = "../../data/tmp/l/";
      $dirS_c = "/public_html/data/goods/s/$_POST[goodsno]/";
      $dirL_c = "/public_html/data/goods/l/$_POST[goodsno]/";
      $img = array("", "", "", "", "");

      list($r_listimg) = $db -> fetch("select listimg from exm_goods where goodsno = '$_POST[goodsno]'", 1);
      list($r_img) = $db -> fetch("select img from exm_goods where goodsno = '$_POST[goodsno]'", 1);
      $r_img = explode("||", $r_img);

      # 리스트와이드 이미지 / 2017.05.29
      list($r_listimg_w) = $db -> fetch("select listimg_w from exm_goods where goodsno = '$_POST[goodsno]'", 1);

      # FTP 접속

      $ftp = ftp_connect($cfg_center[host]);
      ftp_login($ftp, $db_user, $db_pass);

      @ftp_mkdir($ftp, "/public_html/data/goods/s/$_POST[goodsno]");
      @ftp_chmod($ftp, 0707, "/public_html/data/goods/s/$_POST[goodsno]");
      @ftp_mkdir($ftp, "/public_html/data/goods/l/$_POST[goodsno]");
      @ftp_chmod($ftp, 0707, "/public_html/data/goods/l/$_POST[goodsno]");

      # FTP 디렉토리 변경
      ftp_chdir($ftp, $dirS_c);

      # 리스트 이미지 삭제
      if ($_POST[dellistimg]) {
         $ret = @ftp_delete($ftp, $r_listimg);
         if ($ret)
            $db -> query("update exm_goods set listimg = '' where goodsno = '$_POST[goodsno]'");
      }

      # 리스트 이미지 등록
      if (is_uploaded_file($_FILES[listimg][tmp_name])) {

         # 임시파일을 위한 디렉토리 체크
         if (!is_dir("../../data/tmp/")) {
            mkdir("../../data/tmp/", 0707);
            chmod("../../data/tmp/", 0707);
         }
         if (!is_dir($dirS)) {
            mkdir($dirS, 0707);
            chmod($dirS, 0707);
         }

         # 이미지 확장자 결정
         $info = getImageSize($_FILES[listimg][tmp_name]);
         switch($info[2]) {
            case "1" :
               $ext = ".gif";
               break;
            case "2" :
               $ext = ".jpg";
               break;
            case "3" :
               $ext = ".png";
               break;
         }

         # 파일명 생성
         $fsrc = time() . $ext;

         # 임시 파일 생성
         thumbnail($_FILES[listimg][tmp_name], $dirS . $fsrc, $cfg_center[listimg_w]);

         # DB 업데이트
         $db -> query("update exm_goods set listimg = '$fsrc' where goodsno = '$_POST[goodsno]'");

         # FTP를 통한 이미지 수정
         @ftp_put($ftp, $fsrc, $dirS . $fsrc, FTP_BINARY);
         @ftp_chmod($ftp, 0707, $dirS . $fsrc);

         # 기존 이미지 삭제
         @ftp_delete($ftp, $r_listimg);

         # 임시 파일 삭제
         @unlink($dirS . $r_listimg);
      }

      # 리스트와이드 이미지 삭제 / 2017.05.29
      if ($_POST[dellistimg_w]) {
         $ret = @ftp_delete($ftp, $r_listimg_w);
         if ($ret)
            $db -> query("update exm_goods set listimg_w = '' where goodsno = '$_POST[goodsno]'");
      }

      # 리스트와이드 이미지 등록 / 2017.05.29
      if (is_uploaded_file($_FILES[listimg_w][tmp_name])) {

         # 임시파일을 위한 디렉토리 체크
         if (!is_dir("../../data/tmp/")) {
            mkdir("../../data/tmp/", 0707);
            chmod("../../data/tmp/", 0707);
         }
         if (!is_dir($dirS)) {
            mkdir($dirS, 0707);
            chmod($dirS, 0707);
         }

         # 이미지 확장자 결정
         $info = getImageSize($_FILES[listimg_w][tmp_name]);
         switch($info[2]) {
            case "1" :
               $ext = ".gif";
               break;
            case "2" :
               $ext = ".jpg";
               break;
            case "3" :
               $ext = ".png";
               break;
         }

         # 파일명 생성
         $fsrc_w = time() ."_w". $ext;

         # 임시 파일 생성
         thumbnail($_FILES[listimg_w][tmp_name], $dirS . $fsrc_w, $cfg_center[listimg_w]);
      
         # DB 업데이트
         $db -> query("update exm_goods set listimg_w = '$fsrc_w' where goodsno = '$_POST[goodsno]'");
      
         # FTP를 통한 이미지 수정
         @ftp_put($ftp, $fsrc_w, $dirS . $fsrc_w, FTP_BINARY);
         @ftp_chmod($ftp, 0707, $dirS . $fsrc_w);
      
         # 기존 이미지 삭제
         @ftp_delete($ftp, $r_listimg_w);
      
         # 임시 파일 삭제
         @unlink($dirS . $r_listimg_w);
      }

      # 상세 이미지
      foreach ($img as $k => $v) {
         $img[$k] = $r_img[$k];
      }

      # FTP 디렉토리 변경
      ftp_chdir($ftp, $dirL_c);

      # 임시파일을 위한 디렉토리 체크
      if (!is_dir("../../data/tmp/")) {
         mkdir("../../data/tmp/", 0707);
         chmod("../../data/tmp/", 0707);
      }
      if (!is_dir($dirL)) {
         mkdir($dirL, 0707);
         chmod($dirL, 0707);
      }

      if (!is_dir($dirS)) {
         mkdir($dirS, 0707);
         chmod($dirS, 0707);
      }

      ### 상세설명이미지+리스트이미지 수정
      if (is_array($_FILES[img][tmp_name])) {
         $_FILES[img][tmp_name] = array_notnull($_FILES[img][tmp_name]);

         # 임시파일을 위한 디렉토리 체크
         if (!is_dir("../../data/tmp/")) {
            mkdir("../../data/tmp/", 0707);
            chmod("../../data/tmp/", 0707);
         }
         if (!is_dir($dirL)) {
            mkdir($dirL, 0707);
            chmod($dirL, 0707);
         }

         foreach ($_FILES[img][tmp_name] as $k => $v) {

            if (!is_uploaded_file($v))
               continue;

            # 이미지 확장자 결정
            $info = GetImageSize($v);
            switch($info[2]) {
               case "1" :
                  $ext = ".gif";
                  break;
               case "2" :
                  $ext = ".jpg";
                  break;
               case "3" :
                  $ext = ".png";
                  break;
            }
            if (!$ext)
               continue;
            if ($img[$k])
               @ftp_delete($ftp, $img[$k]);

            $fsrc = time() . $k . $ext;
            thumbnail($v, $dirL . $fsrc, $cfg_center[img_w]);

            $img[$k] = $fsrc;

            if ($r_img[$k])
               @ftp_delete($ftp, $r_img[$k]);

            @ftp_put($ftp, $fsrc, $dirL . $fsrc, FTP_BINARY);
            @ftp_chmod($ftp, 0707, $dirL . $fsrc);
            @unlink($dirL . $fsrc);
         }
         //debug($img);exit;
      }

      if (is_array($img)) {

         if (is_numeric($_POST[thumbnail]) && $img[$_POST[thumbnail]]) {

            # 해당파일을 임시파일로 다운로드
            $fsrc = $img[$_POST[thumbnail]];

            $fp = fopen($dirS . $fsrc, "w");
            ftp_fget($ftp, $fp, $fsrc, FTP_BINARY);
            @thumbnail($dirS . $fsrc, $dirS . $fsrc, $cfg_center[listimg_w]);

            # FTP 디렉토리 변경
            ftp_chdir($ftp, $dirS_c);

            # FTP를 통한 이미지 수정
            ftp_put($ftp, $fsrc, $dirS . $fsrc, FTP_BINARY);
            @ftp_chmod($ftp, 0707, $dirS . $fsrc);

            # 기존 이미지 삭제

            @unlink($dirS . $fsrc);
            $db -> query("update exm_goods set listimg = '$fsrc' where goodsno = '$_POST[goodsno]'");

            list($r_listimg) = $db -> fetch("select listimg from exm_goods where goodsno = '$_POST[goodsno]'", 1);
            if ($r_listimg != $fsrc) {
               @ftp_delete($ftp, $r_listimg);
            }
         }

         if ($_POST[delimg]) {
            foreach ($_POST[delimg] as $v) {
               @ftp_delete($ftp, $img[$v]);
               $img[$v] = "";
            }
         }
         $img = array_slice($img, 0, 5);

         $img = implode("||", $img);

         $db -> query("update exm_goods set img = '$img' where goodsno = '$_POST[goodsno]'");
      }

      ftp_close($ftp);

      foreach ($_r_mdn_goodslist_extra_kind as $r_k => $r_v) {
         if ($_POST[$r_k . "_flag"]) {
            if ($_POST[$r_k . "_select_goodsno"])
               $_POST[$r_k . "_select_goodsno"] = implode(",", $_POST[$r_k . "_select_goodsno"]);
            $m_goods -> setAddtionGoodsItem($cid, "I", $_POST[goodsno], $r_k, $_POST[$r_k . "_select_goodsno"], $_POST[$r_k . "_flag"]);
         }
      }

      if ($_POST[privatecid] == $cid) {

         $data = $db -> fetch("select * from exm_goods where goodsno = '$_POST[goodsno]'");

         $query = "
               insert into exm_goods_cid set
                  cid         = '$cid',
                  goodsno     = '$_POST[goodsno]',
                  price       = '$data[price]',
                  reserve     = '$data[reserve]',
                  clistimg    = '$data[listimg]',
                  mall_cprice = '$data[cprice]',
                  cimg        = '$data[img]',
                  cresolution = '$data[resolution]',
                  cgoods_size = '$data[goods_size]',
                  icon_filename = '$icon_filename'
               on duplicate key update
                  price       = '$data[price]',
                  reserve     = '$data[reserve]',
                  clistimg    = '$data[listimg]',
                  mall_cprice = '$data[cprice]',
                  cimg        = '$data[img]',
                  cresolution = '$data[resolution]',
                  cgoods_size = '$data[goods_size]',
                  icon_filename = '$icon_filename'
            ";echo"bb";
         $db -> query($query);

         ### 이미지 복사
         $img = readurl("http://$cfg_center[host]/_sync/img.php?mode=link&cid=$cid&goodsno=$_POST[goodsno]&listimg_w=$cfg[listimg_w]&img_w=$cfg[img_w]");

         $query = "select * from exm_goods_opt where goodsno = '{$_POST[goodsno]}'";
         $res2 = $db -> query($query);
         while ($tmp = $db -> fetch($res2)) {
            $query = "
               insert into exm_goods_opt_price set
                  cid             = '$cid',
                  goodsno         = '{$_POST[goodsno]}',
                  optno           = '$tmp[optno]',
                  mall_opt_cprice = '$tmp[opt_cprice]'
               on duplicate key update
                  mall_opt_cprice = '$tmp[opt_cprice]'
               ";
            $db -> query($query);
         }

         $query = "select * from exm_goods_addopt where goodsno = '{$_POST[goodsno]}'";
         $res2 = $db -> query($query);
         while ($tmp = $db -> fetch($res2)) {
            $query = "
               insert into exm_goods_addopt_price set
                  cid                = '$cid',
                  goodsno            = '{$_POST[goodsno]}',
                  addoptno           = '$tmp[addoptno]',
                  mall_addopt_cprice = '$tmp[addopt_cprice]'
               on duplicate key update
                  mall_addopt_cprice = '$tmp[addopt_cprice]'
               ";
            $db -> query($query);
         }
      }

      list($chk) = $db -> fetch("select goodsno from exm_goods_cid where cid = '$cid' and goodsno = '$_POST[goodsno]'", 1);
      if ($chk)
         set_category_link(array($_POST[goodsno]));
      break;

   case "link_category" :
      for ($i = 0; $i < sizeof($_POST[chk]); $i++) {
         $data = $db -> fetch("select * from exm_goods where goodsno = '{$_POST[chk][$i]}'");

         $query = "
         insert into exm_goods_cid set 
            cid            = '$cid', 
            goodsno        = '{$_POST[chk][$i]}',
            price          = '$data[price]',
            mall_cprice    = '$data[cprice]',
            reserve        = '$data[reserve]',
            clistimg       = '$data[listimg]',
            cimg           = '$data[img]',
            csummary       = '$data[summary]',
            csearch_word   = '$data[search_word]',
            cmatch_goodsnm = '$data[match_goodsnm]',
            cresolution    = '$data[resolution]',
            cgoods_size    = '$data[goods_size]',
            cetc1          = '$data[etc1]',
            cetc2          = '$data[etc2]',
            cetc3          = '$data[etc3]',
            icon_filename  = '$data[icon_filename]'
         on duplicate key update
            goodsno = goodsno
         ";
         echo"cc";
         $db -> query($query);

         if ($_POST[catno2]) {
            //다중 카테고리 처리. 선택값이 없을 경우 해당순위는 삭제         20160324    chunter
            foreach ($_POST[catno2] as $key => $value) {
               $cat_index = $key + 1;
               if ($value) {
                  $query = "
                  insert into exm_goods_link set
                     cid     = '$cid',
                     catno   = '$value',
                     goodsno = '{$_POST[chk][$i]}',
                     sort    = -unix_timestamp(),
                     cat_index = $cat_index
                  on duplicate key update
                     catno   = '$value'
                  ";
               } else {
                  $query = "delete from exm_goods_link where cid = '$cid' and goodsno = '{$_POST[chk][$i]}' and cat_index = $cat_index";
               }
               //debug($query);
               $db -> query($query);
            }
         }

         ### 이미지 복사
         $img = readurl("http://$cfg_center[host]/_sync/img.php?mode=link&cid=$cid&goodsno={$_POST[chk][$i]}&listimg_w=$cfg[listimg_w]&img_w=$cfg[img_w]");

         $query = "select * from exm_goods_opt where goodsno = '{$_POST[chk][$i]}'";
         $res2 = $db -> query($query);
         while ($tmp = $db -> fetch($res2)) {
            $query = "
            insert into exm_goods_opt_price set
               cid            = '$cid',
               goodsno        = '{$_POST[chk][$i]}',
               optno       = '$tmp[optno]',
               mall_opt_cprice   = '$tmp[opt_cprice]'
            on duplicate key update
               mall_opt_cprice   = '$tmp[opt_cprice]'
            ";
            $db -> query($query);
         }

         $query = "select * from exm_goods_addopt where goodsno = '{$_POST[chk][$i]}'";
         $res2 = $db -> query($query);
         while ($tmp = $db -> fetch($res2)) {
            $query = "
            insert into exm_goods_addopt_price set
               cid            = '$cid',
               goodsno        = '{$_POST[chk][$i]}',
               addoptno    = '$tmp[addoptno]',
               mall_addopt_cprice   = '$tmp[addopt_cprice]'
            on duplicate key update
               mall_addopt_cprice   = '$tmp[addopt_cprice]'
            ";
            $db -> query($query);
         }
      }
      set_category_link($_POST[chk]);

      break;

   case "category_mod" :
      $_POST[header] = addslashes(base64_decode($_POST[header]));
      $_POST[introhtml] = addslashes(base64_decode($_POST[introhtml]));

      $dir = "../../data/category/";
      if (!is_dir($dir)) {
         mkdir($dir, 0707);
         chmod($dir, 0707);
      }
	  
	  if ($_POST[is_mobile] == 1) {
	  	  $dir = "../../data/category/m_default/";
	      if (!is_dir($dir)) {
	         mkdir($dir, 0707);
	         chmod($dir, 0707);
	      }
	  } else {
	  	  $dir = "../../data/category/$cfg[skin]/";
	      if (!is_dir($dir)) {
	         mkdir($dir, 0707);
	         chmod($dir, 0707);
	      }
	  }

      $file_path = "";

      //2014.01.13 / minks / exm_category테이블에 is_intro, introhtml컴럼 추가 업데이트
      //20150803 / minks / 모바일전용 카테고리 항목 추가
      //상품진열시 사용자정의 이미지가로사이즈(width) x 이미지세로사이즈(height) 2016.03.08 by kdk
      $query = "
      update
         exm_category
      set
         catnm      = '$_POST[catnm]',
         `rows`     = '$_POST[rows]',
         `cells`    = '$_POST[cells]',
         header     = '$_POST[header]',
         catmain    = '$_POST[catmain]',
         url        = '$_POST[url]',
         is_url     = '$_POST[is_url]',
         url_target = '$_POST[url_target]',
         is_intro   = '$_POST[is_intro]',
         introhtml  = '$_POST[introhtml]',
         is_mobile  = '$_POST[is_mobile]',
         file_path  = '$file_path',
         listimg_w  = '$_POST[listimg_w]',
         listimg_h  = '$_POST[listimg_h]',
         goods_list = '$_POST[goods_list]',
         goods_view = '$_POST[goods_view]',
         params = '$_POST[params]'
      where
         cid = '$cid'
         and catno = '$_POST[catno]'
      ";

      $db -> query($query);
      echo "<script>parent.parent.b_category_obj.innerHTML = '$_POST[catnm]'</script>";
      if ($_POST[chk_dp]) {
         $db -> fetch("update exm_category set `rows`='$_POST[rows]',`cells`='$_POST[cells]' where cid = '$cid' and catno like '$_POST[catno]%'");
      }
      if ($_POST[chk_header]) {
         $db -> fetch("update exm_category set header = '$_POST[header]' where cid = '$cid' and catno like '$_POST[catno]%'");
      }

      if ($_POST[chk_dp_size]) {
         $db -> fetch("update exm_category set `listimg_w`='$_POST[listimg_w]',`listimg_h`='$_POST[listimg_h]' where cid = '$cid' and catno like '$_POST[catno]%'");
      }

      list($imgdel, $oimgdel, $left_img, $left_oimg) = $db -> fetch("select img, oimg, left_img, left_oimg from exm_category where cid = '$cid' and catno = '$_POST[catno]'", 1);
      if ($_POST[imgdel]) {
         @unlink($dir . $imgdel);
         $db -> query("update exm_category set img = '' where cid = '$cid' and catno = '$_POST[catno]'");
      }
      if ($_POST[oimgdel]) {
         @unlink($dir . $oimgdel);
         $db -> query("update exm_category set oimg = '' where cid = '$cid' and catno = '$_POST[catno]'");
      }
      if ($_POST[left_imgdel]) {
         @unlink($dir . $left_imgdel);
         $db -> query("update exm_category set left_img = '' where cid = '$cid' and catno = '$_POST[catno]'");
      }
      if ($_POST[left_oimgdel]) {
         @unlink($dir . $left_oimgdel);
         $db -> query("update exm_category set left_oimg = '' where cid = '$cid' and catno = '$_POST[catno]'");
      }

      if (is_uploaded_file($_FILES[img][tmp_name])) {
         $info = GetImageSize($_FILES[img][tmp_name]);
         switch($info[2]) {
            case "1" :
               $ext = ".gif";
               break;
            case "2" :
               $ext = ".jpg";
               break;
            case "3" :
               $ext = ".png";
               break;
         }
         $name = $_POST[catno] . "_off" . $ext;
         move_uploaded_file($_FILES[img][tmp_name], $dir . $name);
         $db -> query("update exm_category set img = '$name' where cid = '$cid' and catno = '$_POST[catno]'");
      }
      if (is_uploaded_file($_FILES[oimg][tmp_name])) {
         $info = GetImageSize($_FILES[oimg][tmp_name]);
         switch($info[2]) {
            case "1" :
               $ext = ".gif";
               break;
            case "2" :
               $ext = ".jpg";
               break;
            case "3" :
               $ext = ".png";
               break;
         }
         $name = $_POST[catno] . "_on" . $ext;
         move_uploaded_file($_FILES[oimg][tmp_name], $dir . $name);
         $db -> query("update exm_category set oimg = '$name' where cid = '$cid' and catno = '$_POST[catno]'");
      }
      if (is_uploaded_file($_FILES[left_img][tmp_name])) {
         $info = GetImageSize($_FILES[left_img][tmp_name]);
         switch($info[2]) {
            case "1" :
               $ext = ".gif";
               break;
            case "2" :
               $ext = ".jpg";
               break;
            case "3" :
               $ext = ".png";
               break;
         }
         $name = $_POST[catno] . "_left_off" . $ext;
         move_uploaded_file($_FILES[left_img][tmp_name], $dir . $name);
         $db -> query("update exm_category set left_img = '$name' where cid = '$cid' and catno = '$_POST[catno]'");
      }
      if (is_uploaded_file($_FILES[left_oimg][tmp_name])) {
         $info = GetImageSize($_FILES[left_oimg][tmp_name]);
         switch($info[2]) {
            case "1" :
               $ext = ".gif";
               break;
            case "2" :
               $ext = ".jpg";
               break;
            case "3" :
               $ext = ".png";
               break;
         }
         $name = $_POST[catno] . "_left_on" . $ext;
         move_uploaded_file($_FILES[left_oimg][tmp_name], $dir . $name);
         $db -> query("update exm_category set left_oimg = '$name' where cid = '$cid' and catno = '$_POST[catno]'");
      }

      foreach ($_r_mdn_goodslist_extra_kind as $r_k => $r_v) {
         if ($_POST[$r_k . "_flag"]) {
            if ($_POST[$r_k . "_select_goodsno"])
               $_POST[$r_k . "_select_goodsno"] = implode(",", $_POST[$r_k . "_select_goodsno"]);
            $m_goods -> setAddtionGoodsItem($cid, "C", $_POST[catno], $r_k, $_POST[$r_k . "_select_goodsno"], $_POST[$r_k . "_flag"]);
         }
      }

      msg(_("수정되었습니다."));

      echo "<script>parent.location.reload();</script>";

      exit ;
      break;

   case "category_hidden" :
      list($hidden) = $db -> fetch("select hidden from exm_category where cid = '$cid' and catno = '$_POST[catno]'", 1);
      $hidden = ($hidden) ? 0 : 1;
      $query = "update exm_category set hidden = '$hidden' where cid = '$cid' and catno = '$_POST[catno]'";
      $db -> query($query);
      echo $hidden;

      exit ;
      break;

   case "category_add" :
      if (!trim($_POST[catnm])) {
         echo "null";
         exit ;
      }

      $length = strlen($_POST[catno]) + 3;

      list($catno) = $db -> fetch("select max(catno) from exm_category where cid = '$cid' and catno like '$_POST[catno]%' and length(catno)='$length'", 1);
      if (!$catno)
         $catno = $_POST[catno] . "001";
      else
         $catno++;
      $length = sprintf("%02d", $length);
      $catno = sprintf("%0" . $length . "d", $catno);

      $query = "insert into exm_category set cid = '$cid',catno = '$catno',catnm='$_POST[catnm]',sort = unix_timestamp(),`rows`='$_POST[rows]',`cells`='$_POST[cells]', header = '$_POST[header]'";
      $db -> query($query);
      echo $catno;

      exit ;
      break;

   case "category_all" :
      if ($_POST[chk_dp]) {
         $db -> query("update exm_category set `rows`='$_POST[rows]',`cells`='$_POST[cells]' where cid = '$cid'");
      }

      $db -> query("update exm_config set value ='$_POST[rows]' where cid = '$cid' and config = 'rows'");
      $db -> query("update exm_config set value ='$_POST[cells]' where cid = '$cid' and config = 'cells'");

      msg(_("수정되었습니다."));
      echo "<script>parent.location.reload();</script>";

      exit ;
      break;

   case "category_del" :
      list($count) = $db -> fetch("select count(*) from exm_goods_link where cid = '$cid' and catno like '$_POST[catno]%'", 1);
      if ($count) {
         echo "goods";
         exit ;
      } else {
         $query = "delete from exm_category where cid = '$cid' and catno like '$_POST[catno]%'";
         $db -> query($query);

         $m_goods -> delAddtionGoodsItem($cid, "C", $_POST[catno]);
         echo "ok";
      }

      exit ;
      break;

   case "category_sort" :
      if (is_array($_POST))
         foreach ($_POST['sort'] as $k => $v) {
            $query = "update exm_category set sort = '$k' where cid = '$cid' and catno = '$v'";
            $db -> query($query);
         }

      msg(_("정렬순서가 저장되었습니다."));

      exit ;
      break;

   case "catmain" :
      $_POST[content] = addslashes(base64_decode($_POST[content]));

      $db -> query("update exm_category set mainhtml = '$_POST[content]' where cid = '$cid' and catno = '$_POST[catno]'");

      break;

   ### 입점사 수정
   case "release_add" :
   case "release_modify" :
      $_POST[phone] = implode("-", array_notnull($_POST[phone]));

      if ($_POST[order_shiptype])
         $_POST[order_shiptype] = implode(",", $_POST[order_shiptype]);
      //$_POST[zipcode] = implode("-",array_notnull($_POST[zipcode]));

      if ($_POST[password])
         $addqr = "password = md5('$_POST[password]'),";

      $flds = "
      $addqr
      compnm          = '$_POST[compnm]',
      nicknm          = '$_POST[nicknm]',
      name            = '$_POST[name]',
      manager         = '$_POST[manager]',
      phone           = '$_POST[phone]',
      zipcode         = '$_POST[zipcode]',
      address         = '$_POST[address]',
      address_sub     = '$_POST[address_sub]',
      shiptype        = '$_POST[shiptype]',
      shipprice       = '$_POST[shipprice]',
      shipconditional = '$_POST[shipconditional]',
      oshipprice      = '$_POST[oshipprice]',
      adminmemo       = '$_POST[adminmemo]',
      oasis_url       = '$_POST[oasis_url]',
      ship_method     = '$_POST[order_shiptype]',
      cid             = '$cid'
      $regdt
      ";
      $query = ($_POST[mode] == "release_add") ? "insert into exm_release set $flds, rid = '$_POST[rid]'" : "update exm_release set $flds where rid = '$_POST[rid]'";

      $db -> query($query);

      break;

   ### 기타설정
   case "etc" :
      $flds = array('listimg_w' => $_POST[listimg_w], 'img_w' => $_POST[img_w], 'cells' => $_POST[cells], 'rows' => $_POST[rows], 'top_category_sub_off' => $_POST[top_category_sub_off], 'est_upload_url' => $_POST[est_upload_url], 'est_fileinfo_url' => $_POST[est_fileinfo_url], );

      foreach ($flds as $k => $v) {
         $query = "
         insert into exm_config set 
            cid     = '$cid',
            config  = '$k', 
            value   = '$v' 
         on duplicate key 
            update value = '$v'";

         $db -> query($query);
      }
      break;

   ### SNS 상품 퍼가기 설정 / 14.05.02 / kjm
   case "sns" :
      $query = "
      insert into exm_config set 
         cid     = '$cid',
         config  = 'sns_goods', 
         value   = '$_POST[sns]' 
      on duplicate key 
         update value = '$_POST[sns]'";

      $db -> query($query);

      break;

   case "sort_priority" :
      foreach ($_POST[orderby] as $key => $val) {
         if ($val) {
            $query = "insert into md_goods_sort set
                        cid = '$cid',
                        goodsno = '$key',
                        sort = '$_POST[sort]',
                        catno = '$_POST[catno]',
                        priority = '$val'
                      on duplicate key update
                        priority = '$val'
                     ";
            $db -> query($query);
         }
      }
      break;

   ###메인화면 상품 블럭 설정###
   case "main_block" :
      if (!$_POST[id]) {
        msg(_("필수 항목이 없습니다.[id]"), -1);
         exit ;
      }

      if (!$_POST[block_code]) {
         msg(_("필수 항목이 없습니다.[코드]"), -1);
         exit ;
      }

      if (!$_POST[display_text]) {
         msg(_("필수 항목이 없습니다.[분류 표시명]"), -1);
         exit ;
      }

      $uploaddir = "../../data/main_block/$cid/";
      //debug($uploaddir);

      if (!is_dir($uploaddir))
         mkdir($uploaddir, 0707);
      else
         @chmod($uploaddir, 0707);

      if (!is_dir($uploaddir)) {
         msg(_("폴더 생성에 실패했습니다.") . "\\n " . _("관리자에게 문의주세요."), -1);
         exit ;
      }
	
      $query = "update md_main_block 
         set display_text = '$_POST[display_text]',
				display_data = '$_POST[display_data]',
				order_by = '$_POST[order_by]',
				state = '$_POST[state]',
				update_date = now()
			where cid = '$cid' 
				and ID = '$_POST[id]'
				and block_code = '$_POST[block_code]'
      	";
	

      $db -> query($query);
      //debug($query);

      if ($_POST[del_display_img_flag] == "on") {
         @unlink($uploaddir . $_POST[del_display_img]);
         $db -> query("update md_main_block set display_img='' where cid='$cid' and ID='$_POST[id]' and block_code='$_POST[block_code]'");
      }

      if (is_uploaded_file($_FILES[display_img][tmp_name])) {
         $info = GetImageSize($_FILES[display_img][tmp_name]);
         switch($info[2]) {
            case "1" :
               $ext = ".gif";
               break;
            case "2" :
               $ext = ".jpg";
               break;
            case "3" :
               $ext = ".png";
               break;
         }
         $name = $_POST[block_code] . $ext;
         move_uploaded_file($_FILES[display_img][tmp_name], $uploaddir . $name);
         $db -> query("update md_main_block set display_img='$name' where cid='$cid' and ID='$_POST[id]' and block_code='$_POST[block_code]'");
      }

      break;

   ###메인화면 상품 블럭 순서 설정###
   case "main_block_orderby" :
      if (!$_POST[id]) {
         msg(_("필수 항목이 없습니다.[id]"), -1);
         exit ;
      }
	  
	  //debug($_POST[id]);
	  
	  if ($_POST[id]) {
        foreach ($_POST[id] as $k => $v) {

		  $order_by = $k + 1;
	      $query = "update md_main_block 
	         set order_by = '$order_by',
					update_date = now()
				where cid = '$cid' 
					and ID = '$v'
	      	";

	      $db -> query($query);
	      //debug($query);
		}
	  }	

      break;

   case "addtion_goods_list" :
      if ($_POST[catno])
         $addWhere = "c.catno like '$_POST[catno]%'";
      if ($_POST[goodsnm]) {
         if ($addWhere)
            $addWhere .= " and ";
         $addWhere .= "a.goodsnm like '%$_POST[goodsnm]%'";
      }
      $orderby = "order by a.goodsno";
      $data = $m_goods -> getAdminGoodsList($cid, $addWhere, $orderby);

      if ($data) {
         $rtnStr = "<ul>";
         foreach ($data as $k => $v) {
            $rtnStr .= "<li>";
            $rtnStr .= "<span class=\"list_item\"><input type=\"hidden\" name=\"$_POST[kind]_goodsno[]\" value=\"$v[goodsno]\">" . goodsListImg($v[goodsno], 50, 50) . "<b>" . $v[goodsnm] . "</b><br>" . getCatnm($v[catno]) . "</span>";
            $rtnStr .= "<button type=\"button\" class=\"btn btn-sm btn-success list_item_btn add_item\">추가</button>";
            $rtnStr .= "</li>";
         }
         $rtnStr .= "</ul>";
      } else {
         $rtnStr = "<p>등록된 상품이 없습니다.</p>";
      }

      echo $rtnStr;
      exit ;

      break;

   case "addtion_goods_select" :
      $addWhere = "where cid='$cid' and addtion_key_kind='$_POST[kind2]' and addtion_key='$_POST[addtion_key]' and addtion_goods_kind='$_POST[kind]'";
      $data = $m_goods -> getAddtionGoodsItem($cid, $addWhere);

      if ($data[addtion_goodsno]) {
         $addWhere2 = "a.goodsno in ($data[addtion_goodsno])";
         $orderby2 = "order by field(a.goodsno,$data[addtion_goodsno])";
         $data2 = $m_goods -> getAdminGoodsList($cid, $addWhere2, $orderby2);

         if ($data2) {
            $rtnStr = "<ul id=\"$_POST[kind]_sortable\">";
            foreach ($data2 as $k => $v) {
               $rtnStr .= "<li>";
               $rtnStr .= "<span class=\"list_item\"><input type=\"hidden\" name=\"$_POST[kind]_select_goodsno[]\" value=\"$v[goodsno]\">" . goodsListImg($v[goodsno], 50, 50) . "<b>" . $v[goodsnm] . "</b><br>" . getCatnm($v[catno]) . "</span>";
               $rtnStr .= "<button type=\"button\" class=\"btn btn-sm btn-success list_item_btn del_item\">삭제</button>";
               $rtnStr .= "</li>";
            }
            $rtnStr .= "</ul>";
         } else {
            $rtnStr = "<p>선택된 상품이 없습니다.</p>";
         }
      } else {
         $rtnStr = "<p>선택된 상품이 없습니다.</p>";
      }

      echo $rtnStr;
      exit ;

      break;

   //아이콘 추가 및 수정
   case "icon_upload" :
      if (is_uploaded_file($_FILES[iconimg][tmp_name])) {
         $dir = "../../data/icon/";
         if (!is_dir($dir)) {
            mkdir($dir, 0707);
            chmod($dir, 0707);
         }

         $dir = "../../data/icon/$cid/";
         if (!is_dir($dir)) {
            mkdir($dir, 0707);
            chmod($dir, 0707);
         }

         $info = getimagesize($_FILES[iconimg][tmp_name]);

         if (!in_array($info[2], array(1, 2, 3))) {
            msg("아이콘은 gif,jpg,png 이미지만을 업로드 할 수 있습니다.", -1);
            exit ;
         }

         switch ($info[2]) {
            case "1" :
               $ext = ".gif";
               break;
            case "2" :
               $ext = ".jpg";
               break;
            case "3" :
               $ext = ".png";
               break;
         }

         if ($_POST[icon_filename]) {
            //수정
            $iconpath = "../../data/icon/$cid/$_POST[icon_filename]";
            if (file_exists($iconpath))
               @unlink($iconpath);
            $filename = substr($_POST[icon_filename], 0, strrpos($_POST[icon_filename], ".")) . $ext;
         } else {
            //추가
            $filename = "icon_" . $_POST[iconno] . $ext;
         }

         move_uploaded_file($_FILES[iconimg][tmp_name], "../../data/icon/$cid/$filename");
         echo "<script>alert('" . _("저장 완료되었습니다.") . "');this.close();opener.location='icon.php';</script>";
      } else {
         msg("아이콘 이미지를 업로드 해주세요.", -1);
      }

      exit ;
      break;
}

msgNlocationReplace(_("저장 완료되었습니다."), $_SERVER['HTTP_REFERER']);
?>