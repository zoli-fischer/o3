<?php 

/**
 * O3 Engine strings
 *
 * Functions for string handling.
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
/*display, escape and format*/

/**
* Escape string for avoid html injection.
*
* @param string $val String to escape
* @param string $flag (optional) Escaping mode. ENT_COMPAT - convert double-quotes, ENT_QUOTES (default) - convert both, ENT_NOQUOTES - leave both.
* @param string $encoding (optional) Encoding of the escaped string. Default: 'UTF-8'
*
* @return array
*/
function o3_html( $val, $flag = ENT_QUOTES, $encoding = 'UTF-8' ) {
	$f = array( '&', '>', '<' );
	$t = array( '&amp;', '&gt;', '&lt;' );
	if ( $flag & ENT_QUOTES || $flag & ENT_COMPAT ) {
		$f[] = '"';	
		$t[] = '&quot;';
	}
	if ( $flag & ENT_QUOTES ) {
		$f[] = "'";
		$t[] = '&#39;';
	}
	return o3_convert(str_replace( $f, $t, $val ),$encoding);
}

/**
* Trim a string with 
*
* @example o3_trim( '"Test!"', '"!' ); returns Test 
*
* @param string $val String to trim
* @param string $characters (optional) Characters to remove from the begining and end of string. Default value: \s
*
* @return string Trimmed string
*/
function o3_trim( $str, $characters = "\s" ) {
	return preg_replace('/(^['.$characters.']+|['.$characters.']+$)/', '', $str );
}


/**
* Escape a string 
*
* @param string $string String to escape
* @param string $single_quote Default: true
* @param string $double_quote Default: true
* @param string $backslash Default: true
*
* @return string Escaped string
*/
function o3_addslashes( $string, $single_quote = true, $double_quote = true, $backslash = true ) {
	if ( $backslash )
		$string = str_replace('\\','\\\\', $string );
	if ( $single_quote )
		$string = str_replace("'","\\'", $string );
	if ( $double_quote )
		$string = str_replace('"','\\"', $string );
	return $string;
}

/**
* Display byte value in byte, kilobyte, megabyte, gigabyte, terabyte or petabyte.
*
* @param string $format The format of the outputted string. u - unit lcase, U - unit ucase, v - value. Default: "v U".
* @param int $size Bytes to display
*
* @return string
*/
function o3_bytes_display( $format, $size ) {
  $unit_l = array( 'b', 'kb', 'mb', 'gb', 'tb', 'pb' );
  $unit_u = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );
  $v = @round( $size / pow( 1024, ( $i = floor(log($size,1024) ))), 2 );
  $u_l = $unit_l[$i];
  $u_u = $unit_u[$i];
  
  $patterns = array( "@(^|[^\\\\])(v)@",
  									 "@(\\\\){1}(v){1}@",
  									 "@(^|[^\\\\])(u)@",
  									 "@(\\\\){1}(u){1}@",
  									 "@(^|[^\\\\])(U)@",
  									 "@(\\\\){1}(U){1}@" );
  $replacements = array( "\${1}".$v, "$2",
  											 "\${1}".$u_l, "$2",
  											 "\${1}".$u_u, "$2"	);
  $va = preg_replace( $patterns, $replacements, $format );
  //$va = str_replace( "\\", "", $va );
  return $va; 
}

/**
	* Format a number with grouped thousands.
	*
	* Read more: http://dk1.php.net/manual/en/function.number-format.php
	*
	* @param float $nr
	* @param int $decimals (optional) Default: 0
	* @param string $dec_point (optional) Default: '.'
	* @param string $thousands_sep (optional) Default: ','
	*
	* @return string
	*/
function o3_nr_display( $nr, $decimals = 0, $dec_point = '.', $thousands_sep = ',' ) {
	return number_format( $nr, $decimals, $dec_point, $thousands_sep );
}

/*convert, relpace, encode and decode*/


/**
	* Generate a hash value with sha512 algorithm
	*
	* @param string $str String to be hashed
	* @param boolean $raw_output
	* @return string When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits
	*/
function o3_sha3( $str, $raw_output = false ) {
	return hash( "sha512", $str, $raw_output );
}

/**
	* Generate salted hash code from string
	*
	* The encoded string only with the same salt string can be decoded.
	*
	* @see o3_salt_decode() o3_salt_decode()
	* @param string $str String to be hashed
	* @param string $salt Salt to the hash algorithm
	* @return string 
	*/
function o3_salt_encode( $str, $salt = '' ) {
	return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $str, MCRYPT_MODE_ECB, mcrypt_create_iv( mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ) , MCRYPT_RAND) ));
}

/**
	* Generate string from salted hash code
	*
	* @see o3_salt_encode() o3_salt_encode()
	* @param string $str String to decode
	* @param string $salt Salt used at encoding
	* @return string 
	*/
function o3_salt_decode( $str, $salt = '' ) {
	return mcrypt_decrypt( MCRYPT_RIJNDAEL_256, $salt, base64_decode($str), MCRYPT_MODE_ECB, mcrypt_create_iv( mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND) );
}
    
/**
	* Sanitize string.
	*
	* Remove all special characters from a string.
	*
	* @param string $str String to sanitize
	*
	* @return string
	*/
function o3_sanitize( $str ) {			
	$str = o3_is_utf8($str) ? $str : o3_convert($str);
  $str = preg_replace('~[^\\pL0-9_]+~u', '-', $str); // substitutes anything but letters, numbers and '_' with separator
  $str = trim($str, "-");
  $str = @iconv("utf-8", "us-ascii//TRANSLIT", $str); // TRANSLIT does the whole job
  $str = strtolower($str);
  $str = preg_replace('~[^-a-z0-9_]+~', '', $str); // keep only letters, numbers, '_' and separator
  return o3_remove_more('-',2,$str);
}

/**
	* Remove 2 or more occured string in string.
	*
	* @param string $chr Character to remove
	* @param int $count Occurance of the character
	* @param string $str String to remove from 
	*
	* @return string
	*/
function o3_remove_more( $chr, $count, $str ) {
	if ( $count > 1 ) {
		$chrs = str_repeat( $chr, $count );
		while ( strstr( $str, $chrs ) !== false )
			$str = str_replace( $chrs, $chr, $str );
	}
	return $str;
}

/**
	* Convert string from an encoding to another encoding.
	*
	* @param string $str String to convert
	* @param int $to (optional) Encode to. Default: "UTF-8"
	* @param string $from (optional) Encoding from. Default: ''. If '' mb_detect_encoding is used.
	*
	* @return string
	*/
function o3_convert( $str, $to = 'UTF-8', $from = '' ) {
	$from = $from == '' ? mb_detect_encoding($str) : $from;
	return mb_convert_encoding( $str, $to, $from );
}

/**
	* Replace new line character to <br> tag
	*
	* Opposit of the nl2br
	*
	* @param string $string
	*
	* @return string
	*/
function o3_br2nl( $string ) {
	return preg_replace('/\<br(\s*)?\/?\>/i', "\r\n", $string);
}

/*check*/

/**
	* Check if strings encoding is UTF-8
	*
	* Returns true if the string is valid UTF-8
	*
	* @param string $string String to check
	*
	* @return boolean
	*
	* @link http://www.php.net/manual/en/function.mb-detect-encoding.php
	* @author Christopher Andrew Corbyn <chris@w3style.co.uk>
	*
	*/
function o3_is_utf8( $string ) {
	return preg_match('%(?:
        [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
        |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
        |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
        |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
        |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
        |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
        )+%xs', $string);  
}
	
?>