<?
### form 전송 취약점 개선 20160128 by kdk
//메세지 인증(시그니처) 처리
    
function signatureData($cid, $data)
{
	$SecretKey = $cid ."_AAICAQAIAwUABggCBQcEBAIGBAMDBAcBAAUEBwIDBAU=";
	//debug($SecretKey);
	//debug($data);
	$sigData = base64_encode(urlencode(hash_hmac('md5', $data, $SecretKey, true)));
	//debug($sigData);
    
    return rtrim($sigData);
}

function expiresData($add_minutes="") 
{
	$add = 0;
	if($add_minutes) $add = $add_minutes;
	
	//$expData = mktime(date(H),date(i)+$add,date(s),date(m),date(d)+1,date(Y));
	$expData = mktime(date(H),date(i)+$add,date(s),date(m),date(d),date(Y));
	//debug($expData);
	return rtrim($expData);
}

function sig_compare($a, $b) 
{
	if (!$a || !$b) return false;

    if (!is_string($a) || !is_string($b)) { 
        return false; 
    } 
    
    $len = strlen($a); 
    if ($len !== strlen($b)) { 
        return false; 
    } 

    $status = 0; 
    for ($i = 0; $i < $len; $i++) { 
        $status |= ord($a[$i]) ^ ord($b[$i]); 
    }         
    return $status === 0; 
}

function exp_compare($a) 
{
	if (!$a) return false;

	$expData = expiresData();
	//debug($a);
	//debug($expData);
	if($a >= $expData) return true;
	else return false;
}

?>