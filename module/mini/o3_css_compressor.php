<?php
/**
 * CSS/JS minify module for O3 Engine
 *
 * CSS Compressor file
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace o3\module\mini;
 
//Check if Minify_CSS_Compressor class was defined before
if ( !class_exists('Minify_CSS_Compressor', false ) ) 
	require_once('css/Compressor.php');

/**
 * Compress CSS file
 *
 * Extends the CSS Compressor class made by Stephen Clay 
 * 
 * @see Minify_CSS_Compressor 
 */
class o3_css_compressor extends \Minify_CSS_Compressor { };

?>