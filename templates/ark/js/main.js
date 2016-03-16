$(function() {
  console.log('load!');

  var pageJumpVisible = false;

  $('.jump-to-page').on('click', function(e) {
    e.preventDefault();

    console.log('click');
    pageJumpVisible = !pageJumpVisible;
    $('.all-pages').css('display', pageJumpVisible ? 'block' : 'none');
  });
});
