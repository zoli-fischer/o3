<?php
/**
 * HTML5 cache manifest handle module for O3 Engine
 *
 * Handle HTML5 cache manifest files
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
if ( !defined('O3') || !isset($this) )
	die('O3 not defined.');

require_once(str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__))).'/config.php');
require_once('o3_manifest.php');

//create o3 instance in the o3 main object
$this->manifest = new o3_manifest(); 
$this->manifest->set_parent( $this );

if ( isset($o3_module_data['manifest_cache_dir']) )
	$this->manifest->manifest_cache_dir = $o3_module_data['manifest_cache_dir'];

if ( isset($o3_module_data['manifest_cache_url']) )
	$this->manifest->manifest_cache_url = $o3_module_data['manifest_cache_url'];

//add buffer output callback
$this->debug->add_ob_start_callback( array( $this->manifest, 'html_inject' ) );

?>