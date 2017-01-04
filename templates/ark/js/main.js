$(function() {
  var pageJumpVisible = false;

  $('.jump-to-page').on('click', function(e) {
    e.preventDefault();
    pageJumpVisible = !pageJumpVisible;
    $('.all-pages').css('display', pageJumpVisible ? 'block' : 'none');
  });
});
