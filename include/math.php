<?php 

/**
 * O3 Engine math
 *
 * Functions for math handling.
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
* Floor a number with a precision
*
* @param mixed $number
* @param mixed $precision
*
* @return mixed
*/
function o3_floor( $number, $precision ) {
	if ( $precision != 0 ) {
		$p = pow( $precision > 0 ? 0.01 : 10, abs($precision) );				
		return floor( $number / $p ) * $p;
	}
	return floor( $number );
}

/**
* Ceil a number with a precision
*
* @param mixed $number
* @param mixed $precision
*
* @return mixed
*/
function o3_ceil( $number, $precision ) {
	if ( $precision != 0 ) {
		$p = pow( $precision > 0 ? 0.01 : 10, abs($precision) );				
		return ceil( $number / $p ) * $p;
	}
	return ceil( $number );
}
	
?>