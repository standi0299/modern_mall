	var cloud_sig_code = '';
	var timestamp = '';
	
	function waitForMsg(){
		//$.support.cors = true;
		$.ajax({
			type: "GET",
			url: "http://blackorwhite.ilark.co.kr/_cloud/check_complete.php?cloud_sig_code=" + cloud_sig_code + "&timestamp="+timestamp,
			async: true,
			cache: false,			
			dataType: "jsonp",
      jsonpCallback: "Callback",
			success: function(data){
				//alert(data.msg);
				//alert(data.url);
												
				if (data.msg =="OK") {
					//clearInterval(start_setinternal);					
					//realod 와 href 처리.
					if (data.url == 'reload')						
						parent.location.reload();
					else						
					{
						//location.herf = data.url;
						parent.location.href = data.url;						
					}
				
				//sig_code 없을 경우	PARAM_ERROR 발생
				} else if (data.msg == "PARAM_ERROR") {					 
					makeSigCode();
					setTimeout("waitForMsg()",1000);
				
				} else {
					timestamp = data.timestamp;
					setTimeout("waitForMsg()",1000);
				}
			},
			error: function(request,status,error) {
				//alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				//setTimeout("waitForMsg()",5000);
			}
		});
		
		function Callback(data) {			
			//alert(data);
    }    
	}
	
	function makeSigCode()
	{
		var matchStr = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		for (i=1;i<=6;i++ ) { // 6자리 난수 발생
			cloud_sig_code += matchStr.substr(Math.floor(Math.random() * 61), 1);
		}		
	}	
	
	
	function waitForMsgStart()
	{
		makeSigCode();
		//title 변경
		top.document.title = headerTitle + ' [' + cloud_sig_code + ']';
		setTimeout("waitForMsg()",7000);			//7초후 체크 시작...
		//waitForMsg();
	}
	
	
	function waitForMsgEditCopy(request_key){
		//$.support.cors = true;
		$.ajax({
			type: "GET",
			url: "http://blackorwhite.ilark.co.kr/_cloud_copy/copy_check_complete.php?request_key=" + request_key + "&timestamp="+timestamp,
			async: true,
			cache: false,			
			dataType: "jsonp",
      jsonpCallback: "Callback",
			success: function(data){
				//alert(data.msg);
				//alert(data.url);
												
				if (data.msg =="OK") {
					//clearInterval(start_setinternal);					
					//realod 와 href 처리.
					if (data.url == 'reload')						
						location.reload();
					else if (data.url == 'close')						
						window.close();
					else				
						location.href = data.url;						
									
				//sig_code 없을 경우	PARAM_ERROR 발생
				} else if (data.msg == "PARAM_ERROR") {					 
					makeSigCode();
					setTimeout("waitForMsgEditCopy('" + request_key + "')",1000);
				} else if (data.msg == "PODMNG_ERROR") {					 
					alert(data.url);
				} else {
					timestamp = data.timestamp;
					setTimeout("waitForMsgEditCopy('" + request_key + "')",1000);
				}
			},
			error: function(request,status,error) {
				//alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				//setTimeout("waitForMsg()",5000);
			}
		});
		
		function Callback(data) {			
			//alert(data);
    }    
	}
	
	
	//편집 복사 요청하기			20180824		chunter
	function editCopyRequest(storageid, request_kind){
		//$.support.cors = true;
		$.ajax({
			type: "GET",
			url: "/module/indb.php?mode=edit_copy_request&storageid=" + storageid + "&request_kind="+request_kind,
			async: true,
			cache: false,			
			dataType: "json",      
			success: function(data){
				//alert(data.msg);
				//alert(data.url);
				if (data.result == "1")
				{
					alert('편집복사 요청이 정상적으로 처리되었습니다.');
					waitForMsgEditCopy(data.response_key);
				}	else {
					alert(data.msg);
				}									
				
			},
			error: function(request,status,error) {
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				//setTimeout("waitForMsg()",5000);
			}
		}); 
	}
	
	