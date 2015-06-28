<?php
/**
 * CSS/JS minify module for O3 Engine
 *
 * LESS CSS pareser file
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace o3\module\mini;
 
//Check if Less_Parser class was defined before
if ( !class_exists('Less_Parser', false ) ) 
	require_once('less/Less.php');

/**
 * LESS Parser file
 *
 * Extends the LESS Parser class from http://lessphp.gpeasy.com/ & https://github.com/oyejorge/less.php
 * 
 * @see Less_Parser 
 */
class o3_less_parser extends \Less_Parser { };

?>