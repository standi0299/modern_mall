	
	// 0: textarea tag 이름
	// 1: 이미지 업로드 여부 (true, false)
	// 2: 이미지 업로드 경로 (editor, goods)
	// 3: 입력창 resize 여부 (true, false)
	// 3: 에디처 툴바 출력 여부 (true, false)
	
 	function smartEditorInit(tagIDName, imageUploadFlag, uploadMode, bResize, bToolbar)
 	{ 		
 		var skinName = "SmartEditor2Skin_not_image.html";
 		imageUploadFlag = (typeof imageUploadFlag !== undefined) ? imageUploadFlag : false;
 		bResize = (typeof bResize !== undefined) ? bResize : true;
    bToolbar = (typeof bToolbar !== undefined) ? bToolbar : true;
         		
 		if (imageUploadFlag)
 		{
 			uploadMode = (uploadMode !== undefined) ? uploadMode : "editor";
 			
 			if (uploadMode == "editor_banner")
 				skinName = "SmartEditor2Skin_banner.html?mode=" + uploadMode;
 			else
 				skinName = "SmartEditor2Skin.html?mode=" + uploadMode;
 			//alert(skinName);
 		}
 		
 		var aAdditionalFontSet = [["Meiryo", "Meiryo あいうえお"]];	
 		nhn.husky.EZCreator.createInIFrame({
			oAppRef: oEditors,
			elPlaceHolder: tagIDName,			
			sSkinURI: "/js/smarteditor/" + skinName,
			htParams : {
				bUseToolbar : bToolbar,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
				bUseVerticalResizer : bResize,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
				bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
				aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
				fOnBeforeUnload : function(){
					//alert("완료!");
				}
			}, //boolean
			fOnAppLoad : function(){
				//예제 코드
				//oEditors.getById["ir1"].exec("PASTE_HTML", ["로딩이 완료된 후에 본문에 삽입되는 text입니다."]);
			},
			fCreator: "createSEditor2"
		});
 	}
 	
 	function pasteHTML(tagIDName, sHTML) {
		//var sHTML = "<span style='color:#FF0000;'>이미지도 같은 방식으로 삽입합니다.<\/span>";
		oEditors.getById[tagIDName].exec("PASTE_HTML", [sHTML]);
	}
	
	
	//editCheck - 필수 입력값으로 구분여부
	//checkMsg	- 필수값 체크후 오류 메세지.
	function sendContents(tagIDName, bEditCheck, checkMsg) {
		oEditors.getById[tagIDName].exec("UPDATE_CONTENTS_FIELD", []);	// 에디터의 내용이 textarea에 적용됩니다.
		
		bEditCheck = (typeof bEditCheck !== undefined) ? bEditCheck : false;
		checkMsg = (checkMsg !== undefined) ? checkMsg : "내용을 입력해주세요";
		
		if (document.getElementById(tagIDName).value == '<p>&nbsp;</p>')
			document.getElementById(tagIDName).value = '';
		
		if (bEditCheck)
		{
			// 에디터의 내용에 대한 값 검증은 이곳에서 document.getElementById("ir1").value를 이용해서 처리하면 됩니다.
			if(document.getElementById(tagIDName).value == "" || document.getElementById(tagIDName).value == '<p>&nbsp;</p>')
			{
		  	alert(checkMsg);		    
		    return false;
		 	}
		}
		return true;
	}
	
	
	function copyContents(sourceTagIDName, targetTagIDName)
	{
		try{
			var sHTML = oEditors.getById[sourceTagIDName].getIR();		
			pasteHTML(targetTagIDName, sHTML);
		}	catch(e) {}
	}
	
	
	
