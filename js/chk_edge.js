	
	
	function checkBrowser() 
	{
  	var isOpera = !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
		var isEdge = navigator.userAgent.indexOf('Edge/') >= 0;
		var isSafari = navigator.userAgent.indexOf('Safari') >= 0;
		
    return {
    	isOpera: isOpera,
			isEdge :isEdge,
      isFirefox: typeof InstallTrigger !== 'undefined',
      isSafari: isSafari,
      isChrome: !!window.chrome && !isOpera && !isEdge,
			isIE: /*@cc_on!@*/false || !!document.documentMode   // At least IE6
   	}
	}
	
	function getInternetExplorerVersion() {
    var rv = -1;
    if (navigator.appName === "Microsoft Internet Explorer") {
      var ua = navigator.userAgent;
      var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
      if (re.exec(ua) != null)
          rv = parseFloat(RegExp.$1);
    }
    else if (navigator.appName === "Netscape") {
      var ua = navigator.userAgent;
      var re = new RegExp("Trident/.*rv:([0-9]{1,}[\.0-9]{0,})");
      if (re.exec(ua) != null) {
          rv = parseFloat(RegExp.$1);
      }
    }
    return rv;
  }
    

	function _createHiddenIframe(target, uri) 
	{
		var iframe = document.createElement("iframe");
    iframe.src = uri;
    iframe.id = "hiddenIframe";
    iframe.style.display = "none";
    target.appendChild(iframe);
    return iframe;
	}
	
	function _registerEvent(target, eventType, cb) {
  	if (target.addEventListener) {
    	target.addEventListener(eventType, cb);
      return {
      	remove: function () {
        	target.removeEventListener(eventType, cb);
       	}
     	};
   	} else {
    	target.attachEvent(eventType, cb);
     	return {
       	remove: function () {
         	target.detachEvent(eventType, cb);
       	}
     	};
   	}
	}
    
	
	//exe 편집기를 실행하고 오류가 발생되지를 체크한다.	20160624		chunter    
	function openUriUsingFirefox(uri, failCb) {
		var iframe = document.querySelector("#hiddenIframe");
    if (!iframe) {
    	iframe = _createHiddenIframe(document.body, "about:blank");
   	}
    try {
    	iframe.contentWindow.location.href = uri;
   	} catch (e) {
    	if (e.name == "NS_ERROR_UNKNOWN_PROTOCOL") {
      	failCb();
     	}
   	}
  }
  
  
  function openUriWithTimeoutHack(uri, failCb) 
  {
  	var timeout = setTimeout(function () {
    	failCb();
      handler.remove();
   	}, 1000);

    var handler = _registerEvent(window, "blur", onBlur);

    function onBlur() {
    	clearTimeout(timeout);
      handler.remove();
   	}
    window.location = uri;
	}
	
	
	function openUriUsingIE(uri, failCb) 
	{
  	//check if OS is Win 8 or 8.1 or WIN 10 IE11
    var ua = navigator.userAgent.toLowerCase();
    var isWin8 = /windows nt 6.2/.test(ua) || /windows nt 6.3/.test(ua);
		var isWin10 = /windows nt 10./.test(ua);

    if (isWin8) {
    	openUriUsingIEInWindows8(uri, failCb);
    	//openUriWithNotSupported(uri, failCb);
   	} else {
    	if (getInternetExplorerVersion() === 10) {
      	openUriUsingIE10InWindows7(uri, failCb);
     	} else if (getInternetExplorerVersion() === 9 || getInternetExplorerVersion() === 11) {
      	openUriWithHiddenFrame(uri, failCb);
     	} else {
      	openUriInNewWindowHack(uri, failCb);
     	}
   	}
 	}
 	
 	
 	function openUriUsingIEInWindows8(uri, failCb) {
    if (navigator.msLaunchUri) {
      navigator.msLaunchUri(uri,
        function () {
            window.location = uri;
        },
        failCb
      );
    }
    else
    { 
    	failCb();
    }
  }
  
  
  function openUriUsingIE10InWindows7(uri, failCb) {
    var timeout = setTimeout(failCb, 1000);
    window.addEventListener("blur", function () {
      clearTimeout(timeout);
    });

    var iframe = document.querySelector("#hiddenIframe");
    if (!iframe) {
    	iframe = _createHiddenIframe(document.body, "about:blank");
    }
    try {
      iframe.contentWindow.location.href = uri;
    } catch (e) {
      failCb();
      clearTimeout(timeout);
    }
  }

  function openUriInNewWindowHack(uri, failCb) {
    var myWindow = window.open('', '', 'width=1,height=1');

    myWindow.document.write("<iframe src='" + uri + "'></iframe>");
    setTimeout(function () {
      try {
        myWindow.location.href;
        myWindow.setTimeout("window.close()", 1000);
      } catch (e) {
        myWindow.close();
        failCb();
      }
    }, 1000);
  }
  
  
  function openUriWithHiddenFrame(uri, failCb) {

    var timeout = setTimeout(function () {
    	failCb();
      handler.remove();
    }, 1000);

    var iframe = document.querySelector("#hiddenIframe");
    if (!iframe) {
      iframe = _createHiddenIframe(document.body, "about:blank");
    }

    var handler = _registerEvent(window, "blur", onBlur);

    function onBlur() {
      clearTimeout(timeout);
      handler.remove();
    }

    iframe.contentWindow.location.href = uri;
  }
    
    
	
	
    
	//exe 편집기를 실행하고 오류 발생 여부를 확인하지 않는다.	20160624		chunter      
	function openUriWithNotSupported(uri, failCb) 
	{
		//var timeout = setTimeout(function () {
    //	failCb('not supported');
    //}, 1000);

    var iframe = document.querySelector("#hiddenIframe");
    if (!iframe) {
    	iframe = _createHiddenIframe(document.body, "about:blank");
   	}
    iframe.contentWindow.location.href = uri;
    failCb();
	}
		
	
	//exe 편집기 설치되었는지 체크			20160624		chunter
	function checkExeAppInstall(uri, failCallback)
	{
		var browser = checkBrowser();
				
		if (browser.isFirefox) 
		{
    	openUriUsingFirefox(uri, failCallback);    	
		} else if (browser.isChrome) {
    	openUriWithTimeoutHack(uri, failCallback);
   	} else if (browser.isIE) {
   		openUriUsingIE(uri, failCallback);	
   	} else {
			openUriWithNotSupported(uri, failCallback);
		}		
	}
	    




function CheckIE11()
{
	//Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.71 Safari/537.36 Edge/12.0
	//Mozilla/5.0 (Windows Phone 10.0; Android 4.2.1; DEVICE INFO) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.71 Mobile Safari/537.36 Edge/12.0
	
  //var va = GetVersion(v);
  //alert(navigator.userAgent);
  if (navigator.appName == 'Netscape' && navigator.userAgent.search('Edge/') != -1) {
  	//$j(function(){mainpopup('1','100','100','100','100')});
  	//window.open('/service/pop_edge.html','','width=800,height=700,scrollbars=1');
  	
  	location.href = "/service/pop_edge.php";
  	//popupLayer("/service/pop_edge.html");
  	return true;
	} else {
		//alert('adfa');
		//location.href = "/service/pop_edge.php";
		//popupLayer("/service/pop_edge.php", 920, 1080);
  	return false;
  }
}


function getInternetExplorerVersion()
{
  var rv = -1;
  if (navigator.appName == 'Microsoft Internet Explorer')
  {
    var ua = navigator.userAgent;
    var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
    if (re.exec(ua) != null)
      rv = parseFloat( RegExp.$1 );
  }
  else if (navigator.appName == 'Netscape')
  {
    var ua = navigator.userAgent;
    var re  = new RegExp("Trident/.*rv:([0-9]{1,}[\.0-9]{0,})");
    if (re.exec(ua) != null)
      rv = parseFloat( RegExp.$1 );
      
   	var re2  = new RegExp("Edge/.*rv:([0-9]{1,}[\.0-9]{0,})");
    if (re2.exec(ua) != null)
      rv = parseFloat( RegExp.$1 );
  }
  return rv;
}
