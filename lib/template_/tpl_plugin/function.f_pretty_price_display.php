<?
//블루포토(유치원 시즌2) 상품 가격 표시 / 15.07.17 / kjm
function f_pretty_price_display($originPrice){
    global $sess;
    if ($sess[pretty_pricedisplay] == "Y")
        return number_format($originPrice);
    else {
        $priceLen = strlen($originPrice);
        
        $m_char_num = mb_substr($originPrice, 0, $priceLen, 'utf-8');
        $m_char = str_repeat('*', mb_strlen($m_char_num, 'utf-8'));
    
        return $m_char;
    }
}
?>