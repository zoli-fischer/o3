<?php

/**
 * Email sending module for O3 Engine
 *
 * Send emails with or without attachment.
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace o3\module\email;
 
//Check if css2inline class was defined before
if ( !class_exists('css2inline', false ) ) 
	require_once('css2inline/css2inline.php');

/**
 * Add inline style for tags in a HTML based on a CSS list
 *
 * Extends the CSS 2 inline class made by Tijs Verkoyen <php-css-to-inline-styles@verkoyen.eu>
 * 
 * @see css2inline 
 */
class o3_css2inline extends \css2inline { };
