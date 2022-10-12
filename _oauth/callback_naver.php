<?
include "../lib/library.php";
// 네이버 사용자 프로필 조회 이후 프로필 정보를 처리할 callback function
if($cfg[sns_login]) {
	$cfg[sns_login] = unserialize($cfg[sns_login]);
	//debug($cfg[sns_login]);
	
	$naver_client_id = $cfg[sns_login][naver_client_id];
	$naver_client_secret = $cfg[sns_login][naver_client_secret];
	//$naver_redirect_url = "http://210.96.184.229:8282/_oauth/callback_naver.php";	
	//$naver_redirect_url = "http://$_SERVER[SERVER_NAME]/_oauth/callback_naver.php";	

	if($_SERVER['HTTPS'] == 'on'){
		$naver_redirect_url = "https://$_SERVER[SERVER_NAME]/_oauth/callback_naver.php";	
	}else{
		$naver_redirect_url = "http://$_SERVER[SERVER_NAME]/_oauth/callback_naver.php";
	}
	$naver_redirect_url = "https://$_SERVER[SERVER_NAME]/_oauth/callback_naver.php";
	$domain = "http://$_SERVER[SERVER_NAME]";
	//$domain = "http://210.96.184.229:8282";
}	
?>
<!doctype html>
<html lang="ko">
	<head>
		<script src="../js/webtoolkit.base64.js"></script>
<!-- <? echo $_SERVER['HTTPS']; ?>-->
		<script type="text/javascript" src="https://static.nid.naver.com/js/naverLogin_implicit-1.0.2.js" charset="utf-8"></script>
		<script type="text/javascript" src="//code.jquery.com/jquery-1.11.3.min.js"></script>
	</head>
	<body>
		<script type="text/javascript">
			var naver_id_login = new naver_id_login("<?=$naver_client_id?>", "<?=$naver_redirect_url?>");
			// 접근 토큰 값 출력
			//console.log(naver_id_login.oauthParams.access_token);
			
			// 네이버 사용자 프로필 조회
			naver_id_login.get_naver_userprofile("naverSignInCallback()");
			
			// 네이버 사용자 프로필 조회 이후 프로필 정보를 처리할 callback function
			function naverSignInCallback() {
				//console.log(naver_id_login.getProfileData('email'));
				//console.log(naver_id_login.getProfileData('nickname'));
				//console.log(naver_id_login.getProfileData('age'));
				//console.log(naver_id_login.getProfileData('id'));
				//console.log(naver_id_login.getProfileData('name')); //name 호출 API 이슈 (http://forum.developers.naver.com/t/javascript/14157)
				//console.log(naver_id_login.getProfileData('enc_id'));
				//console.log(naver_id_login.getProfileData('gender'));
				//console.log(naver_id_login.getProfileData('birthday'));
				//console.log(naver_id_login.getProfileData('profile_image'));
				
				if(naver_id_login.getProfileData('email') == "") {
					alert('이메일 주소가 없습니다.다시 시도해주세요.');
					return false;
				}
				//프로필 정보를  DB처리 하고 로그인 페이지로 이동하자.
				$.post("indb.php", {
					mode : "naver",
					id : naver_id_login.getProfileData('id'),
					name : naver_id_login.getProfileData('name'),
					email : naver_id_login.getProfileData('email'),
					nickname : naver_id_login.getProfileData('nickname')
				}, function(data) {
					if (data) {
						//alert(data);
						if (data == "login_success") {
							//로그인 성공 메인페이지로 이동.
							var url = "/main/index.php";
							
							//20180618 / minks / 모바일스킨의 경우 redirect로 띄우므로 분기처리
							if ('<?=$cfg[skin]?>' == "m_default") {
								document.location.href = url;
							} else {
								window.opener.goParentUrl(url);
								window.close();
							}
						}
						else {
							//회원 가입을 위한 약관 동의 페이지로 이동.
							var sns_data = {};
							sns_data["mid"] = naver_id_login.getProfileData('id');
							sns_data["name"] = naver_id_login.getProfileData('name');
							sns_data["type"] = "naver";
							sns_data["email"] = naver_id_login.getProfileData('email');
							sns_data["nickname"] = naver_id_login.getProfileData('nickname');
							sns_data = JSON.stringify(sns_data);

							//전송 데이터 인코딩
      						sns_data = Base64.encode(sns_data);
      						//console.log(sns_data);
      						
							var url = "/service/sns_login_agree.php?data=" + sns_data;
							
							//20180618 / minks / 모바일스킨의 경우 redirect로 띄우므로 분기처리
							if ('<?=$cfg[skin]?>' == "m_default") {
								document.location.href = url;
							} else {
								window.opener.goParentUrl(url);
								window.close();
							}
						}
					} else {
						alert('실패했습니다.[' + data + ']');
					}
				});
			}
		</script>
	</body>
</html>
