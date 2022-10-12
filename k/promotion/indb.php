<?

include "../lib.php";

$m_etc = new M_etc();
$m_goods = new M_goods();


/***/switch ($_GET[mode]){

case "del_coupon_member":
	$addWhere = "where no = '$_GET[no]'";
	$data = $m_etc->getCouponSetInfo($cid, $addWhere);
	
	$cc = $data[coupon_code];
	$cu = $data[coupon_use];
	$cic = $data[coupon_issue_code];
	$cich = $data[coupon_issue_code_history];
	$cam = $data[coupon_able_money];
	
	//분할정액쿠폰일 경우
	if ($cich) {
		$data2 = $m_etc->getCouponInfo($cid, $cc);
		
		$cp = $data2[coupon_price];
		$r_cich = explode("|", $cich);
		$chk = (($cp * count($r_cich)) > $cam) ? 1 : 0; //쿠폰사용여부 체크
	}
	
	if ($cu || $chk) {
		msg("이미 회원이 사용한 쿠폰이므로 삭제할 수 없습니다.", $_SERVER[HTTP_REFERER]);
	} else {
		$addColumn = "set coupon_issue_yn = 0, mid = '', coupon_issuedt = null";
		
		if ($cich) {
			foreach ($r_cich as $v) {
				$addWhere2 = "where coupon_issue_code = '$v'";
				$m_etc->setCouponIssueInfo($cid, $addColumn, $addWhere2);
			}
		} else if ($cic) {
			$addWhere3 = "where coupon_issue_code = '$cic'";
			$m_etc->setCouponIssueInfo($cid, $addColumnm, $addWhere3);
		}
		
		$m_etc->delCouponSetInfo($_GET[no]);
		
		msg("삭제되었습니다.",$_SERVER[HTTP_REFERER]);
	}
	
exit; break;

case "coupon_offline_issue":

	set_time_limit(0);
	
	$query = "select * from exm_coupon where cid = '$cid' and coupon_code = '$_GET[coupon_code]'";
	$data = $db->fetch($query);

	$w = array();
	for ($i=0;$i<=9;$i++) $w[$i] = $i;
	for ($i=65;$i<=90;$i++) $w[chr($i)] = chr($i);

	$cp = array();

	if (!$data[coupon_issue_ea]){
		msg("쿠폰발행수량이 0 입니다.\n발행수량을 재수정해주세요.",-1);
		exit;
	}

	for ($k=0;$k<$data[coupon_issue_ea];$k++){

		unset($coupon_issue_code);

		$coupon_issue_code = $data[coupon_offcode];

		$coupon_issue_code .= "-";

		for ($i=0;$i<6;$i++){
			$coupon_issue_code .= array_rand($w);
		}

		$coupon_issue_code .= "-";

		for ($i=0;$i<5;$i++){
			$coupon_issue_code .= array_rand($w);
		}

		if (in_array($coupon_issue_code,$cp)){
			$k--;
			$err++;
		} else {
			$cp[] = $coupon_issue_code;

			$query = "
			insert into exm_coupon_issue set
				cid					= '$cid',
				coupon_issue_code	= '$coupon_issue_code',
				coupon_code			= '$data[coupon_code]',
				coupon_regdt		= now()
			";
			$db->query($query);
		}
	}

	$db->query("update exm_coupon set coupon_off_issue = 1 where cid = '$cid' and coupon_code = '$_GET[coupon_code]'");

	msg("발행이 완료되었습니다",$_SERVER[HTTP_REFERER]);

	break;

case "coupon_del":

	$db->query("delete from exm_coupon where cid = '$cid' and coupon_code = '$_GET[coupon_code]'");
	$db->query("delete from exm_coupon_set where cid = '$cid' and coupon_code = '$_GET[coupon_code]'");
	$db->query("delete from exm_coupon_issue where cid = '$cid' and coupon_code = '$_GET[coupon_code]'");

	break;


case "delEvent":
	### 배너삭제
	$banner = "../../data/event/$cid/$_GET[eventno]";
	if (is_file($banner)) unlink($banner);
	
	$m_etc->delEventInfo($cid, $_GET[eventno]);
	
break;

/*
case "delEvent":

	list ($img)	= $db->fetch("select banner from exm_event where eventno = '$_GET[no]' and cid = '$cid'",1);
	@unlink("../../data/event/".$img);

	$db->query("delete from exm_event where eventno = '$_GET[no]' and cid = '$cid'");
	$db->query("delete from exm_event_link where cid = '$cid' and eventno = '$_GET[no]'");

	break;
*/

case "delFormmail":

	$db->query("delete from exm_formmail where no = '$_GET[no]'");

	break;


case "dnExcel":

	if (!$_GET[query] || !$_GET[kind]) exit;

	### form 전송 취약점 개선 20160128 by kdk
	if(isset($_GET[pod_signed]) && isset($_GET[pod_expired])) {
		//debug($cid);
		//debug($_GET[pod_signed]);
		//debug($_GET[pod_expired]);
		//debug($_GET[query]);
		//debug($_SERVER[PHP_SELF]);
		//debug(urldecode(base64_decode($_GET[query])));
		if(exp_compare($_GET[pod_expired])) {
			//debug("not expired!");
			$url_query = $_SERVER[PHP_SELF]."?query=".$_GET[query];
			//debug($url_query);
			$signedData = signatureData($cid, $url_query);
			//debug($signedData);
			if (sig_compare($signedData, $_GET[pod_signed])) {
				$query = urldecode(base64_decode($_GET[query]));
			} 
			else {
				//debug("sig not match!");
				msg("서버키 검증에 실패했습니다.",$_SERVER[HTTP_REFERER]);exit;
			}
		}
		else {
			//debug("expired!");
			msg("서버키가 만료되었습니다.",$_SERVER[HTTP_REFERER]);exit;
		}
	}
	else {
		$query = urldecode(base64_decode($_GET[query]));
	}
	//debug($_POST[query]);
	//exit;		
	### form 전송 취약점 개선 20160128 by kdk	
	
	header("Content-Type: application/vnd.ms-excel"); 
	header("Content-Disposition: attachment; filename=$_GET[kind].".date("YmdHi").".xls");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
	header("Pragma: public");
	echo "<meta http-equiv=content-type content='text/html; charset=UTF-8'>";

	//$res = $db->query(base64_decode($_GET[query]));
	$res = $db->query($query);
	include "inc.excel.$_GET[kind].php";

	exit; break;
	
case "coupon_use_update" :
	if ($_GET[update_code])
	{		
		$coupon_usedt = "now()";
	} else {		
		$coupon_usedt = 'null';
	}
	$coupon_log = date("Y-m-d H:i:s") .' 관리자변경 처리<br>';
	$query = "
		update exm_coupon_set 
		set
			coupon_use = '$_GET[update_code]',
			payno = 0,
			ordno = 0,
			ordseq = 0,
			coupon_usedt = $coupon_usedt,
			coupon_memo = CONCAT_WS('',coupon_memo, '$coupon_log')
		where
			no = '$_GET[no]'
		";
		$db->query($query);
		
	msg("정상적으로 처리되었습니다.", $_SERVER[HTTP_REFERER]);		
	break;		
	
case "del_coupon_issue":
	$addWhere = "where cid='$cid' and (coupon_issue_code='$_GET[coupon_issue_code]' or coupon_issue_code_history like '%$_GET[coupon_issue_code]%')";
	$cnt = $m_etc->getSoldCouponCnt($cid, $addWhere);
	
	if ($cnt) {
		msg("이미 회원에게 발급된 쿠폰이므로 삭제할 수 없습니다.", $_SERVER[HTTP_REFERER]);
	} else {
		$data = $m_etc->getCouponIssueInfo($cid, $_GET[coupon_issue_code]);
		$cc = $data[coupon_code];
		
		$m_etc->delCouponIssueInfo($_GET[coupon_issue_code]);
		
		$addColumn = "set coupon_issue_ea=coupon_issue_ea-1";
		$addWhere2 = "where cid='$cid' and coupon_code='$cc'";
		$m_etc->setCouponInfo($cid, $addColumn, $addWhere2);
		
		msg("삭제되었습니다.",$_SERVER[HTTP_REFERER]);
	}
	
break;

case "del_coupon_money":
	$addWhere = "where no = '$_GET[no]'";
	$data = $m_etc->getCouponSetInfo($cid, $addWhere);
	
	$cc = $data[coupon_code];
	$cich = $data[coupon_issue_code_history];
	$cam = $data[coupon_able_money];
	
	$data2 = $m_etc->getCouponInfo($cid, $cc);
	
	$cp = $data2[coupon_price];
	$coupon_price = $cam - $cp;
	
	if ($coupon_price < 0) {
		msg("이미 회원이 사용한 쿠폰이므로 삭제할 수 없습니다.", $_SERVER[HTTP_REFERER]);
	} else {
		$r_cich = explode("|", $cich);
		$r_cich = array_diff($r_cich,array($_GET[coupon_issue_code]));
		$cich = implode("|",$r_cich);
		
		list($coupon_issue_code) = array_slice($r_cich,-1,1);
		
		$addColumn = "set coupon_issue_yn = 0, mid = '', coupon_issuedt = null";
		$addWhere2 = "where coupon_issue_code = '$_GET[coupon_issue_code]'";
		$m_etc->setCouponIssueInfo($cid, $addColumn, $addWhere2);
		
		$addColumn2 = "set coupon_issue_code='$coupon_issue_code',coupon_issue_code_history='$cich',coupon_able_money='$coupon_price'";
		$addWhere3 = "where no = '$_GET[no]'";
		$m_etc->setCouponSetInfo($_GET[no], $addColumn2, $addWhere3);
		
		msg("삭제되었습니다.",$_SERVER[HTTP_REFERER]);
	}
	
break;

} /***/switch ($_POST[mode]){

### 이벤트설정
case "eventset":

	$flds = array(
		'eventRows'		=> $_POST[eventRows],
		'eventCells'	=> $_POST[eventCells],
		'eventHeader'	=> $_POST[eventHeader]
	);

	foreach($flds as $k=>$v){
		$query = "
		insert into exm_config set 
			cid		= '$cid',
			config	= '$k', 
			value	= '$v' 
		on duplicate key update
			value = '$v'";
		$db->query($query);
	}

	break;

### 이벤트 등록/수정
case "modEvent":
	$addWhere = "where eventno = '$_POST[eventno]'";
case "addEvent":
	$_POST[catno] = array_notnull($_POST[catno]);
	$_POST[catno] = $_POST[catno][count($_POST[catno])-1];
	if ($_POST[ea]) $_POST[ea] = implode(",", $_POST[ea]);
	### form 전송 취약점 개선 20160128 by kdk
    $_POST[contents] = addslashes(base64_decode($_POST[contents]));
	
	$addColumn = "
		cid			= '$cid',
		title		= '$_POST[title]',
		catno		= '$_POST[catno]',
		sdate		= '$_POST[sdate]',
		edate		= '$_POST[edate]',
		contents	= '$_POST[contents]',
		vtype		= '$_POST[vtype]',
		ea			= '$_POST[ea]',
		adminmemo	= '$_POST[adminmemo]',
		use_comment	= '$_POST[use_comment]'";
		
	if (!$_POST[eventno]) $addColumn .= ",regdt = now()";
	
	$m_etc->setEventInfo($cid, $addColumn, $addWhere, $_POST[eventno]);
	
	if (!$_POST[eventno]) $_POST[eventno] = mysql_insert_id();
	
	$dir = "../../data/event/";
	if (!is_dir($dir)) {
		mkdir($dir,0707);
		chmod($dir,0707);
	}
	
	$dir = "../../data/event/$cid/";
	if (!is_dir($dir)) {
		mkdir($dir,0707);
		chmod($dir,0707);
	}
	
	### 배너저장
	if (is_uploaded_file($_FILES[banner][tmp_name])) {
		move_uploaded_file($_FILES[banner][tmp_name], $dir.$_POST[eventno]);
		$size = getimagesize($dir.$_POST[eventno]);
		//if ($size[0] > 180) thumbnail($fname,$fname,"180");
	}
	
	if ($_POST[delbanner]) @unlink($dir.$_POST[eventno]);

	### 상품연결
	### 그룹상단이미지등록
	if ($_FILES[file][tmp_name]) {
		$_FILES[file][tmp_name] = array_notnull($_FILES[file][tmp_name]);
		
		foreach($_FILES[file][tmp_name] as $grpno=>$file) {
			if (is_uploaded_file($file)) {
				$fname = $dir.$_POST[eventno]."_".$grpno;
				move_uploaded_file($file, $fname);
			}
		}
	}
	
	### 그룹상단이미지삭제
	if ($_POST[delFile]) {
		foreach($_POST[delFile] as $grpno=>$v) {
			@unlink($dir.$_POST[eventno]."_".$grpno);
		}
	}

	$m_etc->setEventLink($cid, $_POST[eventno], $_POST[r_goodsno]);
	
	echo "<script>parent.location.href = 'event_list.php';</script>";
	
exit; break;

/**
### 이벤트 등록/수정
case "addEvent":

	list ($_POST[no]) = $db->fetch("select eventno from exm_event order by eventno desc limit 1",1);
	$_POST[no] += 1;
	$flds = "cid = '$cid',";

case "modEvent":

	if (!$_POST[r_goodsno]) $_POST[r_goodsno] = array();

	### 이벤트상품추출
	$query = "select * from exm_event_link where cid = '$cid' and eventno = '$_POST[no]'";
	$res = $db->query($query);
	$r_relgoods = array();
	while ($tmp = $db->fetch($res)){
		$ [] = $tmp[goodsno];
	}

	$diff = array_diff($r_relgoods,$_POST[r_goodsno]);
	$diff2 = array_diff($_POST[r_goodsno],$r_relgoods);

	foreach ($diff as $k=>$v){
		$db->query("delete from exm_event_link where cid = '$cid' and goodsno = '$v' and eventno = '$_POST[no]'");
	}
	foreach ($diff2 as $k=>$v){
		$db->query("insert into exm_event_link set cid='$cid', goodsno='$v', eventno='$_POST[no]'");
	}
	foreach ($_POST[r_goodsno] as $k=>$v){
		$db->query("update exm_event_link set `sort` = '$k' where cid = '$cid' and goodsno = '$v' and eventno = '$_POST[no]'");
	}

	$_POST[ea] = implode(",",$_POST[ea]);

	$flds .= "
	title		= '$_POST[title]',
	sdate		= '$_POST[sdate]',
	edate		= '$_POST[edate]',
	contents	= '$_POST[contents]',
	ea			= '$_POST[ea]',
	vtype		= '$_POST[vtype]'
	";
	
	list ($img) = $db->fetch("select banner from exm_event where eventno = '$_POST[no]'",1);

	$query = ($_POST[mode]=="addEvent")
			? "insert into exm_event set $flds"
			: "update exm_event set $flds where eventno = '$_POST[no]'";

	$db->query($query);

	if (is_uploaded_file($_FILES[banner][tmp_name])){
		@unlink("../../data/event/".$img);

		$name = getImageSize($_FILES[banner][tmp_name]);
		switch ($name[2]){
			case "1": $ext = "gif"; break;
			case "2": $ext = "jpg"; break;
			case "3": $ext = "png"; break;
			default	: msg("gif,jpg,png만 업로드가 가능합니다.",-1); break;
		}

		$name = time().".".$ext;
		move_uploaded_file($_FILES[banner][tmp_name],"../../data/event/".$name);
		$db->query("update exm_event set banner = '$name' where eventno = '$_POST[no]+1'");
	}

	break;

**/

case "getData":
	$s = $_POST[limit];
	$e = $_POST[pagenum];
	
	/*
	$db_table = "
		exm_goods a
		inner join exm_goods_cid b on a.goodsno = b.goodsno and b.cid='$cid'
		inner join exm_goods_link c on b.goodsno = c.goodsno
		";
	*/
	$db_table = "
	exm_goods a
	inner join exm_goods_cid b on a.goodsno = b.goodsno and b.cid = '$cid'";
	
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
		$where[] = "(usestock = 0 or (usestock=1 and totstock > 0))";
	}
	
	if ($_POST[nodp]) {
		$where[] = "nodp = 0";
	}

	if ($_POST[price1] && $_POST[price2]) {
		$where[count($where)-1] .= " having(cid_price >= '$_POST[price1]' and cid_price <= '$_POST[price2]')";
	} else if ($_POST[price1]) {
		$where[count($where)-1] .= " having(cid_price >= '$_POST[price1]')";
	} else if ($_POST[price2]) {
		$where[count($where)-1] .= " having(cid_price <= '$_POST[price2]')";
	}

	$fields = "*,if(b.price is null,a.price,b.price) cid_price";
	
	$data = $m_goods->getEventGoodsSearch($cid, $db_table, $fields, implode(" and ", $where), $s, $e);
	
	foreach ($data as $k=>$v) {
		$add = ($_POST[type] == "sort") ? "<input type='hidden' name='sort[$v[goodsno]]' value='$v[sort]'>" : "<input type='hidden' name='r_goodsno[$_POST[grpno]][]' value='$v[goodsno]'>";
		echo "<li>".goodsListImg($v[goodsno], "50", "border:1px solid #CCCCCC", $cid)."$v[goodsnm]{$add}</li>";
	}
	
exit; break;

### 이메일보내기
case "sendmail":

	### form 전송 취약점 개선 20160128 by kdk
    $_POST[contents] = addslashes(base64_decode($_POST[contents]));

	$_POST[to] = explode(";",$_POST[to]);

	include "../../lib/class.mail.php";

	$mail = new Mail($params);
	$headers['From']    = $cfg[emailAdmin];
	$headers['Name']	= $cfg[nameSite];
	$headers['Subject'] = $_POST[subject];

	echo "<style>body {margin:0;font:8pt tahoma}</style>";

	if ($_POST[p]==1){	// 전체 메일링
		
		$query = "select * from exm_member where apply_email='1'";
		$res = $db->query($query);

		while ($data=$db->fetch($res)){
			$headers['To']	= $data[email];
			$mail->send($headers, $_POST[contents]);
			if ($_POST[mode2]!="popup") echo "<b>[".(++$idx)."]</b> $data[email] - $data[name] <br><script>scroll(0,99999999)</script>";
			flush();
		}

	} else {
		foreach ($_POST[to] as $k=>$v){
			$headers['To']		= $v;
			$mail->send($headers, $_POST[contents]);

			if ($_POST[mode2]!="popup") echo "<b>[".(++$idx)."]</b> $v <span style='color:red'>[발송완료]</span></br>";
		}
	}

	if ($_POST[mode2]=="popup")	$idx = 1; echo "<script>this.close();opener.location.reload();</script>";
	emailLog($_POST,$idx);
	exit; break;

case "formmail":

	$flds = "
	cid		= '$cid', 
	formid	= '$_POST[formid]', 
	subject = '$_POST[subject]', 
	msg		= '$_POST[msg]',
	regdt	= now()
	";

	$query = (!$_POST[stype])
			? "insert into exm_formmail set $flds"
			: "update exm_formmail set $flds where no = '$_POST[no]'";
	$db->query($query);

	break;

case "coupon":

	if (!$_POST[modify]){

		list($chk) = $db->fetch("select coupon_code from exm_coupon where cid = '$cid' and coupon_code = '$_POST[coupon_code]' limit 1",1);
		if ($chk){
			msg("중복코드가 존재합니다.");
			exit;
		}

		if ($_POST[coupon_kind]=="off"){
			for ($i=0;$i<=9;$i++) $w[$i] = $i;
			for ($i=65;$i<=90;$i++) $w[chr($i)] = chr($i);
			$r_coupon_offcode = array();

			$query = "select coupon_offcode from exm_coupon where cid = '$cid' and coupon_kind = 'off'";
			$res = $db->query($query);
			while ($data = $db->fetch($res)){
				$r_coupon_offcode[] = $data[coupon_offcode];
			}

			$isok = false;
			while ($isok == false){
				$coupon_offcode = "";
				for ($i=0;$i<5;$i++){
					$coupon_offcode .= array_rand($w);
				}
				if (in_array($coupon_offcode,$r_coupon_offcode)){
					$isok = false;
				} else {
					$isok = true;
				}
			}
		}

		$query = "
		insert into exm_coupon set
		";

		$flds = "
		coupon_regdt			= now(),
		admin					= '$sess_admin[mid]',
		cid						= '$cid',
		coupon_code				= '$_POST[coupon_code]',
		coupon_kind				= '$_POST[coupon_kind]',
		coupon_offcode			= '$coupon_offcode',
		";

		$_POST[rurl] = "coupon_list.php?kind=$_POST[coupon_kind]";

	} else {
		$query = "
		update exm_coupon set
		";

		$where = "
		where
			cid				= '$cid'
			and coupon_code	= '$_POST[coupon_code]'
		";

	}

	$flds .= "
		coupon_name				= '$_POST[coupon_name]',
		coupon_type				= '$_POST[coupon_type]',
		coupon_way				= '$_POST[coupon_way]',
		coupon_issue_system		= '$_POST[coupon_issue_system]',
		coupon_issue_unlimit	= '$_POST[coupon_issue_unlimit]',
		coupon_issue_ea_limit	= '$_POST[coupon_issue_ea_limit]',
		coupon_min_ordprice		= '$_POST[coupon_min_ordprice]',
		coupon_period_system	= '$_POST[coupon_period_system]',
		coupon_range			= '$_POST[coupon_range]',
		adminmemo				= '$_POST[adminmemo]',
	";
	//debug($flds);
	if ($_POST[coupon_way]=="rate"){
		$flds .= "
		coupon_price		= null,
		coupon_rate			= '$_POST[coupon_rate]',
		coupon_price_limit	= '$_POST[coupon_price_limit]',
		";
	} else if ($_POST[coupon_way]=="price"){
		$flds .= "
		coupon_price		= '$_POST[coupon_price]',
		coupon_rate			= null,
		coupon_price_limit	= null,
		";
	} else if ($_POST[coupon_way]=="fdate"){
    $flds .= "
    coupon_price    = null,
    coupon_rate     = null,
    coupon_price_limit  = null,
    fix_extension_date  = '".$_POST[coupon_fix_month].",".$_POST[coupon_fix_day]."',
    ";
  }

	if ($_POST[coupon_issue_unlimit]==1){
		$flds .= "
		coupon_issue_sdate	= null,
		coupon_issue_edate	= null,
		";
	} else if ($_POST[coupon_issue_unlimit]==0){
		$flds .= "
		coupon_issue_sdate	= '$_POST[coupon_issue_sdate]',
		coupon_issue_edate	= '$_POST[coupon_issue_edate]',
		";
	}

	if ($_POST[coupon_issue_ea_limit]==0){
		$flds .= "
		coupon_issue_ea		= null,
		";
	} else if ($_POST[coupon_issue_ea_limit]==1){
		$flds .= "
		coupon_issue_ea		= '$_POST[coupon_issue_ea]',
		";
	}

	if ($_POST[coupon_period_system]=="date"){
		$flds .= "
		coupon_period_sdate			= '$_POST[coupon_period_sdate]',
		coupon_period_edate			= '$_POST[coupon_period_edate]',
		coupon_period_deadline		= null,
		coupon_period_deadline_date	= null,
		";
	} else if ($_POST[coupon_period_system]=="deadline"){
		$flds .= "
		coupon_period_sdate			= null,
		coupon_period_edate			= null,
		coupon_period_deadline		= '$_POST[coupon_period_deadline]',
		coupon_period_deadline_date	= null,
		";
	} else if ($_POST[coupon_period_system]=="deadline_date"){
		$flds .= "
		coupon_period_sdate			= null,
		coupon_period_edate			= null,
		coupon_period_deadline		= null,
		coupon_period_deadline_date	= '$_POST[coupon_period_deadline_date]',
		";
	}

	if ($_POST[coupon_range]=="all"){
		$flds .= "
		coupon_catno				= null,
		coupon_goodsno				= null
		";
	} else if ($_POST[coupon_range]=="category"){
		$coupon_catno = @implode(",",$_POST[coupon_catno]);
		$flds .= "
		coupon_catno				= '$coupon_catno',
		coupon_goodsno				= null
		";
	} else if ($_POST[coupon_range]=="goods"){
        $goodsno = array();
        foreach($_POST[r_goodsno] as $k=>$v){
            foreach($v as $vv){
                $goodsno[] = $vv;
            }
        }
        $coupon_goodsno = @implode(",",$goodsno);
		$flds .= "
		coupon_catno				= null,
		coupon_goodsno				= '$coupon_goodsno'
		";
	}

	$query = $query.$flds.$where;

	$db->query($query);
	
	if (!$_POST[rurl]) $_POST[rurl] = $_SERVER[HTTP_REFERER];
	echo "<script>parent.location.href = '".$_POST[rurl]."';</script>";

	exit; break;

case "chk_coupon_code":

	$query = "select coupon_code from exm_coupon where cid = '$cid' and coupon_code = '$_POST[coupon_code]'";
	list($chk) = $db->fetch($query,1);

	if (!$chk) echo "ok";
	else echo "duplicate";

	exit; break;

/*case "event_comment":

	### 유효성체크
	if (!$_POST[no]) break;
	if (!is_array($_POST[no])) $_POST[no] = array($_POST[no]);
	if (!is_numeric($_POST[emoney]) || $_POST[emoney]<=0) break;

	### 댓글중 이머니 미지급건 & 회원작성글 & 숨긴상태가아닐것 추출
	$tmp = "'".implode("','",$_POST[no])."'";
	$query = "
	select 
		a.*
	from 
		exm_event_comment a
		inner join exm_member b on a.cid = b.cid and a.mid = b.mid
	where 
		a.cid		= '$cid'
		and no in ($tmp)
		and a.emoney	=	0
		and a.mid		!=	''
		and !hidden
	";
	$res = $db->query($query);

	while ($data=$db->fetch($res)){
		### event_comment update
		$query = "
		update exm_event_comment 
		set
			admin		= '$sess_admin[mid]',
			emoney		= '$_POST[emoney]',
			emoneydt	= NOW()
		where
			no = '$data[no]'
		";
		$db->query($query);

		### 적립금지급
		set_emoney($data[mid],"[$data[no]] 덧글이벤트 - 적립금지급",$_POST[emoney]);
	}

	if (!$_POST[is_popupLayer]){
		msg("적립금이 정상적으로 적립되었습니다.","../promotion/event.comment.php");
	} else {
		msg("적립금이 정상적으로 적립되었습니다.");
		popupReload();
	}

	exit; break;*/
	
case "bottom_event_emoney":
	### 댓글중 이머니 미지급건 & 회원작성글 & 숨긴상태가아닐것 추출
	$addWhere = "where a.cid='$cid' and no in ($_POST[bno]) and a.emoney=0 and a.mid!='' and !hidden";
	$data = $m_etc->getEventCommentEmoney($cid, $addWhere);
	
	foreach ($data as $k=>$v) {
		### event_comment update
		$addColumn = "set admin = '$sess_admin[mid]',emoney	= '$_POST[emoney]',emoneydt	= now()";
		$addWhere2 = "where	no = '$v[no]'";
		$m_etc->setEventComment($cid, $addColumn, $addWhere2, $v[no]);
		
		### 적립금지급
		//set_emoney($v[mid], "[$v[no]] 덧글이벤트 - 적립금지급", $_POST[emoney]);
		setAddEmoney($cid, $v[mid], $_POST[emoney], " 댓글이벤트적립금 [$v[no]]", $sess_admin[mid]);
	}

	if (!$_POST[is_popup]) {
		msg("적립금이 정상적으로 적립되었습니다.", "../promotion/event_comment.php");
	} else {
		echo "<script>alert('"._("적립금이 정상적으로 적립되었습니다.")."');this.close();opener.location='event_comment.php';</script>";
	}
	
exit; break;

case "event_comment_hidden":
	if (!$_POST[no] || !is_numeric($_POST[hidden])) exit;
	$chg_hidden = ($_POST[hidden] == "1") ? "0" : "1";
	
	$addColumn = "set hidden = '$chg_hidden'";
	$addWhere = "where no = '$_POST[no]'";
	$m_etc->setEventComment($cid, $addColumn, $addWhere, $_POST[no]);
	
	echo $chg_hidden;
	
exit; break;
	
###출석 체크 이벤트
case "attend_event" :
	$query = "
	insert into md_attend_event set
		cid	= '$cid',
		etype	= '$_POST[etype]',
		title	= '$_POST[title]',
		sdate	= '$_POST[sdate]',
		edate	= '$_POST[edate]',
		emoney	= '$_POST[emoney]',
		count_tot	= '$_POST[count_tot]',
		count_seq	= '$_POST[count_seq]',
		add_emoney	= '$_POST[add_emoney]',
		emoney_expire_date	= '$_POST[emoney_expire_date]',
		msg1	= '$_POST[msg1]',
		msg2	= '$_POST[msg2]',
		regdt	= now()
	on duplicate key update
		cid	= '$cid',
		etype	= '$_POST[etype]',
		title	= '$_POST[title]',
		sdate	= '$_POST[sdate]',
		edate	= '$_POST[edate]',
		emoney	= '$_POST[emoney]',
		count_tot	= '$_POST[count_tot]',
		count_seq	= '$_POST[count_seq]',
		add_emoney	= '$_POST[add_emoney]',
		emoney_expire_date	= '$_POST[emoney_expire_date]',
		msg1	= '$_POST[msg1]',
		msg2	= '$_POST[msg2]',
		updatedt	= now()		
	";
    $db -> query($query);

    break;


}

if (!$_POST[rurl]) $_POST[rurl] = $_SERVER[HTTP_REFERER];
go($_POST[rurl]);

?>