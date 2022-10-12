<?

function f_getCouponCnt($limit='')
{
	global $db,$sess,$cid;

	$query = "
	select
		count(*)
	from
		exm_coupon a
		inner join exm_coupon_set b on a.cid = b.cid and a.coupon_code = b.coupon_code and mid = '$sess[mid]'
	where
		a.cid = '$cid'
		and coupon_use = 0
		and
		(
			(
			coupon_period_system = 'date'
			and coupon_period_sdate <= curdate()
			and coupon_period_edate >= curdate()
			) or
			(
			coupon_period_system = 'deadline'
			and adddate(coupon_setdt,interval coupon_period_deadline day) >= adddate(curdate(),interval 1 day)
			) or
			(
			coupon_period_system = 'deadline_date'
			and coupon_period_deadline_date >= curdate()
			) 
		)
	";

	list($ret) = $db->fetch($query,1);

	return $ret;

}

?>