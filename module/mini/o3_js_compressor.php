<?php
/**
 * CSS/JS minify module for O3 Engine
 *
 * JS Packer file
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
namespace o3\module\mini;
 
//Check if JavaScriptPacker class was defined before
if ( !class_exists('JavaScriptPacker', false ) ) 
	require_once('js/class.JavaScriptPacker.php');
	
/**
 * Pack JS file
 *
 * Extends the JS Packer class made by Dean Edwards
 * 
 * @see JavaScriptPacker 
 */
class o3_js_compressor extends \JavaScriptPacker {
	
	public static function process( $_script, $_encoding = 62, $_fastDecode = true, $_specialChars = false ) {
		$obj = new o3_js_compressor( $_script, $_encoding, $_fastDecode, $_specialChars );
		return $obj->pack();		
  	}
  
}

?>