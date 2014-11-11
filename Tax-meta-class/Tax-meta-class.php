<?php 
/**
 * Tax Meta Class
 *
 * The Tax Meta Class is used by including it in your plugin r theme files and using its methods to 
 * Add meta fields for WordPress Taxonomies (categories,tags and custom taxonomies). It is meant to be very simple and 
 * straightforward.
 *
 * This class is derived from My-Meta-Box (https://github.com/bainternet/My-Meta-Box script) which is 
 * a class for creating custom meta boxes for WordPress. 
 * 
 * @version 2.0.2
 * @copyright 2012-2014 Ohad Raz 
 * @author Ohad Raz (email: admin@bainternet.info)
 * @link http://en.bainternet.info
 * 
 * @license GNU General Public LIcense v3.0 - license.txt
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package Tax Meta Class
 * @deprecated replace_insert_to_post_text() @since 1.8.3
 * 
 */

if ( ! class_exists( 'Tax_Meta_Class') ) :

/**
 * All Types Meta Box class.
 *
 * @package All Types Meta Box
 * @since 1.0
 *
 * @todo Nothing.
 */
class Tax_Meta_Class {
  
  /**
   * Holds meta box object
   *
   * @var object
   * @access protected
   */
  protected $_meta_box;
  
  /**
   * Holds meta box fields.
   *
   * @var array
   * @access protected
   */
  protected $_prefix;
  
  /**
   * Holds Prefix for meta box fields.
   *
   * @var array
   * @access protected
   */
  protected $_fields;
  
  /**
   * Use local images.
   *
   * @var bool
   * @access protected
   */
  protected $_Local_images;
  
  /**
   * What form is this? edit or new term.
   *
   * @var string
   * @access protected
   * $since 1.0
   */
  protected $_form_type;
  /**
   * SelfPath to allow themes as well as plugins.
   *
   * @var string
   * @access protected
   * $since 1.0
   */
  protected $SelfPath;
  
  /**
   * Constructor
   *
   * @since 1.0
   * @access public
   *
   * @param array $meta_box 
   */
  public function __construct ( $meta_box ) {
    
    // If we are not in admin area exit.
    if ( ! is_admin() )
      return;
      
    // Assign meta box values to local variables and add it's missed values.
    $this->_meta_box = $meta_box;
    $this->_prefix = (isset($meta_box['prefix'])) ? $meta_box['prefix'] : ''; 
    $this->_fields = $this->_meta_box['fields'];
    $this->_Local_images = (isset($meta_box['local_images'])) ? true : false;
    $this->add_missed_values();
    if (isset($meta_box['use_with_theme'])){
      if ($meta_box['use_with_theme'] === true){
        $this->SelfPath = get_template_directory_uri() . '/Tax-meta-class';
      }elseif($meta_box['use_with_theme'] === false){
        $this->SelfPath = plugins_url( 'Tax-meta-class', plugin_basename( dirname( __FILE__ ) ) );
      }else{
        $this->SelfPath = $meta_box['use_with_theme'];
      }
    }else{
      $this->SelfPath = plugins_url( 'Tax-meta-class', plugin_basename( dirname( __FILE__ ) ) );
    }
    
    
    // Add Actions
    add_action( 'admin_init', array( $this, 'add' ) );
    
    // Load common js, css files
    // Must enqueue for all pages as we need js for the media upload, too.
    add_action( 'admin_print_styles', array( $this, 'load_scripts_styles' ) );

    //overwrite insert into post button
    
    //delete term meta on term deletion
    add_action('delete_term', array($this,'delete_taxonomy_metadata'), 10,2);
  }

  /**
   * Load all Javascript and CSS
   *
   * @since 1.0
   * @access public
   */
  public function load_scripts_styles() {
    
    // Get Plugin Path
    $plugin_path = $this->SelfPath;
    //only load styles and js when needed
    /* 
     * since 1.0
     */
    $taxnow = isset($_REQUEST['taxonomy'])? $_REQUEST['taxonomy'] : '';
    if (in_array($taxnow,$this->_meta_box['pages'])){
      // Check for special fields and add needed actions for them.
      $this->check_field_upload();
      $this->check_field_color();
      $this->check_field_date();
      $this->check_field_time();
      // Enqueue Meta Box Style
      wp_enqueue_style( 'tax-meta-clss', $plugin_path . '/css/Tax-meta-class.css' );
      // Enqueue Meta Box Scripts
      wp_enqueue_script( 'tax-meta-clss', $plugin_path . '/js/tax-meta-clss.js', array( 'jquery' ), null, true );
    
    }
    
  }
  
  /**
   * Check the Field Upload, Add needed Actions
   *
   * @since 1.0
   * @access public
   */
  public function check_field_upload() {
    
    // Check if the field is an image or file. If not, return.
    if ( ! $this->has_field( 'image' ) && ! $this->has_field( 'file' ) )
      return;
    
    // Make upload feature work event when custom post type doesn't support 'editor'
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-sortable' );
  }
  
  /**
   * Check Field Color
   *
   * @since 1.0
   * @access public
   */
  public function check_field_color() {
    
    if ( $this->has_field( 'color' ) && $this->is_edit_page() ) {
      // Enqueu built-in script and style for color picker.
      wp_enqueue_style( 'farbtastic' );
      wp_enqueue_script( 'farbtastic' );
    }
    
  }
  
  /**
   * Check Field Date
   *
   * @since 1.0
   * @access public 
   */
  public function check_field_date() {
    
    if ( $this->has_field( 'date' ) && $this->is_edit_page() ) {
      // Enqueu JQuery UI, use proper version.
      $this->enqueue_jqueryui();
    }
    
  }
  
  /**
   * Check Field Time
   *
   * @since 1.0
   * @access public
   */
  public function check_field_time() {
    
    if ( $this->has_field( 'time' ) && $this->is_edit_page() ) {
      
      // Enqueu JQuery UI, use proper version.
      $this->enqueue_jqueryui();
      wp_enqueue_script( 'at-timepicker', '//cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.js', array( 'tmc-jquery-ui' ),false,true );
    }    
  }
  
  /**
   * Add Meta Box for multiple post types.
   *
   * @since 1.0
   * @access public
   */
  public function add() {
    
    // Loop through array
    foreach ( $this->_meta_box['pages'] as $page ) {
      //add fields to edit form
      add_action($page.'_edit_form_fields',array( $this, 'show_edit_form' ));
      //add fields to add new form
      add_action($page.'_add_form_fields',array( $this, 'show_new_form' )); 
      // this saves the edit fields
      add_action( 'edited_'.$page, array( $this, 'save' ), 10, 2);
      // this saves the add fields
      add_action('created_'.$page,array( $this, 'save' ), 10, 2);
    }
    // Delete all attachments when delete custom post type.
    add_action( 'wp_ajax_at_delete_file',     array( $this, 'delete_file' ) );
    add_action( 'wp_ajax_at_reorder_images',   array( $this, 'reorder_images' ) );
    // Delete file via Ajax
    add_action( 'wp_ajax_at_delete_mupload', array( $this, 'wp_ajax_delete_image' ) );
    
  }
  
  /**
   * Callback function to show fields on add new taxonomy term form.
   *
   * @since 1.0
   * @access public 
   */
  public function show_new_form($term_id){
    $this->_form_type = 'new';
    add_action('admin_footer',array($this,'footer_js'));
    $this->show($term_id);
  }
  
  /**
   * Callback function to show fields on term edit form.
   *
   * @since 1.0
   * @access public 
   */
  public function show_edit_form($term_id){
    $this->_form_type = 'edit';
    $this->show($term_id);
  }
  
  
  
  /**
   * Callback function to show fields in meta box.
   *
   * @since 1.0
   * @access public 
   */
  public function show($term_id) {
    
    wp_nonce_field( basename(__FILE__), 'tax_meta_class_nonce' );
    
    foreach ( $this->_fields as $field ) {
    $multiple = isset($field['multiple'])? $field['multiple'] : false;
      $meta = $this->get_tax_meta( $term_id, $field['id'], !$multiple );
    $meta = ( $meta !== '' ) ? $meta : (isset($field['std'])? $field['std'] : '');
      if ('image' != $field['type'] && $field['type'] != 'repeater')
        $meta = is_array( $meta ) ? array_map( 'esc_attr', $meta ) : esc_attr( $meta );
      
      echo '<tr class="form-field">';
      // Call Separated methods for displaying each type of field.
      call_user_func ( array( $this, 'show_field_' . $field['type'] ), $field, is_array($meta)? $meta : stripslashes($meta) );
      echo '</tr>';
    }
    echo '</table>';
  }
  
  /**
   * Show Repeater Fields.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_repeater( $field, $meta ) {
    // Get Plugin Path
    $plugin_path = $this->SelfPath;
    $this->show_field_begin( $field, $meta );
    echo "<div class='at-repeat' id='{$field['id']}'>";
    
    $c = 0;
        
      if (count($meta) > 0 && is_array($meta) ){
         foreach ($meta as $me){
           //for labling toggles
           $mmm =  isset($me[$field['fields'][0]['id']])? $me[$field['fields'][0]['id']]: "";
           echo '<div class="at-repater-block">'.$mmm.'<br/><table class="repeater-table" style="display: none;">';
           if ($field['inline']){
             echo '<tr class="at-inline" VALIGN="top">';
           }
        foreach ($field['fields'] as $f){
          //reset var $id for repeater
          $id = '';
          $id = $field['id'].'['.$c.']['.$f['id'].']';
          $m = isset($me[$f['id']])? $me[$f['id']]: '';
          $m = ( $m !== '' ) ? $m : $f['std'];
          if ('image' != $f['type'] && $f['type'] != 'repeater')
            $m = is_array( $m) ? array_map( 'esc_attr', $m ) : esc_attr( $m);

          //set new id for field in array format
          $f['id'] = $id;
          if (!$field['inline']){
            echo '<tr>';
          }
          if ($f['type'] == 'wysiwyg')
            call_user_func ( array( $this, 'show_field_' . $f['type'] ), $f, is_array($m)? $m : stripslashes($m),true);
          else
            call_user_func ( array( $this, 'show_field_' . $f['type'] ), $f, is_array($m)? $m : stripslashes($m));
            
          if (!$field['inline']){
            echo '</tr>';
          } 
        }
        if ($field['inline']){  
          echo '</tr>';
        }
        echo '</table>
        <span class="at-re-toggle"><img src="';
           if ($this->_Local_images){
             echo $plugin_path.'/images/edit.png';
           }else{
             echo 'http://i.imgur.com/ka0E2.png';
           }
           echo '" alt="Edit" title="Edit"/></span> 
        <img src="';
        if ($this->_Local_images){
          echo $plugin_path.'/images/remove.png';
        }else{
          echo 'http://i.imgur.com/g8Duj.png';
        }
        echo '" alt="'.__('Remove','tax-meta').'" title="'.__('Remove','tax-meta').'" id="remove-'.$field['id'].'"></div>';
        $c = $c + 1;
        
        }
        $this->show_field_end( $field, $meta );
      }

    echo '<img src="';
    if ($this->_Local_images){
      echo $plugin_path.'/images/add.png';
    }else{
      echo 'http://i.imgur.com/w5Tuc.png';
    }
    echo '" alt="'.__('Add','tax-meta').'" title="'.__('Add','tax-meta').'" id="add-'.$field['id'].'"><br/></div>';
    
    //create all fields once more for js function and catch with object buffer
    ob_start();
    echo '<div class="at-repater-block"><table class="repeater-table">';
    if ($field['inline']){
      echo '<tr class="at-inline" VALIGN="top">';
    } 
    foreach ($field['fields'] as $f){
      //reset var $id for repeater
      $id = '';
      $id = $field['id'].'[CurrentCounter]['.$f['id'].']';
      $f['id'] = $id; 
      if (!$field['inline']){
        echo '<tr>';
      }
      if ($f['type'] == 'wysiwyg')
            call_user_func ( array( $this, 'show_field_' . $f['type'] ), $f, '',true);
          else
            call_user_func ( array( $this, 'show_field_' . $f['type'] ), $f, '');
      
      if (!$field['inline']){
        echo '</tr>';
      }  
    }
    if ($field['inline']){
      echo '</tr>';
    } 
    echo '</table><img src="';
    if ($this->_Local_images){
      echo $plugin_path.'/images/remove.png';
    }else{
      echo 'http://i.imgur.com/g8Duj.png';
    }
    echo '" alt="'.__('Remove','tax-meta').'" title="'.__('Remove','tax-meta').'" id="remove-'.$field['id'].'"></div>';
    $counter = 'countadd_'.$field['id'];
    $js_code = ob_get_clean ();
    $js_code = str_replace("\n","",$js_code);
    $js_code = str_replace("\r","",$js_code);
    $js_code = str_replace("'","\"",$js_code);
    $js_code = str_replace("CurrentCounter","' + ".$counter." + '",$js_code);
    echo '<script>
        jQuery(document).ready(function() {
          var '.$counter.' = '.$c.';
          jQuery("#add-'.$field['id'].'").live(\'click\', function() {
            '.$counter.' = '.$counter.' + 1;
            jQuery(this).before(\''.$js_code.'\');            
            update_repeater_fields();
          });
              jQuery("#remove-'.$field['id'].'").live(\'click\', function() {
                  jQuery(this).parent().remove();
              });
          });
        </script>';            
    echo '<br/><style>
.at-inline{line-height: 1 !important;}
.at-inline .at-field{border: 0px !important;}
.at-inline .at-label{margin: 0 0 1px !important;}
.at-inline .at-text{width: 70px;}
.at-inline .at-textarea{width: 100px; height: 75px;}
.at-repater-block{background-color: #FFFFFF;border: 1px solid;margin: 2px;}
</style>';
    $this->show_field_end($field, $meta);
  }
  
  /**
   * Begin Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_begin( $field, $meta) {
    if (isset($field['group'])){
      if ($field['group'] == "start"){
        echo "<td class='at-field'>";
      }
    }else{
      if ($this->_form_type == 'edit'){
        echo '<th valign="top" scope="row">';
      }else{
        echo '<td><div class="form-field">';
      }
    }
    if ( $field['name'] != '' || $field['name'] != FALSE ) {
      //echo "<div class='at-label'>";
        echo "<label for='{$field['id']}'>{$field['name']}</label>";
      //echo "</div>";
    }
    if ($this->_form_type == 'edit'){
        echo '</th><td>';
    }
  }
  
  /**
   * End Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public 
   */
  public function show_field_end( $field, $meta=NULL ,$group = false) {
    if (isset($field['group'])){
      if ($group == 'end'){
        if ( isset($field['desc']) && $field['desc'] != '' ) {
          echo "<p class='description'>{$field['desc']}</p></td>";
        } else {
          echo "</td>";
        }
      }else {
        if ( isset($field['desc']) && $field['desc'] != '' ) {
          echo "<p class='description'>{$field['desc']}</p><br/>";  
        }else{
          echo '<br/>';
        }  
      }    
    }else{
      if ( isset($field['desc']) && $field['desc'] != '' ) {
        echo "<p class='description'>{$field['desc']}</p>";
      }
      if ($this->_form_type == 'edit'){
        echo '</td>';  
      }else{
        echo '</td></div>';
      }
    }
  }
  
  /**
   * Show Field Text.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_text( $field, $meta) {  
    $this->show_field_begin( $field, $meta );
    echo "<input type='text' class='at-text' name='{$field['id']}' id='{$field['id']}' value='{$meta}' style='{$field['style']}' size='30' />";
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show Field hidden.
   *
   * @param string $field 
   * @param string|mixed $meta 
   * @since 0.1.3
   * @access public
   */
  public function show_field_hidden( $field, $meta) {  
    //$this->show_field_begin( $field, $meta );
    echo "<input type='hidden' class='at-text' name='{$field['id']}' id='{$field['id']}' value='{$meta}'/>";
    //$this->show_field_end( $field, $meta );
  }
  
  /**
   * Show Field Paragraph.
   *
   * @param string $field 
   * @since 0.1.3
   * @access public
   */
  public function show_field_paragraph( $field) {  
    //$this->show_field_begin( $field, $meta );
    echo "<p style='{$field['style']}'>".$field['value']."</p>";
    //$this->show_field_end( $field, $meta );
  }
    
  /**
   * Show Field Textarea.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_textarea( $field, $meta ) {
    $this->show_field_begin( $field, $meta );
      echo "<textarea class='at-textarea large-text' style='{$field['style']}' name='{$field['id']}' id='{$field['id']}' cols='60' rows='10'>{$meta}</textarea>";
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show Field Select.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_select( $field, $meta ) {
    
    if ( ! is_array( $meta ) ) 
      $meta = (array) $meta;
      
    $this->show_field_begin( $field, $meta );
      echo "<select class='at-select' style='{$field['style']}' name='{$field['id']}" . ( $field['multiple'] ? "[]' id='{$field['id']}' multiple='multiple'" : "'" ) . ">";
      foreach ( $field['options'] as $key => $value ) {
        echo "<option value='{$key}'" . selected( in_array( $key, $meta ), true, false ) . ">{$value}</option>";
      }
      echo "</select>";
    $this->show_field_end( $field, $meta );
    
  }
  
  /**
   * Show Radio Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public 
   */
  public function show_field_radio( $field, $meta ) {
    
    if ( ! is_array( $meta ) )
      $meta = (array) $meta;
      
    $this->show_field_begin( $field, $meta );
      foreach ( $field['options'] as $key => $value ) {
        echo "<input style='{$field['style']}' type='radio' class='at-radio' name='{$field['id']}' value='{$key}'" . checked( in_array( $key, $meta ), true, false ) . " /> <span class='at-radio-label'>{$value}</span>";
      }
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show Checkbox Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_checkbox( $field, $meta ) {
  
    $this->show_field_begin($field, $meta);
    echo "<input type='checkbox' style='{$field['style']}' class='rw-checkbox' name='{$field['id']}' id='{$field['id']}'" . checked(!empty($meta), true, false) . " />";
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show Wysiwig Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_wysiwyg( $field, $meta ,$in_repeater = false) {
    $this->show_field_begin( $field, $meta );
    // Add TinyMCE script for WP version < 3.3
    global $wp_version;

    if ( version_compare( $wp_version, '3.2.1' ) < 1 || $in_repeater) {
      echo "<textarea style='{$field['style']}' class='at-wysiwyg theEditor large-text' name='{$field['id']}' id='{$field['id']}' cols='60' rows='10'>{$meta}</textarea>";
    }else{
      // Use new wp_editor() since WP 3.3
      wp_editor( stripslashes(html_entity_decode($meta)), $field['id'], array( 'editor_class' => 'at-wysiwyg' ) );
    }
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show File Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_file( $field, $meta ) {
    $this->show_field_begin( $field, $meta );
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_media();
	$std      = isset($field['std'])? $field['std'] : array('id' => '', 'url' => '');
	$multiple = isset($field['multiple'])? $field['multiple'] : false;
	$multiple = ($multiple)? "multiFile '" : "";
	$name     = esc_attr( $field['id'] );
	$value    = isset($meta['id']) ? $meta : $std;
	$has_file = (empty($value['url']))? false : true;
	$type     = isset($field['mime_type'])? $field['mime_type'] : '';
	$ext      = isset($field['ext'])? $field['ext'] : '';
	$type     = (is_array($type)? implode("|",$type) : $type);
	$ext      = (is_array($ext)? implode("|",$ext) : $ext);
	$id       = $field['id'];
	$li       = ($has_file)? "<li><a href='{$value['url']}' target='_blank'>{$value['url']}</a></li>": "";

	echo "<span class='simplePanelfilePreview'><ul>{$li}</ul></span>";
	echo "<input type='hidden' name='{$name}[id]' value='{$value['id']}'/>";
	echo "<input type='hidden' name='{$name}[url]' value='{$value['url']}'/>";
	if ($has_file)
		echo "<input type='button' class='{$multiple} button simplePanelfileUploadclear' id='{$id}' value='Remove File' data-mime_type='{$type}' data-ext='{$ext}'/>";
	else
		echo "<input type='button' class='{$multiple} button simplePanelfileUpload' id='{$id}' value='Upload File' data-mime_type='{$type}' data-ext='{$ext}'/>";
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show Image Field.
   *
   * @param array $field 
   * @param array $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_image( $field, $meta ) {
  	$this->show_field_begin( $field, $meta );
    wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_media();
	$std          = isset($field['std'])? $field['std'] : array('id' => '', 'url' => '');
	$name         = esc_attr( $field['id'] );
	$value        = isset($meta['id']) ? $meta : $std;
	//backwords capability
	if (!isset($value['url']))
		$value['url'] = '';
  if (!isset($value['id']))
    $value['id'] = '';
	$value['url'] = isset($value['src'])? $value['src']: $value['url'];
	$has_image    = empty($value['url'])? false : true;
	$w            = isset($field['width'])? $field['width'] : 'auto';
	$h            = isset($field['height'])? $field['height'] : 'auto';
	$PreviewStyle = "style='width: $w; height: $h;". ( (!$has_image)? "display: none;'": "'");
	$id           = $field['id'];
	$multiple     = isset($field['multiple'])? $field['multiple'] : false;
	$multiple     = ($multiple)? "multiFile " : "";

	echo "<span class='simplePanelImagePreview'><img {$PreviewStyle} src='{$value['url']}'><br/></span>";
	echo "<input type='hidden' name='{$name}[id]' value='{$value['id']}'/>";
	echo "<input type='hidden' name='{$name}[url]' value='{$value['url']}'/>";
	if ($has_image)
		echo "<input class='{$multiple} button  simplePanelimageUploadclear' id='{$id}' value='".__('Remove Image')."' type='button'/>";
	else
		echo "<input class='{$multiple} button simplePanelimageUpload' id='{$id}' value='".__('Upload Image')."' type='button'/>";
	$this->show_field_end( $field, $meta );
  }
  
  /**
   * Show Color Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_color( $field, $meta ) {
    
    if ( empty( $meta ) ) 
      $meta = '#';
      
    $this->show_field_begin( $field, $meta );
      echo "<input class='at-color' type='text' name='{$field['id']}' id='{$field['id']}' value='{$meta}' size='8' />";
    //  echo "<a href='#' class='at-color-select button' rel='{$field['id']}'>" . __( 'Select a color' ) . "</a>";
      echo "<input type='button' class='at-color-select button' rel='{$field['id']}' value='" . __( 'Select a color','tax-meta' ) . "'/>";
      echo "<div style='display:none' class='at-color-picker' rel='{$field['id']}'></div>";
    $this->show_field_end($field, $meta);
    
  }

  /**
   * Show Checkbox List Field
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_checkbox_list( $field, $meta ) {
    
    if ( ! is_array( $meta ) ) 
      $meta = (array) $meta;
      
    $this->show_field_begin($field, $meta);
    
      $html = array();
    
      foreach ($field['options'] as $key => $value) {
        $html[] = "<input style='{$field['style']}' type='checkbox' class='at-checkbox_list' name='{$field['id']}[]' value='{$key}'" . checked( in_array( $key, $meta ), true, false ) . " /> {$value}";
      }
    
      echo implode( '<br />' , $html );
      
    $this->show_field_end($field, $meta);
    
  }
  
  /**
   * Show Date Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_date( $field, $meta ) {
    $this->show_field_begin( $field, $meta );
      echo "<input style='{$field['style']}' type='text' class='at-date' name='{$field['id']}' id='{$field['id']}' rel='{$field['format']}' value='{$meta}' size='30' />";
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show time field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public 
   */
  public function show_field_time( $field, $meta ) {
    $this->show_field_begin( $field, $meta );
      echo "<input style='{$field['style']}' type='text' class='at-time' name='{$field['id']}' id='{$field['id']}' rel='{$field['format']}' value='{$meta}' size='30' />";
    $this->show_field_end( $field, $meta );
  }
  
   /**
   * Show Posts field.
   * used creating a posts/pages/custom types checkboxlist or a select dropdown
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public 
   */
  public function show_field_posts($field, $meta) {
    global $post;
    
    if (!is_array($meta)) $meta = (array) $meta;
    $this->show_field_begin($field, $meta);
    $options = $field['options'];
    $posts = get_posts($options['args']);
    
    // checkbox_list
    if ('checkbox_list' == $options['type']) {
      foreach ($posts as $p) {
        echo "<input type='checkbox' name='{$field['id']}[]' value='$p->ID'" . checked(in_array($p->ID, $meta), true, false) . " /> $p->post_title<br/>";
      }
    }
    // select
    else {
      echo "<select name='{$field['id']}" . ($field['multiple'] ? "[]' multiple='multiple' style='height:auto'" : "'") . ">";
      foreach ($posts as $p) {
        echo "<option value='$p->ID'" . selected(in_array($p->ID, $meta), true, false) . ">$p->post_title</option>";
      }
      echo "</select>";
    }
    
    $this->show_field_end($field, $meta);
  }
  
  /**
   * Show Taxonomy field.
   * used creating a category/tags/custom taxonomy checkboxlist or a select dropdown
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public 
   * 
   * @uses get_terms()
   */
  public function show_field_taxonomy($field, $meta) {
    global $post;
    
    if (!is_array($meta)) $meta = (array) $meta;
    $this->show_field_begin($field, $meta);
    $options = $field['options'];
    $terms = get_terms($options['taxonomy'], $options['args']);
    
    // checkbox_list
    if ('checkbox_list' == $options['type']) {
      foreach ($terms as $term) {
        echo "<input type='checkbox' name='{$field['id']}[]' value='$term->slug'" . checked(in_array($term->slug, $meta), true, false) . " /> $term->name<br/>";
      }
    }
    // select
    else {
      echo "<select name='{$field['id']}" . ($field['multiple'] ? "[]' multiple='multiple' style='height:auto'" : "'") . ">";
      foreach ($terms as $term) {
        echo "<option value='$term->slug'" . selected(in_array($term->slug, $meta), true, false) . ">$term->name</option>";
      }
      echo "</select>";
    }
    
    $this->show_field_end($field, $meta);
  }
  
  /**
   * Save Data from Metabox
   *
   * @param string $term_id 
   * @since 1.0
   * @access public 
   */
  public function save( $term_id ) {
    
    // check if the we are coming from quick edit issue #38 props to Nicola Peluchetti.
    if (isset($_REQUEST['action'])  &&  $_REQUEST['action'] == 'inline-save-tax') {
      return $term_id;
    }

    if ( ! isset( $term_id )                            // Check Revision
    || ( ! isset( $_POST['taxonomy'] ) )              // Check if current taxonomy type is set.
    || ( ! in_array( $_POST['taxonomy'], $this->_meta_box['pages'] ) )              // Check if current taxonomy type is supported.
    || ( ! check_admin_referer( basename( __FILE__ ), 'tax_meta_class_nonce') )    // Check nonce - Security
    || ( ! current_user_can('manage_categories') ) )                 // Check permission
    {
      return $term_id;
    }
    
    foreach ( $this->_fields as $field ) {
      
      $name = $field['id'];
      $type = $field['type'];
      $multiple = isset($field['multiple'])? $field['multiple']: false;
      $old = $this->get_tax_meta( $term_id, $name, !$multiple  );
      $new = ( isset( $_POST[$name] ) ) ? $_POST[$name] : ( ( $multiple ) ? array() : '' );
            

      // Validate meta value
      if ( class_exists( 'Tax_Meta_Validate' ) && method_exists( 'Tax_Meta_Validate', $field['validate_func'] ) ) {
        $new = call_user_func( array( 'Tax_Meta_Validate', $field['validate_func'] ), $new );
      }
      
      //skip on Paragraph field
      if ($type != "paragraph"){

        // Call defined method to save meta value, if there's no methods, call common one.
        $save_func = 'save_field_' . $type;
        if ( method_exists( $this, $save_func ) ) {
          call_user_func( array( $this, 'save_field_' . $type ), $term_id, $field, $old, $new );
        } else {
          $this->save_field( $term_id, $field, $old, $new );
        }
      }
      
    } // End foreach
  }
  
  /**
   * Common function for saving fields.
   *
   * @param string $term_id 
   * @param string $field 
   * @param string $old 
   * @param string|mixed $new 
   * @since 1.0
   * @access public
   */
  public function save_field( $term_id, $field, $old, $new ) {
    $name = $field['id'];
    $this->delete_tax_meta( $term_id, $name );
    if ( $new === '' || $new === array() ) 
      return;
    
    $this->update_tax_meta( $term_id, $name, $new );
  }  
  
  /**
   * function for saving image field.
   *
   * @param string $term_id 
   * @param string $field 
   * @param string $old 
   * @param string|mixed $new 
   * @since 1.0
   * @access public
   */
  public function save_field_image( $term_id, $field, $old, $new ) {
    $name = $field['id'];
    $this->delete_tax_meta( $term_id, $name );
    if ( $new === '' || $new === array() || $new['id'] == '' || $new['url'] == '') 
      return;
    
    $this->update_tax_meta( $term_id, $name, $new );
  }
  
  /*
   * Save Wysiwyg Field.
   *
   * @param string $term_id 
   * @param string $field 
   * @param string $old 
   * @param string $new 
   * @since 1.0
   * @access public 
   */
  public function save_field_wysiwyg( $term_id, $field, $old, $new ) {
    $this->save_field( $term_id, $field, $old, $new );
  }
  
  /**
   * Save repeater Fields.
   *
   * @param string $term_id 
   * @param string $field 
   * @param string|mixed $old 
   * @param string|mixed $new 
   * @since 1.0
   * @access public 
   */
  public function save_field_repeater( $term_id, $field, $old, $new ) {
    if (is_array($new) && count($new) > 0){
      foreach ($new as $n){
        foreach ( $field['fields'] as $f ) {
          $type = $f['type'];
          switch($type) {
            case 'wysiwyg':
                $n[$f['id']] = wpautop( $n[$f['id']] ); 
                break;
              case 'file':
                $n[$f['id']] = $this->save_field_file_repeater($term_id,$f,'',$n[$f['id']]);
                break;
              default:
                   break;
          }
        }
        if(!$this->is_array_empty($n))
          $temp[] = $n;
      }
      if (isset($temp) && count($temp) > 0 && !$this->is_array_empty($temp)){
        $this->update_tax_meta($term_id,$field['id'],$temp);
      }else{
        //  remove old meta if exists
        $this->delete_tax_meta($term_id,$field['id']);
      }
    }else{
      //  remove old meta if exists
      $this->delete_tax_meta($term_id,$field['id']);
    }
  }
  
  /**
   * Save File Field.
   *
   * @param string $term_id 
   * @param string $field 
   * @param string $old 
   * @param string $new 
   * @since 1.0
   * @access public
   */
  public function save_field_file( $term_id, $field, $old, $new ) {

    $name = $field['id'];
    $this->delete_tax_meta( $term_id, $name );
    if ( $new === '' || $new === array() || $new['id'] == '' || $new['url'] == '') 
      return;
    
    $this->update_tax_meta( $term_id, $name, $new );
  }
  
  
  /**
   * Add missed values for meta box.
   *
   * @since 1.0
   * @access public
   */
  public function add_missed_values() {
    
    // Default values for meta box
    $this->_meta_box = array_merge( array( 'context' => 'normal', 'priority' => 'high', 'pages' => array( 'post' ) ),(array)$this->_meta_box );

    // Default values for fields
    foreach ( (array)$this->_fields as $field ) {
      
      $multiple = in_array( $field['type'], array( 'checkbox_list', 'file', 'image' ) );
      $std = $multiple ? array() : '';
      $format = 'date' == $field['type'] ? 'yy-mm-dd' : ( 'time' == $field['type'] ? 'hh:mm' : '' );

      $field = array_merge( array( 'multiple' => $multiple, 'std' => $std, 'desc' => '', 'format' => $format, 'validate_func' => '' ), $field );
    
    } // End foreach
    
  }

  /**
   * Check if field with $type exists.
   *
   * @param string $type 
   * @since 1.0
   * @access public
   */
  public function has_field( $type ) {
    foreach ( $this->_fields as $field ) {
      if ( $type == $field['type'] ) 
        return true;
      elseif('repeater' == $field['type'] ){
        foreach((array)$field["fields"] as $repeater_field)  {
            if($type == $repeater_field["type"]) return true;
        }
      }
    }
    return false;
  }

  /**
   * Check if current page is edit page.
   *
   * @since 1.0
   * @access public
   */
  public function is_edit_page() {
    global $pagenow;
    return ( $pagenow == 'edit-tags.php' );
  }
  
  /**
   * Fixes the odd indexing of multiple file uploads.
   *
   * Goes from the format: 
   * $_FILES['field']['key']['index']
   * to
   * The More standard and appropriate:
   * $_FILES['field']['index']['key']
   *
   * @param string $files 
   * @since 1.0
   * @access public
   */
  public function fix_file_array( &$files ) {
    
    $output = array();
    
    foreach ( $files as $key => $list ) {
      foreach ( $list as $index => $value ) {
        $output[$index][$key] = $value;
      }
    }
    
    return $files = $output;
  
  }

  /**
   * Get proper JQuery UI version.
   *
   * Used in order to not conflict with WP Admin Scripts.
   *
   * @since 1.0
   * @access public
   */
  public function get_jqueryui_ver() {
    
    global $wp_version;
    
    if ( version_compare( $wp_version, '4.0', '>=') ) {
      return '1.11.2';
    }
    if ( version_compare( $wp_version, '3.9', '>=') ) {
      return '1.10.4';
    }
    if ( version_compare( $wp_version, '3.8', '>=') ) {
      return '1.10.3';
    }
    if ( version_compare( $wp_version, '3.5', '>=') ) {
      return '1.9.2';
    }
    if ( version_compare( $wp_version, '3.1', '>=') ) {
      return '1.8.10';
    }
    
    return '1.7.3';  
  }

  /**
   * Enqueue JQuery UI version.
   *
   * @since 2.0.2
   * @access public
   */
  public function enqueue_jqueryui(){
    wp_enqueue_style( 'tmc-jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/' . $this->get_jqueryui_ver() . '/themes/'.apply_filters('tmc_jquery_ui_theme','smoothness').'/jquery-ui.css' );
    wp_enqueue_script( 'tmc-jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/' . $this->get_jqueryui_ver() . '/jquery-ui.min.js', array( 'jquery' ) );
  }
  
  /**
   *  Add Field to meta box (generic function)
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   */
  public function addField($id,$args){
    $new_field = array('id'=> $id,'std' => '','desc' => '','style' =>'','multiple' => false);
    $new_field = array_merge($new_field, $args);
    $this->_fields[] = $new_field;
  }
  
  /**
   *  Add Text Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'style' =>   // custom style for field, string optional
   *    'validate_func' => // validate function, string optional
   *   @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addText($id,$args,$repeater=false){
    $new_field = array('type' => 'text','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Text Field','multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  /**
   *  Add Hidden Field to meta box
   *  @author Ohad Raz
   *  @since 0.1.3
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'style' =>   // custom style for field, string optional
   *    'validate_func' => // validate function, string optional
   *   @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addHidden($id,$args,$repeater=false){
    $new_field = array('type' => 'hidden','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Text Field', 'multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Paragraph to meta box
   *  @author Ohad Raz
   *  @since 0.1.3
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $value  paragraph html
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addParagraph($id,$args,$repeater=false){
    $new_field = array('type' => 'paragraph','id'=> $id,'value' => '','style' =>'', 'std' => '', 'multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
    
  /**
   *  Add Checkbox Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addCheckbox($id,$args,$repeater=false){
    $new_field = array('type' => 'checkbox','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Checkbox Field','multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add CheckboxList Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $options (array)  array of key => value pairs for select options
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
   *  
   *   @return : remember to call: $checkbox_list = $this->get_tax_meta(get_the_ID(), 'meta_name', false); 
   *   which means the last param as false to get the values in an array
   */
  public function addCheckboxList($id,$options,$args,$repeater=false){
    $new_field = array('type' => 'checkbox_list','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Checkbox List Field', 'multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Textarea Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'style' =>   // custom style for field, string optional
   *    'validate_func' => // validate function, string optional
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addTextarea($id,$args,$repeater=false){
    $new_field = array('type' => 'textarea','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Textarea Field','multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Select Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string field id, i.e. the meta key
   *  @param $options (array)  array of key => value pairs for select options  
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, (array) optional
   *    'multiple' => // select multiple values, optional. Default is false.
   *    'validate_func' => // validate function, string optional
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addSelect($id,$options,$args,$repeater=false){
    $new_field = array('type' => 'select','id'=> $id,'std' => array(),'desc' => '','style' =>'','name' => 'Select Field','multiple' => false,'options' => $options);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  
  /**
   *  Add Radio Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string field id, i.e. the meta key
   *  @param $options (array)  array of key => value pairs for radio options
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional 
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
   */
  public function addRadio($id,$options,$args,$repeater=false){
    $new_field = array('type' => 'radio','id'=> $id,'std' => array(),'desc' => '','style' =>'','name' => 'Radio Field','options' => $options,'multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add Date Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional
   *    'format' => // date format, default yy-mm-dd. Optional. Default "'d MM, yy'"  See more formats here: http://goo.gl/Wcwxn
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addDate($id,$args,$repeater=false){
    $new_field = array('type' => 'date','id'=> $id,'style' =>'','std' => '','desc' => '','format'=>'d MM, yy','name' => 'Date Field','multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add Time Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string- field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional
   *    'format' => // time format, default hh:mm. Optional. See more formats here: http://goo.gl/83woX
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addTime($id,$args,$repeater=false){
    $new_field = array('type' => 'time','id'=> $id,'std' => '','style' =>'','desc' => '','format'=>'hh:mm','name' => 'Time Field','multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Color Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addColor($id,$args,$repeater=false){
    $new_field = array('type' => 'color','id'=> $id,'std' => '','style' =>'','desc' => '','name' => 'ColorPicker Field','multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Image Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'validate_func' => // validate function, string optional
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addImage($id,$args,$repeater=false){
    $new_field = array('type' => 'image','id'=> $id,'desc' => '','style' =>'','name' => 'Image Field', 'std' => '','multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add File Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'validate_func' => // validate function, string optional 
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
   */
  public function addFile($id,$args,$repeater=false){
    $new_field = array('type' => 'file','id'=> $id,'desc' => '','name' => 'File Field','multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add WYSIWYG Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'style' =>   // custom style for field, string optional Default 'width: 300px; height: 400px'
   *    'validate_func' => // validate function, string optional 
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
   */
  public function addWysiwyg($id,$args,$repeater=false){
    $new_field = array('type' => 'wysiwyg','id'=> $id,'std' => '','desc' => '','style' =>'width: 300px; height: 400px','name' => 'WYSIWYG Editor Field','multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Taxonomy Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $options mixed|array options of taxonomy field
   *    'taxonomy' =>    // taxonomy name can be category,post_tag or any custom taxonomy default is category
   *     'type' =>  // how to show taxonomy? 'select' (default) or 'checkbox_list'
   *    'args' =>  // arguments to query taxonomy, see http://goo.gl/uAANN default ('hide_empty' => false)  
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional 
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
   */
  public function addTaxonomy($id,$options,$args,$repeater=false){
    $q = array('hide_empty' => 0);
    $tax = 'category';
    $type = 'select';
    $temp = array('taxonomy' => $tax,'type' => $type,'args' => $q);
    $options = array_merge($temp,$options);
    $new_field = array('type' => 'taxonomy','id'=> $id,'desc' => '','name' => 'Taxonomy Field','options'=> $options,'multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add posts Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $options mixed|array options of taxonomy field
   *    'post_type' =>    // post type name, 'post' (default) 'page' or any custom post type
   *    'type' =>  // how to show posts? 'select' (default) or 'checkbox_list'
   *    'args' =>  // arguments to query posts, see http://goo.gl/is0yK default ('posts_per_page' => -1)  
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional 
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
   */
  public function addPosts($id,$options,$args,$repeater=false){
    $q = array('posts_per_page' => -1);
    $temp = array('post_type' =>'post','type'=>'select','args'=>$q);
    $options = array_merge($temp,$options);
    $new_field = array('type' => 'posts','id'=> $id,'desc' => '','name' => 'Posts Field','options'=> $options,'multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add repeater Field Block to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'style' =>   // custom style for field, string optional
   *    'validate_func' => // validate function, string optional
   *    'fields' => //fields to repeater  
   */
  public function addRepeaterBlock($id,$args){
    $new_field = array('type' => 'repeater','id'=> $id,'name' => 'Reapeater Field','fields' => array(),'inline'=> false);
    $new_field = array_merge($new_field, $args);
    $this->_fields[] = $new_field;
  }
  
  
  /**
   * Finish Declaration of Meta Box
   * @author Ohad Raz
   * @since 1.0
   * @access public
   */
  public function Finish() {
    $this->add_missed_values();
  }
  
  /**
   * Helper function to check for empty arrays
   * @author Ohad Raz
   * @since 1.0
   * @access public
   * @param $args mixed|array
   */
  public function is_array_empty($array){
    if (!is_array($array))
      return true;
    
    foreach ($array as $a){
      if (is_array($a)){
        foreach ($a as $sub_a){
          if (!empty($sub_a) && $sub_a != '')
            return false;
        }
      }else{
        if (!empty($a) && $a != '')
          return false;
      }
    }
    return true;
  }
  
  
  //get term meta field
  public function get_tax_meta($term_id,$key,$multi = false){
    $t_id = (is_object($term_id))? $term_id->term_id: $term_id;
    $m = get_option( 'tax_meta_'.$t_id);  
    if (isset($m[$key])){
      return $m[$key];
    }else{
      return '';
    }
  }
  
  //delete meta
  public function delete_tax_meta($term_id,$key){
    $m = get_option( 'tax_meta_'.$term_id);
    if (isset($m[$key])){
      unset($m[$key]);
    }
    update_option('tax_meta_'.$term_id,$m);
  }
  
  //update meta
  public function update_tax_meta($term_id,$key,$value){
    $m = get_option( 'tax_meta_'.$term_id);
    $m[$key] = $value;
    update_option('tax_meta_'.$term_id,$m);
  }
  

  /**
   * delete_taxonomy_metadata
   * 
   * delete meta on term deletion
   *
   *  answers issue #16
   *  @author Ohad Raz
   *  @since 1.8.1
   *  @access public
   *  @return Void
   */
  public function delete_taxonomy_metadata($term,$term_id) {
    delete_option( 'tax_meta_'.$term_id );
  }

  
  /**
   * footer_js 
   *  fix issue #2
   *  @author Ohad Raz
   *  @since 1.7.4
   *  @access public
   *  @return Void
   */
  public function footer_js(){
    ?>
    <SCRIPT TYPE="text/javascript">
    //fix issue #2
    var numberOfRows = 0;
    jQuery(document).ready(function(){
      numberOfRows = jQuery("#the-list>tr").length;
      jQuery("#the-list").bind("DOMSubtreeModified", function() {
          if(jQuery("#the-list>tr").length !== numberOfRows){
              //update new count
              numberOfRows = jQuery("#the-list>tr").length;
              //clear form 
              clear_form_meta();
          }
      });
      function clear_form_meta(){
          //remove image
          jQuery(".mupload_img_holder").find("img").remove();
          jQuery(".mupload_img_holder").next().next().next().removeClass('at-delete_image_button').addClass('at-upload_image_button');
          jQuery(".mupload_img_holder").next().next().next().val("Upload Image");
          jQuery(".mupload_img_holder").next().next().val('');
          jQuery(".mupload_img_holder").next().val('');

          //clear selections
          jQuery("#addtag select option").removeProp('selected');
          //clear checkbox
          jQuery("#addtag input:checkbox").removeAttr('checked');
          //clear radio buttons
          jQuery("#addtag input:radio").prop('checked', false);
          //remove repeater blocks
          jQuery(".at-repater-block").remove();

      }
    });
    </SCRIPT>
    <?php
  }
  
} // End Class

endif; // End Check Class Exists

/*
 * meta functions for easy access:
 */
//get term meta field
if (!function_exists('get_tax_meta')){
	function get_tax_meta($term_id,$key,$multi = false){
		$t_id = (is_object($term_id))? $term_id->term_id: $term_id;
		$m = get_option( 'tax_meta_'.$t_id);  
		if (isset($m[$key])){
			return $m[$key];
		}else{
			return '';
		}
	}
}

//delete meta
if (!function_exists('delete_tax_meta')){
	function delete_tax_meta($term_id,$key){
		$m = get_option( 'tax_meta_'.$term_id);
		if (isset($m[$key])){
			unset($m[$key]);
		}
		update_option('tax_meta_'.$term_id,$m);
	}
}

//update meta
if (!function_exists('update_tax_meta')){
	function update_tax_meta($term_id,$key,$value){
		$m = get_option( 'tax_meta_'.$term_id);
		$m[$key] = $value;
		update_option('tax_meta_'.$term_id,$m);
	}
}

//get term meta field and strip slashes
if (!function_exists('get_tax_meta_strip')){
	function get_tax_meta_strip($term_id,$key,$multi = false){
		$t_id = (is_object($term_id))? $term_id->term_id: $term_id;
		$m = get_option( 'tax_meta_'.$t_id);  
		if (isset($m[$key])){
			return is_array($m[$key])? $m[$key] : stripslashes($m[$key]);
		}else{
			return '';
		}
	}
}
//get all meta fields of a term
if (!function_exists('get_tax_meta_all')){
	function get_tax_meta_all( $term_id){
		$t_id = (is_object($term_id))? $term_id->term_id: $term_id;
		return get_option( 'tax_meta_'.$t_id,array());
	}
}