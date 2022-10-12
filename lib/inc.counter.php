<?

### 로그 분석
$_log_hour = sprintf("%02d",date("H"));

if (!$_COOKIE[exmLog]){
	
  //referrer ' 오류로 인해 삭제 처리   20150202    chunter
  $_SERVER[HTTP_REFERER] =  str_replace("'", "", $_SERVER[HTTP_REFERER]);
  
//	$_SERVER[HTTP_REFERER] = "http://search.naver.com/search.naver?sm=top_txt&where=nexearch&ie=utf8&query=%EB%82%98%EC%98%81%EC%84%9D%20%EB%A8%B9%ED%8A%80PD%20%EA%B5%B4%EC%9A%95";
	/* 레퍼러 분석 */
	$r = getSiteKeyword($_SERVER[HTTP_REFERER]);

	/* 재방문체크 */
	list($chk) = $db->fetch("select day from exm_counter_ip where cid = '$cid' and day = curdate()+0 and ip = '$_SERVER[REMOTE_ADDR]'",1);

	# tb_counter2
	/*
		순방문 : u 를 업데이트
		재방문 : r 을 업데이트
	*/
	$query = (!$chk) ? "
	insert into exm_counter set
		cid		= '$cid',
		day		= curdate()+0,
		u		= 1,
		u{$_log_hour}	= 1
	on duplicate key update
		u		= u+1,
		u{$_log_hour}	= u{$_log_hour}+1
	"
	: "
	insert into exm_counter set
		cid		= '$cid',
		day		= curdate()+0,
		r		= 1,
		r{$_log_hour}	= 1
	on duplicate key update
		r		= r+1,
		r{$_log_hour}	= r{$_log_hour}+1
	";
	$db->query($query);
	# tb_counter2 /

	
	# tb_counter_log2
	/*
		순방문 : u 를 업데이트
		재방문 : r 을 업데이트
	*/
	$query = (!$chk) ? "
	insert into exm_counter_log set
		cid		= '$cid',
		day		= curdate()+0,
		host	= '$r[host]',
		site	= '$r[site]',
		keyword = '$r[keyword]',
		adtype	= '$r[adtype]',
		u		= 1,
		u{$_log_hour}	= 1
	on duplicate key update
		u	= u+1,
		u{$_log_hour}	= u{$_log_hour}+1
	"
	: "
	insert into exm_counter_log set
		cid		= '$cid',
		day		= curdate()+0,
		host	= '$r[host]',
		site	= '$r[site]',
		keyword = '$r[keyword]',
		adtype	= '$r[adtype]',
		r		= 1,
		r{$_log_hour}	= 1
	on duplicate key update
		r	= r+1,
		r{$_log_hour}	= r{$_log_hour}+1
	";
	$db->query($query);
	$logno = $db->id;
	# tb_counter_log2 /
	

	$query = "
	insert into exm_counter_ip set
		cid		= '$cid',
		day		= curdate()+0,
		ip		= '$_SERVER[REMOTE_ADDR]',
		referer	= '$_SERVER[HTTP_REFERER]',
		regdt	= now(),
		logno	= '$logno'
	on duplicate key update
		day	= day
	";
	$db->query($query);

	setCookie("exmLog",date("Ymd"),0,"/");
}


$query = "
insert into exm_counter set
	cid		= '$cid',
	day		= curdate()+0,
	p		= 1,
	p{$_log_hour}	= 1
on duplicate key update
	p	= p+1,
	p{$_log_hour}	= p{$_log_hour}+1
";
$db->query($query);

?>