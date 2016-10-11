<?php

// Get the cli arguments
list( $arguments, $assoc_args ) = ETSIS_CLI::parse_args( array_slice( $GLOBALS['argv'], 1 ) );

// Global parameter : --quiet/--silent
define( 'ETSIS_CLI_QUIET', isset( $assoc_args['quiet'] ) || isset( $assoc_args['silent'] ) );

// Global parameter :  --require
if ( isset( $assoc_args['require'] ) ) {
    if( file_exists( $assoc_args['require'] ) ) {
        require $assoc_args['require'];
        unset( $assoc_args['require'] );
    } else {
        ETSIS_CLI::error( sprintf( 'File "%s" not found', $assoc_args['require'] ) ) ;
    }
}

// Global parameter :  --path
if ( !empty( $assoc_args['path'] ) ) {
	define( 'ETSIS_ROOT', rtrim( $assoc_args['path'], '/' ) );
    unset( $assoc_args['path'] );
} else {
    // Assume ETSIS root is current directory
	define( 'ETSIS_ROOT', $_SERVER['PWD'] );
}

ETSIS_CLI::run_command( $arguments, $assoc_args );
