<?php

namespace ETSIS_CLI;

/**
 * Do nothing
 *
 * Instantiate this class to hijack all functions of a class
 *
 * @package etsis-cli
 */
final class NoOp {

	function __set( $key, $value ) {
		// do nothing
	}

	function __call( $method, $args ) {
		// do nothing
	}
}
