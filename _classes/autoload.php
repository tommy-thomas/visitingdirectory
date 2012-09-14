<?php
/**
 * This file autoloads classes as needed.
 * @package cip
 */
$path = '/data/php/classes';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

/**
 * Loads classes as needed.
 */
function __autoload($class) {
	require $class . '.class.php';
}
?>