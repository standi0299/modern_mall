/**
 * chkForm(form)
 *
 * 입력박스의 null 유무 체크와 패턴 체크
 *
 * @Usage	<form onSubmit="return chkForm(this)">
 */
function chkForm(form)
{
	for (i=0;i<form.elements.length;i++){
		currEl = form.elements[i];
		if (currEl.disabled) continue;
		if (currEl.getAttribute("required")!=null){
			if (currEl.type=="checkbox" || currEl.type=="radio"){
				if (!chkSelect(form,currEl,currEl.getAttribute("msg"))) return false;
			} else {
				if (!chkText(currEl,currEl.value,currEl.getAttribute("msg"))) return false;
			}
		}
		if (currEl.getAttribute("option")!=null && currEl.value.length>0){
			if (!chkPatten(currEl,currEl.getAttribute("option"),currEl.getAttribute("msgO"))) return false;
		}
		if (currEl.getAttribute("minlength")!=null){
			if (!chkLength(currEl,currEl.getAttribute("minlength"))) return false;
		}
	}
	if (form.password2){
		if (form.password.value!=form.password2.value){
			alert(tls("비밀번호가 일치하지 않습니다"));
			//form.password.value = "";
			form.password2.value = "";
			form.password2.focus();
			return false;
		}
	}

	if (form.chkspam) form.chkspam.value = "1";
	if (form['resno[]'] && (form['resno[]'][0].value || form['resno[]'][1].value) && !chkResno(form['resno[]'][0].value + form['resno[]'][1].value)) return false;
	return true;
}

function chkLength(field,len)
{
	text = field.value;
	if (text.trim().length<len){
		alert(len + tls("자 이상 입력하셔야 합니다"));
		field.focus();
		return false;
	}
	return true;
}

function chkText(field,text,msg)
{
	text = text.replace("　", "");
	text = text.replace(/\s*/, "");
	if (text==""){
		var idx = field.parentNode.cellIndex;		
		var caption = (idx>=1) ? getInnerText(field.parentNode.parentNode.cells[--idx]) : "";
		caption = (caption)? caption.replace("*","") : "";
		if (!field.getAttribute("label")) field.setAttribute("label",(caption)?caption:field.name);
		var msgAct = (field.tagName!="SELECT") ? ("입력") : tls("선택");
		if (!msg){
			var label = field.getAttribute("label");
			//label = _tsl(label);
			msg = label + label.josa(tls("을/를")) + " " + msgAct + tls("해주세요");
		}
		alert(msg);
		if (field.tagName!="SELECT") field.value = "";
		if (field.type!="hidden" && field.style.display!="none") field.focus();
		else document.body.focus();
		return false;
	}
	return true;
}

function chkSelect(form,field,msg)
{
	var ret = false;
	fieldname = eval("form.elements['"+field.name+"']");
	if (fieldname.length){
		for (j=0;j<fieldname.length;j++) if (fieldname[j].checked) ret = true;
	} else {
		if (fieldname.checked) ret = true;
	}
	if (!ret){
		var idx = field.parentNode.cellIndex;		
		var caption = (idx>=1) ? getInnerText(field.parentNode.parentNode.cells[--idx]) : "";
		caption = (caption)? caption.replace("*","") : "";
		if (!field.getAttribute("label")) field.setAttribute("label",(caption)?caption:field.name);
		if (!msg){
			var label = field.getAttribute("label");
			//label = _tsl(label);
			msg = label + label.josa(tls("을/를")) + " " + tls("선택해주세요");
		}
		alert(msg); field.focus();
		return false;
	}
	return true;
}

function chkPatten(field,patten,msg)
{
	var regNum			= /^[0-9]+$/;
	var regEmail		= /^[_a-zA-Z0-9-]+@[._a-zA-Z0-9-]+\.[a-zA-Z]+$/;
	var regUrl			= /^(http\:\/\/)*[.a-zA-Z0-9-]+\.[a-zA-Z]+$/;
	var regAlpha		= /^[a-zA-Z]+$/;
	var regHangul		= /[가-힣]/;
	var regHangulEng	= /[가-힣a-zA-Z]/;
	var regHangulOnly	= /^[가-힣]*$/;
	var regId			= /^[a-zA-Z0-9]{1}[a-zA-Z0-9_-]{3,9}$/;
	var regPass			= /^[a-zA-Z0-9_-]{4,12}$/;
	var regFileName		= /^[.a-zA-Z0-9_-]+$/;

	patten = eval(patten);
	if (!patten.test(field.value)){
		var caption = field.parentNode.parentNode.firstChild.innerText;
		caption = (caption)? caption.replace("*","") : "";
		if (!field.getAttribute("label")) field.setAttribute("label",(caption)?caption:field.name);
		if (!msg){
			var label = field.getAttribute("label");
			//label = _tsl(label);
			msg = label + tls("의 입력형식이 잘못되었습니다");
		}
		if (!arguments[3]){
			alert(msg); field.focus(); field.value += "";
		}
		return false;
	}
	return true;
}

function chkResno(resno)
{
	fmt = /^\d{6}[1234]\d{6}$/;
	if (!fmt.test(resno)) {
		if (!arguments[1]) alert(tls("잘못된 주민등록번호입니다.")); 
		return false;
	}

	birthYear = (resno.charAt(6) <= '2') ? '19' : '20';
	birthYear += resno.substr(0, 2);
	birthMonth = resno.substr(2, 2) - 1;
	birthDate = resno.substr(4, 2);
	birth = new Date(birthYear, birthMonth, birthDate);

	if ( birth.getYear()%100 != resno.substr(0, 2) || birth.getMonth() != birthMonth || birth.getDate() != birthDate) {
		if (!arguments[1]) alert(tls("잘못된 주민등록번호입니다."));
		return false;
	}

	buf = new Array(13);
	for (i = 0; i < 13; i++) buf[i] = parseInt(resno.charAt(i));

	multipliers = [2,3,4,5,6,7,8,9,2,3,4,5];
	for (i = 0, sum = 0; i < 12; i++) sum += (buf[i] *= multipliers[i]);

	if ((11 - (sum % 11)) % 10 != buf[12]) {
		if (!arguments[1]) alert(tls("잘못된 주민등록번호입니다."));
		return false;
	}
	return true;
}