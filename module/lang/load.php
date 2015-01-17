<?php

/**
 * Content language module for O3 Engine
 *
 * Loader file
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
if ( !defined('O3') || !isset($this) )
	die('O3 not defined.');
	
require_once(str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__))).'/config.php');
require_once('o3_lang.php');

//create o3 instance in the o3 main object
$this->lang = new o3_lang(); 
$this->lang->set_parent( $this );

//check module data
if ( isset($o3_module_data['dir']) )
	$this->lang->set_dir( $o3_module_data['dir'] );

if ( isset($o3_module_data['js_url']) )
	$this->lang->set_js_url( $o3_module_data['js_url'] );

if ( isset($o3_module_data['languages']) )
	$this->lang->load( '', $o3_module_data['languages'] ); //load languages

if ( isset($o3_module_data['current']) )
	$this->lang->current( $o3_module_data['current'] ); //set current

if ( isset($o3_module_data['collections']) && $o3_module_data['collections'] != '' )
	$this->lang->load( $o3_module_data['collections'], $o3_module_data['languages'] );

//resource dependencies
$this->head_res( O3_URL.'/resource/js/lib/jquery/jquery-latest.min.js', O3_RES_DIR.'/js/lib/jquery/jquery-latest.min.js' );
$this->head_res( O3_URL.'/resource/js/o3.js', O3_RES_DIR.'/js/o3.js' );

//inject javascript code
$this->debug->add_ob_start_callback( array( $this->lang, 'inject_html_script' ) );

//replace {o3_lang:****} template tags
$this->debug->add_ob_start_callback( array( $this->lang, 'relpace_template_tags' ) );

?>