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

/**
 * Redirect script to a URL
 *
 * @param string $url Redirect to this URL
 *
 * @return void
 */
function o3_redirect( $url ) {
	header('location: '.$url);
	die();
}

/**
 * Set HTTP header by code
 *
 * @param number $code
 *
 * @return void
 */
function o3_header_code( $code ) {
	switch ( $code ) {
		case 404:
		case '404':
			header( "HTTP/1.1 404 Not Found" );			
			break;			
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