<?php

/**
 * O3 Engine validation
 *
 * Functions for data validation
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
* Check if strings is a valid password
*
* @param string $str String to check
* @param int $min (optional) Minimuma allowed length of the string. Default: 8
* @param int $max (optional) Maximum allowed length of the string. Default: 32
* @param string $special_chars (optional) Allowed characters in the string. Default: "-+=?*:;.,@#$^%&"
*
* @return string
*/
function o3_valid_password( $str, $min = 8, $max = 32, $special_chars = "-+=?!*:;.,@#$^%&" ) {
	$min_ = min( $min, $max );
	$max_ = max( $min, $max );
	if ( $min_ < 1 || $max_ < 1 )
		return false; 
	return preg_match('/^[a-zA-Z0-9'.$special_chars.']{'.$min_.','.$max_.'}$/',$str) > 0;
	//((?=(.*\d){3,})(?=.*[a-z])(?=.*[A-Z])(?=(.*[!@#$%^&]){3,}).{8,})
}

/**
* Check if string is a valid email address
* @param string str String to check
* @return boolean True if the string is a valid email adress
*/
function o3_valid_email( $str ) {
	return preg_match('/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/',$str);
};

?>