//sns
function goTwitter(msg,url) {
	var href = "https://twitter.com/intent/tweet?text=" + encodeURIComponent(msg) + "&url=" + encodeURIComponent(url);
	var a = window.open(href, 'twitter', '');
	if ( a ) {
		a.focus();
	}
}

function goFaceBook(msg,url) {
	var href = "http://www.facebook.com/sharer/sharer.php?t=" + encodeURIComponent(msg) + "&u=" + encodeURIComponent(url);
	var a = window.open(href, 'facebook', '');
	if ( a ) {
		a.focus();
	}
}

function goKakaoStory(msg,url) {
	var href = "https://story.kakao.com/share?text=" + encodeURIComponent(url) + "&url=" + encodeURIComponent(msg);
	var a = window.open(href, 'facebook', '');
	if ( a ) {
		a.focus();
	}
}


function sns_popup(src,width,height) {
	var scrollbars = "1";
	var resizable = "no";
	if (typeof(arguments[3])!="undefined") scrollbars = arguments[3];
	if (arguments[4]) resizable = "yes";
	window.open(src,'','width='+width+',height='+height+',scrollbars='+scrollbars+',toolbar=no,status=no,resizable='+resizable+',menubar=no,left=0,top=0');
}