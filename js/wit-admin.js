/*! Widgets In Tabs */
jQuery(function($) {
  $(document.body).on('change', 'input[id$="wit-animate"]', function(){
    if (this.checked) {
      $(this).siblings('[id$="wit-animate-controls"]').slideDown();
    } else {
      $(this).siblings('[id$="wit-animate-controls"]').slideUp();
    }
  });
  $(document.body).on('change', 'input[id$="wit-extra"]', function(){
    if (this.checked) {
      $(this).siblings('[id$="wit-extra-controls"]').slideDown();
    } else {
      $(this).siblings('[id$="wit-extra-controls"]').slideUp();
    }
  });
  $(document.body).on('change', 'input[id$="wit_extra_closable_tabs"]', function(){
    if (this.checked) {
      $('input[id$="wit_extra_closable_tabs_init"]').parent().slideDown();
    } else {
      $('input[id$="wit_extra_closable_tabs_init"]').parent().slideUp();
    }
  });
});