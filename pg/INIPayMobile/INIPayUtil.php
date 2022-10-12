<?php

	function inipayComplete($pg_log, $page_return_url)
	{
		$result = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'.PHP_EOL;				
		$result .= "<body>".PHP_EOL;
		$result .= "<script>".PHP_EOL;
		if ($pg_log)
			$result .= "alert('".$pg_log."');".PHP_EOL;
		//$result .= "window.close();".PHP_EOL;
		if ($page_return_url) {		
			//$result .= "opener.parent.location.href='".$page_return_url."';";
			$result .= "parent.location.href='".$page_return_url."';";
		}
		$result .= "</script>".PHP_EOL;
		$result .= "</body>".PHP_EOL;
		
		return $result;		
	}

	function makeParam($P_TID, $P_MID)
	{   
		return "P_TID=".$P_TID."&P_MID=".$P_MID; 
	} 
	
	
	function socketPostSend($urlStr, $param)
	{
		$url = parse_url($urlStr);
		$fp = @fsockopen ("ssl://".$url[host], 443, $errno, $errstr, 2);
		if ($fp) 
    {
    	@fputs($fp,"POST $url[path] HTTP/1.1\r\n");
      @fputs($fp,"Host: $url[host]\r\n");
      //fputs($fp,"Referer: http://$fileHost$filePath\r\n");
      @fputs($fp,"User-Agent: ".$_SERVER["HTTP_USER_AGENT"]."\r\n"); 
      @fputs($fp,"Content-type: application/x-www-form-urlencoded\n");
      @fputs($fp,"Content-length: " .strlen($param)."\n");
			
			//@fputs($fp,"Accept: */*\r\n");
			//@fputs($fp,"Accept-Language: en-us,en;q=0.5\r\n");  
			//@fputs($fp,"Accept-Charset: ISO-8859-1, utf-8;q=0.66, *;q=0.66\r\n"); 
			
      @fputs($fp,"Connection: Close\r\n\r\n");
      @fputs($fp,"$param\r\n"); 
      @fputs($fp,"\r\n");
            
      while (!feof($fp)) $data = $data.fgets($fp,4096);
   	}
    fclose ($fp);
		
		$data = explode("\r\n\r\n", $data);
    array_shift($data);
    $data = implode("", $data);
		
		return $data;
		//debug($data);		
	}

	
	function writeLog($msg)
	{
		$path = "./log/";
		if (!is_dir($path)){
			mkdir($path,0757);
			chmod($path,0757);
		}
   	$file = "noti_input_".date("Ymd").".log";

    if(!($fp = fopen($path.$file, "a+"))) return 0;
                 
    ob_start();
    print_r($msg);
    $ob_msg = ob_get_contents();
    ob_clean();
         
    if(fwrite($fp, " ".$ob_msg."\n") === FALSE)
    {
    	fclose($fp);
			chmod($path.$file, 0757);
      return 0;
   	}
    fclose($fp);
		chmod($path.$file, 0757);
    return 1;
	}
	
	function INIPayWriteLog($msg, $filenameAppend = "")
	{
		$path = "./log/";
		if (!is_dir($path)){
			mkdir($path,0757);
			chmod($path,0757);
		}
		
   	if ($msg['P_AUTH_DT'])
			$file = "noti_".$msg['P_AUTH_DT'].$filenameAppend.".log";
		else if ($msg['P_TID'])
			$file = "noti_".$msg['P_TID'].$filenameAppend.".log";		
		else 
			$file = "noti_".date("Ymd-His").$filenameAppend.".log";

    if(!($fp = fopen($path.$file, "a"))) return 0;
                 
    ob_start();
    print_r($msg);
    $ob_msg = ob_get_contents();
    ob_clean();
         
    if(fwrite($fp, " ".$ob_msg."\n") === FALSE)
    {
    	fclose($fp);
			chmod($path.$file, "777");
      return 0;
   	}
    fclose($fp);
		chmod($path.$file, "777");
    return 1;
	}
	 
?>
