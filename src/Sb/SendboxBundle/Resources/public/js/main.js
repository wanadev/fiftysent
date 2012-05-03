var timeout; 
   $(function() {
  $(".dropdown").hover(function(){
    $(this).parent().find('.dropdown-menu').show();
    $(this).addClass('open');
    clearTimeout(timeout);
  },
  function(){
    timeout = setTimeout(hideSubMenu, 500);
  });
});
function hideSubMenu() {
  $('.dropdown-menu').hide();
  $(".dropdown").removeClass('open');
  clearTimeout(timeout);
}
  

   

