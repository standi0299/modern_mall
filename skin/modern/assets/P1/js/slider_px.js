$('.main_banner_px').each(function() {
  var $this = $(this);
  var $group = $this.find('.slide_group');
  var $slides = $this.find('.slide');
  var bulletArray = [];
  var currentIndex = 0;
  var timeout;
  
  function move(newIndex) {
    var animateLeft, slideLeft;
    advance();
    
   // if ($group.is(':animated') || currentIndex === newIndex) {
    //  return;
   // }
	$('#'+currentIndex).removeClass('active');
    $('#'+newIndex).addClass('active');
    
    
   // if (newIndex > currentIndex) {                                      슬라이드가 어떤 모양으로 넘어 가는지
  //    slideLeft = '100%';
  //    animateLeft = '-100%';
  //  } else {
 //     slideLeft = '-100%';
 //     animateLeft = '100%';
  //  }
    
    $slides.eq(newIndex).css({
      display: 'block',
      left: slideLeft
    });
    $group.animate({
      left: animateLeft
    }, function() {
      if (currentIndex != newIndex) {
	      $slides.eq(currentIndex).css({
	        display: 'none'
	      });
      }
      $slides.eq(newIndex).css({
        left: 0
      });
      $group.css({
        left: 0
      });
      currentIndex = newIndex;
    });
  }
  
  function advance() {                  // 여기가 넘어 가는거 ,고정, 속도 정함
    clearTimeout(timeout);
    timeout = setTimeout(function() {
      if (currentIndex < ($slides.length - 1)) {
        move(parseInt(currentIndex) + 1);                // 넘어 가는 페이지 수
      } else {
        move(0);
      }
    }, 4900);
  }
  
  $('.next_btn').on('click', function() {                                 //화살표 누를시 다음 페이지로 넘어 가라
    if (currentIndex < ($slides.length - 1)) {
      move(parseInt(currentIndex) + 1);
    } else {
      move(0);
    }
  });
  
  $('.previous_btn').on('click', function() {                    //화살표 누를시 이전 페이지로 넘어 가라
    if (currentIndex !== 0) {
      move(parseInt(currentIndex) - 1);
    } else {
      move(3);
    }
  });
 
  $('.slide_btn').on('click', function() {                                 //화살표 누를시 다음 페이지로 넘어 가라
    move(this.id);
  });
  
  advance();
});