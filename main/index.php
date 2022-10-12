<?
include "../_header.php";
include "../lib/class.page.php";

$chkBrowser = getBrowser();
if($chkBrowser[name] == "Internet Explorer")
   $browser = "IE";
else
   $browser = "notIE";

$goods = new Goods();
$goods->getMain();


if($cfg[skin_theme] == "M2"){

	//ajax로 가져오도록 수정 / 19.05.07 / kjm
    if($cfg[insta_config]) {
        //인스타그램 사용여부에 따라  api 연동 및 main_block_01_M2.htm 보이고 숨기기.
        $cfg[insta_config] = unserialize($cfg[insta_config]);
        //debug($cfg[insta_config]);
      
        if ($cfg[insta_config][insta_use] != "N") {
            //$insta_data = instgram_linkage_data();
            //$tpl -> assign('insta_data', $insta_data);
				$tpl -> assign('insta_config', $cfg[insta_config]);
        }
   }

   $m_board = new M_board();
   $m_etc = new M_etc();

   $notice_list = $m_board->getBoardDataListNLimit($cid, 'notice', 3);

   $now = date("Y-m-d");
   $addWhere = "where cid='$cid' and sdate <= '{$now}' and (!edate or edate >= '{$now}')";
   $orderby = "order by eventno desc";
   $event_list = $m_etc->getEventList($cid, $addWhere, $orderby, "limit 3");

   $tpl->assign('notice_list', $notice_list);
   $tpl->assign('event_list', $event_list);
   $tpl->define("main_block_01_M2", "main/main_block_01_M2.htm"); //메인배너-M2
}
if($cfg[skin_theme] == "M3"){
	
	//ajax로 가져오도록 수정 / 19.05.07 / kjm
    if($cfg[insta_config]) {
        //인스타그램 사용여부에 따라  api 연동 및 main_block_01_M3.htm 보이고 숨기기.
        $cfg[insta_config] = unserialize($cfg[insta_config]);
        //debug($cfg[insta_config]);
      
        if ($cfg[insta_config][insta_use] != "N") {
            $insta_data = instgram_linkage_data();

            //$tpl -> assign('insta_data', $insta_data);
				$tpl -> assign('insta_config', $cfg[insta_config]);
        }
   }
   
   $m_board = new M_board();
   $m_etc = new M_etc();

   $notice_list = $m_board->getBoardDataListNLimit($cid, 'notice', 3);

   $now = date("Y-m-d");
   $addWhere = "where cid='$cid' and sdate <= '{$now}' and (!edate or edate >= '{$now}')";
   $orderby = "order by eventno desc";
   $event_list = $m_etc->getEventList($cid, $addWhere, $orderby, "limit 3");

   $tpl->assign('notice_list', $notice_list);
   $tpl->assign('event_list', $event_list);
   $tpl->define("main_block_01_M3", "main/main_block_01_M3.htm"); //메인배너-M2
}
if($cfg[skin_theme] == "I1"){ //아이스크림몰
	//메인 블럭 컨텐츠 정보 조회.
	$mcdata = $goods->mainBlockContentData;
	
	//메인 블럭 정보 조회.
	$data = $goods->mainBlockData;
	
	#debug($data);
    if($data) {
	   foreach ($data as $key => $val) {
	      #debug($val[block_code]);
		   #debug($val[state]);
	      #debug($val[order_by]);
	      //debug(strlen($val[order_by]));
	
    	   if (strlen($val[order_by]) > 1) { // order_by가 9이상이면...
             if ($val[state] == "0")
                $define_url = "main/".$val[block_code].".htm"; //$define_url = "main/main_block_".$val[order_by].".htm";
             else
                $define_url = "main/".$val[block_code].".htm"; 
    
             $tpl->define("main_block_0".$val[order_by]."_I1", $define_url);
             //$blockDataArr["main_block_".$val[order_by]] = $val;
             $blockDataArr[$val[block_code]] = $val;
          } else {
             if ($val[state] == "1")
                $define_url = "main/".$val[block_code].".htm"; //$define_url = "main/main_block_0".$val[order_by].".htm";
             else
                $define_url = "main/".$val[block_code].".htm"; 
    
             $tpl->define("main_block_0".$val[order_by]."_I1", $define_url);
             //$blockDataArr["main_block_0".$val[order_by]] = $val;
             $blockDataArr[$val[block_code]] = $val;
          }
        }
    } else {
       	$tpl->define("main_block_01_I1", "main/main_block_01_I1.htm");
    	$tpl->define("main_block_02_I1", "main/main_block_02_I1.htm");
    
    }
}


if($cfg[skin_theme] == "P1"){ //픽스스토리
	$now = date("Y-m-d");
	$m_etc = new M_etc();
	$m_member = new M_member();
	
   $addWhere = "where cid='$cid' and sdate <= '{$now}' and (!edate or edate >= '{$now}')";
   $orderby = "order by eventno desc";
   $event_list = $m_etc->getEventList($cid, $addWhere, $orderby, "limit 4");
  
   $best_data = $m_etc->getGalleryBestInfo($cid);
   $review_data = $m_member->getReviewBestInfo($cid);
  
   if ($review_data) {
	  if (strlen($review_data[subject]) > 40) $review_data[subject] = mb_substr($review_data[subject], 0, 15, "utf-8")."...";
	  
      $review_data[content_line] = explode("\n", $review_data[content]);
      for ($j=0; $j<2; $j++) {
         if (strlen($review_data[content_line][$j]) > 200) $review_data[content_line][$j] = mb_substr($review_data[content_line][$j], 0, 70, "utf-8")."...";
	   }
   }

   $tpl->assign('event_list', $event_list);
   $tpl->assign('best_data', $best_data);
   $tpl->assign('review_data', $review_data);
	 
   $tpl->define("main_block_01_P1", "main/main_block_01_P1.htm");
   $tpl->define("main_block_02_P1", "main/main_block_02_P1.htm");
   $tpl->define("main_block_03_P1", "main/main_block_03_P1.htm");
}

if($cid == "print" || $cid == "indigo"){ //알래스카
   //$sql = "select * from exm_review where cid='$cid' order by no desc limit $limitCount";
   
   $db_table = "exm_review";

   $where[] = "cid = '$cid'";

   $pg = new Page($_GET[page], 5);
   $pg->field = "*";
   $pg->setQuery($db_table, $where, "no desc");
   $pg->exec();

   $res = $pg->resource;
   while ($data = $db->fetch($res)) {
      if($data[img])
         $data[img] = explode("|", $data[img]);

      $review_list[] = $data;
   }

   $tpl->assign('review_list',$review_list);
   $tpl->assign('pg',$pg);
   
   //$review_list = $m_board->getBoardDataListNLimit($cid, 'review', 5);
   //$tpl->assign('review_list', $review_list);
}
$tpl->define("main_block_01", "main/blank.htm"); //메인상품1
$tpl->define("main_block_02", "main/blank.htm"); //메인상품2
$tpl->define("main_block_03", "main/blank.htm"); //메인상품3
$tpl->define("main_block_04", "main/blank.htm"); //메인상품4
$tpl->define("main_block_05", "main/blank.htm"); //메인상품5
$tpl->define("main_block_06", "main/blank.htm"); //메인상품6
$tpl->define("main_block_07", "main/blank.htm"); //메인상품7
$tpl->define("main_block_08", "main/blank.htm"); //메인게시판
$tpl->define("main_block_09", "main/blank.htm"); //메인배너+후기
$tpl->define("main_block_10", "main/blank.htm"); //메인게시판+배너
$tpl->define("main_block_11", "main/blank.htm"); //메인상품11-CANVAS
$tpl->define("main_block_12", "main/blank.htm"); //메인배너-3개

//메인 블럭 컨텐츠 정보 조회.
$mcdata = $goods->mainBlockContentData;
//debug($mcdata);

//메인 블럭 정보 조회.
$data = $goods->mainBlockData;
//debug($data);

if($data) {
   foreach ($data as $key => $val) {
      //debug($val[block_code]);
      //debug($val[order_by]);
      //debug(strlen($val[order_by]));

      if (strlen($val[order_by]) > 1) { // order_by가 9이상이면...
         if ($val[state] == "0")
            $define_url = "main/".$val[block_code].".htm"; //$define_url = "main/main_block_".$val[order_by].".htm";
         else
            $define_url = "main/blank.htm";

         $tpl->define("main_block_".$val[order_by], $define_url);
         //$blockDataArr["main_block_".$val[order_by]] = $val;
         $blockDataArr[$val[block_code]] = $val;
      } else {
         if ($val[state] == "0")
            $define_url = "main/".$val[block_code].".htm"; //$define_url = "main/main_block_0".$val[order_by].".htm";
         else
            $define_url = "main/blank.htm";

         $tpl->define("main_block_0".$val[order_by], $define_url);
         //$blockDataArr["main_block_0".$val[order_by]] = $val;
         $blockDataArr[$val[block_code]] = $val;
      }
   }
} else {
	/*$tpl->define("main_block_01", "main/blank.htm"); //메인상품1
	$tpl->define("main_block_02", "main/blank.htm"); //메인상품2
	$tpl->define("main_block_03", "main/blank.htm"); //메인상품3
	$tpl->define("main_block_04", "main/blank.htm"); //메인상품4
	$tpl->define("main_block_05", "main/blank.htm"); //메인상품5
	$tpl->define("main_block_06", "main/blank.htm"); //메인상품6
	$tpl->define("main_block_07", "main/blank.htm"); //메인상품7
	$tpl->define("main_block_08", "main/blank.htm"); //메인게시판
   $tpl->define("main_block_09", "main/blank.htm"); //메인배너+후기
   $tpl->define("main_block_10", "main/blank.htm"); //메인게시판+배너
   $tpl->define("main_block_11", "main/blank.htm"); //메인상품11-CANVAS*/
}

//debug($data);
//debug($blockDataArr);
//debug($blockDataArr[main_block_01]);
//debug($blockDataArr[main_block_02]);
//debug($blockDataArr[main_block_03]);
//debug($blockDataArr[main_block_04]);
//debug($blockDataArr[main_block_05]);
//debug($blockDataArr[main_block_06]);
//debug($blockDataArr[main_block_07]);
//debug($blockDataArr[main_block_08]);
//debug($blockDataArr[main_block_09]);
//debug($blockDataArr[main_block_10]);
//debug($blockDataArr[main_block_11]);
//debug($blockDataArr[main_block_12]);

//공통 헤더에서 선언해야 할 부분이다.		테스트를 위해 일단 여기서.
//$tpl->define("slide_exe_info", "common/top_blide_banner01.htm");
//$tpl->define("top_slide_banner", "common/top_blide_banner01.htm");
$m_board = new M_board();

if($cfg[main_display_board_left]){
	$main_board_limit_cnt = 5;
	if($cfg[skin_theme] == "P1")	$main_board_limit_cnt = 4;
   $board_left_data = $m_board->getBoardDataListNLimit($cid, $cfg[main_display_board_left], $main_board_limit_cnt);
   $board_left_set_data = $m_board->getBoardSetInfo($cid, $cfg[main_display_board_left]);

   $tpl->assign("board_left_data", $board_left_data);
   $tpl->assign("board_left_set_data", $board_left_set_data);
}

if($cfg[main_display_board_right]){
	$main_board_limit_cnt = 5;
	if($cfg[skin_theme] == "P1")	$main_board_limit_cnt = 4;
   $board_right_data = $m_board->getBoardDataListNLimit($cid, $cfg[main_display_board_right], $main_board_limit_cnt);
   $board_right_set_data = $m_board->getBoardSetInfo($cid, $cfg[main_display_board_right]);

   $tpl->assign("board_right_data", $board_right_data);
   $tpl->assign("board_right_set_data", $board_right_set_data);
}

if($blockDataArr[main_block_09] && $blockDataArr[main_block_09][state] == "0"){
   //사용후기 게시글
   $board_review_data = $m_board->getBoardDataListNLimit($cid, "review", 5);
   //$board_review_set_data = $m_board->getBoardSetInfo($cid, "review");
   
   $tpl->assign("board_review_data", $board_review_data);
   //$tpl->assign("board_review_set_data", $board_review_set_data);
}

if($mcdata) {
    if ($mcdata[dg_main_page] == "") $mcdata[dg_main_page] = "ilark";
    
    $tpl->define("main_block_content", "main/main_block_content_". $mcdata[dg_main_page] .".htm");
}
else {
	$tpl->define("main_block_content", "main/main_block_content.htm");
}

//범아(태웅) 테마.
if($cfg[skin_theme] == "B1"){
//debug($data);
    if($data) {
        // ## 현재 2개만 사용함.(메인 분류 관리)
        foreach ($data as $key => $val) {
            if (strlen($val[order_by]) > 1) { // order_by가 9이상이면...
                if ($val[state] == "0")
                    $define_url = "main/".$val[block_code]."_B1.htm"; //$define_url = "main/main_block_".$val[order_by].".htm";
                else
                    $define_url = "main/blank.htm";
    
                $tpl->define("main_block_".$val[order_by], $define_url);
                //$blockDataArr["main_block_".$val[order_by]] = $val;
                $blockDataArr[$val[block_code]] = $val;
            } else {
                if ($val[state] == "0")
                    $define_url = "main/".$val[block_code]."_B1.htm"; //$define_url = "main/main_block_0".$val[order_by].".htm";
                else
                    $define_url = "main/blank.htm";
    
                $tpl->define("main_block_0".$val[order_by], $define_url);
                //$blockDataArr["main_block_0".$val[order_by]] = $val;
                $blockDataArr[$val[block_code]] = $val;
            }        
        }
    }
//debug($blockDataArr);
}

//debug($tpl);

$cfg[policy] = getCfg('policy');
//debug($blockDataArr[main_block_03][goods]);
//debug($tpl);
$tpl -> print_('tpl');
?>
