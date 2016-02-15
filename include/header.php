<?php 

/**
 * O3 Engine http header handler
 *
 * Functions for handling http header
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
  
//set document encoding to utf-8 and content type to text/html 
//return: VOID	

/**
 * Sets the content type and charset in the header.
 *
 * @param int $content_type (optional) Content type of the document. Default value: 'text/html'
 * @param int $charset (optional) Encoding of the document. Default value: 'utf-8'
 *	 
 * @return void
 */
function o3_header_encoding( $content_type = 'text/html', $charset = 'utf-8' ) {
	header('Content-Type: '.$content_type.( $charset != '' ? '; charset='.$charset : '' ) );
}

/*
* Redirect to URL
*
* @param string $url Redirect to this URL
* @param boolean $replace Flag to replace headers
* @param int $http_response_code Forces the HTTP response code 
*/
function o3_redirect( $url, $replace = true, $http_response_code = null ) {
	header( 'location: '.$url, $replace, $http_response_code );
	die();
}

/**
 * Set HTTP header by code
 * @link http://php.net/manual/en/function.http-response-code.php
 *
 * @param number $code
 *
 * @return void
 */
function o3_header_code( $code ) {
	$text = false;
	switch ( intval($code) ) {
		case 100: $text = 'Continue'; break;
	    case 101: $text = 'Switching Protocols'; break;
	    case 200: $text = 'OK'; break;
	    case 201: $text = 'Created'; break;
	    case 202: $text = 'Accepted'; break;
	    case 203: $text = 'Non-Authoritative Information'; break;
	    case 204: $text = 'No Content'; break;
	    case 205: $text = 'Reset Content'; break;
	    case 206: $text = 'Partial Content'; break;
	    case 300: $text = 'Multiple Choices'; break;
	    case 301: $text = 'Moved Permanently'; break;
	    case 302: $text = 'Moved Temporarily'; break;
	    case 303: $text = 'See Other'; break;
	    case 304: $text = 'Not Modified'; break;
	    case 305: $text = 'Use Proxy'; break;
	    case 400: $text = 'Bad Request'; break;
	    case 401: $text = 'Unauthorized'; break;
	    case 402: $text = 'Payment Required'; break;
	    case 403: $text = 'Forbidden'; break;
	    case 404: $text = 'Not Found'; break;
	    case 405: $text = 'Method Not Allowed'; break;
	    case 406: $text = 'Not Acceptable'; break;
	    case 407: $text = 'Proxy Authentication Required'; break;
	    case 408: $text = 'Request Time-out'; break;
	    case 409: $text = 'Conflict'; break;
	    case 410: $text = 'Gone'; break;
	    case 411: $text = 'Length Required'; break;
	    case 412: $text = 'Precondition Failed'; break;
	    case 413: $text = 'Request Entity Too Large'; break;
	    case 414: $text = 'Request-URI Too Large'; break;
	    case 415: $text = 'Unsupported Media Type'; break;
	    case 500: $text = 'Internal Server Error'; break;
	    case 501: $text = 'Not Implemented'; break;
	    case 502: $text = 'Bad Gateway'; break;
	    case 503: $text = 'Service Unavailable'; break;
	    case 504: $text = 'Gateway Time-out'; break;
	    case 505: $text = 'HTTP Version not supported'; break;	
	}
	if ( $text !== false ) {
		$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
		header($protocol.' '.$code.' '.$text);
	} else {
		die('Unknown http status code "'.$code.'"');
	}
}

/**
 * Autocache file
 * 
 * @todo Update function
 * @link http://css-tricks.com/snippets/php/intelligent-php-cache-control/
 * @return void
 */
function o3_auto_cache( $__file__ ) {
	
	//get the last-modified-date of this very file
	$lastModified = filemtime( $__file__ );
	//get a unique hash of this file (etag)
	$etagFile = md5_file( $__file__ );
	//get the HTTP_IF_MODIFIED_SINCE header if set
	$ifModifiedSince = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
	//get the HTTP_IF_NONE_MATCH header if set (etag: unique file hash)
	$etagHeader = (isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);
	
	//set last-modified header
	header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastModified)." GMT");
	//set etag-header
	header("Etag: $etagFile");
	//make sure caching is turned on
	header('Cache-Control: public');
	
	//check if page has changed. If not, send 304 and exit
	if ( @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastModified || $etagHeader == $etagFile ) {
  	header("HTTP/1.1 304 Not Modified");
  	die();
	}
	
}

?>