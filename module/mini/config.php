<?php
/**
 * CSS/JS minify module for O3 Engine
 *
 * Config file
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

use o3\config as config;

/** Allow minimizeing of files */
config\def('O3_MINI_MINIMIZE',false);

/** Allow minimizeing of js files */
config\def('O3_MINI_JS',true);

/** Allow minimizeing of css files */
config\def('O3_MINI_CSS',true);

/** CSS cache max lifetime in seconds */
config\def('O3_MINI_CSS_CACHE_LIFETIME',1209600); //2 weeks

/** JS cache max lifetime in seconds */
config\def('O3_MINI_JS_CACHE_LIFETIME',1209600); //2 weeks

/** The location of the cache directory for css files */
config\def('O3_MINI_CSS_CACHE_DIR', O3_CACHE_DIR);

/** The location of the cache directory for css files */
config\def('O3_MINI_JS_CACHE_DIR', O3_CACHE_DIR);

/** URL to the cache directory for css files */
config\def('O3_MINI_CSS_CACHE_URL', O3_CACHE_URL );

/** URL to the cache directory for js files */
config\def('O3_MINI_JS_CACHE_URL', O3_CACHE_URL );

?>