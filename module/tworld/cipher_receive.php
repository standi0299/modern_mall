<?php 
    
/*--------------------------------------------------
  암호화 관련 Samplepage (Web제휴사전문Ver1.89기준)
  author: 
  updated date: 2013.06.13
--------------------------------------------------*/

/*
 * 요청 전문 생성
 */
function create_request_msg($card_num,$birth){

   /*
   항        목        Type    Length  Byte    내              용
   Message Type        N       4      <br> 4       0200’
   거래 구분 코드      N       6       10      000010 : 승인 요청시 / 000020 : 조회 요청시
   거래 금액           N       12      22      Right Justify(할인전금액 입력)
   전문 전송 일시      N       14      36      YYYYMMDDhhmmss
   Card 번호           N       16      52      SK-T 멤버십 번호
   거래 고유 번호      AN      12      64      67’ + 고유번호(10자리)
   단말#               S       10      74      1000000000’ (fixed)
   가맹점#             S       10      84      제휴사코드’ + ‘점포코드  ’
   상품 유형           N       4       88      4자리 상품코드(default:’    ’)
   사용자 ID 구분 코드 S       2       90      사용자 ID 구분  코드
   사용자 ID           AN      20      110     Space (스페이스처리)  
   dummy               S       15      125     Dummy space (스페이스처리)
   통화 코드           N       3       128     410’
   연계정보            S       88      216     주민번호 연계정보(CI)
   */

   $r_amt = "000000000000";                    //거래금액(12bytpe 우측정렬)
   $r_date = date('YmdHis');                   //전문전송일시
   $r_cardnum = $card_num;                     //카드 번호
   $r_sno = '67'.sprintf("%010d",date('His')); //거래 고유 번호 (자유)  
   $r_pos = '1000000000';                      //단말기 번호(고정)
   //$r_van = 'V3111001  ';                      //가맹점 (고정)
   $r_van = $GLOBALS[cfg][tworld][sklte_van].'1001  ';  //가맹점 (고정)
   $r_ptype = '1001';                          //스페이즈 4자리 2021115수정배포
   $r_utype = '03';                            //사용자 ID구분코드 (03)
   $r_uid = $birth.'              ';           //사용자 아이디(20byte)
   $r_dummy = '               ';               //스페이스 15자리 (15byte)
   $r_cur = '410';                             //통화코드 410 (고정)
   $r_jumin = '';                              //주민번호 연계정보 (88bytpe)
   for($i=0;$i<88;$i++){
      $r_jumin .= ' ';                        //88번 반복
   }

   //전문 조합
   $request_str = "0200000010";
   $request_str .= $r_amt;             //거래금액
   $request_str .= $r_date;            //전문전송일시
   $request_str .= $r_cardnum;         //카드번호
   $request_str .= $r_sno;             //거래 고유 번호
   $request_str .= $r_pos;             //단말기
   $request_str .= $r_van;             //가맹점
   $request_str .= $r_ptype;           //상품 유형
   $request_str .= $r_utype;           //사용자 ID구분
   $request_str .= $r_uid;             //사용자 ID
   $request_str .= $r_dummy;           //사용자 dummy
   $request_str .= $r_cur;             //통화코드
   $request_str .= $r_jumin;           //주민번호 연계 정보

   return $request_str;
}

/*
 * 취소 요청 전문 생성
 */
function create_cancel_msg($card_num,$ack_num){
   $cancel_str = '';
   return $cancel_str;
}

/*
 * 승인 응답 전문 데이터 분석 
 */
function analysis_response_data($decrypted_msg){
   //메세지 타입
   $msgType = substr($decrypted_msg,0,4);
   $return_data = array();

   switch($msgType){
      //승인 응답 전문
      case '0210':
         $return_data = array(
            '0' =>  array(
               'title'     =>  'Message Type',
               'value'     =>  substr($decrypted_msg,0,4),
            ),
            '1' =>  array(
               'title'     =>  '거래 구분 코드 ',
               'value'     =>  substr($decrypted_msg,4,6),
            ),
            '2' =>  array(
               'title'     =>  '거래 금액 ',
               'value'     =>  substr($decrypted_msg,10,12),
            ),
            '2' =>  array(
               'title'     =>  '거래 금액 ',
               'value'     =>  substr($decrypted_msg,10,12),
            ),
            '3' =>  array(
               'title'     =>  '전문 전송 일시 ',
               'value'     =>  substr($decrypted_msg,22,14),
            ),
            '4' =>  array(
               'title'     =>  'Card 번호 ',
               'value'     =>  substr($decrypted_msg,36,16),
            ),
            '5' =>  array(
               'title'     =>  '거래 고유 번호 ',
               'value'     =>  substr($decrypted_msg,52,12),
            ),
            '6' =>  array(
               'title'     =>  '단말# ',
               'value'     =>  substr($decrypted_msg,64,10),
            ),
            '7' =>  array(
               'title'     =>  '가맹점# ',
               'value'     =>  substr($decrypted_msg,74,10),
            ),
            '8' =>  array(
               'title'     =>  '상품 유형 ',
               'value'     =>  substr($decrypted_msg,84,4),
            ),
            '9' =>  array(
               'title'     =>  '응답 코드 ',
               'value'     =>  substr($decrypted_msg,88,2),
            ),
            '10'    =>  array(
               'title'     =>  '승인 번호 ',
               'value'     =>  substr($decrypted_msg,90,8),
            ),
            '11'    =>  array(
               'title'     =>  '레인보우포인트 여부 ',
               'value'     =>  substr($decrypted_msg,98,2),
            ),
            '12'    =>  array(
               'title'     =>  '할인금액  ',
               'value'     =>  substr($decrypted_msg,100,8),
            ),
            '13'    =>  array(
               'title'     =>  '지불금액  ',
               'value'     =>  substr($decrypted_msg,108,10),
            ),
            '14'    =>  array(
               'title'     =>  '잔여한도  ',
               'value'     =>  substr($decrypted_msg,118,10),
            ),
         );
   }// end of switch
   return $return_data;
}

// 복호화 함수
function decrypt_tworld($input, $key) {
   if(!empty($input)){
      $input = hex2ascii($input);
      $td = mcrypt_module_open (MCRYPT_TripleDES, "", MCRYPT_MODE_ECB, "");
      $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size ($td), MCRYPT_RAND );

      if (mcrypt_generic_init($td, $key, $iv) != -1) {
         //decrypt
         $decrypt_plaintext = mdecrypt_generic ($td, $input);

         //cleanup
         mcrypt_generic_deinit ($td);
         mcrypt_module_close($td);

         return trim($decrypt_plaintext);
      }
   }
}

//암호화 함수
function encrypt_tworld($input, $key) {
   if(!empty($input)){
      $td = mcrypt_module_open (MCRYPT_TripleDES, "", MCRYPT_MODE_ECB, "");
      $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size ($td), MCRYPT_RAND );

      if (mcrypt_generic_init($td, $key, $iv) != -1) {
         //encrypt
         $encrypt_plaintext = mcrypt_generic ($td, $input);

         //cleanup
         mcrypt_generic_deinit($td);
         mcrypt_module_close($td);

         $encrypt_plaintext = bin2hexAll($encrypt_plaintext);

         return $encrypt_plaintext;
      }
   }
}

// 헥사를 아스키로 변경
function hex2ascii($string) {
   $num = strlen($string) / 2;
   $pos = 0;

   $ascii_str = '';

   for($i = 1; $i <= $num; $i++) {   //$s;
      $str = substr($string,$pos, 2);
      $dec_num = hexdec($str);
      $int_num = (int) $dec_num;
      $ascii_str .= sprintf ("%c", $int_num);
      $pos += 2;
   }
   return $ascii_str;
}

// 바이너리 를 헥사로 변경
function bin2hexAll($string) {
   $hex_str = '';
   $num = strlen($string);
    
   //문자열 길이 만큼 루프를 돌려서 hex데이터로 만든다.
   for($i = 0; $i < $num; $i++) {
      $temp_str = bin2hex(substr($string,$i, 1));
      $hex_str .= $temp_str;
   }
   return $hex_str;
}
?>