<?php

/**
 * Class autoloader
 */
require_once W3TC_LIB_DIR . DIRECTORY_SEPARATOR . 'Azure' . 
	DIRECTORY_SEPARATOR . 'GuzzleHttp' . DIRECTORY_SEPARATOR .
	'functions_include.php';
require_once W3TC_LIB_DIR . DIRECTORY_SEPARATOR . 'Azure' . 
	DIRECTORY_SEPARATOR . 'GuzzleHttp' . DIRECTORY_SEPARATOR .
	'Promise' . DIRECTORY_SEPARATOR . 'functions_include.php';
require_once W3TC_LIB_DIR . DIRECTORY_SEPARATOR . 'Azure' . 
	DIRECTORY_SEPARATOR . 'GuzzleHttp' . DIRECTORY_SEPARATOR .
	'Psr7' . DIRECTORY_SEPARATOR . 'functions_include.php';


function w3tc_azure_class_autoload( $class ) {
	$base = null;

	// some php pass classes with slash
	if ( substr( $class, 0, 1 ) == "\\" )
		$class = substr( $class, 1 );

	if ( substr( $class, 0, 23 ) == 'MicrosoftAzure\\Storage\\' ) {
		$base = W3TC_LIB_DIR . DIRECTORY_SEPARATOR . 'Azure' . 
			DIRECTORY_SEPARATOR . 'MicrosoftAzureStorage' . DIRECTORY_SEPARATOR;
		$class = substr( $class, 23 );
	} elseif ( substr( $class, 0, 11 ) == 'GuzzleHttp\\' ) {
		$base = W3TC_LIB_DIR . DIRECTORY_SEPARATOR . 'Azure' . 
			DIRECTORY_SEPARATOR . 'GuzzleHttp' . DIRECTORY_SEPARATOR;
		$class = substr( $class, 11 );
	} elseif ( substr( $class, 0, 17 ) == 'Psr\\Http\\Message\\' ) {
		$base = W3TC_LIB_DIR . DIRECTORY_SEPARATOR . 'Azure' . 
			DIRECTORY_SEPARATOR . 'PsrHttpMessage' . DIRECTORY_SEPARATOR;
		$class = substr( $class, 17 );
	}

	if ( !is_null( $base ) ) {
		$file = $base . strtr( $class, "\\_",
			DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR ) . '.php';
		if ( file_exists( $file ) )
			require_once $file;
	}
}

spl_autoload_register( 'w3tc_azure_class_autoload' );
