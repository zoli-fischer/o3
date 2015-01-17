<?php
/**
 * CSS/JS minify module for O3 Engine
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
require_once('o3_mini.php');

//create o3 instance in the o3 main object
$this->mini = new o3_mini(); 
$this->mini->set_parent( $this );

//check module data
if ( isset($o3_module_data['minimize']) )
	$this->mini->allow_mini( $o3_module_data['minimize'] );

if ( isset($o3_module_data['minimize_js']) )
	$this->mini->allow_mini_js( $o3_module_data['minimize_js'] );

if ( isset($o3_module_data['minimize_css']) )
	$this->mini->allow_mini_css(  $o3_module_data['minimize_css'] );

if ( isset($o3_module_data['livetime_js']) )
	$this->mini->set_js_cache_lifetime( $o3_module_data['livetime_js'] );

if ( isset($o3_module_data['livetime_css']) )
	$this->mini->set_css_cache_lifetime( $o3_module_data['livetime_css']  );

if ( isset($o3_module_data['css_cache_dir']) )
	$this->mini->css_cache_dir = $o3_module_data['css_cache_dir'];

if ( isset($o3_module_data['css_cache_url']) )
	$this->mini->css_cache_url = $o3_module_data['css_cache_url'];

if ( isset($o3_module_data['js_cache_dir']) )
	$this->mini->js_cache_dir = $o3_module_data['js_cache_dir'];

if ( isset($o3_module_data['js_cache_url']) )
	$this->mini->js_cache_url = $o3_module_data['js_cache_url'];

?>