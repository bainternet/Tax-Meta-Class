<?php
/*
Plugin Name: Demo Tax meta class
Plugin URI: http://en.bainternet.info
Description: Tax meta class usage demo
Version: 1.0
Author: Bainternet, Ohad Raz
Author URI: http://en.bainternet.info
*/

//include the main class file
require_once("Tax-meta-class/Tax-meta-class.php");
if (is_admin()){
	/* 
	 * prefix of meta keys, optional
	 * use underscore (_) at the beginning to make keys hidden, for example $prefix = '_ba_';
	 *  you also can make prefix empty to disable it
	 * 
	 */
	$prefix = 'ba_';
	/* 
	 * configure your meta box
	 */
	$config = array(
		'id' => 'demo_meta_box',					// meta box id, unique per meta box
		'title' => 'Demo Meta Box',					// meta box title
		'pages' => array('category'),				// taxonomy name, accept categories, post_tag and custom taxonomies
		'context' => 'normal',						// where the meta box appear: normal (default), advanced, side; optional
		'fields' => array(),						// list of meta fields (can be added by field arrays)
		'local_images' => false,					// Use local or hosted images (meta box images for add/remove)
		'use_with_theme' => false					//change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);
	
	
	/*
	 * Initiate your meta box
	 */
	$my_meta =  new Tax_Meta_Class($config);
	
	/*
	 * Add fields to your meta box
	 */
	
	//text field
	$my_meta->addText($prefix.'text_field_id',array('name'=> 'My Text '));
	//textarea field
	$my_meta->addTextarea($prefix.'textarea_field_id',array('name'=> 'My Textarea '));
	//checkbox field
	$my_meta->addCheckbox($prefix.'checkbox_field_id',array('name'=> 'My Checkbox '));
	//select field
	$my_meta->addSelect($prefix.'select_field_id',array('selectkey1'=>'Select Value1','selectkey2'=>'Select Value2'),array('name'=> 'My select ', 'std'=> array('selectkey2')));
	//radio field
	$my_meta->addRadio($prefix.'radio_field_id',array('radiokey1'=>'Radio Value1','radiokey2'=>'Radio Value2'),array('name'=> 'My Radio Filed', 'std'=> array('radionkey2')));
	//date field
	$my_meta->addDate($prefix.'date_field_id',array('name'=> 'My Date '));
	//Time field
	$my_meta->addTime($prefix.'time_field_id',array('name'=> 'My Time '));
	//Color field
	$my_meta->addColor($prefix.'color_field_id',array('name'=> 'My Color '));
	//Image field
	$my_meta->addImage($prefix.'image_field_id',array('name'=> 'My Image '));
	//file upload field
	$my_meta->addFile($prefix.'file_field_id',array('name'=> 'My File '));
	//wysiwyg field
	$my_meta->addWysiwyg($prefix.'wysiwyg_field_id',array('name'=> 'My wysiwyg Editor '));
	//taxonomy field
	$my_meta->addTaxonomy($prefix.'taxonomy_field_id',array('taxonomy' => 'category'),array('name'=> 'My Taxonomy '));
	//posts field
	$my_meta->addPosts($prefix.'posts_field_id',array('post_type' => 'post'),array('name'=> 'My Posts '));
	
	/*
	 * To Create a reapeater Block first create an array of fields
	 * use the same functions as above but add true as a last param
	 */
	
	$repeater_fields[] = $my_meta->addText($prefix.'re_text_field_id',array('name'=> 'My Text '),true);
	$repeater_fields[] = $my_meta->addTextarea($prefix.'re_textarea_field_id',array('name'=> 'My Textarea '),true);
	$repeater_fields[] = $my_meta->addCheckbox($prefix.'re_checkbox_field_id',array('name'=> 'My Checkbox '),true);
	$repeater_fields[] = $my_meta->addImage($prefix.'image_field_id',array('name'=> 'My Image '),true);
	
	/*
	 * Then just add the fields to the repeater block
	 */
	//repeater block
	$my_meta->addRepeaterBlock($prefix.'re_',array('inline' => true, 'name' => 'This is a Repeater Block','fields' => $repeater_fields));
	/*
	 * Don't Forget to Close up the meta box decleration
	 */
	//Finish Meta Box Decleration
	$my_meta->Finish();
}