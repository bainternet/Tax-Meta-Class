<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extracts an array of context data to the function context,
 * and includes the desired template. Variables extracted here
 * are intended to be used in the template partial rendered.
 * This function emulates an MVC-like template engine behaviour.
 *
 * The global post needs to be declared in this function, to allow
 * templates to use custom queries correctly.
 *
 * @param string $template
 * @param array $context
 * @param bool $return
 */
function render( $template_name, array $context = array(), $return = false ) {
	global $post;

	if (!$template_name) {
		return;
	}

	if ( file_exists(TAX_META_CLASS_ABSPATH . $template_name . '.php')) {
		$template_located = TAX_META_CLASS_ABSPATH . $template_name . '.php';
	}

	extract( $context );

	if ( $return ) {
		ob_start();
	}

	if ( ! $template_located OR ! include( $template_located ) ) {

		trigger_error( 'Cannot find template partial called ' . $template_name, E_USER_ERROR );
	}

	if ( $return ) {
		$data = @ob_get_clean();
		return $data;
	}
}


