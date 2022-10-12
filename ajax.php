<?

header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");

include "lib/library.php";

/**/switch ($_POST[mode]){

case "category":

	$loop = array();

	if (!$_POST[cid]) $_POST[cid] = $cid;

	if (!$_POST[catno] && $_POST[depth]>0){
		echo "[]"; exit;
	}

	$len = ($_POST[depth]+1)*3;
	$where[] = "cid = '$_POST[cid]'";
	if ($_POST[catno]){
		$where[] = "catno like '$_POST[catno]%'";
	}
	$where[] = "length(catno) = '$len'";

	if ($_POST[front]==1) $where[] = "hidden=0";

	if ($where) $_where = "where ".implode(" and ",$where);
	$query = "select * from exm_category $_where order by length(catno),sort";
	$res = $db->query($query);
	while ($data=$db->fetch($res)){
		$data[catnm] = addslashes($data[catnm]);
		$loop[] = "$data[catno]|$data[catnm]";
	}
	echo "['".implode("','",$loop)."']";

	exit; break;

case "mainpopup":

	$query = "select * from exm_popup where cid = '$cid' and popupno='$_POST[popupno]'";
	$data = $db->fetch($query);

	$data[content] = str_replace(chr(13),"",$data[content]);
	$data[content] = str_replace(chr(10),"",$data[content]);
	$data[content] = addslashes($data[content]);
	echo "{'title':'$data[title]','content':'$data[content]'}";

	exit; break;

case "mobile_get_cart_count":
	
	//20160530 / minks / 편집 보관일이 지난 주문 삭제 추가
	if (!$cfg[source_save_days]) $cfg[source_save_days] = 15;
    $db->query("delete from exm_cart where cid='$cid' and date_format(updatedt,'%Y-%m-%d') <= adddate(curdate(), interval-$cfg[source_save_days] day)");
	
	if ($sess[mid]) $query = "select count(*) as cart_cnt from exm_cart where cid='$cid' and mid='$sess[mid]'";
	else $query = "select count(*) as cart_cnt from exm_cart where cid='$cid' and mid='' and cartkey='$_COOKIE[cartkey]'";
	$data = $db->fetch($query);
	
	echo "$data[cart_cnt]";
	
	exit; break;
	
case "mobile_get_page_title":
	
	$file_path = $_POST[file_path];
	
	//쿼리문에서 검색할 때 필요한 파라미터 추출
	if (strpos($file_path, "?") !== false) {
		$param = explode("?", $file_path);
		if (strpos($param[1], "&") !== false) {
			$param2 = explode("&", $param[1]);
			foreach ($param2 as $param_k => $param_v) {
				if (strpos($param_v, "=") !== false) {
					$param3 = explode("=", $param_v);
					$file_param[$param3[0]] = $param3[1];
				}
			}
		} else {
			if (strpos($param[1], "=") !== false) {
				$param2 = explode("=", $param[1]);
				$file_param[$param2[0]] = $param2[1];
			}
		}
	}
	
	if (strpos($file_path, "/board/list.php") !== false) {
		$query = "select * from exm_board_set where cid='$cid' and board_id='$file_param[board_id]'";
		$data = $db->fetch($query);
		echo $data[board_name];
	} else if (strpos($file_path, "/goods/list.php") !== false || strpos($file_path, "/goods/main.php") !== false) {
		$query = "select * from exm_category where cid='$cid' and catno='$file_param[catno]'";
		$data = $db->fetch($query);
		echo $data[catnm];
	} else if (strpos($file_path, "/goods/view.php") !== false) {
		$query = "select * from exm_goods a inner join exm_goods_cid b on a.goodsno=b.goodsno where b.cid='$cid' and a.goodsno='$file_param[goodsno]'";
		$data = $db->fetch($query);
		echo $data[goodsnm];
	} else if (strpos($file_path, "/mypage/orderlist.php") !== false || strpos($file_path, "/mypage/orderview.php") !== false) {
		echo _("주문배송조회");
	} else if (strpos($file_path, "/order/cart.php") !== false) {
		echo _("장바구니");
	} else if (strpos($file_path, "/order/order.php") !== false || strpos($file_path, "/order/payment.php") !== false || strpos($file_path, "/order/payend.php") !== false || strpos($file_path, "/order/payfail.php") !== false) {
		echo _("주문/결제");
	} else if (strpos($file_path, "/service/faq.php") !== false) {
		echo _("자주묻는질문");
	} else if (strpos($file_path, "/member/leave.php") !== false) {
		echo _("회원탈퇴");
	} else if (strpos($file_path, "/member/login.php") !== false) {
		echo _("로그인");
	} else if (strpos($file_path, "/member/myinfo.php") !== false) {
		echo _("회원정보수정");
	} else if (strpos($file_path, "/member/register.php") !== false) {
		echo _("회원가입");
	} else if (strpos($file_path, "/member/reminderid.php") !== false) {
		echo _("아이디찾기");
	} else if (strpos($file_path, "/member/reminderpw.php") !== false) {
		echo _("비밀번호찾기");
	} else if (strpos($file_path, "/mypage/coupon.php") !== false) {
		echo _("쿠폰/적립금 관리");
	} else if (strpos($file_path, "/mypage/mycs.php") !== false || strpos($file_path, "/mypage/mycs_write.php") !== false) {
		echo _("1:1문의");
	} else {
		echo $cfg[nameSite];
	}
	
	exit; break;
	
case "mobile_set_registration_id":
	
	//사용자ID 체크후 DB처리
	list($cnt) = $db->fetch("select count(*) as cnt from tb_mobile_registration_id where cid = '$cid' and registration_id = '$_POST[registration_id]'", 1);
    
    //iOS 사용자ID DB 연동 및 DB userAgent 정보 추가 / kdk
	if ($cnt == 0) $db->query("insert into tb_mobile_registration_id set cid = '$cid', registration_id = '$_POST[registration_id]', regdt = now(), uatype = '$_POST[uatype]'");
	
	exit; break;

/**/}

?>