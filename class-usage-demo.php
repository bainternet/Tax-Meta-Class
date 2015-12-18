<?php
/*
Plugin Name: Demo Tax meta class
Plugin URI: https://en.bainternet.info
Description: Tax meta class usage demo
Version: 2.1.0
Author: Bainternet, Ohad Raz
Author URI: https://en.bainternet.info
*/


//include the main class file
require_once("Tax-meta-class/Tax-meta-class.php");
if (is_admin()){
  /* 
   * prefix of meta keys, optional
   */
  $prefix = 'ba_';
  /* 
   * configure your meta box
   */
  $config = array(
    'id' => 'demo_meta_box',          // meta box id, unique per meta box
    'title' => 'Demo Meta Box',          // meta box title
    'pages' => array('category'),        // taxonomy name, accept categories, post_tag and custom taxonomies
    'context' => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
    'fields' => array(),            // list of meta fields (can be added by field arrays)
    'local_images' => false,          // Use local or hosted images (meta box images for add/remove)
    'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
  );
  
  
  /*
   * Initiate your meta box
   */
  $my_meta =  new Tax_Meta_Class($config);
  
  /*
   * Add fields to your meta box
   */
  
  //text field
  $my_meta->addText($prefix.'text_field_id',array('name'=> __('My Text ','tax-meta'),'desc' => 'this is a field desription'));
  //textarea field
  $my_meta->addTextarea($prefix.'textarea_field_id',array('name'=> __('My Textarea ','tax-meta')));
  //checkbox field
  $my_meta->addCheckbox($prefix.'checkbox_field_id',array('name'=> __('My Checkbox ','tax-meta')));
  //select field
  $my_meta->addSelect($prefix.'select_field_id',array('selectkey1'=>'Select Value1','selectkey2'=>'Select Value2'),array('name'=> __('My select ','tax-meta'), 'std'=> array('selectkey2')));
  //radio field
  $my_meta->addRadio($prefix.'radio_field_id',array('radiokey1'=>'Radio Value1','radiokey2'=>'Radio Value2'),array('name'=> __('My Radio Filed','tax-meta'), 'std'=> array('radionkey2')));
  //date field
  $my_meta->addDate($prefix.'date_field_id',array('name'=> __('My Date ','tax-meta')));
  //Time field
  $my_meta->addTime($prefix.'time_field_id',array('name'=> __('My Time ','tax-meta')));
  //Color field
  $my_meta->addColor($prefix.'color_field_id',array('name'=> __('My Color ','tax-meta')));
  //Image field
  $my_meta->addImage($prefix.'image_field_id',array('name'=> __('My Image ','tax-meta')));
  //file upload field
  $my_meta->addFile($prefix.'file_field_id',array('name'=> __('My File ','tax-meta')));
  //wysiwyg field
  $my_meta->addWysiwyg($prefix.'wysiwyg_field_id',array('name'=> __('My wysiwyg Editor ','tax-meta')));
  //taxonomy field
  $my_meta->addTaxonomy($prefix.'taxonomy_field_id',array('taxonomy' => 'category'),array('name'=> __('My Taxonomy ','tax-meta')));
  //posts field
  $my_meta->addPosts($prefix.'posts_field_id',array('args' => array('post_type' => 'page')),array('name'=> __('My Posts ','tax-meta')));
  
  /*
   * To Create a reapeater Block first create an array of fields
   * use the same functions as above but add true as a last param
   */
  
  $repeater_fields[] = $my_meta->addText($prefix.'re_text_field_id',array('name'=> __('My Text ','tax-meta')),true);
  $repeater_fields[] = $my_meta->addTextarea($prefix.'re_textarea_field_id',array('name'=> __('My Textarea ','tax-meta')),true);
  $repeater_fields[] = $my_meta->addCheckbox($prefix.'re_checkbox_field_id',array('name'=> __('My Checkbox ','tax-meta')),true);
  $repeater_fields[] = $my_meta->addImage($prefix.'image_field_id',array('name'=> __('My Image ','tax-meta')),true);
  
  /*
   * Then just add the fields to the repeater block
   */
  //repeater block
  $my_meta->addRepeaterBlock($prefix.'re_',array('inline' => true, 'name' => __('This is a Repeater Block','tax-meta'),'fields' => $repeater_fields));
  /*
   * Don't Forget to Close up the meta box decleration
   */
  //Finish Meta Box Decleration
  $my_meta->Finish();
}
