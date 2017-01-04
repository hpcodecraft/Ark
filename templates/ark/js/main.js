$(function() {
  var pageJumpVisible = false;

  $('.jump-to-page').on('click', function(e) {
    e.preventDefault();
    pageJumpVisible = !pageJumpVisible;
    $('.all-pages').css('display', pageJumpVisible ? 'block' : 'none');
  });

  $(document).on('keyup', function(e) {
    var el;
    switch(e.keyCode) {
      case 75: // k
      case 39: // arrow right
          // go to next page
          el = $('.pageright:last');
          if(!el.hasClass('hidden')) {
            document.location.href = el.prop('href');
          }
          break;

      case 74: // j
      case 37: // arrow left
          // go to previous page
          el = $('.pageleft:last');
          if(!el.hasClass('hidden')) {
            document.location.href = el.prop('href');
          }
          break;
    }
  });
});
