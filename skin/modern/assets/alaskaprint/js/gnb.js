$(function(){
var gnb = {
    init: function() {
		var self = this;	//gnb obj
		$('.gnb_dp1_list .gnb_dp1 > a').mouseenter(function(e) {
			if($(this).next().is(':visible')) {
				//self.closeMenu();
			} else {
				$('.gnb_dp2_wrap:visible').hide();
				self.closeMenu();
				self.openMenu(e.target);
			}
        });
        $('#headerTop').mouseleave(function() {
			self.closeMenu();
        });
		
		$('.gnb_dp2 > a').mouseenter(function(e) {
			elem = $(this);
			$(this).addClass('on');
			$(this).next().addClass('on');
			$('.gnb_dp2 > a').not(elem).removeClass("on");
			$('.gnb_dp2 > a').not(elem).next().removeClass("on");
		});
		$('.gnb_dp2 > a').mouseleave(function() {
			//$(this).removeClass("on");
			//$(this).next().removeClass("on");
		});
    },
	openMenu: function(target) {
		$(target).parent().addClass('mn-active');
		$(target).next().slideDown(300);
		$(target).parent().find('.gnb_dp2').first().children('a').addClass('on');
		$(target).parent().find('.gnb_dp2').first().children('.dp2_img').addClass('on');
	},
	closeMenu: function() {
		$('.gnb_dp2_wrap:visible').slideUp(300);
		$('.gnb_dp1_list > .gnb_dp1.mn-active').removeClass('mn-active');
		$('.gnb_dp2 > a').removeClass('on');
		$('.gnb_dp2 .dp2_img').removeClass('on');
	}
};
gnb.init();    
});    


