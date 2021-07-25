<?php
if ( ! function_exists( 'tax_meta_class_assets' ) )
{
	function tax_meta_class_assets() {
		wp_localize_script( 'taxmeta/js', 'taxmetaAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}

	add_action( 'wp_enqueue_scripts', 'tax_meta_class_assets', 100 );
}

if ( ! function_exists( 'add_tax_meta_repeater_block' ) ){
	function add_tax_meta_repeater_block(){

	}
}
