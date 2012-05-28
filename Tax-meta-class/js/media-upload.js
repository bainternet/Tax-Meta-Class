(function($){ 
  // detect when a image has been added to list after upload
  function tmc_add_buttons()
  {
    // add buttons to media items
    $('#media-items .media-item:not(.tmc-active)').each(function(){     

      // needs attachment ID
      if($(this).children('input[id*="type-of-"]').length == 0){ return false; }
      
      // only once!
      $(this).addClass('tmc-active');
      
      // find id
      var id = $(this).children('input[id*="type-of-"]').attr('id').replace('type-of-', '');
      
      var link = $('<a href="#" class="button tmc-select">Select Image</a>');
      link.click(function(){
        $('input[name="send[' + id + ']"]').click();
        return false;
      });

      // change text of insert button, and add new button
      $(this).find('.filename.new').append(link);
    });
  }
  
  // run the tmc_add_buttons ever 500ms when on the image upload tab
  var acf_t = setInterval(function(){
    tmc_add_buttons();
  }, 500);
  
  // add input filters to allow for tab navigation
  $(document).ready(function(){  
    setTimeout(function(){
      tmc_add_buttons();
    }, 1);
  });
        
})(jQuery);