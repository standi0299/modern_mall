<?
// 카카오 알림톡 Aligo 서비스 200429 jtkim

class KakaoAlimtalkAligo
{

    var $api_key;
    var $api_id;
    var $sender_key;
    var $token;

    var $tokenURL = 'http://kakaoapi.aligo.in/akv10/token/create/300/s';
    var $templateURL = 'http://kakaoapi.aligo.in/akv10/template/list/';
    var $sendUrl = 'http://kakaoapi.aligo.in/akv10/alimtalk/send/';
    
    var $cid;
    var $sendNumber;

    function KakaoAlimtalkAligo(){
        $this->api_key = $GLOBALS[cfg][alimtalk_api_key];
        $this->sender_key = $GLOBALS[cfg][alimtalk_sender_key];
        $this->api_id = $GLOBALS[cfg][alimtalk_api_id];        
        $this->token = $this->alimtalk_aligo_get_token();
        $this->cid = $GLOBALS[cid];
        $this->sendNumber = $GLOBALS[cfg][alimtalk_sender_number];
    }

    // 카카오 알림톡 Aligo 서비스 GET token
    function alimtalk_aligo_get_token(){
        $tokenData = array(
            'apikey' => $this->api_key,
            'userid' => $this->api_id
        );

        $token_res = $this->curl_return($this->tokenURL,$tokenData);

        if($token_res[code] == 0){
            return $token_res[token];            
        }
        /*
        else{
            return "token 생성 오류" + $token_res[message];
        }
        */
    }

    // 카카오 알림톡 Aligo 서비스 GET template
    function alimtalk_aligo_get_template($tpl_code =""){
        $templateData = array(
            'apikey' => $this->api_key,
            'userid' => $this->api_id,
            'token' => $this->token,
            'senderkey' => $this->sender_key,
        );

        //tpl_code 없이 요청시 template 전체리스트 리턴
        if($tpl_code)  $templateData['tpl_code'] = $tpl_code;

        $template_res = array();
        $template_res = $this->curl_return($this->templateURL, $templateData);


        if($template_res[code] == 0){
            return $template_res;
        }
        /*
        else{
            return "template 조회 오류" + $template_res[message];
        }
        */

    }

    // 카카오 알림톡 Aligo 서비스 알림톡 발송
    function auto_alimtalk_aligo($from,$tpl_code,$arr){

    /* 
    -----------------------------------------------------------------------------------
    알림톡 전송
    -----------------------------------------------------------------------------------
    버튼의 경우 템플릿에 버튼이 있을때만 버튼 파라메더를 입력하셔야 합니다.
    버튼이 없는 템플릿인 경우 버튼 파라메더를 제외하시기 바랍니다.
    */

    $res = $this->alimtalk_aligo_get_template($tpl_code);

    if($res['code'] == 0){
        $msg = $res['list'][0]['templtContent'];
        $msg_ = $this->parse_string($msg,$arr);


        $aligo_button_string = "";
        // 버튼이 있을경우
        if(!empty($res['list'][0]['buttons'])){            
            // button data 전송시 string type으로 보내야해서 string으로 변경
            $aligo_button_string = '{"button":[';                            
            foreach($res['list'][0]['buttons'] as $k => $v)
            {
                if($v["ordering"]) unset($v["ordering"]);

                // Template response에서 값이 없는경우 없애기
                foreach($v as $kk => $vv){
                    if(!$vv) unset($v[$kk]);
                 }            
                 if($k == 0){
                    $aligo_button_string .=  str_replace('\\/','/',to_han (json_encode($v)));
                 }else if($k > 0 ){
                    $aligo_button_string .=  ",".str_replace('\\/','/',to_han (json_encode($v)));
                 }
            
            }  
            $aligo_button_string .= ']}';
        }

    }else{
        $msg_ = "";
    }
    
    $template_subject = $res['list'][0]['templtName'];

    $sendData =	array(
        'apikey'      => $this->api_key, 
        'userid'      => $this->api_id,// '사용중이신 아이디', 
        'token'       => $this->token,//'생성한 토큰 문자열', 
        'senderkey'   => $this->sender_key,//'발신프로필키', 
        'tpl_code'    => "$tpl_code",//'전송할 템플릿 코드',
        'sender'      => $this->sendNumber,
        // 'senddate'    => date("YmdHis", strtotime("+10 minutes")),
        'receiver_1'  => $from,
        'recvname_1'  => 'ilark',
        'subject_1'   => $template_subject,
        'message_1'   => $msg_,
    //  'button_1'    => '{"button":[{"name":"테스트 버튼","linkType":"DS"}]}', // 템플릿에 버튼이 없는경우 제거하시기 바랍니다.
    // 'receiver_2'  => '첫번째 알림톡을 전송받을 휴대폰 번호',
    // 'recvname_2'  => '첫번째 알림톡을 전송받을 사용자 명',
    // 'subject_2'   => '첫번째 알림톡을 제목',
    // 'message_2'   => '첫번째 템플릿내용을 기초로 작성된 전송할 메세지 내용',
    //  'button_2'    => '{"button":[{"name":"테스트 버튼","linkType":"DS"}]}' // 템플릿에 버튼이 없는경우 제거하시기 바랍니다.
    );

    if(!empty($aligo_button_string)){
        $sendData['button_1'] = $aligo_button_string;
    }

    /*

    -----------------------------------------------------------------
    치환자 변수에 대한 처리
    -----------------------------------------------------------------

    등록된 템플릿이 "#{이름}님 안녕하세요?" 일경우
    실제 전송할 메세지 (message_x) 에 들어갈 메세지는
    "홍길동님 안녕하세요?" 입니다.

    카카오톡에서는 전문과 템플릿을 비교하여 치환자이외의 부분이 일치할 경우
    정상적인 메세지로 판단하여 발송처리 하는 관계로
    반드시 개행문자도 템플릿과 동일하게 작성하셔야 합니다.

    예제 : message_1 = "홍길동님 안녕하세요?"

    -----------------------------------------------------------------
    버튼타입이 WL일 경우 (웹링크)
    -----------------------------------------------------------------
    링크정보는 다음과 같으며 버튼도 치환변수를 사용할 수 있습니다.
    {"button":[{"name":"버튼명","linkType":"WL","linkP":"https://www.링크주소.com/?example=12345", "linkM": "https://www.링크주소.com/?example=12345"}]}

    -----------------------------------------------------------------
    버튼타입이 AL 일 경우 (앱링크)
    -----------------------------------------------------------------
    {"button":[{"name":"버튼명","linkType":"AL","linkI":"https://www.링크주소.com/?example=12345", "linkA": "https://www.링크주소.com/?example=12345"}]}

    -----------------------------------------------------------------
    버튼타입이 DS 일 경우 (배송조회)
    -----------------------------------------------------------------
    {"button":[{"name":"버튼명","linkType":"DS"}]}

    -----------------------------------------------------------------
    버튼타입이 BK 일 경우 (봇키워드)
    -----------------------------------------------------------------
    {"button":[{"name":"버튼명","linkType":"BK"}]}

    -----------------------------------------------------------------
    버튼타입이 MD 일 경우 (메세지 전달)
    -----------------------------------------------------------------
    {"button":[{"name":"버튼명","linkType":"MD"}]}

    -----------------------------------------------------------------
    버튼이 여러개 인경우 (WL + DS)
    -----------------------------------------------------------------
    {"button":[{"name":"버튼명","linkType":"WL","linkP":"https://www.링크주소.com/?example=12345", "linkM": "https://www.링크주소.com/?example=12345"}, {"name":"버튼명","linkType":"DS"}]}
    */

    $send_res = $this->curl_return($this->sendUrl, $sendData);
    if($send_res["code"] == 0){
        $return_msg = $send_res["code"]." : ".$send_res["message"];
        $return_data = array(
            "result" => "success",
            "return_msg" => "$return_msg",
            "msg" => "$msg_"
        );        
    }
    else{
        $return_msg = $send_res["code"]." : ".$send_res["message"];
        $return_data = array(
            "result" => "fail",
            "return_msg" => "$return_msg",
            "msg" => "$msg_"
        );
    }
    
    return $return_data;

    /*
    code : 0 성공, 나머지 숫자는 에러
    message : 결과 메시지
    */
    }

    function curl_return($url,$data){
        // $_hostInfo =  parse_url($url);
        // $_port = (strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;

        $oCurl = curl_init();
        // curl_setopt($oCurl, CURLOPT_PORT, $_port);
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_POST, 1);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);

        $ret = curl_exec($oCurl);
        $error_msg = curl_error($oCurl);
        curl_close($oCurl);

        // 리턴 JSON 문자열 확인
        // print_r($ret . PHP_EOL);

        // JSON 문자열 배열 변환
        $retArr = json_decode($ret,true);

        // 결과값 출력
        return $retArr;
    }

    function parse_string($str,$arr){
        extract($arr);   
        $str = preg_replace("/#{([a-zA-Z_]+)}/","{\$$1}",$str);
        eval("\$str = \"$str\";");
        return $str;
    }
}
?>