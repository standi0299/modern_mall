function InstallActiveX() {
    document.write('    <object id="ActiveLoader30" classid="clsid:6B679483-873C-45F0-9130-9192C580B36F"');
    document.write('        codebase="http://podmanage.bluepod.kr/ilark/printhome/editor/ActiveLoader30.cab#version=1,0,0,27"');
    document.write('        standby="Downloading iLarkComm ActiveX Control...."');
    document.write('        width="1" height="1">');
    document.write('        <PARAM name="InstallPath" value="webhardupload">');
    document.write('        <PARAM name="Module" value="webhardupload.dll">');
    document.write('    </object>');
}

function ExecDw(editorType, self, mode, cid, order_name, upload_code) {
	
	var ActiveLoader = document.getElementById('ActiveLoader30');
	if (!ActiveLoader) {
		alert(tls("웹하드 다운로더 설치 도중 오류가 발생했습니다. 현재 웹브라우저창을 새로고침해 주시기 바랍니다.1"));
		return;
	}

	if (!ActiveLoader.Update("http://podmanage.bluepod.kr/ilark/printhome/editor/")) {
		alert(tls("웹하드 다운로더 설치 도중 오류가 발생했습니다. 현재 웹브라우저창을 새로고침해 주시기 바랍니다.2"));
		return;
	}

	var IsSuccess = ActiveLoader.NewWork("http://" + self + "/_ilark/printhome_config.php?mode=" + mode + "&taget_cid=" + cid + "&order_name=" + order_name + "&upload_code=" + upload_code + "&editorType=" + editorType, editorType, "", 1);
	ActiveLoader.ReleaseModule();

	if (IsSuccess) {
		alert(tls("다운로드가 완료되었습니다."));
	} else {
		alert(tls("다운로드를 취소하셨습니다."));
	}
	return IsSuccess;
}

function ExecAx(fm, editorType, self, mode, cid, order_name, upload_code, mobile, flag) {
	if (form_chk(fm)) {
		ExecAxRun(fm, editorType, self, mode, cid, order_name, upload_code, mobile, flag);
	}
}

function ExecAxRun(fm, editorType, self, mode, cid, order_name, upload_code, mobile, flag) {
	var ActiveLoader = document.getElementById('ActiveLoader30');

	if (!ActiveLoader) {
		alert(tls("Error01_웹하드 업로더 설치 도중 오류가 발생했습니다. 현재 웹브라우저창을 새로고침해 주시기 바랍니다."));
		return;
	}

	if (!ActiveLoader.Update("http://podmanage.bluepod.kr/ilark/printhome/editor/")) {
		alert(tls("Error02_웹하드 업로더 설치 도중 오류가 발생했습니다. 현재 웹브라우저창을 새로고침해 주시기 바랍니다."));
		return;
	}

	var IsSuccess = ActiveLoader.NewWork("http://" + self + "/_ilark/printhome_config.php?mode=" + mode + "&taget_cid=" + cid + "&order_name=" + order_name + "&upload_code=" + upload_code + "&mobile=" + mobile, editorType, "", 1);
	ActiveLoader.ReleaseModule();
	
	if (IsSuccess) {
		//alert('편집작업을 완료 하였습니다.');		
		if(flag == 'Y') {
			var input_id = document.createElement("input");
			input_id.setAttribute("type", "hidden");
			input_id.setAttribute("name", "upload_code");
			input_id.setAttribute("value", upload_code);

			document.frm.appendChild(input_id);
		}
		document.frm.submit();
	} else {
		alert(tls("파일주문을 취소 하였습니다."));
	}
	return IsSuccess;
}

function formSubmit(fm) {
	if (form_chk(fm)) {
		fm.submit();
	}
	else {
		return false;
	}
}