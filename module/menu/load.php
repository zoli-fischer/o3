<?php

/**
 * Menu handler module for O3 Engine
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
if ( !defined('O3') || !isset($this) )
	die('O3 not defined.');
 
require_once('o3_menu.php');

//create o3 instance in the o3 main object
$this->menu = new o3_menu(); 
$this->menu->set_parent( $this );

?>