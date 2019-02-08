
/*  global $ */
$(function() {
  $("#toggle_postform").on('click',function(e){
    $(".postform_hidden").toggle("fast");
    $(this).hide();
  });

});


