	var mobileZip = false;
	function searchDivButton() {		 
		document.form1.currentPage.value='1';
		getAddr(); //jsonp사용시 enter검색
	}	 
	
	function enterSearch() {
		var evt_code = (window.netscape) ? ev.which : event.keyCode;
		if (evt_code == 13) {    
			event.keyCode = 0; 
			document.form1.currentPage.value='1'; 
			getAddr(); //jsonp사용시 enter검색 
		} 
	}

	function getAddr(){
		
		if (document.form1.keyword.value.length<2){
			alert(tls("검색어는 2자이상이어야 합니다"));
			return;
		}
	
		$.ajax({
			 url :"http://www.juso.go.kr/addrlink/addrLinkApiJsonp.do"  //인터넷망
			,type:"post"
			,data:$("#form").serialize()
			,dataType:"jsonp"
			,jsonp: "callback"
      ,jsonpCallback: "Callback"
			,crossDomain:true
			,success:function(xmlStr){
				
				if(navigator.appName.indexOf("Microsoft") > -1){
					var xmlData = new ActiveXObject("Microsoft.XMLDOM");
					xmlData.loadXML(xmlStr.returnXml);
				}else{
					var xmlData = xmlStr.returnXml;
				}
				$("#list").html("");
				var errCode = $(xmlData).find("errorCode").text();
				var errDesc = $(xmlData).find("errorMessage").text();
				if(errCode != "0"){
					alert(errCode+"="+errDesc);
				}else{
					if(xmlStr != null){
						if (mobileZip)
							makeListMobile(xmlData);
						else
							makeList(xmlData);
						pageDivMake(xmlData);
					}
				}

				
			}
	    ,error: function(xhr,status, error){
	    	alert(tls("에러발생"));
	    }
		});
		
		
		function Callback(data) {
    }
		
	}
	

	function pageDivMake(xmlStr){
		var total = $(xmlStr).find("totalCount").text();
		//$('#totalCntDiv').text("(총 "+total+"건)");	 // 총건수 셋팅
		var pageNum = document.form1.currentPage.value;
		var paggingStr = "";
		if(total < 1){
		}else{
			var PAGEBLOCK=10;
			var pageSize=document.form1.countPerPage.value;;
			var totalPages = Math.floor((total-1)/pageSize) + 1;
			var firstPage = Math.floor((pageNum-1)/PAGEBLOCK) * PAGEBLOCK + 1;
			
			if( firstPage <= 0 ) firstPage = 1;
			
			var lastPage = firstPage-1 + PAGEBLOCK;
			if( lastPage > totalPages ) lastPage = totalPages;
			
			var nextPage = lastPage+1 ;
			var prePage = firstPage-5 ;
			
			if( firstPage > PAGEBLOCK ){
				paggingStr +=  "<a href='javascript:goPageDiv("+prePage+");'>◁</a>&nbsp;&nbsp;" ;
			}
			
			for( i=firstPage; i<=lastPage; i++ ){
				if( pageNum == i )
					paggingStr += "<a style='font-weight:bold;color:blue;font-size:15px;' href='javascript:goPageDiv("+i+");'>" + i + "</a>&nbsp;&nbsp;";
				else
					paggingStr += "<a href='javascript:goPageDiv("+i+");'>" + i + "</a>&nbsp;&nbsp;";
			}
			
			if( lastPage < totalPages ){
				paggingStr +=  "<a href='javascript:goPageDiv("+nextPage+");'>▷</a>";
			}
			
			$("#zipcode_page").html(paggingStr);
		}	
	}
	
	
	
	function makeList(xmlStr){
		var htmlStr = "";
		htmlStr += "<table border='0'>";
		$(xmlStr).find("juso").each(function(){
			var roadAddress = $(this).find('roadAddr').text();
			
			 //htmlStr += "<div class='zcell''>";
			htmlStr += "<tr>";
			htmlStr += "<th width='60'>"+$(this).find('zipNo').text()+"</th>";
			htmlStr += "<td style='word-break:break_all;'>";
			htmlStr += "<a href=\"javascript:_zr('" + $(this).find('zipNo').text() + "','" + roadAddress + "');\">";
			htmlStr += "[" + tls("도로명") + "] " + roadAddress + "</a>";		
			htmlStr += "<br>[" + tls("지") + "&nbsp;&nbsp&nbsp" + tls("번") + "] " + $(this).find('jibunAddr').text();
			
			htmlStr += "</td>";
			htmlStr += "</tr>";
			//htmlStr += "</div>";			
			
		});
		htmlStr += "</table>";
		$("#list").html(htmlStr);
	}


	function makeListMobile(xmlStr){
		var htmlStr = "";
		htmlStr += "<ul>";
		$(xmlStr).find("juso").each(function(){
			var roadAddress = $(this).find('roadAddr').text();
			
			htmlStr += "<li>";		
			htmlStr += "<a href=\"javascript:_zr('" + $(this).find('zipNo').text() + "','" + roadAddress + "');\">";
			htmlStr += $(this).find('zipNo').text() + "<br />";
			htmlStr += "[" + tls("도로명") + "] " + roadAddress + "</a>";
			htmlStr += "<span>[" + tls("지") + "&nbsp;&nbsp;&nbsp;" + tls("번") + "] " + $(this).find('jibunAddr').text() + "</span>";		
			htmlStr += "</li>";
		});
		htmlStr += "</ul>";
		
		if (htmlStr.indexOf('<li>') > -1) $(".postList").html(htmlStr);
		else $(".postList").html("<ul><li style=\"margin-top:80px;\">" + tls("검색 결과가 없습니다.") + "</li></ul>");
	}
	
	
	// 페이지 이동
	function goPageDiv(pageNum){
		document.form1.currentPage.value=pageNum;
		getAddr();
	}