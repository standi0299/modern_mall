(function($){
	$(document).ready(function(){			
		$(window).scroll(function() {
			// 현재 스크롤 위치를 가져온다.
			
			var bannerHeight = $( '#banner-area' ).outerHeight();
      var scrollTop = $( document ).scrollTop();

      if( scrollTop > bannerHeight ) {
    		$('#header-top').hide();
        $('#header-wrap' ).css( { position: 'relative', height: '60px' } );
        $('#site-header' ).css( { position: 'fixed', top: 0, height: '60px' } );            
        $('#content-wrapper #content' ).css( { paddingTop: ( 217 + 60 ) + 'px' } );
        $('#header-area .header-content' ).css( { paddingTop: '17px' } );
        $('#header-area #logo' ).css( { paddingTop: 0 } );
        $('.nav-item-wrap .snb' ).css( { top: '64px' } );
    	}
    	else {
    		$('#header-top').show();
    		$('#header-wrap' ).css( { position: 'relative', height: '114px' } );
        $('#site-header' ).css( { position: 'relative', height: '114px' } );
        $('#content-wrapper #content' ).css( { paddingTop: '217px' } );
        $('#header-area .header-content' ).css( { paddingTop: '57px' } );
        $('#header-area #logo' ).css( { paddingTop: '40px' } );
        $('.nav-item-wrap .snb' ).css( { top: '104px' } );
    	}			
		}).scroll();
	});
})(jQuery1_11_0);

