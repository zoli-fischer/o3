<?php

/**
 * Routing module for O3 Engine
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
require_once('o3_routing.php');

//create o3 instance in the o3 main object
$this->routing = new o3_routing(); 
$this->routing->set_parent( $this );

?>