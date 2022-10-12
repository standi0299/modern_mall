<?php

	function smartXPayComplete($pg_log, $page_return_url)
	{
		$result = "";				
		$result .= "<body>".PHP_EOL;
		$result .= "<script>".PHP_EOL;
		if ($pg_log)
			$result .= "alert('".$pg_log."');".PHP_EOL;
		//$result .= "window.close();".PHP_EOL;
		if ($page_return_url)
		{		
			//$result .= "opener.parent.location.href='".$page_return_url."';</script>";
			$result .= "parent.location.href='".$page_return_url."';";
		} else {
			$result .= "parent.document.getElementById('LGD_PAYMENTWINDOW').style.display = 'none';";
			
		}
		$result .= "</script>".PHP_EOL;
		$result .= "</body>".PHP_EOL;
		
		return $result;		
	}

	

	
	function writeLog($payno, $msg)
	{
		$path = "./log/";
		if (!is_dir($path)){
			mkdir($path,0757);
			chmod($path,0757);
		}
   	$file = date("Ymd")."-".$payno.".log";

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
	 
?>
