$( function() {
    $( '.tab-content' ).hide();
    $( '.tab-content:first' ).show();

    $( 'ul.tab-menu li' ).click( function() {
        $( 'ul.tab-menu li' ).removeClass( 'active' );
        $( this ).addClass( 'active' );
        $( '.tab-content' ).hide();

        var activeTab = $( this ).attr( 'rel' );
        $( '#' + activeTab ).show();
    } );

    // 상단 네비게이션
    $( '.nav-item-wrap' ).on( 'mouseenter', function( $event ) {
        var item = $( $event.currentTarget );

        if( item.has( '.snb' ) ) {
            $( '.nav-item-wrap .snb' ).hide();
            $( '.nav-item-wrap .nav-item-bullet' ).hide();
            item.find( '.snb' ).show();
            item.find( '.nav-item-bullet' ).show();
        }

        if( item.data( 'interval-id' ) ) {
            clearTimeout( item.data( 'interval-id' ) );
            item.data( 'interval-id', null );
        }
    } );
    $( '.nav-item-wrap' ).on( 'mouseleave', function( $event ) {
        var item = $( $event.currentTarget );
        var id = setTimeout( function() {
            if( item.has( '.snb' ) ) {
                item.find( '.snb' ).hide();
                item.find( '.nav-item-bullet' ).hide();
            }
            item.data( 'interval-id', null );
        }, 500 );
        item.data( 'interval-id', id );
    } );
    // 상단 네비게이션 끝

    $( '.bxslider' ).each( function( $index, $element ) {
        $element = $( $element );
        var params = {};
        params.mode = $element.data( 'mode' );
        params.infiniteLoop = $element.data( 'infinite-loop' );
        params.minSlides = $element.data( 'min-slides' );
        params.maxSlides = $element.data( 'max-slides' );
        params.slideWidth = $element.data( 'slide-width' );
        params.slideMargin = $element.data( 'slide-margin' );
        params.pagerCustom = $element.data( 'pager-custom' );

        if( $element.bxSlider ) {
            $element.bxSlider( params );
        }
    } );

    $( '.select-box select' ).on( 'change', function( $event ) {
        var scope = $( $event.currentTarget );
        var selectAmount = scope.children( 'option:selected' ).text();
        scope.siblings( 'label' ).text( selectAmount );
    } );

    $( '#refund-type input[name=refund]' ).on( 'change', function() {
        if( $( '#refund-type #refund-count' ).prop( 'checked' ) ) {
            $( '.refund-count-data' ).show();
        } else {
            $( '.refund-count-data' ).hide();
        }
    } );

    $( '#refund-type input[name=refund]' ).trigger( 'change' );

    $( '#option-data-wrap input[name=option-data]' ).on( 'change', function() {
        if( $( '#option-data-wrap #option-data' ).prop( 'checked' ) ) {
            $( '#option-data-input' ).show();
        } else {
            $( '#option-data-input' ).hide();
        }
    } );

    $( '#option-data-wrap input[name=option-data]' ).trigger( 'change' );

    $( '.description-wrap .description-content' ).hide();

    $( '.description-wrap .description-btn' ).on( 'click', function() {
        $( '.description-wrap .description-content' ).slideToggle( 'fast' );

        if( $( '.description-wrap' ).hasClass( 'active' ) ){
            $( '.description-wrap' ).removeClass( 'active' );
        } else {
            $( '.description-wrap' ).addClass( 'active' );
        }
    } );

    $('.detail-bxslider').bxSlider({
        buildPager: function(slideIndex){
            switch(slideIndex){
                case 0:
                    return '<img src="https://lorempixel.com/65/65/food/1/65x65">';
                case 1:
                    return '<img src="https://lorempixel.com/65/65/food/2/65x65">';
                case 2:
                    return '<img src="https://lorempixel.com/65/65/food/3/65x65">';
                case 3:
                    return '<img src="https://lorempixel.com/65/65/food/4/65x65">';
                case 4:
                    return '<img src="https://lorempixel.com/65/65/food/5/65x65">';
                case 5:
                    return '<img src="https://lorempixel.com/65/65/food/6/65x65">';
            }
        }
    });

    $('.event-bxslider').bxSlider({
        buildPager: function(slideIndex){
            switch(slideIndex){
                case 0:
                    return '<span>1</span>';
                case 1:
                    return '<span>2</span>';
                case 2:
                    return '<span>3</span>';
                case 3:
                    return '<span>4</span>';
                case 4:
                    return '<span>5</span>';
            }
        }
    } );

    // 링크가 빈 해시태그일때 클릭 이벤트 취소
    $( 'a[href="#"]' ).on( 'click', function( $event ) {
        $event.preventDefault();
    } );
    // 링크가 빈 해시태그일때 클릭 이벤트 취소 끝

    // 스크롤시 헤더 크기 조정
    $( window ).on( 'scroll', function() {
        var bannerHeight = $( '#banner-area' ).outerHeight();
        var scrollTop = $( document ).scrollTop();

        if( scrollTop > bannerHeight ) {
        	$( '#header-top' ).hide();
            $( '#site-header' ).css( { position: 'fixed', top: 0, height: '60px' } );
            $( '#content-wrapper #content' ).css( { paddingTop: ( 217 + 60 ) + 'px' } );
            $( '#header-area .header-content' ).css( { paddingTop: '17px' } );
            $( '#header-area #logo' ).css( { paddingTop: 0 } );
            $( '.nav-item-wrap .snb' ).css( { top: '64px' } );
        }
        else {
        	$( '#header-top' ).show();
            $( '#site-header' ).css( { position: 'relative', height: '114px' } );
            $( '#content-wrapper #content' ).css( { paddingTop: 217 + 'px' } );
            $( '#header-area .header-content' ).css( { paddingTop: '57px' } );
            $( '#header-area #logo' ).css( { paddingTop: '40px' } );
            $( '.nav-item-wrap .snb' ).css( { top: 104 + 'px' } );
        }
    } );
    // 스크롤시 헤더 크기 조정 끝

    // 아코디언 메뉴
    $( '.accordion-list .question-title' ).on( 'click', function( $event ) {
        var item = $( $event.currentTarget );
        var answer = item.next();
        if( answer.css( 'display' ) === 'none' ) {
            item.next().show();
        }
        else {
            item.next().hide();
        }

    } );
    // 아코디언 메뉴 끝

    // 퀵메뉴
    $( '#quick-menu-wrap #quick-toggle-btn' ).on( 'click', function() {
        var quickMenu = $( '#quick-menu-wrap' );
        if( quickMenu.data( 'is-open' ) === false ) {
            quickMenu.data( 'is-open', true );
            quickMenu.addClass( 'active' );
            quickMenu.css( { right: 0 } );
        } else {
            quickMenu.data( 'is-open', false );
            quickMenu.removeClass( 'active' );
            quickMenu.css( { right: '-183px' } );
        }
    } );
    // 퀵메뉴 끝

    // 포토북 상세에 퀵옵션
    $( '.quick-option-wrap #quick-toggle-btn' ).on( 'click', function() {
        $( '.quick-option-wrap' ).toggleClass( 'active' );
    } );
    // 포토북 상세에 퀵옵션 끝

    // 결제 모달
    $( '#list-destination-modal' ).hide();
    $( '#edit-destination-point' ).hide();
    $( '#add-destination-point' ).hide();

    $( '.modal-close' ).on( 'click', function() {
        $( '#list-destination-modal' ).hide();
        $( '#edit-destination-point' ).hide();
        $( '#add-destination-point' ).hide();
        $( '.modal-bg' ).hide();
    } );

    $( '.select-destination-point-btn' ).on( 'click', function() {
        $( '#list-destination-modal' ).hide();
        $( '#edit-destination-point' ).hide();
        $( '#add-destination-point' ).hide();
        $( '.modal-bg' ).hide();
    } );

    $( '#delivery-list-btn' ).on( 'click', function() {
        $( '#list-destination-modal' ).show();
        $( '#edit-destination-point' ).hide();
        $( '#add-destination-point' ).hide();
        $( '.modal-bg' ).show();
    } );

    $( '.edit-destination-point-btn' ).on( 'click', function() {
        $( '#edit-destination-point' ).show();
        $( '#list-destination-modal' ).hide();
        $( '#add-destination-point' ).hide();
        $( '.modal-bg' ).show();
    } );

    $( '#add-destination-point-btn' ).on( 'click', function() {
        $( '#add-destination-point' ).show();
        $( '#list-destination-modal' ).hide();
        $( '#edit-destination-point' ).hide();
        $( '.modal-bg' ).show();
    } );

    // 결제 모달 끝

    // 마이페이지>주문/배송 조회 주문목록 모달
    $( '#old-history-btn' ).on( 'click', function() {
        $( '#order-history-modal' ).show();
        $( '.modal-bg' ).show();
    } );

    $( '.close-btn' ).on( 'click', function() {
        $( '#order-history-modal' ).hide();
        $( '.modal-bg' ).hide();
    } );
    // 마이페이지>주문/배송 조회 주문목록 모달 끝
} );