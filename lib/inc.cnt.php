<?

### 로그 분석
$_log_hour = sprintf("%02d",date("H"));

$log_cfg[newtime] = 30; # 신규 및 재방문자를 구분하는 time gap (단위 : day)

if (!$_COOKIE[exm_log]){

	unset($ukey);

	# 아이피,리퍼러 저장
	$query = "
	insert into	exm_cnt_ip set
		`cid`		= '$cid',
		`day`		= curdate()+0,
		`ip`		= '$_SERVER[REMOTE_ADDR]',
		`referer`	= '$_SERVER[HTTP_REFERER]',
		`regdt`		= now()
	on duplicate key update
		day			= day	
	";
	$db->query($query);

	if ($db->count==1){
		$ukey = (time()-$_COOKIE[exm_log_time]>60*60*24*$log_cfg[newtime]) ? "u" : "r";
		$query = "insert into exm_cnt (cid,day,$ukey,u{$_log_hour}) values (curdate()+0,1,1) on duplicate key update $ukey=$ukey+1, u{$_log_hour}=u{$_log_hour}+1";
		$query = "
		insert into exm_cnt set
			`cid`			= '$cid',
			`day`			= curdate()+0,
			`$ukey`			= 1,
			`u{$_log_hour}`	= 1
		on duplicate key update
			`$ukey`			= `$ukey`+1,
			`u{$_log_hour}`	= `u{$_log_hour}`+1
		";
		$db->query($query);
	}

	$r = getSiteKeyword($_SERVER[HTTP_REFERER]);

	$query = ($ukey) ? "
		insert into exm_cnt_log set
			`cid`		= '$cid',
			`day`		= curdate()+0,
			`host`		= '$r[host]',
			`site`		= '$r[site]',
			`keyword`	= '$r[keyword]',
			`adtype`	= '$r[adtype]',
			`hit`		= 1,
			`u`			= 1
		on duplicate key update
			`hit`		= `hit`+1,
			`u`			= `u`+1
		":"
		insert into exm_cnt_log set
			`cid`		= '$cid',
			`day`		= curdate()+0,
			`host`		= '$r[host]',
			`site`		= '$r[site]',
			`keyword`	= '$r[keyword]',
			`adtype`	= '$r[adtype]',
			`hit`		= 1
		on duplicate key update
			`hit`		= `hit`+1
		";
	$db->query($query);

	setCookie("exm_log",date("Ymd"),0,"/");
	setCookie("exm_log_time",time(),time()+60*60*24*32,"/");

}
setCookie("exm_log",date("Ymd"),0,"/");
### 전체로그 갱신
$query = "
update exm_cnt set
	`p`				= p+1,
	`p{$_log_hour}`	= `p{$_log_hour}`+1
where
	`cid` = '$cid'
	and `day` = curdate()+0
";
$db->query($query);

?>