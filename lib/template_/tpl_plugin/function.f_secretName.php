<?
function f_secretName($name){
    //debug($cartno);
    //exit;
	global $db,$cid;

//debug($name);
    $char = preg_match("/[\xE0-\xFF][\x80-\xFF][\x80-\xFF]/", $name);
    $strLen = strlen($name);
        //debug($char);
    //영문, 숫자일때
    if($char == 0)
    {
        //영, 숫자가 2 글자일땐 앞에 글자(숫자)만 보임
        if($strLen == 2) {
            $m_char_num = substr($name, -1, 1);

            $s_char = mb_substr($name, 0, 1, 'utf-8');
            $m_char = str_repeat('*', strlen($m_char_num));
            //$e_char = mb_substr($name, -1, 1);
        } else {
            $m_char_num = mb_substr($name, 1, -1, 'utf-8');
            $s_char = mb_substr($name, 0, 1);
            $m_char = str_repeat('*', mb_strlen($m_char_num, 'utf-8'));
            $e_char = mb_substr($name, -1, 1, 'utf-8');
        }
    $data = $s_char.$m_char.$e_char;
    } else {
        //한글일때

        //한글이 2글자일때 앞의 글자만 보임
        if($strLen == 6){
            $m_char_num = mb_substr($name, -1, 1, 'utf-8');

            $s_char = mb_substr($name, 0, 1, 'utf-8');
            $m_char = str_repeat('*', mb_strlen($m_char_num, 'utf-8'));
            //$e_char = mb_substr($name, -1, 1, 'utf-8');
        } else {
            $m_char_num = mb_substr($name, 1, -1, 'utf-8');

            $s_char = mb_substr($name, 0, 1, 'utf-8');
            $m_char = str_repeat('*', mb_strlen($m_char_num, 'utf-8'));
            $e_char = mb_substr($name, -1, 1, 'utf-8');
        }
        $data = $data = $s_char.$m_char.$e_char;
    }

    //맨 앞, 뒤 한글자를 제외한 나머지 문자 *표 처리
    return $data;
}
?>