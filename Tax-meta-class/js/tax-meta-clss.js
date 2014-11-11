/**
 * All Tax meta class
 *
 * JS used for the custom fields and other form items.
 *
 * Copyright 2012 Ohad Raz (admin@bainternet.info)
 * @since 1.0
 * 
 * @package Tax Meta Class
 * 
 */

var $ =jQuery.noConflict();
function update_repeater_fields(){
    
    /**
     * WysiWyg editor
     *
     * @since 1.9.6
     */
    $(".theEditor").each(function(){
      if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
      tinyMCE.execCommand("mceAddControl", false, $(this).attr('id'));
      }
    });
    
    /**
     * Datepicker Field.
     *
     * @since 1.0
     */
    $('.at-date').each( function() {
      
      var $this  = $(this),
          format = $this.attr('rel');
  
      $this.datepicker( { showButtonPanel: true, dateFormat: format } );
      
    });
  
    /**
     * Timepicker Field.
     *
     * @since 1.0
     */
    $('.at-time').each( function() {
      
      var $this   = $(this),
          format   = $this.attr('rel');
  
      $this.timepicker( { showSecond: true, timeFormat: format } );
      
    });
  
    /**
     * Colorpicker Field.
     *
     * @since 1.0
     */
    /*
    
    
    
    /**
     * Select Color Field.
     *
     * @since 1.0
     */
    $('.at-color-select').click( function(){
      var $this = $(this);
      var id = $this.attr('rel');
      $(this).siblings('.at-color-picker').farbtastic("#" + id).toggle();
      return false;
    });
  
    /**
     * Delete File.
     *
     * @since 1.0
     */
    $('.at-upload').delegate( '.at-delete-file', 'click' , function() {
      
      var $this   = $(this),
        $parent = $this.parent(),
        data = $this.attr('rel');
          
      $.post( ajaxurl, { action: 'at_delete_file', data: data }, function(response) {
        response == '0' ? ( alert( 'File has been successfully deleted.' ), $parent.remove() ) : alert( 'You do NOT have permission to delete this file.' );
      });
      
      return false;
    
    });
  
    /**
     * Reorder Images.
     *
     * @since 1.0
     */
    $('.at-images').each( function() {
      
      var $this = $(this), order, data;
      
      $this.sortable( {
        placeholder: 'ui-state-highlight',
        update: function (){
          order = $this.sortable('serialize');
          data   = order + '|' + $this.siblings('.at-images-data').val();
  
          $.post(ajaxurl, {action: 'at_reorder_images', data: data}, function(response){
            response == '0' ? alert( 'Order saved!' ) : alert( "You don't have permission to reorder images." );
          });
        }
      });
      
    });
    
  }
jQuery(document).ready(function($) {

  /**
   * repater Field
   * @since 1.1
   */
  /*$( ".at-repeater-item" ).live('click', function() {
    var $this  = $(this);
    $this.siblings().toggle();
  });
  jQuery(".at-repater-block").click(function(){
    jQuery(this).find('table').toggle();
  });
  
  */
  //edit
  $(document).on('click','.at-re-toggle',function() {
    $(this).prev().toggle('slow');
  });
  
  
  /**
   * Datepicker Field.
   *
   * @since 1.0
   */
  $('.at-date').each( function() {
    
    var $this  = $(this),
        format = $this.attr('rel');

    $this.datepicker( { showButtonPanel: true, dateFormat: format } );
    
  });

  /**
   * Timepicker Field.
   *
   * @since 1.0
   */
  $('.at-time').each( function() {
    
    var $this   = $(this),
        format   = $this.attr('rel');

    $this.timepicker( { showSecond: true, timeFormat: format } );
    
  });

  /**
   * Colorpicker Field.
   *
   * @since 1.0
   * better handler for color picker with repeater fields support
   * which now works both when button is clicked and when field gains focus.
   */
  $(document).on('focus','.at-color', function() {
    var $this = $(this);
    $(this).siblings('.at-color-picker').farbtastic($this).toggle();
  });

  $(document).on('focusout','.at-color', function() {
    var $this = $(this);
    $(this).siblings('.at-color-picker').farbtastic($this).toggle();
  });
  
  /**
   * Helper Function
   *
   * Get Query string value by name.
   *
   * @since 1.0
   */
  function get_query_var( name ) {
    var match = RegExp('[?&]' + name + '=([^&#]*)').exec(location.href);
    return match && decodeURIComponent(match[1].replace(/\+/g, ' '));     
  }
  var simplePanelupload = (function(){
    var inited;
    var file_id;
    var file_url;
    var file_type;
    function init (){
      return {
        image_frame: new Array(),
        file_frame: new Array(),
        hooks:function(){
          $(document).on('click','.simplePanelimageUpload,.simplePanelfileUpload', function( event ){
            event.preventDefault();
            if ($(this).hasClass('simplePanelfileUpload'))
              inited.upload($(this),'file');
            else
              inited.upload($(this),'image');
          });
          $(document).on('click','.simplePanelimageUploadclear,.simplePanelfileUploadclear', function( event ){
            event.preventDefault();
            inited.set_fields($(this));
            $(inited.file_url).val("");
            $(inited.file_id).val("");
            if ($(this).hasClass('simplePanelimageUploadclear')){
              inited.set_preview('image',false);
              inited.replaceImageUploadClass($(this));
            }else{
              inited.set_preview('file',false);
              inited.replaceFileUploadClass($(this));
            }
          });
        },
        set_fields: function (el){
          inited.file_url = $(el).prev();
          inited.file_id = $(inited.file_url).prev();
        },
        upload:function(el,utype){
          inited.set_fields(el)
          if (utype == 'image')
            inited.upload_Image($(el));
          else
            inited.upload_File($(el));
        },
        upload_File: function(el){
          // If the media frame already exists, reopen it.
          var mime  = $(el).attr('data-mime_type') || '';
          var ext   = $(el).attr("data-ext") || false;
          var name  = $(el).attr('id');
          var multi = ($(el).hasClass("multiFile")? true: false);

          if ( typeof inited.file_frame[name] !== "undefined")  {
            if (ext){
              inited.file_frame[name].uploader.uploader.param( 'uploadeType', ext);
              inited.file_frame[name].uploader.uploader.param( 'uploadeTypecaller', 'my_meta_box' );
            }
            inited.file_frame[name].open();
            return;
          }
          // Create the media frame.

          inited.file_frame[name] = wp.media({
            library: {
              type: mime
            },
            title: jQuery( this ).data( 'uploader_title' ),
            button: {
              text: jQuery( this ).data( 'uploader_button_text' ),
            },
            multiple: multi  // Set to true to allow multiple files to be selected
          });


          // When an image is selected, run a callback.
          inited.file_frame[name].on( 'select', function() {
          // We set multiple to false so only get one image from the uploader
          attachment = inited.file_frame[name].state().get('selection').first().toJSON();
            // Do something with attachment.id and/or attachment.url here
            $(inited.file_id).val(attachment.id);
            $(inited.file_url).val(attachment.url);
            inited.replaceFileUploadClass(el);
            inited.set_preview('file',true);
          });
          // Finally, open the modal

          inited.file_frame[name].open();
          if (ext){
            inited.file_frame[name].uploader.uploader.param( 'uploadeType', ext);
            inited.file_frame[name].uploader.uploader.param( 'uploadeTypecaller', 'my_meta_box' );
          }
        },
        upload_Image:function(el){
          var name = $(el).attr('id');
          var multi = ($(el).hasClass("multiFile")? true: false);
          // If the media frame already exists, reopen it.
          if ( typeof inited.image_frame[name] !== "undefined")  {
            inited.image_frame[name].open();
            return;
          }
          // Create the media frame.
          inited.image_frame[name] =  wp.media({
            library: {
              type: 'image'
            },
            title: jQuery( this ).data( 'uploader_title' ),
            button: {
              text: jQuery( this ).data( 'uploader_button_text' ),
            },
            multiple: multi  // Set to true to allow multiple files to be selected
          });
          //set pre selected images
          inited.image_frame[name].on('open',function(){
            var selection = inited.image_frame[name].state().get('selection');
            var att_ids = $(inited.file_id).val().split("|");
            $.each(att_ids,function(i,v){
              if (v.length){
                attachment = wp.media.attachment(v);
                attachment.fetch();
                selection.add( attachment ? [ attachment ] : [] );
              }
            });
          });
          // When an image is selected, run a callback.
          inited.image_frame[name].on( 'select', function() {
          if (!multi){
            attachment = inited.image_frame[name].state().get('selection').first().toJSON();
            // Do something with attachment.id and/or attachment.url here
            $(inited.file_id).val(attachment.id);
            $(inited.file_url).val(attachment.url);
            inited.replaceImageUploadClass(el);
            inited.set_preview('image',true);
          }else{
            var att_ids = '';
            var att_urls = '';
            inited.image_frame[name].state().get('selection').each(function(i){
            att_ids += i.get('id') + '|';
            att_urls += i.get('url') + '|';
            });
            att_ids  = att_ids.substring(0, att_ids.length - 1);
            att_urls = att_urls.substring(0, att_urls.length - 1);
            $(inited.file_id).val(att_ids);
            $(inited.file_url).val(att_urls);
            inited.set_multiple_img_preview(true);
            }
          });
          // Finally, open the modal
          inited.image_frame[name].open();
        },
        replaceImageUploadClass: function(el){
          if ($(el).hasClass("simplePanelimageUpload")){
            $(el).removeClass("simplePanelimageUpload").addClass('simplePanelimageUploadclear').val('Remove Image');
          }else{
            $(el).removeClass("simplePanelimageUploadclear").addClass('simplePanelimageUpload').val('Upload Image');
          }
        },
        replaceFileUploadClass: function(el){
          if ($(el).hasClass("simplePanelfileUpload")){
            $(el).removeClass("simplePanelfileUpload").addClass('simplePanelfileUploadclear').val('Remove File');
          }else{
            $(el).removeClass("simplePanelfileUploadclear").addClass('simplePanelfileUpload').val('Upload File');
          }
        },
        set_preview: function(stype,ShowFlag){
          ShowFlag = ShowFlag || false;
          var fileuri = $(inited.file_url).val();
          if (stype == 'image'){
            if (ShowFlag)
              $(inited.file_id).prev().find('img').attr('src',fileuri).show();
            else
              $(inited.file_id).prev().find('img').attr('src','').hide();
          }else{
            if (ShowFlag)
              $(inited.file_id).prev().find('ul').append('<li><a href="' + fileuri + '" target="_blank">'+fileuri+'</a></li>');
            else
              $(inited.file_id).prev().find('ul').children().remove();
          }
        },
        set_multiple_img_preview: function(ShowFlag){
          ShowFlag = ShowFlag || false;
          var fileuri = $(inited.file_url).val();
          var fileids = $(inited.file_id).val();
          if (fileuri == '') return;
          fileuri = fileuri.split("|");
          fileids = fileids.split("|");
          if (ShowFlag){
            $(inited.file_id).prev().find('ul').remove();
            $(inited.file_id).prev().append('<ul class="imageSortable"></ul>')
            $.each(fileuri,function(i,f){
              $(inited.file_id).prev().find('.imageSortable').append('<li><img src="'+f+'"data-attid="'+fileids[i]+'" style="width: 80px; height: auto;"></li>');
            });
            $( ".imageSortable" ).sortable({
              placeholder: "ui-state-highlight",
              update: function( event, ui ) {
                var $ul = $(ui.item[0]).parent();
                var att_ids = '';
                var att_urls = '';
                $ul.find('img').each(function(i,v){
                  att_ids += $(v).attr('data-attid') + '|';
                  att_urls += $(v).attr('src') + '|';
                });
                att_ids = att_ids.substring(0, att_ids.length - 1);
                att_urls = att_urls.substring(0, att_urls.length - 1);
                $(inited.file_id).val(att_ids);
                $(inited.file_url).val(att_urls);
              }
            });
          }else{
            $(inited.file_id).prev().find('ul').remove();
          }
        }
      }
    }
    return {
      getInstance :function(){
        if (!inited){
          inited = init();
        }
        return inited; 
      }
    }
    })()
    /*simplePanelmedia = simplePanelupload.getInstance();
    simplePanelmedia.hooks();*/
    simplePanelupload.getInstance().hooks();

    //clear form after submit
    $( document ).ajaxComplete(function( event, xhr, settings ) {
      try{
        $respo = $.parseXML(xhr.responseText);
      
        //exit on error
        if ($($respo).find('wp_error').length) return;
        
        $($respo).find('response').each(function(i,e){
          if ($(e).attr('action').indexOf("add-tag") > -1){
            var tid = $(e).find('term_id');
            if (tid){
              clear_form($( "form[action='edit-tags.php']" ));
            }
          }
        });
      }catch(err) {}
    });
  function version_compare(a,b){
    var c=a.split('.');
    var d=b.split('.');
    for(var i=0;i<c.length;++i){
      if(d.length==i){
        return"gt";
      }
      if(c[i]==d[i]){
        continue;
      }else if(c[i]>d[i]){
        return"gt";
      }else{
        return"lt";
      }
    }
    if(c.length!=d.length){
      return"lt";
    }
    return"eq";
  }
  function clear_form(form){
    $('input[type="text"]:visible, textarea:visible', form).val('');
    //color field
    $('.at-color', form).attr('style','');
    //image upload
    $('.simplePanelImagePreview img').remove();
    $('simplePanelimageUploadclear').each(function(){
      var $field = $(this);
      //clear urls
      $field.prev().val('');
      //clear ids
      $field.prev().prev().val('');
      simplePanelupload.getInstance().replaceImageUploadClass($field);
    });
    //file upload
    $('.simplePanelfilePreview ul li').remove();
    $('simplePanelfileUploadclear').each(function(){
      var $field = $(this);
      //clear urls
      $field.prev().val('');
      //clear ids
      $field.prev().prev().val('');
      simplePanelupload.getInstance().replaceImageUploadClass($field);
    });
    //repeater
    $(".at-repater-block").remove();
    //select
    $('select',form).val('');
    //checkboxes
    $( "input[type='checkbox']",form ).prop('checked',false);
    //radio
    $( "input[type='radio']",form ).prop('checked',false);
  }
  /** pre bind jquery function **/
  $.fn.preBind = function (type, data, fn) {
    this.each(function () {
        var $this = $(this);

        $this.bind(type, data, fn);
        if (version_compare($.fn.jquery,'1.8') == 'lt')
          var currentBindings = $this.data('events')[type];
        else
          var currentBindings = $._data(this, "events")[type];
        if ($.isArray(currentBindings)) {
            currentBindings.unshift(currentBindings.pop());
        }
    });
    return this;
  };
  /** fix tinymce not saving on add screen*/
  $('#submit').preBind('click', function() {
    if(typeof tinymce !== "undefined" && $('input[name=action]').val() == 'add-tag'){
      $.each(tinymce.editors,function(i,editor){
        var tx = editor.targetElm;
        $(tx).html(editor.getContent());
      });
    }
  });
});
var $respo;