<?

include "../../_header.php";
include "config.php";
include "cipher_receive.php"; // 암호화/복호화 관련 파일

$_POST[card_num] = implode($_POST[card_num]);
$_POST[card_num] = trim(str_replace("-","",$_POST[card_num]));

### 응답필드매칭 - DB 필드명과 일치시킬 것
$r_fields = array(
	'Message Type'		  => 'messageType',
	'거래 구분 코드'		  => 'ordCode',
	'거래 금액'			  => 'price',
	'전문 전송 일시'		  => 'senddt',
	'Card 번호'			  => 'card_num',
	'거래 고유 번호'		  => 'ordSerialNumber',
	'단말#'				  => 'terminal',
	'가맹점#'				  => 'franchise',
	'상품 유형'			  => 'prize',
	'응답 코드'			  => 'resCode',
	'승인 번호'			  => 'approvalNumber',
	'레인보우포인트 여부' => 'isRainbowPoint',
	'할인금액'			  => 'dcPoint',
	'지불금액'			  => 'payPoint',
	'잔여한도'			  => 'totPoint',
	);

### 응답코드 - Ver1.89 기준
$r_resCode = array(
	'00' =>'정상',
	'01' =>'이미취소',
	'10' =>'전송일시오류',
	'11' =>'전문고유번호오류',
	'12' =>'통신망관리정보오류',
	'13' =>'POS ENTRY오류',
	'14' =>'취급기관 오류',
	'15' =>'단말,가맹점코드 오류',
	'16' =>'통화코드 오류',
	'17' =>'거래금액 오류',
	'18' =>'Track오류',
	'19' =>'취소구분',
	'20' =>'취소할승인번호 오류',
	'21' =>'전문ID오류',
	'22' =>'상품코드오류',
	'23' =>'미입장카드',
	'28' =>'SKT 제휴 카드 아님',
	'29' =>'모네타카드 불일치',
	'30' =>'사용불능 카드',
	'31' =>'사용정지카드',
	'32' =>'조회불능카드',
	'33' =>'불량가맹점',
	'34' =>'말소가맹점',
	'35' =>'등록Van사 다름',
	'36' =>'말소단말기',
	'37' =>'유효기간 초과',
	'38' =>'유효기간 표시',
	'39' =>'하나SK더블할인대상카드아님',
	'40' =>'한도초과',
	'41' =>'년한도초과',
	'42' =>'일한도초과',
	'43' =>'사용횟수초과(월,년)',
	'45' =>'개인한도 소진',
	'49' =>'VIP 일반전환 승인',
	'50' =>'취소할승인번호 없음',
	'51' =>'취소할금액이 다름',
	'52' =>'가맹점미등록 Segment',
	'53' =>'멤버십번호 업데이트 필요',
	'54' =>'주민번호상이',
	'55' =>'수령확인등록 후 사용요망',
	'56' =>'나이제한',
	'57' =>'거래요일오류',
	'58' =>'이미가입되어있음',
	'80' =>'Check기준오류',
	'81' =>'VIP아님(스피드메이트)',
	'90' =>'시스템 장애',
	'91' =>'시스템지연',
	'95' =>'시스템취소(오류자료)',
	'99' =>'시스템접속거부',
	 );
 
$r_isRainbowPoint = array(
	'MV'	=> 'VIP',
	'MA'	=> '일반',
	'MG'	=> '골드',
	'MS'	=> '실버',

	'LV'	=> 'VIP (최종잔여포인트사용)',
	'LA'	=> '일반 (최종잔여포인트사용)',
	'LG'	=> '골드 (최종잔여포인트사용)',
	'LS'	=> '실버 (최종잔여포인트사용)',
	
	'RV'	=> 'VIP (최종잔여포인트사용완료)',
	'RA'	=> '일반 (최종잔여포인트사용완료)',
	'RG'	=> '골드 (최종잔여포인트사용완료)',
	'RS'	=> '실버 (최종잔여포인트사용완료)',
	);

### 요청 전문 생성
$request_str = create_request_msg($_POST['card_num'],$_POST['birth']);

### 암호화
$encrypted_msg = encrypt_tworld($request_str,$cfg[tworld][sklte_key]);

### 전송 요청
$receive_data = send_request_action($cfg,$encrypted_msg);

### 복호화
$decrypted_msg = decrypt_tworld($receive_data,$cfg[tworld][sklte_key]);

### 전문 분석
$reponse_str = analysis_response_data($decrypted_msg);

### 전문 분석 - 언어셋 & 필드명변경
foreach ($reponse_str as $k=>$v){
	$v[title] = $r_fields[trim(_euckr2utf($v[title]))];
	$v[value] = trim(_euckr2utf($v[value]));
	
	$data[$v[title]] = $v[value];
}
$data[price] = $data[price]+0;
$data[dcPoint]	= $data[dcPoint]+0;
$data[payPoint] = $data[payPoint]+0;
$data[totPoint] = $data[totPoint]+0;
$data[resCodeStr] = $r_resCode[$data[resCode]];
$data[isRainbowPointStr] = $r_isRainbowPoint[$data[isRainbowPoint]];

### 테스트용
//$data[totPoint] = 10000;


### DB저장
$query = "
insert into exm_dc_partnership_tworld set
	messageType		   = '$data[messageType]',
	ordCode			   = '$data[ordCode]',
	price			      = '$data[price]',
	senddt			   = '$data[senddt]',
	card_num		      = '$data[card_num]',
	ordSerialNumber	= '$data[ordSerialNumber]',
	terminal		      = '$data[terminal]',
	franchise		   = '$data[franchise]',
	prize			      = '$data[prize]',
	resCode			   = '$data[resCode]',
	resCodeStr		   = '$data[resCodeStr]',
	approvalNumber	   = '$data[approvalNumber]',
	isRainbowPoint	   = '$data[isRainbowPoint]',
	isRainbowPointStr = '$data[isRainbowPointStr]',
	dcPoint			   = '$data[dcPoint]',
	payPoint		      = '$data[payPoint]',
	totPoint		      = '$data[totPoint]',
	birth			      = '$_POST[birth]'
	";
$db->query($query);
$data[no] = $db->id;

$tpl->assign($data);
$tpl->print_('tpl');



function _euckr2utf($str){
	return (mb_detect_encoding($str,"ASCII,EUC-KR,CP949",true)) ? iconv("EUC-KR","UTF-8",$str) : $str;
}

function send_request_action($cfg,$encrypted_msg){
	$result_code = '';
	$message = "0200";    // 메시지 타입 : 승인요청 ("0200");
	$trans_date = date("YmdHis"); // 전문 전송 일시 : YYYYMMDDhhmmss
	$price = $_POST['price'];
	
	$msg = $message.$price.$trans_date;
	$encrypted_msg = $encrypted_msg; //암호화된 메세지..
	$encrypted_msg = $cfg[tworld][sklte_van].$encrypted_msg."\n";  //가맹점 분류코드를 더한다

	$fp = fsockopen($cfg[tworld][sk_server_ip_addr],$cfg[tworld][sk_server_port],$errno,$errstr,5);

	if(!$fp){
		// 소켓 연결 에러 처리
		echo "$errstr ($errno)<br>\n";
	} else {
		$send_msg = fputs($fp, $encrypted_msg);
		$receive_msg = '';
		while(!feof($fp)){$receive_msg .= fgets($fp, 4096);}
		fclose($fp);
	}
	
	return $receive_msg;
}

?>