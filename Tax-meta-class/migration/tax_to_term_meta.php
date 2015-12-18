<?php 
/**
* tax_to_term_meta
* 
* used to migrate data from options table to 
* WordPress 4.4 term meta table 
*
* to use this migration class you must include it directly before 
* including the tax meta class file mainly if you want to use the old
* 'get_tax_meta', 'delete_tax_meta', 'update_tax_meta', 
* 'get_tax_meta_strip' and 'get_tax_meta_all' this file also contains the 
* new definition of these functions to use the term meta api so you don't 
* have to go look around your code and replace every thing. 
*
* @usage:
*
* 	require_once("Tax-meta-class/migration/tax_to_term_meta.php");
*  	new tax_to_term_meta();
*
* @copyright 2012-2015 Ohad Raz 
* @author Ohad Raz (email: admin@bainternet.info)
* @link https://en.bainternet.info
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
*/
class tax_to_term_meta{
	/**
	 * delete data from options table 
	 * after migraion?
	 * @var bool
	 */
	public $delete_options = false;
	function __construct( $delete_options = false){
		$this->delete_options = $delete_options;
		$this->hooks();
	}

	function hooks(){
		add_action('init',array($this, 'migrate_data_maybe') );
		if ( $this->delete_options ){
			add_action('shutdown',array( $this, 'delete_options') );
		}
	}

	function migrate_data_maybe(){
		//needs migration?
		$do_migration = get_option('tax_meta_migrated', 'do_migration'); 
		if ( 'do_migration' == $do_migration ){
			$this->do_migration();
			update_option( 'tax_meta_migrated', 'done' );
		}
	}

	function do_migration(){
		$options = $this->get_options();
		foreach ($options as $option) {
			$term_id = $this->term_id_from_option( $option );
			$meta = get_option( $option );
			$this->tax_to_term( $term_id, $meta );
			if( $this->delete_options ){
				delete_option( $option );
			}
		}
	}

	function tax_to_term( $term_id, $meta ){
		foreach ((array)$meta as $meta_name => $meta_value) {
			update_term_meta( $term_id, $meta_name, $meta_value );
		}
	}

	function term_id_from_option( $option ){
		return str_replace('tax_meta_', '', $option );
	}

	function get_options(){
		global $wpdb;
		$query = "SELECT option_name 
		FROM $wpdb->options 
		WHERE option_name LIKE 'tax_meta_%'
		";
		$options = $wpdb->get_col( $query );
		return $options;
	}

	function delete_options(){
		$options = $this->get_options();
		foreach ($options as $option) {
			delete_option( $option );
		}
	}
}

/*
 * meta functions for easy access using term meta api
 */
//get term meta field
if (!function_exists('get_tax_meta')){
	function get_tax_meta($term_id,$key,$multi = false){
		$term_id = (is_object($term_id))? $term_id->term_id: $term_id;
		return get_term_meta( $term_id, $key, $multi );
	}
}

//delete meta
if (!function_exists('delete_tax_meta')){
	function delete_tax_meta($term_id,$key){
		$term_id = (is_object($term_id))? $term_id->term_id: $term_id;
		return delete_term_meta( $term_id, $key );
	}
}

//update meta
if (!function_exists('update_tax_meta')){
	function update_tax_meta($term_id,$key,$value){
		$term_id = (is_object($term_id))? $term_id->term_id: $term_id;
		return update_term_meta( $term_id, $key, $value );
	}
}

//get term meta field and strip slashes
if (!function_exists('get_tax_meta_strip')){
	function get_tax_meta_strip($term_id,$key,$multi = false){
		return stripslashes( get_term_meta( $term_id, $key, $multi ) );
	}
}
//get all meta fields of a term
if (!function_exists('get_tax_meta_all')){
	function get_tax_meta_all( $term_id){
		$term_id = (is_object($term_id))? $term_id->term_id: $term_id;
		return get_term_meta( $term_id );
	}
}