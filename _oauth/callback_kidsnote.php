<?
include "../lib/library.php";
// 네이버 사용자 프로필 조회 이후 프로필 정보를 처리할 callback function

if ($cfg[sns_login] && $_GET['code']) {
	$cfg[sns_login] = unserialize($cfg[sns_login]);
	$error = false;

	$kidsnote_client_url = $cfg[sns_login][kidsnote_client_url]."o/token/";
	$kidsnote_client_id = $cfg[sns_login][kidsnote_client_id];
	$kidsnote_client_secret = $cfg[sns_login][kidsnote_client_secret];
	$kidsnote_redirect_uri = "http://".$_SERVER['SERVER_NAME']."/_oauth/callback_kidsnote.php";
	$kidsnote_code = $_GET['code'];

	$kidsnote_me_url = $cfg[sns_login][kidsnote_client_url]."v1/me/";
	$kidsnote_logout_url = $cfg[sns_login][kidsnote_client_url]."o/revoke_token/";

	// debug($kidsnote_client_url);

	$data = array(
		'grant_type' => 'authorization_code',
		'client_id' => $kidsnote_client_id,
		'code' => $kidsnote_code,
		'redirect_uri' => $kidsnote_redirect_uri
	);
	$header_data = array(
		'Content-Type' => 'application/x-www-form-urlencoded'
	);

	$kidsnote_token_str = 'grant_type='.urlencode('authorization_code'). '&client_id='.urlencode($kidsnote_client_id). '&code='.urlencode($kidsnote_code). '&redirect_uri='.urlencode($kidsnote_redirect_uri). '&client_secret='.urlencode($kidsnote_client_secret);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $kidsnote_client_url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header_data);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $kidsnote_token_str);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$token_res = curl_exec($ch);
	curl_close($ch);

	$token_res_arr = json_decode($token_res, true);
	// debug($token_res_arr);

	if ($token_res_arr[error]) {
		// error
		// debug('error');
		$error = "토큰 발급 실패";
	}else{
	  //print_r($token_res_arr);
	  //에러가 없을시 토큰값 쿠키 생성
	  $kidsnote_expires_time = date("Y-m-d H:i:s", strtotime("+".$token_res_arr[expires_in]." seconds"));
	  setCookie('kidsnote_access_token', $token_res_arr[access_token], 0, '/','');
	  setCookie('kidsnote_expires_in', $kidsnote_expires_time,0,'/','');
	  setCookie('kidsnote_token_type', $token_res_arr[token_type], 0, '/','');
//	  debug($_COOKIE); exit;
  }

	// 회원정보 가져오기
	if($token_res[access_token]){
		$header_data2[] = 'Content-Type: application/json';
		$header_data2[] = 'Authorization: '.$token_res_arr[token_type]." ".$token_res_arr[access_token];

		$ch_ = curl_init();
		curl_setopt($ch_, CURLOPT_URL, $kidsnote_me_url);
		curl_setopt($ch_, CURLOPT_HTTPHEADER, $header_data2);
		curl_setopt($ch_, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch_, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch_, CURLOPT_RETURNTRANSFER, true);
		$me_res = curl_exec($ch_);
		curl_close($ch_);
		$me_res_arr = json_decode($me_res, true);
	    // debug($me_res);
	    if($me_res_arr['error']){
	    	$error = "회원정보 가져오기 실패";
	    }

	    if(is_array($me_res_arr[picture])){
        $me_res_arr[picture] = base64_encode(json_encode($me_res_arr[picture]));
      }
	}



	if(!$error){
		$scriptTag = '
		<script src="http://code.jquery.com/jquery-1.11.2.min.js"></script>
		<script>
			$(document).ready(function(){
				  $.post("indb.php", {
						mode:"kidsnote",
						id:"'.$me_res_arr[id].'",
						name:"'.$me_res_arr[username].'",
						email:"",
						nickname:"'.$me_res_arr[name].'",
						picture:"'.$me_res_arr[picture].'",
						user_type:"'.$me_res_arr[type].'"
					}, function(data) {
						if (data){
							//alert(data);
							if (data == "login_success") {
								//로그인 성공 메인페이지로 이동.
								// var url = "/main/index.php";
								// goParentUrl(url);
								opener.document.location.href = "/main/index.php";
								self.close();
							}
						} else {
							alert("실패했습니다.[" + data + "]");
							self.close();
						}
					});
			})
		</script>';
	}else{
		$scriptTag = '
		<script src="http://code.jquery.com/jquery-1.11.2.min.js"></script>
		<script>
			$(document).ready(function(){
			  alert("'.$error.'");
			  self.close();
			})
		</script>';
	}


    //로그아웃 정책 미정
	//logout 
	/*
	$kidsnote_encode = $kidsnote_client_id.":".$kidsnote_client_secret;
	$kidsnote_encode_res = base64_encode($kidsnote_encode);
	$header_data3[] = 'Content-Type: application/x-www-form-urlencoded';
	$header_data3[] = 'Authorization: Basic '.$kidsnote_encode_res;
	$kidsnote_logout_str = 'token='.$token_res_arr[access_token];

	$ch_logout = curl_init();
	curl_setopt($ch_logout, CURLOPT_URL, $kidsnote_logout_url);
	curl_setopt($ch_logout, CURLOPT_HTTPHEADER, $header_data3);
	curl_setopt($ch_logout, CURLOPT_POST, true);
	curl_setopt($ch_logout, CURLOPT_POSTFIELDS, $kidsnote_logout_str);
	curl_setopt($ch_logout, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch_logout, CURLOPT_RETURNTRANSFER, 1);
	$logout_res = curl_exec($ch_logout);
	curl_close($ch_logout);

	$logout_res_arr = json_decode($logout_res, true);
	// debug($logout_res_arr);
	$ch_ = curl_init();
	curl_setopt($ch_, CURLOPT_URL, $kidsnote_me_url);
	curl_setopt($ch_, CURLOPT_HTTPHEADER, $header_data2);
	curl_setopt($ch_, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch_, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch_, CURLOPT_RETURNTRANSFER, true);
	$me_res = curl_exec($ch_);
	$header_info = curl_getinfo($ch_);
	// debug($header_info);
	curl_close($ch_);
	// debug($me_res);
	*/
}

?>

<!doctype html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<title>SNS 로그인</title>
	<script src="../js/webtoolkit.base64.js"></script>
	<?=$scriptTag?>
</head>
<body>

<script>
  function goParentUrl(url){
    parent.document.location.href = url;
  }
</script>
</body>
</html>
