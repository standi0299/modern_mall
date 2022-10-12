
	function getHTTPObject () 
	{
  	var xhr = false;
  	if ( window.XMLHttpRequest ) 
  	{ 
  		xhr = new XMLHttpRequest (); 
  	}
    else if ( window.ActiveXObject ) 
    {
    	try 
    	{ 
    		xhr = new ActiveXObject ( "Msxml2.XMLHTTP" ); 
    	}      
      catch ( e ) {
        try { xhr = new ActiveXObject ( "Microsoft.XMLHTTP" ); }
        catch ( e ) { xhr = false; }
    	}
  	}
  	return xhr;
	}
	function grabFile ( file ) 
	{
  	var req = getHTTPObject();

  	if ( req ) {
    	req.onreadystatechange = function () {  rsltZsf(req); };
      req.open ( "GET", file, true );
      req.send(null);
    }
	}

	function axOk ( req ) 
	{ 
		if ( req.readyState==4 && (req.status==200 || req.status==304) ) 
		{ 
			return true; 
		} 
		else 
		{ 
			return false; 
		} 
	}

	function chkZsf ( zsfObj ) 
	{
  	zsfV=zsfObj.value;
  	if ( zsfV.length>0 ) 
  	{
  		grabFile ( "../../../lib/zmSpamFree/zmSpamFree.php?cfg=zsfCfgAdmin&zsfCode="+zsfV );
		}
		else 
		{
  		document.getElementById("rslt").innerHTML = '<?echo _("보안코드를 입력하셔야 합니다.")?>';
  		document.getElementById("rslt").className = "r";
  		document.getElementById('zsfCode').focus();
		}
	}


  function rsltZsf ( req ) 
  {
    if ( axOk(req) ) 
    {
      zsfV = document.getElementById('zsfCode').value;
      rsltTxt = '<?echo _("잘못")?>';
      rsltCls = "wrong";
      if ( req.responseText*1 == true ) 
      {
        rsltTxt = '<?echo _("정확히")?>';
        rsltCls = "right";
      }
      else 
      {
        document.getElementById('zsfCode').value='';
        document.getElementById('zsfImg').src='/lib/zmSpamFree/zmSpamFree.php?cfg=zsfCfgAdmin&re&zsfimg='+new Date().getTime();
      }
      document.getElementById("rslt").innerHTML = '<?echo _("보안코드를")?>'+" "+rsltTxt+" "+'<?echo _("입력하셨습니다.")?>';
      document.getElementById("rslt").className = rsltCls+"_code";
      document.getElementById('zsfCode').focus();
    }
  }