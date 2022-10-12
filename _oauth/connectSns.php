<?
include "../lib/library.php";

$snsCode = $_REQUEST[sns_code];
//debug($snsCode);

### SNS 로그인 연동 / 2017.02.23 / kdk (네이버,카카오,페이스북)
if($cfg[sns_login]) {
	$cfg[sns_login] = unserialize($cfg[sns_login]);
	//debug($cfg[sns_login]);

	if($snsCode == "naver") { // $cfg[sns_login]['naver_login_use'] == "Y"
		//네이버 로그인
		$naver_client_id = $cfg[sns_login][naver_client_id];
		$naver_client_secret = $cfg[sns_login][naver_client_secret];
		//$naver_redirect_url = "http://210.96.184.229:8282/_oauth/callback_naver.php";
		//$domain = "http://210.96.184.229:8282";
		if( empty($_SERVER['HTTPS']) ){
			$naver_redirect_url = "http://$_SERVER[SERVER_NAME]/_oauth/callback_naver.php";
		}else{
			$naver_redirect_url = "http://$_SERVER[SERVER_NAME]/_oauth/callback_naver.php";
		}
		$domain = "http://$_SERVER[SERVER_NAME]";

		//echo("naver_client_id=".$naver_client_id."<br>");
		//echo("naver_client_secret=".$naver_client_secret."<br>");
		//echo("naver_redirect_url=".$naver_redirect_url."<br>");

		$scriptTag = '
		<script type="text/javascript" src="https://static.nid.naver.com/js/naverLogin_implicit-1.0.2.js" charset="utf-8"></script>
		<script type="text/javascript" src="//code.jquery.com/jquery-1.11.3.min.js"></script>
		';

		$bodyTag = '
	  	<!-- 네이버아이디로로그인 버튼 노출 영역 -->
	  	<div id="naver_id_login"></div>
	  	<script type="text/javascript">
		  	var naver_id_login = new naver_id_login("'.$naver_client_id.'", "'.$naver_redirect_url.'");
		  	var state = naver_id_login.getUniqState();
		  	naver_id_login.setButton("green", 3,40);
		  	naver_id_login.setDomain("'.$domain.'");
		  	naver_id_login.setState(state);
		  	naver_id_login.setPopup();
		  	naver_id_login.init_naver_id_login();

			//버튼 숨기기
			$("#naver_id_login").hide();

			//자동 버튼 클릭
			$("#naver_id_login_anchor").trigger("click");
	  	</script>
		';
	}
	else if($snsCode == "kakao") { // $cfg[sns_login]['kakao_login_use'] == "Y"
		$kakao_rest_api_key = $cfg[sns_login][kakao_rest_api_key];
		$kakao_javascript_key = $cfg[sns_login][kakao_javascript_key];
		//echo("kakao_rest_api_key=".$kakao_rest_api_key."<br>");
		//echo("kakao_javascript_key=".$kakao_javascript_key."<br>");

		$scriptTag = '
		<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
		<script src="https://developers.kakao.com/sdk/js/kakao.min.js"></script>
		<script>
			$(document).ready(function(){
				//사용할 앱의 JavaScript 키를 설정해 주세요.
				Kakao.init("'.$kakao_javascript_key.'");
	
				function getKakaotalkUserProfile(){
					Kakao.API.request({
						url: "/v2/user/me",
						success: function(res) {
							//console.log(res);
							$("#kakao-profile").append(res.id);
							$("#kakao-profile").append(res.properties.nickname);
							$("#kakao-profile").append($("<img/>",{"src":res.properties.profile_image,"alt":res.properties.nickname+"님의 프로필 사진"}));
							$("#kakao-profile").append($("<img/>",{"src":res.properties.thumbnail_image,"alt":res.properties.nickname+"님의 프로필 사진"}));
							
						    //프로필 정보를  DB처리 하고 로그인 페이지로 이동하자.
							$.post("indb.php", {
									mode:"kakao",
									id:res.id,
									name:"",
									email:"",
									nickname:res.properties.nickname
								}, function(data) {
								if (data){
									//alert(data);
									if (data == "login_success") {
										//로그인 성공 메인페이지로 이동.
										var url = "/main/index.php";
										goParentUrl(url);
										self.close();
									}
									else {
										//회원 가입을 위한 약관 동의 페이지로 이동.
										var sns_data = {};
										sns_data["mid"] = res.id;
										sns_data["name"] = "";
										sns_data["type"] = "kakao";
										sns_data["email"] = "";
										sns_data["nickname"] = res.properties.nickname;
										sns_data = JSON.stringify(sns_data);
			
										//전송 데이터 인코딩
										sns_data = Base64.encode(sns_data);
										  
										// get으로 인코딩 문자 전달시 +,& 기호 전달 안되는 문제점 개선 200110 jtkim
										sns_data = sns_data.replace("+","%2B");
										sns_data = sns_data.replace("&","%26");
								
			      						//console.log(sns_data);
			      						
										var url = "/service/sns_login_agree.php?data=" + sns_data;
										goParentUrl(url);
										self.close();
									}
								} else {
									alert("실패했습니다.[" + data + "]");
								}
							});
	
						},
						fail: function(error) {
							console.log(error);
						}
					});
				}
	
				function createKakaotalkLogin(){
					$("#kakao-logged-group .kakao-logout-btn,#kakao-logged-group .kakao-login-btn").remove();
					var loginBtn = $("<a/>",{"id":"kakao_id_login_anchor","class":"kakao-login-btn","text":"로그인"});
					loginBtn.click(function(){
						Kakao.Auth.login({
							persistAccessToken: true,
							persistRefreshToken: true,
							success: function(authObj) {
								getKakaotalkUserProfile();
								createKakaotalkLogout();
							},
							fail: function(err) {
								console.log(err);
							}
						});
					});
					$("#kakao-logged-group").prepend(loginBtn)
				}
	
				function createKakaotalkLogout(){
					$("#kakao-logged-group .kakao-logout-btn,#kakao-logged-group .kakao-login-btn").remove();
					var logoutBtn = $("<a/>",{"class":"kakao-logout-btn","text":"로그아웃"});
					logoutBtn.click(function(){
						Kakao.Auth.logout();
						createKakaotalkLogin();
						$("#kakao-profile").text("");
					});
					$("#kakao-logged-group").prepend(logoutBtn);
				}
	
				if(Kakao.Auth.getRefreshToken()!=undefined&&Kakao.Auth.getRefreshToken().replace(/ /gi,"")!=""){
					createKakaotalkLogout();
					getKakaotalkUserProfile();
				}else{
					createKakaotalkLogin();
					//자동 버튼 클릭
					$("#kakao_id_login_anchor").trigger("click");
				}
			});
		</script>
		';

		$bodyTag = '
		<div id="kakao-logged-group"></div>
		<div id="kakao-profile"></div>
		';

	}
	else if ($snsCode == "facebook") { //$cfg[sns_login]['facebook_login_use'] == "Y"
		$facebook_app_id = $cfg[sns_login][facebook_app_id];
		//echo("facebook_app_id=".$facebook_app_id."<br>");

		$scriptTag = '
		<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
		<script>
			function statusChangeCallback(response) {
				if (response.status === "connected") {
					// 페이스북을 통해서 로그인이 되어있다.
					//testAPI();
				} else if (response.status === "not_authorized") {
					// 페이스북에는 로그인 했으나, 앱에는 로그인이 되어있지 않다.
					document.getElementById("status").innerHTML = "Please log " + "into this app.";
				} else {
					// 페이스북에 로그인이 되어있지 않다. 따라서, 앱에 로그인이 되어있는지 여부가 불확실하다.
					document.getElementById("status").innerHTML = "Please log " + "into Facebook.";
				}
			}

			function checkLoginState() {
				FB.getLoginStatus(function(response) {
					statusChangeCallback(response);
				});
			}

			window.fbAsyncInit = function() {
				FB.init({
					appId : "'.$facebook_app_id.'",
					cookie : true,
					xfbml : true,
					version : "v2.8"					
				});

				FB.getLoginStatus(function(response) {
					statusChangeCallback(response);
				});
				
				FB.login(function(response) {
    				if (response.authResponse) {
						FB.api("/me",{fields: "name,email"}, function(response) {
							document.getElementById("status").innerHTML = "Thanks for logging in, " + response.id + "!, " + response.name + "!, " + response.email + "!";
						    //프로필 정보를  DB처리 하고 로그인 페이지로 이동하자.
							$.post("indb.php", {
									mode:"facebook",
									id:response.id,
									name:response.name,
									email:response.email,
									nickname:""
								}, function(data) {
								if (data){
									//alert(data);
									if (data == "login_success") {
										//로그인 성공 메인페이지로 이동.
										var url = "/main/index.php";
										goParentUrl(url);
										self.close();
									}
									else {
										//회원 가입을 위한 약관 동의 페이지로 이동.
										var sns_data = {};
										sns_data["mid"] = response.id;
										sns_data["name"] = response.name;
										sns_data["type"] = "facebook";
										sns_data["email"] = response.email;
										sns_data["nickname"] = "";
										sns_data = JSON.stringify(sns_data);
			
										//전송 데이터 인코딩
			      						sns_data = Base64.encode(sns_data);
			      						//console.log(sns_data);
			      						
										var url = "/service/sns_login_agree.php?data=" + sns_data;
										goParentUrl(url);
										self.close();
									}									
								} else {
									alert("실패했습니다.[" + data + "]");
								}
							});
														
						});
	     				
    				} else {
     					//console.log("User cancelled login or did not fully authorize.");
    				}
				});
			};

			// SDK를 비동기적으로 호출
			( function(d, s, id) {
					var js, fjs = d.getElementsByTagName(s)[0];
					if (d.getElementById(id))
						return;
					js = d.createElement(s);
					js.id = id;
					js.src = "//connect.facebook.net/ko_KR/all.js";
					fjs.parentNode.insertBefore(js, fjs);
				}(document, "script", "facebook-jssdk"));

			// 로그인이 성공한 다음에는 간단한 그래프 API를 호출한다.
			// 이 호출은 statusChangeCallback()에서 이루어진다.
			function testAPI() {
				FB.api("/me",{fields: "name,email"}, function(response) {
					document.getElementById("status").innerHTML = "Thanks for logging in, " + response.id + "!, " + response.name + "!, " + response.email + "!";
				});
			}
		</script>
		';

		$bodyTag = '
		<!--<fb:login-button id="fblogin" scope="public_profile,email" onlogin="checkLoginState();"></fb:login-button>-->
		<div id="status"></div>
		';
  // kidsnote Oauth2 로그인 작업 jtkim 210823
	}	else if ($snsCode == "kidsnote") {
	  $kidsnote_client_url = $cfg[sns_login][kidsnote_client_url]."o/authorize/";
	  $kidsnote_client_id = $cfg[sns_login][kidsnote_client_id];
	  $kidsnote_client_secret = $cfg[sns_login][kidsnote_client_secret];
	  $kidsnote_redirect_uri = "http://".$_SERVER['SERVER_NAME']."/_oauth/callback_kidsnote.php";

	  $scriptTag = '
            <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
      ';
	  $bodyTag = '
      <script type="text/javascript">
      $(document).ready(function(){
        var kidsnote_client_url = "'.$kidsnote_client_url.'";
        var kidsnote_client_id = "'.$kidsnote_client_id.'";
        var kidsnote_redirect_uri = "'.$kidsnote_redirect_uri.'";
        var kidsnote_client_secret = "'.$kidsnote_client_secret.'";
        // var kidsnoteOAuthUrl = kidsnote_client_url + "?client_id=" + kidsnote_client_id +"&client_secret=" + kidsnote_client_secret + "&redirect_uri=" + kidsnote_redirect_uri +"&response_type=code";
        var kidsnoteOAuthUrl = kidsnote_client_url + "?client_id=" + kidsnote_client_id + "&redirect_uri=" + kidsnote_redirect_uri +"&response_type=code";
        // console.log(kidsnoteOAuthUrl);
        location.href = kidsnoteOAuthUrl;
      });
                </script>
    ';

  }
}

//debug($scriptTag);
//debug($bodyTag);
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
  	<?=$bodyTag?>

	<script>
	function goParentUrl(url){
		parent.document.location.href = url;
	}
	</script>
</body>
</html>
